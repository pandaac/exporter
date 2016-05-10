<?php

namespace pandaac\Exporter;

use pandaac\Exporter\Reader;
use pandaac\Exporter\Contracts;
use Illuminate\Support\Collection;
use pandaac\Exporter\Contracts\Parser as Contract;

abstract class Parser implements Contract
{
    /**
     * Holds the reader implementation.
     *
     * @var \pandaac\Exporter\Contracts\Reader
     */
    protected $reader;

    /**
     * Instantiate the parser object.
     *
     * @param  array  $options  []
     * @return void
     */
    public function __construct(array $options = [])
    {
        $this->extendOptions($options);

        $this->setReader(new Reader);
    }

    /**
     * Return the reader implementation.
     *
     * @return \pandaac\Exporter\Contracts\Reader
     */
    public function getReader()
    {
        return $this->reader;
    }

    /**
     * Set the reader implementation.
     *
     * @param  \pandaac\Exporter\Contracts\Reader  $reader
     * @return void
     */
    public function setReader(Contracts\Reader $reader)
    {
        $this->reader = $reader;
    }

    /**
     * Parse the specified file.
     *
     * @param  string  $file
     * @return \Illuminate\Support\Collection
     */
    public function parse($file)
    {
        $reader = $this->getReader();

        $reader->open($this->file = $file);

        $collection = $this->newCollection();

        do {

            $this->iteration($reader, $collection);

            $reader->setParentIfAvailable();

        } while ($reader->read());

        $reader->close();

        return $collection;
    }

    /**
     * Extend the default options with the custom ones.
     *
     * @param  array  $options
     * @return array
     */
    protected function extendOptions(array $options)
    {
        if (! $options or ! property_exists($this, 'options')) {
            return null;
        }

        $this->options = array_merge($this->options, $options);
    }

    /**
     * Get the value of the specified option.
     *
     * @param  string  $option
     * @return mixed
     */
    protected function option($option)
    {
        if (! isset($this->options[$option])) {
            return null;
        }

        return $this->options[$option];
    }

    /**
     * Check if the specified option is enabled.
     *
     * @param  string  $option
     * @return boolean
     */
    protected function enabled($option)
    {
        return isset($this->options[$option]) and $this->options[$option];
    }

    /**
     * Check if the specified option is disabled.
     *
     * @param  string  $option
     * @return boolean
     */
    protected function disabled($option)
    {
        return ! $this->enabled($option);
    }

    /**
     * Create a new collection object.
     *
     * @return \Illuminate\Support\Collection
     */
    protected function newCollection()
    {
        return new Collection;
    }

    /**
     * Handle every iteration of the parsing process.
     *
     * @param  \pandaac\Exporter\Contracts\Reader  $reader
     * @param  \Illuminate\Support\Collection  $collection
     * @return boolean
     */
    abstract public function iteration(Contracts\Reader $reader, Collection $collection);
}
