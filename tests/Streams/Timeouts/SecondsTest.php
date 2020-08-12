<?php

namespace Tests\kbATeam\SerialPort\Streams\Timeouts;

use kbATeam\SerialPort\Exceptions\DomainException;
use kbATeam\SerialPort\Streams\Timeouts\Seconds;
use PHPUnit\Framework\TestCase;

/**
 * Class SecondsTest
 * @package Tests\kbATeam\SerialPort\Streams\Timeouts
 * @author  Gregor J.
 */
final class SecondsTest extends TestCase
{
    /**
     * Data provider for the successful conversion of a float value to seconds
     * and microseconds.
     * @return array[]
     */
    public function provideSuccessfulConversions(): array
    {
        return [
            [2.9, 2, 900000],
            [3.1, 3, 100000],
            [4.000005, 4, 5],
            [5.0006, 5, 600]
        ];
    }

    /**
     * Test successful conversion of a float value to seconds and microseconds.
     * @param float $value
     * @param int   $seconds
     * @param int   $microseconds
     * @dataProvider provideSuccessfulConversions
     */
    public function testSuccessfulConversions(float $value, int $seconds, int $microseconds): void
    {
        $timeout = new Seconds($value);
        static::assertSame($seconds, $timeout->getSeconds());
        static::assertSame($microseconds, $timeout->getMicroseconds());
    }

    /**
     * Test exception on a negative value.
     */
    public function testValueBelowZero(): void
    {
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Timeout below 0.');
        new Seconds(-1.0);
    }
}
