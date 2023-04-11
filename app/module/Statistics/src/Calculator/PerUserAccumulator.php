<?php

namespace Statistics\Calculator;

use SocialPost\Dto\SocialPostTo;

class PerUserAccumulator
{
    private string $label;
    /**
     * @var array<int>
     */
    private array $user = [];

    public function __construct(string $label)
    {
        $this->label = $label;
    }

    public function accumulateData(SocialPostTo $post): void
    {
        if (!isset($this->user[$post->getAuthorId()])) {
            $this->user[$post->getAuthorId()] = 0;
        }
        $this->user[$post->getAuthorId()]++;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getAccumulatedData(): array
    {
        return $this->user;
    }

    public function average() : float
    {
        if (count($this->user) === 0) {
            return 0;
        }
        return array_sum($this->user) / count($this->user);
    }


}
