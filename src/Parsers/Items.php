<?php

namespace pandaac\Exporter\Parsers;

use pandaac\Exporter\Parser;
use Illuminate\Support\Collection;
use pandaac\Exporter\Contracts\Reader;

class Items extends Parser
{
    /**
     * Holds all of the default options.
     *
     * @var array
     */
    protected $options = [
        'attributes' => false,
        'properties' => false,
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
        // Item information
        if ($iteration = $this->item($reader)) {
            $collection->push($iteration);
        }

        if (! ($reader->isElement() or $reader->isAttribute())) {
            return $collection;
        }

        if ($this->enabled('attributes')) {
            // Item attributes
            if ($iteration = $this->itemAttributes($reader)) {
                $item = $collection->last();

                if (! $item->get('attributes')) {
                    $item->put('attributes', new Collection);
                }

                $item->get('attributes')->push($iteration);

                return $collection;
            }

            if ($this->enabled('properties')) {
                // Item attribute properties
                if ($iteration = $this->itemAttributeProperties($reader)) {
                    $attribute = $collection->last()->get('attributes')->last();

                    if (! $attribute->get('properties')) {
                        $attribute->put('properties', new Collection);
                    }

                    $attribute->get('properties')->push($iteration);

                    return $collection;
                }
            }
        }
        
        return $collection;
    }

    /**
     * Parse the item information from the items file.
     *
     * @param  \pandaac\Exporter\Contracts\Reader  $reader
     * @return \Illuminate\Support\Collection
     */
    protected function item(Reader $reader)
    {
        if (! $reader->is('item')) {
            return false;
        }

        return new Collection(
            $reader->attributes('id', 'fromid', 'toid', 'name')
        );
    }

    /**
     * Parse the item attributes information from the items file.
     *
     * @param  \pandaac\Exporter\Contracts\Reader  $reader
     * @return \Illuminate\Support\Collection
     */
    protected function itemAttributes(Reader $reader)
    {
        if (! ($reader->is('attribute') and $reader->parent('item'))) {
            return false;
        }

        return new Collection(
            $reader->attributes('key', 'value')
        );
    }

    /**
     * Parse the item attribute properties information from the items file.
     *
     * @param  \pandaac\Exporter\Contracts\Reader  $reader
     * @return \Illuminate\Support\Collection
     */
    protected function itemAttributeProperties(Reader $reader)
    {
        if (! ($reader->is('attribute') and $reader->parent('attribute'))) {
            return false;
        }

        return new Collection(
            $reader->attributes('key', 'value')
        );
    }
}
