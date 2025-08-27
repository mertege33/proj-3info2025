<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simulação Hardy-Weinberg</title>
    <link rel="stylesheet" href="styles/global.css">
    <link rel="stylesheet" href="styles/hardyweinberg.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <section class="container">
        <h2>Simulação de Genética de População (Hardy-Weinberg)</h2>
        <p>
            <strong>Lei de Hardy-Weinberg:</strong> Em uma população ideal, as frequências alélicas e genotípicas permanecem constantes de geração para geração, desde que não haja seleção, mutação, migração ou deriva genética.<br>
            <strong>Fórmulas:</strong> p + q = 1 &nbsp;&nbsp;|&nbsp;&nbsp; p² + 2pq + q² = 1<br>
            <strong>Genótipos:</strong> AA (p²), Aa (2pq), aa (q²)
        </p>
        <form id="hw-form">
            <label for="p">Frequência inicial do alelo A (<em>p</em>):</label>
            <input type="number" id="p" min="0" max="1" step="0.01" value="0.5">
            <label for="generations">Número de gerações:</label>
            <input type="number" id="generations" min="1" max="50" step="1" value="10">
            <label for="mutAtoA">Taxa de mutação de A para a (μ):</label>
            <input type="number" id="mutAtoA" min="0" max="1" step="0.0001" value="0.000">
            <label for="mutAtoa">Taxa de mutação de a para A (ν):</label>
            <input type="number" id="mutAtoa" min="0" max="1" step="0.0001" value="0.000">
            <button type="button" class="button" onclick="simulateHW()">Simular</button>
        </form>
        <div id="hw-result">
            <canvas id="hwChart" width="400" height="250"></canvas>
        </div>
        <div id="hw-explanation" style="margin-top:16px;"></div>
        <div style="text-align:center;margin-top:20px;">
            <a href="index.php" class="button">Voltar ao Menu</a>
        </div>
    </section>
    <script src="scripts/hardyweinberg.js"></script>
</body>
</html>