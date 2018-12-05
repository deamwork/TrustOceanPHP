<?php
/**
 * trustocean
 * Component:
 * File: Actions.php
 * Author: jason
 * Time: 11/28/18 11:26 PM
 */

namespace TrustOcean\Core;

use GuzzleHttp\Client as HttpClient;
use Noodlehaus\Config;
use TrustOcean\Exception\ProductException;
use TrustOcean\Exception\ResponseException;
use \stdClass as JSONObject;
use TrustOcean\Exception\ValidationException;
use TrustOcean\Helper\DomainHelper;
use TrustOcean\Helper\OpenSSLHelper;

class Actions
{
    private static $httpClient;
    private $config;
    private static $headers;

    public function __construct(string $config_file)
    {
        // get config
        $this->config = Config::load($config_file);

        if ($this->config->get('account.level') == 'partner') {
            $base_uri = 'https://www.trustocean.com/partner/api/ssl.php';
        } elseif ($this->config->get('account.level') == 'developer') {
            $base_uri = 'https://api.trustocean.com/ssl/v1';
        } elseif (!is_null($this->config->get('trustocean.api_base'))) {
            $base_uri = $this->config->get('trustocean.api_base');
        } else {
            throw new ValidationException('Unknown account level');
        }

        self::$httpClient = new HttpClient([
            'base_uri' => $base_uri
        ]);
    }

    /*
     * responseHandler
     * Handle response from Trust Ocean API server
     *
     * @param   JSONObject  $http_response
     * @throw   ResponseException
     * @return  JSONObject
     */
    protected function responseHandler(JSONObject $http_response)
    {
        if ($http_response->status == 'error') {
            throw new ResponseException($http_response->message, $http_response->code);
        } else {
            return $http_response;
        }
    }

    /*
     * sendRequest
     * Send request to Trust Ocean API server
     *
     * @param   string  $action
     * @throws  ResponseException
     * @return  array   $params
     */
    protected function sendRequest(string $action, array $params)
    {
        $base_params = [
            'action'   => $action,
            'username' => $this->config->get('account.username'),
            'password' => $this->config->get('account.password'),
        ];
        $response = self::$httpClient->post(null, [
            'http_errors' => true,
            'headers'     => self::$headers,
            'form_params' => array_merge($base_params, $params),
        ]);

        try {
            return $this->responseHandler(json_decode($response->getBody()->getContents()));
        } catch (ResponseException $e) {
            throw new ResponseException($e->getMessage(), $e->getCode(), $e);
        }
        // return json_decode($response->getBody()->getContents());
    }

    /*
     * createOrder
     * Implement of placeNewOrder
     *
     * @param   string  $product_name
     * @param   string  $period
     * @param   int     $domain_count
     * @throws  ProductException
     * @throws  ResponseException
     * @return  array
     */
    public function createOrder(string $product_name, string $period, int $domain_count)
    {
        try {
            $product = ProductDefinitions::handler($product_name, $this->config->get('account.level'));
        } catch (ProductException $e) {
            throw new ProductException($e->getMessage(), $e->getCode(), $e);
        }

        $params = [
            'pid'    => $product['id'],
            'period' => $product['period'][$period],
        ];

        if (array_key_exists('san', $product['coverage'])) {
            $params['domain_count'] = $domain_count;
        }

        try {
            $response = $this->sendRequest('createOrder', $params);
        } catch (ResponseException $e) {
            throw new ResponseException($e->getMessage(), $e->getCode(), $e);
        }

        return [
            'cert_status' => $response->cert_status,
            'created_at'  => $response->created_at,
            'order_id'    => $response->trustocean_id,
            'invoice_id'  => $response->invoice_id,
            'unique_id'   => $response->unique_id,
        ];
    }

    /*
     * uploadCSR
     * Implement of uploadCSR
     * You can manually check CSR by calling OpenSSLHelper::checkCSR()
     *
     * @param   int     $order_id
     * @param   string  $csr
     * @throws  ValidationException
     * @throws  ResponseException
     * @return  array
     */
    public function uploadCSR(int $order_id, string $csr)
    {
        try {
            OpenSSLHelper::checkCSR($csr);
        } catch (ValidationException $e) {
            throw new ValidationException($e->getMessage());
        }

        $params = [
            'trustocean_id' => $order_id,
            'csr_code'      => $csr,
        ];

        try {
            $response = $this->sendRequest('uploadCSR', $params);
        } catch (ResponseException $e) {
            throw new ResponseException($e->getMessage(), $e->getCode(), $e);
        }

        return [
            'cert_status'    => $response->cert_status,
            'order_id'       => $response->trustocean_id,
            'vendor_id'      => $response->vendor_id ?? null,
            'certificate_id' => $response->certificate_id ?? null,
        ];
    }

