<?php

declare(strict_types=1);

namespace App\Tests\Domain;

use App\Domain\InvalidPeriodException;
use App\Domain\PeriodTrait;
use PHPUnit\Framework\TestCase;

class PeriodTraitTest extends TestCase
{
    /**
     * @test
     */
    public function test_duration()
    {
        $refDateTime = new \DateTimeImmutable('2021-01-01');

        $from = new \DateTimeImmutable();
        $to = $from->add(new \DateInterval('P4D'));

        $period = new class {
            use PeriodTrait {
                setPeriod as public;
            }
        };
        $period->setPeriod($from, $to);

        $this->assertEquals($refDateTime->add(new \DateInterval('P4D')), $refDateTime->add($period->getDuration()));
        $this->assertEquals(4, $period->getDaysCount());
    }

    public function invalidPeriods()
    {
        $refDateTime = new \DateTimeImmutable();
        return [
            'end before start' => [$refDateTime, $refDateTime->sub(new \DateInterval('P4D'))],
            'start and end at same time' => [$refDateTime, $refDateTime],
        ];
    }
    /**
     * @dataProvider invalidPeriods
     * @test
     */
    public function test_invalid_period(\DateTimeImmutable $from, \DateTimeImmutable $to)
    {
        $this->expectException(InvalidPeriodException::class);

        $period = new class {
            use PeriodTrait {
                setPeriod as public;
            }
        };
        $period->setPeriod($from, $to);
    }
}
