<?php
/**
 * PlantUml Db Support
 *
 * @author    Ashley Kitson
 * @license   BSD-2 Clause
 * @copyright Ashley Kitson, UK, 2020
 */
namespace Chippyash\Command;

use Chippyash\Schema\PumlSchema;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateCommand extends Command
{
    /**
     * @var string|null The default command name
     */
    protected static $defaultName = 'generate';
    /**
     * @var bool
     */
    protected $debug = false;
    /**
     * @var string
     */
    protected $debugFile;

    protected function configure()
    {
        $this->setAliases(['g'])
            ->setDescription('Convert Db UML diagrams to SQL')
            ->addArgument('input', InputArgument::REQUIRED, 'Name of input puml file')
            ->addArgument('output', InputArgument::REQUIRED, 'Name of output sql file')
            ->addOption('debug', 'd', InputOption::VALUE_OPTIONAL, '0|1 write debug file <outDir>/pumldbconv.log', false);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $inFile = $input->getArgument('input');
        if (!file_exists($inFile)) {
            throw new \InvalidArgumentException("{$inFile} does not exist");
        }
        $outFile = $input->getArgument('output');
        if (!is_writeable(dirname($outFile))) {
            throw new \InvalidArgumentException("{$outFile} is not writeable");
        }
        $this->debug = (bool) (int) $input->getOption('debug');
        if($this->debug) {
            $this->debugFile = fopen(dirname($outFile) . '/pumldbconv.log', 'w');
        }
        $this->debug('Debug started');
        $translated = $this->translate($inFile);

        return 0;
    }

    public function __destruct()
    {
        if ($this->debug) {
            fclose($this->debugFile);
        }
    }

    protected function debug(string $msg)
    {
        if (!$this->debug) {
            return;
        }
        fwrite($this->debugFile, $msg . PHP_EOL);
    }

    protected function translate(string $infile): PumlSchema
    {
        //get the xml translation
        $xsldoc = new \DOMDocument();
        $xsldoc->load(dirname(__DIR__) . '/xsl/pumldbconv.xsl');
        $xsl = new \XSLTProcessor();
        $xsl->importStyleSheet($xsldoc);

        $xmldoc = new \DOMDocument();
        $xmldoc->load($infile);

        $out = $xsl->transformToXml($xmldoc);
        eval($out);
        return $schema;
    }
}