<?php

namespace RSO\Collection\Drivers;

interface CollectionInterface
{
    /**
     * get list of collections from collection server
     * @return array
     */
    public function fetch(): array;

    /**
     * store collection in collection server
     * @param string $collectionName
     * @param array $fields
     * @return array
     */
    public function store(string $collectionName, array $fields): array;

    /**
     * update collection in collection server
     * @param string $collectionName
     * @param string|integer $id
     * @param array $fields
     * @return array
     */
    public function update(string $collectionName, $id, array $fields): array;

    /**
     * update collection in collection server
     * @param string $collectionName
     * @param array $filter
     * @return array
     */
    public function findOne(string $collectionName, array $filter): array;

    /**
     * update collection in collection server
     * @param string $collectionName
     * @param array $filter
     * @return array
     */
    public function findAll(string $collectionName, array $filter): array;

    /**
     * update collection in collection server
     * @param string $collectionName
     * @param string|integer $id
     * @return array
     */
    public function findById(string $collectionName, $id): array;

    /**
     * remove collection from collection server
     * @param string $collectionName
     * @param string|integer $id
     * @return mixed
     */
    public function delete(string $collectionName, $id): array;

    /**
     * remove all collections with array of collection ids
     * @param string $collectionName
     * @param array $items
     * @return mixed
     */
    public function deleteMany(string $collectionName, array $items): array;

    /**
     * update collection in collection server by condition
     * @param string $collectionName
     * @param array $filter
     * @param array $fields
     * @return array
     */
    public function updateByWhere(string $collectionName, array $filter, array $fields): array;

    /**
     * delete collection in collection server by condition
     * @param string $collectionName
     * @param array $filter
     * @return array
     */
    public function deleteByWhere(string $collectionName, array $filter): array;
}
