<?php


namespace FintobitBundle\Form;


class ChoosePeriodFormData
{
	private $period;

	public function setPeriod(string $period): void
	{
		$this->period = $period;
	}

	public function getPeriod(): ?string
	{
		return $this->period;
	}
}