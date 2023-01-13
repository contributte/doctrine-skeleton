<?php declare(strict_types = 1);

namespace Tests\Toolkit\Phpunit;

use PHPUnit\Framework\Test;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\TestListener;
use PHPUnit\Framework\TestListenerDefaultImplementation;
use PHPUnit\Framework\TestSuite;
use PHPUnit\Util\Test as TestUtil;

class SpeedTrapListener implements TestListener
{

	use TestListenerDefaultImplementation;

	protected int $suites = 0;

	protected int $slowThreshold;

	protected int $reportLength;

	/** @var mixed[] */
	protected array $slow = [];

	/**
	 * @param mixed[] $options
	 */
	public function __construct(array $options = [])
	{
		$this->loadOptions($options);
	}

	public function endTest(Test $test, float $time): void
	{
		if (!$test instanceof TestCase) {
			return;
		}

		$timeInMilliseconds = $this->toMilliseconds($time);
		$threshold = $this->getSlowThreshold($test);

		if ($this->isSlow($timeInMilliseconds, $threshold)) {
			$this->addSlowTest($test, $timeInMilliseconds);
		}
	}

	public function startTestSuite(TestSuite $suite): void
	{
		$this->suites++;
	}

	public function endTestSuite(TestSuite $suite): void
	{
		$this->suites--;

		if ($this->suites === 0 && $this->hasSlowTests()) {
			arsort($this->slow); // Sort longest running tests to the top

			$this->renderHeader();
			$this->renderBody();
			$this->renderFooter();
		}
	}

	protected function isSlow(int $time, int $slowThreshold): bool
	{
		return $time >= $slowThreshold;
	}

	protected function addSlowTest(TestCase $test, int $time): void
	{
		$label = $this->makeLabel($test);

		$this->slow[$label] = $time;
	}

	protected function hasSlowTests(): bool
	{
		return !empty($this->slow);
	}

	protected function toMilliseconds(float $time): int
	{
		return (int) round($time * 1000);
	}

	protected function makeLabel(TestCase $test): string
	{
		return sprintf('%s:%s', $test::class, $test->getName());
	}

	protected function getReportLength(): int
	{
		return min(count($this->slow), $this->reportLength);
	}

	protected function getHiddenCount(): int
	{
		$total = count($this->slow);
		$showing = $this->getReportLength();

		$hidden = 0;
		if ($total > $showing) {
			$hidden = $total - $showing;
		}

		return $hidden;
	}

	protected function renderHeader(): void
	{
		echo sprintf("\n\nYou should really speed up these slow tests (>%sms)...\n", $this->slowThreshold);
	}

	protected function renderBody(): void
	{
		$slowTests = $this->slow;

		$length = $this->getReportLength();
		for ($i = 1; $i <= $length; ++$i) {
			$label = key($slowTests);
			$time = array_shift($slowTests);

			echo sprintf(" %s. %sms to run %s\n", $i, $time, $label);
		}
	}

	protected function renderFooter(): void
	{
		if ($hidden = $this->getHiddenCount()) {
			echo sprintf('...and there %s %s more above your threshold hidden from view', $hidden === 1 ? 'is' : 'are', $hidden);
		}
	}

	/**
	 * @param mixed[] $options
	 */
	protected function loadOptions(array $options): void
	{
		$this->slowThreshold = $options['slowThreshold'] ?? 500;
		$this->reportLength = $options['reportLength'] ?? 15;
	}

	protected function getSlowThreshold(TestCase $test): int
	{
		$ann = TestUtil::parseTestMethodAnnotations(
			$test::class,
			$test->getName(false)
		);

		return isset($ann['method']['slowThreshold'][0]) ? (int) $ann['method']['slowThreshold'][0] : $this->slowThreshold;
	}

}
