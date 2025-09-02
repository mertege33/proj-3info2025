<?php
require_once '../Model/TipoSanguineo.class.php';
require_once '../../../config/config.inc.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $idUsuario = $_POST['id_usuario'];

    $tipoSanguineo = new TipoSanguineo($db);
    $resultado = $tipoSanguineo->calcular($idUsuario);

    if (is_string($resultado)) {
        echo "<p>{$resultado}</p>";
    } else {
        echo "<h2>Resultados da Previsão</h2>";
        echo "<p>Tipo sanguíneo do pai: {$resultado['pai']}</p>";
        echo "<p>Tipo sanguíneo da mãe: {$resultado['mae']}</p>";
        echo "<p>Possíveis tipos sanguíneos do filho: " . implode(', ', $resultado['filho_tipos']) . "</p>";
        echo "<p>Possíveis fatores Rh do filho: " . implode(', ', $resultado['filho_rh']) . "</p>";
    }
}
?>
