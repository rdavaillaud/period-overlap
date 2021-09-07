<?php

declare(strict_types=1);

namespace App\Domain;

final class AbsencePeriod
{
    use PeriodTrait;

    public function __construct(\DateTimeImmutable $from, \DateTimeImmutable $to)
    {
        $this->setPeriod($from, $to);
    }
}
