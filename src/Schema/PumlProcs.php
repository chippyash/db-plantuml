<?php
/**
 * PlantUml Db Support
 *
 * @author    Ashley Kitson
 * @license   BSD-2 Clause
 * @copyright Ashley Kitson, UK, 2020
 */

namespace Chippyash\Schema;


class PumlProcs
{
    /**
     * @var array
     */
    protected $procs = [];

    public function addProc(PumlProc $proc)
    {
        $this->procs[] = $proc;
        return $this;
    }

    /**
     * @return array
     */
    public function getProcs(): array
    {
        return $this->procs;
    }
}