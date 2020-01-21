<?php
/**
 * PlantUml Db Support
 *
 * @author    Ashley Kitson
 * @license   BSD-2 Clause
 * @copyright Ashley Kitson, UK, 2020
 */

namespace Chippyash\Schema;


class PumlTypes
{
    /**
     * @var PumlType[]
     */
    protected $enums = [];
    /**
     * @var PumlType[]
     */
    protected $sets = [];


    public function addEnum(PumlType $enum): PumlTypes
    {
        $this->enums[] = $enum->setType(PumlType::TP_ENUM);
        return $this;
    }

    public function addSet(PumlType $set): PumlTypes
    {
        $this->sets[] = $set->setType(PumlType::TP_SET);
        return $this;
    }

    /**
     * @return PumlType[]
     */
    public function getEnums(): array
    {
        return $this->enums;
    }

    /**
     * @return PumlType[]
     */
    public function getSets(): array
    {
        return $this->sets;
    }

    public function findType(string $id): PumlType
    {
        $ret = array_filter(
            array_merge($this->enums, $this->sets),
            function(PumlType $type) use($id) {
                return $type->getId() == $id;
            }
        );

        return array_pop($ret);
    }

    public function hasType(string $id): bool
    {
        return array_reduce(
            array_merge($this->enums, $this->sets),
            function($carry, PumlType $type) use($id) {
                return $type->getId() == $id ? true : $carry;
            },
            false
        );
    }
}