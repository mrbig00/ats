#!/usr/bin/env bash
# Create GitHub issues from ATS_GitHub_Tasks.md
# Usage: REPO=owner/repo ./create_github_issues.sh
# Requires: gh CLI (https://cli.github.com/) logged in

set -e
REPO="${REPO:?Set REPO=owner/repo (e.g. REPO=myorg/ats)}"

create_issue() {
  local title="$1"
  local body="$2"
  shift 2
  local labels=("$@")
  local tmpbody
  tmpbody=$(mktemp)
  printf '%s' "$body" > "$tmpbody"
  local args=()
  for L in "${labels[@]}"; do args+=(--label "$L"); done
  gh issue create --repo "$REPO" --title "$title" --body-file "$tmpbody" "${args[@]}"
  rm -f "$tmpbody"
}

# Ensure labels exist (create if missing)
ensure_labels() {
  for label in epic module:dashboard module:candidates module:jobs module:employees module:housing module:todo module:meetings module:settings integration:outlook integration:api spec ui backend i18n documentation; do
    gh label create "$label" --repo "$REPO" 2>/dev/null || true
  done
}

ensure_labels

# --- Epic 1: Menu structure & application shell ---
create_issue "Epic: Menu structure & application shell" \
  "Implement the main application shell and navigation as defined in the spec (base language: German). All main modules must be reachable from a single, consistent menu." \
  epic ui i18n
EPIC1=$(gh issue list --repo "$REPO" --limit 1 --json number --jq '.[0].number')

create_issue "1.1 Implement application shell with base navigation" \
  "Build the main layout (sidebar or top nav) with entries: Dashboard, Candidates, Jobs, Employees, Apartment/Room Management, To Do, Meetings, Settings. Use named routes and ensure responsive behavior.

**Epic:** #$EPIC1" \
  ui spec

create_issue "1.2 Base language (German) for menu labels" \
  "All menu and submenu labels must be available in German as the base language. Add translation keys and lang files (or equivalent) for: Dashboard, Candidates, Jobs, Employees, Apartment/Room Management, To Do, Meetings, Settings.

**Epic:** #$EPIC1" \
  i18n spec

# --- Epic 2: Dashboard ---
create_issue "Epic: Dashboard" \
  "Provide a real-time HR and operational overview in a centralized interface: KPIs, activity chart, key metrics, FullCalendar, To Do block, and internal chat." \
  epic module:dashboard ui backend
EPIC2=$(gh issue list --repo "$REPO" --limit 1 --json number --jq '.[0].number')

create_issue "2.1 KPI / activity chart (time-series)" \
  "Implement a time-series activity chart on the Dashboard with weekly and monthly views. Data sources: new candidate, candidate status change, new employee, resignation/termination, new To Do, new meeting. Persist and aggregate events for charting.

**Epic:** #$EPIC2" \
  module:dashboard ui backend

create_issue "2.2 Dashboard metric: new applicants count" \
  "Display the number of new applicants (configurable period, e.g. last 7/30 days) on the Dashboard. Use efficient queries and cache if appropriate.

**Epic:** #$EPIC2" \
  module:dashboard backend

create_issue "2.3 Dashboard metric: active employees count" \
  "Display the current number of active employees on the Dashboard. Align with the Employees module definition of \"active\".

**Epic:** #$EPIC2" \
  module:dashboard backend

create_issue "2.4 Dashboard metric: upcoming departures (next 30 days)" \
  "Show count and/or list of employees with departure/termination date within the next 30 days. Link to Employees where relevant.

**Epic:** #$EPIC2" \
  module:dashboard backend

create_issue "2.5 Dashboard metric: free rooms count" \
  "Display the number of free rooms from the Apartment/Room Management module. Ensure consistency with the housing \"Free from\" logic.

**Epic:** #$EPIC2" \
  module:dashboard backend module:housing

create_issue "2.6 Dashboard metric: open job positions count" \
  "Display the number of open/active job positions. Reuse Jobs module data and definitions.

**Epic:** #$EPIC2" \
  module:dashboard backend module:jobs

create_issue "2.7 Internal chat on Dashboard" \
  "Implement an internal chat block/widget on the Dashboard. Define scope (e.g. team-wide or per context), persistence, and real-time or polling behavior.

