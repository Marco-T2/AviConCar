<?php
declare(strict_types=1);

include('../app/config.php');
include('../layout/sesion.php');
include('../layout/parte1.php');
include('../app/controllers/personas/listado_personas.php');

if (!isset($_SESSION)) session_start();
if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));

if (!function_exists('h')) {
  function h($v) { return htmlspecialchars((string)($v ?? ''), ENT_QUOTES, 'UTF-8'); }
}
?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Main content -->
  <div class="content">
    <div class="container-fluid">

      <div class="row">
        <div class="col-md-12">
          <div class="card card-primary collapsed-card">
            <div class="card-header">
              <h3 class="card-title">LISTA DE PERSONAS</h3>
              <div class="card-tools">
                <a href="create.php" class="btn btn-primary btn-sm">
                  <i class="fa fa-plus"></i> Nueva persona
                </a>
              </div>
            </div>

            <div class="card-body" style="display:block;">
              <table id="example1" class="table table-bordered table-striped table-sm" style="font-size:0.85rem;vertical-align:middle;">
                <thead>
                  <tr>
                    <th style="width:2%;"><i class="fa fa-list-ol" aria-hidden="true"></i></th>
                    <th style="width:10%;">Nombre</th>
                    <th style="width:5%;">Tipo persona</th>
                    <th style="width:10%;">Direccion</th>
                    <th style="width:4%;">Celular</th>
                    <th style="width:8%;">Descripcion</th>
                    <th style="width:2%;text-align:center;vertical-align:middle;"><i class="fa fa-filter" aria-hidden="true"></i></th>
                  </tr>
                </thead>
                <tbody>
                <?php
                $contador = 0;
                foreach ($personas_datos as $p):
                  $id_persona = (int)$p['id_persona'];
                ?>
                  <tr>
                    <td><?= ++$contador ?></td>
                    <td><?= h($p['name_persona']) ?></td>
                    <td><?= h($p['name_tipoPersona']) ?></td>
                    <td><?= h($p['direccion']) ?></td>
                    <td><?= h($p['celular']) ?></td>
                    <td><?= h($p['descripcion']) ?></td>
                    <td class="text-center">
                      <div class="btn-group">
                        <!-- Editar (sin modal) -->
                        <a href="update.php?id=<?= $id_persona ?>" class="btn btn-success btn-sm">
                          <i class="fa fa-pencil-alt"></i>
                        </a>

                        <!-- Eliminar por POST seguro (CSRF) -->
                        <form action="<?= $URL ?>app/controllers/personas/delete.php" method="post" class="d-inline">
                          <input type="hidden" name="id" value="<?= $id_persona ?>">
                          <input type="hidden" name="csrf" value="<?= $_SESSION['csrf'] ?>">
                          <button type="submit" class="btn btn-danger btn-sm btn-del"
                                  data-nombre="<?= h($p['name_persona']) ?>">
                            <i class="fa fa-trash"></i>
                          </button>
                        </form>
                      </div>
                    </td>
                  </tr>
                <?php endforeach; ?>
                </tbody>
              </table>
            </div>

          </div>
        </div>
      </div>

    </div><!-- /.container-fluid -->
  </div>
  <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<?php
include('../layout/parte2.php');
include('../layout/mensajes.php');
?>

<!-- Confirmación de borrado -->
<script>
document.addEventListener('click', function(e){
  const btn = e.target.closest('.btn-del');
  if (!btn) return;
  e.preventDefault();
  const form = btn.closest('form');
  const nombre = btn.dataset.nombre || 'esta persona';
  if (typeof Swal !== 'undefined') {
    Swal.fire({
      title: '¿Eliminar?',
      text: `Vas a eliminar "${nombre}". Esta acción no se puede revertir.`,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Sí, borrar',
      cancelButtonText: 'Cancelar'
    }).then(r => { if (r.isConfirmed) form.submit(); });
  } else {
    if (confirm(`¿Eliminar "${nombre}"?`)) form.submit();
  }
});
</script>

<!-- DataTables -->
<script>
$(document).ready(function() {
  var table = $('#example1').DataTable({
    pageLength: 5,
    language: {
      emptyTable: "No hay información",
      info: "Mostrando _START_ a _END_ de _TOTAL_ entradas",
      infoEmpty: "Mostrando 0 a 0 de 0 entradas",
      infoFiltered: "(filtrado de _MAX_ total entradas)",
      lengthMenu: "Mostrar _MENU_ entradas",
      loadingRecords: "Cargando...",
      processing: "Procesando...",
      search: "Buscar:",
      zeroRecords: "No se encontraron registros coincidentes",
      paginate: { first:"Primero", last:"Último", next:"Siguiente", previous:"Anterior" }
    },
    responsive: true,
    lengthChange: true,
    autoWidth: false,
    columnDefs: [
      { targets:[1], searchable:true },   // Nombre
      { targets:"_all", searchable:false }
    ]
  });

  // Búsqueda con umbral 3 caracteres
  $('#example1_filter input').off('keyup').on('keyup', function(){
    var searchTerm = this.value;
    if (searchTerm.length >= 3 || searchTerm.length === 0) {
      table.search(searchTerm).draw();
    }
  });
});
</script>
