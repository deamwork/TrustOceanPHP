<?php
/**
 * trustocean
 * Component:
 * File: ProductDefinitions.php
 * Author: jason
 * Time: 11/29/18 12:03 AM
 */

namespace TrustOcean\Core;

use TrustOcean\Exception;

class ProductDefinitions
{
    const OPEN_PARTNER_DEVELOPER       = [
        'TrustOcean Encryption365 SSL' => [
            'id'       => 1,
            'type'     => 'DV',
            'coverage' => ['san' => ['IP', 'domain', 'Wildcard domain']],
            'period'   => ['quarterly']
        ],
    ];
    const AUTHORIZED_PARTNER_DEVELOPER = [
        'TrustOcean Encryption365 SSL'                   => [
            'id'       => 1,
            'type'     => 'DV',
            'coverage' => ['san' => ['IP', 'FQDN domain', 'Wildcard domain']],
            'period'   => ['quarterly']
        ],
        'TrustOcean DV Single Domain Secure SSL'         => [
            'id'       => 46,
            'type'     => 'DV',
            'coverage' => ['single' => ['FQDN domain']],
            'period'   => ['annually', 'biennially']
        ],
        'TrustOcean DV Multi Domain Secure SSL '         => [
            'id'       => 47,
            'type'     => 'DV',
            'coverage' => ['san' => ['FQDN domain']],
            'period'   => ['annually', 'biennially']
        ],
        'TrustOcean DV Wildcard Domain Secure SSL'       => [
            'id'       => 48,
            'type'     => 'DV',
            'coverage' => ['single' => ['Wildcard']],
            'period'   => ['annually', 'biennially']
        ],
        'TrustOcean DV Multi Wildcard Domain Secure SSL' => [
            'id'       => 49,
            'type'     => 'DV',
            'coverage' => ['san' => ['FQDN domain', 'Wildcard']],
            'period'   => ['annually', 'biennially']
        ],
        'TrustOcean DV Public IP Secure SSL'             => [
            'id'       => 50,
            'type'     => 'DV',
            'coverage' => ['san' => ['IP', 'FQDN domain']],
            'period'   => ['annually', 'biennially']
        ],
        'Comodo DV SSL Certificate'                      => [
            'id'       => 51,
            'type'     => 'DV',
            'coverage' => ['single' => ['FQDN domain']],
            'period'   => ['annually', 'biennially']
        ],
        'Comodo DV SSL Wildcard Certificate'             => [
            'id'       => 52,
            'type'     => 'DV',
            'coverage' => ['single' => ['Wildcard']],
            'period'   => ['annually', 'biennially']
        ],
        'Comodo DV SSL UCC Certificate'                  => [
            'id'       => 53,
            'type'     => 'DV',
            'coverage' => ['single' => ['FQDN domain']],
            'period'   => ['annually', 'biennially']
        ],
        'Comodo DV SSL UCC Wildcard Certificate'         => [
            'id'       => 54,
            'type'     => 'DV',
            'coverage' => ['san' => ['FQDN domain', 'Wildcard']],
            'period'   => ['annually', 'biennially']
        ],
        'Comodo DV Positive Single Domain SSL'           => [
            'id'       => 55,
            'type'     => 'DV',
            'coverage' => ['single' => ['FQDN domain']],
            'period'   => ['annually', 'biennially']
        ],
        'Comodo DV Positive Multi Domain SSL'            => [
            'id'       => 56,
            'type'     => 'DV',
            'coverage' => ['san' => ['FQDN domain']],
            'period'   => ['annually', 'biennially']
        ],
        'Comodo DV Positive Wildcard Domain SSL'         => [
            'id'       => 57,
            'type'     => 'DV',
            'coverage' => ['single' => ['Wildcard']],
            'period'   => ['annually', 'biennially']
        ],
        'Comodo DV Positive Multi Wildcard Domain SSL'   => [
            'id'       => 58,
            'type'     => 'DV',
            'coverage' => ['san' => ['FQDN domain', 'Wildcard']],
            'period'   => ['annually', 'biennially']
        ],
        'Comodo OV Instant SSL'                          => [
            'id'       => 59,
            'type'     => 'OV',
            'coverage' => ['single' => ['FQDN domain']],
            'period'   => ['annually', 'biennially']
        ],
        'Comodo OV UCC SSL'                              => [
            'id'       => 60,
            'type'     => 'OV',
            'coverage' => ['san' => ['FQDN domain']],
            'period'   => ['annually', 'biennially']
        ],
        'Comodo OV Premium Wildcard SSL'                 => [
            'id'       => 61,
            'type'     => 'OV',
            'coverage' => ['single' => ['Wildcard']],
            'period'   => ['annually', 'biennially']
        ],
        'Comodo OV UCC Wildcard SSL'                     => [
            'id'       => 62,
            'type'     => 'OV',
            'coverage' => ['san' => ['FQDN domain', 'Wildcard']],
            'period'   => ['annually', 'biennially']
        ],
        'Comodo EV SSL'                                  => [
            'id'       => 63,
            'type'     => 'EV',
            'coverage' => ['single' => ['FQDN domain']],
            'period'   => ['annually', 'biennially']
        ],
        'Comodo EV Multi Domain SSL'                     => [
            'id'       => 64,
            'type'     => 'EV',
            'coverage' => ['san' => ['FQDN domain']],
            'period'   => ['annually', 'biennially']
        ],
        'Comodo EV Positive Single Domain SSL'           => [
            'id'       => 65,
            'type'     => 'EV',
            'coverage' => ['single' => ['FQDN domain']],
            'period'   => ['annually', 'biennially']
        ],
        'Comodo EV Positive Multi Domain SSL'            => [
            'id'       => 66,
            'type'     => 'EV',
            'coverage' => ['san' => ['FQDN domain']],
            'period'   => ['annually', 'biennially']
        ],
    ];
    private static $product_list;

    public function __construct()
    {
        self::$product_list = [
            'opd' => self::OPEN_PARTNER_DEVELOPER,
            'apd' => self::AUTHORIZED_PARTNER_DEVELOPER,
        ];
    }

    public static function handler(string $product = null, string $type = 'apd')
    {
        if (is_null($product)) {
            return self::$product_list;
        } else {
            $target = self::$product_list[$type][$product] ?? null;
            if (is_null($target)) {
                throw new Exception\ProductException('Product is not found in definitions.');
            } else {
                return $target;
            }
        }
    }
}
