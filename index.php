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
        <!-- Bootstrap -->
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
        <!-- 
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
         -->

         <!-- Google Fonts -->
         <link href="https://fonts.googleapis.com/css2?family=Righteous&display=swap" rel="stylesheet">

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
            .leaflet-control-layers{
                opacity:0.85!important;
                filter: alpha(opacity=30); /* para IE8 y posterior */
            }
            .sp { 
                color: blue; 
                font-weight: 600;
                text-family: 'Roboto';
                }
            .tub {
                color: #668A42;
            }
            .infor {
                position:absolute;
                bottom: 10px;
                left: 10px;
                height: 450px;
                width: 480px;
                visibility:hidden;
                border: solid 2px #000;
                z-index: 2000;
                background-color: #fff;
            }
            .infor .card .card-body h5 {
                font-family: 'Righteous', cursive;
                font-size: 24pt;
            }
            .apu {
                color: #007E33;
                font-weight: 600;
            }
        </style>
        <title></title>
    </head>
    <body>
        <button type="button" class="btn btn-info salir" id="btnSalir">Salir</button>

        <!-- Cuadro para el infowindow -->
        <div class="infor">
            <div class="card">
                <!-- <img src="..." class="card-img-top" alt="..."> -->
                <div class="card-body">
                    <h5 id="card-titulo" class="card-title">Servicio Publico</h5>
                    <div id="card-contendido">
                        <p class="card-text">This is a wider card with supporting text below as a natural lead-in to additional content. This content is a little bit longer.</p>
                        <p class="card-text"><small class="text-muted">Last updated 3 mins ago</small></p>
                    </div>
                </div>
            </div>
        </div>
        <!-- Cuadro para el infowindow -->

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
        
        var overlay_CapabaseGIS_0 = L.WMS.layer("http://192.168.10.51:8282/geoserver/wms?version=1.3.0&", "capa_base_mcc:capa_base", {
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
            version: '1.3.0',
            continuousWorld : true,
            tiled: true,
            attribution: "Direccion Gral de GIS",
            info_format: 'application/json',
            opacity: 1
        });

        var WMSprod = new wms_GIS("http://192.168.10.51:8282/geoserver/wms?", {
            format: 'image/png',
            uppercase: true,
            transparent: true,
            version: '1.3.0',
            continuousWorld : true,
            tiled: true,
            attribution: "Direccion Gral de GIS",
            info_format: 'application/json',
            opacity: 1,
            cql_filter: "fecha_servicio > '2019-12-31'"
        });



        // tablero de control de obras
        var vw_servicio_publico_aporte_suelo = WMSprod.getLayer("servicio_publico_20:vw_servicio_publico_aporte_suelo");
        var vw_servicio_publico_cuneteo = WMSprod.getLayer("servicio_publico_20:vw_servicio_publico_Cuneteo");
        var vw_servicio_publico_desbarre_de_calle = WMSprod.getLayer("servicio_publico_20:vw_servicio_publico_desbarre_de_calle");
        var vw_servicio_publico_ensanchamiento = WMSprod.getLayer("servicio_publico_20:vw_servicio_publico_ensanchamiento");
        var vw_servicio_publico_perfilado = WMSprod.getLayer("servicio_publico_20:vw_servicio_publico_perfilado");

        // alumbrado publico
        //var vw_visor_alumbrado_publico = WMSprod.getLayer("infraestructura:vw_visor_alumbrado_publico");
        var vw_visor_alumbrado_publico = new wms_GIS("http://192.168.10.51:8282/geoserver/wms?", {
            format: 'image/png',
            uppercase: true,
            transparent: true,
            version: '1.3.0',
            continuousWorld : true,
            tiled: true,
            attribution: "Direccion Gral de GIS",
            info_format: 'application/json',
            opacity: 1,
            cql_filter: "calle LIKE 'VIEDMA%'"
        }).getLayer("infraestructura:vw_visor_alumbrado_publico");




        // colocacion de tubos
        var vw_visor_colocacion_tubo_acdom = WMSprod.getLayer("servicio_publico_20:vw_visor_colocacion_tubo_acdom");
        var vw_visor_colocacion_tubo_crucecalle = WMSprod.getLayer("servicio_publico_20:vw_visor_colocacion_tubo_crucecalle");


        var lyr_callePorTipoCalzada = WMSprod.getLayer("w_red_vial:vw_ide_calle_por_tipo_calzada");

        var lyr_zona_mantenimiento = servicioWMS.getLayer("mantenimiento_calle_2019:vw_zona_mantenimiento_calle");

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
            '<span class="sp">Aporte de suelo</span>': vw_servicio_publico_aporte_suelo,
            '<span class="sp">Cuneteo</span>': vw_servicio_publico_cuneteo,
            '<span class="sp">Desbarre de calle</span>': vw_servicio_publico_desbarre_de_calle,
            '<span class="sp">Ensanchamiento de calzada</span>': vw_servicio_publico_ensanchamiento,
            '<span class="sp">Perfilado de calle</span><hr/>': vw_servicio_publico_perfilado,



            '<span class="apu">Alumbrado publico</span><hr/>': vw_visor_alumbrado_publico,



            '<span class="tub">Colocacion tubo Acc. Dom.</span>': vw_visor_colocacion_tubo_acdom,
            '<span class="tub">Colocacion tubo Cruce calle</span><hr>': vw_visor_colocacion_tubo_crucecalle,

            '<b>Calle por tipo de calzada</b><br /><div style="padding-left: 13px;"><table><tr><td style="text-align: center;"><img src="legend/calle_por_tipo_calzada.png" /></td></tr></table></div>': lyr_callePorTipoCalzada, 

            '<b>Zonas de mantenimiento</b><div style="padding-left: 13px;"><span><img src="legend/zona_mantenimiento.png" /></span> Zonas de mantenimiento 2019</div>"': lyr_zona_mantenimiento,
        },{
            collapsed:false
        }).addTo(map);

        setBounds();

        L.control.scale().addTo(map);

        function fSalir(){
            document.location='salir/';
        }
        document.getElementById('btnSalir').addEventListener('click', function(){document.location='salir/';}, false);
        </script>

        <script src="https://code.jquery.com/jquery-3.5.1.min.js" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>

    </body>
</html>
