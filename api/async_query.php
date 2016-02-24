<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/client.php';

/**
 * @param Google_Service_Bigquery $service
 * @param string                  $projectId
 * @param string                  $query
 * @param bool                    $batch
 *
 * @return Google_Service_Bigquery_Job
 */
function asyncQuery($service, $projectId, $query, $batch = false)
{
    $jobReference = new Google_Service_Bigquery_JobReference();
    $jobReference->projectId = $projectId;
    $jobReference->jobId = uniqid();

    $configQuery = new Google_Service_Bigquery_JobConfigurationQuery();
    $configQuery->query = $query;
    $configQuery->priority = $batch ? 'BATCH' : 'INTERACTIVE';

    $config = new Google_Service_Bigquery_JobConfiguration();
    $config->setQuery($configQuery);

    $job = new Google_Service_Bigquery_Job();
    $job->setJobReference($jobReference);
    $job->setConfiguration($config);

    return $service->jobs->insert($projectId, $job);
}

/**
 * @param Google_Service_Bigquery     $service
 * @param Google_Service_Bigquery_Job $job
 */
function pollJob($service, $job)
{
    print("Waiting for job to finish...\n");

    while (true) {
        $job = $service->jobs->get($job->getJobReference()->projectId, $job->getJobReference()->jobId);
        if ($job->getStatus()->state == 'DONE') {
            if ($job->getStatus()->errorResult) {
                throw new RuntimeException($job->getStatus()->errorResult->message);
            }
            print("Job complete.\n");
            return;
        }
        sleep(1);
    }
}

/**
 * @param string $projectId
 * @param string $query
 * @param bool   $batch
 */
function main($projectId, $query, $batch)
{
    $client = Client::getMyClient();

    $service = new Google_Service_Bigquery($client);

    $job = asyncQuery($service, $projectId, $query, $batch);

    pollJob($service, $job);

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
    $options = getopt('b::', ['project_id:', 'query:']);

    main(
        $options['project_id'],
        $options['query'],
        $options['b'] ?: false
    );
}
