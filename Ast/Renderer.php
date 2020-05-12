<?php

namespace Symfony\Component\Yaml\Ast;

use Symfony\Component\Yaml\Inline;

class Renderer {

    /**
     * @param Node $node
     * @param int $indent
     * @param int $tag any Yaml::DUMP_* constant. null is always rendered as an empty string
     * @return string
     */
    public function render (Node $node, int $indent = 0, $tag = 0): string {

        $yaml = '';
        $ident = str_repeat(' ', $indent);

        $ident .= $ident;

        $values = $node->getValues();
        $isAssociative = $this->isAssociative($values);

        foreach ($node as $key => $value) {

            foreach ($value->getComments() as $comment) {

                $yaml .= $ident.$comment->getValue()."\n";
            }

            if ($value instanceof Node) {

                $yaml .= $ident.($isAssociative ? Inline::dump($key).':' : '-')."\n".$this->render($value, $indent + 1, $tag)."\n";
            }

            else {

                if ($value instanceof Comment) {

                    $yaml .= $ident.$value->getValue()."\n";
                }

                else if ($value instanceof EmptyLine) {

                    $yaml .= "\n";
                }

                else {

                    $val = $value->getValue();
                    $yaml .= $ident.($isAssociative ? Inline::dump($key).':' : '-').' '.(is_null($val) ? '' : Inline::dump($val, $tag))."\n";
                }
            }
        }

        return rtrim($yaml, "\n");
    }

    protected function isAssociative(array $values) {

        foreach (array_keys($values) as $index => $key) {

            if (!is_numeric($key) || $key != $index) {

                return true;
            }
        }

        return false;
    }
}