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
                <h3 class="card-title">Searching Area</h3>
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
                  $areaOption = '';
                  if (!empty($area)) {
                    foreach ($area as $key => $value) {
                      $areaOption .= "<option value='" . $value['id'] . "'>" . ucwords($value['name']) . "</option>";
                    }
                  }
                  ?>
                  <div id="dropdown-container">
                    <div class="mb-3" data-level="0">
                      <label for="division_id" class="form-label"><?= $area[0]['reference'] ?></label>
                      <select onchange="getNextDropdown(this.id)" class="form-select mb-3" name="divisionId_1" id="divisionId_1" aria-label="Default select example">
                        <option value="">Select <?= $area[0]['reference'] ?></option>
                        <?= $areaOption ?>
                      </select>
                    </div>
                  </div>
                  <input type="hidden" name="count" id="count" value="1">


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

      function getNextDropdown(id) {
        let base_url = '<?= base_url() ?>';
        let parentId = $(`#${id}`).val();

        let elementId = id.split('_');
        elementId = elementId[1];
        console.log({
          elementId: elementId
        });
        let totalRow = $('#count').val();
        let countRowVal = $('#count').val();


        if (parentId == '') {
          console.log('inside null check:', elementId);
          if (countRowVal > elementId) {
            for (let index = elementId; index <= countRowVal; index++) {
              if (index == elementId) {
                continue;
              }
              $(`#dropdown_${index}`).remove();

            }
          }
          document.getElementById('count').value = elementId;
          return;
        }

        $.post(base_url + "/super-admin/getUpozila", {
          id: parentId,
          totalRow: ++totalRow,
        }, function(data) {
          console.log({
            119: elementId,

          });
          if (countRowVal > elementId) {
            for (let index = elementId; index <= countRowVal; index++) {
              if (index == elementId) {
                continue;
              }
              $(`#dropdown_${index}`).remove();

            }
            document.getElementById('count').value = elementId;
          }
          if (data.upozilaHtml) {
            document.getElementById('count').value = totalRow;

            let newDropdown = `<div class="mb-3" id="dropdown_${totalRow}" >
                        ${data.upozilaHtml}
                    </div>`;
            $('#dropdown-container').append(newDropdown);
          }


        }, 'json');

      }
    </script>
    </script>
    <?php include "super-admin-footer.php"; ?>