<?php

declare(strict_types=1);

namespace Cryptr;

use DateTime;
use Exception;

/**
 * Class CryptrClaimsValidator
 */
class CryptrClaimsValidator
{
  /**
   * @param  string $issuer REQUIRED. Issuer URL that JWT should conform to.
   * @param  array $allowedOrigins REQUIRED. Allowed Origins that JWT should conform to.
   *
   * @throws \AssertionError When $issuer or $allowedOrigins is empty string.
   */
  public function __construct(protected string $issuer, protected array $allowedOrigins)
  {
    assert(!empty($issuer), 'issuer is required');
    assert(!empty($allowedOrigins), 'allowedOrigins is required');
  }
  
  /**
   * @param object $decodedToken REQUIRED. Decoded JWT token, can be \Cryptr->getClaims($jwtStringValue).
   *
   * @return bool When all validations succeeded.
   *
   * @throws \Exception Since one of validations failed, according validation error message.
   */
  public function isValid(object $decodedToken)
  {
    return $this->validateExpiration($decodedToken) &&
      $this->validateIssuedAt($decodedToken)
      && $this->validateIssuer($decodedToken)
      && $this->validateAudience($decodedToken);
  }

  /**
   * @param object $decodedToken REQUIRED. Decoded JWT token, can be \Cryptr->getClaims($jwtStringValue).
   *
   * @return bool Result if JWT's expiration not yet reached.
   *
   * @throws \Exception When JWT is expired, message "The expiration of the JWT claim (exp) should be greater than current time".
   */
  public function validateExpiration(object $decodedToken): bool
  {
    $expiration = DateTime::createFromFormat('U', strval($decodedToken->exp));
    if ($expiration < $this->currentTime()) {
      throw new Exception('The expiration of the JWT claim (exp) should be greater than current time');
    }
    return true;
  }

  /**
   * @param object $decodedToken REQUIRED. Decoded JWT token, can be \Cryptr->getClaims($jwtStringValue).
   *
   * @return bool Result if JWT's issuation date yet reached.
   *
   * @throws \Exception When JWT issuation date is in the future, message "The issuedAt of the JWT claim (iat) should be lower than current time".
   */
  public function validateIssuedAt(object $decodedToken): bool
  {
    $issuedAt = DateTime::createFromFormat('U', strval($decodedToken->iat));
    if ($this->currentTime() < $issuedAt) {
      throw new Exception('The issuedAt of the JWT claim (iat) should be lower than current time');
    }
    return true;
  }

  /**
   * @param object $decodedToken REQUIRED. Decoded JWT token, can be \Cryptr->getClaims($jwtStringValue).
   *
   * @return bool Result if JWT's issuer conform to Validator's.
   *
   * @throws \Exception When JWT issuer not match $issuer, message "The issuer of the JWT claim (iss) must conform to the issuer from config".
   */
  public function validateIssuer(object $decodedToken): bool
  {
    assert(!empty($this->issuer), 'issuer is required');
    if ($decodedToken->iss != $this->issuer) {
      throw new Exception('The issuer of the JWT claim (iss) must conform to the issuer from config');
    }
    return true;
  }

  /**
   * @param object $decodedToken REQUIRED. Decoded JWT token, can be \Cryptr->getClaims($jwtStringValue).
   *
   * @return bool Result if JWT's audience is included in $allowedOrigins.
   *
   * @throws \Exception When JWT audience not included in $allowedOrigins, message "The audience of the JWT claim (aud) must conform to audience from config".
   */
  public function validateAudience(object $decodedToken): bool
  {
    if (!in_array($decodedToken->aud, $this->allowedOrigins)) {
      throw new Exception('The audience of the JWT claim (aud) must conform to audience from config');
    }
    return true;
  }

  /**
   * @param object $decodedToken REQUIRED. Decoded JWT token, can be \Cryptr->getClaims($jwtStringValue).
   * @param string $userId REQUIRED. Cryptr User Id to compare to.
   *
   * @return bool Result if JWT's sub equals $userId.
   *
   * @throws \Exception When JWT sub not equals $userId, message "The resource owner identifier (cryptr user id) of the JWT claim (sub) is not compliant".
   */
  public static function validateResourceOwner(object $decodedToken, string $userId): bool
  {
    if ($decodedToken->sub != $userId) {
      throw new Exception('The resource owner identifier (cryptr user id) of the JWT claim (sub) is not compliant');
    }

    return true;
  }

  /**
   * @param object $decodedToken REQUIRED. Decoded JWT token, can be \Cryptr->getClaims($jwtStringValue).
   * @param array $expectedScopes REQUIRED. Expected scopes present in the JWT.
   *
   * @return bool Result if all $expectedScopes are included in JWT's scp value.
   *
   * @throws \Exception When one or many $expectedScopes values are not in JWT, message "The scopes of the JWT claim (scp) are not compliants".
   */
  public static function validateScopes(object $decodedToken, array $expectedScopes): bool
  {
    if (array_intersect($decodedToken->scp, $expectedScopes) != $expectedScopes) {
      throw new Exception('The scopes of the JWT claim (scp) are not compliants');
    }

    return true;
  }


  protected function currentTime(): DateTime
  {
    return new DateTime();
  }
}
