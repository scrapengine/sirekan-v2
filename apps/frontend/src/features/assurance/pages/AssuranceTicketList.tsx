import React, { useEffect, useState } from 'react';
import { assuranceApi } from '../api';
import { Ticket, TicketListParams } from '../types';
import { Card } from '@/components/ui/Card';
import { Button } from '@/components/ui/Button';
import { Pagination } from '@/components/ui/Pagination';
import { Modal } from '@/components/ui/Modal';
import { FileSpreadsheet } from 'lucide-react';

export const AssuranceTicketList: React.FC = () => {
  const [tickets, setTickets] = useState<Ticket[]>([]);
  const [totalTickets, setTotalTickets] = useState(0);
  const [currentPage, setCurrentPage] = useState(1);
  const [totalPages, setTotalPages] = useState(1);
  const [limit, setLimit] = useState(25);
  const [loading, setLoading] = useState(true);

  // Filter states
  const [searchQuery, setSearchQuery] = useState('');
  const [selectedStatus, setSelectedStatus] = useState<string | undefined>(undefined);
  const [selectedWitel, setSelectedWitel] = useState<string | undefined>(undefined);
  const [selectedWorkzone, setSelectedWorkzone] = useState<string | undefined>(undefined);
  const [isActive, setIsActive] = useState<boolean | undefined>(undefined);

  // Import modal state
  const [isImportModalOpen, setIsImportModalOpen] = useState(false);
  const [importFile, setImportFile] = useState<File | null>(null);
  const [importing, setImporting] = useState(false);
  const [importResult, setImportResult] = useState<string | null>(null);

  const fetchTickets = async () => {
    setLoading(true);
    try {
      const params: TicketListParams = {
        page: currentPage,
        limit: limit,
        search: searchQuery || undefined,
        status: selectedStatus || undefined,
        witel: selectedWitel || undefined,
        workzone: selectedWorkzone || undefined,
        is_active: isActive,
      };
      const response = await assuranceApi.list(params);
      setTickets(response.data);
      setTotalTickets(response.total);
      setCurrentPage(response.page);
      setTotalPages(response.total_pages);
      setLimit(response.limit);
    } catch (err) {
      console.error("Failed to fetch tickets:", err);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchTickets();
  }, [currentPage, limit, searchQuery, selectedStatus, selectedWitel, selectedWorkzone, isActive]);

  const handleImportSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!importFile) return;
    setImporting(true);
    setImportResult(null);
    try {
      const res = await assuranceApi.importExcel(importFile);
      setImportResult(`Success! Imported ${res.imported || 0} items.`);
      setTimeout(() => {
        setIsImportModalOpen(false);
        setImportFile(null);
        setImportResult(null);
        fetchTickets();
      }, 1500);
    } catch (err: any) {
      setImportResult(`Import failed: ${err.response?.data?.detail || err.message}`);
    } finally {
      setImporting(false);
    }
  };

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold tracking-tight text-slate-900">Assurance V2 Tickets</h1>
          <p className="text-sm text-slate-500">Kelola dan pantau tiket gangguan jaringan secara real-time.</p>
        </div>
        <Button variant="primary" onClick={() => setIsImportModalOpen(true)}>
          <FileSpreadsheet className="mr-2 h-4 w-4" /> Import Excel
        </Button>
      </div>

      <Card className="p-0 overflow-hidden border border-slate-200">
        <div className="p-4 border-b border-slate-200 bg-slate-50">
          <div className="flex items-center justify-between mb-4">
            <span className="text-sm font-medium text-slate-700">Daftar Tiket Gangguan</span>
            <span className="text-xs text-slate-500">Total: {totalTickets} ({tickets.length} ditampilkan)</span>
          </div>
          <div className="flex items-center space-x-4 flex-wrap gap-y-2">
            <input
              type="text"
              placeholder="Search..."
              className="rounded-md border border-slate-300 px-2 py-1 text-sm shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
              value={searchQuery}
              onChange={(e) => { setSearchQuery(e.target.value); setCurrentPage(1); }}
            />
            <select
              className="rounded-md border border-slate-300 bg-white px-2 py-1 text-sm shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
              value={selectedStatus || ''}
              onChange={(e) => { setSelectedStatus(e.target.value || undefined); setCurrentPage(1); }}
            >
              <option value="">All Status</option>
              {['NEW', 'PENDING', 'ANALYSIS', 'BACKEND', 'DRAFT'].map((s) => (
                <option key={s} value={s}>{s}</option>
              ))}
            </select>
            <input
              type="text"
              placeholder="Witel"
              className="rounded-md border border-slate-300 px-2 py-1 text-sm shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
              value={selectedWitel || ''}
              onChange={(e) => { setSelectedWitel(e.target.value || undefined); setCurrentPage(1); }}
            />
            <input
              type="text"
              placeholder="Workzone"
              className="rounded-md border border-slate-300 px-2 py-1 text-sm shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
              value={selectedWorkzone || ''}
              onChange={(e) => { setSelectedWorkzone(e.target.value || undefined); setCurrentPage(1); }}
            />
            <label className="flex items-center text-sm text-slate-700 cursor-pointer">
              <input
                type="checkbox"
                className="rounded border-slate-300 text-blue-600 shadow-sm focus:ring-blue-500"
                checked={isActive || false}
                onChange={(e) => setIsActive(e.target.checked ? true : undefined)}
              />
              <span className="ml-2">Active Only</span>
            </label>
            <Button variant="ghost" size="sm" onClick={() => { setSearchQuery(''); setSelectedStatus(undefined); setSelectedWitel(undefined); setSelectedWorkzone(undefined); setIsActive(undefined); setCurrentPage(1); }}>
              Reset
            </Button>
          </div>
        </div>
        {loading ? (
          <div className="p-8 text-center text-sm text-slate-500">Memuat data dari server...</div>
        ) : tickets.length === 0 ? (
          <div className="p-8 text-center text-sm text-slate-500">Belum ada data tiket. Silakan lakukan import excel.</div>
        ) : (
          <div>
            <div className="overflow-x-auto">
              <table className="w-full text-sm text-left">
                <thead className="bg-slate-100 text-slate-700 uppercase text-xs">
                  <tr>
                    <th className="px-4 py-3 font-semibold">Incident</th>
                    <th className="px-4 py-3 font-semibold">Customer</th>
                    <th className="px-4 py-3 font-semibold">Status</th>
                    <th className="px-4 py-3 font-semibold">Witel</th>
                    <th className="px-4 py-3 font-semibold">Reported Date</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-slate-200">
                  {tickets.map((ticket) => (
                    <tr key={ticket.id} className="hover:bg-slate-50 transition-colors cursor-pointer" onClick={() => window.location.href = `/assurance/tickets/${ticket.incident}`}>
                      <td className="px-4 py-3 font-medium text-blue-600 underline">{ticket.incident}</td>
                      <td className="px-4 py-3 text-slate-800">{ticket.customer || '-'}</td>
                      <td className="px-4 py-3">
                        <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                          {ticket.status || 'NEW'}
                        </span>
                      </td>
                      <td className="px-4 py-3 text-slate-600">{ticket.witel || '-'}</td>
                      <td className="px-4 py-3 text-slate-500">{ticket.reported_date || '-'}</td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
            
            <div className="p-4 border-t border-slate-200 bg-slate-50 flex items-center justify-between">
              <div className="flex items-center space-x-2 text-sm text-slate-700">
                <span>Items per page:</span>
                <select
                  className="rounded-md border border-slate-300 bg-white px-2 py-1 text-sm shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                  value={limit}
                  onChange={(e) => {
                    setLimit(Number(e.target.value));
                    setCurrentPage(1);
                  }}
                >
                  {[10, 25, 50, 100].map((pageSize) => (
                    <option key={pageSize} value={pageSize}>
                      {pageSize}
                    </option>
                  ))}
                </select>
              </div>
              <Pagination
                currentPage={currentPage}
                totalPages={totalPages}
                onPageChange={setCurrentPage}
              />
            </div>
          </div>
        )}
      </Card>

      {/* Import Modal */}
      <Modal isOpen={isImportModalOpen} onClose={() => setIsImportModalOpen(false)} title="Import Assurance Tickets">
        <form onSubmit={handleImportSubmit} className="space-y-4">
          <div>
            <label className="block text-sm font-medium text-slate-700 mb-1">Select Excel / CSV File</label>
            <input
              type="file"
              accept=".xlsx,.xls,.csv"
              onChange={(e) => setImportFile(e.target.files?.[0] || null)}
              className="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
            />
          </div>
          {importResult && (
            <p className={`text-sm ${importResult.includes('Success') ? 'text-green-600' : 'text-red-600'}`}>
              {importResult}
            </p>
          )}
          <div className="flex justify-end space-x-2 pt-2">
            <Button type="button" variant="outline" onClick={() => setIsImportModalOpen(false)}>
              Cancel
            </Button>
            <Button type="submit" variant="primary" disabled={!importFile || importing}>
              {importing ? 'Importing...' : 'Upload & Import'}
            </Button>
          </div>
        </form>
      </Modal>
    </div>
  );
};
