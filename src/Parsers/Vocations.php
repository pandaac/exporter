<?php

namespace pandaac\Exporter\Parsers;

use pandaac\Exporter\Parser;
use Illuminate\Support\Collection;
use pandaac\Exporter\Contracts\Reader;

class Vocations extends Parser
{
    /**
     * Holds all of the default options.
     *
     * @var array
     */
    protected $options = [
        'formula'   => true,
        'skills'    => true,
    ];

    /**
     * Handle every iteration of the parsing process.
     *
     * @param  \pandaac\Exporter\Contracts\Reader  $reader
     * @param  \Illuminate\Support\Collection  $collection
     * @return \Illuminate\Support\Collection
     */
    public function iteration(Reader $reader, Collection $collection)
    {
        // Vocation information
        if ($reader->is('vocation') and $iteration = $this->vocation($reader)) {
            $collection->push($iteration);
        }

        $vocation = $collection->last();

        // Vocation formula
        if ($this->enabled('formula') and $formula = $this->formula($reader)) {
            $vocation->put('formula', $formula);
        }

        // Vocation skills
        if ($this->enabled('skills') and $skill = $this->skill($reader)) {
            $vocation->get('skills')->push($skill);
        }

        return $collection;
    }

    /**
     * Parse the vocation information from the vocations file.
     *
     * @param  \pandaac\Exporter\Contracts\Reader  $reader
     * @return \Illuminate\Support\Collection
     */
    protected function vocation(Reader $reader)
    {
        $attributes = $reader->attributes('id', 'name', 'clientid', 'description', 'gaincap', 'gainhp', 'gainmana', 'gainhpticks', 'gainhpamount', 'gainmanaticks', 'gainmanaamount', 'manamultiplier', 'attackspeed', 'basespeed', 'soulmax', 'gainsoulticks', 'fromvoc');

        return new Collection(
            array_merge($attributes, array_filter([
                'formula'   => $this->enabled('formula') ? new Collection : [], 
                'skills'    => $this->enabled('skills') ? new Collection : [],
            ]))
        );
    }

    /**
     * Parse the vocation skills from the vocations file.
     *
     * @param  \pandaac\Exporter\Contracts\Reader  $reader
     * @return \Illuminate\Support\Collection
     */
    protected function skill(Reader $reader)
    {
        if (! ($reader->is('skill') and $reader->parent('vocation'))) {
            return false;
        }

        return new Collection(
            $reader->attributes('id', 'multiplier')
        );
    }

    /**
     * Parse the vocation formula from the vocations file.
     *
     * @param  \pandaac\Exporter\Contracts\Reader  $reader
     * @return \Illuminate\Support\Collection
     */
    protected function formula(Reader $reader)
    {
        if (! ($reader->is('formula') and $reader->parent('vocation'))) {
            return false;
        }

        return new Collection(
            $reader->attributes('meleeDamage', 'distDamage', 'defense', 'armor')
        );
    }
}
