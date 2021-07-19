<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Track extends CI_Controller {

    public function __construct(){
        parent::__construct();
        $this->load->model('track_model');
        $this->load->library('user_agent');
        $this->load->helper('cookie');
    }

    public function index(){
        $postIp = $this->input->ip_address();
        $postPath = $this->input->post('path', true);
        $postLang = $this->input->post('lang', true);
        $postZone = $this->input->post('zone', true);
        $postUserAgent = $this->input->user_agent(true);
        $postId = hexdec(crc32($postIp.$postLang.$postZone.$postUserAgent));
        $postTime = time();

        if(!file_exists(APPPATH.'views/'.$postPath.'.php'))
        {
            $postPath = '';
        }

        if ($this->agent->is_referral() && strpos($_SERVER['HTTP_REFERER'], $_SERVER['SERVER_NAME']) === false){
            $postRef = $this->agent->referrer();
        }else{
            $postRef = '';
        }

        $insertVisitor = array(
            'id' => $postId,
            'ip' => $postIp,
            'zone' => $postZone,
            'lang' => $postLang,
            'agent' => $postUserAgent
        );

        $insertVisit = array(
            'visitor_id' => $postId,
            'path' => $postPath,
            'time' => $postTime,
            'ref' => $postRef
        );

        $this->track_model->create_visitor($postId, $postPath, $insertVisitor, $insertVisit);

        exit;
    }

}