<?php

require_once __DIR__ . '../../../vendor/autoload.php';

use Cryptr\CryptrClaimsValidator;
use PHPUnit\Framework\TestCase;

$issuer = 'http://localhost:4000/t/shark-academy';
$audience = 'http://localhost:3000';
$resourceOwnerId = '75ee9050-453a-475f-8528-1f92b9b1b77e';
$scopes = ['openid', 'email', 'profile'];
class CryptrClaimsValidatorTest extends TestCase
{
  /**
   * @test
   */
  public function testValidRightExpiration()
  {
    global $audience;
    $decodedToken = (object)['exp' => 1736326441];
    $validator = new CryptrClaimsValidator("", [$audience]);
    $this->assertTrue($validator->validateExpiration($decodedToken));
  }

  /**
   * @test
   */
  public function testValidDecodedToken()
  {
    global $issuer, $audience, $resourceOwnerId, $scopes;
    $decodedToken = (object)[
      'iss' => $issuer,
      'aud' => $audience,
      'sub' => $resourceOwnerId,
      'exp' => 1736326441,
      'iat' => 1673254441,
    ];
    $validator = new CryptrClaimsValidator($issuer, [$audience]);
    $this->assertTrue($validator->isValid($decodedToken));
  }
  
  public function testValidRightIssuedAt()
  {
    global $audience;
    $decodedToken = (object)['iat' => 1673254441];
    $validator = new CryptrClaimsValidator("", [$audience]);
    $this->assertTrue($validator->validateIssuedAt($decodedToken));
  }
  
  /**
   * @test
   */
  public function testValidRightIssuer()
  {
    global $issuer;
    $validator = new CryptrClaimsValidator($issuer, []);
    $decodedToken = (object) ['iss' => $issuer];
    $this->assertTrue($validator->validateIssuer($decodedToken));
  }

  /**
   * @test
   */
  public function testValidRightAudience()
  {
    global $audience;
    $validator = new CryptrClaimsValidator("", [$audience]);
    $decodedToken = (object) ['aud' => $audience];
    $this->assertTrue($validator->validateAudience($decodedToken));
  }
  
  /**
   * @test
   */
  public function testRightResourceOwner()
  {
    global $resourceOwnerId;
    $decodedToken = (object) ['sub' => $resourceOwnerId];
    $this->assertTrue(CryptrClaimsValidator::validateResourceOwner($decodedToken, $resourceOwnerId));
  }
  
  /**
   * @test
   */
  public function testRightScopes()
  {
    global $scopes;
    $decodedToken = (object) ['scp' => $scopes];
    $this->assertTrue(CryptrClaimsValidator::validateScopes($decodedToken, $scopes));
  }
}