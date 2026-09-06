from pydantic_settings import BaseSettings, SettingsConfigDict
import os

class Settings(BaseSettings):
    PROJECT_NAME: str = "Sirekan V2 API"
    SECRET_KEY: str = "supersecretkey"
    ALGORITHM: str = "HS256"
    ACCESS_TOKEN_EXPIRE_MINUTES: int = 30
    
    # Construct absolute path for the database URL
    _db_path = os.path.abspath(os.path.join(os.path.dirname(__file__), '..', 'sql_app.db')).replace(os.sep, '/')
    DATABASE_URL: str = f"sqlite+aiosqlite:///{_db_path}"

    model_config = SettingsConfigDict(env_file=".env")

settings = Settings()
