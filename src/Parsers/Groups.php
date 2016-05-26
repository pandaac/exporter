<?php

namespace pandaac\Exporter\Parsers;

use pandaac\Exporter\Parser;
use Illuminate\Support\Collection;
use pandaac\Exporter\Contracts\Reader;

class Groups extends Parser
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
        // Group information
        if ($iteration = $this->group($reader)) {
            $collection->push($iteration);
        }

        return $collection;
    }

    /**
     * Parse the group information from the groups file.
     *
     * @param  \pandaac\Exporter\Contracts\Reader  $reader
     * @return \Illuminate\Support\Collection
     */
    protected function group(Reader $reader)
    {
        if (! $reader->is('group')) {
            return false;
        }

        return new Collection(
            $reader->attributes('id', 'name', 'flags', 'access', 'maxdepotitems', 'maxvipentries')
        );
    }
}
