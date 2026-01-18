<?php
/**
 * Bulk Operations API Endpoint
 * CS425 Assignment Grading System - Starter Codebase
 * 
 * Handles bulk operations for External Tool integration.
 * This endpoint is designed to be consumed by Team B's External Tools.
 * 
 * Available endpoints:
 * - POST /api/bulk/grades     - Upload multiple grades at once
 * - POST /api/bulk/students   - Import multiple students at once
 * - GET  /api/bulk/export     - Export all grades as JSON
 */

require_once dirname(__DIR__) . '/models/Submission.php';
require_once dirname(__DIR__) . '/models/User.php';
require_once dirname(__DIR__) . '/models/Assignment.php';
require_once dirname(__DIR__) . '/models/RubricGrade.php';

$submissionModel = new Submission();
$userModel = new User();
$assignmentModel = new Assignment();
$rubricGradeModel = new RubricGrade();

// Get the action from URL segments
$bulkAction = $id ?? '';

switch ($method) {
    case 'POST':
        apiAuth();
        
        if (!isInstructor()) {
            apiError('Only instructors can perform bulk operations', 403);
        }
        
        switch ($bulkAction) {
            case 'grades':
                /**
                 * Bulk Grade Upload
                 * 
                 * Expected JSON format:
                 * {
                 *   "grades": [
                 *     {
                 *       "submission_id": 1,
                 *       "score": 85,
                 *       "feedback": "Good work!"
                 *     },
                 *     ...
                 *   ]
                 * }
                 * 
                 * OR using student email and assignment:
                 * {
                 *   "assignment_id": 1,
                 *   "grades": [
                 *     {
                 *       "student_email": "student@example.com",
                 *       "score": 85,
                 *       "feedback": "Good work!"
                 *     },
                 *     ...
                 *   ]
                 * }
                 */
                if (empty($input['grades']) || !is_array($input['grades'])) {
                    apiError('Grades array is required');
                }
                
                $results = [
                    'success' => [],
                    'failed' => [],
                    'total' => count($input['grades'])
                ];
                
                $assignmentId = $input['assignment_id'] ?? null;
                
                foreach ($input['grades'] as $index => $gradeData) {
                    try {
                        $submissionId = null;
                        
                        // If submission_id is provided directly
                        if (!empty($gradeData['submission_id'])) {
                            $submissionId = intval($gradeData['submission_id']);
                        }
                        // If student_email and assignment_id are provided
                        elseif (!empty($gradeData['student_email']) && $assignmentId) {
                            $student = $userModel->findByEmail($gradeData['student_email']);
                            if ($student) {
                                $submission = $submissionModel->findByStudentAndAssignment(
                                    $student['id'], 
                                    $assignmentId
                                );
                                if ($submission) {
                                    $submissionId = $submission['id'];
                                }
                            }
                        }
                        
                        if (!$submissionId) {
                            $results['failed'][] = [
                                'index' => $index,
                                'data' => $gradeData,
                                'error' => 'Submission not found'
                            ];
                            continue;
                        }
                        
                        // Validate score
                        if (!isset($gradeData['score']) || !is_numeric($gradeData['score'])) {
                            $results['failed'][] = [
                                'index' => $index,
                                'data' => $gradeData,
                                'error' => 'Valid score is required'
                            ];
                            continue;
                        }
                        
                        // Update the submission with grade
                        $updated = $submissionModel->grade(
                            $submissionId,
                            floatval($gradeData['score']),
                            sanitize($gradeData['feedback'] ?? '')
                        );
                        
                        if ($updated) {
                            $results['success'][] = [
                                'index' => $index,
                                'submission_id' => $submissionId,
                                'score' => $gradeData['score']
                            ];
                        } else {
                            $results['failed'][] = [
                                'index' => $index,
                                'data' => $gradeData,
                                'error' => 'Failed to update grade'
                            ];
                        }
                    } catch (Exception $e) {
                        $results['failed'][] = [
                            'index' => $index,
                            'data' => $gradeData,
                            'error' => $e->getMessage()
                        ];
                    }
                }
                
                $results['success_count'] = count($results['success']);
                $results['failed_count'] = count($results['failed']);
                
                apiResponse($results);
                break;
                
            case 'students':
                /**
                 * Bulk Student Import
                 * 
                 * Expected JSON format:
                 * {
                 *   "students": [
                 *     {
                 *       "name": "John Doe",
                 *       "email": "john@example.com",
                 *       "password": "password123"
                 *     },
                 *     ...
                 *   ]
                 * }
                 */
                if (empty($input['students']) || !is_array($input['students'])) {
                    apiError('Students array is required');
                }
                
                $results = [
                    'success' => [],
                    'failed' => [],
                    'total' => count($input['students'])
                ];
                
                foreach ($input['students'] as $index => $studentData) {
                    try {
                        // Validate required fields
                        if (empty($studentData['name']) || empty($studentData['email'])) {
                            $results['failed'][] = [
                                'index' => $index,
                                'data' => $studentData,
                                'error' => 'Name and email are required'
                            ];
                            continue;
                        }
                        
                        // Check if email already exists
                        if ($userModel->findByEmail($studentData['email'])) {
                            $results['failed'][] = [
                                'index' => $index,
                                'data' => $studentData,
                                'error' => 'Email already exists'
                            ];
                            continue;
                        }
                        
                        // Create the student
                        $userId = $userModel->create([
                            'name' => sanitize($studentData['name']),
                            'email' => sanitize($studentData['email']),
                            'password' => $studentData['password'] ?? 'changeme123',
                            'role' => 'student'
                        ]);
                        
                        if ($userId) {
                            $results['success'][] = [
                                'index' => $index,
                                'user_id' => $userId,
                                'email' => $studentData['email']
                            ];
                        } else {
                            $results['failed'][] = [
                                'index' => $index,
                                'data' => $studentData,
                                'error' => 'Failed to create student'
                            ];
                        }
                    } catch (Exception $e) {
                        $results['failed'][] = [
                            'index' => $index,
                            'data' => $studentData,
                            'error' => $e->getMessage()
                        ];
                    }
                }
                
                $results['success_count'] = count($results['success']);
                $results['failed_count'] = count($results['failed']);
                
                apiResponse($results, 201);
                break;
                
            default:
                apiError('Unknown bulk action: ' . $bulkAction, 404);
        }
        break;
        
    case 'GET':
        apiAuth();
        
        switch ($bulkAction) {
            case 'export':
                /**
                 * Export All Grades
                 * 
                 * Query parameters:
                 * - assignment_id (optional): Filter by assignment
                 * - format (optional): 'json' (default) or 'csv'
                 * 
                 * Returns all grades with student and assignment information.
                 */
                $assignmentId = $_GET['assignment_id'] ?? null;
                $format = $_GET['format'] ?? 'json';
                
                // Get all submissions with grades
                if ($assignmentId) {
                    $submissions = $submissionModel->getByAssignment($assignmentId);
                } else {
                    $submissions = $submissionModel->getAllWithDetails();
                }
                
                $exportData = [];
                foreach ($submissions as $submission) {
                    $student = $userModel->findById($submission['student_id']);
                    $assignment = $assignmentModel->findById($submission['assignment_id']);
                    
                    $exportData[] = [
                        'submission_id' => $submission['id'],
                        'student_id' => $submission['student_id'],
                        'student_name' => $student['name'] ?? 'Unknown',
                        'student_email' => $student['email'] ?? 'Unknown',
                        'assignment_id' => $submission['assignment_id'],
                        'assignment_title' => $assignment['title'] ?? 'Unknown',
                        'submitted_at' => $submission['submitted_at'],
                        'score' => $submission['score'],
                        'max_score' => $assignment['max_score'] ?? 100,
                        'feedback' => $submission['feedback'],
                        'status' => $submission['status']
                    ];
                }
                
                if ($format === 'csv') {
                    // Return CSV format
                    header('Content-Type: text/csv');
                    header('Content-Disposition: attachment; filename="grades_export.csv"');
                    
                    $output = fopen('php://output', 'w');
                    
                    // Header row
                    if (!empty($exportData)) {
                        fputcsv($output, array_keys($exportData[0]));
                    }
                    
                    // Data rows
                    foreach ($exportData as $row) {
                        fputcsv($output, $row);
                    }
                    
                    fclose($output);
                    exit();
                }
                
                apiResponse([
                    'count' => count($exportData),
                    'grades' => $exportData
                ]);
                break;
                
            case 'statistics':
                /**
                 * Get Statistics
                 * 
                 * Returns summary statistics for the grading system.
                 */
                $stats = [
                    'total_students' => $userModel->countByRole('student'),
                    'total_instructors' => $userModel->countByRole('instructor'),
                    'total_assignments' => $assignmentModel->count(),
                    'total_submissions' => $submissionModel->count(),
                    'graded_submissions' => $submissionModel->countGraded(),
                    'pending_submissions' => $submissionModel->countPending(),
                    'average_score' => $submissionModel->getAverageScore()
                ];
                
                apiResponse($stats);
                break;
                
            default:
                apiError('Unknown bulk action: ' . $bulkAction, 404);
        }
        break;
        
    default:
        apiError('Method not allowed', 405);
}
