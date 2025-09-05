<?php
// load_data.php
header('Content-Type: application/json');

// Enable CORS if needed
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

// Path to your CSV file
$csvFile = 'data.csv';

// Check if file exists
if (!file_exists($csvFile)) {
    echo json_encode(['error' => 'CSV file not found']);
    exit;
}

// Get parameters from request
$filters = $_POST['filters'] ?? [];
$page = $_POST['page'] ?? 1;
$perPage = $_POST['perPage'] ?? 50;
$search = $_POST['search'] ?? '';

// Read CSV file
$data = [];
$headers = [];
$rowCount = 0;

if (($handle = fopen($csvFile, "r")) !== FALSE) {
    // Get headers
    $headers = fgetcsv($handle);
    
    // Apply filters and search if provided
    while (($row = fgetcsv($handle)) !== FALSE) {
        $rowCount++;
        
        // Skip if we're paginating
        if ($rowCount <= ($page - 1) * $perPage) {
            continue;
        }
        
        if ($rowCount > $page * $perPage) {
            break;
        }
        
        $rowData = array_combine($headers, $row);
        
        // Apply search
        if ($search) {
            $match = false;
            foreach ($rowData as $value) {
                if (stripos($value, $search) !== false) {
                    $match = true;
                    break;
                }
            }
            if (!$match) {
                continue;
            }
        }
        
        // Apply filters
        $include = true;
        foreach ($filters as $key => $value) {
            if (!empty($value) && isset($rowData[$key])) {
                if (stripos($rowData[$key], $value) === false) {
                    $include = false;
                    break;
                }
            }
        }
        
        if ($include) {
            $data[] = $rowData;
        }
    }
    fclose($handle);
}

// Get total count for pagination (this is simplified - in production you'd want a more efficient count)
$totalCount = 0;
if (($handle = fopen($csvFile, "r")) !== FALSE) {
    // Skip header
    fgetcsv($handle);
    
    while (($row = fgetcsv($handle)) !== FALSE) {
        $totalCount++;
    }
    fclose($handle);
}

echo json_encode([
    'data' => $data,
    'total' => $totalCount,
    'page' => $page,
    'perPage' => $perPage
]);
?>