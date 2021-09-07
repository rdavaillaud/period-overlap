<?php

declare(strict_types=1);

namespace App\Domain;

final class MonthPeriod
{
    const MONTH_JANUARY = 1;
    const MONTH_FEBRUARY = 2;
    const MONTH_MARCH = 3;
    const MONTH_APRIL = 4;
    const MONTH_MAY = 5;
    const MONTH_JUNE = 6;
    const MONTH_JULY = 7;
    const MONTH_AUGUST = 8;
    const MONTH_SEPTEMBER = 9;
    const MONTH_OCTOBER = 10;
    const MONTH_NOVEMBER = 11;
    const MONTH_DECEMBER = 12;

    use PeriodTrait;

    public function __construct(int $year, int $month)
    {
        if ($month < 1 || $month > 12) {
            throw InvalidPeriodException::badMonth($month);
        }
        $year = str_pad(strval($year), 4, '0', STR_PAD_LEFT);
        $month = str_pad(strval($month), 2, '0', STR_PAD_LEFT);

        $from = new \DateTimeImmutable($year . $month . '01T00:00:00');
        $to = $from->add(\DateInterval::createFromDateString('+1 month'));

        $this->setPeriod($from, $to);
    }

    public function isAChevalDansPeriode(AbsencePeriod $absence): bool
    {
        return !$this->isInclusDansPeriode($absence)
            && ($absence->startsWithin($this->from, $this->to) || $absence->endsWithin($this->from, $this->to));
    }

    public function isInclusDansPeriode(AbsencePeriod $absence): bool
    {
        return $absence->startsWithin($this->from, $this->to) && $absence->endsWithin($this->from, $this->to);
    }

    public function isPlusLongQuePeriode(AbsencePeriod $absence): bool
    {
        return $absence->startsBefore($this->from) && $absence->endsAfter($this->to);
    }

    public function countJoursCommuns(AbsencePeriod $absence): int
    {
        return $absence->countOverlapingDays($this->from, $this->to);
    }
}
