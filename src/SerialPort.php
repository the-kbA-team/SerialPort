<?php

namespace kbATeam\SerialPort;

use kbATeam\SerialPort\Interfaces\Communication\Command;
use kbATeam\SerialPort\Interfaces\Communication\Response;
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
    public function invoke(Command $command): ?Response
    {
        $this->stream->write($command->get());
        if (!$command->expectResponse()) {
            return null;
        }
        $reader = $command->getReader();
        $response = $reader->read($command->getTimeout());
        return $command->getResponse($response);
    }
}
