<!DOCTYPE html>
<?php
require 'functions.php';
$permisos = ['Administrador','Profesor','Padre'];
permisos($permisos);

?>
<html>
<head>
<title>Inicio | Registro de Notas</title>
    <meta name="description" content="Sistema de Gestion de Notas" />
    <link rel="stylesheet" href="css/style.css" />

</head>
<body>

<nav>
    <ul>
        <li class="active"><a href="inicio.view.php">Inicio</a> </li>
        <li><a href="alumnos.view.php">Registro de Analistas</a> </li>
        <li><a href="listadoalumnos.view.php">Listado de Analistas</a> </li>
        <li><a href="notas.view.php">Registro de Notas</a> </li>
        <li><a href="listadonotas.view.php">Consulta de Notas</a> </li>
        
        <li class="right"><a href="logout.php">Salir</a> </li>
        <li class="right"> Usuario:  <?php echo $_SESSION["username"] ?></li>

    </ul>
</nav>

<div class="body">
    <div class="panel">
           <h1 class="text-center">Proyecto Pacifico Seguros</h1>
        <?php
        if(isset($_GET['err'])){
            echo '<h3 class="error text-center">ERROR: Usuario no autorizado</h3>';
        }
        ?>
        <br>
        <hr>
        <img class="logo" src="css/registro.png" alt="logoregistro"><br>
        <style>
img {
  display: block;
  margin-left: auto;
  margin-right: auto;
}
        </div>
        <p>Derechos reservados &copy; 2020</p>
</div>



</body>

</html>