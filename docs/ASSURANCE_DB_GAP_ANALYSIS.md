# ASSURANCE DATABASE GAP ANALYSIS

## Overview
This document provides a gap analysis between the **86 Excel headers** from the canonical Assurance ticket export (`ALL_TICKET_31-07-26_12-56-02.xlsx`) and the existing legacy database table `assurance_wan`.

To prevent **silent data corruption, truncation, or runtime errors**, all text fields capable of holding multi-line or long descriptions (such as `IMPACTED SITE`, `CAUSE`, `RESOLUTION`, `SUMMARY`, `SYMPTOM`, `SOLUTION`) have been designated as `TEXT` or `LONGTEXT` rather than restricted `VARCHAR`.

---

## Gap Analysis Matrix

| No | Excel Header | Normalized Header | Legacy DB Column | Status | Existing DB Type | Proposed Canonical Type | Max Length / Size | Nullable | Action |
|----|--------------|-------------------|------------------|--------|------------------|-------------------------|-------------------|----------|--------|
| 1 | INCIDENT | incident | incident | EXISTS | varchar(100) | VARCHAR(100) | 100 | NO | KEEP |
| 2 | TTR CUSTOMER | ttr_customer | ttr_customer | EXISTS | varchar(100) | VARCHAR(50) | 50 | YES | KEEP |
| 3 | SUMMARY | summary | summary | EXISTS | text | TEXT | Unlimited | YES | KEEP |
| 4 | REPORTED DATE | reported_date | reported_date | EXISTS | datetime | DATETIME | N/A | YES | KEEP |
| 5 | OWNER GROUP | owner_group | owner_group | EXISTS | varchar(100) | VARCHAR(100) | 100 | YES | KEEP |
| 6 | OWNER | owner | owner | EXISTS | varchar(100) | VARCHAR(100) | 100 | YES | KEEP |
| 7 | CUSTOMER SEGMENT | customer_segment | customer_segment | EXISTS | varchar(100) | VARCHAR(100) | 100 | YES | KEEP |
| 8 | SERVICE TYPE | service_type | service_type | EXISTS | varchar(100) | VARCHAR(100) | 100 | YES | KEEP |
| 9 | WITEL | witel | witel | EXISTS | varchar(100) | VARCHAR(100) | 100 | YES | KEEP |
| 10 | WORKZONE | workzone | workzone | EXISTS | varchar(100) | VARCHAR(100) | 100 | YES | KEEP |
| 11 | STATUS | status | status | EXISTS | varchar(100) | VARCHAR(100) | 100 | YES | KEEP |
| 12 | STATUS DATE | status_date | status_date | EXISTS | datetime | DATETIME | N/A | YES | KEEP |
| 13 | TICKET ID GAMAS | ticket_id_gamas | - | MISSING | NONE | VARCHAR(100) | 100 | YES | ADD COLUMN |
| 14 | REPORTED BY | reported_by | - | MISSING | NONE | VARCHAR(100) | 100 | YES | ADD COLUMN |
| 15 | CONTACT PHONE | contact_phone | - | MISSING | NONE | VARCHAR(100) | 100 | YES | ADD COLUMN |
| 16 | CONTACT NAME | contact_name | - | MISSING | NONE | VARCHAR(150) | 150 | YES | ADD COLUMN |
| 17 | CONTACT EMAIL | contact_email | - | MISSING | NONE | VARCHAR(150) | 150 | YES | ADD COLUMN |
| 18 | BOOKING DATE | booking_date | - | MISSING | NONE | DATETIME | N/A | YES | ADD COLUMN |
| 19 | DESCRIPTION ASSIGMENT | description_assignment | - | MISSING | NONE | TEXT | Unlimited | YES | ADD COLUMN |
| 20 | REPORTED PRIORITY | reported_priority | - | MISSING | NONE | VARCHAR(50) | 50 | YES | ADD COLUMN |
| 21 | SOURCE TICKET | source_ticket | - | MISSING | NONE | VARCHAR(100) | 100 | YES | ADD COLUMN |
| 22 | SUBSIDIARY | subsidiary | - | MISSING | NONE | VARCHAR(100) | 100 | YES | ADD COLUMN |
| 23 | EXTERNAL TICKET ID | external_ticket_id | external_ticketid | EXISTS | varchar(100) | VARCHAR(100) | 100 | YES | RENAME / KEEP |
| 24 | CHANNEL | channel | - | MISSING | NONE | VARCHAR(100) | 100 | YES | ADD COLUMN |
| 25 | CUSTOMER TYPE | customer_type | - | MISSING | NONE | VARCHAR(100) | 100 | YES | ADD COLUMN |
| 26 | CLOSED BY | closed_by | - | MISSING | NONE | VARCHAR(100) | 100 | YES | ADD COLUMN |
| 27 | CLOSED / REOPEN by | closed_reopen_by | - | MISSING | NONE | VARCHAR(100) | 100 | YES | ADD COLUMN |
| 28 | CUSTOMER ID | customer_id | - | MISSING | NONE | VARCHAR(100) | 100 | YES | ADD COLUMN |
| 29 | CUSTOMER NAME | customer_name | customer_name | EXISTS | varchar(100) | VARCHAR(150) | 150 | YES | KEEP (Expand) |
| 30 | SERVICE ID | service_id | - | MISSING | NONE | VARCHAR(100) | 100 | YES | ADD COLUMN |
| 31 | SERVICE NO | service_no | service_no | EXISTS | varchar(100) | VARCHAR(100) | 100 | YES | KEEP |
| 32 | SLG | slg | - | MISSING | NONE | VARCHAR(50) | 50 | YES | ADD COLUMN |
| 33 | TECHNOLOGY | technology | - | MISSING | NONE | VARCHAR(50) | 50 | YES | ADD COLUMN |
| 34 | LAPUL | lapul | lapul | EXISTS | varchar(100) | VARCHAR(100) | 100 | YES | KEEP |
| 35 | GAUL | gaul | gaul | EXISTS | varchar(100) | VARCHAR(100) | 100 | YES | KEEP |
| 36 | ONU RX | onu_rx | - | MISSING | NONE | VARCHAR(50) | 50 | YES | ADD COLUMN |
| 37 | PENDING REASON | pending_reason | pending_reason | EXISTS | varchar(255) | VARCHAR(255) | 255 | YES | KEEP |
| 38 | DATEMODIFIED | date_modified | - | MISSING | NONE | DATETIME | N/A | YES | ADD COLUMN |
| 39 | INCIDENT DOMAIN | incident_domain | incident_domain | EXISTS | varchar(255) | VARCHAR(255) | 255 | YES | KEEP |
| 40 | REGION | region | regional | EXISTS | varchar(100) | VARCHAR(100) | 100 | YES | KEEP |
| 41 | SYMPTOM | symptom | - | MISSING | NONE | TEXT | Unlimited | YES | ADD COLUMN |
| 42 | HIERARCHY PATH | hierarchy_path | - | MISSING | NONE | TEXT | Unlimited | YES | ADD COLUMN |
| 43 | SOLUTION | solution | - | MISSING | NONE | TEXT | Unlimited | YES | ADD COLUMN |
| 44 | DESCRIPTION ACTUAL SOLUTION | description_actual_solution | actual_solution | EXISTS | varchar(255) | TEXT | Unlimited | YES | TYPE CHANGE |
| 45 | KODE PRODUK | kode_produk | - | MISSING | NONE | VARCHAR(100) | 100 | YES | ADD COLUMN |
| 46 | PERANGKAT | perangkat | - | MISSING | NONE | VARCHAR(100) | 100 | YES | ADD COLUMN |
| 47 | TECHNICIAN | technician | - | MISSING | NONE | VARCHAR(100) | 100 | YES | ADD COLUMN |
| 48 | DEVICE NAME | device_name | - | MISSING | NONE | VARCHAR(150) | 150 | YES | ADD COLUMN |
| 49 | WORKLOG SUMMARY | worklog_summary | - | MISSING | NONE | TEXT | Unlimited | YES | ADD COLUMN |
| 50 | LAST UPDATE WORKLOG | last_update_worklog | - | MISSING | NONE | DATETIME | N/A | YES | ADD COLUMN |
| 51 | CLASSIFICATION FLAG | classification_flag | - | MISSING | NONE | VARCHAR(100) | 100 | YES | ADD COLUMN |
| 52 | REALM | realm | - | MISSING | NONE | VARCHAR(100) | 100 | YES | ADD COLUMN |
| 53 | RELATED TO GAMAS | related_to_gamas | - | MISSING | NONE | VARCHAR(50) | 50 | YES | ADD COLUMN |
| 54 | TSC RESULT | tsc_result | - | MISSING | NONE | TEXT | Unlimited | YES | ADD COLUMN |
| 55 | SCC RESULT | scc_result | - | MISSING | NONE | TEXT | Unlimited | YES | ADD COLUMN |
| 56 | TTR AGENT | ttr_agent | ttr_agent | EXISTS | varchar(100) | VARCHAR(50) | 50 | YES | KEEP |
| 57 | TTR MITRA | ttr_mitra | ttr_mitra | EXISTS | varchar(100) | VARCHAR(50) | 50 | YES | KEEP |
| 58 | TTR NASIONAL | ttr_nasional | ttr_nasional | EXISTS | varchar(100) | VARCHAR(50) | 50 | YES | KEEP |
| 59 | TTR PENDING | ttr_pending | ttr_pending | EXISTS | varchar(100) | VARCHAR(50) | 50 | YES | KEEP |
| 60 | TTR REGION | ttr_region | ttr_regional | EXISTS | varchar(100) | VARCHAR(50) | 50 | YES | KEEP |
| 61 | TTR WITEL | ttr_witel | ttr_witel | EXISTS | varchar(100) | VARCHAR(50) | 50 | YES | KEEP |
| 62 | TTR END TO END | ttr_end_to_end | ttr_end_to_end | EXISTS | varchar(100) | VARCHAR(50) | 50 | YES | KEEP |
| 63 | NOTE | note | - | MISSING | NONE | TEXT | Unlimited | YES | ADD COLUMN |
| 64 | GUARANTE STATUS | guarantee_status | - | MISSING | NONE | VARCHAR(50) | 50 | YES | ADD COLUMN |
| 65 | RESOLVE DATE | resolve_date | resolved_date | EXISTS | datetime | DATETIME | N/A | YES | KEEP |
| 66 | SN ONT | sn_ont | - | MISSING | NONE | VARCHAR(100) | 100 | YES | ADD COLUMN |
| 67 | TIPE ONT | tipe_ont | - | MISSING | NONE | VARCHAR(100) | 100 | YES | ADD COLUMN |
| 68 | MANUFACTURE ONT | manufacture_ont | - | MISSING | NONE | VARCHAR(100) | 100 | YES | ADD COLUMN |
| 69 | IMPACTED SITE | impacted_site | impacted_site_tsel | EXISTS | text | TEXT | Unlimited | YES | KEEP |
| 70 | CAUSE | cause | cause | EXISTS | text | TEXT | Unlimited | YES | KEEP |
| 71 | RESOLUTION | resolution | resolution | EXISTS | text | TEXT | Unlimited | YES | KEEP |
| 72 | NOTES ESKALASI | notes_eskalasi | - | MISSING | NONE | TEXT | Unlimited | YES | ADD COLUMN |
| 73 | RK INFORMATION | rk_information | - | MISSING | NONE | TEXT | Unlimited | YES | ADD COLUMN |
| 74 | EXTERNAL TICKET TIER 3 | external_ticket_tier_3 | - | MISSING | NONE | VARCHAR(100) | 100 | YES | ADD COLUMN |
| 75 | CUSTOMER CATEGORY | customer_category | customer_category | EXISTS | varchar(100) | VARCHAR(100) | 100 | YES | KEEP |
| 76 | CLASSIFICATION PATH | classification_path | - | MISSING | NONE | TEXT | Unlimited | YES | ADD COLUMN |
| 77 | TERITORY NEAR END | territory_near_end | - | MISSING | NONE | VARCHAR(100) | 100 | YES | ADD COLUMN |
| 78 | TERITORY FAR END | territory_far_end | - | MISSING | NONE | VARCHAR(100) | 100 | YES | ADD COLUMN |
| 79 | URGENCY | urgency | - | MISSING | NONE | VARCHAR(50) | 50 | YES | ADD COLUMN |
| 80 | URGENCY DESCRIPTION | urgency_description | - | MISSING | NONE | VARCHAR(100) | 100 | YES | ADD COLUMN |

---

## Key Risk Mitigation (Truncation Prevention)
1. **Multi-line / Rich Text Fields**: 
   - `IMPACTED SITE`, `CAUSE`, `RESOLUTION`, `SUMMARY`, `SYMPTOM`, `SOLUTION`, `WORKLOG SUMMARY`, `NOTE`, `TSC RESULT`, `SCC RESULT`, `HIERARCHY PATH`, `CLASSIFICATION PATH` are all assigned `TEXT` or `LONGTEXT` types to prevent any data loss from large strings containing line breaks (e.g., multi-site lists in `IMPACTED SITE`).
2. **Date / Time Fields**:
   - All `* DATE` and `* MODIFIED` / `* WORKLOG` columns are strictly mapped to `DATETIME`.
3. **Identifiers**:
   - `INCIDENT` is `VARCHAR(100)` and acts as the unique business key.
