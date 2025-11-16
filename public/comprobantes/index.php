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
                <!-- Carga por AJAX (server-side) -->
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
  var baseURL = '<?php echo $URL; ?>';
</script>

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
      processing: true,
      serverSide: true,
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
        search: "Buscar en DB:",
        zeroRecords: "Sin resultados encontrados",
        paginate: { first:"Primero", last:"Último", next:"Siguiente", previous:"Anterior" }
      },
      responsive: true,
      lengthChange: true,
      autoWidth: false,
      ajax: {
        url: baseURL + '/app/controllers/comprobantes/listado_comprobantes_ajax.php',
        type: 'POST',
        data: function(d) {
          d.desde = '<?php echo htmlspecialchars($desde, ENT_QUOTES, 'UTF-8'); ?>';
          d.hasta = '<?php echo htmlspecialchars($hasta, ENT_QUOTES, 'UTF-8'); ?>';
        }
      },
      columns: [
        { data: 'fecha' },
        { data: 'tipo' },
        { data: 'nro' },
        { data: 'cliente' },
        { data: 'descripcion' },
        { data: 'debe', className: 'text-right' },
        { data: 'haber', className: 'text-right' },
        { data: 'acciones', orderable: false, searchable: false }
      ],
      buttons: [
        { extend:'collection', text:'Reportes', orientation:'landscape', buttons:['copy','pdf','csv','excel','print'] },
        { extend:'colvis', text:'Filtro de columnas' }
      ],
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
  });
</script>
