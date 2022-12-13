<?php

namespace Zlt\Airtable;


use Zlt\Airtable\Plugins\Search;

class Client
{
    protected string $base = 'https://api.airtable.com/v0/';
    protected ?string $appId = null;
    protected ?string $table = null;

    public function __construct(protected string $token, string $base = null)
    {
        if ($base) {
            $this->base = $base;
        }
    }

    /**
     * @throws \Exception
     */
    protected function call(string $endPoint, ?array $body = null, string $method = 'GET')
    {
        $this->validate();
        $headers = [
            'Authorization: Bearer ' . $this->token,
            "Content-Type: application/json",
        ];
        $url = $this->base . $this->appId . '/' . $this->table . $endPoint;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        if ($body) {
            if ($method === 'GET') {
                $fields = http_build_query($body);
                $url = $url . '?' . $fields;
            } else {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
            }
        }
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
        if (!$result) {
            throw new \Exception('Error Happens in calling Airtable API' . PHP_EOL . curl_error($ch), code: curl_errno($ch));
        }
        return json_decode($result);
    }

    /**
     * @throws \Exception
     */
    public function get(array|string $parameters = null)
    {
        if (is_string($parameters)) {
            return $this->call("/$parameters");
        }
        return $this->call('/', $parameters);
    }

    public function search(): Search
    {
        return new Search(function ($filterByFormula) {
            return $this->call('/', ['filterByFormula' => $filterByFormula]);
        });
    }

    /**
     * Get all records from table
     * This method will iterate call to get all records using offset
     * @param array $parameters
     * @param bool $shouldDebug If it is enabled, will print out the offset
     * @return array
     * @throws \Exception
     */
    public function all(array $parameters = [], bool $shouldDebug = false): array
    {
        $data = [];
        do {
            if ($shouldDebug) {
                echo "calling airtable api " . (array_key_exists('offset', $parameters) ? ", offset: {$parameters['offset']}" : null) . PHP_EOL;
            }
            $response = $this->get($parameters);
            $data[] = $response->records ?? [];
            if (!isset($response->offset)) {
                break;
            }
            $parameters['offset'] = $response->offset;
            sleep(1);
        } while (isset($response->records) && count($response->records) > 0);
        return array_merge(...$data);
    }

    /**
     * @throws \Exception
     */
    public function create(array $item = [])
    {
        return $this->call('/', ['fields' => $item], 'POST');
    }

    /**
     * @throws \Exception
     */
    public function createMany(array $items = [])
    {
        $records = [];
        foreach ($items as $item) {
            $records[] = ['fields' => $item];
        }
        return $this->call('/', ['records' => $records], 'POST');
    }

    /**
     * @param string $id
     * @param array $fields
     * @param bool $shouldDeleteOtherFields should perform a destructive update and clear all unspecified cell values
     * @throws \Exception
     */
    public function update(string $id, array $fields = [], bool $shouldDeleteOtherFields = false)
    {
        return $this->call("/$id", ['fields' => $fields], $shouldDeleteOtherFields ? 'PUT' : 'PATCH');
    }

    /**
     * @param array $items Array of fields to update with the record id as key
     * @throws \Exception
     */
    public function updateMany(array $items = [], bool $shouldDeleteOtherFields = false)
    {
        $records = [];
        foreach ($items as $id => $item) {
            if (!is_string($id)) {
                throw new \Exception('Please specify the record id as the key of the item');
            }
            $records[] = [
                'id' => $id,
                'fields' => $item
            ];
        }
        return $this->call('/', ['fields' => $records], $shouldDeleteOtherFields ? 'PUT' : 'PATCH');
    }

    /**
     * @param string $id The record id
     * @throws \Exception
     */
    public function delete(string $id)
    {
        return $this->call('/' . $id, null, 'DELETE');
    }

    /**
     * @param array $ids Array of record ids
     * @throws \Exception
     */
    public function deleteMany(array $ids)
    {
        $query = '';
        foreach ($ids as $id) {
            if ($query != '') {
                $query .= '&';
            }
            $query .= 'records%5B%5D=' . $id;
        }
        return $this->call("?$query", null, 'DELETE');
    }

    /**
     * @throws \Exception
     */
    protected function validate(): void
    {
        if (!$this->appId) {
            throw new \Exception('App Id is required');
        }
        if (!$this->table) {
            throw new \Exception('Please set table/view');
        }
    }

    /**
     * @param string $appId
     * @return $this
     */
    public function setAppId(string $appId): static
    {
        $this->appId = $appId;
        return $this;
    }

    /**
     * @param string $table
     * @return $this
     */
    public function setTable(string $table): static
    {
        $this->table = $table;
        return $this;
    }
}
