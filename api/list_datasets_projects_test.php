<?php

require_once __DIR__ . '/list_datasets_projects.php';

function testMain()
{
    $config = require __DIR__ . '/config.php';

    main($config['project_id']);
}

testMain();
