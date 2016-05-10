<?php

namespace pandaac\Exporter;

use Exception;
use XMLReader;
use pandaac\Exporter\Contracts\Reader as Contract;

class Reader implements Contract
{
    /**
     * Holds the process implementation.
     *
     * @var \XMLReader
     */
    protected $process;

    /**
     * Holds the parent element name.
     *
     * @var string
     */
    protected $parent;

    /**
     * Instantiate the reader object.
     *
     * @return void
     */
    public function __construct()
    {
        $this->process = new XMLReader;
    }

    /**
     * Open a file process.
     *
     * @param  string  $file
     * @return boolean
     */
    public function open($file)
    {
        if (! file_exists($file)) {
            throw new Exception(sprintf('Unable to open file %s', $file));
        }

        return $this->process->open($file);
    }

    /**
     * Read the file process.
     *
     * @return boolean
     */
    public function read()
    {
        return $this->process->read();
    }

    /**
     * Close the file process.
     *
     * @return boolean
     */
    public function close()
    {
        return $this->process->close();
    }

    /**
     * Set the parent element if there is one.
     *
     * @return void
     */
    public function setParentIfAvailable()
    {
        if (! $this->process->readInnerXML()) {
            return;
        }

        $this->parent = $this->name();
    }

    /**
     * Check if the current element matches the specified name.
     *
     * @param  string  $element
     * @return boolean
     */
    public function is($element)
    {
        return (boolean) $this->isElement() and strtolower($this->name()) === strtolower($element);
    }

    /**
     * Get the name of the current node.
     *
     * @return string
     */
    public function name()
    {
        return $this->process->name;
    }

    /**
     * Get the name of the current node.
     *
     * @return string
     */
    public function type()
    {
        return $this->process->nodeType;
    }

    /**
     * Get the value of the current node.
     *
     * @return mixed
     */
    public function value()
    {
        return $this->process->value;
    }

    /**
     * Get the name of the parent element, if there is one.
     *
     * @param  string  $element  null
     * @return boolean|string
     */
    public function parent($element = null)
    {
        if ($element) {
            return strtolower($this->parent) === strtolower($element);
        }

        return $this->parent;
    }

    /**
     * Get the value of a specified attribute of the current node.
     *
     * @param  string  $attribute
     * @return mixed
     */
    public function attribute($attribute)
    {
        return $this->castify(
            $this->process->getAttribute($attribute)
        );
    }

    /**
     * Get the values of all the specified attributes of the current node.
     *
     * @return mixed
     */
    public function attributes($attributes = [])
    {
        $attributes = func_num_args() > 1 ? func_get_args() : (array) $attributes;

        $values = [];
        foreach ($attributes as $attribute) {
            if (is_array($attribute)) {
                $values += array_splice($this->attributes($attribute), 0, 1, true);
                continue;
            }

            if (! ($value = $this->attribute($attribute))) {
                continue;
            }

            $values[$attribute] = $value;
        }

        return $values;
    }

    /**
     * Check if the current node is an element.
     *
     * @var boolean
     */
    public function isElement()
    {
        return (boolean) ($this->type() === XMLReader::ELEMENT);
    }

    /**
     * Check if the current node is an attribute.
     *
     * @var boolean
     */
    public function isAttribute()
    {
        return (boolean) ($this->type() === XMLReader::ATTRIBUTE);
    }

    /**
     * Check if the current node is a comment.
     *
     * @var boolean
     */
    public function isComment()
    {
        return (boolean) ($this->type() === XMLReader::COMMENT);
    }

    /**
     * Convert a string value into its appropriate cast type.
     *
     * @param  string  $value
     * @return mixed
     */
    private function castify($value)
    {
        if (preg_match('/^(\-?[0-9]+)$/', $value)) {
            return (int) $value;
        }

        return $value;
    }
}
