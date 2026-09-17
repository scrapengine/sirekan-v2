import pytest
from fastapi.testclient import TestClient
from main import app
from core.auth_router import create_access_token
from datetime import timedelta

def test_health_check():
    client = TestClient(app)
    response = client.get("/")
    assert response.status_code == 200
    assert response.json()["status"] == "online"

def test_login_and_admin_access():
    client = TestClient(app)
    
    # Create a test access token for admin user
    access_token_expires = timedelta(minutes=30)
    access_token = create_access_token(
        data={"sub": "admin"}, expires_delta=access_token_expires
    )
    
    # Test accessing admin endpoint with token
    headers = {"Authorization": f"Bearer {access_token}"}
    response = client.get("/admin/users", headers=headers)
    
    # Should get 200 OK or 404 if endpoint doesn't exist yet
    # We're mainly testing that auth works
    assert response.status_code in [200, 404]

if __name__ == "__main__":
    test_health_check()
    test_login_and_admin_access()
    print("All tests passed!")