import sqlite3

conn = sqlite3.connect('sql_app.db')
c = conn.cursor()

# Get all tables
c.execute('SELECT name FROM sqlite_master WHERE type="table" ORDER BY name')
tables = c.fetchall()

print("Table Name | Row Count")
print("-----------|----------")
for (table_name,) in tables:
    try:
        c.execute(f'SELECT COUNT(*) FROM "{table_name}"')
        count = c.fetchone()[0]
        print(f"{table_name:<30} | {count}")
    except Exception as e:
        print(f"{table_name:<30} | ERROR: {e}")

conn.close()