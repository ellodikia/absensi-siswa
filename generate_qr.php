<?php
// Gunakan library PHPQRCode atau API eksternal untuk simpelnya
$secret_word = "SEKOLAH_AMAN_2024";
$timestamp = floor(time() / 30); // Berubah tiap 30 detik
$token = md5($secret_word . $timestamp);

$qr_data = "https://domain-kamu.com/proses_absen.php?token=" . $token;
$qr_api = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=" . urlencode($qr_data);
?>

<div class="text-center">
    <h2 class="text-xl font-bold mb-4">Scan untuk Absen (Berlaku 30 detik)</h2>
    <img src="<?php echo $qr_api; ?>" alt="QR Code" class="mx-auto border-8 border-white shadow-lg">
    <p id="timer" class="mt-2 text-red-500 font-mono"></p>
</div>

<script>
    // Refresh otomatis setiap 30 detik agar token ganti
    setTimeout(() => { location.reload(); }, 30000);
</script>