<?php
/**
 * Assignment Reminder Tool - External Tool Template
 * CS425 Assignment Grading System
 * 
 * This is a TEMPLATE for Team B to build upon.
 * It demonstrates how to:
 * - Fetch assignments from the Core System API
 * - Check for upcoming deadlines
 * - Generate reminder notifications
 * 
 * Usage: php assignment_reminder.php [days]
 * 
 * TODO for Team B:
 * - Add email notifications
 * - Add SMS notifications
 * - Add scheduling (cron job integration)
 * - Add student-specific reminders
 * - Add configuration file
 */

// Configuration - TODO: Move to config file
define('API_BASE_URL', 'http://localhost:8080/api');
define('INSTRUCTOR_EMAIL', 'instructor@example.com');
define('INSTRUCTOR_PASSWORD', 'password123');
define('COOKIE_FILE', '/tmp/reminder_cookies.txt');

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
 * Reminder Generator
 */
class ReminderGenerator {
    
    /**
     * Categorize assignments by urgency
     */
    public static function categorize($assignments, $daysThreshold = 7) {
        $now = new DateTime();
        $categories = [
            'overdue' => [],
            'due_today' => [],
            'due_tomorrow' => [],
            'due_this_week' => [],
            'upcoming' => []
        ];
        
        foreach ($assignments as $assignment) {
            $dueDate = new DateTime($assignment['due_date']);
            $diff = $now->diff($dueDate);
            $daysUntilDue = $diff->invert ? -$diff->days : $diff->days;
            
            $assignment['days_until_due'] = $daysUntilDue;
            $assignment['due_date_formatted'] = $dueDate->format('l, F j, Y');
            
            if ($daysUntilDue < 0) {
                $categories['overdue'][] = $assignment;
            } elseif ($daysUntilDue === 0) {
                $categories['due_today'][] = $assignment;
            } elseif ($daysUntilDue === 1) {
                $categories['due_tomorrow'][] = $assignment;
            } elseif ($daysUntilDue <= $daysThreshold) {
                $categories['due_this_week'][] = $assignment;
            } else {
                $categories['upcoming'][] = $assignment;
            }
        }
        
        return $categories;
    }
    
