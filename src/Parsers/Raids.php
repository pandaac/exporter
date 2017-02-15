<?php

namespace pandaac\Exporter\Parsers;

use pandaac\Exporter\Output;
use pandaac\Exporter\Exporter;
use pandaac\Exporter\Engines\XML;
use Illuminate\Support\Collection;
use pandaac\Exporter\Contracts\Parser as Contract;

class Raids implements Contract
{
    /** 
     * Get the relative file path.
     *
     * @return string
     */
    public function filePath()
    {
        return '/data/raids/raids.xml';
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
        $raids = $output->first()->get('raid', new Collection);

        if (! isset($attributes['recursive']) or $attributes['recursive'] !== true) {
            return $raids;
        }

        return $raids->each(function ($monster) use ($exporter) {
            try {
                if ($monster->has('file')) {
                    $response = $exporter->parse(new Raid, [], $monster->get('file'));

                    $monster->put('details', $response);
                }
            } catch (Exception $e) {
                // 
            }
        });
    }
}
