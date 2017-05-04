<?php

namespace pandaac\Exporter\Sources;

use pandaac\Exporter\Contracts\Source as Contract;

class StringContent implements Contract
{
    /**
     * Holds the content.
     *
     * @var string
     */
    protected $content;

    /**
     * Create a new string source instance.
     *
     * @param  string  $content
     * @return void
     */
    public function __construct($content)
    {
        $this->content = trim($content);
    }

    /**
     * Retrieve the source content.
     *
     * @return string
     */
    public function content()
    {
        return $this->content;
    }
}
