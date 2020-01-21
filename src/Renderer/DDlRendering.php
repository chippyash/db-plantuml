<?php
/**
 * PlantUml Db Support
 *
 * @author    Ashley Kitson
 * @license   BSD-2 Clause
 * @copyright Ashley Kitson, UK, 2020
 */
namespace Chippyash\Renderer;

use Chippyash\Schema\PumlSchema;

/**
 * Class that can render SQL DDL from a PumlSchema instance
 */
interface DDlRendering
{
    /**
     * Render DDL string
     *
     * @param PumlSchema $schema
     * @return string
     */
    public function renderDdl(PumlSchema $schema): string;
}