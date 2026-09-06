from sqlalchemy import Column, Integer, String
from core.database import Base

class NodeB(Base):
    __tablename__ = "nodeb"
    
    idnodeb = Column(Integer, primary_key=True)
    site_id = Column(String)
    site_name = Column(String)
    port_metro = Column(String)
    hostname_ont = Column(String)
    ip_ont = Column(String)
    port_onu = Column(String)
    ont_type = Column(String)
    serial_number = Column(String)
    odc = Column(String)
    odp = Column(String)
    tikor_site = Column(String)
    on_air = Column(String)
    idsto = Column(String)
    hostname_metro = Column(String)
    hostname_olt = Column(String)
    evidence = Column(String)
    created_at = Column(String)
    updated_at = Column(String)
    deleted_at = Column(String)
