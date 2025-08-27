function getGametes(genotype) {
    if (genotype === "AA") return ["A"];
    if (genotype === "aa") return ["a"];
    if (genotype === "Aa" || genotype === "aA") return ["A", "a"];
    return [];
}

function sortGenotype(alleles) {
    return alleles.sort().join('');
}

function getExplanation(p1, p2) {
    if ((p1 === "AA" && p2 === "Aa") || (p1 === "Aa" && p2 === "AA")) {
        return `<p><strong>Explicação:</strong> O genótipo AA só produz gametas A. O genótipo Aa produz gametas A e a. Assim, metade dos descendentes será AA e metade será Aa.</p>`;
    }
    if (p1 === "AA" && p2 === "aa" || p1 === "aa" && p2 === "AA") {
        return `<p><strong>Explicação:</strong> AA só produz A, aa só produz a. Todos os descendentes serão Aa (100%).</p>`;
    }
    if (p1 === "Aa" && p2 === "Aa") {
        return `<p><strong>Explicação:</strong> Ambos produzem gametas A e a. As combinações possíveis são AA, Aa e aa, com proporção 1:2:1.</p>`;
    }
    if ((p1 === "aa" && p2 === "Aa") || (p1 === "Aa" && p2 === "aa")) {
        return `<p><strong>Explicação:</strong> aa só produz a, Aa produz A e a. Metade dos descendentes será Aa e metade será aa.</p>`;
    }
    if (p1 === "AA" && p2 === "AA") {
        return `<p><strong>Explicação:</strong> Ambos só produzem A. Todos os descendentes serão AA (100%).</p>`;
    }
    if (p1 === "aa" && p2 === "aa") {
        return `<p><strong>Explicação:</strong> Ambos só produzem a. Todos os descendentes serão aa (100%).</p>`;
    }
    return `<p><strong>Explicação:</strong> Escolha os genótipos dos pais para ver a explicação.</p>`;
}

function generatePunnett() {
    const parent1 = document.getElementById("parent1").value;
    const parent2 = document.getElementById("parent2").value;
    const gametes1 = getGametes(parent1);
    const gametes2 = getGametes(parent2);

    let results = {};
    let total = gametes1.length * gametes2.length;

    let table = `<table border="1" cellpadding="5"><tr><th></th>`;
    gametes2.forEach(g2 => table += `<th>${g2}</th>`);
    table += `</tr>`;

    gametes1.forEach(g1 => {
        table += `<tr><th>${g1}</th>`;
        gametes2.forEach(g2 => {
            let genotype = sortGenotype([g1, g2]);
            results[genotype] = (results[genotype] || 0) + 1;
            table += `<td>${genotype}</td>`;
        });
        table += `</tr>`;
    });
    table += `</table>`;

    let freq = `<h3>Frequências Genotípicas:</h3><ul>`;
    for (let genotype in results) {
        let percent = ((results[genotype] / total) * 100).toFixed(1);
        freq += `<li>${genotype}: ${results[genotype]} (${percent}%)</li>`;
    }
    freq += `</ul>`;

    let explanation = getExplanation(parent1, parent2);

    document.getElementById("punnett-result").innerHTML = explanation + table + freq;
}