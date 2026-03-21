<?php
#
#   public/api/index.php
#
const SPARKS = '/etc/spark/sparks';


#
#   function[s]
#
function create(string $file, string $key) {
    if (file_exists($file)) {
        respond(409, ['error' => 'Already exists']);
    }
    $body = file_get_contents('php://input');
    file_put_contents($file, $body);
    respond(201, ['ok' => true, 'key' => $key]);
}

function delete(string $file, string $key) {
    if (!file_exists($file)) {
        respond(404, ['error' => 'Not found']);
    }
    unlink($file);
    respond(200, ['ok' => true, 'key' => $key]);
}

function read(string $file) {
    if (!file_exists($file)) {
        respond(404, ['error' => 'Not found']);
    }
    respond(200, file_get_contents($file), 'text/markdown');
}

function respond($code, $body, $contentType = 'application/json') {
    http_response_code($code);
    header("Content-Type: $contentType");
    echo is_array($body) ? json_encode($body) : $body;
    exit;
}

function update(string $file, string $key) {
    if (!file_exists($file)) {
        respond(404, ['error' => 'Not found']);
    }
    $body = file_get_contents('php://input');
    file_put_contents($file, $body);
    respond(200, ['ok' => true, 'key' => $key]);
}


#
#   main
#

# set method
$method = $_SERVER['REQUEST_METHOD'];

# set key
$key = $_GET['key'] ?? null;

# check key
if (!$key || !preg_match('/^[a-zA-Z0-9_\-]+$/', $key)) {
    respond(400, ['error' => 'Missing or invalid key']);
}

# set file
$file = SPARKS . '/' . $key . '.md';

# route
switch ($method) {
    case 'GET':    read($file);             break;
    case 'POST':   create($file, $key);    break;
    case 'PUT':    update($file, $key);    break;
    case 'DELETE': delete($file, $key);    break;
    default:       respond(405, ['error' => 'Method not allowed']);
}
