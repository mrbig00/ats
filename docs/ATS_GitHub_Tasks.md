# ATS System — GitHub task list (from ATS_System_Spec_EN.md)

Use this list to create hierarchical issues. **To have the agent create them via GitHub MCP, provide your repository `owner/repo` (e.g. `myorg/ats`).**

Suggested labels (create in repo if missing): `epic`, `module:dashboard`, `module:candidates`, `module:jobs`, `module:employees`, `module:housing`, `module:todo`, `module:meetings`, `module:settings`, `integration:outlook`, `integration:api`, `spec`, `ui`, `backend`, `i18n`.

---

## 1. Epic: Menu structure & application shell

**Labels:** `epic`, `ui`, `i18n`  
**Description:** Implement the main application shell and navigation as defined in the spec (base language: German). All main modules must be reachable from a single, consistent menu.

### 1.1 Implement application shell with base navigation
**Labels:** `ui`, `spec`  
**Description:** Build the main layout (sidebar or top nav) with entries: Dashboard, Candidates, Jobs, Employees, Apartment/Room Management, To Do, Meetings, Settings. Use named routes and ensure responsive behavior.

### 1.2 Base language (German) for menu labels
**Labels:** `i18n`, `spec`  
**Description:** All menu and submenu labels must be available in German as the base language. Add translation keys and lang files (or equivalent) for: Dashboard, Candidates, Jobs, Employees, Apartment/Room Management, To Do, Meetings, Settings.

---

## 2. Epic: Dashboard

**Labels:** `epic`, `module:dashboard`, `ui`, `backend`  
**Description:** Provide a real-time HR and operational overview in a centralized interface: KPIs, activity chart, key metrics, FullCalendar, To Do block, and internal chat.

### 2.1 KPI / activity chart (time-series)
**Labels:** `module:dashboard`, `ui`, `backend`  
**Description:** Implement a time-series activity chart on the Dashboard with weekly and monthly views. Data sources: new candidate, candidate status change, new employee, resignation/termination, new To Do, new meeting. Persist and aggregate events for charting.

### 2.2 Dashboard metric: new applicants count
**Labels:** `module:dashboard`, `backend`  
**Description:** Display the number of new applicants (configurable period, e.g. last 7/30 days) on the Dashboard. Use efficient queries and cache if appropriate.

### 2.3 Dashboard metric: active employees count
**Labels:** `module:dashboard`, `backend`  
**Description:** Display the current number of active employees on the Dashboard. Align with the Employees module definition of "active".

### 2.4 Dashboard metric: upcoming departures (next 30 days)
**Labels:** `module:dashboard`, `backend`  
**Description:** Show count and/or list of employees with departure/termination date within the next 30 days. Link to Employees where relevant.

### 2.5 Dashboard metric: free rooms count
**Labels:** `module:dashboard`, `backend`, `module:housing`  
**Description:** Display the number of free rooms from the Apartment/Room Management module. Ensure consistency with the housing "Free from" logic.

### 2.6 Dashboard metric: open job positions count
**Labels:** `module:dashboard`, `backend`, `module:jobs`  
**Description:** Display the number of open/active job positions. Reuse Jobs module data and definitions.

### 2.7 Internal chat on Dashboard
**Labels:** `module:dashboard`, `ui`, `backend`  
**Description:** Implement an internal chat block/widget on the Dashboard. Define scope (e.g. team-wide or per context), persistence, and real-time or polling behavior.

### 2.8 FullCalendar on Dashboard
**Labels:** `module:dashboard`, `ui`, `backend`  
**Description:** Embed a FullCalendar (or equivalent) on the Dashboard showing: interviews (Candidates), internal meetings (Meetings), entry and exit dates, apartment/room "Free from" dates. Support monthly, weekly, and daily views; events must be clickable and link to the related entity.

### 2.9 To Do block on Dashboard (upcoming tasks)
**Labels:** `module:dashboard`, `module:todo`, `ui`  
**Description:** Display a To Do block on the Dashboard with upcoming and overdue tasks, sorted by priority and deadline. Reuse To Do module data; consider permission/visibility (e.g. personal vs team).

---

## 3. Epic: Candidates

**Labels:** `epic`, `module:candidates`, `ui`, `backend`  
**Description:** Manage the entire recruitment process: candidate list with pipeline statuses, notes, documents, interviews, and one-click conversion to employee.

### 3.1 Candidate list with pipeline statuses
**Labels:** `module:candidates`, `ui`, `backend`  
**Description:** Implement the main candidate list with filterable/sortable pipeline statuses. Support list and optionally kanban views. Statuses must be configurable via Settings (pipeline/status configuration).

### 3.2 Short notes for candidates
**Labels:** `module:candidates`, `ui`, `backend`  
**Description:** Allow adding and editing short notes per candidate. Store and display in candidate detail view; consider audit/history if required by spec.