**Epic:** #$EPIC2" \
  module:dashboard ui backend

create_issue "2.8 FullCalendar on Dashboard" \
  "Embed a FullCalendar (or equivalent) on the Dashboard showing: interviews (Candidates), internal meetings (Meetings), entry and exit dates, apartment/room \"Free from\" dates. Support monthly, weekly, and daily views; events must be clickable and link to the related entity.

**Epic:** #$EPIC2" \
  module:dashboard ui backend

create_issue "2.9 To Do block on Dashboard (upcoming tasks)" \
  "Display a To Do block on the Dashboard with upcoming and overdue tasks, sorted by priority and deadline. Reuse To Do module data; consider permission/visibility (e.g. personal vs team).

**Epic:** #$EPIC2" \
  module:dashboard module:todo ui

# --- Epic 3: Candidates ---
create_issue "Epic: Candidates" \
  "Manage the entire recruitment process: candidate list with pipeline statuses, notes, documents, interviews, and one-click conversion to employee." \
  epic module:candidates ui backend
EPIC3=$(gh issue list --repo "$REPO" --limit 1 --json number --jq '.[0].number')

create_issue "3.1 Candidate list with pipeline statuses" \
  "Implement the main candidate list with filterable/sortable pipeline statuses. Support list and optionally kanban views. Statuses must be configurable via Settings (pipeline/status configuration).

**Epic:** #$EPIC3" \
  module:candidates ui backend

create_issue "3.2 Short notes for candidates" \
  "Allow adding and editing short notes per candidate. Store and display in candidate detail view; consider audit/history if required by spec.

**Epic:** #$EPIC3" \
  module:candidates ui backend

create_issue "3.3 Document management for candidates" \
  "Support uploading, listing, and downloading documents per candidate. Define allowed types, size limits, and access control.

**Epic:** #$EPIC3" \
  module:candidates ui backend

create_issue "3.4 Interviews (Candidates module)" \
  "Allow scheduling and listing interviews linked to candidates. Integrate with Meetings and with Dashboard FullCalendar. Store date, time, type, and optional notes.

**Epic:** #$EPIC3" \
  module:candidates module:meetings ui backend

create_issue "3.5 One-click convert candidate to employee" \
  "Implement \"Convert to employee\" action: create an Employee record from the candidate data with one click, and optionally archive or move the candidate status. Define data mapping and validation.

**Epic:** #$EPIC3" \
  module:candidates module:employees backend

# --- Epic 4: Jobs ---
create_issue "Epic: Jobs" \
  "Manage job postings/positions: active positions, closed positions, and candidates per position." \
  epic module:jobs ui backend
EPIC4=$(gh issue list --repo "$REPO" --limit 1 --json number --jq '.[0].number')

create_issue "4.1 Active positions list and management" \
  "List and CRUD for active job positions. Define what \"active\" means (e.g. open for applications, date range). Support search and filters.

**Epic:** #$EPIC4" \
  module:jobs ui backend

create_issue "4.2 Closed positions list and view" \
  "List and view closed positions (read-only or limited edits). Ensure consistency with Dashboard \"open positions\" count (exclude closed).

**Epic:** #$EPIC4" \
  module:jobs ui backend

create_issue "4.3 Candidates per position" \
  "Associate candidates with positions and show \"candidates per position\" (list or count). Support assigning/unassigning candidates to/from jobs.

**Epic:** #$EPIC4" \
  module:jobs module:candidates ui backend

# --- Epic 5: Employees ---
create_issue "Epic: Employees" \
  "Manage staff: active/former employees, contracts, housing link; automatic status update on termination." \
  epic module:employees ui backend
EPIC5=$(gh issue list --repo "$REPO" --limit 1 --json number --jq '.[0].number')

create_issue "5.1 Active employees list and management" \
  "List and manage active employees. Support CRUD, search, and filters. Align with Dashboard \"active employees\" metric.

**Epic:** #$EPIC5" \
  module:employees ui backend

create_issue "5.2 Former employees list and view" \
  "List and view former employees (e.g. after termination). Optional: limited editing or read-only. Consider data retention and privacy.

**Epic:** #$EPIC5" \
  module:employees ui backend

