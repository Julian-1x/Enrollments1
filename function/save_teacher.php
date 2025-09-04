<?php
include "function.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Support both create and update when id is present
    $id         = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    // Use submitted teacher_id when provided (form shows hidden field); otherwise generate
    $teacher_id = $id > 0 ? ($_POST['teacher_id'] ?? '') : ($_POST['teacher_id'] ?? generateUniqueTeacherId());
    $fname      = $_POST['fname'] ?? '';
    $mname      = $_POST['mname'] ?? '';
    $lname      = $_POST['lname'] ?? '';
    $gender     = $_POST['gender'] ?? '';
    $email      = $_POST['email'] ?? '';
    $department = $_POST['department'] ?? '';

    if ($id > 0) {
        $result = updateTeacher($id, $teacher_id, $fname, $mname, $lname, $gender, $email, $department);
    } else {
        $result = addTeacher($teacher_id, $fname, $mname, $lname, $gender, $email, $department);
    }

    if (is_array($result) && isset($result['success']) && $result['success'] === true) {
        $dest = $id > 0 ? 'teacher_list' : 'add_teacher';
        header("Location: ../index.php?page={$dest}&status=success");
        exit;
    }

    $errorField = is_array($result) && isset($result['error_field']) && $result['error_field'] ? $result['error_field'] : '';
    $errorCode  = is_array($result) && isset($result['error_code']) ? $result['error_code'] : 1;
    $errorMsg   = is_array($result) && isset($result['error_message']) ? urlencode($result['error_message']) : '';

    $qs = "status=error&code={$errorCode}";
    if ($errorField !== '') { $qs .= "&field={$errorField}"; }
    if ($errorMsg !== '')   { $qs .= "&msg={$errorMsg}"; }

    $dest = $id > 0 ? 'add_teacher&id='.$id : 'add_teacher';
    header("Location: ../index.php?page={$dest}&{$qs}");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['delete_id'])) {
    $deleteId = (int)$_GET['delete_id'];
    $result = deleteTeacher($deleteId);
    if (is_array($result) && isset($result['success']) && $result['success'] === true) {
        header("Location: ../index.php?page=teacher_list&status=deleted");
        exit;
    }
    $errorCode  = is_array($result) && isset($result['error_code']) ? $result['error_code'] : 1;
    $errorMsg   = is_array($result) && isset($result['error_message']) ? urlencode($result['error_message']) : '';
    $qs = "status=error&code={$errorCode}";
    if ($errorMsg !== '')   { $qs .= "&msg={$errorMsg}"; }
    header("Location: ../index.php?page=teacher_list&{$qs}");
    exit;
}


