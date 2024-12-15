<?php
session_start();
require_once "../Database/Database.php";

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['username']) || $_SESSION['username'] == null) {
    echo "<script>alert('Por favor, inicie sesión.');</script>";
    header("Refresh:0 , url = ../index.html");
    exit();
}

// Procesar el formulario cuando se envían los datos
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!empty($_POST['name']) && !empty($_POST['amount']) && !empty($_POST['precio'])) {
        // Preparar la consulta de inserción
        $sql = "INSERT INTO product (proname, amount, precio) VALUES ('" . trim($_POST['name']) . "', '" . trim($_POST['amount']) . "', '" . trim($_POST['precio']) . "')";
        
        // Ejecutar la consulta
        if ($conn->query($sql)) {
            echo "<script>alert('Producto agregado exitosamente');</script>";
            header("Refresh:0 , url = ../list.php");
            exit();
        } else {
            echo "<script>alert('Error al agregar el producto');</script>";
            header("Refresh:0 , url = ../list.php");
            exit();
        }
    } else {
        echo "<script>alert('Por favor, complete toda la información.');</script>";
        header("Refresh:0 , url = ../list.php");
        exit();
    }
}

// Cerrar la conexión a la base de datos
mysqli_close($conn);
?>

<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Producto</title>
    <link rel="stylesheet" href="add_list.css"> <!-- Enlace al archivo CSS externo -->
</head>
<body>
    <div class="container">
        <h1>Agregar Producto</h1>
        <form action="addlist.php" method="post">
            <div class="form-group">
                <label for="name">Nombre del Producto:</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="amount">Cantidad:</label>
                <input type="number" id="amount" name="amount" required>
            </div>
            <div class="form-group">
                <label for="precio">Precio:</label>
                <input type="number" id="precio" name="precio" step="0.01" required>
            </div>
            <button type="submit" class="btn-submit">Agregar Producto</button>
        </form>
    </div>
</body>
</html>
