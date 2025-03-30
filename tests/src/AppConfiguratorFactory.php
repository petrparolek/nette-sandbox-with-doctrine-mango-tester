<?php declare(strict_types = 1);

namespace AppTests;

use Nette\Bootstrap\Configurator;
use Nette\DI\Container as DIContainer;
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
		$this->databaseCreator->createTestDatabase();

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

		$configurator->addConfig($appDir . '/config/common.neon');
		$configurator->addConfig($appDir . '/config/local.neon');

		$configurator->addConfig(__DIR__ . '/../config/app.neon');

		$configurator->addConfig(
			[
				'console' => [
					'url' => null,
				],
				'nettrine.dbal' => [
					'debug' => [
						'panel' => false,
					],
					'connections' => [
						'default' => [
							'dbname' => $testDatabaseName,
						],
					],
				],
			]
		);

		return $configurator;
	}

}
