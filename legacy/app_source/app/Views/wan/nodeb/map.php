<?= $this->extend('layout/default') ?>

<?= $this->section('leaflet-css') ?>
<link rel="stylesheet" href="<?= base_url() ?>/template/node_modules/leaflet/leaflet.css">
<link rel="stylesheet" href="<?= base_url() ?>/template/node_modules/leaflet/leaflet-search.css">
<link rel="stylesheet" href="<?= base_url() ?>/template/node_modules/leaflet/leaflet-cluster.css">
<link rel="stylesheet" href="<?= base_url() ?>/template/node_modules/leaflet/leaflet.defaultextent.css">
<link rel="stylesheet" href="<?= base_url() ?>/template/node_modules/leaflet/leaflet-measure.css">
<link rel="stylesheet" href="<?= base_url() ?>/template/node_modules/leaflet/leaflet-easybutton.css">
<link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.css">

<?= $this->endSection(); ?>

<?= $this->section('content') ?>

<!-- Main Content -->
<div class="main-content">
    <section class="section">
        <div class="section-header mb-3 px-3">
            <div class="font-weight-bold ml-3" style="font-size: 1rem;">MAPS NODE-B</span></div>
        </div>
        <div class="section-body">
            <label for="LatLong">Search : </label>
            <input type="text" id="LatLong">
            <button type="button" onclick="goThere()">Go</button>
            <div class="card card-primary">
                <div class="card-body col-md-12 p-0">
                    <div id="map" style="width: 100%; height: 600px;"></div>
                </div>
            </div>
        </div>
    </section>
</div>

<?= $this->endSection(); ?>

