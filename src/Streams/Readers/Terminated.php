<?php

namespace kbATeam\SerialPort\Streams\Readers;

use kbATeam\SerialPort\Exceptions\DomainException;
use kbATeam\SerialPort\Exceptions\EofException;
use kbATeam\SerialPort\Exceptions\TimeoutException;
use kbATeam\SerialPort\Interfaces\Stream;
use kbATeam\SerialPort\Interfaces\Stream\Reader;
use kbATeam\SerialPort\Interfaces\Stream\Timeout;
use function strlen;

/**
 * Read from the stream until the given character appears.
 * @package kbATeam\SerialPort\Streams\Readers
 * @author  Gregor J.
 */
final class Terminated implements Reader
{
    /**
     * @var \kbATeam\SerialPort\Interfaces\Stream
     */
    private $stream;

    /**
     * @var string
     */
    private $termination;

    /**
     * Read from a stream until a termination character appears.
     * @param \kbATeam\SerialPort\Interfaces\Stream $stream      The stream to read.
     * @param string                                $termination The termination
     *                                                           character.
     */
    public function __construct(Stream $stream, string $termination)
    {
        $this->stream = $stream;
        if ($termination === '') {
            throw new DomainException('No termination character given.');
        }
        if (strlen($termination) > 1) {
            throw new DomainException('Multiple termination characters given.');
        }
        $this->termination = $termination;
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
        do {
            $char = $this->stream->readChar();
            if ($char === null && $this->stream->timedOut()) {
                throw new TimeoutException(
                    $response,
                    'Timed out while waiting for termination character.'
                );
            }
            if ($char === null) {
                throw new EofException(
                    $response,
                    'EOF before termination character.'
                );
            }
            $response .= $char;
        } while ($char !== $this->termination);
        return $response;
    }
}
