<?php declare(strict_types = 1);

namespace Tests\Cases\Unit\Domain\Database;

use App\Domain\Database\User;
use Tests\Toolkit\Phpunit\TestCase;

final class UserTest extends TestCase
{

	public function testEntity(): void
	{
		$user = new User('foo');

		$this->assertEquals('foo', $user->getUsername());
	}

}
