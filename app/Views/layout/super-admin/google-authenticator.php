<?php

use App\Constant\Constants;

include "super-admin-header.php"; ?>

<!-- Begin page -->
<div id="layout-wrapper">

 

  <!-- ============================================================== -->
  <!-- Start right Content here -->
  <!-- ============================================================== -->

  <div class="main-content">
    <div class="page-content">
      <div class="container-fluid">
        <div class="row justify-content-center mb-10 mt-10">
          <!-- left column -->
          <div class="col-md-6">
            <!-- general form elements -->
            <div class="card ">
              <div class="card-header">
                <h3 class="card-title text-center fs-1"> Two Factor Authentication</h3>
              </div>
              <!-- /.card-header -->
              <!-- form start -->
              <form onsubmit="return validation()" method="post" action="<?= $formUrl; ?>">
                <?php if (session()->getFlashdata('form_error')) : ?>
                  <div class="alert alert-danger">
                    <ul>
                      <?php foreach (session()->getFlashdata('form_error') as $error) : ?>
                        <li><?= $error ?></li>
                      <?php endforeach; ?>
                    </ul>
                  </div>
                <?php endif; ?>
                <div class="card-body">
                  <div class="col-xs-0 col-sm-6 col-md-12" style="text-align:center; color:<?php echo $status; ?>">
                    <b><?php echo $fmsg; ?></b>
                  </div>
                  <div class="mb-3">
                   
                    <input type="text" class="form-control" id="otp" name="otp" placeholder="Enter OTP">
                  </div>
                 
                  <div class="text-center">
                    <button type="submit" class="btn btn-primary">Submit</button>
                  </div>
              </form>
              <!-- /.card-body -->

            </div>
            <!-- /.card -->
          </div>
          <!--/.col (left) -->
        </div>

      </div>
      <!-- container-fluid -->
    </div>
    <!-- End Page-content -->

    <script>
      
      
    </script>
    <?php include "super-admin-footer.php"; ?>