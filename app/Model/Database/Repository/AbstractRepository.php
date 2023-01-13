<?php declare(strict_types = 1);

namespace App\Model\Database\Repository;

use App\Model\Database\Entity\AbstractEntity;
use App\Model\Exception\Runtime\EntityNotFoundException;
use Doctrine\ORM\EntityRepository;

/**
 * @template T of object
 * @extends EntityRepository<T>
 */
abstract class AbstractRepository extends EntityRepository
{

	/**
	 * @throws EntityNotFoundException
	 */
	public function fetch(int $id, ?int $lockMode = null, ?int $lockVersion = null): AbstractEntity
	{
		/** @var AbstractEntity|null $entity */
		$entity = $this->find($id, $lockMode, $lockVersion);

		if (!$entity) {
			throw EntityNotFoundException::createForId($this->getClassName(), $id);
		}

		return $entity;
	}

	/**
	 * @param array<string,mixed> $criteria
	 * @param array<string,string>|null $orderBy
	 * @throws EntityNotFoundException
	 */
	public function fetchBy(array $criteria, ?array $orderBy = null): AbstractEntity
	{
		/** @var AbstractEntity|null $entity */
		$entity = $this->findOneBy($criteria, $orderBy);

		if (!$entity) {
			throw EntityNotFoundException::createForCriteria($this->getClassName(), $criteria);
		}

		return $entity;
	}

	/**
	 * Fetches all records like $key => $value pairs
	 *
	 * @param array<string,mixed> $criteria
	 * @param array<string,string> $orderBy
	 * @return mixed[]
	 */
	public function findPairs(?string $key, string $value, array $criteria = [], array $orderBy = []): array
	{
		if ($key === null) {
			$key = $this->getClassMetadata()->getSingleIdentifierFieldName();
		}

		$qb = $this->createQueryBuilder('e')
			->select(['e.' . $value, 'e.' . $key])
			->resetDQLPart('from')
			->from($this->getEntityName(), 'e', 'e.' . $key);

		foreach ($criteria as $v) {
			if (is_array($v)) {
				$qb->andWhere(sprintf('e.%s IN(:%s)', $key, $key))->setParameter($key, array_values($v));
			} else {
				$qb->andWhere(sprintf('e.%s = :%s', $key, $key))->setParameter($key, $v);
			}
		}

		foreach ($orderBy as $column => $order) {
			$qb->addOrderBy($column, $order);
		}

		/** @phpstan-ignore-next-line */
		return array_map(fn ($row) => reset($row), $qb->getQuery()->getArrayResult());
	}

}
