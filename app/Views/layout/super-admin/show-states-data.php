<?php

use App\Constant\Constants;

include('super-admin-header.php') ?>
<!-- Begin page -->
<div id="layout-wrapper">

  <?= $this->include('partials/super-admin/menu') ?>

  <!-- ============================================================== -->
  <!-- Start right Content here -->
  <!-- ============================================================== -->
  <div class="main-content">

    <div class="page-content">
      <div class="container-fluid">

        <div class="row">
          <div class="col-lg-12">
            <div class="card">
              <div class="card-header">
                <h5 class="card-title mb-0">Show State</h5>
              </div>
              <div class="card-body">
                <div class="col-xs-0 col-sm-6 col-md-12" style="text-align:center; color:<?php echo $status; ?>">
                  <b><?php echo $fmsg; ?></b>
                </div>
                <table id="scroll-horizontal" class="table nowrap align-middle" style="width:100%">
                  <thead>
                    <tr>

                      <th>States</th>
                      <th>Sub States</th>
                      <th>Reference</th>
                      <th>Sort</th>
                      <th class="text-center">Action</th>


                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    if (!empty($data)) :
                      foreach ($data as $key => $value) { ?>
                        <tr>
                          <td> <?= $value['name'] ?? '' ?> </td>
                          <td> <?= $value['parent_name'] ?? '' ?> </td>
                          <td> <?= $value['reference'] ?? '' ?> </td>
                          <td> <?= $value['sort'] ?? '' ?> </td>
                          <td class="">
                            <div class="d-flex gap-2 justify-content-center">
                              <a href="<?= base_url('/super-admin/edit-states-data/' . $value['id']); ?>"><i style="font-size: 20px;" class='bx bxs-edit'></i></a>
                              <?php $actDec = $value['is_active'] == Constants::DEACTIVE ? Constants::ACTIVE  : Constants::DEACTIVE  ?>
                              <?php if ($value['is_active'] == Constants::ACTIVE) : ?>
                                <button onclick="actDecData('state-act-dec',this.value,<?php echo json_encode($actDec) ?>,'state','super-admin/show-states')" type="button" value="<?= $value['id'] ?>" class="form-check form-switch border-0 out-line-0 bg-transparent"><input class="form-check-input" type="checkbox" role="switch" id="SwitchCheck4" checked=""></button>
                              <?php endif ?>
                              <?php if ($value['is_active'] == Constants::DEACTIVE) : ?>
                                <button onclick="actDecData('state-act-dec',this.value,<?php echo json_encode($actDec) ?>,'state','super-admin/show-states')" type="button" value="<?= $value['id'] ?>" class="form-check form-switch border-0 bg-transparent"><input class="form-check-input" type="checkbox" role="switch" id="SwitchCheck4"></button>
                              <?php endif ?>
                            </div>
                          </td>


                        </tr>
                    <?php }
                    endif; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div><!--end col-->
        </div><!--end row-->
      </div>
      <!-- container-fluid -->
    </div>
    <!-- End Page-content -->

    <?= $this->include('partials/footer') ?>
  </div>
  <!-- end main content-->

</div>
<!-- END layout-wrapper -->
<script>
  function actDecData(routing, id, actDec, table, backRoute) {
    if (actDec == 0) {
      if (confirm('Do you want to deactivate?')) {
        $.ajax({
          url: '<?= BASE_URL ?>super-admin/act-dec/' + routing + '/' + id + '/' + actDec + '/' + table + '/' + backRoute,
          success: function(response) {
            window.location.reload();
          }
        })
      }
    } else {
      if (confirm('Do you want to activate?')) {
        $.ajax({
          url: '<?= BASE_URL ?>super-admin/act-dec/' + routing + '/' + id + '/' + actDec + '/' + table + '/' + backRoute,
          success: function(response) {
            window.location.reload();
          }
        })
      }
    }
  }

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

<?php include('super-admin-footer.php') ?>