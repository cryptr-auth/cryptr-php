<?php

require_once __DIR__ . '../../../vendor/autoload.php';

use Cryptr\Cryptr;
use PHPUnit\Framework\TestCase;

$unexpiredToken = "eyJhbGciOiJSUzI1NiIsImlzcyI6Imh0dHA6Ly9sb2NhbGhvc3Q6NDAwMC90L2RyYWctbi1zdXJ2ZXkiLCJraWQiOiI0ODg4YzQ0OC04NjI4LTQyNGYtYjYzMi1hYTc3MjNhYWJmMWQiLCJ0eXAiOiJKV1QifQ.eyJhcHBsaWNhdGlvbl9tZXRhZGF0YSI6e30sImF1ZCI6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMCIsImNpZCI6IjdkNzBlZWQ1LWU2MDItNDU0MS1iN2E2LWZjZWI3N2JkMWE0NCIsImRicyI6InNhbmRib3giLCJlbWFpbCI6InRoaWJhdWRAZHJhZy1uLXN1cnZleS5jbyIsImV4cCI6MTczNjMyNjQ0MSwiaWF0IjoxNjczMjU0NDQxLCJpcHMiOiJjcnlwdHIiLCJpc3MiOiJodHRwOi8vbG9jYWxob3N0OjQwMDAvdC9kcmFnLW4tc3VydmV5IiwianRpIjoiOWExOTNmODYtYjFmZC00MTFjLTk1MGItNjM3NzQ3OWNmN2JhIiwianR0IjoiYWNjZXNzIiwic2NpIjpudWxsLCJzY3AiOlsib3BlbmlkIiwiZW1haWwiLCJwcm9maWxlIl0sInN1YiI6IjkwYzA3Mzc0LWE1NjQtNDc2Zi04NmJlLWY4Mjk1Mzg2NmVlZCIsInRudCI6ImRyYWctbi1zdXJ2ZXkiLCJ2ZXIiOjF9.KNqq8oMB5-mFPrIQmfVU-zHqHOMt8IjSCOrpkEweXkrCkXcX6CI66o_81sjL_N2iEaFp2i_Im0qV0FZxWcxLLUf4_ecRjFDvOdekOL0RKlUbtXSXpQmA2Vmf-n8bfhA0ZpQRXpmLaQwnxks5AqobyBKeceL3QjsyKYdNaLwy4lskItk0cm1q35dYR9dtAUhaF5XSww_N4axInD-YaXFQCbjNgAwhMXirDHPYfNiXdZwqSZe3nqcFcwjdO1FU-0xz9UPcN4qWSb5S7pPn4gk5CGE5njjugu93o2LXqPVj_PZBBoFYQArajCWKF62YN5DbB10QN8ILTYz_d65Lkz5h3EcOjRKuF9tCgnT5tVhzc3_wRJ6Zf8yXmv2EFlTo2DOTuIJLWbSfW4RVHZ68vig_gPhX1lRQAANlZ97fSg0xur7d5GN2Vg58vln4G4PjaB_5ze78OkVUoPqJ8q9R-q10RSt37AF8gbpi64UxoP_J6dPwhLNYg2RqaMZAQ_lLcMDaAFOKD5NVj8Q4vwYF5-vhcg9EjUm-Vhm7_QBmsRLDlgWvz3JaqMdEyv2RS58gm6OXks8mAHX30VuqooQoo5YxcUXvXs0p-P4JtZqW1JXL8AwNFh5GvsXYmcFBINEbF22U4_B4dah8OCSC5vCNxcZeSgIUTOrAKnBR00rJTOHLOm8";
$expiredToken = "eyJhbGciOiJSUzI1NiIsImlzcyI6Imh0dHA6Ly9sb2NhbGhvc3Q6NDAwMC90L2RyYWctbi1zdXJ2ZXkiLCJraWQiOiI0ODg4YzQ0OC04NjI4LTQyNGYtYjYzMi1hYTc3MjNhYWJmMWQiLCJ0eXAiOiJKV1QifQ.eyJhcHBsaWNhdGlvbl9tZXRhZGF0YSI6e30sImF1ZCI6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMCIsImNpZCI6IjdkNzBlZWQ1LWU2MDItNDU0MS1iN2E2LWZjZWI3N2JkMWE0NCIsImRicyI6InNhbmRib3giLCJlbWFpbCI6InRoaWJhdWRAZHJhZy1uLXN1cnZleS5jbyIsImV4cCI6MTY3MzM0MzgyMSwiaWF0IjoxNjczMzQzODIyLCJpcHMiOiJjcnlwdHIiLCJpc3MiOiJodHRwOi8vbG9jYWxob3N0OjQwMDAvdC9kcmFnLW4tc3VydmV5IiwianRpIjoiYjE2MWVhNDYtYTQyZi00ODU0LTlmODctMDM3ZDA0OGMyNTRkIiwianR0IjoiYWNjZXNzIiwic2NpIjpudWxsLCJzY3AiOlsib3BlbmlkIiwiZW1haWwiLCJwcm9maWxlIl0sInN1YiI6IjkwYzA3Mzc0LWE1NjQtNDc2Zi04NmJlLWY4Mjk1Mzg2NmVlZCIsInRudCI6ImRyYWctbi1zdXJ2ZXkiLCJ2ZXIiOjF9.u1decjiFBoa56UCkqauYoPy8Y8az2mJRY-OTOqYwnDY2C2fM7_cTivWUO26vOZEXzUmSqrzGTxJZNi1EEZa_0zqE0SafrDtcrhWo6U7FitF4o-jNPn-0e0vKs6061R1E9fqe21hCysTIOKuHlv78Qw3vzu5eXb2hDqxuI3yXpsazx7bxDw3iMbrJin9Y67kRHb0kb28r3PV2wVL3Z30Jbu9cBBmBwZdJXnVeadz5QV5S3PaVoj3zXC7LfibyFKv_qyzmhW9wfLqEA5xrbq30wEglaQsqWQHi9CpPItT3nsJuMoNO_VMD4zD9bluYcbckRKJZqJEiXgQhLsxB4WkHXNND9Bvl83ZyE20RNnOwYiHYpxnxdfmX-IJyIrLjXHbTEOAa2vLAmxCuHD4SOlrYExb2ToIf2MOofcxFlzhgTfvrh4KVNLuCRBeKR7TOkwgTvIjAnosSOi45uRM2ivh_cLysBG-PyYzq1uPsnyCDZf8iUBPs9ttFGCioT0suwaB3M7M5hlwJMuqxShvLSdMza968OFroXJNQ9-70kR5Sj6jq97F2t4FY2UhveN8VMt-pbgxVURhqp4J-9SQo4ZqComhNhlf1pZy-G7b8Eo-mF5r_6xESIRfuCak7SOyGvx3rf_l7Xk3bQZMwfBeBjWJhyhh2EgoZHdotRcLULFSBvMg";

