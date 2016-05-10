<?php

namespace pandaac\Exporter\Parsers;

use pandaac\Exporter\Parser;
use Illuminate\Support\Collection;
use pandaac\Exporter\Contracts\Reader;

class Commands extends Parser
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
        // Command information
        if ($iteration = $this->command($reader)) {
            $collection->push($iteration);
        }

        return $collection;
    }

    /**
     * Parse the command information from the commands file.
     *
     * @param  \pandaac\Exporter\Contracts\Reader  $reader
     * @return \Illuminate\Support\Collection
     */
    protected function command(Reader $reader)
    {
        if (! $reader->is('command')) {
            return false;
        }

        return new Collection(
            $reader->attributes('cmd', 'group', 'acctype', 'log')
        );
    }
}
