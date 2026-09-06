# HERMES WORKFLOW

## Standard Task Protocol
READ → UNDERSTAND → PLAN → IMPLEMENT → TEST → REVIEW → UPDATE STATE

Before work:
- read AGENTS.md
- read CURRENT_STATE.md
- read relevant map
- read DECISIONS.md

Inspect narrowly. Do not load unrelated source.

For non-trivial changes, define scope, files, acceptance criteria, and risks.

After implementation:
- run focused tests
- review regressions/concurrency/security
- update CURRENT_STATE
- update DECISIONS only when architecture changes

## Resume Protocol
1. Read CURRENT_STATE.
2. Determine last completed milestone.
3. Continue next unfinished task.
4. Do not repeat completed analysis.
5. If ambiguous, inspect recent changes and relevant files first.

## Audit Mode
No implementation. Evidence first. Classify findings. Update maps. Stop at requested milestone.

## Review Mode
Inspect changes and report correctness/security/regression findings without rewriting code unless asked.
