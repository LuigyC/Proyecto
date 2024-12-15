<?php
session_start();
require_once "Database/Database.php";

// Verificar que el usuario haya iniciado sesión
if (!isset($_SESSION['username']) || $_SESSION['username'] == null) {
    echo "<script>alert('Por favor, inicie sesión.');</script>";
    header("Refresh:0 , url=index.html");
    exit();
}

$username = $_SESSION['username'];

// Inicializar variable de búsqueda y ordenamiento
$search_term = isset($_GET['search']) ? trim($_GET['search']) : '';
$order_by = isset($_GET['column']) ? $_GET['column'] : 'id';
$order_dir = isset($_GET['order']) && $_GET['order'] === 'desc' ? 'desc' : 'asc';

// Validar las columnas permitidas para evitar inyección SQL
$allowed_columns = ['id', 'proname', 'amount', 'precio', 'time'];
if (!in_array($order_by, $allowed_columns)) {
    $order_by = 'id';
}

// Construir consulta SQL con búsqueda y ordenamiento
$sql_fetch_todos = "SELECT * FROM product";
if ($search_term !== '') {
    $sql_fetch_todos .= " WHERE id LIKE '%$search_term%' OR proname LIKE '%$search_term%'";
}
$sql_fetch_todos .= " ORDER BY $order_by $order_dir";

$query = mysqli_query($conn, $sql_fetch_todos);

// Verificar si la consulta se ejecutó correctamente
if (!$query) {
    die("<p style='color: red;'>Error en la consulta SQL: " . mysqli_error($conn) . "</p>");
}

// Alternar dirección de orden
$next_order_dir = $order_dir === 'asc' ? 'desc' : 'asc';
?>

<!doctype html>
<html lang="es">

<head>
    <title>LISTADO DE PRODUCTOS</title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="icon" href="faviconconfiguroweb.png">
    <link href="https://fonts.googleapis.com/css2?family=Mitr&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles_list.css"> <!-- Agregamos el archivo CSS -->
</head>

<body>
    <div class="header" style="position: fixed; z-index: 10;">
        <h3>GCH</h3>
        <a name="" id="" class="button-logout" href="logout.php" role="button">Cerrar Sesión</a>
    </div>
    
    <div class="container">
        <h1>Listado de Productos</h1>
        <h4>Sesión iniciada como <?php echo strtoupper($username); ?></h4>
    </div>
    
    <!-- Barra de búsqueda -->
    <div class="search-container">
        <form method="GET" action="">
            <input type="text" name="search" placeholder="Buscar por ID o nombre" value="<?php echo htmlspecialchars($search_term); ?>">
            <button type="submit">Buscar</button>
        </form>
    </div>

    <a class="Addlist" style="float:right" href="addlist.php" role="button">Agregar Producto</a>
    <br>
    <br>
    <div class="table-product">
        <table>
            <tr>
                <th scope="col">Orden</th>
                <th scope="col">
                    <a href="?column=id&order=<?php echo $next_order_dir; ?>">ID:Producto 
                        <?php echo $order_by === 'id' ? ($order_dir === 'asc' ? '▲' : '▼') : ''; ?>
                    </a>
                </th>
                <th scope="col">
                    <a href="?column=proname&order=<?php echo $next_order_dir; ?>">Nombre:Producto 
                        <?php echo $order_by === 'proname' ? ($order_dir === 'asc' ? '▲' : '▼') : ''; ?>
                    </a>
                </th>
                <th scope="col">
                    <a href="?column=amount&order=<?php echo $next_order_dir; ?>">Cantidades 
                        <?php echo $order_by === 'amount' ? ($order_dir === 'asc' ? '▲' : '▼') : ''; ?>
                    </a>
                </th>
                <th scope="col">
                    <a href="?column=precio&order=<?php echo $next_order_dir; ?>">Precio Und 
                        <?php echo $order_by === 'precio' ? ($order_dir === 'asc' ? '▲' : '▼') : ''; ?>
                    </a>
                </th>
                <th scope="col">
                    <a href="?column=time&order=<?php echo $next_order_dir; ?>">Fecha:Registro 
                        <?php echo $order_by === 'time' ? ($order_dir === 'asc' ? '▲' : '▼') : ''; ?>
                    </a>
                </th>
                <th scope="col">Editar</th>
                <th scope="col">Eliminar</th>
            </tr>
            <tbody>
                <?php
                $idpro = 1;
                while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) { ?>
                    <tr>
                        <td scope="row"><?php echo $idpro; ?></td>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo $row['proname']; ?></td>
                        <td><?php echo $row['amount']; ?></td>
                        <td><?php echo '$' . number_format($row['precio'], 0, ',', '.'); ?></td>
                        <td class="timeregis"><?php echo $row['time']; ?></td>
                        <td class="modify"><a class="bfix" href="fix.php?id=<?php echo $row['id'] ?>&message=<?php echo $row['proname'] ?>&amount=<?php echo $row['amount'] ?>&precio=<?php echo $row['precio']; ?>" role="button">
                                Editar
                            </a></td>
                        <td class="delete">
                            <a class="bdelete" href="javascript:void(0);" 
                               onclick="confirmDelete('<?php echo $row['id']; ?>', '<?php echo $row['proname']; ?>')" role="button">
                                Eliminar
                            </a>
                        </td>
                    </tr>
                <?php
                    $idpro++;
                } ?>
            </tbody>
        </table>
    </div>
    
    <?php
    // Cerrar la conexión a la base de datos
    mysqli_close($conn);
    ?>
    
    <!-- Script para confirmación de eliminación -->
    <script>
        function confirmDelete(productId, productName) {
            // Mostrar alerta personalizada
            const userConfirmed = confirm(`¿Está seguro de que desea eliminar el producto "${productName}"?`);
            if (userConfirmed) {
                // Redirigir a la URL de eliminación si el usuario confirma
                window.location.href = `main/delete.php?id=${productId}`;
            }
            // Si el usuario cancela, no se hace nada
        }
    </script>
</body>

</html>
