<?php
/**
 * Grade Report Generator - External Tool Template
 * CS425 Assignment Grading System
 * 
 * This is a TEMPLATE for Team B to build upon.
 * It demonstrates how to:
 * - Fetch grades from the Core System API
 * - Generate summary reports
 * - Export data to different formats
 * 
 * Usage: php grade_report_generator.php [assignment_id] [format]
 * 
 * Formats: text, html, csv
 * 
 * TODO for Team B:
 * - Add PDF export
 * - Add charts/visualizations
 * - Add email functionality
 * - Add scheduling support
 * - Add more statistics
 */

// Configuration - TODO: Move to config file
define('API_BASE_URL', 'http://localhost:8080/api');
define('INSTRUCTOR_EMAIL', 'instructor@example.com');
define('INSTRUCTOR_PASSWORD', 'password123');
define('COOKIE_FILE', '/tmp/report_generator_cookies.txt');
define('OUTPUT_DIR', __DIR__ . '/reports');

/**
 * API Client Class
 */
class ApiClient {
    private $baseUrl;
    private $cookieFile;
    
    public function __construct($baseUrl, $cookieFile) {
        $this->baseUrl = $baseUrl;
        $this->cookieFile = $cookieFile;
    }
    
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
    
    public function login($email, $password) {
        return $this->post('/auth/login', [
            'email' => $email,
            'password' => $password
        ]);
    }
}

/**
 * Statistics Calculator
 */
class Statistics {
    public static function calculate($grades) {
        if (empty($grades)) {
            return [
                'count' => 0,
                'average' => 0,
                'median' => 0,
                'min' => 0,
                'max' => 0,
                'std_dev' => 0,
                'pass_rate' => 0
            ];
        }
        
        $scores = array_filter(array_column($grades, 'score'), function($s) {
            return $s !== null;
        });
        
        if (empty($scores)) {
            return [
                'count' => count($grades),
                'graded' => 0,
                'average' => 0,
                'median' => 0,
                'min' => 0,
                'max' => 0,
                'std_dev' => 0,
                'pass_rate' => 0
            ];
        }
        
        sort($scores);
        $count = count($scores);
        $sum = array_sum($scores);
        $average = $sum / $count;
        
        // Median
        $middle = floor($count / 2);
        $median = $count % 2 ? $scores[$middle] : ($scores[$middle - 1] + $scores[$middle]) / 2;
        
        // Standard deviation
        $variance = 0;
        foreach ($scores as $score) {
            $variance += pow($score - $average, 2);
        }
        $std_dev = sqrt($variance / $count);
        
        // Pass rate (assuming 60% is passing)
        $passing = array_filter($scores, function($s) { return $s >= 60; });
        $pass_rate = (count($passing) / $count) * 100;
        
        return [
            'count' => count($grades),
            'graded' => $count,
            'average' => round($average, 2),
            'median' => round($median, 2),
            'min' => min($scores),
            'max' => max($scores),
            'std_dev' => round($std_dev, 2),
            'pass_rate' => round($pass_rate, 1)
        ];
    }
    
    public static function gradeDistribution($grades) {
        $distribution = [
            'A (90-100)' => 0,
            'B (80-89)' => 0,
            'C (70-79)' => 0,
            'D (60-69)' => 0,
            'F (0-59)' => 0,
            'Not Graded' => 0
        ];
        
        foreach ($grades as $grade) {
            $score = $grade['score'];
            if ($score === null) {
                $distribution['Not Graded']++;
            } elseif ($score >= 90) {
                $distribution['A (90-100)']++;
            } elseif ($score >= 80) {
                $distribution['B (80-89)']++;
            } elseif ($score >= 70) {
                $distribution['C (70-79)']++;
            } elseif ($score >= 60) {
                $distribution['D (60-69)']++;
            } else {
                $distribution['F (0-59)']++;
            }
        }
        
        return $distribution;
    }
}

/**
 * Report Generator
 */
class ReportGenerator {
    private $grades;
    private $stats;
    private $distribution;
    private $assignmentTitle;
    
    public function __construct($grades, $assignmentTitle = 'All Assignments') {
        $this->grades = $grades;
        $this->assignmentTitle = $assignmentTitle;
        $this->stats = Statistics::calculate($grades);
        $this->distribution = Statistics::gradeDistribution($grades);
    }
    
