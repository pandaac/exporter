<?php

namespace pandaac\Exporter\Contracts;

interface Parser
{
    /**
     * Instantiate the parser object.
     *
     * @param  array  $options  []
     * @return void
     */
    public function __construct(array $options = []);

    /**
     * Return the reader implementation.
     *
     * @return \pandaac\Exporter\Contracts\Reader
     */
    public function getReader();

    /**
     * Set the reader implementation.
     *
     * @param  \pandaac\Exporter\Contracts\Reader  $reader
     * @return void
     */
    public function setReader(Reader $reader);

    /**
     * Parse the specified file.
     *
     * @param  string  $file
     * @return \Illuminate\Support\Collection
     */
    public function parse($file);
}
