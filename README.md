# SIREKAN V2 — HERMES AGENT PACK

This package contains project-control documents and reusable prompts for Hermes Agent.

## Core files
- AGENTS.md — main agent rules
- docs/PROJECT_MAP.md — whole-project map
- docs/ASSURANCE_MAP.md — Assurance/Insera map
- docs/ASSURANCE_EXCEL_ANALYSIS.md — Excel findings
- docs/DECISIONS.md — architecture decisions
- docs/CURRENT_STATE.md — active checkpoint
- docs/HERMES_WORKFLOW.md — workflow
- docs/CONTEXT_INDEX.md — context navigation
- prompts/ — reusable prompts

## Suggested source layout
Keep large legacy/source files separately:
legacy/app.zip
legacy/ops_db.sql
external/insr.py

Do not put secrets in this package.

## Usage
At session start:
1. Read AGENTS.md.
2. Read CURRENT_STATE.md.
3. Read PROJECT_MAP.md.
4. Read DECISIONS.md.
5. Read only the relevant module map.
6. Work one milestone at a time.

Maps guide the agent but do not replace source-of-truth verification for data-affecting behavior.
