<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
  <link rel="icon" type="image/png" href="<?= base_url(); ?>/assets/img/icon-title.png" />
  <title> <?= $title; ?> </title>

  <!-- General CSS Files -->
  <!-- <link rel="stylesheet" href="https://10.27.110.100/template/node_modules/bootstrap/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://10.27.110.100/template/node_modules/@fortawesome/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Poppins" /> -->

  <!-- CSS Libraries -->
  <link rel="stylesheet" href="https://10.27.110.100/template/node_modules/datatables/datatables.min.css">
  <!-- Template CSS -->
  <link rel="stylesheet" href="https://10.27.110.100/template/assets/css/style.css">
  <link rel="stylesheet" href="https://10.27.110.100/template/assets/css/components.css">

  <style>
    body {
      font-size: xx-small;
    }

    .invoice .invoice-title .invoice-number {
      float: right;
      /* font-size: 20px;
      font-weight: 700;
      margin-top: -45px; */
    }

    /* .invoice hr {
      margin-top: 50px;
      margin-bottom: 40px;
      border-top-color: #f9f9f9;
    } */

    /* container */
    .three-columns-grid {
      display: grid;
      grid-auto-rows: 1fr;
      grid-template-columns: 1fr 1fr 1fr;
    }

    /* columns */
    .three-columns-grid>* {
      padding: 1rem;
    }

    address {
      font-style: normal;
    }

    .text-center {
      text-align: center !important;
    }

    address {
      margin-bottom: 1rem;
      font-style: normal;
      line-height: inherit;
    }

    table {
      border-collapse: collapse;
    }

    .table {
      width: 100%;
      margin-bottom: 1rem;
      color: #212529;
    }

    .table th,
    .table td {
      padding: 0.20rem;
      vertical-align: top;
      border-top: 1px solid #dee2e6;
    }

    .table-sm th,
    .table-sm td {
      padding: 0.2rem;
    }

    .table-borderless th,
    .table-borderless td,
    .table-borderless thead th,
    .table-borderless tbody+tbody {
      border: 0;
      padding-bottom: 0;
    }

    th {
      text-align: inherit;
      text-align: -webkit-match-parent;
    }

    .table-striped tbody tr:nth-of-type(odd) {
      background-color: rgba(0, 0, 0, 0.05);
    }

    @media (max-width: 575.98px) {
      .table-responsive-sm {
        display: block;
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
      }

      .table-responsive-sm>.table-bordered {
        border: 0;
      }
    }

    @media (max-width: 767.98px) {
      .table-responsive-md {
        display: block;
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
      }

      .table-responsive-md>.table-bordered {
        border: 0;
      }
    }

    @media (max-width: 991.98px) {
      .table-responsive-lg {
        display: block;
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
      }

      .table-responsive-lg>.table-bordered {
        border: 0;
      }
    }

    @media (max-width: 1199.98px) {
      .table-responsive-xl {
        display: block;
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
      }

      .table-responsive-xl>.table-bordered {
        border: 0;
      }
    }

    .table-responsive {
      display: block;
      width: 100%;
      overflow-x: auto;
      -webkit-overflow-scrolling: touch;
    }

    .table-responsive>.table-bordered {
      border: 0;
    }

    hr {
      box-sizing: content-box;
      height: 0;
      overflow: visible;
    }

    hr {
      margin-top: 1rem;
      margin-bottom: 0, 5rem;
      border: 0;
      border-top: 1px solid rgba(0, 0, 0, 0.1);
    }

    h1,
    h2,
    h3,
    h4,
    h5,
    h6 {
      margin-top: 0;
      margin-bottom: 0.5rem;
    }
  </style>

</head>

