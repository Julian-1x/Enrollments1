<?php include "function/function.php"; ?>

<div class="pagetitle">
  <h1>Student List</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="index.php?page=dashboard">Home</a></li>
      <li class="breadcrumb-item">Tables</li>
      <li class="breadcrumb-item active">Student List</li>
    </ol>
  </nav>
</div>

<section class="section">
  <div class="row">
    <div class="col-lg-12">

      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Students</h5>

          <?php
          $rows = fetchAll("SELECT s.id, s.student_id, s.fname, s.mname, s.lname, s.gender, s.email, c.name AS class_name
                             FROM students s
                             LEFT JOIN classes c ON c.id = s.class_id
                             ORDER BY s.created_at DESC");
          ?>

          
          </div>

          <table class="table" id="studentTable">
            <thead>
              <tr>
                <th>Student ID</th>
                <th>First Name</th>
                <th>Middle Name</th>
                <th>Last Name</th>
                <th>Gender</th>
                <th>Email</th>
                <th>Class</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($rows as $r): ?>
              <tr>
                <td><?php echo htmlspecialchars($r['student_id']); ?></td>
                <td><?php echo htmlspecialchars($r['fname']); ?></td>
                <td><?php echo htmlspecialchars($r['mname']); ?></td>
                <td><?php echo htmlspecialchars($r['lname']); ?></td>
                <td><?php echo htmlspecialchars($r['gender']); ?></td>
                <td><?php echo htmlspecialchars($r['email']); ?></td>
                <td><?php echo htmlspecialchars($r['class_name']); ?></td>
                <td>
                  <a href="index.php?page=student_form&id=<?php echo (int)$r['id']; ?>" class="btn btn-sm btn-outline-primary me-1"><i class="bi bi-pencil"></i></a>
                  <a href="function/save_student.php?delete_id=<?php echo (int)$r['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this student?');"><i class="bi bi-trash"></i></a>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>

          <script>
            document.addEventListener('DOMContentLoaded', function () {
              var table = new simpleDatatables.DataTable('#studentTable', {
                searchable: true,
                fixedHeight: true,
                perPage: 10
              });

              var sizeSel = document.getElementById('studentTablePageSize');
              sizeSel.addEventListener('change', function () {
                table.options.perPage = parseInt(this.value, 10);
                table.update();
              });

              var searchInput = document.getElementById('studentTableSearch');
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


