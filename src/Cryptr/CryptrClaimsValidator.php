<?php

namespace Cryptr;

use DateTime;
use Exception;

class CryptrClaimsValidator
{
  public function __construct(protected string $issuer, protected array $allowedOrigins)
  {
    
  }
  
  public function isValid(object $decodedToken)
  {
    return $this->validateExpiration($decodedToken) &&
      $this->validateIssuedAt($decodedToken)
      && $this->validateIssuer($decodedToken)
      && $this->validateAudience($decodedToken);
  }

  public function validateExpiration(object $decodedToken): bool
  {
    $expiration = DateTime::createFromFormat('U', $decodedToken->exp);
    if ($expiration < $this->currentTime()) {
      throw new Exception('The expiration of the JWT claim (exp) should be greater than current time');
    }
    return true;
  }

  public function validateIssuedAt(object $decodedToken): bool
  {
    $issuedAt = DateTime::createFromFormat('U', $decodedToken->iat);
    if ($this->currentTime() < $issuedAt) {
      throw new Exception('The issuedAt of the JWT claim (iat) should be lower than current time');
    }
    return true;
  }

  public function validateIssuer(object $decodedToken): bool
  {
    if ($decodedToken->iss != $this->issuer) {
      throw new Exception('The issuer of the JWT claim (iss) must conform to the issuer from config');
    }
    return true;
  }

  public function validateAudience(object $decodedToken): bool
  {
    if(!in_array($decodedToken->aud, $this->allowedOrigins)) {
      throw new Exception('The audience of the JWT claim (aud) must conform to audience from config');
    }
    return true;
  }

  public static function validateResourceOwner(object $decodedToken, string $userId): bool
  {
    if ($decodedToken->sub != $userId) {
      throw new Exception('The resource owner identifier (cryptr user id) of the JWT claim (sub) is not compliant');
    }

    return true;
  }

  public static function validateScopes(object $decodedToken, array $authorizedScopes): bool
  {
    if (array_intersect($decodedToken->scp, $authorizedScopes) != $decodedToken->scp) {
      throw new Exception('The scopes of the JWT claim (scp) are not compliants');
    }

    return true;
  }


  protected function currentTime(): DateTime
  {
    return new DateTime();
  }
}