<?php
namespace Intercom\Tests;
use Intercom\CustomerInvitation;
use Intercom\Exceptions\NoCustomersToInviteException;

class CustomerInvitationTest extends BaseTest
{
    protected static $pathToCustomersJsonFile;

    // type hinting to allow code autocompletion
    /**
     * @var CustomerInvitation
     */
    protected $invitationObject;

    public static function setupBeforeClass()
    {
        static::$pathToCustomersJsonFile = tempnam(sys_get_temp_dir(), 'customers.json.');
        if (!copy(__DIR__ . '/../data/customers.json', static::$pathToCustomersJsonFile))
        {
            throw new \RuntimeException("Unable to copy data/customers.json to " . static::$pathToCustomersJsonFile);
        }
        parent::setupBeforeclass();
    }

    public function setup()
    {
        $this->invitationObject = new CustomerInvitation(__DIR__ . '/../data/customers.json');//static::$pathToCustomersJsonFile, 53.3381985, -6.2592576);
        parent::setup();
    }

    // not testing cases of:
    // - file doesn't exist
    // - file is not readable
    // - file is empty
    // - file has invalid json string
    // - file has invalid customers data
    // because those aren't this classes responsibility


    public function testConstructSetsPropertiesCorrectly()
    {
        $this->assertEquals(53.3381985, $this->invitationObject->getOfficeLatitude());
        $this->assertEquals(-6.2592576, $this->invitationObject->getOfficeLongitude());
        $this->assertEmpty($this->invitationObject->getInvitedCustomers());
        $this->assertCount(32, $this->invitationObject->getCustomers());
    }

    /**
     * @depends testConstructSetsPropertiesCorrectly
     */
    public function testInviteFindsCorrectCustomers()
    {
        $this->invitationObject->invite(50);
        $expectedCustomersToBeInvited = [
            4 => 'Ian Kehoe',
            5 => 'Nora Dempsey',
            6 => 'Theresa Enright',
            11 => 'Richard Finnegan',
            12 => 'Christina McArdle',
            15 => 'Michael Ahearn',
            31 => 'Alan Behan',
            39 => 'Lisa Ahearn',
        ];
        $this->assertEquals($expectedCustomersToBeInvited, $this->invitationObject->getInvitedCustomers());
    }

    /**
     * @depends testInviteFindsCorrectCustomers
     */
    public function testInviteFindsCorrectCustomersWithVaryingDistance()
    {
        $this->invitationObject->invite(150);
        $expectedCustomersToBeInvited = [
            4 => 'Ian Kehoe',
            5 => 'Nora Dempsey',
            6 => 'Theresa Enright',
            8 => 'Eoin Ahearn',
            9 => 'Jack Dempsey',
            10 => 'Georgina Gallagher',
            11 => 'Richard Finnegan',
            12 => 'Christina McArdle',
            13 => 'Olive Ahearn',
            15 => 'Michael Ahearn',
            17 => 'Patricia Cahill',
            23 => 'Eoin Gallagher',
            24 => 'Rose Enright',
            26 => 'Stephen McArdle',
            28 => 'Charlie Halligan',
            29 => 'Oliver Ahearn',
            30 => 'Nick Enright',
            31 => 'Alan Behan',
            39 => 'Lisa Ahearn',
        ];
        $this->assertEquals($expectedCustomersToBeInvited, $this->invitationObject->getInvitedCustomers());
    }

    /**
     * @depends testInviteFindsCorrectCustomers
    * @expectedException     Intercom\Exceptions\NoCustomersToInviteException
    */
    public function testInviteThrowExceptionIfNoInvitableCustomersAreFound()
    {
        $this->invitationObject->invite(5);
    }


    /**
     * @depends testInviteFindsCorrectCustomersWithVaryingDistance
     */
    public function testToStringPrintsProperRepresentation()
    {
        $this->invitationObject->invite(50);
        $expectedOutput = <<<CSTM
#4 - Ian Kehoe
#5 - Nora Dempsey
#6 - Theresa Enright
#11 - Richard Finnegan
#12 - Christina McArdle
#15 - Michael Ahearn
#31 - Alan Behan
#39 - Lisa Ahearn
CSTM;
        $this->assertEquals($expectedOutput, strval($this->invitationObject));
    }

    /**
     * @depends  testToStringPrintsProperRepresentation
     */
    public function testToStringPrintsProperRepresentationForNoCustomersFound()
    {
        try {
            $this->invitationObject->invite(10);
            $this->fail("We shouldn't be able to find any customers in 10km radius");
        } catch (NoCustomersToInviteException $e)
        {
        }
        $expectedOutput = "No customers to be invited";
        $this->assertEquals($expectedOutput, strval($this->invitationObject));
    }

    public function tearDown()
    {
        unset($this->invitationObject);
        parent::tearDown();
    }

    public static function tearDownAfterClass()
    {
        unlink(static::$pathToCustomersJsonFile);
        parent::tearDownAfterClass();
    }
}