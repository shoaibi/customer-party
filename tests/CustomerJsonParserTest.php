<?php
namespace Intercom\Tests;
use Intercom\CustomerJsonParser;

class CustomerJsonParserTest extends BaseTest
{
    // could probably use vfs to test the files related stuff below:

    /**
     * @expectedException     Intercom\Exceptions\FileNotFoundException
     */
    public function testParseCustomersFromInexistingFile()
    {
        (new CustomerJsonParser())->parseCustomersFromFile('file_does_not_exist');
    }

    /**
     * @expectedException     Intercom\Exceptions\FileNotReadableException
     */
    public function testParseCustomersFromUnreadableFile()
    {
        $tempFileName = tempnam(sys_get_temp_dir(), "unreadable");
        umask(000);
        chmod($tempFileName, 0300);
        (new CustomerJsonParser())->parseCustomersFromFile($tempFileName);
        unlink($tempFileName);
    }

    /**
     * @expectedException     Intercom\Exceptions\EmptyFileException
     */
    public function testParseCustomersFromEmptyFile()
    {
        $tempFileName = tempnam(sys_get_temp_dir(), "empty");
        (new CustomerJsonParser())->parseCustomersFromFile($tempFileName);
        unlink($tempFileName);
    }

    /**
     * @expectedException     Intercom\Exceptions\InvalidJsonStringException
     */
    public function testParseCustomerFromStringWithEmptyString()
    {
        (new CustomerJsonParser())->parseCustomerFromString('');
    }

    /**
     * @expectedException     Intercom\Exceptions\InvalidJsonStringException
     */
    public function testParseCustomerFromStringWithNonJsonString()
    {
        (new CustomerJsonParser())->parseCustomerFromString('randomString');
    }

    /**
     * @expectedException     Intercom\Exceptions\InvalidCustomersJsonException
     */
    public function testParseCustomerFromStringWithJsonButNotCustomerString()
    {
        (new CustomerJsonParser())->parseCustomerFromString(json_encode(['user' => 'john']));
    }

    public function testParseCustomerFromStringWithValidCustomerJsonString()
    {
        $customer = ['user_id' => 1,
            'name' => 'John Doe',
            'latitude' => 10.10,
            'longitude' => 20.20,
            ];
        $decodedCustomer = (new CustomerJsonParser())->parseCustomerFromString(json_encode($customer));
        $this->assertEquals($decodedCustomer, $customer);
    }

    /**
     * @depends testParseCustomerFromStringWithEmptyString
     * @expectedException     Intercom\Exceptions\EmptyCustomersArrayException
     */
    public function testParseCustomersFromArrayWithEmptyArray()
    {
        (new CustomerJsonParser())->parseCustomersFromArray([]);
    }

    /**
     * @depends testParseCustomerFromStringWithNonJsonString
     * @expectedException     Intercom\Exceptions\InvalidJsonStringException
     */
    public function testParseCustomersFromArrayWithNonJsonStringArray()
    {
        (new CustomerJsonParser())->parseCustomersFromArray(['key' => 'value']);
    }

    /**
     * @depends testParseCustomerFromStringWithJsonButNotCustomerString
     * @expectedException     Intercom\Exceptions\InvalidCustomersJsonException
     */
    public function testParseCustomersFromArrayWithJsonButNotCustomerStringArray()
    {
        (new CustomerJsonParser())->parseCustomersFromArray([json_encode(['user' => 'john'])]);
    }

    public function testParseCustomersFromArrayWithValidCustomerJsonStringArray()
    {
        $customers = [
            ['user_id' => 1,
            'name' => 'John Doe',
            'latitude' => 10.10,
            'longitude' => 20.20,
            ],
            ['user_id' => 2,
                'name' => 'Jane Doe',
                'latitude' => 30.30,
                'longitude' => 40.40,
            ],
            ];
        $jsonStringArray = [json_encode($customers[0]),  json_encode($customers[1])];
        $decodedCustomers = (new CustomerJsonParser())->parseCustomersFromArray($jsonStringArray);
        $this->assertEquals($customers, $decodedCustomers);
    }
}