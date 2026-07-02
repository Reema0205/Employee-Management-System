const ctx = document.getElementById('reportChart');

new Chart(ctx, {
    type: 'doughnut',
    data: {
        labels: ['IT', 'HR', 'Finance', 'Others'],
        datasets: [{
            data: [25, 25, 25, 13],
            backgroundColor: [
                '#ff4b91',
                '#0d2be6',
                '#ffe53b',
                '#37c8f3'
            ],
            borderWidth: 0
        }]
    },
    options: {
        responsive: true,
        cutout: '30%',
        plugins: {
            legend: {
                position: 'right'
            }
        }
    }
});

function exportExcel(){
    alert("Excel Exported Successfully!");
}

function exportPDF(){
    alert("PDF Exported Successfully!");
}