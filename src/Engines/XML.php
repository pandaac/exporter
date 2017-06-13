<?php

namespace pandaac\Exporter\Engines;

use Exception;
use XMLReader;
use ErrorException;
use pandaac\Exporter\Output;
use InvalidArgumentException;
use UnexpectedValueException;
use pandaac\Exporter\Exporter;
use Illuminate\Support\Collection;
use pandaac\Exporter\Contracts\Source;
use pandaac\Exporter\Contracts\Engine as Contract;

class XML implements Contract
{
    /**
     * Holds the Exporter implementation.
     *
     * @var \pandaac\Exporter\Exporter
     */
    protected $exporter;

    /**
     * Holds the source.
     *
     * @var \pandaac\Exporter\Contracts\Source|string
     */
    protected $source;

    /**
     * Holds the reader implementation.
     *
     * @var \XMLReader
     */
    protected $reader;

    /**
     * Holds the attributes.
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * Holds the plural attributes.
     *
     * @var array
     */
    protected $plural = [];

    /**
     * Holds a virtual element.
     *
     * @var \Illuminate\Support\Collection
     */
    protected $element;

    /**
     * Holds the parsed elements.
     *
     * @var \Illuminate\Support\Collection
     */
    protected $elements;

    /**
     * Holds the current element ID.
     *
     * @var array
     */
    protected $parents = [];

    /**
     * Holds references to elements by their IDs.
     *
     * @var array
     */
    protected $references = [];

    /**
     * A list of all the node types.
     *
     * @var array
     */
    protected $nodes = [
        XMLReader::NONE                     => 'None',
        XMLReader::ELEMENT                  => 'Element',
        XMLReader::ATTRIBUTE                => 'Attribute',
        XMLReader::TEXT                     => 'Text',
        XMLReader::CDATA                    => 'CDATA',
        XMLReader::ENTITY_REF               => 'EntityReference',
        XMLReader::ENTITY                   => 'Entity',
        XMLReader::PI                       => 'ProcessingInstruction',
        XMLReader::COMMENT                  => 'Comment',
        XMLReader::DOC                      => 'Document',
        XMLReader::DOC_TYPE                 => 'DocumentType',
        XMLReader::DOC_FRAGMENT             => 'DocumentFragment',
        XMLReader::NOTATION                 => 'Notation',
        XMLReader::WHITESPACE               => 'Whitespace',
        XMLReader::SIGNIFICANT_WHITESPACE   => 'SignificantWhitespace',
        XMLReader::END_ELEMENT              => 'EndElement',
        XMLReader::END_ENTITY               => 'EndEntity',
        XMLReader::XML_DECLARATION          => 'XMLDeclaration',
    ];

    /**
     * Instantiate the engine object.
     *
     * @param  \pandaac\Exporter\Exporter  $exporter
     * @param  array  $attributes  []
     * @param  array  $plural  []
     * @return void
     */
    public function __construct(Exporter $exporter, array $attributes = [], array $plural = [])
    {
        $this->exporter = $exporter;
        $this->attributes = $attributes;
        $this->plural = $plural;

        $this->reader = new XMLReader;
        $this->elements = new Collection;
    }

    /**
     * Open a source resource.
     *
     * @param  \pandaac\Exporter\Contracts\Source|string  $source
     * @return void
     */
    public function open($source)
    {
        libxml_use_internal_errors(true);

        $this->source = $source;

        if (! ($source instanceof Source ? $this->openSource($source) : $this->openFile($source))) {
            return false;
        }

        $this->reader->setParserProperty(XMLReader::VALIDATE, true);

        if (! $this->reader->isValid()) {
            $this->triggerException(
                new UnexpectedValueException('The source data is not valid.')
            );
        }

        $this->reader->setParserProperty(XMLReader::VALIDATE, false);
    }

    /**
     * Open a source resource.
     *
     * @param  \pandaac\Exporter\Contracts\Source  $source
     * @return void
     */
    protected function openSource(Source $source)
    {
        return $this->reader->xml($source->content(), null);
    }

    /**
     * Open a file resource.
     *
     * @param  string  $source
     * @return void
     */
    protected function openFile($source)
    {
        if (! is_file($source)) {
            if ($this->exporter->setting('xml.strict', false)) {
                throw new InvalidArgumentException(sprintf('%s could not be found.', $source));
            }

            return;
        }

        return $this->reader->open($source, null);
    }

