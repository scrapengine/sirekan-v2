### Assurance Sync Rules

* Deduplication Strategy: Identify unique ticket key (e.g., incident_id).
* Upsert Logic: If exists -> update existing fields; if new -> insert.
* Business Rules: Use Insera integration three-query strategy.
* TTR: Store raw values; display-time calculation for BACKEND.
