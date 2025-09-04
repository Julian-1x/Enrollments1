<?php include "function/function.php"; ?>
<?php $editId = isset($_GET['id']) ? (int)$_GET['id'] : 0; $editTeacher = $editId > 0 ? getTeacherById($editId) : null; ?>

<div class="pagetitle">
  <h1>Add Teacher</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="index.php?page=dashboard">Home</a></li>
      <li class="breadcrumb-item">Forms</li>
      <li class="breadcrumb-item active">Add Teacher</li>
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
                    ✅ Teacher has been <strong>added successfully!</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php elseif ($_GET['status'] == 'error'): ?>
                <?php
                  $errField = $_GET['field'] ?? '';
                  $errCode = $_GET['code'] ?? '';
                  $errMsg = isset($_GET['msg']) ? urldecode($_GET['msg']) : '';
                  $friendly = '';
                  if ($errCode == '1062') {
                      if ($errField === 'teacher_id') { $friendly = 'Teacher ID already exists.'; }
                      elseif ($errField === 'email') { $friendly = 'Email already exists.'; }
                      else { $friendly = 'Duplicate entry.'; }
                  } elseif ($errField === 'gender') {
                      $friendly = 'Invalid gender value.';
                  } else {
                      $friendly = 'Something went wrong while saving the teacher.';
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

          <h5 class="card-title"><?php echo $editTeacher ? 'Edit Teacher' : 'Add a New Teacher'; ?></h5>

          <form action="function/save_teacher.php" method="POST" novalidate>
            <?php if ($editTeacher): ?>
            <input type="hidden" name="id" value="<?php echo (int)$editTeacher['id']; ?>">
            <?php endif; ?>

            <div class="row mb-3">
              <label for="teacher_id" class="col-sm-4 col-form-label">Teacher ID</label>
              <div class="col-sm-8">
                <?php if ($editTeacher): ?>
                  <input type="text" name="teacher_id" value="<?php echo htmlspecialchars($editTeacher['teacher_id']); ?>" class="form-control<?php echo (($_GET['field'] ?? '') === 'teacher_id') ? ' is-invalid' : ''; ?>" id="teacher_id" required>
                <?php else: ?>
                  <?php $previewId = function_exists('generateUniqueTeacherId') ? generateUniqueTeacherId() : '02-2526-XXXXXX'; ?>
                  <input type="hidden" name="teacher_id" value="<?php echo htmlspecialchars($previewId); ?>">
                  <input type="text" value="<?php echo htmlspecialchars($previewId); ?>" class="form-control" id="teacher_id" readonly>
                  <div class="form-text">Auto-generated on save (format: 02-2526-XXXXXX)</div>
                <?php endif; ?>
              </div>
            </div>

            <div class="row mb-3">
              <label for="fname" class="col-sm-4 col-form-label">First Name</label>
              <div class="col-sm-8">
                <input type="text" name="fname" value="<?php echo htmlspecialchars($editTeacher['fname'] ?? ''); ?>" class="form-control" id="fname" required>
              </div>
            </div>

            <div class="row mb-3">
              <label for="mname" class="col-sm-4 col-form-label">Middle Name</label>
              <div class="col-sm-8">
                <input type="text" name="mname" value="<?php echo htmlspecialchars($editTeacher['mname'] ?? ''); ?>" class="form-control" id="mname">
              </div>
            </div>

            <div class="row mb-3">
              <label for="lname" class="col-sm-4 col-form-label">Last Name</label>
              <div class="col-sm-8">
                <input type="text" name="lname" value="<?php echo htmlspecialchars($editTeacher['lname'] ?? ''); ?>" class="form-control" id="lname" required>
              </div>
            </div>

            <div class="row mb-3">
              <label for="gender" class="col-sm-4 col-form-label">Gender</label>
              <div class="col-sm-8">
                <select name="gender" class="form-select<?php echo (($_GET['field'] ?? '') === 'gender') ? ' is-invalid' : ''; ?>" id="gender" required>
                  <option value="">-- Select Gender --</option>
                  <option value="Male" <?php echo (($editTeacher['gender'] ?? '') === 'Male') ? 'selected' : ''; ?>>Male</option>
                  <option value="Female" <?php echo (($editTeacher['gender'] ?? '') === 'Female') ? 'selected' : ''; ?>>Female</option>
                </select>
              </div>
            </div>

            <div class="row mb-3">
              <label for="email" class="col-sm-4 col-form-label">Email</label>
              <div class="col-sm-8">
                <input type="email" name="email" value="<?php echo htmlspecialchars($editTeacher['email'] ?? ''); ?>" class="form-control<?php echo (($_GET['field'] ?? '') === 'email') ? ' is-invalid' : ''; ?>" id="email" required>
              </div>
            </div>

            <div class="row mb-3">
              <label for="department" class="col-sm-4 col-form-label">Department</label>
              <div class="col-sm-8">
                <input type="text" name="department" value="<?php echo htmlspecialchars($editTeacher['department'] ?? ''); ?>" class="form-control" id="department">
              </div>
            </div>

            <div class="row mb-3">
              <div class="col-sm-12 text-end">
                <button type="submit" class="btn btn-primary">
                  <i class="bi bi-save"></i> <?php echo $editTeacher ? 'Update Teacher' : 'Save Teacher'; ?>
                </button>
              </div>
            </div>

          </form>

        </div>
      </div>

    </div>
  </div>
</section>