    /**
     * Generate text report
     */
    public function generateText() {
        $report = [];
        $report[] = "============================================";
        $report[] = "  GRADE REPORT";
        $report[] = "  " . $this->assignmentTitle;
        $report[] = "  Generated: " . date('Y-m-d H:i:s');
        $report[] = "============================================";
        $report[] = "";
        $report[] = "SUMMARY STATISTICS";
        $report[] = "--------------------------------------------";
        $report[] = sprintf("Total Submissions:    %d", $this->stats['count']);
        $report[] = sprintf("Graded:               %d", $this->stats['graded']);
        $report[] = sprintf("Average Score:        %.2f", $this->stats['average']);
        $report[] = sprintf("Median Score:         %.2f", $this->stats['median']);
        $report[] = sprintf("Minimum Score:        %.2f", $this->stats['min']);
        $report[] = sprintf("Maximum Score:        %.2f", $this->stats['max']);
        $report[] = sprintf("Standard Deviation:   %.2f", $this->stats['std_dev']);
        $report[] = sprintf("Pass Rate (>=60%%):    %.1f%%", $this->stats['pass_rate']);
        $report[] = "";
        $report[] = "GRADE DISTRIBUTION";
        $report[] = "--------------------------------------------";
        
        foreach ($this->distribution as $grade => $count) {
            $bar = str_repeat('█', $count) . str_repeat('░', max(0, 20 - $count));
            $report[] = sprintf("%-15s %s %d", $grade, $bar, $count);
        }
        
        $report[] = "";
        $report[] = "INDIVIDUAL GRADES";
        $report[] = "--------------------------------------------";
        $report[] = sprintf("%-30s %-10s %s", "Student", "Score", "Status");
        $report[] = "--------------------------------------------";
        
        foreach ($this->grades as $grade) {
            $status = $grade['score'] === null ? 'Pending' : ($grade['score'] >= 60 ? 'Pass' : 'Fail');
            $score = $grade['score'] === null ? 'N/A' : $grade['score'];
            $report[] = sprintf("%-30s %-10s %s", 
                substr($grade['student_name'] ?? $grade['student_email'], 0, 28),
                $score,
                $status
            );
        }
        
        $report[] = "";
        $report[] = "============================================";
        $report[] = "  End of Report";
        $report[] = "============================================";
        
        return implode("\n", $report);
    }
    
