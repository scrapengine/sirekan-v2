import React, { useState, useEffect, useCallback } from 'react';
import axios from 'axios';
import { Card } from '@/components/ui/Card';
import { Button } from '@/components/ui/Button';
import { Filter, ChevronDown, Plus, Trash2, Edit, Eye, Trash, Minus, Download, FileSpreadsheet } from 'lucide-react';

interface NodeBItem {
  idnodeb: number;
  idsto: string;
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
  odc: string;
  odp: string;
  tikor_site: string;
  on_air: string;
}

export const NodeBTable: React.FC = () => {
  const [data, setData] = useState<NodeBItem[]>([]);
  const [total, setTotal] = useState(0);
  const [currentPage, setCurrentPage] = useState(1);
  const [pageSize, setPageSize] = useState(25);
  const [loading, setLoading] = useState(false);
  const [search, setSearch] = useState('');
  const [debouncedSearch, setDebouncedSearch] = useState('');
  const [showFilter, setShowFilter] = useState(false);
  const [showColDropdown, setShowColDropdown] = useState(false);
  const [dynamicFilters, setDynamicFilters] = useState<{id: number}[]>([]);

  const [columnVisibility, setColumnVisibility] = useState<{ [key: string]: boolean }>({
    action: true, number: true, idsto: true, site_id: true, site_name: true,
    hostname_metro: true, port_metro: true, ip_olt: true, port_onu: true, 
    ip_ont: true, serial_number: true,
  });

  const columnsDef = [
    { key: 'action', label: 'Action' },
    { key: 'number', label: '#' },
    { key: 'idsto', label: 'STO' },
    { key: 'site_id', label: 'Site ID' },
    { key: 'site_name', label: 'Site Name' },
    { key: 'hostname_metro', label: 'Hostname Metro' },
    { key: 'port_metro', label: 'Port Metro' },
    { key: 'ip_olt', label: 'IP OLT' },
    { key: 'port_onu', label: 'Port Onu' },
    { key: 'ip_ont', label: 'IP ONT' },
    { key: 'serial_number', label: 'Serial Number' },
  ];

  const toggleColumn = (key: string) => setColumnVisibility(prev => ({ ...prev, [key]: !prev[key] }));

  useEffect(() => {
    const handler = setTimeout(() => setDebouncedSearch(search), 400);
    return () => clearTimeout(handler);
  }, [search]);

  const fetchData = useCallback(async () => {
    setLoading(true);
    try {
      const res = await axios.get('http://127.0.0.1:8001/api/master-data/nodeb', { 
        params: { page: currentPage, limit: pageSize, search: debouncedSearch } 
      });
      setData(res.data.data);
      setTotal(res.data.total);
    } catch (err) { console.error(err); } finally { setLoading(false); }
  }, [currentPage, pageSize, debouncedSearch]);

  useEffect(() => { fetchData(); }, [fetchData]);

  return (
    <div className="p-6 space-y-6 h-screen overflow-y-auto bg-gray-50 text-sm">
      <Card className="p-4 shadow-sm border border-gray-200 bg-white rounded-xl">
        <div className="flex justify-between items-center cursor-pointer" onClick={() => setShowFilter(!showFilter)}>
          <h4 className="flex items-center gap-2 font-semibold text-gray-700"><Filter size={16} /> Filter Data</h4>
          <ChevronDown size={16} className={`transition ${showFilter ? 'rotate-180' : ''}`} />
        </div>
        {showFilter && (
          <div className="mt-4 border-t pt-4">
            <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
              <select className="border p-2 rounded-lg text-sm"><option>On Air</option></select>
              <input type="date" className="border p-2 rounded-lg text-sm" />
              <input type="date" className="border p-2 rounded-lg text-sm" />
            </div>
            {dynamicFilters.map(f => (
              <div key={f.id} className="grid grid-cols-12 gap-2 mt-2">
                <select className="col-span-4 border p-2 rounded-lg text-sm"><option>Site ID</option></select>
                <input className="col-span-7 border p-2 rounded-lg text-sm" placeholder="Value" />
                <Button size="icon" variant="ghost" onClick={() => setDynamicFilters(prev => prev.filter(x => x.id !== f.id))} className="text-red-500"><Minus size={16}/></Button>
              </div>
            ))}
            <div className="flex justify-center gap-2 mt-4">
              <Button size="sm" variant="outline"><Download size={14}/> Export</Button>
              <Button size="sm">Search Table</Button>
              <Button size="sm" variant="secondary"><FileSpreadsheet size={14}/> Import</Button>
              <Button size="sm" variant="accent" onClick={() => setDynamicFilters([...dynamicFilters, { id: Date.now() }])}><Plus size={14}/></Button>
            </div>
            <p className="text-center text-xs text-gray-400 mt-2">Click + to show another search parameter</p>
          </div>
        )}
      </Card>

      <Card className="p-4 shadow-sm border border-gray-200 bg-white rounded-xl">
        <div className="flex justify-between items-center mb-4">
          <h4 className="font-bold text-lg text-gray-800">DATA NODE-B</h4>
          <div className="flex gap-2">
            <Button size="sm" className="bg-blue-600 text-white"><Plus size={16}/> Add Data</Button>
            <Button size="sm" className="bg-red-500 text-white"><Trash2 size={16}/> Trash</Button>
          </div>
        </div>

        <div className="flex justify-between items-center mb-4">
          <div className="flex gap-2 items-center">
            <select value={pageSize} onChange={(e) => { setPageSize(Number(e.target.value)); setCurrentPage(1); }} className="border p-1.5 rounded-lg text-xs">
              <option value={25}>25</option><option value={50}>50</option><option value={100}>100</option><option value={-1}>∞</option>
            </select>
            <input placeholder="Search..." value={search} onChange={(e) => { setSearch(e.target.value); setCurrentPage(1); }} className="border p-1.5 rounded-lg text-xs w-48" />
          </div>
          <Button size="sm" variant="outline" onClick={() => setShowColDropdown(!showColDropdown)}>Columns <ChevronDown size={14}/></Button>
        </div>

        {showColDropdown && (
          <div className="absolute z-50 bg-white border rounded shadow p-2 text-xs space-y-1 right-6">
            {columnsDef.map(col => (
              <label key={col.key} className="flex items-center gap-2 hover:bg-gray-50 p-1">
                <input type="checkbox" checked={columnVisibility[col.key]} onChange={() => toggleColumn(col.key)} /> {col.label}
              </label>
            ))}
          </div>
        )}

        {loading ? <div className="p-10 text-center">Loading...</div> : (
          <div className="overflow-x-auto">
            <table className="w-full text-xs text-left">
              <thead className="bg-gray-100 border-b">
                <tr>{columnsDef.map(col => columnVisibility[col.key] && <th key={col.key} className="p-3">{col.label}</th>)}</tr>
              </thead>
              <tbody className="divide-y">
                {data.map((item, idx) => (
                  <tr key={item.idnodeb} className="hover:bg-gray-50">
                    {columnVisibility.action && <td className="p-2 flex gap-1"><Eye size={14}/><Edit size={14} className="text-yellow-600"/><Trash size={14} className="text-red-600"/></td>}
                    {columnVisibility.number && <td className="p-3">{(currentPage - 1) * pageSize + idx + 1}</td>}
                    {columnVisibility.idsto && <td className="p-3">{item.idsto}</td>}
                    {columnVisibility.site_id && <td className="p-3 font-semibold">{item.site_id}</td>}
                    {columnVisibility.site_name && <td className="p-3">{item.site_name}</td>}
                    {columnVisibility.hostname_metro && <td className="p-3">{item.hostname_metro}</td>}
                    {columnVisibility.port_metro && <td className="p-3">{item.port_metro}</td>}
                    {columnVisibility.ip_olt && <td className="p-3">{item.hostname_olt}</td>}
                    {columnVisibility.port_onu && <td className="p-3">{item.port_onu}</td>}
                    {columnVisibility.ip_ont && <td className="p-3">{item.ip_ont}</td>}
                    {columnVisibility.serial_number && <td className="p-3">{item.serial_number}</td>}
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        )}

        <div className="flex justify-between items-center mt-4 text-xs text-gray-600">
          <span>Total: {total} records</span>
          <div className="flex gap-2">
            <Button size="sm" variant="outline" disabled={currentPage === 1} onClick={() => setCurrentPage(currentPage - 1)}>Prev</Button>
            <span>Page {currentPage}</span>
            <Button size="sm" variant="outline" onClick={() => setCurrentPage(currentPage + 1)}>Next</Button>
          </div>
        </div>
      </Card>
    </div>
  );
};
export default NodeBTable;
