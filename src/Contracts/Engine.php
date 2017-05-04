<?php

namespace pandaac\Exporter\Contracts;

interface Engine
{
    /**
     * Open a source resource.
     *
     * @param  \pandaac\Exporter\Contracts\Source|string  $source
     * @return void
     */
    public function open($source);

    /**
     * Read and parse the source.
     *
     * @return \pandaac\Exporter\Output
     */
    public function output();

    /**
     * Close the opened source resource.
     *
     * @return void
     */
    public function close();

    /**
     * Get the source data or path.
     *
     * @return string
     */
    public function getSource();
}
