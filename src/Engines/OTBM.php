<?php

namespace pandaac\Exporter\Engines;

use Exception;
use pandaac\Exporter\Contracts\Engine as Contract;

class OTBM implements Contract
{
    /**
     * Open a file resource.
     *
     * @param  string  $file
     * @return void
     */
    public function open($file)
    {
        throw new Exception('The OTBM engine has not yet been developed.');
    }

    /**
     * Read and parse the file.
     *
     * @return \pandaac\Exporter\Output
     */
    public function output()
    {
        //
    }

    /**
     * Close the opened file resource.
     *
     * @return void
     */
    public function close()
    {
        //
    }

    /**
     * Get the absolute file path.
     *
     * @return string
     */
    public function getFile()
    {
        //
    }
}
