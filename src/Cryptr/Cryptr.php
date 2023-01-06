<?php

namespace Cryptr;

use Exception;

class Cryptr
{
  private string $cryptrBaseUrl;

  public function __construct(string $cryptrBaseUrl = null)
  {
    $this->setCryptrBaseUrl($cryptrBaseUrl);
  }

  protected function setCryptrBaseUrl(string $cryptrBaseUrl = null)
  {
    $newCryptrBaseUrl = $cryptrBaseUrl;
    $this->cryptrBaseUrl = self::retrieveOrError($newCryptrBaseUrl);
  }

  public function getCryptrBaseUrl()
  {
    return $this->cryptrBaseUrl;
  }

  private static function retrieveOrError($inputVal, string $message = "Missing attribute")
  {
    if(isset($inputVal)) {
      return $inputVal;
    }

    return new Exception($message, 1);
  }
}