    /**
     * Generate text reminder
     */
    public static function generateTextReminder($categories) {
        $lines = [];
        $lines[] = "╔══════════════════════════════════════════════════════════╗";
        $lines[] = "║           ASSIGNMENT DEADLINE REMINDERS                   ║";
        $lines[] = "║           Generated: " . date('Y-m-d H:i:s') . "              ║";
        $lines[] = "╚══════════════════════════════════════════════════════════╝";
        $lines[] = "";
        
        // Overdue (Critical)
        if (!empty($categories['overdue'])) {
            $lines[] = "🚨 OVERDUE ASSIGNMENTS (" . count($categories['overdue']) . ")";
            $lines[] = str_repeat("─", 60);
            foreach ($categories['overdue'] as $a) {
                $lines[] = "  ❌ " . $a['title'];
                $lines[] = "     Due: " . $a['due_date_formatted'] . " (" . abs($a['days_until_due']) . " days ago)";
            }
            $lines[] = "";
        }
        
        // Due Today (Urgent)
        if (!empty($categories['due_today'])) {
            $lines[] = "⚠️  DUE TODAY (" . count($categories['due_today']) . ")";
            $lines[] = str_repeat("─", 60);
            foreach ($categories['due_today'] as $a) {
                $lines[] = "  🔴 " . $a['title'];
                $lines[] = "     Due: " . $a['due_date_formatted'];
            }
            $lines[] = "";
        }
        
        // Due Tomorrow
        if (!empty($categories['due_tomorrow'])) {
            $lines[] = "📅 DUE TOMORROW (" . count($categories['due_tomorrow']) . ")";
            $lines[] = str_repeat("─", 60);
            foreach ($categories['due_tomorrow'] as $a) {
                $lines[] = "  🟠 " . $a['title'];
                $lines[] = "     Due: " . $a['due_date_formatted'];
            }
            $lines[] = "";
        }
        
        // Due This Week
        if (!empty($categories['due_this_week'])) {
            $lines[] = "📆 DUE THIS WEEK (" . count($categories['due_this_week']) . ")";
            $lines[] = str_repeat("─", 60);
            foreach ($categories['due_this_week'] as $a) {
                $lines[] = "  🟡 " . $a['title'];
                $lines[] = "     Due: " . $a['due_date_formatted'] . " (" . $a['days_until_due'] . " days)";
            }
            $lines[] = "";
        }
        
        // Upcoming
        if (!empty($categories['upcoming'])) {
            $lines[] = "📋 UPCOMING ASSIGNMENTS (" . count($categories['upcoming']) . ")";
            $lines[] = str_repeat("─", 60);
            foreach ($categories['upcoming'] as $a) {
                $lines[] = "  🟢 " . $a['title'];
                $lines[] = "     Due: " . $a['due_date_formatted'] . " (" . $a['days_until_due'] . " days)";
            }
            $lines[] = "";
        }
        
        // Summary
        $total = count($categories['overdue']) + count($categories['due_today']) + 
                 count($categories['due_tomorrow']) + count($categories['due_this_week']) +
                 count($categories['upcoming']);
        $urgent = count($categories['overdue']) + count($categories['due_today']) + count($categories['due_tomorrow']);
        
        $lines[] = str_repeat("═", 60);
        $lines[] = "SUMMARY";
        $lines[] = "  Total Assignments: $total";
        $lines[] = "  Urgent (Due within 2 days): $urgent";
        $lines[] = str_repeat("═", 60);
        
        return implode("\n", $lines);
    }
    
