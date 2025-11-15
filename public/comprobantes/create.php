<?php
include('../app/config.php');
include('../layout/sesion.php');
include('../layout/parte1.php');

include('../app/controllers/tipocomprobantes/listado_tipocomprobantes.php');
include('../app/controllers/personas/listado_personas.php');
include('../app/controllers/subCuentas/listado_subCuentas.php');
?>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/mathjs/9.5.0/math.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<div class="content-wrapper">
  <div class="content">
    <div class="container-fluid">
      <div class="row"><div class="col-md-12">
        <div class="card card-outline card-success">
          <div class="card-header">
            <h3 class="card-title card-title-sm" style="font-size:0.85rem">NUEVO COMPROBANTE</h3>
          </div>
          <div class="card-body" style="display:block;">
            <div class="row"><div class="col-md-12">
              <form action="#" method="post" onsubmit="return false;">
                <div class="row mb-2">
                  <div class="col-md-3">
                    <label class="form-label text-sm">Tipo comprobante</label>
                    <select id="id_tipocomprobante" class="form-control form-control-sm" name="id_tipocomprobante">
                      <option value="" disabled selected>Seleccionar</option>
                      <?php foreach ($tipocomprobantesComprobantes_datos as $t): ?>
                        <option value="<?php echo $t['id_tipocomprobante']; ?>"><?php echo $t['name_tipocomprobante']; ?></option>
                      <?php endforeach; ?>
                    </select>
                  </div>

                  <div class="col-md-3">
                    <label class="form-label text-sm">Nro Comprobante</label>
                    <input type="number" id="num_comprobante" name="num_comprobante" class="form-control form-control-sm" placeholder="#" required>
                  </div>

                  <script>
                  $(function(){
                    $('#id_tipocomprobante').change(function(){
                      const idTipocomprobante = parseInt($(this).val()||'0',10);
                      if ([1,2,4,5,8,9,10].includes(idTipocomprobante)) {
                        $.get('../app/controllers/comprobantes/numero_comprobantes.php',{id_tipocomprobante:idTipocomprobante})
                          .done(n => $('#num_comprobante').val(n||''))
                          .fail((x,s,e)=>console.error('Error num:',s,e));
                      } else { $('#num_comprobante').val(''); }
                    });
                  });
                  </script>

                  <div class="col-md-3">
                    <label class="form-label text-sm">Fecha</label>
                    <input type="date" id="fecha_comprobante" name="fecha_comprobante" class="form-control form-control-sm" value="<?php echo $fecha; ?>" required>
                  </div>
                  <div class="col-md-3">
                    <label class="form-label text-sm">Hora</label>
                    <input type="time" id="hora_comprobante" name="hora_comprobante" class="form-control form-control-sm" value="<?php echo $hora; ?>" required>
                  </div>
                </div>

                <div class="form-group">
                  <label class="form-label text-sm">Descripción</label>
                  <input type="text" id="descripcion" name="descripcion" class="form-control form-control-sm" placeholder="Poner una descripción" required>
                </div>

                <div class="form-group" hidden>
                  <input type="text" id="id_usuario" name="id_usuario" class="form-control" value="<?php echo $id_usuario; ?>">
                </div>

                <hr>

                <div class="table-responsive">
                  <table id="invoice_item_table" class="table table-bordered table-striped table-sm" style="font-size:.85rem;vertical-align:middle;">
                    <thead>
                      <tr>
                        <th style="width:2%;"><i class="fa fa-list-ol"></i></th>
                        <th style="width:5%;">Cuenta</th>
                        <th style="width:9%;">Debe</th>
                        <th style="width:9%;">Haber</th>
                        <th style="width:20%;">Descripción</th>
                        <th style="width:5%;">Persona</th>
                        <th style="width:2%;text-align:center;vertical-align:middle;"><i class="fa fa-filter"></i></th>
                      </tr>
                    </thead>
                    <tbody>
                      <!-- Filas dinámicas -->
                      <tr id="row_id_final">
                        <td colspan="2"></td>
                        <th style="text-align:right;" id="total_debe">0.00</th>
                        <th style="text-align:right;" id="total_haber">0.00</th>
                        <td colspan="3"></td>
                      </tr>
                    </tbody>
                  </table>
                  <div align="center">
                    <button type="button" name="add_row" id="add_row" class="btn btn-success btn-xs">+</button>
                  </div>
                </div>

                <br>

                <div class="modal-footer justify-content-between">
                  <button type="button" class="btn btn-success btn-sm" id="btn_crear_comprobante">Guardar</button>
                  <a href="index.php" class="btn btn-secondary btn-sm">Cancelar</a>
                </div>
              </form>

              <script>
              $(function () {
                let count = 0;

                function initializeSelect2() {
                  $('.js-select2').select2({ placeholder:'Seleccionar', width:'100%' });
                }

                function actualizarTotal() {
                  let totalDebe = 0, totalHaber = 0;
                  $('input[name="item_debe[]"]').each(function(){
                    const v = ($(this).val()||'').trim();
                    let n = 0; try{ n = v? math.evaluate(v):0; }catch(e){ n=0; }
                    totalDebe += Number(n)||0;
                  });
                  $('input[name="item_haber[]"]').each(function(){
                    const v = ($(this).val()||'').trim();
                    let n = 0; try{ n = v? math.evaluate(v):0; }catch(e){ n=0; }
                    totalHaber += Number(n)||0;
                  });
                  $('#total_debe').text(totalDebe.toFixed(2));
                  $('#total_haber').text(totalHaber.toFixed(2));
                }

                    // 1) Mutua exclusión (DEJAR tal cual)
                    $(document).on('input','input[name="item_debe[]"]', function(){
                    $(this).closest('tr').find('input[name="item_haber[]"]').val('');
                    actualizarTotal();
                    });
                    $(document).on('input','input[name="item_haber[]"]', function(){
                    $(this).closest('tr').find('input[name="item_debe[]"]').val('');
                    actualizarTotal();
                    });

                    // 2) Normalizar al salir del input (AGREGAR)
                    $(document).on('blur','input[name="item_debe[]"], input[name="item_haber[]"]', function(){
                    const txt = ($(this).val()||'').trim();
                    let n = 0; try { n = txt ? math.evaluate(txt) : 0; } catch(e) { n = 0; }
                    $(this).val(Number(n).toFixed(2));
                    actualizarTotal();
                    });


                // Normaliza celdas antes de enviar (lo que ves es lo que viaja)
                function normalizaCeldas(){
                  $('#invoice_item_table tbody tr.data-row').each(function(){
                    ['item_debe[]','item_haber[]'].forEach(name=>{
                      const $i = $(this).find(`input[name="${name}"]`);
                      const txt = ($i.val()||'').trim();
                      let n = 0; try{ n = txt? math.evaluate(txt):0; }catch(e){ n=0; }
                      $i.val(Number(n).toFixed(2));
                    });
                  });
                  // refresca totales
                  let d=0,h=0;
                  $('input[name="item_debe[]"]').each((_,i)=> d += parseFloat(i.value||0));
                  $('input[name="item_haber[]"]').each((_,i)=> h += parseFloat(i.value||0));
                  $('#total_debe').text(d.toFixed(2));
                  $('#total_haber').text(h.toFixed(2));
                }

                function addRow() {
                  count += 1;
                  const html = `
                  <tr class="data-row" id="row_id_${count}">
                    <td style="vertical-align:middle;"><span class="sr_no">${count}</span></td>
                    <td>
                      <select class="js-select2 form-control form-control-sm" name="item_subcuentas[]" style="width:100%;">
                        <option value="" disabled selected>Seleccionar</option>
                        <?php foreach ($subcuentasActivoCorriente_datos as $s): ?>
                          <option value="<?php echo $s['id_subCuenta']; ?>"><?php echo $s['name_subCuenta']; ?></option>
                        <?php endforeach; ?>
                      </select>
                      <small style="color:red;display:none;" class="lbl_subcuentas">*Requerido</small>
                    </td>
                    <td><input style="text-align:right;" type="text" name="item_debe[]"  class="form-control form-control-sm number_only orden_item_debe"  placeholder="0.00"></td>
                    <td><input style="text-align:right;" type="text" name="item_haber[]" class="form-control form-control-sm number_only orden_item_haber" placeholder="0.00"></td>
                    <td><input type="text" name="item_descripcion[]" class="form-control form-control-sm input-sm" placeholder=""></td>
                    <td>
                      <select class="js-select2 form-control form-control-sm" name="item_persona[]" style="width:100%;">
                        <option value="" disabled selected>Seleccionar</option>
                        <?php foreach ($personas_datos as $p): ?>
                          <option value="<?php echo $p['id_persona']; ?>"><?php echo $p['name_persona']; ?></option>
                        <?php endforeach; ?>
                      </select>
                      <small style="color:red;display:none;" class="lbl_persona">*Requerido</small>
                    </td>
                    <td style="vertical-align:middle;"><button type="button" name="remove_row" class="btn btn-danger btn-xs remove_row">-</button></td>
                  </tr>`;
                  $(html).insertBefore('#row_id_final');
                  initializeSelect2();
                }

                // Botón +
                $('#add_row').on('click', function(){ addRow(); actualizarTotal(); });

                // Enter/Tab evalúa
                $(document).on('keydown','input[name^="item_debe"], input[name^="item_haber"]', function(e){
                  if (e.key==='Enter' || e.key==='Tab'){
                    let txt = $(this).val(); if (txt.trim()===''){ $(this).val('0.00'); actualizarTotal(); return; }
                    try{ const n = math.evaluate(txt); $(this).val(Number(n).toFixed(2)); }catch(_){ $(this).val('0.00'); }
                    actualizarTotal();
                  }
                });

                // Quitar fila
                $(document).on('click','.remove_row', function(){ $(this).closest('tr').remove(); actualizarTotal(); });

                // Guardar
                $('#btn_crear_comprobante').click(function(){
                  normalizaCeldas(); // 🔑 asegura que viajen números correctos

                  const totalDebe  = parseFloat($('#total_debe').text()) || 0;
                  const totalHaber = parseFloat($('#total_haber').text()) || 0;
                  const dataRows   = $('#invoice_item_table tbody tr.data-row').length;

                  if (dataRows < 2){
                    Swal.fire({icon:'error',title:'No hay suficientes registros',text:'Debe haber al menos dos líneas.'}); return;
                  }
                  if (totalDebe.toFixed(2) === '0.00' || totalHaber.toFixed(2) === '0.00'){
                    Swal.fire({icon:'error',title:'Montos inválidos',text:'El total del Debe y el Haber no pueden ser cero.'}); return;
                  }
                  if (totalDebe.toFixed(2) !== totalHaber.toFixed(2)){
                    Swal.fire({icon:'error',title:'Los montos no coinciden',text:'El total del Debe y el Haber debe ser igual.'}); return;
                  }

                  const id_tipocomprobante = $('#id_tipocomprobante').val();
                  const num_comprobante    = $('#num_comprobante').val();
                  const fecha_comprobante  = $('#fecha_comprobante').val();
                  const hora_comprobante   = $('#hora_comprobante').val();
                  const descripcion        = $('#descripcion').val();
                  const id_usuario         = $('#id_usuario').val();

                  const detalles_comprobante = [];
                  $('#invoice_item_table tbody tr.data-row').each(function(){
                    const id_subcuenta = $(this).find('select[name="item_subcuentas[]"]').val();
                    const debe         = $(this).find('input[name="item_debe[]"]').val();
                    const haber        = $(this).find('input[name="item_haber[]"]').val();
                    const descDet      = $(this).find('input[name="item_descripcion[]"]').val();
                    const id_persona   = $(this).find('select[name="item_persona[]"]').val();
                    if (!id_subcuenta && !id_persona && !debe && !haber) return;
                    detalles_comprobante.push({
                      id_subcuenta: id_subcuenta, // clave correcta para backend
                      debe: debe,
                      haber: haber,
                      descripcion: descDet,
                      id_persona: id_persona
                    });
                  });

                  const datos_comprobante = {
                    id_tipocomprobante, num_comprobante, fecha_comprobante,
                    hora_comprobante, descripcion, id_usuario, detalles_comprobante
                  };
                  axios.post("../app/controllers/comprobantes/create.php", datos_comprobante, {
                    headers:{ "Content-Type":"application/json" }
                  })
                  .then(res=>{
                    if (res.data && res.data.success){
                      Swal.fire({icon:'success',title:'¡Éxito!',text:'Comprobante creado exitosamente'})
                        .then(()=>{ window.location.href = "<?php echo $URL ?>/comprobantes"; });
                    } else {
                      const msg = (res.data && res.data.error) ? res.data.error : 'No se pudo crear el comprobante.';
                      Swal.fire({icon:'error',title:'Error',text: msg});
                    }
                  })
                  .catch(err=>{
                    console.error('Error de AJAX:', err);
                    Swal.fire({icon:'error',title:'Error',text:'Ocurrió un error al procesar la solicitud'});
                  });
                });

                // 2 filas por defecto
                addRow(); addRow(); actualizarTotal();
              });
              </script>

            </div></div>
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
</script>

<?php
include('../layout/mensajes.php');
include('../layout/parte2.php');
?>
