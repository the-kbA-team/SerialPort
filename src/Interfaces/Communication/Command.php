<?php

namespace kbATeam\SerialPort\Interfaces\Communication;

use kbATeam\SerialPort\Interfaces\Stream\Reader;
use kbATeam\SerialPort\Interfaces\Stream\Timeout;

/**
 * A command is a string sent to a serial port. Depending on the command,
 * there can be a response containing values. Therefore the command not only
 * defines the command string, but also which values to expect and how long to
 * wait for these values.
 * @package kbATeam\SerialPort\Interfaces
 * @author  Gregor J.
 */
interface Command
{
    /**
     * Get the command including all parameters.
     * @return string
     */
    public function get(): string;

    /**
     * Transform the command to a printable string for logging.
     * Non-printable characters are expected to be displayed as printable.
     * @return string
     */
    public function __toString(): string;

    /**
     * Expect a response after invoking the command?
     * @return bool
     */
    public function expectResponse(): bool;

    /**
     * Return a reader instance.
     * The command defines how to read the response.
     * @return \kbATeam\SerialPort\Interfaces\Stream\Reader
     */
    public function getReader(): Reader;

    /**
     * Create a response instance from the given response string.
     * @param string $response
     * @return \kbATeam\SerialPort\Interfaces\Communication\Response
     */
    public function getResponse(string $response): Response;

    /**
     * Get the timeout to wait for a reply, in case this command expects a
     * response.
     * @return \kbATeam\SerialPort\Interfaces\Stream\Timeout Returns NULL in case this command expects no response.
     */
    public function getTimeout(): ?Timeout;
}
