<?php
  session_name('mapa_gis');
  session_start();

  require_once('conPDO1921681051.php');

  $datos = $_POST;
  $id_sp = $datos['id_sp'];
  $srv = $datos['srv'];

  $valret = '';

  // lee id_tramo_calle
  $qry_id_tramo_calle = 'select t1.id_tramo_calle
  from actualizar.vw_servicios_publicos_full1 t1 
  where id_sp = ' . $id_sp;

  $rst_id_tramo_calle = $conPdoPg->query($qry_id_tramo_calle);

  $reg_id_tramo_calle = $rst_id_tramo_calle->fetch(PDO::FETCH_OBJ);

  // lee registros de servicios del tramo seleccionado
  $qry_servicio = "select t2.*
  from actualizar.vw_servicios_publicos_full1 t2
  where ( (t2.id_tramo_calle = ' . $reg_id_tramo_calle->id_tramo_calle . ') and (fecha_servicio > '2019-12-31') and (actividad = '" . $datos['actividad'] . "') )
  order by t2.fecha_servicio DESC
  ;";

  $rst_servicio = $conPdoPg->query($qry_servicio);

  if($rst_servicio->rowCount() == 0){
                
    echo '<div class="alert alert-primary" style="width:450px;"><p class="h3 text-center">Sin actividad en 2020</p></div>';

    exit;

  }

  $valret ='
  <p class="h2 text-primary">Aporte de suelo</p>
  <p class="h5">' . $datos['calle'] . ' ' . $datos['altur_par'] . ' - ' . $datos['barrio'] . '</p>
  <div style="max-height: 450px; width: 450px; overflow: auto;">
    <table class="table table-striped" style="width:441px;">
      <thead class="thead-dark">
        <th scope="col">Fecha</th>
        <th scope="col">Actividad</th>
        <th scope="col">Calzada</th>
      </thead>
      <tbody>
    ';

    while($reg_servicio = $rst_servicio->fetch(PDO::FETCH_OBJ)){
      $valret .= '<tr>';
      $valret .= '  <th scope="row">';
      $valret .= substr($reg_servicio->fecha_servicio, 8, 2) . '/' . substr($reg_servicio->fecha_servicio, 5, 2) . '/' . substr($reg_servicio->fecha_servicio, 0, 4);
      $valret .= '  </th>';

      $valret .= '<td>';
      $valret .=  $reg_servicio->actividad;
      $valret .= '</td>';

      $valret .= '<td>';
      $valret .=  $reg_servicio->tipo_calzada;
      $valret .= '</td>';
      
      $valret .= '</tr>';
  }

  $valret .= '
    </tbody>
  </table>
  </div>';

  echo $valret;


  unset($id_sp, $id_sp, $qry_id_tramo_calle, $qry_servicio, $rst_id_tramo_calle, $rst_servicio, $reg_id_tramo_calle, $reg_servicio);


  exit;

?>
