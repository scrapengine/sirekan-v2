# SIREKAN V2 — CURRENT STATE

## Updated: 2026-09-06

## Backend — NodeB Management (`apps/backend/master_data/router.py`)
- Endpoints implemented:
  - `GET /api/master-data/nodeb`: Active list with 6-table joins (STO, Metro, OLT, ONT, Cacti) and server-side pagination/search.
  - `GET /api/master-data/nodeb/trash`: Deleted records list with the same join structure.
  - `GET /api/master-data/nodeb/{id}`: Single record detail view.
  - `POST /api/master-data/nodeb`: Create new NodeB with ONT tracking logic and Cacti graph_id insertion.
  - `PATCH /api/master-data/nodeb/{id}`: Update existing NodeB with identical tracking and Cacti logic.
  - `DELETE /api/master-data/nodeb/{id}`: Soft delete (sets `deleted_at = now`).
  - `POST /api/master-data/nodeb/restore/{id}`: Restore soft-deleted record.
  - `POST /api/master-data/nodeb/restore-all`: Restore all records from trash.
  - `DELETE /api/master-data/nodeb/purge/{id}`: Permanent hard delete.
  - `DELETE /api/master-data/nodeb/purge-all`: Permanent hard delete for all trash.
  - `GET /api/master-data/nodeb/export`: Generates Excel file (`StreamingResponse`) using `pandas`.
  - `POST /api/master-data/nodeb/import`: Stubbed for file upload.

## Frontend — NodeB Pages (`apps/frontend/src/pages/wan/`)
- **List Page (`NodeBPage.tsx`)**: Orbit UI dark theme, pagination, column visibility, trash toggle, asychronous Excel export.
- **Detail Page (`NodeBDetailPage.tsx`)**: 4 sections (#SITE, #TICKETS, #OTHERS_DATA), context-aware (shows "TRASH" badge and dynamic "Back" link if opened from trash).
- **Add/Edit Pages (`NodeBAddPage.tsx`, `NodeBEditPage.tsx`)**: 4-section layout, STO searchable combobox, OLT search modal with pagination.
- **Trash Page (`NodeBTrashPage.tsx`)**: Mirror of list page with Restore/Delete mass actions and row-level controls.

## Frontend — Layout & Global
- **Topbar**: Breadcrumbs changed from static "Orbit / Dashboard" to dynamic `Sirekan / [URL_SEGMENTS]` (e.g., `Sirekan / WAN / NODEB / TRASH`).
- **App.tsx**: Centralized `document.title` logic to update page title based on current route (e.g., "NODE-B", "NODE-B TRASH").
- **Favicon**: Updated to `sirekan-icon.svg`.

## Git & Workflow
- Initialized local repository and connected to `git@github.com:scrapengine/sirekan-v2.git`.
- Configured `.gitignore` to protect databases (`sql_app.db`), `.env`, and migration artifacts.
- Successfully performed `/git-commit` and `/git-push` to `main`.

## Known Issues
1. **Import Logic**: Backend `import_nodeb` endpoint is currently a placeholder; requires full implementation of the transactional multi-table insertion logic.
2. **Path Artifact**: `apps/frontend/apps/frontend/` exists but is ignored.

## Next Steps
1. Implement full Excel import logic on the backend.
2. Extend pagination/search to other master data modules (OLT, Metro).
3. Connect Ticket data to the #TICKETS section in NodeB Detail.
