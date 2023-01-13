<?php declare(strict_types = 1);

namespace Tests\Cases\E2E\Database\Entity;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaValidator;
use Tests\Toolkit\Phpunit\ContainerTestCase;

final class MappingTest extends ContainerTestCase
{

	public function testMapping(): void
	{
		/** @var EntityManager $em */
		$em = $this->createContainer()->getByType(EntityManagerInterface::class);

		// Validation
		$validator = new SchemaValidator($em);
		$validations = $validator->validateMapping();
		foreach ($validations as $fails) {
			foreach ((array) $fails as $fail) {
				$this->fail($fail);
			}
		}

		$this->assertCount(0, $validations);
	}

}
