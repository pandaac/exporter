<?php

namespace pandaac\Exporter\Contracts;

interface Source
{
    /**
     * Retrieve the source content.
     *
     * @return string
     */
    public function content();
}
