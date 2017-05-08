<?php

namespace pandaac\Exporter\Contracts;

use pandaac\Exporter\Output;
use pandaac\Exporter\Exporter;

interface Parser
{
    /** 
     * Get the relative file path.
     *
     * @return string
     */
    public function filePath();

    /**
     * Get the parser engine.
     *
     * @param  \pandaac\Exporter\Exporter  $exporter
     * @param  array  $attributes
     * @return \pandaac\Exporter\Contracts\Engine
     */
    public function engine(Exporter $exporter, array $attributes);

    /**
     * Parse the file.
     *
     * @param  \pandaac\Exporter\Exporter  $exporter
     * @param  \pandaac\Exporter\Output  $output
     * @param  array  $attributes
     * @return \Illuminate\Support\Collection
     */
    public function parse(Exporter $exporter, Output $output, array $attributes);
}
