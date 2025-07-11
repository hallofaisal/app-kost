<?php
class SmsGateway {
    private $userkey = 'userkey'; // Ganti dengan userkey Zenziva
    private $passkey = 'passkey'; // Ganti dengan passkey Zenziva
    private $url = 'https://reguler.zenziva.net/apps/smsapi.php';

    public function send($to, $message) {
        $params = [
            'userkey' => $this->userkey,
            'passkey' => $this->passkey,
            'nohp'    => $to,
            'pesan'   => $message
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
        // Bisa tambahkan logika parsing response jika perlu
        return $result;
    }
} 