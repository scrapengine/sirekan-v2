import sqlite3
conn = sqlite3.connect('sql_app.db')
c = conn.cursor()
c.execute('SELECT id, username, email, role, is_active FROM users')
rows = c.fetchall()
for r in rows:
    print(r)
conn.close()