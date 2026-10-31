import React, { useState, useEffect } from 'react';
import { useNavigate, useParams } from 'react-router-dom';
import { Card } from '@/components/ui/Card';
import { Button } from '@/components/ui/Button';
import client from '@/api/client';
import { SearchableCombobox } from '@/components/ui/SearchableCombobox';

export const MetroEditPage: React.FC = () => {
  const navigate = useNavigate();
  const { hostname } = useParams<{ hostname: string }>();
  const [loading, setLoading] = useState(false);
  const [stos, setStos] = useState<{ value: string; label: string }[]>([]);
  const [formData, setFormData] = useState({
    hostname_metro: '',
    ip_metro: '',
    idsto: ''
  });

  useEffect(() => {
    client.get('/api/master-data/sto', { params: { limit: 1000 } })
      .then(res => setStos(res.data.data
        .filter((s: any) => s.idsto !== '-')
        .map((s: any) => ({ value: s.idsto, label: `${s.idsto} - ${s.nama_sto || ''}` }))))
      .catch(console.error);

    if (hostname) {
      client.get(`/api/master-data/metro`)
        .then(res => {
          const found = res.data.data.find((item: any) => item.hostname_metro === hostname);
          if (found) setFormData(found);
        })
        .catch(console.error);
    }
  }, [hostname]);

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setLoading(true);
    try {
      await client.patch(`/api/master-data/metro/${hostname}`, formData);
      navigate('/metro');
    } catch (err) { console.error(err); alert('Failed to update metro'); }
    finally { setLoading(false); }
  };

  return (
    <div className="p-6 max-w-2xl mx-auto">
      <Card className="p-6 bg-orbit-surface border-orbit-border">
        <h2 className="text-xl font-bold text-slate-100 mb-6">EDIT METRO: {hostname}</h2>
        <form onSubmit={handleSubmit} className="space-y-4">
          <div>
              <label className="block text-xs font-medium text-slate-400 mb-1">HOSTNAME METRO</label>
              <input disabled value={formData.hostname_metro} 
                className="w-full bg-orbit-bg border border-orbit-border rounded-lg p-2 text-sm text-slate-500 cursor-not-allowed" />
            </div>
            <div>
              <label className="block text-xs font-medium text-slate-400 mb-1">IP METRO</label>
              <input value={formData.ip_metro} onChange={e => setFormData({...formData, ip_metro: e.target.value})} 
                className="w-full bg-orbit-bg border border-orbit-border rounded-lg p-2 text-sm text-slate-100" />
            </div>
            <div>
              <label className="block text-xs font-medium text-slate-400 mb-1">STO</label>
              <SearchableCombobox
                options={stos}
                value={formData.idsto}
                onChange={val => setFormData({...formData, idsto: val})}
                placeholder="Select STO..."
              />
            </div>
          <div className="flex gap-2 pt-4">
            <Button type="submit" disabled={loading} variant="accent">{loading ? 'Saving...' : 'Update Data'}</Button>
            <Button type="button" variant="outline" onClick={() => navigate('/metro')}>Cancel</Button>
          </div>
        </form>
      </Card>
    </div>
  );
};
