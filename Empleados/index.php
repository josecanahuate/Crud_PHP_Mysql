<?php
//print_r($_POST);

$txtID = (isset($_POST['txtID'])) ? $_POST['txtID'] : "";
$txtNombre = (isset($_POST['txtNombre'])) ? $_POST['txtNombre'] : "";
$txtApellido_Materno = (isset($_POST['txtApellido_Materno'])) ? $_POST['txtApellido_Materno'] : "";
$txtApellido_Paterno = (isset($_POST['txtApellido_Paterno'])) ? $_POST['txtApellido_Paterno'] : "";
$txtCorreo = (isset($_POST['txtCorreo'])) ? $_POST['txtCorreo'] : "";
$txtFoto = (isset($_FILES['txtFoto']["name"])) ? $_FILES['txtFoto']["name"] : "";

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

        $Fecha = new DateTime();
        $nombreArchivo = ($txtFoto != "") ? $Fecha->getTimestamp() . "_" . $_FILES["txtFoto"]["name"] : "imgcrud.jpg";
        $tmpFoto = $_FILES["txtFoto"]["tmp_name"];
        if ($tmpFoto != "") {
            move_uploaded_file($tmpFoto, "../Imagenes/" . $nombreArchivo);
        }
        $sentencia->bindParam(':Foto', $nombreArchivo);

        //ejecuta la sentencia INSERT
        $sentencia->execute();
        //redireccionar a index.php | limpiar campos
        header('Location: index.php');
        break;

    case "btnModificar":
        $sentencia = $conn->prepare("UPDATE empleados SET 
        Nombre=:Nombre, 
        Apellido_Materno=:Apellido_Materno, 
        Apellido_Paterno=:Apellido_Paterno, 
        Correo=:Correo WHERE 
        ID=:ID");

        $sentencia->bindParam(':ID', $txtID);
        $sentencia->bindParam(':Nombre', $txtNombre);
        $sentencia->bindParam(':Apellido_Materno', $txtApellido_Materno);
        $sentencia->bindParam(':Apellido_Paterno', $txtApellido_Paterno);
        $sentencia->bindParam(':Correo', $txtCorreo);
        //ejecuta la sentencia INSERT
        $sentencia->execute();


        //actualizando la imagen
        $Fecha = new DateTime();
        $nombreArchivo = ($txtFoto != "") ? $Fecha->getTimestamp() . "_" . $_FILES["txtFoto"]["name"] : "imgcrud.jpg";
        $tmpFoto = $_FILES["txtFoto"]["tmp_name"];
        //si el usuario adjunto una imagen se inserta
        if ($tmpFoto != "") {
            move_uploaded_file($tmpFoto, "../Imagenes/" . $nombreArchivo);

            //Modificando imagen, eliminando img anterior
            $sentencia = $conn->prepare("SELECT Foto FROM empleados WHERE ID=:ID"); //seleccionando la imagen
            $sentencia->bindParam(':ID', $txtID);
            $sentencia->execute();
            $empleado = $sentencia->fetch(PDO::FETCH_LAZY); //devuelve la imagen

            //si hay un registro se verifica, y si existe se elimina la img de la carpeta
            if (isset($empleado["Foto"])) {
                if (file_exists("../Imagenes/" . $empleado["Foto"])) {
                    unlink("../Imagenes/" . $empleado["Foto"]);
                }
            }


            $sentencia = $conn->prepare("UPDATE empleados SET 
        Foto=:Foto WHERE ID=:ID");
            $sentencia->bindParam(':Foto', $nombreArchivo);
            $sentencia->bindParam(':ID', $txtID);
            $sentencia->execute();
        }

        //redireccionar a index.php | limpiar campos
        header('Location: index.php');
        break;

    case "btnEliminar":
        //eliminando imagen de la carpeta Imagenes
        $sentencia = $conn->prepare("SELECT Foto FROM empleados WHERE ID=:ID"); //seleccionando la imagen
        $sentencia->bindParam(':ID', $txtID);
        $sentencia->execute();
        $empleado = $sentencia->fetch(PDO::FETCH_LAZY); //devuelve la imagen

        //si hay un registro se verifica, y si existe se elimina la img de la carpeta
        if (isset($empleado["Foto"])) {
            if (file_exists("../Imagenes/" . $empleado["Foto"])) {
                unlink("../Imagenes/" . $empleado["Foto"]);
            }
        }

        $sentencia = $conn->prepare("DELETE FROM empleados WHERE 
        ID=:ID");
        $sentencia->bindParam(':ID', $txtID);
        //ejecuta la sentencia INSERT
        $sentencia->execute();

        //redireccionar a index.php | limpiar campos
        header('Location: index.php');
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

                <!-- Button trigger modal -->
                <button type="button" class="btn btn-primary mb-5" data-bs-toggle="modal" data-bs-target="#exampleModal">
                    Nuevo Registro
                </button>

                <!-- Modal -->
                <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5" id="exampleModalLabel">Nuevo Registro de Empleado</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">

                                <div class="form-row">
                                    <input type="hidden" class="form-control" value="<?php echo $txtID; ?>" name="txtID" id="txtID" placeholder="ID" required>

                                    <div class="col-md-12 mb-2">
                                        <label for="" class="mb-2 form-label">Nombre</label>
                                        <input type="text" class="form-control" name="txtNombre" value="<?php echo $txtNombre; ?>" id="txtNombre" placeholder="Nombre" required>
                                        <br>

                                        <label for="" class="mb-2 form-label">Apellido Materno</label>
                                        <input type="text" class="form-control" name="txtApellido_Materno" value="<?php echo $txtApellido_Materno; ?>" id="txtApellido_Materno" placeholder="Apellido Materno" required>
                                        <br>


                                        <label for="" class="mb-2 form-label">Apellido Paterno</label>
                                        <input type="text" class="form-control" name="txtApellido_Paterno" value="<?php echo $txtApellido_Paterno; ?>" id="txtApellido_Paterno" placeholder="Apellido Paterno" required>
                                        <br>

                                        <label for="" class="mb-2 form-label">Correo</label>
                                        <input type="email" class="form-control" name="txtCorreo" value="<?php echo $txtCorreo; ?>" id="txtCorreo" placeholder="name@example.com" required>
                                        <br>

                                        <label for="" class="mb-2 form-label">Foto</label>
                                        <input type="file" accept="image/*" class="form-control" name="txtFoto" value="<?php echo $txtFoto; ?>" id="txtFoto" required>
                                    </div>
                                </div>

                            </div>

                            <div class="modal-footer">
                                <div class="text-center mt-2 mb-4">
                                    <button value="btnAgregar" type="submit" class="btn btn-success mb-3" name="accion">Agregar</button>
                                    <button value="btnModificar" type="submit" class="btn btn-primary mb-3" name="accion">Modificar</button>
                                    <button value="btnEliminar" type="submit" class="btn btn-danger mb-3" name="accion">Eliminar</button>
                                    <button value="btnCancelar" type="submit" class="btn btn-warning mb-3" name="accion">Cancelar</button>
                                    <button type="button" class="btn btn-secondary mb-3" data-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="container">
        <div class="row">
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Nombre Completo</th>
                        <th scope="col">Correo</th>
                        <th scope="col">Foto</th>
                        <th scope="col">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($listaEmpleados as $empleado) { ?>
                        <tr>
                            <td><?php echo $empleado['ID']; ?></td>
                            <td><?php echo $empleado['Nombre'] . " " . $empleado['Apellido_Materno'] . " " . $empleado['Apellido_Paterno']; ?></td>
                            <td><?php echo $empleado['Correo']; ?></td>
                            <td>
                                <img class="img-thumbnail" width="100px" src="../Imagenes/<?php echo $empleado['Foto']; ?>" />
                            </td>
                            <td>
                                <form action="" method="POST">
                                    <input type="hidden" name="txtID" value="<?php echo $empleado['ID']; ?>">
                                    <input type="hidden" name="txtNombre" value="<?php echo $empleado['Nombre']; ?>">
                                    <input type="hidden" name="txtApellido_Materno" value="<?php echo $empleado['Apellido_Materno']; ?>">
                                    <input type="hidden" name="txtApellido_Paterno" value="<?php echo $empleado['Apellido_Paterno']; ?>">
                                    <input type="hidden" name="txtCorreo" value="<?php echo $empleado['Correo']; ?>">
                                    <input type="hidden" name="txtFoto" value="<?php echo $empleado['Foto']; ?>">

                                    <input type="submit" class="btn btn-warning" value="Editar" name="accion" href=""></input>
                                    <button value="btnEliminar" type="submit" class="btn btn-danger" name="accion">Eliminar</button>
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