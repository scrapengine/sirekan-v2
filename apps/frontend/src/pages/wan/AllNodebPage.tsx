import React, { useState, useEffect, useCallback } from 'react';
import { useQuery } from '@tanstack/react-query';
import client from '@/api/client';
import { Card } from '@/components/ui/Card';
import { Button } from '@/components/ui/Button';
import { ChevronUp, ChevronDown, ChevronsUpDown } from 'lucide-react';

interface AllNodebItem {
  idnodeb?: number;
  site_id: string;
  site_name: string;
  transport?: string;
  tikor_site?: string;
  type: 'nodeb' | 'radio';
  sto?: string;
  // Radio specific fields
  nsa?: string;
  kabupaten?: string;
  kecamatan?: string;
  kelurahan?: string;
  tech?: string;
  band_2g?: string;
  band_3g?: string;
  band_4g?: string;
  band_updated?: string;
  s_power?: string;
}

export const AllNodebPage: React.FC = () => {
  const [page, setPage] = useState(1);
  const [search, setSearch] = useState('');
  const [sortConfig, setSortConfig] = useState<{ key: string; direction: 'asc' | 'desc' }>({ key: 'site_id', direction: 'asc' });

  const { data, isLoading } = useQuery({
    queryKey: ['allnodeb', page, search, sortConfig],
    queryFn: async () => {
      const res = await client.get('/api/master-data/allnodeb', { 
        params: { page, limit: 25, search, sort_by: sortConfig.key, order: sortConfig.direction } 
      });
      return res.data;
    }
  });

  return (
    <div className="p-6 space-y-6">
      <Card className="p-4">
        <h2 className="text-xl font-bold mb-4">All NodeB & Radio Data</h2>
        <input 
          type="text" 
          placeholder="Search..." 
          className="border p-2 mb-4 w-full rounded"
          onChange={(e) => setSearch(e.target.value)}
        />
        <table className="w-full border-collapse border border-slate-700">
          <thead>
            <tr className="bg-slate-800">
              <th className="border p-2">Site ID</th>
              <th className="border p-2">Site Name</th>
              <th className="border p-2">Type</th>
              <th className="border p-2">Transport</th>
            </tr>
          </thead>
          <tbody>
            {isLoading ? (
              <tr><td colSpan={4} className="text-center p-4">Loading...</td></tr>
            ) : data?.data.map((item: AllNodebItem, i: number) => (
              <tr key={i} className="hover:bg-slate-700">
                <td className="border p-2">{item.site_id}</td>
                <td className="border p-2">{item.site_name}</td>
                <td className="border p-2 uppercase">{item.type}</td>
                <td className="border p-2">{item.transport || '-'}</td>
              </tr>
            ))}
          </tbody>
        </table>
      </Card>
    </div>
  );
};