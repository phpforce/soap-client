[![Build Status](https://secure.travis-ci.org/phpforce/soap-client.png?branch=master)](http://travis-ci.org/phpforce/soap-client)

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
The recommended way to install this library is through [Composer](http://getcomposer.org).

To install it, add the following to your `composer.json`:

```JSON
{
    "require": {
        ...
        "phpforce/soap-client": "dev-master",
        ...
    }
}
```

And run `$ php composer.phar install`.