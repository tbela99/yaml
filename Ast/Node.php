<?php

namespace Symfony\Component\Yaml\Ast;

use ArrayAccess;
use ArrayIterator;
use Exception;
use IteratorAggregate;
use Symfony\Component\Yaml\Dumper;
use Symfony\Component\Yaml\Parser;
use Symfony\Component\Yaml\Yaml;

class Node implements NodeInterface, IteratorAggregate, ArrayAccess {

    use NodeTrait;

    /**
     * @var NodeInterface[]
     */
    protected array $values = [];

    /**
     * @param NodeInterface $node
     * @param string|null $key
     * @return NodeInterface
     * @throws Exception
     */
    public function append(NodeInterface $node, ?string $key) {

        return $this->appendValue($node, $key);
    }

    /**
     * @param Node $node
     * @param string|null $key
     * @return $this
     */
    public function appendNode(Node $node, ?string $key) : Node {

       if (is_null($key) || $key === '') {

           $this->values[] = $node;
       }

       else {

           $this->values[$key] = $node;
       }

        return $this;
    }

    /**
     * @return NodeInterface
     * @throws Exception
     */
    public function appendEmptyLine() : NodeInterface {

        return $this->append(new EmptyLine(), null);
    }

    /**
     * @param mixed $value
     * @param string|null $key
     * @param Comment[] $comments
     * @return mixed
     * @throws Exception
     */
    public function appendValue($value, ?string $key, array $comments = []) {

        if (!is_null($value) && !is_scalar($value) && !($value instanceof NodeInterface)) {

            $node = new Node();

            $node->setComments($comments);
            $this->appendNode($node, null);

            foreach ($value as $k => $v) {

                $node[$k] = $v;
            }
        }

        else {

            $node = $value instanceof NodeInterface ? $value : new Value($value);

            $node->setComments($comments);

            if (is_null($key) || $key === '') {

                $this->values[] = $node;
            }
            else {

                $this->values[$key] = $node;
            }
        }

        return $value;
    }

    /**
     * @return array
     */
    public function getData() : array {

        return array_map(function (NodeInterface $node) {

            if ($node instanceof Node) {

                return $node->getData();
            }

            return $node->getValue();

        }, array_filter($this->values, function (NodeInterface $value) {

            return !($value instanceof Comment);

        }));
    }

    /**
     * @inheritDoc
     */
    public function getValues(): array {

        return $this->values;
    }

    public function jsonSerialize()
    {
        return $this->values;
    }

    public function __toString()
    {
        return (new Renderer())->render($this);
    }

    public function getIterator()
    {
        return new ArrayIterator($this->values);
    }

    protected function offsetCheck($offset) {

        if (strpos($offset, '.') > 0) {

            $offsets = explode('.', $offset);
            $offset = array_pop($offsets);

            $value = $this;

            foreach ($offsets as $off) {

                if (!isset($value->values[$off])) {

                    return false;
                }

                $value = $value->values[$off] ?? null;
            }

            return isset($value->values[$offset]) ? [$value, $offset] : false;
        }

        return isset($this->values[$offset]) ? [$this, $offset] : false;
    }

    /**
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        $map = $this->offsetCheck($offset);

        return $map !== false && isset($map[0]->values[$map[1]]);
    }

    /**
     * @param mixed $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        $map = $this->offsetCheck($offset);

        return $map === false ? null : ($map[0]->values[$map[1]] ?? null);
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {

        $offsets = explode('.', $offset);
        $offset = array_pop($offsets);

        $object = $this;

        foreach ($offsets as $off) {

            if (!isset($object->values[$off]) || !($object->values[$off] instanceof Node)) {

                $object->values[$off] = new Node();
            }

            $object = $object->values[$off];
        }

        if (is_scalar($value) || ($value instanceof Value)) {

            if (isset($object->values[$offset]) && ($object->values[$offset] instanceof Value)) {

                $object->values[$offset]->setValue($value);
            }

            else {

                $object->values[$offset] = $value instanceof Value ? $value : new Value($value);
            }
        }

        else if (is_array($value) || is_object($value)) {

            $object->values[$offset] = new Node();

            $object = $object->values[$offset];

            foreach ($value as $key => $val) {

                // recursion?
                $object[$key] = $val;
            }
        }
    }

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        $map = $this->offsetCheck($offset);

        if ($map !== false) {

            unset($map[0]->values[$map[1]]);
        }
    }
}