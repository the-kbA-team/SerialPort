<?php

namespace kbATeam\SerialPort;

use kbATeam\SerialPort\Exceptions\DomainException;
use kbATeam\SerialPort\Exceptions\EofException;
use kbATeam\SerialPort\Exceptions\OpenStreamException;
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
     * Number of microseconds per second.
     */
    private const MICROSECONDS_PER_SECOND = 1000000;

    /**
     * Default number of connection attempts.
     */
    public const DEFAULT_ATTEMPTS = 3;

    /**
     * Default seconds to wait between connection attempts.
     */
    public const DEFAULT_RETRY_WAIT = 1.0;

    /**
     * @var \kbATeam\SerialPort\Interfaces\Stream
     */
    private $stream;

    /**
     * @inheritDoc
     */
    public function __construct(Stream $stream, int $attempts = null, float $retryWait = null)
    {
        $this->stream = $stream;
        //Either use the given number of connection attempts or the default.
        $attempts = $attempts ?: self::DEFAULT_ATTEMPTS;
        //Either use the given retry sleep seconds or the default.
        $retryWait = $retryWait ?: self::DEFAULT_RETRY_WAIT;
        //Convert seconds to microseconds for usleep().
        $retryWait *= self::MICROSECONDS_PER_SECOND;
        //Establish connection.
        $this->connect($attempts, (int)$retryWait);
    }

    /**
     * Try to open the connection.
     * @param int $attemptsLeft
     * @param int $retryWait
     * @throws \kbATeam\SerialPort\Exceptions\OpenStreamException
     * @throws \kbATeam\SerialPort\Exceptions\StreamStateException
     */
    private function connect(int $attemptsLeft, int $retryWait): void
    {
        try {
            $this->stream->open();
        } catch (OpenStreamException $exception) {
            --$attemptsLeft;
            if ($attemptsLeft < 1) {
                throw $exception;
            }
            usleep($retryWait);
            $this->connect($attemptsLeft, $retryWait);
        }
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
                throw new TimeoutException($response, 'Timed out while waiting for termination character.');
            }
            if ($char === null) {
                throw new EofException($response, 'EOF before termination character.');
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
