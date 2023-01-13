<?php declare(strict_types = 1);

namespace Tests\Toolkit\Utils;

final class Memo
{

	/** @var mixed[] */
	public static array $memos = [];

	public static function add(mixed $memo): void
	{
		self::$memos[] = $memo;
	}

	public static function clear(): void
	{
		self::$memos = [];
	}

	/**
	 * @return mixed[]
	 */
	public static function fetch(): array
	{
		$res = self::$memos;
		self::$memos = [];

		return $res;
	}

}
