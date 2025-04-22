<?php

use PHPUnit\Framework\TestCase;
use support\exception\InputValueException;
use support\Translation;
use Wegar\Validate\Rule\ArrayRule;
use Wegar\Validate\Rule\BoolRule;
use Wegar\Validate\Rule\Date;
use Wegar\Validate\Rule\Email;
use Wegar\Validate\Rule\FloatRule;
use Wegar\Validate\Rule\Idcard;
use Wegar\Validate\Rule\In;
use Wegar\Validate\Rule\IntRule;
use Wegar\Validate\Rule\Ip;
use Wegar\Validate\Rule\Max;
use Wegar\Validate\Rule\Min;
use Wegar\Validate\Rule\Not;
use Wegar\Validate\Rule\ObjectRule;
use Wegar\Validate\Rule\Regex;
use Wegar\Validate\Rule\Required;
use Wegar\Validate\Rule\Url;

Translation::instance('', [
  'locale'          => 'zh_CN',
  'fallback_locale' => ['zh_CN', 'en'],
  'path'            => dirname(__DIR__) . '/src/Translation',
]);

class RulesTest extends TestCase
{
  function testArrayRule()
  {
    ArrayRule::validate('test', [1, 2, 3]);
    $this->expectException(InputValueException::class);
    ArrayRule::validate('test', 'string');
  }

  function testBoolRule()
  {
    BoolRule::validate('test', true);
    $this->expectException(InputValueException::class);
    BoolRule::validate('test', 'true');
  }

  function testDateRule()
  {
    Date::validate('test', date('Y-m-d H:i:s'));
    Date::validate('test', date('Y-m-d H:i'), 'Y-m-d H:i');
    $this->expectException(InputValueException::class);
    Date::validate('test', date('Y-m-d H:i'));
  }

  function testEmailRule()
  {
    Email::validate('test', 'test@test.com');
    $this->expectException(InputValueException::class);
    Email::validate('test', 'test');
  }

  function testFloatRule()
  {
    $validated_values = [
      ['1.0', 1],
      [1.1, 1],
      ['1.1']
    ];
    foreach ($validated_values as $validated_value) {
      FloatRule::validate('test', $validated_value[0], $validated_value[1] ?? null);
    }
    $this->assertNull(null);

    $invalidated_values = [
      [1.0, 1],
      [1, 1],
      [1.1, 2],
    ];
    foreach ($invalidated_values as $invalidated_value) {
      try {
        FloatRule::validate('test', $invalidated_value[0], $invalidated_value[1] ?? null);
        $this->fail('Failed to catch InputValueException');
      } catch (InputValueException $e) {
        $this->assertTrue(str_contains($e->getMessage(), isset($invalidated_value[1]) ? '小数点后' : '浮点数'));
      }
    }
  }

  function testIdcardRule()
  {
    Idcard::validate('test', '100000190001010002');
    $this->expectException(InputValueException::class);
    Idcard::validate('test', '100000190001010000');
  }

  function testInRule()
  {
    In::validate('test', 'test', 'test,test1,test2');
    In::validate('test', 'test1', 'test,test1,test2');
    In::validate('test', 'test2', 'test,test1,test2');
    In::validate('test', 1, '1,2,3');
    $this->expectException(InputValueException::class);
    In::validate('test', 'test3', 'test,test1,test2');
  }

  function testIntRule()
  {
    IntRule::validate('test', 1);
    IntRule::validate('test', "999999999999999999");
    $this->expectException(InputValueException::class);
    IntRule::validate('test', '1.1');
  }

  function testIpRule()
  {
    Ip::validate('test', '127.0.0.1');
    Ip::validate('test', '::1');
    $this->expectException(InputValueException::class);
    Ip::validate('test', 'test');
  }

  function testMaxRule()
  {
    Max::validate('test', 'test', 4);
    Max::validate('test', 'test', 5);
    $this->expectException(InputValueException::class);
    Max::validate('test', 'test', 3);
  }

  function testMinRule()
  {
    Min::validate('test', 'test', 4);
    Min::validate('test', 'test', 3);
    $this->expectException(InputValueException::class);
    Min::validate('test', 'test', 5);
  }

  function testNotRule()
  {
    Not::validate('test', 'test', 'test1,test2,test3');
    $this->expectException(InputValueException::class);
    Not::validate('test', 'test', 'test,test1,test2');
  }

  function testObjectRule()
  {
    ObjectRule::validate('test', ['foo' => 1]);
    // invalidated values
    $invalidated_values = [
      [1, 2, 3], // list
      'foo',      // string
    ];
    foreach ($invalidated_values as $invalidated_value) {
      try {
        ObjectRule::validate('test', $invalidated_value);
        $this->fail('Failed to catch InputValueException');
      } catch (InputValueException $e) {
        $this->assertTrue(str_contains($e->getMessage(), '对象'));
      }
    }
  }

  function testRegxRule()
  {
    Regex::validate('test', 'test', '/^test$/');
    Regex::validate('test', 'TEST', '/^(test|Foo)$/i');
    $this->expectException(InputValueException::class);
    Regex::validate('test', 'test1', '/^test$/');
  }

  function testRequiredRule()
  {
    Required::validate('test', 'test');
    Required::validate('test', 1);
    Required::validate('test', 0);
    $this->expectException(InputValueException::class);
    Required::validate('test', null);
  }

  function testUrlRule()
  {
    Url::validate('test', 'https://example.com');
    Url::validate('test', 'http://example.com');
    Url::validate('test', 'ftp://example.com');
    Url::validate('test', 'ssl://example.com');
    $this->expectException(InputValueException::class);
    Url::validate('test', 'test');
  }
}
