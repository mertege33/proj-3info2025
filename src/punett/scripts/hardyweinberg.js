let hwChart = null;

function simulateHW() {
    let p = parseFloat(document.getElementById("p").value);
    const generations = parseInt(document.getElementById("generations").value);
    const mutAtoA = parseFloat(document.getElementById("mutAtoA").value); // μ: A → a
    const mutAtoa = parseFloat(document.getElementById("mutAtoa").value); // ν: a → A

    let AA = [], Aa = [], aa = [], labels = [], pArr = [], qArr = [];

    for (let gen = 0; gen < generations; gen++) {
        let q = 1 - p;
        AA.push((p * p).toFixed(3));
        Aa.push((2 * p * q).toFixed(3));
        aa.push((q * q).toFixed(3));
        labels.push(`G${gen+1}`);
        pArr.push(p.toFixed(3));
        qArr.push(q.toFixed(3));

        // Mutação: p' = p*(1-μ) + q*ν
        // q' = q*(1-ν) + p*μ
        const pNext = p * (1 - mutAtoA) + q * mutAtoa;
        p = pNext;
        // q = 1 - p; // recalculado no início do loop
    }

    // Gráfico
    const ctx = document.getElementById('hwChart').getContext('2d');
    if (hwChart) hwChart.destroy();
    hwChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'AA (p²)',
                    data: AA,
                    borderColor: '#388e3c',
                    backgroundColor: 'rgba(56,142,60,0.1)',
                    fill: false,
                    tension: 0.4
                },
                {
                    label: 'Aa (2pq)',
                    data: Aa,
                    borderColor: '#1976d2',
                    backgroundColor: 'rgba(25,118,210,0.1)',
                    fill: false,
                    tension: 0.4
                },
                {
                    label: 'aa (q²)',
                    data: aa,
                    borderColor: '#d32f2f',
                    backgroundColor: 'rgba(211,47,47,0.1)',
                    fill: false,
                    tension: 0.4
                },
                {
                    label: 'p (Alelo A)',
                    data: pArr,
                    borderColor: '#ff9800',
                    backgroundColor: 'rgba(255,152,0,0.1)',
                    fill: false,
                    borderDash: [5,5],
                    tension: 0.4,
                    yAxisID: 'y'
                },
                {
                    label: 'q (Alelo a)',
                    data: qArr,
                    borderColor: '#9c27b0',
                    backgroundColor: 'rgba(156,39,176,0.1)',
                    fill: false,
                    borderDash: [5,5],
                    tension: 0.4,
                    yAxisID: 'y'
                }
            ]
        },
        options: {
            responsive: false,
            plugins: {
                legend: { position: 'top' },
                title: {
                    display: true,
                    text: 'Frequências Genotípicas e Alélicas por Geração'
                }
            },
            scales: {
                y: { min: 0, max: 1 }
            }
        }
    });

    // Explicação dinâmica
    document.getElementById("hw-explanation").innerHTML = `
        <p>
            <strong>Explicação:</strong> Com mutação, as frequências alélicas mudam a cada geração.<br>
            Fórmula usada: p' = p*(1-μ) + q*ν<br>
            Onde μ é a taxa de mutação de A para a, e ν de a para A.<br>
            Veja como as frequências genotípicas e alélicas evoluem ao longo das gerações.
        </p>
    `;
}