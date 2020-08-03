<?php

namespace Tests\kbATeam\SerialPort\Streams;

use kbATeam\SerialPort\Exceptions\StreamStateException;
use kbATeam\SerialPort\Streams\Socket;
use PHPUnit\Framework\TestCase;

/**
 * Class SocketTest
 * @package Tests\kbATeam\SerialPort\Streams
 * @author  Gregor J.
 */
class SocketTest extends TestCase
{
    /**
     * TCP port ncat listens on.
     */
    public const NCAT_PORT = 9999;

    /**
     * Test actual reading and writing from an echo service.
     */
    public function testReadingAndWriting(): void
    {
        $socket = new Socket('127.0.0.1', self::NCAT_PORT);
        $socket->open();
        $bytes = $socket->write('1234');
        static::assertSame(4, $bytes);
        $socket->setTimeout(0, 500000);
        $response = '';
        while ($char = $socket->readChar()) {
            $response .= $char;
        }
        static::assertSame('1234', $response);
        $socket->close();
    }

    /**
     * Test exception thrown in case stream is already opened.
     */
    public function testOpeningTwice(): void
    {
        $socket = new Socket('127.0.0.1', self::NCAT_PORT);
        $socket->open();
        $this->expectException(StreamStateException::class);
        $this->expectExceptionMessage('Stream already opened.');
        $socket->open();
    }

    /**
     * Test exception thrown in case stream is not opened.
     */
    public function testWritingWithoutOpeningFirst(): void
    {
        $socket = new Socket('127.0.0.1', self::NCAT_PORT);
        $this->expectException(StreamStateException::class);
        $this->expectExceptionMessage('Stream not opened.');
        $socket->write('');
    }

    /**
     * Test exception thrown in case stream is not opened.
     */
    public function testReadWithoutOpeningFirst(): void
    {
        $socket = new Socket('127.0.0.1', self::NCAT_PORT);
        $this->expectException(StreamStateException::class);
        $this->expectExceptionMessage('Stream not opened.');
        $socket->readChar();
    }

    /**
     * Test exception thrown in case stream is not opened.
     */
    public function testSetTimeoutWithoutOpeningFirst(): void
    {
        $socket = new Socket('127.0.0.1', self::NCAT_PORT);
        $this->expectException(StreamStateException::class);
        $this->expectExceptionMessage('Stream not opened.');
        $socket->setTimeout(0, 0);
    }
}
