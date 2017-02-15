<?php

namespace pandaac\Exporter\Contracts;

interface Engine
{
    /**
     * Open a file resource.
     *
     * @param  string  $file
     * @return void
     */
    public function open($file);

    /**
     * Read and parse the file.
     *
     * @return \pandaac\Exporter\Output
     */
    public function output();

    /**
     * Close the opened file resource.
     *
     * @return void
     */
    public function close();

    /**
     * Get the absolute file path.
     *
     * @return string
     */
    public function getFile();
}
