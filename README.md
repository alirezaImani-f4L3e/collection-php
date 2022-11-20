# Rso Collection php Client

## Installation

### Run :

```
composer require rso/collection-php
```

you must add required environment variables

```
COLLECTION_BASE_URL  <!--- specify collection base url -->
COLLECTION_DRIVER <!--- specify collection driver -->
COLLECTION_TOKEN <!--- specify authentication token (bearer token)-->
COLLECTION_TOKEN_KEY <!--- authentication token to set in header -->
COLLECTION_VERIFY_CLIENT <!--- boolean to specify client ssl verification -->
```

## installation bpms :

[collection helper wiki](https://gitlab.rso-co.ir/rso/bpms/core/-/wikis/ESB-Helper-function-in-scripts)

## Uninstall

### Run :

```
composer remove rso/collection-php
```

## Helper Methods Listing

### collection helper parameters
 collection helper has an optional `environment` parameter to specify whether or not showing full error trace 
```
collection([,$environment = 'production']);
```

fetch all driver collections
```
collection()->fetch() ; 
```

store a document in collection
```
collection()->store(string $collectionName , [
    "fields"=>[]
]);
```

update a document by id
```
collection()->update(string $collectionName ,int $documentId , [
    "fields"=>[]
]);
```

update a document with other filters
```
collection()->updateByWhere(string $collectionName ,array $filter , [
    "fields"=>[]
]);
```

find all documents with specified filter
```
collection()->findAll(string $collectionName ,array $filter);
```

delete a document with specified filter
```
collection()->deleteByWhere(string $collectionName ,array $filter);
```

find a document by id 
```
collection()->findById(string $collectionName ,int $documentId);
```

find first document with given filter
```
collection()->findOne(string $collectionName ,array $filter);
```

delete a document by id 
```
collection()->delete(string $collectionName ,int $documentId);
```

delete multiple documents with an array of document ids
```
collection()->deleteMany(string $collectionName , array $documentIds);
```