### 3.3 Document management for candidates
**Labels:** `module:candidates`, `ui`, `backend`  
**Description:** Support uploading, listing, and downloading documents per candidate. Define allowed types, size limits, and access control.

### 3.4 Interviews (Candidates module)
**Labels:** `module:candidates`, `module:meetings`, `ui`, `backend`  
**Description:** Allow scheduling and listing interviews linked to candidates. Integrate with Meetings and with Dashboard FullCalendar. Store date, time, type, and optional notes.

### 3.5 One-click convert candidate to employee
**Labels:** `module:candidates`, `module:employees`, `backend`  
**Description:** Implement "Convert to employee" action: create an Employee record from the candidate data with one click, and optionally archive or move the candidate status. Define data mapping and validation.

---

## 4. Epic: Jobs

**Labels:** `epic`, `module:jobs`, `ui`, `backend`  
**Description:** Manage job postings/positions: active positions, closed positions, and candidates per position.

### 4.1 Active positions list and management
**Labels:** `module:jobs`, `ui`, `backend`  
**Description:** List and CRUD for active job positions. Define what "active" means (e.g. open for applications, date range). Support search and filters.

### 4.2 Closed positions list and view
**Labels:** `module:jobs`, `ui`, `backend`  
**Description:** List and view closed positions (read-only or limited edits). Ensure consistency with Dashboard "open positions" count (exclude closed).

### 4.3 Candidates per position
**Labels:** `module:jobs`, `module:candidates`, `ui`, `backend`  
**Description:** Associate candidates with positions and show "candidates per position" (list or count). Support assigning/unassigning candidates to/from jobs.

---

## 5. Epic: Employees

**Labels:** `epic`, `module:employees`, `ui`, `backend`  
**Description:** Manage staff: active/former employees, contracts, housing link; automatic status update on termination.

### 5.1 Active employees list and management
**Labels:** `module:employees`, `ui`, `backend`  
**Description:** List and manage active employees. Support CRUD, search, and filters. Align with Dashboard "active employees" metric.

### 5.2 Former employees list and view
**Labels:** `module:employees`, `ui`, `backend`  
**Description:** List and view former employees (e.g. after termination). Optional: limited editing or read-only. Consider data retention and privacy.

### 5.3 Contracts management
**Labels:** `module:employees`, `ui`, `backend`  
**Description:** Manage contracts per employee: store contract type, dates, and related metadata. Link to employee and support listing/editing.

### 5.4 Housing link (Employees ↔ Housing module)
**Labels:** `module:employees`, `module:housing`, `backend`  
**Description:** Link employees to apartments/rooms (housing module). Display current assignment in employee detail; support assigning/unassigning. Used for "free rooms" and "Free from" logic.

### 5.5 Automatic status update on termination
**Labels:** `module:employees`, `backend`  
**Description:** When an employee is terminated, automatically update status to "former", trigger housing "Free from" logic (see Housing epic), and ensure Dashboard metrics (e.g. active count, upcoming departures) stay correct.

---

## 6. Epic: Apartment / Room Management

**Labels:** `epic`, `module:housing`, `ui`, `backend`  
**Description:** Housing management: apartments, rooms per apartment, free rooms view, and automatic "Free from" when an employee is terminated.

### 6.1 Apartment list and management
**Labels:** `module:housing`, `ui`, `backend`  
**Description:** CRUD for apartments (e.g. name, address, notes). List view with search/filters.

### 6.2 Rooms per apartment
**Labels:** `module:housing`, `ui`, `backend`  
**Description:** Manage rooms belonging to each apartment. Support listing rooms per apartment and assigning/unassigning employees to rooms.

### 6.3 Free rooms view
**Labels:** `module:housing`, `ui`, `backend`  
**Description:** View/list of currently free rooms (and optionally "Free from" date). Used by Dashboard "free rooms" count and FullCalendar "Free from" events.

### 6.4 Automatic "Free from" on employee termination
**Labels:** `module:housing`, `module:employees`, `backend`  
**Description:** When an employee is terminated, automatically set the room they occupied to "Free from [termination date]" and update availability. Integrate with Employees termination flow.

---

## 7. Epic: To Do

**Labels:** `epic`, `module:todo`, `ui`, `backend`  
**Description:** Task management: personal tasks, team tasks, and deadline-based tasks. Feeds Dashboard To Do block and activity KPIs.

### 7.1 Personal tasks
**Labels:** `module:todo`, `ui`, `backend`  
**Description:** Tasks assigned to the current user. CRUD, list, filters, and due date/priority. Visible in Dashboard To Do block for the user.

### 7.2 Team tasks
**Labels:** `module:todo`, `ui`, `backend`  
**Description:** Tasks visible to or assignable within a team. Define "team" (e.g. by role or group) and permissions. List and filter by assignee/team.

