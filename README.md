# Cryptr-PHP

PHP SDK for [Cryptr](https://cryptr.co) Authentication processes

Learn more on our [online Documentation 📚](https://docs.cryptr.co).

## Installation

### Requirements

- PHP 7.3+


### Installation

Use the following composer command to install our SDK

```bash
composer require cryptr/cryptr-php
```

This SDK is mainly focused on PHP API applications, securing your endpoints.

here is how you can use it

```php
<?php

require Cryptr\Cryptr;

$yourFetchedTokenFromAuthHeader = 'ey....';
$cryptrBaseUrl = "https://your-company.authent.me";
$allowedOrigins = ["https://www.your-company.com"];

$cryptr = new Cryptr($cryptrBaseUrl, $allowedOrigins);

$cryptr->validateToken($yourFetchedTokenFromAuthHeader) // -> returns true or throw according error

// Be aware that we look for $_ENV['CRYPTR_BASE_URL'] and $_ENV['CRYPTR_ALLOWED_ORIGINS']
// Like that you can just call
$cryptr = new Cryptr();

```
