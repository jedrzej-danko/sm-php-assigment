<?php

namespace Statistics\Calculator;

use SocialPost\Dto\SocialPostTo;
use Statistics\Dto\ParamsTo;
use Statistics\Dto\StatisticsTo;

class AveragePostsPerUserPerMonth extends AbstractCalculator
{
    protected const UNITS = 'posts';

    private array $accumulator = [];

    /** @var PerUserAccumulator[] */
    private array $months = [];

    public function setParameters(ParamsTo $params): CalculatorInterface
    {
        if ($params->getStartDate() < $params->getEndDate()) {
            $startDate = clone $params->getStartDate();
            $endDate = clone $params->getEndDate();
        } else {
            $startDate = clone $params->getEndDate();
            $endDate = clone $params->getStartDate();
        }
        while ((int) $startDate->format('Ym') <= (int) $endDate->format('Ym')) {
            $label = $this->monthLabel($startDate);
            $this->months[$label] = new PerUserAccumulator($label);
            $startDate->modify('+1 month');
        }
        return parent::setParameters($params);
    }

    private function monthLabel(\DateTime $date): string
    {
        return $date->format('M, Y');
    }

    protected function doAccumulate(SocialPostTo $postTo): void
    {
        $postLabel = $this->monthLabel($postTo->getDate());
        if (isset($this->months[$postLabel])) {
            $this->months[$postLabel]->accumulateData($postTo);
        }
    }

    protected function doCalculate(): StatisticsTo
    {
        $stats = new StatisticsTo();
        foreach ($this->months as $month) {
            $child = (new StatisticsTo())
                ->setName($this->parameters->getStatName())
                ->setSplitPeriod($month->getLabel())
                ->setValue($month->average())
                ->setUnits(self::UNITS);
            $stats->addChild($child);
        }

        return $stats;
    }




}
