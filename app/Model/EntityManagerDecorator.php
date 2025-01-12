<?php declare(strict_types = 1);

namespace App\Model;

use Doctrine\ORM\Decorator\EntityManagerDecorator as NettrineEntityManager;

/**
 * Custom EntityManager
 */
final class EntityManagerDecorator extends NettrineEntityManager
{

}
