<?php

return [
    'scopes' => [
        'https://www.googleapis.com/auth/bigquery',
        'https://www.googleapis.com/auth/cloud-platform',
        'https://www.googleapis.com/auth/devstorage.read_only',
        'https://www.googleapis.com/auth/devstorage.read_write',
        'https://www.googleapis.com/auth/devstorage.full_control'
    ],
    'private_key_file_path' => 'PRIVATE_KEY_FILE_PATH_FROM_PROJECT_ROOT_HERE',
    'project_id' => 'PROJECT_ID_HERE',
    'bucket_id' => 'BUCKET_ID_HERE_IF_USE_CLOUD_STORAGE_TO_IMPORT_OR_EXPORT'
];
