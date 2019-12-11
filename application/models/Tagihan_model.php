<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Tagihan_model extends CI_Model {

    public function find($no_tagihan, $bulan, $tahun, $lunas = 'N')
    {
        $conditions = compact('no_tagihan', 'bulan', 'tahun', 'lunas');

        return $this->db->get_where('tagihan', $conditions)->first_row();
    }

    public function update($no_tagihan, $data)
    {
        return $this->db->update('tagihan', $data, compact('no_tagihan'));
    }
}