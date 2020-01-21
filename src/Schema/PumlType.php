<?php
/**
 * PlantUml Db Support
 *
 * @author    Ashley Kitson
 * @license   BSD-2 Clause
 * @copyright Ashley Kitson, UK, 2020
 */

namespace Chippyash\Schema;


class PumlType
{
    use Attributing;
    use NameConstructing;

    public const TP_ENUM = 'enum';
    public const TP_SET = 'set';
    /**
     * @var string
     */
    protected $type;

    /**
     * @param string $type
     *
     * @return PumlType
     */
    public function setType(string $type): PumlType
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }
}