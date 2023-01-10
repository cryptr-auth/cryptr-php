<?php

require_once __DIR__ . '../../../vendor/autoload.php';

use Cryptr\SDK\CryptrClaimsValidator;
use PHPUnit\Framework\TestCase;

$issuer = 'http://localhost:4000/t/shark-academy';
$audience = 'http://localhost:3000';
$resourceOwnerId = '75ee9050-453a-475f-8528-1f92b9b1b77e';
$scopes = ['openid', 'email', 'profile'];
class CryptrClaimsValidatorTest extends TestCase
{

  /**
   * @expectedException Exception
   * @expectedExceptionMessage issuer is required
   */
  public function testWrongIssuerValidator()
  {
    global $audience;
    $this->expectException(\AssertionError::class);
    $this->expectExceptionMessage('issuer is required');
    new CryptrClaimsValidator("", [$audience]);
  }

  /**
   * @expectedException AssertionError
   * @expectedExceptionMessage allowedOrigins is required
   */
  public function testWrongOriginsValidator()
  {
    global $issuer;
    $this->expectException(\AssertionError::class);
    $this->expectExceptionMessage('allowedOrigins is required');
    new CryptrClaimsValidator($issuer, []);
  }

  /**
   * @test
   */
  public function testValidDecodedToken()
  {
    global $issuer, $audience, $resourceOwnerId;
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
  
  public function testWrongDecodedToken()
  {
    global $issuer, $audience, $resourceOwnerId;
    $this->expectException(\Exception::class);
    $this->expectExceptionMessage('The issuedAt of the JWT claim (iat) should be lower than current time');
    $decodedToken = (object)[
      'iss' => $issuer,
      'aud' => $audience,
      'sub' => $resourceOwnerId,
      'exp' => 1736326441,
      'iat' => 1736326441,
    ];
    $validator = new CryptrClaimsValidator($issuer, [$audience]);
    $validator->isValid($decodedToken);
  }

  /**
   * @test
   */
  public function testValidRightExpiration()
  {
    global $audience, $issuer;
    $decodedToken = (object)['exp' => 1736326441];
    $validator = new CryptrClaimsValidator($issuer, [$audience]);
    $this->assertTrue($validator->validateExpiration($decodedToken));
  }
  
  /**
   * @test
   */
  public function testValidWrongExpiration()
  {
    global $audience, $issuer;
    $this->expectException(\Exception::class);
    $this->expectExceptionMessage('The expiration of the JWT claim (exp) should be greater than current time');
    $decodedToken = (object)['exp' => 1673254441];
    $validator = new CryptrClaimsValidator($issuer, [$audience]);
    $validator->validateExpiration($decodedToken);
  }
  
  /**
   * @test
   */
  public function testValidRightIssuedAt()
  {
    global $audience, $issuer;
    $decodedToken = (object)['iat' => 1673254441];
    $validator = new CryptrClaimsValidator($issuer, [$audience]);
    $this->assertTrue($validator->validateIssuedAt($decodedToken));
  }
  
  /**
   * @test
   */
  public function testValidWrongIssuedAt()
  {
    global $audience, $issuer;
    $this->expectException(\Exception::class);
    $this->expectExceptionMessage('The issuedAt of the JWT claim (iat) should be lower than current time');
    $decodedToken = (object)['iat' => 1736326441];
    $validator = new CryptrClaimsValidator($issuer, [$audience]);
    $validator->validateIssuedAt($decodedToken);
  }
  
  /**
   * @test
   */
  public function testValidRightIssuer()
  {
    global $issuer, $audience;
    $validator = new CryptrClaimsValidator($issuer, [$audience]);
    $decodedToken = (object) ['iss' => $issuer];
    $this->assertTrue($validator->validateIssuer($decodedToken));
  }
  
  /**
   * @test
   */
  public function testValidWrongIssuer()
  {
    global $audience, $issuer;
    $this->expectException(\Exception::class);
    $this->expectExceptionMessage('The issuer of the JWT claim (iss) must conform to the issuer from config');
    $validator = new CryptrClaimsValidator($issuer, [$audience]);
    $decodedToken = (object) ['iss' => 'http://example.com'];
    $validator->validateIssuer($decodedToken);
  }

  /**
   * @test
   */
  public function testValidRightAudience()
  {
    global $audience, $issuer;
    $validator = new CryptrClaimsValidator($issuer, [$audience]);
    $decodedToken = (object) ['aud' => $audience];
    $this->assertTrue($validator->validateAudience($decodedToken));
  }
  
  /**
   * @test
   */
  public function testValidWrongAudience()
  {
    global $audience, $issuer;
    $this->expectException(\Exception::class);
    $this->expectExceptionMessage('The audience of the JWT claim (aud) must conform to audience from config');
    $validator = new CryptrClaimsValidator($issuer, [$audience]);
    $decodedToken = (object) ['aud' => 'http://example.com'];
    $validator->validateAudience($decodedToken);
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
  public function testValidWrongResourceOwner()
  {
    global $resourceOwnerId;
    $this->expectException(\Exception::class);
    $this->expectExceptionMessage('The resource owner identifier (cryptr user id) of the JWT claim (sub) is not compliant');
    $decodedToken = (object) ['sub' => 'some-id'];
    CryptrClaimsValidator::validateResourceOwner($decodedToken, $resourceOwnerId);
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
  
  /**
   * @test
   */
  public function testWrongScopes()
  {
    global $scopes;
    $this->expectException(\Exception::class);
    $this->expectExceptionMessage('The scopes of the JWT claim (scp) are not compliant');
    $decodedToken = (object) ['scp' => ['something']];
    CryptrClaimsValidator::validateScopes($decodedToken, $scopes);
  }
  
  public function testWrongExpectedScopes()
  {
    $expectedScope = ['openid', 'email', 'profile', 'some_custom_scope '];
    $this->expectException(\Exception::class);
    $this->expectExceptionMessage('The scopes of the JWT claim (scp) are not compliant');
    $decodedToken = (object) ['scp' => ['openid', 'email', 'profile']];
    CryptrClaimsValidator::validateScopes($decodedToken, $expectedScope);
  }
}