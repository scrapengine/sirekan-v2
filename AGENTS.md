# SIREKAN V2 — HERMES AGENT RULES

## Mission
You are the local development agent for Sirekan V2. Help migrate CodeIgniter 4 + MySQL Sirekan into a maintainable FastAPI + React application while preserving verified business behavior.

The legacy source, database, project maps, and explicit user decisions are the source of truth. Do not invent business rules.

## Golden Rules
1. Read `docs/CURRENT_STATE.md` before work.
2. Read `docs/PROJECT_MAP.md` for project-wide architecture.
3. Read the relevant module map before touching a module.
4. Read `docs/DECISIONS.md` before architectural decisions.
5. Distinguish VERIFIED / INFERENCE / PROPOSED / UNKNOWN.
6. Prefer the smallest safe change.
7. Do not refactor unrelated code.
8. Do not delete legacy functionality merely because V2 has an alternative.
9. Do not modify production-like database schema without explicit approval.
10. Keep workers isolated; one worker failure must not stop unrelated workers.
11. Worker intervals are configuration-driven, never hardcoded.
12. Do not put long-running/blocking work in FastAPI request paths.
13. Prefer async I/O for external HTTP/network work.
14. Selenium is a transition/fallback mechanism where direct HTTP session handling is not yet sufficient.
15. Test meaningful changes.
16. Update CURRENT_STATE after meaningful milestones.
17. Record accepted architectural changes in DECISIONS.
18. Report conflicts instead of silently choosing.
19. Ask before destructive operations.

## Development Loop
READ → UNDERSTAND → PLAN → IMPLEMENT → TEST → REVIEW → UPDATE STATE

## Legacy Preservation
CI4 is the behavioral reference. Before replacing a feature, identify its legacy flow, database effects, and verified rules. A cleaner implementation is not automatically equivalent.

## Data Safety
Never truncate, mass-delete, drop tables, alter keys/types, or run destructive migrations without explicit approval. Protect imports/syncs from duplicates, malformed dates, external response changes, retry duplication, and concurrent runs.

## Insera / Assurance
`insr.py` is from the Telethon project, not CI4. Separate Telethon-specific code from reusable session/HTTP/data logic. Prefer aiohttp. Validate external responses before parsing.

Known Assurance target retrieval concept:
1. Closed tickets + today's status-date window.
2. Open endpoint + final/resolved-type statuses + today's status-date window.
3. Open endpoint + active statuses without status-date filter.

Known active statuses: BACKEND, ANALYSIS, DRAFT, NEW, PENDING.
These requirements still need verification against evidence.

## Worker Architecture
Each worker has independent lifecycle, configurable enabled state, configurable interval, non-overlap lock, timeout, retry, logging, run history, graceful failure, and shutdown. One worker must not terminate unrelated workers.

## API / Frontend
Backend: FastAPI, async-first where appropriate, Pydantic validation, consistent service/repository boundaries.
Frontend: React, TypeScript preferred, documented API contracts.

## Code Quality
Avoid dead imports, unused functions, duplicate logic, giant functions, hidden global state, magic numbers, hardcoded intervals, credentials in source.
Prefer small services, explicit dependencies, typed interfaces, configuration, structured logging, deterministic business logic, focused tests.

## Security
Never commit passwords, tokens, session cookies, TOTP secrets, `.env`, or DB credentials.

## Git Safety
Inspect git status before large changes. Keep changes scoped. Never rewrite history or force-push without explicit approval.

## Communication
For each task report: inspected, changed, why, tests, results, risks, documentation/state updates.

## Stop Conditions
Ask before destructive DB changes, changing accepted architecture, deleting legacy modules, changing authentication strategy, adding major dependencies, or changing public API contracts with downstream impact.
