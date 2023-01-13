<?php declare(strict_types = 1);

namespace App\Model\Exception\Runtime;

use App\Model\Exception\RuntimeException;
use Throwable;

final class EntityNotFoundException extends RuntimeException
{

	/** @var array<mixed>|null */
	public ?array $criteria;

	private function __construct(string $message = '', int $code = 0, ?Throwable $previous = null)
	{
		parent::__construct($message, $code, $previous);
	}

	public static function createForId(string $class, int $id): self
	{
		return new self(sprintf('Entity of type %s was not found for ID %d', $class, $id));
	}

	/**
	 * @param array<mixed> $criteria
	 */
	public static function createForCriteria(string $class, array $criteria): self
	{
		$self = new self(sprintf('Entity of type %s was not found for criteria', $class));
		$self->criteria = $criteria;

		return $self;
	}

}
