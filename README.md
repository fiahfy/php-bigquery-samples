PHP BigQuery Samples
==========

Getting Started
-----

###Clone This Project
```
$ git clone https://github.com/fiahfy/php-bigquery-samples.git
$ cd php-bigquery-samples
```

###Install Packages
```
$ composer install
```

###Prepare Credentials
1. Go https://console.developers.google.com/
2. Create service account  
`Show Menu List -> API Manager -> Credentials -> Create credentials`
3. And download JSON private key file (`xxxxx.json`)

###Setup Config
Copy `api/config.default.php` to `api/config.php` and Update for your config.
```
return [
    'scopes' => [
        'https://www.googleapis.com/auth/bigquery',
        'https://www.googleapis.com/auth/cloud-platform',
        'https://www.googleapis.com/auth/devstorage.read_only',
        'https://www.googleapis.com/auth/devstorage.read_write',
        'https://www.googleapis.com/auth/devstorage.full_control'
    ],
    'private_key_file_path' => 'PRIVATE_KEY_FILE_PATH_FROM_PROJECT_ROOT_HERE',
    'project_id' => 'PROJECT_ID_HERE',
    'bucket_id' => 'BUCKET_ID_HERE_IF_USE_CLOUD_STORAGE_TO_IMPORT_OR_EXPORT'
];
```

CLI
-----

###[Getting Started](./api/getting_started.php)
```
$ php api/getting_started.php --project_id PROJECT_ID
# Test
$ php api/getting_started_test.php
```

###[List Datasets and Projects](./api/list_datasets_projects.php)
```
$ php api/list_datasets_projects.php --project_id PROJECT_ID
# Test
$ php api/list_datasets_projects_test.php
```

###[Load Data](./api/load_data_by_post.php)
```
$ php api/load_data_by_post.php --project_id PROJECT_ID --dataset_id DATASET_ID --table_id TABLE_ID --schema_file SCHEMA_FILE --data_file DATA_FILE
# Test CSV Data
$ php api/load_data_by_post_test.php -t testLoadCSVData
# Test JSON Data
$ php api/load_data_by_post_test.php -t testLoadJSONData
```

###[Load Data from Cloud Storage](./api/load_data_from_csv.php)
```
$ php api/load_data_from_csv.php --project_id PROJECT_ID --dataset_id DATASET_ID --table_id TABLE_ID --schema_file SCHEMA_FILE --data_path CLOUD_STORAGE_PATH
# Test
$ php api/load_data_from_csv_test.php
```

###[Streaming](./api/streaming.php)
```
$ php api/streaming.php --project_id PROJECT_ID --dataset_id DATASET_ID --table_id TABLE_ID
# Test
$ php api/streaming_test.php
```

###[Sync Query](./api/sync_query.php)
```
$ php api/sync_query.php --project_id PROJECT_ID --query QUERY
# Test
$ php api/sync_query_test.php
```

###[Async Query](./api/async_query.php)
```
$ php api/async_query.php --project_id PROJECT_ID --query QUERY
# Test
$ php api/async_query_test.php
```

###[Export Data to Cloud Storage](./api/export_data_to_cloud_storage.php)
```
$ php api/export_data_to_cloud_storage.php --gcs_path CLOUD_STORAGE_PATH --project_id PROJECT_ID --dataset_id DATASET_ID --table_id TABLE_ID
# Test CSV Data
$ php api/export_data_to_cloud_storage_test.php -t testExportTableCSV
# Test JSON Data
$ php api/export_data_to_cloud_storage_test.php -t testExportTableJSON
# Test AVRO Data
$ php api/export_data_to_cloud_storage_test.php -t testExportTableAVRO
```

Reference
-----
* [Python BigQuery Samples](https://github.com/GoogleCloudPlatform/python-docs-samples/tree/master/bigquery)
