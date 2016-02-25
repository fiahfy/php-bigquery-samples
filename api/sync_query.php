<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/client.php';

/**
 * @param Google_Service_Bigquery $service
 * @param string                  $projectId
 * @param string                  $query
 * @param int                     $timeout
 * @param int                     $retries
 *
 * @return Google_Service_Bigquery_QueryResponse
 */
function syncQuery($service, $projectId, $query, $timeout = 10000, $retries = 5)
{
    $service->getClient()->setConfig('retry', ['retries' => $retries]);

    $request = new Google_Service_Bigquery_QueryRequest();
    $request->query = $query;
    $request->timeoutMs = $timeout;

    return $service->jobs->query($projectId, $request);
}

/**
 * @param string $projectId
 * @param string $query
 * @param int    $timeout
 * @param int    $retries
 */
function main($projectId, $query, $timeout, $retries)
{
    $client = Client::getMyClient();

    $service = new Google_Service_Bigquery($client);

    $job = syncQuery($service, $projectId, $query, $timeout);

    $results = [];
    $pageToken = null;
    while (true) {
        $page = $service->jobs->getQueryResults(
            $job->getJobReference()->projectId,
            $job->getJobReference()->jobId,
            ['pageToken' => $pageToken]
        );
        $results = array_merge($results, $page->getRows());
        $pageToken = $page->pageToken;
        if (!$pageToken) {
            break;
        }
    }
    var_dump($results);
}

if (realpath($_SERVER['SCRIPT_FILENAME']) == realpath(__FILE__)) {
    $options = getopt('t::r::', ['project_id:', 'query:']);

    main(
        $options['project_id'],
        $options['query'],
        $options['t'] ?: 30,
        $options['r'] ?: 5
    );
}
