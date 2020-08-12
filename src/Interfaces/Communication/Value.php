<?php

namespace kbATeam\SerialPort\Interfaces\Communication;

/**
 * A single response value can only be a primitive type (bool, int, float or
 * string). The type of the value can be used to store a unit (ampere, seconds,
 * celsius).
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
     * Get the type string of this value, e.g. a unit like ampere, seconds, or
     * celsius.
     * @return string|null Returns null in case this value has no type.
     */
    public function type(): ?string;

    /**
     * Transform this value to a printable string for logging.
     * Non-printable characters are expected to be displayed as printable.
     * @return string
     */
    public function __toString(): string;
}
