<?php

namespace pandaac\Exporter\Parsers;

use pandaac\Exporter\Output;
use pandaac\Exporter\Exporter;
use pandaac\Exporter\Engines\XML;
use Illuminate\Support\Collection;
use pandaac\Exporter\Contracts\Parser as Contract;

class Vocations implements Contract
{
    /** 
     * Get the relative file path.
     *
     * @return string
     */
    public function filePath()
    {
        return '/data/XML/vocations.xml';
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
        $vocations = $output->first()->get('vocation', new Collection);

        $vocations->each(function ($vocation) {
            if ($vocation->has('formula')) {
                $vocation->put('formula', $vocation->get('formula')->first());
            }
        });

        return $vocations;
    }
}
