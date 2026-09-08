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
  ChevronsUpDown 
} from 'lucide-react';
import client from '@/api/client';
import { Card } from '@/components/ui/Card';
import { Button } from '@/components/ui/Button';
import { Modal } from '@/components/ui/Modal';

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
}

export const NodeBTable: React.FC = () => {
  const [data, setData] = useState<NodeBItem[]>([]);
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
  const [showDetailModal, setShowDetailModal] = useState(false);
  const [selectedNodeB, setSelectedNodeB] = useState<NodeBItem | null>(null);

  const [columnVisibility, setColumnVisibility] = useState<{ [key: string]: boolean }>({
    action: true, 
    number: true, 
    idsto: true, 
    site_id: true, 
    site_name: true,
    hostname_metro: true, 
    ip_metro: false, 
    port_metro: true, 
    ip_olt: true, 
    port_onu: true, 
    ip_ont: true, 
    serial_number: true, 
    hostname_olt: false, 
    hostname_ont: false,
    ont_type: false, 
    odc: false, 
    odp: false, 
    tikor_site: false, 
    on_air: false,
  });

  const [sortConfig, setSortConfig] = useState<{ key: string; direction: 'asc' | 'desc' }>({ 
    key: 'on_air', 
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
      const res = await client.get('/api/master-data/nodeb', { 
        params: { 
          page: currentPage, 
          limit: pageSize, 
          search: debouncedSearch, 
          sort_by: sortConfig.key, 
          order: sortConfig.direction 
        } 
      });
      setData(res.data.data);
      setTotal(res.data.meta.total);
      setTotalPages(res.data.meta.total_pages);
    } catch (err) { 
      console.error(err); 
    } finally { 
      setLoading(false); 
    }
  }, [currentPage, pageSize, debouncedSearch, sortConfig.key, sortConfig.direction]);

  const handleDelete = async (id: number) => {
    if (confirm('Are you sure you want to delete this data? It will be moved to trash.')) {
      try {
        await client.delete(`/api/master-data/nodeb/${id}`);
        fetchData();
      } catch (err) {
        console.error('Failed to delete nodeb', err);
      }
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
            <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
              <select className="border p-2 rounded-lg text-sm" name="dateParam">
                <option value="on_air">On Air</option>
                <option value="created_at">Created At</option>
              </select>
              <input type="date" name="fromdate" className="border p-2 rounded-lg text-sm" />
              <input type="date" name="untildate" className="border p-2 rounded-lg text-sm" />
            </div>
            {dynamicFilters.map(f => (
              <div key={f.id} className="grid grid-cols-12 gap-2 mt-2">
                <select name="choice[]" className="col-span-4 border p-2 rounded-lg text-sm">
                  <option value="site_id">Site ID</option>
                  <option value="site_name">Site Name</option>
                  <option value="idsto">STO</option>
                  <option value="hostname_metro">Hostname Metro</option>
                  <option value="hostname_olt">Hostname OLT</option>
                  <option value="ip_olt">IP OLT</option>
                  <option value="serial_number">SN</option>
                  <option value="odc">ODC</option>
                  <option value="odp">ODP</option>
                </select>
                <input name="values[]" className="col-span-7 border p-2 rounded-lg text-sm" placeholder="Value" />
                <Button size="icon" variant="ghost" onClick={() => setDynamicFilters(prev => prev.filter(x => x.id !== f.id))} className="text-red-500">
                  <Minus size={16}/>
                </Button>
              </div>
            ))}
            <div className="flex justify-center gap-2 mt-4">
              <Button size="sm" variant="outline" onClick={async () => {
                try {
                  const params: any = {};
                  const dateParamVal = (document.querySelector("select[name='dateParam']") as HTMLSelectElement)?.value;
                  const fromDateVal = (document.querySelector("input[name='fromdate']") as HTMLInputElement)?.value;
                  const untilDateVal = (document.querySelector("input[name='untildate']") as HTMLInputElement)?.value;
                  
                  if (dateParamVal) params.dateParam = dateParamVal;
                  if (fromDateVal) params.fromdate = fromDateVal;
                  if (untilDateVal) params.untildate = untilDateVal;
                  
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
                  
                  const response = await client.get('/api/master-data/nodeb/export', { params, responseType: 'blob' });
                  const url = window.URL.createObjectURL(new Blob([response.data]));
                  const link = document.createElement('a');
                  link.href = url;
                  link.setAttribute('download', `NODEB-${new Date().toISOString().slice(0, 10).replace(/-/g, '')}-${new Date().getHours()}${new Date().getMinutes()}${new Date().getSeconds()}.xlsx`);
                  document.body.appendChild(link);
                  link.click();
                  link.remove();
                } catch (err) {
                  console.error('Export failed', err);
                }
              }}>
                <Download size={14}/> Export
              </Button>
              <Button size="sm" onClick={() => {
                const params: any = {};
                const dateParamVal = (document.querySelector("select[name='dateParam']") as HTMLSelectElement)?.value;
                const fromDateVal = (document.querySelector("input[name='fromdate']") as HTMLInputElement)?.value;
                const untilDateVal = (document.querySelector("input[name='untildate']") as HTMLInputElement)?.value;
                
                if (dateParamVal) params.dateParam = dateParamVal;
                if (fromDateVal) params.fromdate = fromDateVal;
                if (untilDateVal) params.untildate = untilDateVal;
                
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
                
                client.get('/api/master-data/nodeb', { params })
                  .then(res => {
                    setData(res.data.data);
                    setTotal(res.data.meta.total);
                    setTotalPages(res.data.meta.total_pages);
                  });
              }}>
                Search Table
              </Button>
              <label className="cursor-pointer bg-slate-700 hover:bg-slate-600 text-white px-3 py-2 rounded text-xs flex items-center gap-1">
                <Upload size={14}/> Import
                <input type="file" className="hidden" onChange={(e) => {
                  if (e.target.files?.[0]) {
                    const formData = new FormData();
                    formData.append('file_excel', e.target.files[0]);
                    client.post('/api/master-data/nodeb/import', formData).then(() => { alert('Import OK'); fetchData(); });
                  }
                }} />
              </label>
              <Button size="sm" variant="accent" onClick={() => setDynamicFilters([...dynamicFilters, { id: Date.now() }])}>
                <Plus size={14}/>
              </Button>
            </div>
            <p className="text-center text-xs text-gray-400 mt-2">Click + to show another search parameter</p>
          </div>
        )}
      </Card>

      <Card className="p-4 shadow-sm border border-orbit-border bg-orbit-surface rounded-xl">
        <div className="flex justify-between items-center mb-4">
          <h4 className="font-bold text-lg text-slate-100">DATA NODE-B</h4>
          <div className="flex gap-2">
            <Link to="/wan/nodeb/add" className="bg-blue-600 text-white px-3 py-1.5 text-xs rounded hover:bg-blue-700 flex items-center gap-1">
              <Plus size={16}/> Add Data
            </Link>
            <Link to="/wan/nodeb/trash" className="bg-red-500 text-white px-3 py-1.5 text-xs rounded hover:bg-red-600 flex items-center gap-1">
              <Trash2 size={16}/> Trash
            </Link>
          </div>
        </div>

        <div className="flex justify-between items-center mb-4">
          <div className="flex gap-2 items-center">
            <select value={pageSize} onChange={(e) => { setPageSize(Number(e.target.value)); setCurrentPage(1); }} className="border p-1.5 rounded-lg text-xs">
              <option value={25}>25</option>
              <option value={50}>50</option>
              <option value={100}>100</option>
              <option value={-1}>∞</option>
            </select>
            <input placeholder="Search..." value={search} onChange={(e) => { setSearch(e.target.value); setCurrentPage(1); }} className="border p-1.5 rounded-lg text-xs w-48" />
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
                        <div className="flex items-center gap-1">
                          <Link to={`/wan/nodeb/detail/${item.idnodeb}/${item.site_id.toLowerCase().replace(/\s+/g, '-')}`}>
                            <Eye size={14} className="hover:text-orbit-primary transition-colors"/>
                          </Link>
                          <Link to={`/wan/nodeb/edit/${item.idnodeb}`}>
                            <Edit size={14} className="text-yellow-600 cursor-pointer hover:text-yellow-500 transition-colors"/>
                          </Link>
                          <Trash size={14} onClick={() => handleDelete(item.idnodeb)} className="text-red-600 cursor-pointer hover:text-red-500 transition-colors"/>
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

      <Modal
        isOpen={showDetailModal}
        onClose={() => setShowDetailModal(false)}
        title="Detail Data Node-B"
      >
        {selectedNodeB && (
          <div className="grid grid-cols-2 gap-4 text-slate-300">
            {Object.entries(selectedNodeB).map(([key, value]) => (
              <div key={key}>
                <p className="font-semibold text-slate-400 text-xs uppercase mb-1">{key.replace(/_/g, ' ')}</p>
                <p className="text-sm">{value !== null ? String(value) : '-'}</p>
              </div>
            ))}
          </div>
        )}
      </Modal>
    </div>
  );
};
