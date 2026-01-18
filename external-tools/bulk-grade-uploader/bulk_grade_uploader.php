<?php
/**
 * Bulk Grade Uploader - External Tool Template
 * CS425 Assignment Grading System
 * 
 * This is a TEMPLATE for Team B to build upon.
 * It demonstrates how to:
 * - Read grades from a CSV file
 * - Authenticate with the Core System API
 * - Upload grades in bulk via the API
 * 
 * Usage: php bulk_grade_uploader.php grades.csv
 * 
 * CSV Format:
 * student_email,score,feedback
 * alice@example.com,85,Good work!
 * bob@example.com,92,Excellent!
 * 
 * TODO for Team B:
 * - Add input validation
 * - Add error handling
 * - Add progress display
 * - Add logging
 * - Add configuration file support
 */

// Configuration - TODO: Move to config file
define('API_BASE_URL', 'http://localhost:8080/api');
define('INSTRUCTOR_EMAIL', 'instructor@example.com');
define('INSTRUCTOR_PASSWORD', 'password123');
define('COOKIE_FILE', '/tmp/grade_uploader_cookies.txt');

/**
 * API Client Class
 * Handles communication with the Core System API
 */
class ApiClient {
    private $baseUrl;
    private $cookieFile;
    
    public function __construct($baseUrl, $cookieFile) {
        $this->baseUrl = $baseUrl;
        $this->cookieFile = $cookieFile;
    }
    
    /**
     * Make a GET request to the API
     */
    public function get($endpoint, $params = []) {
        $url = $this->baseUrl . $endpoint;
        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookieFile);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        return [
            'code' => $httpCode,
            'data' => json_decode($response, true)
        ];
    }
    
    /**
     * Make a POST request to the API
     */
    public function post($endpoint, $data = []) {
        $ch = curl_init($this->baseUrl . $endpoint);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookieFile);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookieFile);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        return [
            'code' => $httpCode,
            'data' => json_decode($response, true)
        ];
    }
    
    /**
     * Login to the API
     */
    public function login($email, $password) {
        return $this->post('/auth/login', [
            'email' => $email,
            'password' => $password
        ]);
    }
}

/**
 * CSV Reader Class
 * Reads and parses CSV files
 */
class CsvReader {
    /**
     * Read grades from CSV file
     * Expected columns: student_email, score, feedback
     */
    public static function readGrades($filePath) {
        if (!file_exists($filePath)) {
            throw new Exception("File not found: $filePath");
        }
        
        $grades = [];
        $handle = fopen($filePath, 'r');
        
        // Read header row
        $header = fgetcsv($handle);
        if ($header === false) {
            throw new Exception("Empty CSV file");
        }
        
        // Normalize header names
        $header = array_map('strtolower', array_map('trim', $header));
        
        // Find column indices
        $emailIndex = array_search('student_email', $header);
        $scoreIndex = array_search('score', $header);
        $feedbackIndex = array_search('feedback', $header);
        
        if ($emailIndex === false || $scoreIndex === false) {
            throw new Exception("CSV must have 'student_email' and 'score' columns");
        }
        
        // Read data rows
        while (($row = fgetcsv($handle)) !== false) {
            if (empty($row[$emailIndex])) continue;
            
            $grades[] = [
                'student_email' => trim($row[$emailIndex]),
                'score' => floatval($row[$scoreIndex]),
                'feedback' => $feedbackIndex !== false ? trim($row[$feedbackIndex] ?? '') : ''
            ];
        }
        
        fclose($handle);
        return $grades;
    }
}

/**
 * Main Application
 */
class BulkGradeUploader {
    private $api;
    
    public function __construct() {
        $this->api = new ApiClient(API_BASE_URL, COOKIE_FILE);
    }
    
    /**
     * Run the bulk upload process
     */
    public function run($csvFile, $assignmentId) {
        echo "===========================================\n";
        echo "  Bulk Grade Uploader - External Tool\n";
        echo "===========================================\n\n";
        
        // Step 1: Login
        echo "[1/4] Logging in to the API...\n";
        $loginResult = $this->api->login(INSTRUCTOR_EMAIL, INSTRUCTOR_PASSWORD);
        
        if ($loginResult['code'] !== 200 || !$loginResult['data']['success']) {
            echo "ERROR: Login failed!\n";
            echo "Response: " . json_encode($loginResult['data']) . "\n";
            return false;
        }
        echo "      Login successful!\n\n";
        
        // Step 2: Read CSV file
        echo "[2/4] Reading grades from CSV file...\n";
        try {
            $grades = CsvReader::readGrades($csvFile);
            echo "      Found " . count($grades) . " grades to upload.\n\n";
        } catch (Exception $e) {
            echo "ERROR: " . $e->getMessage() . "\n";
            return false;
        }
        
        if (empty($grades)) {
            echo "ERROR: No grades found in CSV file.\n";
            return false;
        }
        
        // Step 3: Upload grades via bulk API
        echo "[3/4] Uploading grades to the API...\n";
        $uploadResult = $this->api->post('/bulk/grades', [
            'assignment_id' => $assignmentId,
            'grades' => $grades
        ]);
        
        if ($uploadResult['code'] !== 200) {
            echo "ERROR: Upload failed with HTTP code " . $uploadResult['code'] . "\n";
            echo "Response: " . json_encode($uploadResult['data']) . "\n";
            return false;
        }
        
        // Step 4: Display results
        echo "[4/4] Processing results...\n\n";
        
        $data = $uploadResult['data']['data'];
        echo "===========================================\n";
        echo "  Upload Results\n";
        echo "===========================================\n";
        echo "Total grades:     " . $data['total'] . "\n";
        echo "Successful:       " . $data['success_count'] . "\n";
        echo "Failed:           " . $data['failed_count'] . "\n";
        echo "===========================================\n\n";
        
        // Show failed uploads if any
        if (!empty($data['failed'])) {
            echo "Failed uploads:\n";
            foreach ($data['failed'] as $failure) {
                echo "  - Index " . $failure['index'] . ": " . $failure['error'] . "\n";
                echo "    Data: " . json_encode($failure['data']) . "\n";
            }
            echo "\n";
        }
        
        // Show successful uploads
        if (!empty($data['success'])) {
            echo "Successful uploads:\n";
            foreach ($data['success'] as $success) {
                echo "  - Submission #" . $success['submission_id'] . ": Score " . $success['score'] . "\n";
            }
        }
        
        echo "\nDone!\n";
        return true;
    }
}

// ============================================
// Command Line Interface
// ============================================

if (php_sapi_name() !== 'cli') {
    die("This script must be run from the command line.\n");
}

// Parse command line arguments
if ($argc < 3) {
    echo "Usage: php bulk_grade_uploader.php <csv_file> <assignment_id>\n";
    echo "\nExample: php bulk_grade_uploader.php grades.csv 1\n";
    echo "\nCSV Format:\n";
    echo "student_email,score,feedback\n";
    echo "alice@example.com,85,Good work!\n";
    echo "bob@example.com,92,Excellent!\n";
    exit(1);
}

$csvFile = $argv[1];
$assignmentId = intval($argv[2]);

if ($assignmentId <= 0) {
    echo "ERROR: Invalid assignment ID.\n";
    exit(1);
}

// Run the uploader
$uploader = new BulkGradeUploader();
$success = $uploader->run($csvFile, $assignmentId);

exit($success ? 0 : 1);
