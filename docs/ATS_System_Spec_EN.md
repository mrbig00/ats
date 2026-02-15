# ATS System -- Complete Functional Specification

This document contains the complete, unified functional specification of
the ATS (Applicant Tracking System).

Its purpose is to clearly define the system structure, logic, menus,
submenus, the relationships between modules, and preparation for future
integrations (e.g., Outlook).

## 1. Menu Structure (base language: German)

-   **Dashboard** -- Overview / control panel
-   **Candidates** -- Applicants
-   **Jobs** -- Job postings / positions
-   **Employees** -- Staff members
-   **Apartment / Room Management** -- Housing management
-   **To Do** -- Task management
-   **Meetings** -- Meetings / interviews
-   **Settings** -- Configuration

## 2. Dashboard

**Goal:** Provide a real-time HR and operational overview in a
centralized interface.

### Dashboard elements

-   KPI / activity chart (time-series -- weekly / monthly)
-   Number of new applicants
-   Number of active employees
-   Upcoming departures (next 30 days)
-   Number of free rooms (housing module)
-   Number of open job positions
-   Internal chat

### Activity KPI sources

-   new candidate
-   candidate status change
-   new employee
-   resignation / termination
-   new To Do
-   new meeting

### 2.1 FullCalendar on the Dashboard

The Dashboard contains an embedded **FullCalendar module**:

-   Interviews (Candidates module)
-   Internal meetings (Meetings module)
-   Entry and exit dates
-   Apartment / room availability dates ("Free from")

**Views:** monthly / weekly / daily\
Events are clickable.

### 2.2 To Do -- upcoming tasks

The Dashboard displays a To Do block containing upcoming and overdue
tasks, sorted by priority and deadline.

## 3. Candidates

**Goal:** Manage the entire recruitment process.

-   Candidate list (with pipeline statuses)
-   Short notes
-   Document management
-   Interviews

**Logic:** A candidate can be converted into an employee with one click.

## 4. Jobs

-   Active positions
-   Closed positions
-   Candidates per position

## 5. Employees

-   Active employees
-   Former employees
-   Contracts
-   Housing (housing module)

Automatic status update when termination occurs.

## 6. Apartment / Room Management

-   Apartment list
-   Rooms per apartment
-   Free rooms view

Automatic "Free from" status when termination occurs.

## 7. To Do

-   Personal tasks
-   Team tasks
-   Deadline-based tasks

## 8. Meetings

-   Interviews
-   Internal meetings
-   Notes

## 9. Settings

-   Users and permissions
-   Pipeline and status configuration
-   KPI configuration
-   Integrations

## 10. Outlook Integration -- Preparation

-   Email logging
-   Calendar synchronization
-   Unified communication

## 11. API Preparation for Future Integrations

**API-first approach:**

-   REST / JSON
-   Versioned endpoints
-   OAuth2 authentication
-   Permission handling
-   Webhooks

The API is initially intended for internal usage in documented form.
