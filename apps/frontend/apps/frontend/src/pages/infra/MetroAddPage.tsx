import React, { useState, useEffect } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { ArrowLeft, Save, Loader2, Search, X } from 'lucide-react';
import client from '@/api/client';
import { Card } from '@/components/ui/Card';
import { Button } from '@/components/ui/Button';
import { Input } from '@/components/ui/Input';

interface StoOption {
  idsto: string;
  nama_sto: string;
}

export const MetroAddPage: React.FC = () => {
  const navigate = useNavigate();
  const [loading, setLoading] = useState(false);
  const [stoOptions, setStoOptions] = useState<StoOption[]>([]);
  const [stoSearch, setStoSearch] = useState('');
  const [showStoDropdown, setShowStoDropdown] = useState(false);
  const [selectedSto, setSelectedSto] = useState<StoOption | null>(null);

  const [formData, setFormData] = useState({
    hostname_metro: '',
    ip_metro: '',
    idsto: ''
  });

  const [errors, setErrors] = useState<Record<string, string>>({});

  useEffect(() => {
    const fetchSto = async () => {
      try {
        const res = await client.get('/api/master-data/sto', {
          params: { limit: 1000, keyword: '' }
        });
        setStoOptions(res.data.data || []);
      } catch (err) {
        console.error('Failed to fetch STO:', err);
      }
    };
    fetchSto();
  }, []);

  const filteredSto = stoOptions.filter(s =>
    s.idsto.toLowerCase().includes(stoSearch.toLowerCase()) ||
    s.nama_sto.toLowerCase().includes(stoSearch.toLowerCase())
  );

  const validate = () => {
    const newErrors: Record<string, string> = {};
    if (!formData.hostname_metro.trim()) newErrors.hostname_metro = 'Hostname Metro wajib diisi';
    if (!formData.ip_metro.trim()) newErrors.ip_metro = 'IP Metro wajib diisi';
    if (!formData.idsto) newErrors.idsto = 'STO wajib dipilih';
    setErrors(newErrors);
    return Object.keys(newErrors).length === 0;
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!validate()) return;

    setLoading(true);
    try {
      await client.post('/api/master-data/metro', {
        hostname_metro: formData.hostname_metro,
        ip_metro: formData.ip_metro,
        idsto: formData.idsto
      });
      navigate('/metro');
    } catch (err: any) {
      console.error('Failed to create metro:', err);
      if (err.response?.data?.detail) {
        alert(err.response.data.detail);
      }
    } finally {
      setLoading(false);
    }
  };

  const handleChange = (e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement>) => {
    const { name, value } = e.target;
    setFormData(prev => ({ ...prev, [name]: value }));
    if (errors[name]) setErrors(prev => ({ ...prev, [name]: '' }));
  };

  return (
    <div className="p-6 space-y-6 h-screen overflow-y-auto bg-orbit-bg text-sm">
      <div className="flex items-center gap-4">
        <Button variant="ghost" size="icon" onClick={() => navigate('/metro')}>
          <ArrowLeft size={20} />
        </Button>
        <h2 className="text-2xl font-bold text-slate-100">Tambah Data Metro</h2>
      </div>

      <Card className="p-6 shadow-sm border border-orbit-border bg-orbit-surface rounded-xl max-w-2xl">
        <form onSubmit={handleSubmit} className="space-y-4">
          <div>
            <label className="block text-sm font-medium text-slate-300 mb-1">Hostname Metro *</label>
            <Input
              name="hostname_metro"
              value={formData.hostname_metro}
              onChange={handleChange}
              placeholder="Masukkan hostname metro"
              className={`w-full ${errors.hostname_metro ? 'border-red-500' : ''}`}
            />
            {errors.hostname_metro && <p className="text-red-500 text-xs mt-1">{errors.hostname_metro}</p>}
          </div>

          <div>
            <label className="block text-sm font-medium text-slate-300 mb-1">IP Metro *</label>
            <Input
              name="ip_metro"
              type="text"
              value={formData.ip_metro}
              onChange={handleChange}
              placeholder="Masukkan IP metro"
              className={`w-full ${errors.ip_metro ? 'border-red-500' : ''}`}
            />
            {errors.ip_metro && <p className="text-red-500 text-xs mt-1">{errors.ip_metro}</p>}
          </div>

          <div>
            <label className="block text-sm font-medium text-slate-300 mb-1">STO *</label>
            <div className="relative">
              <div className="relative">
                <Input
                  name="idsto"
                  type="text"
                  value={selectedSto ? `${selectedSto.idsto} - ${selectedSto.nama_sto}` : ''}
                  onChange={(e) => {
                    setStoSearch(e.target.value);
                    setShowStoDropdown(true);
                  }}
                  onFocus={() => setShowStoDropdown(true)}
                  placeholder="Cari atau pilih STO..."
                  readOnly
                  className={`w-full ${errors.idsto ? 'border-red-500' : ''}`}
                />
                {selectedSto && (
                  <button
                    type="button"
                    className="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-red-500"
                    onClick={(e) => {
                      e.preventDefault();
                      setSelectedSto(null);
                      setFormData(prev => ({ ...prev, idsto: '' }));
                      setStoSearch('');
                    }}
                  >
                    <X size={16} />
                  </button>
                )}
              </div>
              {showStoDropdown && (
                <div className="absolute z-50 w-full mt-1 bg-orbit-surface border border-orbit-border rounded-lg shadow-lg max-h-60 overflow-y-auto">
                  <div className="p-2 border-b border-orbit-border">
                    <Input
                      value={stoSearch}
                      onChange={(e) => setStoSearch(e.target.value)}
                      placeholder="Cari STO..."
                      className="text-sm"
                    />
                  </div>
                  {filteredSto.length === 0 ? (
                    <div className="p-4 text-center text-slate-400 text-sm">STO tidak ditemukan</div>
                  ) : (
                    filteredSto.map(sto => (
                      <button
                        key={sto.idsto}
                        type="button"
                        className="w-full text-left px-3 py-2 hover:bg-orbit-border text-sm flex items-center gap-2"
                        onClick={() => {
                          setSelectedSto(sto);
                          setFormData(prev => ({ ...prev, idsto: sto.idsto }));
                          setStoSearch(`${sto.idsto} - ${sto.nama_sto}`);
                          setShowStoDropdown(false);
                        }}
                      >
                        <span className="font-medium text-slate-100">{sto.idsto}</span>
                        <span className="text-slate-400">{sto.nama_sto}</span>
                      </button>
                    ))
                  )}
                </div>
              )}
            </div>
            {errors.idsto && <p className="text-red-500 text-xs mt-1">{errors.idsto}</p>}
          </div>

          <div className="flex justify-end gap-3 pt-4 border-t border-orbit-border">
            <Button type="button" variant="outline" onClick={() => navigate('/metro')}>
              Batal
            </Button>
            <Button type="submit" disabled={loading}>
              {loading ? <Loader2 size={16} className="animate-spin" /> : <Save size={16} />}
              Simpan
            </Button>
          </div>
        </form>
      </Card>
    </div>
  );
};