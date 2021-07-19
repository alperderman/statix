<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Track_model extends CI_Model {

    public function __construct(){
        parent::__construct();
        $this->load->database('track');
    }

    public function insert_track($fields, $table){
        $fields = $this->escape_input($fields);
        $table = $this->escape_input($table);
        $this->db->insert($table, $fields);
    }

    public function escape_input($input){
        if(!is_array($input)){
            $esc = $this->db->escape_str($input);
        }else{
            $esc = array();
            if(!empty($input)){
                foreach($input as $key => $val){  
                    $esc[$key] = $this->db->escape_str($val);
                }
            }
        }
        return $esc; 
    }

    public function create_visitor($id, $path, $insertVisitor, $insertVisit){
        $id = $this->escape_input($id);
        $path = $this->escape_input($path);

        $sqlVisitor = 'select exists(select 1 from visitors where id = "'.$id.'") as bool;';
        $checkVisitor = $this->db->query($sqlVisitor);
        $checkVisitor = $checkVisitor->result_array();

        if($checkVisitor[0]['bool'] == 0){
            $this->insert_track($insertVisitor, 'visitors');
        }

        $cdTime = time() - (60 * 60 * 12);
        $sqlVisit = 'select exists(select 1 from visits where visitor_id = "'.$id.'" and path = "'.$path.'" and time = (select max(time) from visits where visitor_id = "'.$id.'" and path = "'.$path.'") and time > '.$cdTime.') as bool;';
        $checkVisit = $this->db->query($sqlVisit);
        $checkVisit = $checkVisit->result_array();

        if($checkVisit[0]['bool'] == 0){
            $this->insert_track($insertVisit, 'visits');
        }else{
            $time = time();
            $sqlUpdate = 'update visits set time = '.$time.' where visitor_id = "'.$id.'" and path = "'.$path.'" and time = (select max(time) from visits where visitor_id = "'.$id.'" and path = "'.$path.'");';
            $this->db->query($sqlUpdate);
        }
    }

}