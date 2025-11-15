<?php
include('app/config.php');
include('layout/sesion.php');
include('layout/parte1.php');

include('app/controllers/informes/listarInformes.php');
include('app/controllers/personas/listado_personas.php');
include('app/controllers/usuarios/listado_usuarios.php');
include('app/controllers/informes/saldoclientestipo.php')
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">Bienvenido al sistema - <?php echo $rol_sesion ?></h1>
        </div><!-- /.col -->
      </div><!-- /.row -->
    </div><!-- /.container-fluid -->
  </div>


  <section class="content">
    <div class="container-fluid">

      <div class="row">
        <div class="col-12 col-sm-6 col-md-3">
          <div class="info-box">
            <span class="info-box-icon bg-info elevation-1"><i class="fas fa-dollar-sign"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">CAJA GENERAL</span>
              <a href="../contabilidad/mayorsubcuenta.php?id_subcuenta=1" class="ml-3" style="font-weight: bold;">
                <?php echo htmlspecialchars(number_format($sumaCajaGeneral_datos['saldo_total'], 2, '.', ',')); ?>
              </a>
            </div>

          </div>

        </div>

        <div class="col-12 col-sm-6 col-md-3">
          <div class="info-box mb-3">
            <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-chart-bar"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">Notas de despacho</span>
              <a href="despachos/index.php" class="btn btn-primary btn-sm">
                Ver informe
              </a>
            </div>

          </div>

        </div>


        <div class="clearfix hidden-md-up"></div>
        <div class="col-12 col-sm-6 col-md-3">
          <div class="info-box mb-3">
            <span class="info-box-icon bg-success elevation-1"><i class="fas fa-receipt"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">Comprobantes</span>
              <a href="comprobantes/index.php" class="btn btn-primary btn-sm">
                Ver informe
              </a>
            </div>

          </div>

        </div>

        <div class="col-12 col-sm-6 col-md-3">
          <div class="info-box mb-3">
            <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-file-alt"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">Plan de cuentas</span>
              <a href="contabilidad/index.php" class="btn btn-primary btn-sm">
                Ver informe
              </a>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-md-12">
          <div class="card">
            <div class="card-header">
              <h5 class="card-title">Reportes</h5>
              <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                  <i class="fas fa-minus"></i>
                </button>

                <button type="button" class="btn btn-tool" data-card-widget="remove">
                  <i class="fas fa-times"></i>
                </button>
              </div>
            </div>

            <div class="card-body" style="display: block;">
              <div class="row">
                <div class="col-md-8 US-Visitors Report ">
                  <p class="text-center">
                    <strong>Resumen de ventas y corbranzas por dia</strong>
                  </p>
                  <div class="chart">
                    <div class="chartjs-size-monitor">
                      <div class="chartjs-size-monitor-expand">
                        <div class=""></div>
                      </div>
                      <div class="chartjs-size-monitor-shrink">
                        <div class=""></div>
                      </div>
                    </div>
                    <canvas id="salesChart" height="100" style="height: 180px; display: block; width: 358px;"></canvas>
                  </div>
                </div>


                <script>
                  document.addEventListener('DOMContentLoaded', function() {
                    const ctx = document.getElementById('salesChart').getContext('2d');
                    const tableBody = document.getElementById('data-table-body'); // Asegúrate de que este ID coincida con tu elemento tbody

                    fetch('<?php echo $URL; ?>/app/controllers/informes/graficoVentas.php')
                      .then(response => response.json())
                      .then(data => {
                        console.log("Datos recibidos del servidor:", data);

                        const lastIndex = data.fechas.length;
                        const firstIndexGraph = Math.max(0, lastIndex - 10);
                        const firstIndexTable = Math.max(0, lastIndex - 4);

                        const salesChart = new Chart(ctx, {
                          type: 'line',
                          data: {
                            labels: data.fechas.slice(firstIndexGraph).map(fecha => {
                              const [year, month, day] = fecha.split('-');
                              return `${day}/${month}`; // Formatea las fechas como dd/mm
                            }),
                            datasets: [{
                              label: 'Ventas por día',
                              data: data.ventas.slice(firstIndexGraph),
                              borderColor: 'rgba(54, 162, 235, 1)',
                              backgroundColor: 'rgba(54, 162, 235, 0.2)',
                              borderWidth: 1,
                              fill: false
                            }, {
                              label: 'Cobranzas por día',
                              data: data.cobranzas.slice(firstIndexGraph),
                              borderColor: 'rgba(255, 99, 132, 1)',
                              backgroundColor: 'rgba(255, 99, 132, 0.2)',
                              borderWidth: 1,
                              fill: false
                            }]
                          },
                          options: {
                            responsive: true,
                            scales: {
                              y: {
                                beginAtZero: true
                              }
                            }
                          }
                        });

                        // Limpia el cuerpo de la tabla antes de añadir nuevas filas
                        tableBody.innerHTML = '';

                        // Añadir filas a la tabla para los últimos 4 registros en orden inverso
                        for (let i = lastIndex - 1; i >= firstIndexTable; i--) {
                          const [year, month, day] = data.fechas[i].split('-');
                          const formattedDate = `${year}-${month}-${day}`; // Formato de fecha para el enlace
                          const row = document.createElement('tr');
                          row.innerHTML = `
                            <td class="text-center">${day}/${month}/${year}</td>
                            <td class="text-center"><a href="kardex/resumenVentas.php?fecha_inicio=${formattedDate}&fecha_fin=${formattedDate}">${data.ventas[i]}</a></td>
                            <td class="text-center">${data.cobranzas[i]}</td>
                          `;
                          tableBody.appendChild(row);
                        }
                      })
                      .catch(error => console.error('Error al cargar los datos del gráfico:', error));
                  });
                </script>

                <!-- Otros elementos del dashboard aquí -->
                <div class="col-md-4">
                  <table class="table">
                    <thead>
                      <tr>
                        <th class="text-center">Fecha</th>
                        <th class="text-center">Ventas</th>
                        <th class="text-center">Cobranzas</th>
                      </tr>
                    </thead>
                    <tbody id="data-table-body">
                      <!-- Las filas se añadirán aquí dinámicamente -->
                    </tbody>
                  </table>

                </div>
              </div>
            </div>



            <div class="card-footer" style="display: block;" hidden>
              <div class="row">
                <div class="col-sm-3 col-6">
                  <div class="description-block border-right">
                    <span class="description-percentage text-success"><i class="fas fa-caret-up"></i> 17%</span>
                    <h5 class="description-header">$35,210.43</h5>
                    <span class="description-text">TOTAL REVENUE</span>
                  </div>

                </div>

                <div class="col-sm-3 col-6">
                  <div class="description-block border-right">
                    <span class="description-percentage text-warning"><i class="fas fa-caret-left"></i> 0%</span>
                    <h5 class="description-header">$10,390.90</h5>
                    <span class="description-text">TOTAL COST</span>
                  </div>

                </div>

                <div class="col-sm-3 col-6">
                  <div class="description-block border-right">
                    <span class="description-percentage text-success"><i class="fas fa-caret-up"></i> 20%</span>
                    <h5 class="description-header">$24,813.53</h5>
                    <span class="description-text">TOTAL PROFIT</span>
                  </div>

                </div>

                <div class="col-sm-3 col-6">
                  <div class="description-block">
                    <span class="description-percentage text-danger"><i class="fas fa-caret-down"></i> 18%</span>
                    <h5 class="description-header">1200</h5>
                    <span class="description-text">GOAL COMPLETIONS</span>
                  </div>

                </div>
              </div>

            </div>

          </div>

        </div>

      </div>

      <div class="row" hidden>
        <div class="col-md-8">
          <div class="card">
            <div class="card-header border-transparent">
              <h3 class="card-title">Latest Orders</h3>
              <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                  <i class="fas fa-minus"></i>
                </button>
                <button type="button" class="btn btn-tool" data-card-widget="remove">
                  <i class="fas fa-times"></i>
                </button>
              </div>
            </div>

            <div class="card-body p-0">
              <div class="table-responsive">
                <table class="table m-0">
                  <thead>
                    <tr>
                      <th>Order ID</th>
                      <th>Item</th>
                      <th>Status</th>
                      <th>Popularity</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td><a href="pages/examples/invoice.html">OR9842</a></td>
                      <td>Call of Duty IV</td>
                      <td><span class="badge badge-success">Shipped</span></td>
                      <td>
                        <div class="sparkbar" data-color="#00a65a" data-height="20">90,80,90,-70,61,-83,63</div>
                      </td>
                    </tr>
                    <tr>
                      <td><a href="pages/examples/invoice.html">OR1848</a></td>
                      <td>Samsung Smart TV</td>
                      <td><span class="badge badge-warning">Pending</span></td>
                      <td>
                        <div class="sparkbar" data-color="#f39c12" data-height="20">90,80,-90,70,61,-83,68</div>
                      </td>
                    </tr>
                    <tr>
                      <td><a href="pages/examples/invoice.html">OR7429</a></td>
                      <td>iPhone 6 Plus</td>
                      <td><span class="badge badge-danger">Delivered</span></td>
                      <td>
                        <div class="sparkbar" data-color="#f56954" data-height="20">90,-80,90,70,-61,83,63</div>
                      </td>
                    </tr>
                    <tr>
                      <td><a href="pages/examples/invoice.html">OR7429</a></td>
                      <td>Samsung Smart TV</td>
                      <td><span class="badge badge-info">Processing</span></td>
                      <td>
                        <div class="sparkbar" data-color="#00c0ef" data-height="20">90,80,-90,70,-61,83,63</div>
                      </td>
                    </tr>
                    <tr>
                      <td><a href="pages/examples/invoice.html">OR1848</a></td>
                      <td>Samsung Smart TV</td>
                      <td><span class="badge badge-warning">Pending</span></td>
                      <td>
                        <div class="sparkbar" data-color="#f39c12" data-height="20">90,80,-90,70,61,-83,68</div>
                      </td>
                    </tr>
                    <tr>
                      <td><a href="pages/examples/invoice.html">OR7429</a></td>
                      <td>iPhone 6 Plus</td>
                      <td><span class="badge badge-danger">Delivered</span></td>
                      <td>
                        <div class="sparkbar" data-color="#f56954" data-height="20">90,-80,90,70,-61,83,63</div>
                      </td>
                    </tr>
                    <tr>
                      <td><a href="pages/examples/invoice.html">OR9842</a></td>
                      <td>Call of Duty IV</td>
                      <td><span class="badge badge-success">Shipped</span></td>
                      <td>
                        <div class="sparkbar" data-color="#00a65a" data-height="20">90,80,90,-70,61,-83,63</div>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>

            </div>

            <div class="card-footer clearfix">
              <a href="javascript:void(0)" class="btn btn-sm btn-info float-left">Place New Order</a>
              <a href="javascript:void(0)" class="btn btn-sm btn-secondary float-right">View All Orders</a>
            </div>

          </div>

        </div>

        <div class="col-md-4">

          <div class="info-box mb-3 bg-warning">
            <span class="info-box-icon"><i class="fas fa-tag"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">Inventory</span>
              <span class="info-box-number">5,200</span>
            </div>

          </div>

          <div class="info-box mb-3 bg-success">
            <span class="info-box-icon"><i class="far fa-heart"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">Mentions</span>
              <span class="info-box-number">92,050</span>
            </div>

          </div>

          <div class="info-box mb-3 bg-danger">
            <span class="info-box-icon"><i class="fas fa-cloud-download-alt"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">Downloads</span>
              <span class="info-box-number">114,381</span>
            </div>

          </div>

          <div class="info-box mb-3 bg-info">
            <span class="info-box-icon"><i class="far fa-comment"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">Direct Messages</span>
              <span class="info-box-number">163,921</span>
            </div>

          </div>

        </div>

      </div>

    </div>
  </section>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="<?php echo $URL; ?>/public/templeates/AdminLTE-3.2.0/plugins/jquery/jquery.min.js"></script>
<script src="<?php echo $URL; ?>/public/templeates/AdminLTE-3.2.0/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="<?php echo $URL; ?>/public/templeates/AdminLTE-3.2.0/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
<script src="<?php echo $URL; ?>/public/templeates/AdminLTE-3.2.0/dist/js/adminlte.js?v=3.2.0"></script>
<script src="<?php echo $URL; ?>/public/templeates/AdminLTE-3.2.0/plugins/jquery-mousewheel/jquery.mousewheel.js"></script>
<script src="<?php echo $URL; ?>/public/templeates/AdminLTE-3.2.0/plugins/raphael/raphael.min.js"></script>
<script src="<?php echo $URL; ?>/public/templeates/AdminLTE-3.2.0/plugins/jquery-mapael/jquery.mapael.min.js"></script>
<script src="<?php echo $URL; ?>/public/templeates/AdminLTE-3.2.0/plugins/jquery-mapael/maps/usa_states.min.js"></script>
<script src="<?php echo $URL; ?>/public/templeates/AdminLTE-3.2.0/plugins/chart.js/Chart.min.js"></script>

<?php
include('layout/parte2.php');
include('layout/mensajes.php');
?>