<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/client.php';

/**
 * @param Google_Service_Bigquery $service
 * @param string                  $projectId
 */
function listDatasets($service, $projectId)
{
    $listDatasets = $service->datasets->listDatasets($projectId);
    $datasets = $listDatasets->getDatasets();
    print("Dataset list:\n");
    var_dump($datasets);
}

/**
 * @param Google_Service_Bigquery $service
 */
function listProjects($service)
{
    $listProjects = $service->projects->listProjects();
    $projects = $listProjects->getProjects();
    print("Project list:\n");
    var_dump($projects);
}

/**
 * @param string $projectId
 */
function main($projectId)
{
    $client = Client::getMyClient();

    $service = new Google_Service_Bigquery($client);

    listDatasets($service, $projectId);
    listProjects($service);
}

if (realpath($_SERVER['SCRIPT_FILENAME']) == realpath(__FILE__)) {
    $options = getopt('', ['project_id:']);

    main($options['project_id']);
}
