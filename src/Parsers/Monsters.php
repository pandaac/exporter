<?php

namespace pandaac\Exporter\Parsers;

use Exception;
use pandaac\Exporter\Output;
use pandaac\Exporter\Exporter;
use pandaac\Exporter\Engines\XML;
use Illuminate\Support\Collection;
use pandaac\Exporter\Contracts\Parser as Contract;

class Monsters implements Contract
{
    /** 
     * Get the relative file path.
     *
     * @return string
     */
    public function filePath()
    {
        return '/data/monster/monsters.xml';
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
        $monsters = $output->first()->get('monster', new Collection);

        if (! isset($attributes['recursive']) or $attributes['recursive'] !== true) {
            return $monsters;
        }

        return $monsters->each(function ($monster) use ($exporter) {
            if ($monster->has('file')) {
                $response = $exporter->parse(new Monster, [], $monster->get('file'));

                $monster->put('details', $response);
            }
        });
    }
}
