<?php

namespace Symfony\Component\Yaml\Ast;

class Value implements NodeInterface {

    use NodeTrait;

    protected $value = null;

    public function __construct($value = null)
    {

        if (!is_null($value)) {

            $this->setValue($value);
        }
    }

    public function setValue ($value) : NodeInterface {

        $this->value = $value;
        return $this;
    }

    public function getValue () {

        return $this->value;
    }

    public function __toString() {

        return $this->value;
    }

    public function jsonSerialize()
    {
        return $this;
    }
}