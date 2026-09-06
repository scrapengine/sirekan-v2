# SIREKAN V2 — ASSURANCE MAP

Assurance is an external-data integration module. `insr.py` is from the Telethon project, not CI4 source. Keep CURRENT / TARGET / PROPOSED / UNKNOWN separate.

## 1. Overview
Insera → Fetcher → Parser → Normalizer → Validator → Deduplicator → Sync Engine → MySQL

## 2. Current CI4 Implementation
Controller:
Model:
Import:
Views:
Tables:
Worklog:

## 3. External `insr.py`
### Telethon-specific
TODO
### Selenium-specific
TODO
### Authentication/session
TODO
### HTTP
TODO
### Assurance processing
TODO
### Reusable candidates
TODO
### Non-reusable candidates
TODO

## 4. Database Mapping
| External/Excel Field | Sirekan Field | Table | DB Column | Transform | Classification |
|---|---|---|---|---|---|

## 5. Excel Mapping
| Excel Column | DB Column | Required | Type | Transformation | Validation |
|---|---|---|---|---|---|

## 6. Retrieval Strategy
### Query 1 — Closed
Endpoint:
Filter:
Date window:
Classification:

### Query 2 — Final/Resolved
Endpoint:
Statuses:
Date window:
Classification:

### Query 3 — Active
Endpoint:
Statuses:
Date filter:
Classification:

Known active statuses: BACKEND, ANALYSIS, DRAFT, NEW, PENDING.

## 7. Merge / Deduplication
External identity:
Current CI4 rule:
DB unique constraint:
Target V2 rule:

## 8. Status Rules
TODO

## 9. Date Rules
TODO

## 10. Insert Rules
TODO

## 11. Update Rules
TODO

## 12. Skip Rules
TODO

## 13. Worklog
Table:
Relationship:
Duplicate rule:

## 14. Session Architecture
Current: Selenium login/session in external project.
Target: aiohttp/session manager where possible; Selenium fallback during transition.
Session expiry behavior: TODO

## 15. Error / Retry
| Failure | Expected Behavior |
|---|---|
| Session expired | Refresh/re-authenticate according to verified flow |
| Timeout | Retry according to configured policy |
| Invalid response | Reject before parsing |
| Database failure | Fail run safely |
| Duplicate | Apply verified sync rule |

## 16. Worker
Name: Assurance Worker
Interval: Configurable
Enabled: Configurable
Non-overlap lock: Required
Manual run: Target

## 17. Run Metrics
records_found, records_inserted, records_updated, records_skipped, records_failed, duration, started_at, finished_at, error_summary.

## 18. Unknowns
| Question | Status |
|---|---|
| Definitive external ticket key | UNKNOWN |
| Exact update fields | UNKNOWN |
| Exact worklog duplicate rule | UNKNOWN |
| Exact authentication refresh flow | UNKNOWN until audited |

## 19. Implementation Checklist
- [ ] Insera client
- [ ] Session manager
- [ ] Assurance fetcher
- [ ] Parser
- [ ] Normalizer
- [ ] Validator
- [ ] Deduplicator
- [ ] Sync engine
- [ ] Worklog sync
- [ ] Assurance worker
- [ ] Scheduler
- [ ] Retry
- [ ] Run history
- [ ] Logging
- [ ] API
- [ ] React UI
- [ ] Manual import
- [ ] Integration tests
