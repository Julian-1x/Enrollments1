<?php include "function/function.php"; ?>

<div class="pagetitle">
  <h1>Add Section</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="index.php?page=dashboard">Home</a></li>
      <li class="breadcrumb-item">Forms</li>
      <li class="breadcrumb-item active">Add Section</li>
    </ol>
  </nav>
</div>

<section class="section">
  <div class="row">
    <div class="col-lg-6">

      <div class="card">
        <div class="card-body">
          <?php if (isset($_GET['status'])): ?>
            <?php if ($_GET['status'] == 'success'): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    ✅ Section has been <strong>added successfully!</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php elseif ($_GET['status'] == 'error'): ?>
                <?php
                  $errField = $_GET['field'] ?? '';
                  $errCode = $_GET['code'] ?? '';
                  $errMsg = isset($_GET['msg']) ? urldecode($_GET['msg']) : '';
                  $friendly = '';
                  if ($errCode == '1062' || $errField === 'name') {
                      $friendly = 'Section name already exists.';
                  } elseif ($errField === 'name' && $errCode == '0') {
                      $friendly = 'Class name is required.';
                  } else {
                      $friendly = 'Something went wrong while saving the section.';
                  }
                ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    ❌ <strong>Failed!</strong> <?php echo htmlspecialchars($friendly); ?>
                    <?php if ($errMsg): ?>
                      <div class="small text-muted mt-1">(<?php echo htmlspecialchars($errMsg); ?>)</div>
                    <?php endif; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
          <?php endif; ?>

          <h5 class="card-title">Add a New Section</h5>

          <form action="function/save_class.php" method="POST" novalidate>

            <div class="row mb-3">
              <label for="name" class="col-sm-3 col-form-label">Section Name</label>
              <div class="col-sm-9">
                <input type="text" name="name" class="form-control<?php echo (($_GET['field'] ?? '') === 'name') ? ' is-invalid' : ''; ?>" id="name" required>
              </div>
            </div>

            <div class="row mb-3">
              <label for="description" class="col-sm-3 col-form-label">Description</label>
              <div class="col-sm-9">
                <textarea name="description" class="form-control" id="description" rows="3"></textarea>
              </div>
            </div>

            <div class="row mb-3">
              <label for="teacher_id" class="col-sm-3 col-form-label">Assign Teacher</label>
              <div class="col-sm-9">
                <select name="teacher_id" id="teacher_id" class="form-select">
                  <option value="">-- Optional: Select Teacher --</option>
                  <?php
                  // Fetch teachers list if table exists
                  try {
                      $teachers = fetchAll("SELECT id, CONCAT(fname, ' ', lname) AS name FROM teachers ORDER BY lname, fname");
                      foreach ($teachers as $t) {
                          echo "<option value='{$t['id']}'>{$t['name']}</option>";
                      }
                  } catch (Throwable $e) {
                      // If teachers table not present, silently ignore
                  }
                  ?>
                </select>
              </div>
            </div>

            <div class="row mb-3">
              <div class="col-sm-12 text-end">
                <button type="submit" class="btn btn-primary">
                  <i class="bi bi-save"></i> Save Section
                </button>
              </div>
            </div>

          </form>

        </div>
      </div>

    </div>
  </div>
</section>


