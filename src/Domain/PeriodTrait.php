<?php

declare(strict_types=1);

namespace App\Domain;

trait PeriodTrait
{
    protected \DateTimeImmutable $from;
    protected \DateTimeImmutable $to;

    protected function setPeriod(\DateTimeImmutable $from, \DateTimeImmutable $to)
    {
        $this->validatePeriod($from, $to);

        $this->from = $from;
        $this->to = $to;
    }

    private function validatePeriod(\DateTimeImmutable $from, \DateTimeImmutable $to)
    {
        if (!($from < $to)) {
            throw InvalidPeriodException::fromIsLaterThanTo($from, $to);
        }
    }

    public function getDaysCount(): int
    {
        return intval($this->getDuration()->format('%a'));
    }

    public function getDuration(): \DateInterval
    {
        return $this->from->diff($this->to);
    }

    public function contains(\DateTimeImmutable $date)
    {
        return $date >= $this->from && $date < $this->to;
    }

    public function countOverlapingDays(\DateTimeImmutable $from, \DateTimeImmutable $to): int
    {
        if ($this->startsWithin($from, $to) && $this->endsWithin($from, $to)) {
            return intval($this->from->diff($this->to)->format('%a'));
        }

        if ($this->startsWithin($from, $to)) {
            return intval($this->from->diff($to)->format('%a'));
        }

        if ($this->endsWithin($from, $to)) {
            return intval($from->diff($this->to)->format('%a'));
        }

        if ($this->startsBefore($from) && $this->endsAfter($to)) {
            return intval($from->diff($to)->format('%a'));
        }

        return 0;
    }

    public function startsWithin(\DateTimeImmutable $from, \DateTimeImmutable $to): bool
    {
        return $this->from >= $from && $this->from < $to;
    }

    public function endsWithin(\DateTimeImmutable $from, \DateTimeImmutable $to): bool
    {
        return $this->to > $from && $this->to <= $to;
    }

    public function startsBefore(\DateTimeImmutable $from): bool
    {
        return $this->from < $from;
    }

    public function endsAfter(\DateTimeImmutable $to): bool
    {
        return $this->to >= $to;
    }
}
