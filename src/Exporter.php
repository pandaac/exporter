<?php

namespace pandaac\Exporter;

use pandaac\Exporter\Contracts\Reader;
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
     * Holds the file to be parsed.
     *
     * @var string
     */
    protected $file;

    /**
     * Instantiate the exporter object.
     *
     * @param  string  $file
     * @param  \pandaac\Exporter\Contracts\Parser  $parser
     * @param  \pandaac\Exporter\Contracts\Reader  $reader  null
     * @return void
     */
    public function __construct($file, Parser $parser, Reader $reader = null)
    {
        $this->file = $file;
        $this->parser = $parser;
        
        if ($reader) {
            $this->getParser()->setReader($reader);
        }
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
     * Export the response from the parser implementation.
     *
     * @return \Illuminate\Support\Collection
     */
    public function export()
    {
        return $this->parser->parse($this->file);
    }
}
