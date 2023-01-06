<?php

require_once __DIR__ . '../../../vendor/autoload.php';

use Cryptr\Cryptr;
use PHPUnit\Framework\TestCase;


final class CryptrTest extends TestCase
{

  /**
   * @test
   */
  public function testWrongConfig()
  {
    $this->expectException(AssertionError::class);
    $this->expectExceptionMessage('$cryptrBaseUrl is required');
    $cryptr = new Cryptr("");
    //  $this->expectException(Exception::class);
  }
  
  public function testProperConfig()
  {
    $cryptr = new Cryptr('https://some.base.url');
    $this->assertEquals($cryptr->getCryptrBaseUrl(), 'https://some.base.url');
  }

  /**
   * @expectedException Exception
   * @expectedExceptionMessage Invalid token to fetch claims
   */
  public function testWrongToken()
  {
    $this->expectException(Exception::class);
    $this->expectExceptionMessage('Invalid token to fetch claims');
    $cryptr = new Cryptr('http://localhost:4000');
    $cryptr->validateToken('azerty', []);
  }
  
  public function testRightToken()
  {
    $cryptr = new Cryptr('http://localhost:4000');
    $rightToken = "eyJhbGciOiJSUzI1NiIsImlzcyI6Imh0dHA6Ly9sb2NhbGhvc3Q6NDAwMC90L2RyYWctbi1zdXJ2ZXkiLCJraWQiOiI0ODg4YzQ0OC04NjI4LTQyNGYtYjYzMi1hYTc3MjNhYWJmMWQiLCJ0eXAiOiJKV1QifQ.eyJhcHBsaWNhdGlvbl9tZXRhZGF0YSI6e30sImF1ZCI6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMCIsImNpZCI6IjdkNzBlZWQ1LWU2MDItNDU0MS1iN2E2LWZjZWI3N2JkMWE0NCIsImRicyI6InNhbmRib3giLCJlbWFpbCI6InRoaWJhdWRAZHJhZy1uLXN1cnZleS5jbyIsImV4cCI6MTY3MzA1MTUxMywiaWF0IjoxNjczMDE1NTE0LCJpcHMiOiJjcnlwdHIiLCJpc3MiOiJodHRwOi8vbG9jYWxob3N0OjQwMDAvdC9kcmFnLW4tc3VydmV5IiwianRpIjoiNjQ3YzBhMmQtODZlNy00ZTBlLWE5MjQtMGJkNWQwOTgxNWY2IiwianR0IjoiYWNjZXNzIiwic2NpIjpudWxsLCJzY3AiOlsib3BlbmlkIiwiZW1haWwiLCJwcm9maWxlIl0sInN1YiI6IjkwYzA3Mzc0LWE1NjQtNDc2Zi04NmJlLWY4Mjk1Mzg2NmVlZCIsInRudCI6ImRyYWctbi1zdXJ2ZXkiLCJ2ZXIiOjF9.pnqncUXyaVfdEL1NreATyAMCPq73WfvBOtTF7oEa4b9XHjBs0n1RqHYNNhJTvPTvYLcddRHRv6tyhwkEckxkCBJm_ZWWWR1c0O_PwlUIq_ecEihhp2qzR2-e2Dr2sI45oDziQTHKxG44wKA_aYoerTFCvzPJpLiHuIOfZziCbN2LiB7qmk7IrhfDuldu7H-6D64-AxfyUXQEN23FI3W6mQlneFQPn00s99u204_hTFHKlzP1v8nuppZ5biEpODVKM955vjD9kAlJIc5eltlkLPBv6mXtPq1sWZRALQ4dhSsKkuqBCBZn4MutrC5b5hvYQXutcRa13wpByTO3EGe0FQiDIwT4K8un_YWYE2qDjxsAp3p3MlvunHJPD638GvZYQ9hvtZ-BQk67cig2OOh6fHnHs-CwXI8L_npN50QemHcdl_hyPr_H_xn3E0EY1bcjBko_zWT0xz44PEIMzuJG45snSmDUXvwQqZQHgdF93BLoTbONd0y0fCj1YoGstYYapQsITB5F3FOczcFoKDPJVcQDRX32b4CZBDZjeR_RaGCqYwo4KrRHDzrcXVE0IUVNB9d7sKa6Cx1sb_RqKArYAjoI6CdsH9CRC72vHbcB-t7QJdGs08x3PsioNqvQGV4B0woo2XNAIc8G7ZrQir4CIwlD0W3Cyx2NtLDIK8m9SOg";
    $allowedOrigins = ["http://localhost:8000"];
    $this->assertTrue($cryptr->validateToken($rightToken, $allowedOrigins));
  }

