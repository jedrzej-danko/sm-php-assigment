<?php

namespace Tests\unit\Calculator;

use PHPUnit\Framework\TestCase;
use SocialPost\Dto\SocialPostTo;
use Statistics\Calculator\AveragePostsPerUserPerMonth;
use Statistics\Dto\ParamsTo;

class AveragePostsPerUserPerMonthTest extends TestCase
{

    public function test_calculate_period_length()
    {
        $forwardPeriod = (new ParamsTo())
            ->setStatName('AveragePostsPerUserPerMonth')
            ->setStartDate(new \DateTime('2022-01-01'))
            ->setEndDate(new \DateTime('2023-04-30'));
        $calculator = new AveragePostsPerUserPerMonth();
        $calculator->setParameters($forwardPeriod);

        $result = $calculator->calculate();
        self::assertCount(16, $result->getChildren(), '(Forward period) Number of children doesn\'t match number of months');

        $invertedPeriod = (new ParamsTo())
            ->setStatName('AveragePostsPerUserPerMonth')
            ->setStartDate(new \DateTime('2023-04-30'))
            ->setEndDate(new \DateTime('2022-01-01'));
        $calculator = new AveragePostsPerUserPerMonth();
        $calculator->setParameters($invertedPeriod);

        $result = $calculator->calculate();
        self::assertCount(16, $result->getChildren(), '(Inverted period) Number of children doesn\'t match number of months');

        $singleMonth = (new ParamsTo())
            ->setStatName('AveragePostsPerUserPerMonth')
            ->setStartDate(new \DateTime('2023-04-01'))
            ->setEndDate(new \DateTime('2023-04-30'));
        $calculator = new AveragePostsPerUserPerMonth();
        $calculator->setParameters($singleMonth);

        $result = $calculator->calculate();
        self::assertCount(1, $result->getChildren(), '(Single month) Number of children doesn\'t match number of months');
    }

    private function createPost(string $date, int $authorId) : SocialPostTo
    {
        $post = new SocialPostTo();
        $post->setDate(new \DateTime($date));
        $post->setAuthorId($authorId);
        return $post;
    }

    public function test_results()
    {
        $params = (new ParamsTo())
            ->setStatName('AveragePostsPerUserPerMonth')
            ->setStartDate(new \DateTime('2023-03-01'))
            ->setEndDate(new \DateTime('2023-04-30'));

        $calculator = new AveragePostsPerUserPerMonth();
        $calculator->setParameters($params);

        $calculator->accumulateData($this->createPost('2023-02-01', 1));
        $calculator->accumulateData($this->createPost('2023-03-01', 1));
        $calculator->accumulateData($this->createPost('2023-03-01', 2));
        $calculator->accumulateData($this->createPost('2023-03-15', 1));
        $calculator->accumulateData($this->createPost('2023-04-01', 1));
        $calculator->accumulateData($this->createPost('2023-04-01', 1));
        $calculator->accumulateData($this->createPost('2023-04-01', 1));

        $result = $calculator->calculate();
        self::assertCount(2, $result->getChildren(), 'Number of children doesn\'t match number of months');
        $firstMonthResult = $result->getChildren()[0];
        self::assertEquals('Mar, 2023', $firstMonthResult->getSplitPeriod(), 'First month label is incorrect');
        self::assertEquals(1.5, $firstMonthResult->getValue(), 'First month value is incorrect');
        $secondMonthResult = $result->getChildren()[1];
        self::assertEquals('Apr, 2023', $secondMonthResult->getSplitPeriod(), 'Second month label is incorrect');
        self::assertEquals(3, $secondMonthResult->getValue(), 'Second month value is incorrect');
    }


}
