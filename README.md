# Exporter
The aim of the pandaac exporter is to provide a simple & relatively quick interface to export data from the static XML files found in your Open Tibia server.

> _It is strongly recommended that you cache the response rather than parsing it over and over again._

## Requirements
* PHP 5.5.9+
* PHP Extensions
  * libxml
  * mbstring

## Parsers
Parsers are what decides how to parse a certain file, and how to structure its response. It is important you use the correct parser for the correct file.

+ **pandaac\Exporter\Parsers\Monster**  
   Parses an individual monster file.  
   e.g. `./data/monsters/Demons/demon.xml`   
   [Example](https://github.com/pandaac/exporter/wiki/Example:-Individual-monster-(e.g.-demon.xml))

+ **pandaac\Exporter\Parsers\Monsters**  
   Parses the entire list of monsters, and optionally every individual monster file found within it.  
   i.e. `./data/monsters/monsters.xml`  
   [Example](https://github.com/pandaac/exporter/wiki/Example:-Monster-list-(monsters.xml))

+ **pandaac\Exporter\Parsers\Items**  
   Parses the entire list of items.  
   i.e. `./data/items/items.xml`  
   [Example](https://github.com/pandaac/exporter/wiki/Example:-Item-list-(items.xml))

+ **pandaac\Exporter\Parsers\Commands**  
   Parses the entire list of commands.  
   i.e. `./data/XML/commands.xml`  
   [Example](https://github.com/pandaac/exporter/wiki/Example:-Commands-list-(commands.xml))

+ **Spells, Groups, Mounts, Outfits, Quests, Stages, Vocations etc...**  
   These will all be available eventually, but as of right now, you'll have to create your own parsers, or put in a request for it under the [issues](https://github.com/pandaac/exporter/issues).

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