    /*
     * addDomains
     * Implement of uploadDomains
     *
     * @param   int     $order_id
     * @param   array   $domains
     * @throws  ValidationException
     * @throws  ResponseException
     * @return  array
     */
    public function addDomains(int $order_id, array $domains)
    {
        try {
            DomainHelper::domainChecker($domains);
        } catch (ValidationException $e) {
            throw new ValidationException($e->getMessage(), $e->getCode(), $e);
        }

        $params = [
            'trustocean_id' => $order_id,
            'domains'       => implode(',', $domains),
        ];

        try {
            $response = $this->sendRequest('uploadDomains', $params);
        } catch (ResponseException $e) {
            throw new ResponseException($e->getMessage(), $e->getCode(), $e);
        }

        return [
            'cert_status'    => $response->cert_status,
            'order_id'       => $response->trustocean_id,
            'vendor_id'      => $response->vendor_id ?? null,
            'certificate_id' => $response->certificate_id ?? null,
        ];
    }

    /*
     * getOrderDetails
     * Implement of syncGetOrderDetails
     *
     * @param   int     $order_id
     * @return  array
     */
    public function getOrderDetails(int $order_id)
    {
        $params = [
            'trustocean_id' => $order_id,
        ];

        try {
            $response = $this->sendRequest('syncGetOrderDetails', $params);
        } catch (ResponseException $e) {
            throw new ResponseException($e->getMessage(), $e->getCode(), $e);
        }

        return [
            'vendor_id'                      => $response->vendor_id ?? null,
            'certificate_id'                 => $response->certificate_id ?? null,
            'order_id'                       => $response->trustocean_id,
            'domains'                        => $response->domains,
            'unique_id'                      => $response->unique_id,
            'csr_hash'                       => $response->csr_hash,
            'dcv_info'                       => $response->dcv_info,
            'csr_status'                     => ($response->csr_status == '1') ? true : false,
            'dcv_status_code'                => ($response->dcv_status_code == '1') ? true : false,
            'ov_callback_status'             => ($response->ov_callback_status == '1') ? true : false,
            'free_dv_up_status'              => ($response->free_dv_up_status == '1') ? true : false,
            'organization_validation_status' => ($response->organization_validation_status == '1') ? true : false,
            'ev_click_through_status'        => ($response->ev_click_through_status == '1') ? true : false,
            'ev_legal_existence'             => $response->ev_legal_existence ?? null,
            'ev_assumed_name'                => $response->ev_assumed_name ?? null,
            'ev_physical_existence'          => $response->ev_physical_existence ?? null,
            'ev_operational_existence'       => $response->ev_operational_existence ?? null,
            'ev_signer_approver_requester'   => $response->ev_signer_approver_requester ?? null,
            'ev_signer_second_approval'      => $response->ev_signer_second_approval ?? null,
            'suggested_org_details'          => $response->suggested_org_details ?? null,
        ];
    }

    /*
     * changeDCVMethod
     * Implement of syncChangeDCVMethod
     *
     * @param   int     $order_id
     * @return  array
     */
    public function changeDCVMethod(int $order_id, array $domain_method)
    {
        try {
            $order_details = $this->getOrderDetails($order_id);
        } catch (ResponseException $e) {
            throw new ResponseException($e->getMessage(), $e->getCode(), $e);
        }

        $domain_helper = new DomainHelper($order_details['dcv_info']);

        $result = [];
        foreach ($domain_method as $domain => $method) {
            if ($domain_helper->domainInDCV($domain)) {
                if (!in_array($method, ['CSR_CNAME_HASH', 'HTTP_CSR_HASH', 'HTTPS_CSR_HASH'])) {
                    if (!$domain_helper->emailInDCV($method, $domain)) {
                        $result[$domain] = 'Failed, Email not in DCV or not in paired domain';
                        continue;
                    }
                }

                $params = [
                    'trustocean_id' => $order_id,
                    'method'        => $method,
                    'domain'        => $domain,
                ];

                try {
                    $this->sendRequest('syncChangeDCVMethod', $params);
                } catch (ResponseException $e) {
                    $result[$domain] = "Failed, TrustOcean API server says: {$e->getMessage()}";
                }
                $result[$domain] = $method;
                continue;
            } else {
                $result[$domain] = 'Failed, Domain not in DCV';
                continue;
            }
        }

        return $result;
    }

