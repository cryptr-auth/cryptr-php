<?php

declare(strict_types=1);

namespace Cryptr;

use Exception;
use Firebase\JWT\JWK;
use Firebase\JWT\JWT;
use Cryptr\CryptrClaimsValidator;

/**
 * Class Cryptr
 */
class Cryptr
{
  /**
   * Cryptr Server URL
   */
  private string $cryptrBaseUrl;
  /**
   * All Client Origins that JWT has to conform
   */
  private array $allowedOrigins;

  /**
   * Cryptr constructor
   * 
   * @param  string|null $cryptrBaseUrl . Cryptr Server URL, if not provided, will be retrieve from $_ENV['CRYPTR_BASE_URL'].
   * @param  array|null $allowedOrigins . Allowed Origins that JWT should conform to, if not provided, will be retrieve from $_ENV['CRYPTR_ALLOWED_ORIGINS'].
   * 
   * @throws \Exception When cryptrBaseUrl is null (either from construct or $_ENV)
   */
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

  /**
   * @return string Return the current Cryptr Sever URL.
   */
  public function getCryptrBaseUrl()
  {
    return $this->cryptrBaseUrl;
  }

  /**
   * @param  string $token REQUIRED. JWT token to validate
   * @param  array|null $allowedOrigins . Origins to validate JWT "aud" claim, if not provided class property will be used
   * 
   * @return bool If the JWT $token provided is valid accordingly to used config
   *
   * @throws \Exception When token is not a JWT or not conforms to used config with proper message
   */
  public function validateToken(string $token, array $allowedOrigins = null): bool
  {
    $tenant = self::getTokenTenant($token);
    $jwksUri = $this->buildJwksUriFromTenant($tenant);
    $jwks = $this->getJwks($jwksUri);
    $publicKeys = JWK::parseKeySet($jwks);
    return $this->validateTokenWithKeys($token, $publicKeys, $allowedOrigins ?: $this->allowedOrigins);
  }

  /**
   * @param  string $token REQUIRED. JWT token to validate
   * @param  array $publicKeys REQUIRED. Public keys to validate the token.
   * @param  array|null $allowedOrigins . Origins to validate JWT "aud" claim, if not provided class property will be used
   * 
   * @return bool If the JWT $token provided is valid accordingly to used config
   *
   * @throws \Exception When token is not a JWT or not conforms to either $publicKeys or used config, giving proper message
   */
  public function validateTokenWithKeys(string $token, array $publicKeys, array $allowedOrigins = null): bool
  {
    $validToken = false;
    try {
      JWT::decode($token, $publicKeys, array('RS256'));
      $validToken = true;
    } catch (\Exception $e) {
      $validToken = false;
    }
    $claims = self::getClaims($token);
    $validator = new CryptrClaimsValidator($claims->iss, $allowedOrigins ?: $this->allowedOrigins);
    $isValid = $validator->isValid($claims);
    return $isValid && $validToken;
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

  /**
   * @param  string $token REQUIRED. JWT token to decode
   *
   * @return  object|null . The decoded object claims of given JWT token
   *
   * @throws  \Exception When $token is not a JWT or decoding failed.
   */  
  public static function getClaims(string $token): ?object
  {
    $wrongFormatException = new Exception("Invalid JWT format", 1);
    try {
      $parts = explode('.', $token);
      if (count($parts) < 2) {
        throw $wrongFormatException;
      }
      [, $payload_b64] = $parts;
      return JWT::jsonDecode(JWT::urlsafeB64Decode($payload_b64));
    } catch (Exception $e) {
      throw $wrongFormatException;
    }
  }

  private static function getTokenTenant(string $token): ?string
  {
    return self::getClaims($token)->tnt;
  }
}
