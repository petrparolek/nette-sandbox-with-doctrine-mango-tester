<?php declare(strict_types = 1);

namespace AppTests;

use Nette\Bootstrap\Configurator;
use Nette\DI\Container as DIContainer;
use Nette\DI\Definitions\Statement as DIStatement;
use Webnazakazku\MangoTester\DatabaseCreator\DatabaseCreator;
use Webnazakazku\MangoTester\Infrastructure\Container\IAppConfiguratorFactory;

class AppConfiguratorFactory implements IAppConfiguratorFactory
{

	private DatabaseCreator $databaseCreator;

	public function __construct(DatabaseCreator $databaseCreator)
	{
		$this->databaseCreator = $databaseCreator;
	}

	public function create(DIContainer $testContainer): Configurator
	{
		$testDatabaseName = $this->databaseCreator->getDatabaseName();

		$testContainerParameters = $testContainer->getParameters();

		$dbDir = $testContainerParameters['tempDir'] . '/test_databases';
		@mkdir($dbDir, 0777, true);
		touch($dbDir . '/' . $testDatabaseName);

		$configurator = new Configurator();
		$configurator->setDebugMode(true);
		$configurator->setTempDirectory($testContainerParameters['tempDir']);

		$appDir = __DIR__ . '/../../app';
		$wwwDir = __DIR__ . '/../../temp/tests/www';

		$configurator->addStaticParameters(
			[
				'appDir' => $appDir,
				'wwwDir' => $wwwDir,
			]
		);

		$configurator->addConfig(__DIR__ . '/../config/app.neon');

		$configurator->addConfig($appDir . '/config/common.neon');
		$configurator->addConfig($appDir . '/config/local.neon');

		$configurator->addConfig(
			[
				'console' => [
					'url' => null,
				],
				'nettrine.dbal' => [
					'debug' => [
						'panel' => false,
					],
					'connection' => [
						'dbname' => $testDatabaseName,
					],
				],
				'services' => [
					'nettrine.dbal.connection' => [
						'setup' => [
							new DIStatement('@databaseCreator::createTestDatabase'),
						],
					],
				],
			]
		);

		return $configurator;
	}

}
