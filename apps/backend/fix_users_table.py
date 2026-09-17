import asyncio
import sqlite3
from core.config import settings

async def add_column():
    db_path = settings.DATABASE_URL.replace("sqlite+aiosqlite:///", "")
    conn = sqlite3.connect(db_path)
    cursor = conn.cursor()
    try:
        cursor.execute("ALTER TABLE users ADD COLUMN telegram_id TEXT")
        cursor.execute("CREATE UNIQUE INDEX ix_users_telegram_id ON users (telegram_id)")
        conn.commit()
        print("Successfully added telegram_id column to users table.")
    except sqlite3.OperationalError as e:
        if "duplicate column name" in str(e):
            print("Column telegram_id already exists.")
        else:
            print(f"Error: {e}")
    finally:
        conn.close()

if __name__ == "__main__":
    asyncio.run(add_column())
