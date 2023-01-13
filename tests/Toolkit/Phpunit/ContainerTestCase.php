<?php declare(strict_types = 1);

namespace Tests\Toolkit\Phpunit;

use App\Bootstrap;
use Nette\DI\Container;

abstract class ContainerTestCase extends TestCase
{

	protected function createContainer(): Container
	{
		return Bootstrap::boot()->createContainer();
	}

}
