import React from 'react';
import { X } from 'lucide-react';
import { Button } from './Button';

interface ModalProps {
  isOpen: boolean;
  onClose: () => void;
  title: string;
  children: React.ReactNode;
}

export const Modal: React.FC<ModalProps> = ({ isOpen, onClose, title, children }) => {
  if (!isOpen) return null;

  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
      <div className="w-full max-w-lg rounded-xl bg-orbit-surface border border-orbit-border shadow-2xl">
        <div className="flex items-center justify-between border-b border-orbit-border p-4">
          <h3 className="text-lg font-semibold text-slate-100">{title}</h3>
          <Button variant="ghost" size="sm" onClick={onClose}>
            <X size={18} />
          </Button>
        </div>
        <div className="p-6">{children}</div>
      </div>
    </div>
  );
};
