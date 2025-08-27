<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

$possibleDbPaths = [
    __DIR__ . '/../../DAO/Database.class.php',        
    __DIR__ . '/../DAO/Database.class.php',
    __DIR__ . '/../../../src/DAO/Database.class.php',
];

$dbIncluded = false;
foreach ($possibleDbPaths as $p) {
    if (file_exists($p)) {
        require_once $p;
        $dbIncluded = true;
        break;
    }
}
if (!$dbIncluded) {
    die("Database.class.php não encontrado. Procurei em:\n" . implode("\n", $possibleDbPaths));
}

function dbFetch($sql, $params = []) {
    $stmt = Database::executar($sql, $params);
    if ($stmt === false) return false;
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function dbFetchAll($sql, $params = []) {
    $stmt = Database::executar($sql, $params);
    if ($stmt === false) return [];
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// --- configuração ---
define('MAX_GEN', 6); // até quantas gerações subir (1=pais,2=avós,3=bisavós,...)
$uid = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0;
$treeNodes = [];   // nodes coletados
$visited = [];     // evitar loops (id_usuario já visitado)
$aggregate = [];   // nacionalidade => soma de pesos
$totalWeight = 0.0;
$usedNodesCount = 0;
$error = null;

// --- função recursiva para percorrer ancestrais ---
// OBS: a função chama traverseAncestors para o pai e para a mãe,
// portanto conta ambos os ramos (paterno e materno) e suas gerações.
function traverseAncestors($userId, $generation = 1) {
    global $treeNodes, $visited, $aggregate, $totalWeight, $usedNodesCount;

    if ($userId <= 0) return;
    if ($generation > MAX_GEN) return;
    if (isset($visited[$userId])) return; // evita ciclos
    $visited[$userId] = true;

    // Buscar perfil do usuário (nacionalidade, id_pai, id_mae)
    $profile = dbFetch("SELECT usuario_idusuario, id_pai, id_mae, nacionalidade FROM perfil WHERE usuario_idusuario = :uid LIMIT 1", [':uid' => $userId]);

    // Buscar nome (se existir) na tabela usuario para apresentar
    $userRow = dbFetch("SELECT id_usuario, nome FROM usuario WHERE id_usuario = :uid LIMIT 1", [':uid' => $userId]);

    $node = [
        'id' => $userId,
        'nome' => $userRow ? $userRow['nome'] : null,
        'nacionalidade' => $profile && trim($profile['nacionalidade'] ?? '') !== '' ? $profile['nacionalidade'] : null,
        'generation' => $generation,
        'id_pai' => $profile ? (int)$profile['id_pai'] : null,
        'id_mae' => $profile ? (int)$profile['id_mae'] : null,
    ];

    $treeNodes[] = $node;
    $usedNodesCount++;

    // se tiver nacionalidade, acumula com peso
    if ($node['nacionalidade']) {
        $weight = pow(0.5, $generation); // geração 1 -> 0.5, 2 -> 0.25, etc
        if (!isset($aggregate[$node['nacionalidade']])) $aggregate[$node['nacionalidade']] = 0.0;
        $aggregate[$node['nacionalidade']] += $weight;
        $totalWeight += $weight;
    }

    // recursivamente para pai e mãe
    if ($node['id_pai'] && $node['id_pai'] !== 0) traverseAncestors($node['id_pai'], $generation + 1);
    if ($node['id_mae'] && $node['id_mae'] !== 0) traverseAncestors($node['id_mae'], $generation + 1);
}

// --- execução principal ---
if ($uid > 0) {
    // primeiro: verificar se o usuário existe (na tabela usuario)
    $exists = dbFetch("SELECT id_usuario, nome FROM usuario WHERE id_usuario = :uid LIMIT 1", [':uid' => $uid]);
    if (!$exists) {
        $error = "Usuário com user_id = {$uid} não encontrado.";
    } else {
        // iniciar travessia a partir dos pais do usuário (geração 1)
        // para pegar pais, usamos o perfil do próprio usuário para descobrir id_pai / id_mae
        $perfilUsuario = dbFetch("SELECT id_pai, id_mae, nacionalidade FROM perfil WHERE usuario_idusuario = :uid LIMIT 1", [':uid' => $uid]);

        if ($perfilUsuario) {
            $pai = (int)$perfilUsuario['id_pai'];
            $mae = (int)$perfilUsuario['id_mae'];

            // se não existirem pais registrados, podemos tentar usar a nacionalidade do próprio perfil como fallback
            if (($pai <= 0 && $mae <= 0) && (!isset($perfilUsuario['nacionalidade']) || trim($perfilUsuario['nacionalidade']) === '')) {
                $error = "Nenhum ancestral encontrado (id_pai/id_mae ausentes) e sem nacionalidade no perfil do usuário.";
            } else {
                // percorrer cada pai/mãe se existir; geração 1
                if ($pai > 0) traverseAncestors($pai, 1);
                if ($mae > 0) traverseAncestors($mae, 1);

                // Caso não tenham nacionalidades nos ancestrais (totalWeight == 0) mas o próprio perfil tem nacionalidade,
                // fazer fallback: usar nacionalidade do próprio usuário com 100%
                if ($totalWeight == 0.0) {
                    if (isset($perfilUsuario['nacionalidade']) && trim($perfilUsuario['nacionalidade']) !== '') {
                        $aggregate[$perfilUsuario['nacionalidade']] = 1.0; // peso arbitrário antes de normalizar
                        $totalWeight = 1.0;
                        // também adicionar como node para exibição
                        $treeNodes[] = [
                            'id' => $uid,
                            'nome' => $exists['nome'] ?? null,
                            'nacionalidade' => $perfilUsuario['nacionalidade'],
                            'generation' => 0,
                            'id_pai' => null,
                            'id_mae' => null,
                        ];
                    } else {
                        // nenhum dado disponível
                        $error = "Nenhuma nacionalidade encontrada nos ancestrais, nem nacionalidade do próprio perfil.";
                    }
                }
            }
        } else {
            // sem perfil do usuário — podemos tentar mensagem mais amigável
            $error = "Perfil do usuário não encontrado. Para calcular ancestrais é preciso que exista um registro em `perfil` ligado ao usuário.";
        }
    }
}

// --- normalizar para porcentagens (100%) ---
$percentages = [];
if (!$error && $totalWeight > 0) {
    foreach ($aggregate as $country => $w) {
        $percentages[$country] = ($w / $totalWeight) * 100.0;
    }
    // ordernar por maior
    arsort($percentages);
}

// --- função para renderizar árvore HTML indentada (gera <ul>) ---
function renderTreeHTML($nodes) {
    $html = '<div class="tree-raw" style="margin-top:12px;">';
    $html .= '<h4>Árvore de ancestrais (visual)</h4>';
    $html .= '<div style="font-size:13px; color:#dfeff1;">';
    usort($nodes, function($a,$b){ return $a['generation'] <=> $b['generation']; });
    foreach ($nodes as $n) {
        $gen = (int)$n['generation'];
        $indent = max(0, $gen - 1) * 18;
        $name = $n['nome'] ? htmlspecialchars($n['nome']) : "Usuário #".htmlspecialchars($n['id']);
        $nat = $n['nacionalidade'] ? htmlspecialchars($n['nacionalidade']) : '<span style="opacity:0.7">— não informado —</span>';
        $html .= "<div style='padding-left:{$indent}px; margin-bottom:8px; display:flex; gap:10px; align-items:center;'>";
        $html .= "<div style='min-width:220px'><strong>{$name}</strong></div>";
        $html .= "<div style='width:160px'>Nacionalidade: {$nat}</div>";
        $html .= "<div style='color:#9bd; font-size:13px;'>Geração: ".($gen===0 ? 'º (próprio)' : $gen)."</div>";
        $html .= "</div>";
    }
    $html .= '</div></div>';
    return $html;
}

?><!doctype html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Origens — Simulador Multigeracional</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../../public/styles.css" />
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
  <style>
    .orig-card { max-width:1100px; margin:110px auto; padding:22px; border-radius:12px; background: rgba(10,10,10,0.6); }
    .controls { display:flex; gap:8px; margin-bottom:12px; align-items:center; }
    .country-item { display:flex; justify-content:space-between; padding:8px 0; border-bottom:1px dashed rgba(255,255,255,0.04); }
    .meta { color:#dfeff1; margin-top:10px; font-size:14px; }
  </style>
</head>
<body id="page-module-template">
  <header class="topbar" role="navigation">
    <nav class="nav-inner">
      <a href="../cadastro_menu_login/View/homescreen.html" class="nav-link">Home</a>
      <a href="/public/menu.html" class="nav-link">Menu</a>
      <a href="/public/tampletes.html" class="nav-link">Templates</a>
    </nav>
  </header>

  <div class="background-video" aria-hidden="true">
    <video autoplay loop muted playsinline>
      <source src="/public/218955.mp4" type="video/mp4" />
    </video>
  </div>
  <div class="overlay" aria-hidden="true"></div>

  <main>
    <div class="orig-card">
      <h2>Origens — cálculo multigeracional</h2>
      <p class="subtitle">Informe o <strong>user_id</strong> do usuário cujas origens você quer analisar. O sistema sobe até <strong><?= MAX_GEN ?></strong> gerações por padrão (pais → avós → bisavós ...).</p>

      <form method="get" class="controls" style="align-items:center;">
        <input name="user_id" placeholder="user_id (ex: 1)" value="<?= htmlspecialchars($uid > 0 ? $uid : '') ?>" style="padding:10px 12px; border-radius:8px;"/>
        <button class="btn primary" type="submit">Calcular</button>
        <a href="/public/menu.html" class="btn ghost">Voltar</a>
      </form>

      <?php if ($uid <= 0): ?>
        <div class="meta">Coloque um <strong>user_id</strong> e clique em <em>Calcular</em>.</div>
      <?php else: ?>
        <?php if ($error): ?>
          <div role="alert" style="color:#ffd1d1;"><?= htmlspecialchars($error) ?></div>
        <?php else: ?>
          <div style="display:flex; gap:20px; flex-wrap:wrap;">
            <div style="flex:1 1 420px;">
              <h3>Porcentagem estimada por nacionalidade</h3>
              <div class="meta">Baseado em <strong><?= $usedNodesCount ?></strong> nó(s) analisado(s) — pesos por geração (pais: 50%, avós: 25%, ...).</div>
              <div style="margin-top:12px;">
                <?php foreach ($percentages as $country => $pct): ?>
                  <div class="country-item"><div><?= htmlspecialchars($country) ?></div><div><?= number_format((float)$pct, 2, ',', '.') ?>%</div></div>
                <?php endforeach; ?>
              </div>

              <?= renderTreeHTML($treeNodes) ?>

            </div>

            <div style="width:360px; flex:0 0 360px; text-align:center;">
              <canvas id="pie" width="320" height="320" aria-label="Gráfico de pizza"></canvas>
              <div style="margin-top:12px; color:#dfeff1; font-size:13px;">Gráfico gerado a partir das porcentagens normalizadas.</div>
            </div>
          </div>

          <script>
            const breakdown = <?= json_encode($percentages, JSON_HEX_TAG | JSON_HEX_APOS | JSON_UNESCAPED_UNICODE) ?>;
            const labels = Object.keys(breakdown);
            const values = labels.map(l => parseFloat(breakdown[l].toFixed(2)));
            const ctx = document.getElementById('pie').getContext('2d');
            new Chart(ctx, {
              type: 'pie',
              data: { labels: labels, datasets: [{ data: values }] },
              options: { plugins: { legend: { position: 'bottom' } } }
            });
          </script>

        <?php endif; ?>
      <?php endif; ?>

    </div>
  </main>

  <script src="/public/app.js"></script>
</body>
</html>
