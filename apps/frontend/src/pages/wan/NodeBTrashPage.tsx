import React, { useState, useEffect, useCallback } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import client from '@/api/client';
import { Card } from '@/components/ui/Card';
import { Button } from '@/components/ui/Button';
import {
  ArrowLeft, Eye, RotateCcw, Trash2, RotateCw, XCircle,
  ChevronDown, ChevronUp, ChevronsUpDown
} from 'lucide-react';

interface NodeBItem {
  idnodeb: number;
  sto?: string;
  site_id: string;
  site_name: string;
  hostname_metro: string;
  ip_metro: string;
  port_metro: string;
  hostname_olt: string;
  ip_olt: string;
  port_onu: string;
  hostname_ont: string;
  ip_ont: string;
  ont_type: string;
  serial_number: string;
  odc: string;
  odp: string;
  tikor_site: string;
  on_air: string;
  deleted_at: string;
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
  const [showColDropdown, setShowColDropdown] = useState(false);

  const [columnVisibility, setColumnVisibility] = useState<{ [key: string]: boolean }>({
    action: true,
    number: true,
    idsto: true,
    site_id: true,
    site_name: true,
    hostname_metro: true,
    ip_metro: false,
    port_metro: true,
    hostname_olt: false,
    ip_olt: true,
    port_onu: true,
    hostname_ont: false,
    ip_ont: true,
    ont_type: false,
    serial_number: true,
    odc: false,
    odp: false,
    tikor_site: false,
    on_air: false,
  });

  const [sortConfig, setSortConfig] = useState<{ key: string; direction: 'asc' | 'desc' }>({ 
    key: 'deleted_at', 
    direction: 'desc' 
  });

  const columnsDef = [
    { key: 'action', label: 'Action' },
    { key: 'number', label: '#' },
    { key: 'idsto', label: 'STO' },
    { key: 'site_id', label: 'Site ID' },
    { key: 'site_name', label: 'Site Name' },
    { key: 'hostname_metro', label: 'Hostname Metro' },
    { key: 'ip_metro', label: 'IP Metro' },
    { key: 'port_metro', label: 'Port Metro' },
    { key: 'hostname_olt', label: 'Hostname OLT' },
    { key: 'ip_olt', label: 'IP OLT' },
    { key: 'port_onu', label: 'Port Onu' },
    { key: 'hostname_ont', label: 'Hostname ONT' },
    { key: 'ip_ont', label: 'IP ONT' },
    { key: 'ont_type', label: 'ONT Type' },
    { key: 'serial_number', label: 'Serial Number' },
    { key: 'odc', label: 'ODC' },
    { key: 'odp', label: 'ODP' },
    { key: 'tikor_site', label: 'Coordinate' },
    { key: 'on_air', label: 'On Air' },
  ];

  const toggleColumn = (key: string) => setColumnVisibility(prev => ({ ...prev, [key]: !prev[key] }));

  const handleSort = (key: string) => {
    let direction: 'asc' | 'desc' = 'asc';
    if (sortConfig.key === key && sortConfig.direction === 'asc') {
      direction = 'desc';
    }
    setSortConfig({ key, direction });
  };

  const getSortIcon = (key: string) => {
    if (sortConfig.key !== key) return <ChevronsUpDown size={12} className="text-slate-500" />;
    return sortConfig.direction === 'asc' ? <ChevronUp size={12} className="text-orbit-primary" /> : <ChevronDown size={12} className="text-orbit-primary" />;
  };

  const SkeletonRow = () => (
    <tr className="animate-pulse">
      {columnsDef.map(col => columnVisibility[col.key] && (
        <td key={col.key} className="p-3">
          <div className="h-4 bg-slate-800 rounded w-full"></div>
        </td>
      ))}
    </tr>
  );

  useEffect(() => {
    const handler = setTimeout(() => setDebouncedSearch(search), 400);
    return () => clearTimeout(handler);
  }, [search]);

  const fetchData = useCallback(async () => {
    setLoading(true);
    try {
      const params: any = {
        page: currentPage,
        limit: pageSize,
        search: debouncedSearch,
        sort_by: sortConfig.key,
        order: sortConfig.direction
      };

      const res = await client.get('/api/master-data/nodeb/trash', { params });
      setData(res.data.data);
      setTotal(res.data.meta.total);
      setTotalPages(res.data.meta.total_pages);
    } catch (err) { console.error(err); } finally { setLoading(false); }
  }, [currentPage, pageSize, debouncedSearch, sortConfig.key, sortConfig.direction]);

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
            <Button size="sm" variant="outline" onClick={handleRestoreAll} className="text-sky-400 hover:text-sky-300">
              <RotateCw size={14} className="mr-2" /> Restore All
            </Button>
            <Button size="sm" variant="outline" onClick={handlePurgeAll} className="text-red-500 hover:text-red-400">
              <XCircle size={14} className="mr-2" /> Delete All
            </Button>
          </div>
        </div>

        <div className="flex justify-between items-center mb-4">
          <div className="flex gap-2 items-center">
            <select value={pageSize} onChange={(e) => { setPageSize(Number(e.target.value)); setCurrentPage(1); }} className="border p-1.5 rounded-lg text-xs bg-orbit-surface2 text-slate-100 border-orbit-border">
              <option value={25}>25</option><option value={50}>50</option><option value={100}>100</option>
            </select>
            <input placeholder="Search..." value={search} onChange={(e) => { setSearch(e.target.value); setCurrentPage(1); }} className="border p-1.5 rounded-lg text-xs w-48 bg-orbit-surface2 text-slate-100 border-orbit-border" />
          </div>
          <Button size="sm" variant="outline" onClick={() => setShowColDropdown(!showColDropdown)}>
            Columns <ChevronDown size={14}/>
          </Button>
        </div>

