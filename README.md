# SerialPort

[![License: MIT][license-mit]](LICENSE)
[![Build Status][build-status]][travis-ci]
[![Maintainability][maintainability-badge]][maintainability]
[![Test Coverage][coverage-badge]][coverage]

PHP class `\kbATeam\SerialPort\SerialPort` to connect to serial ports using streams. Nothing more. Only socket (TCP) streams are implemented at the moment. You need to create classes implementing `\kbATeam\SerialPort\Interfaces\Communication\Command`, `\kbATeam\SerialPort\Interfaces\Communication\Response` and `\kbATeam\SerialPort\Interfaces\Communication\Value`. The implementations of these interfaces depend on the device you want to communicate with.

## Usage

Use [pySerial](https://pyserial.readthedocs.io/en/latest/examples.html) to map a serial device to a TCP port.

Use `Streams\Socket` to create a connection to a `SerialPort` use `SerialPort->invoke()` to invoke a _`Communication\Command`_ and get either `NULL` or a _`Communication\Response`_ containing at least one _`Communication\Value`_.

A _`Communication\Command`_ instance is a string sent to a `SerialPort` instance. Depending on the command, there can be a _`Communication\Response`_ containing at least one `Communication\Value`. In case `Command->expectsResponse() === false`, `NULL` is returned instead of a `Communication\Response`.

The _`Communication\Command`_ defines how to read the string returned by the device via a class implementing the _`Stream\Reader`_ interface. This repository provides the `Streams\Readers\Complete` and `Streams\Readers\Terminated` classes implementing the _`Stream\Reader`_ interface.

The `Streams\Readers\Complete` class will read the whole response until either `EOF` or the _`Stream\Timeout`_ is reached. It will not throw a `TimeoutException` or `EofException`.  The `Streams\Readers\Terminated` class will read until a given termination character appears. It will throw a `TimeoutException` or `EofException` in case the _`Stream\Timeout`_ or `EOF` is reached before the termination character appears. The _`Stream\Reader`_ implementations will return a string.

`Reader->read()` requires a timeout defined by a class implementing the _`Stream\Timeout`_ interface. This repository provides the `Streams\Timeouts\Seconds` class implementing the _`Stream\Timeout`_ interface. The _`Communication\Command`_ defines which _`Stream\Timeout`_ implementation to use and its configuration.

The _`Communication\Response`_ parses a string retrieved from a _`Stream\Reader`_ instance and created at least one _`Communication\Value`_ from that parsed string. The _`Communication\Response`_ defines the names of these _`Communication\Value`_ instances. A _`Communication\Value`_ contains one parsed and typecasted value and optionally a unit, like ampere, seconds, celsius, etc.

[license-mit]: https://img.shields.io/badge/license-MIT-blue.svg
[build-status]: https://travis-ci.org/the-kbA-team/SerialPort.svg?branch=master
[travis-ci]: https://travis-ci.org/the-kbA-team/SerialPort
[maintainability-badge]: https://api.codeclimate.com/v1/badges/b3bbe0f1d518dbeacc07/maintainability
[maintainability]: https://codeclimate.com/github/the-kbA-team/SerialPort/maintainability
[coverage-badge]: https://api.codeclimate.com/v1/badges/b3bbe0f1d518dbeacc07/test_coverage
[coverage]: https://codeclimate.com/github/the-kbA-team/SerialPort/test_coverage
