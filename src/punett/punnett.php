<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quadro de Punnett Interativo</title>
    <link rel="stylesheet" href="styles/global.css">
    <link rel="stylesheet" href="styles/punnett.css">
</head>
<body>
    <section class="container">
        <h2>Quadro de Punnett Interativo</h2>
        <form id="punnett-form">
            <label for="parent1">Genótipo da Mãe:</label>
            <select id="parent1">
                <option value="AA">AA</option>
                <option value="Aa">Aa</option>
                <option value="aa">aa</option>
            </select>
            <label for="parent2">Genótipo do Pai:</label>
            <select id="parent2">
                <option value="AA">AA</option>
                <option value="Aa">Aa</option>
                <option value="aa">aa</option>
            </select>
            <button type="button" class="button" onclick="generatePunnett()">Gerar Quadro</button>
        </form>
        <div id="punnett-result">
            <table border="1" cellpadding="5">
                <tr>
                    <th></th>
                    <th>-</th>
                    <th>-</th>
                </tr>
                <tr>
                    <th>-</th>
                    <td>-</td>
                    <td>-</td>
                </tr>
                <tr>
                    <th>-</th>
                    <td>-</td>
                    <td>-</td>
                </tr>
            </table>
        </div>
        <div style="text-align:center;margin-top:20px;">
            <a href="index.php" class="button">Voltar ao Menu</a>
        </div>
    </section>
    <script src="scripts/punnett.js"></script>
</body>
</html>