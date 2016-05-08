<?php

namespace pandaac\Exporter;

abstract class ListParser extends Parser
{
    /**
     * Holds the recursion status.
     *
     * @var boolean
     */
    protected $recursion;

    /**
     * Instantiate a new parser object.
     *
     * @param  string  $file
     * @return void
     */
    public function __construct($file)
    {
        parent::__construct($file);

        $this->enableRecursion();
    }

    /**
     * Enable recursive file parsing.
     *
     * @return void
     */
    public function enableRecursion()
    {
        $this->recursion = true;
    }

    /**
     * Disable recursive file parsing.
     *
     * @return void
     */
    public function disableRecursion()
    {
        $this->recursion = false;
    }

    /**
     * Check if recursive file parsing is enabled.
     *
     * @return boolean
     */
    public function isRecursionEnabled()
    {
        return (boolean) $this->recursion;
    }
}
