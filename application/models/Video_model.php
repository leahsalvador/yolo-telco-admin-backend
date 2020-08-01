<?php
class Video_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }
    
    function get_all_videos()
    {
        $this->db->select('A.*, B.first_name, B.last_name');
        $this->db->from('video_tbl A');
        $this->db->join('users B', 'A.uploader_id = B.user_id', 'left');
        $this->db->where("A.active", 1);
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            return $q->result();
        } else {
            return false;
        }
    }
    
    function add_video($args){
        $this->db->insert('video_tbl', array(
            'video_title' => $args['video_title'],
            'video_description' => $args['video_description'],
            'video_path' => $args['video_path'],
            'confidential' => $args['confidential'] == 'on' ? 1 : 0,
            'uploader_id' => $args['uploader_id']
        ));
    }

    function update_video($args){
        $this->db->set('video_title', $args['video_title']);
        $this->db->set('video_description', $args['video_description']);
        $this->db->set('confidential', $args['confidential']);
        $this->db->where('video_id', $args['video_id']);
        $this->db->update('video_tbl');
    }
    function delete_vid($video_id){
        $this->db->set('active', 0);
        $this->db->where('video_id', $video_id);
        $this->db->update('video_tbl');
    }
}