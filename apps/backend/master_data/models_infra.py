from sqlalchemy import Column, String, DateTime
from core.database import Base


class Metro(Base):
    __tablename__ = "datametro"

    hostname_metro = Column(String, primary_key=True)
    ip_metro = Column(String)
    idsto = Column(String)
    deleted_at = Column(String)
    created_at = Column(String)
    updated_at = Column(String)


class OLT(Base):
    __tablename__ = "dataolt"

    hostname_olt = Column(String, primary_key=True)
    witel = Column(String)
    idsto = Column(String)
    hostname_metro = Column(String)
    port_metro = Column(String)
    ip_olt = Column(String)
    port_olt = Column(String)
    platform = Column(String)
    type_olt = Column(String)
    deleted_at = Column(String)
    created_at = Column(String)
    updated_at = Column(String)
    lokasi = Column(String)
    tikor = Column(String)
    koordinat = Column(String)


class STO(Base):
    __tablename__ = "datasto"

    idsto = Column(String, primary_key=True)
    nama_sto = Column(String)
    witel = Column(String)
    longitude = Column(String)
    latitude = Column(String)
    alamat = Column(String)
    deleted_at = Column(String)
    created_at = Column(String)
    updated_at = Column(String)