    /**
     * Read and parse the source.
     *
     * @return \pandaac\Exporter\Output
     */
    public function output()
    {
        while ($this->reader->read()) {
            if (method_exists($this, $method = $this->getMethodName($this->reader->nodeType))) {
                call_user_func_array([$this, $method], []);
            }

            $this->stopVirtualElement();
        }

        if (libxml_get_last_error() !== false) {
            $error = libxml_get_last_error();

            $this->triggerException(
                new ErrorException($error->message, $error->code, $error->level, $error->file, $error->line)
            );
        }

        unset($this->references);

        return new Output($this->elements);
    }

    /**
     * Close the opened source resource.
     *
     * @return void
     */
    public function close()
    {
        libxml_clear_errors();

        $this->reader->close();
    }

    /**
     * Get the source data or path.
     *
     * @return \pandaac\Exporter\Contracts\Source|string
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Read an element node.
     *
     * @return void
     */
    protected function readElementNode()
    {
        $this->element = $this->createVirtualElement();

        if ($this->reader->hasAttributes) {
            foreach ($this->readElementAttributes() as $attribute => $value) {
                $this->element->put($attribute, $value);
            }
        }
    }

    /**
     * Read an end element node.
     *
     * @return void
     */
    protected function readTextNode()
    {
        $this->element->put('text', $this->reader->value);
    }

    /**
     * Read an end element node.
     *
     * @return void
     */
    protected function readCdataNode()
    {
        $value = preg_replace('/\\r?\\n(\s+)/', PHP_EOL, $this->reader->value);

        $value = trim($value, PHP_EOL);

        $key = $this->element->has('value') ? 'cdata' : 'value';

        $this->element->put($key, $value);
    }

    /**
     * Read the attributes on an element node.
     *
     * @return \Illuminate\Support\Collection
     */
    protected function readElementAttributes()
    {
        $attributes = new Collection;

        while ($this->reader->moveToNextAttribute()) {
            $attributes->put($this->reader->name, $this->castify($this->reader->value));
        }

        $this->reader->moveToElement();

        return $attributes;
    }

    /**
     * Create a virtual element.
     *
     * @return void
     */
    protected function createVirtualElement()
    {
        $id = uniqid();

        $depth = $this->reader->depth;

        $parent = isset($this->parents[$depth - 1]) ? $this->parents[$depth - 1] : null;

        $attributes = [
            '@id'       => $id,
            '@parent'   => $parent,
            '@element'  => $this->reader->name,
        ];

        $element = new Collection($attributes);

        $this->parents[$depth] = $id;

        $this->references[$id] = $element;

        return $element;
    }

    /**
     * Destroy the virtual element.
     *
     * @return void
     */
    protected function stopVirtualElement()
    {
        if (! $this->element or $this->reader->nodeType !== XMLReader::ELEMENT) {
            return;
        }

        if ($parent = $this->findVirtualElementById($this->element->get('@parent'))) {
            $key = $this->getPluralized($this->element->get('@element'));

            if (! $parent->has($key)) {
                $parent->put($key, new Collection);
            }

            if ($parent->get($key) instanceof Collection) {
                $parent->get($key)->push($this->element);
            }
        } else {
            $this->elements->push($this->element);
        }

        $this->element->forget(['@id', '@parent', '@element']);
    }

    /**
     * Get the pluralized name of the given key.
     *
     * @param  string  $key
     * @return string
     */
    protected function getPluralized($key)
    {
        return isset($this->plural[$key]) ? $this->plural[$key] : $key;
    }

    /**
     * Find a virtual element by its ID.
     *
     * @param  integer  $id
     * @return \Illuminate\Support\Collection|null
     */
    protected function findVirtualElementById($id)
    {
        return isset($this->references[$id]) ? $this->references[$id] : null;
    }

    /**
     * Convert the value into its proper value type.
     *
     * @param  mixed  $value
     * @return mixed
     */
    protected function castify($value)
    {
        if (preg_match('/^\-?([0-9]+)$/', $value)) {
            return intval($value);
        }

        if (preg_match('/^\-?\d+(\.\d+)?$/', $value)) {
            // @todo fix json returning float values.
            // return floatval($value);

            return $value;
        }

        return $value;
    }

    /**
     * Convert the given node type into a method name.
     *
     * @param  integer  $node
     * @return string
     */
    protected function getMethodName($node)
    {
        if (! isset($this->nodes[$node])) {
            return null;
        }

        return 'read'.$this->nodes[$node].'Node';
    }

    /**
     * Throw an exception, unless it's being suppressed.
     *
     * @param  \Exception  $e
     * @return void
     */
    protected function triggerException(Exception $e)
    {
        if (! $this->exporter->setting('xml.validate', true)) {
            return;
        }

        return $this->exporter->triggerException($e);
    }
}
