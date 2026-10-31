import React, { useState, useEffect } from 'react';
import { useNavigate, Link, useParams } from 'react-router-dom';
import client from '@/api/client';
import { Button } from '@/components/ui/Button';
import { ArrowLeft, Save, ChevronDown } from 'lucide-react';
import { SearchableCombobox } from '@/components/ui/SearchableCombobox';

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
    <input type={type} name={name} value={value || ''} onChange={onChange} readOnly={readOnly} className={`bg-orbit-surface2 border border-orbit-border p-2 rounded-lg text-sm text-slate-100 focus:outline-none focus:border-blue-500 ${readOnly ? 'opacity-60 cursor-not-allowed' : ''}`} />
  </div>
);

export const OLTEditPage: React.FC = () => {
  const { hostname_olt } = useParams();
  const navigate = useNavigate();
  const [formData, setFormData] = useState<any>({
    witel: '', idsto: '', hostname_metro: '', port_metro: '', hostname_olt: '',
    ip_olt: '', port_olt: '', platform: '', type_olt: ''
  });
  const [stoOptions, setStoOptions] = useState<{value: string, label: string}[]>([]);
  const [metroOptions, setMetroOptions] = useState<{value: string, label: string}[]>([]);
  const [loading, setLoading] = useState(false);
  const [fetching, setFetching] = useState(true);

  useEffect(() => { 
    client.get('/api/master-data/sto', { params: { limit: 1000 } }).then(res => {
      const list = Array.isArray(res.data) ? res.data : (res.data.data || []);
      setStoOptions(list
        .filter((item: any) => item.idsto && item.idsto !== '-')
        .map((item: any) => ({ value: item.idsto, label: `${item.idsto} - ${item.nama_sto || ''}` }))
      );
    }).catch(() => setStoOptions([]));

    client.get('/api/master-data/metro', { params: { limit: 1000 } }).then(res => {
      const list = Array.isArray(res.data) ? res.data : (res.data.data || []);
      setMetroOptions(list.map((item: any) => ({ value: item.hostname_metro, label: `${item.hostname_metro} (${item.ip_metro || ''})` })));
    }).catch(() => setMetroOptions([]));
    
    if (hostname_olt) {
      client.get(`/api/master-data/olt/${hostname_olt}`)
        .then(res => {
          setFormData(res.data);
          setFetching(false);
        })
        .catch(err => {
          console.error(err);
          alert('Gagal mengambil data OLT.');
          navigate('/olt');
        });
    }
  }, [hostname_olt, navigate]);

  const handleChange = (e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement>) => {
    const { name, value } = e.target;
    setFormData((prev: any) => ({ ...prev, [name]: value }));
  };

  const handleComboboxChange = (name: string, value: string) => {
    setFormData((prev: any) => ({ ...prev, [name]: value }));
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setLoading(true);
    try {
      await client.patch(`/api/master-data/olt/${hostname_olt}`, formData);
      alert('Data OLT berhasil diperbarui.');
      navigate('/olt');
    } catch (err) {
      console.error(err);
      alert('Gagal memperbarui data.');
    } finally { setLoading(false); }
  };

  if (fetching) return <div className="p-6 text-slate-100">Loading...</div>;

  return (
    <div className="p-6 h-screen overflow-y-auto bg-orbit-bg text-sm text-slate-100">
      <div className="flex items-center justify-between mb-6">
        <div className="flex items-center gap-4">
          <Link to="/olt" className="p-2 hover:bg-white/5 rounded-full"><ArrowLeft size={18} /></Link>
          <h2 className="font-bold text-base">EDIT DATA OLT: {hostname_olt}</h2>
        </div>
        <Button onClick={handleSubmit} disabled={loading} className="bg-blue-600 text-white flex items-center gap-2"><Save size={16} /> {loading ? 'Saving...' : 'Update Data'}</Button>
      </div>

      <form onSubmit={handleSubmit}>
        <Section title="OLT IDENTITY & LOCATION">
          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            <Field label="Witel" name="witel" value={formData.witel} onChange={handleChange} />
            <SearchableCombobox
              label="STO"
              value={formData.idsto}
              onChange={(v) => handleComboboxChange('idsto', v)}
              options={stoOptions}
              placeholder="Select STO..."
            />
            <SearchableCombobox
              label="Hostname Metro"
              value={formData.hostname_metro}
              onChange={(v) => handleComboboxChange('hostname_metro', v)}
              options={metroOptions}
              placeholder="Select Metro..."
            />
            <Field label="Port Metro" name="port_metro" value={formData.port_metro} onChange={handleChange} />
            <Field label="Hostname OLT" name="hostname_olt" value={formData.hostname_olt} onChange={handleChange} readOnly={true} />
            <Field label="IP OLT" name="ip_olt" value={formData.ip_olt} onChange={handleChange} />
            <Field label="Port OLT" name="port_olt" value={formData.port_olt} onChange={handleChange} />
            <Field label="Platform" name="platform" value={formData.platform} onChange={handleChange} />
            <Field label="Type OLT" name="type_olt" value={formData.type_olt} onChange={handleChange} />
            <Field label="Lokasi" name="lokasi" value={formData.lokasi} onChange={handleChange} />
            <Field label="Koordinat" name="koordinat" value={formData.koordinat} onChange={handleChange} />
          </div>
        </Section>
      </form>
    </div>
  );
};
