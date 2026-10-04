import React, { useEffect, useState, useRef, useMemo } from 'react';
import L from 'leaflet';

// CSS
import 'leaflet/dist/leaflet.css';
import 'leaflet.markercluster/dist/MarkerCluster.css';
import 'leaflet.markercluster/dist/MarkerCluster.Default.css';
import 'leaflet-routing-machine/dist/leaflet-routing-machine.css';
import 'leaflet-easybutton/src/easy-button.css';
import 'leaflet.polylinemeasure/Leaflet.PolylineMeasure.css';
import 'leaflet-search/src/leaflet-search.css';
import 'leaflet.fullscreen/dist/Control.FullScreen.css';

// JS plugins
import 'leaflet.markercluster';
import 'leaflet-routing-machine';
import 'leaflet-easybutton';
import 'leaflet.polylinemeasure';
import 'leaflet-search';
import 'leaflet.fullscreen';

import { useQuery } from '@tanstack/react-query';
import { Button } from '@/components/ui/Button';
import { Card } from '@/components/ui/Card';
import { Navigation } from 'lucide-react';

import { distanceBetween, bearingBetween, formatDistance, coordinateToGoogleMaps, isValidCoordinate } from '@/utils/geo';

const ICON_OPTIONS = { iconSize: [32, 32] as [number, number], iconAnchor: [16, 32] as [number, number], popupAnchor: [0, -32] as [number, number], shadowSize: [32, 32] as [number, number] };
const blueIcon = L.icon({ iconUrl: '/icons/metro-48.png', shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-shadow.png', ...ICON_OPTIONS });
const orangeIcon = L.icon({ iconUrl: '/icons/radio-48.png', shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-shadow.png', ...ICON_OPTIONS });

type BtsItem = { idnodeb: string; site_id: string; site_name: string; transport: string; tikor_site: string; coordinates: [number, number]; type: 'nodeb' | 'radio'; d?: number; };
const initialCenter: [number, number] = [-3.797326, 102.266005];

const MapsPage = () => {
  const [coords, setCoords] = useState<{ lat: number; lng: number } | null>(null);
  const [searchFilter, setSearchFilter] = useState('');
  const [measurementStartPoint, setMeasurementStartPoint] = useState<[number, number] | null>(null);
  const [radiusKm, setRadiusKm] = useState(0);
  const [radiusCenter, setRadiusCenter] = useState<[number, number] | null>(null);
  const [currentLocation, setCurrentLocation] = useState<{lat: number, lng: number, accuracy: number} | null>(null);
  const [nearestBts, setNearestBts] = useState<BtsItem[]>([]);
  const [nearestBtsVisible, setNearestBtsVisible] = useState(false);
  const [isFullscreen, setFullscreen] = useState(false);

  const toggleFullscreen = () => {
    const element = containerRef.current;
    if (document.fullscreenElement) {
      document.exitFullscreen();
    } else {
      element?.requestFullscreen();
    }
  };

  const mapInstanceRef = useRef<L.Map | null>(null);
  const containerRef = useRef<HTMLDivElement | null>(null);
  const controlsRef = useRef<Record<string, any>>({}); 
  const latLongInputRef = useRef<HTMLInputElement | null>(null);
  const radiusInputRef = useRef<HTMLInputElement | null>(null);
  const tempMeasurementLayerRef = useRef<L.LayerGroup | null>(null);
  const radiusCircleRef = useRef<L.Circle | null>(null);
  const userLocationMarkerRef = useRef<L.Marker | null>(null);
  const userLocationCircleRef = useRef<L.Circle | null>(null);
  const searchMarkerRef = useRef<L.Marker | null>(null);


  const { data: mapData, isLoading } = useQuery({
    queryKey: ['maps', 'nodeb'],
    queryFn: async () => {
      const res = await fetch('http://127.0.0.1:8001/api/master-data/maps/nodeb');
      if (!res.ok) throw new Error('Failed to fetch');
      return res.json();
    }
  });

  const visibleMarkers = useMemo(() => {
    if (!mapData) return [];
    const nodebIds = new Set(mapData.nodeb?.map((n: any) => n.site_id) || []);
    const parse = (s: string) => s?.replace(/[^\d.,-]/g, '').split(',').map(Number) as [number, number];
    const nodeb = (mapData.nodeb || []).map((item: any) => ({ ...item, type: 'nodeb' as const, coordinates: parse(item.tikor_site) })).filter((m: BtsItem) => m.coordinates?.length === 2 && !isNaN(m.coordinates[0]));
    const radio = (mapData.radio || []).filter((item: any) => !nodebIds.has(item.site_id)).map((item: any) => ({ ...item, type: 'radio' as const, coordinates: parse(item.tikor_site) })).filter((m: BtsItem) => m.coordinates?.length === 2 && !isNaN(m.coordinates[0]));
    const all = [...nodeb, ...radio];
    const term = searchFilter.toLowerCase();
    let filtered = all.filter(m => (m.site_id?.toLowerCase().includes(term)) || (m.site_name?.toLowerCase().includes(term)));
    if (radiusKm > 0 && radiusCenter) {
      filtered = filtered.filter(m => distanceBetween(radiusCenter[0], radiusCenter[1], m.coordinates[0], m.coordinates[1]) <= radiusKm * 1000);
    }
    return filtered;
  }, [mapData, searchFilter, radiusKm, radiusCenter]);

  useEffect(() => {
    if (!containerRef.current || mapInstanceRef.current) return;
    const map = L.map(containerRef.current, { center: initialCenter, zoom: 13, scrollWheelZoom: true });
    mapInstanceRef.current = map;
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

    controlsRef.current.scale = L.control.scale({ position: 'bottomleft' }).addTo(map);
    
// Polyline Measure Control
  controlsRef.current.measure = (L.control as any).polylineMeasure({
    position: 'topleft',
    unit: 'kilometres',
    units: ['metres', 'kilometres', 'nauticalmiles', 'miles'],
    showBearings: true,
    clearMeasurementsOnStop: false,
    showClearControl: true,
    showUnitControl: true
  }).addTo(map);

    let routingControlRef = (L as any).Routing.control({ 
      position: 'bottomright',
      collapsible: true, 
      show: false 
    }).addTo(map);
    controlsRef.current.routing = routingControlRef; // Store reference

    // Initially add routing container to bottom-right position for legacy layout
    map.once('zoomend', () => {
      const rc = map.getContainer().querySelector('.leaflet-control-routing');
      if (rc) {
        const tr = document.querySelector('.leaflet-top.leaflet-right');
        if (tr) tr.appendChild(rc);
      }
    });
    controlsRef.current.cluster = (L as any).markerClusterGroup({ maxClusterRadius: 50, disableClusteringAtZoom: 18 });
    map.addLayer(controlsRef.current.cluster);

    controlsRef.current.search = new (L.Control as any).Search({ position: 'topright', layer: controlsRef.current.cluster, propertyName: 'title', moveToLocation: (latlng: L.LatLng, t: any, m: L.Map) => m.setView(latlng, 18) }).addTo(map);
    controlsRef.current.easy = (L as any).easyButton('<span>🙈</span>', (btn: any) => { 
      if (map.hasLayer(controlsRef.current.cluster)) { map.removeLayer(controlsRef.current.cluster); btn.button.innerHTML = '<span>👁️</span>'; } 
      else { map.addLayer(controlsRef.current.cluster); btn.button.innerHTML = '<span>🙈</span>'; }
    }).addTo(map);

    if ((L.control as any).fullscreen) (L.control as any).fullscreen({ position: 'topleft' }).addTo(map);

    tempMeasurementLayerRef.current = L.layerGroup().addTo(map);
    userLocationMarkerRef.current = L.marker([0,0]);
    userLocationCircleRef.current = L.circle([0,0], {radius: 0});
    const userLayer = L.layerGroup([userLocationMarkerRef.current, userLocationCircleRef.current]).addTo(map);
    radiusCircleRef.current = L.circle(initialCenter, {radius: 0, color: '#ff6600', fillOpacity: 0.1}).addTo(map);
    
    L.control.layers({ "OSM": L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png') }, {
      "BTS": controlsRef.current.cluster, "Measure": tempMeasurementLayerRef.current, "User": userLayer, "Radius": radiusCircleRef.current
    }).addTo(map);

    map.on('click', () => { setNearestBtsVisible(false); });

    return () => { map.remove(); mapInstanceRef.current = null; };
  }, []);

  // Global styles for modern Leaflet controls
  useEffect(() => {
    const style = document.createElement('style');
    style.innerHTML = `
      .leaflet-top.leaflet-right .leaflet-control {
        margin-right: 12px !important;
        margin-top: 8px !important;
        border: 1px solid rgba(255,255,255,0.12) !important;
        border-radius: 10px !important;
        box-shadow: 0 6px 18px rgba(0,0,0,0.35) !important;
        overflow: hidden;
        backdrop-filter: blur(10px);
      }
      /* Dark mode (default) */
      .leaflet-control-layers,
      .leaflet-control-search,
      .leaflet-routing-container {
        background: rgba(15, 23, 42, 0.95) !important; /* slate-950 */
        color: #f1f5f9 !important; /* slate-50 */
      }
      /* Light mode override */
      @media (prefers-color-scheme: light) {
        .leaflet-top.leaflet-right .leaflet-control {
          border-color: rgba(0,0,0,0.1) !important;
          box-shadow: 0 6px 18px rgba(0,0,0,0.15) !important;
        }
        .leaflet-control-layers,
        .leaflet-control-search,
        .leaflet-routing-container {
          background: rgba(255,255,255,0.95) !important;
          color: #0f172a !important; /* slate-950 */
        }
        .leaflet-control-search .search-input {
          color: #0f172a !important;
        }
      }
      .leaflet-control-layers-list,
      .search-input,
      .leaflet-routing-alt {
        font-size: 11px !important;
        font-family: inherit !important;
      }
      .leaflet-control-layers-toggle {
        background-size: 16px 16px !important;
        width: 32px !important;
        height: 32px !important;
      }
      .leaflet-routing-container {
        max-width: 280px !important;
        max-height: 400px !important;
        overflow-y: auto !important;
      }
      .leaflet-routing-collapse-btn {
        top: 4px !important;
        right: 4px !important;
      }
      .leaflet-popup-content {
        background: rgba(15, 23, 42, 0.95) !important;
        color: #f1f5f9 !important;
        border-radius: 8px !important;
        box-shadow: 0 4px 12px rgba(0,0,0,0.3) !important;
        padding: 8px !important;
      }
      .leaflet-control-search .search-input {
        color: #e2e8f0 !important;
      }
    `;
    document.head.appendChild(style);
    return () => { document.head.removeChild(style); };
  }, []);

  useEffect(() => {
    const cluster = controlsRef.current.cluster;
    if (!cluster || !mapInstanceRef.current) return;
    cluster.clearLayers();
    visibleMarkers.forEach(item => {
      const isBlue = item.type === 'nodeb' || item.transport?.toLowerCase().includes('metro');
      const marker = L.marker(item.coordinates, { icon: isBlue ? blueIcon : orangeIcon, title: item.site_id });
      const div = L.DomUtil.create('div', 'text-[11px] space-y-1 w-48');
      div.innerHTML = `<b>Site ID :</b> ${item.site_id}<br><b>Site Name :</b> ${item.site_name}<br><b>Transport :</b> ${item.transport || 'Metro-E'}<br><b>Coord :</b> ${item.coordinates.join(',')}<br>
        <div class="flex flex-col gap-1 mt-2 border-t pt-2">
          <a target="_blank" href="/wan/${item.type==='nodeb'?'nodeb':'allnodeb'}/detail/${item.idnodeb}" class="text-blue-500 font-bold">View Detail</a>
          <button class="m-f-btn bg-purple-600 text-white p-1 rounded">Measure From Here</button>
          <button class="m-t-btn bg-yellow-600 text-white p-1 rounded">Measure To Here</button>
          <button class="g-m-btn bg-red-600 text-white p-1 rounded">Google Maps</button>
        </div>`;
      L.DomEvent.on(div, 'click', (e: any) => {
        const t = e.target as HTMLElement;
        if (t.classList.contains('m-f-btn')) { 
          setMeasurementStartPoint(item.coordinates); 
          mapInstanceRef.current?.closePopup(); 
        }
        if (t.classList.contains('m-t-btn') && measurementStartPoint) {
          const start = measurementStartPoint;
          const end = item.coordinates;
          if (controlsRef.current.routing) {
            controlsRef.current.routing.setWaypoints([
              L.latLng(start[0], start[1]),
              L.latLng(end[0], end[1])
            ]);
            controlsRef.current.routing.show();
          }
          if (tempMeasurementLayerRef.current) {
            tempMeasurementLayerRef.current.clearLayers();
          }
          setMeasurementStartPoint(null);
        }
        if (t.classList.contains('g-m-btn')) window.open(coordinateToGoogleMaps(item.coordinates[0], item.coordinates[1]), '_blank');
      });
      marker.bindPopup(div, { maxHeight: 220, autoPan: true });
      cluster.addLayer(marker);
    });
  }, [visibleMarkers, measurementStartPoint]);

  useEffect(() => {
    const onFullscreenChange = () => {
      setFullscreen(!!document.fullscreenElement);
    };
    document.addEventListener('fullscreenchange', onFullscreenChange);
    return () => {
      document.removeEventListener('fullscreenchange', onFullscreenChange);
    };
  }, []);

  const handleCurrentLocation = () => {
    navigator.geolocation.getCurrentPosition((p) => {
      const { latitude, longitude, accuracy } = p.coords;
      setCurrentLocation({ lat: latitude, lng: longitude, accuracy });
      mapInstanceRef.current?.flyTo([latitude, longitude], 16);
      userLocationMarkerRef.current?.setLatLng([latitude, longitude]);
      userLocationCircleRef.current?.setLatLng([latitude, longitude]).setRadius(accuracy);
    }, (e) => alert(e.message), { enableHighAccuracy: true });
  };

  const applyRadius = () => {
    const val = parseFloat(radiusInputRef.current?.value || '0');
    setRadiusKm(val);
    const map = mapInstanceRef.current;
    if (!map) return;
    const center = currentLocation ? [currentLocation.lat, currentLocation.lng] as [number, number] : [map.getCenter().lat, map.getCenter().lng] as [number, number];
    setRadiusCenter(center);
    if (radiusCircleRef.current) {
      radiusCircleRef.current.setRadius(val * 1000).setLatLng(center);
    }
  };

  return (
    <div className="p-2 h-screen bg-orbit-bg flex flex-col overflow-hidden">
      <Card className="flex-1 flex flex-col p-2 bg-orbit-surface border-orbit-border overflow-hidden relative">
        <div className="flex justify-between items-center mb-1">
          <h4 className="font-bold text-xs text-amber-500 uppercase tracking-wider">Maps BTS</h4>
        </div>

        <div className="flex flex-wrap gap-x-4 gap-y-1 items-end mb-2 p-1.5 bg-orbit-surface/80 backdrop-blur-sm rounded border border-orbit-border">
          <div className="flex flex-col"><label className="text-[8px] text-slate-500 dark:text-slate-400 font-bold uppercase mb-0.5">Coordinate</label>
            <div className="flex gap-1"><input ref={latLongInputRef} className="w-24 bg-orbit-bg border border-orbit-border text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 text-[10px] px-1.5 py-0.5 rounded outline-none focus:border-amber-500" placeholder="-3.7, 102.2" />
            <Button size="xs" className="h-5 px-2 bg-amber-600 hover:bg-amber-700 text-white text-[9px]" onClick={() => { const v = latLongInputRef.current?.value.split(',').map(Number);
 if (v?.length===2) {
   const lat = v[0]; const lng = v[1];
   setCoords({ lat, lng });
   mapInstanceRef.current?.setView([lat, lng], 16);
   // Add a search marker if not exists
   if (!searchMarkerRef.current) {
     const marker = L.marker([lat, lng], { icon: L.icon({ iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-red.png', iconSize: [25, 41], iconAnchor: [12, 41], popupAnchor: [1, -35] }) });
     const popupContent = `
       <div class="flex flex-col gap-1 text-[11px]">
         <b>Coord :</b> ${lat.toFixed(6)}, ${lng.toFixed(6)}
         <button class='m-f-btn bg-purple-600 text-white p-1 rounded w-full'>Measure From Here</button>
         <button class='m-t-btn bg-yellow-600 text-white p-1 rounded w-full'>Measure To Here</button>
         <button class='g-m-btn bg-red-600 text-white p-1 rounded w-full'>Google Maps</button>
       </div>`;
     marker.bindPopup(popupContent);
     marker.addTo(mapInstanceRef.current);
     searchMarkerRef.current = marker;
   } else {
     searchMarkerRef.current.setLatLng([lat, lng]);
     searchMarkerRef.current.setPopupContent(`
       <div class="flex flex-col gap-1 text-[11px]">
         <b>Coord :</b> ${lat.toFixed(6)}, ${lng.toFixed(6)}
         <button class='m-f-btn bg-purple-600 text-white p-1 rounded w-full'>Measure From Here</button>
         <button class='m-t-btn bg-yellow-600 text-white p-1 rounded w-full'>Measure To Here</button>
         <button class='g-m-btn bg-red-600 text-white p-1 rounded w-full'>Google Maps</button>
       </div>`);
   }
 } }}>Go</Button></div>
          </div>
          <Button size="xs" className="h-5 bg-slate-600 hover:bg-slate-700 text-white text-[9px]" onClick={handleCurrentLocation}><Navigation size={10} className="mr-1" /> Lokasi Saya</Button>
          <div className="flex flex-col"><label className="text-[8px] text-slate-500 dark:text-slate-400 font-bold uppercase mb-0.5">Filter Site</label>
            <input value={searchFilter} onChange={e => setSearchFilter(e.target.value)} className="w-32 bg-orbit-bg border border-orbit-border text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 text-[10px] px-1.5 py-0.5 rounded outline-none focus:border-amber-500" placeholder="ID / Name..." /></div>
          <div className="flex flex-col"><label className="text-[8px] text-slate-500 dark:text-slate-400 font-bold uppercase mb-0.5">Rad (km)</label>
            <div className="flex gap-1"><input ref={radiusInputRef} type="number" defaultValue={1} className="w-10 bg-orbit-bg border border-orbit-border text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 text-[10px] px-1 py-0.5 rounded outline-none focus:border-amber-500" />
            <Button size="xs" className="h-5 bg-green-700 hover:bg-green-800 text-white text-[9px]" onClick={applyRadius}>Apply</Button></div>
          </div>
          <Button size="xs" className="h-5 bg-blue-700 hover:bg-blue-800 text-white text-[9px]" onClick={() => { 
            const c = currentLocation ? [currentLocation.lat, currentLocation.lng] : mapInstanceRef.current?.getCenter();
            if(!c) return;
            const lat = (c as any).lat ?? (c as any)[0]; const lng = (c as any).lng ?? (c as any)[1];
            const dists = visibleMarkers.map(m => ({...m, d: distanceBetween(lat, lng, m.coordinates[0], m.coordinates[1])})).sort((a,b)=>a.d-b.d).slice(0,5);
            setNearestBts(dists); setNearestBtsVisible(true);
          }}>Nearest</Button>
          <Button size="xs" className="h-5 bg-slate-600 hover:bg-slate-700 text-white text-[9px]" onClick={toggleFullscreen}>{isFullscreen ? 'Exit Fullscreen' : 'Fullscreen'}</Button>
        </div>

        <div className="relative flex-1 border border-orbit-border rounded overflow-hidden">
          <div ref={containerRef} className="h-full w-full bg-slate-900" />

          {isLoading && <div className="absolute inset-0 flex items-center justify-center bg-black/40 z-[1001] text-amber-500 text-xs font-bold">LOADING MAP DATA...</div>}
        </div>

        {nearestBtsVisible && (
          <div className="absolute bottom-6 left-6 bg-orbit-surface p-2 rounded border border-orbit-border shadow-2xl z-[2000] w-52 text-[10px]">
            <div className="flex justify-between font-bold text-amber-500 mb-1 border-b border-white/10 pb-1 uppercase tracking-tighter"><span>Nearest BTS (Top 5)</span><button onClick={()=>setNearestBtsVisible(false)} className="text-white hover:text-red-500">×</button></div>
            <div className="max-h-40 overflow-y-auto pr-1">
              {nearestBts.map(b => <div key={b.idnodeb} className="py-1 border-b border-white/5 flex justify-between hover:bg-white/5 cursor-pointer" onClick={()=>mapInstanceRef.current?.setView(b.coordinates, 18)}><span><b>{b.site_id}</b></span><span className="text-slate-400">{formatDistance(b.d || 0)}</span></div>)}
              {nearestBts.length===0 && <div className="text-slate-500 italic py-2">No BTS nearby.</div>}
            </div>
          </div>
        )}
      </Card>
    </div>
  );
};
export default MapsPage;