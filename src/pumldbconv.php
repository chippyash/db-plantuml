#!/usr/bin/env php
<?php
/**
 * PlantUml Db Support
 *
 * @author    Ashley Kitson
 * @license   BSD-2 Clause
 * @copyright Ashley Kitson, UK, 2020
 */

use Chippyash\Command\GenerateCommand;
use Symfony\Component\Console\Application;

include dirname(__DIR__) . '/vendor/autoload.php';

$app = new Application(
    'pmldbconv',
    file_get_contents(dirname(__FILE__, 2) . '/VERSION')
);
$app->add(new GenerateCommand());
$app->run();