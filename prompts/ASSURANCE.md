# ASSURANCE

Before touching Assurance, read docs/ASSURANCE_MAP.md, docs/PROJECT_MAP.md, docs/DECISIONS.md, docs/CURRENT_STATE.md.

Treat `insr.py` as external Telethon integration reference, not CI4 source.

Preferred conceptual boundary:
Session Manager → Insera Client → Assurance Fetcher → Parser → Normalizer → Validator → Deduplicator → Sync Engine → MySQL

Known target retrieval groups:
1. closed + today's status-date window
2. open + final/resolved-type statuses + today's status-date window
3. open + active statuses without status-date filter

Active statuses: BACKEND, ANALYSIS, DRAFT, NEW, PENDING.

Verify exact behavior against evidence before implementation.
