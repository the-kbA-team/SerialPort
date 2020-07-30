<?php

namespace kbATeam\SerialPort\Interfaces;

/**
 * Interface Stream
 * Defines how to read from either a file or a tcp connection.
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
     * Gets character from
     * @return string|null Returns a string containing a single character read
     *                     from the stream. Returns NULL on EOF.
     * @throws \kbATeam\SerialPort\Exceptions\StreamStateException
     */
    public function readChar(): ?string;

    /**
     * Set timeout period on the stream
     * @param int $seconds The seconds part of the timeout to be set.
     * @param int $microseconds The microseconds part of the timeout to be set.
     * @return bool Returns TRUE on success or FALSE on failure.
     */
    public function setTimeout(int $seconds, int $microseconds): bool;

    /**
     * Tests for end-of-file on the stream.
     * @return bool Returns TRUE if the stream is at EOF or an error occurs
     *              (including socket timeout); otherwise returns FALSE.
     * @throws \kbATeam\SerialPort\Exceptions\StreamStateException
     */
    public function eof(): bool;

    /**
     * Set blocking/non-blocking mode on the stream.
     * This function works for any stream that supports non-blocking mode
     * (currently, regular files and socket streams).
     * @param bool $mode If mode is FALSE, the given stream will be switched to
     *                   non-blocking mode, and if TRUE, it will be switched to
     *                   blocking mode. This affects calls like fgets() and
     *                   fread() that read from the stream. In non-blocking mode
     *                   an fgets() call will always return right away while in
     *                   blocking mode it will wait for data to become available
     *                   on the stream.
     * @return bool Returns TRUE on success or FALSE on failure.
     * @throws \kbATeam\SerialPort\Exceptions\StreamStateException
     */
    public function blocking(bool $mode): bool;
}