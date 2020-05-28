<?php

namespace Symfony\Component\Yaml\Ast;

use JsonSerializable;

interface NodeInterface extends JsonSerializable  {

    public function addComment(Comment $comment): NodeInterface;

    /**
     * @param Comment[] $comments
     * @return NodeInterface
     */
    public function setComments(array $comments): NodeInterface;
    public function getComments(): array;
    public function removeComments(): NodeInterface;
    public function getValue();
}