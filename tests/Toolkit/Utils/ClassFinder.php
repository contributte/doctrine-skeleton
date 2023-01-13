<?php declare(strict_types = 1);

namespace Tests\Toolkit\Utils;

use Nette\Loaders\RobotLoader;
use ReflectionClass;

final class ClassFinder
{

	/** @var string[] */
	private array $folders = [];

	/** @var callable[] */
	private array $callbacks = [];

	public static function create(): self
	{
		return new self();
	}

	public function addFolder(string $folder): self
	{
		$this->folders[] = $folder;

		return $this;
	}

	public function includeSubclass(string $class): self
	{
		$this->callbacks[] = static fn (ReflectionClass $rc) => is_a($rc->getName(), $class, true);

		return $this;
	}

	public function excludeSubclass(string $class): self
	{
		$this->callbacks[] = static fn (ReflectionClass $rc) => !is_a($rc->getName(), $class, true);

		return $this;
	}

	/**
	 * @return array<string, ReflectionClass>
	 */
	public function find(): array
	{
		$loader = new RobotLoader();

		// Add folders
		foreach ($this->folders as $folder) {
			$loader->addDirectory($folder);
		}

		// Collect classes
		$loader->rebuild();

		// Get classes
		$classes = $loader->getIndexedClasses();

		// Iterate over classes
		$output = [];
		foreach ($classes as $class => $file) {
			$rc = new ReflectionClass($class);

			// Apply callbacks
			foreach ($this->callbacks as $callback) {
				$res = $callback($rc);

				// Skip class if callback is false
				if ($res === false) {
					continue 2;
				}
			}

			$output[$class] = $rc;
		}

		return $output;
	}

}
