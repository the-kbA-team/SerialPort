<?php

namespace kbATeam\SerialPort\Interfaces\Stream;

/**
 * Interface Reader
 * @package kbATeam\SerialPort\Interfaces\Stream
 * @author  Gregor J.
 */
interface Reader
{
    /**
     * Read from a stream.
     * @param \kbATeam\SerialPort\Interfaces\Stream\Timeout $timeout
     * @return string
     * @throws \kbATeam\SerialPort\Exceptions\StreamStateException
     * @throws \kbATeam\SerialPort\Exceptions\TimeoutException
     * @throws \kbATeam\SerialPort\Exceptions\EofException
     */
    public function read(Timeout $timeout): string;
}
