<?php

namespace Tests\unit\Calculator;

use SocialPost\Dto\SocialPostTo;
use Statistics\Calculator\PerUserAccumulator;
use PHPUnit\Framework\TestCase;

class PerUserAccumulatorTest extends TestCase
{
    private function createPost(int $authorId): SocialPostTo
    {
        $post = new SocialPostTo();
        $post->setAuthorId($authorId);

        return $post;
    }
    public function test_accumulates_data()
    {
        $accumulator = new PerUserAccumulator('test');
        $accumulator->accumulateData($this->createPost(1));
        $accumulator->accumulateData($this->createPost(1));
        $accumulator->accumulateData($this->createPost(2));
        $accumulator->accumulateData($this->createPost(2));
        $accumulator->accumulateData($this->createPost(2));
        $accumulator->accumulateData($this->createPost(3));
        $accumulator->accumulateData($this->createPost(3));
        $accumulator->accumulateData($this->createPost(3));
        $accumulator->accumulateData($this->createPost(3));

        $result = $accumulator->getAccumulatedData();

        self::assertCount(3, $result);
        self::assertEquals(2, $result[1]);
        self::assertEquals(3, $result[2]);
        self::assertEquals(4, $result[3]);
    }

    public function test_calculates_average()
    {
        $accumulator = new PerUserAccumulator('test');
        $accumulator->accumulateData($this->createPost(1));
        $accumulator->accumulateData($this->createPost(1));
        $accumulator->accumulateData($this->createPost(2));
        $accumulator->accumulateData($this->createPost(2));
        $accumulator->accumulateData($this->createPost(2));
        $accumulator->accumulateData($this->createPost(3));
        $accumulator->accumulateData($this->createPost(3));
        $accumulator->accumulateData($this->createPost(3));
        $accumulator->accumulateData($this->createPost(3));

        self::assertEquals(3, $accumulator->average());

        $accumulator = new PerUserAccumulator('test-2');
        $accumulator->accumulateData($this->createPost(1));
        $accumulator->accumulateData($this->createPost(1));
        $accumulator->accumulateData($this->createPost(2));

        self::assertEquals(1.5, $accumulator->average());

        $accumulator = new PerUserAccumulator('test-3');
        self::assertEquals(0, $accumulator->average());
    }


}
