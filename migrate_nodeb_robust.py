
import sqlite3
import os

legacy_sql = 'legacy/ops_db.sql'
v2_db = 'apps/backend/sql_app.db'

def migrate_nodeb():
    conn = sqlite3.connect(v2_db)
    cursor = conn.cursor()
    cursor.execute("DELETE FROM nodeb")
    
    with open(legacy_sql, 'r', encoding='utf-8', errors='ignore') as f:
        in_datanodeb = False
        count = 0
        for line in f:
            if 'INSERT INTO `datanodeb`' in line:
                in_datanodeb = True
                continue
            if in_datanodeb:
                line = line.strip()
                if line.startswith('('):
                    # Prepare SQL safely
                    values = line.rstrip(',;')
                    sql = f"INSERT INTO nodeb VALUES {values}"
                    try:
                        cursor.execute(sql)
                        count += 1
                    except Exception as e:
                        pass
                elif line == ';' or 'INSERT INTO' in line:
                    in_datanodeb = False
    
    conn.commit()
    print(f"Total Migrated: {count}")
    conn.close()

migrate_nodeb()