<body>
  <div id="app">
    <!-- Main Content -->
    <div class="main-content">
      <section class="section">
        <div class="section-body">
          <div class="invoice">
            <div class="invoice-print">
              <div class="row">
                <div class="col-12">
                  <div class="invoice-title">
                    <h2>
                      <!-- <img width="150" src='https://10.27.110.100/img/surat/Logo_TA_New.png' /> -->
                      <!-- <div class="invoice-number"><img src='data:image/jpeg;base64,<?= $logo; ?>' alt="logoTA" width="187" height="132"></div> -->
                      <div style="clear: both">
                        <p style="margin-top: 0pt; margin-bottom: 0pt; line-height: normal">
                          <span style="height: 0pt; display: block; position: absolute; z-index: -65537"><img src="data:image/png;base64,<?= $logo; ?>" width="187" height="132" alt="" style="
            margin-top: -35.2pt;
            margin-left: 388.45pt;
            position: absolute;
          " /></span>&#xa0;
                        </p>
                      </div>
                    </h2><br>
                  </div>
                  <hr><br>
                  <h4 class="text-center" style="margin-top: 0px;">SURAT TUGAS</h4><br>
                  <div class="row">
                    <div class="col-md-12">
                      <address>
                        <strong>Yang bertanda tangan di bawah ini, Saya :</strong><br>
                        <table class="table table-sm table-borderless ml-5">
                          <tr>
                            <th>Nama</th>
                            <th>:</th>
                            <td>Eko Yudiansyah</td>
                          </tr>
                          <tr>
                            <th>NIP/NIK/NRP*</th>
                            <th>:</th>
                            <td>896274</td>
                          </tr>
                          <tr>
                            <th>Jabatan</th>
                            <th>:</th>
                            <td>Korlap Provisioning & Migration B2B</td>
                          </tr>
                          <tr>
                            <th>Instansi</th>
                            <th>:</th>
                            <td>PT. TELKOM AKSES</td>
                          </tr>
                          <tr>
                            <th>Alamat</th>
                            <th>:</th>
                            <td>JL.SUPRAPTO NO.132 KOTA BENGKULU</td>
                          </tr>
                          <tr>
                            <th>Telp</th>
                            <th>:</th>
                            <td>085208452117</td>
                          </tr>
                        </table>
                      </address>
                    </div>
                  </div>
                </div>
              </div>

              <div class="row mt-4">
                <div class="col-md-12">
                  <div class="section-title" style="font-weight: bold;">Menugaskan kepada :</div><br>
                  <!-- <p class="section-lead">All items here cannot be deleted.</p> -->
                  <div class="table-responsive">
                    <table class="table table-striped table-hover table-md">
                      <tr>
                        <th data-width="40">#</th>
                        <th>NAMA</th>
                        <th class="text-center">NIK</th>
                        <th class="text-center">UNIT KERJA</th>
                        <th class="text-right">KONTAK PIC</th>
                      </tr>
                      <?php $i = 1 ?>
                      <?php foreach ($petugas as $d) : ?>
                        <tr>
                            <td><?= $i++ ?></td>
                          <td><?= $d[0]; ?></td>
                          <td class="text-center"><?= $d[1]; ?></td>
                          <td class="text-center"><?= $d[2]; ?></td>
                          <td><?= $d[3]; ?></td>
                        </tr>
                        <?php endforeach ?>
                    </table>
                  </div>
                  <p class="mt-3">Terhitung mulai tanggal <?= $tanggal; ?> sampai selesai nama di atas ditugaskan untuk melakukan pekerjaan <strong><?= $jenis; ?> :</strong></p><br>
                  <div class="table-responsive">
                    <table class="table table-striped table-hover table-md">
                      <tr>
                        <th data-width="40">#</th>
                        <th style="width:200px">NAMA PELANGGAN</th>
                        <th class="text-center">ALAMAT</th>
                        <th class="text-center">NO ORDER</th>
                      </tr>
                      <?php $i = 1 ?>
                      <?php foreach ($cust as $d) : ?>
                        <tr>
                            <td><?= $i++ ?></td>
                          <td><?= $d[0]; ?></td>
                          <td class="text-center"><?= $d[1]; ?></td>
                          <td class="text-center"><?= $d[2]; ?></td>
                        </tr>
                        <?php endforeach ?>
                    </table>
                  </div>
                  <p class="mt-3">Surat Tugas ini Berlaku selama bertugas di lokasi, dan sewaktu-waktu dapat ditinjau ulang sesuai kebutuhan dan peruntukannya.</p>
                  <p class="mt-3">Demikian surat ini dibuat agar dapat di pergunakan dengan penuh tanggung jawab.</p>
                  <div class="three-columns-grid">
                    <div>
                      <address class="text-center" style="padding-left: 30rem;">
                        <strong>Bengkulu, <?= $tanggal; ?></strong><br>
                        Mengetahui,<br>
                        Korlap Provisioning & Migration B2B<br>
                        <img src='data:image/jpeg;base64,<?= $ttd; ?>' alt="ttd" width="189" height="93"><br>
                        Eko Yudiansyah<br>
                        NIK 896274
                      </address>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <hr>
            <div class="col-12 text-md-left">
              <address>
                <strong style="color: red;">PT Telkom Akses</strong><br>
                <table>
                  <tr>
                    <td>Jl. Mayjend Suprapto No. 132</td>
                    <td> </td>
                    <td> </td>
                    <td> </td>
                    <td> </td>
                    <td> </td>
                    <td><strong>t.</strong> (0736) 28555</td>
                  </tr>
                  <tr>
                    <td>Kota Bengkulu</td>
                    <td> </td>
                    <td> </td>
                    <td> </td>
                    <td> </td>
                    <td> </td>
                    <td><strong>e.</strong> info@telkomakses.co.id</td>
                  </tr>
                </table>
              </address>
            </div>
          </div>
      </section>
    </div>
  </div>

</body>

</html>