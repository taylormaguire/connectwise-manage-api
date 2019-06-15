# ConnectWise Manage API for PHP/Laravel

## Warning
Currently building and testing. Not production ready.

## Installation

### Composer Installation
Install the package through composer in your terminal.

```
composer require taylormaguire/connectwise-manage-api
```

### Setup The Environment (.env File)
Add these details to your .env environment file with your own details as appropriate.

```
CW_API_URL=https://api-na.myconnectwise.net
CW_CLIENT_ID=7a3bedaed-73f0-441b-609c-c65e27aa3e12
CW_API_VERSION=2019.3
CW_COMPANY_ID=company
CW_API_PUBLIC_KEY=company+PflTy8uZrw9yLoz6
CW_API_PRIVATE_KEY=da34naA8Cja39aE1
```

## Usage
### GET Request
Collect data from the ConnectWise Manage API using the query string parameters provided in the ConnectWise Manage API Documentation. This package will only passthrough keys that are provided by ConnectWise for Query String integrity.

Query String Parameters can be found here:
https://developer.connectwise.com/Products/Manage/Developer_Guide#Query_String_Parameters

#### Example GET Request
Here is an GET Request Example using the Facade

```php
CWManageAPI::get('company/companies', [
    'pageSize' => 30,
    'page' => 1,
    'orderBy' => 'chargeToId asc',
    'fields' => 'id,company/name,status'
]);
```

The example above will output a query string of

```
/company/companies?pageSize=30&page=1&orderBy=company/name%20%asc&fields=id,company/name,status
```
