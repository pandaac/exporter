<?php

namespace pandaac\Exporter\Parsers;

use pandaac\Exporter\Parser;
use Illuminate\Support\Collection;
use pandaac\Exporter\Contracts\Reader;

class Spells extends Parser
{
    /**
     * Holds all of the default options.
     *
     * @var array
     */
    protected $options = [
        'instant'   => true,
        'runes'     => true,
        'conjure'   => true,
    ];

    /**
     * Holds all of the predefined sections.
     */
    protected $sections = [
        'instant', 'runes', 'conjure',
    ];

    /**
     * Create a new collection object.
     *
     * @return \Illuminate\Support\Collection
     */
    protected function newCollection()
    {
        $collection = new Collection;

        foreach ($this->sections as $section) {
            if ($this->disabled($section)) {
                continue;
            }

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
        // Instant spells
        if ($this->enabled('instant')) {
            if ($iteration = $this->instant($reader)) {
                $collection->get('instant')->push($iteration);
            }

            // Instant spell vocations
            if ($iteration = $this->instantVocations($reader)) {
                $collection->get('instant')->last()->get('vocations')->push(
                    $iteration->get('name')
                );
            }
        }

        // Rune spells
        if ($this->enabled('runes')) {
            if ($iteration = $this->rune($reader)) {
                $collection->get('runes')->push($iteration);
            }

            // Rune spell vocations
            if ($iteration = $this->runeVocations($reader)) {
                $collection->get('runes')->last()->get('vocations')->push(
                    $iteration->get('name')
                );
            }
        }

        // Conjure spells
        if ($this->enabled('conjure')) {
            if ($iteration = $this->conjure($reader)) {
                $collection->get('conjure')->push($iteration);
            }

            // Conjure spell vocations
            if ($iteration = $this->conjureVocations($reader)) {
                $collection->get('conjure')->last()->get('vocations')->push(
                    $iteration->get('name')
                );
            }
        }

        return $collection;
    }

    /**
     * Get the default spell attributes.
     *
     * @param \pandaac\Exporter\Contracts\Reader  $reader
     * @return array
     */
    private function getDefaultSpellAttributes(Reader $reader)
    {
        return $reader->attributes('name', 'script', 'spellid', 'group', 'groupcooldown', 'secondarygroup', 'secondarygroupcooldown', 'lvl', 'maglv', 'mana', 'manapercent', 'soul', 'range', ['exhaustion', 'cooldown'], 'prem', 'enabled', 'needtarget', 'needweapon', 'selftarget', 'needlearn', 'blocking', 'blocktype', 'aggressive');
    }

    /**
     * Parse the instant spells from the spells file.
     *
     * @param  \pandaac\Exporter\Contracts\Reader  $reader
     * @return \Illuminate\Support\Collection
     */
    protected function instant(Reader $reader)
    {
        if (! $reader->is('instant')) {
            return false;
        }

        $attributes = $this->getDefaultSpellAttributes($reader);

        $attributes += $reader->attributes('params', 'playernameparam', ['direction', 'casterTargetOrDirection'], 'blockwalls', 'words', 'separator');
        
        return new Collection(
            array_merge($attributes, ['vocations' => new Collection])
        );
    }

    /**
     * Parse the instant spell vocations from the spells file.
     *
     * @param  \pandaac\Exporter\Contracts\Reader  $reader
     * @return \Illuminate\Support\Collection
     */
    protected function instantVocations(Reader $reader)
    {
        if (! ($reader->is('vocation') and $reader->parent('instant'))) {
            return false;
        }

        return new Collection(
            $reader->attributes('name')
        );
    }

    /**
     * Parse the rune spells from the spells file.
     *
     * @param  \pandaac\Exporter\Contracts\Reader  $reader
     * @return \Illuminate\Support\Collection
     */
    protected function rune(Reader $reader)
    {
        if (! $reader->is('rune')) {
            return false;
        }

        $attributes = $this->getDefaultSpellAttributes($reader);

        $attributes += $reader->attributes('id', 'charges', 'allowfaruse', 'blockwalls', 'checkfloor');
        
        return new Collection(
            array_merge($attributes, ['vocations' => new Collection])
        );
    }

    /**
     * Parse the rune spell vocations from the spells file.
     *
     * @param  \pandaac\Exporter\Contracts\Reader  $reader
     * @return \Illuminate\Support\Collection
     */
    protected function runeVocations(Reader $reader)
    {
        if (! ($reader->is('vocation') and $reader->parent('rune'))) {
            return false;
        }

        return new Collection(
            $reader->attributes('name')
        );
    }

    /**
     * Parse the conjure spells from the spells file.
     *
     * @param  \pandaac\Exporter\Contracts\Reader  $reader
     * @return \Illuminate\Support\Collection
     */
    protected function conjure(Reader $reader)
    {
        if (! $reader->is('conjure')) {
            return false;
        }

        $attributes = $this->getDefaultSpellAttributes($reader);

        $attributes += $reader->attributes('conjureId', 'conjureCount', 'reagentId', 'params', 'playernameparam', ['direction', 'casterTargetOrDirection'], 'blockwalls', 'words', 'separator');
        
        return new Collection(
            array_merge($attributes, ['vocations' => new Collection])
        );
    }

    /**
     * Parse the conjure spell vocations from the spells file.
     *
     * @param  \pandaac\Exporter\Contracts\Reader  $reader
     * @return \Illuminate\Support\Collection
     */
    protected function conjureVocations(Reader $reader)
    {
        if (! ($reader->is('vocation') and $reader->parent('conjure'))) {
            return false;
        }

        return new Collection(
            $reader->attributes('name')
        );
    }
}
