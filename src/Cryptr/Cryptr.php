<?php

declare(strict_types=1);

namespace Cryptr;

use Exception;
use Firebase\JWT\JWK;
use Firebase\JWT\JWT;
use Cryptr\CryptrClaimsValidator;

class Cryptr
{
  private string $cryptrBaseUrl;
  private array $allowedOrigins;

  public function __construct(string $cryptrBaseUrl = null, array $allowedOrigins = null)
  {
    $baseUrlFromEnv = isset($_ENV['CRYPTR_BASE_URL']) ? $_ENV['CRYPTR_BASE_URL'] : '';
    $newCryptrBaseUrl = $cryptrBaseUrl ?: $baseUrlFromEnv;
    assert(!empty($newCryptrBaseUrl), 'cryptrBaseUrl is required');
    $this->cryptrBaseUrl = $newCryptrBaseUrl;

    $allowedOriginsFromEnv = isset($_ENV['CRYPTR_ALLOWED_ORIGINS'])
      ? explode(";", $_ENV['CRYPTR_ALLOWED_ORIGINS']) : [];
    $this->allowedOrigins = $allowedOrigins ?: $allowedOriginsFromEnv;
  }

  public function getCryptrBaseUrl()
  {
    return $this->cryptrBaseUrl;
  }

  public function validateToken(string $token, array $allowedOrigins = null): bool
  {
    $tenant = self::getTokenTenant($token);
    $jwksUri = $this->buildJwksUriFromTenant($tenant);
    $jwks = $this->getJwks($jwksUri);
    $publicKeys = JWK::parseKeySet($jwks);
    return $this->validateTokenWithKeys($token, $publicKeys, $allowedOrigins ?: $this->allowedOrigins);
  }

  public function validateTokenWithKeys(string $token, array $publicKeys, array $allowedOrigins = null): bool
  {
    $validToken = false;
    $error = null;
    try {
      JWT::decode($token, $publicKeys, array('RS256'));
      $validToken = true;
    } catch (\Exception $e) {
      $error = $e;
      $validToken = false;
    }
    $claims = self::getClaims($token);
    if (isset($claims)) {
      $validator = new CryptrClaimsValidator($claims->iss, $allowedOrigins ?: $this->allowedOrigins);
      $isValid = $validator->isValid($claims);
      return $isValid && $validToken;
    } else {
      throw $error;
    }
  }

  public function buildIssuer(string $tenant): string
  {
    return $this->getCryptrBaseUrl() . '/t/' . $tenant;
  }

  public function buildJwksUriFromTenant(string $tenant): string
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

  public static function getClaims(string $token): ?object
  {
    try {
      [, $payload_b64] = explode('.', $token);
      return JWT::jsonDecode(JWT::urlsafeB64Decode($payload_b64));
    } catch (Exception $e) {
      throw new \Exception("Invalid JWT format", 1);
    }
  }

  private static function getTokenTenant(string $token): ?string
  {
    return self::getClaims($token)->tnt;
  }
}
