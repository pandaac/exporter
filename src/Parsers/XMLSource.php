<?php

namespace pandaac\Exporter\Parsers;

use pandaac\Exporter\Output;
use pandaac\Exporter\Exporter;
use pandaac\Exporter\Engines\XML;
use Illuminate\Support\Collection;
use pandaac\Exporter\Contracts\Source;
use pandaac\Exporter\Contracts\Parser as Contract;

class XMLSource implements Contract
{
    /**
     * Instantiate a new XML Source parser.
     *
     * @param  \pandaac\Exporter\Contracts\Source  $source
     */
    public function __construct(Source $source)
    {
        $this->source = $source;
    }

    /** 
     * Get the relative file path.
     *
     * @return \pandaac\Exporter\Contracts\Source
     */
    public function filePath()
    {
        return $this->source;
    }

    /**
     * Get the parser engine.
     *
     * @param  array  $attributes
     * @return \pandaac\Exporter\Contracts\Engine
     */
    public function engine(array $attributes)
    {
        return new XML($attributes);
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
        return $output;
    }
}
