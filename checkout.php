<?php session_start(); ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - IMAMART</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700;900&family=Courier+Prime&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        html { overflow-y: scroll; }
        body { font-family: 'Lato', sans-serif; }
        .logo-white { filter: brightness(0) invert(1); }
        
        @media print {
            header, nav, footer, .container, #checkout-bar, #toast, .swal2-container { display: none !important; }
            #receipt-area {
                display: block !important;
                position: absolute !important;
                top: 0; left: 0; width: 100%;
                margin: 0; padding: 0;
                background-color: white; color: black; z-index: 9999;
            }
            #receipt-area * { visibility: visible !important; color: black !important; }
            .flex { display: flex !important; }
            .justify-between { justify-content: space-between !important; }
            .text-center { text-align: center !important; }
            .text-right { text-align: right !important; }
            @page { margin: 0.5cm; }
        }
    </style>
</head>
<body class="font-lato bg-white text-gray-800 flex flex-col min-h-screen">

    <header class="bg-blue-600 text-white shadow-lg sticky top-0 z-50">
        <div class="container mx-auto px-4 py-3 flex justify-between items-center">
            <div class="flex items-center">
                <a href="home.php">
                    <img src="LOGO_IMAMART.png" alt="IMAMART Logo" class="h-10 w-auto logo-white hover:opacity-90 transition">
                </a>
            </div>

            <nav class="hidden md:flex space-x-8 font-bold text-blue-100">
                <a href="home.php" class="hover:text-white transition">Home</a>
                <a href="product.php" class="hover:text-white transition">Product</a>
                <a href="voucher.php" class="hover:text-white transition">Voucher</a>
                <a href="tentang_kami.php" class="hover:text-white transition">Tentang Kami</a>
            </nav>

            <div class="flex items-center space-x-4">
                <div class="relative hidden sm:block">
                    <input type="text" placeholder="Cari produk..." class="bg-blue-500 text-white placeholder-blue-200 rounded-full px-4 py-1.5 focus:outline-none focus:ring-2 focus:ring-white text-sm w-48 transition-all focus:w-64">
                    <button class="absolute right-3 top-1/2 transform -translate-y-1/2 text-blue-200 hover:text-white">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>
                </div>

                <a href="checkout.php" class="text-2xl text-blue-200 hover:text-white transition relative group mt-1">
                    <i class="fa-solid fa-cart-shopping"></i>
                    <!-- Badge angka jumlah produk (Dijalankan oleh JavaScript) -->
                    <span id="cart-badge" class="absolute -top-1 -right-2 bg-red-500 text-white text-[10px] font-bold w-4 h-4 flex items-center justify-center rounded-full hidden">
                        0
                    </span>
                    <!-- Tooltip saat di-hover -->
                    <span class="absolute right-0 top-8 bg-gray-800 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition whitespace-nowrap">
                        Keranjang Belanja
                    </span>
                </a>
                
                <?php 
                    // Cek apakah user sudah login
                    if(isset($_SESSION['user'])) {
                        // Jika SUDAH login, arahkan ke profile.php
                        $profile_link = "profile.php";
                        $icon_class = "text-white"; // Indikator aktif (putih terang)
                    } else {
                        // Jika BELUM login, arahkan ke login.php
                        $profile_link = "login.php";
                        $icon_class = "text-blue-200 hover:text-white"; // Indikator belum aktif
                    }
                ?>
                <a href="<?php echo $profile_link; ?>" class="text-2xl <?php echo $icon_class; ?> transition relative group">
                    <i class="fa-solid fa-circle-user"></i>
                    <span class="absolute right-0 top-8 bg-gray-800 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition whitespace-nowrap">
                        <?php echo isset($_SESSION['user']) ? 'Hai, '.$_SESSION['user'] : 'Masuk Akun'; ?>
                    </span>
                </a>

                <!-- Burger Button -->
                <button onclick="toggleMobileMenu()" class="md:hidden text-white text-2xl ml-2 focus:outline-none print:hidden">
                    <i class="fa-solid fa-bars"></i>
                </button>

            </div>
        </div>

        <!-- Mobile Menu -->
        <nav id="mobile-menu" class="hidden md:hidden bg-blue-700 text-white font-bold flex-col space-y-3 px-4 py-4 shadow-inner print:hidden">
            <a href="home.php" class="block hover:text-blue-200 transition">Home</a>
            <a href="product.php" class="block hover:text-blue-200 transition">Product</a>
            <a href="voucher.php" class="block hover:text-blue-200 transition">Voucher</a>
            <a href="tentang_kami.php" class="block hover:text-blue-200 transition">Tentang Kami</a>
        </nav>
    </header>

    <div class="container mx-auto px-4 py-8 flex-grow max-w-5xl print:hidden">
        <div class="flex flex-col lg:flex-row gap-8">
            <div class="w-full lg:w-2/3">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                    <h2 class="text-xl font-bold mb-4 flex justify-between items-center">
                        Daftar Belanja
                        <button onclick="clearCart()" class="text-sm text-red-500 hover:underline">Hapus Semua</button>
                    </h2>
                    <div id="cart-items-container" class="space-y-4"></div>
                    <div class="mt-6 pt-4 border-t border-gray-100">
                        <a href="product.php" class="inline-flex items-center text-blue-600 font-bold hover:underline">
                            <i class="fa-solid fa-plus mr-2"></i> Tambahkan Produk Lainnya
                        </a>
                    </div>
                </div>
            </div>

            <div class="w-full lg:w-1/3">
                <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6 sticky top-24">
                    <h3 class="font-bold text-lg mb-4">Metode Pembayaran</h3>
                    <div class="space-y-3 mb-6">
                         <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-blue-50 has-[:checked]:bg-blue-50 has-[:checked]:border-blue-500">
                            <input type="radio" name="payment" value="TUNAI" class="mr-3" checked onchange="togglePaymentInput()">
                            <span class="font-bold text-gray-700"><i class="fa-solid fa-money-bill-wave mr-2 text-green-600"></i> Tunai</span>
                        </label>
                        <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-blue-50 has-[:checked]:bg-blue-50 has-[:checked]:border-blue-500">
                            <input type="radio" name="payment" value="Banking" class="mr-3" onchange="togglePaymentInput()">
                            <span class="font-bold text-gray-700"><i class="fa-solid fa-wallet mr-2 text-blue-600"></i> Banking</span>
                        </label>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Kode Voucher</label>
                        <div class="flex gap-2">
                            <input type="text" id="voucher-code" placeholder="Punya kode promo?" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm uppercase">
                            <button onclick="applyVoucher()" class="bg-blue-600 text-white font-bold px-4 py-2 rounded-md hover:bg-blue-700 text-sm">Pakai</button>
                        </div>
                        <p id="voucher-msg" class="text-xs mt-1 font-bold hidden"></p>
                    </div>
                    <div class="border-t border-dashed border-gray-300 my-4"></div>

                    <div class="flex justify-between mb-2 text-gray-600">
                        <span>Total Harga</span>
                        <span id="subtotal-display" class="font-bold">Rp 0</span>
                    </div>
                    <div class="flex justify-between mb-2 text-gray-600">
                        <span>Biaya Admin</span>
                        <span class="font-bold">Rp 1.000</span>
                    </div>
                    <div id="discount-row" class="flex justify-between mb-4 text-green-600 hidden">
                        <span><i class="fa-solid fa-tags mr-1"></i> Diskon Voucher</span>
                        <span id="discount-display" class="font-bold">-Rp 0</span>
                    </div>

                    <div class="flex justify-between mb-6 text-xl font-black text-blue-600 pt-2 border-t">
                        <span>Total Tagihan</span>
                        <span id="total-display">Rp 0</span>
                    </div>


                    <button onclick="konfirmasiPesanan()" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-xl shadow-lg transition transform hover:-translate-y-1 disabled:bg-gray-400 disabled:cursor-not-allowed" id="btn-pay">
                        Konfirmasi Pesanan <i class="fa-solid fa-check-circle ml-2"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div id="receipt-area" class="hidden bg-white text-black p-8 max-w-sm mx-auto font-mono text-sm leading-relaxed border border-gray-200">
        <div class="text-center mb-6">
            <h2 class="text-2xl font-bold uppercase tracking-widest mb-1">IMAMART</h2>
            <p class="text-xs">Jl. Minimarket No. 1, Jakarta Pusat</p>
        </div>
        <div class="border-b-2 border-dashed border-black my-4"></div>
        <div class="flex justify-between text-xs mb-1"><span>TANGGAL:</span><span id="struk-date"></span></div>
        <div class="flex justify-between text-xs mb-1"><span>NO. INV:</span><span id="struk-id"></span></div>
        <div class="flex justify-between text-xs mb-1"><span>METODE:</span><span id="struk-payment" class="font-bold"></span></div>
        <div class="flex justify-between text-xs mb-1"><span>PENGIRIMAN:</span><span id="struk-metode-kirim" class="font-bold"></span></div>
        <div class="border-b-2 border-dashed border-black my-4"></div>
        <div id="struk-items" class="space-y-2 mb-4"></div>
        <div class="border-b-2 border-dashed border-black my-4"></div>
        <div class="flex justify-between font-bold text-md mt-1"><span>SUBTOTAL:</span><span id="struk-subtotal"></span></div>
        <div class="flex justify-between text-sm mt-1"><span>DISKON:</span><span id="struk-discount"></span></div>
        <div class="flex justify-between font-bold text-lg mt-2"><span>TOTAL:</span><span id="struk-total"></span></div>

        <div class="text-center mt-8 text-xs"><p>*** TERIMA KASIH ***</p></div>
    </div>

    <footer class="bg-blue-600 text-white pt-12 pb-6">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
                
                <div class="text-center md:text-left">
                    <h3 class="text-xl font-bold mb-4 flex items-center justify-center md:justify-start gap-2">
                        <i class="fa-solid fa-store"></i> IMAMART
                    </h3>
                    <p class="text-blue-100 text-sm leading-relaxed md:pr-4">
                        Minimarket modern pilihan keluarga dengan harga bersahabat, produk segar, dan pelayanan ramah sepenuh hati.
                    </p>
                </div>

                <div class="text-center md:text-left">
                    <h3 class="text-lg font-bold mb-4 border-b-2 border-blue-400 inline-block pb-1">Akses Cepat</h3>
                    <ul class="space-y-2 text-blue-100 text-sm">
                        <li><a href="#" class="hover:text-white hover:pl-1 transition-all">Promo Hari Ini</a></li>
                        <li><a href="#" class="hover:text-white hover:pl-1 transition-all">Cek Voucher</a></li>
                        <li><a href="#" class="hover:text-white hover:pl-1 transition-all">Status Pesanan</a></li>
                        <li><a href="#" class="hover:text-white hover:pl-1 transition-all">Hubungi Kami</a></li>
                    </ul>
                </div>

                <div class="text-center md:text-left">
                    <h3 class="text-lg font-bold mb-4 border-b-2 border-blue-400 inline-block pb-1">Tentang Market</h3>
                    <ul class="space-y-2 text-blue-100 text-sm">
                        <li><a href="#" class="hover:text-white hover:pl-1 transition-all">Profil Pendiri</a></li>
                        <li><a href="#" class="hover:text-white hover:pl-1 transition-all">Izin Usaha</a></li>
                        <li><a href="#" class="hover:text-white hover:pl-1 transition-all">Jam Operasional</a></li>
                    </ul>
                </div>

                <div class="text-center md:text-left flex flex-col items-center md:items-start">
                    <h3 class="text-lg font-bold mb-4 border-b-2 border-blue-400 inline-block pb-1">Lokasi Kami</h3>
                    <div class="rounded-lg overflow-hidden shadow-lg border-2 border-blue-400 mb-3 aspect-square w-40">
                        <iframe 
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3966.666466953933!2d106.82496467499002!3d-6.175387060510842!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69f5d2e764587d%3A0x649e330b632945a8!2sMonumen%20Nasional!5e0!3m2!1sid!2sid!4v1709228812345!5m2!1sid!2sid" 
                            style="border:0;"
                            class="w-full h-full" 
                            allowfullscreen="" 
                            loading="lazy">
                        </iframe>
                    </div>
                    <p class="text-xs text-blue-100"><i class="fa-solid fa-location-dot mr-1"></i> Jl. Minimarket No. 1, Jakarta Pusat</p>
                </div>
            </div>

            <hr class="border-blue-500 mb-6">

            <div class="text-center text-sm text-blue-200">
                © <?php echo date("Y"); ?> IMAMART. All rights reserved.
            </div>
        </div>
    </footer>

    <script>
        let cart = JSON.parse(localStorage.getItem('imamart_cart')) || [];
        const serviceFee = 1000;
        let grandTotal = 0;
        let subtotal = 0;
        let discountAmount = 0; // Variabel baru untuk menyimpan nominal diskon

        renderCart();

        function renderCart() {
            const container = document.getElementById('cart-items-container');
            const subtotalEl = document.getElementById('subtotal-display');
            const totalEl = document.getElementById('total-display');
            const discountEl = document.getElementById('discount-display');
            
            if (cart.length === 0) {
                container.innerHTML = '<div class="text-center py-10"><p class="text-gray-400">Keranjang kosong...</p></div>';
                subtotalEl.innerText = formatRupiah(0);
                totalEl.innerText = formatRupiah(0);
                grandTotal = 0;
                return;
            }

            let html = '';
            subtotal = 0;
            cart.forEach((item, index) => {
                subtotal += item.price * item.qty;
                html += `
                <div class="flex items-center gap-4 py-4 border-b border-gray-100 last:border-0">
                    <div class="flex-grow">
                        <h4 class="font-bold text-gray-800">${item.name}</h4>
                        <p class="text-blue-600 font-bold text-sm">${formatRupiah(item.price)} x ${item.qty}</p>
                    </div>
                    <div class="font-bold">${formatRupiah(item.price * item.qty)}</div>
                    <button onclick="removeItem(${index})" class="text-red-500 ml-2"><i class="fa-solid fa-trash"></i></button>
                </div>`;
            });

            // HITUNG GRAND TOTAL (Subtotal + Admin - Diskon)
            grandTotal = subtotal + serviceFee - discountAmount;
            if (grandTotal < 0) grandTotal = 0; // Tidak boleh minus

            container.innerHTML = html;
            subtotalEl.innerText = formatRupiah(subtotal);
            
            // Update Tampilan Diskon
            if (discountAmount > 0) {
                document.getElementById('discount-row').classList.remove('hidden');
                discountEl.innerText = '-' + formatRupiah(discountAmount);
            } else {
                document.getElementById('discount-row').classList.add('hidden');
            }

            totalEl.innerText = formatRupiah(grandTotal);
        }

        // --- FUNGSI BARU: APPLY VOUCHER ---
        async function applyVoucher() {
            const code = document.getElementById('voucher-code').value;
            const msgEl = document.getElementById('voucher-msg');

            if(!code) return Swal.fire('Eits', 'Isi kode dulu!', 'warning');

            // Cek jika keranjang kosong
            if(subtotal === 0) return Swal.fire('Kosong', 'Belanja dulu baru pakai voucher!', 'warning');

            try {
                // Request ke check_voucher.php
                const res = await fetch('check_voucher.php', {
                    method: 'POST',
                    body: JSON.stringify({ code: code })
                });
                const data = await res.json();

                if(data.status === 'success') {
                    const potentialDiscount = parseInt(data.discount_amount);
                    
                    // --- VALIDASI UTAMA ---
                    // Cek apakah Subtotal mencukupi nilai Voucher
                    if (subtotal < potentialDiscount) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal Pakai Voucher',
                            text: `Minimal belanja harus Rp ${formatRupiah(potentialDiscount)} untuk menggunakan voucher ini. Total belanja kamu cuma Rp ${formatRupiah(subtotal)}.`,
                        });
                        return; // Stop, jangan pasang diskon
                    }

                    // Jika lolos validasi, baru pasang diskon
                    discountAmount = potentialDiscount;
                    
                    msgEl.innerText = `Kode ${data.title} dipakai!`;
                    msgEl.className = "text-xs mt-1 font-bold text-green-600 block";
                    renderCart(); // Update tampilan harga
                    Swal.fire('Sukses', `Hemat ${formatRupiah(discountAmount)}`, 'success');

                } else {
                    discountAmount = 0;
                    msgEl.innerText = data.message;
                    msgEl.className = "text-xs mt-1 font-bold text-red-500 block";
                    renderCart();
                }
            } catch(e) {
                console.error(e);
                Swal.fire('Error', 'Gagal koneksi database', 'error');
            }
        }
        // ----------------------------------


        // ========== FLOW KONFIRMASI PESANAN ==========
        function konfirmasiPesanan() {
            if (cart.length === 0) return;
            


            // STEP 1: Pilih metode pengiriman
            Swal.fire({
                title: 'Pilih Metode Pengiriman',
                html: `
                    <p class="text-gray-500 text-sm mb-4">Bagaimana Anda ingin menerima pesanan?</p>
                    <div style="display:flex; flex-direction:column; gap:12px;">
                        <button id="btn-ditempat" style="padding:16px; border:2px solid #e5e7eb; border-radius:12px; background:white; cursor:pointer; display:flex; align-items:center; gap:12px; transition:all 0.2s;" 
                            onmouseover="this.style.borderColor='#2563eb'; this.style.background='#eff6ff'" 
                            onmouseout="this.style.borderColor='#e5e7eb'; this.style.background='white'">
                            <div style="width:48px;height:48px;background:#dbeafe;border-radius:12px;display:flex;align-items:center;justify-content:center;">
                                <i class="fa-solid fa-store" style="font-size:20px;color:#2563eb;"></i>
                            </div>
                            <div style="text-align:left;">
                                <div style="font-weight:700;color:#1f2937;">Bayar di Tempat</div>
                                <div style="font-size:12px;color:#6b7280;">Ambil dan bayar langsung di kasir</div>
                            </div>
                        </button>
                        <button id="btn-kurir" style="padding:16px; border:2px solid #e5e7eb; border-radius:12px; background:white; cursor:pointer; display:flex; align-items:center; gap:12px; transition:all 0.2s;"
                            onmouseover="this.style.borderColor='#16a34a'; this.style.background='#f0fdf4'" 
                            onmouseout="this.style.borderColor='#e5e7eb'; this.style.background='white'">
                            <div style="width:48px;height:48px;background:#dcfce7;border-radius:12px;display:flex;align-items:center;justify-content:center;">
                                <i class="fa-brands fa-whatsapp" style="font-size:24px;color:#16a34a;"></i>
                            </div>
                            <div style="text-align:left;">
                                <div style="font-weight:700;color:#1f2937;">Diantar oleh Kurir</div>
                                <div style="font-size:12px;color:#6b7280;">Pesan via WhatsApp, diantar ke rumah</div>
                            </div>
                        </button>
                    </div>
                `,
                showConfirmButton: false,
                showCloseButton: true,
                didOpen: () => {
                    document.getElementById('btn-ditempat').addEventListener('click', () => {
                        Swal.close();
                        prosesKonfirmasiPembayaran('Bayar di Tempat');
                    });
                    document.getElementById('btn-kurir').addEventListener('click', () => {
                        Swal.close();
                        kirimKeWhatsApp();
                    });
                }
            });
        }

        // STEP 2a: Kirim ke WhatsApp dengan template
        function kirimKeWhatsApp() {
            const paymentMethod = document.querySelector('input[name="payment"]:checked').value;
            const invoiceNumber = 'INV-' + Math.floor(Date.now() / 1000);
            
            // Buat daftar item belanja
            let itemList = '';
            cart.forEach((item, i) => {
                itemList += `${i+1}. ${item.name} x${item.qty} = ${formatRupiah(item.price * item.qty)}\n`;
            });

            // Template WhatsApp
            const template = `*PESANAN BARU - IMAMART*\n` +
                `━━━━━━━━━━━━━━━━━━\n` +
                `No. Invoice: ${invoiceNumber}\n\n` +
                `*Detail Pesanan:*\n${itemList}\n` +
                `Subtotal: ${formatRupiah(subtotal)}\n` +
                (discountAmount > 0 ? `🏷️ Diskon: -${formatRupiah(discountAmount)}\n` : '') +
                `Biaya Admin: ${formatRupiah(1000)}\n` +
                `*TOTAL: ${formatRupiah(grandTotal)}*\n` +
                `Metode Bayar: ${paymentMethod}\n\n` +
                `━━━━━━━━━━━━━━━━━━\n` +
                `*Data Penerima:*\n` +
                `Nama Penerima: \n` +
                `No. HP: \n` +
                `Alamat Lengkap: \n` +
                `Patokan/Catatan: \n\n` +
                `Terima kasih sudah berbelanja di IMAMART! 🛒`;

            // Nomor WhatsApp toko (ganti sesuai kebutuhan)
            const nomorWA = '6289502506737';
            const waURL = `https://wa.me/${nomorWA}?text=${encodeURIComponent(template)}`;
            
            // Buka WhatsApp di tab baru
            window.open(waURL, '_blank');

            // Lanjut ke konfirmasi pembayaran
            setTimeout(() => {
                prosesKonfirmasiPembayaran('Diantar Kurir (WhatsApp)');
            }, 500);
        }

        // STEP 2b / 3: Proses Konfirmasi Pembayaran
        async function prosesKonfirmasiPembayaran(metode) {
            const paymentMethod = document.querySelector('input[name="payment"]:checked').value;
            const invoiceNumber = 'INV-' + Math.floor(Date.now() / 1000);

            // Siapkan Struk
            const now = new Date();
            document.getElementById('struk-date').innerText = now.toLocaleDateString('id-ID');
            document.getElementById('struk-id').innerText = invoiceNumber;
            document.getElementById('struk-payment').innerText = paymentMethod;
            document.getElementById('struk-metode-kirim').innerText = metode;
            
            let strukHtml = '';
            cart.forEach(item => {
                strukHtml += `<div class="flex justify-between mb-1"><span>${item.name} x${item.qty}</span><span>${formatRupiah(item.price * item.qty)}</span></div>`;
            });
            document.getElementById('struk-items').innerHTML = strukHtml;
            document.getElementById('struk-subtotal').innerText = formatRupiah(subtotal);
            document.getElementById('struk-discount').innerText = discountAmount > 0 ? '-' + formatRupiah(discountAmount) : '-';
            document.getElementById('struk-total').innerText = formatRupiah(grandTotal);

            // Konfirmasi Pembayaran
            const konfirmasi = await Swal.fire({
                title: 'Konfirmasi Pembayaran',
                html: `
                    <div style="text-align:left; font-size:14px;">
                        <div style="background:#eff6ff; padding:12px; border-radius:8px; margin-bottom:12px;">
                            <p style="margin:0 0 4px 0;"><strong>Metode Pengiriman:</strong></p>
                            <p style="margin:0; color:#2563eb; font-weight:700;">${metode}</p>
                        </div>
                        <div style="display:flex; justify-content:space-between; margin-bottom:6px;">
                            <span>Total Tagihan</span>
                            <strong>${formatRupiah(grandTotal)}</strong>
                        </div>
                    </div>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#2563eb',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Bayar Sekarang',
                cancelButtonText: 'Batal'
            });

            if (!konfirmasi.isConfirmed) return;

            // Kirim ke Backend
            try {
                const response = await fetch('process_order.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        invoice_number: invoiceNumber,
                        total_amount: grandTotal,
                        payment_method: paymentMethod
                    })
                });
                
                const result = await response.json();

                if (result.status === 'success') {
                    // Hapus cart
                    localStorage.removeItem('imamart_cart');
                    cart = [];
                    
                    // STEP FINAL: Pilih Selesai atau Cetak Struk
                    Swal.fire({
                        title: 'Pembayaran Berhasil!',
                        html: `
                            <div style="margin-bottom:8px;">
                                <div style="width:64px;height:64px;background:#dcfce7;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 12px;">
                                    <i class="fa-solid fa-check" style="font-size:28px;color:#16a34a;"></i>
                                </div>
                                <p style="color:#6b7280; font-size:14px;">Terima kasih telah berbelanja di IMAMART!</p>
                            </div>
                        `,
                        showCancelButton: true,
                        confirmButtonColor: '#2563eb',
                        cancelButtonColor: '#16a34a',
                        confirmButtonText: '<i class="fa-solid fa-print mr-1"></i> Cetak Struk',
                        cancelButtonText: '<i class="fa-solid fa-check mr-1"></i> Selesai',
                        reverseButtons: true,
                        allowOutsideClick: false
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Cetak struk lalu kembali ke home
                            window.print();
                            setTimeout(() => { window.location.href = 'home.php'; }, 1000);
                        } else {
                            // Selesai - langsung ke home
                            window.location.href = 'home.php';
                        }
                    });

                } else {
                    Swal.fire('Gagal!', result.message, 'error');
                }

            } catch (error) {
                console.error(error);
                Swal.fire('Error!', 'Terjadi kesalahan koneksi.', 'error');
            }
        }

        function removeItem(index) {
            cart.splice(index, 1);
            localStorage.setItem('imamart_cart', JSON.stringify(cart));
            // Saat item dihapus, reset diskon agar dihitung ulang jika ada syarat min belanja (optional, disini saya reset diskon agar user apply lagi)
            discountAmount = 0; 
            document.getElementById('voucher-code').value = '';
            document.getElementById('voucher-msg').classList.add('hidden');
            
            renderCart();
        }

        function clearCart() {
            if(confirm('Kosongkan?')) {
                localStorage.removeItem('imamart_cart');
                cart = [];
                discountAmount = 0;
                renderCart();
            }
        }

        function formatRupiah(number) {
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(number);
        }
    </script>
    <script>
        function toggleMobileMenu() {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
            menu.classList.toggle('flex');
        }
    </script>
</body>
</html>