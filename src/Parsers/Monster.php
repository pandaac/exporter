<?php

namespace pandaac\Exporter\Parsers;

use pandaac\Exporter\Parser;
use Illuminate\Support\Collection;
use pandaac\Exporter\Contracts\Reader;

class Monster extends Parser
{
    /**
     * Holds all of the default options.
     *
     * @var array
     */
    protected $options = [
        'health'        => true,
        'look'          => true,
        'targetchange'  => false,
        'flags'         => false,
        'attacks'       => false,
        'defenses'      => false,
        'elements'      => false,
        'immunities'    => false,
        'voices'        => false,
        'summons'       => false,
        'loot'          => true,
    ];

    /**
     * Holds all of the predefined sections.
     */
    protected $sections = [
        'health', 'look', 'targetchange', 'flags', 'attacks', 'defenses', 
        'elements', 'immunities', 'voices', 'summons', 'loot',
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
        // Monster details
        if ($reader->is('monster')) {
            return $this->details($reader, $collection);
        }

        if (! ($reader->isElement() or $reader->isAttribute() or $reader->isComment())) {
            return $collection;
        }

        // Monster health
        if ($this->enabled('health') and $iteration = $this->health($reader)) {
            return $collection->put('health', $iteration);
        }

        // Monster look
        if ($this->enabled('look') and $iteration = $this->look($reader)) {
            return $collection->put('look', $iteration);
        }

        // Monster target change
        if ($this->enabled('targetchange') and $iteration = $this->targetchange($reader)) {
            return $collection->put('targetchange', $iteration);
        }

        // Monster flags
        if ($this->enabled('flags') and $iteration = $this->flags($reader)) {
            $flags = $collection->get('flags')->merge($iteration);

            return $collection->put('flags', $flags);
        }

        // Monster attacks
        if ($this->enabled('attacks')) {
            $attacks = $collection->get('attacks');

            // Attacks
            if ($iteration = $this->attacks($reader)) {
                return $collection->put('attacks', $attacks->push($iteration));
            }

            // Attack attributes
            if ($iteration = $this->attackAttributes($reader)) {
                $attacks->last()->get('attributes')->put(
                    $iteration->get('key'), $iteration->get('value')
                );

                return $collection->put('attacks', $attacks);
            }
        }

        // Monster defenses
        if ($this->enabled('defenses')) {
            // Statistics
            if ($iteration = $this->defenseStatistics($reader)) {
                return $collection->put('defenses', $iteration);
            }
            
            $statistics = $collection->get('defenses');

            // Defenses
            if ($iteration = $this->defenses($reader)) {
                $statistics->get('defenses')->push($iteration);

                return $collection->put('defenses', $statistics);
            }

            // Defense attributes
            if ($iteration = $this->defenseAttributes($reader)) {
                $statistics->get('defenses')->last()->get('attributes')->put(
                    $iteration->get('key'), $iteration->get('value')
                );

                return $collection->put('defenses', $statistics);
            }
        }

        // Monster elements
        if ($this->enabled('elements') and $iteration = $this->elements($reader)) {
            $elements = $collection->get('elements')->merge($iteration);

            return $collection->put('elements', $elements);
        }

        // Monster immunities
        if ($this->enabled('immunities') and $iteration = $this->immunities($reader)) {
            $immunities = $collection->get('immunities')->merge($iteration);

            return $collection->put('immunities', $immunities);
        }

        // Monster voices
        if ($this->enabled('voices')) {
            // Statistics
            if ($iteration = $this->voices($reader)) {
                return $collection->put('voices', $iteration);
            }

            // Sentences
            if ($iteration = $this->voiceSentences($reader)) {
                $voices = $collection->get('voices');
                
                $voices->get('sentences')->push($iteration['sentence']);

                return $collection->put('voices', $voices);
            }
        }

        // Monster summons
        if ($this->enabled('summons')) {
            // Statistics
            if ($iteration = $this->summonStatistics($reader)) {
                return $collection->put('summons', $iteration);
            }

            // Summons
            if ($iteration = $this->summons($reader)) {
                $summons = $collection->get('summons');
                
                $summons->get('summons')->push($iteration);

                return $collection->put('summons', $summons);
            }
        }

        // Loot
        if ($this->enabled('loot')) {
            $loot = $collection->get('loot');

            // Items
            if ($iteration = $this->loot($reader)) {
                return $collection->put('loot', $loot->push($iteration));
            }

            // Possible item comments (usually item names, not reliable though)
            if ($iteration = $this->lootComments($reader)) {
                $loot->last()->put('comment', $iteration);

                return $collection->put('loot', $loot);
            }
        }
    }

