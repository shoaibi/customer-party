<?php
declare(strict_types=1);

namespace Intercom;
use Intercom\Exceptions\NoCustomersToInviteException;

class CustomerInvitation
{
    protected $customers = [];

    protected $invitedCustomers = [];

    protected $officeLatitude;

    protected $officeLongitude;

    public function __construct(string $customerJsonFilePath = '../data/customers.json',
                                float $officeLatitude = 53.3381985,
                                float $officeLongitude = -6.2592576)
    {
        $this->customers = $this->findCustomersFromJson($customerJsonFilePath);
        $this->officeLatitude = $officeLatitude;
        $this->officeLongitude = $officeLongitude;
    }

    public function invite(int $distanceInKm = 100)
    {
        $this->invitedCustomers = $this->findCustomersToInvite($this->customers, $distanceInKm);
        ksort($this->invitedCustomers);
    }

    protected function findCustomersFromJson(string $customerJsonFilePath): array
    {
        return (new CustomerJsonParser())->parseCustomersFromFile($customerJsonFilePath);
    }

    protected function findCustomersToInvite(array $customers, int $distanceInKm): array
    {
        $customersToInvite = [];
        foreach ($customers as $customer)
        {
            if ($this->shouldCustomerBeInvited($customer, $distanceInKm))
            {
                $customersToInvite[$customer['user_id']] = $customer['name'];
            }
        }

        if ($this->hasAtLeastOneCustomerToInvite($customersToInvite))
        {
            return $customersToInvite;
        }
    }

    protected function shouldCustomerBeInvited(array $customer, int $distanceInKm): bool
    {
        $distance = (new DistanceCalculator())->calculate($this->officeLatitude,
                                                            $this->officeLongitude,
                                                            floatval($customer['latitude']),
                                                            floatval($customer['longitude']));
        return ($distance <= $distanceInKm);
    }

    protected function hasAtLeastOneCustomerToInvite(array $customersToInvite): bool
    {
        if (!$customersToInvite)
        {
            throw new NoCustomersToInviteException;
        }
        return true;
    }

    public function __toString(): string
    {
        if (empty($this->invitedCustomers))
        {
            return 'No customers to be invited';
        }
        return $this->compileOutputForCustomers();
    }

    protected function compileOutputForCustomers(): string
    {
        $output = '';
        foreach ($this->invitedCustomers as $customerId => $invitedCustomerName)
        {
            $output .= "#${customerId} - ${invitedCustomerName}" . PHP_EOL;
        }
        return rtrim($output);
    }

    public function  getCustomers(): array
    {
        return $this->customers;
    }

    public function getInvitedCustomers(): array
    {
        return $this->invitedCustomers;
    }

    public function getOfficeLatitude(): float
    {
        return $this->officeLatitude;
    }

    public function getOfficeLongitude(): float
    {
        return $this->officeLongitude;
    }

}