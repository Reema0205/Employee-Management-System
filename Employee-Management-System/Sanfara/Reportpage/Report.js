let chart;
const ctx = document.getElementById("chart");

function createChart(title, dataValues) {
    if (chart) {
        chart.destroy();
    }

    
    const labels = ['IT', 'HR', 'Finance', 'Others'];
    const formattedLabels = labels.map((label, index) => `${label} (${dataValues[index]}%)`);

    chart = new Chart(ctx, {
        type: 'doughnut', 
        data: {
            labels: formattedLabels,
            datasets: [{
                data: dataValues,
                backgroundColor: [
                    '#f5158c', // IT
                    '#0d46b7', // HR
                    '#f3d531', // Finance
                    '#3ac5e4'  // Others
                ],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right', 
                    labels: {
                        boxWidth: 15,
                        padding: 15,
                        font: {
                            size: 12
                        }
                    }
                }
            },
            cutout: '40%' 
        }
    });

    document.getElementById("reportTitle").innerText = title + " Report Preview";
}


createChart("Employee", [25, 25, 25, 13]);

function changeReport(type) {
    
    document.querySelectorAll(".report").forEach(x => {
        x.classList.remove("active");
    });
    
    
    event.currentTarget.classList.add("active");

    if (type === "Employee") {
        createChart(type, [25, 25, 25, 13]);
    }
    if (type === "Attendance") {
        createChart(type, [40, 20, 25, 15]);
    }
    if (type === "Leave") {
        createChart(type, [15, 40, 20, 25]);
    }
    if (type === "Payroll") {
        createChart(type, [30, 30, 20, 20]);
    }
    if (type === "Performance") {
        createChart(type, [50, 15, 20, 15]);
    }
}

function exportPDF() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();
    doc.text("Report Dashboard", 20, 20);
    doc.save("Report.pdf");
}

function exportExcel() {
    let data = [
        ["Department", "Value"],
        ["IT", 25],
        ["HR", 25],
        ["Finance", 25],
        ["Others", 13]
    ];

    let ws = XLSX.utils.aoa_to_sheet(data);
    let wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, "Report");
    XLSX.writeFile(wb, "Report.xlsx");
}