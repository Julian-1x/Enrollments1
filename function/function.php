<?php
// function/function.php

// Database connection
function dbConnect() {
    $host = "localhost";   // DB host
    $user = "root";        // DB username
    $pass = "";            // DB password
    $dbname = "enroll"; // DB name

    $conn = mysqli_connect($host, $user, $pass, $dbname);

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }
    return $conn;
}

// Fetch all rows
function fetchAll($query) {
    $conn = dbConnect();
    $result = mysqli_query($conn, $query);
    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    mysqli_close($conn);
    return $data;
}

// Insert Student with validation and detailed error reporting
function addStudent($student_id, $fname, $mname, $lname, $gender, $email, $class_id) {
    $conn = dbConnect();

    // Basic normalization
    $student_id = trim($student_id);
    $fname = trim($fname);
    $mname = $mname !== null ? trim($mname) : null;
    $lname = trim($lname);
    $gender = trim($gender);
    $email = trim($email);
    $class_id = (int)$class_id;

    // Validate gender
    if (!in_array($gender, ["Male", "Female"], true)) {
        mysqli_close($conn);
        return [
            'success' => false,
            'error_code' => 0,
            'error_field' => 'gender',
            'error_message' => 'Invalid gender value.'
        ];
    }

    // Ensure class exists
    $checkClassStmt = mysqli_prepare($conn, "SELECT 1 FROM classes WHERE id = ? LIMIT 1");
    mysqli_stmt_bind_param($checkClassStmt, "i", $class_id);
    mysqli_stmt_execute($checkClassStmt);
    mysqli_stmt_store_result($checkClassStmt);
    $classExists = mysqli_stmt_num_rows($checkClassStmt) > 0;
    mysqli_stmt_close($checkClassStmt);
    if (!$classExists) {
        mysqli_close($conn);
        return [
            'success' => false,
            'error_code' => 1452,
            'error_field' => 'class_id',
            'error_message' => 'Selected class does not exist.'
        ];
    }

    // Insert student
    $stmt = mysqli_prepare($conn, 
        "INSERT INTO students (student_id, fname, mname, lname, gender, email, class_id) 
         VALUES (?, ?, ?, ?, ?, ?, ?)"
    );
    mysqli_stmt_bind_param($stmt, "ssssssi", $student_id, $fname, $mname, $lname, $gender, $email, $class_id);

    $execOk = mysqli_stmt_execute($stmt);
    if ($execOk) {
        $insertId = mysqli_insert_id($conn);
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return [
            'success' => true,
            'insert_id' => $insertId
        ];
    }

    // Handle errors
    $errno = mysqli_errno($conn);
    $err = mysqli_error($conn);
    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    $errorField = null;
    if ($errno === 1062) { // duplicate key
        if (strpos($err, "student_id") !== false) {
            $errorField = 'student_id';
        } elseif (strpos($err, "email") !== false) {
            $errorField = 'email';
        }
    }

    return [
        'success' => false,
        'error_code' => $errno,
        'error_field' => $errorField,
        'error_message' => $err
    ];
}

// Generate a unique student ID in the format 02-2526-XXXXXX
function generateUniqueStudentId() {
    $conn = dbConnect();
    $prefix = '02-2526-';

    // Try multiple times to avoid rare collisions
    for ($i = 0; $i < 10; $i++) {
        $randomNumber = str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $candidate = $prefix . $randomNumber;

        $stmt = mysqli_prepare($conn, "SELECT 1 FROM students WHERE student_id = ? LIMIT 1");
        mysqli_stmt_bind_param($stmt, "s", $candidate);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        $exists = mysqli_stmt_num_rows($stmt) > 0;
        mysqli_stmt_close($stmt);

        if (!$exists) {
            mysqli_close($conn);
            return $candidate;
        }
    }

    mysqli_close($conn);
    // Fallback (very unlikely to reach)
    return $prefix . str_pad((string)mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);
}

