# Easy-To-Use PHP SDK for Airtable API

> This package also supports easy to use **Search** feature which is implemented under the hood of `filterByFormula`
> parameter. Check [here](#search) for more details.

## Table Of Contents

- [Installation](#installation)
- [Usage](#usage)
    - [Initialize](#initialize)
    - [How To Get appId, tableId and viewId](#how-to-get-appid-tableid-and-viewid)
    - [Setting appId/baseId](#set-appid)
    - [Setting table](#set-tableid)
    - [Create Record](#create-a-record)
    - [Create Multiple Records](#create-multiple-records)
    - [Get Record](#get-a-record)
    - [Get Multiple Records](#get-multiple-records)
    - [Update Record](#update-a-record)
    - [Update Multiple Records](#update-multiple-records)
    - [Delete Record](#delete-a-record)
    - [Delete Multiple Records](#delete-multiple-records)
- [Search](#search)
- [Support Me](#support-me)

## Installation

```bash
$ composer require zlt/airtable
```

## Usage

- ### Initialize

    ```php
    $client = new Client('token');
    ```
  The base api endpoint is `https://api.airtable.com/v0/` .
  You can change it by specifying the second parameter of the constructor.
  ```php
   $client = new Client('token','custom-endpoint');
  ```
- ### How to get appId, tableId and viewId
  The format of the url is `https://airtable.com/{appId}/{tableId}/{viewId}` .

- ### Set appId
  ```php
  $client->setAppId('appId');
  ```
- ### Set tableId
  ```php
  $client->setTable('tableId');
  ```
- ### Create a record
    ```php
    $client->create([
        'field1' => 'value1',
        'field2' => 'value2',
    ]);
    ```
- ### Create multiple records
    ```php
    $client->createMany([[
        'field1' => 'value1',
        'field2' => 'value2',
    ],[
        'field1' => 'value1',
        'field2' => 'value2',
    ]]);
    ```
- ### Get a record
  ```php
  $client->get('recordId'); 
  ```
- ### Get multiple records
  ```php
  $client->get(['recordId1','recordId2']); 
  ```
- ### Get all records in table
  ```php
  $client->all();
  ```
- ### Update a record
  ```php
  $client->update('recordId',[
      'field1' => 'value1',
      'field2' => 'value2',
  ]);
  ```
- ### Update multiple records
  ```php
  $client->updateMany([
      'recordId1' => [
          'field1' => 'value1',
          'field2' => 'value2',
      ],
      'recordId2' => [
          'field1' => 'value1',
          'field2' => 'value2',
      ],
  ]);
  ```
- ### Delete a record
  ```php
  $client->delete('recordId');
  ```  
- ### Delete multiple records
  ```php
  $client->deleteMany(['recordId1','recordId2']);
  ```

- ### Search

  You can perform operations such as `where`, `whereOr`, `whereNot`, and `and`.
    ```php
    $client->search()
            ->whereNot('Name', 'Doe')
            ->whereOr('Name', 'John')
            ->and(function ($search) {
                return $search->where('Status', 'Active');
            })
            ->get()
    ```

## Support me

I would be really appreciated if you buy me a coffee via **Binance**. 😄😄

<img src="https://zawlintun.me/BinancePayQR.png" alt="binancePayQR" width="200"/>
