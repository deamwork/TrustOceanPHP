TrustOceanPHP
=========

Composer package implementation for [TrustOcean](https://www.trustocean.com/) API.

Raw document provided by TrustOcean can be found in [here](https://api.trustocean.com/SSL-Certificate-API/), as well as their [product definition](https://api.trustocean.com/).

Product list will update once TrustOcean updated it first.

Feel free to fork and open pull request to this package. [Contributes](#contributing) are welcome.

## Install

```bash
composer require deamwork/trustoceanphp
```

## Usage

Make a json config file.
```bash
vi config.json
```

### Config example
`level` can be `partner` or `developer`, will uses different API base uri and product definitions.

You can always set your own base API uri, fill it to `api_base`.

Don't forget to fill your TrustOcean username and password. It won't store and it only send to TrustOcean API. Details can be found in [security](#Security) section.
```json
{
  "account": {
    "level": "developer",
    "username": "",
    "password": ""
  },
  "trustocean": {
    "api_base": null
  }
}
```

then, reference `use TrustOcean\Core\Actions as TOAPI` in your workflow.

## Flow

### Create new order
```php
$flow = new TOAPI('/path/to/your/config.json');
$order = $flow->createOrder('TrustOcean Encryption365 SSL', 'quarterly', 3);
$order_id = $order['order_id'];
```

### Add more SANs
```php
$flow->addSANs($order_id, 3);
```

### Upload CSR
```php
$flow->uploadCSR($order_id, "CSR TEXT GOES HERE");
```

### Set domain(s)
```php
$flow->addDomains($order_id, [
    'a.domain.tld', 'b.domain.tld', 'c.domain.tld',
    'd.domain.tld', 'e.domain.tld', 'f.domain.tld',
]);
```

### Remove domain(s)
```php
$flow->removeDomain($order_id, [
    'd.domain.tld', 'e.domain.tld', 'f.domain.tld',
]);
```

### Get order detail(s)
```php
$flow->getOrderDetails($order_id);
```

### Change verification method
```php
$flow->changeDCVMethod($order_id, [
    'a.domain.tld' => 'admin@domain.tld',
    'b.domain.tld' => 'CSR_CNAME_HASH',
    'c.domain.tld' => 'HTTP_CSR_HASH',
]);
```

### Call CA to verify your domain
```php
$flow->reDoDCVCheck($order_id);
$flow->resendDCVEmail($order_id);
```
... or ...
```php
$flow->resendDCVEmailReDoDCVCheck($order_id);
```

### After verification, get your certs

```php
$cert = $flow->getCertDetails($order_id);
print_r($cert['cert_code'], true); // get your certificate
print_r($cert['ca_code'], true);   // get CA certificate
```

### Wants reissue?

```php
$flow->setCertReissue($order_id);
```
Then redo the flow.

## Testing

right in the todo list.

## Contributing

See [CONTRIBUTING](CONTRIBUTING.md) for more information.

## Security

This library only talks to the TrustOcean API, it will not store nor send your any information to any third party. 

**In fact? This library won't store anything**

If you discover any security related issues, feel free to open a new issue and I will mark it as "Security" ASAP.

## License

This project is open-sourced under MIT license.

This demand is [required by TrustOcean](https://www.v2ex.com/t/512510), which to be honest, I prefer to choose BSD-3...