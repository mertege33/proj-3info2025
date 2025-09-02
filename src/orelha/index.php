<?php
$mysqli = new mysqli("localhost", "root", "", "BioLineage");
if ($mysqli->connect_error) {
    die("Falha na conexão: " . $mysqli->connect_error);
}

$usuarios = $mysqli->query("SELECT id_usuario, nome FROM usuario ORDER BY nome");
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Probabilidade Genética – Tipo de Orelha</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="style.css">

</head>
<body>
  <h2>Probabilidade Genética – Orelha Presa (Sem divisão) vs Solta (Com divisão)</h2>

  <form method="post">
    <label for="pai">Selecione o Pai:</label>
    <select name="pai" id="pai" required>
      <option value="">-- escolha --</option>
      <?php if ($usuarios) { while($u = $usuarios->fetch_assoc()) { ?>
        <option value="<?= $u['id_usuario'] ?>"><?= htmlspecialchars($u['nome']) ?></option>
      <?php } } ?>
    </select>

    <?php
      if ($usuarios) $usuarios->data_seek(0);
    ?>

    <label for="mae">Selecione a Mãe:</label>
    <select name="mae" id="mae" required>
      <option value="">-- escolha --</option>
      <?php if ($usuarios) { while($u = $usuarios->fetch_assoc()) { ?>
        <option value="<?= $u['id_usuario'] ?>"><?= htmlspecialchars($u['nome']) ?></option>
      <?php } } ?>
    </select>

    <div style="margin-top:12px;">
      <button type="submit">Calcular probabilidade</button>
    </div>
  </form>

<?php
function calcularProbabilidade($fenotipoPai, $fenotipoMae) {
    $fenPai = $fenotipoPai;
    $fenMae = $fenotipoMae;

    $fenPai = trim($fenPai);
    $fenMae = trim($fenMae);

    $isPaiSem = ($fenPai === 'Sem divisão');
    $isMaeSem = ($fenMae === 'Sem divisão');

    if ($isPaiSem && $isMaeSem) {
        $sem_assum = 100; $sem_min = 100; $sem_max = 100;
    }
    else if (($isPaiSem && !$isMaeSem) || (!$isPaiSem && $isMaeSem)) {
        // Assumimos pai/mãe dominante como heterozigoto (Ee)
        $sem_assum = 50;       // Ee x ee => 50% Sem divisão
        $sem_min = 0;          // Se dominante for E
        $sem_max = 50;         // Se dominante for Ee
    }
    // 3) E_ x E_
    else {
        // Assumimos ambos heterozigotos (Ee x Ee)
        $sem_assum = 25;       // 25% ee
        $sem_min = 0;          // se houver pelo menos um EE, pode cair a 0%
        $sem_max = 25;         // no caso Ee x Ee
    }

    $com_assum = 100 - $sem_assum;
    $com_min   = 100 - $sem_max;
    $com_max   = 100 - $sem_min;

    return [
        'assumido_sem' => $sem_assum,
        'faixa_sem'    => [$sem_min, $sem_max],
        'assumido_com' => $com_assum,
        'faixa_com'    => [$com_min, $com_max],
    ];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['pai']) && !empty($_POST['mae'])) {
    $paiId = (int) $_POST['pai'];
    $maeId = (int) $_POST['mae'];

    // Busca fenótipo (tipo_orelha) dos perfis
    $qPai = $mysqli->query("SELECT tipo_orelha, usuario_idusuario FROM perfil WHERE usuario_idusuario = $paiId LIMIT 1");
    $qMae = $mysqli->query("SELECT tipo_orelha, usuario_idusuario FROM perfil WHERE usuario_idusuario = $maeId LIMIT 1");

    if ($qPai && $qMae && $qPai->num_rows && $qMae->num_rows) {
        $pai = $qPai->fetch_assoc();
        $mae = $qMae->fetch_assoc();

        $res = calcularProbabilidade($pai['tipo_orelha'], $mae['tipo_orelha']);

        // Busca nomes para exibir
        $np = $mysqli->query("SELECT nome FROM usuario WHERE id_usuario = $paiId")->fetch_assoc()['nome'] ?? "Pai";
        $nm = $mysqli->query("SELECT nome FROM usuario WHERE id_usuario = $maeId")->fetch_assoc()['nome'] ?? "Mãe";

        echo '<div class="card result">';
        echo "<strong>Pai:</strong> ".htmlspecialchars($np)." — <em>{$pai['tipo_orelha']}</em><br>";
        echo "<strong>Mãe:</strong> ".htmlspecialchars($nm)." — <em>{$mae['tipo_orelha']}</em>";

        echo "<table>
                <tr>
                  <th></th>
                  <th>Assumido*</th>
                  <th>Faixa possível</th>
                </tr>
                <tr>
                  <td>Filho com <strong>orelha presa</strong> (Sem divisão)</td>
                  <td>{$res['assumido_sem']}%</td>
                  <td>{$res['faixa_sem'][0]}% a {$res['faixa_sem'][1]}%</td>
                </tr>
                <tr>
                  <td>Filho com <strong>orelha solta</strong> (Com divisão)</td>
                  <td>{$res['assumido_com']}%</td>
                  <td>{$res['faixa_com'][0]}% a {$res['faixa_com'][1]}%</td>
                </tr>
              </table>";

        echo '<p class="small">* Assumimos que qualquer genótipo dominante desconhecido (fenótipo "Com divisão") é heterozigoto (Ee). ';
        echo 'A faixa mostra o mínimo e máximo possíveis caso um ou ambos dominantes sejam homozigotos (EE).</p>';
        echo '</div>';
    } else {
        echo '<div class="card">Não encontrei perfil de Pai e/ou Mãe selecionados.</div>';
    }
}
?>
</body>
</html>
