<?php

namespace Cryptr;

use Exception;
use Firebase\JWT\JWK;
use Firebase\JWT\JWT;

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

  public function validateToken(string $token, array $allowedOrigins): bool
  {
    $tenant = self::getTokenTenant($token);
    $issuer = $this->getCryptrBaseUrl() . "/t/" . $tenant;
    $jwksUri = $this->buildJwksUri($issuer);
    $jwks = self::getJwks($jwksUri);
    $publicKeys = JWK::parseKeySet($jwks);
    $decodedToken = JWT::decode($token, $publicKeys, array('RS256'));
    $validator = new CryptrClaimsValidator($issuer, $allowedOrigins);
    return $validator->isValid($decodedToken);
  }

  private function buildJwksUri(string $tenant): string
  {
    return $this->getCryptrBaseUrl() . "/t/" . $tenant . '/.well-known';
  }

  private static function getJwks($jwksUri)
  {
    try {
      $content = file_get_contents($jwksUri, true);
      return json_decode($content, true);
    } catch (Exception $e) {
      echo 'Cannot fetch JWKS : ', $e->getMessage(), "\n";
      return [];
    }
  }

  private static function getClaims(string $token): ?object
  {
    try {
      [, $payload_b64] = explode('.', $token);
      return JWT::jsonDecode(JWT::urlsafeB64Decode($payload_b64));
    } catch (Exception $e) {
      return null;
    }
  }

  private static function getTokenTenant(string $token): ?string
  {
    try {
      return self::getClaims($token)->tnt;
    } catch (Exception $e) {
      echo $e->getMessage();
      throw new \Exception("Invalid token to fetch claims", 1);
    }
  }

  private static function retrieveOrError($inputVal, string $message = "Missing attribute")
  {
    if (isset($inputVal)) {
      return $inputVal;
    }

    return new Exception($message, 1);
  }
}