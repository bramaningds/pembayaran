<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Bayar extends CI_Controller {

    public function index()
    {
        $this->load->model(['user_model', 'tagihan_model']);
        
        $headers = getallheaders();

        try {

            $this->validate($headers);

            $this->verify($headers);

            $pembayaran = $this->process(
                $this->input->post('NO_TAGIHAN'),
                date('m'),
                date('Y'),
                $this->get_kd_pengesahan()
            );

            die(json_encode([
                'RESPONSE_CODE' => '00',
                'RESPONSE_DESC' => 'SUKSES',
                'DATA' => [
                    'NO_TAGIHAN' => $pembayaran->no_tagihan,
                    'KD_PENGESAHAN' => $pembayaran->kd_pengesahan
                ]
            ]));

        } catch (Exception $e) {
            
            die(json_encode([
                'RESPONSE_CODE' => $e->getCode(),
                'RESPONSE_DESC' => $e->getMessage(),
                'DATA'          => NULL
            ]));

        }
    }

    private function validate($headers)
    {
        if (empty($headers['Userid'])) throw new Exception("Security error, Userid kosong", 11);

        if (empty($headers['Signature'])) throw new Exception("Security error, Signature kosong", 12);

        if (empty($headers['Timestamp'])) throw new Exception("Security error, Timestamp kosong", 13);

        if (empty($this->input->post('NO_TAGIHAN'))) throw new Exception("Security error, Nomor Tagihan kosong", 14);
    }

    private function verify($headers)
    {
        $user = $this->user_model->find($headers['Userid']);

        if (empty($user)) throw new Exception("Security error, User tidak ditemukan.", 15);

        $signature = base64_encode(
            hash_hmac("sha256", "{$headers['Userid']}&{$headers['Timestamp']}", $user->key, true)
        );

        if ($signature != $headers['Signature']) throw new Exception("Security error, signature salah.", 16);
    }

    private function process($no_tagihan, $bulan, $tahun, $kd_pengesahan)
    {
        $lunas = 'N';

        $tagihan = $this->tagihan_model->find(
            $this->input->post('NO_TAGIHAN'),
            $bulan,
            $tahun,
            $lunas
        );

        if (empty($tagihan)) throw new Exception("Tagihan tidak ditemukan", 21);

        $updated = $this->tagihan_model->update($tagihan->no_tagihan, [
            'lunas' => 'Y',
            'kd_pengesahan' => $kd_pengesahan
        ]);

        if (! $updated) {
            if ($db_error = $this->db->error()) {
                throw new Exception($db_error['message'], 22);
            }
            else {
                throw new Exception("Error Updating Database.", 23);
            }
        }

        $lunas = 'Y';

        return $this->tagihan_model->find(
            $this->input->post('NO_TAGIHAN'),
            $bulan,
            $tahun,
            $lunas
        );
    }

    private function get_kd_pengesahan()
    {
        return md5(date('Ymd'));
    }
}