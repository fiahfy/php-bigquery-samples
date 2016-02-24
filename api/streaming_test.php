<?php

require_once __DIR__ . '/streaming.php';

const DATASET_ID = 'test_dataset';
const TABLE_ID = 'test_table';

function testStreamRowToBigquery()
{
    function getMyRows()
    {
        $rows = json_decode(file_get_contents('api/resources/streamrows.json'), true);
        return array_map(function($row) {
            return json_encode($row);
        }, $rows);
    }

    $config = require __DIR__ . '/config.php';

    main($config['project_id'], DATASET_ID, TABLE_ID);
}

testStreamRowToBigquery();
