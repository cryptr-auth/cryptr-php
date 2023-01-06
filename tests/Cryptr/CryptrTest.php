<?php

require_once __DIR__ . '../../../vendor/autoload.php';

use Cryptr\Cryptr;
use PHPUnit\Framework\TestCase;


final class CryptrTest extends TestCase
{

  /**
   * @expectedException Exception
   * @expectedExceptionMessage Missing attribute
   */
  public function testSomeTest()
  {
    new Cryptr();
  }
  
  public function testSecondTest()
  {
    $cryptr = new Cryptr('https://some.base.url');
    $this->assertEquals($cryptr->getCryptrBaseUrl(), 'https://some.base.url');
  }
}