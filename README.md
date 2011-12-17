Ddeboer Salesforce Client Bundle
================================

Introduction
------------

This bundle is a client for the
[Salesforce SOAP API](http://www.salesforce.com/us/developer/docs/api/index.htm).
The bundle is intended as a replacement for the
[Force.com Tookit for PHP](http://wiki.developerforce.com/page/Force.com_Toolkit_for_PHP).

### Features

This bundle’s features include the following.

* Automatic conversion between PHP and SOAP date and datetime objects.
* Automatic conversion of Salesforce (UTC) times to your local timezone.
* Easily extensible through events: add custom logging, error handling etc.
* Iterating over large results sets that require multiple calls to the API
  is easy through the record iterator.
* The BulkSaver helps you stay within your Salesforce API limits by using bulk
  creates, deletes, updates and upserts.
* Completely unit tested (still working on that one).
* Use the client in conjunction with the
  [Mapper Bundle](https://github.com/ddeboer/DdeboerSalesforceMapperBundle)
  to get even easier access to your Salesforce data.

Documentation
-------------

Documentation is included in the [Resources/doc directory](http://github.com/ddeboer/DdeboerSalesforceClientBundle/tree/master/Resources/doc/index.md).