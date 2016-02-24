<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/client.php';

/**
 * @param Google_Service_Bigquery $service
 * @param string                  $projectId
 * @param string                  $datasetId
 * @param string                  $tableId
 * @param string                  $sourceSchema
 * @param string                  $sourcePath
 *
 * @return Google_Service_Bigquery_Job
 */
function loadTable($service, $projectId, $datasetId, $tableId, $sourceSchema, $sourcePath)
{
    $jobReference = new Google_Service_Bigquery_JobReference();
    $jobReference->projectId = $projectId;
    $jobReference->jobId = uniqid();

    $fields = array_map(function($data) {
        $field = new Google_Service_Bigquery_TableFieldSchema();
        $field->type = $data['type'];
        $field->name = $data['name'];
        return $field;
    }, $sourceSchema);

    $schema = new Google_Service_Bigquery_TableSchema();
    $schema->setFields($fields);

    $table = new Google_Service_Bigquery_TableReference();
    $table->projectId = $projectId;
    $table->datasetId = $datasetId;
    $table->tableId = $tableId;

    $load = new Google_Service_Bigquery_JobConfigurationLoad();
    $load->setSchema($schema);
    $load->setDestinationTable($table);
    $load->sourceUris = [$sourcePath];

    $config = new Google_Service_Bigquery_JobConfiguration();
    $config->setLoad($load);

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
 * @param string $datasetId
 * @param string $tableId
 * @param string $schemaPath
 * @param string $dataPath
 */
function main($projectId, $datasetId, $tableId, $schemaPath, $dataPath)
{
    $client = Client::getMyClient();

    $service = new Google_Service_Bigquery($client);

    $schemaData = json_decode(file_get_contents($schemaPath), true);

    $job = loadTable($service, $projectId, $datasetId, $tableId, $schemaData, $dataPath);

    pollJob($service, $job);
}

if (realpath($_SERVER['SCRIPT_FILENAME']) == realpath(__FILE__)) {
    $options = getopt('', ['project_id:', 'dataset_id:', 'table_id:', 'schema_file:', 'data_path:']);

    main(
        $options['project_id'],
        $options['dataset_id'],
        $options['table_id'],
        $options['schema_file'],
        $options['data_path']
    );
}
