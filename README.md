# Exporter
The aim of the pandaac exporter is to provide a simple & quick interface to export data from the common XML files found within the data/ directory of your Open Tibia server.

> _It is strongly recommended that you cache the response rather than parsing it over and over again._

> _As of right now, this exporter assumes that you're using [The Forgotten Server 1.1](https://github.com/otland/forgottenserver/tree/1.1). I have no immediate plans to expand this to any other distrobutions until I expand the distrobution range for [pandaac](https://github.com/pandaac/pandaac) itself. However, if there's enough public pressure for a specific distrobution, I may reconsider._

## Requirements
* PHP 5.6.4+
* PHP Extensions
  * libxml

## Install
##### Via Composer
```
composer require pandaac/exporter
```

## Usage
Pass the absolute path to your Open Tibia server as the first argument of the `Exporter` object and specify which parser you would like to use as the first argument of the `parse` method of the `Exporter` object.

The `parse` method also accepts a second argument for additional attributes (depends on the parser), and a third argument for overriding the default filepath (or providing a specific parser with a filepath).

```php
use pandaac\Exporter\Parsers;
use pandaac\Exporter\Exporter;

$exporter = new Exporter(
    '/home/pandaac/theforgottenserver'
);

$response = $exporter->parse(new Parsers\Weapons);
```

## Parsers
Parsers are what decides how to parse a certain file, and how to structure its response. It is important you use the correct parser for the correct file.

### XML Parsers
+ **Actions**  
   > This parser has no attributes.  

   ```php
   $exporter->parse(new \pandaac\Exporter\Parsers\Actions);
   ```
+ **Chat Channels**  
   > This parser has no attributes.  

   ```php
   $exporter->parse(new \pandaac\Exporter\Parsers\ChatChannels);
   ```
+ **Commands**  
   > This parser has no attributes.  
   
   ```php
   $exporter->parse(new \pandaac\Exporter\Parsers\Commands);
   ```
+ **Creature Scripts**  
   > This parser has no attributes.  
   
   ```php
   $exporter->parse(new \pandaac\Exporter\Parsers\CreatureScripts);
   ```
+ **Events**  
   > This parser has no attributes.  
   
   ```php
   $exporter->parse(new \pandaac\Exporter\Parsers\Events);
   ```
+ **Global Events**  
   > This parser has no attributes.  
   
   ```php
   $exporter->parse(new \pandaac\Exporter\Parsers\GlobalEvents);
   ```
+ **Groups**  
   > This parser has no attributes.  
   
   ```php
   $exporter->parse(new \pandaac\Exporter\Parsers\Groups);
   ```
+ **Items**  
   > This parser has no attributes.  
   
   ```php
   $exporter->parse(new \pandaac\Exporter\Parsers\Items);
   ```
+ **Map Houses**  
   > You must specify the relative filename as the third argument.  

   ```php
   $exporter->parse(new \pandaac\Exporter\Parsers\MapHouses, [], 'forgotten-house.xml');
   ```
+ **Map Spawns**  
   > You must specify the relative filename as the third argument.  

   ```php
   $exporter->parse(new \pandaac\Exporter\Parsers\MapSpawns, [], 'forgotten-spawn.xml');
   ```
+ **Monster**  
   > You must specify the relative filename as the third argument.  

   ```php
   $exporter->parse(new \pandaac\Exporter\Parsers\Monster, [], 'Demons/Demon.xml');
   ```
+ **Monsters**  
   > You may also load the data from within each individual monster file by setting the `recursive` attribute to `true`.  
   
   ```php
   $exporter->parse(new \pandaac\Exporter\Parsers\Monsters, [ 'recursive' => true ]);
   ```
+ **Mounts**  
   > This parser has no attributes.  
   
   ```php
   $exporter->parse(new \pandaac\Exporter\Parsers\Mounts);
   ```
+ **Movements**  
   > This parser has no attributes.  
   
   ```php
   $exporter->parse(new \pandaac\Exporter\Parsers\Movements);
   ```
+ **NPC**  
   > You must specify the relative filename as the third argument.  

   ```php
   $exporter->parse(new \pandaac\Exporter\Parsers\NPC, [], 'The Oracle.xml');
   ```
+ **Outfits**  
   > This parser has no attributes.  
   
   ```php
   $exporter->parse(new \pandaac\Exporter\Parsers\Outfits);
   ```
+ **Quests**  
   > This parser has no attributes.  
   
   ```php
   $exporter->parse(new \pandaac\Exporter\Parsers\Quests);
   ```
+ **Raid**  
   > You must specify the relative filename as the third argument.  

   ```php
   $exporter->parse(new \pandaac\Exporter\Parsers\Raid, [], 'testraid.xml');
   ```
+ **Raids**  
   > You may also load the data from within each individual raid file by setting the `recursive` attribute to `true`.  

   ```php
   $exporter->parse(new \pandaac\Exporter\Parsers\Raids, [ 'recursive' => true ]);
   ```
+ **Spells**  
   > This parser has no attributes.  
   
   ```php
   $exporter->parse(new \pandaac\Exporter\Parsers\Spells);
   ```
+ **Stages**  
   > This parser has no attributes.  
   
   ```php
   $exporter->parse(new \pandaac\Exporter\Parsers\Stages);
   ```
+ **TalkActions**  
   > This parser has no attributes.  
   
   ```php
   $exporter->parse(new \pandaac\Exporter\Parsers\TalkActions);
   ```
+ **Vocations**  
   > This parser has no attributes.  
   
   ```php
   $exporter->parse(new \pandaac\Exporter\Parsers\Vocations);
   ```
+ **Weapons**  
   > This parser has no attributes.  
   
   ```php
   $exporter->parse(new \pandaac\Exporter\Parsers\Weapons);
   ```

### OTBM Parsers
> The OTBM engine has not yet been developed, and thus the following parsers are rendered obsolete for the time being.

+ **Towns**  
   > This parser has no attributes.  
   
   ```php
   $exporter->parse(new \pandaac\Exporter\Parsers\Towns);
   ```

## Contributing
Please refer to the [PSR-2 guidelines](http://www.php-fig.org/psr/psr-2/) and squash your commits together into one before submitting a pull request.

Thank you.
