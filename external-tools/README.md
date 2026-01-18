# External Tools - Team B Templates

This directory contains **template External Tools** for Team B to build upon. These tools demonstrate how to communicate with the Core System via the REST API.

## Overview

| Tool | Description | Complexity |
|------|-------------|------------|
| [Bulk Grade Uploader](./bulk-grade-uploader/) | Upload grades from CSV file | Low |
| [Grade Report Generator](./grade-report-generator/) | Generate grade reports in multiple formats | Medium |
| [Assignment Reminder](./assignment-reminder/) | Check deadlines and send reminders | Medium |

## How to Use These Templates

1. **Choose a tool** that matches your team's assignment
2. **Read the README** in the tool's directory
3. **Study the code** to understand the API communication pattern
4. **Enhance the tool** with the TODO items listed
5. **Test thoroughly** with the Core System

## Common Patterns

All tools follow a similar structure:

```php
// 1. Configuration
define('API_BASE_URL', 'http://localhost:8080/api');

// 2. API Client class for HTTP requests
class ApiClient {
    public function get($endpoint) { ... }
    public function post($endpoint, $data) { ... }
    public function login($email, $password) { ... }
}

// 3. Business logic class
class MyTool {
    public function process() { ... }
}

// 4. Main application
$app = new MyTool();
$app->run();
```

## API Documentation

See the [API Documentation](../docs/API_DOCUMENTATION.md) for complete endpoint reference.

## Key API Endpoints for External Tools

| Endpoint | Method | Description |
|----------|--------|-------------|
| `/api/auth/login` | POST | Authenticate and get session |
| `/api/assignments` | GET | List all assignments |
| `/api/submissions` | GET | List submissions |
| `/api/grades` | GET | Get grades |
| `/api/bulk/grades` | POST | Upload multiple grades |
| `/api/bulk/export` | GET | Export all grades |
| `/api/bulk/statistics` | GET | Get system statistics |

## Testing Your External Tool

1. **Start the Core System:**
   ```bash
   cd /path/to/cs425-starter
   docker compose up -d
   ```

2. **Run your tool:**
   ```bash
   cd external-tools/your-tool
   php your_tool.php
   ```

3. **Verify in Core System:**
   - Open http://localhost:8080
   - Login and check if changes were applied

## Dockerizing Your External Tool

Team B should containerize their External Tool. Example Dockerfile:

```dockerfile
FROM php:8.1-cli

# Install curl extension
RUN apt-get update && apt-get install -y libcurl4-openssl-dev \
    && docker-php-ext-install curl

WORKDIR /app
COPY . .

CMD ["php", "your_tool.php"]
```

## Assessment Criteria

Your External Tool will be assessed on:

1. **Functionality** - Does it work correctly with the API?
2. **Error Handling** - Does it handle errors gracefully?
3. **Code Quality** - Is the code well-organized and documented?
4. **Enhancements** - Did you add meaningful improvements?
5. **Containerization** - Can it run in Docker?
6. **Documentation** - Is it well-documented?

## Need Help?

- Check the [API Documentation](../docs/API_DOCUMENTATION.md)
- Review the template code comments
- Test with cURL first to understand the API
- Ask your instructor for clarification
