<?php

namespace pandaac\Exporter\Parsers;

use pandaac\Exporter\Parser;
use Illuminate\Support\Collection;
use pandaac\Exporter\Contracts\Reader;

class Monsters extends Parser
{
    /**
     * Holds all of the default options.
     *
     * @var array
     */
    protected $options = [
        'recursion' => true,
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
        if (! $reader->is('monster')) {
            return false;
        }

        // Monster information
        if ($iteration = $this->information($reader)) {
            $collection->push($iteration);
        }

        // Monster details
        if ($this->enabled('recursion') and $iteration = $this->details($reader, $collection)) {
            $monster = $collection->pop();

            return $collection->push($iteration->put('paths', $monster));
        }

        return $collection;
    }

    /**
     * Parse the basic information from the monster file.
     *
     * @param  \pandaac\Exporter\Contracts\Reader  $reader
     * @return \Illuminate\Support\Collection
     */
    protected function information(Reader $reader)
    {
        $attributes = $reader->attributes('name', 'file');

        return new Collection(
            array_merge($attributes, [
                'path' => realpath(dirname($this->file).'/'.$attributes['file']),
            ])
        );
    }

    /**
     * Parse the detailed information from the monster file.
     *
     * @param  \pandaac\Exporter\Contracts\Reader  $reader
     * @param  \Illuminate\Support\Collection  $collection
     * @return \pandaac\Exporter\Parsers\Monster
     */
    protected function details(Reader $reader, Collection $collection)
    {
        $monster = $collection->last();

        if (! ($path = $monster->get('path'))) {
            return false;
        }

        return $this->parseMonster($reader, $path);
    }

    /**
     * Parse an invidiual monster file.
     *
     * @param  \pandaac\Exporter\Contracts\Reader  $reader
     * @param  string  $path
     * @return \pandaac\Exporter\Parsers\Monster
     */
    private function parseMonster(Reader $reader, $path)
    {
        $parser = new Monster($this->options);

        $readerClass = get_class($reader);

        $parser->setReader(new $readerClass);

        return $parser->parse($path);
    }
}
