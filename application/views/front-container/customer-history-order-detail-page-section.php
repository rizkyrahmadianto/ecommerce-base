<!-- Main content from side menu customer section -->
<div class="col-lg-9 order-1 order-lg-2">
  <div class="product-show-option">
    <div class="row">
      <?php if ($this->session->flashdata('success')) : ?>
        <div class="alert alert-success alert-dismissible fade show col-lg-12" role="alert">
          <strong>Alert <i class="fa fa-check" aria-hidden="true"></i></strong>
          <br>
          <?php echo $this->session->flashdata('success'); ?>
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
      <?php elseif ($this->session->flashdata('error')) : ?>
        <div class="alert alert-danger alert-dismissible fade show col-lg-12" role="alert">
          <strong>Alert <i class="fa fa-exclamation" aria-hidden="true"></i></strong>
          <br>
          <?php echo $this->session->flashdata('error'); ?>
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
      <?php endif; ?>
    </div>

    <div class="row">
      <div class="container">
        <div class="card mb-3 shadow">
          <div class="card-body">
            <h5 class="card-title"><i class="fa fa-info-circle" aria-hidden="true"></i> Attention</h5>
            <p class="card-text">You can cancel your order while the order status is still pending. Apart from that you have to contact the Admin.</p>
            <a href="<?php echo base_url(); ?>print-customer-purchase/<?php echo $data_order['id_order'] ?>" target="__blank" class="btn btn-sm btn-secondary"><i class="fa fa-print" aria-hidden="true"></i> Print order</a>
            <a href="#" class="btn btn-sm btn-danger"><i class="fa fa-ban" aria-hidden="true"></i> Cancel</a>
            <a href="#" class="btn btn-sm btn-info"><i class="fa fa-phone" aria-hidden="true"></i> Contact admin</a>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="container">
        <div class="card mb-3 shadow">
          <div class="card-header">
            Invoice
            <strong><?php echo $data_order['invoice_order'] ?></strong>
            <span class="float-right"> <strong>Status:</strong> <?php echo $data_order['status_name'] ?></span>

          </div>
          <div class="card-body">
            <div class="row mb-4">
              <div class="col-sm-6">
                <h6 class="mb-3">From:</h6>
                <div>
                  <strong><?php echo $company['company_name'] ?></strong>
                </div>
                <div><?php echo $company_address['street_name'] ?>, <?php echo $company_address['city_name'] ?>, <?php echo $company_address['province'] ?></div>
                <div>Email: <?php echo $company['business_email'] ?></div>
                <div>Phone: <?php echo $company['phone'] ?></div>
              </div>

              <div class="col-sm-6">
                <h6 class="mb-3">To:</h6>
                <div>
                  <strong><?php echo $customer['customer_name'] ?></strong>
                </div>
                <div><?php echo $customer['street_name'] ?>, <?php echo $customer['city_name'] ?>, <?php echo $customer['province'] ?></div>
                <div>Email: <?php echo $customer['email'] ?></div>
                <div>Phone: <?php echo $customer['customer_phone'] ?></div>
              </div>



            </div>

            <div class="table-responsive-sm">
              <table class="table table-striped">
                <thead>
                  <tr>
                    <th class="center">#</th>
                    <th>Item Name</th>
                    <th>Weight</th>
                    <th class="right">Price</th>
                    <th class="center">Qty</th>
                    <th class="right">Total</th>
                  </tr>
                </thead>
                <tbody>

                  <?php if ($detail_order != null && $detail_order != 0) : ?>
                    <?php $no = 1; ?>
                    <?php foreach ($detail_order as $val) : ?>
                      <tr>
                        <td class="center"><?php echo $no++; ?></td>

                        <td class="left strong"><?php echo $val['product_name'] ?></td>

                        <td class="center"><?php echo $val['weight_order'] ?> Gram</td>
                        <td class="right">Rp. <?php echo number_format($val['price'], 0, ',', '.') ?></td>
                        <td class="center"><?php echo $val['qty_order'] ?></td>
                        <td class="right">Rp. <?php echo number_format($val['amount'], 0, ',', '.') ?></td>
                      </tr>
                    <?php endforeach; ?>
                  <?php endif; ?>

                </tbody>
              </table>
            </div>
            <div class="row">

              <div class="col-lg-5 col-sm-5 ml-auto">
                <table class="table table-clear">
                  <tbody>
                    <tr>
                      <td class="left">
                        <strong>Sub-total</strong>
                      </td>
                      <td class="right">Rp. <?php echo number_format($data_order['gross_amount'], 0, ',', '.') ?></td>
                    </tr>

                    <?php if ($data_order['ship_amount'] != null && $data_order['ship_amount'] != 0) : ?>
                      <tr>
                        <td class="left">
                          <strong>Shipping cost</strong>
                        </td>
                        <td class="right">Rp. <?php echo number_format($data_order['ship_amount'], 0, ',', '.') ?></td>
                      </tr>
                    <?php endif; ?>

                    <tr>
                      <td class="left">
                        <strong>VAT (<?php echo $data_order['vat_charge_rate'] ?>%)</strong>
                      </td>
                      <td class="right">Rp. <?php echo number_format($data_order['vat_charge_val'], 0, ',', '.') ?></td>
                    </tr>

                    <?php if ($data_order['coupon_charge_rate'] != null && $data_order['coupon_charge_rate'] != 0) : ?>
                      <tr>
                        <td class="left">
                          <strong>Coupon (<?php echo $data_order['coupon_charge_rate'] ?>%)</strong>
                        </td>
                        <td class="right">Rp. <?php echo number_format($data_order['coupon_charge'], 0, ',', '.') ?></td>
                      </tr>
                    <?php endif; ?>

                    <tr>
                      <td class="left">
                        <strong>Total</strong>
                      </td>
                      <td class="right">
                        <strong>Rp. <?php echo number_format($data_order['net_amount'], 0, ',', '.') ?></strong>
                      </td>
                    </tr>
                  </tbody>
                </table>

              </div>

            </div>

          </div>
        </div>
      </div>
    </div>
  </div>
</div>
</div>
</div>
</section>
<!-- Main content from side menu customer section end -->