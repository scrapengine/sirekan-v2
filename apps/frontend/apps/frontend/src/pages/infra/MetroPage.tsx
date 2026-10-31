import React, { useState, useEffect, useCallback, useRef } from 'react';
import { Link } from 'react-router-dom';
import { Filter, ChevronDown, Plus, Trash2, Edit, Trash, Minus, Download, Upload, Eye } from 'lucide-react';
import client from '@/api/client';
import { Card } from '@/components/ui/Card';
import { Button } from '@/components/ui/Button';
import { Modal } from '@/components/ui/Modal';

interface MetroItem {
  hostname_metro: string;
  ip_metro: string;
  idsto: string;
  created_at: string;
}

export const MetroPage: React.FC = () => {
  const [data, setData] = useState<MetroItem[]>([]);
  const [total, setTotal] = useState(0);
  const [currentPage, setCurrentPage] = useState(1);
  const [pageSize, setPageSize] = useState(25);
  const [loading, setLoading] = useState(false);
  const [search, setSearch] = useState('');
  const [debouncedSearch, setDebouncedSearch] = useState('');
  const [showFilter, setShowFilter] = useState(false);
  const [showColDropdown, setShowColDropdown] = useState(false);
  const [dynamicFilters, setDynamicFilters] = useState<{id: number}[]>([]);
  const [showImportModal, setShowImportModal] = useState(false);
  const fileInputRef = useRef<HTMLInputElement>(null);

  const [columnVisibility, setColumnVisibility] = useState<{ [key: string]: boolean }>({
    action: true, number: true, hostname_metro: true, ip_metro: true, idsto: true
  });

  const columnsDef = [
    { key: 'action', label: 'Action' },
    { key: 'number', label: '#' },
    { key: 'hostname_metro', label: 'Hostname Metro' },
    { key: 'ip_metro', label: 'IP Metro' },
    { key: 'idsto', label: 'STO' },
  ];

  const toggleColumn = (key: string) => setColumnVisibility(prev => ({ ...prev, [key]: !prev[key] }));

  useEffect(() => {
    const handler = setTimeout(() => setDebouncedSearch(search), 400);
    return () => clearTimeout(handler);
  }, [search]);

  const fetchData = useCallback(async () => {
    setLoading(true);
    try {
      const res = await client.get('/api/master-data/metro', {
        params: { page: currentPage, limit: pageSize, keyword: debouncedSearch }
      });
      setData(res.data.data);
      setTotal(res.data.meta.total);
    } catch (err) { console.error(err); }
    finally { setLoading(false); }
  }, [currentPage, pageSize, debouncedSearch]);

  const handleImport = async (file: File) => {
    const formData = new FormData();
    formData.append('file_excel', file);
    try {
      await client.post('/api/master-data/metro/import', formData);
      alert('Import berhasil');
      fetchData();
      setShowImportModal(false);
    } catch (err: any) { alert(err.response?.data?.detail || 'Import gagal'); }
  };

  useEffect(() => { fetchData(); }, [fetchData]);

  return (
    <div className="p-6 space-y-6 h-screen overflow-y-auto bg-orbit-bg text-sm">
      {/* Collapsible Filter Card - IDENTICAL to NodeBPage */}
      <Card className="p-4 shadow-sm border border-orbit-border bg-orbit-surface rounded-xl">
        <div className="flex justify-between items-center cursor-pointer" onClick={() => setShowFilter(!showFilter)}>
          <h4 className="flex items-center gap-2 font-semibold text-slate-100"><Filter size={16} /> Filter Data</h4>
          <ChevronDown size={16} className={`transition ${showFilter ? 'rotate-180' : ''}`} />
        </div>
        {showFilter && (
          <div className="mt-4 border-t pt-4 border-orbit-border">
            <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
              <select className="border border-orbit-border p-2 rounded-lg text-sm bg-orbit-surface text-slate-100" name="dateParam">
                <option value="created_at">Created At</option>
              </select>
              <input type="date" name="fromdate" className="border border-orbit-border p-2 rounded-lg text-sm bg-orbit-surface text-slate-100" />
              <input type="date" name="untildate" className="border border-orbit-border p-2 rounded-lg text-sm bg-orbit-surface text-slate-100" />
            </div>
            {dynamicFilters.map(f => (
              <div key={f.id} className="grid grid-cols-12 gap-2 mt-2">
                <select name="choice[]" className="col-span-4 border border-orbit-border bg-orbit-surface text-slate-100 p-2 rounded-lg text-sm">
                  <option value="hostname_metro">Hostname Metro</option>
                  <option value="ip_metro">IP Metro</option>
                  <option value="idsto">STO</option>
                </select>
                <input name="values[]" className="col-span-7 border border-orbit-border bg-orbit-surface text-slate-100 p-2 rounded-lg text-sm" placeholder="Value" />
                <Button size="icon" variant="ghost" onClick={() => setDynamicFilters(prev => prev.filter(x => x.id !== f.id))} className="text-red-500">
                  <Minus size={16}/>
                </Button>
              </div>
            ))}
            <div className="flex justify-center gap-2 mt-4">
              <Button size="sm" variant="outline">
                <Download size={14}/> Export
              </Button>
              <Button size="sm" variant="outline" onClick={() => fetchData()}>Search Table</Button>
              <Button size="sm" variant="outline" onClick={() => setShowImportModal(true)}>
                <Upload size={14}/> Import
              </Button>
              <Button size="sm" variant="accent" onClick={() => setDynamicFilters([...dynamicFilters, { id: Date.now() }])}>
                <Plus size={14}/>
              </Button>
            </div>
            <p className="text-center text-xs text-gray-400 mt-2">Click + to show another search parameter</p>
          </div>
        )}
      </Card>

      {/* Table Card - IDENTICAL to NodeBPage */}
      <Card className="p-4 shadow-sm border border-orbit-border bg-orbit-surface rounded-xl">
        <div className="flex justify-between items-center mb-4">
          <h4 className="font-bold text-lg text-slate-100">DATA METRO</h4>
          <div className="flex gap-2">
            <Link to="/metro/add" className="bg-blue-600 text-white px-3 py-1.5 text-xs rounded hover:bg-blue-700 flex items-center gap-1">
              <Plus size={16}/> Add Data
            </Link>
            <Link to="/metro/trash" className="bg-red-500 text-white px-3 py-1.5 text-xs rounded hover:bg-red-600 flex items-center gap-1">
              <Trash2 size={16}/> Trash
            </Link>
          </div>
        </div>

        <div className="flex justify-between items-center mb-4">
          <div className="flex gap-2 items-center">
            <select value={pageSize} onChange={(e) => { setPageSize(Number(e.target.value)); setCurrentPage(1); }} className="border border-orbit-border p-1.5 rounded-lg text-xs bg-orbit-surface text-slate-100">
              <option value={25}>25</option>
              <option value={50}>50</option>
              <option value={100}>100</option>
              <option value={-1}>∞</option>
            </select>
            <input placeholder="Search..." value={search} onChange={(e) => { setSearch(e.target.value); setCurrentPage(1); }} className="border border-orbit-border p-1.5 rounded-lg text-xs w-48 bg-orbit-surface text-slate-100" />
          </div>
          <Button size="sm" variant="outline" onClick={() => setShowColDropdown(!showColDropdown)}>Columns <ChevronDown size={14}/></Button>
        </div>

        {showColDropdown && (
          <div className="absolute z-50 bg-orbit-surface border border-orbit-border rounded shadow p-2 text-xs space-y-1 right-6 text-slate-100">
            {columnsDef.map(col => (
              <label key={col.key} className="flex items-center gap-2 hover:bg-slate-800/50 p-1 cursor-pointer">
                <input type="checkbox" checked={columnVisibility[col.key]} onChange={() => toggleColumn(col.key)} /> {col.label}
              </label>
            ))}
          </div>
        )}

        {loading ? <div className="p-10 text-center">Loading...</div> : (
          <div className="overflow-x-auto">
            <table className="w-full text-xs text-left">
              <thead className="bg-gray-100 border-b border-orbit-border sticky top-0 z-10">
                <tr>{columnsDef.map(col => columnVisibility[col.key] && <th key={col.key} className="p-3">{col.label}</th>)}</tr>
              </thead>
              <tbody className="divide-y divide-orbit-border">
                {data.map((item, idx) => (
                  <tr key={item.hostname_metro} className="hover:bg-gray-50">
                    {columnVisibility.action && <td className="p-2 flex gap-1"><Eye size={14}/><Edit size={14} className="text-yellow-600"/><Trash size={14} className="text-red-600"/></td>}
                    {columnVisibility.number && <td className="p-3">{(currentPage - 1) * pageSize + idx + 1}</td>}
                    {columnVisibility.hostname_metro && <td className="p-3 font-semibold">{item.hostname_metro}</td>}
                    {columnVisibility.ip_metro && <td className="p-3">{item.ip_metro}</td>}
                    {columnVisibility.idsto && <td className="p-3">{item.idsto}</td>}
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
            <span>Page {currentPage} of {Math.ceil(total / pageSize) || 1}</span>
            <Button size="sm" variant="outline" disabled={currentPage >= Math.ceil(total / pageSize) || 1} onClick={() => setCurrentPage(currentPage + 1)}>Next</Button>
          </div>
        </div>
      </Card>
      {showImportModal && (
        <Modal isOpen={showImportModal} onClose={() => setShowImportModal(false)} title="Import Metro Data" size="md">
          <div className="space-y-4">
            <input type="file" ref={fileInputRef} accept=".xlsx,.xls" className="border p-2 rounded w-full text-sm" />
            <div className="flex justify-end gap-2">
              <Button variant="outline" onClick={() => setShowImportModal(false)}>Cancel</Button>
              <Button onClick={() => fileInputRef.current?.files?.[0] && handleImport(fileInputRef.current.files[0])}>Import</Button>
            </div>
          </div>
        </Modal>
      )}
    </div>
  );
};