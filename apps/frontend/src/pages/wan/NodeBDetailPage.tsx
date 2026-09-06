import React, { useState, useEffect } from 'react';
import { useParams, useNavigate, Link } from 'react-router-dom';
import client from '@/api/client';
import { Card } from '@/components/ui/Card';
import { ArrowLeft, MapPin, Server, Activity, ShieldCheck, Image as ImageIcon } from 'lucide-react';

interface NodeBDetail {
  idnodeb: number;
  sto: string;
  site_id: string;
  site_name: string;
  hostname_metro: string;
  ip_metro: string;
  port_metro: string;
  hostname_olt: string;
  ip_olt: string;
  type_olt: string;
  port_onu: string;
  hostname_ont: string;
  ip_ont: string;
  ont_type: string;
  serial_number: string;
  odc: string;
  odp: string;
  tikor_site: string;
  on_air: string;
  graph_id: string;
  evidence?: string;
}

const Section: React.FC<{ title: string; defaultOpen?: boolean; children: React.ReactNode }> = ({ title, defaultOpen = true, children }) => {
  const [open, setOpen] = useState(defaultOpen);
  return (
    <div className="border border-orbit-border rounded-lg mb-4 overflow-hidden bg-orbit-surface">
      <button
        onClick={() => setOpen(!open)}
        className="w-full flex justify-between items-center p-4 bg-orbit-surface2 hover:bg-white/5 transition-colors text-left"
      >
        <span className="font-bold text-slate-100 text-sm tracking-wide">#{title}</span>
        <span className="text-slate-400 text-xs">{open ? '−' : '+'}</span>
      </button>
      {open && <div className="p-4 border-t border-orbit-border">{children}</div>}
    </div>
  );
};

const Row: React.FC<{ label: string; value?: string | number | null }> = ({ label, value }) => (
  <div className="flex border-b border-orbit-border py-2 text-sm">
    <div className="w-1/3 text-slate-400 text-xs uppercase tracking-wider">{label}</div>
    <div className="w-6 text-slate-500">:</div>
    <div className="flex-1 text-slate-100 font-medium font-mono break-all">{value !== null && value !== undefined && value !== '' ? String(value) : '-'}</div>
  </div>
);

export const NodeBDetailPage: React.FC = () => {
  const { id } = useParams<{ id: string }>();
  const navigate = useNavigate();
  const [nodeB, setNodeB] = useState<NodeBDetail | null>(null);
  const [loading, setLoading] = useState(true);
  const isTrash = window.location.pathname.includes('/trash') || window.location.search.includes('from=trash');

  useEffect(() => {
    const fetchDetail = async () => {
      try {
        setLoading(true);
        const res = await client.get(`/api/master-data/nodeb/${id}`);
        setNodeB(res.data);
      } catch (err) {
        console.error('Failed to fetch detail', err);
      } finally {
        setLoading(false);
      }
    };
    if (id) fetchDetail();
  }, [id]);

  if (loading) {
    return <div className="p-6 text-slate-400 bg-orbit-bg h-screen">Loading NodeB detail...</div>;
  }

  if (!nodeB) {
    return <div className="p-6 text-red-400 bg-orbit-bg h-screen">NodeB Data not found.</div>;
  }

  return (
    <div className="p-6 space-y-4 h-screen overflow-y-auto bg-orbit-bg text-sm text-slate-100">
      {/* Header (mirip legacy section-header) */}
      <div className="flex items-center gap-4 mb-4">
        <button onClick={() => navigate(isTrash ? '/wan/nodeb/trash' : '/wan/nodeb')} className="p-2 hover:bg-white/5 rounded-full text-slate-400 transition-colors">
          <ArrowLeft size={18} />
        </button>
        <h2 className="font-bold text-base flex items-center gap-2">
          DATA NODE-B
          {isTrash && <span className="text-red-500 font-bold tracking-widest uppercase text-sm">TRASH</span>}
        </h2>
      </div>

      <Card className="p-0 bg-orbit-surface border border-orbit-border rounded-xl shadow-lg overflow-hidden">

        {/* #SITE */}
        <Section title="SITE">
          <div className="grid grid-cols-1 gap-0">
            <Row label="Site ID" value={nodeB.site_id} />
            <Row label="Site Name" value={nodeB.site_name} />
            <Row label="STO" value={nodeB.sto} />
            <Row label="Hostname Metro" value={nodeB.hostname_metro} />
            <Row label="IP Metro" value={nodeB.ip_metro} />
            <Row label="Port Metro" value={nodeB.port_metro} />
            <Row label="Hostname OLT" value={nodeB.hostname_olt} />
            <Row label="IP OLT" value={nodeB.ip_olt} />
            <Row label="Port ONU" value={nodeB.port_onu} />
            <Row label="Hostname ONT" value={nodeB.hostname_ont} />
            <Row label="IP ONT" value={nodeB.ip_ont} />
            <Row label="ONT Type" value={nodeB.ont_type?.toUpperCase()} />
            <Row label="Serial Number" value={nodeB.serial_number} />
            <Row label="ODC" value={nodeB.odc} />
            <Row label="ODP" value={nodeB.odp} />
            <Row label="Coordinate Site" value={nodeB.tikor_site} />
            <Row label="On Air Date" value={nodeB.on_air} />
            <Row label="Graph ID (Cacti)" value={nodeB.graph_id} />
          </div>
        </Section>

        {/* #TICKETS (placeholder, struktur siap untuk data assurance) */}
        <Section title="TICKETS" defaultOpen={false}>
          <div className="text-slate-400 text-xs italic">No ticket data available for this NodeB.</div>
        </Section>

        {/* #OTHERS_DATA */}
        <Section title="OTHERS_DATA" defaultOpen={false}>
          <div className="grid grid-cols-1 gap-0">
            <Row label="Evidence" value={nodeB.evidence || '-'} />
            {nodeB.evidence && (
              <div className="mt-3 p-3 bg-orbit-surface2 border border-orbit-border rounded-lg flex items-center gap-3">
                <ImageIcon size={20} className="text-slate-400" />
                <span className="text-xs text-slate-300 font-mono">{nodeB.evidence}</span>
              </div>
            )}
          </div>
        </Section>

      </Card>
    </div>
  );
};
