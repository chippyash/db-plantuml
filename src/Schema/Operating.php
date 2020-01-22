<?php
/**
 * PlantUml Db Support
 *
 * @author    Ashley Kitson
 * @license   BSD-2 Clause
 * @copyright Ashley Kitson, UK, 2020
 */
namespace Chippyash\Schema;

trait Operating
{
    /**
     * @var array
     */
    protected $operations = [];

    public function addOperation(string $id, string $content)
    {
        $this->operations[$id] = $this->parseOperation($content);
        return $this;
    }

    /**
     * @return array [id => opContent]
     */
    public function getOperations(): array
    {
        return $this->operations;
    }

    protected function parseOperation(string $content)
    {
        return $content;
    }
}