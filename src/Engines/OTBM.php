<?php

namespace pandaac\Exporter\Engines;

use Exception;
use pandaac\Exporter\Exporter;
use pandaac\Exporter\Contracts\Engine as Contract;

class OTBM implements Contract
{
    /**
     * Open a source resource.
     *
     * @param  \pandaac\Exporter\Contracts\Source|string  $source
     * @return void
     */
    public function open($source)
    {
        throw new Exception('The OTBM engine has not yet been developed.');
    }

    /**
     * Read and parse the source.
     *
     * @return \pandaac\Exporter\Output
     */
    public function output()
    {
        //
    }

    /**
     * Close the opened source resource.
     *
     * @return void
     */
    public function close()
    {
        //
    }

    /**
     * Get the source data or path.
     *
     * @return \pandaac\Exporter\Contracts\Source|string
     */
    public function getSource()
    {
        //
    }
}
