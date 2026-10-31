
import sqlite3
import re

legacy_sql_path = 'legacy/ops_db.sql'
v2_db_path = 'sql_app.db'

conn_v2 = sqlite3.connect(v2_db_path)
cursor_v2 = conn_v2.cursor()

cursor_v2.execute('DROP TABLE IF EXISTS nodeb;')
cursor_v2.execute('''
CREATE TABLE nodeb (
    idnodeb INTEGER PRIMARY KEY AUTOINCREMENT,
    site_id TEXT,
    site_name TEXT,
    port_metro TEXT,
    hostname_ont TEXT,
    ip_ont TEXT,
    port_onu TEXT,
    ont_type TEXT,
    serial_number TEXT,
    odc TEXT,
    odp TEXT,
    tikor_site TEXT,
    on_air TEXT,
    idsto TEXT,
    hostname_metro TEXT,
    hostname_olt TEXT,
    evidence TEXT,
    created_at TEXT,
    updated_at TEXT,
    deleted_at TEXT
);
''')

with open(legacy_sql_path, 'r', encoding='utf-8', errors='ignore') as f:
    for line in f:
        if 'INSERT INTO `datanodeb`' in line:
            # Replace table name to `nodeb` and run
            fixed_line = line.replace('`datanodeb`', '`nodeb`')
            try:
                cursor_v2.execute(fixed_line)
            except Exception as e:
                # SQLite doesn't accept backslashes or some MySQL specific escape, strip MySQL quirks
                fixed_line = fixed_line.replace("\'", "''").replace('\"', '"')
                try:
                    cursor_v2.execute(fixed_line)
                except Exception as ex:
                    print("Failed line:", ex)

conn_v2.commit()
cursor_v2.execute("SELECT COUNT(*) FROM nodeb;")
print(f"NodeB Total Rows: {cursor_v2.fetchone()[0]}")
conn_v2.close()
