import httpx
from datetime import datetime, timedelta
from core.config import settings

async def send_telegram_message(chat_id: str, text: str) -> bool:
    url = f"https://api.telegram.org/bot{settings.TELEGRAM_BOT_TOKEN}/sendMessage"
    async with httpx.AsyncClient() as client:
        try:
            resp = await client.post(url, json={"chat_id": chat_id, "text": text})
            if resp.status_code != 200:
                print(f"TELEGRAM ERROR: {resp.status_code} - {resp.text}")
            return resp.status_code == 200
        except Exception as e:
            print(f"TELEGRAM EXCEPTION: {str(e)}")
            return False

def generate_otp() -> str:
    import random
    return str(random.randint(100000, 999999))

def otp_expires_at(minutes: int = 5) -> datetime:
    return datetime.utcnow() + timedelta(minutes=minutes)
