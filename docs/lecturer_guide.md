# Lecturer's Guide

## CS425 Assignment Grading System - Starter Codebase

---

## Table of Contents

1. [Introduction](#1-introduction)
2. [Team Structure: Provider-Consumer-API Model](#2-team-structure-provider-consumer-api-model)
3. [Setting Up for Distribution](#3-setting-up-for-distribution)
4. [Running Student Submissions](#4-running-student-submissions)
5. [Assessment Guide](#5-assessment-guide)
6. [Academic Integrity](#6-academic-integrity)
7. [Common Issues and Solutions](#7-common-issues-and-solutions)

---

## 1. Introduction

This document provides guidance for lecturers on setting up, distributing, running, and assessing student projects based on the CS425 Assignment Grading System starter codebase.

### Purpose of the Starter Codebase

The goal of this starter project is to shift the focus of the assignment from building a foundational application to **enhancing, optimizing, and managing an existing system**. This approach allows students to:

- Engage with a more realistic software engineering workflow
- Focus on **project management** as the primary learning objective
- Work with a **Provider-Consumer-API architecture** as required by the assignment
- Demonstrate collaboration between two teams working on interconnected systems

### What Students Will Demonstrate

- **Project Management (30%):** Planning, coordination, version control, team communication
- **Software Design (20%):** Code quality, architecture improvements, documentation
- **Infrastructure (15%):** Docker, deployment, environment configuration
- **Requirements (15%):** Feature implementation, API development
- **Testing (15%):** Test coverage, test documentation
- **Demonstration (5%):** Live demo of both systems working together

---

## 2. Team Structure: Provider-Consumer-API Model

### Architecture Overview

The assignment requires students to "create two systems, a provider and a consumer, that will communicate with each other via a third application – an API." This starter codebase fulfills this requirement through:

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

### Team Responsibilities

| Team | Role | What They Build | Key Deliverables |
|------|------|-----------------|------------------|
| **Team A** | Provider | Enhance the Core System | Improved web app, enhanced API, security, testing |
| **Team B** | Consumer | Build an External Tool | Standalone PHP tool that uses the API, containerized |

### How This Meets Assignment Requirements

| Requirement | How It's Fulfilled |
|-------------|-------------------|
| Two systems | Core System (Team A) + External Tool (Team B) |
| Provider and Consumer | Core System provides data/API; External Tool consumes it |
| API communication | External Tool communicates **only** via REST API |
| Containerization | Both systems run in separate Docker containers |
| Platform independence | Docker ensures consistent deployment across machines |

---

## 3. Setting Up for Distribution

### Option 1: Git Repository (Recommended)

1. **Create a Git repository** on GitHub, GitLab, or your institution's Git server

2. **Upload the starter code:**
   ```bash
   git init
   git add .
   git commit -m "Initial starter codebase"
   git remote add origin <your-repo-url>
   git push -u origin main
   ```

3. **Set repository permissions:**
   - Make the repository read-only (students should fork it)
   - Or create a template repository

4. **Instruct students to fork** the repository for their group

### Option 2: Direct Download

1. **Create a zip file** of the starter code
2. **Upload to your LMS** (Moodle, Canvas, etc.)
3. **Provide download link** to students

### Pre-Distribution Checklist

- [ ] Test Docker setup on a clean machine (Windows, Mac, Linux)
- [ ] Verify all default credentials work
- [ ] Ensure database seeds correctly
- [ ] Test API endpoints with Postman/cURL
- [ ] Test External Tool templates
- [ ] Review and customize assessment criteria if needed
- [ ] Update due dates in documentation

---

## 4. Running Student Submissions

### Prerequisites

- Docker Desktop installed
- Git installed (if using repositories)
- Web browser

### Step-by-Step: Running a Student Submission

1. **Obtain the submission:**
   ```bash
   # If using Git
   git clone <student-repo-url>
   cd <student-project>
   
   # If using zip file
   unzip student-submission.zip
   cd student-project
   ```

2. **Check for required files:**
   ```bash
   ls docker-compose.yml
   ls -la external-tools/
   ```

3. **Start the containers:**
   ```bash
   docker compose up -d
   ```

4. **Wait for initialization** (30-60 seconds for first run)

5. **Access the Core System:**
   - Web Application: http://localhost:8080
   - phpMyAdmin: http://localhost:8081

6. **Test the External Tool:**
   ```bash
   cd external-tools/<their-tool>
   php <their-tool>.php
   # Or if containerized:
   docker compose up external-tool
   ```

7. **Clean up after assessment:**
   ```bash
   docker compose down -v
   ```

### Running Multiple Submissions

To avoid port conflicts:

1. **Stop previous submission completely:**
   ```bash
   docker compose down -v
   ```

2. **Or modify ports** in student's `docker-compose.yml` before running

---

## 5. Assessment Guide

### Assessment Criteria (Revised Weights)

| Criterion | Weight | Focus |
|-----------|--------|-------|
| **Project Management** | **30%** | Primary focus - planning, coordination, documentation |
| Software Design | 20% | Code quality, architecture, API design |
| Infrastructure | 15% | Docker, deployment, containerization |
| Requirements | 15% | Feature implementation, functionality |
| Testing | 15% | Test coverage, documentation |
| Demonstration | 5% | Live demo of both systems |

### Detailed Rubric

#### Project Management (30%)

| Grade | Description |
|-------|-------------|
| **Excellent (27-30)** | Clear Git history with meaningful commits from all members, consistent branching strategy (e.g., GitFlow), comprehensive project board with task tracking, documented team meetings, clear role division between Team A and Team B, evidence of coordination between teams, timeline with milestones |
| **Good (21-26)** | Good Git usage, project tracking evident, reasonable documentation, some evidence of team coordination |
| **Satisfactory (15-20)** | Basic Git usage, minimal project tracking, limited documentation |
| **Poor (0-14)** | Minimal commits, no project tracking, no documentation |

**What to Look For:**
- Git log: `git log --graph --oneline --all`
- Branching strategy
- Project board screenshots (Trello, Jira, GitHub Projects)
- Meeting notes or decision logs
- Team role documentation
- Evidence of Team A/B coordination (e.g., API requirement discussions)

#### Software Design & Code Quality (20%)

| Grade | Description |
|-------|-------------|
| **Excellent (18-20)** | Clean, well-organized code following MVC pattern, comprehensive comments, clear API design, good error handling, security improvements implemented |
| **Good (14-17)** | Reasonably organized code, adequate comments, functional API |
| **Satisfactory (10-13)** | Basic organization, minimal comments, working functionality |
| **Poor (0-9)** | Disorganized code, no comments, broken functionality |

**What to Look For:**
- Code review of modified files
- API endpoint design
- Error handling
- Security improvements (input validation, authentication)
- Database schema improvements (indexes, constraints)

#### Infrastructure & Version Control (15%)

| Grade | Description |
|-------|-------------|
| **Excellent (14-15)** | Both Core System and External Tool properly containerized, clean Docker setup, environment variables used, easy deployment, External Tool has its own Dockerfile |
| **Good (11-13)** | Working Docker setup, some configuration, deployable |
| **Satisfactory (8-10)** | Basic Docker setup works, minimal configuration |
| **Poor (0-7)** | Docker doesn't work, manual setup required |

**What to Look For:**
- `docker-compose.yml` modifications
- External Tool containerization
- Environment variable usage
- Deployment documentation

#### Requirements Coverage (15%)

| Grade | Description |
|-------|-------------|
| **Excellent (14-15)** | All required features implemented, meaningful enhancements, API fully functional, External Tool adds real value and works correctly |
| **Good (11-13)** | Most features implemented, API works, External Tool functional |
| **Satisfactory (8-10)** | Basic features work, some API endpoints, basic External Tool |
| **Poor (0-7)** | Missing features, broken API, non-functional External Tool |

**What to Look For:**
- Core System enhancements
- API endpoint functionality
- External Tool usefulness
- Integration between systems

#### Testing (15%)

| Grade | Description |
|-------|-------------|
| **Excellent (14-15)** | Comprehensive test cases documented, automated tests where possible, evidence of testing process, API tests, External Tool tests |
| **Good (11-13)** | Good test coverage, documented test cases, some automation |
| **Satisfactory (8-10)** | Basic test cases, manual testing documented |
| **Poor (0-7)** | Minimal testing, no documentation |

**What to Look For:**
- `tests/` directory contents
- Test case documentation
- Automated tests (PHPUnit)
- API test results

#### Demonstration (5%)

| Grade | Description |
|-------|-------------|
| **Excellent (5)** | Smooth demo showing both Core System and External Tool working together, clear presentation, good Q&A responses, all team members contribute |
| **Good (4)** | Working demo, reasonable presentation |
| **Satisfactory (3)** | Demo works with minor issues |
| **Poor (0-2)** | Demo fails, poor presentation |

**What to Look For:**
- Both systems running in Docker
- External Tool successfully communicating with Core System via API
- Clear explanation of enhancements
- Team coordination evident

### Assessment Checklist

#### Core System (Team A)

- [ ] Application starts with `docker compose up -d`
- [ ] Login works with provided credentials
- [ ] Instructor can create/edit/delete assignments
- [ ] Student can view assignments and submit work
- [ ] Grading functionality works
- [ ] API endpoints respond correctly
- [ ] Code improvements are documented
- [ ] Security enhancements implemented

#### External Tool (Team B)

- [ ] Tool has its own documentation (README)
- [ ] Tool communicates **only** via API (no direct DB access)
- [ ] Tool is containerized (has Dockerfile)
- [ ] Tool provides useful functionality
- [ ] Error handling is implemented
- [ ] Code is well-commented

#### Project Management (Both Teams)

- [ ] Git repository has meaningful commit history
- [ ] Branching strategy is evident
- [ ] Project board/tracking tool was used
- [ ] Team roles are documented
- [ ] Meeting notes or decisions documented
- [ ] Timeline/milestones documented
- [ ] Evidence of Team A/B coordination

---

## 6. Academic Integrity

Since all students start from the same codebase, standard plagiarism detection tools may flag high similarity scores between submissions. Mitigate this by:

### Focus on Unique Contributions

- **Require a Statement of Work:** Each team must clearly document which enhancements they implemented
- **Review Git History:** The commit history provides a fingerprint of the team's work process
- **Focus on the "Why":** Students should explain their design choices in documentation

### Red Flags to Watch For

- Commits all made by one person
- Large commits with no meaningful messages
- External Tool that directly accesses the database (bypassing API)
- Identical enhancements between different groups
- Documentation that doesn't match the code

### Verification Steps

1. Check Git log for contribution patterns
2. Ask students to explain their code during demo
3. Verify External Tool uses API (check for database connection code)
4. Compare enhancement choices between groups

---

## 7. Common Issues and Solutions

### Docker Issues

**Issue:** Containers won't start
```bash
# Check logs
docker compose logs

# Rebuild
docker compose down -v
docker compose up -d --build
```

**Issue:** Port conflict
```bash
# Stop all containers
docker compose down

# Or change ports in docker-compose.yml
```

### Database Issues

**Issue:** Database not initialized
```bash
docker compose down -v
docker compose up -d
# Wait 60 seconds
```

### Student Submission Issues

**Issue:** Student submission won't run
1. Check if `docker-compose.yml` exists
2. Check if required files are present
3. Review student's README for custom instructions
4. Contact student for clarification

**Issue:** External Tool doesn't work
1. Ensure Core System is running first
2. Check API credentials in tool configuration
3. Verify API endpoints are accessible

---

## Quick Reference

### Default Credentials

| Role | Email | Password |
|------|-------|----------|
| Instructor | instructor@example.com | password123 |
| Student | alice@example.com | password123 |

### Default Ports

| Service | Port |
|---------|------|
| Web Application | 8080 |
| phpMyAdmin | 8081 |
| MySQL | 3307 |

### Useful Commands

```bash
# Start containers
docker compose up -d

# Stop containers
docker compose down

# Stop and remove data
docker compose down -v

# View logs
docker compose logs

# Check status
docker compose ps
```

---

## Support

For issues with the starter codebase or assessment guidance, please contact the course coordinator.
