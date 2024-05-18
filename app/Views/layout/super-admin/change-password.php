<?php

use App\Constant\Constants;

include "super-admin-header.php"; ?>

<!-- Begin page -->
<div id="layout-wrapper">

  <?= $this->include('partials/super-admin/menu') ?>

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
                <h3 class="card-title">Change Password</h3>
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
                    <label for="password" class="form-label">New Password</label>
                    <div class="position-relative auth-pass-inputgroup mb-3">
                      <input type="password" class="form-control pe-5 password-input pass1" placeholder="Enter password" id="password-input" name="password">
                      <button class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted password-addon" type="button" id="password-addon"><i class="ri-eye-fill align-middle"></i></button>
                    </div>

                  </div>
                  <div class="mb-3">
                    <label for="repass" class="form-label">Re Password</label>
                    <div class="position-relative auth-pass-inputgroup mb-3">
                      <input type="password" class="form-control pe-5 password-input repass" placeholder="Enter password" id="password-input" name="repass">
                      <button class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted password-addon" type="button" id="password-addon"><i class="ri-eye-fill align-middle"></i></button>
                    </div>
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
      function validatePassword() {
        var password = $(".pass1").val();
        var hasSpecialChar = /[!@#$%^&*()_+\-=\[\]{}|;:'",.<>\/?]/.test(password);
        var hasNumber = /\d/.test(password);
        var hasString = /[a-zA-Z]/.test(password);
        var isMinLength = password.length >= 8;
        if (hasSpecialChar && hasNumber && hasString && isMinLength) {
          return true;
        } else {
          return false;
        }
      }

      function validation() {
        let checkCondition = validatePassword();


        if ($('.pass1').val() == '') {
          alert('Password is required');
          return false;
        }
        if ($('.repass').val() == '') {
          alert('Re Password  is required');
          return false;
        }
        if ($('.pass1').val() != $('.repass').val()) {
          alert('Password does not match ');
          return false;
        }
        if (checkCondition === false) {
          alert("Password has to be at least 8 characters, speacial character, number and string!!!");
          return false;
        }

      }
    </script>
    <?php include "super-admin-footer.php"; ?>