# PHP Wrapper for Airtable Api

> Currently, you can only call `get` method.

## Installation

```bash
$ composer require zlt/airtable
```

## Preparation

Since airtable api needs api key, you have to specify your key under `config -> services.php -> airtable.key`
```php
// config/services.php
return [
  ... 
  'airtable' => [
        'key' => env('AIRTABLE_API'),
    ],
];
```

## Usage

- ### Basic
  Use `Zlt\Airtable\AirtableApi::get($appId,$tableName,$airtableApiRequest)` to get data.
  For example, if you want to get data from `daily-notes` **view** in `daily` table of which appId is `1234567`,
  ```php
  $request = new \Zlt\Airtable\AirtableRequest();
  $request->view = 'daily-notes';
  dd(Zlt\Airtable\AirtableApi::get('1234567','daily',$request));
  ``` 

- ### Getting full rows/data 
  Maximum umber of roles which will be returned in each api request is `100`. So in order to get full rows/data, 
  use `Zlt\Airtable\AirtableApi::getFullList($appId,$tableName,$airtableApiRequest)`
  ```php
  $request = new \Zlt\Airtable\AirtableRequest();
  $request->view = 'daily-notes';
  dd(Zlt\Airtable\AirtableApi::getFullList('1234567','daily',$request));
  ```   

- ### Filtering 
  You can filter data using specifying `filterByFormula` of request. 
  For example, let's say you want to get rows in which column `type` is equal to `personal`. 
 ```php
 $request = new \Zlt\Airtable\AirtableRequest();
 $request->view = 'daily-notes';
 $request->filterByFormula = "AND({type}='personal')";
 dd(Zlt\Airtable\AirtableApi::getFullList('1234567','daily',$request));
 ```
 > For more information about filter, please consult [airtable api documentation](https://support.airtable.com/docs/formula-field-reference)
