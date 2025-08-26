<?php
// tentar localizar config.inc.php em alguns caminhos plausíveis
$possible = [
    __DIR__ . '/../../../config/config.inc.php', // src/origens/View -> ../../.. -> config/
    __DIR__ . '/../config/config.inc.php',       // src/origens/View -> ../config (se houver)
    __DIR__ . '/config.inc.php',                 // mesmo diretório
    $_SERVER['DOCUMENT_ROOT'] . '/proj-3info2025/config/config.inc.php' // caminho absoluto no XAMPP
];

$configFound = false;
foreach ($possible as $p) {
    if (file_exists($p)) {
        require_once $p;
        $configFound = true;
        break;
    }
}

if (!$configFound) {
    // mensagem útil para desenvolvimento (não exponha em produção)
    die("Arquivo config.inc.php não encontrado. Verifique o caminho. Procurei em:\n" . implode("\n", $possible));
}


$pdo = null;
try {
    $pdo = new PDO(DSN, USUARIO, SENHA, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    die("Erro DB: " . htmlspecialchars($e->getMessage()));
}

$uid = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0;
$result = [
    'status' => 'idle',
    'message' => '',
    'breakdown' => [], // country => percent
    'raw' => [], // detalhe bruto
];

if ($uid > 0) {
    // buscar perfil do usuário
    $stmt = $pdo->prepare("SELECT usuario_idusuario, id_pai, id_mae, nacionalidade FROM perfil WHERE usuario_idusuario = :uid LIMIT 1");
    $stmt->execute([':uid' => $uid]);
    $perfil = $stmt->fetch();

    if (!$perfil) {
        $result['status'] = 'error';
        $result['message'] = "Perfil não encontrado para user_id = {$uid}";
    } else {
        $result['raw']['usuario'] = $perfil;
        $pais = [];

        // função auxiliar para pegar nacionalidade do perfil de um usuario
        $getNat = function($personId) use ($pdo) {
            if (!$personId) return null;
            $s = $pdo->prepare("SELECT nacionalidade FROM perfil WHERE usuario_idusuario = :pid LIMIT 1");
            $s->execute([':pid' => $personId]);
            $r = $s->fetch();
            return $r ? ($r['nacionalidade'] !== null && trim($r['nacionalidade']) !== '' ? $r['nacionalidade'] : null) : null;
        };

        $paiId = (int)$perfil['id_pai'];
        $maeId = (int)$perfil['id_mae'];
        $natPai = $paiId ? $getNat($paiId) : null;
        $natMae = $maeId ? $getNat($maeId) : null;

        $result['raw']['pai'] = ['id' => $paiId, 'nacionalidade' => $natPai];
        $result['raw']['mae'] = ['id' => $maeId, 'nacionalidade' => $natMae];

        // Regras simples:
        if ($natPai && $natMae) {
            // ambos têm nacionalidade
            if ($natPai === $natMae) {
                $break = [$natPai => 100.0];
            } else {
                $break = [$natPai => 50.0, $natMae => 50.0];
            }
            $result['status'] = 'ok';
            $result['message'] = "Calculado a partir de pai e mãe.";
            $result['breakdown'] = $break;
        } elseif ($natPai || $natMae) {
            // apenas um dos pais tem nacionalidade
            $knownNat = $natPai ?: $natMae;
            $break = [$knownNat => 50.0, 'Desconhecido' => 50.0];
            $result['status'] = 'ok';
            $result['message'] = "Apenas um pai/mãe com nacionalidade definida.";
            $result['breakdown'] = $break;
        } else {
            // nenhum dos pais com nacionalidade definida -> usar a nacionalidade do próprio perfil, se existir
            $selfNat = $perfil['nacionalidade'] !== null && trim($perfil['nacionalidade']) !== '' ? $perfil['nacionalidade'] : null;
            if ($selfNat) {
                $result['status'] = 'ok';
                $result['message'] = "Sem dados de pais — usando nacionalidade do próprio perfil.";
                $result['breakdown'] = [$selfNat => 100.0];
            } else {
                $result['status'] = 'error';
                $result['message'] = "Nenhuma nacionalidade encontrada em pais ou no próprio perfil.";
            }
        }
    }
}
?><!doctype html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Simulador simples de descendência</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="styles.css" />
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
  <style>
    /* pequenos ajustes locais para o card do simulador */
    .sim-card { max-width:820px; margin:110px auto; padding:22px; border-radius:12px; background: rgba(10,10,10,0.6); }
    .controls { display:flex; gap:8px; margin-bottom:12px; }
    .info { margin-top:12px; color:#dfeff1; }
    .country-item { display:flex; justify-content:space-between; padding:8px 0; border-bottom:1px dashed rgba(255,255,255,0.04); }
  </style>
</head>
<body id="page-module-template">
  <header class="topbar" role="navigation" aria-label="Navegação principal">
    <nav class="nav-inner">
      <a href="homescreen.html" class="nav-link">Home</a>
      <a href="menu.html" class="nav-link">Menu</a>
      <a href="tampletes.html" class="nav-link">Templates</a>
    </nav>
  </header>

  <div class="background-video" aria-hidden="true">
    <video autoplay loop muted playsinline>
      <source src="218955.mp4" type="video/mp4" />
    </video>
  </div>
  <div class="overlay" aria-hidden="true"></div>

  <main>
    <div class="sim-card">
      <h2>Simulador simples de descendência</h2>
      <p class="subtitle">Insira o <strong>user_id</strong> e clique em calcular. O sistema usa <em>perfil.nacionalidade</em> e os campos <em>id_pai</em> / <em>id_mae</em>.</p>

      <form method="get" class="controls" style="align-items:center;">
        <input name="user_id" placeholder="user_id (ex: 1)" value="<?= htmlspecialchars($uid > 0 ? $uid : '') ?>" style="padding:10px 12px; border-radius:8px;"/>
        <button class="btn primary" type="submit">Calcular</button>
        <a href="menu.html" class="btn ghost">Voltar</a>
      </form>

      <?php if ($uid > 0): ?>
        <?php if ($result['status'] === 'error'): ?>
          <div class="info" role="alert" style="color:#ffd1d1;"><?= htmlspecialchars($result['message']) ?></div>
        <?php else: ?>
          <div style="display:flex; gap:20px; flex-wrap:wrap;">
            <div style="flex:1 1 320px;">
              <h3>Resultado</h3>
              <div class="info"><?= htmlspecialchars($result['message']) ?></div>
              <div style="margin-top:12px;">
                <?php foreach ($result['breakdown'] as $country => $pct): ?>
                  <div class="country-item"><div><?= htmlspecialchars($country) ?></div><div><?= number_format((float)$pct, 2, ',', '.') ?>%</div></div>
                <?php endforeach; ?>
              </div>

              <h4 style="margin-top:12px;">Dados brutos</h4>
              <pre style="white-space:pre-wrap; background:rgba(0,0,0,0.3); padding:8px; border-radius:8px; font-size:13px;"><?= htmlspecialchars(json_encode($result['raw'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) ?></pre>
            </div>

            <div style="width:320px; flex:0 0 320px; text-align:center;">
              <canvas id="pie" width="300" height="300" aria-label="Gráfico de pizza"></canvas>
            </div>
          </div>

          <script>
            // dados do PHP para o gráfico
            const breakdown = <?= json_encode($result['breakdown'], JSON_HEX_TAG | JSON_HEX_APOS) ?>;
            const labels = Object.keys(breakdown);
            const values = labels.map(l => parseFloat(breakdown[l]));
            const ctx = document.getElementById('pie').getContext('2d');
            new Chart(ctx, {
              type: 'pie',
              data: { labels: labels, datasets: [{ data: values }] },
              options: { plugins: { legend: { position: 'bottom' } } }
            });
          </script>
        <?php endif; ?>
      <?php else: ?>
        <div class="info">Coloque um <strong>user_id</strong> e clique em <em>Calcular</em>.</div>
      <?php endif; ?>

    </div>
  </main>

  <script src="app.js"></script>
</body>
</html>
