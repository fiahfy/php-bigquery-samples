<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/client.php';

/**
 * @param string $schemaPath
 * @param string $dataPath
 * @param string $projectId
 * @param string $datasetId
 * @param string $tableId
 */
function loadData($schemaPath, $dataPath, $projectId, $datasetId, $tableId)
{
    $sourceFormat = 'CSV';
    if (substr($dataPath, -5) == '.json') {
        $sourceFormat = 'NEWLINE_DELIMITED_JSON';
    }

    $client = Client::getMyClient();

    $service = new Google_Service_Bigquery($client);

    $schemaData = json_decode(file_get_contents($schemaPath), true);

    $fields = array_map(function($data) {
        $field = new Google_Service_Bigquery_TableFieldSchema();
        $field->type = $data['type'];
        $field->name = $data['name'];
        return $field;
    }, $schemaData);

    $schema = new Google_Service_Bigquery_TableSchema();
    $schema->setFields($fields);

    $table = new Google_Service_Bigquery_TableReference();
    $table->projectId = $projectId;
    $table->datasetId = $datasetId;
    $table->tableId = $tableId;

    $load = new Google_Service_Bigquery_JobConfigurationLoad();
    $load->setSchema($schema);
    $load->setDestinationTable($table);
    $load->sourceFormat = $sourceFormat;

    $config = new Google_Service_Bigquery_JobConfiguration();
    $config->setLoad($load);

    $job = new Google_Service_Bigquery_Job();
    $job->setConfiguration($config);

    $job = $service->jobs->insert($projectId, $job, [
        'data' => file_get_contents($dataPath),
        'mimeType' => 'application/octet-stream',
        'uploadType' => 'media'
    ]);

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
    loadData($schemaPath, $dataPath, $projectId, $datasetId, $tableId);
}

if (realpath($_SERVER['SCRIPT_FILENAME']) == realpath(__FILE__)) {
    $options = getopt('', ['project_id:', 'dataset_id:', 'table_id:', 'schema_file:', 'data_file:']);

    main(
        $options['project_id'],
        $options['dataset_id'],
        $options['table_id'],
        $options['schema_file'],
        $options['data_file']
    );
}
