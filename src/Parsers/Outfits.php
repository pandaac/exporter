<?php

namespace pandaac\Exporter\Parsers;

use pandaac\Exporter\Parser;
use Illuminate\Support\Collection;
use pandaac\Exporter\Contracts\Reader;

class Outfits extends Parser
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
        if ($iteration = $this->outfit($reader)) {
            $collection->push($iteration);
        }

        return $collection;
    }

    /**
     * Parse the outfit information from the outfits file.
     *
     * @param  \pandaac\Exporter\Contracts\Reader  $reader
     * @return \Illuminate\Support\Collection
     */
    protected function outfit(Reader $reader)
    {
        if (! $reader->is('outfit')) {
            return false;
        }

        return new Collection(
            $reader->attributes('type', 'looktype', 'name', 'premium', 'unlocked', 'enabled')
        );
    }
}
