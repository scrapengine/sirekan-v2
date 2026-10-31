
import sqlite3
import re
import os

legacy_sql = 'legacy/ops_db.sql'
v2_db = 'sql_app.db'

def fix_mysql_line(line):
    # MySQL escaping fixes for SQLite
    line = line.replace("\\'", "''").replace('\\"', '"').replace('\\n', '\n').replace('\\r', '\r')
    return line

def migrate():
    if not os.path.exists(legacy_sql):
        print(f"Error: {legacy_sql} not found")
        return

    conn = sqlite3.connect(v2_db)
    cursor = conn.cursor()

    # Re-create tables based on models
    # NodeB table
    cursor.execute("DROP TABLE IF EXISTS nodeb")
    cursor.execute('''
    CREATE TABLE nodeb (
        idnodeb INTEGER PRIMARY KEY,
        site_id TEXT, site_name TEXT, port_metro TEXT, hostname_ont TEXT, ip_ont TEXT,
        port_onu TEXT, ont_type TEXT, serial_number TEXT, odc TEXT, odp TEXT,
        tikor_site TEXT, on_air TEXT, idsto TEXT, hostname_metro TEXT, hostname_olt TEXT,
        evidence TEXT, created_at TEXT, updated_at TEXT, deleted_at TEXT
    )''')

    # Assurance table
    cursor.execute("DROP TABLE IF EXISTS assurance_tickets")
    cursor.execute('''
    CREATE TABLE assurance_tickets (
        id INTEGER PRIMARY KEY,
        incident TEXT UNIQUE, customer TEXT, summary TEXT, owner_group TEXT, owner TEXT,
        external_ticketid TEXT, customer_segment TEXT, service_no TEXT, service_type TEXT,
        reported_date TEXT, lapul TEXT, gaul TEXT, ttr_customer TEXT, ttr_nasional TEXT,
        ttr_regional TEXT, ttr_witel TEXT, ttr_mitra TEXT, ttr_agent TEXT, ttr_pending TEXT,
        pending_reason TEXT, status TEXT, status_date TEXT, resolved_by TEXT, workzone TEXT,
        witel TEXT, regional TEXT, actual_solution TEXT, incident_domain TEXT,
        resolved_date TEXT, jumlah_site_tsel_nossa TEXT, kategori_site_tsel TEXT,
        impacted_site_tsel TEXT, divisi TEXT, rca TEXT, evidence TEXT, created_at TEXT,
        updated_at TEXT, deleted_at TEXT, ttr_end_to_end TEXT, cause TEXT, resolution TEXT
    )''')

    print("Importing Data...")
    
    with open(legacy_sql, 'r', encoding='utf-8', errors='ignore') as f:
        in_nodeb = False
        in_assurance = False
        
        nodeb_count = 0
        assurance_count = 0

        for line in f:
            if 'INSERT INTO `datanodeb`' in line:
                in_nodeb = True
                continue
            if 'INSERT INTO `assurance_wan`' in line:
                in_assurance = True
                continue
            
            if in_nodeb or in_assurance:
                line = line.strip()
                if not line or line.startswith('--') or line.startswith('/*'):
                    continue
                
                # Check if end of INSERT block
                is_end = line.endswith(';')
                line_to_exec = line.rstrip(';')
                
                if in_nodeb:
                    sql = f"INSERT OR REPLACE INTO nodeb VALUES {line_to_exec}"
                    try:
                        cursor.execute(fix_mysql_line(sql))
                        nodeb_count += cursor.rowcount
                    except Exception as e:
                        pass
                elif in_assurance:
                    sql = f"INSERT OR REPLACE INTO assurance_tickets VALUES {line_to_exec}"
                    try:
                        cursor.execute(fix_mysql_line(sql))
                        assurance_count += cursor.rowcount
                    except Exception as e:
                        pass
                
                if is_end:
                    in_nodeb = False
                    in_assurance = False

    conn.commit()
    print(f"Migration complete!")
    print(f"NodeB: {nodeb_count} rows")
    print(f"Assurance: {assurance_count} rows")
    conn.close()

if __name__ == '__main__':
    migrate()
