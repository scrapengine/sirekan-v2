import React, { useEffect, useState } from 'react';
import { useParams, useNavigate } from 'react-router-dom';
import { assuranceApi } from '../api';
import { Ticket } from '../types';
import { Card } from '@/components/ui/Card';
import { Button } from '@/components/ui/Button';

export const AssuranceTicketDetail: React.FC = () => {
  const { incident } = useParams<{ incident: string }>();
  const navigate = useNavigate();
  const [ticket, setTicket] = useState<Ticket | null>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    const fetchTicketDetail = async () => {
      if (!incident) return;
      try {
        const data = await assuranceApi.get(incident);
        setTicket(data);
      } catch (err: any) {
        setError(err.response?.data?.detail || 'Gagal memuat detail tiket');
      } finally {
        setLoading(false);
      }
    };
    fetchTicketDetail();
  }, [incident]);

  if (loading) return <div className="p-6 text-slate-500">Memuat detail tiket...</div>;
  if (error) return <div className="p-6 text-red-500">{error}</div>;
  if (!ticket) return <div className="p-6 text-slate-500">Tiket tidak ditemukan.</div>;

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold tracking-tight text-slate-900">Detail Tiket: {ticket.incident}</h1>
          <p className="text-sm text-slate-500">Informasi lengkap gangguan dan status penanganan.</p>
        </div>
        <Button variant="outline" onClick={() => navigate('/assurance/tickets')}>Kembali</Button>
      </div>

      <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
        <Card className="p-6 space-y-4">
          <h3 className="font-semibold text-slate-800 border-b pb-2">Informasi Utama</h3>
          <div className="grid grid-cols-2 gap-2 text-sm">
            <span className="text-slate-500">Incident ID:</span>
            <span className="font-medium">{ticket.incident}</span>
            <span className="text-slate-500">Status:</span>
            <span className="font-medium text-amber-600">{ticket.status || '-'}</span>
            <span className="text-slate-500">Customer:</span>
            <span className="font-medium">{ticket.customer || '-'}</span>
            <span className="text-slate-500">Witel:</span>
            <span className="font-medium">{ticket.witel || '-'}</span>
            <span className="text-slate-500">Workzone:</span>
            <span className="font-medium">{ticket.workzone || '-'}</span>
          </div>
        </Card>

        <Card className="p-6 space-y-4">
          <h3 className="font-semibold text-slate-800 border-b pb-2">Waktu & Penugasan</h3>
          <div className="grid grid-cols-2 gap-2 text-sm">
            <span className="text-slate-500">Reported Date:</span>
            <span className="font-medium">{ticket.reported_date || '-'}</span>
            <span className="text-slate-500">Status Date:</span>
            <span className="font-medium">{ticket.status_date || '-'}</span>
            <span className="text-slate-500">Owner Group:</span>
            <span className="font-medium">{ticket.owner_group || '-'}</span>
          </div>
        </Card>
      </div>

      <Card className="p-6 space-y-4">
        <h3 className="font-semibold text-slate-800 border-b pb-2">Summary / Keterangan</h3>
        <p className="text-sm text-slate-700 bg-slate-50 p-4 rounded-lg">{ticket.summary || 'Tidak ada summary.'}</p>
      </Card>
    </div>
  );
};
