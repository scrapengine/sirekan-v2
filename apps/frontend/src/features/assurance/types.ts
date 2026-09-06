export interface Ticket {
  id: number;
  incident: string;
  status?: string;
  summary?: string;
  customer?: string;
  witel?: string;
  workzone?: string;
  reported_date?: string;
  status_date?: string;
  owner_group?: string;
}

export interface TicketListParams {
  page?: number;
  limit?: number;
  search?: string;
  status?: string;
  witel?: string;
  workzone?: string;
  is_active?: boolean;
}

export interface PaginatedResponse<T> {
  data: T[];
  total: number;
  page: number;
  limit: number;
  total_pages: number;
}
