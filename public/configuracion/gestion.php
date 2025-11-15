<?php
include('../app/config.php');
include('../layout/sesion.php');
include('../layout/parte1.php');

include('../app/controllers/gestion/listargestion.php')
?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- /.content -->
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6">
                    <div class="card card-primary collapsed-card">
                        <div class="card-header">
                            <h3 class="card-title">ADMINISTRACION DE GESTION</h3>
                        </div>
                        <br>
                        <div class="d-flex justify-content-center">
                            <button type="button" class="btn btn-outline-primary btn-lg" disabled>Gestion Activa: <?php echo GESTION_ACTIVA ?> - <?php echo $anio_gestion ?></button>
                        </div>
                        <div class="card-body d-flex justify-content-center align-items-center" style="display: block; min-height: 200px;">
                            <form id="changeGestionForm" action="#" method="POST" class="mt-4">
                                <div class="input-group mb-3">
                                    <label for="gestionSelect" class="input-group-text">Gestión:</label>
                                    <select class="form-control" id="gestionSelect" name="gestion">
                                        <option selected>Seleccionar</option>
                                        <?php foreach ($gestion_datos as $gestion) : ?>
                                            <option value="<?php echo $gestion['id_gestion']; ?>">
                                                <?php echo $gestion['anio_gestion']; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <button type="button" onclick="confirmChangeGestion();" class="btn btn-primary">
                                    Cambiar Gestión
                                    <i class="fa fa-cog"></i>
                                </button>
                            </form>
                        </div>
                        <script>
                            function confirmChangeGestion() {
                                Swal.fire({
                                    title: '¿Estás seguro de cambiar la gestión?',
                                    text: "Este cambio afectará la configuración del sistema",
                                    icon: 'warning',
                                    showCancelButton: true,
                                    confirmButtonColor: '#3085d6',
                                    cancelButtonColor: '#d33',
                                    confirmButtonText: 'Sí, cambiar'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        var gestionId = document.getElementById('gestionSelect').value;
                                        if (gestionId !== "Seleccionar") {
                                            window.location.href = "../app/controllers/gestion/update.php?id=" + gestionId;
                                        } else {
                                            Swal.fire('Por favor, seleccione una gestión.', '', 'info');
                                        }
                                    }
                                });
                            }
                        </script>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card card-success collapsed-card">
                        <div class="card-header">
                            <h3 class="card-title">CREAR NUEVA GESTION</h3>
                        </div>
                        <div class="card-body d-flex justify-content-center align-items-center" style="min-height: 200px;">
                            <form action="ruta_a_tu_controlador" method="POST" class="mt-4">
                                <div class="mb-3">
                                    <label for="nuevaGestion" class="form-label">Crear Nueva Gestión</label>
                                    <select class="form-control" id="nuevaGestion" name="nuevaGestion">
                                        <option value="" disabled selected>Selecciona un año</option>
                                        <?php
                                        $anioFinal = $ultimo_anio_registrado + 10; // Genera años hasta 10 años después del último registrado.
                                        for ($anio = $ultimo_anio_registrado + 1; $anio <= $anioFinal; $anio++) {
                                            echo "<option value='$anio'>$anio</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <button type="button" onclick="confirmCreateGestion();" class="btn btn-success">
                                    Crear Gestión
                                    <i class="fa fa-plus"></i>
                                </button>
                            </form>
                            <script>
                                function confirmCreateGestion() {
                                    var nuevaGestion = document.getElementById('nuevaGestion').value;
                                    if (nuevaGestion === '') {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Selección requerida',
                                            text: 'Por favor, selecciona un año para la nueva gestión.',
                                        });
                                        return;
                                    }

                                    Swal.fire({
                                        title: '¿Estás seguro de crear la nueva gestión?',
                                        text: "Este cambio afectará la configuración del sistema",
                                        icon: 'warning',
                                        showCancelButton: true,
                                        confirmButtonColor: '#3085d6',
                                        cancelButtonColor: '#d33',
                                        confirmButtonText: 'Sí, crear',
                                        cancelButtonText: 'Cancelar'
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            // Cambiar a la ruta correcta para crear la gestión.
                                            window.location.href = "../app/controllers/gestion/create.php?anio=" + nuevaGestion;
                                        }
                                    });
                                }
                            </script>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </div>
</div>
<!-- /.content-wrapper -->
<script>
    if (sessionStorage.getItem('mensaje') && sessionStorage.getItem('icono')) {
        Swal.fire({
            icon: sessionStorage.getItem('icono'),
            title: sessionStorage.getItem('mensaje'),
            showConfirmButton: false,
            timer: 1500
        });

        // Limpiar sessionStorage después de mostrar el mensaje
        sessionStorage.removeItem('mensaje');
        sessionStorage.removeItem('icono');
    }
</script>

<?php
include('../layout/parte2.php');
include('../layout/mensajes.php');
?>