<?= $this->section('leaflet-js') ?>
<script src="<?= base_url() ?>/template/node_modules/leaflet/leaflet.js"></script>
<script src="<?= base_url() ?>/template/node_modules/leaflet/leaflet-search.js"></script>
<script src="<?= base_url() ?>/template/node_modules/leaflet/leaflet-cluster.js"></script>
<script src="<?= base_url() ?>/template/node_modules/leaflet/leaflet.defaultextent.js"></script>
<script src="<?= base_url() ?>/template/node_modules/leaflet/leaflet-measure.js"></script>
<script src="<?= base_url() ?>/template/node_modules/leaflet/leaflet-easybutton.js"></script>
<script src="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.js"></script>
<script>
    function slugify(str) {
        return String(str)
            .normalize('NFKD') // split accented characters into their base characters and diacritical marks
            .replace(/[\u0300-\u036f]/g, '') // remove all the accents, which happen to be all in the \u03xx UNICODE block.
            .trim() // trim leading or trailing whitespace
            .toLowerCase() // convert to lowercase
            .replace(/[^a-z0-9 -]/g, '') // remove non-alphanumeric characters
            .replace(/\s+/g, '-') // replace spaces with hyphens
            .replace(/-+/g, '-'); // remove consecutive hyphens
    }
    var data = [
        <?php foreach ($dataNodeb as $key => $value) : ?> {
                "tikor_site": [<?= $value->tikor_site; ?>],
                "site_id": "<?= $value->site_id; ?>",
                "site_name": "<?= $value->site_name; ?>",
                "idnodeb": "<?= $value->idnodeb; ?>",
            },
        <?php endforeach ?>
    ];
    var data_radio = [
        <?php foreach ($datanodeb_all as $key => $value) : ?> {
                "tikor_site": [<?= $value->lat_long; ?>],
                "site_id": "<?= $value->site_id_all; ?>",
                "site_name": "<?= $value->site_name_all; ?>",
                "transport": "<?= $value->transport; ?>",
                "idnodeb": "<?= $value->site_id_all; ?>",
                "tp": "<?= $value->tp; ?>",
            },
        <?php endforeach ?>
    ];

    var map = new L.Map('map', {
        zoom: 15,
        center: new L.latLng(-3.797326, 102.266005)
    }); //set center from first location

    map.addLayer(new L.TileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png')); //base layer

    var markersLayer = new L.LayerGroup(); //layer contain searched elements
    var markers = L.markerClusterGroup({
        maxClusterRadius: 10
    });
    map.addLayer(markers);
    map.addLayer(markersLayer);

    //start measure
    L.control.scale({
        maxWidth: 240,
        metric: true,
        imperial: false,
        position: 'bottomleft'
    }).addTo(map);
    let polylineMeasure = L.control.polylineMeasure({
        position: 'topleft',
        unit: 'kilometres',
        showBearings: true,
        clearMeasurementsOnStop: false,
        showClearControl: true,
        showUnitControl: true
    })
    polylineMeasure.addTo(map);

    function debugevent(e) {
        console.debug(e.type, e, polylineMeasure._currentLine)
    }

    map.on('polylinemeasure:toggle', debugevent);
    map.on('polylinemeasure:start', debugevent);
    map.on('polylinemeasure:resume', debugevent);
    map.on('polylinemeasure:finish', debugevent);
    map.on('polylinemeasure:change', debugevent);
    map.on('polylinemeasure:clear', debugevent);
    map.on('polylinemeasure:add', debugevent);
    map.on('polylinemeasure:insert', debugevent);
    map.on('polylinemeasure:move', debugevent);
    map.on('polylinemeasure:remove', debugevent);
    // end measure


    var controlSearch = new L.Control.Search({
        position: 'topright',
        layer: markersLayer,
        initial: false,
        zoom: 20,
        marker: false,
    });

    map.addControl(new L.Control.Search({
        position: 'topright',
        layer: markersLayer,
        initial: false,
        zoom: 20,
        collapsed: true
    }));


    //icon
    var metro_e = L.icon({
        iconUrl: "<?= base_url() ?>/template/node_modules/leaflet/images/metro-48.png",
        shadowUrl: '<?= base_url() ?>/template/node_modules/leaflet/images/marker-shadow.png',

        iconSize: [38, 38], // size of the icon
        shadowSize: [30, 54], // size of the shadow
        iconAnchor: [19, 35], // point of the icon which will correspond to marker's location
        shadowAnchor: [4, 52], // the same for the shadow
        popupAnchor: [-3, -25] // point from which the popup should open relative to the iconAnchor
    });

    //populate map with markers from sample data
    for (i in data) {
        var site_id = data[i].site_id, //value searched
            tikor_site = data[i].tikor_site, //position found
            site_name = data[i].site_name, //position found
            idnodeb = data[i].idnodeb, //position found
            marker = new L.Marker(new L.latLng(tikor_site), {
                icon: metro_e,
                title: site_id
            }); //set property searched

        var container = L.DomUtil.create('div'),
            isi = container.innerHTML = '<b>Site ID :</b> ' + site_id + '<br>' +
            '<b>Site Name :</b> ' + site_name + '<br>' +
            '<b>Transport :</b> ' + 'Metro-E Telkom' + '<br>' +
            '<b>Coordinate :</b> ' + tikor_site + '<br>' +
            '<br><a target="_blank" rel="noopener noreferrer" href="/wan/nodeb/detail/' + idnodeb + '/' + slugify(site_id + ' ' + site_name) + '" type="button">View Detail</a><br><br>',
            startBtn = createButton('Start from this location', container, tikor_site),
            destBtn = createButton('Go to this location', container, tikor_site);

        marker.bindPopup(container);

        L.DomEvent.on(startBtn, 'click', function() {
            tikor_site = $(this).data('tikor');
            tikor_site.replaceAll(" ", "");
            var coordinates = tikor_site.split(","); //create an array containing lat and lng as strings
            coordinates[0] = parseFloat(coordinates[0]); //convert lat string to number
            coordinates[1] = parseFloat(coordinates[1]); //convert lng string to number
            var latlng = L.latLng(coordinates);
            routeControl.spliceWaypoints(0, 1, latlng);
            map.closePopup();
        });
        L.DomEvent.on(destBtn, 'click', function() {
            tikor_site = $(this).data('tikor');
            tikor_site.replaceAll(" ", "");
            var coordinates = tikor_site.split(","); //create an array containing lat and lng as strings
            coordinates[0] = parseFloat(coordinates[0]); //convert lat string to number
            coordinates[1] = parseFloat(coordinates[1]); //convert lng string to number
            var latlng = L.latLng(coordinates);
            routeControl.spliceWaypoints(routeControl.getWaypoints().length - 1, 1, latlng);
            map.closePopup();
        });
        markersLayer.addLayer(markers);
        markers.addLayer(marker);
    }


    //icon
    var radio = L.icon({
        iconUrl: "<?= base_url() ?>/template/node_modules/leaflet/images/radio-48.png",
        shadowUrl: '<?= base_url() ?>/template/node_modules/leaflet/images/marker-shadow.png',

        iconSize: [38, 38], // size of the icon
        shadowSize: [30, 54], // size of the shadow
        iconAnchor: [19, 35], // point of the icon which will correspond to marker's location
        shadowAnchor: [4, 52], // the same for the shadow
        popupAnchor: [-3, -25] // point from which the popup should open relative to the iconAnchor
    });
    //populate map with markers from sample data
    var func = [];
    for (var i = 0; i < data_radio.length; i++) {
        var site_id = data_radio[i].site_id, //value searched
            tikor_site = data_radio[i].tikor_site, //position found
            site_name = data_radio[i].site_name, //position found
            idnodeb = data_radio[i].idnodeb, //position found
            transport = data_radio[i].transport, //position found
            tp = data_radio[i].tp, //position found
            marker = new L.Marker(new L.latLng(tikor_site), {
                icon: radio,
                title: site_id
            }); //set property searched

        var container = L.DomUtil.create('div'),
            isi = container.innerHTML = '<b>Site ID :</b> ' + site_id + '<br>' +
            '<b>Site Name :</b> ' + site_name + '<br>' +
            '<b>Transport :</b> ' + transport + '<br>' +
            '<b>Site Owner :</b> ' + tp + '<br>' +
            '<b>Coordinate :</b> ' + tikor_site + '<br>' +
            '<br><a target="_blank" rel="noopener noreferrer" href="/wan/allnodeb/detail/' + idnodeb + '" type="button">View Detail</a><br><br>',
            startBtn = createButton('Start from this location', container, tikor_site),
            destBtn = createButton('Go to this location', container, tikor_site);

        marker.bindPopup(container);

        L.DomEvent.on(startBtn, 'click', function() {
            tikor_site = $(this).data('tikor');
            tikor_site.replaceAll(" ", "");
            var coordinates = tikor_site.split(","); //create an array containing lat and lng as strings
            coordinates[0] = parseFloat(coordinates[0]); //convert lat string to number
            coordinates[1] = parseFloat(coordinates[1]); //convert lng string to number
            var latlng = L.latLng(coordinates);
            routeControl.spliceWaypoints(0, 1, latlng);
            map.closePopup();
        });
        L.DomEvent.on(destBtn, 'click', function() {
            tikor_site = $(this).data('tikor');
            tikor_site.replaceAll(" ", "");
            var coordinates = tikor_site.split(","); //create an array containing lat and lng as strings
            coordinates[0] = parseFloat(coordinates[0]); //convert lat string to number
            coordinates[1] = parseFloat(coordinates[1]); //convert lng string to number
            var latlng = L.latLng(coordinates);
            routeControl.spliceWaypoints(routeControl.getWaypoints().length - 1, 1, latlng);
            map.closePopup();
        });
        markersLayer.addLayer(markers);
        markers.addLayer(marker);

    }

    // to search cordinates
    function goThere() {
        var LatLong = document.getElementById('LatLong').value;
        LatLong = LatLong.replaceAll(" ", "");
        var coordinates = LatLong.split(","); //create an array containing lat and lng as strings
        coordinates[0] = parseFloat(coordinates[0]); //convert lat string to number
        coordinates[1] = parseFloat(coordinates[1]); //convert lng string to number
        var latlng = L.latLng(coordinates);
        marker = L.marker(latlng).addTo(map);
        var container = L.DomUtil.create('div'),
            isi = container.innerHTML = '<b>Coordinate :</b> ' + LatLong + '<br><br>',
            startBtn = createButton('Start from this location', container, tikor_site),
            destBtn = createButton('Go to this location', container, tikor_site);
        marker.bindPopup(container)
        map.setView(latlng, 20);

        L.DomEvent.on(startBtn, 'click', function() {
            routeControl.spliceWaypoints(0, 1, latlng);
            map.closePopup();
        });
        L.DomEvent.on(destBtn, 'click', function() {
            routeControl.spliceWaypoints(routeControl.getWaypoints().length - 1, 1, latlng);
            map.closePopup();
        });
    }

    // start routing distance
    var routeControl = L.Routing.control({
            collapsible: true,
            routeWhileDragging: true,
            show: false
        })
        .addTo(map);

    function createButton(label, container, tikor_site) {
        var btn = L.DomUtil.create('button', '', container);
        btn.setAttribute('type', 'button');
        btn.setAttribute('data-tikor', tikor_site);
        btn.innerHTML = label;
        return btn;
    }

    // map.on('click', function(e) {
    //     var container = L.DomUtil.create('div'),
    //         isi = container.innerHTML = `${e.latlng.toString()}<br><br>`,
    //         startBtn = createButton('Start from this location', container),
    //         destBtn = createButton('Go to this location', container);


    //     L.popup()
    //         .setContent(container)
    //         .setContent(container)
    //         .setLatLng(e.latlng)
    //         .openOn(map);
    //     L.DomEvent.on(startBtn, 'click', function() {
    //         routeControl.spliceWaypoints(0, 1, e.latlng);
    //         map.closePopup();
    //     });
    //     L.DomEvent.on(destBtn, 'click', function() {
    //         routeControl.spliceWaypoints(routeControl.getWaypoints().length - 1, 1, e.latlng);
    //         map.closePopup();
    //     });
    // });

    // end routing distance





    // const map = L.map('map').setView([-3.797326, 102.266005], 15);
    L.control.defaultExtent().addTo(map);

    function onMapClick(e) {
        // L.popup()
        //     .setLatLng(e.latlng)
        //     .setContent(`${e.latlng.toString()}`)
        //     .openOn(map);
        var container = L.DomUtil.create('div'),
            isi = container.innerHTML = `${e.latlng.toString()}<br><br>`,
            startBtn = createButton('Start from this location', container),
            destBtn = createButton('Go to this location', container);

        L.popup()
            .setContent(container)
            .setLatLng(e.latlng)
            .openOn(map);
        L.DomEvent.on(startBtn, 'click', function() {
            routeControl.spliceWaypoints(0, 1, e.latlng);
            map.closePopup();
        });
        L.DomEvent.on(destBtn, 'click', function() {
            routeControl.spliceWaypoints(routeControl.getWaypoints().length - 1, 1, e.latlng);
            map.closePopup();
        });
    }

    map.on('click', onMapClick);

    // start easyButton
    var toggle = L.easyButton({
  states: [{
    stateName: 'add-markers',
    icon: 'far fa-eye',
    title: 'show markers',
    onClick: function(control) {
      map.addLayer(markers);
      control.state('remove-markers');
    }
  }, {
    icon: 'fas fa-eye-slash',
    stateName: 'remove-markers',
    onClick: function(control) {
      map.removeLayer(markers);
      control.state('add-markers');
    },
    title: 'hide markers'
  }]
});
toggle.addTo(map);
    // end easyButton
</script>
<?= $this->endSection(); ?>