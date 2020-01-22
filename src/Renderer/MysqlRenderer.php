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
use Chippyash\Schema\PumlProc;
use Chippyash\Schema\PumlRelationship;
use Chippyash\Schema\PumlSchema;
use Chippyash\Schema\PumlTable;
use Chippyash\Schema\PumlTrigger;
use Chippyash\Schema\PumlType;
use Chippyash\Schema\PumlView;

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
            $out .= "CREATE TABLE `{$table->getName()}` (\n";
            /** @var PumlColumn $column */
            foreach($table->getAttributes() as $column) {
                $out .= $pad . "`{$column->getName()}`" . ' ';
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
                    ? ' AUTO_INCREMENT'
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

    protected function renderIndexes(PumlSchema $schema): string
    {
        $out = '';
        $pad = str_pad('', 4);
        /** @var PumlTable $table */
        foreach($schema->getTables()->getTables() as $table) {
            foreach($table->getOperations() as $opKey => $op) {
                list($method, $body) = explode('(', $op);
                $method = 'render' . ucfirst($method);
                $out .= $this->$method(rtrim($body, ')'), $table->getName(), $opKey);
            }
        }

        return $out;
    }

    private function renderIndex($cols, $tName, $opKey) {
        $cols = str_replace([',',' '], ['`,`',''], $cols);
        return "CREATE INDEX idx_{$opKey} ON {$tName} (`$cols`);\n\n";
    }

    private function renderKey($cols, $tName, $opKey) {
        $cols = str_replace([',',' '], ['`,`',''], $cols);
        return "CREATE UNIQUE INDEX idx_{$opKey} ON {$tName} (`$cols`);\n\n";
    }

    private function renderFk($args, $tName, $opKey) {
        list($refCol, $targetTable) = explode(',', $args);
        $targetTable = trim($targetTable);
        $pad = str_pad('', 4);
        return "ALTER TABLE `{$tName}` ADD FOREIGN KEY fk_{$opKey} (`{$refCol}`)\n"
            . $pad . "REFERENCES `{$targetTable}` (`id`)\n"
            . $pad . "ON DELETE CASCADE\n"
            . $pad . "ON UPDATE RESTRICT;\n\n";
    }

    protected function renderViews(PumlSchema $schema): string
    {
        $out = '';
        $pad = str_pad('', 4);
        /** @var PumlView $view */
        foreach($schema->getViews()->getViews() as $view) {
            $definition = array_filter(
                $view->getOperations(),
                function($op) {
                    return strpos($op, 'select(') === 0;
                }
            );
            if (count($definition) !== 1) {
                $out .= "# No select method found in view definition: {$view->getName()}\n\n";
                continue;
            }

            $out .= "CREATE VIEW `{$view->getName()}` \n"
                 . $pad . 'AS SELECT ' . str_replace('select(', '',substr(array_pop($definition), 0, -1))
                 . ";\n\n";
        }

        return $out;
    }

    protected function renderProcs(PumlSchema $schema): string
    {
        $out = '';
        if (count($schema->getProcs()->getProcs()) == 0) {
            return $out;
        }
        $out .= "DELIMITER //\n\n";
        $pad = str_pad('', 4);

        /** @var PumlProc $proc */
        foreach($schema->getProcs()->getProcs() as $proc) {
            foreach($proc->getOperations() as $op) {
                $out .= "CREATE PROCEDURE sp_{$proc->getName()}_{$op}\n"
                    . $pad . "BEGIN\n"
                    . $pad . $pad . "# complete proc body and parameter typing\n"
                    . $pad . "END;\n\n";
            }
        }

        $out .= "DELIMITER ;\n\n";

        return $out;

    }

    protected function renderTriggers(PumlSchema $schema): string
    {
        $out = '';
        if (count($schema->getTriggers()->getTriggers()) == 0) {
            return $out;
        }
        $pad = str_pad('', 4);

        /** @var PumlTrigger $proc */
        foreach($schema->getTriggers()->getTriggers() as $proc) {
            $tableName = '<table_name>';
            //see if there is a relationship between trigger and a table
            $relationships = $schema->getRelationships()
                ->getRelationshipsForNode($proc->getId())
                ->getRelations();
            if (count($relationships) > 0) {
                /** @var PumlRelationship $firstRelation */
                $firstRelation = array_pop($relationships);
                $rels = array_filter(
                    $firstRelation->getNodes(),
                    function(PumlNode $node) use ($proc) {
                        return $node->getId() != $proc->getId();
                    }
                );
                try {
                    $tableName = $schema->getTables()
                        ->getTableForId(array_pop($rels)->getId())
                        ->getName();
                } catch (\InvalidArgumentException $e) {
                    //do nothing
                }
            }
            foreach($proc->getOperations() as $op) {
                $opName = rtrim($op, '()');
                switch ($opName) {
                    case 'beforeUpdate':
                        $triggerTime = 'BEFORE UPDATE';
                        break;
                    case 'afterUpdate':
                        $triggerTime = 'AFTER UPDATE';
                        break;
                    case 'beforeInsert':
                        $triggerTime = 'BEFORE INSERT';
                        break;
                    case 'afterInsert':
                        $triggerTime = 'AFTER INSERT';
                        break;
                    case 'beforeDelete':
                        $triggerTime = 'BEFORE DELETE';
                        break;
                    case 'afterDelete':
                        $triggerTime = 'AFTER DELETE';
                        break;
                    default:
                        $triggerTime = '<trigger_time>';
                }
                $out .= "CREATE DEFINER=`root`@`localhost` TRIGGER {$proc->getName()}_{$opName}\n"
                    . $pad . "{$triggerTime} ON `{$tableName}` FOR EACH ROW\n"
                    . $pad . "BEGIN\n"
                    . $pad . $pad . "# complete trigger body and declaration\n"
                    . $pad . "END;\n\n";
            }
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