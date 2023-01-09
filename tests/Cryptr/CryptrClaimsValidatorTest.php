<?php

require_once __DIR__ . '../../../vendor/autoload.php';

use Cryptr\CryptrClaimsValidator;
use PHPUnit\Framework\TestCase;
use PHPUnit\Util\Xml\Validator;

class CryptrClaimsValidatorTest extends TestCase
{
  public function testValidRightIssuer()
  {
    $issuer = 'http://localhost:4000/t/shark-academy';
    $validator = new CryptrClaimsValidator($issuer, []);
    $decodedToken = (object) ['iss' => $issuer];
    $this->assertTrue($validator->validateIssuer($decodedToken));
  }
  
  public function testValidRightAudience()
  {
    $audience = 'http://localhost:3000';
    $validator = new CryptrClaimsValidator("", [$audience]);
    $decodedToken = (object) ['aud' => $audience];
    $this->assertTrue($validator->validateAudience($decodedToken));
  }

  public function testRightResourceOwner()
  {
    $resourceOwnerId = '75ee9050-453a-475f-8528-1f92b9b1b77e';
    $decodedToken = (object) ['sub' => $resourceOwnerId];
    $this->assertTrue(CryptrClaimsValidator::validateResourceOwner($decodedToken, $resourceOwnerId));
  }
  
  public function testRightScopes()
  {
    $scopes = ['openid', 'email', 'profile'];
    $decodedToken = (object) ['scp' => $scopes];
    $this->assertTrue(CryptrClaimsValidator::validateScopes($decodedToken, $scopes));
  }

  /**
   * @test
   */
  public function testValidRightExpiration()
  {
    $mock = $this->getMockBuilder(CryptrClaimsValidator::class)
              ->setMethods(['currentTime'])
              ->getMock();
    $mock->expects($this->once())
          ->method('currentTime')
          ->will($this->returnValue(new DateTime('@1587027200')));
    // $mock = $this->createMock(CryptrClaimsValidator::class);
    // $mock->method('currentTime')
    // ->willReturn(new DateTime('@1587027200')); // 2020-04-17 00:00:00

    // 2020-04-17 00:00:01
    $decodedToken = (object)['exp' => 1587027201];
    $audience = 'http://localhost:3000';
    $validator = new CryptrClaimsValidator("", [$audience]);
    $this->assertTrue($validator->validateExpiration($decodedToken));
  }
}