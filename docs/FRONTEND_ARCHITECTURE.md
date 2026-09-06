# Frontend Architecture - Sirekan V2

## Stack
- Framework: React (Vite, TypeScript)
- UI: Tailwind CSS v4
- Icons: Lucide React
- Routing: React Router v7 (or latest)
- API: Axios
- Utility: clsx, tailwind-merge

## Structure
```
frontend/
├── src/
│   ├── app/ (routing, config)
│   ├── components/ (ui, layout)
│   ├── features/ (feature-based modules: assurance, master-data, etc)
│   ├── api/ (client, services)
│   ├── hooks/
│   ├── types/
│   ├── utils/
│   └── styles/
```

## Conventions
- Always use semantic color tokens defined in `styles/theme.css`.
- Use feature-based architecture for new modules.
- Keep components small and reusable.
- Lazy-load page-level routes.
