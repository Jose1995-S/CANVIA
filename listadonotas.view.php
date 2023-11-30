<!DOCTYPE html>
<?php
require 'functions.php';

$permisos = ['Administrador', 'Profesor', 'Padre'];
permisos($permisos);
//consulta las materias
$materias = $conn->prepare("select * from materias");
$materias->execute();
$materias = $materias->fetchAll();

//consulta de grados
$grados = $conn->prepare("select * from grados");
$grados->execute();
$grados = $grados->fetchAll();

//consulta las secciones
$secciones = $conn->prepare("select * from secciones");
$secciones->execute();
$secciones = $secciones->fetchAll();
?>
<html>

<head>
    <title>Notas | Registro de Notas</title>
    <meta name="description" content="Registro de Notas del Centro Escolar Profesor Lennin">
    <link rel="stylesheet" href="css/style.css">
    
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js">
  </script>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.3.2/jspdf.min.js">
  </script>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/2.0.37/jspdf.plugin.autotable.js">
  </script> 
</head>

<body>

    <nav>
        <ul>
        <li><a href="inicio.view.php">Inicio</a> </li>
        <li><a href="alumnos.view.php">Registro de Analistas</a> </li>
        <li><a href="listadoalumnos.view.php">Listado de Analistas</a> </li>
        <li><a href="notas.view.php">Registro de Notas</a> </li>
        <li class="active"><a href="listadonotas.view.php">Consulta de Notas</a> </li>
        <li class="right"><a href="logout.php">Salir</a> </li>
        <li class="right"> Usuario:  <?php echo $_SESSION["username"] ?></li>

        </ul>
    </nav>

    <div class="body">
        <div class="panel">
            <h3>Consulta de Notas</h3>
            <?php
            if (!isset($_GET['consultar'])) {
                ?>
                <p>Seleccione el area, examen  y nivel</p>
                <form method="get" class="form" action="listadonotas.view.php">
                    <label>Seleccione Area</label><br>
                    <select name="grado" required>
                        <?php foreach ($grados as $grado): ?>
                            <option value="<?php echo $grado['id'] ?>"><?php echo $grado['nombre'] ?></option>
                        <?php endforeach; ?>
                    </select>
                    <br><br>
                    <label>Seleccione Examen</label><br>
                    <select name="materia" required>
                        <?php foreach ($materias as $materia): ?>
                            <option value="<?php echo $materia['id'] ?>"><?php echo $materia['nombre'] ?></option>
                        <?php endforeach; ?>
                    </select>

                    <br><br>
                    <label>Seleccione nivel</label><br><br>

                    <?php foreach ($secciones as $seccion): ?>
                        <input type="radio" name="seccion" required value="<?php echo $seccion['id'] ?>"><?php echo $seccion['nombre'] ?>
                    <?php endforeach; ?>

                    <br><br>
                    <button type="submit" name="consultar" value="1">Consultar Notas</button></a>
                    <br><br>
                </form>
                <?php
            }
            ?>
            <hr>

            <?php
            if (isset($_GET['consultar'])) {
                $id_materia = $_GET['materia'];
                $id_grado = $_GET['grado'];
                $id_seccion = $_GET['seccion'];

                //extrayendo el numero de evaluaciones para esa materia seleccionada
                $num_eval = $conn->prepare("select num_evaluaciones from materias where id = " . $id_materia);
                $num_eval->execute();
                $num_eval = $num_eval->fetch();
                $num_eval = $num_eval['num_evaluaciones'];


                //mostrando el cuadro de notas de todos los alumnos del grado seleccionado
                $sqlalumnos = $conn->prepare("select a.id,  a.apellidos, a.nombres, b.nota,b.observaciones, avg(b.nota) as promedio from alumnos as a left join notas as b on a.id = b.id_alumno
 where id_grado = " . $id_grado . " and id_seccion = " . $id_seccion . " group by a.id");
                $sqlalumnos->execute();
                $alumnos = $sqlalumnos->fetchAll();
                $num_alumnos = $sqlalumnos->rowCount();
                $promediototal = 0.0;

                ?>
                <br>
                <a href="listadonotas.view.php"><strong>
                        << Volver</strong></a>
                <br>
                <br>

                <button onclick="generateExcel()">Excel</button>
  <button onclick="generatePDF()">PDF</button>
  <br />


<script>
    // Función para descargar un archivo PDF
    function descargarPDF() {
        // Reemplaza 'ruta/al/archivo.pdf' con la URL real del archivo PDF
        var urlPDF = 'ruta/al/archivo.pdf';
        window.open(urlPDF, '_blank');
    }

    // Función para descargar un archivo Excel
    function descargarExcel() {
        // Reemplaza 'ruta/al/archivo.xlsx' con la URL real del archivo Excel
        var urlExcel = 'ruta/al/archivo.xlsx';
        window.open(urlExcel, '_blank');
    }
</script>


                <table id="table_with_data" class="table table-striped table-bordered" cellspacing="0" width="100%">
                    <tr>
                        
                        <th>Apellidos</th>
                        <th>Nombres</th>
                        <?php
                        for ($i = 1; $i <= $num_eval; $i++) {
                            echo '<th>Nota ' . $i . '</th>';
                        }
                        ?>
                        <th>Promedio</th>
                        <th>Observaciones</th>
                    </tr>
                    <?php foreach ($alumnos as $index => $alumno): ?>
                        <!-- campos ocultos necesarios para realizar el insert-->
                        <tr>
                            
                            <td>
                                <?php echo $alumno['apellidos'] ?>
                            </td>
                            <td>
                                <?php echo $alumno['nombres'] ?>
                            </td>
                            <?php

                            //escribiendo las notas en columnas
                            $notas = $conn->prepare("select id, nota from notas where id_alumno = " . $alumno['id'] . " and id_materia = " . $id_materia);
                            $notas->execute();
                            $notas = $notas->fetchAll();

                            foreach ($notas as $eval => $nota) {

                                echo '<td align="center"><input type="hidden" 
                                            name="nota' . $eval . '" value="' . $nota['nota'] . '" >' . $nota['nota'] . '</td>';

                            }

                            echo '<td align="center">' . number_format($alumno['promedio'], 2) . '</td>';
                            //echo '<td><a href="notas.view.php?grado='.$id_grado.'&materia='.$id_materia.'&seccion='.$id_seccion.'">Editar</a> </td>';
                            $promediototal += number_format($alumno['promedio'], 2);
                            echo '<td>' . $alumno['observaciones'] . '</td>';
                            ?>

                        </tr>
                    <?php endforeach; ?>
                   
                </table>

                <br>


                <?php
            }
            ?>
       </div>
       

      
<script>
//export table to excel
function generateExcel() {
    //getting data from our table
    var data_type = 'data:application/vnd.ms-excel';
    var table_div = document.getElementById('table_with_data');
    var table_html = table_div.outerHTML.replace(/ /g, '%20');

    var a = document.createElement('a');
    a.href = data_type + ', ' + table_html;
    a.download = 'Example_Table_To_Excel.xls';
    a.click();
}


//export table to pdf
function generatePDF() {
  var doc = new jsPDF('l', 'pt');

  var elem = document.getElementById('table_with_data');
  var data = doc.autoTableHtmlToJson(elem);
  doc.autoTable(data.columns, data.rows, {
    margin: {left: 35},
    theme: 'grid',
    tableWidth: 'auto',
    fontSize: 8,
    overflow: 'linebreak',
    }
  );
    
  doc.save('Example_Table_To_PDF.pdf');
}
</script>
       
       <p>Derechos reservados &copy; 2020</p>
    </div>


</body>
<script>
    <?php
    for ($i = 0; $i < $num_eval; $i++) {
        echo 'var values' . $i . ' = [];
        var promedio' . $i . ';
    var valor' . $i . ' = 0;
    var nota' . $i . ' = document.getElementsByName("nota' . $i . '");
    for(var i = 0; i < nota' . $i . '.length; i++) {
        valor' . $i . ' += parseFloat(nota' . $i . '[i].value);
    }
    promedio' . $i . ' = (valor' . $i . ' / parseFloat(nota' . $i . '.length));
    document.getElementById("promedio' . $i . '").innerHTML = (promedio' . $i . ').toFixed(2);';

    }
    ?>
</script>

</html>