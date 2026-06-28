document.addEventListener("DOMContentLoaded", function () {
    const ctx = document.getElementById('salesTrendsChart').getContext('2d');

    // Tạo hiệu ứng đổ bóng mờ (Gradient) phía dưới đường line
    const gradient = ctx.createLinearGradient(0, 0, 0, 300);
    gradient.addColorStop(0, 'rgba(179, 155, 125, 0.2)'); 
    gradient.addColorStop(1, 'rgba(179, 155, 125, 0.0)'); 

    const salesChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [{
                label: 'Sales ($)',
                data: [65000, 58000, 81000, 80000, 55000, 92000], 
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
            plugins: { legend: { display: false } },
            scales: {
                y: {
                    min: 0,
                    max: 100000,
                    ticks: {
                        stepSize: 10000,
                        callback: function(value) { return '$' + value / 1000 + 'k'; },
                        font: { size: 10 }
                    },
                    grid: { color: '#f4f1eb' },
                    border: { dash: [5, 5] } 
                },
                x: {
                    grid: { display: false },
                    ticks: { font: { size: 11 } }
                }
            }
        }
    });

    // --- QUẢN LÝ DANH SÁCH GIAO DỊCH (TRANSACTIONS VIEW ALL) ---
    const transactionsData = [
        { id: "#8921", item: '1x "Aurelia" Eternity Ring', amount: "$2,450.00", time: "2m ago", img: "https://images.unsplash.com/photo-1605100804763-247f67b3557e?w=100&auto=format&fit=crop&q=60" },
        { id: "#8920", item: "1x Pearl Drop Earrings", amount: "$1,200.00", time: "15m ago", img: "https://images.unsplash.com/photo-1599643478518-a784e5dc4c8f?w=100&auto=format&fit=crop&q=60" },
        { id: "#8919", item: "1x Diamond Tennis Bracelet", amount: "$4,800.00", time: "1h ago", img: "https://images.unsplash.com/photo-1603561591411-07134e71a2a9?w=100&auto=format&fit=crop&q=60" },
        { id: "#8918", item: "2x Gold Chain Choker", amount: "$1,950.00", time: "3h ago", img: "https://images.unsplash.com/photo-1535632066927-ab7c9ab60908?w=100&auto=format&fit=crop&q=60" },
        { id: "#8917", item: "1x Sapphire Pendant", amount: "$3,100.00", time: "5h ago", img: "https://images.unsplash.com/photo-1617038260897-41a1f14a8ca0?w=100&auto=format&fit=crop&q=60" },
        { id: "#8916", item: "1x Emerald Solitaire Ring", amount: "$5,200.00", time: "1d ago", img: "https://images.unsplash.com/photo-1605100804763-247f67b3557e?w=100&auto=format&fit=crop&q=60" },
        { id: "#8915", item: "1x Cushion-Cut Topaz Brooch", amount: "$2,900.00", time: "2d ago", img: "https://images.unsplash.com/photo-1599643478518-a784e5dc4c8f?w=100&auto=format&fit=crop&q=60" }
    ];

    const modalListContainer = document.getElementById("modalTransactionsList");
    const widgetListContainer = document.getElementById("widgetTransactionsList");

    function generateTransactionHTML(tx) {
        return `
            <div class="activity-item d-flex justify-content-between align-items-center p-2 rounded row-hover-effect">
                <div class="d-flex align-items-center gap-3">
                    <div class="item-img bg-light rounded" style="width:45px; height:45px; background: url('${tx.img}') center/cover;"></div>
                    <div>
                        <h6 class="mb-0 small fw-bold">Order ${tx.id}</h6>
                        <small class="text-muted font-xs">${tx.item}</small>
                    </div>
                </div>
                <div class="text-end">
                    <span class="d-block small fw-bold text-dark">${tx.amount}</span>
                    <small class="text-muted font-xs">${tx.time}</small>
                </div>
            </div>
        `;
    }

    if (transactionsData.length > 0) {
        if (modalListContainer) modalListContainer.innerHTML = transactionsData.map(generateTransactionHTML).join("");
        if (widgetListContainer) widgetListContainer.innerHTML = transactionsData.slice(0, 2).map(generateTransactionHTML).join("");
    }

    // --- CHỨC NĂNG EXPORT BÁO CÁO ---
    const btnExportSales = document.getElementById('btnExportSales');
    const btnExportInventory = document.getElementById('btnExportInventory');
    const mainDropdownBtn = document.getElementById('dropdownExportReport');

    function downloadCSV(csvString, fileName) {
        try {
            const blob = new Blob([csvString], { type: 'text/csv;charset=utf-8;' });
            const url = URL.createObjectURL(blob);
            const link = document.createElement("a");
            link.setAttribute("href", url);
            link.setAttribute("download", fileName);
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            URL.revokeObjectURL(url);
        } catch (err) {
            alert("Trình duyệt chặn tải xuống. Vui lòng kiểm tra lại!");
        }
    }

    function setButtonLoading(isLoading) {
        if (!mainDropdownBtn) return;
        if (isLoading) {
            mainDropdownBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Exporting...';
            mainDropdownBtn.disabled = true;
        } else {
            mainDropdownBtn.innerHTML = '<i class="bi bi-check2 me-2"></i>Success!';
            mainDropdownBtn.classList.replace('btn-gold', 'btn-success');
            setTimeout(() => {
                mainDropdownBtn.innerHTML = '<i class="bi bi-download me-2"></i>Export Report';
                mainDropdownBtn.classList.replace('btn-success', 'btn-gold');
                mainDropdownBtn.disabled = false;
            }, 2000);
        }
    }

    if (btnExportSales) {
        btnExportSales.addEventListener('click', function (e) {
            e.preventDefault();
            setButtonLoading(true);
            setTimeout(() => {
                const dateToday = new Date().toISOString().slice(0, 10);
                let csv = "\uFEFF--- AURELIA FINE JEWELRY - REPORT: SALES & REVENUE ---\n";
                csv += `Exported Date: ${dateToday}\n\n`;
                csv += "[1. KEY METRICS]\nMetric Name,Value,Analysis / Note\n\"Gross Revenue\",\"$482,950.00\",\"Total sales value generated before deductions.\"\n\"Net Revenue\",\"$458,800.00\",\"After subtracting discounts, returns and cancellations.\"\n\"Total Orders\",\"1,284\",\"Total successful and processing orders.\"\n\"Average Order Value (AOV)\",\"$376.13\",\"High-end premium consumer basket value average.\"\n\n";
                csv += "[2. REVENUE BY CATEGORY SHARE]\nCategory / Collection,Percentage Share (%),Estimated Value\n\"Ethereal Collection (Necklaces)\",\"45%\",\" $217,327.50 \"\n\"Classic Artisanal (Rings)\",\"32%\",\" $154,544.00 \"\n\"Heirloom Pearls (Earrings)\",\"23%\",\" $111,078.50 \"\n\n";
                csv += "[3. PROMOTION CODE PERFORMANCE]\nCoupon Code,Usage Count,Total Generated Revenue,Campaign Status\n\"AURELIA10\",\"342\",\" $85,500.00 \",\"Active\"\n\"WELCOME5\",\"189\",\" $32,400.00 \",\"Active\"\n\"FLASHGOLD20\",\"78\",\" $45,200.00 \",\"Expired\"\n";
                downloadCSV(csv, `Aurelia_Sales_Report_${dateToday}.csv`);
                setButtonLoading(false);
            }, 800);
        });
    }

    if (btnExportInventory) {
        btnExportInventory.addEventListener('click', function (e) {
            e.preventDefault();
            setButtonLoading(true);
            setTimeout(() => {
                const dateToday = new Date().toISOString().slice(0, 10);
                let csv = "\uFEFF--- AURELIA FINE JEWELRY - REPORT: PRODUCT & INVENTORY ---\n";
                csv += `Exported Date: ${dateToday}\n\n`;
                csv += "[1. TOP BEST-SELLERS PERFORMANCE]\nProduct Name,SKU,Quantity Sold,Total Revenue,Metric Rank Type\n\"1x \"\"Aurelia\"\" Eternity Ring\",\"AUR-ER-001\",\"420\",\" $1,029,000.00 \",\"Top Revenue Generator\"\n\"1x Pearl Drop Earrings\",\"AUR-PE-012\",\"310\",\" $372,000.00 \",\"Top Volume Sold\"\n\"1x Diamond Tennis Bracelet\",\"AUR-DT-009\",\"150\",\" $720,000.00 \",\"Premium Performer\"\n\n";
                csv += "[2. DEADSTOCK & SLOW-MOVING ITEMS]\nProduct Name,SKU,Days In Warehouse,Current Stock Units,Suggested Action\n\"Silver Geometric Cuff\",\"AUR-SC-089\",\"185 Days\",\"45\",\"Flash Sale / Clearance Pricing\"\n\"Vintage Amber Brooch\",\"AUR-VB-102\",\"240 Days\",\"12\",\"Redesign / Material Melting Extraction\"\n\n";
                csv += "[3. INVENTORY STATUS BY ATTRIBUTES]\nProduct Base,Variant Attribute (Size / Stone),Units Available,Status\n\"\"\"Aurelia\"\" Eternity Ring\",\"Size 6 - Diamond 0.5ct\",\"18\",\"In Stock\"\n\"\"\"Aurelia\"\" Eternity Ring\",\"Size 7 - Diamond 0.5ct\",\"2\",\"Low Stock Warning\"\n\"Pearl Drop Earrings\",\"Standard Size - Akoya Pearl\",\"25\",\"In Stock\"\n\n";
                csv += "[4. LOW STOCK CRITICAL ALERTS]\nProduct Name,SKU Barcode,Stock Remaining,Minimum Threshold,Priority Level\n\"Moonstone Solitaire\",\"AUR-MS-002\",\"2\",\"5\",\"CRITICAL - RESTOCK NOW\"\n\"Petite Diamond Hoops\",\"AUR-DH-045\",\"5\",\"5\",\"WARNING - REQUIRE PRODUCTION\"\n";
                downloadCSV(csv, `Aurelia_Inventory_Report_${dateToday}.csv`);
                setButtonLoading(false);
            }, 800);
        });
    }
});