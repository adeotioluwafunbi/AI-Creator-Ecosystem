document.addEventListener('DOMContentLoaded', function() {

    // Copy insight text
    document.querySelectorAll('.eai-copy').forEach(btn => {
        btn.addEventListener('click', function() {
            const text = this.parentElement.querySelector('p').innerText;
            navigator.clipboard.writeText(text).then(() => alert('Insight copied!'));
        });
    });

    // Search filter
    const searchInput = document.getElementById('eai-search');
    searchInput.addEventListener('input', function() {
        const term = this.value.toLowerCase();
        document.querySelectorAll('.eai-insight-card').forEach(card => {
            card.style.display = card.querySelector('p').innerText.toLowerCase().includes(term) ? 'block' : 'none';
        });
    });

    // --- Charts ---
    const barCtx = document.getElementById('eai-bar-chart').getContext('2d');
    new Chart(barCtx, {
        type: 'bar',
        data: {
            labels: ['Posts', 'Users', 'Comments', 'Plugins'],
            datasets: [{
                label: 'Count',
                data: [
                    parseInt(document.querySelector('.eai-card:nth-child(1) .eai-card-value').innerText),
                    parseInt(document.querySelector('.eai-card:nth-child(2) .eai-card-value').innerText),
                    parseInt(document.querySelector('.eai-card:nth-child(3) .eai-card-value').innerText),
                    parseInt(document.querySelector('.eai-card:nth-child(4) .eai-card-value').innerText)
                ],
                backgroundColor: ['#00a32a','#0073aa','#f39c12','#dd4b39']
            }]
        },
        options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, precision:0 } } }
    });

    // Pie chart: insight categories
    const categories = {};
    document.querySelectorAll('.eai-insight-card').forEach(card => {
        const cat = card.dataset.category || 'General';
        categories[cat] = (categories[cat] || 0) + 1;
    });

    const pieCtx = document.getElementById('eai-pie-chart').getContext('2d');
    new Chart(pieCtx, {
        type: 'pie',
        data: { labels: Object.keys(categories), datasets: [{ data: Object.values(categories), backgroundColor: ['#00a32a','#0073aa','#f39c12','#dd4b39','#8e44ad'] }] },
        options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
    });
});
