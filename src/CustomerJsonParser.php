<?php
declare(strict_types=1);

namespace Intercom;
use Intercom\Exceptions\{
    EmptyFileException,
    FileNotFoundException,
    FileNotReadableException,
    InvalidCustomersJsonException,
    InvalidJsonStringException,
    EmptyCustomersArrayException};

class CustomerJsonParser
{
    public function parseCustomersFromFile(string $customersJsonFilePath): array
    {
        if ($this->validateFile($customersJsonFilePath))
        {
            $customers = file($customersJsonFilePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            return $this->parseCustomersFromArray($customers);
        }
        // no need for a default case here as an exception will be thrown by the if
        // condition anyways
    }

    public function parseCustomersFromArray(array $customersJsonArray): array
    {
        if ($this->validateArrayHasCustomers($customersJsonArray)) {
            $customers = [];
            foreach ($customersJsonArray as $customerJsonString) {
                $customers[] = $this->parseCustomerFromString($customerJsonString);
            }
            return $customers;
        }
    }

    public function parseCustomerFromString(string $customerJsonString): array
    {
        $customer = json_decode($customerJsonString, true);
        if ($this->isValidJson() && $this->hasValidCustomer($customer))
        {
            return $customer;
        }
        // no need for a default case here as an exception will be thrown by the if
        // condition anyways
    }

    protected function validateFile(string $customersJsonFilePath): bool
    {
        return ($this->validateFileExists($customersJsonFilePath) &&
                $this->validateFileIsReadable($customersJsonFilePath) &&
                $this->validateFileIsNotEmpty($customersJsonFilePath));
    }

    protected function validateFileExists(string $customersJsonFilePath): bool
    {
        if (!file_exists($customersJsonFilePath))
        {
            throw new FileNotFoundException("$customersJsonFilePath does not exist");
        }
        return true;
    }

    protected function validateFileIsReadable(string $customersJsonFilePath): bool
    {
        if (!is_readable($customersJsonFilePath))
        {
            throw new FileNotReadableException("$customersJsonFilePath is not readable");
        }
        return true;
    }

    protected function validateFileIsNotEmpty(string $customersJsonFilePath): bool
    {
        if (filesize($customersJsonFilePath) == 0)
        {
            throw new EmptyFileException("$customersJsonFilePath is empty");
        }
        return true;
    }

    protected function isValidJson(): bool
    {
        if (json_last_error() != JSON_ERROR_NONE)
        {
            throw new InvalidJsonStringException;
        }
        return true;
    }

    protected function hasValidCustomer(array $customer): bool
    {
        if (!(count($customer) == 4) ||
            !isset($customer['latitude'], $customer['longitude'],
                    $customer['name'], $customer['user_id']))
        {
            throw new InvalidCustomersJsonException();
        }
        return true;
    }

    protected function validateArrayHasCustomers(array $customersJsonArray): bool
    {
        // don't care about coercion. It could be both, false or an empty array.
        if (!$customersJsonArray)
        {
            throw new EmptyCustomersArrayException();
        }
        return true;
    }
}