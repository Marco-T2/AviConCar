<?php
include('../app/config.php');
include('../layout/sesion.php');
include('../layout/parte1.php');

// Administración de cajas (TipoCaja)
?>

<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-md-8">
          <h4 style="margin:0">Administración de cajas</h4>
          <small>Crear / editar tipos de caja y activar/inactivar</small>
        </div>
        <div class="col-md-4 text-right">
          <button id="btnNewTipoCaja" class="btn btn-primary">+ Nueva Tipo Caja</button>
        </div>
      </div>
    </div>
  </div>

  <div class="content">
    <div class="container-fluid">
      <div class="card">
        <div class="card-body">
          <table id="tblTipos" class="table table-striped table-bordered" style="width:100%">
            <thead>
              <tr>
                <th>ID</th>
                <th>Código</th>
                <th>Descripción</th>
                <th>Color</th>
                <th>Activo</th>
                <th style="width:140px">Acciones</th>
              </tr>
            </thead>
            <tbody>
              <?php
              // Cargar tipos desde la base
              try {
                $sql = "SELECT id, codigo, descripcion, color_referencia, activo FROM tipo_caja ORDER BY id";
                $st = $pdo->prepare($sql);
                $st->execute();
                $rows = $st->fetchAll(PDO::FETCH_ASSOC);
              } catch (PDOException $e) {
                $rows = [];
              }
              foreach ($rows as $r) {
                $id = (int)$r['id'];
                $cod = htmlspecialchars($r['codigo']);
                $desc = htmlspecialchars($r['descripcion']);
                $color = htmlspecialchars($r['color_referencia']);
                $activo = $r['activo'] ? 'Sí' : 'No';
                echo "<tr data-id=\"{$id}\">\n";
                echo "<td>{$id}</td>";
                echo "<td>{$cod}</td>";
                echo "<td>{$desc}</td>";
                echo "<td style=\"background:{$color};width:60px;\">{$color}</td>";
                echo "<td>{$activo}</td>";
                echo "<td class=\"actions-col\"><button class=\"btn btn-sm btn-secondary btn-edit\">Editar</button> <button class=\"btn btn-sm btn-danger btn-toggle\">Activar/Desactivar</button></td>\n";
                echo "</tr>\n";
              }
              ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal (crear / editar) -->
<div id="modalTipo" class="modal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form id="formTipo">
        <div class="modal-header">
          <h5 class="modal-title">Tipo Caja</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">&times;</button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="id" id="tipo_id">
          <div class="form-group">
            <label>Código</label>
            <input class="form-control" name="codigo" id="tipo_codigo" required>
          </div>
          <div class="form-group">
            <label>Descripción</label>
            <input class="form-control" name="descripcion" id="tipo_descripcion">
          </div>
          <div class="form-group">
            <label>Color referencia</label>
            <div style="display:flex;gap:.5rem;align-items:center;">
              <input type="color" class="form-control" id="tipo_color_picker" value="#cfe8ff" style="width:48px;padding:0;border:none;background:transparent;">
              <input class="form-control" name="color_referencia" id="tipo_color" placeholder="#cfe8ff" style="width:120px;">
            </div>
          </div>
          <div class="form-group">
            <label><input type="checkbox" name="activo" id="tipo_activo" checked> Activo</label>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Guardar</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php
include('../layout/parte2.php');
include('../layout/mensajes.php');
?>

<style>
  /* make actions column a bit narrower */
  #tblTipos .actions-col { width:140px; white-space:nowrap; }
  #tblTipos th:last-child { width:140px; }
</style>

<script>
$(function(){
  $('#tblTipos').DataTable();

  function openModal(data){
    $('#tipo_id').val(data.id||'');
    $('#tipo_codigo').val(data.codigo||'');
    $('#tipo_descripcion').val(data.descripcion||'');
    $('#tipo_color').val(data.color_referencia||'');
    $('#tipo_color_picker').val(data.color_referencia && data.color_referencia.startsWith('#') ? data.color_referencia : (data.color_referencia ? '#'+data.color_referencia : '#cfe8ff'));
    $('#tipo_activo').prop('checked', data.activo==1 || data.activo===true);
    $('#modalTipo').modal('show');
  }

  $('#btnNewTipoCaja').on('click', function(){ openModal({}); });

  $('#tblTipos').on('click', '.btn-edit', function(){
    const tr = $(this).closest('tr');
    const id = tr.data('id');
    // cargar via ajax simple
    $.getJSON('<?php echo $URL;?>/app/controllers/cajas/get_tipo_caja.php', {id:id}, function(resp){
      if(resp.success) openModal(resp.data);
      else Swal.fire('Error', 'No se pudo cargar', 'error');
    });
  });

  $('#tblTipos').on('click', '.btn-toggle', function(){
    const tr = $(this).closest('tr');
    const id = tr.data('id');
    $.post('<?php echo $URL;?>/app/controllers/cajas/toggle_tipo_caja.php', {id:id}, function(r){
      location.reload();
    });
  });

  $('#formTipo').on('submit', function(e){
    e.preventDefault();
    const d = $(this).serialize();
    $.post('<?php echo $URL;?>/app/controllers/cajas/save_tipo_caja.php', d, function(r){
      location.reload();
    }, 'json');
  });

  // sync color picker and text input
  $('#tipo_color_picker').on('input change', function(){
    $('#tipo_color').val($(this).val());
  });
  $('#tipo_color').on('input change', function(){
    const v = $(this).val();
    // try normalize hex
    if (/^#?[0-9a-fA-F]{6}$/.test(v)) {
      $('#tipo_color_picker').val(v.startsWith('#')?v:'#'+v);
    }
  });
});
</script>

