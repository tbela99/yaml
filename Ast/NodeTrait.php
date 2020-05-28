<?php

namespace Symfony\Component\Yaml\Ast;

Trait NodeTrait {

    protected array $comments = [];

    public function escapeKey(string $key) {

        return str_replace('.', '\\.', $key);
    }

    public function unescapeKey(string $key) {

        return str_replace('\\.', '.', $key);
    }

    /**
     * @param Comment|string $comment
     * @return NodeInterface
     */
    public function addComment($comment): NodeInterface {

        if (is_string($comment)) {

            $comment = ltrim($comment);

            if (substr($comment, 0, 1) != '#') {

                $comment = '# '.$comment;
            }
        }

        $this->comments[] = $comment instanceof Comment ? $comment : new Comment($comment);
        return $this;
    }

    /**
     * @param Comment[]|string[] $comments
     * @return NodeInterface
     */
    public function setComments(array $comments): NodeInterface {

        $this->comments = [];

        foreach ($comments as $comment) {

            $this->addComment($comment);
        }

        return $this;
    }

    public function removeComments(): NodeInterface {

        $this->comments = [];
        return $this;
    }

    public function getComments(): array {

        return $this->comments;
    }
}