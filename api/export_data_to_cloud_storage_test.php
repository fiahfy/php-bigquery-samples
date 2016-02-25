<?php

require_once __DIR__ . '/export_data_to_cloud_storage.php';

const DATASET_ID = 'test_dataset';
const TABLE_ID = 'test_table';

function testExportTableCSV()
{
    $config = require __DIR__ . '/config.php';

    main(
        "gs://{$config['bucket_id']}/output.csv",
        $config['project_id'],
        DATASET_ID,
        TABLE_ID,
        5,
        'CSV'
    );
}

function testExportTableJSON()
{
    $config = require __DIR__ . '/config.php';

    main(
        "gs://{$config['bucket_id']}/output.json",
        $config['project_id'],
        DATASET_ID,
        TABLE_ID,
        5,
        'NEWLINE_DELIMITED_JSON'
    );
}

function testExportTableAVRO()
{
    $config = require __DIR__ . '/config.php';

    main(
        "gs://{$config['bucket_id']}/output.avro",
        $config['project_id'],
        DATASET_ID,
        TABLE_ID,
        5,
        'AVRO'
    );
}

$options = getopt('t:');

if (function_exists($options['t'])) {
    call_user_func($options['t']);
}
