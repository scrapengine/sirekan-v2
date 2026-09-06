import client from '../../api/client';
import { Ticket, TicketListParams, PaginatedResponse } from '@/features/assurance/types';

export const assuranceApi = {
  list: async (params: TicketListParams): Promise<PaginatedResponse<Ticket>> => {
    const response = await client.get('/assurance/tickets', { params });
    return response.data;
  },
  get: async (incident: string): Promise<Ticket> => {
    const response = await client.get(`/assurance/tickets/${incident}`);
    return response.data;
  },
  importExcel: async (file: File) => {
    const formData = new FormData();
    formData.append('file', file);
    const response = await client.post('/assurance/import-excel', formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    });
    return response.data;
  }
};
