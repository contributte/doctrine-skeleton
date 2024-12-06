<?php declare(strict_types = 1);

namespace Tests\Cases\E2E\Database;

use App\Bootstrap;
use Contributte\Tester\Toolkit;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaValidator;
use Tester\Assert;

require_once __DIR__ . '/../../../bootstrap.php';

Toolkit::test(function (): void {
	$container = Bootstrap::boot()->createContainer();

	/** @var EntityManagerInterface $em */
	$em = $container->getByType(EntityManagerInterface::class);

	// Validation
	$validator = new SchemaValidator($em);
	$validations = $validator->validateMapping();

	foreach ($validations as $fails) {
		foreach ((array) $fails as $fail) {
			Assert::fail($fail);
		}
	}

	Assert::count(0, $validations);
});
