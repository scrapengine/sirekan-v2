import React from 'react';
import { useLocation, Link } from 'react-router-dom';

const breadcrumbNameMap: { [key: string]: string } = {
  'wan': 'WAN',
  'nodeb': 'NODE-B',
  'add': 'ADD',
  'trash': 'TRASH',
  'detail': 'DETAIL',
  'edit': 'EDIT',
  'olt': 'OLT',
  'metro': 'METRO',
  'ont': 'ONT',
  'olo': 'OLO',
  'assurance': 'ASSURANCE',
  'fulfillment': 'FULFILLMENT'
};

export const Breadcrumbs: React.FC = () => {
  const location = useLocation();
  const pathnames = location.pathname.split('/').filter((x) => x);

  return (
    <nav className="text-xs text-slate-400 mb-4 flex items-center gap-2">
      <Link to="/" className="hover:text-slate-200">Sirekan</Link>
      {pathnames.map((value, index) => {
        const last = index === pathnames.length - 1;
        const to = `/${pathnames.slice(0, index + 1).join('/')}`;
        const label = breadcrumbNameMap[value.toLowerCase()] || value.toUpperCase();

        return (
          <React.Fragment key={to}>
            <span className="text-slate-600">/</span>
            {last ? (
              <span className="text-slate-100 font-semibold">{label}</span>
            ) : (
              <Link to={to} className="hover:text-slate-200">{label}</Link>
            )}
          </React.Fragment>
        );
      })}
    </nav>
  );
};
