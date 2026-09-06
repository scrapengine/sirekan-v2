import sqlite3
import re

def import_legacy_db():
    print("Reading legacy ops_db.sql...")
    with open('../../legacy/ops_db.sql', 'r', encoding='utf-8', errors='ignore') as f:
        sql_content = f.read()

    print("Cleaning SQL for SQLite...")
    sql_content = re.sub(r'ENGINE=InnoDB.*?;', ';', sql_content, flags=re.IGNORECASE)
    sql_content = re.sub(r'DEFAULT CHARSET=[\w\-_]+;', ';', sql_content, flags=re.IGNORECASE)
    sql_content = re.sub(r'COLLATE=[\w\-_]+;', '', sql_content, flags=re.IGNORECASE)
    sql_content = re.sub(r'AUTO_INCREMENT=\d+', '', sql_content, flags=re.IGNORECASE)
    sql_content = re.sub(r'\bunsigned\b', '', sql_content, flags=re.IGNORECASE)
    sql_content = re.sub(r'`', '"', sql_content)

    conn = sqlite3.connect('legacy_ops.db')
    cursor = conn.cursor()

    statements = sql_content.split(';')
    success = 0
    failed = 0

    print("Executing SQL statements into legacy_ops.db...")
    for stmt in statements:
        stmt = stmt.strip()
        if not stmt or stmt.startswith('/*') or stmt.startswith('--') or stmt.upper().startswith('SET') or stmt.upper().startswith('START') or stmt.upper().startswith('COMMIT'):
            continue
        try:
            cursor.execute(stmt)
            success += 1
        except Exception as e:
            failed += 1

    conn.commit()
    conn.close()
    print(f"Legacy import finished: {success} statements succeeded, {failed} failed.")

if __name__ == '__main__':
    import_legacy_db()
