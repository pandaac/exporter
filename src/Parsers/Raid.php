<?php

namespace pandaac\Exporter\Parsers;

use pandaac\Exporter\Output;
use pandaac\Exporter\Exporter;
use pandaac\Exporter\Engines\XML;
use pandaac\Exporter\Contracts\Parser as Contract;

class Raid implements Contract
{
    /** 
     * Get the relative file path.
     *
     * @return string
     */
    public function filePath()
    {
        return '/data/raids/';
    }

    /**
     * Get the parser engine.
     *
     * @param  \pandaac\Exporter\Exporter  $exporter
     * @param  array  $attributes
     * @return \pandaac\Exporter\Contracts\Engine
     */
    public function engine(Exporter $exporter, array $attributes)
    {
        return new XML($exporter, $attributes);
    }

    /**
     * Parse the file.
     *
     * @param  \pandaac\Exporter\Exporter  $exporter
     * @param  \pandaac\Exporter\Output  $output
     * @param  array  $attributes
     * @return \Illuminate\Support\Collection
     */
    public function parse(Exporter $exporter, Output $output, array $attributes)
    {
        return $output->first();
    }
}
