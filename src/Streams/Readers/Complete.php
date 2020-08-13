<?php

namespace kbATeam\SerialPort\Streams\Readers;

use kbATeam\SerialPort\Interfaces\Stream;
use kbATeam\SerialPort\Interfaces\Stream\Reader;
use kbATeam\SerialPort\Interfaces\Stream\Timeout;

/**
 * Read from the stream until either EOF or the timeout is reached but don't throw
 * an exception upon timeout.
 * @package kbATeam\SerialPort\Streams\Readers
 * @author  Gregor J.
 */
final class Complete implements Reader
{
    /**
     * @var \kbATeam\SerialPort\Interfaces\Stream
     */
    private $stream;

    /**
     * Read everything from the stream.
     * @param \kbATeam\SerialPort\Interfaces\Stream $stream The stream to read.
     */
    public function __construct(Stream $stream)
    {
        $this->stream = $stream;
    }

    /**
     * @inheritDoc
     */
    public function read(Timeout $timeout): string
    {
        $this->stream->setTimeout(
            $timeout->getSeconds(),
            $timeout->getMicroseconds()
        );
        $response = '';
        while ($char = $this->stream->readChar()) {
            $response .= $char;
        }
        return $response;
    }
}
