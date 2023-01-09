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
  public function testValidRightIssuer()
  {
    global $issuer;
    $validator = new CryptrClaimsValidator($issuer, []);
    $decodedToken = (object) ['iss' => $issuer];
    $this->assertTrue($validator->validateIssuer($decodedToken));
  }
  
  public function testValidRightAudience()
  {
    global $audience;
    $validator = new CryptrClaimsValidator("", [$audience]);
    $decodedToken = (object) ['aud' => $audience];
    $this->assertTrue($validator->validateAudience($decodedToken));
  }

  public function testRightResourceOwner()
  {
    global $resourceOwnerId;
    $decodedToken = (object) ['sub' => $resourceOwnerId];
    $this->assertTrue(CryptrClaimsValidator::validateResourceOwner($decodedToken, $resourceOwnerId));
  }
  
  public function testRightScopes()
  {
    global $scopes;
    $decodedToken = (object) ['scp' => $scopes];
    $this->assertTrue(CryptrClaimsValidator::validateScopes($decodedToken, $scopes));
  }

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
}