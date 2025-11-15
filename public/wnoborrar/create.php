<?php
include('../app/config.php');
include('../layout/sesion.php');
include('../layout/parte1.php');

include('../app/controllers/tipocomprobantes/listado_tipocomprobantes.php');
include('../app/controllers/subCuentas/listado_subCuentas.php');
include('../app/controllers/personas/listado_personas.php');
include('../app/controllers/comprobantes/listado_comprobantes.php')


?>

<!-- Content Wrapper. Contains page content -->
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>


<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Creacion comprobante</h1>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-outline card-success">
                        <div class="card-header">
                            <!-- CONTADOR DE VENTAS -->
                            <?php
                            $contador_de_comprobantes = 0;
                            foreach ($comprobantes_datos as $comprobantes_dato) {
                                $contador_de_comprobantes++;
                            }
                            ?>
                            <h3 class="card-title"><i class="fa fa-window-maximize"></i> Nro de Referencia
                                <input type="text" value="<?php echo $contador_de_comprobantes + 1 ?>" style="text-align: center;" disable>
                            </h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body" style="display: block;">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label for="">Tipo comprobante</label>
                                            <select id="id_tipocomprobante" class="class form-control" name="id_tipocomprobante">
                                                <option value="" disabled selected>Seleccionar</option>
                                                <?php
                                                foreach ($tipocomprobantes_datos as $tipocomprobantes_dato) {
                                                ?>
                                                    <option value="<?php echo $tipocomprobantes_dato['id_tipocomprobante']; ?>"><?php echo $tipocomprobantes_dato['name_tipocomprobante']; ?></option>
                                                <?php
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label for="">Nro Comprobante</label>
                                            <input type="number" name="num_comprobante" id="num_comprobante" class="form-control" placeholder="#" required>
                                        </div>
                                        <div class="col-md-3">
                                            <label for="">Fecha</label>
                                            <input type="date" name="fecha_comprobante" id="fecha_comprobante" class="form-control" placeholder="" value="<?php echo $fecha; ?>" required>
                                        </div>
                                        <div class="col-md-3">
                                            <label for="">Hora</label>
                                            <input type="time" name="hora_comprobante" id="hora_comprobante" class="form-control" placeholder="" value="<?php echo $hora; ?>" required>
                                        </div>
                                    </div>
                                    <br>
                                    <div class="form-group">
                                        <label for="">Descripcion</label>
                                        <input type="text" name="descripcion" id="descripcion" class="form-control" placeholder="Poner una descripcion" required>
                                    </div>
                                    <div class="form-group" hidden>
                                        <input type="text" name="id_usuario" id="id_usuario" class="form-control" value="<?php echo $id_usuario ?>">
                                    </div>
                                    <hr>
                                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-create">
                                        <i class="fa fa-plus"></i> Adicionar cuentas
                                    </button>
                                    <hr>

                                    <!-- /.modal registrar cuentas -->
                                    <div class="modal fade" id="modal-create">
                                        <div class="modal-dialog modal-xl">
                                            <div class="modal-content">
                                                <div class="modal-header bg-primary">
                                                    <h4 class="modal-title">Busqueda SubCuenta</h4>
                                                </div>
                                                <div class="modal-body">
                                                    <table id="example1" class="table table-bordered table-striped">
                                                        <thead>
                                                            <tr>
                                                                <th>Nro</th>
                                                                <th>
                                                                    <center>Acciones</center>
                                                                </th>
                                                                <th>Sub cuenta</th>
                                                                <th>Cuenta</th>
                                                                <th>Saldo</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php
                                                            $contador = 0;
                                                            foreach ($subcuentas_datos as $subcuentas_dato) {

                                                                $id_subCuenta = $subcuentas_dato['id_subCuenta'];
                                                            ?>
                                                                <tr>
                                                                    <td><?php echo $contador = $contador + 1; ?></td>
                                                                    <td>
                                                                        <button class="btn btn-info" id="btn_seleccionar<?php echo $id_subCuenta; ?>">
                                                                            <i class="fa fa-plus"></i> Seleccionar
                                                                        </button>
                                                                        <script>
                                                                            $('#btn_seleccionar<?php echo $id_subCuenta ?>').click(function() {

                                                                                var name_subCuenta = "<?php echo $subcuentas_dato['name_subCuenta'] ?>";
                                                                                $('#name_subCuenta').val(name_subCuenta);

                                                                                var id_subCuenta = "<?php echo $subcuentas_dato['id_subCuenta'] ?>";
                                                                                $('#id_subCuenta').val(id_subCuenta);


                                                                                $('#debe').focus();



                                                                                //alert('<?php echo $id_subCuenta ?>')

                                                                            })
                                                                        </script>
                                                                    </td>
                                                                    <td><?php echo $subcuentas_dato['name_subCuenta']; ?></td>
                                                                    <td><?php echo $subcuentas_dato['id_cuenta'];
                                                                        echo " ";
                                                                        echo $subcuentas_dato['name_cuenta']; ?>
                                                                    </td>
                                                                    <td><?php echo $subcuentas_dato['saldo']; ?></td>
                                                                </tr>
                                                            <?php
                                                            }
                                                            ?>
                                                        </tbody>

                                                    </table>
                                                    <hr>
                                                    <div class="row">
                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <input type="text" id="id_subCuenta" hidden>
                                                                <label for="">Subcuenta</label>
                                                                <input style="font-size: 14px;" type="text" id="name_subCuenta" class="form-control" disabled>
                                                                <small style="color: red; display:none;" id="lbl_subcuenta"> *Este campo es requerido</small>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <label for="">Debe</label>
                                                                <input type="text" id="debe" class="form-control" onblur="handleInput('debe', 'haber')" onkeydown="handleKeyDown(event, 'haber')">
                                                                <small style="color: red; display:none;" id="lbl_debe"> *Este campo es requerido</small>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <label for="">Haber</label>
                                                                <input type="text" id="haber" class="form-control" onblur="handleInput('haber', 'debe')" onkeydown="handleKeyDown(event, 'debe')">
                                                                <small style="color: red; display:none;" id="lbl_haber"> *Este campo es requerido</small>
                                                            </div>
                                                        </div>

                                                        <script src="https://cdnjs.cloudflare.com/ajax/libs/mathjs/11.7.0/math.min.js"></script>
                                                        <script>
                                                            function handleInput(activeInputId, inactiveInputId) {
                                                                var activeInput = document.getElementById(activeInputId);
                                                                var inactiveInput = document.getElementById(inactiveInputId);

                                                                if (activeInput.value !== "") {
                                                                    inactiveInput.disabled = parseFloat(activeInput.value) > 0 ? true : false;
                                                                    inactiveInput.value = parseFloat(activeInput.value) > 0 ? 0 : inactiveInput.value;
                                                                } else {
                                                                    inactiveInput.disabled = false;
                                                                }

                                                                // Realizar operaciones matemáticas
                                                                realizarOperaciones();
                                                            }

                                                            function realizarOperaciones() {
                                                                var debe = document.getElementById('debe').value;
                                                                var haber = document.getElementById('haber').value;

                                                                try {
                                                                    var resultadoDebe = debe !== "" ? math.evaluate(debe) : 0;
                                                                    var resultadoHaber = haber !== "" ? math.evaluate(haber) : 0;

                                                                    // Validar que el resultado no sea negativo
                                                                    resultadoDebe = Math.max(resultadoDebe, 0);
                                                                    resultadoHaber = Math.max(resultadoHaber, 0);

                                                                    // Actualizar los valores con los resultados
                                                                    document.getElementById('debe').value = resultadoDebe;
                                                                    document.getElementById('haber').value = resultadoHaber;
                                                                } catch (error) {
                                                                    console.error("Error al realizar operaciones: " + error);
                                                                }
                                                            }

                                                            function handleKeyDown(event, nextInputId) {
                                                                if (event.key === 'Tab') {
                                                                    event.preventDefault(); // Evitar el comportamiento predeterminado del Tab
                                                                    realizarOperaciones();

                                                                    // Enfocar en el siguiente input
                                                                    document.getElementById(nextInputId).focus();
                                                                }
                                                            }
                                                        </script>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label for="">Descripcion</label>
                                                                <input type="text" id="descripcionComp" class="form-control">
                                                            </div>
                                                            <small style="color: red; display:none;" id="lbl_descripcion"> *Este campo es requerido</small>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <label for="">Persona</label>

                                                                <!-- /.INPUT DE PERSONA -->
                                                                <input style="font-size: 14px;" list="personas" id="id_persona" class="form-control" name="id_persona">
                                                                <small style="color: red; display:none;" id="lbl_persona"> *Este campo es requerido</small>
                                                                <datalist id="personas">
                                                                    <option value="" disabled selected>Seleccionar</option>
                                                                    <?php
                                                                    foreach ($personas_datos as $personas_dato) {
                                                                    ?>
                                                                        <option data-id="<?php echo $personas_dato['id_persona']; ?>" value="<?php echo $personas_dato['name_persona']; ?>"><?php echo $personas_dato['name_persona']; ?></option>
                                                                    <?php
                                                                    }
                                                                    ?>
                                                                </datalist>
                                                                <span style="font-size: 14px;color: red;" id="mensaje-error"></span>

                                                                <script>
                                                                    var idPersonaInput = document.getElementById('id_persona');
                                                                    var mensajeError = document.getElementById('mensaje-error');

                                                                    idPersonaInput.addEventListener('change', function() {
                                                                        var selectedOption = document.querySelector('#personas option[value="' + this.value + '"]');

                                                                        if (selectedOption) {
                                                                            this.setAttribute('data-id', selectedOption.getAttribute('data-id'));
                                                                            var nombrePersona = selectedOption.textContent; // Obtener el nombre de la persona seleccionada
                                                                            console.log('Nombre de la persona seleccionada:', nombrePersona);
                                                                            mensajeError.textContent = ''; // Limpiar el mensaje de error si existe
                                                                        } else {
                                                                            this.removeAttribute('data-id');
                                                                            this.value = ''; // Limpiar el valor si no coincide con ninguna opción
                                                                            mensajeError.textContent = 'No existe';
                                                                        }
                                                                    });
                                                                </script>
                                                                <!-- /.INPUT DE PERSONA -->
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <button style="float: right;" type="button" class="btn btn-primary" id="btn_registrar_comprobante">Registrar</button>

                                                    <!-- /.SCRIPT REGISTRO COMPROBANTES -->
                                                    <script>
                                                        // Variable para mantener el contador de filas
                                                        var contadorFilas = 1;

                                                        $('#btn_registrar_comprobante').click(function() {
                                                            var id_comprobante = '<?php echo $contador_de_comprobantes + 1 ?>';
                                                            var id_subCuenta = $('#id_subCuenta').val();
                                                            var id_persona = $('#id_persona').attr('data-id');
                                                            var debe = parseFloat($('#debe').val());
                                                            var haber = parseFloat($('#haber').val());
                                                            var descripcionComp = $('#descripcionComp').val();
                                                            var name_subCuenta = $('#name_subCuenta').val();
                                                            var name_persona = $('#id_persona').val();

                                                            // Validación de campos vacíos y valores mayores que 0
                                                            if (id_subCuenta == "" || id_persona == undefined || (debe <= 0 && haber <= 0)) {
                                                                // Al menos uno de los campos está vacío o ninguno de los campos "Debe" o "Haber" tiene un valor mayor que 0, muestra las advertencias correspondientes
                                                                if (id_subCuenta == "") {
                                                                    $('#lbl_subcuenta').css('display', 'block');
                                                                }
                                                                if (id_persona == undefined) {
                                                                    $('#id_persona').focus();
                                                                    $('#lbl_persona').css('display', 'block');
                                                                }
                                                                if (debe <= 0) {
                                                                    $('#lbl_debe').css('display', 'block');
                                                                }
                                                                if (haber <= 0) {
                                                                    $('#lbl_haber').css('display', 'block');
                                                                }
                                                            } else {
                                                                // Todos los campos están llenos y al menos uno de los campos "Debe" o "Haber" tiene un valor mayor que 0, continuar con la lógica de tu aplicación
                                                                // Construye la fila de la tabla HTML con los datos ingresados en el modal
                                                                var filaTabla = '<tr id="row_id_' + contadorFilas + '">';
                                                                filaTabla += '<td><center>' + contadorFilas + '</center></td>';
                                                                filaTabla += '<td class="id_subCuenta" style="display: none;">' + id_subCuenta + '</td>';
                                                                filaTabla += '<td>' + name_subCuenta + '</td>';
                                                                filaTabla += '<td class="debe" style="text-align: right;">' + debe.toFixed(2) + '</td>';
                                                                filaTabla += '<td class="haber" style="text-align: right;">' + haber.toFixed(2) + '</td>';
                                                                filaTabla += '<td class="descripcionComp">' + descripcionComp + '</td>';
                                                                filaTabla += '<td class="id_persona" style="display: none;">' + id_persona + '</td>';
                                                                filaTabla += '<td><center>' + name_persona + '</center></td>';
                                                                filaTabla += '<td><button type="button" style="width: 100%;" name="remove_row" class="btn btn-danger btn-xs remove_row">-</button></td>';
                                                                filaTabla += '</tr>';


                                                                // Agrega la fila a la tabla existente
                                                                $('#tablaExistente tbody').append(filaTabla);

                                                                // Incrementa el contador de filas para la próxima fila
                                                                contadorFilas++;

                                                                // Cierra el modal
                                                                $('#modal-create').modal('hide');

                                                                // Limpia los campos del modal para la próxima entrada
                                                                // Limpiar campos del modal aquí
                                                                $('#id_subCuenta').val('');
                                                                $('#id_persona').val('');
                                                                $('#debe').prop('disabled', false).val('0');
                                                                $('#haber').prop('disabled', false).val('0');
                                                                //$('#descripcion').val('');
                                                                $('#name_subCuenta').val('');
                                                                $('#lbl_subcuenta').css('display', 'none');
                                                                $('#lbl_persona').css('display', 'none');
                                                                $('#lbl_debe').css('display', 'none');
                                                                $('#lbl_haber').css('display', 'none');

                                                                // Actualizar total
                                                                actualizarTotal();
                                                            }
                                                        });

                                                        // Evento para eliminar una fila al hacer clic en el botón de eliminar
                                                        $(document).on('click', '.remove_row', function() {
                                                            var row_id = $(this).closest('tr').attr("id");
                                                            $('#' + row_id).remove(); // Corrección aquí
                                                            // Actualizar total
                                                            actualizarTotal();
                                                        });

                                                        // Función para calcular el total y actualizar la fila de total
                                                        function actualizarTotal() {
                                                            var totalDebe = sumarColumna(3); // Suma la columna de "Debe" (índice 3)
                                                            var totalHaber = sumarColumna(4); // Suma la columna de "Haber" (índice 4)
                                                            $('#total_debe').text(totalDebe.toFixed(2));
                                                            $('#total_haber').text(totalHaber.toFixed(2));

                                                            // Mover la fila de totales al final de la tabla
                                                            $('#row_id_final').detach().appendTo($('#tablaExistente tbody'));
                                                        }

                                                        // Función para sumar los valores de una columna
                                                        function sumarColumna(indice) {
                                                            var total = 0;
                                                            $('#tablaExistente tbody tr').each(function() {
                                                                var valor = parseFloat($(this).find('td:eq(' + indice + ')').text());
                                                                total += isNaN(valor) ? 0 : valor;
                                                            });
                                                            return total;
                                                        }
                                                    </script>


                                                </div>
                                            </div>
                                        </div>
                                        <!-- /.modal-content -->
                                    </div>
                                    <!-- /.modal-dialog -->

                                </div>
                                <br><br>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-sm table-hover table-striped" id="tablaExistente">
                                        <thead>
                                            <tr>
                                                <th style="width: 2%; background-color: #e7e7e7;text-align: center;">Nro</th>
                                                <th style="width: 15%; background-color: #e7e7e7;text-align: center;">Cuenta</th>
                                                <th style="width: 7%; background-color: #e7e7e7;text-align: center;">Debe</th>
                                                <th style="width: 7%; background-color: #e7e7e7;text-align: center;">Haber</th>
                                                <th style="width: 20%; background-color: #e7e7e7;text-align: center;">Descripcion</th>
                                                <th style="width: 10%; background-color: #e7e7e7;text-align: center;">Persona</th>
                                                <th style="width: 2%; background-color: #e7e7e7; text-align: center; vertical-align: middle;">
                                                    <i class="fa fa-filter" aria-hidden="true"></i>
                                                </th>
                                            </tr>
                                        </thead>

                                        <body>
                                            <tr id="row_id_final">
                                                <td colspan="2"></td>
                                                <th style="text-align: right;" id="total_debe">0.00</th>
                                                <th style="text-align: right;" id="total_haber">0.00</th>
                                                <th colspan="3"></th>
                                            </tr>
                                        </body>
                                    </table>
                                    <div class="modal-footer justify-content-between">
                                        <button type="button" class="btn btn-success" id="btn_crear_comprobante">Guardar</button>
                                        <a href="index.php" class="btn btn-secondary">Cancelar</a>
                                    </div>
                                    <script>
                                        $('#btn_crear_comprobante').click(function() {
                                            var id_tipocomprobante = $('#id_tipocomprobante').val();
                                            var num_comprobante = $('#num_comprobante').val();
                                            var fecha_comprobante = $('#fecha_comprobante').val();
                                            var hora_comprobante = $('#hora_comprobante').val();
                                            var descripcion = $('#descripcion').val();
                                            var id_usuario = $('#id_usuario').val();

                                            //alert(id_tipocomprobante + ' ' + num_comprobante + ' ' + fecha_comprobante + ' ' + hora_comprobante + ' ' + descripcion +
                                            //    ' ' + id_usuario);


                                            var detalles_venta = [];
                                            // Seleccionar todas las filas excepto la última
                                            $('#tablaExistente tbody tr').slice(0, -1).each(function() {
                                                var detalle = {
                                                    id_subCuenta: $(this).find('.id_subCuenta').text(),
                                                    debe: $(this).find('.debe').text(),
                                                    haber: $(this).find('.haber').text(),
                                                    descripcion: $(this).find('.descripcionComp').text(),
                                                    id_persona: $(this).find('.id_persona').text()
                                                };
                                                detalles_venta.push(detalle);
                                            });




                                            //console.log(detalles_venta);


                                            var datos_venta = {
                                                id_tipocomprobante: id_tipocomprobante,
                                                num_comprobante: num_comprobante,
                                                fecha_comprobante: fecha_comprobante,
                                                hora_comprobante: hora_comprobante,
                                                descripcion: descripcion,
                                                id_usuario: id_usuario,
                                                // Otros campos de la venta
                                                detalles_venta: detalles_venta
                                            };
                                            console.log(datos_venta);
                                        
                                            axios.post("../app/controllers/comprobantes/create.php", datos_venta)
                                                .then(response => {
                                                    if (response.data.success) {
                                                        alert('Datos recibidos del servidor: ' + JSON.stringify(response.data));
                                                    } else {
                                                        alert('Error al insertar la venta');
                                                    }
                                                })
                                                .catch(error => {
                                                    alert('Error de AJAX: ' + error);
                                                });
                                        });
                                    </script>
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
    $(function() {
        $("#example1").DataTable({
            "pageLength": 3,
            language: {
                "emptyTable": "No hay información",
                "decimal": "",
                "info": "Mostrando _START_ a _END_ de _TOTAL_ SubCuentas",
                "infoEmpty": "Mostrando 0 a 0 de 0 SubCuentas",
                "infoFiltered": "(Filtrado de _MAX_ total SubCuentas)",
                "infoPostFix": "",
                "thousands": ",",
                "lengthMenu": "Mostrar _MENU_ SubCuentas",
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
        }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    });
</script>