        {showColDropdown && (
          <div className="absolute z-50 bg-white border rounded shadow p-2 text-xs space-y-1 right-6 text-black">
            {columnsDef.map(col => (
              <label key={col.key} className="flex items-center gap-2 hover:bg-gray-50 p-1 cursor-pointer">
                <input type="checkbox" checked={columnVisibility[col.key]} onChange={() => toggleColumn(col.key)} /> {col.label}
              </label>
            ))}
          </div>
        )}

        <div className="overflow-x-auto" style={{ maxHeight: 'calc(100vh - 250px)', overflowY: 'auto' }}>
          <table className="w-full text-xs text-left border-collapse">
            <thead className="bg-orbit-surface border-b border-orbit-border sticky top-0 z-10">
              <tr>
                {columnsDef.map(col => columnVisibility[col.key] && (
                  <th 
                    key={col.key} 
                    className="p-3 text-slate-300 cursor-pointer select-none whitespace-nowrap bg-orbit-surface"
                    onClick={() => col.key !== 'action' && col.key !== 'number' && handleSort(col.key)}
                  >
                    <div className="flex items-center gap-1">
                      {col.label}
                      {col.key !== 'action' && col.key !== 'number' && getSortIcon(col.key)}
                    </div>
                  </th>
                ))}
              </tr>
            </thead>
            <tbody className="divide-y divide-orbit-border">
              {loading && data.length === 0 ? (
                Array.from({ length: pageSize > 0 ? pageSize : 10 }).map((_, i) => <SkeletonRow key={i} />)
              ) : (
                data.map((item, idx) => (
                  <tr key={item.idnodeb} className="hover:bg-blue-50/50 dark:hover:bg-slate-800/50 transition-colors">
                    {columnVisibility.action && (
                      <td className="p-3 text-slate-400 align-middle">
                        <div className="flex items-center justify-center gap-1">
                          <Link to={`/wan/nodeb/detail/${item.idnodeb}/${item.site_id.toLowerCase().replace(/\s+/g, '-')}?from=trash`} title="View Detail" className="p-1 hover:bg-white/5 rounded">
                            <Eye size={14} className="hover:text-orbit-primary transition-colors"/>
                          </Link>
                          <button onClick={() => handleRestore(item.idnodeb)} title="Restore Data" className="p-1 text-sky-400 hover:text-sky-300 hover:bg-white/5 rounded">
                            <RotateCcw size={14} />
                          </button>
                          <button onClick={() => handlePurge(item.idnodeb)} title="Delete Permanently" className="p-1 text-red-500 hover:text-red-400 hover:bg-white/5 rounded">
                            <Trash2 size={14} />
                          </button>
                        </div>
                      </td>
                    )}
                    {columnVisibility.number && <td className="p-3 text-slate-400 align-middle">{(currentPage - 1) * pageSize + idx + 1}</td>}
                    {columnVisibility.idsto && <td className="p-3 text-slate-400 align-middle">{item.sto}</td>}
                    {columnVisibility.site_id && <td className="p-3 font-semibold text-slate-100 align-middle">{item.site_id}</td>}
                    {columnVisibility.site_name && <td className="p-3 text-slate-400 align-middle">{item.site_name}</td>}
                    {columnVisibility.hostname_metro && <td className="p-3 text-slate-400 align-middle">{item.hostname_metro}</td>}
                    {columnVisibility.ip_metro && <td className="p-3 text-slate-400 align-middle">{item.ip_metro}</td>}
                    {columnVisibility.port_metro && <td className="p-3 text-slate-400 align-middle">{item.port_metro}</td>}
                    {columnVisibility.hostname_olt && <td className="p-3 text-slate-400 align-middle">{item.hostname_olt}</td>}
                    {columnVisibility.ip_olt && <td className="p-3 text-slate-400 align-middle">{item.ip_olt}</td>}
                    {columnVisibility.port_onu && <td className="p-3 text-slate-400 align-middle">{item.port_onu}</td>}
                    {columnVisibility.hostname_ont && <td className="p-3 text-slate-400 align-middle">{item.hostname_ont}</td>}
                    {columnVisibility.ip_ont && <td className="p-3 text-slate-400 align-middle">{item.ip_ont}</td>}
                    {columnVisibility.ont_type && <td className="p-3 text-slate-400 align-middle">{item.ont_type}</td>}
                    {columnVisibility.serial_number && <td className="p-3 text-slate-400 align-middle">{item.serial_number}</td>}
                    {columnVisibility.odc && <td className="p-3 text-slate-400 align-middle">{item.odc}</td>}
                    {columnVisibility.odp && <td className="p-3 text-slate-400 align-middle">{item.odp}</td>}
                    {columnVisibility.tikor_site && <td className="p-3 text-slate-400 align-middle">{item.tikor_site}</td>}
                    {columnVisibility.on_air && <td className="p-3 text-slate-400 align-middle">{item.on_air}</td>}
                  </tr>
                ))
              )}
            </tbody>
          </table>
        </div>

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
