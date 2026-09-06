from sqlalchemy import Column, Integer, String
from core.database import Base

class STO(Base):
    __tablename__ = "sto"
    id = Column(Integer, primary_key=True)
    name = Column(String)
from .models_nodeb import NodeB