    /**
     * Generate HTML reminder (for email)
     */
    public static function generateHtmlReminder($categories) {
        $html = '<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; }
        h1 { color: #333; border-bottom: 2px solid #4CAF50; }
        .category { margin: 20px 0; padding: 15px; border-radius: 8px; }
        .overdue { background: #ffebee; border-left: 4px solid #f44336; }
        .today { background: #fff3e0; border-left: 4px solid #ff9800; }
        .tomorrow { background: #fff8e1; border-left: 4px solid #ffc107; }
        .week { background: #e3f2fd; border-left: 4px solid #2196f3; }
        .upcoming { background: #e8f5e9; border-left: 4px solid #4CAF50; }
        .assignment { margin: 10px 0; padding: 10px; background: white; border-radius: 4px; }
        .title { font-weight: bold; }
        .due { color: #666; font-size: 14px; }
    </style>
</head>
<body>
    <h1>📚 Assignment Deadline Reminders</h1>
    <p>Generated: ' . date('F j, Y g:i A') . '</p>';
        
        if (!empty($categories['overdue'])) {
            $html .= '<div class="category overdue"><h3>🚨 Overdue</h3>';
            foreach ($categories['overdue'] as $a) {
                $html .= '<div class="assignment"><div class="title">' . htmlspecialchars($a['title']) . '</div>';
                $html .= '<div class="due">Was due: ' . $a['due_date_formatted'] . '</div></div>';
            }
            $html .= '</div>';
        }
        
        if (!empty($categories['due_today'])) {
            $html .= '<div class="category today"><h3>⚠️ Due Today</h3>';
            foreach ($categories['due_today'] as $a) {
                $html .= '<div class="assignment"><div class="title">' . htmlspecialchars($a['title']) . '</div>';
                $html .= '<div class="due">Due: Today!</div></div>';
            }
            $html .= '</div>';
        }
        
        if (!empty($categories['due_tomorrow'])) {
            $html .= '<div class="category tomorrow"><h3>📅 Due Tomorrow</h3>';
            foreach ($categories['due_tomorrow'] as $a) {
                $html .= '<div class="assignment"><div class="title">' . htmlspecialchars($a['title']) . '</div>';
                $html .= '<div class="due">Due: ' . $a['due_date_formatted'] . '</div></div>';
            }
            $html .= '</div>';
        }
        
        if (!empty($categories['due_this_week'])) {
            $html .= '<div class="category week"><h3>📆 Due This Week</h3>';
            foreach ($categories['due_this_week'] as $a) {
                $html .= '<div class="assignment"><div class="title">' . htmlspecialchars($a['title']) . '</div>';
                $html .= '<div class="due">Due: ' . $a['due_date_formatted'] . ' (' . $a['days_until_due'] . ' days)</div></div>';
            }
            $html .= '</div>';
        }
        
        if (!empty($categories['upcoming'])) {
            $html .= '<div class="category upcoming"><h3>📋 Upcoming</h3>';
            foreach ($categories['upcoming'] as $a) {
                $html .= '<div class="assignment"><div class="title">' . htmlspecialchars($a['title']) . '</div>';
                $html .= '<div class="due">Due: ' . $a['due_date_formatted'] . ' (' . $a['days_until_due'] . ' days)</div></div>';
            }
            $html .= '</div>';
        }
        
        $html .= '</body></html>';
        return $html;
    }
}

/**
 * Main Application
 */
class AssignmentReminderApp {
    private $api;
    
    public function __construct() {
        $this->api = new ApiClient(API_BASE_URL, COOKIE_FILE);
    }
    
    public function run($daysThreshold = 7) {
        echo "Assignment Reminder Tool\n";
        echo "========================\n\n";
        
        // Login
        echo "Logging in...\n";
        $loginResult = $this->api->login(INSTRUCTOR_EMAIL, INSTRUCTOR_PASSWORD);
        
        if ($loginResult['code'] !== 200 || !$loginResult['data']['success']) {
            echo "ERROR: Login failed!\n";
            return false;
        }
        echo "Login successful!\n\n";
        
        // Fetch assignments
        echo "Fetching assignments...\n";
        $assignmentsResult = $this->api->get('/assignments');
        
        if ($assignmentsResult['code'] !== 200 || !$assignmentsResult['data']['success']) {
            echo "ERROR: Failed to fetch assignments!\n";
            return false;
        }
        
        $assignments = $assignmentsResult['data']['data'];
        echo "Found " . count($assignments) . " assignments.\n\n";
        
        // Categorize and generate reminder
        $categories = ReminderGenerator::categorize($assignments, $daysThreshold);
        $reminder = ReminderGenerator::generateTextReminder($categories);
        
        echo $reminder . "\n";
        
        // Save HTML version
        $htmlReminder = ReminderGenerator::generateHtmlReminder($categories);
        $htmlFile = __DIR__ . '/reminder_' . date('Y-m-d') . '.html';
        file_put_contents($htmlFile, $htmlReminder);
        echo "\nHTML reminder saved to: $htmlFile\n";
        
        // TODO: Send email notifications
        // $this->sendEmailReminders($categories);
        
        return true;
    }
    
    /**
     * TODO: Implement email sending
     */
    private function sendEmailReminders($categories) {
        // This is a placeholder for email functionality
        // Team B should implement this using PHP's mail() function
        // or a library like PHPMailer
        
        // Example:
        // $to = "student@example.com";
        // $subject = "Assignment Deadline Reminder";
        // $body = ReminderGenerator::generateHtmlReminder($categories);
        // $headers = "Content-Type: text/html; charset=UTF-8\r\n";
        // mail($to, $subject, $body, $headers);
    }
}

// ============================================
// Command Line Interface
// ============================================

if (php_sapi_name() !== 'cli') {
    die("This script must be run from the command line.\n");
}

$daysThreshold = isset($argv[1]) ? intval($argv[1]) : 7;

$app = new AssignmentReminderApp();
$success = $app->run($daysThreshold);

exit($success ? 0 : 1);
