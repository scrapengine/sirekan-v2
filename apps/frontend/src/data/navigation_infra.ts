import { HardDrive, Network, Building2 } from 'lucide-react';
import type { NavSection } from '@/types';

export const infraNavigation: NavSection = {
  title: 'Infra',
  items: [
    {
      label: 'Network',
      icon: Network,
      children: [
        { label: 'Metro', href: '/metro' },
        { label: 'OLT', href: '/olt' },
        { label: 'STO', href: '/sto' },
      ],
    },
  ],
};
