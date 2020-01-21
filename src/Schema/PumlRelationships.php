<?php
/**
 * PlantUml Db Support
 *
 * @author    Ashley Kitson
 * @license   BSD-2 Clause
 * @copyright Ashley Kitson, UK, 2020
 */
namespace Chippyash\Schema;

class PumlRelationships
{
    /**
     * @var PumlRelationship[]
     */
    protected $relations;

    public function addRelationship(PumlRelationship $relationship): PumlRelationships
    {
        $this->relations[] = $relationship;
        return $this;
    }

    public function getRelationshipsForNode(string $nodeId): PumlRelationships
    {
        $result = array_filter(
            $this->relations,
            function (PumlRelationship $relationship) use ($nodeId) {
                return $relationship->getFromNode()->getId() == $nodeId
                    ||  $relationship->getToNode()->getId() == $nodeId;
            }
        );
        $rels = new PumlRelationships();
        foreach ($result as $rel) {
            $rels->addRelationship($rel);
        }

        return $rels;
    }

    public function getRelationshipForType(string $type): PumlRelationship
    {
        $rels = array_filter(
            $this->relations,
            function(PumlRelationship $relationship) use($type) {
                return $relationship->getName() == $type;
            }
        );
        if (count($rels) == 0) {
            throw new \InvalidArgumentException("No relationship for type: {$type}");
        }
        return array_pop($rels);
    }
}