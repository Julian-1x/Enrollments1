<?php
include "function.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    // Use submitted student_id when provided (form shows hidden field); otherwise generate
    $student_id = $id > 0 ? ($_POST['student_id'] ?? '') : ($_POST['student_id'] ?? generateUniqueStudentId());
    $fname      = $_POST['fname'];
    $mname      = $_POST['mname'];
    $lname      = $_POST['lname'];
    $gender     = $_POST['gender'];
    $email      = $_POST['email'];
    $class_id   = $_POST['class_id'];

    if ($id > 0) {
        $result = updateStudent($id, $student_id, $fname, $mname, $lname, $gender, $email, $class_id);
    } else {
        $result = addStudent($student_id, $fname, $mname, $lname, $gender, $email, $class_id);
    }

    if (is_array($result) && isset($result['success']) && $result['success'] === true) {
        $dest = $id > 0 ? 'student_list' : 'student_form';
        header("Location: ../index.php?page={$dest}&status=success");
        exit;
    }

    $errorField = is_array($result) && isset($result['error_field']) && $result['error_field'] ? $result['error_field'] : '';
    $errorCode = is_array($result) && isset($result['error_code']) ? $result['error_code'] : 1;
    $errorMsg  = is_array($result) && isset($result['error_message']) ? urlencode($result['error_message']) : '';

    $qs = "status=error&code={$errorCode}";
    if ($errorField !== '') { $qs .= "&field={$errorField}"; }
    if ($errorMsg !== '') { $qs .= "&msg={$errorMsg}"; }
    $dest = $id > 0 ? 'student_form&id='.$id : 'student_form';
    header("Location: ../index.php?page={$dest}&{$qs}");

    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['delete_id'])) {
    $deleteId = (int)$_GET['delete_id'];
    $result = deleteStudent($deleteId);
    if (is_array($result) && isset($result['success']) && $result['success'] === true) {
        header("Location: ../index.php?page=student_list&status=deleted");
        exit;
    }
    $errorCode  = is_array($result) && isset($result['error_code']) ? $result['error_code'] : 1;
    $errorMsg   = is_array($result) && isset($result['error_message']) ? urlencode($result['error_message']) : '';
    $qs = "status=error&code={$errorCode}";
    if ($errorMsg !== '')   { $qs .= "&msg={$errorMsg}"; }
    header("Location: ../index.php?page=student_list&{$qs}");
    exit;
}
