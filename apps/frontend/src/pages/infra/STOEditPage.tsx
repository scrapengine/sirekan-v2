import React, { useState, useEffect } from 'react';
import { useNavigate, useParams, Link } from 'react-router-dom';
import { ArrowLeft, Save } from 'lucide-react';
import client from '@/api/client';
import { Card } from '@/components/ui/Card';
import { Button } from '@/components/ui/Button';
import { SearchableCombobox } from '@/components/ui/SearchableCombobox';

interface Option { value: string; label: string; }

export const STOEditPage: React.FC = () => {
  const { id } = useParams<{ id: string }>();
  const navigate = useNavigate();
  const [formData, setFormData] = useState({
    witel: '', idsto: '', nama_sto: '', longitude: '', latitude: '', alamat: ''
  });
  const [loading, setLoading] = useState(false);
  const [witelOptions, setWitelOptions] = useState<Option[]>([]);

  useEffect(() => {
    client.get('/api/master-data/sto', { params: { limit: 1000 } })
      .then(res => {
        const list = Array.isArray(res.data) ? res.data : (res.data.data || []);
        const witels = [...new Set(list.map((s: any) => s.witel).filter(Boolean))];
        setWitelOptions(witels.map(w => ({ value: String(w), label: String(w) })));
      }).catch(console.error);

    if (id) {
      client.get(`/api/master-data/sto/${id}`)
        .then(res => setFormData(res.data))
        .catch(() => navigate('/sto'));
    }
  }, [id, navigate]);

  const handleChange = (field: string, value: any) => setFormData(prev => ({ ...prev, [field]: value }));

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!formData.nama_sto?.trim()) return alert('Name required');
    setLoading(true);
    try {
      await client.patch(`/api/master-data/sto/${id}`, formData);
      navigate('/sto');
    } catch (err: any) { alert(err.response?.data?.detail || 'Failed'); }
    finally { setLoading(false); }
  };

  return (
    <div className="p-6 bg-orbit-bg min-h-screen text-slate-100">
      <div className="max-w-2xl mx-auto space-y-6">
        <Link to="/sto" className="flex items-center gap-2 text-blue-400 text-sm"><ArrowLeft size={16} /> Back</Link>
        <Card className="p-6 bg-orbit-surface border-orbit-border rounded-xl">
          <h2 className="text-xl font-bold mb-6">Edit STO: {id}</h2>
          <form onSubmit={handleSubmit} className="space-y-4">
            <div>
              <label className="text-xs font-semibold text-slate-400 mb-1 block">Witel</label>
              <input 
                name="witel" 
                value={formData.witel || ''} 
                onChange={e => handleChange('witel', e.target.value)} 
                className="w-full bg-orbit-bg border border-orbit-border rounded-lg p-2 text-sm text-slate-100 focus:outline-none focus:border-blue-500" 
              />
            </div>
            <div>
              <label className="text-xs font-semibold text-slate-400 mb-1 block">ID STO (Locked)</label>
              <input disabled value={formData.idsto} className="w-full bg-orbit-bg border border-orbit-border rounded-lg p-2 text-sm opacity-50 cursor-not-allowed" />
            </div>
            <div>
              <label className="text-xs font-semibold text-slate-400 mb-1 block">Nama STO *</label>
              <input required value={formData.nama_sto || ''} onChange={e => handleChange('nama_sto', e.target.value)} className="w-full bg-orbit-bg border border-orbit-border rounded-lg p-2 text-sm" />
            </div>
            <div className="grid grid-cols-2 gap-4">
              <div>
                <label className="text-xs font-semibold text-slate-400 mb-1 block">Longitude</label>
                <input value={formData.longitude || ''} onChange={e => handleChange('longitude', e.target.value)} className="w-full bg-orbit-bg border border-orbit-border rounded-lg p-2 text-sm" />
              </div>
              <div>
                <label className="text-xs font-semibold text-slate-400 mb-1 block">Latitude</label>
                <input value={formData.latitude || ''} onChange={e => handleChange('latitude', e.target.value)} className="w-full bg-orbit-bg border border-orbit-border rounded-lg p-2 text-sm" />
              </div>
            </div>
            <div>
              <label className="text-xs font-semibold text-slate-400 mb-1 block">Alamat</label>
              <textarea value={formData.alamat || ''} onChange={e => handleChange('alamat', e.target.value)} className="w-full bg-orbit-bg border border-orbit-border rounded-lg p-2 text-sm h-20" />
            </div>
            <div className="flex justify-end gap-2 pt-4">
              <Button type="button" variant="outline" onClick={() => navigate('/sto')}>Cancel</Button>
              <Button type="submit" disabled={loading} variant="accent"><Save size={16} className="mr-2"/> Update</Button>
            </div>
          </form>
        </Card>
      </div>
    </div>
  );
};