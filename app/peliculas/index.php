<?php
    session_start();
    require("../config/database.php");

    $sqlPeliculas = "SELECT p.id, p.nombre, p.descripcion, g.nombre AS genero FROM pelicula AS p 
    INNER JOIN genero as g ON p.id_genero = g.id";
    $peliculas = $conn->query($sqlPeliculas);
    $dir = "posters/";

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD Modal</title>
    <link rel="stylesheet" href="../../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../assets/css/all.min.css">
</head>
<body>
    <div class="container py-3">
        <h2 class="text-center">Peliculas</h2>
        <hr>
        <?php if (isset($_SESSION['msg']) && isset($_SESSION['color'])) {?>
            <div class="alert alert-<?php echo $_SESSION['color']; ?> alert-dismissible fade show" role="alert">
            <?php echo $_SESSION['msg']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php 
            unset($_SESSION['msg']);
            unset($_SESSION['color']);
        }?>
        <div class="row justify-content-end">
            <div class="col-auto">
                <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#nuevoModal"><i class="fa-solid fa-circle-plus"></i> Nuevo registro</a>
            </div>
        </div>
        <table class="table table-sm table-striped table-hover mt-3">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Género</th>
                    <th>Poster</th>
                    <th>Acción</th>                    
                </tr>
            </thead>
            <tbody>
                <?php while($row_pelicula = $peliculas->fetch_assoc()) {?>
                    <tr>
                        <td><?php echo $row_pelicula['id']; ?></td>
                        <td><?php echo $row_pelicula['nombre']; ?></td>
                        <td><?php echo $row_pelicula['descripcion']; ?></td>
                        <td><?php echo $row_pelicula['genero']; ?></td>
                        <td><img src="<?= $dir.$row_pelicula['id'].'.jpg?n='.time();?>" width="100px" alt=""></td>
                        <td>
                            <a href="#" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-id="<?= $row_pelicula['id']; ?>" data-bs-target="#editarModal"><i class="fa-solid fa-pen-to-square"></i> Editar</a>
                            <a href="#" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-id="<?= $row_pelicula['id']; ?>" data-bs-target="#eliminaModal"><i class="fa-solid fa-trash"></i> Eliminar</a>
                        </td>
                    </tr>
                <?php }?>
            </tbody>
        </table>
    </div>

    <?php
        $sqlGenero = "SELECT id, nombre FROM  genero";
        $generos = $conn->query($sqlGenero);
    ?>

    <?php include("nuevoModal.php"); ?>
    <?php $generos->data_seek(0) ?>
    <?php include("editarModal.php"); ?>
    <?php include("eliminaModal.php"); ?>

    <script>
        let nuevoModal = document.getElementById('nuevoModal');
        let editarModal = document.getElementById('editarModal');
        let eliminaModal = document.getElementById('eliminaModal');

        nuevoModal.addEventListener('shown.bs.modal',event =>{
            nuevoModal.querySelector('.modal-body #nombre').focus();
        })

        // Limpiar modal de nuevo despues de cerrar
        nuevoModal.addEventListener('hide.bs.modal', event=>{
            nuevoModal.querySelector('.modal-body #nombre').value ="";
            nuevoModal.querySelector('.modal-body #descripcion').value ="";
            nuevoModal.querySelector('.modal-body #genero').value ="";
            nuevoModal.querySelector('.modal-body #poster').value ="";            
        })

        // Limpiar modal de editar despues de cerrar
        editarModal.addEventListener('hide.bs.modal', event=>{
            editarModal.querySelector('.modal-body #nombre').value ="";
            editarModal.querySelector('.modal-body #descripcion').value ="";
            editarModal.querySelector('.modal-body #genero').value ="";
            editarModal.querySelector('.modal-body #img_poster').value ="";
            editarModal.querySelector('.modal-body #poster').value ="";            
        })

        editarModal.addEventListener('shown.bs.modal',event=>{
            let button = event.relatedTarget;
            let id = button.getAttribute('data-bs-id');
            
            let inputId = editarModal.querySelector('.modal-body #id');
            let inputNombre = editarModal.querySelector('.modal-body #nombre');
            let inputDescripcion = editarModal.querySelector('.modal-body #descripcion');
            let inputGenero = editarModal.querySelector('.modal-body #genero');
            let poster = editarModal.querySelector('.modal-body #img_poster');

            let url = "getPelicula.php";
            let formData = new FormData();
            formData.append('id', id);

            fetch(url,{
                method:"POST",
                body:formData
            }).then(response => response.json())
            .then(data=>{
                inputId.value = data.id;
                inputNombre.value = data.nombre;
                inputDescripcion.value = data.descripcion;
                inputGenero.value = data.id_genero;
                poster.src = '<?= $dir ?>'+ data.id + '.jpg';
            }).catch(err => console.log(err))
        });

        eliminaModal.addEventListener('shown.bs.modal',event=>{
            let button = event.relatedTarget;
            let id = button.getAttribute('data-bs-id');
            eliminaModal.querySelector('.modal-footer #id').value = id;
        });
    </script>
    
    <script src="../../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>