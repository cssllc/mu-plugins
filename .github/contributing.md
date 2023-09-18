# Contributing

[![PHPCS](https://github.com/cssllc/mu-plugins/actions/workflows/phpcs.yml/badge.svg)](https://github.com/cssllc/mu-plugins/actions/workflows/phpcs.yml)
[![PHPStan](https://github.com/cssllc/mu-plugins/actions/workflows/phpstan.yml/badge.svg)](https://github.com/cssllc/mu-plugins/actions/workflows/phpstan.yml)

## Checks

There are two checks run on pull requests:

1. PHPCS
1. PHPStan

PHPCS and PHPStan are managed via Composer.

### Setup

To setup the checks locally, run the install command with the root of the WordPress install (directory with `composer.json`):

```
composer install
```

### Run

#### PHPCS

```
vendor/bin/phpcs
```

#### PHPStan

```
vendor/bin/phpstan --memory-limit=-1
```