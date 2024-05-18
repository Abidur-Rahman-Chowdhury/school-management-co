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
                <h3 class="card-title">Add User Data</h3>
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
                  <?php
                  $userGroup = '';
                  foreach (Constants::USER_GROUP as $type) {
                    $value = explode('_', $type);
                    $join = implode(' ',   $value);
                    $userGroup .= "<option value='$type'>" . ucwords($join) . "</option>";
                  }
                  ?>
                  <div class="mb-3">
                    <label for="user_group" class="form-label">User Group</label>
                    <select class="form-select mb-3" onchange="toggleClient(this.value)" name="user_group" id="user_group">
                      <option value="">Select User Group</option>
                      <?= $userGroup ?>
                    </select>
                  </div>
                  <div class="mb-3" id="client" style="display:none;">
                    <label for="client_name" class="form-label">Client Name</label>
                    <input class="form-control clientName" onkeyup="getClientName(this.id, this.value)" autocomplete="off" type="text" id="client_name" placeholder="Client Name" name="client_name" value="" />
                    <input type="hidden" name="client_id" id="client_id">
                    <?php
                    $tableTR = '';
                    $tableTR = "<div id='clientNameSuggestions' style='display:none;width:95%; border-radius:5px; height:250px;' class='ClassempIdSuggestions'>
                            <div id='suggestingClientName' class='ClasssuggestingEmpIdList'>
                            </div>
                        </div>";
                    echo $tableTR;
                    ?>
                  </div>
                  <div class="mb-3">
                    <label for="name" class="form-label">First Name</label>
                    <input type="text" class="form-control" id="name" name="name" placeholder="First Name">
                  </div>
                  <div class="mb-3">
                    <label for="last_name" class="form-label">Last Name</label>
                    <input type="text" class="form-control" id="last_name" name="last_name" placeholder="Last Name">
                  </div>
                  <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="Email">
                  </div>
                  <div class="mb-3">
                    <label for="phone" class="form-label">Phone</label>
                    <input type="text" class="form-control" id="phone" name="phone" placeholder="Phone">
                  </div>
                  <div class="mb-3">
                    <label for="present_address" class="form-label">Present Address</label>
                    <input type="text" class="form-control" id="present_address" name="present_address" placeholder="Present Address">
                  </div>
                  <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
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
                  <div class="mb-3">
                    <label for="b_date" class="form-label">Date Of Birth</label>
                    <input type="date" class="form-control" id="b_date" name="b_date" value="">
                  </div>
                  <div class="mb-3">
                    <label for="start_date" class="form-label">Start Date</label>
                    <input type="date" class="form-control" id="start_date" name="start_date" value="">
                  </div>
                  <div class="mb-3">
                    <label for="end_date" class="form-label">End Date</label>
                    <input type="date" class="form-control" id="end_date" name="end_date" value="">
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
      function getClientName(id, val) {
        var base_url = "<?= BASE_URL ?>super-admin";
        if (val == '') {
          document.getElementById('client_name').value = '';
          document.getElementById('client_id').value = '';
          $('#clientNameSuggestions').fadeOut();
          return false;
        }
        $.post(base_url + "/getClientName", {
          val: val,
          id: id
        }, function(data) {
          if (data.length > 0) {
            $('#suggestingClientName').html(data);
            $('#clientNameSuggestions').fadeIn("slow");
          }
        });
      }

      function fill_client_id_by_tanent(id, cName) {
        document.getElementById('client_name').value = cName;
        document.getElementById('client_id').value = id;
        $('#clientNameSuggestions').fadeOut();
      }

      function toggleClient(value) {
        let userGroup = <?php echo json_encode(Constants::USER_GROUP); ?>;
        if (userGroup[0] == value || userGroup[1] == value) {
          $('#client').hide();
          $('#client_id').val('');

        } else {
          $('#client').show();
        }
      }

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
        let userGroup = <?php echo json_encode(Constants::USER_GROUP); ?>;

        if ($('#user_group').val() == '') {
          alert('User group is required');
          return false;
        }
        if ($('#user_group').val() == userGroup[2] || $('#user_group').val() == userGroup[3]) {
          if ($('#client_id').val() == '') {
            alert('Client name is required');
            return false;
          }
        }
        if ($('#name').val() == '') {
          alert('First name is required');
          return false;
        }
        if ($('#last_name').val() == '') {
          alert('Last  name is required');
          return false;
        }
        if ($('#email').val() == '') {
          alert('Email is required');
          return false;
        }
        if ($('#phone').val() == '') {
          alert('Phone is required');
          return false;
        }
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
        if ($('#start_date').val() == '') {
          alert('Start date is required');
          return false;
        }
      }
    </script>
    <?php include "super-admin-footer.php"; ?>