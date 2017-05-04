<?php

namespace pandaac\Exporter;

use LogicException;
use InvalidArgumentException;
use UnexpectedValueException;
use pandaac\Exporter\Contracts\Engine;
use pandaac\Exporter\Contracts\Parser;
use pandaac\Exporter\Contracts\Source;

class Exporter
{
    /**
     * Set the version.
     *
     * @var string
     */
    const VERSION = '2.0.1';

    /**
     * Set the git repository issues link.
     *
     * @var string
     */
    const ISSUES = 'https://github.com/pandaac/exporter/issues';

    /**
     * Holds the source.
     *
     * @var \pandaac\Exporter\Contracts\Source|string
     */
    protected $source;

    /**
     * Holds the output implementation.
     *
     * @var \pandaac\Exporter\Output
     */
    protected $output;

    /**
     * Instantiate a new exporter object.
     *
     * @param  \pandaac\Exporter\Contracts\Source|string  $source
     * @return void
     */
    public function __construct($source)
    {
        $this->source = $source;

        if (! $source instanceof Source and ! file_exists($source)) {
            throw new InvalidArgumentException('The first argument must be a valid directory.');
        }
    }

    /**
     * Get the absolute file path.
     *
     * @param  string  $path
     * @param  string  $custom  null
     * @return \pandaac\Exporter\Contracts\Source|string
     */
    public function getAbsolutePath($path, $custom = null)
    {
        if ($this->source instanceof Source) {
            return $this->source;
        }

        $path = ltrim($path, DIRECTORY_SEPARATOR);

        if (substr($path, -1, 1) === DIRECTORY_SEPARATOR) {
            $custom = $path.$custom;
        }

        return $this->source.DIRECTORY_SEPARATOR.($custom ?: $path);
    }

    /**
     * Run a specific parser.
     *
     * @param  \pandaac\Exporter\Contracts\Parser  $parser
     * @param  array  $attributes  []
     * @param  string  $file  null
     * @return \Illuminate\Support\Collection
     */
    public function parse(Parser $parser, array $attributes = [], $file = null)
    {
        $engine = $parser->engine($attributes);

        if (! ($engine instanceof Engine)) {
            throw new LogicException('The provided engine must implement the pandaac\Exporter\Contracts\Engine interface.');
        }

        $engine->open(
            $this->getAbsolutePath($parser->filePath(), $file)
        );

        $response = $parser->parse($this, $this->output = $engine->output(), $attributes);

        $engine->close();

        return $response;
    }

    /**
     * Retrieve the exporter meta details.
     *
     * @return \Illuminate\Support\Collection
     */
    public function meta()
    {
        if (! $this->output) {
            throw new UnexpectedValueException('Run a parser before fetching the exporter meta.');
        }

        return $this->output->meta();
    }
}
