/*
 * Extiendo la clase L.WMS.source
 */

var wms_GIS = L.WMS.Source.extend({

    'showFeatureInfo': function(latlng, info) {

        if (!this._map){
            return;
        }

        var objMapa = this._map;
  
        var datos = JSON.parse(info);

        var capasServicio = ['vw_servicio_publico_aporte_suelo', 'vw_servicio_publico_cuneteo1', 'vw_servicio_publico_desbarre_de_calle', 'vw_servicio_publico_ensanchamiento', 'vw_servicio_publico_perfilado'];

        // no trae ningun dato
        if(datos.features.length===0) return true;

        /* que layer */
        var queLayer = datos.features[0].id.split('.');

        if(capasServicio.includes(queLayer[0])){

          $.ajax('lee_atributo.php', {
            async: true,
            data: {
              id_sp: datos.features[0].properties.id_sp, 
              actividad: datos.features[0].properties.actividad,
              calle: datos.features[0].properties.nombre_calles,
              altura: datos.features[0].properties.altur_par,
              barrio: datos.features[0].properties.nombre_barrio,
              srv: queLayer[0]
            },
            dataType: 'html',
            error: function(jqXHR, textStatus, errorThrown) {
              console.log(jqXHR);
              console.log('textStatus', textStatus);
              console.log(errorThrown);
              return true;
            },
            success: function(data, textStatus, jqXHR) {
              objMapa.openPopup(data, latlng);
              return true;
            },
            type: 'POST'
          });

          return true; 
        }

        if(queLayer[0] === 'vw_visor_alumbrado_publico'){

          var info = '<div class="card text-dark" style="width:20rem;">';
          info += '<div class="card-header"><h5>Alumbrado publico</h5></div>';
          info += '<div class="card-body">';
          //info += '<h5 class="card-title">Alumbrado publico</h5>';
          info += '<p class="card-text">';
          info += '<div style="display:block;"><div style="float:left;font-weight:600;width:7rem;">Nomenclador: </div><div style="float:left;">' + datos.features[0].properties.cod_nomenc + '</div></div><br/>';
          info += '<div style="display:block;"><div style="float:left;font-weight:600;width:7rem;">Lampara: </div><div style="float:left;">' + datos.features[0].properties.lampara + '</div></div><br/>';
          info += '<div style="display:block;"><div style="float:left;font-weight:600;width:7rem;">Potencia: </div><div style="float:left;">' + datos.features[0].properties.potencia + '</div></div><br/>';
          info += '<div style="display:block;"><div style="float:left;font-weight:600;width:7rem;">Condicion: </div><div style="float:left;">' + datos.features[0].properties.condicion + '</div></div><br/>';
          info += '<div style="display:block;"><div style="float:left;font-weight:600;width:7rem;">Calle: </div><div style="float:left;">' + datos.features[0].properties.calle + '</div></div><br/>';
          info += '<div style="display:block;"><div style="float:left;font-weight:600;width:7rem;">Altura: </div><div style="float:left;">' + datos.features[0].properties.altura + '</div></div><br/>';
          info += '<div style="display:block;"><div style="float:left;font-weight:600;width:7rem;">Barrio: </div><div style="float:left;">' + datos.features[0].properties.barrio + '</div></div>';
          info += '';
          info += '</p>';
          info+= '</div>';
          info += '</div>';

          this._map.openPopup(info, latlng);

          return true; 

        }

        alert('Esta funcion estara disponible muuuuyy pronto');

        return false;

    


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
  