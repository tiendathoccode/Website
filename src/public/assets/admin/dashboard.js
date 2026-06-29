document.addEventListener("DOMContentLoaded", function () {
    const ctx = document.getElementById('salesTrendsChart').getContext('2d');

    // Tạo hiệu ứng đổ bóng mờ (Gradient) phía dưới đường line
    const gradient = ctx.createLinearGradient(0, 0, 0, 300);
    gradient.addColorStop(0, 'rgba(179, 155, 125, 0.2)'); // Màu vàng nhạt mờ ở trên
    gradient.addColorStop(1, 'rgba(179, 155, 125, 0.0)'); // Trong suốt ở dưới cùng

    const salesChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [{
                label: 'Sales ($)',
                data: [65000, 58000, 81000, 80000, 55000, 92000], // Bộ dữ liệu tương ứng đồ thị của hình mẫu
                borderColor: '#b39b7d', // Màu đường line (vàng đồng)
                borderWidth: 2,
                backgroundColor: gradient,
                fill: true,
                tension: 0.4, // Độ uốn cong mượt mà của đường lượn sóng
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
                    display: false // Ẩn nhãn chú thích thừa
                }
            },
            scales: {
                y: {
                    min: 0,
                    max: 100000,
                    ticks: {
                        stepSize: 10000,
                        callback: function(value) {
                            return '$' + value / 1000 + 'k'; // Format trục Y thành $10k, $20k...
                        },
                        font: { size: 10 }
                    },
                    grid: {
                        color: '#f4f1eb' // Màu các đường kẻ ngang mờ
                    },
                    border: { dash: [5, 5] } // Nét đứt
                },
                x: {
                    grid: {
                        display: false // Ẩn lưới trục dọc X cho sạch mắt
                    },
                    ticks: { font: { size: 11 } }
                }
            }
        }
    });
});
// --- QUẢN LÝ DANH SÁCH GIAO DỊCH (TRANSACTIONS VIEW ALL) ---
document.addEventListener("DOMContentLoaded", function () {
    const ctx = document.getElementById('salesTrendsChart').getContext('2d');

    // Tạo hiệu ứng đổ bóng mờ (Gradient) phía dưới đường line
    const gradient = ctx.createLinearGradient(0, 0, 0, 300);
    gradient.addColorStop(0, 'rgba(179, 155, 125, 0.2)'); // Màu vàng nhạt mờ ở trên
    gradient.addColorStop(1, 'rgba(179, 155, 125, 0.0)'); // Trong suốt ở dưới cùng

    const salesChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [{
                label: 'Sales ($)',
                data: [65000, 58000, 81000, 80000, 55000, 92000], 
                borderColor: '#b39b7d', // Màu đường line (vàng đồng)
                borderWidth: 2,
                backgroundColor: gradient,
                fill: true,
                tension: 0.4, // Độ uốn cong mượt mà của đường lượn sóng
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
                    display: false // Ẩn nhãn chú thích thừa
                }
            },
            scales: {
                y: {
                    min: 0,
                    max: 100000,
                    ticks: {
                        stepSize: 10000,
                        callback: function(value) {
                            return '$' + value / 1000 + 'k'; // Format trục Y thành $10k, $20k...
                        },
                        font: { size: 10 }
                    },
                    grid: {
                        color: '#f4f1eb' // Màu các đường kẻ ngang mờ
                    },
                    border: { dash: [5, 5] } // Nét đứt
                },
                x: {
                    grid: {
                        display: false // Ẩn lưới trục dọc X cho sạch mắt
                    },
                    ticks: { font: { size: 11 } }
                }
            }
        }
    });
});

