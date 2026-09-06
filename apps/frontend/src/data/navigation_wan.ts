import { Network } from 'lucide-react';
import type { NavSection } from '@/types';

export const wanNavigation: NavSection = {
  title: 'WAN Management',
  items: [
    {
      label: 'WAN Services',
      icon: Network,
      children: [
        { label: 'Assurance', href: '/wan/assurance' },
        { label: 'Fulfillment', href: '/wan/fulfillment' },
        { label: 'NodeB All', href: '/wan/allnodeb' },
        { label: 'NodeB', href: '/wan/nodeb' },
        { label: 'OLT', href: '/wan/olt' },
        { label: 'ONT', href: '/wan/ont' },
        { label: 'Metro', href: '/wan/metro' },
        { label: 'OLO', href: '/wan/olo' },
      ],
    },
  ],
};
