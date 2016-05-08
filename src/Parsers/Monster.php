<?php

namespace pandaac\Exporter\Parsers;

use Exception;

use pandaac\Exporter\Parser;
use pandaac\Exporter\Contracts\Parser as Contract;

class Monster extends Parser implements Contract
{
    /**
     * Parse the file.
     *
     * @param  array  $settings  []
     * @return mixed
     */
    public function parse(array $settings = [])
    {
        $this->assignSettings($settings, [
            'health'        => true,
            'look'          => true,
            'targetchange'  => true,
            'flags'         => true,
            'attacks'       => true,
            'defenses'      => true,
            'elements'      => true,
            'voices'        => true,
            'summons'       => true,
            'loot'          => true,
        ]);

        if (! $this->reader->open($this->file)) {
            throw new Exception(sprintf('Unable to read file %s', $this->file));
        }

        while ($this->reader->read()) {
            $this->iterate($settings);

            $this->setPreviousElement();
        }

        $this->reader->close();

        return $this->response;
    }

    /**
     * Get the monster iteration.
     *
     * @return void
     */
    protected function iterate()
    {
        // Get the monster information
        if ($monster = $this->getMonsterInformation()) {
            $this->response = array_merge((array) $this->response, $monster);
        }

        // Get the monster health
        if ($this->isSettingEnabled('health') and $health = $this->getMonsterHealth()) {
            $this->response['health'] = $health;
        }

        // Get the monster look
        if ($this->isSettingEnabled('look') and $look = $this->getMonsterLook()) {
            $this->response['look'] = $look;
        }

        // Get the monster targetChange
        if ($this->isSettingEnabled('targetchange') and $targetChange = $this->getMonsterTargetChange()) {
            $this->response['targetchange'] = $targetChange;
        }

        // Get the monster flags
        if ($this->isSettingEnabled('flags') and $flag = $this->getMonsterFlags()) {
            $this->response['flags'][key($flag)] = current($flag);
        }

        // Get the monster attacks
        if ($this->isSettingEnabled('attacks') and $attacks = $this->getMonsterAttacks()) {
            $this->response['attacks'][] = $attacks;
        }

        // Get the monster attack attributes
        if ($this->isSettingEnabled('attacks') and $attributes = $this->getMonsterAttackAttributes()) {
            if (! isset($this->response['attacks']) or ! is_array(($attacks = $this->response['attacks']))) {
                return false;
            }

            end($attacks);
            $this->response['attacks'][key($attacks)]['attributes'][$attributes['key']] = $attributes['value'];
        }

        // Get the monster defense stats
        if ($this->isSettingEnabled('defenses') and $defenses = $this->getMonsterDefenseStats()) {
            $this->response['defenses'] = $defenses;
        }

        // Get the monster defenses
        if ($this->isSettingEnabled('defenses') and $defenses = $this->getMonsterDefenses()) {
            $this->response['defenses']['defenses'][] = $defenses;
        }

        // Get the monster defense attributes
        if ($this->isSettingEnabled('defenses') and $attributes = $this->getMonsterDefenseAttributes()) {
            if (! isset($this->response['defenses']['defenses']) or ! is_array(($defenses = $this->response['defenses']['defenses']))) {
                return false;
            }

            end($defenses);
            $this->response['defenses']['defenses'][key($defenses)]['attributes'][$attributes['key']] = $attributes['value'];
        }

        // Get the monster elements
        if ($this->isSettingEnabled('elements') and $element = $this->getMonsterElements()) {
            $this->response['elements'][key($element)] = current($element);
        }

        // Get the monster immunities
        if ($this->isSettingEnabled('statistics') and $immunity = $this->getMonsterImmunities()) {
            $this->response['immunities'][key($immunity)] = current($immunity);
        }

        // Get the monster voices
        if ($this->isSettingEnabled('voices') and $voices = $this->getMonsterVoices()) {
            $this->response['voices'] = $voices;
        }

        // Get the monster voice sentences
        if ($this->isSettingEnabled('voices') and $sentence = $this->getMonsterVoiceSentences()) {
            $this->response['voices']['sentences'][] = current($sentence);
        }

        // Get the monster summon statistics
        if ($this->isSettingEnabled('summons') and $summons = $this->getMonsterSummonStats()) {
            $this->response['summons'] = $summons;
        }

        // Get the monster summons
        if ($this->isSettingEnabled('summons') and $summon = $this->getMonsterSummons()) {
            $this->response['summons']['summons'][] = $summon;
        }

        // Get the monster loot
        if ($this->isSettingEnabled('loot') and $loot = $this->getMonsterLoot()) {
            $this->response['loot'][] = $loot;
        }

        // Get the monster loot comments
        if ($this->isSettingEnabled('loot') and $itemComment = $this->getMonsterLootComment()) {
            if (! isset($this->response['loot']) or ! is_array(($items = $this->response['loot']))) {
                return false;
            }

            end($items);
            $this->response['loot'][key($items)]['comment'] = trim($itemComment);
        }
    }

    /**
     * Get the monster information.
     *
     * @return mixed
     */
    protected function getMonsterInformation()
    {
        if (! $this->isElement('monster')) {
            return false;
        }

        return $this->attributes(
            'name', 
            'nameDescription', 
            'race', 
            'experience', 
            'speed', 
            'manacost', 
            'skull'
        );
    }

    /**
     * Get the monster health.
     *
     * @return mixed
     */
    protected function getMonsterHealth()
    {
        if (! $this->isElement('health')) {
            return false;
        }

        return $this->attributes(
            'now', 
            'max'
        );
    }

