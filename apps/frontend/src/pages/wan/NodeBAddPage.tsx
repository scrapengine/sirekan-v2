import React, { useState, useEffect } from 'react';
import { useNavigate, Link } from 'react-router-dom';
import client from '@/api/client';
import { Card } from '@/components/ui/Card';
import { Button } from '@/components/ui/Button';
import { ArrowLeft, Save, ChevronDown, Search, Check } from 'lucide-react';

const OltModal: React.FC<{ isOpen: boolean; onClose: () => void; onSelect: (olt: any) => void }> = ({ isOpen, onClose, onSelect }) => {
  const [olts, setOlts] = useState<any[]>([]);
  const [page, setPage] = useState(1);
  const [totalPages, setTotalPages] = useState(1);
  const [search, setSearch] = useState('');
  const [loading, setLoading] = useState(false);

  useEffect(() => {
    if (!isOpen) return;
    const fetch = async () => {
      setLoading(true);
      const res = await client.get('/api/master-data/olt', { params: { page, limit: 10, search } });
      setOlts(res.data.data);
      setTotalPages(res.data.meta.total_pages);
      setLoading(false);
    };
    fetch();
  }, [isOpen, page, search]);

  if (!isOpen) return null;
  return (
    <div className="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
      <Card className="bg-orbit-surface border border-orbit-border w-full max-w-2xl max-h-[80vh] flex flex-col">
        <div className="p-4 border-b border-orbit-border flex justify-between items-center">
          <h4 className="font-bold">Data OLT</h4>
          <button onClick={onClose}>×</button>
        </div>
        <div className="p-3 border-b border-orbit-border">
          <input placeholder="Search OLT..." value={search} onChange={(e) => { setSearch(e.target.value); setPage(1); }} className="w-full bg-orbit-surface2 border border-orbit-border p-2 rounded-lg text-sm" />
        </div>
        <div className="overflow-y-auto flex-1">
          <table className="w-full text-xs text-left">
            <thead className="bg-orbit-surface2 sticky top-0"><tr><th className="p-3">Hostname</th><th className="p-3">IP</th><th className="p-3">Platform</th><th className="p-3">Action</th></tr></thead>
            <tbody>
              {loading ? <tr><td colSpan={4} className="p-4 text-center">Loading...</td></tr> :
                olts.length > 0 ? olts.map(o => (
                  <tr key={o.hostname_olt} className="border-t border-orbit-border">
                    <td className="p-3">{o.hostname_olt}</td>
                    <td className="p-3">{o.ip_olt}</td>
                    <td className="p-3">{o.platform}</td>
                    <td className="p-3 text-right">
                      <Button size="sm" onClick={() => { onSelect(o); onClose(); }} className="bg-blue-600 text-white"><Check size={12}/></Button>
                    </td>
                  </tr>
                )) : <tr><td colSpan={4} className="p-4 text-center text-slate-500">No data</td></tr>
              }
            </tbody>
          </table>
        </div>
        <div className="p-3 border-t border-orbit-border flex justify-between items-center text-xs text-slate-400">
          <span>Page {page} of {totalPages}</span>
          <div className="flex gap-2">
            <Button size="sm" variant="outline" disabled={page === 1} onClick={() => setPage(page - 1)}>Prev</Button>
            <Button size="sm" variant="outline" disabled={page >= totalPages} onClick={() => setPage(page + 1)}>Next</Button>
          </div>
        </div>
      </Card>
    </div>
  );
};

const Section: React.FC<{ title: string; defaultOpen?: boolean; children: React.ReactNode }> = ({ title, defaultOpen = true, children }) => {
  const [open, setOpen] = useState(defaultOpen);
  return (
    <div className="border border-orbit-border rounded-lg mb-4 overflow-hidden bg-orbit-surface">
      <button type="button" onClick={() => setOpen(!open)} className="w-full flex justify-between items-center p-4 bg-orbit-surface2 hover:bg-white/5 transition-colors text-left">
        <span className="font-bold text-slate-100 text-sm tracking-wide">#{title}</span>
        <ChevronDown size={16} className={`text-slate-400 transition-transform ${open ? 'rotate-180' : ''}`} />
      </button>
      {open && <div className="p-4 border-t border-orbit-border">{children}</div>}
    </div>
  );
};

const Field: React.FC<{ label: string; name: string; value: any; onChange: (e: any) => void; type?: string; readOnly?: boolean }> = ({ label, name, value, onChange, type = "text", readOnly = false }) => (
  <div className="flex flex-col gap-1">
    <label className="text-xs uppercase text-slate-400 font-semibold tracking-wider">{label}</label>
    <input type={type} name={name} value={value || ''} onChange={onChange} readOnly={readOnly} className={`bg-orbit-surface2 border border-orbit-border p-2 rounded-lg text-sm text-slate-100 focus:outline-none focus:border-blue-500 font-mono ${readOnly ? 'opacity-60 cursor-not-allowed' : ''}`} />
  </div>
);

