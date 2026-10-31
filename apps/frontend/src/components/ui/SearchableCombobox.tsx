import React, { useState, useRef, useEffect } from 'react';
import { X, ChevronDown, Search } from 'lucide-react';

interface Option {
  value: string;
  label: string;
}

interface SearchableComboboxProps {
  label?: string;
  value: string;
  onChange: (value: string) => void;
  options: Option[];
  placeholder?: string;
  disabled?: boolean;
}

export const SearchableCombobox: React.FC<SearchableComboboxProps> = ({
  label,
  value,
  onChange,
  options,
  placeholder = "Select...",
  disabled = false
}) => {
  const [isOpen, setIsOpen] = useState(false);
  const [searchTerm, setSearchTerm] = useState('');
  const [selectedOption, setSelectedOption] = useState<Option | null>(null);
  const dropdownRef = useRef<HTMLDivElement>(null);
  const inputRef = useRef<HTMLInputElement>(null);

  // Find selected option
  useEffect(() => {
    const found = options.find(o => o.value === value);
    setSelectedOption(found || null);
  }, [value, options]);

  // Filter options based on search
  const filteredOptions = options.filter(o => 
    (o.label || '').toString().toLowerCase().includes(searchTerm.toLowerCase()) ||
    (o.value || '').toString().toLowerCase().includes(searchTerm.toLowerCase())
  );

  // Handle click outside
  useEffect(() => {
    const handleClickOutside = (e: MouseEvent) => {
      if (dropdownRef.current && !dropdownRef.current.contains(e.target as Node)) {
        setIsOpen(false);
        setSearchTerm('');
      }
    };
    if (isOpen) {
      document.addEventListener('mousedown', handleClickOutside);
    }
    return () => document.removeEventListener('mousedown', handleClickOutside);
  }, [isOpen]);

  const handleSelect = (option: Option) => {
    onChange(option.value);
    setSelectedOption(option);
    setIsOpen(false);
    setSearchTerm('');
  };

  const handleClear = (e: React.MouseEvent) => {
    e.stopPropagation();
    onChange('');
    setSelectedOption(null);
    setIsOpen(false);
  };

  const displayValue = selectedOption ? selectedOption.label : placeholder;

  return (
    <div className="relative w-full" ref={dropdownRef}>
      {label && <label className="text-xs uppercase text-slate-400 font-semibold tracking-wider block mb-1">{label}</label>}
      <div 
        className={`w-full h-9 px-3 bg-orbit-bg border border-orbit-border rounded-lg text-sm text-slate-100 flex justify-between items-center cursor-pointer ${disabled ? 'opacity-60 cursor-not-allowed' : ''}`}
        onClick={() => !disabled && setIsOpen(!isOpen)}
      >
        <span className={`text-sm ${selectedOption ? 'text-slate-100' : 'text-slate-400'}`}>
          {displayValue}
        </span>
        <div className="flex items-center gap-1">
          {selectedOption && (
            <button 
              onClick={handleClear}
              className="text-slate-400 hover:text-slate-200 p-0.5"
              aria-label="Clear selection"
            >
              <X size={14} />
            </button>
          )}
          <ChevronDown size={14} className={`text-slate-400 transition-transform ${isOpen ? 'rotate-180' : ''}`} />
        </div>
      </div>
      
      {isOpen && !disabled && (
        <div className="absolute z-50 w-full mt-1 bg-orbit-surface border border-orbit-border rounded-lg shadow-xl overflow-hidden">
          <div className="p-2 border-b border-orbit-border flex items-center gap-2 bg-orbit-bg">
            <Search size={14} className="text-slate-400" />
            <input 
              ref={inputRef}
              placeholder="Search..." 
              className="w-full bg-transparent text-sm text-slate-100 focus:outline-none" 
              type="text" 
              value={searchTerm}
              onChange={(e) => setSearchTerm(e.target.value)}
              onClick={(e) => e.stopPropagation()}
              autoFocus
            />
          </div>
          <div className="max-h-60 overflow-y-auto">
            {filteredOptions.length === 0 ? (
              <div className="p-2 text-xs text-slate-500">No options found</div>
            ) : (
              filteredOptions.map((opt) => (
                <div
                  key={opt.value}
                  className={`p-2 text-xs cursor-pointer transition-colors ${
                    selectedOption?.value === opt.value 
                      ? 'bg-blue-600/20 text-blue-400 font-medium' 
                      : 'text-slate-200 hover:bg-slate-700/50'
                  }`}
                  onClick={() => handleSelect(opt)}
                  onMouseDown={(e) => e.preventDefault()}
                >
                  {opt.label}
                </div>
              ))
            )}
          </div>
        </div>
      )}
    </div>
  );
};