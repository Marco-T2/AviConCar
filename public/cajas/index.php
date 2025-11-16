<?php
include('../app/config.php');
include('../layout/sesion.php');
include('../layout/parte1.php');
?>

<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-md-8">
          <h4 style="margin:0">CAJAS — Menú</h4>
        </div>
      </div>
    </div>
  </div>

  <div class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-4">
          <div class="small-box bg-info">
            <div class="inner">
              <h5>Registro de recojo</h5>
              <p>Crear y registrar operaciones de recojo de cajas</p>
            </div>
            <div class="icon"><i class="fas fa-box"></i></div>
            <a href="./recojo.php" class="small-box-footer">Abrir <i class="fas fa-arrow-circle-right"></i></a>
          </div>
        </div>

        <div class="col-md-4">
          <div class="small-box bg-success">
            <div class="inner">
              <h5>Registro de entrega</h5>
              <p>Registrar entregas de cajas a clientes</p>
            </div>
            <div class="icon"><i class="fas fa-truck-loading"></i></div>
            <a href="./entrega.php" class="small-box-footer">Abrir <i class="fas fa-arrow-circle-right"></i></a>
          </div>
        </div>

        <div class="col-md-4">
          <div class="small-box bg-warning">
            <div class="inner">
              <h5>Administración de cajas</h5>
              <p>Crear y gestionar tipos de caja (NEG, VER, AZU, etc.)</p>
            </div>
            <div class="icon"><i class="fas fa-cubes"></i></div>
            <a href="./ajustes.php" class="small-box-footer">Abrir <i class="fas fa-arrow-circle-right"></i></a>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>

<?php
include('../layout/parte2.php');
include('../layout/mensajes.php');
?>

