<?php

declare(strict_types=1);

namespace Talleu\RedisOm\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use Talleu\RedisOm\Client\RedisClientInterface;
use Talleu\RedisOm\Console\Runner;
use Talleu\RedisOm\Om\RedisObjectManager;
use Talleu\RedisOm\Tests\Client\Client;
use Talleu\RedisOm\Tests\Fixtures\FixturesGenerator;
use Talleu\RedisOm\Tests\Fixtures\Hash\DummyHash;

class RedisAbstractTestCase extends ApiTestCase
{
    public static function createRedisClient(): RedisClientInterface
    {
        return (new Client())->redisClient;
    }

    public static function emptyRedis(): void
    {
        static::createRedisClient()->flushAll();
    }

    public static function generateIndex(): void
    {
        Runner::generateSchema('tests');
    }

    public static function loadRedisFixtures(?string $dummyClass = DummyHash::class, ?bool $flush = true): array
    {
        $objectManager = new RedisObjectManager(self::createRedisClient());
        $dummies = FixturesGenerator::generateDummies($dummyClass);
        foreach ($dummies as $dummy) {
            $objectManager->persist($dummy);
        }

        if ($flush) {
            $objectManager->flush();
        }

        return $dummies;
    }
}
