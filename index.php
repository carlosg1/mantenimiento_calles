<?php
session_name('mapa_gis');
session_start();
/*
var_dump($_SESSION);
exit; 
*/
if(!isset($_SESSION['validado'])){
    echo 'Acceso no autorizado';
    header('location: login/');
    exit;
}

if($_SESSION['validado']!='SI'){
    echo 'Acceso no autorizado';
    header('location: login/');
    exit;
}

?><!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="initial-scale=1,user-scalable=no,maximum-scale=1,width=device-width">
        <meta name="mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <link rel="stylesheet" href="css/leaflet.css">
        <link rel="stylesheet" href="css/qgis2web.css">
        <link rel="stylesheet" href="css/Control.OSMGeocoder.css">
        <link rel="stylesheet" href="css/leaflet-measure.css">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
        <style>
        html, body, #map {
            width: 100%;
            height: 100%;
            padding: 0;
            margin: 0;
        }
        #map { cursor: default; }
        .salir {
            z-index: 1500;
            position: absolute;
            left: 55px;
            top: 10px;
        }
        </style>
        <title></title>
    </head>
    <body>
        <button type="button" class="btn btn-info salir" id="btnSalir">Salir</button>

        <div id="map"></div>

        <script src="js/qgis2web_expressions.js"></script>
        <script src="js/leaflet.js"></script>
        <script src="js/leaflet.rotatedMarker.js"></script>
        <script src="js/leaflet.pattern.js"></script>
        <script src="js/leaflet-hash.js"></script>
        <script src="js/Autolinker.min.js"></script>
        <script src="js/rbush.min.js"></script>
        <script src="js/labelgun.min.js"></script>
        <script src="js/labels.js"></script>
        <script src="js/leaflet.wms.js"></script>
        <script src="js/Control.OSMGeocoder.js"></script>
        <script src="js/leaflet-measure.js"></script>
        <script src="js/mostrar-infowindow.js"></script>
        <script>

        var map = L.map('map', {
            drawControl: true,
            //center: [-27.49,-58.82],
            zoomControl: true, 
            maxZoom: 25, 
            minZoom: 1
        }).fitBounds([[-27.5535444089,-58.9200306504],[-27.4048480239,-58.6404398294]]);
        
        var hash = new L.Hash(map);
        //L.control.attribution({prefix: false, position: 'bottomlef'});

        map.attributionControl.addAttribution(false);
        map.attributionControl.getContainer().innerHTML='<?php echo 'Usuario: ' . $_SESSION['usuario'] . ' - '; ?>'+'<a href="http://gis.ciudaddecorrientes.gov.ar" target="_blank">Direccion Gral de SIG</a>';
        
        var measureControl = new L.Control.Measure({
            primaryLengthUnit: 'meters',
            secondaryLengthUnit: 'kilometers',
            primaryAreaUnit: 'sqmeters',
            secondaryAreaUnit: 'hectares'
        });
        
        measureControl.addTo(map);
        
        var bounds_group = new L.featureGroup([]);
        
        function setBounds() {
        }

        var overlay_GooglecnSatellite_0 = L.tileLayer('http://www.google.cn/maps/vt?lyrs=s@189&gl=cn&x={x}&y={y}&z={z}', {
            opacity: 1.0
        });
        
        var overlay_CapabaseGIS_0 = L.WMS.layer("http://172.25.50.50:8080/geoserver/wms?version=1.1.1&", "wvca", {
            format: 'image/png',
            uppercase: true,
            transparent: true,
            continuousWorld : true,
            tiled: true,
            info_format: 'text/html',
            opacity: 1,
            identify: false,
        });

        map.addLayer(overlay_CapabaseGIS_0);

        var servicioWMS = new wms_GIS("http://172.25.8.80:8080/geoserver/mantenimiento_calle_2019/wms?", {
            format: 'image/png',
            uppercase: true,
            transparent: true,
            version: '1.1.1',
            continuousWorld : true,
            tiled: true,
            attribution: "Direccion Gral de GIS",
            info_format: 'application/json',
            opacity: 1
        });

        var WMS50 = new wms_GIS("http://172.25.50.50:8080/geoserver/wms?", {
            format: 'image/png',
            uppercase: true,
            transparent: true,
            version: '1.1.1',
            continuousWorld : true,
            tiled: true,
            attribution: "Direccion Gral de GIS",
            info_format: 'application/json',
            opacity: 1
        });

        var lyr_perfilado = servicioWMS.getLayer("mantenimiento_calle_2019:vw_mantenimiento_calle_2019_perfilado");
        let lyr_perfilado2019 = servicioWMS.getLayer("mantenimiento_calle_2019:vw_mantenimiento_calle_2019_perfilado_2019");

        var lyr_reconstruccion = servicioWMS.getLayer("mantenimiento_calle_2019:vw_mantenimiento_calle_2019_reconstruccion");
        var lyr_reconstruccion2019 = servicioWMS.getLayer("mantenimiento_calle_2019:vw_mantenimiento_calle_2019_reconstruccion_2019");

        var lyr_cuneteo = servicioWMS.getLayer("mantenimiento_calle_2019:vw_mantenimiento_calle_2019_cuneteo");
        var lyr_cuneteo2019 = servicioWMS.getLayer("mantenimiento_calle_2019:vw_mantenimiento_calle_2019_cuneteo_2019");

        var lyr_callePorTipoCalzada = WMS50.getLayer("w_red_vial:vw_ide_calle_por_tipo_calzada");

        var lyr_zona_mantenimiento = L.WMS.layer("http://172.25.8.80:8080/geoserver/wms?version=1.1.1&", "mantenimiento_calle_2019:vw_zona_mantenimiento_calle", {
            format: 'image/png',
            uppercase: true,
            transparent: true,
            continuousWorld : true,
            tiled: true,
            info_format: 'text/html',
            opacity: 1
        });

        /* Calles por tipo de calzada */
