# laminas-stratigility

[![Build Status](https://travis-ci.org/laminas/laminas-stratigility.svg?branch=master)](https://travis-ci.org/laminas/laminas-stratigility)

> From "Strata", Latin for "layer", and "agility".

This package supercedes and replaces [phly/conduit](https://github.com/phly/conduit).

Stratigility is a port of [Sencha Connect](https://github.com/senchalabs/connect) to PHP. It allows you to build applications out of _middleware_.

* File issues at https://github.com/laminas/laminas-stratigility/issues
* Issue patches to https://github.com/laminas/laminas-stratigility/pulls

## Installation

Install this library using composer:

```console
$ composer require laminas/laminas-diactoros laminas/laminas-stratigility
```

## Documentation

Documentation is [in the doc tree](doc/), and can be compiled using [bookdown](http://bookdown.io):

```console
$ bookdown doc/bookdown.json
$ php -S 0.0.0.0:8080 -t doc/html/ # then browse to http://localhost:8080/
```

> ### Bookdown
>
> You can install bookdown globally using `composer global require bookdown/bookdown`. If you do
> this, make sure that `$HOME/.composer/vendor/bin` is on your `$PATH`.
