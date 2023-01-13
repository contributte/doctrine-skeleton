<?php declare(strict_types = 1);

namespace Tests\Cases\E2E;

use App\Bootstrap;
use Contributte\Utils\FileSystem;
use Nette\Application\Application as WebApplication;
use Nette\DI\Container;
use Symfony\Component\Console\Application as ConsoleApplication;
use Tests\Toolkit\Phpunit\TestCase;

final class EntrypointTest extends TestCase
{

	public function setUp(): void
	{
		parent::setUp();

		if (!file_exists(self::CONFIG_DIR . '/local.neon')) {
			FileSystem::copy(
				self::CONFIG_DIR . '/local.neon.example',
				self::CONFIG_DIR . '/local.neon'
			);
		}
	}

	/**
	 * @runInSeparateProcess
	 */
	public function testWeb(): void
	{
		$container = Bootstrap::boot()->createContainer();
		$container->getByType(WebApplication::class);

		$this->assertInstanceOf(Container::class, $container);
	}

	/**
	 * @runInSeparateProcess
	 */
	public function testCli(): void
	{
		$container = Bootstrap::boot()->createContainer();
		$container->getByType(ConsoleApplication::class);

		$this->assertInstanceOf(Container::class, $container);
	}

	/**
	 * @runInSeparateProcess
	 */
	public function testTest(): void
	{
		$container = Bootstrap::boot()->createContainer();
		$container->getByType(Container::class);

		$this->assertInstanceOf(Container::class, $container);
	}

}
