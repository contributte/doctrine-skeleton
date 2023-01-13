<?php declare(strict_types = 1);

namespace App\Model\Database;

use App\Domain\Database\User;
use App\Domain\Database\UserRepository;

/**
 * Shortcuts for type hinting
 *
 * @mixin EntityManagerDecorator
 */
trait TRepositories
{

	public function getUserRepository(): UserRepository
	{
		$repository = $this->getRepository(User::class);
		assert($repository instanceof UserRepository);

		return $repository;
	}

}
