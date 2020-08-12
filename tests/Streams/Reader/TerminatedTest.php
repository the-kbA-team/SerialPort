<?php

namespace Tests\kbATeam\SerialPort\Streams\Reader;

use kbATeam\SerialPort\Exceptions\DomainException;
use kbATeam\SerialPort\Exceptions\EofException;
use kbATeam\SerialPort\Exceptions\ReadException;
use kbATeam\SerialPort\Exceptions\TimeoutException;
use kbATeam\SerialPort\Interfaces\Stream;
use kbATeam\SerialPort\Streams\Reader\Terminated;
use kbATeam\SerialPort\Streams\Timeouts\Seconds;
use PHPUnit\Framework\TestCase;

/**
 * Class TerminatedTest
 * @package Tests\kbATeam\SerialPort\Streams\Reader
 * @author  Gregor J.
 */
final class TerminatedTest extends TestCase
{
    /**
     * Test creating a reader instance with an empty termination character.
     */
    public function testEmptyTerminationChar(): void
    {
        $stream = $this->getMockBuilder(Stream::class)->getMock();
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('No termination character given.');
        /** @noinspection PhpParamsInspection */
        new Terminated($stream, '');
    }

    /**
     * Test creating a reader instance with an empty termination character.
     */
    public function testLongTerminationChar(): void
    {
        $stream = $this->getMockBuilder(Stream::class)->getMock();
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Multiple termination characters given.');
        /** @noinspection PhpParamsInspection */
        new Terminated($stream, 'AB');
    }

    /**
     * Test succesfully reading from a stream until a termination character appears.
     */
    public function testSuccessfulRead(): void
    {
        $stream = $this->getMockBuilder(Stream::class)->getMock();
        $stream->expects(self::once())
            ->method('setTimeout')
            ->with(
                self::identicalTo(2),
                self::identicalTo(600000)
            );
        $stream->expects(self::exactly(2))
            ->method('readChar')
            ->will(self::onConsecutiveCalls('A', 'B', 'C', null));
        /** @noinspection PhpParamsInspection */
        $reader = new Terminated($stream, 'B');
        $timeout = new Seconds(2.6);
        $result = $reader->read($timeout);
        static::assertSame('AB', $result);
    }

    /**
     * Test succesfully reading from a stream until a termination character appears.
     */
    public function testReadUntilTimeout(): void
    {
        $stream = $this->getMockBuilder(Stream::class)->getMock();
        $stream->expects(self::once())
            ->method('setTimeout')
            ->with(
                self::identicalTo(2),
                self::identicalTo(600000)
            );
        $stream->expects(self::exactly(4))
            ->method('readChar')
            ->will(self::onConsecutiveCalls('A', 'B', 'C', null));
        $stream->expects(self::once())
            ->method('timedOut')
            ->willReturn(true);
        /** @noinspection PhpParamsInspection */
        $reader = new Terminated($stream, 'Z');
        $timeout = new Seconds(2.6);
        $exception = null;
        try {
            $reader->read($timeout);
        } catch (ReadException $exception) {
        }
        static::assertNotNull($exception, 'TimeoutException was never thrown!');
        static::assertInstanceOf(TimeoutException::class, $exception);
        static::assertSame('ABC', $exception->getResponse());
    }

    /**
     * Test succesfully reading from a stream until a termination character appears.
     */
    public function testReadUntilEof(): void
    {
        $stream = $this->getMockBuilder(Stream::class)->getMock();
        $stream->expects(self::once())
            ->method('setTimeout')
            ->with(
                self::identicalTo(2),
                self::identicalTo(600000)
            );
        $stream->expects(self::exactly(4))
            ->method('readChar')
            ->will(self::onConsecutiveCalls('A', 'B', 'C', null));
        $stream->expects(self::once())
            ->method('timedOut')
            ->willReturn(false);
        /** @noinspection PhpParamsInspection */
        $reader = new Terminated($stream, 'Z');
        $timeout = new Seconds(2.6);
        $exception = null;
        try {
            $reader->read($timeout);
        } catch (ReadException $exception) {
        }
        static::assertNotNull($exception, 'EofException was never thrown!');
        static::assertInstanceOf(EofException::class, $exception);
        static::assertSame('ABC', $exception->getResponse());
    }
}
