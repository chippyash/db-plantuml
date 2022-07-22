<?php
/**
 * PlantUml Db Support
 *
 * @author    Ashley Kitson
 * @license   BSD-2 Clause
 * @copyright Ashley Kitson, UK, 2020
 */

namespace Chippyash\Schema;


class PumlTriggers
{
    /**
     * @var PumlTrigger[]
     */
    protected $triggers;

    public function addTrigger(PumlTrigger $trigger): PumlTriggers
    {
        $this->triggers[] = $trigger;
        return $this;
    }

    /**
     * @return PumlTrigger[]
     */
    public function getTriggers(): array
    {
        if ($this->triggers == null) {
            return [];
        }
        return $this->triggers;
    }
}