<?php
include('../app/config.php');
include('../layout/sesion.php');
include('../layout/parte1.php');

// --------- Filtro de fechas (por defecto: últimas 2 semanas) ----------
$hoy = new DateTime('today');
$default_hasta = $hoy->format('Y-m-d');
$default_desde = (clone $hoy)->modify('-14 days')->format('Y-m-d');

$desde = (isset($_GET['desde']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $_GET['desde'])) ? $_GET['desde'] : $default_desde;
$hasta = (isset($_GET['hasta']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $_GET['hasta'])) ? $_GET['hasta'] : $default_hasta;

// Corrige si vienen invertidas
if ($desde > $hasta) { $tmp = $desde; $desde = $hasta; $hasta = $tmp; }

// Pasa el rango al controlador
$_FILTRO_DESDE = $desde;
$_FILTRO_HASTA = $hasta;

include('../app/controllers/comprobantes/listado_comprobantes.php');
?>

<div class="content-wrapper">
  <!-- Content Header -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-md-8">
          <a href="create.php" class="btn btn-primary btn-sm">
            <i class="fa fa-plus"></i> Nuevo comprobante
          </a>
          <a href="<?php echo $URL; ?>/kardex" class="btn btn-success btn-sm mb-2 mb-md-0">
            Ir a kardex
          </a>
        </div>
        <div class="col-md-4">
          <!-- Filtro fechas -->
          <form class="form-inline float-md-right" method="get" action="index.php">
            <label class="mr-1 mb-2">Desde</label>
            <input type="date" name="desde" class="form-control form-control-sm mr-2 mb-2"
                   value="<?php echo htmlspecialchars($desde, ENT_QUOTES, 'UTF-8'); ?>">
            <label class="mr-1 mb-2">Hasta</label>
            <input type="date" name="hasta" class="form-control form-control-sm mr-2 mb-2"
                   value="<?php echo htmlspecialchars($hasta, ENT_QUOTES, 'UTF-8'); ?>">
            <button type="submit" class="btn btn-info btn-sm mb-2" title="Aplicar filtro">
              <i class="fa fa-filter"></i>
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>
  <!-- /.content-header -->

  <!-- Main content -->
  <div class="content">
    <div class="container-fluid">
      <div class="row"><div class="col-md-12">
        <div class="card card-primary collapsed-card">
          <div class="card-header">
            <h3 class="card-title card-title-sm" style="font-size:0.85rem">
              REGISTRO COMPROBANTES
              <small class="ml-2">[<?php echo htmlspecialchars($desde); ?> → <?php echo htmlspecialchars($hasta); ?>]</small>
            </h3>
          </div>
          <div class="card-body" style="display:block;">
            <?php if (isset($fallback_aplicado) && $fallback_aplicado): ?>
              <div class="alert alert-info py-1 my-2">
                Sin resultados en el rango. Mostrando últimos 15 comprobantes.
              </div>
            <?php endif; ?>
            <table id="example1" class="table table-bordered table-striped table-sm" style="font-size:0.85rem; vertical-align: middle;">
              <thead>
                <tr>
                  <th style="width: 8%;">Fecha</th>
                  <th style="width: 12%;">Tipo</th>
                  <th style="width: 6%;">Nro</th>
                  <th style="width: 15%;">Cliente</th>
                  <th style="width: 25%;">Descripción</th>
                  <th style="width: 6%; text-align:right;">Debe</th>
                  <th style="width: 6%; text-align:right;">Haber</th>
                  <th style="width: 2%; text-align:center;"><i class="fa fa-filter"></i></th>
                </tr>
              </thead>
              <tbody>
                <?php if (!empty($comprobantes_datos)): ?>
                  <?php foreach ($comprobantes_datos as $r): ?>
                    <tr>
                      <td><?php echo date('d/m/Y', strtotime($r['fecha_comprobante'])); ?></td>
                      <td><?php echo htmlspecialchars($r['name_tipocomprobante'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                      <td><?php echo (int)($r['num_comprobante'] ?? 0); ?></td>
                      <td><?php echo htmlspecialchars($r['name_persona'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                      <td><?php echo htmlspecialchars($r['descripcion'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                      <td style="text-align:right;"><?php echo number_format((float)($r['debe'] ?? 0), 2); ?></td>
                      <td style="text-align:right;"><?php echo number_format((float)($r['haber'] ?? 0), 2); ?></td>
                      <td>
                        <center>
                          <div class="btn-group">
                            <a href="print.php?id=<?php echo (int)$r['id_comprobante']; ?>" class="btn btn-warning btn-sm" title="Imprimir">
                              <i class="fas fa-print fa-sm"></i>
                            </a>
                            <a href="update.php?id=<?php echo (int)$r['id_comprobante']; ?>" class="btn btn-success btn-sm" title="Editar">
                              <i class="fa fa-pencil-alt fa-sm"></i>
                            </a>
                            <a href="#" onclick="confirmDelete(<?php echo (int)$r['id_comprobante']; ?>);" class="btn btn-danger btn-sm" title="Eliminar">
                              <i class="fa fa-trash fa-sm"></i>
                            </a>
                          </div>
                        </center>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div></div>
    </div>
  </div>
</div>

<script>
  if (sessionStorage.getItem('mensaje') && sessionStorage.getItem('icono')) {
    Swal.fire({icon:sessionStorage.getItem('icono'), title:sessionStorage.getItem('mensaje'), showConfirmButton:false, timer:1500});
    sessionStorage.removeItem('mensaje'); sessionStorage.removeItem('icono');
  }
  function confirmDelete(id){
    Swal.fire({
      title:'¿Estás seguro?',
      text:'No podrás revertir esto',
      icon:'warning',
      showCancelButton:true,
      confirmButtonColor:'#3085d6',
      cancelButtonColor:'#d33',
      confirmButtonText:'Sí, bórralo'
    }).then((result)=>{ if(result.isConfirmed){ window.location.href="../app/controllers/comprobantes/delete.php?id="+id; }});
  }
</script>

<?php
include('../layout/parte2.php');
include('../layout/mensajes.php');
?>

<script>
$(function () {
  var table = $("#example1").DataTable({
    pageLength: 15,
    order: [],
    language: {
      emptyTable: "No hay información",
      info: "Mostrando _START_ a _END_ de _TOTAL_ Comprobantes",
      infoEmpty: "Mostrando 0 a 0 de 0 Comprobantes",
      infoFiltered: "(Filtrado de _MAX_ total Comprobantes)",
      lengthMenu: "Mostrar _MENU_ Comprobantes",
      loadingRecords: "Cargando...",
      processing: "Procesando...",
      search: "Buscador:",
      zeroRecords: "Sin resultados encontrados",
      paginate: { first:"Primero", last:"Último", next:"Siguiente", previous:"Anterior" }
    },
    responsive: true,
    lengthChange: true,
    autoWidth: false,
    buttons: [
      { extend:'collection', text:'Reportes', orientation:'landscape', buttons:['copy','pdf','csv','excel','print'] },
      { extend:'colvis', text:'Filtro de columnas' }
    ],
  }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
});
</script>
