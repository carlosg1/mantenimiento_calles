$(window).on('load', function () {
    var lang = "es-AR";
    anio = new Date();
    var year = anio.getFullYear();

    var ini_desde = new Date(year, 0, 1);
    ini_desde = ini_desde.toISOString().slice(0, 10);

    var ini_hasta = new Date(year, 4, 1);
    ini_hasta = ini_hasta.toISOString().slice(0, 10);

    $('#confirmar').click(function () {
        cadena(); //Esta funcion devuelve por consola la variable cadena final
    });

    //Inicializo la variable cadena final con un valor por defecto
    var cadena_final = `fecha_servicio BETWEEN '${ini_desde}' AND '${ini_hasta}'`;

    function dateToTS(date) {
        return date.valueOf();
    }

    function tsToDate(ts) {
        var d = new Date(ts);

        return d.toLocaleDateString(lang, {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
    }

    var fechaSlider = $(".js-range-slider").ionRangeSlider({
        skin: "big",
        type: "double",
        grid: true,
        min: dateToTS(new Date(year, 0, 1)), //Valor minimo 1 de enero del año actual
        max: dateToTS(new Date()), //fecha actual
        from: dateToTS(new Date(year, 0, 1)), //Desde 1 de enero del año actual
        to: dateToTS(new Date(year, 4, 1)), //Hasta 1 de mayo del año actual
        prettify: tsToDate,
        onChange: armarCadena
    });

    function armarCadena(data) {
        var desde;
        var hasta;
        let mesdesde;
        let meshasta;

        d = data.from_pretty;
        d = d.split('de');

        for (let i = 0; i < d.length; i++) {
            d[i] = d[i].trim();
        }

        h = data.to_pretty;
        h = h.split('de');

        for (let i = 0; i < h.length; i++) {
            h[i] = h[i].trim();
        }

        mesdesde = calcularMes(d);
        meshasta = calcularMes(h);

        desde = d[2] + '-' + mesdesde + '-' + d[0];
        hasta = h[2] + '-' + meshasta + '-' + h[0];

        desde = new Date(desde);
        desde = desde.toISOString().slice(0, 10);
        hasta = new Date(hasta);
        hasta = hasta.toISOString().slice(0, 10);

        cadena_final = `fecha_servicio BETWEEN '${desde}' AND '${hasta}'`;
    }

    function cadena() {
        vw_servicio_publico_perfilado.remove();

        vw_servicio_publico_perfilado = new wms_GIS("http://192.168.10.51:8282/geoserver/wms?", {
            format: 'image/png',
            uppercase: true,
            transparent: true,
            version: '1.3.0',
            continuousWorld : true,
            tiled: true,
            attribution: "Direccion Gral de GIS",
            info_format: 'application/json',
            opacity: 1,
            cql_filter: cadena_final
        }).getLayer("servicio_publico_20:vw_servicio_publico_perfilado").addTo(map);
    }

    function calcularMes(m) {
        var mes
        switch (m[1]) {
            case 'enero':
                mes = '01'
                break;
            case 'febrero':
                mes = '02'
                break;
            case 'marzo':
                mes = '03'
                break;
            case 'abril':
                mes = '04'
                break;
            case 'mayo':
                mes = '05'
                break;
            case 'junio':
                mes = '06'
                break;
            case 'julio':
                mes = '07'
                break;
            case 'agosto':
                mes = '08'
                break;
            case 'septiembre':
                mes = '09'
                break;
            case 'octubre':
                mes = '10'
                break;
            case 'noviembre':
                mes = '11'
                break;
            case 'diciembre':
                mes = '12'
                break;
            default:
                break;
        }

        return mes;
    }
});