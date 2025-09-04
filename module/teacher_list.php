<?php include "function/function.php"; ?>

<div class="pagetitle">
  <h1>Teacher List</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="index.php?page=dashboard">Home</a></li>
      <li class="breadcrumb-item">Tables</li>
      <li class="breadcrumb-item active">Teacher List</li>
    </ol>
  </nav>
</div>

<section class="section">
  <div class="row">
    <div class="col-lg-12">

      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Teachers</h5>

          <?php
          $rows = [];
          try {
            $rows = fetchAll("SELECT id, teacher_id, fname, mname, lname, gender, email, department FROM teachers ORDER BY created_at DESC");
          } catch (Throwable $e) {
            $rows = [];
          }
          ?>

          

          <table class="table" id="teacherTable">
            <thead>
              <tr>
                <th>Teacher ID</th>
                <th>First Name</th>
                <th>Middle Name</th>
                <th>Last Name</th>
                <th>Gender</th>
                <th>Email</th>
                <th>Department</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($rows as $r): ?>
              <tr>
                <td><?php echo htmlspecialchars($r['teacher_id']); ?></td>
                <td><?php echo htmlspecialchars($r['fname']); ?></td>
                <td><?php echo htmlspecialchars($r['mname']); ?></td>
                <td><?php echo htmlspecialchars($r['lname']); ?></td>
                <td><?php echo htmlspecialchars($r['gender']); ?></td>
                <td><?php echo htmlspecialchars($r['email']); ?></td>
                <td><?php echo htmlspecialchars($r['department']); ?></td>
                <td>
                  <a href="index.php?page=add_teacher&id=<?php echo (int)$r['id']; ?>" class="btn btn-sm btn-outline-primary me-1"><i class="bi bi-pencil"></i></a>
                  <a href="function/save_teacher.php?delete_id=<?php echo (int)$r['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this teacher?');"><i class="bi bi-trash"></i></a>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>

          <script>
            document.addEventListener('DOMContentLoaded', function () {
              var table = new simpleDatatables.DataTable('#teacherTable', {
                searchable: true,
                fixedHeight: true,
                perPage: 10
              });

              var sizeSel = document.getElementById('teacherTablePageSize');
              sizeSel.addEventListener('change', function () {
                table.options.perPage = parseInt(this.value, 10);
                table.update();
              });

              var searchInput = document.getElementById('teacherTableSearch');
              searchInput.addEventListener('input', function () {
                table.search(this.value);
              });
            });
          </script>

        </div>
      </div>

    </div>
  </div>
</section>


