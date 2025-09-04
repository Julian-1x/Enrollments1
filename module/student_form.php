<?php include "function/function.php"; ?>
<?php $editId = isset($_GET['id']) ? (int)$_GET['id'] : 0; $editStudent = $editId > 0 ? getStudentById($editId) : null; ?>

<div class="pagetitle">
  <h1>Enrollment Form</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="index.php?page=dashboard">Home</a></li>
      <li class="breadcrumb-item">Forms</li>
      <li class="breadcrumb-item active">Enrollment Form</li>
    </ol>
  </nav>
</div><!-- End Page Title -->

<section class="section">
  <div class="row">
    <div class="col-lg-6">

      <div class="card">
        <div class="card-body">
          <?php if (isset($_GET['status'])): ?>
            <?php if ($_GET['status'] == 'success'): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    ✅ Student has been <strong>added successfully!</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php elseif ($_GET['status'] == 'error'): ?>
                <?php
                  $errField = $_GET['field'] ?? '';
                  $errCode = $_GET['code'] ?? '';
                  $errMsg = isset($_GET['msg']) ? urldecode($_GET['msg']) : '';
                  $friendly = '';
                  if ($errCode == '1062') {
                      if ($errField === 'student_id') { $friendly = 'Student ID already exists.'; }
                      elseif ($errField === 'email') { $friendly = 'Email already exists.'; }
                      else { $friendly = 'Duplicate entry.'; }
                  } elseif ($errCode == '1452' || $errField === 'class_id') {
                      $friendly = 'Selected class does not exist.';
                  } elseif ($errField === 'gender') {
                      $friendly = 'Invalid gender value.';
                  } else {
                      $friendly = 'Something went wrong while saving the student.';
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

          <h5 class="card-title"><?php echo $editStudent ? 'Edit Student' : 'Student Enrollment Form'; ?></h5>

          <!-- Student Form -->
          <form action="function/save_student.php" method="POST" novalidate>
            <?php if ($editStudent): ?>
              <input type="hidden" name="id" value="<?php echo (int)$editStudent['id']; ?>">
            <?php endif; ?>

            <div class="row mb-3">
              <label for="studentId" class="col-sm-3 col-form-label">Student ID</label>
              <div class="col-sm-9">
                <?php if ($editStudent): ?>
                  <input type="text" name="student_id" value="<?php echo htmlspecialchars($editStudent['student_id']); ?>" class="form-control<?php echo (($errField ?? '') === 'student_id') ? ' is-invalid' : ''; ?>" id="studentId" required>
                <?php else: ?>
                  <?php $previewId = function_exists('generateUniqueStudentId') ? generateUniqueStudentId() : '02-2526-XXXXXX'; ?>
                  <input type="hidden" name="student_id" value="<?php echo htmlspecialchars($previewId); ?>">
                  <input type="text" value="<?php echo htmlspecialchars($previewId); ?>" class="form-control" id="studentId" readonly>
                  <div class="form-text">Auto-generated on save (format: 02-2526-XXXXXX)</div>
                <?php endif; ?>
              </div>
            </div>

            <div class="row mb-3">
              <label for="fname" class="col-sm-3 col-form-label">First Name</label>
              <div class="col-sm-9">
                <input type="text" name="fname" value="<?php echo htmlspecialchars($editStudent['fname'] ?? ''); ?>" class="form-control" id="fname" required>
              </div>
            </div>

            <div class="row mb-3">
              <label for="mname" class="col-sm-3 col-form-label">Middle Name</label>
              <div class="col-sm-9">
                <input type="text" name="mname" value="<?php echo htmlspecialchars($editStudent['mname'] ?? ''); ?>" class="form-control" id="mname">
              </div>
            </div>

            <div class="row mb-3">
              <label for="lname" class="col-sm-3 col-form-label">Last Name</label>
              <div class="col-sm-9">
                <input type="text" name="lname" value="<?php echo htmlspecialchars($editStudent['lname'] ?? ''); ?>" class="form-control" id="lname" required>
              </div>
            </div>

            <div class="row mb-3">
              <label for="gender" class="col-sm-3 col-form-label">Gender</label>
              <div class="col-sm-9">
                <select name="gender" class="form-select<?php echo (($errField ?? '') === 'gender') ? ' is-invalid' : ''; ?>" id="gender" required>
                  <option value="">-- Select Gender --</option>
                  <option value="Male" <?php echo (($editStudent['gender'] ?? '') === 'Male') ? 'selected' : ''; ?>>Male</option>
                  <option value="Female" <?php echo (($editStudent['gender'] ?? '') === 'Female') ? 'selected' : ''; ?>>Female</option>
                </select>
              </div>
            </div>

            <div class="row mb-3">
              <label for="email" class="col-sm-3 col-form-label">Email</label>
              <div class="col-sm-9">
                <input type="email" name="email" value="<?php echo htmlspecialchars($editStudent['email'] ?? ''); ?>" class="form-control<?php echo (($errField ?? '') === 'email') ? ' is-invalid' : ''; ?>" id="email" required>
              </div>
            </div>

            <div class="row mb-3">
              <label for="class_id" class="col-sm-3 col-form-label">Class</label>
              <div class="col-sm-9">
                <select name="class_id" class="form-select<?php echo (($errField ?? '') === 'class_id') ? ' is-invalid' : ''; ?>" id="class_id" required>
                  <option value="">-- Select Class --</option>
                  <?php
                  $classes = fetchAll("SELECT id, name FROM classes");
                  foreach ($classes as $c) {
                      $sel = (($editStudent['class_id'] ?? null) == $c['id']) ? 'selected' : '';
                      echo "<option value='{$c['id']}' {$sel}>{$c['name']}</option>";
                  }
                  ?>
                </select>
              </div>
            </div>

            <!-- Submit Button -->
            <div class="row mb-3">
              <div class="col-sm-12 text-end">
                <button type="submit" class="btn btn-primary">
                  <i class="bi bi-save"></i> Save Student
                </button>
              </div>
            </div>

          </form><!-- End Student Form -->

        </div>
      </div>

    </div>
  </div>
</section>
