<?php

namespace pandaac\Exporter\Parsers;

use pandaac\Exporter\Output;
use pandaac\Exporter\Exporter;
use pandaac\Exporter\Engines\XML;
use Illuminate\Support\Collection;
use pandaac\Exporter\Contracts\Parser as Contract;

class Groups implements Contract
{
    /**
     * Constant representing a player that cannot use combat.
     *
     * @var integer
     */
    const FLAG_CANNOTUSECOMBAT = 1 << 0;

    /**
     * Constant representing a player that cannot attack other players.
     *
     * @var integer
     */
    const FLAG_CANNOTATTACKPLAYER = 1 << 1;

    /**
     * Constant representing a player that cannot attack monsters.
     *
     * @var integer
     */
    const FLAG_CANNOTATTACKMONSTER = 1 << 2;

    /**
     * Constant representing a player that cannot be attacked.
     *
     * @var integer
     */
    const FLAG_CANNOTBEATTACKED = 1 << 3;

    /**
     * Constant representing a player that can convince any creature.
     *
     * @var integer
     */
    const FLAG_CANCONVINCEALL = 1 << 4;

    /**
     * Constant representing a player that can summon any creature.
     *
     * @var integer
     */
    const FLAG_CANSUMMONALL = 1 << 5;

    /**
     * Constant representing a player that can transform themselves into any creature.
     *
     * @var integer
     */
    const FLAG_CANILLUSIONALL = 1 << 6;

    /**
     * Constant representing a player that can see invisible creatures.
     *
     * @var integer
     */
    const FLAG_CANSENSEINVISIBILITY = 1 << 7;

    /**
     * Constant representing a player that cannot be detected by monsters.
     *
     * @var integer
     */
    const FLAG_IGNOREDBYMONSTERS = 1 << 8;

    /**
     * Constant representing a player that is immune against battle restrictions.
     *
     * @var integer
     */
    const FLAG_NOTGAININFIGHT = 1 << 9;

    /**
     * Constant representing a player that has infinite mana.
     *
     * @var integer
     */
    const FLAG_HASINFINITEMANA = 1 << 10;

    /**
     * Constant representing a player that has infinite soul points.
     *
     * @var integer
     */
    const FLAG_HASINFINITESOUL = 1 << 11;

    /**
     * Constant representing a player that is immune against exhaustion.
     *
     * @var integer
     */
    const FLAG_HASNOEXHAUSTION = 1 << 12;

    /**
     * Constant representing a player that cannot use spells.
     *
     * @var integer
     */
    const FLAG_CANNOTUSESPELLS = 1 << 13;

    /**
     * Constant representing a player that cannot pick up items.
     *
     * @var integer
     */
    const FLAG_CANNOTPICKUPITEM = 1 << 14;

    /**
     * Constant representing a player that is immune against login restrictions.
     *
     * @var integer
     */
    const FLAG_CANALWAYSLOGIN = 1 << 15;

    /**
     * Constant representing a player that can broadcast messages.
     *
     * @var integer
     */
    const FLAG_CANBROADCAST = 1 << 16;

    /**
     * Constant representing a player that can edit houses.
     *
     * @var integer
     */
    const FLAG_CANEDITHOUSES = 1 << 17;

    /**
     * Constant representing a player that is immune against rule violations.
     *
     * @var integer
     */
    const FLAG_CANNOTBEBANNED = 1 << 18;

    /**
     * Constant representing a player that cannot be pushed.
     *
     * @var integer
     */
    const FLAG_CANNOTBEPUSHED = 1 << 19;

    /**
     * Constant representing a player that has infinite capacity.
     *
     * @var integer
     */
    const FLAG_HASINFINITECAPACITY = 1 << 20;

    /**
     * Constant representing a player that can push any creature.
     *
     * @var integer
     */
    const FLAG_CANPUSHALLCREATURES = 1 << 21;

    /**
     * Constant representing a player that can talk red in private messages.
     *
     * @var integer
     */
    const FLAG_CANTALKREDPRIVATE = 1 << 22;

    /**
     * Constant representing a player that can talk red in channels.
     *
     * @var integer
     */
    const FLAG_CANTALKREDCHANNEL = 1 << 23;

    /**
     * Constant representing a player that can talk orange in the help channel.
     *
     * @var integer
     */
    const FLAG_TALKORANGEHELPCHANNEL = 1 << 24;

    /**
     * Constant representing a player that cannot gain any experience points.
     *
     * @var integer
     */
    const FLAG_NOTGAINEXPERIENCE = 1 << 25;

    /**
     * Constant representing a player that cannot gain any mana points.
     *
     * @var integer
     */
    const FLAG_NOTGAINMANA = 1 << 26;

    /**
     * Constant representing a player that cannot gain any health points.
     *
     * @var integer
     */
    const FLAG_NOTGAINHEALTH = 1 << 27;

    /**
     * Constant representing a player that cannot gain any skill points.
     *
     * @var integer
     */
    const FLAG_NOTGAINSKILL = 1 << 28;

    /**
     * Constant representing a player that has the maximum base speed.
     *
     * @var integer
     */
    const FLAG_SETMAXSPEED = 1 << 29;

    /**
     * Constant representing a player that cannot be added to someone else's VIP list.
     *
     * @var integer
     */
    const FLAG_SPECIALVIP = 1 << 30;

    /**
     * Constant representing a player that doesn't generate any loot from creatures.
     *
     * @var integer
     */
    const FLAG_NOTGENERATELOOT = 1 << 31;

