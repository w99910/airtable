<?php

namespace Zlt\Airtable;


use Airtable\AirtableApiRequest;
use DateTime;
use Illuminate\Support\Facades\Http;

class AirtableApi
{
    protected string $token;

    protected int $apiCall = 0;

    protected int $maxCallPerSecond = 5;

    protected datetime $date;

    private static ?AirtableApi $instance = null;

    private static string $base = 'https://api.airtable.com/v0/';

    protected string $apiKey;

    /**
     * @throws \Exception
     */
    private function __construct()
    {
        $this->token = config('services.airtable.key');
        if(!$this->token){
            throw new \Exception('Please specify airtable api key in services.airtable.key');
        }
        $this->date = now();
    }

    private static function getInstance(): AirtableApi
    {
        if (!self::$instance) {
            self::$instance = new AirtableApi();
        }

        return self::$instance;
    }

    /**
     * @throws \Exception
     */
    static function get(string $appId, string $tableName, AirtableApiRequest $request = null)
    {
        $api = self::getInstance();
        if ($api->apiCall == 4 && $api->date->diffInSeconds(now()) > 3) {
            $api->apiCall = 0;
        }
        if ($api->apiCall > $api->maxCallPerSecond) {
            throw new \Exception('Api call exceeded');
        }

        if (!$request) {
            $request = new AirtableApiRequest();
        }

        $query = self::$base . $appId . '/' . $tableName . '?' . $request->getParametersQuery();
        $response = Http::withToken($api->token)->get($query);
        if ($response->failed()) {
            throw new \Exception($response->body() . PHP_EOL . 'Error Query: ' . $query);
        }
        $api->apiCall++;
        return json_decode($response->body());
    }

    /**
     * @throws \Exception
     */
    static function getFullList(string $appId, string $tableName, ?AirtableApiRequest $request): array
    {
        $data = [];
        try {
            do {
                $response = self::get($appId, $tableName, $request);
                $data[] = $response->records;
                if (!isset($response->offset)) {
                    break;
                }
                $request->offset = $response->offset;
            } while (isset($response->records) && count($response->records) > 0);
            return array_merge(...$data);
        } catch (\Exception $e) {
            throw new \Exception('Error Happens in getting list' . PHP_EOL . $e->getMessage(), code: $e->getCode(), previous: $e);
        }
    }
}
