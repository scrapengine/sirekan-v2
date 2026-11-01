# SIREKAN V2 — CURRENT STATE

## Updated: 2026-10-31

## Backend — Infrastructure (`apps/backend/master_data/router_infra.py`)
- CRUD complete: Metro, OLT, STO.
- New features:
  - OLT: `import` logic added, `lokasi` and `koordinat` fields implemented in `OLT` model and DTO.
  - OLT: `export` with dynamic filter support.
  - STO: `restore` and `purge` endpoints added.

## Frontend — Infra Pages (`apps/frontend/src/pages/infra/`)
- **OLT**: 
  - Import button added to filter panel.
  - Column visibility and filter support for `lokasi` and `koordinat`.
  - Uniform font size (`text-sm`) in OLTAdd/OLTEdit forms.
- **STO**: 
  - CRUD complete, table alignment standardized with `align-middle`.
  - Correct fields in Add/Edit forms (only STO, no OLT fields).
- **Metro**: CRUD and Export fully functional.

## Git & Workflow
- Changes pushed to `main` branch.

## Next Steps
- Metro-trash-page pending implementation.
- Connect NodeB Ticket section.