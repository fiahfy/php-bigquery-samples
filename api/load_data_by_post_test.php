<?php

require_once __DIR__ . '/load_data_by_post.php';

const DATASET_ID = 'ephemeral_test_dataset';
const TABLE_ID = 'load_data_by_post';

function testLoadCSVData()
{
    $config = require __DIR__ . '/config.php';

    loadData(
        'api/resources/schema.json',
        'api/resources/data.csv',
        $config['project_id'],
        DATASET_ID,
        TABLE_ID
    );
}

function testLoadJSONData()
{
    $config = require __DIR__ . '/config.php';

    loadData(
        'api/resources/schema.json',
        'api/resources/data.json',
        $config['project_id'],
        DATASET_ID,
        TABLE_ID
    );
}

$options = getopt('t:');

if (function_exists($options['t'])) {
    call_user_func($options['t']);
}
