<?php
/**
 * PlantUml Db Support
 *
 * @author    Ashley Kitson
 * @license   BSD-2 Clause
 * @copyright Ashley Kitson, UK, 2020
 */
namespace Test\Chippyash\Command;

use Chippyash\Command\GenerateCommand;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Tester\CommandTester;

class GenerateCommandTest extends TestCase
{
    /**
     * @var CommandTester
     */
    protected $sut;
    /**
     * @var vfsStreamDirectory
     */
    protected $fileSystem;

    public function testYouMustProvideAnInputFileArgument()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Not enough arguments (missing: "input, output")');
        $this->sut->execute([]);

    }
    public function testYouMustProvideAnOutputFileArgument()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Not enough arguments (missing: "output")');
        $this->sut->execute([
            'input' => 'foo'
        ]);
    }

    public function testInputFileMustExist()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('foo.puml does not exist');
        $this->sut->execute([
            'input' => 'foo.puml',
            'output' => $this->fileSystem->url() .'/out.sql'
        ]);
    }

    public function testOutputFileMustBeWriteable()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('vfs://root/out.sql is not writeable');
        $this->fileSystem->chmod(0400);
        $this->sut->execute([
            'input' => dirname(__DIR__) . '/fixtures/User-Logical.xmi',
            'output' => $this->fileSystem->url() .'/out.sql'
        ]);
    }
    
    public function testTheCommandAcceptsADebugOption()
    {
        $this->sut->execute([
            'input' => dirname(__DIR__) . '/fixtures/User-Logical.xmi',
            'output' => $this->fileSystem->url() .'/out.sql',
            '--debug' => 1
        ]);
        $this->assertFileExists($this->fileSystem->url() .'/pumldbconv.log');
    }

    public function testDebugOptionWillWriteDebugFile()
    {
        $this->sut->execute([
            'input' => dirname(__DIR__) . '/fixtures/User-Logical.xmi',
            'output' => $this->fileSystem->url() .'/out.sql',
            '--debug' => 1
        ]);

        $this->assertFileExists($this->fileSystem->url() .'/pumldbconv.log');
    }

    public function testByDefaultDebugFileIsNotWritten()
    {
        $this->sut->execute([
            'input' => dirname(__DIR__) . '/fixtures/User-Physical.xmi',
            'output' => $this->fileSystem->url() .'/out.sql'
        ]);

        $this->assertFileNotExists($this->fileSystem->url() .'/pumldbconv.log');
    }

    public function testCommandCanTranslateInputFile()
    {
        $this->sut->execute([
            'input' => dirname(__DIR__) . '/fixtures/User-Physical.xmi',
            'output' => $this->fileSystem->url() .'/out.sql',
            '--debug' => 1
        ]);

        $debug = file($this->fileSystem->url() .'/pumldbconv.log', FILE_IGNORE_NEW_LINES);

    }

    protected function setUp(): void
    {
        $app = new Application();
        $app->add(new GenerateCommand());
        $this->sut = new CommandTester($app->find('generate'));
        $this->fileSystem = vfsStream::setup();
        $this->fileSystem->chmod(0700);
    }
}
