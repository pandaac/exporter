<?php

namespace pandaac\Exporter;

use Illuminate\Support\Collection;

class Output
{
    /**
     * Holds the output collection.
     *
     * @var \Illuminate\Support\Collection
     */
    protected $collection;

    /**
     * Create a new output instance.
     *
     * @param  \Illuminate\Support\Collection  $collection
     * @return void
     */
    public function __construct(Collection $collection)
    {
        $this->collection = $collection;
    }

    /**
     * Retrieve the exporter meta details.
     *
     * @param  boolean  $data  false
     * @return \Illuminate\Support\Collection
     */
    public function meta($data = false)
    {
        $meta = new Collection([
            '@version'      => Exporter::VERSION,
            '@issues'       => Exporter::ISSUES,
            '@generated'    => $this->generated(),
        ]);

        if ($data) {
            $meta->put('@data', $this->collection);
        }

        return $meta;
    }

    /**
     * Retrieve an array of timestamps.
     *
     * @return \Illuminate\Support\Collection
     */
    protected function generated()
    {
        return new Collection([
            'unix'      => time(),
            'formatted' => date('jS F, Y H:i:s e'),
        ]);
    }

    /**
     * Attempt to call missing methods through the collection.
     *
     * @param  string  $name
     * @param  array  $arguments
     * @return mixed
     */
    public function __call($name, array $arguments)
    {
        if (! method_exists($this->collection, $name)) {
            trigger_error('Call to undefined method '.__CLASS__.'::'.$name.'()', E_USER_ERROR);
        }

        return call_user_func_array([$this->collection, $name], $arguments);
    }
}
