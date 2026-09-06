
def process_nodeb_record(d):
    # Mapping legacy logic
    ont_type = d.get('ont_type')
    if ont_type and 'direct' not in ont_type:
        ont_type = f"{d.get('merk') or ''}-{d.get('type') or ''}"

    port_metro = d.get('port_metro_nodeb')
    if 'GPON' in (d.get('hostname_olt_nodeb') or ''):
        port_metro = d.get('port_metro_olt')

    type_olt = ""
    port_onu = d.get('port_onu') or ''
    type_olt_legacy = d.get('type_olt')
    
    if ':' in port_onu and '/' in port_onu:
        type_olt = type_olt_legacy
    elif '/' in port_onu:
        type_olt = f"direct{type_olt_legacy}"
    elif type_olt_legacy is None:
        type_olt = 'directMetro'
    else:
        type_olt = type_olt_legacy

    return {
        "idnodeb": d.get('idnodeb'),
        "sto": d.get('idsto_nodeb'),
        "site_id": d.get('site_id'),
        "site_name": d.get('site_name'),
        "hostname_metro": d.get('hostname_metro_nodeb'),
        "ip_metro": d.get('ip_metro'),
        "port_metro": port_metro,
        "type_olt": type_olt,
        "hostname_olt": d.get('hostname_olt_nodeb'),
        "ip_olt": d.get('ip_olt'),
        "port_onu": port_onu,
        "hostname_ont": d.get('hostname_ont'),
        "ip_ont": d.get('ip_ont'),
        "ont_type": ont_type,
        "serial_number": d.get('serial_number'),
        "odc": d.get('odc'),
        "odp": d.get('odp'),
        "tikor_site": d.get('tikor_site'),
        "on_air": d.get('on_air'),
        "graph_id": d.get('graph_id'),
    }
