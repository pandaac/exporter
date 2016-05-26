<?php

namespace pandaac\Exporter\Parsers;

use pandaac\Exporter\Parser;
use Illuminate\Support\Collection;
use pandaac\Exporter\Contracts\Reader;

class Quests extends Parser
{
    /**
     * Holds all of the default options.
     *
     * @var array
     */
    protected $options = [
        'missions' => true,
        'states' => true,
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
        // Quest information
        if ($reader->is('quest') and $iteration = $this->quest($reader)) {
            $collection->push($iteration);
        }

        // Quest missions
        if ($this->enabled('missions')) {
            $quest = $collection->last();

            // Mission information
            if ($iteration = $this->mission($reader)) {
                $quest->last()->push($iteration);
            }

            // Mission states
            if ($this->enabled('states')) {
                if ($iteration = $this->missionState($reader)) {
                    $quest->get('missions')->last()->get('states')->put(
                        $iteration->get('id'), $iteration->get('description')
                    );
                }
            }
        }

        return $collection;
    }

    /**
     * Parse the quest information from the quests file.
     *
     * @param  \pandaac\Exporter\Contracts\Reader  $reader
     * @return \Illuminate\Support\Collection
     */
    protected function quest(Reader $reader)
    {
        $attributes = $reader->attributes('name', 'startstorageid', 'startstoragevalue');

        return new Collection(
            $this->enabled('missions') ? array_merge($attributes, ['missions' => new Collection]) : $attributes
        );
    }

    /**
     * Parse the mission information from the quests file.
     *
     * @param  \pandaac\Exporter\Contracts\Reader  $reader
     * @return \Illuminate\Support\Collection
     */
    protected function mission(Reader $reader)
    {
        if (! $reader->is('mission')) {
            return false;
        }

        $attributes = $reader->attributes('name', 'description', 'storageid', 'startvalue', 'endvalue', 'ignoreendvalue');

        return new Collection(
            $this->enabled('states') ? array_merge($attributes, ['states' => new Collection]) : $attributes
        );
    }

    /**
     * Parse the mission states from the quests file.
     *
     * @param  \pandaac\Exporter\Contracts\Reader  $reader
     * @return \Illuminate\Support\Collection
     */
    protected function missionState(Reader $reader)
    {
        if (! ($reader->is('missionstate') and $reader->parent('mission'))) {
            return false;
        }

        return new Collection(
            $reader->attributes('id', 'description')
        );
    }
}