/*        var lyr_callePorTipoCalzada = L.WMS.layer("http://172.25.50.50:8080/geoserver/wms?version=1.1.1&", "w_red_vial:vw_ide_calle_por_tipo_calzada", {
            format: 'image/png',
            uppercase: true,
            transparent: true,
            continuousWorld : true,
            tiled: true,
            info_format: 'text/html',
            opacity: 1
        });
*/
        var osmGeocoder = new L.Control.OSMGeocoder({
            collapsed: false,
            position: 'topleft',
            text: 'Search',
        });

        //osmGeocoder.addTo(map);

        var baseMaps = {
            "Google Satelite": overlay_GooglecnSatellite_0,
            "Capa base GIS": overlay_CapabaseGIS_0,
        };

        L.control.layers(baseMaps,{
            '<b>Limpieza de Cuneta 2019</b><div style="padding-left: 13px;"><span><img src="legend/cuneteo2019.png" /></span> Solo Año 2019</div>': lyr_cuneteo2019,
            '<b>Limpieza de cuneta (completo)</b><br /><div style="padding-left: 9px;"><table><tr><td style="text-align: center;"><img src="legend/Limpiezadecunetas_4_10.png" /></td><td> 1 Intervencion</td></tr><tr><td style="text-align: center;"><img src="legend/Limpiezadecunetas_4_2Y3Intervenciones1.png" /></td><td> 2 Y 3 Intervenciones</td></tr><tr><td style="text-align: center;"><img src="legend/Limpiezadecunetas_4_Masde3Intervenciones2.png" /></td><td> Mas de 3 Intervenciones</td></tr></table></div>': lyr_cuneteo,
            '<b>Reconstruccion de calzada 2019</b><div style="padding-left: 13px;"><span><img src="legend/reconstruccion2019.png" /></span> Solo Año 2019</div>': lyr_reconstruccion2019,
            '<b>Reconstruccion de calzada (completo)</b><br /><div style="padding-left: 13px;"><table><tr><td style="text-align: center;"><img src="legend/Reconstrucciondecordones_3_10.png" /></td><td> 1 Intervencion</td></tr><tr><td style="text-align: center;"><img src="legend/Reconstrucciondecordones_3_2Y3Intervenciones1.png" /></td><td> 2 Y 3 Intervenciones</td></tr><tr><td style="text-align: center;"><img src="legend/Reconstrucciondecordones_3_Masde3Intervenciones2.png" /></td><td> Mas de 3 Intervenciones</td></tr></table></div>': lyr_reconstruccion,
            '<b>Perfilado de calles 2019</b><div style="padding-left: 13px;"><span><img src="legend/perfilado2019.png" /></span> Solo Año 2019</div>': lyr_perfilado2019, 
            
            '<b>Perfilado de Calles (completo)</b><br /><div style="padding-left: 13px;"><table><tr><td style="text-align: center;"><img src="legend/PerfiladodeCalles_2_10.png" /></td><td> 1 Intervencion</td></tr><tr><td style="text-align: center;"><img src="legend/PerfiladodeCalles_2_2Y3Intervenciones1.png" /></td><td> 2 Y 3 Intervenciones</td></tr><tr><td style="text-align: center;"><img src="legend/PerfiladodeCalles_2_Masde3Intervenciones2.png" /></td><td>Mas de 3 Intervenciones</td></tr></table></div>': lyr_perfilado,

            '<b>Calle por tipo de calzada</b><br /><div style="padding-left: 13px;"><table><tr><td style="text-align: center;"><img src="legend/calle_por_tipo_calzada.png" /></td></tr></table></div>': lyr_callePorTipoCalzada,

            '<b>Zonas de mantenimiento</b><div style="padding-left: 13px;"><span><img src="legend/zona_mantenimiento.png" /></span> Zonas de mantenimiento 2019</div>"': lyr_zona_mantenimiento,
        },{
            collapsed:false
        }).addTo(map);

        setBounds();

        function fSalir(){
            document.location='salir/';
        }
        document.getElementById('btnSalir').addEventListener('click', function(){document.location='salir/';}, false);
        </script>
    </body>
</html>
