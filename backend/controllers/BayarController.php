<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../utils/SmsGateway.php';

class BayarController {
    private $pdo;
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function handle($method, $uri) {
        if ($method === 'POST') {
            $this->store();
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
        }
    }

    private function store() {
        $input = json_decode(file_get_contents('php://input'), true);
        // Validasi sederhana
        if (empty($input['id_tagihan']) || empty($input['jml_bayar'])) {
            http_response_code(422);
            echo json_encode(['error' => 'id_tagihan dan jml_bayar wajib diisi']);
            return;
        }
        try {
            $stmt = $this->pdo->prepare('INSERT INTO tb_bayar (id_tagihan, jml_bayar, status) VALUES (?, ?, ?)');
            $status = ($input['jml_bayar'] >= $this->getTagihan($input['id_tagihan'])) ? 'lunas' : 'pending';
            $stmt->execute([
                $input['id_tagihan'],
                $input['jml_bayar'],
                $status
            ]);
            // Ambil info penghuni dan kamar
            $info = $this->getPenghuniInfo($input['id_tagihan']);
            if ($info) {
                $sms = new SmsGateway();
                $pesan = "Pembayaran tagihan kos untuk kamar {$info['nomor']} atas nama {$info['nama']} sejumlah Rp".number_format($input['jml_bayar'],0,',','.')." telah diterima. Status: $status.";
                $sms->send($info['no_hp'], $pesan);
            }
            echo json_encode(['success' => true]);
        } catch (PDOException $e) {
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    private function getTagihan($id_tagihan) {
        $stmt = $this->pdo->prepare('SELECT jml_tagihan FROM tb_tagihan WHERE id = ?');
        $stmt->execute([$id_tagihan]);
        $row = $stmt->fetch();
        return $row ? $row['jml_tagihan'] : 0;
    }

    private function getPenghuniInfo($id_tagihan) {
        $sql = "SELECT p.nama, p.no_hp, k.nomor FROM tb_tagihan t
                JOIN tb_kmr_penghuni kp ON t.id_kmr_penghuni = kp.id
                JOIN tb_penghuni p ON kp.id_penghuni = p.id
                JOIN tb_kamar k ON kp.id_kamar = k.id
                WHERE t.id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id_tagihan]);
        return $stmt->fetch();
    }
} 