create_issue "5.3 Contracts management" \
  "Manage contracts per employee: store contract type, dates, and related metadata. Link to employee and support listing/editing.

**Epic:** #$EPIC5" \
  module:employees ui backend

create_issue "5.4 Housing link (Employees ↔ Housing module)" \
  "Link employees to apartments/rooms (housing module). Display current assignment in employee detail; support assigning/unassigning. Used for \"free rooms\" and \"Free from\" logic.

**Epic:** #$EPIC5" \
  module:employees module:housing backend

create_issue "5.5 Automatic status update on termination" \
  "When an employee is terminated, automatically update status to \"former\", trigger housing \"Free from\" logic (see Housing epic), and ensure Dashboard metrics (e.g. active count, upcoming departures) stay correct.

**Epic:** #$EPIC5" \
  module:employees backend

# --- Epic 6: Apartment / Room Management ---
create_issue "Epic: Apartment / Room Management" \
  "Housing management: apartments, rooms per apartment, free rooms view, and automatic \"Free from\" when an employee is terminated." \
  epic module:housing ui backend
EPIC6=$(gh issue list --repo "$REPO" --limit 1 --json number --jq '.[0].number')

create_issue "6.1 Apartment list and management" \
  "CRUD for apartments (e.g. name, address, notes). List view with search/filters.

**Epic:** #$EPIC6" \
  module:housing ui backend

create_issue "6.2 Rooms per apartment" \
  "Manage rooms belonging to each apartment. Support listing rooms per apartment and assigning/unassigning employees to rooms.

**Epic:** #$EPIC6" \
  module:housing ui backend

create_issue "6.3 Free rooms view" \
  "View/list of currently free rooms (and optionally \"Free from\" date). Used by Dashboard \"free rooms\" count and FullCalendar \"Free from\" events.

**Epic:** #$EPIC6" \
  module:housing ui backend

create_issue "6.4 Automatic \"Free from\" on employee termination" \
  "When an employee is terminated, automatically set the room they occupied to \"Free from [termination date]\" and update availability. Integrate with Employees termination flow.

**Epic:** #$EPIC6" \
  module:housing module:employees backend

# --- Epic 7: To Do ---
create_issue "Epic: To Do" \
  "Task management: personal tasks, team tasks, and deadline-based tasks. Feeds Dashboard To Do block and activity KPIs." \
  epic module:todo ui backend
EPIC7=$(gh issue list --repo "$REPO" --limit 1 --json number --jq '.[0].number')

create_issue "7.1 Personal tasks" \
  "Tasks assigned to the current user. CRUD, list, filters, and due date/priority. Visible in Dashboard To Do block for the user.

**Epic:** #$EPIC7" \
  module:todo ui backend

create_issue "7.2 Team tasks" \
  "Tasks visible to or assignable within a team. Define \"team\" (e.g. by role or group) and permissions. List and filter by assignee/team.

**Epic:** #$EPIC7" \
  module:todo ui backend

create_issue "7.3 Deadline-based tasks and priority" \
  "Support deadline and priority on tasks. Dashboard To Do block shows upcoming/overdue sorted by priority and deadline. Ensure \"new To Do\" is one of the Dashboard activity KPI sources.

**Epic:** #$EPIC7" \
  module:todo ui backend

# --- Epic 8: Meetings ---
create_issue "Epic: Meetings" \
  "Meetings and interviews: schedule, list, and attach notes. Integrate with Candidates (interviews) and Dashboard FullCalendar." \
  epic module:meetings ui backend
EPIC8=$(gh issue list --repo "$REPO" --limit 1 --json number --jq '.[0].number')

create_issue "8.1 Interviews (Meetings ↔ Candidates)" \
  "Interviews linked to candidates. Same data as in Candidates module; ensure consistency and single source of truth. Show in FullCalendar and in candidate detail.

**Epic:** #$EPIC8" \
  module:meetings module:candidates ui backend

create_issue "8.2 Internal meetings" \
  "Internal meetings (not necessarily candidate-related). CRUD, list, and calendar integration. \"New meeting\" is a Dashboard activity KPI source.

**Epic:** #$EPIC8" \
  module:meetings ui backend

create_issue "8.3 Meeting notes" \
  "Notes per meeting (and optionally per interview). Store and display in meeting/interview detail view.