    /*
     * reDoDCVCheck
     * Implement of syncResendDCVEmailOrReDoDCVCheck
     *
     * @param   int     $order_id
     * @throws  ResponseException
     * @return  boolean
     */
    public function reDoDCVCheck(int $order_id)
    {
        try {
            return $this->resendDCVEmailReDoDCVCheck($order_id);
        } catch (ResponseException $e) {
            throw new ResponseException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /*
     * resendDCVEmail
     * Implement of syncResendDCVEmailOrReDoDCVCheck
     *
     * @param   int     $order_id
     * @throws  ResponseException
     * @return  boolean
     */
    public function resendDCVEmail(int $order_id)
    {
        try {
            return $this->resendDCVEmailReDoDCVCheck($order_id);
        } catch (ResponseException $e) {
            throw new ResponseException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /*
     * syncResendDCVEmailOrReDoDCVCheck
     * Implement of syncResendDCVEmailOrReDoDCVCheck
     *
     * @param   int     $order_id
     * @throws  ResponseException
     * @return  boolean
     */
    public function resendDCVEmailReDoDCVCheck(int $order_id)
    {
        $params = [
            'trustocean_id' => $order_id,
        ];

        try {
            $this->sendRequest('syncResendDCVEmailReDoDCVCheck', $params);
        } catch (ResponseException $e) {
            throw new ResponseException($e->getMessage(), $e->getCode(), $e);
        }

        return true;
    }

    /*
     * getCertDetails
     * Implement of getCertificateDetails
     *
     * @param   int     $order_id
     * @throws  ResponseException
     * @return  array
     */
    public function getCertDetails(int $order_id)
    {
        $params = [
            'trustocean_id' => $order_id,
        ];

        try {
            $response = $this->sendRequest('syncResendDCVEmailReDoDCVCheck', $params);
        } catch (ResponseException $e) {
            throw new ResponseException($e->getMessage(), $e->getCode(), $e);
        }

        return [
            'cert_status'    => $response->cert_status,
            'created_at'     => $response->created_at,
            'order_id'       => $response->trustocean_id,
            'domains'        => $response->domains,
            'csr_code'       => $response->csr_code ?? null,
            'cert_code'      => $response->cert_code ?? null,
            'ca_code'        => $response->ca_code ?? null,
            'contact_email'  => $response->contact_email ?? null,
            'org_info'       => $response->org_info ?? null,
            'vendor_id'      => $response->vendor_id ?? null,
            'certificate_id' => $response->certificate_id ?? null,
            'issued_at'      => $response->issued_at ?? null,
            'unique_id'      => $response->unique_id ?? null,
            'type'           => $response->type ?? null,
            'reissue'        => $response->reissue ? true : false,
            'renew'          => $response->renew ? true : false,
            'domain_count'   => $response->domain_count ?? null,
        ];
    }

    /*
     * addSANs
     * Implement of addSANOrder
     *
     * @param   int     $order_id
     * @param   int     $new_sans_count
     * @throws  ResponseException
     * @return  boolean
     */
    public function addSANs(int $order_id, int $new_sans_count)
    {
        $params = [
            'trustocean_id' => $order_id,
            'newsan'        => $new_sans_count,
        ];

        try {
            $this->sendRequest('addSANOrder', $params);
        } catch (ResponseException $e) {
            throw new ResponseException($e->getMessage(), $e->getCode(), $e);
        }

        return true;
    }

    /*
     * setCertReissue
     * Implement of setCertificateReissue
     *
     * @param   int     $order_id
     * @throws  ResponseException
     * @return
     */
    public function setCertReissue(int $order_id)
    {
        $params = [
            'trustocean_id' => $order_id,
        ];

        try {
            $response = $this->sendRequest('setCertificateReissue', $params);
        } catch (ResponseException $e) {
            throw new ResponseException($e->getMessage(), $e->getCode(), $e);
        }

        return [
            'unique_id' => $response->unique_id,
        ];
    }

    /*
     * syncRemoveDomain
     * Implement of syncRemoveDomain
     *
     * @param   int     $order_id
     * @param   array   $domains
     * @throws  ResponseException
     * @return
     */
    public function removeDomain(int $order_id, array $domains)
    {
        try {
            $order_details = $this->getOrderDetails($order_id);
        } catch (ResponseException $e) {
            throw new ResponseException($e->getMessage(), $e->getCode(), $e);
        }

        $domain_helper = new DomainHelper($order_details['dcv_info']);

        $result = [];
        foreach ($domains as $domain) {
            if ($domain_helper->domainInDCV($domain)) {
                $params = [
                    'trustocean_id' => $order_id,
                    'domain'        => $domain,
                ];

                try {
                    $this->sendRequest('syncRemoveDomain', $params);
                } catch (ResponseException $e) {
                    $result[$domain] = "Failed, TrustOcean API server says: {$e->getMessage()}";
                }
                $result[$domain] = 'REMOVED';
                continue;
            } else {
                $result[$domain] = 'Failed, Domain not in DCV';
                continue;
            }
        }

        return $result;
    }
}
