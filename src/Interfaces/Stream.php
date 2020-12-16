<?php

namespace kbATeam\SerialPort\Interfaces;

/**
 * A stream interface to write to and read from.
 *
 * Bluntly copied and adapted from Peter Gribanovs example:
 * @link https://github.com/jupeter/clean-code-php/issues/178
 *
 * @package kbATeam\SerialPort\Interfaces
 * @author  Gregor J.
 * @author  Peter Gribanov
 */
interface Stream
{
    /**
     * Has the stream already been opened?
     * @return bool
     */
    public function isOpen(): bool;

    /**
     * Opens a stream
     * @throws \kbATeam\SerialPort\Exceptions\OpenStreamException
     * @throws \kbATeam\SerialPort\Exceptions\StreamStateException
     */
    public function open(): void;

    /**
     * Closes a stream
     */
    public function close(): void;

    /**
     * Writes the contents of the string to the stream.
     * @param string $string The string that is to be written.
     * @return int returns the number of bytes written
     * @throws \kbATeam\SerialPort\Exceptions\StreamStateException
     * @throws \kbATeam\SerialPort\Exceptions\WriteStreamException
     */
    public function write(string $string): int;

    /**
     * Read a single character from the stream.
     * @return string|null Returns a string containing a single character read
     *                     from the stream. Returns NULL on EOF.
     * @throws \kbATeam\SerialPort\Exceptions\StreamStateException
     */
    public function readChar(): ?string;

    /**
     * Set timeout period on the stream.
     * @param int $seconds The seconds part of the timeout to be set.
     * @param int $microseconds The microseconds part of the timeout to be set.
     * @return bool Returns TRUE on success or FALSE on failure.
     * @throws \kbATeam\SerialPort\Exceptions\StreamStateException
     */
    public function setTimeout(int $seconds, int $microseconds): bool;

    /**
     * Retrieves timeout meta data from the stream.
     * @return bool TRUE if the stream timed out while waiting for data on the last readChar().
     * @throws \kbATeam\SerialPort\Exceptions\StreamStateException
     */
    public function timedOut(): bool;
}
