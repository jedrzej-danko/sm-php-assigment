<?php

namespace Tests\e2e;

use PHPUnit\Framework\TestCase;
use function PHPUnit\Framework\assertArrayHasKey;

class StatisticsTest extends TestCase
{
    const API_URL = 'http://app-web/statistics';

    private function makeCall($startDate, $endDate) : array
    {
        $context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'header' => 'Content-Type: application/json',
            ],
        ]);
        $url = self::API_URL . '?start_date=' . urlencode($startDate) . '&end_date=' . urlencode($endDate);
        $result = file_get_contents($url, false, $context);
        return json_decode($result, true);
    }
    public function test_get_statistics()
    {
        $result = $this->makeCall('November, 2022', 'April, 2023');
        self::assertArrayHasKey('stats', $result);
        self::assertArrayHasKey(3, $result['stats']['children']);
        $perUserStats = $result['stats']['children'][3];
        self::assertEquals('average-posts-per-user-per-month', $perUserStats['name']);
        self::assertEquals('Average number of posts per user per month', $perUserStats['label']);
        self::assertEquals('posts', $perUserStats['units']);
        self::assertArrayHasKey('children', $perUserStats);
        self::assertCount(6, $perUserStats['children']);

    }
}

