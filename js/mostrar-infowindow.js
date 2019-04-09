/*
 * Extiendo la clase L.WMS.source
 */

var wms_GIS = L.WMS.Source.extend({

    'showFeatureInfo': function(latlng, info) {
        if (!this._map){
            return;
        }
  
        //info = '<div>Plan Hidrico - Restitucion de pluviales</div>' + info;
  
        var datos = JSON.parse(info);

        /* que layer */
        var queLayer = datos.features[0].id.split('.');

        if (queLayer[0] === "vw_ide_calle_por_tipo_calzada") { return false; };

        if (queLayer[0] === "vw_zona_mantenimiento_calle") { return false; };
        
        /* si hace click en la capa de calles por tipo de calzada, no muestra el infowindow */
        if (datos.features[0].properties['FNA_BARIOS'] !== undefined) { return false; };

        if(datos.features[0].properties['ultima_fecha_reconstruccion'] != undefined){
            var datos1 = '<div style="width:360px; color: #ecb85b;"><h5>RECONSTRUCCION DE CALZADA</h5></div>';
            datos1 += '<B>Ultima Fecha Reconstruccion:</B> ' + datos.features[0].properties['ultima_fecha_reconstruccion'].substring(0,10) + '<br>';
            datos1 += '<B>Nro de Intervenciones:</B> ' + datos.features[0].properties['nro_intervenciones'] + '<br>';
        }
  
        if(datos.features[0].properties['ultima_fecha_perfilado'] != undefined){
            var datos1 = '<div style="width:300px; color: #5cc7f9;"><h5>PERFILADO DE CALLE</h5></div>';
            datos1 += '<B>Ultima Fecha Perfilado:</B> ' + datos.features[0].properties['ultima_fecha_perfilado'].substring(0,10) + '<br>';
            datos1 += '<B>Nro de Intervenciones:</B> ' + datos.features[0].properties['nro_intervenciones'] + '<br>';
        }

        if(datos.features[0].properties['ultima_fecha_cuneteo'] != undefined){
            var datos1 = '<div style="width:300px; color: #c3dd6d;"><h5>LIMPIEZA DE CUNETA</h5></div>';
            datos1 += '<B>Ultima Fecha Perfilado:</B> ' + datos.features[0].properties['ultima_fecha_perfilado'] + '<br>';
            datos1 += '<B>Nro de Intervenciones:</B> ' + datos.features[0].properties['nro_intervenciones'] + '<br>';
            datos1 += '<br />';
        }

        datos1 += '<div style="border-top: 1px solid #7f7f7f; padding-top: 7px; margin-top: 7px; font-family: Roboto; font-size: 11px; color: #7f7f7f">Fuente: Dir. Redes Viales</div>';

        this._map.openPopup(datos1, latlng);
    } /* ,
  
    'ajax': function(url, callback) {
        ajax.call(this, 'curl.php?url='+url, callback);
    }
  */
  
  })
  
  function leerAjax(url, callback) {
    var context = this,
        request = new XMLHttpRequest();
    request.onreadystatechange = change;
    request.open('GET', 'curl.php?url=' + url);
    request.send();
  
    function change() {
      if (request.readyState === 4) {
        if (request.status === 200) {
          callback.call(context, request.responseText);
        } else {
          callback.call(context, "error");
        }
      }
    }
  };
  