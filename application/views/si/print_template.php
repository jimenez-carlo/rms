<!DOCTYPE html>
<head>
  <link rel="stylesheet" type="text/css" href="<?php echo base_url('common/styles/design.css'); ?>" />
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
  <style>
    .width-100 {
      width:100%;
    }

    * {
      font-family: 'Dejavu Sans';
      font-weight: 550;
    }

    li {
      list-style-type: none;
    }

    .inline-block {
      display: inline-block;
    }
  </style>
</head>
<?php foreach($si_prints as $print): ?>
<div style="page-break-after: always;">
<header>
  <div class="inline-block" style="width:240px;">
    <p style="margin:0 0 0 0; font-size:12px;">
    <?php
      switch (true) {
        case (in_array($print['si_bcode'], range(1000, 1999))):
          echo "Motortrade Nationwide Corporation";
          break;
        case (in_array($print['si_bcode'], range(3000, 3999))):
          echo "Honda Prestige Traders, Inc.";
          break;
        case (in_array($print['si_bcode'], range(6000, 6999))):
          echo "Motortrade Topline Inc.";
          break;
        case (in_array($print['si_bcode'], range(8000, 8999))):
          echo "Motorjoy Depot, Inc.";
          break;
        default:
          echo "Other Company";

      }
    ?>
    </p>
    <p style="margin:1px 0 8px 0; line-height:.9; font-size:12px;"><?php echo $print['si_baddress']; ?></p>
  </div>
  <div class="inline-block"; style="width: 33%; margin-top:-8px; height: 100px;">
    <p class="text-center mb-0 mt-0" style="font: bold 18px 'Dejavu Sans'"><?php echo $print['si_bname']; ?></p>
    <p class="text-center mb-0 mt-0" style="font-size: 14px;"><?php echo 'VAT REG. TIN '.$print['si_vatregtin']; ?></p>
  </div>
  <div class="inline-block" style="width:33%; padding:-10px 0 0 110px;">
    <p style="margin:0 0 -10px 0; font:bold 16px 'Dejavu Sans';">
      SALES INVOICE
    </p>
    <p style="margin:-20px 0 0 0; font:bold 16px 'Dejavu Sans';">
      <?php echo $print['si_sino']; ?>
    </p>
    </p>
    <p style="margin:0; font-size: 14px;"><?php echo $print['si_dsold']; ?></p>
  </div>
</header>
<div style="font-size:12px; height: 60px; padding-top:-80px; display:block; width:100%;">
  <p style="width:45%; position:relative; margin-left: 20px; float:left;">
    <small style="font-size: 12px;">SOLD TO:</small><br>
    <?php echo $print['si_custname']; ?>
    <?php if($print['si_cust_tin'] !== " "): ?>
      <br><?php echo 'TIN: '.$print['si_cust_tin']; ?>
    <?php endif; ?>
  </p>
  <p style="width:45%; position:relative; float:right;"> <?php echo $print['si_cust_add']; ?> </p>
</div>
<ul style="font-size: 13px; margin-top:50px">
  <li style="width: 10%; display: inline-block; vertical-align: top;">
    <ul style="position:relative; left: -25px; padding:0;">
      <li>1 UN</li>
    </ul>
  </li>
  <li style="width: 50%; display: inline-block; vertical-align: top;">
    <ul>
      <li><?php echo $print['si_brand']; ?></li>
      <li>Model Code: <?php echo $print['si_modelcode']; ?></li>
      <li>Engine No: <?php echo $print['si_engin_no']; ?></li>
      <li>Chassis: <?php echo $print['si_chassisno']; ?></li>
      <li>Color: <?php echo $print['si_color']; ?></li>
      <li style="font-size: 11px">Complete w/ Standard Tools, Battery & Accessories</li>
      <li style="margin-top: 10px;"><?php echo $print['si_app_id']; ?></li>
      <li>FI Doc No. <?php echo $print['si_fidocno']; ?></li>
      <li>SO No. <?php echo $print['si_sono']; ?></li>
      <li>Cust.Code <?php echo $print['si_custcode']; ?></li>
    </ul>
  </li>
  <li style="width: 40%; display: inline-block; vertical-align: top;">
    <ul style="">
      <li style="border: 1px solid white; text-align: right;"><?php echo $print['si_price']; ?></li>
      <?php if($print['si_discount'] !== '0.00'): ?>
      <li style="border: 1px solid white;">Less Discount <div style="float:right;"><?php echo $print['si_discount']; ?></div></li>
      <?php endif; ?>
      <li style="border-top: 2px solid black;">VATable Sale <div style="float:right;"><?php echo $print['si_vatsale']; ?></div></li>
      <li style="border: 1px solid white;">VAT-Exempt Sale <div style="float:right;"><?php echo $print['si_vatexp']; ?></div></li>
      <li style="border: 1px solid white;">VAT Zero-Rated Sale <div style="float:right"><?php echo $print['si_vatzero']; ?></div></li>
      <li style="border-bottom: 2px solid black;">VAT <div style="float:right"><?php echo $print['si_val_val']; ?></div></li>
      <li style="border-bottom: 2px solid black;">Total Amount <div style="float:right"><?php echo $print['si_totalamt']; ?></div></li>
    </ul>
  </li>
</ul>
<p style="<?php echo (!isset($print['si_birpermitno']) || $print['si_birpermitno'] === " ") ?  'position:fixed;' : ''; ?>height:15px; margin:40px 0 0 100px; font-size:9px;">
  <?php echo $print['si_birpermitno']; ?>
</p>
<p style="position:relative; height:50px; font-size:11px; text-align:center;">
  <span style="position:absolute; height:11px; bottom:0; left:190px;"><?php echo $this->input->post('prepared_by'); ?></span>
  <span style="position:absolute; height:11px; bottom:0; left:80px; right:0;"><?php echo $this->input->post('approved_by'); ?></span>
  <span style="position:absolute; height:11px; bottom:0; left:485px;"><?php echo $print['si_custname']; ?></span>
</p>
<p style="font-size:10px; position:absolute; margin:0; right:0; bottom:-35px;"><?php echo $print['si_billing_no']." - ".$print['si_drno']; ?></p>
</div>
<?php endforeach; ?>

