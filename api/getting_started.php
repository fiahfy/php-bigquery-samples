<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/client.php';

/**
 * @param string $projectId
 *
 * @throws Exception
 */
function main($projectId)
{
    $client = Client::getMyClient();

    $service = new Google_Service_Bigquery($client);

    try {
        $request = new Google_Service_Bigquery_QueryRequest();
        $request->query = "SELECT TOP(corpus, 10) as title, "
                        . "COUNT(*) as unique_words "
                        . "FROM [publicdata:samples.shakespeare]";

        $response = $service->jobs->query($projectId, $request);

        print("Query Results:\n");

        foreach ($response->getRows() as $row) {
            print(implode("\t", array_map(function($f) { return $f->v; }, $row->f)) . "\n");
        }
    } catch (Exception $e) {
        print("Error: {$e->getMessage()}\n");
        throw $e;
    }
}

if (realpath($_SERVER['SCRIPT_FILENAME']) == realpath(__FILE__)) {
    $options = getopt('', ['project_id:']);

    main($options['project_id']);
}
