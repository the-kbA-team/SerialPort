<?php

namespace kbATeam\SerialPort\Streams;

use kbATeam\SerialPort\Exceptions\OpenStreamException;
use kbATeam\SerialPort\Exceptions\StreamStateException;
use kbATeam\SerialPort\Exceptions\WriteStreamException;
use kbATeam\SerialPort\Interfaces\Stream;
use function error_get_last;
use function fclose;
use function fgetc;
use function fsockopen;
use function fwrite;
use function is_resource;
use function stream_set_timeout;
use function strlen;

/**
 * Class Socket
 * Create a TCP connection stream
 *
 * Bluntly copied and adapted from Peter Gribanovs example:
 * @link https://github.com/jupeter/clean-code-php/issues/178
 *
 * @package kbATeam\SerialPort\Streams
 * @author  Gregor J.
 * @author  Peter Gribanov
 */
final class Socket implements Stream
{
    /**
     * Default connection timeout in seconds.
     */
    public const DEFAULT_CONNECTION_TIMEOUT = 2;

    /**
     * @var string Hostname/IP
     */
    private $host;

    /**
     * @var int TCP port
     */
    private $port;

    /**
     * @var int Connection timeout
     */
    private $connectionTimeout;

    /**
     * @var resource
     */
    private $socket;

    /**
     * Create a TCP socket.
     * @param string     $host The hostname.
     * @param int        $port The port number.
     * @param float|null $timeout The optional connection timeout, in seconds.
     */
    public function __construct(string $host, int $port, float $timeout = null)
    {
        $this->host = $host;
        $this->port = $port;
        $this->connectionTimeout = $timeout ?: self::DEFAULT_CONNECTION_TIMEOUT;
    }

    /**
     * Socket destructor
     */
    public function __destruct()
    {
        $this->close();
    }

    /**
     * @inheritDoc
     */
    public function isOpen(): bool
    {
        return is_resource($this->socket);
    }

    /**
     * @inheritDoc
     */
    public function open(): void
    {
        if ($this->isOpen()) {
            throw new StreamStateException('Stream already opened.');
        }
        $socket = @fsockopen($this->host, $this->port, $errno, $errstr, $this->connectionTimeout);
        if (!is_resource($socket)) {
            throw new OpenStreamException($errstr, $errno);
        }
        $this->socket = $socket;
    }

    /**
     * @inheritDoc
     */
    public function close(): void
    {
        if ($this->isOpen()) {
            fclose($this->socket);
            $this->socket = null;
        }
    }

    /**
     * @inheritDoc
     */
    public function write(string $string): int
    {
        if (!$this->isOpen()) {
            throw new StreamStateException('Stream not opened.');
        }
        $length = strlen($string);
        $bytes = fwrite($this->socket, $string, $length);
        if ($bytes === false) {
            throw new WriteStreamException(error_get_last());
        }
        return $bytes;
    }

    /**
     * @inheritDoc
     */
    public function readChar(): ?string
    {
        if (!$this->isOpen()) {
            throw new StreamStateException('Stream not opened.');
        }
        $char = fgetc($this->socket);
        if ($char === false) {
            return null;
        }
        return $char;
    }

    /**
     * @inheritDoc
     */
    public function setTimeout(int $seconds, int $microseconds): bool
    {
        if (!$this->isOpen()) {
            throw new StreamStateException('Stream not opened.');
        }
        return stream_set_timeout($this->socket, $seconds, $microseconds);
    }

    /**
     * @inheritDoc
     */
    public function timedOut(): bool
    {
        if (!$this->isOpen()) {
            throw new StreamStateException('Stream not opened.');
        }
        $metadata = stream_get_meta_data($this->socket);
        return (bool)$metadata['timed_out'];
    }
}
