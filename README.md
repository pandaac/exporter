# Exporter
The aim of the pandaac exporter is to provide a simple & relatively quick interface to export data from the static XML files found in your Open Tibia server.

## Requirements
* PHP 5.5.9+
* PHP LibXML extension

## Parsers
#### pandaac\Exporter\Parsers\Monster ([Example](https://github.com/pandaac/exporter/wiki/Example:-Individual-monster-(e.g.-demon.xml)))

> Parses an individual monster file (e.g. `./data/monsters/Demons/demon.xml`).

#### pandaac\Exporter\Parsers\Monsters ([Example](https://github.com/pandaac/exporter/wiki/Example:-Monster-list-(monsters.xml)))

> Parses the entire list of monsters (`./data/monsters/monsters.xml`), and optionally every individual monster file found within it.

#### pandaac\Exporter\Parsers\Items ([Example](https://github.com/pandaac/exporter/wiki/Example:-Item-list-(items.xml)))

> Parses the entire list of items (`./data/items/items.xml`).

#### pandaac\Exporter\Parsers\Commands ([Example](https://github.com/pandaac/exporter/wiki/Example:-Commands-list-(commands.xml)))

> Parses the entire list of commands (`./data/XML/commands.xml`).

#### Commands, Groups, Mounts, Outfits, Quests, Stages, Vocations etc...
These will all be available eventually, but as of right now, you'll have to create your own parsers, or put in a request for it under the [issues](https://github.com/pandaac/exporter/issues).
