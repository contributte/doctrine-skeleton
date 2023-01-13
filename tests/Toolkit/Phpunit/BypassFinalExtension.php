<?php declare(strict_types = 1);

namespace Tests\Toolkit\Phpunit;

use DG\BypassFinals;
use PHPUnit\Runner\BeforeTestHook;

final class BypassFinalExtension implements BeforeTestHook
{

	public function executeBeforeTest(string $test): void
	{
		// Does not work in dataProvider
		// Some classes are autoloaded at autoloader init, the only way how to remove final from those
		// is to enable bypass before require autoloader in bootstrap = removes final from everything
		BypassFinals::enable();
	}

}
