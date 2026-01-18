# CS425 Assignment Grading System

## Starter Codebase for Software Engineering & Project Management

This is a **lecturer-provided starter codebase** for the CS425 assignment. It provides a functional foundation that students will enhance, optimize, and extend as part of their assessment.

---

## Table of Contents

1. [Project Overview](#project-overview)
2. [Team Structure](#team-structure)
3. [Quick Start](#quick-start)
4. [Project Structure](#project-structure)
5. [For Team A: Core System Enhancement](#for-team-a-core-system-enhancement)
6. [For Team B: External Tool Development](#for-team-b-external-tool-development)
7. [API Documentation](#api-documentation)
8. [Assessment Criteria](#assessment-criteria)
9. [Troubleshooting](#troubleshooting)

---

## Project Overview

The Assignment Grading System is a web application that allows:
- **Instructors** to create assignments, define rubrics, and grade student submissions
- **Students** to view assignments, submit work, and view their grades
- **External Tools** to interact with the system via REST API

### Technology Stack

| Component | Technology |
|-----------|------------|
| Backend | PHP 8.x |
| Database | MySQL 8.0 |
| Frontend | HTML, CSS, JavaScript |
| Containerization | Docker, Docker Compose |
| API | RESTful JSON API |

### Architecture: Provider-Consumer-API Model

This project follows a **Provider-Consumer-API** architecture as required by the assignment:

```
┌─────────────────────────────────────────────────────────────┐
│                     DOCKER ENVIRONMENT                       │
├─────────────────────┬─────────────────────┬─────────────────┤
│   PROVIDER          │   CONSUMER          │   DATABASE      │
│   Core System       │   External Tool     │                 │
│   (Team A)          │   (Team B)          │                 │
│   ┌─────────────┐   │   ┌─────────────┐   │   ┌─────────┐   │
│   │ PHP/Apache  │   │   │ PHP CLI     │   │   │ MySQL   │   │
│   │ Web App     │◄──┼───│ Tool        │   │   │         │   │
│   │             │   │   │             │   │   │         │   │
│   │ REST API ◄──┼───┼───┤             │   │   │         │   │
│   │             │───┼───┼─────────────┼───┼──►│         │   │
│   └─────────────┘   │   └─────────────┘   │   └─────────┘   │
│   Port: 8080        │   (Separate         │   Port: 3307    │
│                     │    Container)       │                 │
└─────────────────────┴─────────────────────┴─────────────────┘
```

- **Provider (Core System):** Owns the data and business logic, exposes REST API
- **API:** The interface for communication between systems (`/api/*` endpoints)
- **Consumer (External Tool):** Uses the API to access and manipulate data

---

## Team Structure

Your group will be divided into two teams that work together:

### Team A: Core System Enhancement (Provider)

**Responsibility:** Enhance and optimize the provided Core System (this starter codebase).

**Focus Areas:**
- Improve existing functionality
- Add new API endpoints for Team B to consume
- Enhance security (authentication, input validation)
- Optimize database queries
- Improve UI/UX
- Add comprehensive testing

### Team B: External Tool Development (Consumer)

**Responsibility:** Build an External Tool that communicates with the Core System **only via the API**.

**Focus Areas:**
- Develop a standalone PHP application
- Consume the Core System's REST API
- Implement useful functionality (see templates in `/external-tools/`)
- Containerize the tool in its own Docker container
- Document the tool thoroughly

### Collaboration Requirements

- Team A and Team B must coordinate on API requirements
- Team B should request new API endpoints from Team A if needed
- Both teams share version control and project management responsibilities
- Final demo must show both systems working together

---

## Quick Start

### Prerequisites

- [Docker Desktop](https://www.docker.com/products/docker-desktop/) installed
- Git installed
- Text editor or IDE (VS Code recommended)

### Setup Instructions

1. **Extract the starter code** to a directory (e.g., `C:\cs425-starter` on Windows)

2. **Open a terminal** and navigate to the project:
   ```bash
   cd /path/to/cs425-starter
   ```

3. **Start the Docker containers:**
   ```bash
   docker compose up -d
   ```

4. **Wait for initialization** (first run takes 1-2 minutes)

5. **Access the application:**
   - **Web Application:** http://localhost:8080
   - **phpMyAdmin:** http://localhost:8081

### Default Login Credentials

| Role | Email | Password |
|------|-------|----------|
| Instructor | instructor@example.com | password123 |
| Student | alice@example.com | password123 |

### Stopping the Application

```bash
docker compose down
```

---

## Project Structure

```
cs425-starter/
├── app/                        # Main application code (PROVIDER)
│   ├── api/                    # REST API endpoints
│   │   ├── index.php           # API router
│   │   ├── assignments.php     # Assignments API
│   │   ├── submissions.php     # Submissions API
│   │   ├── grades.php          # Grades API
│   │   ├── bulk.php            # Bulk operations API
│   │   └── ...
│   ├── config/                 # Configuration files
│   ├── controllers/            # MVC Controllers
│   ├── models/                 # Database models
│   ├── views/                  # HTML templates
│   ├── assets/                 # CSS, JS, images
│   ├── instructor/             # Instructor pages
│   └── student/                # Student pages
├── database/
│   ├── schema.sql              # Database structure
│   └── seed.sql                # Sample data
├── docker/
│   └── Dockerfile              # PHP container config
├── docs/
│   ├── API_DOCUMENTATION.md    # Complete API reference
│   └── lecturer_guide.md       # Guide for lecturers
├── external-tools/             # CONSUMER templates (Team B)
│   ├── README.md               # Overview of all templates
│   ├── bulk-grade-uploader/    # Template: Grade uploader
│   ├── grade-report-generator/ # Template: Report generator
│   └── assignment-reminder/    # Template: Reminder tool
├── scripts/
│   ├── setup.sh                # Docker setup script
│   └── setup-xampp.sh          # XAMPP setup script
├── tests/
│   ├── api_tests.md            # API test cases
│   └── manual_tests.md         # Manual test cases
├── docker-compose.yml          # Docker configuration
├── .env.example                # Environment variables template
└── README.md                   # This file
```

---

## For Team A: Core System Enhancement

### Your Tasks

1. **Review the existing code** - Understand the architecture and identify areas for improvement

2. **Enhance the API** - Add new endpoints that Team B needs, improve authentication

3. **Improve Security:**
   - [ ] Implement JWT token authentication for API
   - [ ] Add input validation and sanitization
   - [ ] Implement CSRF protection for web forms
   - [ ] Add rate limiting to prevent abuse

4. **Optimize Performance:**
   - [ ] Add database indexing to `schema.sql`
   - [ ] Implement query caching
   - [ ] Optimize slow SQL queries
   - [ ] Add pagination to list endpoints

5. **Improve UI/UX:**
   - [ ] Enhance the dashboard with charts
   - [ ] Add client-side form validation
   - [ ] Improve error messages and feedback
   - [ ] Add loading indicators for AJAX requests

6. **Add Testing:**
   - [ ] Write PHPUnit tests for models
   - [ ] Create API integration tests
   - [ ] Document all test cases

### Key Files to Modify

| File | Purpose |
|------|---------|
| `app/api/*.php` | API endpoints - add new endpoints, improve security |
| `app/models/*.php` | Database models - add validation, optimize queries |
| `app/controllers/*.php` | Business logic - add error handling |
| `app/views/*.php` | UI templates - improve design |
| `database/schema.sql` | Database - add indexes, constraints |

### Coordination with Team B

Team A should:
- Document all API changes clearly
- Notify Team B of any breaking changes
- Respond to API endpoint requests from Team B
- Ensure API stability for Team B's development

---

## For Team B: External Tool Development

### Your Tasks

1. **Choose a tool type** from the templates or propose your own idea

2. **Study the API documentation** in `docs/API_DOCUMENTATION.md`

3. **Develop your tool** using the templates as a starting point

4. **Containerize your tool** with its own Dockerfile

5. **Document your tool** thoroughly

### Available Templates

| Template | Location | Description |
|----------|----------|-------------|
| Bulk Grade Uploader | `external-tools/bulk-grade-uploader/` | Upload grades from CSV file |
| Grade Report Generator | `external-tools/grade-report-generator/` | Generate grade reports (text/HTML/CSV) |
| Assignment Reminder | `external-tools/assignment-reminder/` | Check deadlines and send reminders |

### External Tool Requirements

Your External Tool **must**:

1. **Communicate only via API** - No direct database access allowed
2. **Be containerized** - Run in its own Docker container
3. **Handle errors gracefully** - Provide meaningful error messages
4. **Be well-documented** - Include README and code comments
5. **Add value** - Provide useful functionality beyond the templates

### Creating Your Tool's Docker Container

Create a `Dockerfile` in your tool's directory:

```dockerfile
FROM php:8.1-cli

# Install required extensions
RUN apt-get update && apt-get install -y libcurl4-openssl-dev \
    && docker-php-ext-install curl

WORKDIR /app
COPY . .

CMD ["php", "your_tool.php"]
```

Add your tool to the project's `docker-compose.yml`:

```yaml
  external-tool:
    build: ./external-tools/your-tool
    depends_on:
      - app
    networks:
      - grading-network
```

### Testing Your Tool

```bash
# Start the Core System
docker compose up -d

# Run your tool (from external-tools directory)
cd external-tools/your-tool
php your_tool.php
```

### Coordination with Team A

Team B should:
- Review the API documentation before starting
- Request new API endpoints from Team A if needed
- Report any API issues to Team A
- Test thoroughly with the Core System

---

## API Documentation

Complete API documentation is available at:

📄 **[docs/API_DOCUMENTATION.md](docs/API_DOCUMENTATION.md)**

### Quick API Reference

| Endpoint | Method | Description |
|----------|--------|-------------|
| `/api/health` | GET | Health check |
| `/api/auth/login` | POST | User login |
| `/api/auth/logout` | POST | User logout |
| `/api/assignments` | GET | List assignments |
| `/api/assignments/{id}` | GET | Get single assignment |
| `/api/submissions` | GET | List submissions |
| `/api/submissions` | POST | Create submission |
| `/api/grades` | GET | Get grades |
| `/api/bulk/grades` | POST | Bulk grade upload |
| `/api/bulk/export` | GET | Export all grades |
| `/api/bulk/statistics` | GET | Get system statistics |

### Testing the API

Use cURL or Postman to test:

```bash
# Health check
curl http://localhost:8080/api/health

# Login
curl -X POST http://localhost:8080/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"instructor@example.com","password":"password123"}' \
  -c cookies.txt

# Get assignments (with session cookie)
curl http://localhost:8080/api/assignments -b cookies.txt
```

---

## Assessment Criteria

Your work will be assessed based on:

| Criteria | Weight | Description |
|----------|--------|-------------|
| **Project Management** | **30%** | Planning, communication, version control, team coordination |
| Software Design | 20% | Code quality, architecture, documentation |
| Infrastructure | 15% | Docker, deployment, environment setup |
| Requirements | 15% | Feature completeness, functionality |
| Testing | 15% | Test coverage, test documentation |
| Demonstration | 5% | Live demo showing both systems working together |

### Project Management Expectations

- Use Git with meaningful commit messages and branching strategy
- Use project management tools (Trello, Jira, GitHub Projects)
- Document team meetings and decisions
- Create and follow a project timeline
- Demonstrate clear role division between Team A and Team B
- Show evidence of coordination between teams

---

## Troubleshooting

### Docker Issues

**Container won't start:**
```bash
# Check container status
docker compose ps

# View logs
docker compose logs

# Rebuild containers
docker compose down
docker compose up -d --build
```

**Port already in use:**
- Change ports in `docker-compose.yml`
- Or stop conflicting services

### Database Issues

**Can't connect to database:**
- Wait 30 seconds after starting containers
- Check if MySQL container is running: `docker compose ps`
- Access phpMyAdmin at http://localhost:8081

**Reset database:**
```bash
docker compose down -v
docker compose up -d
```

### API Issues

**401 Unauthorized:**
- Login first via `/api/auth/login`
- Include session cookie in subsequent requests

**404 Not Found:**
- Check endpoint URL spelling
- Ensure the API router is working

### XAMPP Alternative

If Docker doesn't work on your machine, see `scripts/setup-xampp.sh` for XAMPP setup instructions.

---

## Support

- Review the [API Documentation](docs/API_DOCUMENTATION.md)
- Check the [External Tools README](external-tools/README.md)
- Consult your instructor during lab sessions

---

## License

This starter codebase is provided for educational purposes as part of the CS425 course.

---

**Good luck with your assignment!** 🎓
