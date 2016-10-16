<?php

use Directus\Util\ArrayUtils;

class ArrayUtilsTest extends PHPUnit_Framework_TestCase
{
    public function testGetItem()
    {
        $item = ['name' => 'Jim', 'country' => ['name' => 'Germany', 'population' => '9']];
        $this->assertEquals(ArrayUtils::get($item, 'name'), 'Jim');
        $this->assertEquals(ArrayUtils::get($item, 'age', 18), 18);
        $this->assertSame('Germany', ArrayUtils::get($item, 'country.name'));
        $this->assertSame('German', ArrayUtils::get($item, 'country.language', 'German'));
    }

    public function testPickItems()
    {
        $items = ['name' => 'Jim', 'age' => 79, 'sex' => 'M', 'country' => 'N/A'];
        $this->assertEquals(count(ArrayUtils::pick($items, ['name', 'age'])), 2);
        $this->assertEquals(count(ArrayUtils::pick($items, ['name', 'age', 'city'])), 2);
        $this->assertEquals(ArrayUtils::pick($items, 'name'), ['name' => 'Jim']);

        $this->assertEquals(count(ArrayUtils::filterByKey($items, ['name', 'age'])), 2);
        $this->assertEquals(count(ArrayUtils::filterByKey($items, ['name', 'age', 'city'])), 2);
        $this->assertEquals(ArrayUtils::filterByKey($items, 'name'), ['name' => 'Jim']);

        $this->assertEquals(count(ArrayUtils::filterByKey($items, ['name', 'age'], false)), 2);
        $this->assertEquals(count(ArrayUtils::filterByKey($items, ['name', 'age', 'city'], false)), 2);
        $this->assertEquals(ArrayUtils::filterByKey($items, 'name'), ['name' => 'Jim'], false);
    }

    public function testOmitItems()
    {
        $items = ['name' => 'Jim', 'age' => 79, 'sex' => 'M', 'country' => 'N/A'];
        $this->assertEquals(count(ArrayUtils::omit($items, ['country'])), 3);
        $this->assertEquals(count(ArrayUtils::omit($items, ['country', 'city'])), 3);
        $this->assertEquals(ArrayUtils::omit($items, 'name'), ['age' => 79, 'sex' => 'M', 'country' => 'N/A']);

        $this->assertEquals(count(ArrayUtils::filterByKey($items, ['country'], true)), 3);
        $this->assertEquals(count(ArrayUtils::filterByKey($items, ['name', 'age', 'city'], true)), 2);
        $this->assertEquals(ArrayUtils::filterByKey($items, 'name', true), ['age' => 79, 'sex' => 'M', 'country' => 'N/A']);
    }

    public function testContainsItems()
    {
        $items = ['name' => 'Jim', 'age' => 79, 'sex' => 'M', 'country' => 'N/A'];
        $this->assertTrue(ArrayUtils::contains($items, ['name', 'age']));
        $this->assertFalse(ArrayUtils::contains($items, ['name', 'age', 'city']));
        $this->assertTrue(ArrayUtils::contains($items, 'name', 'age'));
    }

    public function testFlatKeys()
    {
        $array = [
            'user' => [
                'name' => 'John',
                'country' => [
                    'name' => 'yes'
                ],
                'email' => []
            ]
        ];

        $result = ArrayUtils::dot($array);

        $this->assertInternalType('array', $result);
        $this->assertArrayHasKey('user.name', $result);
        $this->assertArrayHasKey('user.country.name', $result);
        $this->assertArrayHasKey('user.email', $result);
        $this->assertNotInternalType('array', $result['user.email']);

        $result = ArrayUtils::flatKey('_', $array);

        $this->assertInternalType('array', $result);
        $this->assertArrayHasKey('user_name', $result);
        $this->assertArrayHasKey('user_country_name', $result);
    }

    public function testDotKeys()
    {
        $array = [
            'user' => [
                'name' => 'John',
                'country' => [
                    'name' => 'yes'
                ],
                'email' => []
            ]
        ];

        $this->assertTrue(ArrayUtils::has($array, 'user.email'));
        $this->assertFalse(ArrayUtils::has($array, 'user.country.language'));
        $this->assertSame('yes', ArrayUtils::get($array, 'user.country.name'));
        $this->assertSame('John', ArrayUtils::get($array, 'user.name'));
        $this->assertNull(ArrayUtils::get($array, 'user.language'));
        $this->assertSame('English', ArrayUtils::get($array, 'user.language', 'English'));
    }

    public function testMissing()
    {
        $array1 = ['one', 'two', 'three', 'five'];
        $array2 = ['one', 'four', 'five'];
        $result = ArrayUtils::missing($array1, $array2);

        $this->assertTrue(in_array('four', $result));
    }

    public function testDefaults()
    {
        $array1 = [
            'database' => [
                'hostname' => 'localhost',
                'username' => 'root',
                'driver' => 'mysql'
            ],
            'status_name' => 'active',
        ];

        $array2 = [
            'database' => [
                'hostname' => 'localhost',
                'username' => 'root',
                'password' => 'root',
                'database' => 'directus'
            ]
        ];

        $newArray = ArrayUtils::defaults($array1, $array2);
        $this->assertTrue(ArrayUtils::has($newArray, 'database.database'));
        $this->assertSame('mysql', ArrayUtils::get($newArray, 'database.driver'));

        $newNewArray = ArrayUtils::defaults($newArray, [
            'database' => [
                'hostname' => '127.0.0.1'
            ]
        ]);
        $this->assertSame('127.0.0.1', ArrayUtils::get($newNewArray, 'database.hostname'));
    }
}
