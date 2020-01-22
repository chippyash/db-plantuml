<?php
/**
 * PlantUml Db Support
 *
 * @author    Ashley Kitson
 * @license   BSD-2 Clause
 * @copyright Ashley Kitson, UK, 2020
 */
namespace Chippyash\Command;

use Chippyash\Renderer\MysqlRenderer;
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
    /**
     * @var string
     */
    protected $plantUml;

    protected function configure()
    {
        $this->setAliases(['g'])
            ->setDescription('Convert Db UML diagrams to SQL')
            ->addArgument('input', InputArgument::REQUIRED, 'Name of input puml file')
            ->addArgument('output', InputArgument::REQUIRED, 'Name of output sql file')
            ->addOption('debug', 'd', InputOption::VALUE_OPTIONAL, '0|1 write debug file <outDir>/pumldbconv.log', false)
            ->addOption('plantuml_loc', 'p', InputOption::VALUE_OPTIONAL, 'Directory where plantuml.jar file exists. Will try to auolocat otherwise', null);
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

        $this->locatePlantUml($input);
        if (!file_exists($this->plantUml)) {
            $output->writeln('Cannot locate plantuml at: ' . $this->plantUml);
            return -1;
        }

        $this->debug('Debug started');
        $translated = $this->translate($inFile);
        $this->debug('Translation complete');
        $renderer = new MysqlRenderer();
        $rendered = $renderer->renderDdl($translated);
        $this->debug('Render complete');
        file_put_contents($outFile, $rendered);
        $this->debug('Output written to ' . $outFile);

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
        //create the xml from the diagram
        $xmlDir = dirname($infile);
        exec("java -jar {$this->plantUml} -txmi:star {$infile} 2>/dev/null");

        //get the xml translation
        $xsldoc = new \DOMDocument();
        $xsldoc->load(dirname(__DIR__) . '/xsl/pumldbconv.xsl');
        $xsl = new \XSLTProcessor();
        $xsl->importStyleSheet($xsldoc);

        $xmldoc = new \DOMDocument();
        $xmiFile = $xmlDir . '/' . pathinfo($infile, PATHINFO_FILENAME) . '.xmi';
        $xmldoc->load($xmiFile);
        unlink($xmiFile);

        $out = $xsl->transformToXml($xmldoc);
        eval($out);
        return $schema;
    }

    protected function locatePlantUml(InputInterface $input): void {
        if (!is_null($input->getOption('plantuml_loc'))) {
            $this->plantUml = $input->getOption('plantuml_loc') . '/plantuml.jar';
            return;
        }
        if (PHP_OS_FAMILY == 'Windows') {
            $this->plantUml = '"C:/Program Files/Java/jars/plantuml.jar"';
            return;
        }

        $this->plantUml = '/usr/share/plantuml/plantuml.jar';
    }
}