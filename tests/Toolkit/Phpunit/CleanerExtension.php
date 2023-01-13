<?php declare(strict_types = 1);

namespace Tests\Toolkit\Phpunit;

use Contributte\Utils\FileSystem;
use PHPUnit\Runner\AfterLastTestHook;

final class CleanerExtension implements AfterLastTestHook
{

	public function executeAfterLastTest(): void
	{
		if (is_dir(TestCase::TMP_DIR)) {
			FileSystem::purge(TestCase::TMP_DIR);
		}
	}

}
