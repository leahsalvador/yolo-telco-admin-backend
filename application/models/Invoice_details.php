<?php 
class Invoice_details extends CI_Model{
    function __construct(){
        parent:: __construct();
        $this->load->database();
    
    }
function get_invoice_details(){
    $q = $this->db->get('invoice');
    if($q->num_rows()>0){
        return $q->result();
    }else{
        return false;
    }
}
    function add_invoice_details($invoice_detail){
        if($this->db->insert('invoice_details', $invoice_detail)){
            return true;
        }else{
            return false;
        }
    }

    function add_invoice($invoice){
        if($this->db->insert('invoice', $invoice)){
            return true;
        }else{
            return false;
        }
    }

    function delete_invoice($invoice_id){
        $this->db->where('invoice_id',$invoice_id);
        $this->db->delete('invoice');

    }
    function delete_invoice_details($invoice_id){
        $this->db->where('invoice_id',$invoice_id);
        $this->db->delete('invoice_details');
    }

    function get_new_invoice_id(){
    
        $this->db->select('*');
        $this->db->from('invoice');
        $this->db->order_by('invoice_id','DESC');
        $this->db->limit(1);
        $query = $this->db->get();
        return $query->result();
    }

    function invoice_details_single($invoice_id){
    $this->db->select('*');
    $this->db->where_in('invoice_id', $invoice_id);
    $query = $this->db->get("invoice_details");
    if($query->num_rows()>0){
        return $query->result();
    }else{
        return false;
    }
}
 
}
?>