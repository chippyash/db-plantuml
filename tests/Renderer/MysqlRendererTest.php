<?php
/**
 * PlantUml Db Support
 *
 * @author    Ashley Kitson
 * @license   BSD-2 Clause
 * @copyright Ashley Kitson, UK, 2020
 */

namespace Test\Chippyash\Renderer;

use Chippyash\Renderer\MysqlRenderer;
use Chippyash\Schema\PumlSchema;
use PHPUnit\Framework\TestCase;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;

class MysqlRendererTest extends TestCase
{
    /**
     * @var MysqlRenderer
     */
    protected $sut;
    /**
     * @var PumlSchema
     */
    protected $schema;

    public function testTablesAreRendered()
    {
        $output = $this->sut->renderDdl($this->schema);
        $this->assertStringContainsString("CREATE TABLE `user`", $output);
    }

    protected function setUp(): void
    {
        $this->sut = new MysqlRenderer();
        include_once(dirname(__DIR__) . '/fixtures/evalled.php');
        $this->schema = $schema;
    }
}
