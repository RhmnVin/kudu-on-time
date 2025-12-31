<canvas id="workloadChart"></canvas>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
new Chart(document.getElementById('workloadChart'), {
    type: 'bar',
    data: {
        labels: ['Semester 1','Semester 2'],
        datasets: [{
            label: 'Total Jam',
            data: [40, 65]
        }]
    }
});
</script>
