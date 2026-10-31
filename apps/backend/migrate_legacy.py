import sqlite3
import re

db_path = "apps/backend/sql_app.db"
sql_path = "D:/PTR/Project/Sirekan/legacy/ops_db.sql"

conn = sqlite3.connect(db_path)
cursor = conn.cursor()

# Simple logic: read lines. If it starts with '(', it's a data row to be inserted.
# We need to know which table.
current_table = None

with open(sql_path, 'r', encoding='utf-8', errors='ignore') as f:
    for line in f:
        if line.startswith('INSERT INTO `'):
            current_table = line.split('`')[1]
        elif line.startswith('(') and current_table == 'datanodeb':
            # This is a row for datanodeb
            # Row ends with ), or );
            row_data = line.strip().rstrip(';,')
            try:
                # The columns are: idnodeb, site_id, ...
                # Use INSERT OR IGNORE to be safe
                cursor.execute(f"INSERT OR IGNORE INTO datanodeb VALUES {row_data}")
            except Exception as e:
                # print(f"Error inserting row: {e}")
                pass

conn.commit()
print(f"Total rows: {cursor.execute('SELECT count(*) FROM datanodeb').fetchone()[0]}")
conn.close()
