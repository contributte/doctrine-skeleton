<?php declare(strict_types = 1);

namespace App\Model\Database;

use App\Model\Database\Entity\AbstractEntity;
use App\Model\Database\Repository\AbstractRepository;
use Nettrine\ORM\EntityManagerDecorator as NettrineEntityManagerDecorator;

final class EntityManagerDecorator extends NettrineEntityManagerDecorator
{

	use TRepositories;

	/**
	 * @template T of AbstractEntity
	 * @param class-string<T> $entityName
	 * @return AbstractRepository<T>
	 * @internal
	 */
	public function getRepository($entityName): AbstractRepository
	{
		return $this->wrapped->getRepository($entityName);
	}

}
