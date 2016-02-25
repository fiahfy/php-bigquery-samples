<?php

require_once __DIR__ . '/../vendor/autoload.php';

class Client
{
    /**
     * @return Google_Client
     */
    static function getMyClient()
    {
        $config = require __DIR__ . '/config.php';

        $client = new Google_Client();
        $client->setAuthConfigFile($config['private_key_file_path']);
        $client->setScopes($config['scopes']);
        // Or if using Application Default Credentials
        // $client->useApplicationDefaultCredentials();
        return $client;
    }
}
