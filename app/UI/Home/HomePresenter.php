<?php declare(strict_types = 1);

namespace App\UI\Home;

use App\Domain\Database\User;
use App\Model\Database\EntityManagerDecorator;
use App\UI\BasePresenter;
use Nette\DI\Attributes\Inject;
use Nette\Utils\Random;

class HomePresenter extends BasePresenter
{

	#[Inject]
	public EntityManagerDecorator $em;

	public function actionDefault(): void
	{
		$this->template->users = $this->em->getRepository(User::class)->findAll();
	}

	public function handleCreateUser(): void
	{
		$user = new User(Random::generate(20));
		$this->em->persist($user);
		$this->em->flush();
		$this->flashMessage('Saved');
		$this->redirect('this');
	}

	public function handleDeleteUsers(): void
	{
		$this->em->getRepository(User::class)
			->createQueryBuilder('u')
			->delete()
			->getQuery()
			->execute();

		$this->flashMessage('Removed');
		$this->redirect('this');
	}

}
