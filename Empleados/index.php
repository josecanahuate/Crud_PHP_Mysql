<?php
//print_r($_POST);

$txtID = (isset($_POST['txtID'])) ? $_POST['txtID'] : "";
$txtNombre = (isset($_POST['txtNombre'])) ? $_POST['txtNombre'] : "";
$txtApellido_Materno = (isset($_POST['txtApellido_Materno'])) ? $_POST['txtApellido_Materno'] : "";
$txtApellido_Paterno = (isset($_POST['txtApellido_Paterno'])) ? $_POST['txtApellido_Paterno'] : "";
$txtCorreo = (isset($_POST['txtCorreo'])) ? $_POST['txtCorreo'] : "";
$txtFoto = (isset($_POST['txtFoto'])) ? $_POST['txtFoto'] : "";

$accion = (isset($_POST['accion'])) ? $_POST['accion'] : "";

//incluir la conexion de php y mysql
include("../conexion/conexion.php");

switch ($accion) {
    case "btnAgregar":

        $sentencia = $conn->prepare("INSERT INTO empleados(Nombre, Apellido_Materno, Apellido_Paterno, Correo, Foto) 
        VALUES (:Nombre, :Apellido_Materno, :Apellido_Paterno, :Correo, :Foto)");

        $sentencia->bindParam(':Nombre', $txtNombre);
        $sentencia->bindParam(':Apellido_Materno', $txtApellido_Materno);
        $sentencia->bindParam(':Apellido_Paterno', $txtApellido_Paterno);
        $sentencia->bindParam(':Correo', $txtCorreo);
        $sentencia->bindParam(':Foto', $txtFoto);

        //ejecuta la sentencia INSERT
        $sentencia->execute();
        break;

    case "btnModificar":

        $sentencia = $conn->prepare("UPDATE empleados SET 
        Nombre=:Nombre, 
        Apellido_Materno=:Apellido_Materno, 
        Apellido_Paterno=:Apellido_Paterno, 
        Correo=:Correo, 
        Foto=:Foto WHERE 
        ID=:ID");

        $sentencia->bindParam(':ID', $txtID);
        $sentencia->bindParam(':Nombre', $txtNombre);
        $sentencia->bindParam(':Apellido_Materno', $txtApellido_Materno);
        $sentencia->bindParam(':Apellido_Paterno', $txtApellido_Paterno);
        $sentencia->bindParam(':Correo', $txtCorreo);
        $sentencia->bindParam(':Foto', $txtFoto);

        //ejecuta la sentencia INSERT
        $sentencia->execute();

        //redireccionar a index.php | limpiar campos
        header('Location: index.php');


        break;

    case "btnEliminar":
        echo $txtID;
        echo "Presionaste btnEliminar";
        break;

    case "btnCancelar":
        echo $txtID;
        echo "Presionaste btnCancelar";
        break;
}

//MOSTRANDO TODOS LOS REGISTROS
$sentencia = $conn->prepare("SELECT * FROM `empleados` WHERE 1");
$sentencia->execute();
$listaEmpleados = $sentencia->fetchAll(PDO::FETCH_ASSOC); //MOSTRANDO REGISTROS EN UN ARREGLO

//print_r($listaEmpleados);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body>
    <div class="container">
        <div class="row mt-4">
            <form action="" method="POST" enctype="multipart/form-data">
                <h1 class="text-center mb-5">CRUD PHP + MYSQL</h1>
                <div class="row">
                    <div class="col-lg-6">
                        <label for="" class="mb-2 form-label">ID</label>
                        <input type="text" class="form-control" value="<?php echo $txtID; ?>" name="txtID" id="txtID" placeholder="ID" require="">
                        <br>

                        <label for="" class="mb-2 form-label">Nombre</label>
                        <input type="text" class="form-control" name="txtNombre" value="<?php echo $txtNombre; ?>" id="txtNombre" placeholder="Nombre" require="">
                        <br>

                        <label for="" class="mb-2 form-label">Apellido Materno</label>
                        <input type="text" class="form-control" name="txtApellido_Materno" value="<?php echo $txtApellido_Materno; ?>" id="txtApellido_Materno" placeholder="Apellido Materno" require="">
                        <br>
                    </div>

                    <div class="col-lg-6">
                        <label for="" class="mb-2 form-label">Apellido Paterno</label>
                        <input type="text" class="form-control" name="txtApellido_Paterno" value="<?php echo $txtApellido_Paterno; ?>" id="txtApellido_Paterno" placeholder="Apellido Paterno" require="">
                        <br>

                        <label for="" class="mb-2 form-label">Correo</label>
                        <input type="email" class="form-control" name="txtCorreo" value="<?php echo $txtCorreo; ?>" id="txtCorreo" placeholder="name@example.com" require="">
                        <br>

                        <label for="" class="mb-2 form-label">Foto</label>
                        <input type="text" class="form-control" name="txtFoto" value="<?php echo $txtFoto; ?>" id="txtFoto" placeholder="Foto" require="">
                        <br>
                    </div>
                </div>

                <div class="text-center mt-2 mb-4">
                    <button value="btnAgregar" type="submit" class="btn btn-success mb-3" name="accion">Agregar</button>
                    <button value="btnModificar" type="submit" class="btn btn-primary mb-3" name="accion">Modificar</button>
                    <button value="btnEliminar" type="submit" class="btn btn-danger mb-3" name="accion">Eliminar</button>
                    <button value="btnCancelar" type="submit" class="btn btn-warning mb-3" name="accion">Cancelar</button>
                </div>
        </div>
        </form>

        <div class="row">
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">Nombre Completo</th>
                        <th scope="col">Correo</th>
                        <th scope="col">Foto</th>
                        <th scope="col">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($listaEmpleados as $empleado) { ?>
                        <tr>
                            <td><?php echo $empleado['Nombre'] . " " . $empleado['Apellido_Materno'] . " " . $empleado['Apellido_Paterno']; ?></td>
                            <td><?php echo $empleado['Correo']; ?></td>
                            <td><?php echo $empleado['Foto']; ?></td>
                            <td>
                                <form action="" method="POST">
                                    <input type="hidden" name="txtID" value="<?php echo $empleado['ID']; ?>">
                                    <input type="hidden" name="txtNombre" value="<?php echo $empleado['Nombre']; ?>">
                                    <input type="hidden" name="txtApellido_Materno" value="<?php echo $empleado['Apellido_Materno']; ?>">
                                    <input type="hidden" name="txtApellido_Paterno" value="<?php echo $empleado['Apellido_Paterno']; ?>">
                                    <input type="hidden" name="txtCorreo" value="<?php echo $empleado['Correo']; ?>">
                                    <input type="hidden" name="txtFoto" value="<?php echo $empleado['Foto']; ?>">

                                    <input type="submit" class="btn btn-warning" value="Editar" name="accion" href=""></input>

                                    <!-- <a type="submit" class="btn btn-danger" href="">Eliminar</a> -->
                                </form>
                            </td>
                        </tr>

                    <?php    } ?>
                </tbody>
            </table>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>