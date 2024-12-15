<?php
session_start();
require_once "../Database/Database.php";

// Verificar que todos los campos estén completos
if (!empty($_POST['username']) && !empty($_POST['name']) && !empty($_POST['password']) && !empty($_POST['cfpassword'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $cfpassword = mysqli_real_escape_string($conn, $_POST['cfpassword']);
    
    // Verificar que las contraseñas coincidan
    if ($password === $cfpassword) {
        // Verificar si el usuario ya existe (insensible a mayúsculas y minúsculas)
        $checkUserQuery = "SELECT * FROM user WHERE LOWER(username) = LOWER('$username')";
        $checkCombinationQuery = "SELECT * FROM user WHERE LOWER(username) = LOWER('$username') AND LOWER(name) = LOWER('$name')";

        $userExists = $conn->query($checkUserQuery);
        $combinationExists = $conn->query($checkCombinationQuery);

        if ($userExists->num_rows > 0) {
            // Cierra la conexión antes de salir
            mysqli_close($conn);
            echo "<script>alert('El usuario ya existe')</script>";
            header("Refresh:0, url = member.html");
            exit();
        } elseif ($combinationExists->num_rows > 0) {
            // Cierra la conexión antes de salir
            mysqli_close($conn);
            echo "<script>alert('La combinación de usuario y nombre ya existe')</script>";
            header("Refresh:0, url = member.html");
            exit();
        } else {
            // Encriptar la contraseña
            $password = password_hash($password, PASSWORD_DEFAULT);

            // Insertar el nuevo usuario en la base de datos
            $sql = "INSERT INTO user (username, name, password) VALUES ('$username', '$name', '$password')";
            
            if ($conn->query($sql)) {
                mysqli_close($conn); // Cierra la conexión antes de salir
                echo "<script>alert('Registro exitoso')</script>";
                header("Refresh:0, url = ../index.html");
                exit();
            } else {
                // Mostrar el error específico de MySQL
                $error = mysqli_error($conn);
                mysqli_close($conn); // Cierra la conexión antes de salir
                echo "<script>alert('Error al registrar el usuario: $error')</script>";
                header("Refresh:0, url = member.html");
                exit();
            }
        }
    } else {
        mysqli_close($conn); // Cierra la conexión antes de salir
        echo "<script>alert('Las contraseñas no coinciden')</script>";
        header("Refresh:0, url = member.html");
        exit();
    }
} else {
    mysqli_close($conn); // Cierra la conexión antes de salir
    echo "<script>alert('Registro incompleto')</script>";
    header("Refresh:0, url = member.html");
    exit();
}
?>
