<?php

require_once __DIR__ . '/getting_started.php';

function testMain()
{
    $config = require __DIR__ . '/config.php';

    main($config['project_id']);
}

testMain();
