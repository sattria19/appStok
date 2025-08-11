<?php include 'header.php'; ?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">📦 Scan QR Code Barang Masuk</h4>
                </div>
                <div class="card-body">
                    <!-- QR Scanner Container -->
                    <div class="qr-scanner-container mb-3">
                        <div id="qr-reader" style="width: 100%; max-width: 500px; margin: 0 auto;"></div>
                    </div>

                    <!-- Status dan Hasil -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="scanner-status" class="form-label">Status Scanner:</label>
                                <div id="scanner-status" class="alert alert-info">
                                    Memulai kamera...
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="scan-result" class="form-label">Hasil Scan:</label>
                                <input type="text" class="form-control" id="scan-result" readonly placeholder="Kode QR akan muncul di sini">
                            </div>
                        </div>
                    </div>

                    <!-- Hasil Pengecekan Database -->
                    <div id="barcode-info" class="card mt-3" style="display: none;">
                        <div class="card-header">
                            <h6 class="mb-0">📋 Informasi Produk</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Nama Barang:</strong>
                                    <p id="nama-barang" class="mb-2">-</p>
                                </div>
                                <div class="col-md-6">
                                    <strong>Kode Barcode:</strong>
                                    <p id="kode-barcode" class="mb-2">-</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Status:</strong>
                                    <p id="status-barang" class="mb-2">
                                        <span id="status-badge" class="badge">-</span>
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <strong>Diupdate:</strong>
                                    <p id="updated-at" class="mb-2">-</p>
                                </div>
                            </div>
                            <div class="mt-3" id="action-buttons">
                                <button type="button" class="btn btn-primary" id="proses-btn" onclick="prosesBarangMasuk()">
                                    📦 Proses Barang Masuk
                                </button>
                                <button type="button" class="btn btn-secondary" onclick="resetForm()">
                                    🔄 Reset
                                </button>
                            </div>
                        </div>
                    </div>



                    <!-- Tombol Kontrol -->
                    <div class="mt-3 text-center">
                        <button type="button" class="btn btn-success mb-2" id="start-scan" onclick="startScanner()">
                            📷 Mulai Scanner
                        </button>
                        <button type="button" class="btn btn-danger mb-2" id="stop-scan" onclick="stopScanner()">
                            ⏹️ Hentikan Scanner
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- QR Code Scanner Library -->
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

