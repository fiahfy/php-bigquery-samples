<?php

require_once __DIR__ . '/sync_query.php';

function testSyncQuery()
{
    $config = require __DIR__ . '/config.php';

    $query = "SELECT corpus "
           . "FROM publicdata:samples.shakespeare "
           . "GROUP BY corpus;";

    main($config['project_id'], $query, 30, 5);
}

testSyncQuery();
