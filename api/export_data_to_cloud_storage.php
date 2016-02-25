<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/client.php';

/**
 * @param Google_Service_Bigquery $service
 * @param string                  $cloudStoragePath
 * @param string                  $projectId
 * @param string                  $datasetId
 * @param string                  $tableId
 * @param int                     $retries
 * @param string                  $exportFormat
 * @param string                  $compression
 *
 * @return Google_Service_Bigquery_Job
 */
function exportTable(
    $service, $cloudStoragePath, $projectId, $datasetId, $tableId,
    $retries = 5, $exportFormat = 'CSV', $compression = 'NONE'
) {
    $service->getClient()->setConfig('retry', ['retries' => $retries]);

    $jobReference = new Google_Service_Bigquery_JobReference();
    $jobReference->projectId = $projectId;
    $jobReference->jobId = uniqid();

    $table = new Google_Service_Bigquery_TableReference();
    $table->projectId = $projectId;
    $table->datasetId = $datasetId;
    $table->tableId = $tableId;

    $extract = new Google_Service_Bigquery_JobConfigurationExtract();
    $extract->setSourceTable($table);
    $extract->destinationUris = [$cloudStoragePath];
    $extract->destinationFormat = $exportFormat;
    $extract->compression = $compression;

    $config = new Google_Service_Bigquery_JobConfiguration();
    $config->setExtract($extract);

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
 * @param string $cloudStoragePath
 * @param string $projectId
 * @param string $datasetId
 * @param string $tableId
 * @param int    $retries
 * @param string $exportFormat
 * @param string $compression
 */
function main($cloudStoragePath, $projectId, $datasetId, $tableId, $retries, $exportFormat = 'CSV', $compression = 'NONE')
{
    $client = Client::getMyClient();

    $service = new Google_Service_Bigquery($client);

    $job = exportTable($service, $cloudStoragePath, $projectId, $datasetId, $tableId, $retries, $exportFormat, $compression);

    pollJob($service, $job);
}

if (realpath($_SERVER['SCRIPT_FILENAME']) == realpath(__FILE__)) {
    $options = getopt('z::', ['project_id:', 'dataset_id:', 'table_id:', 'gcs_path:']);

    main(
        $options['gcs_path'],
        $options['project_id'],
        $options['dataset_id'],
        $options['table_id'],
        $options['r'] ?: 5,
        'CSV',
        $options['z'] ? 'GZIP' :'NONE'
    );
}
