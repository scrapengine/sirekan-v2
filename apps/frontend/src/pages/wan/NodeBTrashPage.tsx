import React, { useState, useEffect, useCallback } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import client from '@/api/client';
import { Card } from '@/components/ui/Card';
import { Button } from '@/components/ui/Button';
import { ArrowLeft, Eye, RotateCcw, Trash2, RotateCw, XCircle } from 'lucide-react';

interface NodeBItem {
  idnodeb: number;
  sto?: string;
  site_id: string;
  site_name: string;
  hostname_metro: string;
  ip_metro: string;
  port_metro: string;
  hostname_olt: string;
  port_onu: string;
  hostname_ont: string;
  ip_ont: string;
  ont_type: string;
  serial_number: string;
}

export const NodeBTrashPage: React.FC = () => {
  const navigate = useNavigate();
  const [data, setData] = useState<NodeBItem[]>([]);
  const [total, setTotal] = useState(0);
  const [totalPages, setTotalPages] = useState(0);
  const [currentPage, setCurrentPage] = useState(1);
  const [pageSize, setPageSize] = useState(25);
  const [loading, setLoading] = useState(false);
  const [search, setSearch] = useState('');
  const [debouncedSearch, setDebouncedSearch] = useState('');

  useEffect(() => {
    const handler = setTimeout(() => setDebouncedSearch(search), 400);
    return () => clearTimeout(handler);
  }, [search]);

  const fetchData = useCallback(async () => {
    setLoading(true);
    try {
      const res = await client.get('/api/master-data/nodeb/trash', {
        params: { page: currentPage, limit: pageSize, search: debouncedSearch }
      });
      setData(res.data.data);
      setTotal(res.data.meta.total);
      setTotalPages(res.data.meta.total_pages);
    } catch (err) { console.error(err); } finally { setLoading(false); }
  }, [currentPage, pageSize, debouncedSearch]);

  useEffect(() => { fetchData(); }, [fetchData]);

  const handleRestore = async (id: number) => {
    if (confirm('Restore this NodeB data?')) {
      await client.post(`/api/master-data/nodeb/restore/${id}`);
      fetchData();
    }
  };

  const handlePurge = async (id: number) => {
    if (confirm('Permanently delete this NodeB data? This action cannot be undone.')) {
      await client.delete(`/api/master-data/nodeb/purge/${id}`);
      fetchData();
    }
  };

  const handleRestoreAll = async () => {
    if (confirm('Restore ALL deleted NodeB data?')) {
      await client.post('/api/master-data/nodeb/restore-all');
      fetchData();
    }
  };

  const handlePurgeAll = async () => {
    if (confirm('Permanently delete ALL trash NodeB data? This action cannot be undone.')) {
      await client.delete('/api/master-data/nodeb/purge-all');
      fetchData();
    }
  };

  return (
    <div className="p-6 space-y-6 h-screen overflow-y-auto bg-orbit-bg text-sm">
      <Card className="p-4 shadow-sm border border-orbit-border bg-orbit-surface rounded-xl">
        <div className="flex justify-between items-center mb-4">
          <div className="flex items-center gap-3">
            <button onClick={() => navigate('/wan/nodeb')} title="Back" className="p-2 hover:bg-white/5 rounded-full text-slate-400 transition-colors">
              <ArrowLeft size={18} />
            </button>
            <h4 className="font-bold text-lg text-slate-100">TRASH - DATA NODE-B</h4>
          </div>
          <div className="flex gap-2">
            <button onClick={handleRestoreAll} title="Restore All" className="p-2 hover:bg-white/5 rounded-full text-sky-400 transition-colors">
              <RotateCw size={18} />
            </button>
            <button onClick={handlePurgeAll} title="Delete All" className="p-2 hover:bg-white/5 rounded-full text-red-500 transition-colors">
              <XCircle size={18} />
            </button>
          </div>
        </div>

        <div className="flex justify-between items-center mb-4">
          <div className="flex gap-2 items-center">
            <select value={pageSize} onChange={(e) => { setPageSize(Number(e.target.value)); setCurrentPage(1); }} className="border p-1.5 rounded-lg text-xs bg-orbit-surface2 text-slate-100 border-orbit-border">
              <option value={25}>25</option><option value={50}>50</option><option value={100}>100</option>
            </select>
            <input placeholder="Search Trash..." value={search} onChange={(e) => { setSearch(e.target.value); setCurrentPage(1); }} className="border p-1.5 rounded-lg text-xs w-48 bg-orbit-surface2 text-slate-100 border-orbit-border" />
          </div>
        </div>

        {loading ? <div className="p-10 text-center text-slate-400">Loading Trash...</div> : (
          <div className="overflow-x-auto">
            <table className="w-full text-xs text-left">
              <thead className="bg-orbit-surface2 border-b border-orbit-border">
                <tr>
                  <th className="p-3 text-slate-300 text-center">Action</th>
                  <th className="p-3 text-slate-300">#</th>
                  <th className="p-3 text-slate-300">STO</th>
                  <th className="p-3 text-slate-300">Site ID</th>
                  <th className="p-3 text-slate-300">Site Name</th>
                  <th className="p-3 text-slate-300">Hostname Metro</th>
                  <th className="p-3 text-slate-300">Port Metro</th>
                  <th className="p-3 text-slate-300">IP OLT</th>
                  <th className="p-3 text-slate-300">Port ONU</th>
                  <th className="p-3 text-slate-300">IP ONT</th>
                  <th className="p-3 text-slate-300">Serial Number</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-orbit-border">
                {data.map((item, idx) => (
                  <tr key={item.idnodeb} className="hover:bg-white/5 transition-colors">
                    <td className="p-3 text-center">
                      <div className="flex items-center justify-center gap-2 text-slate-400">
                        <Link to={`/wan/nodeb/detail/${item.idnodeb}/${item.site_id.toLowerCase().replace(/\s+/g, '-')}?from=trash`} title="View Detail">
                          <Eye size={14} className="hover:text-orbit-primary transition-colors"/>
                        </Link>
                        <button onClick={() => handleRestore(item.idnodeb)} title="Restore Data" className="text-sky-400 hover:text-sky-300">
                          <RotateCcw size={14} />
                        </button>
                        <button onClick={() => handlePurge(item.idnodeb)} title="Delete Permanently" className="text-red-500 hover:text-red-400">
                          <Trash2 size={14} />
                        </button>
                      </div>
                    </td>
                    <td className="p-3 text-slate-400">{(currentPage - 1) * pageSize + idx + 1}</td>
                    <td className="p-3 text-slate-400">{item.sto}</td>
                    <td className="p-3 font-semibold text-slate-100">{item.site_id}</td>
                    <td className="p-3 text-slate-400">{item.site_name}</td>
                    <td className="p-3 text-slate-400">{item.hostname_metro}</td>
                    <td className="p-3 text-slate-400">{item.port_metro}</td>
                    <td className="p-3 text-slate-400">{item.ip_olt}</td>
                    <td className="p-3 text-slate-400">{item.port_onu}</td>
                    <td className="p-3 text-slate-400">{item.ip_ont}</td>
                    <td className="p-3 text-slate-400">{item.serial_number}</td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        )}

        <div className="flex justify-between items-center mt-4 text-xs text-slate-400">
          <span>Menampilkan {data.length} dari {total} records</span>
          <div className="flex gap-2">
            <Button size="sm" variant="outline" disabled={currentPage === 1} onClick={() => setCurrentPage(currentPage - 1)}>Prev</Button>
            <span>Page {currentPage} of {totalPages}</span>
            <Button size="sm" variant="outline" disabled={currentPage >= totalPages} onClick={() => setCurrentPage(currentPage + 1)}>Next</Button>
          </div>
        </div>
      </Card>
    </div>
  );
};
