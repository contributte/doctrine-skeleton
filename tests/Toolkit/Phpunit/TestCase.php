<?php declare(strict_types = 1);

namespace Tests\Toolkit\Phpunit;

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use Tests\Toolkit\Utils\Notes;

abstract class TestCase extends PHPUnitTestCase
{

	use MockeryPHPUnitIntegration;

	public const APP_DIR = __DIR__ . '/../../../src';
	public const CONFIG_DIR = __DIR__ . '/../../../config';
	public const TMP_DIR = __DIR__ . '/../../../var/tmp/tests';

	public static function setUpBeforeClass(): void
	{
		parent::setUpBeforeClass();

		// Timezone
		date_default_timezone_set('Europe/Prague');
	}

	public function setUp(): void
	{
		parent::setUp();

		// Change method to public
	}

	public function tearDown(): void
	{
		parent::tearDown();

		// Change method to public

		Notes::clear();
	}

	protected function assertEqualsArrays(mixed $expected, mixed $actual, string $message = ''): void
	{
		sort($expected);
		sort($actual);

		$this->assertEquals($expected, $actual, $message);
	}

	protected function assertEqualsSpaceless(mixed $expected, mixed $actual, string $message = ''): void
	{
		$this->assertEquals(trim($expected), $actual, $message);
	}

	protected function markTestComplete(): void
	{
		$this->assertTrue(true);
	}

}
