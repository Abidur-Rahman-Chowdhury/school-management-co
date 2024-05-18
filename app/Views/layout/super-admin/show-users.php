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
                <h5 class="card-title mb-0">View Users</h5>
              </div>
              <div class="card-body">
                <div class="col-xs-0 col-sm-6 col-md-12" style="text-align:center; color:<?php echo $status; ?>">
                  <b><?php echo $fmsg; ?></b>
                </div>
                <form action="<?= $formUrl ?>" method="post">
                  <div class="d-flex gap-2" style="margin-bottom: 10px;">

                    <div class="form-group">
                      <div class="">
                        <label>Client Name</label>
                        <input class="form-control" onkeyup="getClientName(this.id, this.value)" autocomplete="off" class="search-field" type="text" id="client_name" name="client_name" value="" />
                        <input type="hidden" name="client_id" id="client_id">
                        <?php
                        $tableTR = '';
                        $tableTR = "<div id='clientNameSuggestions' style='display:none;width:195px; border-radius:5px; height:250px;' class='ClassempIdSuggestions'>
                            <div id='suggestingClientName' class='ClasssuggestingEmpIdList'>
                            </div>
                        </div>";
                        echo $tableTR;
                        ?>
                      </div>
                    </div>
                    <div class="form-group">
                      <div class="">
                        <label>Email</label>
                        <input class="form-control" autocomplete="off" class="search-field" onkeyup="getClientEmail(this.id, this.value);" type="email" id="email" name="email" value="" />
                        <input type="hidden" name="user_id" id="user_id">
                        <?php
                        $tableTR = '';
                        $tableTR = "<div id='emailSuggestions' style='display:none;width:195px; border-radius:5px; height:250px;' class='ClassempIdSuggestions'>
                            <div id='suggestingEmail' class='ClasssuggestingEmpIdList'>
                            </div>
                        </div>";
                        echo $tableTR;
                        ?>
                      </div>
                    </div>
                    <div class="form-group">
                      <div class="">
                        <label>Phone</label>
                        <input class="form-control" onkeyup="getClientPhone(this.id, this.value)" autocomplete="off" type="text" id="phone" name="phone" value="" />
                        <?php
                        $tableTR = '';
                        $tableTR = "<div id='phoneSuggestions' style='display:none;width:195px; border-radius:5px; height:250px;' class='ClassempIdSuggestions'>
                            <div id='suggestingPhone' class='ClasssuggestingEmpIdList'>
                            </div>
                        </div>";
                        echo $tableTR;
                        ?>
                      </div>
                    </div>
                    <?php
                  $userGroup = '';
                  foreach (Constants::USER_GROUP as $type) {
                    $value = explode('_', $type);
                    $join = implode(' ',   $value);
                    $userGroup .= "<option value='$type'>" . ucwords($join) . "</option>";
                  }
                  ?>
                    <div>
                      <label for="user_group" class="form-label">User Group</label>
                      <select class="form-select mb-3" name="user_group" id="user_group">
                        <option value="">Select User Group</option>
                        <?= $userGroup ?>
                      </select>
                    </div>
                    <div class="form-group">
                      <div class="">
                        <label>From</label>
                        <input class="form-control" type="date" id="from" name="from" id="">
                      </div>

                    </div>
                    <div class="">
                      <label>To</label>
                      <input class="form-control" type="date" id="to" name="to" id="">
                    </div>
                    <div>
                      <button class="btn btn-primary ml-2" style="margin-top:28px;" type="submit">Search</button>
                    </div>
                  </div>

                </form>
                <table id="scroll-horizontal" class="table nowrap align-middle" style="width:100%">
                  <thead>
                    <tr>

                      <th>Client Name</th>
                      <th>First Name</th>
                      <th>Last Name</th>
                      <th>Email</th>
                      <th>Phone</th>
                      <th>Present Address</th>
                      <th>Birth Date</th>
                      <th>Start Date </th>
                      <th>End Date</th>
                      <th>Created At</th>
                      <th>User Group</th>
                      <th class="text-center">Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    if (!empty($data)) :
                      foreach ($data as $key => $value) { ?>
                        <tr>
                          <td>
                            <?= $value['client_name'] ?>
                          </td>
                          <td>
                            <?= $value['name'] ?>
                          </td>
                          <td>
                            <?= $value['last_name'] ?>
                          </td>
                          <td>
                            <?= $value['email'] ?>
                          </td>

                          <td>
                            <?= $value['phone'] ?>
                          </td>
                          <td>
                            <?= $value['present_address'] ?? '' ?>
                          </td>
                          <td>
                            <?= $value['b_date'] == '0000-00-00' ? '':  date('Y-m-d', strtotime($value['b_date'])) ?>
                          </td>
                          <td>
                            <?= !empty($value['start_date']) ?  date('Y-m-d', strtotime($value['start_date'])) :  '' ?>
                          </td>

                          <td>
                            <?= $value['end_date'] == '0000-00-00' ? '' : date('Y-m-d', strtotime($value['end_date']))   ?>
                          </td>
                          <td>
                            <?= date('Y-m-d', strtotime($value['created_at'])); ?>
                          </td>
                          <td><?=  $value['user_group']?></td>
                          <td class="">
                            <div class="d-flex gap-2 justify-content-center">
                              <a href="<?= base_url('/super-admin/edit-user/' . $value['id']); ?>"><i style="font-size: 20px;" class='bx bxs-edit'></i></a>
                              <?php $actDec = $value['is_active'] == Constants::DEACTIVE ? Constants::ACTIVE  : Constants::DEACTIVE  ?>
                              <?php if ($value['is_active'] == Constants::ACTIVE) : ?>
                                <button onclick="actDecData('user-act-dec',this.value,<?php echo json_encode($actDec) ?>,'users','super-admin/view-user')" type="button" value="<?= $value['id'] ?>" class="form-check form-switch border-0 out-line-0 bg-transparent"><input class="form-check-input" type="checkbox" role="switch" id="SwitchCheck4" checked=""></button>
                              <?php endif ?>
                              <?php if ($value['is_active'] == Constants::DEACTIVE) : ?>
                                <button onclick="actDecData('user-act-dec',this.value,<?php echo json_encode($actDec) ?>,'users','super-admin/view-user')" type="button" value="<?= $value['id'] ?>" class="form-check form-switch border-0 bg-transparent"><input class="form-check-input" type="checkbox" role="switch" id="SwitchCheck4"></button>
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

  function getClientEmail(id, val) {
    var base_url = "<?= BASE_URL ?>super-admin";
    if (val == '') {
      document.getElementById('email').value = '';
      document.getElementById('user_id').value = '';
      $('#clientNameSuggestions').fadeOut();
      return false;
    }
    $.post(base_url + "/getUserEmail", {
      val: val,
      id: id
    }, function(data) {
      if (data.length > 0) {
        $('#suggestingEmail').html(data);
        $('#emailSuggestions').fadeIn("slow");
      }
    });
  }

  function fill_email_id_by_tanent(id, email) {
    document.getElementById('email').value = email;
    document.getElementById('user_id').value = id;
    $('#emailSuggestions').fadeOut();
  }

  function getClientPhone(id, val) {
    var base_url = "<?= BASE_URL ?>super-admin";
    if (val == '') {
      document.getElementById('phone').value = '';
      document.getElementById('user_id').value = '';
      $('#clientNameSuggestions').fadeOut();
      return false;
    }
    $.post(base_url + "/getUserPhone", {
      val: val,
      id: id
    }, function(data) {
      if (data.length > 0) {
        $('#suggestingPhone').html(data);
        $('#phoneSuggestions').fadeIn("slow");
      }
    });
  }

  function fill_phone_id_by_tanent(id, phone) {
    document.getElementById('phone').value = phone;
    document.getElementById('user_id').value = id;
    $('#phoneSuggestions').fadeOut();
  }
</script>

<?php include('super-admin-footer.php') ?>