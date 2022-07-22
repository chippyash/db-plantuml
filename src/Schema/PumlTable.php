<?php
/**
 * PlantUml Db Support
 *
 * @author    Ashley Kitson
 * @license   BSD-2 Clause
 * @copyright Ashley Kitson, UK, 2020
 */
namespace Chippyash\Schema;

class PumlTable
{
    use Attributing;
    use Operating;
    use NameConstructing;

    protected function parseAttribute(string $content)
    {
        $parts = explode(' ', $content);
        $column = new PumlColumn($parts[0], $parts[1]);
        if (strpos($content, 'PRIMARY KEY') > 0) {
            $column->setIsPrimary(true);
        }
        if (strpos($content, 'AUTOINCREMENT') > 0) {
            $column->setIsAutoincrementing(true);
        }
        if (strpos($content, 'NOT NULL') > 0) {
            $column->setIsNull(false);
        }
        if (strpos($content, 'UNSIGNED') > 0) {
            $column->setIsUnsigned(true);
        }

        return $column;
    }
}