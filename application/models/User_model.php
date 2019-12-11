<?php defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model {

    public function find($userid)
    {
        return $this->db->get_where("users", compact('userid'))->first_row();
    }

}