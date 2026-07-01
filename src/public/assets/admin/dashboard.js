function showToast(message, type = 'success') {
    let container = document.getElementById('custom-toast-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'custom-toast-container';
        container.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 99999; display: flex; flex-direction: column; gap: 10px;';
        document.body.appendChild(container);
    }
    const toast = document.createElement('div');
    toast.style.cssText = 'background: #ffffff; border-left: 4px solid #c8a165; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08); color: #333333; padding: 14px 20px; font-size: 13px; font-family: "Inter", sans-serif; border-radius: 4px; display: flex; align-items: center; gap: 10px; min-width: 280px; max-width: 380px; transition: all 0.3s ease; opacity: 1;';
    
    if (type === 'error') {
        toast.style.borderLeftColor = '#dc3545';
    } else if (type === 'success') {
        toast.style.borderLeftColor = '#198754';
    }

    let icon = '<i class="fas fa-check-circle" style="color:#198754"></i>';
    if (type === 'error') {
        icon = '<i class="fas fa-times-circle" style="color:#dc3545"></i>';
    } else if (type === 'info') {
        icon = '<i class="fas fa-info-circle" style="color:#c8a165"></i>';
    }
    
    toast.innerHTML = `${icon} <span>${message}</span>`;
    container.appendChild(toast);
    
    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateY(-20px)';
        setTimeout(() => {
            toast.remove();
        }, 300);
    }, 3000);
}

document.addEventListener("DOMContentLoaded", function () {
    const ctx = document.getElementById('salesTrendsChart');
    if (ctx) {
        fetch("/index.php?page=admin_api_sales_chart")
            .then(res => res.json())
            .then(data => {
                const context = ctx.getContext('2d');
                const gradient = context.createLinearGradient(0, 0, 0, 300);
                gradient.addColorStop(0, 'rgba(179, 155, 125, 0.2)');
                gradient.addColorStop(1, 'rgba(179, 155, 125, 0.0)');

                new Chart(context, {
                    type: 'line',
                    data: {
                        labels: data.months || ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                        datasets: [{
                            label: 'Doanh thu (VND)',
                            data: data.sales || [0, 0, 0, 0, 0, 0],
                            borderColor: '#b39b7d',
                            borderWidth: 2,
                            backgroundColor: gradient,
                            fill: true,
                            tension: 0.4,
                            pointBackgroundColor: '#ffffff',
                            pointBorderColor: '#b39b7d',
                            pointBorderWidth: 2,
                            pointRadius: 4,
                            pointHoverRadius: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                min: 0,
                                ticks: {
                                    callback: function(value) {
                                        if (value >= 1000000) {
                                            return (value / 1000000) + 'M';
                                        }
                                        return value.toLocaleString('vi-VN') + ' VND';
                                    },
                                    font: { size: 10 }
                                },
                                grid: {
                                    color: '#f4f1eb'
                                },
                                border: { dash: [5, 5] }
                            },
                            x: {
                                grid: {
                                    display: false
                                },
                                ticks: { font: { size: 11 } }
                            }
                        }
                    }
                });
            })
            .catch(err => {
                console.error("Lỗi lấy dữ liệu doanh số:", err);
            });
    }

    // Export report actions
    const btnExportSales = document.getElementById('btnExportSales');
    const btnExportInventory = document.getElementById('btnExportInventory');

    if (btnExportSales) {
        btnExportSales.addEventListener('click', function(e) {
            e.preventDefault();
            showToast("Bắt đầu xuất Báo cáo Doanh thu... (Tính năng Backend sẽ tạo file Excel/CSV)", "info");
        });
    }
    if (btnExportInventory) {
        btnExportInventory.addEventListener('click', function(e) {
            e.preventDefault();
            showToast("Bắt đầu xuất Báo cáo Tồn kho... (Tính năng Backend sẽ tạo file Excel/CSV)", "info");
        });
    }
});