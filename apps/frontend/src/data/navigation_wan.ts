import { Network } from 'lucide-react';
import type { NavSection } from '@/types';

export const wanNavigation: NavSection = {
  title: 'Infrastructure',
  items: [
    {
      label: 'WAN Services',
      icon: Network,
      children: [
        { label: 'Maps', href: '/wan/maps' },
        { label: 'Assurance', href: '/wan/assurance' },
        { label: 'Fulfillment', href: '/wan/fulfillment' },
        { label: 'NodeB All', href: '/wan/allnodeb' },
        { label: 'NodeB', href: '/wan/nodeb' },
        { label: 'ONT', href: '/wan/ont' },
        { label: 'OLO', href: '/wan/olo' },
      ],
    },
  ],
};
