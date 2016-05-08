<?php

namespace pandaac\Exporter\Parsers;

use Exception;

use pandaac\Exporter\ListParser;
use pandaac\Exporter\Contracts\Parser as Contract;

class Monsters extends ListParser implements Contract
{
    /**
     * Parse the file.
     *
     * @param  array  $settings  []
     * @return mixed
     */
    public function parse(array $settings = [])
    {
        $this->assignSettings($settings);

        if (! $this->reader->open($this->file)) {
            throw new Exception(sprintf('Unable to read file %s', $this->file));
        }

        while ($this->reader->read()) {
            if (! ($monster = $this->getMonster())) {
                continue;
            }

            $this->response[] = $monster;
        }

        $this->reader->close();

        return $this->response;
    }

    /**
     * Get the current monster.
     *
     * @return mixed
     */
    protected function getMonster()
    {
        if (! $this->isElement('monster')) {
            return false;
        }

        $path = realpath(
            dirname($this->file).'/'.$this->reader->getAttribute('file')
        );

        if ($this->isRecursionEnabled()) {
            return (new Monster($path))->parse($this->settings);
        }

        return [
            'name' => $this->reader->getAttribute('name'),
            'file' => $path,
        ];
    }
}
