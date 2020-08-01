<?php
defined('BASEPATH') or exit('No direct script access allowed');
error_reporting(E_ALL);
ini_set("display_errors", 1);
header('Access-Control-Allow-Origin: *');
date_default_timezone_set('Asia/Bangkok');

class Invoice_info extends CI_Controller
{

    public function __construct()
    {

        parent::__construct();

        $this->load->library('session');
        $this->load->model('invoice_details');
    }

    public function invoice_add_details()
    {
        $this->load->model('invoice_details');
        $latest_invoice_id = $_POST['latest_invoice_id'];
        $description = $_POST['description'];
        $price = $_POST['price'];
        $quantity = $_POST['quantity'];
        $subtotal = $_POST['subtotal'];
        $index = 0;

        for ($index = 0; $index < count($description); $index++) {
            $index;
            $invoice_detail = array("invoice_id" => $latest_invoice_id, "description" => $description[$index], "price" => $price[$index], "qty" => $quantity[$index], "subtotal" => $subtotal[$index]);
            $this->invoice_details->add_invoice_details($invoice_detail);
        }
    }

    public function invoice()
    {
        $this->load->model('invoice_details');

        if (isset($_POST['invoice_name'])) {
            $invoice_name = $_POST['invoice_name'];
            $date = date('Y-m-d');
            $amount = $_POST['amount'];

            $invoice = array(
                "invoice_name" => $invoice_name,
                "date_added" => $date,
                "amount" => $amount
            );
            $this->invoice_details->add_invoice($invoice);
            echo "recieve";
        } else {
            echo 'do not recieve';
        }
    }
    
    public function upload_invoice(){
        $file_name = 'Invoice - ' . date('Ymdhms') . '.pdf';
        move_uploaded_file($_FILES["data"]["tmp_name"], './uploads/invoice/' . $file_name);
        echo json_encode(array(
            'url' => 'http://34.74.113.124/telco/uploads/invoice/' . $file_name
        ));
    }

    public function get_invoice()
    {
        $this->load->model('invoice_details');
        $invoice['data'] = $this->invoice_details->get_invoice_details();
        echo json_encode($invoice);
    }
    public function get_latest_invoice_id()
    {
        $this->load->model('invoice_details');
        $latest_invoice_id = $this->invoice_details->get_new_invoice_id();
        foreach ($latest_invoice_id as $iv_id) {
            echo $iv_id->invoice_id;
        }
    }
    public function delete_invoice()
    {
        $this->load->model('invoice_details');
        if (isset($_POST['invoice_id'])) {
            $this->load->model('invoice_details');
            $this->invoice_details->delete_invoice($_POST['invoice_id']);
        }
    }

    public function delete_invoice_content_details()
    {
        $this->load->model('invoice_details');
        if (isset($_POST['invoice_id'])) {
            $this->load->model('invoice_details');
            $this->invoice_details->delete_invoice_details($_POST['invoice_id']);
        }
    }

    public function invoice_details_pop()
    {
        $id = $this->input->post('invoice_id');
        $this->load->model('invoice_details');
        $invs_details = $this->invoice_details->invoice_details_single($id);

        $invoice_info = "";
        if ($invs_details) {
            foreach ($invs_details as $invd) {
                if ($invd->qty != "dynamic") {
                    $invoice_info .=
                        '<tr>
                        <td>' . $invd->qty . '</td>
                        <td>' . $invd->description . '</td>
                        <td>' . $invd->price . '</td>
                        <td>' . $invd->subtotal . '</td>
                    </tr>';
                } else {
                    $invoice_info .=
                        '<tr>
                        <td>' . $invd->description . '</td>
                        <td>' . $invd->qty . '</td>
                        <td>' . $invd->price . '</td>
                        <td>' . $invd->subtotal . '</td>
                    </tr>

                ';
                }
            }
        }

        echo $invoice_info;
    }
    public function invoice_details_pdf()
    {
        $id = $this->input->post('invoice_id');
        $this->load->model('invoice_details');
        $invs_details = $this->invoice_details->invoice_details_single($id);

        $invoice_info = "";
        if ($invs_details) {
            foreach ($invs_details as $invd) {
                if ($invd->qty != "dynamic") {
                    $invoice_info .=
                        '<tr>
                        <td>' . $invd->description . '</td>
                        <td class = "text-center">' . $invd->qty . '</td>
                        <td class = "text-center">' . $invd->price . '</td>
                        <td class = "text-right">' . $invd->subtotal . '</td>
                    </tr>';
                } else {
                    $invoice_info .=
                        '<tr>
                        <td>' . $invd->description . '</td>
                        <td class = "text-center">' . $invd->qty . '</td>
                        <td class = "text-center">' . $invd->price . '</td>
                        <td class = "text-right">' . $invd->subtotal . '</td>
                    </tr>';
                }
            }
        }

        echo $invoice_info;
    }

    public function get_pdf()
    {
        $this->load->library('session');
        $pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetTitle('Invoice');
        $pdf->SetHeaderMargin(20);
        $pdf->SetTopMargin(10);
        $pdf->setFooterMargin(10);
        $pdf->SetAutoPageBreak(true);
        $pdf->SetAuthor('Yolo-Telco');
        $pdf->SetDisplayMode('real', 'default');
        $pdf->AddPage();
        $html = '';
        $html .= "<h4>Reciever</h4>";
        $html .= "<label>Name:</label>";
    }
}
