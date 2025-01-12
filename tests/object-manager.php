<?php declare(strict_types = 1);

use App\Bootstrap;
use App\Model\EntityManagerDecorator;

return Bootstrap::boot(__DIR__ . '/../temp/phpstan')
	->createContainer()
	->getByType(EntityManagerDecorator::class);
