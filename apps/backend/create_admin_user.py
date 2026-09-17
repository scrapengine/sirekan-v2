import os
import sys
import sqlite3
from datetime import datetime

# Add the backend directory to sys.path to allow importing core.security
sys.path.append(os.path.join(os.path.dirname(__file__), 'apps', 'backend'))

from apps.backend.core.security import get_password_hash

db_path = 'apps/backend/sql_app.db'

username = "admin"
password = "admin123"
email = "admin@sirekan.id"
role = "administrator"

hashed_password = get_password_hash(password)

conn = sqlite3.connect(db_path)
c = conn.cursor()

try:
    c.execute("INSERT INTO users (username, email, telegram_id, hashed_password, role, is_active, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
              (username, email, None, hashed_password, role, True, datetime.utcnow(), datetime.utcnow()))
    conn.commit()
    print(f"User '{username}' created successfully.")
except sqlite3.IntegrityError as e:
    if "UNIQUE constraint failed: users.username" in str(e) or "UNIQUE constraint failed: users.email" in str(e):
        print(f"User '{username}' or '{email}' already exists. Skipping creation.")
    else:
        print(f"Database error: {e}")
except Exception as e:
    print(f"An unexpected error occurred: {e}")
finally:
    conn.close()