<?php
$contraseña_ingresada = 'admin123';
$contraseña_guardada = '$2y$10$nvBpLdsCtdsNta19vmUy9OhZlQfcIHiU9FJRzEqAxIPBJ/sulEwOy'; // Copia la contraseña encriptada desde phpMyAdmin

if (password_verify($contraseña_ingresada, $contraseña_guardada)) {
    echo "Contraseña correcta";
} else {
    echo "Contraseña incorrecta";
}
?>
