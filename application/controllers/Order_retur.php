<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Order_retur extends CI_Controller
{
  public function __construct()
  {
    parent::__construct();

    $this->load->helper(['template', 'authaccess', 'sidebar']);
    checkSessionLog();

    $this->load->library('pdfgenerator');
  }

  public function index()
  {
    $info['title'] = "Data Retur Order";

    $company = $this->company_m->getCompanyById(1);
    $info['company_data'] = $company;
    $info['is_vat_enabled'] = ($company['vat_charge_value'] > 0) ? true : false;
    $info['is_service_enabled'] = ($company['service_charge_value'] > 0) ? true : false;

    // $info['product'] = $this->product_m->getActiveProduct();


    // $info[] = $this->load->view('modals/modal-order', $info);
    // $info[] = $this->load->view('modals/modal-order-edit', $info);

    // $info[] = $this->load->view('modals/modal-retur-order');
    $info[] = $this->load->view('modals/modal-delete');

    renderBackTemplate('retur_orders/index', $info);
  }

  // DataTables Controller Setup
  function show_ajax_retur()
  {
    $list = $this->returorder_m->get_datatables();
    $data = array();
    $no = @$_POST['start'];
    foreach ($list as $item) {
      $no++;
      $id = htmlspecialchars(json_encode($item->order_id));
      $row = array();
      $row[] = $no . ".";
      $row[] = $item->id;
      $row[] = date('d M Y', strtotime($item->retur_date));
      $row[] = $item->order_id;
      $row[] = $item->total;
      $row[] = $item->discount;
      $row[] = "Rp " . number_format($item->net_amount, 0, ',', '.');
      if ($this->session->userdata('role_id') == 'tOBawuCPBAhAZzmyT10F1UbCIc7I42OwLBRmnRhS8e8=') {
        $row[] = $item->user_create;
      }

      // add html for action
      $row[] =
        '
        <!-- <a href="javascript:void(0)" onclick="delete_retur(' . $id . ')" class="btn btn-danger btn-xs" title="delete data"><i class="fa fa-trash-o"></i> Delete</a> -->

        <a target="__blank" href="' . base_url('order_retur/print_retur/' . $item->order_id) . '" class="btn btn-default btn-xs" title="print data"><i class="fa fa-print"></i> Print</a>
      ';
      $data[] = $row;
    }
    $output = array(
      "draw" => @$_POST['draw'],
      "recordsTotal" => $this->returorder_m->count_all(),
      "recordsFiltered" => $this->returorder_m->count_filtered(),
      "data" => $data,
    );
    // output to json format
    echo json_encode($output);
  }
  // DataTables Cntroller End Setup

  public function getCustomerOrder()
  {
    $data = $this->returorder_m->getOrders();
    echo json_encode($data);
  }

  public function getOrderValueById()
  {
    $data = $this->order_m->getOrdersById($this->input->post('order_id', true));
    echo json_encode($data);
  }

  public function getTableProductRow()
  {
    $data = $this->returorder_m->getProductOrder($this->input->post('order_id', true));
    echo json_encode($data);
  }

  public function getProduct()
  {
    $data = $this->category_m->getAllCategory();
    echo json_encode($data);
  }

  public function getProductQtyById()
  {
    $product_id = $this->input->post('product_id');
    if ($product_id) {
      $data = $this->returorder_m->getProductDetailsQtyByID($product_id);
      echo json_encode($data);
    }
  }

  public function getProductValueById()
  {
    $product_id = $this->input->post('product_id');
    if ($product_id) {
      $data = $this->returorder_m->getProductDetailsByID($product_id);
      echo json_encode($data);
    }
  }

  public function create_code()
  {
    $this->db->select('RIGHT(id,4) as kode', FALSE);
    $this->db->order_by('id', 'DESC');
    $this->db->limit(1);

    $query = $this->db->get('order_returns');      //cek dulu apakah ada sudah ada kode di tabel.    

    if ($query->num_rows() <> 0) {
      //jika kode ternyata sudah ada.      
      $data = $query->row();
      $kode = intval($data->kode) + 1;
    } else {
      //jika kode belum ada      
      $kode = 1;
    }
    // https://stackoverflow.com/questions/35583733/right-substring-statement-sql
    $max_code = str_pad($kode, 4, "0", STR_PAD_LEFT); // angka 4 menunjukkan jumlah digit angka 0
    $generate = "RO" . date('d') . date("m") . date('y') . '-' . $max_code;

    // return $generate;
    echo json_encode($generate);
  }

  public function addRetur()
  {
    $info['title'] = "Add New Retur";

    $this->form_validation->set_rules('retur_date', 'retur date', 'trim|required');
    $this->form_validation->set_rules('order_id', 'order id', 'trim|required');

    $this->form_validation->set_rules('product[]', 'product', 'trim|required');

    $file = [
      'id' => $this->input->post('id'),
      'retur_date' => $this->input->post('retur_date', true),
      'order_id' => $this->input->post('order_id', true),
      'gross_amount' => $this->input->post('gross_amount_value', true),
      'discount' => $this->input->post('discount', true),
      'net_amount' => $this->input->post('net_amount_value', true),
      'user_create' => $this->session->userdata('email')
    ];

    if ($this->form_validation->run() == FALSE) {
      renderBackTemplate('retur_orders/add-retur', $info);
    } else {
      $count_product = count($this->input->post('product'));
      for ($j = 0; $j < $count_product; $j++) {
        $getProduct = $this->returorder_m->getProductDetailsByID($this->input->post('product')[$j]);
        $qtyProduct = $this->input->post('qty')[$j];
      }

      if ($qtyProduct <= $getProduct['order_qty']) {
        $this->returorder_m->insertOrderRetur($file);

        // Store to sales_order_details
        for ($i = 0; $i < $count_product; $i++) {
          $products = array(
            'retur_id' => $this->input->post('id'),
            // 'order_id' => $this->input->post('order_id'),
            'product_id' => $this->input->post('product')[$i],
            'description' => $this->input->post('description')[$i],
            'qty' => $this->input->post('qty')[$i],
            'unit_price' => $this->input->post('price_value')[$i],
            'amount' => $this->input->post('amount_value')[$i]
          );

          $this->returorder_m->insertOrderReturDetails($products);

          // Update qty product to increase stock after doing new retur 
          $data_product = $this->product_m->getProductById($this->input->post('product')[$i]);
          $qty_product = (int) $data_product['qty'] + (int) $this->input->post('qty')[$i];

          $update_product = array(
            'qty' => $qty_product
          );

          $this->product_m->update($this->input->post('product')[$i], $update_product);

          // Update qty order details to decrease stock after doing new retur 
          $data_order_detail = $this->returorder_m->getOrdersById($this->input->post('product')[$i]);
          $qty_order_detail = (int) $data_order_detail['qty'] - (int) $this->input->post('qty')[$i];

          $update_order_detail = array(
            'qty' => $qty_order_detail
          );

          $this->returorder_m->updateOrderDetail($this->input->post('product')[$i], $update_order_detail);

          // Update net amount order after doing new retur 
          $amount_order = (int) $data_order_detail['amount'] - (int) $this->input->post('amount_value')[$i];

          $update_amount_order = array(
            'amount' => $amount_order
          );

          $this->returorder_m->updateOrderDetail($this->input->post('product')[$i], $update_amount_order);
        }

        $data_order = $this->returorder_m->getSimpleOrdersById($this->input->post('order_id'));

        $net_amount_order = (int) $data_order['net_amount'] - (int) $this->input->post('net_amount_value');

        $update_netamount_order = array(
          'net_amount' => $net_amount_order
        );

        $this->returorder_m->update($this->input->post('order_id'), $update_netamount_order);

        $this->session->set_flashdata('success', 'Your retur order has been added !');
        redirect('order_retur', 'refresh');
      } else {
        $this->session->set_flashdata('error', 'Your quantity of ' . $getProduct['product_name'] . ' is limit from the previous order !');
        redirect('order_retur/addretur', 'refresh');
      }
    }
  }

  public function edit_order($id)
  {
    $data = $this->order_m->getOrdersById($id);
    echo json_encode($data);
  }

  public function detail_order($id)
  {
    // $id = $this->input->post('id');
    $data = $this->order_m->getProductById($id);
    echo json_encode($data);
  }

  public function print_retur($id)
  {
    $retur_data = $this->returorder_m->getReturOrdersById($id);

    $retur_detail_data = $this->returorder_m->getReturOrderDetailsById($id);

    $company_data = $this->company_m->getCompanyById(1);

    // Checking for discount exist retur_data['discount']
    if ($retur_data['retur_discount']) {
      $discount  = $retur_data['retur_discount'] . '%';
    } else {
      $discount = '-';
    }

    $html = '<!DOCTYPE html>
    <html>
    <head>
      <meta charset="utf-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <title>Screen Printing | Invoice ' . $retur_data['id'] . '</title>
      <!-- Tell the browser to be responsive to screen width -->
      <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
      <!-- Bootstrap 3.3.7 -->
      <link rel="stylesheet" href="' . base_url() . 'back-assets/bower_components/bootstrap/dist/css/bootstrap.min.css">
      <!-- Font Awesome -->
      <link rel="stylesheet" href="' . base_url() . 'back-assets/bower_components/font-awesome/css/font-awesome.min.css">
      <!-- Ionicons -->
      <link rel="stylesheet" href="' . base_url() . 'back-assets/bower_components/Ionicons/css/ionicons.min.css">
      <!-- Theme style -->
      <link rel="stylesheet" href="' . base_url() . 'back-assets/dist/css/AdminLTE.min.css">
    
      <!-- Google Font -->
      <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
    </head>
    <body onload="window.print();">
    <div class="wrapper">
      <!-- Main content -->
      <section class="invoice">
        <!-- title row -->
        <div class="row">
          <div class="col-xs-12">
            <h2 class="page-header">
              <i class="fa fa-globe"></i> ' . $company_data['company_name'] . '.
              <small class="pull-right">Date: ' . date('d M Y', strtotime($retur_data['retur_date'])) . '</small>
            </h2>
          </div>
          <!-- /.col -->
        </div>
        <!-- info row -->
        <div class="row invoice-info">
          <div class="col-sm-4 invoice-col">
            From
            <address>
              <strong>' . $retur_data['customer_name'] . '</strong><br>
              ' . $retur_data['customer_address'] . '<br>
              Phone: ' . $retur_data['customer_phone'] . '<br>
              <!-- Email: john.doe@example.com -->
            </address>
          </div>
          <!-- /.col -->
          <div class="col-sm-4 invoice-col">
            <b>Invoice #' . $retur_data['retur_id'] . '</b><br>
            <br>
            <b>Order ID:</b> ' . $retur_data['id'] . '<br>
            <!-- <b>Payment Due:</b> 2/22/2014<br> -->
            <b>Account:</b> 968-34567
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
    
        <!-- Table row -->
        <div class="row">
          <div class="col-xs-12 table-responsive">
            <table class="table table-striped">
              <thead>
              <tr>
                <th>Product ID</th>
                <th>Product Name</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Amount</th>
              </tr>
              </thead>
              <tbody>';
    foreach ($retur_detail_data as $val) {
      $product_data = $this->product_m->getProductById($val['product_id']);

      $html .= '<tr>
                  <td>' . $val['product_id'] . '</td>
                  <td>
                    ' . $product_data['product_name'] . ' <br>
                    [' . $val['description'] . ']
                  </td>
                  <td>Rp. ' . number_format($val['unit_price'], 0, ',', '.') . '</td>
                  <td>' . $val['qty'] . '</td>
                  <td>Rp. ' . number_format($val['amount'], 0, ',', '.') . '</td>
                </tr>';
    }

    $html .= '</tbody>
            </table>
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
    
        <div class="row">
          <!-- accepted payments column -->
          <div class="col-xs-6">
            <p class="lead">Payment Methods:</p>
            <img src="' . base_url() . 'back-assets/dist/img/credit/visa.png" alt="Visa">
            <img src="' . base_url() . 'back-assets/dist/img/credit/mastercard.png" alt="Mastercard">
            <img src="' . base_url() . 'back-assets/dist/img/credit/american-express.png" alt="American Express">
            <img src="' . base_url() . 'back-assets/dist/img/credit/paypal2.png" alt="Paypal">
    
            <p class="text-muted well well-sm no-shadow" style="margin-top: 10px;">
              Etsy doostang zoodles disqus groupon greplin oooj voxy zoodles, weebly ning heekya handango imeem plugg dopplr
              jibjab, movity jajah plickers sifteo edmodo ifttt zimbra.
            </p>
          </div>
          <!-- /.col -->
          <div class="col-xs-6">
            <!-- <p class="lead">Amount Due 2/22/2014</p> -->
    
            <div class="table-responsive">
              <table class="table">
                <tr>
                  <th style="width:50%">Gross Amount:</th>
                  <td>Rp. ' . number_format($retur_data['retur_gross_amount'], 0, ',', '.') . '</td>
                </tr>';

    $html .= '<tr>
                  <th>Discount:</th>
                  <td>' . $discount . '</td>
                </tr>';

    $html .= '<tr>
                  <th>Net Amount:</th>
                  <td>Rp. ' . number_format($retur_data['retur_net_amount'], 0, ',', '.') . '</td>
                </tr>
              </table>
            </div>
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
      </section>
      <!-- /.content -->
    </div>
    <!-- ./wrapper -->
    </body>
    </html>';
    echo $html;
  }
}

/* End of file Order_retur.php */