$key = array(
  "alg" => "RS256",
  "e" => "AQAB",
  "kid" => "4888c448-8628-424f-b632-aa7723aabf1d",
  "kty" => "RSA",
  "n" => "7eq2KCQZIX5p1iJWcsWq--vSILghaMfuac3TnqM3jgQHNBW2mTMpiNp1v0W0PrKOtvujBEtgq4mqagSEa7LSotDu8IW8Ve-N6KaBnVlmyy_dG_pN96QOlKVcsUTIHi5DrHg2dF1mRaKil-rn5exUv3pVsA8jDCrNlnmQeA_mf7mi6Mue7v1hG2e4JbWpVGEhYHDr5SWO9otuucaK_DlZGpuqVg3q14oNkKnjlP8L-N48VXBS0ehXRBjaNjQ3_Iv5D2OVzk-8s6XWI2AKsw3F85r9ngIE9d3S1e_BTqg4xATS0E8bBNsRzqpQItjcWRwPqZEXYbgpWnb_W7X524dEtTsgvZkFEm4Sw_dnp3qDpvvi8mr5BWqpQgaWCjPaHZehoU2e0Bu-QpvLqGye8YFHfmOkXK1OWXj6K3MSDyt1TUPhmAFe19TwCz2aThZV3Me0w7fRF3MOOLdvx8FE1AL_5svq3-A_8iqt-HPnJ_p-EE4cP1p69jxjS3QY9XLBn5XH4h15j8eCw2KJ7q3V0k9whbZVzILvnqJz8HWAL3pXPx-NbsraMZY7pBSDCWn0Jtj4VksLgVOTPVLWiHKbsymwqgceGfxF1iaYeDu_IF8oeOQauPJsoP3zFKREqHl0-dg7dZOJ9IA77vUvxjxoajoZ72yTF68V1BWZ8G-iB6IAWUU",
  "use" => "sig",
  "x5c" => array("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"),
  "x5t_sha256" => "uCo7jM9dizykIG_-y6rbO0j8HreCyPMeU4IykFIYNiM="
);
$cryptrBaseUrl = 'http://localhost:4000';
$clientUrl = "http://localhost:8000";
$tenant = "drag-n-survey";

final class CryptrTest extends \PHPUnit\Framework\TestCase
{

  /**
   * @test
   */
  public function testWrongConfigWithEmptyBaseUrl()
  {
    $this->expectException(AssertionError::class);
    $this->expectExceptionMessage('cryptrBaseUrl is required');
    new Cryptr("");
  }
  
  /**
   * @test
   */
  public function testWrongConfigWithNullBaseUrl()
  {
    $this->expectException(AssertionError::class);
    $this->expectExceptionMessage('cryptrBaseUrl is required');
    new Cryptr();
  }
  
