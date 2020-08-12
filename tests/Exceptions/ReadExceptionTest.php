<?php

namespace Tests\kbATeam\SerialPort\Exceptions;

use kbATeam\SerialPort\Exceptions\ReadException;
use PHPUnit\Framework\TestCase;

/**
 * Class ReadExceptionTest
 * @package Tests\kbATeam\SerialPort\Exceptions
 * @author  Gregor J.
 */
final class ReadExceptionTest extends TestCase
{
    /**
     * Test setting and getting a response.
     */
    public function testSettingResponse(): void
    {
        $exception = new ReadException('ABC');
        static::assertSame('ABC', $exception->getResponse());
        static::assertSame('', $exception->getMessage());
        static::assertSame(0, $exception->getCode());
        static::assertNull($exception->getPrevious());
    }
}
