import React, { useState, useEffect, useCallback } from 'react';
import { Link } from 'react-router-dom';
import { ChevronDown, RotateCcw, Trash, Filter, Download } from 'lucide-react';
import client from '@/api/client';
import { Card } from '@/components/ui/Card';
import { Button } from '@/components/ui/Button';

interface MetroItem {
  hostname_metro: string;
  ip_metro: string;
  idsto: string;
}

export const MetroTrashPage: React.FC = () => {
  const [data, setData] = useState<MetroItem[]>([]);
  const [total, setTotal] = useState(0);
  const [currentPage, setCurrentPage] = useState(1);
  const [pageSize, setPageSize] = useState(25);
  const [loading, setLoading] = useState(false);
  const [search, setSearch] = useState('');

  const fetchData = useCallback(async () => {
    setLoading(true);
    try {
      const res = await client.get('/api/master-data/metro/trash', {
        params: { page: currentPage, limit: pageSize, keyword: search }
      });
      setData(res.data.data);
      setTotal(res.data.meta.total);
    } catch (err) { console.error(err); }
    finally { setLoading(false); }
  }, [currentPage, pageSize, search]);

  useEffect(() => { fetchData(); }, [fetchData]);

  const handleRestore = async (hostname: string) => {
    if (!confirm(`Restore ${hostname}?`)) return;
    try {
      await client.post(`/api/master-data/metro/restore/${hostname}`);
      fetchData();
    } catch (err) { alert('Restore failed'); }
  };

  const handlePurge = async (hostname: string) => {
    if (!confirm(`Permanently delete ${hostname}? This cannot be undone.`)) return;
    try {
      await client.delete(`/api/master-data/metro/purge/${hostname}`);
      fetchData();
    } catch (err) { alert('Purge failed'); }
  };

  const totalPages = Math.ceil(total / pageSize) || 1;

  return (
    <div className="p-6 space-y-6 bg-orbit-bg min-h-screen text-sm">
      <Card className="p-4 bg-orbit-surface border-orbit-border rounded-xl">
        <div className="flex justify-between items-center mb-6">
          <h4 className="font-bold text-lg text-slate-100 flex items-center gap-2">
            <Trash size={20} className="text-red-500"/> METRO TRASH
          </h4>
          <Link to="/metro" className="text-xs text-blue-400 hover:underline">Back to List</Link>
        </div>

        <div className="flex justify-between items-center mb-4">
          <div className="flex gap-2">
            <select value={pageSize} onChange={(e) => { setPageSize(Number(e.target.value)); setCurrentPage(1); }} className="bg-orbit-bg border border-orbit-border p-1.5 rounded text-xs text-slate-100">
              <option value={25}>25</option>
              <option value={50}>50</option>
            </select>
            <input placeholder="Search trash..." value={search} onChange={(e) => setSearch(e.target.value)} className="bg-orbit-bg border border-orbit-border p-1.5 rounded text-xs text-slate-100 w-48" />
          </div>
        </div>

        {loading ? <div className="p-10 text-center text-slate-400">Loading trash...</div> : (
          <div className="overflow-x-auto">
            <table className="w-full text-xs text-left">
              <thead className="bg-white/5 border-b border-orbit-border">
                <tr>
                  <th className="p-3 text-slate-100">Action</th>
                  <th className="p-3 text-slate-100">#</th>
                  <th className="p-3 text-slate-100">Hostname Metro</th>
                  <th className="p-3 text-slate-100">IP Metro</th>
                  <th className="p-3 text-slate-100">STO</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-orbit-border">
                {data.map((item, idx) => (
                  <tr key={item.hostname_metro} className="hover:bg-white/5 transition-colors">
                    <td className="p-3 flex items-center gap-2">
                      <button onClick={() => handleRestore(item.hostname_metro)} className="p-1 hover:bg-blue-500/20 rounded text-blue-400" title="Restore">
                        <RotateCcw size={14}/>
                      </button>
                      <button onClick={() => handlePurge(item.hostname_metro)} className="p-1 hover:bg-red-500/20 rounded text-red-500" title="Purge">
                        <Trash size={14}/>
                      </button>
                    </td>
                    <td className="p-3 text-slate-400">{(currentPage - 1) * pageSize + idx + 1}</td>
                    <td className="p-3 font-semibold text-slate-100">{item.hostname_metro}</td>
                    <td className="p-3 text-slate-300">{item.ip_metro}</td>
                    <td className="p-3 text-slate-300">{item.idsto}</td>
                  </tr>
                ))}
                {data.length === 0 && (
                  <tr><td colSpan={5} className="p-10 text-center text-slate-500">Trash is empty</td></tr>
                )}
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
    </div>
  );
};