  /**
   * @test
   */
  public function testRightConfigWithNullBaseUrl()
  {
    global $cryptrBaseUrl;
    $_ENV['CRYPTR_BASE_URL'] = $cryptrBaseUrl;
    $cryptr = new Cryptr();
    $this->assertEquals($cryptr->getCryptrBaseUrl(), 'http://localhost:4000');
  }
  
  public function testProperConfig()
  {
    global $cryptrBaseUrl;
    $cryptr = new Cryptr($cryptrBaseUrl);
    $this->assertEquals($cryptr->getCryptrBaseUrl(), $cryptrBaseUrl);
  }

  public function testIssuerBuild()
  {
    global $cryptrBaseUrl;
    $cryptr = new Cryptr($cryptrBaseUrl);
    $this->assertEquals($cryptr->buildIssuer('shark-academy'), 'http://localhost:4000/t/shark-academy');
  }
  
  public function testJwksUriBuild()
  {
    global $cryptrBaseUrl;
    $cryptr = new Cryptr($cryptrBaseUrl);
    $this->assertEquals($cryptr->buildJwksUriFromTenant('shark-academy'), 'http://localhost:4000/t/shark-academy/.well-known');
  }

  /**
   * @test
   */
  public function testRightToken()
  {
    global $key, $unexpiredToken, $cryptrBaseUrl, $clientUrl;
    $jwks = array("keys" => array($key));
    $cryptr = new Cryptr($cryptrBaseUrl);
    $publicKeys = \Firebase\JWT\JWK::parseKeySet($jwks);
    $allowedOrigins = [$clientUrl];
    $res = $cryptr->validateTokenWithKeys($unexpiredToken, $publicKeys, $allowedOrigins);
    $this->assertTrue($res);
  }

  /**
   * @expectedException Exception
   * @expectedExceptionMessage Invalid JWT format
   */
  public function testWrongToken()
  {
    global $key, $cryptrBaseUrl, $clientUrl;
    $this->expectException(Exception::class);
    $this->expectExceptionMessage('Invalid JWT format');
    $jwks = array("keys" => array($key));
    $cryptr = new Cryptr($cryptrBaseUrl);
    $publicKeys = \Firebase\JWT\JWK::parseKeySet($jwks);
    $allowedOrigins = [$clientUrl];
    $cryptr->validateTokenWithKeys('azerty', $publicKeys, $allowedOrigins);
  }

  public function testExpiredToken()
  {
    global $key, $expiredToken, $cryptrBaseUrl, $clientUrl;
    $this->expectException(Exception::class);
    $this->expectExceptionMessage('The expiration of the JWT claim (exp) should be greater than current time');
    $jwks = array('keys' => array($key));
    $cryptr = new Cryptr($cryptrBaseUrl);
    $publicKeys = \Firebase\JWT\JWK::parseKeySet(($jwks));
    $allowedOrigins = [$clientUrl];
    $cryptr->validateTokenWithKeys($expiredToken, $publicKeys, $allowedOrigins);
  }

  /**
   * @test
  */
  public function testTokenWithoutOriginShouldFail()
  {
    global $key, $cryptrBaseUrl, $unexpiredToken;
    $this->expectException(\AssertionError::class);
    $this->expectExceptionMessage('allowedOrigins is required');
    $jwks = array('keys' => array($key));
    $cryptr = new Cryptr($cryptrBaseUrl);
    $publicKeys = \Firebase\JWT\JWK::parseKeySet($jwks);
    $cryptr->validateTokenWithKeys($unexpiredToken, $publicKeys, null);
  }

  public function testClaimsForRightToken()
  {
    global $cryptrBaseUrl, $unexpiredToken, $clientUrl, $tenant;
    $cryptr = new Cryptr($cryptrBaseUrl);
    $expectedClaims = array(
      'application_metadata' => new stdClass(),
      'aud' => $clientUrl,
      'cid' => '7d70eed5-e602-4541-b7a6-fceb77bd1a44',
      'dbs' => 'sandbox',
      'email' => 'thibaud@drag-n-survey.co',
      'exp' => 1736326441,
      'iat' => 1673254441,
      'ips' => 'cryptr',
      'iss' => $cryptrBaseUrl . '/t/' . $tenant,
      'jti' => '9a193f86-b1fd-411c-950b-6377479cf7ba',
      'jtt' => 'access',
      'sci' => null,
      'scp' => ['openid', 'email', 'profile'],
      'sub' => '90c07374-a564-476f-86be-f82953866eed',
      'tnt' => 'drag-n-survey',
      'ver' => 1
 
    );
    $this->assertEquals(get_object_vars($cryptr->getClaims($unexpiredToken)), $expectedClaims);
  }

  public function testClaimsForWrongToken()
  {
    global $cryptrBaseUrl;
    $this->expectException(\Exception::class);
    $this->expectExceptionMessage('Invalid JWT format');
    $cryptr = new Cryptr($cryptrBaseUrl);
    $cryptr->getClaims('azerty');
  }
}