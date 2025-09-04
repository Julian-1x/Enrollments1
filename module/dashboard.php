<?php include "function/function.php"; ?>

<div class="pagetitle">
  <h1>Dashboard</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="index.php?page=dashboard">Home</a></li>
      <li class="breadcrumb-item active">Dashboard</li>
    </ol>
  </nav>
</div>

<?php
$studentCount = 0; $teacherCount = 0; $classCount = 0;
try {
  $studentCount = (int) (fetchAll("SELECT COUNT(*) AS c FROM students")[0]['c'] ?? 0);
} catch (Throwable $e) { $studentCount = 0; }
try {
  $teacherCount = (int) (fetchAll("SELECT COUNT(*) AS c FROM teachers")[0]['c'] ?? 0);
} catch (Throwable $e) { $teacherCount = 0; }
try {
  $classCount = (int) (fetchAll("SELECT COUNT(*) AS c FROM classes")[0]['c'] ?? 0);
} catch (Throwable $e) { $classCount = 0; }

$recentStudents = [];
$recentTeachers = [];
try {
  $recentStudents = fetchAll("SELECT student_id, CONCAT(fname,' ',lname) AS name, created_at FROM students ORDER BY created_at DESC LIMIT 5");
} catch (Throwable $e) { $recentStudents = []; }
try {
  $recentTeachers = fetchAll("SELECT teacher_id, CONCAT(fname,' ',lname) AS name, created_at FROM teachers ORDER BY created_at DESC LIMIT 5");
} catch (Throwable $e) { $recentTeachers = []; }
?>

<section class="section dashboard">
  <div class="row">

    <!-- Metric cards -->
    <div class="col-xxl-4 col-md-4">
      <div class="card info-card">
        <div class="card-body">
          <h5 class="card-title">Students</h5>
          <div class="d-flex align-items-center">
            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
              <i class="bi bi-people"></i>
            </div>
            <div class="ps-3">
              <h6><?php echo $studentCount; ?></h6>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-xxl-4 col-md-4">
      <div class="card info-card">
        <div class="card-body">
          <h5 class="card-title">Teachers</h5>
          <div class="d-flex align-items-center">
            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
              <i class="bi bi-person-badge"></i>
            </div>
            <div class="ps-3">
              <h6><?php echo $teacherCount; ?></h6>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-xxl-4 col-md-4">
      <div class="card info-card">
        <div class="card-body">
          <h5 class="card-title">Sections</h5>
          <div class="d-flex align-items-center">
            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
              <i class="bi bi-journal-text"></i>
            </div>
            <div class="ps-3">
              <h6><?php echo $classCount; ?></h6>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Recent lists -->
    <div class="col-lg-6">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Recent Students</h5>
          <ul class="list-group list-group-flush">
            <?php if (!$recentStudents) : ?>
              <li class="list-group-item text-muted">No data</li>
            <?php else: foreach ($recentStudents as $s): ?>
              <li class="list-group-item d-flex justify-content-between align-items-center">
                <span><?php echo htmlspecialchars($s['name']); ?></span>
                <span class="badge bg-secondary"><?php echo htmlspecialchars($s['student_id']); ?></span>
              </li>
            <?php endforeach; endif; ?>
          </ul>
        </div>
      </div>
    </div>

    <div class="col-lg-6">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Recent Teachers</h5>
          <ul class="list-group list-group-flush">
            <?php if (!$recentTeachers) : ?>
              <li class="list-group-item text-muted">No data</li>
            <?php else: foreach ($recentTeachers as $t): ?>
              <li class="list-group-item d-flex justify-content-between align-items-center">
                <span><?php echo htmlspecialchars($t['name']); ?></span>
                <span class="badge bg-secondary"><?php echo htmlspecialchars($t['teacher_id']); ?></span>
              </li>
            <?php endforeach; endif; ?>
          </ul>
        </div>
      </div>
    </div>

  </div>
</section>