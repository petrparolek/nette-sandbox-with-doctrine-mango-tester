<?php declare(strict_types = 1);

namespace App\Model\Repositories;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityRepository as DoctrineEntityRepository;
use Doctrine\ORM\Mapping\MappingException;
use Throwable;

/**
 * @template TEntityClass of object
 * @extends  DoctrineEntityRepository<TEntityClass>
 */
abstract class EntityRepository extends DoctrineEntityRepository
{

	/** @return TEntityClass */
	public function get(int $id)
	{
		$entity = $this->find($id);

		if (!$entity instanceof $this->_entityName) {
			$path = explode('\\', $this->_entityName);
			$class = array_pop($path);

			throw new NotExistsEntityException($class . ' not found');
		}

		return $entity;
	}

	/**
	 * @param array<mixed>     $criteria
	 * @param ?array<mixed>    $orderBy
	 * @return TEntityClass
	 */
	public function getOneBy(array $criteria, ?array $orderBy = null)
	{
		$entity = $this->findOneBy($criteria, $orderBy);

		if (!$entity instanceof $this->_entityName) {
			$path = explode('\\', $this->_entityName);
			$class = array_pop($path);

			throw new NotExistsEntityException($class . ' not found');
		}

		return $entity;
	}

	/**
	 * Fetches all records like $key => $value pairs
	 *
	 * @param array<mixed>       $criteria parameter can be skipped
	 * @param ?string            $value    mandatory
	 * @param ?string            $key      optional
	 * @param ?string            $orderBy  optional
	 * @return array<string>
	 * @throws MappingException
	 */
	public function findPairs(array $criteria, ?string $value = null, ?string $key = null, ?string $orderBy = null): array
	{
		if ($key === null || strlen($key) === 0) {
			$key = $this->getClassMetadata()->getSingleIdentifierFieldName();
		}

		$qb = $this->createQueryBuilder('e');

		foreach ($criteria as $criteriaKey => $criteriaValue) {
			$qb->andWhere('e.' . $criteriaKey . ' = :' . $criteriaKey)
				->setParameter($criteriaKey, $criteriaValue);
		}

		$qb->select(['e.' . $value, 'e.' . $key])
			->resetDQLPart('from')->from($this->getEntityName(), 'e', 'e.' . $key);

		if ($orderBy !== null) {
			$qb->orderBy('e.' . $orderBy);
		}

		$query = $qb->getQuery();

		try {
			return array_map(
				fn ($row) => reset($row),
				$query->getResult(AbstractQuery::HYDRATE_ARRAY)
			);
		} catch (Throwable $e) {
			throw new NotExistsEntityException($e->getMessage());
		}
	}

}
