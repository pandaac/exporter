# pandaac Exporter
The aim of the pandaac exporter is to provide a simple interface to export data from the static XML files found in your Open Tibia server.

## Requirements
* PHP 5.4+
* PHP LibXML extension

## Parsers
#### Monster
Parses an individual monster file (e.g. `./data/monsters/Amazons/amazon.xml`).
```php
\pandaac\Exporter\Parsers\Monster
```

#### Monsters
Parses the entire list of monsters (`./data/monsters/monsters.xml`), and optionally every individual monster file found within it.
```php
\pandaac\Exporter\Parsers\Monsters
```
#### Items, Commands, Groups, Mounts, Outfits, Quests, Stages, Vocations etc...
These will all be available eventually, but as of right now, you'll have to create your own parsers, or put in a request for it under the [issues](https://github.com/pandaac/exporter/issues).

## Examples

### Parse an individual monster file
```php
use pandaac\Exporter\Exporter;
use pandaac\Exporter\Parsers\Monster;

$amazon = new Exporter(
  new Parsers\Monster('./monsters/Amazons/amazon.xml')
);

$response = $amazon->export();
```
```json
{
  "name": "Amazon",
  "nameDescription": "a amazon",
  "race": "blood",
  "experience": 60,
  "speed": 180,
  "manacost": 390,
  "health": {
    "now": 110,
    "max": 110
  },
  "look": {
    "type": 137,
    "head": 113,
    "body": 120,
    "legs": 114,
    "feet": 132,
    "corpse": 20323
  },
  "targetchange": {
    "interval": 4000,
    "chance": 10
  },
  "flags": {
    "summonable": 1,
    "attackable": 0,
    "hostile": 1,
    "illusionable": 1,
    "convinceable": 1,
    "pushable": 0,
    "canpushitems": 1,
    "canpushcreatures": 0,
    "targetdistance": 4,
    "staticattack": 90,
    "runonhealth": 10
  },
  "attacks": [
    {
      "name": "melee",
      "interval": 2000,
      "min": 0,
      "max": -45
    },
    {
      "name": "physical",
      "interval": 2000,
      "min": 0,
      "max": -40,
      "chance": 15,
      "range": 7,
      "attributes": {
        "shootEffect": "throwingknife"
      }
    }
  ],
  "defenses": {
    "armor": 10,
    "defense": 10
  },
  "elements": {
    "physicalPercent": -5,
    "deathPercent": -5
  },
  "voices": {
    "interval": 5000,
    "chance": 10,
    "sentences": [
      "Yeeee ha!",
      "Your head shall be mine!",
      "Your head will be mine!"
    ]
  },
  "loot": [
    {
      "id": 2148,
      "countmax": 20,
      "chance": 40000,
      "comment": "gold coin"
    },
    {
      "id": 2229,
      "countmax": 2,
      "chance": 80000,
      "comment": "skull"
    },
    {
      "id": 2379,
      "chance": 80000,
      "comment": "dagger"
    },
    {
      "id": 12400,
      "chance": 5000,
      "comment": "Protective Charm"
    },
    {
      "id": 2691,
      "chance": 30333,
      "comment": "brown bread"
    },
    {
      "id": 2385,
      "chance": 23000,
      "comment": "sabre"
    },
    {
      "id": 12399,
      "chance": 10000,
      "comment": "Girlish Hair Decoration"
    },
    {
      "id": 2050,
      "chance": 1005,
      "comment": "torch"
    },
    {
      "id": 2125,
      "chance": 287,
      "comment": "crystal necklace"
    },
    {
      "id": 2147,
      "chance": 161,
      "comment": "small ruby"
    }
  ]
}
```

### Parse an individual monster file with limited data
```php
use pandaac\Exporter\Exporter;
use pandaac\Exporter\Parsers\Monster;

$amazon = new Exporter(
  new Parsers\Monster('./monsters/Amazons/amazon.xml')
);

$response = $amazon->export([
  'health'        => false,
  'look'          => false,
  'targetchange'  => false,
  'flags'         => false,
  'attacks'       => false,
  'defenses'      => false,
  'elements'      => false,
  'voices'        => false,
  'summons'       => false,
  'loot'          => false,
]);
```
```json
{
  "name": "Amazon",
  "nameDescription": "a amazon",
  "race": "blood",
  "experience": 60,
  "speed": 180,
  "manacost": 390
}
```

### Parse the monsters.xml file including individual monster files
```php
use pandaac\Exporter\Exporter;
use pandaac\Exporter\Parsers\Monsters;

$monsters = new Exporter(
  new Parsers\Monsters('./monsters/monsters.xml')
);

$response = $monsters->export([
  'health'        => false,
  'look'          => false,
  'targetchange'  => false,
  'flags'         => false,
  'attacks'       => false,
  'defenses'      => false,
  'elements'      => false,
  'voices'        => false,
  'summons'       => false,
  'loot'          => false,
]);
```
```json
[
  {
    "name": "Amazon",
    "nameDescription": "a amazon",
    "race": "blood",
    "experience": 60,
    "speed": 180,
    "manacost": 390
  },
  {
    "name": "Valkyrie",
    "nameDescription": "a valkyrie",
    "race": "blood",
    "experience": 85,
    "speed": 190,
    "manacost": 450
  },
  {
    "name": "Carrion Worm",
    "nameDescription": "a carrion worm",
    "race": "blood",
    "experience": 70,
    "speed": 160,
    "manacost": 380,
  },

  ...
]
```

### Parse the monsters.xml file excluding individual monster files
```php
use pandaac\Exporter\Exporter;
use pandaac\Exporter\Parsers\Monsters;

$monsters = new Exporter(
  new Parsers\Monsters('./monsters/monsters.xml')
);

$monsters->getParser()->disableRecursion();

$response = $monsters->export();
```
```json
[
  {
    "name": "Amazon",
    "file": "/Users/eklundchristopher/Documents/monsters/Amazons/amazon.xml"
  },
  {
    "name": "Valkyrie",
    "file": "/Users/eklundchristopher/Documents/monsters/Amazons/valkyrie.xml"
  },
  {
    "name": "Carrion Worm",
    "file": "/Users/eklundchristopher/Documents/monsters/Annelids/carrion worm.xml"
  },
  
  ...
]
```
