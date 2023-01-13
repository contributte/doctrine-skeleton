<?php declare(strict_types = 1);

namespace Tests\Toolkit\Phpunit;

final class TestCaseExtender
{

	public static function ignoreStdout(TestCase $testCase): void
	{
		$testCase->setOutputCallback(static fn (): string => '');
	}

}
