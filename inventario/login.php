<?php
if (trim($_POST['username']) == null || trim($_POST['password']) == null) {
    echo "<script>alert('Por favor diligencia los campos correspondientes')</script>";
    header("Refresh:0 , url = index.html");
    exit();
} else {
    require_once "./Database/Database.php";
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];
    
    // Consulta para obtener la contraseña encriptada de la base de datos
    $sql = "SELECT username, password FROM user WHERE username = '$username'";
    $query = mysqli_query($conn, $sql);

    if ($query && mysqli_num_rows($query) > 0) {
        $result = mysqli_fetch_array($query, MYSQLI_ASSOC);
        
        // Verificar la contraseña ingresada con la contraseña encriptada almacenada
        if (password_verify($password, $result['password'])) {
            session_start();
            $_SESSION['username'] = $result['username'];
            header("Location: list.php");
            session_write_close();
        } else {
            echo "<script>alert('Usuario o Contraseña Inválida')</script>";
            header("Refresh:0, url = logout.php");
            exit();
        }
    } else {
        echo "<script>alert('Usuario o Contraseña Inválida')</script>";
        header("Refresh:0, url = logout.php");
        exit();
    }
}
mysqli_close($conn);
?>
