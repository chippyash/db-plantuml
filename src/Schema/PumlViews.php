<?php
/**
 * PlantUml Db Support
 *
 * @author    Ashley Kitson
 * @license   BSD-2 Clause
 * @copyright Ashley Kitson, UK, 2020
 */

namespace Chippyash\Schema;


class PumlViews
{
    /**
     * @var array
     */
    protected $views = [];

    public function addView(PumlView $view):PumlViews
    {
        $this->views[] = $view;
        return $this;
    }

    /**
     * @return array
     */
    public function getViews(): array
    {
        return $this->views;
    }
}