    /**
     * Generate HTML report
     */
    public function generateHtml() {
        $html = '<!DOCTYPE html>
<html>
<head>
    <title>Grade Report - ' . htmlspecialchars($this->assignmentTitle) . '</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f5f5f5; }
        .container { max-width: 900px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        h1 { color: #333; border-bottom: 2px solid #4CAF50; padding-bottom: 10px; }
        h2 { color: #666; margin-top: 30px; }
        .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; margin: 20px 0; }
        .stat-card { background: #f8f9fa; padding: 15px; border-radius: 8px; text-align: center; }
        .stat-value { font-size: 24px; font-weight: bold; color: #4CAF50; }
        .stat-label { font-size: 12px; color: #666; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #4CAF50; color: white; }
        tr:hover { background: #f5f5f5; }
        .pass { color: #4CAF50; }
        .fail { color: #f44336; }
        .pending { color: #ff9800; }
        .bar { background: #e0e0e0; height: 20px; border-radius: 4px; overflow: hidden; }
        .bar-fill { background: #4CAF50; height: 100%; }
        .footer { text-align: center; color: #999; margin-top: 30px; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>📊 Grade Report</h1>
        <p><strong>Assignment:</strong> ' . htmlspecialchars($this->assignmentTitle) . '</p>
        <p><strong>Generated:</strong> ' . date('F j, Y g:i A') . '</p>
        
        <h2>Summary Statistics</h2>
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-value">' . $this->stats['count'] . '</div>
                <div class="stat-label">Total Submissions</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">' . $this->stats['average'] . '</div>
                <div class="stat-label">Average Score</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">' . $this->stats['median'] . '</div>
                <div class="stat-label">Median Score</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">' . $this->stats['pass_rate'] . '%</div>
                <div class="stat-label">Pass Rate</div>
            </div>
        </div>
        
        <h2>Grade Distribution</h2>
        <table>
            <tr><th>Grade</th><th>Count</th><th>Distribution</th></tr>';
        
        $maxCount = max($this->distribution) ?: 1;
        foreach ($this->distribution as $grade => $count) {
            $percentage = ($count / $maxCount) * 100;
            $html .= '<tr>
                <td>' . htmlspecialchars($grade) . '</td>
                <td>' . $count . '</td>
                <td><div class="bar"><div class="bar-fill" style="width: ' . $percentage . '%"></div></div></td>
            </tr>';
        }
        
        $html .= '</table>
        
        <h2>Individual Grades</h2>
        <table>
            <tr><th>Student</th><th>Email</th><th>Score</th><th>Status</th></tr>';
        
        foreach ($this->grades as $grade) {
            $score = $grade['score'] === null ? 'N/A' : $grade['score'];
            if ($grade['score'] === null) {
                $status = '<span class="pending">Pending</span>';
            } elseif ($grade['score'] >= 60) {
                $status = '<span class="pass">Pass</span>';
            } else {
                $status = '<span class="fail">Fail</span>';
            }
            
            $html .= '<tr>
                <td>' . htmlspecialchars($grade['student_name'] ?? 'Unknown') . '</td>
                <td>' . htmlspecialchars($grade['student_email'] ?? 'Unknown') . '</td>
                <td>' . $score . '</td>
                <td>' . $status . '</td>
            </tr>';
        }
        
        $html .= '</table>
        
        <div class="footer">
            Generated by Grade Report Generator - CS425 External Tool
        </div>
    </div>
</body>
</html>';
        
        return $html;
    }
    
    /**
     * Generate CSV report
     */
    public function generateCsv() {
        $output = fopen('php://temp', 'r+');
        
        // Header
        fputcsv($output, ['Student Name', 'Student Email', 'Score', 'Max Score', 'Percentage', 'Status', 'Feedback']);
        
        // Data
        foreach ($this->grades as $grade) {
            $score = $grade['score'] ?? '';
            $maxScore = $grade['max_score'] ?? 100;
            $percentage = $score !== '' ? round(($score / $maxScore) * 100, 1) . '%' : '';
            $status = $score === '' ? 'Pending' : ($score >= 60 ? 'Pass' : 'Fail');
            
            fputcsv($output, [
                $grade['student_name'] ?? 'Unknown',
                $grade['student_email'] ?? 'Unknown',
                $score,
                $maxScore,
                $percentage,
                $status,
                $grade['feedback'] ?? ''
            ]);
        }
        
        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);
        
        return $csv;
    }
}

/**
 * Main Application
 */
class GradeReportApp {
    private $api;
    
    public function __construct() {
        $this->api = new ApiClient(API_BASE_URL, COOKIE_FILE);
    }
    
    public function run($assignmentId = null, $format = 'text') {
        echo "Grade Report Generator\n";
        echo "======================\n\n";
        
        // Login
        echo "Logging in...\n";
        $loginResult = $this->api->login(INSTRUCTOR_EMAIL, INSTRUCTOR_PASSWORD);
        
        if ($loginResult['code'] !== 200 || !$loginResult['data']['success']) {
            echo "ERROR: Login failed!\n";
            return false;
        }
        echo "Login successful!\n\n";
        
        // Fetch grades
        echo "Fetching grades...\n";
        $params = [];
        if ($assignmentId) {
            $params['assignment_id'] = $assignmentId;
        }
        
        $gradesResult = $this->api->get('/bulk/export', $params);
        
        if ($gradesResult['code'] !== 200 || !$gradesResult['data']['success']) {
            echo "ERROR: Failed to fetch grades!\n";
            return false;
        }
        
        $grades = $gradesResult['data']['data']['grades'];
        echo "Found " . count($grades) . " grades.\n\n";
        
        // Get assignment title if specific assignment
        $assignmentTitle = 'All Assignments';
        if ($assignmentId) {
            $assignmentResult = $this->api->get('/assignments/' . $assignmentId);
            if ($assignmentResult['code'] === 200 && $assignmentResult['data']['success']) {
                $assignmentTitle = $assignmentResult['data']['data']['title'];
            }
        }
        
        // Generate report
        echo "Generating $format report...\n";
        $generator = new ReportGenerator($grades, $assignmentTitle);
        
        // Create output directory
        if (!is_dir(OUTPUT_DIR)) {
            mkdir(OUTPUT_DIR, 0755, true);
        }
        
        $timestamp = date('Y-m-d_His');
        
        switch ($format) {
            case 'html':
                $content = $generator->generateHtml();
                $filename = OUTPUT_DIR . "/grade_report_{$timestamp}.html";
                break;
            case 'csv':
                $content = $generator->generateCsv();
                $filename = OUTPUT_DIR . "/grade_report_{$timestamp}.csv";
                break;
            case 'text':
            default:
                $content = $generator->generateText();
                $filename = OUTPUT_DIR . "/grade_report_{$timestamp}.txt";
                // Also print to console
                echo "\n" . $content . "\n";
                break;
        }
        
        // Save to file
        file_put_contents($filename, $content);
        echo "\nReport saved to: $filename\n";
        
        return true;
    }
}

// ============================================
// Command Line Interface
// ============================================

if (php_sapi_name() !== 'cli') {
    die("This script must be run from the command line.\n");
}

$assignmentId = isset($argv[1]) && is_numeric($argv[1]) ? intval($argv[1]) : null;
$format = $argv[2] ?? 'text';

if (!in_array($format, ['text', 'html', 'csv'])) {
    echo "Invalid format. Use: text, html, or csv\n";
    exit(1);
}

$app = new GradeReportApp();
$success = $app->run($assignmentId, $format);

exit($success ? 0 : 1);
