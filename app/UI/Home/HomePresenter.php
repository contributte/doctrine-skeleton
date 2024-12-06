<?php declare(strict_types = 1);

namespace App\UI\Home;

use App\Domain\Database\User;
use App\UI\BasePresenter;
use Nette\DI\Attributes\Inject;
use Nette\Utils\Random;
use Nettrine\ORM\ManagerRegistry;

class HomePresenter extends BasePresenter
{

	#[Inject]
	public ManagerRegistry $managerRegistry;

	public function actionDefault(): void
	{
		// PostgreSQL
		$users = $this->managerRegistry->getManager('default')->getRepository(User::class)->findAll();

		// MariaDB
		$users2 = $this->managerRegistry->getManager('second')->getRepository(User::class)->findAll();

		$this->template->users = [...$users, ...$users2];
	}

	public function handleCreateUser(): void
	{
		$user = new User(Random::generate(20));
		$this->managerRegistry->getManager('default')->persist($user);
		$this->managerRegistry->getManager('default')->flush();
		$this->flashMessage('Saved');
		$this->redirect('this');
	}

	public function handleDeleteUsers(): void
	{
		$this->managerRegistry->getManager('default')->getRepository(User::class)
			->createQueryBuilder('u')
			->delete()
			->getQuery()
			->execute();

		$this->flashMessage('Removed');
		$this->redirect('this');
	}

}