    /**
     * Constant representing a player that can talk red in channels, anonymously.
     *
     * @var integer
     */
    const FLAG_CANTALKREDCHANNELANONYMOUS = 1 << 32;

    /**
     * Constant representing a player that is immune against protection zone restrictions.
     *
     * @var integer
     */
    const FLAG_IGNOREPROTECTIONZONE = 1 << 33;

    /**
     * Constant representing a player that is immune against spell requirements.
     *
     * @var integer
     */
    const FLAG_IGNORESPELLCHECK = 1 << 34;

    /**
     * Constant representing a player that is immune against equipment requirements.
     *
     * @var integer
     */
    const FLAG_IGNOREWEAPONCHECK = 1 << 35;

    /**
     * Constant representing a player that cannot be muted.
     *
     * @var integer
     */
    const FLAG_CANNOTBEMUTED = 1 << 36;

    /**
     * Constant representing a player that has infinite premium benefits.
     *
     * @var integer
     */
    const FLAG_ISALWAYSPREMIUM = 1 << 37;

    /**
     * Retrieve all the available flags.
     *
     * @var array
     */
    protected static $flags = [
        'cannotusecombat' => self::FLAG_CANNOTUSECOMBAT,
        'cannotattackplayer' => self::FLAG_CANNOTATTACKPLAYER,
        'cannotattackmonster' => self::FLAG_CANNOTATTACKMONSTER,
        'cannotbeattacked' => self::FLAG_CANNOTBEATTACKED,
        'canconvinceall' => self::FLAG_CANCONVINCEALL,
        'cansummonall' => self::FLAG_CANSUMMONALL,
        'canillusionall' => self::FLAG_CANILLUSIONALL,
        'cansenseinvisibility' => self::FLAG_CANSENSEINVISIBILITY,
        'ignoredbymonsters' => self::FLAG_IGNOREDBYMONSTERS,
        'notgaininfight' => self::FLAG_NOTGAININFIGHT,
        'hasinfinitemana' => self::FLAG_HASINFINITEMANA,
        'hasinfinitesoul' => self::FLAG_HASINFINITESOUL,
        'hasnoexhaustion' => self::FLAG_HASNOEXHAUSTION,
        'cannotusespells' => self::FLAG_CANNOTUSESPELLS,
        'cannotpickupitem' => self::FLAG_CANNOTPICKUPITEM,
        'canalwayslogin' => self::FLAG_CANALWAYSLOGIN,
        'canbroadcast' => self::FLAG_CANBROADCAST,
        'canedithouses' => self::FLAG_CANEDITHOUSES,
        'cannotbebanned' => self::FLAG_CANNOTBEBANNED,
        'cannotbepushed' => self::FLAG_CANNOTBEPUSHED,
        'hasinfinitecapacity' => self::FLAG_HASINFINITECAPACITY,
        'canpushallcreatures' => self::FLAG_CANPUSHALLCREATURES,
        'cantalkredprivate' => self::FLAG_CANTALKREDPRIVATE,
        'cantalkredchannel' => self::FLAG_CANTALKREDCHANNEL,
        'talkorangehelpchannel' => self::FLAG_TALKORANGEHELPCHANNEL,
        'notgainexperience' => self::FLAG_NOTGAINEXPERIENCE,
        'notgainmana' => self::FLAG_NOTGAINMANA,
        'notgainhealth' => self::FLAG_NOTGAINHEALTH,
        'notgainskill' => self::FLAG_NOTGAINSKILL,
        'setmaxspeed' => self::FLAG_SETMAXSPEED,
        'specialvip' => self::FLAG_SPECIALVIP,
        'notgenerateloot' => self::FLAG_NOTGENERATELOOT,
        'cantalkredchannelanonymous' => self::FLAG_CANTALKREDCHANNELANONYMOUS,
        'ignoreprotectionzone' => self::FLAG_IGNOREPROTECTIONZONE,
        'ignorespellcheck' => self::FLAG_IGNORESPELLCHECK,
        'ignoreweaponcheck' => self::FLAG_IGNOREWEAPONCHECK,
        'cannotbemuted' => self::FLAG_CANNOTBEMUTED,
        'isalwayspremium' => self::FLAG_ISALWAYSPREMIUM,
    ];

    /** 
     * Get the relative file path.
     *
     * @return string
     */
    public function filePath()
    {
        return '/data/XML/groups.xml';
    }

    /**
     * Get the parser engine.
     *
     * @param  array  $attributes
     * @return \pandaac\Exporter\Contracts\Engine
     */
    public function engine(array $attributes)
    {
        return new XML($attributes);
    }

    /**
     * Parse the file.
     *
     * @param  \pandaac\Exporter\Exporter  $exporter
     * @param  \pandaac\Exporter\Output  $output
     * @param  array  $attributes
     * @return \Illuminate\Support\Collection
     */
    public function parse(Exporter $exporter, Output $output, array $attributes)
    {
        $groups = $output->first()->get('group', new Collection);

        $groups->each(function ($group) {
            if ($group->has('flags')) {
                $group->put('attributes', $this->getFlags($group->get('flags')));
            }
        });

        return $groups;
    }

    /**
     * Retrieve a list of all the flags within the provided flags identifier.
     *
     * @param  integer  $identifier
     * @return array
     */
    protected function getFlags($identifier)
    {
        $flags = new Collection(static::$flags);

        return $flags->transform(function ($value, $flag) use ($identifier) {
            return (boolean) ($identifier & $value);
        });
    }
}
