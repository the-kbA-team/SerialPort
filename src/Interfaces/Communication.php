<?php

namespace kbATeam\SerialPort\Interfaces;

use kbATeam\SerialPort\Interfaces\Communication\Command;
use kbATeam\SerialPort\Interfaces\Communication\Response;

/**
 * A stream communication interface to send commands and get responses.
 * @package kbATeam\SerialPort\Interfaces
 * @author  Gregor J.
 */
interface Communication
{
    /**
     * Open a connection using the given stream.
     * @param \kbATeam\SerialPort\Interfaces\Stream $stream
     * @throws \kbATeam\SerialPort\Exceptions\OpenStreamException
     * @throws \kbATeam\SerialPort\Exceptions\StreamStateException
     */
    public function __construct(Stream $stream);

    /**
     * Close the connection to the stream.
     */
    public function __destruct();

    /**
     * Invoke a command on the stream.
     * @param \kbATeam\SerialPort\Interfaces\Communication\Command $command
     * @return \kbATeam\SerialPort\Interfaces\Communication\Response|null Returns
     *                                                                    null in
     *                                                                    case the
     *                                                                    command
     *                                                                    expects no
     *                                                                    response
     * @throws \kbATeam\SerialPort\Exceptions\StreamStateException
     * @throws \kbATeam\SerialPort\Exceptions\WriteStreamException
     * @throws \kbATeam\SerialPort\Exceptions\TimeoutException
     * @throws \kbATeam\SerialPort\Exceptions\EofException
     */
    public function invoke(Command $command): ?Response;
}
