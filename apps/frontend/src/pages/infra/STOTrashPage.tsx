import React, { useState, useEffect, useCallback } from 'react';
import { Link } from 'react-router-dom';
import { Filter, ChevronDown, Trash2, ArrowLeft, RotateCcw } from 'lucide-react';
import client from '@/api/client';
import { Card } from '@/components/ui/Card';
import { Button } from '@/components/ui/Button';
import { Modal } from '@/components/ui/Modal';

interface STOItem {
  idsto: string;
  nama_sto: string;
  witel: string;
  deleted_at: string;
}

export const STOTrashPage: React.FC = () => {
  const [data, setData] = useState<STOItem[]>([]);
  const [total, setTotal] = useState(0);
  const [currentPage, setCurrentPage] = useState(1);
  const [pageSize, setPageSize] = useState(25);
  const [loading, setLoading] = useState(false);
  const [search, setSearch] = useState('');
  const [debouncedSearch, setDebouncedSearch] = useState('');
  
  const [showRestoreModal, setShowRestoreModal] = useState(false);
  const [stoToRestore, setStoToRestore] = useState<string | null>(null);

  const [showPurgeModal, setShowPurgeModal] = useState(false);
  const [stoToPurge, setStoToPurge] = useState<string | null>(null);

  useEffect(() => {
    const handler = setTimeout(() => setDebouncedSearch(search), 400);
    return () => clearTimeout(handler);
  }, [search]);

  const fetchTrash = useCallback(async () => {
    setLoading(true);
    try {
      const res = await client.get('/api/master-data/sto-trash', {
        params: { page: currentPage, limit: pageSize, keyword: debouncedSearch }
      });
      if (res.data && res.data.data) {
        setData(res.data.data);
        setTotal(res.data.meta?.total || 0);
      }
    } catch (err) {
      console.error(err);
      setData([]);
    } finally {
      setLoading(false);
    }
  }, [currentPage, pageSize, debouncedSearch]);

  useEffect(() => { fetchTrash(); }, [fetchTrash]);

  const handleRestore = async () => {
    if (stoToRestore) {
      try {
        await client.post(`/api/master-data/sto/restore/${stoToRestore}`);
        fetchTrash();
        setShowRestoreModal(false);
        setStoToRestore(null);
      } catch (err: any) { alert(err.response?.data?.detail || 'Gagal restore'); }
    }
  };

  const handlePurge = async () => {
    if (stoToPurge) {
      try {
        await client.delete(`/api/master-data/sto/purge/${stoToPurge}`);
        fetchTrash();
        setShowPurgeModal(false);
        setStoToPurge(null);
      } catch (err: any) { alert(err.response?.data?.detail || 'Gagal hapus permanen'); }
    }
  };

  const totalPages = Math.ceil(total / pageSize) || 1;

  return (
    <div className="p-6 space-y-6 h-screen overflow-y-auto bg-orbit-bg text-sm text-slate-100">
      <div className="flex items-center gap-4">
        <Link to="/sto" className="text-blue-400 hover:text-blue-300 flex items-center gap-1">
          <ArrowLeft size={16} /> Back to STO
        </Link>
        <h2 className="text-xl font-bold">Trash STO</h2>
      </div>

      <Card className="p-4 shadow-sm border border-orbit-border bg-orbit-surface rounded-xl">
        <div className="flex justify-between items-center mb-4">
          <div className="flex gap-2 items-center">
            <select value={pageSize} onChange={(e) => { setPageSize(Number(e.target.value)); setCurrentPage(1); }} className="border border-orbit-border p-1.5 rounded-lg text-xs bg-orbit-surface text-slate-100">
              <option value={25}>25</option>
              <option value={50}>50</option>
              <option value={100}>100</option>
            </select>
            <input placeholder="Search trash..." value={search} onChange={(e) => { setSearch(e.target.value); setCurrentPage(1); }} className="border border-orbit-border p-1.5 rounded-lg text-xs w-48 bg-orbit-surface text-slate-100" />
          </div>
        </div>

        {loading ? <div className="p-10 text-center">Loading...</div> : (
          <div className="overflow-x-auto">
            <table className="w-full text-xs text-left">
              <thead className="bg-orbit-surface border-b border-orbit-border">
                <tr>
                  <th className="p-3">Action</th>
                  <th className="p-3">#</th>
                  <th className="p-3">ID STO</th>
                  <th className="p-3">Nama STO</th>
                  <th className="p-3">Witel</th>
                  <th className="p-3">Deleted At</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-orbit-border">
                {data.map((item, idx) => (
                  <tr key={item.idsto} className="hover:bg-white/5">
                    <td className="p-3 flex items-center gap-2">
                      <Button size="sm" variant="outline" className="text-emerald-400 hover:text-emerald-300" onClick={() => { setStoToRestore(item.idsto); setShowRestoreModal(true); }}>
                        <RotateCcw size={14} className="mr-1" /> Restore
                      </Button>
                      <Button size="sm" variant="destructive" onClick={() => { setStoToPurge(item.idsto); setShowPurgeModal(true); }}>
                        <Trash2 size={14} className="mr-1" /> Delete
                      </Button>
                    </td>
                    <td className="p-3">{(currentPage - 1) * pageSize + idx + 1}</td>
                    <td className="p-3 font-semibold">{item.idsto}</td>
                    <td className="p-3">{item.nama_sto}</td>
                    <td className="p-3">{item.witel}</td>
                    <td className="p-3">{item.deleted_at}</td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        )}

        <div className="flex justify-between items-center mt-4 border-t border-orbit-border pt-4 text-xs text-slate-400">
          <span>Total: {total} records</span>
          <div className="flex gap-2">
            <Button size="sm" variant="outline" disabled={currentPage === 1} onClick={() => setCurrentPage(currentPage - 1)}>Prev</Button>
            <span>Page {currentPage} of {totalPages}</span>
            <Button size="sm" variant="outline" disabled={currentPage >= totalPages} onClick={() => setCurrentPage(currentPage + 1)}>Next</Button>
          </div>
        </div>
      </Card>

      <Modal isOpen={showRestoreModal} onClose={() => setShowRestoreModal(false)} title="Confirm Restore">
        <div className="space-y-4">
          <p className="text-slate-300">Restore STO ID: {stoToRestore}?</p>
          <div className="flex justify-end gap-2">
            <Button variant="outline" onClick={() => setShowRestoreModal(false)}>Cancel</Button>
            <Button variant="accent" onClick={handleRestore}>Restore</Button>
          </div>
        </div>
      </Modal>

      <Modal isOpen={showPurgeModal} onClose={() => setShowPurgeModal(false)} title="Confirm Permanent Delete">
        <div className="space-y-4">
          <p className="text-slate-300">Permanently delete STO ID: {stoToPurge}? This action cannot be undone.</p>
          <div className="flex justify-end gap-2">
            <Button variant="outline" onClick={() => setShowPurgeModal(false)}>Cancel</Button>
            <Button variant="destructive" onClick={handlePurge}>Delete Permanently</Button>
          </div>
        </div>
      </Modal>
    </div>
  );
};
