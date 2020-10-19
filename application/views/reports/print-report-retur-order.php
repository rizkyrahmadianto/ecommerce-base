<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title><?php echo $title; ?></title>

  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

  <style>
    .line-title {
      border: 0;
      border-style: inset;
      border-top: 1px solid #000;
    }
  </style>
</head>

<body onload="window.print();">
  <!-- <img src="assets/img/marketing.png" style="position:absolute; width:60px; height:auto;"> -->
  <table style="width: 100%;">
    <tr>
      <td align="center">
        <span style="line-height: 1.6; font-weight: bold;">
          <?php echo strtoupper($company['company_name']) ?>
        </span>
      </td>
    </tr>
    <tr>
      <td align="center">
        <span style="line-height: 1.6;">
          <?php echo $company['address'] ?>
        </span><br>
        <span style="line-height: 1.6;">
          <?php echo $company['business_email'] ?>
        </span>, <span><?php echo $company['phone'] ?></span>
      </td>
    </tr>
  </table>
  <hr class="line-title">

  <p align="center">
    LAPORAN DATA RETUR ORDER
  </p>
  <p align="center">
    <?php echo date('d F Y', strtotime($startdate)); ?> sampai dengan <?php echo date('d F Y', strtotime($enddate)); ?>
  </p>

  <div class="table-responsive">
    <table class="table table-bordered">
      <thead>
        <tr>
          <th>No</th>
          <th>Retur ID</th>
          <th>Retur Date</th>
          <th>Order ID</th>
          <th>Customer Name</th>
          <th>Total Products</th>
          <th>Total Amount</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $no = 1;
        $total_sum = 0;
        foreach ($data as $val) :
          $total_sum += $val['net_amount'];
          ?>
          <tr>
            <td><?php echo $no++; ?></td>
            <td><?php echo $val['retur_id']; ?></td>
            <td><?php echo date('d M Y', strtotime($val['retur_date'])); ?></td>
            <td><?php echo $val['order_id']; ?></td>
            <td><?php echo $val['customer_name'] ?></td>
            <td><?php echo $val['jumlah']; ?></td>
            <td><?php echo "Rp. " . number_format($val['net_amount'], 0, ',', '.'); ?></td>
          </tr>
        <?php endforeach; ?>
        <tr>
          <td colspan="6" align="center"><strong>TOTAL</strong></td>
          <td><strong><?php echo "Rp. " . number_format($total_sum, 0, ',', '.'); ?></strong></td>
        </tr>
      </tbody>
    </table>
  </div>
</body>

</html>