# Exporter
The aim of the pandaac exporter is to provide a simple & quick interface to export data from the static XML files found in your Open Tibia server.

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
* [amazon.xml](https://github.com/pandaac/exporter/wiki/Example:-amazon.xml)
* [monsters.xml without recursion](https://github.com/pandaac/exporter/wiki/Example:-monsters.xml-(without-recursion))
* [monsters.xml with recursion](https://github.com/pandaac/exporter/wiki/Example:-monsters.xml-(with-recursion))
