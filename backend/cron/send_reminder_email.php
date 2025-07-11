<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../utils/Mailer.php';

// Query penghuni dengan tagihan H-3
$today = date('Y-m-d');
$target = date('Y-m-d', strtotime('+3 days'));
$sql = "
SELECT p.nama, p.no_hp, p.no_ktp, p.tgl_masuk, p.tgl_keluar, p.id, t.id as tagihan_id, t.bulan, t.jml_tagihan, k.nomor, p.no_hp, p.no_ktp, p.tgl_masuk, p.tgl_keluar
FROM tb_tagihan t
JOIN tb_kmr_penghuni kp ON t.id_kmr_penghuni = kp.id
JOIN tb_penghuni p ON kp.id_penghuni = p.id
JOIN tb_kamar k ON kp.id_kamar = k.id
WHERE t.bulan = DATE_FORMAT('$target', '%Y-%m')
  AND t.jml_tagihan > 0
  AND t.id NOT IN (SELECT id_tagihan FROM tb_bayar WHERE status = 'lunas')
";
$stmt = $pdo->query($sql);
$rows = $stmt->fetchAll();

$mailer = new Mailer();
foreach ($rows as $row) {
    $to = 'user@email.com'; // Ganti dengan email penghuni jika ada kolom email
    $subject = "[Reminder] Tagihan Kos Jatuh Tempo 3 Hari Lagi";
    $body = "<p>Halo {$row['nama']},<br>
    Tagihan kos untuk kamar <b>{$row['nomor']}</b> bulan <b>{$row['bulan']}</b> sebesar <b>Rp".number_format($row['jml_tagihan'],0,',','.')."</b> akan jatuh tempo dalam 3 hari.<br>
    Mohon segera lakukan pembayaran.<br><br>
    Terima kasih.<br>Admin Kos Modern</p>";
    $mailer->send($to, $subject, $body);
}
echo "Reminder email sent: ".count($rows)." penghuni\n"; 