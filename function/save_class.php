<?php
include "function.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name        = $_POST['name'] ?? '';
    $description = $_POST['description'] ?? '';
    $teacherId   = $_POST['teacher_id'] ?? '';

    $result = addClass($name, $description);

    if (is_array($result) && isset($result['success']) && $result['success'] === true) {
        // If teacher provided, assign to the newly created class
        if (!empty($teacherId)) {
            $assign = assignTeacherToClass($result['insert_id'], (int)$teacherId);
            if (!(is_array($assign) && isset($assign['success']) && $assign['success'] === true)) {
                $af = isset($assign['error_field']) ? $assign['error_field'] : '';
                $ac = isset($assign['error_code']) ? $assign['error_code'] : 1;
                $am = isset($assign['error_message']) ? urlencode($assign['error_message']) : '';
                $qs = "status=error&code={$ac}";
                if ($af !== '') { $qs .= "&field={$af}"; }
                if ($am !== '') { $qs .= "&msg={$am}"; }
                header("Location: ../index.php?page=add_section&{$qs}");
                exit;
            }
        }
        header("Location: ../index.php?page=add_section&status=success");
        exit;
    }

    $errorField = is_array($result) && isset($result['error_field']) && $result['error_field'] ? $result['error_field'] : '';
    $errorCode  = is_array($result) && isset($result['error_code']) ? $result['error_code'] : 1;
    $errorMsg   = is_array($result) && isset($result['error_message']) ? urlencode($result['error_message']) : '';

    $qs = "status=error&code={$errorCode}";
    if ($errorField !== '') { $qs .= "&field={$errorField}"; }
    if ($errorMsg !== '')   { $qs .= "&msg={$errorMsg}"; }

    header("Location: ../index.php?page=add_section&{$qs}");
    exit;
}