<script>
    let html5QrcodeScanner;
    let isScanning = false;

    function onScanSuccess(decodedText, decodedResult) {
        // Tampilkan hasil scan
        document.getElementById('scan-result').value = decodedText;
        document.getElementById('scanner-status').innerHTML = `
        <span class="text-success">✅ QR Code berhasil dibaca!</span>
    `;
        document.getElementById('scanner-status').className = 'alert alert-success';

        // Proses barcode
        processBarcode(decodedText);

        // Optional: Hentikan scanner setelah berhasil scan
        // stopScanner();
    }

    function onScanFailure(error) {
        // Handle scan failure - bisa diabaikan untuk error normal
        // console.warn(`QR scan error: ${error}`);
    }

    function startScanner() {
        if (isScanning) return;

        const config = {
            fps: 10,
            qrbox: {
                width: 250,
                height: 250
            },
            aspectRatio: 1.0,
            rememberLastUsedCamera: true
        };

        html5QrcodeScanner = new Html5Qrcode("qr-reader");

        html5QrcodeScanner.start({
                facingMode: "environment"
            }, // Gunakan kamera belakang jika tersedia
            config,
            onScanSuccess,
            onScanFailure
        ).then(() => {
            isScanning = true;
            document.getElementById('scanner-status').innerHTML = `
            <span class="text-primary">📷 Scanner aktif - Arahkan kamera ke QR code</span>
        `;
            document.getElementById('scanner-status').className = 'alert alert-primary';
            document.getElementById('start-scan').disabled = true;
            document.getElementById('stop-scan').disabled = false;
        }).catch(err => {
            document.getElementById('scanner-status').innerHTML = `
            <span class="text-danger">❌ Error: ${err}</span>
        `;
            document.getElementById('scanner-status').className = 'alert alert-danger';
        });
    }

    function stopScanner() {
        if (!isScanning) return;

        html5QrcodeScanner.stop().then(() => {
            isScanning = false;
            document.getElementById('scanner-status').innerHTML = `
            <span class="text-secondary">⏹️ Scanner dihentikan</span>
        `;
            document.getElementById('scanner-status').className = 'alert alert-secondary';
            document.getElementById('start-scan').disabled = false;
            document.getElementById('stop-scan').disabled = true;
        }).catch(err => {
            console.error("Error stopping scanner:", err);
        });
    }

    function processBarcode(code = null) {
        const barcodeValue = code || document.getElementById('manual-input').value;

        if (!barcodeValue) {
            alert('Silakan scan QR code atau masukkan kode secara manual');
            return;
        }

        // Clear manual input after processing
        if (document.getElementById('manual-input')) {
            document.getElementById('manual-input').value = '';
        }

        // Update scan result display
        document.getElementById('scan-result').value = barcodeValue;

        // Show loading
        showLoading('Memeriksa barcode di database...');

        // Cek barcode di database menggunakan AJAX
        fetch('cek-barcode.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'kode_barcode=' + encodeURIComponent(barcodeValue)
            })
            .then(response => response.json())
            .then(data => {
                hideLoading();

                if (data.status === 'success') {
                    // Barcode ditemukan - tampilkan info produk
                    showBarcodeInfo(data.data);
                    showAlert('success', '✅ Barcode ditemukan di database!');
                } else {
                    // Barcode tidak ditemukan
                    hideBarcodeInfo();
                    showAlert('error', '❌ ' + data.message);
                }
            })
            .catch(error => {
                hideLoading();
                console.error('Error:', error);
                showAlert('error', '❌ Terjadi kesalahan saat memeriksa barcode');
            });
    }

    function showBarcodeInfo(data) {
        document.getElementById('nama-barang').textContent = data.nama_barang;
        document.getElementById('kode-barcode').textContent = data.kode_barcode;
        document.getElementById('updated-at').textContent = new Date(data.updated_at).toLocaleString('id-ID');

        // Update status dengan badge
        const statusBadge = document.getElementById('status-badge');
        const status = data.status || 'pending';

        statusBadge.textContent = status;

        // Set warna badge berdasarkan status
        statusBadge.className = 'badge ';
        if (status === 'di_gudang') {
            statusBadge.className += 'bg-success';
        } else if (status === 'pending') {
            statusBadge.className += 'bg-warning text-dark';
        } else if (status === 'terjual') {
            statusBadge.className += 'bg-danger text-white';
        } else {
            statusBadge.className += 'bg-secondary';
        }

        // Sembunyikan tombol proses jika status sudah di_gudang
        const prosesBtn = document.getElementById('proses-btn');
        if (status === 'di_gudang' || status === 'terjual') {
            prosesBtn.style.display = 'none';

            // Tampilkan pesan bahwa barang sudah di gudang
            document.getElementById('scanner-status').innerHTML = `
                <span class="text-success">✅ Barang sudah berada di gudang</span>
            `;
            document.getElementById('scanner-status').className = 'alert alert-success';
        } else {
            prosesBtn.style.display = 'inline-block';
            prosesBtn.disabled = false; // Pastikan tombol tidak disabled
        }

        document.getElementById('barcode-info').style.display = 'block';
    }

    function hideBarcodeInfo() {
        document.getElementById('barcode-info').style.display = 'none';
    }

    function showLoading(message) {
        document.getElementById('scanner-status').innerHTML = `
            <span class="text-info">🔄 ${message}</span>
        `;
        document.getElementById('scanner-status').className = 'alert alert-info';
    }

    function hideLoading() {
        // Status akan diupdate oleh fungsi lain
    }

    function showAlert(type, message) {
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        document.getElementById('scanner-status').innerHTML = `
            <span>${message}</span>
        `;
        document.getElementById('scanner-status').className = `alert ${alertClass}`;
    }

    function prosesBarangMasuk() {
        const kodeBarcode = document.getElementById('scan-result').value;

        if (!kodeBarcode) {
            alert('Tidak ada barcode yang dipilih');
            return;
        }

        if (confirm(`Apakah Anda yakin ingin memproses barang masuk untuk kode: ${kodeBarcode}?`)) {
            // Show loading
            showLoading('Memproses barang masuk...');

            // Disable tombol proses
            document.getElementById('proses-btn').disabled = true;

            // Proses barang masuk menggunakan AJAX
            fetch('proses-barang-masuk.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'kode_barcode=' + encodeURIComponent(kodeBarcode)
                })
                .then(response => {
                    // Debug: log response
                    console.log('Response status:', response.status);
                    console.log('Response headers:', response.headers.get('content-type'));

                    if (!response.ok) {
                        throw new Error('HTTP error! status: ' + response.status);
                    }

                    return response.text(); // Gunakan text() dulu untuk debug
                })
                .then(text => {
                    console.log('Raw response:', text); // Debug: lihat response mentah

                    try {
                        const data = JSON.parse(text);
                        console.log('Parsed data:', data); // Debug: lihat data parsed

                        if (data.status === 'success') {
                            // Berhasil diproses
                            showAlert('success', '✅ ' + data.message);

                            // Update status di tampilan
                            const statusBadge = document.getElementById('status-badge');
                            statusBadge.textContent = 'di_gudang';
                            statusBadge.className = 'badge bg-success';

                            // Sembunyikan tombol proses
                            document.getElementById('proses-btn').style.display = 'none';

                            // Reset disabled state untuk proses selanjutnya
                            document.getElementById('proses-btn').disabled = false;

                            // Play success sound (opsional)
                            // playSuccessSound();

                        } else {
                            // Gagal diproses
                            showAlert('error', '❌ ' + data.message);
                            document.getElementById('proses-btn').disabled = false;
                        }
                    } catch (parseError) {
                        console.error('JSON Parse Error:', parseError);
                        console.error('Raw text that failed to parse:', text);
                        showAlert('error', '❌ Response tidak valid dari server');
                        document.getElementById('proses-btn').disabled = false;
                    }
                })
                .catch(error => {
                    console.error('Fetch Error:', error);
                    showAlert('error', '❌ Terjadi kesalahan saat memproses barang masuk: ' + error.message);
                    document.getElementById('proses-btn').disabled = false;
                });
        }
    }

    function resetForm() {
        document.getElementById('scan-result').value = '';
        if (document.getElementById('manual-input')) {
            document.getElementById('manual-input').value = '';
        }
        hideBarcodeInfo();

        // Reset tombol proses ke keadaan normal
        const prosesBtn = document.getElementById('proses-btn');
        prosesBtn.disabled = false;
        prosesBtn.style.display = 'inline-block';

        document.getElementById('scanner-status').innerHTML = `
            <span class="text-secondary mt-2">🔄 Form direset</span>
        `;
        document.getElementById('scanner-status').className = 'alert alert-secondary';
    }

    // Auto start scanner saat halaman dimuat
    document.addEventListener('DOMContentLoaded', function() {
        // Delay sedikit untuk memastikan elemen sudah siap
        setTimeout(() => {
            startScanner();
        }, 1000);

        // Event listener untuk input manual - Enter key
        document.getElementById('manual-input').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                processBarcode();
            }
        });
    });

    // Cleanup saat halaman ditutup
    window.addEventListener('beforeunload', function() {
        if (isScanning) {
            stopScanner();
        }
    });
</script>

<?php include 'footer.php'; ?>