<?php
/**
 * PlantUml Db Support
 *
 * @author    Ashley Kitson
 * @license   BSD-2 Clause
 * @copyright Ashley Kitson, UK, 2020
 */
namespace Chippyash\Renderer;

use Chippyash\Schema\PumlColumn;
use Chippyash\Schema\PumlNode;
use Chippyash\Schema\PumlRelationship;
use Chippyash\Schema\PumlSchema;
use Chippyash\Schema\PumlTable;

/**
 * Abstract Renderer
 *
 * You may extend this class for your renderer
 * @see MysqlRenderer
 */
abstract class AbstractRenderer implements DDlRendering
{
    public function renderDdl(PumlSchema $schema): string
    {
        $out = $this->renderTables($schema);

        return $out;
    }

    abstract protected function renderTables(PumlSchema $schema): string;

    abstract protected function getRenderedAliasColTypes(PumlSchema $schema): array;

    /**
     * @param PumlSchema $schema
     * @return array [typeName => PumlType]
     */
    protected function getAliasColTypes(PumlSchema $schema): array
    {
        //The types that have a dependency association [type => ?]
        $typeRaw = array_filter(
            array_flip(
                array_unique(
                    array_map(
                        function (PumlColumn $column) {
                            return $column->getType();
                        },
                        array_merge(
                            ...array_map(
                                function(PumlTable $table) {
                                    return $table->getAttributes();
                                },
                                $schema->getTables()->getTables()
                            )
                        )
                    )
                )
            ),
            function ($type) use ($schema) {
                try {
                    $schema->getRelationships()->getRelationshipForType($type);
                    return true;
                } catch (\InvalidArgumentException $e) {
                    return false;
                }
            },
            ARRAY_FILTER_USE_KEY
        );

        //[typeName => PumlType]
        return array_map(
            function(PumlRelationship $relationship) use ($schema) {
                //find the relationship that is a Type
                $rel = array_filter(
                    $relationship->getNodes(),
                    function (PumlNode $node) use ($schema) {
                        return $schema->getTypes()->hasType($node->getId());
                    }
                );
                return $schema->getTypes()->findType(array_pop($rel)->getId());
            },
            // [type => relationship for type]
            array_combine(
                array_flip($typeRaw),
                array_map(
                    function($key) use($schema) {
                        return $schema->getRelationships()->getRelationshipForType($key);
                    },
                    array_keys($typeRaw)
                )
            )
        );
    }
}