$(document).ready(function(){
    
    overlay_CapabaseGIS_0.addTo(map);

    // capas de servicio publico
    $("#chkPerfilado").click(function(ev){
        if(this.checked){
            ev.stopPropagation();
            vw_servicio_publico_perfilado.addTo(map);
        }else{
            map.removeLayer(vw_servicio_publico_perfilado);
        }
    });

    $("#chkEnsanchamiento").click(function(ev){
        if(this.checked){
            ev.stopPropagation();
            vw_servicio_publico_ensanchamiento.addTo(map);
        }else{
            map.removeLayer(vw_servicio_publico_ensanchamiento);
        }
    });

    $("#chkAporteSuelo").click(function(ev){
        if(this.checked){
            ev.stopPropagation();
            vw_servicio_publico_aporte_suelo.addTo(map);
        }else{
            map.removeLayer(vw_servicio_publico_aporte_suelo);
        }
    });

    $("#chkCuneteo").click(function(ev){
        if(this.checked){
            ev.stopPropagation();
            vw_servicio_publico_cuneteo.addTo(map);
        }else{
            map.removeLayer(vw_servicio_publico_cuneteo);
        }
    });

    $("#chkDesbarre").click(function(ev){
        if(this.checked){
            ev.stopPropagation();
            vw_servicio_publico_desbarre_de_calle.addTo(map);
        }else{
            map.removeLayer(vw_servicio_publico_desbarre_de_calle);
        }
    });

    // capa alumbrado publico
    $("#chkAlumbrado").click(function(ev){
        if(this.checked){
            ev.stopPropagation();
            vw_visor_alumbrado_publico.addTo(map);
        }else{
            map.removeLayer(vw_visor_alumbrado_publico);
        }
    });

    // colocacion de tubos
    $("#chkTuboAccesoDomicilio").click(function(ev){
        if(this.checked){
            ev.stopPropagation();
            vw_visor_colocacion_tubo_acdom.addTo(map);
        }else{
            map.removeLayer(vw_visor_colocacion_tubo_acdom);
        }
    });

    // cruce de calles
    $("#chkTuboCruceCalle").click(function(ev){
        if(this.checked){
            ev.stopPropagation();
            vw_visor_colocacion_tubo_crucecalle.addTo(map);
        }else{
            map.removeLayer(vw_visor_colocacion_tubo_crucecalle);
        }
    });
});

