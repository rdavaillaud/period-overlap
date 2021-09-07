<?php

declare(strict_types=1);

namespace App\Tests\Domain;

use App\Domain\AbsencePeriod;
use App\Domain\PeriodTrait;
use PHPUnit\Framework\TestCase;

class AbsencePeriodTest extends TestCase
{
    /**
     * @test
     */
    public function test_it_is_a_period()
    {
        $this->assertContains(PeriodTrait::class, (new \ReflectionClass(AbsencePeriod::class))->getTraitNames());
    }
}
