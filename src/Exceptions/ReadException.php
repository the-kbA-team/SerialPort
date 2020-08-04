<?php

namespace kbATeam\SerialPort\Exceptions;

use Throwable;

/**
 * Class ReadException
 * @package kbATeam\SerialPort\Exceptions
 * @author  Gregor J.
 */
class ReadException extends RuntimeException
{
    /**
     * @var string
     */
    private $response;

    /**
     * Construct the exception. Note: The message is NOT binary safe.
     * @link https://php.net/manual/en/exception.construct.php
     * @param string          $response [optional] The response from the serial port until the exception is thrown.
     * @param string          $message  [optional] The Exception message to throw.
     * @param int             $code     [optional] The Exception code.
     * @param \Throwable|null $previous [optional] The previous throwable used for the
     *                                  exception chaining.
     */
    public function __construct(string $response = "", string $message = "", int $code = 0, Throwable $previous = null)
    {
        $this->response = $response;
        parent::__construct($message, $code, $previous);
    }

    /**
     * @return string
     */
    public function getResponse(): string
    {
        return $this->response;
    }
}