    /**
     * Parse the detailed information from the monster file.
     *
     * @param  \pandaac\Exporter\Contracts\Reader  $reader
     * @param  \Illuminate\Support\Collection  $collection
     * @return \Illuminate\Support\Collection
     */
    protected function details(Reader $reader, Collection $collection)
    {
        $attributes = $reader->attributes('name', 'nameDescription', 'race', 'experience', 'speed', 'manacost', 'skull');

        foreach (array_reverse($attributes) as $attribute => $value) {
            $collection->prepend($value, $attribute);
        }

        return $collection;
    }

    /**
     * Parse the health information from the monster file.
     *
     * @param  \pandaac\Exporter\Contracts\Reader  $reader
     * @return \Illuminate\Support\Collection
     */
    protected function health(Reader $reader)
    {
        if (! $reader->is('health')) {
            return false;
        }

        return new Collection(
            $reader->attributes('now', 'max')
        );
    }

    /**
     * Parse the look information from the monster file.
     *
     * @param  \pandaac\Exporter\Contracts\Reader  $reader
     * @return \Illuminate\Support\Collection
     */
    protected function look(Reader $reader)
    {
        if (! $reader->is('look')) {
            return false;
        }

        return new Collection(
            $reader->attributes('type', 'typeex', 'head', 'body', 'legs', 'feet', 'addons', 'mount', 'corpse')
        );
    }

    /**
     * Parse the targetchange information from the monster file.
     *
     * @param  \pandaac\Exporter\Contracts\Reader  $reader
     * @return \Illuminate\Support\Collection
     */
    protected function targetchange(Reader $reader)
    {
        if (! $reader->is('targetchange')) {
            return false;
        }

        return new Collection(
            $reader->attributes(['speed', 'interval'], 'chance')
        );
    }

    /**
     * Parse the flags information from the monster file.
     *
     * @param  \pandaac\Exporter\Contracts\Reader  $reader
     * @return \Illuminate\Support\Collection
     */
    protected function flags(Reader $reader)
    {
        if (! $reader->is('flag')) {
            return false;
        }

        return new Collection(
            $reader->attributes('summonable', 'attackable', 'hostile', 'illusionable', 'convinceable', 'pushable', 'canpushitems', 'canpushcreatures', 'staticattack', 'lightlevel', 'lightcolor', 'targetdistance', 'runonhealth', 'hidehealth')
        );
    }

    /**
     * Parse the attacks information from the monster file.
     *
     * @param  \pandaac\Exporter\Contracts\Reader  $reader
     * @return \Illuminate\Support\Collection
     */
    protected function attacks(Reader $reader)
    {
        if (! $reader->is('attack')) {
            return false;
        }

        $attributes = $reader->attributes('name', 'interval', 'min', 'max', 'chance', 'range', 'radius', 'target', 'skill', 'attack', 'speedchange', 'duration');

        return new Collection(
            array_merge($attributes, ['attributes' => new Collection])
        );
    }

    /**
     * Parse the attack attributes information from the monster file.
     *
     * @param  \pandaac\Exporter\Contracts\Reader  $reader
     * @return \Illuminate\Support\Collection
     */
    protected function attackAttributes(Reader $reader)
    {
        if (! ($reader->is('attribute') and $reader->parent('attack'))) {
            return false;
        }

        return new Collection(
            $reader->attributes('key', 'value')
        );
    }

    /**
     * Parse the defense statistics attributes information from the monster file.
     *
     * @param  \pandaac\Exporter\Contracts\Reader  $reader
     * @return \Illuminate\Support\Collection
     */
    protected function defenseStatistics(Reader $reader)
    {
        if (! $reader->is('defenses')) {
            return false;
        }

        $attributes = $reader->attributes('armor', 'defense');

        return new Collection(
            array_merge($attributes, ['defenses' => new Collection])
        );
    }

    /**
     * Parse the defenses information from the monster file.
     *
     * @param  \pandaac\Exporter\Contracts\Reader  $reader
     * @return \Illuminate\Support\Collection
     */
    protected function defenses(Reader $reader)
    {
        if (! $reader->is('defense')) {
            return false;
        }

        $attributes = $reader->attributes('name', 'interval', 'min', 'max', 'chance', 'range', 'radius', 'target', 'skill', 'attack', 'speedchange', 'duration');

        return new Collection(
            array_merge($attributes, ['attributes' => new Collection])
        );
    }