export const NodeBAddPage: React.FC = () => {
  const navigate = useNavigate();
  const [formData, setFormData] = useState<any>({
    sto: '', site_id: '', site_name: '', hostname_metro: '', ip_metro: '',
    port_metro: '', hostname_olt: '', ip_olt: '', port_onu: '',
    hostname_ont: '', ip_ont: '', ont_type: '', serial_number: '',
    odc: '', odp: '', tikor_site: '', on_air: '', graph_id: '', evidence: ''
  });
  const [stoOptions, setStoOptions] = useState<string[]>([]);
  const [showOltModal, setShowOltModal] = useState(false);
  const [loading, setLoading] = useState(false);

  useEffect(() => { client.get('/api/master-data/sto').then(res => setStoOptions(res.data)); }, []);

  const handleChange = (e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement>) => {
    const { name, value } = e.target;
    setFormData((prev: any) => ({ ...prev, [name]: value }));
  };

  const handleOltSelect = (olt: any) => {
    setFormData((prev: any) => ({
      ...prev,
      hostname_metro: olt.hostname_metro,
      port_metro: olt.port_metro,
      hostname_olt: olt.hostname_olt,
      ip_olt: olt.ip_olt
    }));
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setLoading(true);
    try {
      await client.post('/api/master-data/nodeb', formData);
      alert('Data NodeB berhasil ditambahkan.');
      navigate('/wan/nodeb');
    } catch (err) {
      console.error(err);
      alert('Gagal menyimpan data.');
    } finally { setLoading(false); }
  };

  return (
    <div className="p-6 h-screen overflow-y-auto bg-orbit-bg text-sm text-slate-100">
      <div className="flex items-center justify-between mb-6">
        <div className="flex items-center gap-4">
          <Link to="/wan/nodeb" className="p-2 hover:bg-white/5 rounded-full"><ArrowLeft size={18} /></Link>
          <h2 className="font-bold text-base">ADD DATA NODE-B</h2>
        </div>
        <Button onClick={handleSubmit} disabled={loading} className="bg-blue-600 text-white flex items-center gap-2"><Save size={16} /> {loading ? 'Saving...' : 'Save Data'}</Button>
      </div>

      <OltModal isOpen={showOltModal} onClose={() => setShowOltModal(false)} onSelect={handleOltSelect} />

      <form onSubmit={handleSubmit}>
        <Section title="SITE">
          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div className="flex flex-col gap-1">
              <label className="text-xs uppercase text-slate-400">STO</label>
              <select name="sto" value={formData.sto} onChange={handleChange} className="bg-orbit-surface2 border border-orbit-border p-2 rounded-lg text-sm text-slate-100">
                <option value="">Select STO...</option>
                {stoOptions.map(o => <option key={o} value={o}>{o}</option>)}
              </select>
            </div>
            <Field label="Site ID" name="site_id" value={formData.site_id} onChange={handleChange} />
            <Field label="Site Name" name="site_name" value={formData.site_name} onChange={handleChange} />
            <Field label="Coordinate Site" name="tikor_site" value={formData.tikor_site} onChange={handleChange} />
          </div>
        </Section>
        <Section title="METRO-OLT">
          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            <Field label="Hostname Metro" name="hostname_metro" value={formData.hostname_metro} onChange={handleChange} />
            <Field label="Port Metro" name="port_metro" value={formData.port_metro} onChange={handleChange} />
            <div className="flex flex-col gap-1">
              <label className="text-xs uppercase text-slate-400 font-semibold tracking-wider">Hostname OLT</label>
              <div className="flex gap-2">
                <input name="hostname_olt" value={formData.hostname_olt || ''} onChange={handleChange} className="flex-1 bg-orbit-surface2 border border-orbit-border p-2 rounded-lg text-sm font-mono" />
                <Button type="button" onClick={() => setShowOltModal(true)} className="bg-slate-700"><Search size={16}/></Button>
              </div>
            </div>
            <Field label="IP OLT" name="ip_olt" value={formData.ip_olt} onChange={handleChange} readOnly />
            <Field label="Port ONU" name="port_onu" value={formData.port_onu} onChange={handleChange} />
          </div>
        </Section>
        <Section title="ONT">
          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            <Field label="Hostname ONT" name="hostname_ont" value={formData.hostname_ont} onChange={handleChange} />
            <Field label="IP ONT" name="ip_ont" value={formData.ip_ont} onChange={handleChange} />
            <Field label="ONT Type" name="ont_type" value={formData.ont_type} onChange={handleChange} />
            <Field label="Serial Number" name="serial_number" value={formData.serial_number} onChange={handleChange} />
          </div>
        </Section>
        <Section title="OTHERS_DATA">
          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            <Field label="ODC" name="odc" value={formData.odc} onChange={handleChange} />
            <Field label="ODP" name="odp" value={formData.odp} onChange={handleChange} />
            <Field label="On Air Date" name="on_air" value={formData.on_air} onChange={handleChange} type="date" />
            <Field label="Graph ID" name="graph_id" value={formData.graph_id} onChange={handleChange} />
            <Field label="Evidence" name="evidence" value={formData.evidence} onChange={handleChange} type="file" />
          </div>
        </Section>
      </form>
    </div>
  );
};
