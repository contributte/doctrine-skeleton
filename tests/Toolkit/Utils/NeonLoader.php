<?php declare(strict_types = 1);

namespace Tests\Toolkit\Utils;

use Nette\DI\Config\Adapters\NeonAdapter;
use Nette\Neon\Neon;

final class NeonLoader
{

	/**
	 * @return mixed[]
	 */
	public static function load(string $str): array
	{
		return (new NeonAdapter())->process((array) Neon::decode($str));
	}

}