// --- QUẢN LÝ DANH SÁCH GIAO DỊCH (TRANSACTIONS VIEW ALL) ---
document.addEventListener("DOMContentLoaded", function () {
    // 1. Mảng dữ liệu mẫu
    const transactionsData = [
        { id: "#8921", item: '1x "Aurelia" Eternity Ring', amount: "$2,450.00", time: "2m ago", img: "https://images.unsplash.com/photo-1605100804763-247f67b3557e?w=100&auto=format&fit=crop&q=60" },
        { id: "#8920", item: "1x Pearl Drop Earrings", amount: "$1,200.00", time: "15m ago", img: "https://images.unsplash.com/photo-1599643478518-a784e5dc4c8f?w=100&auto=format&fit=crop&q=60" },
        { id: "#8919", item: "1x Diamond Tennis Bracelet", amount: "$4,800.00", time: "1h ago", img: "https://images.unsplash.com/photo-1603561591411-07134e71a2a9?w=100&auto=format&fit=crop&q=60" },
        { id: "#8918", item: "2x Gold Chain Choker", amount: "$1,950.00", time: "3h ago", img: "https://images.unsplash.com/photo-1535632066927-ab7c9ab60908?w=100&auto=format&fit=crop&q=60" },
        { id: "#8917", item: "1x Sapphire Pendant", amount: "$3,100.00", time: "5h ago", img: "https://images.unsplash.com/photo-1617038260897-41a1f14a8ca0?w=100&auto=format&fit=crop&q=60" },
        { id: "#8916", item: "1x Emerald Solitaire Ring", amount: "$5,200.00", time: "1d ago", img: "https://images.unsplash.com/photo-1605100804763-247f67b3557e?w=100&auto=format&fit=crop&q=60" },
        { id: "#8915", item: "1x Cushion-Cut Topaz Brooch", amount: "$2,900.00", time: "2d ago", img: "https://images.unsplash.com/photo-1599643478518-a784e5dc4c8f?w=100&auto=format&fit=crop&q=60" }
    ];

    // 2. Tìm các khung chứa trên giao diện HTML
    const modalListContainer = document.getElementById("modalTransactionsList");
    const widgetListContainer = document.getElementById("widgetTransactionsList");

    // Hàm tạo chuỗi HTML cho mỗi dòng giao dịch
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

    // 3. Tự động đổ dữ liệu vào giao diện
    if (transactionsData.length > 0) {
        if (modalListContainer) {
            modalListContainer.innerHTML = transactionsData.map(generateTransactionHTML).join("");
        }

        if (widgetListContainer) {
            const recentTwo = transactionsData.slice(0, 2); 
            widgetListContainer.innerHTML = recentTwo.map(generateTransactionHTML).join("");
        }
    }

    // ==========================================
    // MẢNG DỮ LIỆU ĐƯỢC GIỮ LẠI ĐỂ PHỤC VỤ AUDIT LOGS MODAL
    // ==========================================
    const coreNotifications = [
        { id: 1, cat: 'order', title: 'New Order #8922', desc: 'Customer placed an order for 1x "Aurelia" Eternity Ring.', time: 'Just now', icon: 'bi-bag-check', bg: '#e8f5e9', color: '#2e7d32', isUnread: true },
        { id: 2, cat: 'payment', title: 'Payment Success', desc: 'VNPay confirmed payment of $1,200.00 for Order #8920.', time: '12m ago', icon: 'bi-credit-card', bg: '#e8f5e9', color: '#2e7d32', isUnread: true },
        { id: 3, cat: 'refund', title: 'Cancellation Request', desc: 'Order #8914 requested a cancellation & full refund.', time: '1h ago', icon: 'bi-arrow-counterclockwise', bg: '#ffebee', color: '#c62828', isUnread: true },
        { id: 4, cat: 'stock', title: 'Low Stock Alert', desc: '"Petite Diamond Hoops" touched minimum limit (5 left).', time: '3h ago', icon: 'bi-exclamation-triangle', bg: '#ffebee', color: '#c62828', isUnread: true },
        { id: 5, cat: 'stock', title: 'Out of Stock', desc: '"Gold Chain Choker" is completely sold out.', time: '5h ago', icon: 'bi-x-circle', bg: '#f5f5f5', color: '#424242', isUnread: false },
        { id: 6, cat: 'review', title: 'New 5-Star Review', desc: 'Sophia L. left a review: "Absolutely stunning craftsmanship!"', time: '1d ago', icon: 'bi-star-fill', bg: '#e3f2fd', color: '#1565c0', isUnread: false },
        { id: 7, cat: 'chat', title: 'Live Chat Message', desc: 'Guest customer is asking about diamond certification sizes.', time: '1d ago', icon: 'bi-chat-dots', bg: '#e3f2fd', color: '#1565c0', isUnread: false },
        { id: 8, cat: 'security', title: 'Security Alert', desc: 'Unusual admin login attempt detected from Hanoi location.', time: '2d ago', icon: 'bi-shield-exclamation', bg: '#fff3e0', color: '#ef6c00', isUnread: false },
        { id: 9, cat: 'report', title: 'Monthly Report Ready', desc: 'May sales summary report has been compiled successfully.', time: '3d ago', icon: 'bi-graph-up-arrow', bg: '#f3e5f5', color: '#6a1b9a', isUnread: false },
        { id: 10, cat: 'shipping', title: 'Package Dispatched', desc: 'Shipper confirmed successful pickup for Order #8918.', time: '4d ago', icon: 'bi-truck', bg: '#e1f5fe', color: '#0288d1', isUnread: false }
    ];

    // ==========================================
    // 4. CHỨC NĂNG EXPORT BÁO CÁO (ĐÃ FIX LỖI)
    // ==========================================
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
            console.error("Lỗi khi tải file:", err);
            alert("Trình duyệt chặn tải xuống. Vui lòng kiểm tra lại!");
        }
    }

    function setButtonLoading(isLoading) {
        if (!mainDropdownBtn) return;
        if (isLoading) {
            mainDropdownBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Exporting...';
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
            e.preventDefault(); // Ngăn hành vi nhảy trang
            setButtonLoading(true);

            setTimeout(() => {
                const dateToday = new Date().toISOString().slice(0, 10);
                let csv = "\uFEFF"; 
                
                csv += "--- AURELIA FINE JEWELRY - REPORT: SALES & REVENUE ---\n";
                csv += `Exported Date: ${dateToday}\n\n`;

                csv += "[1. KEY METRICS]\n";
                csv += "Metric Name,Value,Analysis / Note\n";
                csv += "\"Gross Revenue\",\"$482,950.00\",\"Total sales value generated before deductions.\"\n";
                csv += "\"Net Revenue\",\"$458,800.00\",\"After subtracting discounts, returns and cancellations.\"\n";
                csv += "\"Total Orders\",\"1,284\",\"Total successful and processing orders.\"\n";
                csv += "\"Average Order Value (AOV)\",\"$376.13\",\"High-end premium consumer basket value average.\"\n\n";

                csv += "[2. REVENUE BY CATEGORY SHARE]\n";
                csv += "Category / Collection,Percentage Share (%),Estimated Value\n";
                csv += "\"Ethereal Collection (Necklaces)\",\"45%\",\" $217,327.50 \"\n";
                csv += "\"Classic Artisanal (Rings)\",\"32%\",\" $154,544.00 \"\n";
                csv += "\"Heirloom Pearls (Earrings)\",\"23%\",\" $111,078.50 \"\n\n";

                csv += "[3. PROMOTION CODE PERFORMANCE]\n";
                csv += "Coupon Code,Usage Count,Total Generated Revenue,Campaign Status\n";
                csv += "\"AURELIA10\",\"342\",\" $85,500.00 \",\"Active\"\n";
                csv += "\"WELCOME5\",\"189\",\" $32,400.00 \",\"Active\"\n";
                csv += "\"FLASHGOLD20\",\"78\",\" $45,200.00 \",\"Expired\"\n";

                downloadCSV(csv, `Aurelia_Sales_Report_${dateToday}.csv`);
                setButtonLoading(false);
            }, 800);
        });
    }

    if (btnExportInventory) {
        btnExportInventory.addEventListener('click', function (e) {
            e.preventDefault(); // Ngăn hành vi nhảy trang
            setButtonLoading(true);

            setTimeout(() => {
                const dateToday = new Date().toISOString().slice(0, 10);
                let csv = "\uFEFF";
                
                csv += "--- AURELIA FINE JEWELRY - REPORT: PRODUCT & INVENTORY ---\n";
                csv += `Exported Date: ${dateToday}\n\n`;

                csv += "[1. TOP BEST-SELLERS PERFORMANCE]\n";
                csv += "Product Name,SKU,Quantity Sold,Total Revenue,Metric Rank Type\n";
                csv += "\"1x \"\"Aurelia\"\" Eternity Ring\",\"AUR-ER-001\",\"420\",\" $1,029,000.00 \",\"Top Revenue Generator\"\n";
                csv += "\"1x Pearl Drop Earrings\",\"AUR-PE-012\",\"310\",\" $372,000.00 \",\"Top Volume Sold\"\n";
                csv += "\"1x Diamond Tennis Bracelet\",\"AUR-DT-009\",\"150\",\" $720,000.00 \",\"Premium Performer\"\n\n";

                csv += "[2. DEADSTOCK & SLOW-MOVING ITEMS]\n";
                csv += "Product Name,SKU,Days In Warehouse,Current Stock Units,Suggested Action\n";
                csv += "\"Silver Geometric Cuff\",\"AUR-SC-089\",\"185 Days\",\"45\",\"Flash Sale / Clearance Pricing\"\n";
                csv += "\"Vintage Amber Brooch\",\"AUR-VB-102\",\"240 Days\",\"12\",\"Redesign / Material Melting Extraction\"\n\n";

                csv += "[3. INVENTORY STATUS BY ATTRIBUTES]\n";
                csv += "Product Base,Variant Attribute (Size / Stone),Units Available,Status\n";
                csv += "\"\"\"Aurelia\"\" Eternity Ring\",\"Size 6 - Diamond 0.5ct\",\"18\",\"In Stock\"\n";
                csv += "\"\"\"Aurelia\"\" Eternity Ring\",\"Size 7 - Diamond 0.5ct\",\"2\",\"Low Stock Warning\"\n";
                csv += "\"Pearl Drop Earrings\",\"Standard Size - Akoya Pearl\",\"25\",\"In Stock\"\n\n";

                csv += "[4. LOW STOCK CRITICAL ALERTS]\n";
                csv += "Product Name,SKU Barcode,Stock Remaining,Minimum Threshold,Priority Level\n";
                csv += "\"Moonstone Solitaire\",\"AUR-MS-002\",\"2\",\"5\",\"CRITICAL - RESTOCK NOW\"\n";
                csv += "\"Petite Diamond Hoops\",\"AUR-DH-045\",\"5\",\"5\",\"WARNING - REQUIRE PRODUCTION\"\n";

                downloadCSV(csv, `Aurelia_Inventory_Report_${dateToday}.csv`);
                setButtonLoading(false);
            }, 800);
        });
    }
});