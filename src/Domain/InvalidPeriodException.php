<?php

declare(strict_types=1);

namespace App\Domain;

class InvalidPeriodException extends \InvalidArgumentException
{
    public static function fromIsLaterThanTo(\DateTimeImmutable $from, \DateTimeImmutable $to): InvalidPeriodException
    {
        return new self(
            sprintf(
                'From date (%s) is after to date (%s)',
                $from->format(\DateTimeInterface::RFC850),
                $to->format(\DateTimeInterface::RFC850)
            )
        );
    }

    public static function badMonth(int $month): InvalidPeriodException
    {
        return new self(
            sprintf(
                'There is no month %d, must be between 1 and 12 included',
                $month,
            )
        );
    }
}
