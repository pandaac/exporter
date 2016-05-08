<?php

namespace pandaac\Exporter\Contracts;

interface Parser
{
    /**
     * Instantiate a new parser object.
     *
     * @param  string  $file
     * @return void
     */
    public function __construct($file);

    /**
     * Parse the file.
     *
     * @param  array  $settings  []
     * @return mixed
     */
    public function parse(array $settings = []);
}
