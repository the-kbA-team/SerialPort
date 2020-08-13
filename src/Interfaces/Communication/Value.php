<?php

namespace kbATeam\SerialPort\Interfaces\Communication;

/**
 * A single response value can only be a primitive type (bool, int, float or
 * string). The value optionally has a unit like ampere, seconds, celsius, etc.
 * @package kbATeam\SerialPort\Interfaces
 * @author  Gregor J.
 */
interface Value
{
    /**
     * Get the actual value.
     * As you will be requesting this value, you should know what type it has.
     * @return bool|int|float|string
     */
    public function get();

    /**
     * Get the unit of this value, e.g. ampere, seconds, celsius, etc.
     * @return string|null Returns null in case this value has no unit.
     */
    public function getUnit(): ?string;

    /**
     * Transform this value to a printable string for logging.
     * Non-printable characters are expected to be displayed as printable.
     * @return string
     */
    public function __toString(): string;
}
