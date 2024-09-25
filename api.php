<?php
header('Content-Type: application/json');

function scanPorts($target) {
    $command = "nmap -p 1-65535 $target";
    exec($command, $output);

    $openPorts = [];
    foreach ($output as $line) {
        if (preg_match("/^(\d+)\/tcp\s+open$/", $line, $matches)) {
            $openPorts[] = intval($matches[1]);
        }
    }

    return $openPorts;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!$data || !isset($data['target'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid request data']);
        exit;
    }

    $target = $data['target'];
    $openPorts = scanPorts($target);

    $result = [
        'target' => $target,
        'open_ports' => $openPorts
    ];

    echo json_encode($result);
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}
?>
