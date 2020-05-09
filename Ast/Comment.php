<?php

namespace Symfony\Component\Yaml\Ast;

class Comment extends Value {

    public function setValue ($value) : NodeInterface {

        $this->value = trim($value);
        return $this;
    }

}