import React, { useState, useEffect } from 'react';
import { useNavigate, useParams, Link } from 'react-router-dom';
import { ArrowLeft, Calendar, Clock, Edit, Trash2 } from 'lucide-react';
import client from '@/api/client';
import { Card } from '@/components/ui/Card';
import { Button } from '@/components/ui/Button';

interface OLTDetail {
  hostname_olt: string;
  ip_olt: string;
  port_olt?: string;
  platform?: string;
  type_olt?: string;
  witel?: string;
  hostname_metro?: string;
  ip_metro?: string;
  port_metro?: string;
  idsto?: string;
  created_at?: string;
  updated_at?: string;
  deleted_at?: string | null;
}

export const OLTDetailPage: React.FC = () => {
  const { hostname_olt } = useParams<{ hostname_olt: string }>();
  const navigate = useNavigate();
  const [olt, setOlt] = useState<OLTDetail | null>(null);
  const [loading, setLoading] = useState(true);
  const [fromTrash, setFromTrash] = useState(false);

  useEffect(() => {
    const fetchDetail = async () => {
      try {
        const res = await client.get(`/api/master-data/olt/${hostname_olt}`);
        setOlt(res.data);
        const referrer = document.referrer;
        if (referrer.includes('/olt/trash') || referrer.includes('/trash')) {
          setFromTrash(true);
        }
      } catch (err: any) {
        alert(err.response?.data?.detail || 'Failed to fetch OLT detail');
        navigate('/olt');
      } finally {
        setLoading(false);
      }
    };
    if (hostname_olt) fetchDetail();
  }, [hostname_olt, navigate]);

  if (loading) return <div className="p-6 text-center text-slate-100">Loading...</div>;
  if (!olt) return <div className="p-6 text-center text-red-500">OLT not found</div>;

  return (
    <div className="p-6 space-y-6 h-screen overflow-y-auto bg-orbit-bg text-sm">
      <div className="flex items-center gap-4">
        <Button variant="ghost" size="sm" onClick={() => fromTrash ? navigate('/olt/trash') : navigate('/olt')}>
          <ArrowLeft size={16} /> Back
        </Button>
        <h2 className="text-xl font-bold text-slate-100">OLT Detail: {olt.hostname_olt}</h2>
        {olt.deleted_at && <span className="bg-red-500 text-white px-2 py-0.5 rounded text-xs">TRASH</span>}
      </div>

      <Card className="p-6 shadow-sm border border-orbit-border bg-orbit-surface rounded-xl">
        <h4 className="font-semibold text-lg mb-4 text-slate-100">#OLT IDENTITY</h4>
        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label className="text-xs text-gray-400 block mb-1">Hostname OLT</label>
            <p className="font-semibold text-slate-100">{olt.hostname_olt}</p>
          </div>
          <div>
            <label className="text-xs text-gray-400 block mb-1">IP OLT</label>
            <p className="text-slate-100 font-mono">{olt.ip_olt || '-'}</p>
          </div>
          <div>
            <label className="text-xs text-gray-400 block mb-1">Platform</label>
            <p className="text-slate-100">{olt.platform || '-'}</p>
          </div>
          <div>
            <label className="text-xs text-gray-400 block mb-1">Type OLT</label>
            <p className="text-slate-100">{olt.type_olt || '-'}</p>
          </div>
          <div>
            <label className="text-xs text-gray-400 block mb-1">Port OLT</label>
            <p className="text-slate-100">{olt.port_olt || '-'}</p>
          </div>
        </div>
      </Card>

      <Card className="p-6 shadow-sm border border-orbit-border bg-orbit-surface rounded-xl">
        <h4 className="font-semibold text-lg mb-4 text-slate-100">#LOCATION & NETWORK</h4>
        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label className="text-xs text-gray-400 block mb-1">Witel</label>
            <p className="text-slate-100">{olt.witel || '-'}</p>
          </div>
          <div>
            <label className="text-xs text-gray-400 block mb-1">STO ID</label>
            <p className="text-slate-100">{olt.idsto || '-'}</p>
          </div>
          <div>
            <label className="text-xs text-gray-400 block mb-1">Hostname Metro</label>
            <p className="text-slate-100 font-mono">{olt.hostname_metro || '-'}</p>
          </div>
          <div>
            <label className="text-xs text-gray-400 block mb-1">IP Metro</label>
            <p className="text-slate-100 font-mono">{olt.ip_metro || '-'}</p>
          </div>
          <div>
            <label className="text-xs text-gray-400 block mb-1">Port Metro</label>
            <p className="text-slate-100">{olt.port_metro || '-'}</p>
          </div>
        </div>
      </Card>

      <Card className="p-6 shadow-sm border border-orbit-border bg-orbit-surface rounded-xl">
        <h4 className="font-semibold text-lg mb-4 text-slate-100">#TIMING</h4>
        <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div className="flex items-center gap-2">
            <Calendar size={16} className="text-gray-400" />
            <div>
              <label className="text-xs text-gray-400 block">Created At</label>
              <p className="text-slate-100">{olt.created_at || '-'}</p>
            </div>
          </div>
          <div className="flex items-center gap-2">
            <Clock size={16} className="text-gray-400" />
            <div>
              <label className="text-xs text-gray-400 block">Updated At</label>
              <p className="text-slate-100">{olt.updated_at || '-'}</p>
            </div>
          </div>
          {olt.deleted_at && (
            <div className="flex items-center gap-2">
              <Calendar size={16} className="text-red-500" />
              <div>
                <label className="text-xs text-gray-400 block">Deleted At</label>
                <p className="text-red-500">{olt.deleted_at}</p>
              </div>
            </div>
          )}
        </div>
      </Card>

      <div className="flex gap-2">
        <Link to={`/olt/edit/${olt.hostname_olt}`} className="bg-yellow-600 text-white px-4 py-2 text-xs rounded hover:bg-yellow-700 flex items-center gap-1">
          <Edit size={14} /> Edit
        </Link>
        {!olt.deleted_at && (
          <Button variant="destructive" size="sm" onClick={async () => { 
            if (confirm('Soft-delete this OLT?')) {
              await client.delete(`/api/master-data/olt/${olt.hostname_olt}`);
              navigate('/olt');
            }
          }}>
            <Trash2 size={14} /> Delete
          </Button>
        )}
      </div>
    </div>
  );
};