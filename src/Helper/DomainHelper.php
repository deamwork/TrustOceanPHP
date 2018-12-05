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
use Pdp\Domain;

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

        // is IPv4, not supporting IPv6
        if (filter_var($domain, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
            return true;
        }

        $domain = new Domain($domain);
        if (!$domain->isResolvable() || !$domain->isKnown() || !$domain->isICANN() || $domain->isPrivate()) {
            throw new ValidationException('Domain name or IP address is not valid.');
        }

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