**Epic:** #$EPIC8" \
  module:meetings ui backend

# --- Epic 9: Settings ---
create_issue "Epic: Settings" \
  "Configuration: users/permissions, pipeline and status, KPI configuration, and integrations." \
  epic module:settings ui backend
EPIC9=$(gh issue list --repo "$REPO" --limit 1 --json number --jq '.[0].number')

create_issue "9.1 Users and permissions" \
  "User management and permission model (roles/permissions). Restrict access to modules and actions based on roles. Consider Laravel gates/policies.

**Epic:** #$EPIC9" \
  module:settings backend

create_issue "9.2 Pipeline and status configuration" \
  "Configure candidate pipeline stages and statuses (and optionally other entity statuses). Used by Candidates list and reports. Admin-only or restricted to certain roles.

**Epic:** #$EPIC9" \
  module:settings module:candidates ui backend

create_issue "9.3 KPI configuration" \
  "Configure which KPIs appear on the Dashboard and optionally thresholds or time ranges. Store in DB or config; apply in Dashboard widgets.

**Epic:** #$EPIC9" \
  module:settings module:dashboard ui backend

create_issue "9.4 Integrations (preparation)" \
  "Settings area for future integrations (e.g. Outlook, API keys). Placeholder UI and/or config keys; no full implementation required in this task.

**Epic:** #$EPIC9" \
  module:settings integration:api

# --- Epic 10: Outlook integration (preparation) ---
create_issue "Epic: Outlook integration (preparation)" \
  "Prepare for Outlook integration: email logging, calendar sync, and unified communication. Focus on design and extension points rather than full implementation." \
  epic integration:outlook backend
EPIC10=$(gh issue list --repo "$REPO" --limit 1 --json number --jq '.[0].number')

create_issue "10.1 Email logging (design / prep)" \
  "Design how emails (e.g. with candidates) will be logged and linked to candidates/employees. Define data model and API or hooks for future Outlook integration.

**Epic:** #$EPIC10" \
  integration:outlook backend

create_issue "10.2 Calendar synchronization (design / prep)" \
  "Design calendar sync with Outlook: which events (interviews, meetings) sync, conflict handling, and auth flow. Document approach and extension points.

**Epic:** #$EPIC10" \
  integration:outlook backend

create_issue "10.3 Unified communication (design / prep)" \
  "Document and prepare for unified communication (email + calendar + possibly chat) with Outlook. No full implementation required.

**Epic:** #$EPIC10" \
  integration:outlook backend

# --- Epic 11: API for future integrations ---
create_issue "Epic: API for future integrations" \
  "API-first approach for internal and future external use: REST/JSON, versioning, OAuth2, permissions, webhooks. Document for internal usage." \
  epic integration:api backend
EPIC11=$(gh issue list --repo "$REPO" --limit 1 --json number --jq '.[0].number')

create_issue "11.1 REST / JSON versioned endpoints" \
  "Implement versioned REST API (e.g. /api/v1/...) returning JSON. Cover main resources (candidates, jobs, employees, etc.) as needed for internal use. Follow Laravel API resources and versioning conventions.

**Epic:** #$EPIC11" \
  integration:api backend

create_issue "11.2 OAuth2 authentication for API" \
  "Secure the API with OAuth2 (e.g. Laravel Passport or Sanctum). Document token issuance and usage for internal clients.

**Epic:** #$EPIC11" \
  integration:api backend

create_issue "11.3 API permission handling" \
  "Apply permission/scope checks to API endpoints so that tokens only allow authorized actions. Align with Settings \"users and permissions\" where possible.

**Epic:** #$EPIC11" \
  integration:api backend

create_issue "11.4 Webhooks (design / optional implementation)" \
  "Design webhook model (events, payload, retries). Implement optional outbound webhooks for key events (e.g. new candidate, status change) for internal or future external consumers.

**Epic:** #$EPIC11" \
  integration:api backend

create_issue "11.5 API documentation for internal use" \
  "Document API endpoints, auth, and usage for internal teams. Use OpenAPI/Swagger or static docs; keep up to date with versioned routes.

**Epic:** #$EPIC11" \
  integration:api documentation

echo "Done. Issues created in $REPO"
