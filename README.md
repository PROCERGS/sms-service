PROCERGS SMS Service
====================

**This is for internal use only.** If you do not work at PROCERGS or
any of its software, this is most certainly not useful to you.

Installation
------------

**Important**: Currently this lib depends on
[RestClientBundle](https://github.com/CircleOfNice/CiRestClientBundle)
so you'll have to load this bundle onto Symfony. We will change to
Guzzle 6 HTTP client as soon as PROCERGS updates it's PHP servers.

Run the following command to add the composer dependency and install it:

    composer require procergs/sms-service

If you are using Symfony, enable RestClientBundle on your
`AppKernel.php`:

``` php
public function registerBundles()
{
    $bundles = array(
        // ...
        new Circle\RestClientBundle\CircleRestClientBundle(),
    );
}
```

Or just initialize it:

``` php
require_once 'vendor/autoload.php';

$optionsHandler = new \Circle\RestClientBundle\Services\CurlOptionsHandler([]);
$curl = new \Circle\RestClientBundle\Services\Curl($optionsHandler);
$client = new \Circle\RestClientBundle\Services\RestClient($curl);
```

Usage
-----

You can send a message with the following code:

``` php
require_once 'vendor/autoload.php';

use libphonenumber\PhoneNumber;
use PROCERGS\Sms\SmsService;

/* initialize or get HTTP client from Symfony */

$options = [
    'send_url' => "https://some.address/send",
    'receive_url' => 'https://some.address/receive',
    'status_url' => 'https://some.address/status',
    'system_id' => 'SystemID',
    'from_string' => 'SystemID',
    'service_order' => 999999,
    'authentication' => [
        'system_id' => 'SystemID',
        'system_key' => 'your_secret_key',
    ],
];

$service = new SmsService($client, $options);

$to = new PhoneNumber();
$to->setCountryCode('55')
    ->setNationalNumber('51987654321');

try {
    $response = $service->easySend($to, "hello world!");
    var_dump($response);
} catch (Exception $e) {
    var_dump($e->getMessage());
}
```
