<?php declare(strict_types = 1);

namespace App\UI\Home;

use App\Model\Database\EntityManagerDecorator;
use App\UI\BasePresenter;
use Nette\DI\Attributes\Inject;

class HomePresenter extends BasePresenter
{

	#[Inject]
	public EntityManagerDecorator $em;

	public function actionDefault(): void
	{
		$this->template->users = $this->em->getUserRepository()->findAll();
	}

}
