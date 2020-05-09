<?php

namespace Symfony\Component\Yaml\Ast;

class EmptyLine extends Value {

    public function getValue()
    {
        return ' ';
    }
}