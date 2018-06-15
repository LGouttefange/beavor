<?php

require 'vendor/autoload.php';

$app = new \Symfony\Component\Console\Application();

$app->add(new \Beavor\Commands\GenerateDto('generateDto'));
$app->setDefaultCommand('generateDto');
$app->run();