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
use Chippyash\Schema\PumlSchema;
use Chippyash\Schema\PumlTable;
use Chippyash\Schema\PumlType;

/**
 * Renders physical diagrams to MySql syntax DDL
 */
class MysqlRenderer extends AbstractRenderer
{
    protected function renderTables(PumlSchema $schema): string
    {
        $out = '';
        $pad = str_pad('', 4);
        $aliases = $this->getRenderedAliasColTypes($schema);
        /** @var PumlTable $table */
        foreach($schema->getTables()->getTables() as $table) {
            $out .= "create table {$table->getName()} (\n";
            /** @var PumlColumn $column */
            foreach($table->getAttributes() as $column) {
                $out .= $pad . $column->getName() . ' ';
                $colType = array_key_exists($column->getType(), $aliases)
                    ? $aliases[$column->getType()]
                    : $column->getType();
                $out .= $column->getLength() > 0
                    ? "{$colType}({$column->getLength()})"
                    : $colType;
                $out .= $column->isPrimary()
                    ? ' PRIMARY KEY'
                    : '';
                $out .= $column->isAutoincrementing()
                    ? ' AUTOINCREMENT'
                    : '';
                $out .= $column->isNull()
                    ? ''
                    : ' NOT NULL';
                $out .= ",\n";
            }
            $out = rtrim($out, ",\n") . "\n);\n\n";
        }

        return $out;
    }

    /**
     * @param PumlSchema $schema
     * @return string[]  [typeName => renderedType]
     */
    protected function getRenderedAliasColTypes(PumlSchema $schema): array
    {
        return array_map(
            function(PumlType $type) {
                $variations = implode("','", $type->getAttributes());
                switch ($type->getType()) {
                    case PumlType::TP_SET:
                        return "set('{$variations}')";
                        break;
                    case PumlType::TP_ENUM:
                        return "enum('{$variations}')";
                }
            },
            $this->getAliasColTypes($schema)
        );
    }
}