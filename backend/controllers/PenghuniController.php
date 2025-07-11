<?php
class PenghuniController {
    private $pdo;
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function handle($method, $uri) {
        $id = null;
        if (preg_match('#^/api/penghuni/(\d+)$#', $uri, $matches)) {
            $id = (int)$matches[1];
        }
        switch ($method) {
            case 'GET':
                if ($id) {
                    $this->show($id);
                } else {
                    $this->index();
                }
                break;
            case 'POST':
                $this->store();
                break;
            case 'PUT':
            case 'PATCH':
                if ($id) {
                    $this->update($id);
                } else {
                    http_response_code(400);
                    echo json_encode(['error' => 'ID required']);
                }
                break;
            case 'DELETE':
                if ($id) {
                    $this->destroy($id);
                } else {
                    http_response_code(400);
                    echo json_encode(['error' => 'ID required']);
                }
                break;
            default:
                http_response_code(405);
                echo json_encode(['error' => 'Method not allowed']);
        }
    }

    private function index() {
        try {
            $stmt = $this->pdo->query('SELECT * FROM tb_penghuni');
            $data = $stmt->fetchAll();
            echo json_encode($data);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    private function show($id) {
        try {
            $stmt = $this->pdo->prepare('SELECT * FROM tb_penghuni WHERE id = ?');
            $stmt->execute([$id]);
            $data = $stmt->fetch();
            if ($data) {
                echo json_encode($data);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Not found']);
            }
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    private function store() {
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$this->validate($input)) return;
        try {
            $stmt = $this->pdo->prepare('INSERT INTO tb_penghuni (nama, no_ktp, no_hp, tgl_masuk, tgl_keluar) VALUES (?, ?, ?, ?, ?)');
            $stmt->execute([
                $input['nama'],
                $input['no_ktp'],
                $input['no_hp'],
                $input['tgl_masuk'],
                $input['tgl_keluar'] ?? null
            ]);
            echo json_encode(['success' => true, 'id' => $this->pdo->lastInsertId()]);
        } catch (PDOException $e) {
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    private function update($id) {
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$this->validate($input, true)) return;
        try {
            $stmt = $this->pdo->prepare('UPDATE tb_penghuni SET nama=?, no_ktp=?, no_hp=?, tgl_masuk=?, tgl_keluar=? WHERE id=?');
            $stmt->execute([
                $input['nama'],
                $input['no_ktp'],
                $input['no_hp'],
                $input['tgl_masuk'],
                $input['tgl_keluar'] ?? null,
                $id
            ]);
            echo json_encode(['success' => true]);
        } catch (PDOException $e) {
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    private function destroy($id) {
        try {
            $stmt = $this->pdo->prepare('DELETE FROM tb_penghuni WHERE id = ?');
            $stmt->execute([$id]);
            echo json_encode(['success' => true]);
        } catch (PDOException $e) {
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    private function validate($input, $isUpdate = false) {
        $required = ['nama', 'no_ktp', 'no_hp', 'tgl_masuk'];
        foreach ($required as $field) {
            if (empty($input[$field])) {
                http_response_code(422);
                echo json_encode(['error' => "$field wajib diisi"]);
                return false;
            }
        }
        if (!preg_match('/^[0-9]{16}$/', $input['no_ktp'])) {
            http_response_code(422);
            echo json_encode(['error' => 'no_ktp harus 16 digit']);
            return false;
        }
        if (!preg_match('/^08[0-9]{8,11}$/', $input['no_hp'])) {
            http_response_code(422);
            echo json_encode(['error' => 'no_hp tidak valid']);
            return false;
        }
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $input['tgl_masuk'])) {
            http_response_code(422);
            echo json_encode(['error' => 'tgl_masuk format YYYY-MM-DD']);
            return false;
        }
        if (!empty($input['tgl_keluar']) && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $input['tgl_keluar'])) {
            http_response_code(422);
            echo json_encode(['error' => 'tgl_keluar format YYYY-MM-DD']);
            return false;
        }
        return true;
    }
} 