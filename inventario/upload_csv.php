<?php
session_start();
require_once "Database/Database.php";

if ($_SESSION['username'] == null) {
    echo "<script>alert('Please login.');</script>";
    header("Refresh:0 , url=index.html");
    exit();
}

// Procesar el archivo CSV
if (isset($_FILES['csv_file']) && $_FILES['csv_file']['error'] == 0) {
    $filename = $_FILES['csv_file']['tmp_name'];

    if (($handle = fopen($filename, "r")) !== false) {
        $row = 0;
        $duplicated_products = []; // Array para guardar los nombres de productos duplicados

        while (($data = fgetcsv($handle, 1000, ",")) !== false) {
            // Ignorar la primera fila (cabecera)
            if ($row == 0) {
                $row++;
                continue;
            }

            $name = strtolower(trim($data[0])); // Nombre del producto en minúsculas
            $amount = (int)$data[1]; // Cantidad
            $precio = (float)$data[2]; // Precio

            // Verificar si el producto ya existe
            $sql_check_product = "SELECT * FROM product WHERE LOWER(proname) = ?";
            $stmt = $conn->prepare($sql_check_product);
            $stmt->bind_param("s", $name);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                // Si el producto ya existe, agregarlo a la lista de duplicados
                $existing_product = $result->fetch_assoc();
                $duplicated_products[] = $existing_product['proname'];
                continue;
            }

            // Insertar el producto si no es duplicado
            $sql_insert_product = "INSERT INTO product (proname, amount, precio) VALUES (?, ?, ?)";
            $stmt_insert = $conn->prepare($sql_insert_product);
            $stmt_insert->bind_param("sid", $name, $amount, $precio);
            $stmt_insert->execute();
        }

        fclose($handle);

        // Mostrar alerta con los productos duplicados si hay alguno
        if (!empty($duplicated_products)) {
            $duplicated_list = implode(", ", $duplicated_products);
            echo "<script>alert('Los siguientes productos ya existen en la base de datos y no se agregaron: $duplicated_list');</script>";
        } else {
            echo "<script>alert('Archivo CSV procesado exitosamente.');</script>";
        }

        header("Refresh:0 , url=addlist.php");
    } else {
        echo "<script>alert('Error al abrir el archivo CSV.');</script>";
        header("Refresh:0 , url=addlist.php");
    }
} else {
    echo "<script>alert('No se seleccionó un archivo válido.');</script>";
    header("Refresh:0 , url=addlist.php");
}

// Cerrar la conexión a la base de datos
$conn->close();
?>
