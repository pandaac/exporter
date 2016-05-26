<?php

namespace pandaac\Exporter\Parsers;

use pandaac\Exporter\Parser;
use Illuminate\Support\Collection;
use pandaac\Exporter\Contracts\Reader;

class Mounts extends Parser
{
    /**
     * Handle every iteration of the parsing process.
     *
     * @param  \pandaac\Exporter\Contracts\Reader  $reader
     * @param  \Illuminate\Support\Collection  $collection
     * @return \Illuminate\Support\Collection
     */
    public function iteration(Reader $reader, Collection $collection)
    {
        // Mount information
        if ($iteration = $this->mount($reader)) {
            $collection->push($iteration);
        }

        return $collection;
    }

    /**
     * Parse the mount information from the mounts file.
     *
     * @param  \pandaac\Exporter\Contracts\Reader  $reader
     * @return \Illuminate\Support\Collection
     */
    protected function mount(Reader $reader)
    {
        if (! $reader->is('mount')) {
            return false;
        }

        return new Collection(
            $reader->attributes('id', 'clientid', 'name', 'speed', 'premium')
        );
    }
}
