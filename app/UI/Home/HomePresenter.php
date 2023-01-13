<?php declare(strict_types = 1);

namespace App\UI\Home;

use App\UI\BasePresenter;
use Tracy\Debugger;
use Tracy\ILogger;

class HomePresenter extends BasePresenter
{

	public function handleError(): void
	{
		Debugger::log('test2', ILogger::ERROR);
	}

}
