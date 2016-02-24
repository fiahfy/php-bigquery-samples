<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/client.php';

/**
 * @param Google_Service_Bigquery $service
 * @param string                  $projectId
 * @param string                  $datasetId
 * @param string                  $tableId
 * @param string                  $rowString
 *
 * @return Google_Service_Bigquery_TableDataInsertAllResponse
 */
function streamRowToBigquery($service, $projectId, $datasetId, $tableId, $rowString)
{
    $row = new Google_Service_Bigquery_TableDataInsertAllRequestRows();
    $row->json = json_decode($rowString, true);
    $row->insertId = uniqid();

    $request = new Google_Service_Bigquery_TableDataInsertAllRequest();
    $request->setRows([$row]);

    return $service->tabledata->insertAll($projectId, $datasetId, $tableId, $request);
}

/**
 * @param string $projectId
 * @param string $datasetId
 * @param string $tableId
 */
function main($projectId, $datasetId, $tableId)
{
    $client = Client::getMyClient();

    $service = new Google_Service_Bigquery($client);

    foreach (getRows() as $row) {
        $response = streamRowToBigquery($service, $projectId, $datasetId, $tableId, $row);
        var_dump($response);
    }
}

/**
 * @return array
 */
function getRows()
{
    if (function_exists('getMyRows')) {
        return getMyRows();
    }
    $lines = [];
    print('Enter a row (json string) into the table: ');
    while (true) {
        $handle = fopen('php://stdin', 'r');
        $line = trim(fgets($handle));
        if (!$line) {
            break;
        }
        $lines[] = $line;
        print("Enter another row into the table \n"
            . "[hit enter to stop]: ");
    }
    return $lines;
}

if (realpath($_SERVER['SCRIPT_FILENAME']) == realpath(__FILE__)) {
    $options = getopt('', ['project_id:', 'dataset_id:', 'table_id:']);

    main(
        $options['project_id'],
        $options['dataset_id'],
        $options['table_id']
    );
}
