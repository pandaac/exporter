# Exporter
The aim of the pandaac exporter is to provide a simple & relatively quick interface to export data from the static XML files found in your Open Tibia server.

> _It is strongly recommended that you cache the response rather than parsing it over and over again._

> _As of right now, this exporter assumes that you're using [The Forgotten Server 1.1](https://github.com/otland/forgottenserver/tree/1.1). I have no immediate plans to expand this to any other distrobutions until I expand the distrobution range for [pandaac](https://github.com/pandaac/pandaac) itself. However, if there's enough public pressure for a specific distrobution, I may reconsider._

## Requirements
* PHP 5.5.9+
* PHP Extensions
  * libxml
  * mbstring

## Install
##### Via Composer
```
composer require pandaac/exporter
```

## Parsers
Parsers are what decides how to parse a certain file, and how to structure its response. It is important you use the correct parser for the correct file.

+ **pandaac\Exporter\Parsers\Commands <sub>[ [Example](https://github.com/pandaac/exporter/wiki/Example:-Command-list-(commands.xml)) ]</sub>**  
   Parses the entire list of commands.  
   i.e. `./data/XML/commands.xml`

+ **pandaac\Exporter\Parsers\Groups <sub>[ [Example](https://github.com/pandaac/exporter/wiki/Example:-Group-list-(groups.xml)) ]</sub>**  
   Parses the entire list of groups.  
   i.e. `./data/XML/groups.xml`

+ **pandaac\Exporter\Parsers\Items <sub>[ [Example](https://github.com/pandaac/exporter/wiki/Example:-Item-list-(items.xml)) ]</sub>**  
   Parses the entire list of items.  
   i.e. `./data/items/items.xml`

+ **pandaac\Exporter\Parsers\Monster <sub>[ [Example](https://github.com/pandaac/exporter/wiki/Example:-Individual-monster-(e.g.-demon.xml)) ]</sub>**  
   Parses an individual monster file.  
   e.g. `./data/monsters/Demons/demon.xml`

+ **pandaac\Exporter\Parsers\Monsters <sub>[ [Example](https://github.com/pandaac/exporter/wiki/Example:-Monster-list-(monsters.xml)) ]</sub>**  
   Parses the entire list of monsters, and optionally every individual monster file found within it.  
   i.e. `./data/monsters/monsters.xml`

+ **pandaac\Exporter\Parsers\Mounts <sub>[ [Example](https://github.com/pandaac/exporter/wiki/Example:-Mount-list-(mounts.xml)) ]</sub>**  
   Parses the entire list of mounts.  
   i.e. `./data/XML/mounts.xml`

+ **pandaac\Exporter\Parsers\Outfits <sub>[ [Example](https://github.com/pandaac/exporter/wiki/Example:-Outfit-list-(outfits.xml)) ]</sub>**  
   Parses the entire list of outfits.  
   i.e. `./data/XML/outfits.xml`

+ **pandaac\Exporter\Parsers\Quests <sub>[ [Example](https://github.com/pandaac/exporter/wiki/Example:-Quest-list-(quests.xml)) ]</sub>**  
   Parses the entire list of quests.  
   i.e. `./data/XML/quests.xml`

+ **pandaac\Exporter\Parsers\Spells <sub>[ [Example](https://github.com/pandaac/exporter/wiki/Example:-Spell-list-(spells.xml)) ]</sub>**  
   Parses the entire list of spells.  
   i.e. `./data/spells/spells.xml`

+ **pandaac\Exporter\Parsers\Stages <sub>[ [Example](https://github.com/pandaac/exporter/wiki/Example:-Level-stages-(stages.xml)) ]</sub>**  
   Parses the entire list of level stages.  
   i.e. `./data/XML/stages.xml`

+ **pandaac\Exporter\Parsers\Vocations <sub>[ [Example](https://github.com/pandaac/exporter/wiki/Example:-Vocation-list-(vocations.xml)) ]</sub>**  
   Parses the entire list of vocations.  
   i.e. `./data/XML/vocations.xml`

+ **Other?**  
   If you feel like I've missed a vital XML file that needs parsing, please do let me know through the [issues](https://github.com/pandaac/exporter/issues).

## Extending
#### Parsers
As the `parser` implementation is passed as an argument to the `Exporter` class when it's instantiated, all you really need to think about when writing your own implementation is including the [contract](https://github.com/pandaac/exporter/blob/master/src/Contracts/Parser.php).

> _You may also extend the `pandaac\Exporter\Parser` class to get a bunch of predefined functionality (e.g. options)._

```php
namespace Example\Parsers;

use pandaac\Exporter\Contracts\Parser as Contract;

class MyCustomMonsterParser implements Contract
{
  // Define all of the necessary methods as per the contract...
}
```

```php
use pandaac\Exporter\Exporter;
use Example\Parsers\MyCustomMonsterParser;

$exporter = new Exporter(
  './data/monster/monsters.xml',
  new MyCustomMonsterParser
);
```

#### Reader
The same principles applies to the `reader` implementation, however, obviously with its own [contract](https://github.com/pandaac/exporter/blob/master/src/Contracts/Reader.php). The only difference is the way you register it. You'll have to call the `setReader` method on the `parser` implementation.

> _By default, we utilise the [XMLReader](http://php.net/manual/en/book.xmlreader.php) class as part of our reader implementation._

```php
namespace Example;

use pandaac\Exporter\Contracts\Reader as Contract;

class MyCustomReader implements Contract
{
  // Define all of the necessary methods as per the contract...
}
```

```php
use Example\MyCustomReader;
use pandaac\Exporter\Exporter;
use Example\Parsers\MyCustomMonsterParser;

$exporter = new Exporter(
  './data/monster/monsters.xml',
  new MyCustomMonsterParser
);

$exporter->getParser()->setReader(new MyCustomReader);
```

## Contributing
Please refer to the [PSR-2 guidelines](http://www.php-fig.org/psr/psr-2/) and squash your commits together into one before submitting a pull request.

Thank you.
