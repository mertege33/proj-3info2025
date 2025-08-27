<?php
// Conexão
$conn = new mysqli("localhost", "root", "", "BioLineage");

// Busca todos usuários para montar o formulário
$usuarios = $conn->query("SELECT id_usuario, nome FROM usuario");
?>

<form method="post">
    <label>Selecione o Pai:</label>
    <select name="pai">
        <?php while($row = $usuarios->fetch_assoc()) { ?>
            <option value="<?= $row['id_usuario'] ?>"><?= $row['nome'] ?></option>
        <?php } ?>
    </select><br><br>

    <?php
    // resetando o ponteiro para listar de novo
    $usuarios->data_seek(0);
    ?>

    <label>Selecione a Mãe:</label>
    <select name="mae">
        <?php while($row = $usuarios->fetch_assoc()) { ?>
            <option value="<?= $row['id_usuario'] ?>"><?= $row['nome'] ?></option>
        <?php } ?>
    </select><br><br>

    <button type="submit">Calcular orelha do filho</button>
</form>
121321
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pai = $_POST['pai'];
    $mae = $_POST['mae'];

    // Busca os perfis do pai e mãe
    $paiPerfil = $conn->query("SELECT tipo_orelha FROM perfil WHERE usuario_idusuario = $pai")->fetch_assoc();
    $maePerfil = $conn->query("SELECT tipo_orelha FROM perfil WHERE usuario_idusuario = $mae")->fetch_assoc();

    $resultado = "Indefinido";

    if ($paiPerfil['tipo_orelha'] === 'Sem divisão' && $maePerfil['tipo_orelha'] === 'Sem divisão') {
        $resultado = "Filho terá orelha presa (sem divisão)";
    } else {
        $resultado = "Filho terá orelha solta (com divisão)";
    }

    echo "<h3>Resultado: $resultado</h3>";
}
?>
