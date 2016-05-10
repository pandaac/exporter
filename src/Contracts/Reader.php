<?php

namespace pandaac\Exporter\Contracts;

interface Reader
{
    /**
     * Open a file process.
     *
     * @param  string  $file
     * @return boolean
     */
    public function open($file);

    /**
     * Read the file process.
     *
     * @return boolean
     */
    public function read();

    /**
     * Close the file process.
     *
     * @return boolean
     */
    public function close();

    /**
     * Set the parent element if there is one.
     *
     * @return void
     */
    public function setParentIfAvailable();

    /**
     * Revert back to the previous parent when we leave the active element.
     *
     * @return void
     */
    public function revertParentIfNecessary();

    /**
     * Check if the current element matches the specified name.
     *
     * @param  string  $element
     * @return boolean
     */
    public function is($element);

    /**
     * Get the name of the current node.
     *
     * @return string
     */
    public function name();
 
    /**
     * Get the type of the current node.
     *
     * @return string
     */
    public function type();
 
    /**
     * Get the value of the current node.
     *
     * @return string
     */
    public function value();

    /**
     * Get the name of the parent element, if there is one.
     *
     * @param  string  $element  null
     * @return boolean|string
     */
    public function parent($element = null);

    /**
     * Get the value of a specified attribute of the current node.
     *
     * @param  string  $attribute
     * @return mixed
     */
    public function attribute($attribute);

    /**
     * Get the values of all the specified attributes of the current node.
     *
     * @return mixed
     */
    public function attributes($attributes = []);

    /**
     * Check if the current node is an element.
     *
     * @var boolean
     */
    public function isElement();

    /**
     * Check if the current node is an attribute.
     *
     * @var boolean
     */
    public function isAttribute();

    /**
     * Check if the current node is a comment.
     *
     * @var boolean
     */
    public function isComment();
}
