<?php declare(strict_types = 1);

namespace App\UI\Home;

use App\Domain\Database\User;
use App\UI\BasePresenter;
use Doctrine\ORM\EntityManagerInterface;
use Nette\Utils\Random;
use Nettrine\ORM\ManagerProvider;

class HomePresenter extends BasePresenter
{

	private EntityManagerInterface $em1;

	private EntityManagerInterface $em2;

	public function __construct(ManagerProvider $managerProvider)
	{
		parent::__construct();

		$this->em1 = $managerProvider->getManager('default');
		$this->em2 = $managerProvider->getManager('second');
	}

	public function actionDefault(): void
	{
		// PostgreSQL
		$users = $this->em1->getRepository(User::class)->findAll();

		// MariaDB
		$users2 = $this->em2->getRepository(User::class)->findAll();

		$this->template->users = [...$users, ...$users2];
	}

	public function handleCreateUser(): void
	{
		$user = new User(Random::generate(20));
		$this->em1->persist($user);
		$this->em1->flush();
		$this->flashMessage('Saved');
		$this->redirect('this');
	}

	public function handleDeleteUsers(): void
	{
		$this->em1->getRepository(User::class)
			->createQueryBuilder('u')
			->delete()
			->getQuery()
			->execute();

		$this->flashMessage('Removed');
		$this->redirect('this');
	}

}
