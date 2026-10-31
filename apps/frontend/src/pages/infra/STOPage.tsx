import React, { useState, useEffect, useCallback, useRef } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { Filter, ChevronDown, Plus, Trash2, Edit, Trash, Minus, Download, Eye, Upload } from 'lucide-react';
import client from '@/api/client';
import { Card } from '@/components/ui/Card';
import { Button } from '@/components/ui/Button';
import { Modal } from '@/components/ui/Modal';

interface STOItem {
  idsto: string;
  nama_sto: string;
  witel: string;
  longitude: string;
  latitude: string;
  alamat: string;
}

export const STOPage: React.FC = () => {
  const [data, setData] = useState<STOItem[]>([]);
  const [total, setTotal] = useState(0);
  const [currentPage, setCurrentPage] = useState(1);
  const [pageSize, setPageSize] = useState(25);
  const [loading, setLoading] = useState(false);
  const [search, setSearch] = useState('');
  const [debouncedSearch, setDebouncedSearch] = useState('');
  const [showFilter, setShowFilter] = useState(false);
  const [showColDropdown, setShowColDropdown] = useState(false);
  const [dynamicFilters, setDynamicFilters] = useState<{ id: number; choice: string; value: string }[]>([]);
  const [showImportModal, setShowImportModal] = useState(false);
  const fileInputRef = useRef<HTMLInputElement>(null);
  const [showDeleteModal, setShowDeleteModal] = useState(false);
  const [stoToDelete, setStoToDelete] = useState<string | null>(null);

  const [columnVisibility, setColumnVisibility] = useState<{ [key: string]: boolean }>({
    action: true, number: true, idsto: true, nama_sto: true, longitude: true, latitude: true, alamat: true
  });

  const columnsDef = [
    { key: 'action', label: 'Action' },
    { key: 'number', label: '#' },
    { key: 'idsto', label: 'Code' },
    { key: 'nama_sto', label: 'Name' },
    { key: 'longitude', label: 'Longitude' },
    { key: 'latitude', label: 'Latitude' },
    { key: 'alamat', label: 'Address' },
  ];

  const toggleColumn = (key: string) => setColumnVisibility(prev => ({ ...prev, [key]: !prev[key] }));

  useEffect(() => {
    const handler = setTimeout(() => setDebouncedSearch(search), 400);
    return () => clearTimeout(handler);
  }, [search]);

  const fetchData = useCallback(async () => {
    setLoading(true);
    try {
      const params = {
        page: currentPage,
        limit: pageSize,
        keyword: debouncedSearch,
        choice: dynamicFilters.map(f => f.choice),
        values: dynamicFilters.map(f => f.value),
      };
      
      const res = await client.get('/api/master-data/sto', { params });
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
  }, [currentPage, pageSize, debouncedSearch, dynamicFilters]);

  const handleImport = async (file: File) => {
    const formData = new FormData();
    formData.append('file_excel', file);
    try {
      await client.post('/api/master-data/sto/import', formData);
      alert('Import berhasil');
      fetchData();
      setShowImportModal(false);
    } catch (err: any) { alert(err.response?.data?.detail || 'Import gagal'); }
  };

  const handleExport = async () => {
    const params: any = {
      keyword: debouncedSearch,
      choice: dynamicFilters.map(f => f.choice),
      values: dynamicFilters.map(f => f.value),
    };
    // Remove pagination parameters for export to get all data
    delete params.page;
    delete params.limit;

    try {
      const res = await client.get('/api/master-data/sto/export', { params, responseType: 'blob' });
      const url = window.URL.createObjectURL(new Blob([res.data]));
      const link = document.createElement('a');
      link.href = url;
      const contentDisposition = res.headers['content-disposition'];
      let filename = `STO-${new Date().toISOString().slice(0,10)}.xlsx`;
      if (contentDisposition) {
        const filenameMatch = contentDisposition.match(/filename="(.+)"/);
        if (filenameMatch && filenameMatch[1]) {
          filename = filenameMatch[1];
        }
      }
      link.setAttribute('download', filename);
      document.body.appendChild(link);
      link.click();
      link.remove();
    } catch (err: any) { alert(err.response?.data?.detail || 'Export gagal'); }
  };

  const handleDelete = async () => {
    if (stoToDelete) {
      try {
        await client.delete(`/api/master-data/sto/${stoToDelete}`);
        fetchData();
        setShowDeleteModal(false);
        setStoToDelete(null);
      } catch (err: any) { alert(err.response?.data?.detail || 'Delete gagal'); }
    }
  };

  useEffect(() => { fetchData(); }, [fetchData]);

  const totalPages = Math.ceil(total / pageSize) || 1;

  return (
    <div className="p-6 space-y-6 h-screen overflow-y-auto bg-orbit-bg text-sm">
      <Card className="p-4 shadow-sm border border-orbit-border bg-orbit-surface rounded-xl">
        <div className="flex justify-between items-center cursor-pointer" onClick={() => setShowFilter(!showFilter)}>
          <h4 className="flex items-center gap-2 font-semibold text-slate-100"><Filter size={16} /> Filter Data</h4>
          <ChevronDown size={16} className={`transition ${showFilter ? 'rotate-180' : ''}`} />
        </div>
        {showFilter && (
          <div className="mt-4 border-t pt-4 border-orbit-border">
            {dynamicFilters.map((f, index) => (
              <div key={f.id} className="grid grid-cols-12 gap-2 mt-2">
                <select className="col-span-4 border border-orbit-border bg-orbit-surface text-slate-100 p-2 rounded-lg text-sm" value={f.choice} onChange={(e) => {
                  const newFilters = [...dynamicFilters];
                  newFilters[index].choice = e.target.value;
                  setDynamicFilters(newFilters);
                }}>
                  <option value="">Select Field</option>
                  <option value="idsto">Code</option>
                  <option value="nama_sto">Name</option>
                  <option value="witel">Witel</option>
                  <option value="alamat">Address</option>
                </select>
                <input className="col-span-7 border border-orbit-border bg-orbit-surface text-slate-100 p-2 rounded-lg text-sm" placeholder="Value" value={f.value} onChange={(e) => {
                  const newFilters = [...dynamicFilters];
                  newFilters[index].value = e.target.value;
                  setDynamicFilters(newFilters);
                }} />
                <Button size="icon" variant="ghost" onClick={() => setDynamicFilters(dynamicFilters.filter(df => df.id !== f.id))} className="text-red-500">
                  <Minus size={16} />
                </Button>
              </div>
            ))}
            <div className="flex justify-center gap-2 mt-4">
              <Button size="sm" variant="outline" onClick={fetchData}>Search Table</Button>
              <Button size="sm" variant="outline" onClick={() => setShowImportModal(true)}>
                <Upload size={14} className="mr-1" /> Import
              </Button>
              <Button size="sm" variant="outline" onClick={handleExport}>
                <Download size={14} className="mr-1" /> Export
              </Button>
              <Button size="sm" variant="outline" onClick={() => { setSearch(''); setDynamicFilters([]); setCurrentPage(1); }}>Reset Filters</Button>
              <Button size="sm" variant="accent" onClick={() => setDynamicFilters([...dynamicFilters, { id: Date.now(), choice: '', value: '' }])}>
                <Plus size={14} />
              </Button>
            </div>
          </div>
        )}
      </Card>

      <Card className="p-4 shadow-sm border border-orbit-border bg-orbit-surface rounded-xl">
        <div className="flex justify-between items-center mb-4">
          <h4 className="font-bold text-lg text-slate-100">DATA STO</h4>
          <div className="flex gap-2">
            <Link to="/sto/add" className="bg-blue-600 text-white px-3 py-1.5 text-xs rounded hover:bg-blue-700 flex items-center gap-1">
              <Plus size={16} /> Add Data
            </Link>
            <Link to="/sto/trash" className="bg-red-500 text-white px-3 py-1.5 text-xs rounded hover:bg-red-600 flex items-center gap-1">
              <Trash2 size={16} /> Trash
            </Link>
          </div>
        </div>

        <div className="flex justify-between items-center mb-4">
          <div className="flex gap-2 items-center">
            <select value={pageSize} onChange={(e) => { setPageSize(Number(e.target.value)); setCurrentPage(1); }} className="border border-orbit-border p-1.5 rounded-lg text-xs bg-orbit-surface text-slate-100">
              <option value={25}>25</option>
              <option value={50}>50</option>
              <option value={100}>100</option>
            </select>
            <input placeholder="Search..." value={search} onChange={(e) => { setSearch(e.target.value); setCurrentPage(1); }} className="border border-orbit-border p-1.5 rounded-lg text-xs w-48 bg-orbit-surface text-slate-100" />
          </div>
          <Button size="sm" variant="outline" onClick={() => setShowColDropdown(!showColDropdown)}>Columns <ChevronDown size={14} /></Button>
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

        {loading ? <div className="p-10 text-center text-slate-100">Loading...</div> : (
          <div className="overflow-x-auto">
            <table className="w-full text-xs text-left">
              <thead className="bg-orbit-surface border-b border-orbit-border sticky top-0 z-10">
                <tr>{columnsDef.map(col => columnVisibility[col.key] && <th key={col.key} className="p-3 text-slate-100">{col.label}</th>)}</tr>
              </thead>
              <tbody className="divide-y divide-orbit-border text-slate-300">
                {data.map((item, idx) => (
                  <tr key={`${item.idsto}-${idx}`} className="hover:bg-white/5 transition-colors">
                    {columnVisibility.action && (
                      <td className="p-3 text-slate-400 align-middle">
                        <div className="flex items-center gap-1">
                          <Link to={`/sto/detail/${item.idsto}`}>
                            <Eye size={14} className="hover:text-orbit-primary transition-colors"/>
                          </Link>
                          <Link to={`/sto/edit/${item.idsto}`}>
                            <Edit size={14} className="text-yellow-600 cursor-pointer hover:text-yellow-500 transition-colors"/>
                          </Link>
                          <Trash size={14} onClick={() => { setStoToDelete(item.idsto); setShowDeleteModal(true); }} className="text-red-600 cursor-pointer hover:text-red-500 transition-colors"/>
                        </div>
                      </td>
                    )}
                    {columnVisibility.number && <td className="p-3 align-middle">{(currentPage - 1) * pageSize + idx + 1}</td>}
                    {columnVisibility.idsto && <td className="p-3 align-middle font-semibold">{item.idsto}</td>}
                    {columnVisibility.nama_sto && <td className="p-3 align-middle">{item.nama_sto}</td>}
                    {columnVisibility.longitude && <td className="p-3 align-middle">{item.longitude}</td>}
                    {columnVisibility.latitude && <td className="p-3 align-middle">{item.latitude}</td>}
                    {columnVisibility.alamat && <td className="p-3 align-middle">{item.alamat}</td>}
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        )}

        <div className="flex justify-between items-center mt-4 border-t border-orbit-border pt-4 text-xs text-slate-400">
          <span>Showing {(currentPage - 1) * pageSize + 1} to {Math.min(currentPage * pageSize, total)} of {total} records</span>
          <div className="flex gap-2">
            <Button size="sm" variant="outline" disabled={currentPage === 1} onClick={() => setCurrentPage(currentPage - 1)}>Prev</Button>
            <span className="flex items-center">Page {currentPage} of {totalPages}</span>
            <Button size="sm" variant="outline" disabled={currentPage >= totalPages} onClick={() => setCurrentPage(currentPage + 1)}>Next</Button>
          </div>
        </div>
      </Card>

      <Modal isOpen={showImportModal} onClose={() => setShowImportModal(false)} title="Import STO Data">
        <div className="space-y-4">
          <input type="file" ref={fileInputRef} accept=".xlsx,.xls" className="border border-orbit-border bg-orbit-surface text-slate-100 p-2 rounded w-full text-sm" />
          <div className="flex justify-end gap-2">
            <Button variant="outline" onClick={() => setShowImportModal(false)}>Cancel</Button>
            <Button variant="accent" onClick={() => fileInputRef.current?.files?.[0] && handleImport(fileInputRef.current.files[0])}>Upload</Button>
          </div>
        </div>
      </Modal>

      <Modal isOpen={showDeleteModal} onClose={() => setShowDeleteModal(false)} title="Confirm Delete">
        <div className="space-y-4">
          <p className="text-slate-300">Are you sure you want to delete STO {stoToDelete}?</p>
          <div className="flex justify-end gap-2">
            <Button variant="outline" onClick={() => setShowDeleteModal(false)}>Cancel</Button>
            <Button variant="destructive" onClick={handleDelete}>Delete</Button>
          </div>
        </div>
      </Modal>
    </div>
  );
};