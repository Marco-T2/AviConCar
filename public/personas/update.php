<?php
include('../app/config.php');
include('../layout/sesion.php');
include('../layout/parte1.php');

include('../app/controllers/tipopersonas/listado_tipopersonas.php');
include('../app/controllers/personas/update_persona.php');


?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Main content -->
    <div class="content">
        <div class="container-fluid">

            <style>
            .tipo-table .form-check-label { color: #6b6b6b; }
            .tipo-table input.form-check-input:checked + .form-check-label { color: #111; font-weight:600; }
            .tipo-table td { padding: .45rem .75rem; }
            fieldset.border { border:1px solid #e3e3e3; border-radius:4px; }
            fieldset legend { font-weight:700; padding:0 8px; width:auto; }
            </style>

            <div class="row">
                <div class="col-md-12">
                    <div class="card card-outline card-success">
                        <div class="card-header">
                            <h3 class="card-title">EDITAR PERSONA</h3>
                            <div class="card-tools">
                            </div>
                        </div>
                        <div class="card-body" style="display: block;">
                            <div class="row">
                                <div class="col-md-12">
                                    <form action="../app/controllers/personas/update.php" method="post">
                                        <input type="text" name="id_persona" value="<?php echo $id_persona; ?>" hidden>

                                        <fieldset class="border p-2 mb-3">
                                            <legend class="w-auto">DATOS PERSONALES</legend>
                                            <div class="form-row">
                                                <div class="form-group col-md-6">
                                                    <label for="">Nombre completo</label>
                                                    <input type="text" name="name_persona" class="form-control" placeholder="Nombre completo" value="<?php echo $name_persona; ?>">
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label for="">Direccion</label>
                                                    <input type="text" name="direccion" class="form-control" placeholder="Escriba la direccion" value="<?php echo $direccion ?>">
                                                </div>
                                            </div>
                                            <div class="form-row">
                                                <div class="form-group col-md-6">
                                                    <label for="">Celular</label>
                                                    <input type="text" name="celular" class="form-control" placeholder="Escriba el # de celular" value="<?php echo $celular ?>">
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label for="">Descripcion</label>
                                                    <input type="text" name="descripcion" class="form-control" placeholder="Poner una descripcion" value="<?php echo $descripcion ?>">
                                                </div>
                                            </div>
                                        </fieldset>

                                        <fieldset class="border p-2 mb-3 tipo-table">
                                            <legend class="w-auto">TIPO DE PERSONA</legend>
                                            <div class="mb-2"><small class="text-muted">Marque los roles que correspondan y seleccione cuál será el tipo <strong>principal</strong>.</small></div>
                                            <?php
                                            // Render tipos in a bordered table (two columns) for neat, straight lines
                                            $existing_tipo_ids = $existing_tipo_ids ?? [];
                                            $primary_tipo_id = $primary_tipo_id ?? null;
                                            $items = $tipopersonas_datos;
                                            echo '<table class="table table-sm table-bordered" style="margin-bottom:0">';
                                            $count = count($items);
                                            for ($i = 0; $i < $count; $i += 2) {
                                                echo '<tr>';
                                                // first column
                                                $a = $items[$i];
                                                $tid = $a['id_tipoPersona'];
                                                $checked = in_array($tid, $existing_tipo_ids) ? 'checked' : '';
                                                $primary_checked = ($primary_tipo_id == $tid) ? 'checked' : '';
                                                $label = htmlspecialchars($a['name_tipoPersona']);
                                                echo '<td style="vertical-align:middle">';
                                                echo '<div class="d-flex align-items-center">';
                                                echo "<input class=\"form-check-input tipo-checkbox\" type=\"checkbox\" id=\"tipo_{$tid}\" name=\"id_tipoPersona[]\" value=\"{$tid}\" {$checked}>";
                                                echo "<label class=\"form-check-label ml-2\" for=\"tipo_{$tid}\">{$label}</label>";
                                                echo '<div style="margin-left:auto">';
                                                echo '<label class="mb-0" style="font-size:.9rem">Principal ';
                                                echo "<input type=\"radio\" name=\"primary_tipo\" class=\"primary-radio\" value=\"{$tid}\" {$primary_checked} style=\"margin-left:6px;\">";
                                                echo '</label></div>';
                                                echo '</div>';
                                                echo '</td>';
                                                // second column (if exists)
                                                if (isset($items[$i+1])) {
                                                    $b = $items[$i+1];
                                                    $tid2 = $b['id_tipoPersona'];
                                                    $checked2 = in_array($tid2, $existing_tipo_ids) ? 'checked' : '';
                                                    $primary_checked2 = ($primary_tipo_id == $tid2) ? 'checked' : '';
                                                    $label2 = htmlspecialchars($b['name_tipoPersona']);
                                                    echo '<td style="vertical-align:middle">';
                                                    echo '<div class="d-flex align-items-center">';
                                                    echo "<input class=\"form-check-input tipo-checkbox\" type=\"checkbox\" id=\"tipo_{$tid2}\" name=\"id_tipoPersona[]\" value=\"{$tid2}\" {$checked2}>";
                                                    echo "<label class=\"form-check-label ml-2\" for=\"tipo_{$tid2}\">{$label2}</label>";
                                                    echo '<div style="margin-left:auto">';
                                                    echo '<label class="mb-0" style="font-size:.9rem">Principal ';
                                                    echo "<input type=\"radio\" name=\"primary_tipo\" class=\"primary-radio\" value=\"{$tid2}\" {$primary_checked2} style=\"margin-left:6px;\">";
                                                    echo '</label></div>';
                                                    echo '</div>';
                                                    echo '</td>';
                                                } else {
                                                    echo '<td></td>';
                                                }
                                                echo '</tr>';
                                            }
                                            echo '</table>';
                                            ?>
                                            <div>
                                                <small class="form-text text-muted">Si no seleccionas un tipo principal, el sistema usará el primer tipo marcado.</small>
                                            </div>
                                        </fieldset>

                                        <fieldset class="border p-2 mb-3">
                                            <legend class="w-auto">TAGS</legend>
                                            <div class="form-group" style="margin-bottom:0">
                                                <input type="text" name="tags" class="form-control" placeholder="p.ej. cajas, empleado, fletero" value="<?= htmlspecialchars($existing_tags ?? '') ?>">
                                                <small class="form-text text-muted">Los tags permiten filtrar en informes (ej. 'cajas').</small>
                                            </div>
                                        </fieldset>
                                        <div class="form-group" hidden>
                                            <input type="text" name="id_usuario" class="form-control" value="<?php echo $id_usuario ?>">
                                        </div>
                                        <hr>
                                        <div class=form-group>
                                            <button type="submit" class="btn btn-success">Guardar</button>
                                            <a href="index.php" class="btn btn-secondary">Cancelar</a>
                                        </div>
                                    </form>
                                </div>
                            </div>
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
<script>
document.addEventListener('change', function(e){
    // when checking a tipo checkbox, auto-select primary if none chosen
    const cb = e.target.closest('.tipo-checkbox');
    if(!cb) return;
    const tid = cb.value;
    const radios = document.getElementsByName('primary_tipo');
    const anyRadio = Array.from(radios).some(r=>r.checked);
    if (cb.checked && !anyRadio) {
        const r = document.querySelector('input.primary-radio[value="'+tid+'"]');
        if (r) r.checked = true;
    }
    if (!cb.checked) {
        const primaryRadio = document.querySelector('input.primary-radio[value="'+tid+'"]');
        if (primaryRadio && primaryRadio.checked) {
            const nextCb = document.querySelector('.tipo-checkbox:checked');
            if (nextCb) {
                const r2 = document.querySelector('input.primary-radio[value="'+nextCb.value+'"]');
                if (r2) r2.checked = true;
            } else {
                // no tipos left checked -> unset primary
                primaryRadio.checked = false;
            }
        }
    }
});
</script>