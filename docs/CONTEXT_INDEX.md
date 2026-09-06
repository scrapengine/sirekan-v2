# SIREKAN V2 — CONTEXT INDEX

| File | Purpose | When to Read |
|---|---|---|
| AGENTS.md | Agent rules | Always |
| PROJECT_MAP.md | Whole-project map | Project-wide work |
| ASSURANCE_MAP.md | Assurance/Insera map | Assurance work |
| ASSURANCE_EXCEL_ANALYSIS.md | Excel findings | Excel/import work |
| DECISIONS.md | Accepted architecture decisions | Before architecture changes |
| CURRENT_STATE.md | Active checkpoint | Always |
| HERMES_WORKFLOW.md | Development workflow | Complex tasks |

## Source of Truth Hierarchy
1. Explicit user instruction for current task
2. Verified source code/database behavior
3. Accepted decisions in DECISIONS.md
4. Project maps
5. Context/inference
6. Agent proposal

If sources conflict, report the conflict and do not silently overwrite higher-priority evidence.