function getStudentById($id) {
    $conn = dbConnect();
    $id = (int)$id;
    $stmt = mysqli_prepare($conn, "SELECT id, student_id, fname, mname, lname, gender, email, class_id FROM students WHERE id = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = $result ? mysqli_fetch_assoc($result) : null;
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    return $row ?: null;
}

function updateStudent($id, $student_id, $fname, $mname, $lname, $gender, $email, $class_id) {
    $conn = dbConnect();

    $id = (int)$id;
    $student_id = trim($student_id);
    $fname = trim($fname);
    $mname = $mname !== null ? trim($mname) : null;
    $lname = trim($lname);
    $gender = trim($gender);
    $email = trim($email);
    $class_id = (int)$class_id;

    if (!in_array($gender, ["Male", "Female"], true)) {
        mysqli_close($conn);
        return [ 'success' => false, 'error_code' => 0, 'error_field' => 'gender', 'error_message' => 'Invalid gender value.' ];
    }

    // Ensure class exists
    $checkClassStmt = mysqli_prepare($conn, "SELECT 1 FROM classes WHERE id = ? LIMIT 1");
    mysqli_stmt_bind_param($checkClassStmt, "i", $class_id);
    mysqli_stmt_execute($checkClassStmt);
    mysqli_stmt_store_result($checkClassStmt);
    $classExists = mysqli_stmt_num_rows($checkClassStmt) > 0;
    mysqli_stmt_close($checkClassStmt);
    if (!$classExists) {
        mysqli_close($conn);
        return [ 'success' => false, 'error_code' => 1452, 'error_field' => 'class_id', 'error_message' => 'Selected class does not exist.' ];
    }

    $stmt = mysqli_prepare($conn,
        "UPDATE students SET student_id = ?, fname = ?, mname = ?, lname = ?, gender = ?, email = ?, class_id = ? WHERE id = ?"
    );
    if (!$stmt) {
        $errno = mysqli_errno($conn);
        $err = mysqli_error($conn);
        mysqli_close($conn);
        return [ 'success' => false, 'error_code' => $errno ?: 1, 'error_field' => null, 'error_message' => $err ?: 'Failed to prepare statement.' ];
    }
    mysqli_stmt_bind_param($stmt, "ssssssii", $student_id, $fname, $mname, $lname, $gender, $email, $class_id, $id);
    $execOk = mysqli_stmt_execute($stmt);
    if ($execOk) {
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return [ 'success' => true ];
    }

    $errno = mysqli_errno($conn);
    $err = mysqli_error($conn);
    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    $errorField = null;
    if ($errno === 1062) {
        if (strpos($err, "student_id") !== false) { $errorField = 'student_id'; }
        elseif (strpos($err, "email") !== false) { $errorField = 'email'; }
    }
    return [ 'success' => false, 'error_code' => $errno, 'error_field' => $errorField, 'error_message' => $err ];
}

function deleteStudent($id) {
    $conn = dbConnect();
    $id = (int)$id;
    $stmt = mysqli_prepare($conn, "DELETE FROM students WHERE id = ?");
    if (!$stmt) {
        $errno = mysqli_errno($conn);
        $err = mysqli_error($conn);
        mysqli_close($conn);
        return [ 'success' => false, 'error_code' => $errno ?: 1, 'error_message' => $err ?: 'Failed to prepare statement.' ];
    }
    mysqli_stmt_bind_param($stmt, "i", $id);
    $execOk = mysqli_stmt_execute($stmt);
    $affected = mysqli_stmt_affected_rows($stmt);
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    if ($execOk && $affected > 0) { return [ 'success' => true ]; }
    return [ 'success' => false, 'error_code' => 0, 'error_message' => 'Student not found or not deleted.' ];
}
// Insert Class/Section with validation and detailed error reporting
function addClass($name, $description) {
    $conn = dbConnect();

    $name = trim($name);
    $description = $description !== null ? trim($description) : null;

    if ($name === '') {
        mysqli_close($conn);
        return [
            'success' => false,
            'error_code' => 0,
            'error_field' => 'name',
            'error_message' => 'Class name is required.'
        ];
    }

    $stmt = mysqli_prepare($conn,
        "INSERT INTO classes (name, description) VALUES (?, ?)"
    );
    mysqli_stmt_bind_param($stmt, "ss", $name, $description);

    $execOk = mysqli_stmt_execute($stmt);
    if ($execOk) {
        $insertId = mysqli_insert_id($conn);
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return [
            'success' => true,
            'insert_id' => $insertId
        ];
    }

    $errno = mysqli_errno($conn);
    $err = mysqli_error($conn);
    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    $errorField = null;
    if ($errno === 1062) { // duplicate key
        if (strpos($err, "name") !== false) {
            $errorField = 'name';
        }
    }

    return [
        'success' => false,
        'error_code' => $errno,
        'error_field' => $errorField,
        'error_message' => $err
    ];
}

// Insert Teacher with validation and detailed error reporting
function addTeacher($teacher_id, $fname, $mname, $lname, $gender, $email, $department = null) {
    $conn = dbConnect();

    $teacher_id = trim($teacher_id);
    $fname = trim($fname);
    $mname = $mname !== null ? trim($mname) : null;
    $lname = trim($lname);
    $gender = trim($gender);
    $email = trim($email);
    $department = $department !== null ? trim($department) : null;

    if (!in_array($gender, ["Male", "Female"], true)) {
        mysqli_close($conn);
        return [
            'success' => false,
            'error_code' => 0,
            'error_field' => 'gender',
            'error_message' => 'Invalid gender value.'
        ];
    }

    // Attempt insert into teachers table
    $stmt = mysqli_prepare($conn,
        "INSERT INTO teachers (teacher_id, fname, mname, lname, gender, email, department) VALUES (?, ?, ?, ?, ?, ?, ?)"
    );
    if (!$stmt) {
        // Likely table missing or SQL error
        $errno = mysqli_errno($conn);
        $err = mysqli_error($conn);
        mysqli_close($conn);
        return [
            'success' => false,
            'error_code' => $errno ?: 1,
            'error_field' => null,
            'error_message' => $err ?: 'Failed to prepare statement.'
        ];
    }
    mysqli_stmt_bind_param($stmt, "sssssss", $teacher_id, $fname, $mname, $lname, $gender, $email, $department);

    $execOk = mysqli_stmt_execute($stmt);
    if ($execOk) {
        $insertId = mysqli_insert_id($conn);
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return [
            'success' => true,
            'insert_id' => $insertId
        ];
    }

    $errno = mysqli_errno($conn);
    $err = mysqli_error($conn);
    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    $errorField = null;
    if ($errno === 1062) { // duplicate key
        if (strpos($err, "teacher_id") !== false) {
            $errorField = 'teacher_id';
        } elseif (strpos($err, "email") !== false) {
            $errorField = 'email';
        }
    }

    return [
        'success' => false,
        'error_code' => $errno,
        'error_field' => $errorField,
        'error_message' => $err
    ];
}

// Generate a unique teacher ID in the format 02-2526-XXXXXX
function generateUniqueTeacherId() {
    $conn = dbConnect();
    $prefix = '02-2526-';

    for ($i = 0; $i < 10; $i++) {
        $randomNumber = str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $candidate = $prefix . $randomNumber;

        $stmt = mysqli_prepare($conn, "SELECT 1 FROM teachers WHERE teacher_id = ? LIMIT 1");
        if (!$stmt) { break; }
        mysqli_stmt_bind_param($stmt, "s", $candidate);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        $exists = mysqli_stmt_num_rows($stmt) > 0;
        mysqli_stmt_close($stmt);

        if (!$exists) {
            mysqli_close($conn);
            return $candidate;
        }
    }

    mysqli_close($conn);
    return $prefix . str_pad((string)mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);
}

function getTeacherById($id) {
    $conn = dbConnect();
    $id = (int)$id;
    $stmt = mysqli_prepare($conn, "SELECT id, teacher_id, fname, mname, lname, gender, email, department FROM teachers WHERE id = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = $result ? mysqli_fetch_assoc($result) : null;
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    return $row ?: null;
}

function updateTeacher($id, $teacher_id, $fname, $mname, $lname, $gender, $email, $department = null) {
    $conn = dbConnect();

    $id = (int)$id;
    $teacher_id = trim($teacher_id);
    $fname = trim($fname);
    $mname = $mname !== null ? trim($mname) : null;
    $lname = trim($lname);
    $gender = trim($gender);
    $email = trim($email);
    $department = $department !== null ? trim($department) : null;

    if (!in_array($gender, ["Male", "Female"], true)) {
        mysqli_close($conn);
        return [ 'success' => false, 'error_code' => 0, 'error_field' => 'gender', 'error_message' => 'Invalid gender value.' ];
    }

    $stmt = mysqli_prepare($conn,
        "UPDATE teachers SET teacher_id = ?, fname = ?, mname = ?, lname = ?, gender = ?, email = ?, department = ? WHERE id = ?"
    );
    if (!$stmt) {
        $errno = mysqli_errno($conn);
        $err = mysqli_error($conn);
        mysqli_close($conn);
        return [ 'success' => false, 'error_code' => $errno ?: 1, 'error_field' => null, 'error_message' => $err ?: 'Failed to prepare statement.' ];
    }
    mysqli_stmt_bind_param($stmt, "sssssssi", $teacher_id, $fname, $mname, $lname, $gender, $email, $department, $id);

    $execOk = mysqli_stmt_execute($stmt);
    if ($execOk) {
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return [ 'success' => true ];
    }

    $errno = mysqli_errno($conn);
    $err = mysqli_error($conn);
    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    $errorField = null;
    if ($errno === 1062) {
        if (strpos($err, "teacher_id") !== false) { $errorField = 'teacher_id'; }
        elseif (strpos($err, "email") !== false) { $errorField = 'email'; }
    }
    return [ 'success' => false, 'error_code' => $errno, 'error_field' => $errorField, 'error_message' => $err ];
}

function deleteTeacher($id) {
    $conn = dbConnect();
    $id = (int)$id;
    $stmt = mysqli_prepare($conn, "DELETE FROM teachers WHERE id = ?");
    if (!$stmt) {
        $errno = mysqli_errno($conn);
        $err = mysqli_error($conn);
        mysqli_close($conn);
        return [ 'success' => false, 'error_code' => $errno ?: 1, 'error_message' => $err ?: 'Failed to prepare statement.' ];
    }
    mysqli_stmt_bind_param($stmt, "i", $id);
    $execOk = mysqli_stmt_execute($stmt);
    $affected = mysqli_stmt_affected_rows($stmt);
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    if ($execOk && $affected > 0) { return [ 'success' => true ]; }
    return [ 'success' => false, 'error_code' => 0, 'error_message' => 'Teacher not found or not deleted.' ];
}

// Assign a teacher to a class (junction table class_teachers)
function assignTeacherToClass($class_id, $teacher_id) {
    $conn = dbConnect();

    $class_id = (int)$class_id;
    $teacher_id = (int)$teacher_id;

    // Validate class exists
    $checkClassStmt = mysqli_prepare($conn, "SELECT 1 FROM classes WHERE id = ? LIMIT 1");
    mysqli_stmt_bind_param($checkClassStmt, "i", $class_id);
    mysqli_stmt_execute($checkClassStmt);
    mysqli_stmt_store_result($checkClassStmt);
    $classExists = mysqli_stmt_num_rows($checkClassStmt) > 0;
    mysqli_stmt_close($checkClassStmt);
    if (!$classExists) {
        mysqli_close($conn);
        return [ 'success' => false, 'error_code' => 1452, 'error_field' => 'class_id', 'error_message' => 'Class not found.' ];
    }

    // Validate teacher exists
    $checkTeacherStmt = mysqli_prepare($conn, "SELECT 1 FROM teachers WHERE id = ? LIMIT 1");
    mysqli_stmt_bind_param($checkTeacherStmt, "i", $teacher_id);
    mysqli_stmt_execute($checkTeacherStmt);
    mysqli_stmt_store_result($checkTeacherStmt);
    $teacherExists = mysqli_stmt_num_rows($checkTeacherStmt) > 0;
    mysqli_stmt_close($checkTeacherStmt);
    if (!$teacherExists) {
        mysqli_close($conn);
        return [ 'success' => false, 'error_code' => 1452, 'error_field' => 'teacher_id', 'error_message' => 'Teacher not found.' ];
    }

    // Insert or ignore duplicate
    $stmt = mysqli_prepare($conn, "INSERT INTO class_teachers (class_id, teacher_id) VALUES (?, ?)");
    if (!$stmt) {
        $errno = mysqli_errno($conn);
        $err = mysqli_error($conn);
        mysqli_close($conn);
        return [ 'success' => false, 'error_code' => $errno ?: 1, 'error_field' => null, 'error_message' => $err ?: 'Failed to prepare statement.' ];
    }
    mysqli_stmt_bind_param($stmt, "ii", $class_id, $teacher_id);
    $execOk = mysqli_stmt_execute($stmt);
    if ($execOk) {
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return [ 'success' => true ];
    }

    $errno = mysqli_errno($conn);
    $err = mysqli_error($conn);
    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    return [ 'success' => false, 'error_code' => $errno, 'error_field' => null, 'error_message' => $err ];
}
