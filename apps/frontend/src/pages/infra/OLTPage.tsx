import React, { useState, useEffect, useCallback } from 'react';
import { Link } from 'react-router-dom';
import { 
  Filter, 
  ChevronDown, 
  Plus, 
  Trash2, 
  Edit, 
  Eye, 
  Trash, 
  Minus, 
  Download, 
  Upload,
  ChevronUp, 
  ChevronsUpDown,
} from 'lucide-react';
import client from '@/api/client';
import { Card } from '@/components/ui/Card';
import { Button } from '@/components/ui/Button';
import { Modal } from '@/components/ui/Modal';

interface OLTItem {
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
  lokasi?: string;
  koordinat?: string;
  created_at?: string;
}

export const OLTPage: React.FC = () => {
  const [data, setData] = useState<OLTItem[]>([]);
  const [total, setTotal] = useState(0);
  const [totalPages, setTotalPages] = useState(0);
  const [currentPage, setCurrentPage] = useState(1);
  const [pageSize, setPageSize] = useState(25);
  const [loading, setLoading] = useState(false);
  const [search, setSearch] = useState('');
  const [debouncedSearch, setDebouncedSearch] = useState('');
  const [showFilter, setShowFilter] = useState(false);
  const [showColDropdown, setShowColDropdown] = useState(false);
  const [dynamicFilters, setDynamicFilters] = useState<{id: number}[]>([]);
  const [showImportModal, setShowImportModal] = useState(false);
  const fileInputRef = React.useRef<HTMLInputElement>(null);

  const [columnVisibility, setColumnVisibility] = useState<{ [key: string]: boolean }>({
    action: true, 
    number: true, 
    hostname_olt: true, 
    ip_olt: true, 
    port_olt: true, 
    platform: false,
    type_olt: true, 
    witel: true, 
    hostname_metro: false, 
    ip_metro: false, 
    port_metro: false, 
    idsto: true,
    lokasi: false,
    koordinat: false,
  });

  const [sortConfig, setSortConfig] = useState<{ key: string; direction: 'asc' | 'desc' }>({ 
    key: 'hostname_olt', 
    direction: 'asc' 
  });

  const columnsDef = [
    { key: 'action', label: 'Action' },
    { key: 'number', label: '#' },
    { key: 'hostname_olt', label: 'Hostname OLT' },
    { key: 'ip_olt', label: 'IP OLT' },
    { key: 'port_olt', label: 'Port OLT' },
    { key: 'platform', label: 'Platform' },
    { key: 'type_olt', label: 'Type OLT' },
    { key: 'witel', label: 'Witel' },
    { key: 'hostname_metro', label: 'Hostname Metro' },
    { key: 'ip_metro', label: 'IP Metro' },
    { key: 'port_metro', label: 'Port Metro' },
    { key: 'idsto', label: 'STO ID' },
    { key: 'lokasi', label: 'Lokasi' },
    { key: 'koordinat', label: 'Koordinat' },
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

  const fetchData = useCallback(async (filters: any = {}) => {
    setLoading(true);
    try {
      const params = { 
        page: currentPage, 
        limit: pageSize, 
        search: debouncedSearch, 
        sort_by: sortConfig.key, 
        order: sortConfig.direction,
        ...filters
      };
      const res = await client.get('/api/master-data/olt', { params });
      setData(res.data.data);
      setTotal(res.data.meta.total);
      setTotalPages(res.data.meta.total_pages);
    } catch (err) { 
      console.error(err); 
    } finally { 
      setLoading(false); 
    }
  }, [currentPage, pageSize, debouncedSearch, sortConfig.key, sortConfig.direction]);

  const handleDelete = async (hostname_olt: string) => {
    if (confirm('Are you sure you want to delete this data? It will be moved to trash.')) {
      try {
        await client.delete(`/api/master-data/olt/${hostname_olt}`);
        fetchData();
      } catch (err) {
        console.error('Failed to delete OLT', err);
      }
    }
  };

  const handleFilterSearch = async () => {
    const params: any = {};
    const choiceSelects = document.querySelectorAll("select[name='choice[]']");
    const valueInputs = document.querySelectorAll("input[name='values[]']");
    const choices = Array.from(choiceSelects).map(s => (s as HTMLSelectElement).value);
    const values = Array.from(valueInputs).map(i => (i as HTMLInputElement).value);
    
    choices.forEach((c, i) => { if(c && c !== 'cancel' && values[i]) { 
      if (!params.choice) params.choice = [];
      if (!params.values) params.values = [];
      params.choice.push(c);
      params.values.push(values[i]);
    }});
    
    fetchData(params);
  };

  const handleFilterReset = () => {
    setDynamicFilters([]);
    fetchData({});
  };

  const handleExport = async () => {
    try {
      const params: any = {};
      const choiceSelects = document.querySelectorAll("select[name='choice[]']");
      const valueInputs = document.querySelectorAll("input[name='values[]']");
      const choices = Array.from(choiceSelects).map(s => (s as HTMLSelectElement).value);
      const values = Array.from(valueInputs).map(i => (i as HTMLInputElement).value);
      
      choices.forEach((c, i) => { if(c && c !== 'cancel' && values[i]) { 
        if (!params.choice) params.choice = [];
        if (!params.values) params.values = [];
        params.choice.push(c);
        params.values.push(values[i]);
      }});
      
      const response = await client.get('/api/master-data/olt/export', { params, responseType: 'blob' });
      const url = window.URL.createObjectURL(new Blob([response.data]));
      const link = document.createElement('a');
      link.href = url;
      link.setAttribute('download', `OLT-${new Date().toISOString().slice(0, 10).replace(/-/g, '')}-${new Date().getHours()}${new Date().getMinutes()}${new Date().getSeconds()}.xlsx`);
      document.body.appendChild(link);
      link.click();
      link.remove();
    } catch (err) {
      console.error('Export failed', err);
    }
  };

  const handleImport = async (file: File) => {
    const formData = new FormData();
    formData.append('file_excel', file);
    try {
      await client.post('/api/master-data/olt/import', formData);
      alert('Import berhasil');
      fetchData({});
      setShowImportModal(false);
    } catch (err: any) {
      alert(err.response?.data?.detail || 'Import gagal');
    }
  };

  useEffect(() => { 
    fetchData(); 
  }, [fetchData]);

  return (
    <div className="p-6 space-y-6 h-screen overflow-y-auto bg-orbit-bg text-sm">
      <Card className="p-4 shadow-sm border border-orbit-border bg-orbit-surface rounded-xl">
        <div className="flex justify-between items-center cursor-pointer" onClick={() => setShowFilter(!showFilter)}>
          <h4 className="flex items-center gap-2 font-semibold text-slate-100"><Filter size={16} /> Filter Data</h4>
          <ChevronDown size={16} className={`transition ${showFilter ? 'rotate-180' : ''}`} />
        </div>
        {showFilter && (
          <div className="mt-4 border-t pt-4">
            {dynamicFilters.map(f => (
              <div key={f.id} className="grid grid-cols-12 gap-2 mt-2">
                <select name="choice[]" className="col-span-4 border border-orbit-border bg-orbit-surface p-2 rounded-lg text-sm text-slate-100">
                  <option value="hostname_olt">Hostname OLT</option>
                  <option value="ip_olt">IP OLT</option>
                  <option value="port_olt">Port OLT</option>
                  <option value="platform">Platform</option>
                  <option value="type_olt">Type OLT</option>
                  <option value="witel">Witel</option>
                  <option value="hostname_metro">Hostname Metro</option>
                  <option value="ip_metro">IP Metro</option>
                  <option value="port_metro">Port Metro</option>
                  <option value="idsto">STO ID</option>
                  <option value="lokasi">Lokasi</option>
                  <option value="koordinat">Koordinat</option>
                </select>
                <input name="values[]" className="col-span-7 border border-orbit-border bg-orbit-surface p-2 rounded-lg text-sm text-slate-100" placeholder="Value" />
                <Button size="icon" variant="ghost" onClick={() => setDynamicFilters(prev => prev.filter(x => x.id !== f.id))} className="text-red-500">
                  <Minus size={16}/>
                </Button>
              </div>
            ))}
            <div className="flex justify-center gap-2 mt-4">
              <Button size="sm" variant="outline" onClick={handleFilterSearch}>
                Search Table
              </Button>
              <Button size="sm" variant="outline" onClick={handleExport}>
                <Download size={14} className="mr-1"/> Export
              </Button>
              <Button size="sm" variant="outline" onClick={() => setShowImportModal(true)}>
                <Upload size={14} className="mr-1"/> Import
              </Button>
              <Button size="sm" variant="secondary" onClick={handleFilterReset}>
                Reset Filters
              </Button>
              <Button size="sm" variant="accent" onClick={() => setDynamicFilters([...dynamicFilters, { id: Date.now() }])}>
                <Plus size={14}/>
              </Button>
            </div>
            <p className="text-center text-xs text-gray-400 mt-2">Click + to show search parameter</p>
          </div>
        )}
      </Card>

      <Card className="p-4 shadow-sm border border-orbit-border bg-orbit-surface rounded-xl">
        <div className="flex justify-between items-center mb-4">
          <h4 className="font-bold text-lg text-slate-100">DATA OLT</h4>
          <div className="flex gap-2">
            <Link to="/olt/add" className="bg-blue-600 text-white px-3 py-1.5 text-xs rounded hover:bg-blue-700 flex items-center gap-1">
              <Plus size={16}/> Add Data
            </Link>
            <Link to="/olt/trash" className="bg-red-500 text-white px-3 py-1.5 text-xs rounded hover:bg-red-600 flex items-center gap-1">
              <Trash2 size={16}/> Trash
            </Link>
          </div>
        </div>

        <div className="flex justify-between items-center mb-4">
          <div className="flex gap-2 items-center">
            <select value={pageSize} onChange={(e) => { setPageSize(Number(e.target.value)); setCurrentPage(1); }} className="border border-orbit-border bg-orbit-surface p-1.5 rounded-lg text-xs text-slate-100">
              <option value={25}>25</option>
              <option value={50}>50</option>
              <option value={100}>100</option>
              <option value={-1}>∞</option>
            </select>
            <input placeholder="Search..." value={search} onChange={(e) => { setSearch(e.target.value); setCurrentPage(1); }} className="border border-orbit-border bg-orbit-surface p-1.5 rounded-lg text-xs w-48 text-slate-100" />
          </div>
          <Button size="sm" variant="outline" onClick={() => setShowColDropdown(!showColDropdown)}>
            Columns <ChevronDown size={14}/>
          </Button>
        </div>

        {showColDropdown && (
          <div className="absolute z-50 bg-orbit-surface border border-orbit-border rounded shadow p-2 text-xs space-y-1 right-6 text-slate-100">
            {columnsDef.map(col => (
              <label key={col.key} className="flex items-center gap-2 hover:bg-white/5 p-1 cursor-pointer">
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
                  <tr key={item.hostname_olt} className="hover:bg-white/5 transition-colors">
                    {columnVisibility.action && (
                      <td className="p-3 text-slate-400 align-middle">
                        <div className="flex items-center gap-1">
                          <Link to={`/olt/detail/${item.hostname_olt}`}>
                            <Eye size={14} className="hover:text-orbit-primary transition-colors"/>
                          </Link>
                          <Link to={`/olt/edit/${item.hostname_olt}`}>
                            <Edit size={14} className="text-yellow-600 cursor-pointer hover:text-yellow-500 transition-colors"/>
                          </Link>
                          <Trash size={14} onClick={() => handleDelete(item.hostname_olt)} className="text-red-600 cursor-pointer hover:text-red-500 transition-colors"/>
                        </div>
                      </td>
                    )}
                    {columnVisibility.number && <td className="p-3 text-slate-400 align-middle">{(currentPage - 1) * pageSize + idx + 1}</td>}
                    {columnVisibility.hostname_olt && <td className="p-3 font-semibold text-slate-100 align-middle">{item.hostname_olt}</td>}
                    {columnVisibility.ip_olt && <td className="p-3 text-slate-400 align-middle">{item.ip_olt}</td>}
                    {columnVisibility.port_olt && <td className="p-3 text-slate-400 align-middle">{item.port_olt}</td>}
                    {columnVisibility.platform && <td className="p-3 text-slate-400 align-middle">{item.platform}</td>}
                    {columnVisibility.type_olt && <td className="p-3 text-slate-400 align-middle">{item.type_olt}</td>}
                    {columnVisibility.witel && <td className="p-3 text-slate-400 align-middle">{item.witel}</td>}
                    {columnVisibility.hostname_metro && <td className="p-3 text-slate-400 align-middle">{item.hostname_metro}</td>}
                    {columnVisibility.ip_metro && <td className="p-3 text-slate-400 align-middle">{item.ip_metro}</td>}
                    {columnVisibility.port_metro && <td className="p-3 text-slate-400 align-middle">{item.port_metro}</td>}
                    {columnVisibility.idsto && <td className="p-3 text-slate-400 align-middle">{item.idsto}</td>}
                    {columnVisibility.lokasi && <td className="p-3 text-slate-400 align-middle">{item.lokasi}</td>}
                    {columnVisibility.koordinat && <td className="p-3 text-slate-400 align-middle">{item.koordinat}</td>}
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
      
      {showImportModal && (
        <Modal isOpen={showImportModal} onClose={() => setShowImportModal(false)} title="Import OLT Data">
          <div className="space-y-4">
            <p className="text-xs text-slate-400">Upload file Excel (.xlsx / .xls) untuk import data OLT.</p>
            <input 
              type="file" 
              ref={fileInputRef} 
              accept=".xlsx, .xls"
              className="w-full text-xs text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-orbit-surface file:text-slate-100 hover:file:bg-white/10"
            />
            <div className="flex justify-end gap-2 pt-4">
              <Button size="sm" variant="ghost" onClick={() => setShowImportModal(false)}>Cancel</Button>
              <Button size="sm" onClick={() => {
                const file = fileInputRef.current?.files?.[0];
                if (file) handleImport(file);
              }}>Import Now</Button>
            </div>
          </div>
        </Modal>
      )}
    </div>
  );
};