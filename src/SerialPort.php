<?php

namespace kbATeam\SerialPort;

use kbATeam\SerialPort\Interfaces\Communication\Command;
use kbATeam\SerialPort\Interfaces\Communication\Container;
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
     * @inheritDoc
     */
    public function __destruct()
    {
        $this->stream->close();
    }

    /**
     * @inheritDoc
     */
    public function invoke(Command $command): ?Container
    {
        return $command->invoke($this->stream);
    }
}
