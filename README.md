# SerialPort

[![License: MIT][license-mit]](LICENSE)
[![Build Status][build-status]][travis-ci]
[![Maintainability][maintainability-badge]][maintainability]
[![Test Coverage][coverage-badge]][coverage]

PHP class `\kbATeam\SerialPort\SerialPort` to connect to serial ports using streams. Nothing more. Only socket (TCP) streams are implemented at the moment.

You need to create classes implementing `\kbATeam\SerialPort\Interfaces\Communication\Command`, `\kbATeam\SerialPort\Interfaces\Communication\Container` and `\kbATeam\SerialPort\Interfaces\Communication\Value`. The implementations of these interfaces depend on the device you want to communicate with.

## Usage

Use [pySerial] to map a serial device to a TCP port.

Use `Streams\Socket` to create a connection to a `SerialPort` use `SerialPort->invoke()` to invoke a _`Communication\Command`_ and get either `NULL` or a _`Communication\Container`_ containing at least one _`Communication\Value`_.

A _`Communication\Command`_ instance is a string sent to a `SerialPort` instance. The `SerialPort` instance invokes the _`Communication\Command`_ using its _`Stream`_ instance.

Your implementation of _`Communication\Command`_ needs to define how to read the string returned by the device and either return a _`Communication\Container`_ containing at least one `Communication\Value`, or `NULL`.



[pySerial]: https://pyserial.readthedocs.io/en/latest/examples.html
[license-mit]: https://img.shields.io/badge/license-MIT-blue.svg
[build-status]: https://travis-ci.org/the-kbA-team/SerialPort.svg?branch=master
[travis-ci]: https://travis-ci.org/the-kbA-team/SerialPort
[maintainability-badge]: https://api.codeclimate.com/v1/badges/b3bbe0f1d518dbeacc07/maintainability
[maintainability]: https://codeclimate.com/github/the-kbA-team/SerialPort/maintainability
[coverage-badge]: https://api.codeclimate.com/v1/badges/b3bbe0f1d518dbeacc07/test_coverage
[coverage]: https://codeclimate.com/github/the-kbA-team/SerialPort/test_coverage