    /**
     * Parse the defense attributes information from the monster file.
     *
     * @param  \pandaac\Exporter\Contracts\Reader  $reader
     * @return \Illuminate\Support\Collection
     */
    protected function defenseAttributes(Reader $reader)
    {
        if (! ($reader->is('attribute') and $reader->parent('defense'))) {
            return false;
        }

        return new Collection(
            $reader->attributes('key', 'value')
        );
    }

    /**
     * Parse the elements information from the monster file.
     *
     * @param  \pandaac\Exporter\Contracts\Reader  $reader
     * @return \Illuminate\Support\Collection
     */
    protected function elements(Reader $reader)
    {
        if (! $reader->is('element')) {
            return false;
        }

        $attributes = $reader->attributes('physicalPercent', 'icePercent', ['poisonPercent', 'earthPercent'], 'firePercent', 'energyPercent', 'holyPercent', 'deathPercent', 'drownPercent', 'lifedrainPercent', 'manadrainPercent');

        $keys = array_keys($attributes);

        array_walk($keys, function (&$attribute) {
            $attribute = preg_replace('/Percent$/i', null, $attribute);
        });

        return new Collection(
            array_combine($keys, array_values($attributes))
        );
    }

    /**
     * Parse the immunities information from the monster file.
     *
     * @param  \pandaac\Exporter\Contracts\Reader  $reader
     * @return \Illuminate\Support\Collection
     */
    protected function immunities(Reader $reader)
    {
        if (! $reader->is('immunity')) {
            return false;
        }

        return new Collection(
            $reader->attributes('physical', 'energy', 'fire', ['poison', 'earth'], 'drown', 'ice', 'holy', 'death', 'lifedrain', 'manadrain', 'paralyze', 'outfit', 'drunk', ['invisible', 'invisibility'], 'bleed')
        );
    }

    /**
     * Parse the voices information from the monster file.
     *
     * @param  \pandaac\Exporter\Contracts\Reader  $reader
     * @return \Illuminate\Support\Collection
     */
    protected function voices(Reader $reader)
    {
        if (! $reader->is('voices')) {
            return false;
        }

        $attributes = $reader->attributes(['speed', 'interval'], 'chance');

        return new Collection(
            array_merge($attributes, ['sentences' => new Collection])
        );
    }

    /**
     * Parse the voice sentences information from the monster file.
     *
     * @param  \pandaac\Exporter\Contracts\Reader  $reader
     * @return \Illuminate\Support\Collection
     */
    protected function voiceSentences(Reader $reader)
    {
        if (! $reader->is('voice')) {
            return false;
        }

        return new Collection(
            $reader->attributes('sentence')
        );
    }

    /**
     * Parse the summon statistics information from the monster file.
     *
     * @param  \pandaac\Exporter\Contracts\Reader  $reader
     * @return \Illuminate\Support\Collection
     */
    protected function summonStatistics(Reader $reader)
    {
        if (! $reader->is('summons')) {
            return false;
        }

        $attributes = $reader->attributes('maxSummons');

        return new Collection(
            array_merge($attributes, ['summons' => new Collection])
        );
    }

    /**
     * Parse the summons information from the monster file.
     *
     * @param  \pandaac\Exporter\Contracts\Reader  $reader
     * @return \Illuminate\Support\Collection
     */
    protected function summons(Reader $reader)
    {
        if (! $reader->is('summon')) {
            return false;
        }

        return new Collection(
            $reader->attributes('name', ['speed', 'interval'], 'chance')
        );
    }

    /**
     * Parse the loot information from the monster file.
     *
     * @param  \pandaac\Exporter\Contracts\Reader  $reader
     * @return \Illuminate\Support\Collection
     */
    protected function loot(Reader $reader)
    {
        if (! $reader->is('item')) {
            return false;
        }

        return new Collection(
            $reader->attributes('id', 'countmax', 'chance')
        );
    }

    /**
     * Parse the loot comment information from the monster file.
     *
     * @param  \pandaac\Exporter\Contracts\Reader  $reader
     * @return string
     */
    protected function lootComments(Reader $reader)
    {
        if (! ($reader->isComment() and $reader->parent('loot'))) {
            return false;
        }

        return trim($reader->value());
    }
}
