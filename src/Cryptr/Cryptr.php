<?php

namespace Cryptr;

use Exception;
use Firebase\JWT\JWK;
use Firebase\JWT\JWT;
use Cryptr\CryptrClaimsValidator;

class Cryptr
{
  private string $cryptrBaseUrl;

  public function __construct(string $cryptrBaseUrl)
  {
    assert(!empty($cryptrBaseUrl), '$cryptrBaseUrl is required');
    $this->cryptrBaseUrl = $cryptrBaseUrl;
  }

  public function getCryptrBaseUrl()
  {
    return $this->cryptrBaseUrl;
  }

  public function validateToken(string $token, array $allowedOrigins): bool
  {
    $tenant = self::getTokenTenant($token);
    $jwksUri = $this->buildJwksUriFromTenant($tenant);
    $jwks = $this->getJwks($jwksUri);
    $publicKeys = JWK::parseKeySet($jwks);
    return $this->validateTokenWithKeys($token, $allowedOrigins, $publicKeys);
  }

  public function validateTokenWithKeys(string $token, array $allowedOrigins, array $publicKeys): bool
  {
    $decodedToken = JWT::decode($token, $publicKeys, array('RS256'));
    $validator = new CryptrClaimsValidator($decodedToken->iss, $allowedOrigins);
    return $validator->isValid($decodedToken);
  }

  protected function buildIssuer(string $tenant): string
  {
    return $this->getCryptrBaseUrl() . '/t/' . $tenant;
  }

  private function buildJwksUriFromTenant(string $tenant): string
  {
    return $this->buildIssuer($tenant) . '/.well-known';
  }

  public function getJwks(string $jwksUri): array
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
}