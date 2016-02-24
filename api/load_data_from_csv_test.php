<?php

require_once __DIR__ . '/load_data_from_csv.php';

const DATASET_ID = 'test_dataset';
const TABLE_ID = 'test_import_table';

function testLoadTable()
{
    $config = require __DIR__ . '/config.php';

    main(
        $config['project_id'],
        DATASET_ID,
        TABLE_ID,
        'api/resources/schema.json',
        "gs://{$config['bucket_id']}/data.csv"
    );
}

testLoadTable();
