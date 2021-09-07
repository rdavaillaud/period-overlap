<?php

declare(strict_types=1);

namespace App\Tests\Domain;

use App\Domain\AbsencePeriod;
use App\Domain\InvalidPeriodException;
use App\Domain\MonthPeriod;
use PHPUnit\Framework\TestCase;

class MonthPeriodTest extends TestCase
{
    const INCLUDED_YES = true;
    const INCLUDED_NO = false;
    const OVERLAPPING_YES = true;
    const OVERLAPPING_NO = false;
    const LONGER_YES = true;
    const LONGER_NO = false;

    public function datasetBoundaries(): array
    {
        return [
            'included' => [2021, MonthPeriod::MONTH_JANUARY, new \DateTimeImmutable('2021-01-10'), self::INCLUDED_YES],
            'included february bissextile' => [
                2020,
                MonthPeriod::MONTH_FEBRUARY,
                new \DateTimeImmutable('2020-02-29'),
                self::INCLUDED_YES
            ],
            'not included february non bissextile' => [
                2021,
                MonthPeriod::MONTH_JANUARY,
                new \DateTimeImmutable('2021-02-29'),
                self::INCLUDED_NO
            ],
            'not included' => [
                2021,
                MonthPeriod::MONTH_AUGUST,
                new \DateTimeImmutable('2021-04-24'),
                self::INCLUDED_NO
            ],
            'included last day' => [
                2021,
                MonthPeriod::MONTH_APRIL,
                new \DateTimeImmutable('2021-04-30'),
                self::INCLUDED_YES
            ],
            'excluded last day' => [
                2021,
                MonthPeriod::MONTH_APRIL,
                new \DateTimeImmutable('2021-05-01'),
                self::INCLUDED_NO
            ],
        ];
    }

    /**
     * @dataProvider datasetBoundaries
     * @test
     */
    public function test_period_boundaries(
        int $year,
        int $month,
        \DateTimeImmutable $day,
        bool $included
    ) {
        $monthPeriod = new MonthPeriod($year, $month);

        $this->assertEquals($included, $monthPeriod->contains($day));
    }

    public function datasetAbsenceOverlap(): array
    {
        return [
            'included' => [
                2021,
                MonthPeriod::MONTH_JANUARY,
                new \DateTimeImmutable('2021-01-10'),
                new \DateTimeImmutable('2021-01-18'),
                self::INCLUDED_YES,
                self::OVERLAPPING_NO,
                self::LONGER_NO,
                8,
            ],
            'not included' => [
                2021,
                MonthPeriod::MONTH_FEBRUARY,
                new \DateTimeImmutable('2021-01-10'),
                new \DateTimeImmutable('2021-01-18'),
                self::INCLUDED_NO,
                self::OVERLAPPING_NO,
                self::LONGER_NO,
                0,
            ],
            'overlap beginning' => [
                2021,
                MonthPeriod::MONTH_JANUARY,
                new \DateTimeImmutable('2020-12-25'),
                new \DateTimeImmutable('2021-01-02'),
                self::INCLUDED_NO,
                self::OVERLAPPING_YES,
                self::LONGER_NO,
                1,
            ],
            'overlap ending' => [
                2021,
                MonthPeriod::MONTH_JANUARY,
                new \DateTimeImmutable('2021-01-23'),
                new \DateTimeImmutable('2021-02-06'),
                self::INCLUDED_NO,
                self::OVERLAPPING_YES,
                self::LONGER_NO,
                9,
            ],
            'absence ends day before' => [
                2021,
                MonthPeriod::MONTH_FEBRUARY,
                new \DateTimeImmutable('2021-01-23'),
                new \DateTimeImmutable('2021-02-01'),
                self::INCLUDED_NO,
                self::OVERLAPPING_NO,
                self::LONGER_NO,
                0,
            ],
            'absence starts day after' => [
                2021,
                MonthPeriod::MONTH_JANUARY,
                new \DateTimeImmutable('2021-02-01'),
                new \DateTimeImmutable('2021-02-06'),
                self::INCLUDED_NO,
                self::OVERLAPPING_NO,
                self::LONGER_NO,
                0,
            ],
            'absence ends on the last day' => [
                2021,
                MonthPeriod::MONTH_JANUARY,
                new \DateTimeImmutable('2021-01-24'),
                new \DateTimeImmutable('2021-02-01'),
                self::INCLUDED_YES,
                self::OVERLAPPING_NO,
                self::LONGER_NO,
                8,
            ],
            'absence starts on the first day' => [
                2021,
                MonthPeriod::MONTH_OCTOBER,
                new \DateTimeImmutable('2021-10-01'),
                new \DateTimeImmutable('2021-10-07'),
                self::INCLUDED_YES,
                self::OVERLAPPING_NO,
                self::LONGER_NO,
                6,
            ],
            'absence longer than month' => [
                2021,
                MonthPeriod::MONTH_JULY,
                new \DateTimeImmutable('2021-06-28'),
                new \DateTimeImmutable('2021-08-07'),
                self::INCLUDED_NO, // as the absence is not fully included, but overlapping on each side
                self::OVERLAPPING_NO,
                self::LONGER_YES,
                31,
            ],

        ];
    }

    /**
     * @dataProvider datasetAbsenceOverlap
     * @test
     */
    public function test_period_overlapping(
        int $year,
        int $month,
        \DateTimeImmutable $absenceFrom,
        \DateTimeImmutable $absenceToExcluded,
        bool $included,
        bool $overlapping,
        bool $longer,
        int $commonDaysCount
    ) {
        $monthPeriod = new MonthPeriod($year, $month);
        $absence = new AbsencePeriod($absenceFrom, $absenceToExcluded);

        $this->assertEquals($included, $monthPeriod->isInclusDansPeriode($absence), 'absence est inclue');
        $this->assertEquals($overlapping, $monthPeriod->isAChevalDansPeriode($absence), 'absence est à cheval');
        $this->assertEquals($longer, $monthPeriod->isPlusLongQuePeriode($absence), 'absence plus longue que le mois');
        $this->assertEquals($commonDaysCount, $monthPeriod->countJoursCommuns($absence), 'nombre de jours communs');
    }

    /**
     * @test
     */
    public function test_it_fails_with_non_existing_month()
    {
        $this->expectException(InvalidPeriodException::class);
        new MonthPeriod(1979, 13);
    }
}
