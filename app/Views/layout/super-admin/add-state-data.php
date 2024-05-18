<?php include "super-admin-header.php"; ?>

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
                <h3 class="card-title">Add States Data</h3>
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
                    <label for="parent_id" class="form-label">State</label>
                    <select class="form-select mb-3" name="parent_id" aria-label="Default select example">
                      <option value="">Select States</option>
                      <?= $states ?>
                    </select>
                  </div>
                  <div class="mb-3">
                    <label for="name" class="form-label">State Name</label>
                    <input type="text" class="form-control" id="name" name="name" placeholder="State Name">
                  </div>
                  <div class="mb-3">
                    <label for="reference" class="form-label">Reference</label>
                    <input type="text" class="form-control" id="reference" name="reference" placeholder="Reference">
                  </div>

                  <div class="mb-3">
                    <label for="sort" class="form-label">Sort</label>
                    <input type="number" class="form-control" id="sort" name="sort" placeholder="Sort value">
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
      function validation() {
        if ($('#name').val() == '') {
          alert('Name is required');
          return false;
        }
        if ($('#sort').val() == '') {
          alert('Sort is required');
          return false;
        }
        return true;
      }
    </script>
    <?php include "super-admin-footer.php"; ?>