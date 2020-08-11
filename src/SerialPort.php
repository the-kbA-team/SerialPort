<?php

namespace kbATeam\SerialPort;

use kbATeam\SerialPort\Exceptions\DomainException;
use kbATeam\SerialPort\Exceptions\EofException;
use kbATeam\SerialPort\Exceptions\TimeoutException;
use kbATeam\SerialPort\Interfaces\Communication;
use kbATeam\SerialPort\Interfaces\Stream;

/**
 * Class SerialPort
 * @package kbATeam\SerialPort
 * @author  Gregor J.
 */
final class SerialPort implements Communication
{
    /**
     * @var \kbATeam\SerialPort\Interfaces\Stream
     */
    private $stream;

    /**
     * @inheritDoc
     */
    public function __construct(Stream $stream)
    {
        $this->stream = $stream;
        $this->stream->open();
    }

    /**
     * Close stream
     */
    public function __destruct()
    {
        $this->stream->close();
    }

    /**
     * @inheritDoc
     */
    public function write(string $message): int
    {
        return $this->stream->write($message);
    }

    /**
     * @inheritDoc
     */
    public function read(): string
    {
        $response = '';
        while ($char = $this->stream->readChar()) {
            $response .= $char;
        }
        return $response;
    }

    /**
     * @inheritDoc
     */
    public function readUntil(string $termination): string
    {
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
        } while ($char !== $termination);
        return $response;
    }

    /**
     * @inheritDoc
     */
    public function setTimeout(float $timeout): bool
    {
        if ($timeout < 0) {
            throw new DomainException('Timeout below 0.');
        }
        $formatted = number_format($timeout, 6, '.', '');
        [$seconds, $microseconds] = explode('.', $formatted);
        return $this->stream->setTimeout((int)$seconds, (int)$microseconds);
    }
}
