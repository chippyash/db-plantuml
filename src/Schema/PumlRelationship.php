<?php
/**
 * PlantUml Db Support
 *
 * @author    Ashley Kitson
 * @license   BSD-2 Clause
 * @copyright Ashley Kitson, UK, 2020
 */
namespace Chippyash\Schema;

class PumlRelationship
{
    /**
     * @var string
     */
    protected $id;
    /**
     * @var string
     */
    protected $name;
    /**
     * @var PumlNode[]
     */
    protected $nodes;

    /**
     * PumlRelationship constructor.
     * @param string $id
     * @param string|null $name
     */
    public function __construct(string $id, ?string $name='')
    {
        $this->id = $id;
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    public function addNode(PumlNode $node): PumlRelationship
    {
        $this->nodes[] = $node;
        return $this;
    }

    public function getFromNode(): PumlNode
    {
        return $this->nodes[0];
    }

    public function getToNode(): PumlNode
    {
        return $this->nodes[1];
    }

    /**
     * @return PumlNode[]
     */
    public function getNodes(): array
    {
        return $this->nodes;
    }
}