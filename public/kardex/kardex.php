<?php
// C:\web\stack\ct\public\kardex\kardex.php  (vista)
include('../app/config.php');
include('../layout/sesion.php');
include('../layout/parte1.php');

include('../app/controllers/informes/listarpersona.php'); // Debe definir $id_persona, $name_persona
include('../app/controllers/gestion/listarperiodo.php'); // Debe definir $periodos_datos

// Fechas por defecto (últimos 14 días)
$hoy = new DateTime('today');
$fecha          = $hoy->format('Y-m-d');
$fechaQuincenal = (clone $hoy)->modify('-14 days')->format('Y-m-d');
?>
<!-- JSZip (requerido por excelHtml5) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<div class="content-wrapper">
  <div class="content">
    <div class="container-fluid">
      <div class="row"><div class="col-md-12">
        <div class="card card-primary collapsed-card">

          <div class="container mt-3">
            <div class="row g-2">
              <div class="col-12 col-md-6 d-flex flex-wrap align-items-center">
                <input type="hidden" id="id_persona" value="<?php echo (int)($id_persona ?? 0); ?>">
                <div class="me-2 mb-2 mb-md-0">
                  <label class="form-label text-sm">Reporte:</label>
                  <select id="id_periodo" class="form-control form-control-sm btn-primary" name="id_periodo">
                    <option value="" disabled selected>Seleccionar</option>
                    <?php if (!empty($periodos_datos)): ?>
                      <?php foreach ($periodos_datos as $periodo): ?>
                        <option value="<?php echo (int)$periodo['id_periodo']; ?>">
                          <?php echo htmlspecialchars($periodo['name_periodo'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
                        </option>
                      <?php endforeach; ?>
                    <?php endif; ?>
                  </select>
                </div>
                <span class="text-sm align-self-center mx-2"></span>
                <div class="me-2 mb-2 mb-md-0">
                  <label for="fecha_inicio" class="form-label text-sm">Fecha Inicio:</label>
                  <input type="date" class="form-control form-control-sm" id="fecha_inicio" name="fecha_inicio"
                         value="<?php echo htmlspecialchars($fechaQuincenal, ENT_QUOTES, 'UTF-8'); ?>">
                </div>
                <div class="mb-2 mb-md-0">
                  <label for="fecha_fin" class="form-label text-sm">Fecha Fin:</label>
                  <input type="date" class="form-control form-control-sm" id="fecha_fin" name="fecha_fin"
                         value="<?php echo htmlspecialchars($fecha, ENT_QUOTES, 'UTF-8'); ?>">
                </div>
              </div>

              <div class="col-12 col-md-6 d-flex justify-content-md-end align-items-center flex-wrap">
                <button type="button" id="aplicarFiltros" class="btn btn-success btn-sm me-2 mb-2 mb-md-0">Aplicar</button>
                <span class="text-sm align-self-center mx-2"></span>
                <button type="button" id="imprimir" class="btn btn-warning btn-sm mb-2 mb-md-0"><i class="fa fa-print fa-sm"></i> Imprimir</button>
                <span class="text-sm align-self-center mx-2"></span>
                <a href="<?php echo $URL; ?>/despachos/create.php?id=<?php echo (int)($id_persona ?? 0); ?>" class="btn btn-primary btn-sm mb-2 mb-md-0"><i class="fa fa-plus fa-sm"></i> Despacho</a>
                <span class="text-sm align-self-center mx-2"></span>
                <a href="<?php echo $URL; ?>/comprobantes/create.php?id=<?php echo (int)($id_persona ?? 0); ?>" class="btn btn-primary btn-sm mb-2 mb-md-0"><i class="fa fa-plus fa-sm"></i> Comprobante</a>
                <span class="text-sm align-self-center mx-2"></span>
                <a href="<?php echo $URL; ?>/kardex" class="btn btn-secondary btn-sm mb-2 mb-md-0">Regresar</a>
              </div>
            </div>
          </div>

          <hr>
          <div class="d-flex flex-column justify-content-center align-items-center text-center" style="gap:.10rem;">
            <h4 style="font-size:.9rem;margin-bottom:0;"><strong>AVICOLA EL CARMEN</strong></h4>
            <span class="text-sm" style="font-size:.85rem;">Kardex - <?php echo htmlspecialchars($name_persona ?? 'Cliente', ENT_QUOTES, 'UTF-8'); ?></span>
            <span id="dateRangeSpan" style="font-size:.85rem;">día mes - día mes, año</span>
          </div>

          <div class="card-body" style="display:block;">
            <div class="table-responsive">
              <table id="example1" class="table table-bordered table-striped table-sm" style="font-size:.85rem; vertical-align:middle;">
                <thead>
                  <tr>
                    <th style="width:6%;">Fecha</th>
                    <th style="width:10%;">Doc</th>
                    <th style="width:5%;">Nro</th>
                    <th style="width:25%;">Detalle</th>
                    <th style="width:6%;">Debe</th>
                    <th style="width:6%;">Haber</th>
                    <th style="width:6%;">Saldo</th>
                    <th style="width:2%;"><center><i class="fa fa-filter" aria-hidden="true"></i></center></th>
                  </tr>
                </thead>
                <tbody id="tableBody"><!-- Se llena por AJAX --></tbody>
              </table>
            </div>
          </div>

        </div>
      </div></div>
    </div>
  </div>
</div>

<script>
  // Nombre de cliente disponible para exportaciones
  window.NOMBRE_CLIENTE = <?= json_encode($name_persona ?? 'Cliente') ?>;

  $(function(){
    // --- util: formato legible del rango ---
    function formatDateSpan(dateString) {
      const months = ['enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre'];
      const d = new Date(dateString + 'T00:00:00');
      return `${d.getDate()} ${months[d.getMonth()]}, ${d.getFullYear()}`;
    }
    $('#dateRangeSpan').text(
      `${formatDateSpan($('#fecha_inicio').val())} - ${formatDateSpan($('#fecha_fin').val())}`
    );

    // --- periodos rápidos ---
    $('#id_periodo').on('change', function(){
      const val = String($(this).val()||'');
      const today = new Date();
      const ymd = d => d.toISOString().slice(0,10);
      const startOfWeek = d => { const n=new Date(d), day=n.getDay(), diff=n.getDate()-day+(day===0?-6:1); n.setDate(diff); return n; };
      const endOfWeek   = d => { const s=startOfWeek(d), e=new Date(s); e.setDate(s.getDate()+6); return e; };
      const startOfMonth= d => new Date(d.getFullYear(), d.getMonth(), 1);
      const endOfMonth  = d => new Date(d.getFullYear(), d.getMonth()+1, 0);
      const startOfYear = d => new Date(d.getFullYear(), 0, 1);
      const endOfYear   = d => new Date(d.getFullYear(), 11, 31);
      let fi=$('#fecha_inicio').val(), ff=$('#fecha_fin').val();
      switch(val){
        case '1': fi=ymd(today);               ff=ymd(today);              break;
        case '2': fi=ymd(startOfWeek(today));  ff=ymd(endOfWeek(today));   break;
        case '3': fi=ymd(startOfWeek(today));  ff=ymd(today);              break;
        case '4': fi=ymd(startOfMonth(today)); ff=ymd(endOfMonth(today));  break;
        case '5': fi=ymd(startOfMonth(today)); ff=ymd(today);              break;
        case '6': fi=ymd(startOfYear(today));  ff=ymd(endOfYear(today));   break;
        case '7': fi=ymd(startOfYear(today));  ff=ymd(today);              break;
      }
      $('#fecha_inicio').val(fi);
      $('#fecha_fin').val(ff);
      $('#dateRangeSpan').text(`${formatDateSpan(fi)} - ${formatDateSpan(ff)}`);
    });

    // --- helpers de DataTables (nombres de archivo dinámicos) ---
    const pad = n => String(n).padStart(2,'0');
    const selloTiempo = () => {
      const d=new Date();
      return `${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())}_${pad(d.getHours())}-${pad(d.getMinutes())}-${pad(d.getSeconds())}`;
    };
    const safeName = s => (s||'').normalize('NFD').replace(/[\u0300-\u036f]/g,'').replace(/[^a-z0-9\-_. ]/gi,'_').replace(/\s+/g,'_');
    function fileBaseName(){
      const cliente = safeName(window.NOMBRE_CLIENTE || 'Cliente');
      const fi = ($('#fecha_inicio').val()||'').replaceAll('-','');
      const ff = ($('#fecha_fin').val()||'').replaceAll('-','');
      const rango = (fi && ff) ? `_${fi}_a_${ff}` : '';
      return `Kardex_${cliente}${rango}_${selloTiempo()}`;
    }

    let dt = null;
    function initDT(){
      dt = $("#example1").DataTable({
        pageLength: 10,
        order: [],
        language: {
          emptyTable: "No hay información",
          info: "Mostrando _START_ a _END_ de _TOTAL_ Movimientos",
          infoEmpty: "Mostrando 0 a 0 de 0 Movimientos",
          infoFiltered: "(Filtrado de _MAX_ total Movimientos)",
          lengthMenu: "Mostrar _MENU_ Movimientos",
          loadingRecords: "Cargando...",
          processing: "Procesando...",
          search: "Buscador:",
          zeroRecords: "Sin resultados encontrados",
          paginate: { first: "Primero", last: "Ultimo", next: "Siguiente", previous: "Anterior" }
        },
        responsive: true,
        lengthChange: true,
        autoWidth: false,
        buttons: [{
          extend: 'collection',
          text: 'Reportes',
          orientation: 'landscape',
          buttons: [
            { text:'Copiar', extend:'copy' },
            { extend:'pdfHtml5',  filename: () => fileBaseName(), title: null },
            { extend:'csvHtml5',  filename: () => fileBaseName(), title: null },
            { extend:'excelHtml5',filename: () => fileBaseName(), title: null },
            { text:'Imprimir', extend:'print' }
          ]
        }]
      });
      dt.buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    }

    function refreshDT(html){
      if (dt) { dt.destroy(); }
      $('#example1 tbody').html(html);
      initDT();
    }

    // --- Carga inicial: últimos 20 ---
    const idPersona = $('#id_persona').val();
    $.post('../app/controllers/informes/listarkardexfiltro.php', {
      id_persona: idPersona,
      ultimos: 20
    }).done(function(html){
      refreshDT(html);
    }).fail(function(xhr){
      console.error('Error inicial Kardex:', xhr.responseText);
      $('#example1 tbody').html("<tr><td colspan='8' class='text-center text-danger'>No se pudo cargar los últimos movimientos</td></tr>");
      initDT(); // inicializar aunque falle para no romper UI
    });

    // --- Aplicar filtros ---
    $('#aplicarFiltros').on('click', function(){
      const fi = $('#fecha_inicio').val();
      const ff = $('#fecha_fin').val();
      $('#dateRangeSpan').text(`${formatDateSpan(fi)} - ${formatDateSpan(ff)}`);

      $.ajax({
        url: '../app/controllers/informes/listarkardexfiltro.php',
        type: 'POST',
        data: { fecha_inicio: fi, fecha_fin: ff, id_persona: idPersona },
        success: function(html){ refreshDT(html); },
        error: function(xhr, st, err){ console.error('AJAX error:', st, err); }
      });
    });

    // --- Imprimir ---
    const baseURL = '<?php echo $URL; ?>';
    $('#imprimir').on('click', function(){
      const fi=$('#fecha_inicio').val(), ff=$('#fecha_fin').val(), id=idPersona;
      window.open(baseURL + '/kardex/kardexprint.php?fecha_inicio='+fi+'&fecha_fin='+ff+'&id_persona='+id, '_blank');
    });
  });
</script>

<?php
include('../layout/parte2.php');
include('../layout/mensajes.php');
?>
