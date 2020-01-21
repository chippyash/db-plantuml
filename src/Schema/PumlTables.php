<?php
/**
 * PlantUml Db Support
 *
 * @author    Ashley Kitson
 * @license   BSD-2 Clause
 * @copyright Ashley Kitson, UK, 2020
 */

namespace Chippyash\Schema;


class PumlTables
{
    /**
     * @var PumlTable[]
     */
    protected $tables;

    public function addTable(PumlTable $table): PumlTables
    {
        $this->tables[] = $table;
        return $this;
    }

    /**
     * @return PumlTable[]
     */
    public function getTables(): array
    {
        return $this->tables;
    }
}