### 7.3 Deadline-based tasks and priority
**Labels:** `module:todo`, `ui`, `backend`  
**Description:** Support deadline and priority on tasks. Dashboard To Do block shows upcoming/overdue sorted by priority and deadline. Ensure "new To Do" is one of the Dashboard activity KPI sources.

---

## 8. Epic: Meetings

**Labels:** `epic`, `module:meetings`, `ui`, `backend`  
**Description:** Meetings and interviews: schedule, list, and attach notes. Integrate with Candidates (interviews) and Dashboard FullCalendar.

### 8.1 Interviews (Meetings ↔ Candidates)
**Labels:** `module:meetings`, `module:candidates`, `ui`, `backend`  
**Description:** Interviews linked to candidates. Same data as in Candidates module; ensure consistency and single source of truth. Show in FullCalendar and in candidate detail.

### 8.2 Internal meetings
**Labels:** `module:meetings`, `ui`, `backend`  
**Description:** Internal meetings (not necessarily candidate-related). CRUD, list, and calendar integration. "New meeting" is a Dashboard activity KPI source.

### 8.3 Meeting notes
**Labels:** `module:meetings`, `ui`, `backend`  
**Description:** Notes per meeting (and optionally per interview). Store and display in meeting/interview detail view.

---

## 9. Epic: Settings

**Labels:** `epic`, `module:settings`, `ui`, `backend`  
**Description:** Configuration: users/permissions, pipeline and status, KPI configuration, and integrations.

### 9.1 Users and permissions
**Labels:** `module:settings`, `backend`  
**Description:** User management and permission model (roles/permissions). Restrict access to modules and actions based on roles. Consider Laravel gates/policies.

### 9.2 Pipeline and status configuration
**Labels:** `module:settings`, `module:candidates`, `ui`, `backend`  
**Description:** Configure candidate pipeline stages and statuses (and optionally other entity statuses). Used by Candidates list and reports. Admin-only or restricted to certain roles.

### 9.3 KPI configuration
**Labels:** `module:settings`, `module:dashboard`, `ui`, `backend`  
**Description:** Configure which KPIs appear on the Dashboard and optionally thresholds or time ranges. Store in DB or config; apply in Dashboard widgets.

### 9.4 Integrations (preparation)
**Labels:** `module:settings`, `integration:api`  
**Description:** Settings area for future integrations (e.g. Outlook, API keys). Placeholder UI and/or config keys; no full implementation required in this task.

---

## 10. Epic: Outlook integration (preparation)

**Labels:** `epic`, `integration:outlook`, `backend`  
**Description:** Prepare for Outlook integration: email logging, calendar sync, and unified communication. Focus on design and extension points rather than full implementation.

### 10.1 Email logging (design / prep)
**Labels:** `integration:outlook`, `backend`  
**Description:** Design how emails (e.g. with candidates) will be logged and linked to candidates/employees. Define data model and API or hooks for future Outlook integration.

### 10.2 Calendar synchronization (design / prep)
**Labels:** `integration:outlook`, `backend`  
**Description:** Design calendar sync with Outlook: which events (interviews, meetings) sync, conflict handling, and auth flow. Document approach and extension points.

### 10.3 Unified communication (design / prep)
**Labels:** `integration:outlook`, `backend`  
**Description:** Document and prepare for unified communication (email + calendar + possibly chat) with Outlook. No full implementation required.

---

## 11. Epic: API for future integrations

**Labels:** `epic`, `integration:api`, `backend`  
**Description:** API-first approach for internal and future external use: REST/JSON, versioning, OAuth2, permissions, webhooks. Document for internal usage.

### 11.1 REST / JSON versioned endpoints
**Labels:** `integration:api`, `backend`  
**Description:** Implement versioned REST API (e.g. /api/v1/...) returning JSON. Cover main resources (candidates, jobs, employees, etc.) as needed for internal use. Follow Laravel API resources and versioning conventions.

### 11.2 OAuth2 authentication for API
**Labels:** `integration:api`, `backend`  
**Description:** Secure the API with OAuth2 (e.g. Laravel Passport or Sanctum). Document token issuance and usage for internal clients.

### 11.3 API permission handling
**Labels:** `integration:api`, `backend`  
**Description:** Apply permission/scope checks to API endpoints so that tokens only allow authorized actions. Align with Settings "users and permissions" where possible.

### 11.4 Webhooks (design / optional implementation)
**Labels:** `integration:api`, `backend`  
**Description:** Design webhook model (events, payload, retries). Implement optional outbound webhooks for key events (e.g. new candidate, status change) for internal or future external consumers.

### 11.5 API documentation for internal use
**Labels:** `integration:api`, `documentation`  
**Description:** Document API endpoints, auth, and usage for internal teams. Use OpenAPI/Swagger or static docs; keep up to date with versioned routes.
