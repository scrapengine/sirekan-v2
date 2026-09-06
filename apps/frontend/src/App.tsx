import { Routes, Route, Navigate, useLocation } from 'react-router-dom'
import { useEffect } from 'react'
import { Layout } from '@/components/layout/Layout'
import { DashboardPage } from '@/pages/dashboard/DashboardPage'
import { BillingPage } from '@/pages/billing/BillingPage'
import { ContactsPage } from '@/pages/crm/ContactsPage'
import { ChatPage } from '@/pages/ai/ChatPage'
import { SettingsPage } from '@/pages/settings/SettingsPage'
import { HelpPage } from '@/pages/help/HelpPage'
import { SignInPage } from '@/pages/auth/SignInPage'
import { SignUpPage } from '@/pages/auth/SignUpPage'
import { ForgotPasswordPage } from '@/pages/auth/ForgotPasswordPage'
import { ComponentsPage } from '@/pages/components/ComponentsPage'
import { NodeBTable } from '@/pages/wan/NodeBPage';
import { NodeBDetailPage } from '@/pages/wan/NodeBDetailPage';
import { NodeBEditPage } from '@/pages/wan/NodeBEditPage';
import { NodeBAddPage } from '@/pages/wan/NodeBAddPage';
import { NodeBTrashPage } from '@/pages/wan/NodeBTrashPage';
import { WanPage } from '@/pages/wan/WanPlaceholder';
import { AssuranceTicketList } from '@/features/assurance/pages/AssuranceTicketList';
import { AssuranceTicketDetail } from '@/features/assurance/pages/AssuranceTicketDetail';

export default function App() {
  const location = useLocation();

  useEffect(() => {
    const titles: { [key: string]: string } = {
      '/wan/nodeb': 'NODE-B',
      '/wan/nodeb/add': 'NODE-B ADD',
      '/wan/nodeb/trash': 'NODE-B TRASH',
      '/wan/olt': 'OLT',
      '/wan/metro': 'METRO'
    };
    document.title = titles[location.pathname] || 'Sirekan';
  }, [location]);

  return (
    <Routes>
      {/* Auth routes — no sidebar layout */}
      <Route path="/sign-in" element={<SignInPage />} />
      <Route path="/sign-up" element={<SignUpPage />} />
      <Route path="/forgot-password" element={<ForgotPasswordPage />} />

      {/* Main app routes — with sidebar layout */}
      <Route element={<Layout />}>
        <Route path="/" element={<Navigate to="/dashboard" replace />} />
        <Route path="/dashboard" element={<DashboardPage />} />
        <Route path="/billing" element={<BillingPage />} />
        <Route path="/crm/contacts" element={<ContactsPage />} />
        <Route path="/ai/chat" element={<ChatPage />} />
        <Route path="/settings" element={<SettingsPage />} />
        <Route path="/help" element={<HelpPage />} />
        <Route path="/components" element={<ComponentsPage />} />
        <Route path="/assurance/tickets" element={<AssuranceTicketList />} />
        <Route path="/assurance/tickets/:incident" element={<AssuranceTicketDetail />} />
        <Route path="/wan/assurance" element={<WanPage title="WAN Assurance" />} />
        <Route path="/wan/fulfillment" element={<WanPage title="WAN Fulfillment" />} />
        <Route path="/wan/allnodeb" element={<WanPage title="WAN NodeB All" />} />
        <Route path="/wan/nodeb" element={<NodeBTable />} />
        <Route path="/wan/nodeb/add" element={<NodeBAddPage />} />
        <Route path="/wan/nodeb/trash" element={<NodeBTrashPage />} />
        <Route path="/wan/nodeb/detail/:id/:slug" element={<NodeBDetailPage />} />
        <Route path="/wan/nodeb/edit/:id" element={<NodeBEditPage />} />
        <Route path="/wan/olt" element={<WanPage title="WAN OLT" />} />
        <Route path="/wan/ont" element={<WanPage title="WAN ONT" />} />
        <Route path="/wan/metro" element={<WanPage title="WAN Metro" />} />
        <Route path="/wan/olo" element={<WanPage title="WAN OLO" />} />
      </Route>
    </Routes>
  )
}
