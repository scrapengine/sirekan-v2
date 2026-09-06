### ASSURANCE_FRONTEND_BACKEND_GAPS.md

| Feature | Problem | Suggested Endpoint | Priority |
| :--- | :--- | :--- | :--- |
| Ticket Detail | No detail endpoint | `GET /assurance/tickets/{incident}` | High |
| Search | Backend search not implemented | `GET /assurance/tickets?q=...` | High |
| Filter | Backend filter not implemented | `GET /assurance/tickets?status=...&witel=...` | Med |
| Worklog | No worklog endpoint | `POST /assurance/tickets/{incident}/worklog` | High |
| Pagination | Backend lacks total count | Add `total`, `has_more` to response | Med |
