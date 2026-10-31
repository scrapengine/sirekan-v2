
import sqlite3
import re
import os

legacy_sql = 'legacy/ops_db.sql'
v2_db = 'apps/backend/sql_app.db'

def clean_mysql_type(col_def):
    col_def = re.sub(r'bigint\(\d+\)', 'INTEGER', col_def, flags=re.IGNORECASE)
    col_def = re.sub(r'int\(\d+\)', 'INTEGER', col_def, flags=re.IGNORECASE)
    col_def = re.sub(r'varchar\(\d+\)', 'TEXT', col_def, flags=re.IGNORECASE)
    col_def = re.sub(r'datetime', 'TEXT', col_def, flags=re.IGNORECASE)
    col_def = re.sub(r'date', 'TEXT', col_def, flags=re.IGNORECASE)
    col_def = re.sub(r'timestamp', 'TEXT', col_def, flags=re.IGNORECASE)
    col_def = re.sub(r'text', 'TEXT', col_def, flags=re.IGNORECASE)
    col_def = re.sub(r'unsigned', '', col_def, flags=re.IGNORECASE)
    col_def = re.sub(r'auto_increment', '', col_def, flags=re.IGNORECASE)
    col_def = re.sub(r"CHARACTER SET [a-z0-9]+\s*", '', col_def, flags=re.IGNORECASE)
    col_def = re.sub(r"COLLATE [a-z0-9_]+\s*", '', col_def, flags=re.IGNORECASE)
    col_def = re.sub(r"DEFAULT NULL", '', col_def, flags=re.IGNORECASE)
    col_def = re.sub(r"DEFAULT '([^']*)'", r"DEFAULT '\1'", col_def)
    col_def = re.sub(r"NOT NULL\s+DEFAULT", "DEFAULT", col_def, flags=re.IGNORECASE)
    col_def = re.sub(r"ON UPDATE CURRENT_TIMESTAMP", '', col_def, flags=re.IGNORECASE)
    return col_def.strip().rstrip(',')

def split_tuples(s):
    """Split a multi-row VALUES string like (1, 'a'), (2, 'b') into single rows."""
    rows = []
    depth = 0
    current = []
    in_string = False
    escape = False
    i = 0
    while i < len(s):
        c = s[i]
        if escape:
            current.append(c)
            escape = False
        elif c == '\\' and in_string:
            current.append(c)
            escape = True
        elif c == "'":
            in_string = not in_string
            current.append(c)
        elif not in_string and c == '(' and depth == 0:
            depth += 1
            current = ['(']
        elif not in_string and c == ')' and depth == 1:
            current.append(')')
            rows.append(''.join(current).strip())
            current = []
            depth = 0
        elif not in_string and c == ',' and depth == 0:
            pass
        else:
            if depth > 0:
                current.append(c)
        i += 1
    return rows

def migrate_all():
    with open(legacy_sql, 'r', encoding='utf-8', errors='ignore') as f:
        content = f.read()

    conn = sqlite3.connect(v2_db)
    cursor = conn.cursor()
    cursor.execute("PRAGMA foreign_keys = OFF;")

    # 1. Get all CREATE TABLE definitions
    table_pattern = re.compile(r"CREATE TABLE `(.*?)` \((.*?)\)\s*(?:ENGINE=[^;]*)?;", re.DOTALL)
    tables_defs = table_pattern.findall(content)

    # 2. Build a map of table -> CREATE SQL
    table_sql_map = {}
    for t_name, t_body in tables_defs:
        lines = t_body.split('\n')
        col_defs = []
        for line in lines:
            line = line.strip()
            if not line: continue
            if line.upper().startswith(('PRIMARY KEY', 'KEY', 'UNIQUE KEY', 'CONSTRAINT', 'INDEX', 'FULLTEXT')):
                continue
            cleaned = clean_mysql_type(line)
            if cleaned:
                col_defs.append(cleaned)
        create_sql = f"CREATE TABLE IF NOT EXISTS `{t_name}` (\n  " + ",\n  ".join(col_defs) + "\n);"
        table_sql_map[t_name] = create_sql

    # 3. Recreate tables cleanly
    for t_name, sql in table_sql_map.items():
        try:
            cursor.execute(f"DROP TABLE IF EXISTS `{t_name}`;")
            cursor.execute(sql)
        except Exception as e:
            print(f"Create fail {t_name}: {e}")

    # 4. Process INSERT statements: split each into individual row inserts
    insert_pattern = re.compile(r"INSERT INTO `(\w+)` (?:VALUES|\([^)]+\) VALUES) (.*?);\s*\n", re.DOTALL)
    matches = insert_pattern.findall(content)
    
    total_rows = 0
    for t_name, body in matches:
        if t_name not in table_sql_map:
            continue
        rows = split_tuples(body)
        for r in rows:
            r = r.rstrip(';')
            sql = f"INSERT OR IGNORE INTO `{t_name}` VALUES {r};"
            try:
                cursor.execute(sql)
                total_rows += 1
            except Exception:
                pass

    conn.commit()
    conn.close()
    print(f"Migration complete. Total rows inserted: {total_rows}")

if __name__ == '__main__':
    migrate_all()
