<?php

namespace kbATeam\SerialPort\Interfaces;

/**
 * Interface Communication
 * @package kbATeam\SerialPort\Interfaces
 * @author  Gregor J.
 */
interface Communication
{
    /**
     * Open a serial port connection using the given stream.
     * @param \kbATeam\SerialPort\Interfaces\Stream $stream
     * @throws \kbATeam\SerialPort\Exceptions\OpenStreamException
     * @throws \kbATeam\SerialPort\Exceptions\StreamStateException
     */
    public function __construct(Stream $stream);

    /**
     * Close the serial port connection.
     */
    public function __destruct();

    /**
     * Send a message to the serial port.
     * @param string $message The message to send to the serial port.
     * @return int The number of bytes sent.
     * @throws \kbATeam\SerialPort\Exceptions\StreamStateException
     * @throws \kbATeam\SerialPort\Exceptions\WriteStreamException
     */
    public function write(string $message): int;

    /**
     * Read from the serial port until either EOF or the timeout is reached.
     * @return string
     */
    public function read(): string;

    /**
     * Read from the serial port until the given character appears.
     * @param string $termination
     * @return string
     * @throws \kbATeam\SerialPort\Exceptions\TimeoutException
     * @throws \kbATeam\SerialPort\Exceptions\EofException
     */
    public function readUntil(string $termination): string;

    /**
     * Set the timeout for reading from the serial port.
     * The timeout must be set after write(), otherwise it has no effect.
     * @param float $timeout Timeout in seconds.
     * @return bool Returns TRUE on success or FALSE on failure.
     * @throws \kbATeam\SerialPort\Exceptions\DomainException
     */
    public function setTimeout(float $timeout): bool;
}
