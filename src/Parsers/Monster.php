<?php

namespace pandaac\Exporter\Parsers;

use pandaac\Exporter\Output;
use pandaac\Exporter\Exporter;
use pandaac\Exporter\Engines\XML;
use pandaac\Exporter\Contracts\Parser as Contract;

class Monster implements Contract
{
    /** 
     * Get the relative file path.
     *
     * @return string
     */
    public function filePath()
    {
        return '/data/monster/';
    }

    /**
     * Define plural representations of certain words.
     *
     * @retun array
     */
    public function plural()
    {
        return [
            'defense'   => 'defenses',
            'summon'    => 'summons',
            'voice'     => 'sentences',
        ];
    }

    /**
     * Get the parser engine.
     *
     * @param  array  $attributes
     * @return \pandaac\Exporter\Contracts\Engine
     */
    public function engine(array $attributes)
    {
        return new XML($attributes, $this->plural());
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
        $monster = $output->first();

        if ($monster->has('health')) {
            $monster->put('health', $monster->get('health')->first());
        }

        if ($monster->has('look')) {
            $monster->put('look', $monster->get('look')->first());
        }

        if ($monster->has('targetchange')) {
            $monster->put('targetchange', $monster->get('targetchange')->first());
        }

        if ($monster->has('flags')) {
            $monster->put('flags', $monster->get('flags')->first()->get('flag')->collapse());
        }

        if ($monster->has('attacks')) {
            $monster->put('attacks', $monster->get('attacks')->first()->get('attack'));
        }

        if ($monster->has('defenses')) {
            $monster->put('defenses', $monster->get('defenses')->first());
        }

        if ($monster->has('elements') and $monster->get('elements')->first()->has('element')) {
            $monster->put('elements', $monster->get('elements')->first()->get('element')->collapse());
        }

        if ($monster->has('immunities') and $monster->get('immunities')->first()->has('immunity')) {
            $monster->put('immunities', $monster->get('immunities')->first()->get('immunity')->collapse());
        }

        if ($monster->has('summons')) {
            $monster->put('summons', $monster->get('summons')->first());
        }

        if ($monster->has('voices') and $monster->get('voices')->first()->has('sentences')) {
            $monster->put('voices', $monster->get('voices')->first());
            $monster->get('voices')->put('sentences', $monster->get('voices')->get('sentences')->flatten());
        }
        
        if ($monster->has('loot')) {
            $monster->put('loot', $monster->get('loot')->first()->get('item'));
        }

        return $monster;
    }
}
