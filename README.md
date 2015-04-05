[![Build Status](https://travis-ci.org/phpforce/soap-client.svg?branch=master)](https://travis-ci.org/phpforce/soap-client)  
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/phpforce/soap-client/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/phpforce/soap-client/?branch=master)

###[I’m looking for maintainers!](https://github.com/phpforce/soap-client/issues/4)


PHPForce Soap Client: a PHP client for the Salesforce SOAP API
==============================================================

Introduction
------------

This library is a client for the
[Salesforce SOAP API](http://www.salesforce.com/us/developer/docs/api/index.htm),
and intended as a replacement for the
[Force.com Tookit for PHP](http://wiki.developerforce.com/page/Force.com_Toolkit_for_PHP).

### Features

This library’s features include the following.

* Automatic conversion between PHP and SOAP date and datetime objects.
* Automatic conversion of Salesforce (UTC) times to your local timezone.
* Easily extensible through events: add custom logging, caching, error handling etc.
* Iterating over large results sets that require multiple calls to the API
  is easy through the record iterator.
* The BulkSaver helps you stay within your Salesforce API limits by using bulk
  creates, deletes, updates and upserts.
* Completely unit tested (still working on that one).
* Use the client in conjunction with the Symfony2
  [Mapper Bundle](https://github.com/ddeboer/DdeboerSalesforceMapperBundle)
  to get even easier access to your Salesforce data.

Installation
------------

This library is available on [Packagist](http://packagist.org/packages/phpforce/soap-client). 
The recommended way to install this library is through [Composer](http://getcomposer.org):

```bash
$ php composer.phar require phpforce/soap-client dev-master
```

Usage
-----

### The client

Use the client to query and manipulate your organisation’s Salesforce data. First construct a client using the builder:

```php
$builder = new \Phpforce\SoapClient\ClientBuilder(
  '/path/to/your/salesforce/wsdl/sandbox.enterprise.wsdl.xml',
  'username',
  'password',
  'security_token'
);

$client = $builder->build();
```

### SOQL queries

```php
$results = $client->query('select Name, SystemModstamp from Account limit 5');
```

This will fetch five accounts from Salesforce and return them as a
`RecordIterator`. You can now iterate over the results. The account’s
`SystemModstamp` is returned as a `\DateTime` object:

```php
foreach ($results as $account) {
    echo 'Last modified: ' . $account->SystemModstamp->format('Y-m-d H:i:') . "\n";
}
```

### One-to-many relations (subqueries)

Results from [subqueries](http://www.salesforce.com/us/developer/docs/api/Content/sforce_api_calls_soql_select.htm) 
are themselves returned as record iterators. So:

```php
$accounts = $client->query(
    'select Id, (select Id, Name from Contacts) from Account limit 10'
);

foreach ($accounts as $account) {
    if (isset($account->Contacts)) {
        foreach ($account->Contacts as $contact) {
            echo sprintf("Contact %s has name %s\n", $contact->Id, $contact->Name);
        }
    }
}
```

### Fetching large numbers of records

If you issue a query that returns over 2000 records, only the first 2000 records
will be returned by the Salesforce API. Using the `queryLocator`, you can then
fetch the following results in batches of 2000. The record iterator does this
automatically for you:

```php
$accounts = $client->query('Select Name from Account');
echo $accounts->count() . ' accounts returned';
foreach ($accounts as $account) {
    // This will iterate over the 2000 first accounts, then fetch the next 2000
    // and iterate over these, etc. In the end, all your organisations’s accounts
    // will be iterated over.
}
```

### Logging

To enable logging for the client, call `withLog()` on the builder. For instance when using [Monolog](https://github.com/Seldaek/monolog):

```php
$log = new \Monolog\Logger('name');  
$log->pushHandler(new \Monolog\Handler\StreamHandler('path/to/your.log'));

$builder = new \Phpforce\SoapClient\ClientBuilder(
  '/path/to/your/salesforce/wsdl/sandbox.enterprise.wsdl.xml',
  'username',
  'password',
  'security_token'
);
$client = $builder->withLog($log)
  ->build();
```

All requests to the Salesforce API, as well as the responses and any errors that it returns, will now be logged.
