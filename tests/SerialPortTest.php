<?php

namespace Tests\kbATeam\SerialPort;

use kbATeam\SerialPort\Exceptions\DomainException;
use kbATeam\SerialPort\Exceptions\EofException;
use kbATeam\SerialPort\Exceptions\OpenStreamException;
use kbATeam\SerialPort\Exceptions\TimeoutException;
use kbATeam\SerialPort\Interfaces\Stream;
use kbATeam\SerialPort\SerialPort;
use PHPUnit\Framework\TestCase;

/**
 * Class SerialPortTest
 * @package Tests\kbATeam\SerialPort
 * @author  Gregor J.
 */
class SerialPortTest extends TestCase
{
    /**
     * TCP port ncat listens on.
     */
    public const ECHO_PORT = 9999;

    /**
     * TCP port ncat listens on.
     */
    public const FIFO_PORT = 9998;

    /**
     * @var string
     */
    private $fifo;

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->fifo = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'fifoService';
    }

    /**
     * Test connection retries.
     */
    public function testConnectionFailed(): void
    {
        $stream = $this->getMockBuilder(Stream::class)->getMock();
        $stream->expects(static::once())
            ->method('open')
            ->willThrowException(new OpenStreamException('Connection failed!', 111));
        $this->expectException(OpenStreamException::class);
        $this->expectExceptionMessage('Connection failed!');
        $this->expectExceptionCode(111);
        /** @noinspection PhpParamsInspection */
        new SerialPort($stream);
    }

    /**
     * Test writing to serial port.
     */
    public function testWrite(): void
    {
        $stream = $this->getMockBuilder(Stream::class)->getMock();
        $stream->expects(static::once())
            ->method('open');
        $stream->expects(static::once())
            ->method('close');
        $stream->expects(static::once())
            ->method('write')
            ->with('test message')
            ->willReturn(strlen('test message'));
        /** @noinspection PhpParamsInspection */
        $device = new SerialPort($stream);
        $device->write('test message');
    }

    /**
     * Test reading from serial port.
     */
    public function testReadChar(): void
    {
        $stream = $this->getMockBuilder(Stream::class)->getMock();
        $stream->expects(static::once())
            ->method('open');
        $stream->expects(static::once())
            ->method('close');
        $stream->expects(self::exactly(3))
            ->method('readChar')
            ->will(self::onConsecutiveCalls('1', '2', null));
        /** @noinspection PhpParamsInspection */
        $device = new SerialPort($stream);
        $result = $device->read();
        static::assertSame('12', $result);
    }

    /**
     * Test reading from serial port until a given character appears.
     */
    public function testReadUntil(): void
    {
        $stream = $this->getMockBuilder(Stream::class)->getMock();
        $stream->expects(static::once())
            ->method('open');
        $stream->expects(static::once())
            ->method('close');
        $stream->expects(static::never())
            ->method('timedOut');
        $stream->expects(self::exactly(2))
            ->method('readChar')
            ->will(self::onConsecutiveCalls('A', 'B', 'C', null));
        /** @noinspection PhpParamsInspection */
        $device = new SerialPort($stream);
        $result = $device->readUntil('B');
        static::assertSame('AB', $result);
    }

    /**
     * Test reading from serial port until timeout because the given char didn't appear
     */
    public function testReadUntilTimeout(): void
    {
        $stream = $this->getMockBuilder(Stream::class)->getMock();
        $stream->expects(static::once())
            ->method('open');
        $stream->expects(static::once())
            ->method('close');
        $stream->expects(static::once())
            ->method('timedOut')
            ->willReturn(true);
        $stream->expects(self::exactly(4))
            ->method('readChar')
            ->will(self::onConsecutiveCalls('A', 'B', 'C', null));
        /** @noinspection PhpParamsInspection */
        $device = new SerialPort($stream);
        $exception = null;
        try {
            $device->readUntil('Z');
        } catch (TimeoutException $exception) {
        }
        static::assertInstanceOf(TimeoutException::class, $exception, 'TimeoutException was never thrown!');
        static::assertSame('ABC', $exception->getResponse());
    }

    /**
     * Test reading from serial port until EOF because the given char didn't appear.
     */
    public function testReadUntilEof(): void
    {
        $stream = $this->getMockBuilder(Stream::class)->getMock();
        $stream->expects(static::once())
            ->method('open');
        $stream->expects(static::once())
            ->method('close');
        $stream->expects(static::once())
            ->method('timedOut')
            ->willReturn(false);
        $stream->expects(self::exactly(4))
            ->method('readChar')
            ->will(self::onConsecutiveCalls('A', 'B', 'C', null));
        /** @noinspection PhpParamsInspection */
        $device = new SerialPort($stream);
        $exception = null;
        try {
            $device->readUntil('Z');
        } catch (EofException $exception) {
        }
        static::assertInstanceOf(EofException::class, $exception, 'EofException was never thrown!');
        static::assertSame('ABC', $exception->getResponse());
    }

    /**
     * Data provider for testSetTimeout()
     * @return array[]
     */
    public static function provideSetTimeout(): array
    {
        return [
            [2.9, 2, 900000],
            [3.1, 3, 100000],
            [4.000005, 4, 5],
            [5.0006, 5, 600]
        ];
    }

    /**
     * @param float $timeout
     * @param int   $seconds
     * @param int   $microseconds
     * @dataProvider provideSetTimeout
     */
    public function testSetTimeout(float $timeout, int $seconds, int $microseconds): void
    {
        $stream = $this->getMockBuilder(Stream::class)->getMock();
        $stream->expects(static::once())
            ->method('open');
        $stream->expects(static::once())
            ->method('close');
        $stream->expects(static::once())
            ->method('setTimeout')
            ->with($seconds, $microseconds)
            ->willReturn(true);
        /** @noinspection PhpParamsInspection */
        $device = new SerialPort($stream);
        $device->setTimeout($timeout);
    }

    /**
     * Test exception on timeout below 0.
     */
    public function testSetTimeoutBelowZero(): void
    {
        $stream = $this->getMockBuilder(Stream::class)->getMock();
        $stream->expects(static::once())
            ->method('open');
        $stream->expects(static::once())
            ->method('close');
        $stream->expects(static::never())
            ->method('setTimeout');
        /** @noinspection PhpParamsInspection */
        $device = new SerialPort($stream);
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Timeout below 0.');
        $device->setTimeout(-1.0);
    }

//    /**
//     * Test the EOF exception while waiting for a termination character, that
//     * doesn't appear.
//     */
//    public function testReadUntilEofException(): void
//    {
//        file_put_contents($this->fifo, 'B', FILE_APPEND);
//        $serial = new SerialPort(new Socket('127.0.0.1', self::FIFO_PORT), 1, 0);
//        $serial->setTimeout(0.5);
//        $exception = null;
//        try {
//            $serial->readUntil('A');
//        } catch (EofException $exception) {
//        }
//        static::assertInstanceOf(EofException::class, $exception, 'EofException was never thrown!');
//        static::assertSame('B', $exception->getResponse());
//    }
//
//    /**
//     * Test the EOF exception while waiting for a termination character, that
//     * doesn't appear.
//     */
//    public function testReadUntilTimeoutException(): void
//    {
//        $serial = new SerialPort(new Socket('127.0.0.1', self::ECHO_PORT), 1, 0);
//        $serial->setTimeout(0.5);
//        $exception = null;
//        try {
//            $serial->readUntil('A');
//        } catch (TimeoutException $exception) {
//        }
//        static::assertInstanceOf(TimeoutException::class, $exception, 'TimeoutException was never thrown!');
//        static::assertSame('', $exception->getResponse());
//    }
}