  /**
   * @test
   */
  public function testTokenWithKeysMock()
  {
    $mock = $this->createMock(Cryptr::class);
    $mock->method('getJwks')->willReturn(['keys' => []]);
    $rightToken = "eyJhbGciOiJSUzI1NiIsImlzcyI6Imh0dHA6Ly9sb2NhbGhvc3Q6NDAwMC90L2RyYWctbi1zdXJ2ZXkiLCJraWQiOiI0ODg4YzQ0OC04NjI4LTQyNGYtYjYzMi1hYTc3MjNhYWJmMWQiLCJ0eXAiOiJKV1QifQ.eyJhcHBsaWNhdGlvbl9tZXRhZGF0YSI6e30sImF1ZCI6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMCIsImNpZCI6IjdkNzBlZWQ1LWU2MDItNDU0MS1iN2E2LWZjZWI3N2JkMWE0NCIsImRicyI6InNhbmRib3giLCJlbWFpbCI6InRoaWJhdWRAZHJhZy1uLXN1cnZleS5jbyIsImV4cCI6MTY3MzA1MTUxMywiaWF0IjoxNjczMDE1NTE0LCJpcHMiOiJjcnlwdHIiLCJpc3MiOiJodHRwOi8vbG9jYWxob3N0OjQwMDAvdC9kcmFnLW4tc3VydmV5IiwianRpIjoiNjQ3YzBhMmQtODZlNy00ZTBlLWE5MjQtMGJkNWQwOTgxNWY2IiwianR0IjoiYWNjZXNzIiwic2NpIjpudWxsLCJzY3AiOlsib3BlbmlkIiwiZW1haWwiLCJwcm9maWxlIl0sInN1YiI6IjkwYzA3Mzc0LWE1NjQtNDc2Zi04NmJlLWY4Mjk1Mzg2NmVlZCIsInRudCI6ImRyYWctbi1zdXJ2ZXkiLCJ2ZXIiOjF9.pnqncUXyaVfdEL1NreATyAMCPq73WfvBOtTF7oEa4b9XHjBs0n1RqHYNNhJTvPTvYLcddRHRv6tyhwkEckxkCBJm_ZWWWR1c0O_PwlUIq_ecEihhp2qzR2-e2Dr2sI45oDziQTHKxG44wKA_aYoerTFCvzPJpLiHuIOfZziCbN2LiB7qmk7IrhfDuldu7H-6D64-AxfyUXQEN23FI3W6mQlneFQPn00s99u204_hTFHKlzP1v8nuppZ5biEpODVKM955vjD9kAlJIc5eltlkLPBv6mXtPq1sWZRALQ4dhSsKkuqBCBZn4MutrC5b5hvYQXutcRa13wpByTO3EGe0FQiDIwT4K8un_YWYE2qDjxsAp3p3MlvunHJPD638GvZYQ9hvtZ-BQk67cig2OOh6fHnHs-CwXI8L_npN50QemHcdl_hyPr_H_xn3E0EY1bcjBko_zWT0xz44PEIMzuJG45snSmDUXvwQqZQHgdF93BLoTbONd0y0fCj1YoGstYYapQsITB5F3FOczcFoKDPJVcQDRX32b4CZBDZjeR_RaGCqYwo4KrRHDzrcXVE0IUVNB9d7sKa6Cx1sb_RqKArYAjoI6CdsH9CRC72vHbcB-t7QJdGs08x3PsioNqvQGV4B0woo2XNAIc8G7ZrQir4CIwlD0W3Cyx2NtLDIK8m9SOg";
    $allowedOrigins = ["https://example.com"];
    $this->assertTrue($mock->validateToken($token, $allowedOrigins));
  }
}