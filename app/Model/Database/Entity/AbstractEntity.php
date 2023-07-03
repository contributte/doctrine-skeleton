<?php declare(strict_types = 1);

namespace App\Model\Database\Entity;

use Doctrine\ORM\Mapping as ORM;

abstract class AbstractEntity
{

	/**
	 * @ORM\Column(type="integer", nullable=FALSE)
	 * @ORM\Id
	 * @ORM\GeneratedValue
	 */
	protected int $id;

	public function getId(): int
	{
		return $this->id;
	}

	public function __clone()
	{
		unset($this->id);
	}

}
