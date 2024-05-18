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
                <h3 class="card-title">Add Client Data</h3>
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
                  $clientType = '';
                  foreach (Constants::CLIENT_TYPE as $type) {
                    $clientType .= "<option value='$type'>" . ucwords($type) . "</option>";
                  }
                  ?>
                  <div class="mb-3">
                    <label for="client_type" class="form-label">Client Type</label>
                    <select class="form-select mb-3" name="client_type" id="client_type">
                      <option value="">Select Client Type</option>
                      <?= $clientType ?>
                    </select>
                  </div>
                  <?php
                  $countryOption = '';
                  if (!empty($country)) {
                    
                    foreach ($country as $key => $value) {
                      $selected = '';
                      if($value['country_id'] == Constants::BANGLADESH){
                        $selected = 'selected';
                      }
                      $countryOption .= "<option value=" . $value['country_id'] . " $selected>" . $value['short_name'] . "</option>";
                    
                    }
                  }
                  ?>
                   <div class="mb-3">
                    <label for="choices-single-default" class="form-label">Country</label>
                    <select onchange="toggleDistrict()" class="form-control" data-choices name="country_id" id="country_id">
                      <option value="">Select Country</option>
                      <?= $countryOption ?>
                    </select>
                  </div>
                  <div class="mb-3" id="state">
                    <label for="district_id" class="form-label">District</label>
                    <select class="form-select mb-3" name="district_id" id="district_id">
                      <option value="">Select District</option>
                      <?= $states ?>
                    </select>
                  </div>

                  <div class="mb-3">
                    <label for="client_name" class="form-label">Client Name</label>
                    <input type="text" class="form-control" id="client_name" name="client_name" placeholder="Client Name">
                  </div>
                  <div class="mb-3">
                    <label for="client_title" class="form-label">Client Title</label>
                    <input type="text" class="form-control" id="client_title" name="client_title" placeholder="Client Title">
                  </div>
                  <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="Email">
                  </div>
                  <div class="mb-3">
                    <label for="contact_number" class="form-label">Mobile</label>
                    <input type="text" class="form-control" id="contact_number" name="contact_number" placeholder="Contact Number">
                  </div>
                  <div class="mb-3">
                    <label for="address" class="form-label">Address</label>
                    <input type="text" class="form-control" id="address" name="address" placeholder="Address">
                  </div>
                  <div class="mb-3">
                    <label for="description">Description</label>
                    <textarea class="form-control" name="description" id="description" cols="30" rows="5"></textarea>
                  </div>
                  <div class="mb-3">
                    <label for="google_map" class="form-label">Google Map</label>
                    <input type="text" class="form-control" id="google_map" name="google_map" placeholder="Google Map">
                  </div>
                  <div class="mb-3">
                    <label for="facebook" class="form-label">Facebook</label>
                    <input type="text" class="form-control" id="facebook" name="facebook" placeholder="Facebook">
                  </div>
                  <div class="mb-3">
                    <label for="youtube" class="form-label">Youtube</label>
                    <input type="text" class="form-control" id="youtube" name="youtube" placeholder="Youtube">
                  </div>
                  <div class="mb-3">
                    <label for="x" class="form-label">X</label>
                    <input type="text" class="form-control" id="x" name="x" placeholder="X">
                  </div>
                  <div class="mb-3">
                    <label for="instagram" class="form-label">Instagram</label>
                    <input type="text" class="form-control" id="instagram" name="instagram" placeholder="Instagram">
                  </div>
                  <div class="mb-3">
                    <label for="expired_at" class="form-label">Expired At</label>
                    <input type="date" class="form-control" id="expired_at" name="expired_at">
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
      let bangladesh = <?php echo json_encode(Constants::BANGLADESH);?>;
    
      function validation() {

        if ($('#client_type').val() == '') {
          alert('Select client type is required');
         
          return false;
        }
        if ($('#country_id').val() == '') {
          alert('Country is required');
         
          return false;
        }
        if (($('#country_id').val() == bangladesh )&& $('#district_id').val() == '') {
          alert('District is required');
          console.log(bangladesh);
          return false;
        }
        if ($('#client_name').val() == '') {
          alert('Please Enter Client Name');
          return false;
        }
        if ($('#client_title').val() == '') {
          alert('Please Enter Client Title');
          return false;
        }
        if ($('#email').val() == '') {
          alert('Please Enter Email');
          return false;
        }
        if ($('#contact_number').val() == '') {
          alert('Please Enter Contact Number');
          return false;
        }
        if ($('#address').val() == '') {
          alert('Please Enter Address');
          return false;
        }
        if ($('#expired_at').val() == '') {
          alert('Expire Date is required');
          return false;
        }
      }
    function toggleDistrict() {
      if($('#country_id').val() == <?php echo json_encode(Constants::BANGLADESH);?>) {
        $('#state').show();
      }   else {
        $('#state').hide();
      }
    }
    </script>
    <?php include "super-admin-footer.php"; ?>