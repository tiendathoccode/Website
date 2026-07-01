document.addEventListener("DOMContentLoaded", function () {
    const chartCanvas = document.getElementById("salesTrendsChart");
    if (!chartCanvas) {
        return;
    }

    const ctx = chartCanvas.getContext("2d");
    const gradient = ctx.createLinearGradient(0, 0, 0, 300);
    gradient.addColorStop(0, "rgba(179, 155, 125, 0.2)");
    gradient.addColorStop(1, "rgba(179, 155, 125, 0)");

    const salesChart = new Chart(ctx, {
        type: "line",
        data: {
            labels: window.salesTrendLabels || ["Jan", "Feb", "Mar", "Apr", "May", "Jun"],
            datasets: [{
                data: window.salesTrendData || [0, 0, 0, 0, 0, 0],
                borderColor: "#b39b7d",
                borderWidth: 2,
                backgroundColor: gradient,
                fill: true,
                tension: 0.42,
                pointBackgroundColor: "#ffffff",
                pointBorderColor: "#b39b7d",
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function (context) {
                            return formatVnd(context.parsed.y);
                        }
                    }
                }
            },
            scales: {
                y: {
                    min: 0,
                    max: window.salesTrendMax || 1000000,
                    ticks: {
                        stepSize: window.salesTrendStepSize || 200000,
                        callback: function (value) {
                            if (value >= 1000000) {
                                return (value / 1000000) + "M";
                            }
                            if (value >= 1000) {
                                return (value / 1000) + "k";
                            }
                            return value;
                        },
                        font: { size: 10 }
                    },
                    grid: {
                        color: "#f4f1eb",
                        borderDash: [5, 5]
                    }
                },
                x: {
                    grid: { display: false },
                    ticks: { font: { size: 11 } }
                }
            }
        }
    });

    const rangeSelect = document.getElementById("salesTrendRange");
    if (rangeSelect) {
        rangeSelect.addEventListener("change", function () {
            updateSalesTrendChart(salesChart, parseInt(rangeSelect.value, 10));
        });
    }

    setupCsvExport();
});

function updateSalesTrendChart(chart, monthCount) {
    const labels = window.salesTrendLabels || [];
    const values = window.salesTrendData || [];
    const startIndex = Math.max(labels.length - monthCount, 0);
    const selectedLabels = labels.slice(startIndex);
    const selectedValues = values.slice(startIndex);
    const maxValue = Math.max(...selectedValues, 0);
    const roundedMax = maxValue <= 0 ? 1000000 : Math.max(1000000, Math.ceil(maxValue / 1000000) * 1000000);

    chart.data.labels = selectedLabels;
    chart.data.datasets[0].data = selectedValues;
    chart.options.scales.y.max = roundedMax;
    chart.options.scales.y.ticks.stepSize = Math.max(200000, Math.ceil(roundedMax / 5));
    chart.update();
}

function formatVnd(value) {
    return Number(value || 0).toLocaleString("vi-VN") + "đ";
}

function setupCsvExport() {
    const salesButton = document.getElementById("btnExportSales");
    const inventoryButton = document.getElementById("btnExportInventory");

    if (salesButton) {
        salesButton.addEventListener("click", function (event) {
            event.preventDefault();

            const labels = window.salesTrendLabels || [];
            const values = window.salesTrendData || [];
            const rangeSelect = document.getElementById("salesTrendRange");
            const monthCount = rangeSelect ? parseInt(rangeSelect.value, 10) : labels.length;
            const startIndex = Math.max(labels.length - monthCount, 0);
            let csv = "\uFEFFThang,Doanh thu\n";

            labels.slice(startIndex).forEach(function (label, index) {
                csv += `"${label}",${values[startIndex + index] || 0}\n`;
            });

            downloadCsv(csv, "dashboard-doanh-thu.csv");
        });
    }

    if (inventoryButton) {
        inventoryButton.addEventListener("click", function (event) {
            event.preventDefault();
            downloadCsv("\uFEFFBao cao ton kho\nVui long xem muc Canh bao ton kho tren dashboard.\n", "dashboard-ton-kho.csv");
        });
    }
}

function downloadCsv(csv, fileName) {
    const blob = new Blob([csv], { type: "text/csv;charset=utf-8;" });
    const url = URL.createObjectURL(blob);
    const link = document.createElement("a");

    link.href = url;
    link.download = fileName;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    URL.revokeObjectURL(url);
}