    /**
     * Get the monster look.
     *
     * @return mixed
     */
    protected function getMonsterLook()
    {
        if (! $this->isElement('look')) {
            return false;
        }

        return $this->attributes(
            'type', 
            'typeex', 
            'head', 
            'body', 
            'legs', 
            'feet', 
            'addons', 
            'mount', 
            'corpse'
        );
    }

    /**
     * Get the monster target change.
     *
     * @return mixed
     */
    protected function getMonsterTargetChange()
    {
        if (! $this->isElement('targetchange')) {
            return false;
        }

        return $this->attributes(
            ['speed', 'interval'], 
            'chance'
        );
    }

    /**
     * Get the monster flags.
     *
     * @return mixed
     */
    protected function getMonsterFlags()
    {
        if (! $this->isElement('flag')) {
            return false;
        }

        return $this->attributes(
            'summonable', 
            'attackable', 
            'hostile', 
            'illusionable', 
            'convinceable', 
            'pushable', 
            'canpushitems', 
            'canpushcreatures', 
            'staticattack', 
            'lightlevel', 
            'lightcolor', 
            'targetdistance', 
            'runonhealth', 
            'hidehealth'
        );
    }

    /**
     * Get the monster attacks.
     *
     * @return mixed
     */
    protected function getMonsterAttacks()
    {
        if (! $this->isElement('attack')) {
            return false;
        }

        return $this->attributes(
            'name', 
            'interval', 
            'min', 
            'max', 
            'chance', 
            'range', 
            'radius', 
            'target', 
            'skill', 
            'attack', 
            'speedchange', 
            'duration'
        );
    }

    /**
     * Get the monster attack attributes.
     *
     * @return mixed
     */
    protected function getMonsterAttackAttributes()
    {
        if (! ($this->isElement('attribute') and $this->previousElementWas('attack'))) {
            return false;
        }

        return $this->attributes(
            'key', 
            'value'
        );
    }

    /**
     * Get the monster defense statistics.
     *
     * @return mixed
     */
    protected function getMonsterDefenseStats()
    {
        if (! $this->isElement('defenses')) {
            return false;
        }

        return $this->attributes(
            'armor', 
            'defense'
        );
    }

    /**
     * Get the monster defenses.
     *
     * @return mixed
     */
    protected function getMonsterDefenses()
    {
        if (! $this->isElement('defense')) {
            return false;
        }

        return $this->attributes(
            'name', 
            'interval', 
            'min', 
            'max', 
            'chance', 
            'range', 
            'radius', 
            'target', 
            'skill', 
            'attack', 
            'speedchange', 
            'duration'
        );
    }

    /**
     * Get the monster defense attributes.
     *
     * @return mixed
     */
    protected function getMonsterDefenseAttributes()
    {
        if (! ($this->isElement('attribute') and $this->previousElementWas('defense'))) {
            return false;
        }

        return $this->attributes(
            'key', 
            'value'
        );
    }

    /**
     * Get the monster elements.
     *
     * @return mixed
     */
    protected function getMonsterElements()
    {
        if (! $this->isElement('element')) {
            return false;
        }

        return $this->attributes(
            'physicalPercent', 
            'icePercent', 
            ['poisonPercent', 'earthPercent'], 
            'firePercent', 
            'energyPercent', 
            'holyPercent', 
            'deathPercent', 
            'drownPercent', 
            'lifedrainPercent', 
            'manadrainPercent'
        );
    }

    /**
     * Get the monster immunities.
     *
     * @return mixed
     */
    protected function getMonsterImmunities()
    {
        if (! $this->isElement('immunity')) {
            return false;
        }

        return $this->attributes(
            'physical', 
            'energy', 
            'fire', 
            ['poison', 'earth'], 
            'drown', 
            'ice', 
            'holy', 
            'death', 
            'lifedrain', 
            'manadrain', 
            'paralyze', 
            'outfit', 
            'drunk', 
            ['invisible', 'invisibility'], 
            'bleed' 
        );
    }

    /**
     * Get the monster voices.
     *
     * @return mixed
     */
    protected function getMonsterVoices()
    {
        if (! $this->isElement('voices')) {
            return false;
        }

        return $this->attributes(
            ['speed', 'interval'], 
            'chance'
        );
    }

    /**
     * Get the monster voice sentences.
     *
     * @return mixed
     */
    protected function getMonsterVoiceSentences()
    {
        if (! $this->isElement('voice')) {
            return false;
        }

        return $this->attributes(
            'sentence' 
        );
    }

    /**
     * Get the monster summon statistics.
     *
     * @return mixed
     */
    protected function getMonsterSummonStats()
    {
        if (! $this->isElement('summons')) {
            return false;
        }

        return $this->attributes(
            'maxSummons'
        );
    }

    /**
     * Get the monster summons.
     *
     * @return mixed
     */
    protected function getMonsterSummons()
    {
        if (! $this->isElement('summon')) {
            return false;
        }

        return $this->attributes(
            'name', 
            ['speed', 'interval'], 
            'chance'
        );
    }

    /**
     * Get the monster loot.
     *
     * @return mixed
     */
    protected function getMonsterLoot()
    {
        if (! $this->isElement('item')) {
            return false;
        }

        return $this->attributes(
            'id', 
            'countmax', 
            'chance'
        );
    }

    /**
     * Get the monster loot comment.
     *
     * @return mixed
     */
    protected function getMonsterLootComment()
    {
        if (! ($this->isComment() and $this->previousElementWas('item'))) {
            return false;
        }

        return $this->value();
    }
}
