<?php

namespace Tests\kbATeam\SerialPort;

use kbATeam\SerialPort\Exceptions\OpenStreamException;
use kbATeam\SerialPort\Interfaces\Communication\Command;
use kbATeam\SerialPort\Interfaces\Communication\Response;
use kbATeam\SerialPort\Interfaces\Communication\Value;
use kbATeam\SerialPort\Interfaces\Stream\Reader;
use kbATeam\SerialPort\Interfaces\Stream\Timeout;
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
    public function testCommandWithoutResponse(): void
    {
        /**
         * Build stream mock
         */
        $stream = $this->getMockBuilder(Stream::class)->getMock();
        $stream->expects(static::once())
            ->method('open');
        $stream->expects(static::once())
            ->method('close');
        $stream->expects(static::once())
            ->method('write')
            ->with(self::identicalTo('test command'))
            ->willReturn(strlen('test command'));
        /**
         * Build command mock
         */
        $command = $this->getMockBuilder(Command::class)->getMock();
        $command->expects(static::once())
            ->method('expectResponse')
            ->willReturn(false);
        $command->expects(static::once())
            ->method('get')
            ->willReturn('test command');
        /** @noinspection PhpParamsInspection */
        $device = new SerialPort($stream);
        /** @noinspection PhpParamsInspection */
        $result = $device->invoke($command);
        static::assertNull($result);
    }

    /**
     * Test writing to serial port.
     */
    public function testCommandWithResponse(): void
    {
        /**
         * Build stream mock object
         */
        $stream = $this->getMockBuilder(Stream::class)->getMock();
        $stream->expects(static::once())
            ->method('open');
        $stream->expects(static::once())
            ->method('close');
        $stream->expects(static::once())
            ->method('write')
            ->with(self::identicalTo('another command'))
            ->willReturn(strlen('another command'));
        /**
         * Build reader mock object
         */
        $reader = $this->getMockBuilder(Reader::class)->getMock();
        $reader->expects(static::once())
            ->method('read')
            ->with(static::isInstanceOf(Timeout::class))
            ->willReturn('test response');
        /**
         * Build value mock object
         */
        $value = $this->getMockBuilder(Value::class)->getMock();
        $value->expects(static::once())
            ->method('get')
            ->willReturn('test response');
        /**
         * Build response mock object
         */
        $response = $this->getMockBuilder(Response::class)->getMock();
        $response->expects(static::once())
            ->method('get')
            ->with(self::equalTo(0))
            ->willReturn($value);
        /**
         * Build command mock object
         */
        $command = $this->getMockBuilder(Command::class)->getMock();
        $command->expects(static::once())
            ->method('expectResponse')
            ->willReturn(true);
        $command->expects(static::once())
            ->method('get')
            ->willReturn('another command');
        $command->expects(static::once())
            ->method('getTimeout')
            ->willReturn($this->getMockBuilder(Timeout::class)->getMock());
        $command->expects(static::once())
            ->method('getReader')
            ->willReturn($reader);
        $command->expects(static::once())
            ->method('getResponse')
            ->with('test response')
            ->willReturn($response);
        /**
         * Run invoke test with response
         */
        /** @noinspection PhpParamsInspection */
        $device = new SerialPort($stream);
        /** @noinspection PhpParamsInspection */
        $result = $device->invoke($command);
        static::assertSame($result->get(0)->get(), 'test response');
    }
}
