# API Documentation

## CS425 Assignment Grading System - REST API Reference

This document provides comprehensive documentation for the REST API endpoints available in the Assignment Grading System. **Team B** should use this documentation to build External Tools that communicate with the Core System.

---

## Table of Contents

1. [Overview](#overview)
2. [Authentication](#authentication)
3. [Response Format](#response-format)
4. [Error Handling](#error-handling)
5. [Endpoints](#endpoints)
   - [Health Check](#health-check)
   - [Authentication](#authentication-endpoints)
   - [Assignments](#assignments)
   - [Submissions](#submissions)
   - [Grades](#grades)
   - [Users](#users)
   - [Rubrics](#rubrics)
   - [Bulk Operations](#bulk-operations)

---

## Overview

**Base URL:** `http://localhost:8080/api`

The API follows RESTful conventions and returns JSON responses. All requests should include appropriate headers:

```
Content-Type: application/json
Accept: application/json
```

---

## Authentication

Most endpoints require authentication. The API supports session-based authentication.

### Login Flow

1. Send credentials to `/api/auth/login`
2. Receive session cookie
3. Include cookie in subsequent requests

**Note for Team B:** When building External Tools, you may need to implement token-based authentication. This is a suggested enhancement for Team A.

---

## Response Format

All API responses follow this structure:

### Success Response

```json
{
    "success": true,
    "data": { ... },
    "timestamp": "2026-01-18T12:00:00+00:00"
}
```

### Error Response

```json
{
    "success": false,
    "error": "Error message description",
    "timestamp": "2026-01-18T12:00:00+00:00"
}
```

---

## Error Handling

| HTTP Status | Meaning |
|-------------|---------|
| 200 | Success |
| 201 | Created |
| 400 | Bad Request - Invalid input |
| 401 | Unauthorized - Authentication required |
| 403 | Forbidden - Insufficient permissions |
| 404 | Not Found - Resource doesn't exist |
| 405 | Method Not Allowed |
| 500 | Internal Server Error |

---

## Endpoints

### Health Check

Check if the API is running.

**Endpoint:** `GET /api/health`

**Authentication:** Not required

**Response:**
```json
{
    "success": true,
    "data": {
        "status": "ok",
        "version": "1.0.0"
    },
    "timestamp": "2026-01-18T12:00:00+00:00"
}
```

---

### Authentication Endpoints

#### Login

**Endpoint:** `POST /api/auth/login`

**Authentication:** Not required

**Request Body:**
```json
{
    "email": "instructor@example.com",
    "password": "password123"
}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "user": {
            "id": 1,
            "name": "Demo Instructor",
            "email": "instructor@example.com",
            "role": "instructor"
        },
        "message": "Login successful"
    },
    "timestamp": "2026-01-18T12:00:00+00:00"
}
```

#### Logout

**Endpoint:** `POST /api/auth/logout`

**Authentication:** Required

**Response:**
```json
{
    "success": true,
    "data": {
        "message": "Logout successful"
    },
    "timestamp": "2026-01-18T12:00:00+00:00"
}
```

#### Get Current User

**Endpoint:** `GET /api/auth/me`

**Authentication:** Required

**Response:**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "name": "Demo Instructor",
        "email": "instructor@example.com",
        "role": "instructor"
    },
    "timestamp": "2026-01-18T12:00:00+00:00"
}
```

---

### Assignments

#### List All Assignments

**Endpoint:** `GET /api/assignments`

**Authentication:** Required

**Response:**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "instructor_id": 1,
            "title": "Assignment 1",
            "description": "First assignment description",
            "due_date": "2026-02-01",
            "max_score": 100,
            "instructor_name": "Demo Instructor",
            "created_at": "2026-01-15T10:00:00"
        }
    ],
    "timestamp": "2026-01-18T12:00:00+00:00"
}
```

#### Get Single Assignment

**Endpoint:** `GET /api/assignments/{id}`

**Authentication:** Required

**Response:**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "instructor_id": 1,
        "title": "Assignment 1",
        "description": "First assignment description",
        "due_date": "2026-02-01",
        "max_score": 100,
        "instructor_name": "Demo Instructor",
        "created_at": "2026-01-15T10:00:00"
    },
    "timestamp": "2026-01-18T12:00:00+00:00"
}
```

#### Create Assignment

**Endpoint:** `POST /api/assignments`

**Authentication:** Required (Instructor only)

**Request Body:**
```json
{
    "title": "New Assignment",
    "description": "Assignment description",
    "due_date": "2026-02-15",
    "max_score": 100
}
```

**Response:** (201 Created)
```json
{
    "success": true,
    "data": {
        "id": 2,
        "message": "Assignment created successfully"
    },
    "timestamp": "2026-01-18T12:00:00+00:00"
}
```

#### Update Assignment

**Endpoint:** `PUT /api/assignments/{id}`

**Authentication:** Required (Instructor only)

**Request Body:**
```json
{
    "title": "Updated Assignment Title",
    "description": "Updated description",
    "due_date": "2026-02-20",
    "max_score": 100
}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "message": "Assignment updated successfully"
    },
    "timestamp": "2026-01-18T12:00:00+00:00"
}
```

#### Delete Assignment

**Endpoint:** `DELETE /api/assignments/{id}`

**Authentication:** Required (Instructor only)

**Response:**
```json
{
    "success": true,
    "data": {
        "message": "Assignment deleted successfully"
    },
    "timestamp": "2026-01-18T12:00:00+00:00"
}
```

---

### Submissions

#### List Submissions

**Endpoint:** `GET /api/submissions`

**Authentication:** Required

**Query Parameters:**
- `assignment_id` (optional): Filter by assignment

**Response:**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "student_id": 2,
            "assignment_id": 1,
            "file_path": "/uploads/submission1.pdf",
            "text_content": null,
            "submitted_at": "2026-01-17T14:30:00",
            "grade": 85,
            "feedback": "Good work!",
            "student_name": "Alice Student",
            "student_email": "alice@example.com"
        }
    ],
    "timestamp": "2026-01-18T12:00:00+00:00"
}
```

#### Get Single Submission

**Endpoint:** `GET /api/submissions/{id}`

**Authentication:** Required

**Response:**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "student_id": 2,
        "assignment_id": 1,
        "file_path": "/uploads/submission1.pdf",
        "text_content": null,
        "submitted_at": "2026-01-17T14:30:00",
        "grade": 85,
        "feedback": "Good work!",
        "student_name": "Alice Student",
        "assignment_title": "Assignment 1"
    },
    "timestamp": "2026-01-18T12:00:00+00:00"
}
```

#### Create Submission

**Endpoint:** `POST /api/submissions`

**Authentication:** Required (Student only)

**Request Body:**
```json
{
    "assignment_id": 1,
    "text_content": "My submission content..."
}
```

**Response:** (201 Created)
```json
{
    "success": true,
    "data": {
        "id": 2,
        "message": "Submission created successfully"
    },
    "timestamp": "2026-01-18T12:00:00+00:00"
}
```

---

### Grades

#### Get Grades

**Endpoint:** `GET /api/grades`

**Authentication:** Required

**Query Parameters:**
- `student_id` (optional): Filter by student
- `assignment_id` (optional): Filter by assignment

**Response:**
```json
{
    "success": true,
    "data": [
        {
            "submission_id": 1,
            "student_id": 2,
            "student_name": "Alice Student",
            "assignment_id": 1,
            "assignment_title": "Assignment 1",
            "grade": 85,
            "max_score": 100,
            "feedback": "Good work!",
            "graded_at": "2026-01-18T10:00:00"
        }
    ],
    "timestamp": "2026-01-18T12:00:00+00:00"
}
```

#### Update Grade

**Endpoint:** `PUT /api/grades/{submission_id}`

**Authentication:** Required (Instructor only)

**Request Body:**
```json
{
    "grade": 90,
    "feedback": "Excellent improvement!"
}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "message": "Grade updated successfully"
    },
    "timestamp": "2026-01-18T12:00:00+00:00"
}
```

---

### Users

#### List Users

**Endpoint:** `GET /api/users`

**Authentication:** Required (Instructor only)

**Query Parameters:**
- `role` (optional): Filter by role (`student` or `instructor`)

**Response:**
```json
{
    "success": true,
    "data": [
        {
            "id": 2,
            "name": "Alice Student",
            "email": "alice@example.com",
            "role": "student",
            "created_at": "2026-01-10T09:00:00"
        }
    ],
    "timestamp": "2026-01-18T12:00:00+00:00"
}
```

#### Get Single User

**Endpoint:** `GET /api/users/{id}`

**Authentication:** Required

**Response:**
```json
{
    "success": true,
    "data": {
        "id": 2,
        "name": "Alice Student",
        "email": "alice@example.com",
        "role": "student",
        "created_at": "2026-01-10T09:00:00"
    },
    "timestamp": "2026-01-18T12:00:00+00:00"
}
```

---

### Rubrics

#### Get Rubrics for Assignment

**Endpoint:** `GET /api/rubrics?assignment_id={id}`

**Authentication:** Required

**Response:**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "assignment_id": 1,
            "criterion_name": "Content Quality",
            "description": "Accuracy and depth of content",
            "max_points": 40
        },
        {
            "id": 2,
            "assignment_id": 1,
            "criterion_name": "Presentation",
            "description": "Organization and formatting",
            "max_points": 30
        }
    ],
    "timestamp": "2026-01-18T12:00:00+00:00"
}
```

#### Create Rubric Criterion

**Endpoint:** `POST /api/rubrics`

**Authentication:** Required (Instructor only)

**Request Body:**
```json
{
    "assignment_id": 1,
    "criterion_name": "Technical Accuracy",
    "description": "Correct implementation",
    "max_points": 30
}
```

**Response:** (201 Created)
```json
{
    "success": true,
    "data": {
        "id": 3,
        "message": "Rubric criterion created successfully"
    },
    "timestamp": "2026-01-18T12:00:00+00:00"
}
```

---

### Bulk Operations

These endpoints are designed for **External Tool integration** (Team B).

#### Bulk Grade Upload

Upload multiple grades at once.

**Endpoint:** `POST /api/bulk/grades`

**Authentication:** Required (Instructor only)

**Request Body (Option 1 - Using submission IDs):**
```json
{
    "grades": [
        {
            "submission_id": 1,
            "score": 85,
            "feedback": "Good work!"
        },
        {
            "submission_id": 2,
            "score": 92,
            "feedback": "Excellent!"
        }
    ]
}
```

**Request Body (Option 2 - Using student emails):**
```json
{
    "assignment_id": 1,
    "grades": [
        {
            "student_email": "alice@example.com",
            "score": 85,
            "feedback": "Good work!"
        },
        {
            "student_email": "bob@example.com",
            "score": 92,
            "feedback": "Excellent!"
        }
    ]
}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "total": 2,
        "success_count": 2,
        "failed_count": 0,
        "success": [
            {"index": 0, "submission_id": 1, "score": 85},
            {"index": 1, "submission_id": 2, "score": 92}
        ],
        "failed": []
    },
    "timestamp": "2026-01-18T12:00:00+00:00"
}
```

#### Bulk Student Import

Import multiple students at once.

**Endpoint:** `POST /api/bulk/students`

**Authentication:** Required (Instructor only)

**Request Body:**
```json
{
    "students": [
        {
            "name": "John Doe",
            "email": "john@example.com",
            "password": "password123"
        },
        {
            "name": "Jane Smith",
            "email": "jane@example.com",
            "password": "password123"
        }
    ]
}
```

**Response:** (201 Created)
```json
{
    "success": true,
    "data": {
        "total": 2,
        "success_count": 2,
        "failed_count": 0,
        "success": [
            {"index": 0, "user_id": 5, "email": "john@example.com"},
            {"index": 1, "user_id": 6, "email": "jane@example.com"}
        ],
        "failed": []
    },
    "timestamp": "2026-01-18T12:00:00+00:00"
}
```

#### Export Grades

Export all grades for reporting.

**Endpoint:** `GET /api/bulk/export`

**Authentication:** Required

**Query Parameters:**
- `assignment_id` (optional): Filter by assignment
- `format` (optional): `json` (default) or `csv`

**Response (JSON):**
```json
{
    "success": true,
    "data": {
        "count": 2,
        "grades": [
            {
                "submission_id": 1,
                "student_id": 2,
                "student_name": "Alice Student",
                "student_email": "alice@example.com",
                "assignment_id": 1,
                "assignment_title": "Assignment 1",
                "submitted_at": "2026-01-17T14:30:00",
                "score": 85,
                "max_score": 100,
                "feedback": "Good work!",
                "status": "graded"
            }
        ]
    },
    "timestamp": "2026-01-18T12:00:00+00:00"
}
```

**Response (CSV):** Returns a downloadable CSV file.

#### Get Statistics

Get system-wide statistics.

**Endpoint:** `GET /api/bulk/statistics`

**Authentication:** Required

**Response:**
```json
{
    "success": true,
    "data": {
        "total_students": 25,
        "total_instructors": 3,
        "total_assignments": 5,
        "total_submissions": 45,
        "graded_submissions": 30,
        "pending_submissions": 15,
        "average_score": 78.5
    },
    "timestamp": "2026-01-18T12:00:00+00:00"
}
```

---

## Example Usage with cURL

### Login
```bash
curl -X POST http://localhost:8080/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"instructor@example.com","password":"password123"}' \
  -c cookies.txt
```

### Get Assignments
```bash
curl -X GET http://localhost:8080/api/assignments \
  -H "Content-Type: application/json" \
  -b cookies.txt
```

### Bulk Grade Upload
```bash
curl -X POST http://localhost:8080/api/bulk/grades \
  -H "Content-Type: application/json" \
  -b cookies.txt \
  -d '{
    "assignment_id": 1,
    "grades": [
      {"student_email": "alice@example.com", "score": 85, "feedback": "Good!"},
      {"student_email": "bob@example.com", "score": 90, "feedback": "Excellent!"}
    ]
  }'
```

---

## Example Usage with PHP

```php
<?php
// Example: Fetch all assignments using the API

$baseUrl = 'http://localhost:8080/api';

// Login first
$loginData = [
    'email' => 'instructor@example.com',
    'password' => 'password123'
];

$ch = curl_init($baseUrl . '/auth/login');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($loginData));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIEJAR, '/tmp/cookies.txt');
$response = curl_exec($ch);
curl_close($ch);

// Get assignments
$ch = curl_init($baseUrl . '/assignments');
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIEFILE, '/tmp/cookies.txt');
$response = curl_exec($ch);
curl_close($ch);

$data = json_decode($response, true);
print_r($data);
```

---

## Notes for Team B (External Tool Development)

1. **Always check the `success` field** in responses before processing data.
2. **Handle errors gracefully** - display meaningful messages to users.
3. **Use bulk endpoints** for efficiency when processing multiple records.
4. **Store cookies/sessions** properly for authenticated requests.
5. **Validate data** before sending to the API.

---

## Suggested Enhancements (for Team A)

- [ ] Implement JWT token-based authentication
- [ ] Add rate limiting to prevent abuse
- [ ] Add pagination for list endpoints
- [ ] Implement API versioning (e.g., `/api/v1/`)
- [ ] Add request logging for debugging
- [ ] Implement webhook notifications
