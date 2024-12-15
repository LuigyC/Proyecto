<?php
session_start();
require_once "Database/Database.php";

if ($_SESSION['username'] == null) {
    echo "<script>alert('Please login.');</script>";
    header("Refresh:0 , url=index.html");
    exit();
}

$username = $_SESSION['username'];

// Procesar formulario manual
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['name'])) {
    $name = strtolower(trim($_POST['name'])); // Convertir a minúsculas para comparación
    $amount = $_POST['amount'];
    $precio = $_POST['precio'];

    // Verificar si el producto ya existe
    $sql_check_product = "SELECT * FROM product WHERE LOWER(proname) = ?";
    $stmt = $conn->prepare($sql_check_product);
    $stmt->bind_param("s", $name);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Producto ya existe
        $existing_product = $result->fetch_assoc();
        echo "<script>alert('El producto \"" . $existing_product['proname'] . "\" ya existe en la base de datos. No se agregó.');</script>";
        header("Refresh:0 , url=addlist.php");
        exit();
    } else {
        // Insertar el producto
        $sql_insert_product = "INSERT INTO product (proname, amount, precio) VALUES (?, ?, ?)";
        $stmt_insert = $conn->prepare($sql_insert_product);
        $stmt_insert->bind_param("sid", $name, $amount, $precio);

        if ($stmt_insert->execute()) {
            echo "<script>alert('Producto agregado con éxito.');</script>";
            header("Refresh:0 , url=list.php");
        } else {
            echo "<script>alert('Error al agregar el producto.');</script>";
        }
    }
    $stmt->close();
    $stmt_insert->close();
}

// Obtener productos para la tabla
$sql_fetch_todos = "SELECT * FROM product ORDER BY id ASC";
$query = mysqli_query($conn, $sql_fetch_todos);
?>
<!doctype html>
<html lang="en">

<head>
    <title>AGREGAR PRODUCTOS</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="icon" href="faviconconfiguroweb.png">
    <link href="https://fonts.googleapis.com/css2?family=Mitr&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="add_list.css">
</head>

<body>
    <div class="header" style="position: fixed; z-index: 10;">
        <h3>GCH</h3>
        <a name="" id="" class="button-logout" href="logout.php" role="button">Cerrar Sesión</a>
    </div>
    <div class="container">
        <h1>Agregar Producto</h1>
        <h2>Has accedido como <?php echo strtoupper($username); ?></h2>
    </div>
    <!-- Formulario para agregar productos manualmente -->
    <div class="addproduct">
        <form method="POST" action="addlist.php">
            <div class="form-group">
                <label for="exampleInputEmail1">Nombre de Producto</label>
                <br>
                <input type="text" class="form-control" name="name" required>
            </div>
            <div class="form-group">
                <label for="exampleInputPassword1">Cantidad</label>
                <br>
                <input type="number" class="form-control" name="amount" required>
            </div>
            <div class="form-group">
                <label for="exampleInputPrice">Precio</label>
                <br>
                <input type="number" step="0.01" class="form-control" name="precio" required>
            </div>
            <br>
            <div class="form-button">
                <button type="submit" class="modify" style="float:right">Agregar Producto</button>
                <a name="" id="" class="return" href="list.php" role="button" style="float:left">Volver</a>
            </div>
        </form>
    </div>

    <!-- Formulario para subir archivo CSV -->
    <div class="upload-section">
        <form method="POST" action="upload_csv.php" enctype="multipart/form-data" class="upload-form">
            <div class="custom-file-input">
                <label for="csv_file" id="file-label">Seleccionar archivo</label>
                <input type="file" id="csv_file" name="csv_file" accept=".csv" required onchange="updateFileName()">
            </div>
            <button type="submit" class="upload-button">Subir y Procesar CSV</button>
        </form>
    </div>
    <div class="table-product">
        <table class="table">
            <thead class="thead-dark">
                <tr>
                    <th scope="col">Orden</th>
                    <th scope="col">ID:Producto</th>
                    <th scope="col">Nombre:Producto</th>
                    <th scope="col">Cantidades</th>
                    <th scope="col">Precio</th>
                    <th scope="col">Fecha:Registro</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $idpro = 1;
                while ($row = mysqli_fetch_array($query)) { ?>
                    <tr>
                        <td scope="row"><?php echo $idpro ?></td>
                        <td><?php echo $row['id'] ?></td>
                        <td><?php echo $row['proname'] ?></td>
                        <td><?php echo $row['amount'] ?></td>
                        <td><?php echo '$' . number_format($row['precio'], 0, ',', '.'); ?></td>
                        <td class="timeregis"><?php echo $row['time'] ?></td>
                    </tr>
                <?php
                    $idpro++;
                } ?>
            </tbody>
        </table>
        <br>
    </div>
    <?php mysqli_close($conn); ?>

    <!-- Script para cambiar el texto del botón al nombre del archivo seleccionado -->
    <script>
        function updateFileName() {
            const input = document.getElementById('csv_file');
            const label = document.getElementById('file-label');

            if (input.files.length > 0) {
                label.textContent = input.files[0].name; // Mostrar el nombre del archivo
            } else {
                label.textContent = "Seleccionar archivo"; // Restablecer si no hay archivo seleccionado
            }
        }
    </script>
</body>

</html>
