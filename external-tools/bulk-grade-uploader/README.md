# Bulk Grade Uploader

## Overview

This External Tool allows instructors to upload multiple grades at once from a CSV file. It communicates with the Core System via the REST API.

## Requirements

- PHP 7.4 or higher
- cURL extension enabled
- Access to the Core System API

## Usage

```bash
php bulk_grade_uploader.php <csv_file> <assignment_id>
```

### Example

```bash
php bulk_grade_uploader.php sample_grades.csv 1
```

## CSV Format

The CSV file must have the following columns:

| Column | Required | Description |
|--------|----------|-------------|
| student_email | Yes | Student's email address |
| score | Yes | Numeric score |
| feedback | No | Text feedback for the student |

### Example CSV

```csv
student_email,score,feedback
alice@example.com,85,Good work!
bob@example.com,92,Excellent!
```

## Configuration

Edit the constants at the top of `bulk_grade_uploader.php`:

```php
define('API_BASE_URL', 'http://localhost:8080/api');
define('INSTRUCTOR_EMAIL', 'instructor@example.com');
define('INSTRUCTOR_PASSWORD', 'password123');
```

## TODO for Team B

This is a **template** - you should enhance it with:

- [ ] Configuration file support (don't hardcode credentials)
- [ ] Better error handling and validation
- [ ] Progress bar for large uploads
- [ ] Logging to file
- [ ] Dry-run mode to preview changes
- [ ] Support for updating existing grades
- [ ] Interactive mode for manual confirmation

## API Endpoints Used

- `POST /api/auth/login` - Authentication
- `POST /api/bulk/grades` - Bulk grade upload

See the [API Documentation](../../docs/API_DOCUMENTATION.md) for details.
