<?php declare(strict_types = 1);

namespace App\Domain\Database;

use App\Model\Database\Entity\AbstractEntity;
use App\Model\Database\Entity\TId;
use Contributte\Utils\DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Domain\Database\UserRepository")
 */
class User extends AbstractEntity
{

	use TId;

	/** @ORM\Column(type="string") */
	private string $username;

	/** @ORM\Column(type="datetime") */
	private DateTime $createdAt;

	/** @ORM\Column(type="datetime", nullable=true) */
	private ?DateTime $updatedAt = null;

	public function getUsername(): string
	{
		return $this->username;
	}

	public function setUsername(string $username): void
	{
		$this->username = $username;
	}

	public function getCreatedAt(): DateTime
	{
		return $this->createdAt;
	}

	public function setCreatedAt(DateTime $createdAt): void
	{
		$this->createdAt = $createdAt;
	}

	public function getUpdatedAt(): ?DateTime
	{
		return $this->updatedAt;
	}

	public function setUpdatedAt(?DateTime $updatedAt): void
	{
		$this->updatedAt = $updatedAt;
	}

}
