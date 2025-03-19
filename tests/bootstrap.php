<?php declare(strict_types = 1);

use DG\BypassFinals;
use Nette\Bootstrap\Configurator;
use Nette\Bootstrap\Extensions\ConstantsExtension;
use Nette\Bootstrap\Extensions\PhpExtension;
use Nette\Bridges\CacheDI\CacheExtension;
use Nette\DI\Extensions\DecoratorExtension;
use Nette\DI\Extensions\DIExtension;
use Nette\DI\Extensions\ExtensionsExtension;
use Nette\DI\Extensions\InjectExtension;
use Tester\Dumper;
use Tester\Environment;
use Tracy\Bridges\Nette\TracyExtension;
use Tracy\Debugger;

require __DIR__ . '/../vendor/autoload.php';

BypassFinals::enable();

$configurator = new Configurator();

// we need to override defaultExtensions because Nette\Configurator registers
// butch of extensions we don't need and that clash with the Mango Tester
$configurator->defaultExtensions = [
	'php' => PhpExtension::class,
	'constants' => ConstantsExtension::class,
	'extensions' => ExtensionsExtension::class,
	'decorator' => DecoratorExtension::class,
	'cache' => [CacheExtension::class, ['%tempDir%']],
	'di' => [DIExtension::class, ['%debugMode%']],
	//'database' => [Nette\Bridges\DatabaseDI\DatabaseExtension::class, ['%debugMode%']],
	'tracy' => [TracyExtension::class, ['%debugMode%', '%consoleMode%']],
	'inject' => InjectExtension::class,
];

$configurator->setDebugMode(true);
$logDir = __DIR__ . '/../temp/tests/log';
$tempDir = __DIR__ . '/../temp/tests';
@mkdir($logDir, 0777, true);
$configurator->enableTracy($logDir);
$configurator->setTempDirectory($tempDir);

$sessionsDir = __DIR__ . '/../temp/tests/sessions';
@mkdir($sessionsDir, 0777, true);

$appDir = __DIR__ . '/../app';

$configurator->createRobotLoader()
	->addDirectory(__DIR__ . '/../app')
	->addDirectory(__DIR__)
	->register();

$configurator->addStaticParameters(
	[
		'appDir' => $appDir,
		'migrations' => [
			'withDummyData' => true,
		],
	]
);

$configurator->addConfig(__DIR__ . '/config/tests.neon');

if (file_exists(__DIR__ . '/config/tests.local.neon')) {
	$configurator->addConfig(__DIR__ . '/config/tests.local.neon');
}

$configurator->addConfig(__DIR__ . '/../app/config/local.neon');

Environment::setup();
Dumper::$maxPathSegments = 32;
Debugger::$showLocation = true;
Debugger::$showBar = true;

return [$configurator, 'createContainer'];