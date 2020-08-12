<?php

namespace kbATeam\SerialPort\Interfaces\Communication;

/**
 * A response to a command contains one or multiple values. The values from the
 * reponse can be queried by their name.
 * @package kbATeam\SerialPort\Interfaces
 * @author  Gregor J.
 */
interface Response
{
    /**
     * Get a value from the response.
     * You need to know the names of values that will be replied by a command.
     * Use class constants for the value names.
     * @param string $name The name of the value to get from the response.
     * @return \kbATeam\SerialPort\Interfaces\Communication\Value
     */
    public function get(string $name): Value;

    /**
     * Transform the response to a printable string for logging.
     * Non-printable characters are expected to be displayed as printable.
     * @return string
     */
    public function __toString(): string;
}
