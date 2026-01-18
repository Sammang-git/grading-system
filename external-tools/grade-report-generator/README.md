# Grade Report Generator

## Overview

This External Tool generates comprehensive grade reports from the Core System. It fetches data via the API and produces reports in multiple formats.

## Requirements

- PHP 7.4 or higher
- cURL extension enabled
- Access to the Core System API

## Usage

```bash
# Generate report for all assignments (text format)
php grade_report_generator.php

# Generate report for specific assignment
php grade_report_generator.php 1

# Generate HTML report
php grade_report_generator.php 1 html

# Generate CSV report
php grade_report_generator.php 1 csv
```

## Output Formats

| Format | Description |
|--------|-------------|
| text | Plain text report (default) - also prints to console |
| html | Styled HTML report with charts |
| csv | CSV file for spreadsheet import |

## Report Contents

- **Summary Statistics**: Count, average, median, min, max, standard deviation, pass rate
- **Grade Distribution**: A/B/C/D/F breakdown with visual bars
- **Individual Grades**: List of all students with scores and status

## Output Location

Reports are saved to the `reports/` subdirectory with timestamps:
- `reports/grade_report_2026-01-18_120000.txt`
- `reports/grade_report_2026-01-18_120000.html`
- `reports/grade_report_2026-01-18_120000.csv`

## Configuration

Edit the constants at the top of `grade_report_generator.php`:

```php
define('API_BASE_URL', 'http://localhost:8080/api');
define('INSTRUCTOR_EMAIL', 'instructor@example.com');
define('INSTRUCTOR_PASSWORD', 'password123');
```

## TODO for Team B

This is a **template** - you should enhance it with:

- [ ] PDF export using a library like TCPDF or FPDF
- [ ] Charts and visualizations (using Chart.js in HTML output)
- [ ] Email functionality to send reports
- [ ] Scheduled report generation
- [ ] Comparison reports (between assignments)
- [ ] Student-specific reports
- [ ] Configuration file support

## API Endpoints Used

- `POST /api/auth/login` - Authentication
- `GET /api/bulk/export` - Fetch all grades
- `GET /api/assignments/{id}` - Get assignment details

See the [API Documentation](../../docs/API_DOCUMENTATION.md) for details.
