<?php

namespace RSO\Collection\Drivers;

use Exception;
use GuzzleHttp\Client;

class DirectusCollection implements CollectionInterface
{
    /**
     * @var array
     */
    private $_cached_fields;
    /**
     * @var Client
     */
    private $_client;

    private $environment;

    public function __construct($environment)
    {
        $this->environment = $environment;
    }

    /**
     * client for connect to collection server
     * @return Client
     */
    private function _client(): Client
    {
        if (!$this->_client) {
            $headers = [
                "Accept" => "application/json",
                "Content-Type" => "application/json"
            ];
            if ($token = collection_config('token')) {
                $headers[collection_config('token_key')] = $token;
            }
            $this->_client = new Client([
                'verify' => collection_config('verify_client'),
                'headers' => $headers
            ]);
        }
        return $this->_client;
    }

    /**
     * get all collections
     * @return array|array[]
     */
    public function fetch(): array
    {
        $collections = [];
        try {
            $response = $this->_client()->get(collection_config('base_url') . "/collections?limit=-1");
        } catch (Exception $e) {
            return $this->_showError($e);
        }
        $array = $this->_jsonResponse($response);
        if (isset($array) && isset($array['data']) && @count($array['data'])) {
            foreach ($array['data'] as $item) {
                if (!@$item['meta']['system']) {
                    $name = $item['collection'];
                    $collections[] = [
                        'name' => $name,
                        'fields' => $this->_getFields($name),
                    ];
                }
            }
        }
        return [
            'data' => $collections
        ];
    }

    /**
     * @param string $collectionName
     * @param array $fields
     * @return array
     */
    public function store(string $collectionName, array $fields): array
    {
        try {
            $response = $this->_client()->post(collection_config('base_url') . "/items/$collectionName", [
                'json' => $fields["fields"],
            ]);
        } catch (Exception $e) {
            return $this->_showError($e);
        }
        return $this->_jsonResponse($response);
    }

    /**
     * @param string $collectionName
     * @param int|string $id
     * @param array $fields
     * @return array
     */
    public function update(string $collectionName, $id, array $fields): array
    {
        try {
            $response = $this->_client()->patch(collection_config('base_url') . "/items/$collectionName/$id", [
                'json' => $fields["fields"],
            ]);
        } catch (Exception $e) {
            return $this->_showError($e);
        }
        return $this->_jsonResponse($response);
    }

    /**
     * @param string $collectionName
     * @param array $filter
     * @param array $fields
     * @return array
     */
    public function updateByWhere(string $collectionName, array $filter, array $fields): array
    {
        $filter = ["filter" => $filter];
        $row = $this->findOne($collectionName, $filter);
        return $this->update($collectionName, $row['data']['id'], $fields);
    }

    /* find methods start */
    /**
     * @param string $collectionName
     * @param int|string $id
     * @return array
     */
    public function findById(string $collectionName, $id): array
    {
        try {
            $response = $this->_client()->get(collection_config('base_url') . "/items/$collectionName/$id");
        } catch (Exception $e) {
            return $this->_showError($e);
        }
        return $this->_jsonResponse($response);
    }

    /**
     * @param string $collectionName
     * @param array $filter
     * @return array
     */
    public function findOne(string $collectionName, array $filter): array
    {
        $filter['limit'] = 1;
        $rows = $this->_find($collectionName, $filter);
        if (!isset($row['data']['errors'])) {
            $rows['data'] = $rows['data'][0];
        }
        return $rows;
    }

    /**
     * @param string $collectionName
     * @param array $filter
     * @return array
     */
    public function findAll(string $collectionName, array $filter): array
    {
        $filter = ["filter" => $filter];
        return $this->_find($collectionName, $filter);
    }

    /**
     * @param string $collectionName
     * @param array $filter
     * @return array
     */
    private function _find(string $collectionName, array $filter): array
    {
        $params = http_build_query($filter);
        try {
            $response = $this->_client()->get(collection_config('base_url') . "/items/$collectionName?$params");
        } catch (Exception $e) {
            return $this->_showError($e);
        }
        return $this->_jsonResponse($response);
    }
    /* find methods end */

    /* delete methods start */
    /**
     * @param string $collectionName
     * @param int|string $id
     * @return array
     */
    public function delete(string $collectionName, $id): array
    {
        try {
            $response = $this->_client()->delete(collection_config('base_url') . "/items/$collectionName/$id");
        } catch (Exception $e) {
            return $this->_showError($e);
        }
        $status = $response->getStatusCode();
        return [
            'statusCode' => $status,
            'data' => [
                ['success' => true, "id" => $id]
            ],
        ];
    }

    /**
     * remove all collections with array of collection ids
     * @param string $collectionName
     * @param array $items
     * @return mixed
     */
    public function deleteMany(string $collectionName, array $items): array
    {
        try {
            $response = $this->_client()->delete(collection_config('base_url') . "/items/$collectionName/", [
                "json" => $items
            ]);
        } catch (Exception $e) {
            return $this->_showError($e);
        }
        $status = $response->getStatusCode();
        return [
            'statusCode' => $status,
            'data' => [
                ['success' => true]
            ],
        ];
    }

    /**
     * @param string $collectionName
     * @param array $filter
     * @return array
     */
    public function deleteByWhere(string $collectionName, array $filter): array
    {
        $rows = $this->findAll($collectionName, $filter);
        if ($rows && isset($rows['data']) && count($rows["data"]) > 0) {
            $results = [];
            foreach ($rows["data"] as $key => $value) {
                // $results[$value["id"]] = $this->delete($collectionName, $value['id']);
                array_push($results, $value["id"]);
            }
            $response = $this->deleteMany($collectionName, $results);
            return [
                "data" => [
                    "statusCode" => 200,
                    "results" => $response
                ]
            ];
        } else {
            return [
                'statusCode' => 404,
                'data' => [
                    'errors' => [__('Not Found')]
                ],
            ];
        }
    }
    /* delete methods end */

    /**
     * get all fields in all collections and cache them
     * @return array
     */
    private function _cached_fields(): array
    {
        if (!$this->_cached_fields) {
            $response = $this->_client()->get(collection_config('base_url') . "/fields?limit=-1");
            try {
                $array = json_decode($response->getBody()->getContents(), true);
            } catch (Exception $e) {
            }
            $fields = [];
            if (isset($array) && isset($array['data']) && @count($array['data'])) {
                foreach ($array['data'] as $item) {
                    $fields[$item['collection']][] = [
                        'field' => $item['field'],
                        'type' => $item['type'],
                    ];
                }
            }
            $this->_cached_fields = $fields;
        }
        return $this->_cached_fields;
    }

    /**
     * get fields of specific collection by name
     * @param string $name
     * @return array
     */
    private function _getFields(string $name): array
    {
        if (isset($this->_cached_fields()[$name])) {
            return $this->_cached_fields()[$name];
        }
        return [];
    }

    /**
     * error response
     * @param $e
     * @return array
     */
    private function _showError($e): array

    {
        return [

            'statusCode' => $e->getCode(),
            'data' => [
                'errors' => [$e->getMessage()]
            ],
        ];
    }

    /**
     * get json of response
     * @param $response
     * @return array
     */
    private function _jsonResponse($response): array
    {
        try {
            $data = json_decode($response->getBody()->getContents(), true);
        } catch (Exception $e) {
            return $this->_showError($e);
        }
        $status = $response->getStatusCode();
        if (isset($data['data'])) {
            $return = $data;
        } else {
            $return = ["data" => $data];
        }
        $return['statusCode'] = $status;
        return $return;
    }
}
