<?php
/**
 * PlantUml Db Support
 *
 * @author    Ashley Kitson
 * @license   BSD-2 Clause
 * @copyright Ashley Kitson, UK, 2020
 */
namespace Chippyash\Schema;

/**
 * Container for DDL Schema
 */
class PumlSchema
{
    /**
     * @var PumlTables
     */
    protected $tables;
    /**
     * @var PumlViews
     */
    protected $views;
    /**
     * @var PumlProcs
     */
    protected $procs;
    /**
     * @var PumlTriggers
     */
    protected $triggers;
    /**
     * @var PumlTypes
     */
    protected $types;
    /**
     * @var PumlRelationships
     */
    protected $relationships;

    /**
     * PumlSchema constructor.
     * @param PumlTables $tables
     * @param PumlViews $views
     * @param PumlTypes $types
     * @param PumlProcs $procs
     * @param PumlTriggers $triggers
     * @param PumlRelationships $relationships
     */
    public function __construct(PumlTables $tables, PumlViews $views, PumlTypes $types, PumlProcs $procs, PumlTriggers $triggers, PumlRelationships $relationships)
    {
        $this->tables = $tables;
        $this->views = $views;
        $this->types = $types;
        $this->procs = $procs;
        $this->triggers = $triggers;
        $this->relationships = $relationships;
    }

    /**
     * @return PumlTables
     */
    public function getTables(): PumlTables
    {
        return $this->tables;
    }

    /**
     * @return PumlProcs
     */
    public function getViews(): PumlProcs
    {
        return $this->views;
    }

    /**
     * @return PumlProcs
     */
    public function getProcs(): PumlProcs
    {
        return $this->procs;
    }

    /**
     * @return PumlTriggers
     */
    public function getTriggers(): PumlTriggers
    {
        return $this->triggers;
    }

    /**
     * @return PumlTypes
     */
    public function getTypes(): PumlTypes
    {
        return $this->types;
    }

    /**
     * @return PumlRelationships
     */
    public function getRelationships(): PumlRelationships
    {
        return $this->relationships;
    }
}