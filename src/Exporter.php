<?php

namespace pandaac\Exporter;

use pandaac\Exporter\Contracts\Parser;

class Exporter
{
    /**
     * Holds the parser implementation.
     *
     * @var \pandaac\Exporter\Contracts\Parser
     */
    protected $parser;

    /**
     * Holds the save file path.
     *
     * @var string
     */
    protected $file;

    /**
     * Instantiate a new exporter object.
     *
     * @param  \pandaac\Exporter\Contracts\Parser  $parser
     * @return void
     */
    public function __construct(Parser $parser)
    {
        $this->parser = $parser;
    }

    /**
     * Returns the parser implementation.
     *
     * @return \pandaac\Exporter\Contracts\Parser
     */
    public function getParser()
    {
        return $this->parser;
    }

    /**
     * Save the exported response to a file.
     *
     * @param  string  $file
     * @return self
     */
    public function save($file)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * Export the response from the parser implementation.
     *
     * @param  array  $settings  []
     * @return mixed
     */
    public function export(array $settings = [])
    {
        $response = $this->parser->parse($settings);

        if ($this->file) {
            file_put_contents($this->file, $response);
        }

        return $response;
    }
}
