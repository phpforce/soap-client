Ddeboer Salesforce Client Bundle
================================

Introduction
------------

This bundle is a client for the [Salesforce SOAP API](http://www.salesforce.com/us/developer/docs/api/index.htm).
The bundle is intended as a replacement for the [Force.com Tookit for PHP](http://wiki.developerforce.com/page/Force.com_Toolkit_for_PHP).

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

Installation
------------

This bundle is available on [Packagist](http://packagist.org/packages/ddeboer/salesforce-client-bundle).

### 1. To add this bundle to your project, add the following to your `composer.json`:

```
{
    ...
    "require": {
        ...,
        "ddeboer/salesforce-client-bundle": "*"
    }
    ...
}
```

### 2. Install it:

```
$ composer.phar update
```

### 3. Finally, add the bundle to your kernel:

Add the following to `AppKernel.php`:
```
    public function registerBundles()
    {
        $bundles = array(
            ...
            new Ddeboer\Salesforce\ClientBundle\DdeboerSalesforceClientBundle(),
            ...
        );
    }
```

Usage
-----

Once installed, the bundle offers several services:

* a client: `ddeboer_salesforce_client`
* a bulk saver: `ddeboer_salesforce_client.bulk_saver`.

Use the client to query and manipulate your organisation’s Salesforce data.

```
$result = $this->container->get('ddeboer_salesforce_client')->query(
    "select Name, SystemModstamp from Account LIMIT 5");
```

This will fetch five accounts from Salesforce and return them as a
`RecordIterator`. You can now iterate over the results. The account’s
`SystemModstamp` is returned as a `\DateTime` object.

```
foreach ($results as $account) {
    echo 'Last modified: ' . $account->SystemModstamp->format('Y-m-d H:i:') . "\n";
}
```

### Fetching large numbers of records

If you issue a query that returns over 2000 records, only the first 2000 records
will be returned by the Salesforce API. Using the `queryLocator`, you can then
fetch the following results in batches of 2000. The record iterator does this
automatically for you:

```
$accounts = $client->query('Select Name from Account');
echo $accounts->count() . ' accounts returned';
foreach ($accounts as $account) {
    // This will iterate over the 2000 first accounts, then fetch the next 2000
    // and iterate over these, etc. In the end, all your organisations’s accounts
    // will be iterated over.
}
```