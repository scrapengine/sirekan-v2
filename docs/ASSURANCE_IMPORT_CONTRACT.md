# ASSURANCE IMPORT CONTRACT

## Overview
This contract defines the strict requirements for importing Assurance data (Assurance tickets, worklogs, and job assignments) into the V2 system. All imports are header-based to ensure schema evolution safety.

## 1. Header-Based Mapping Rule
- Importer MUST NOT use column index (e.g., `row[0]`).
- Importer MUST resolve header names dynamically at runtime.
- If an Excel header is found in the mapping registry, it maps to the canonical field.
- If a header is unknown, it MUST be ignored and logged as a warning.
- If a mandatory header is missing, the import MUST FAIL.

## 2. Mandatory Headers (Critical)
The following headers are required. Missing these will abort the import:
- `INCIDENT` (Business Key)

## 3. Data Validation Rules
- **Date/Time Fields**: MUST be parsed using strict ISO-8601 or Excel-native formats. Invalid dates MUST NOT be coerced; they MUST be reported as errors with row/col reference.
- **String Fields**: Truncation is FORBIDDEN. If the input exceeds the database limit, the import MUST FAIL for that row, or the database column must be updated to accommodate the input (e.g., promote `VARCHAR` to `TEXT`).
- **Required Fields**: Empty values for mandatory fields are treated as failures.

## 4. Error Handling & Reporting
- **Fatal Error (Abort)**: Missing mandatory headers, duplicate critical identifiers, or unrecoverable system failure.
- **Row-level Error (Skip & Log)**: Invalid data types, invalid date formats, or exceeding field length.
- **Import Report Structure**:
  - `total_rows`: Total rows processed.
  - `inserted_rows`: Successful insertions.
  - `skipped_rows`: Rows with invalid data.
  - `warnings`: List of unknown headers or missing optional headers.
  - `errors`: Detailed list of row-level validation failures with `row`, `header`, `value`, and `reason`.

## 5. Column Shift Mitigation (Schema Evolution)
- Importer MUST be column-agnostic.
- The `header_map` dictionary (Excel Header -> Canonical Field) acts as the single source of truth for the runtime parser.
- Adding columns to Excel (Version D+) MUST NOT affect existing mapping.
