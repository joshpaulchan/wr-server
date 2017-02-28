# Web-Response/Server

TODO: CI badges

## Intro

This is the server component of web response. It serves the web response API, as well as the angular app maintained in `/client`.

## Usage

### Installation

**Note**: You'll need [Composer](https://getcomposer.org/doc/00-intro.md) to download the required dependencies for the server.

```bash
$ git clone https://github.com/rutgers-ucm/wr-server.git
$ composer install
```

### Running It

After following the installation procedure, copy the downloaded folder to
wherever your Apache web root is and (if necessary) given it the necessary
permissions.

## Architecture

For implementation/design questions in case of future maintenance, please refer
to the [docs](./docs) folder. I've included documentation of the over-arching
design and data modeling of the system, and I've adopted Javadocs-style code
commenting for the code, so the source code should be transparent. Also, read
the Code Igniter 2.x documentation to follow along.

## Changelog

Detailed changes or each release are documents in the [release notes](#).

## License

All rights reserved by the Rutgers University Office of Communications and Media.
