import React, { useState, useEffect } from 'react';
import { useParams, useNavigate, Link } from 'react-router-dom';
import { ArrowLeft, Save, Edit } from 'lucide-react';
import client from '@/api/client';
import { Card } from '@/components/ui/Card';
import { Button } from '@/components/ui/Button';

export const STODetailPage: React.FC = () => {
  const { id } = useParams<{ id: string }>();
  const navigate = useNavigate();
  const [data, setData] = useState<any>(null);

  useEffect(() => {
    client.get(`/api/master-data/sto/${id}`)
      .then(res => setData(res.data))
      .catch(() => navigate('/sto'));
  }, [id, navigate]);

  if (!data) return <div className="p-6 text-center text-slate-100">Loading...</div>;

  return (
    <div className="p-6 bg-orbit-bg h-screen text-slate-100 overflow-y-auto">
      <div className="max-w-3xl mx-auto pb-10">
        <Link to="/sto" className="flex items-center gap-2 text-blue-400 text-sm mb-4"><ArrowLeft size={16} /> Back to List</Link>
        <Card className="p-6 bg-orbit-surface border-orbit-border rounded-xl">
          <div className="flex justify-between items-start mb-6">
            <div>
              <h2 className="text-2xl font-bold text-slate-100">{data.nama_sto}</h2>
              <p className="text-slate-400">STO ID: {data.idsto}</p>
            </div>
            <Button variant="outline" onClick={() => navigate(`/sto/edit/${id}`)}>
               <Edit size={14} className="mr-1" /> Edit
            </Button>
          </div>
          <div className="grid grid-cols-2 gap-8">
            <div className="space-y-4">
              <div>
                <label className="text-xs text-slate-500 uppercase font-bold">Witel</label>
                <p className="text-slate-200">{data.witel}</p>
              </div>
              <div>
                <label className="text-xs text-slate-500 uppercase font-bold">Coordinates</label>
                <p className="text-slate-200">{data.latitude}, {data.longitude}</p>
              </div>
            </div>
            <div className="space-y-4">
              <div>
                <label className="text-xs text-slate-500 uppercase font-bold">Address</label>
                <p className="text-slate-200">{data.alamat || '-'}</p>
              </div>
            </div>
          </div>
        </Card>
      </div>
    </div>
  );
};
