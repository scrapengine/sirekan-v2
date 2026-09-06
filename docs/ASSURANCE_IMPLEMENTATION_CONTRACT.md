### Assurance V2 Implementation Contract

**1. Ticket Listing & Details**
* List view with search and filter.
* Detailed view with full ticket data.

**2. Manual Input/Edit**
* Support manual updates for specific fields (e.g., status, worklog).

**3. Excel Import Rules**
* Header-based mapping (ignore column position).
* Canonical Field Mapping: `Excel Header -> Canonical Field -> Normalize -> Validate -> Sync`.
* Handling Missing Optional Headers: Set to NULL/empty, log warning, proceed.
* Handling Unknown Headers: Ignore, log warning, proceed.
* Handling Missing Critical Headers (e.g., INCIDENT): REJECT import, log clear error.
* Handling Duplicate Headers: Deterministic handling, log warning/error.

**4. Worker / Automation Infrastructure**
* Independent execution.
* Configurable intervals.
* No overlap for the same worker.
* Retry, backoff, timeout mechanisms.
* Detailed status and history tracking.
* Manual "Run Now" capability.

**5. Insera Integration Strategy**
* Modular integration layer.
* Strategy: Three-query approach.
    1. Closed ticket + today's status-date window.
    2. Open endpoint + final/resolved-type statuses + today's status-date window.
    3. Open endpoint + active statuses without status-date filter.
* Active statuses: BACKEND, ANALYSIS, DRAFT, NEW, PENDING.
* Use aiohttp for normal retrieval; Selenium for session recovery only.

**6. Database Schema**
* Comprehensive schema based on the full 86 analyzed headers.
