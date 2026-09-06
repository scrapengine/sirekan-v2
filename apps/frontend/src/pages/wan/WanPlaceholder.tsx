import React from 'react';
import { Card } from '@/components/ui/Card';

export const WanPage: React.FC<{ title: string }> = ({ title }) => (
  <div className="p-6">
    <h1 className="text-2xl font-bold mb-4">{title}</h1>
    <Card className="p-8 text-center text-slate-500">
      Fitur {title} sedang dalam pengembangan (Legacy flow: {title.toLowerCase()}).
    </Card>
  </div>
);
