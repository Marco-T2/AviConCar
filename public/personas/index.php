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
                <button id="bulk-delete-btn" class="btn btn-danger btn-sm" style="margin-left:8px;">
                  <i class="fa fa-trash"></i> Eliminar seleccionados
                </button>
              </div>
            </div>

            <div class="card-body" style="display:block;">
              <table id="example1" class="table table-bordered table-striped table-sm" style="font-size:0.85rem;vertical-align:middle;">
                <thead>
                  <tr>
                    <th style="width:3%;text-align:center"><input type="checkbox" id="select-all" style="margin:0"></th>
                    <th style="width:4%">#</th>
                    <th style="width:28%">Nombre</th>
                    <th style="width:18%">Tipos</th>
                    <th style="width:16%">Tags</th>
                    <th style="width:20%">Informacion</th>
                    <th style="width:3%;text-align:center;vertical-align:middle;"><i class="fa fa-filter" aria-hidden="true"></i></th>
                  </tr>
                </thead>
                <tbody>
                <?php
                $contador = 0;
                foreach ($personas_datos as $p):
                  $id_persona = (int)$p['id_persona'];
                ?>
                  <tr>
                    <td class="text-center"><input type="checkbox" class="row-select" name="ids[]" value="<?= $id_persona ?>" style="margin:0"></td>
                    <td><?= ++$contador ?></td>
                    <td>
                      <?php $hasMov = isset($p['trans_count']) && (int)$p['trans_count'] > 0; ?>
                      <div style="font-weight:700;color:<?= $hasMov ? '#28a745' : '#6c757d' ?>;display:flex;align-items:center;">
                        <span><?= h($p['name_persona']) ?></span>
                        <small style="margin-left:8px;font-size:0.8rem;color:inherit;">(<?= $hasMov ? 'Movimientos' : 'Sin movimientos' ?>)</small>
                      </div>
                      <div style="font-size:.85rem;color:#666"><?= h($p['descripcion']) ?></div>
                    </td>
                    <td>
                      <?php
                        $primary = $p['name_tipoPersona'] ?? null;
                        $extra = $p['tipos_extra'] ?? null;
                        if($primary) echo '<span class="badge badge-primary mr-1">'.h($primary).'</span>';
                        if($extra) {
                          foreach(array_filter(array_map('trim', explode(',', $extra))) as $ex){ echo '<span class="badge badge-secondary mr-1">'.h($ex).'</span>'; }
                        }
                      ?>
                    </td>
                    <td>
                      <?php
                        $tags = $p['tags'] ?? '';
                        if($tags){
                          foreach(array_filter(array_map('trim', explode(',', $tags))) as $tg){ echo '<span class="badge badge-info mr-1">'.h($tg).'</span>'; }
                        }
                      ?>
                      <!-- tags shown above; checkbox removed (use tipos for 'cajas') -->
                    </td>
                    <td>
                      <div><?= h($p['direccion']) ?></div>
                      <div style="font-size:.85rem;color:#666"><?= h($p['celular']) ?></div>
                    </td>
                    <td class="text-center">
                      <div class="btn-group">
                        <!-- Editar (sin modal) -->
                        <a href="update.php?id=<?= $id_persona ?>" class="btn btn-success btn-sm">
                          <i class="fa fa-pencil-alt"></i>
                        </a>

                        <!-- Eliminar individual -->
                        <form action="../app/controllers/personas/delete.php" method="post" class="d-inline">
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

<!-- Bulk delete handler -->
<form id="bulk-delete-form" action="../app/controllers/personas/delete_multiple.php" method="post" style="display:none;">
  <input type="hidden" name="csrf" value="<?= $_SESSION['csrf'] ?>">
</form>
<script>
document.getElementById('select-all').addEventListener('change', function(){
  const checked = this.checked;
  document.querySelectorAll('.row-select').forEach(cb => cb.checked = checked);
});

document.getElementById('bulk-delete-btn').addEventListener('click', function(e){
  e.preventDefault();
  const selected = Array.from(document.querySelectorAll('.row-select:checked')).map(cb => cb.value);
  if (selected.length === 0) { alert('Selecciona al menos una persona.'); return; }
  const confirmDelete = (typeof Swal !== 'undefined') ? Swal.fire({
    title: 'Eliminar seleccionados?', text: `Vas a eliminar ${selected.length} personas.`, icon:'warning', showCancelButton:true, confirmButtonText:'Sí, borrar'
  }).then(r => r.isConfirmed) : Promise.resolve(confirm(`Vas a eliminar ${selected.length} personas. Continuar?`));
  Promise.resolve(confirmDelete).then(ok => { if (!ok) return; 
    const form = document.getElementById('bulk-delete-form');
    // append ids
    selected.forEach(id => {
      const inp = document.createElement('input'); inp.type='hidden'; inp.name='ids[]'; inp.value=id; form.appendChild(inp);
    });
    form.submit();
  });
});
</script>

<!-- tag checkbox removed: tags are editable in persona edit form -->

<!-- DataTables -->
<script>
$(document).ready(function() {
  var table = $('#example1').DataTable({
    pageLength: 10,
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
      { targets:[2], searchable:true },   // Nombre
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
