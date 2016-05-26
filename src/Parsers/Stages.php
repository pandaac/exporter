<?php

namespace pandaac\Exporter\Parsers;

use pandaac\Exporter\Parser;
use Illuminate\Support\Collection;
use pandaac\Exporter\Contracts\Reader;

class Stages extends Parser
{
    /**
     * Holds all of the predefined sections.
     */
    protected $sections = ['stages'];

    /**
     * Create a new collection object.
     *
     * @return \Illuminate\Support\Collection
     */
    protected function newCollection()
    {
        $collection = new Collection;

        foreach ($this->sections as $section) {
            $collection->put($section, new Collection);
        }

        return $collection;
    }

    /**
     * Handle every iteration of the parsing process.
     *
     * @param  \pandaac\Exporter\Contracts\Reader  $reader
     * @param  \Illuminate\Support\Collection  $collection
     * @return \Illuminate\Support\Collection
     */
    public function iteration(Reader $reader, Collection $collection)
    {
        // Stage configuration
        if ($reader->is('config')) {
            return $this->isEnabled($reader, $collection);
        }

        // Stage information
        if ($iteration = $this->stage($reader)) {
            $stages = $collection->get('stages')->push($iteration);

            return $collection->put('stages', $stages);
        }

        return $collection;
    }

    /**
     * Parse the status information from the stages file.
     *
     * @param  \pandaac\Exporter\Contracts\Reader  $reader
     * @param  \Illuminate\Support\Collection  $collection
     * @return \Illuminate\Support\Collection
     */
    protected function isEnabled(Reader $reader, Collection $collection)
    {
        return $collection->prepend((boolean) $reader->attribute('enabled'), 'enabled');
    }

    /**
     * Parse the stage information from the stages file.
     *
     * @param  \pandaac\Exporter\Contracts\Reader  $reader
     * @return \Illuminate\Support\Collection
     */
    protected function stage(Reader $reader)
    {
        if (! $reader->is('stage')) {
            return false;
        }

        return new Collection(
            $reader->attributes('minlevel', 'maxlevel', 'multiplier')
        );
    }
}
