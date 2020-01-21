<?php
/**
 * PlantUml Db Support
 *
 * @author    Ashley Kitson
 * @license   BSD-2 Clause
 * @copyright Ashley Kitson, UK, 2020
 */
namespace Chippyash\Schema;

trait Attributing
{
    /**
     * @var array
     */
    protected $attributes = [];

    public function addAttribute(string $id, string $content)
    {
        $this->attributes[$id] = $this->parseAttribute($content);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    protected function parseAttribute(string $content)
    {
        return $content;
    }
}