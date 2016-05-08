<?php

namespace pandaac\Exporter;

use Exception;
use XMLReader;

use pandaac\Exporter\Contracts\Parser as Contract;

abstract class Parser implements Contract
{
    /**
     * Holds the file path.
     *
     * @var string
     */
    protected $file;

    /**
     * Holds the reader implementation.
     *
     * @var \XMLReader
     */
    protected $reader;

    /**
     * Holds the response implementation.
     *
     * @var array
     */
    protected $response = [];

    /**
     * Holds the previous node.
     *
     * @var string
     */
    protected $previousNode;

    /**
     * Holds the settings array.
     *
     * @var array
     */
    protected $settings;

    /**
     * Instantiate a new parser object.
     *
     * @param  string  $file
     * @return void
     */
    public function __construct($file)
    {
        $this->file = realpath($file);
        $this->reader = new XMLReader;

        if (! file_exists($this->file)) {
            throw new Exception(sprintf('Could not locate file %s', $this->file));
        }
    }

    /**
     * Get attributes from the reader implementation.
     *
     * @return array
     */
    protected function attributes()
    {
        $attributes = [];

        foreach (func_get_args() as $attribute) {
            if (is_array($attribute)) {
                if ($alternative = call_user_func_array([$this, 'attributes'], $attribute)) {
                    $attributes[key($alternative)] = current($alternative);
                }
                
                continue;
            }

            $value = $this->reader->getAttribute($attribute);

            if (preg_match('/^(\-?[0-9]+)$/', $value)) {
                $value = (int) $value;
            }

            $attributes[$attribute] = $value;
        }

        return array_filter($attributes, function ($attribute) {
            return ! is_null($attribute);
        });
    }

    /**
     * Assign the settings.
     *
     * @param  array  $settings
     * @param  array  $defaults  []
     * @return boolean
     */
    protected function assignSettings(array $settings, array $defaults = [])
    {
        $this->settings = array_merge($defaults, $settings);
    }

    /**
     * Check if a setting is enabled.
     *
     * @param  string  $name
     * @return boolean
     */
    protected function isSettingEnabled($name)
    {
        return isset($this->settings[$name]) and $this->settings[$name];
    }

    /**
     * Get the node value.
     *
     * @return mixed
     */
    protected function value()
    {
        return $this->reader->value;
    }

    /**
     * Check if the node is an element.
     *
     * @param  string  $name  null
     * @return boolean
     */
    protected function isElement($name = null)
    {
        if ($name and strtolower($this->reader->name) !== strtolower($name)) {
            return false;
        }

        return $this->reader->nodeType === XMLReader::ELEMENT;
    }

    /**
     * Check if the node is a comment.
     *
     * @return boolean
     */
    protected function isComment()
    {
        return $this->reader->nodeType === XMLReader::COMMENT;
    }

    /**
     * Check if the previous node was a specific element.
     *
     * @param  string  $name
     * @return boolean
     */
    protected function previousElementWas($name)
    {
        return $this->previousNode === strtolower($name);
    }

    /**
     * Sets the previous node.
     *
     * @return void
     */
    protected function setPreviousElement()
    {
        if (! $this->isElement()) {
            return;
        }

        $this->previousNode = strtolower($this->reader->name);
    }
}
