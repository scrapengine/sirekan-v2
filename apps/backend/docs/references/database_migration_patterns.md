# Common Database Migration Patterns for Sirekan V2

This document outlines reliable patterns for schema changes, especially when migrating from MySQL (CI4) to SQLite (FastAPI).

## 1. Initial Table Creation (Base.metadata.create_all)

*   **Purpose**: To create all tables defined in `core/models.py` if they do not already exist.
*   **Usage**: Run `sync_db.py` (`asyncio.run(Base.metadata.create_all(engine))`).
*   **Limitation**: `create_all` will **NOT** modify existing tables (e.g., add new columns, change column types). It only creates what's missing.

## 2. Adding Columns to Existing Tables (ALTER TABLE)

*   **Purpose**: To add new columns to an existing table without losing data.
*   **Method**: Use explicit `ALTER TABLE ADD COLUMN` SQL statements.
*   **Example (Adding `telegram_id` to `users`):**
    ```python
    import asyncio
    import sqlite3
    from core.config import settings

    async def add_telegram_id_column():
        db_path = settings.DATABASE_URL.replace("sqlite+aiosqlite:///", "")
        conn = sqlite3.connect(db_path)
        cursor = conn.cursor()
        try:
            cursor.execute("ALTER TABLE users ADD COLUMN telegram_id TEXT")
            # Optional: Add unique index for faster lookups and uniqueness enforcement
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
        asyncio.run(add_telegram_id_column())
    ```
*   **Pitfall**: Running `Base.metadata.create_all()` after defining new columns in an existing model will NOT add those columns. Explicit `ALTER TABLE` is required.

## 3. Full Schema Recreation (Development Only - Destructive)

*   **Purpose**: Completely reset the database schema (useful for development when data loss is acceptable).
*   **Method**: `Base.metadata.drop_all(engine)` followed by `Base.metadata.create_all(engine)`.
*   **Warning**: This deletes ALL data in the database. Use with extreme caution.

## 4. Alembic Migrations (Recommended for Production)

*   **Purpose**: Managed, versioned database schema changes for production environments.
*   **Usage**: Requires Alembic setup (`alembic init`, `alembic revision --autogenerate`, `alembic upgrade head`).
*   **Current State**: Not fully integrated into Sirekan V2 workflow for minor schema changes, but available for major structured migrations.

