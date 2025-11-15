<?php
// C:\web\stack\ct\public\despachos\index.php
include('../app/config.php');
include('../layout/sesion.php');
include('../layout/parte1.php');

// --------- Filtro de fechas (por defecto: últimas 2 semanas) ----------
$hoy = new DateTime('today');
$default_hasta  = $hoy->format('Y-m-d');
$default_desde  = (clone $hoy)->modify('-14 days')->format('Y-m-d');

$desde = isset($_GET['desde']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $_GET['desde']) ? $_GET['desde'] : $default_desde;
$hasta = isset($_GET['hasta']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $_GET['hasta']) ? $_GET['hasta'] : $default_hasta;

// Corrige si vienen invertidas
if ($desde > $hasta) { $tmp = $desde; $desde = $hasta; $hasta = $tmp; }

// Variables para el controlador
$_FILTRO_DESDE = $desde;
$_FILTRO_HASTA = $hasta;

// Carga de datos con rango de fechas
include('../app/controllers/despachos/listado_despachos.php');
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-md-8">
          <a href="create.php" class="btn btn-primary btn-sm">
            <i class="fa fa-plus"></i> Nuevo despacho
          </a>
          <a href="<?php echo $URL; ?>/kardex" class="btn btn-success btn-sm mb-2 mb-md-0">
            Ir a kardex
          </a>
        </div>
        <div class="col-md-4">
          <!-- Filtro por fechas -->
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
              DESPACHO <small class="ml-2">[<?php echo htmlspecialchars($desde); ?> → <?php echo htmlspecialchars($hasta); ?>]</small>
            </h3>
          </div>
          <div class="card-body" style="display:block;">
            <?php if (!empty($fallback_aplicado)): ?>
              <div class="alert alert-info py-1 my-2">Sin resultados en el rango. Mostrando últimos 15 despachos.</div>
            <?php endif; ?>            
            <table id="example1" class="table table-bordered table-striped table-sm" style="font-size:0.85rem; vertical-align: middle;">
              <thead>
                <tr>
                  <th style="width:4%;text-align:center;">Fecha</th>
                  <th style="width:10%;text-align:center;">Tipo</th>
                  <th style="width:6%;text-align:center;">Nro</th>
                  <th style="width:8%;text-align:center;">Cliente</th>
                  <th style="width:25%;text-align:center;">Descripción</th>
                  <th style="width:8%;text-align:center;">Monto (Bs)</th>
                  <th style="width:5%; text-align:center;">
                    <i class="fa fa-filter" aria-hidden="true"></i>
                  </th>
                </tr>
              </thead>
              <tbody>
                <?php if (!empty($transacciones_datos)): ?>
                  <?php foreach ($transacciones_datos as $r): ?>
                    <tr>
                      <td style="text-align:center;"><?php echo date('d/m/Y', strtotime($r['fecha_comprobante'])); ?></td>
                      <td style="text-align:center;"><?php echo htmlspecialchars($r['name_tipocomprobante'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                      <td style="text-align:center;"><?php echo (int)($r['num_comprobante'] ?? 0); ?></td>
                      <td><?php echo htmlspecialchars($r['name_persona'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                      <td><?php echo htmlspecialchars($r['descripcion'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                      <td style="text-align:right;"><?php echo number_format((float)($r['subTotal'] ?? 0), 2); ?></td>
                      <td>
                        <center>
                          <div class="btn-group">
                            <button type="button" class="imprimir btn btn-warning btn-sm mb-2 mb-md-0"
                                    data-id-comprobante="<?php echo (int)$r['id_comprobante']; ?>" title="Imprimir">
                              <i class="fa fa-print fa-sm"></i>
                            </button>
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
  <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<script>
  // Notificaciones post acción (usa sessionStorage como en tu base)
  if (sessionStorage.getItem('mensaje') && sessionStorage.getItem('icono')) {
    Swal.fire({
      icon: sessionStorage.getItem('icono'),
      title: sessionStorage.getItem('mensaje'),
      showConfirmButton: false,
      timer: 1500
    });
    sessionStorage.removeItem('mensaje');
    sessionStorage.removeItem('icono');
  }
</script>

<?php
include('../layout/parte2.php');
include('../layout/mensajes.php');
?>

<script>
  var baseURL = '<?php echo $URL; ?>';

  $(function () {
    var table = $("#example1").DataTable({
      "pageLength": 15,
      "order": [],
      language: {
        "emptyTable": "No hay información",
        "decimal": "",
        "info": "Mostrando _START_ a _END_ de _TOTAL_ Despacho",
        "infoEmpty": "Mostrando 0 a 0 de 0 Despacho",
        "infoFiltered": "(Filtrado de _MAX_ total Despacho)",
        "infoPostFix": "",
        "thousands": ",",
        "lengthMenu": "Mostrar _MENU_ Despacho",
        "loadingRecords": "Cargando...",
        "processing": "Procesando...",
        "search": "Buscador:",
        "zeroRecords": "Sin resultados encontrados",
        "paginate": {
          "first": "Primero",
          "last": "Ultimo",
          "next": "Siguiente",
          "previous": "Anterior"
        }
      },
      "responsive": true,
      "lengthChange": true,
      "autoWidth": false,
      buttons: [
        {
          extend: 'collection',
          text: 'Reportes',
          orientation: 'landscape',
          buttons: ['copy', 'pdf', 'csv', 'excel', 'print']
        },
        {
          extend: 'colvis',
          text: 'Filtro de columnas'
        }
      ],
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');

    // Botón imprimir (usa data-id-comprobante)
    $(document).on('click', '.imprimir', function(){
      var id = $(this).data('id-comprobante');
      if (!id) return;
      window.open(baseURL + '/despachos/despachoprint.php?id_comprobante=' + id, '_blank');
    });
  });

  // Confirmación de borrado
  function confirmDelete(id){
    Swal.fire({
      title: '¿Estás seguro?',
      text: "No podrás revertir esto",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Sí, bórralo'
    }).then((result) => {
      if (result.isConfirmed) {
        window.location.href = "../app/controllers/despachos/delete.php?id=" + id;
      }
    });
  }
</script>
