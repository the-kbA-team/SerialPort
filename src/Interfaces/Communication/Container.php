<?php

namespace kbATeam\SerialPort\Interfaces\Communication;

/**
 * The Command needs to know which values will be returned and assigns
 * these values to names. A Values from the Reponse can be queried by its name.
 * Use Command class constants for Value names.
 * @package kbATeam\SerialPort\Interfaces
 * @author  Gregor J.
 */
interface Container
{
    /**
     * Get a Value from the Container.
     * @param string $name The name of the Value.
     * @return \kbATeam\SerialPort\Interfaces\Communication\Value
     * @throws \kbATeam\SerialPort\Exceptions\NotFoundException
     */
    public function get(string $name): Value;

    /**
     * Determine whether the Container contains a Value.
     * @param string $name The name of the Value.
     * @return bool
     */
    public function has(string $name): bool;

    /**
     * Transform the Container to a printable string for logging.
     * Non-printable characters are expected to be displayed as printable!
     * @return string
     */
    public function __toString(): string;
}
