<?php

namespace Zoho\CRM\Api;

use function Zoho\CRM\booleanToString;

class UrlParameters implements \ArrayAccess
{
    private $parameters = [];

    public function __construct(array $parameters = [])
    {
        $this->parameters = $parameters;
    }

    public function extend($others)
    {
        if ($others instanceof UrlParameters)
            $others = $others->toArray();

        return new UrlParameters(array_replace($this->parameters, $others));
    }

    public function contains($key)
    {
        return isset($this->parameters[$key]);
    }

    public function get($key)
    {
        return isset($this->parameters[$key]) ? $this->parameters[$key] : null;
    }

    public function reset()
    {
        $this->parameters = [];
    }

    public function offsetSet($key, $value)
    {
        if ($key === null)
            $this->parameters[] = $value;
        else
            $this->parameters[$key] = $value;
    }

    public function offsetExists($key)
    {
        return $this->contains($key);
    }

    public function offsetUnset($key)
    {
        unset($this->parameters[$key]);
    }

    public function offsetGet($key)
    {
        return $this->get($key);
    }

    public function toArray()
    {
        return $this->parameters;
    }

    public function toString()
    {
        $chunks = [];

        foreach ($this->parameters as $key => $value) {
            $chunk = "$key";

            // Support for parameters with a value
            if ($value !== null) {

                // Support for arrays
                if (is_array($value)) {
                    // Stringify boolean values
                    $value = array_map(function($el) {
                        return is_bool($el) ? booleanToString($el) : $el;
                    }, $value);

                    // Join elements with comas i.e.: (el1,el2,el3,el4)
                    $value = '(' . implode(',', $value) . ')';

                } else {
                    // Stringify boolean values
                    if (is_bool($value)) {
                        $value = booleanToString($value);
                    }
                }

                $chunk .= "=$value";
            }

            $chunks[] = $chunk;
        }

        return implode('&', $chunks);
    }

    public function __toString()
    {
        return $this->toString();
    }
}
