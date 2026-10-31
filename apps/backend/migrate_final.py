import sqlite3
import re
import os

legacy_sql = 'D:/PTR/Project/Sirekan/legacy/ops_db.sql'
v2_db = 'D:/PTR/Project/Sirekan/apps/backend/sql_app.db'

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
    col_def = re.sub(r'CHARACTER SET [a-z0-9]+\s*', '', col_def, flags=re.IGNORECASE)
    col_def = re.sub(r'COLLATE [a-z0-9_]+\s*', '', col_def, flags=re.IGNORECASE)
    col_def = re.sub(r'DEFAULT NULL', '', col_def, flags=re.IGNORECASE)
    col_def = re.sub(r'ON UPDATE CURRENT_TIMESTAMP', '', col_def, flags=re.IGNORECASE)
    return col_def.strip().rstrip(',')

def split_tuples(s):
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
        elif not in_string and c == ',' and depth == 0: pass
        else:
            if depth > 0: current.append(c)
        i += 1
    return rows

with open(legacy_sql, 'r', encoding='utf-8', errors='ignore') as f:
    content = f.read()

conn = sqlite3.connect(v2_db)
cursor = conn.cursor()
cursor.execute('PRAGMA foreign_keys = OFF;')

table_pattern = re.compile(r"CREATE TABLE `(.*?)` \((.*?)\)(?: ENGINE=.*?;)", re.DOTALL)
tables = table_pattern.findall(content)

for t_name, t_body in tables:
    if t_name == 'users':
        continue
    cols = [clean_mysql_type(line.strip()) for line in t_body.split('\n') if line.strip() and not line.strip().upper().startswith(('PRIMARY KEY', 'KEY', 'UNIQUE KEY', 'CONSTRAINT', 'INDEX', 'FULLTEXT', 'CREATE TABLE'))]
    # Allow users table if it somehow didn't exist or we want to recreate it
    # No, skip users table completely as per our requirement
    if t_name == 'users':
        continue
    # ... rest of the logic ...
    create_sql = f'CREATE TABLE IF NOT EXISTS `{t_name}` (' + ','.join(cols) + ');'
    cursor.execute(f'DROP TABLE IF EXISTS `{t_name}`')
    cursor.execute(create_sql)
    
    insert_pattern = re.compile(rf"INSERT INTO `{t_name}`.*?VALUES\s*(.*?);", re.DOTALL | re.IGNORECASE)
    inserts = insert_pattern.findall(content)
    for block in inserts:
        rows = split_tuples(block)
        for r in rows:
            cursor.execute(f'INSERT OR IGNORE INTO `{t_name}` VALUES {r}')

conn.commit()
conn.close()
print("All tables successfully migrated.")
