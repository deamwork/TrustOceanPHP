<?php
/**
 * trustocean
 * Component:
 * File: DomainHelper.php
 * Author: jason
 * Time: 11/29/18 6:27 AM
 */

namespace TrustOcean\Helper;

use TrustOcean\Exception\ValidationException;

class DomainHelper extends Helper
{
    protected $pair = [];

    public function __construct($group)
    {
        foreach ($group as $item) {
            $this->pair['domain'][] = $item->domain;
            $this->pair['domain'][$item->domain] = $item->validDcvEmails;
        }
    }

    /*
     * domainChecker
     * Check domain is valid or not.
     *
     * @param   array|string    $domain
     */
    public static function domainChecker($domain)
    {
        if (gettype($domain) === 'array') {
            foreach ($domain as $item) {
                self::domainChecker($item);
                return true;
            }
        }

        if (!preg_match("/^([a-z\d](-*[a-z\d])*)(\.([a-z\d](-*[a-z\d])*))*$/i", $domain)) {
            // Valid chars check
            throw new ValidationException('Domain name is not valid. Abnormal character detected.');
        }

        if (!preg_match("/^.{1,253}$/", $domain)) {
            // Overall length check
            throw new ValidationException('Domain name is not valid. Domain is too long (more than 253 chars).');
        }

        if (!preg_match("/^[^\.]{1,63}(\.[^\.]{1,63})*$/", $domain)) {
            // Length of each label
            throw new ValidationException('Domain name is not valid. One or more label of domain is too long (more than 63 chars).');
        }

        return true;
    }

    public function domainInDCV(string $domain)
    {
        if (array_key_exists($domain, $this->pair['domain'])) {
            return true;
        } else {
            return false;
        }
    }

    public function emailInDCV(string $email, string $domain)
    {
        $this->emailChecker($email);
        if (in_array($email, $this->pair['domain'][$domain])) {
            return true;
        } else {
            return false;
        }
    }

    private function emailChecker(string $email)
    {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return true;
        } else {
            throw new ValidationException("\"$email\" Email is not valid.");
        }
    }
}
