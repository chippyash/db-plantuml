<?php
/**
 * PlantUml Db Support
 *
 * @author    Ashley Kitson
 * @license   BSD-2 Clause
 * @copyright Ashley Kitson, UK, 2020
 */

namespace Chippyash\Schema;


class PumlColumn
{
    /**
     * @var string
     */
    protected $name;
    /**
     * @var string
     */
    protected $type;
    /**
     * @var int
     */
    protected $length = 0;
    /**
     * @var bool
     */
    protected $isNull = true;
    /**
     * @var bool
     */
    protected $isPrimary = false;
    /**
     * @var bool
     */
    protected $isAutoincrementing = false;

    /**
     * PumlColumn constructor.
     * @param string $name
     * @param string $type
     */
    public function __construct(string $name, string $type)
    {
        $this->name = $name;
        $this->type = $this->setLengthFromType($type);
    }

    private function setLengthFromType(string $type)
    {
        $match = [];
        preg_match('/^(?P<name>\w+\[(?P<length>\d+)\])$/', $type,$match);
        if (empty($match['length'])) {
            return $type;
        }
        $this->length = (int) $match['length'];

        return preg_replace('/\[\d+\]/', '', $match['name']);
    }

    /**
     * @param bool $isNull
     * @return PumlColumn
     */
    public function setIsNull(bool $isNull): PumlColumn
    {
        $this->isNull = $isNull;
        return $this;
    }

    /**
     * @param bool $isPrimary
     * @return PumlColumn
     */
    public function setIsPrimary(bool $isPrimary): PumlColumn
    {
        $this->isPrimary = $isPrimary;
        return $this;
    }

    /**
     * @param bool $isAutoincrementing
     * @return PumlColumn
     */
    public function setIsAutoincrementing(bool $isAutoincrementing): PumlColumn
    {
        $this->isAutoincrementing = $isAutoincrementing;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return int
     */
    public function getLength(): int
    {
        return $this->length;
    }

    /**
     * @return bool
     */
    public function isNull(): bool
    {
        return $this->isNull;
    }

    /**
     * @return bool
     */
    public function isPrimary(): bool
    {
        return $this->isPrimary;
    }

    /**
     * @return bool
     */
    public function isAutoincrementing(): bool
    {
        return $this->isAutoincrementing;
    }
}