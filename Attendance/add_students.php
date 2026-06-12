<?php
$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'attendance_system';

$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

if ($conn->connect_error) {
    die(json_encode([
        'success' => false,
        'error' => 'Database connection failed: ' . $conn->connect_error
    ]));
}


$nextID = 1;
$result = $conn->query("SELECT MAX(student_id) AS student_id FROM students");
if ($result && $row = $result->fetch_assoc()) {
    $nextID = $row["student_id"] + 1;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="./assets\dist\css\bootstrap.min.css">
    <link rel="stylesheet" href="./assets\dist\js\bootstrap.bundle.min.js">
</head>
<style>
    * {
        padding: 0;
        margin: 0;
    }

    .container1 {
        display: flex;
        gap: 150px;
        margin-left: 70px;

    }

    table {
        border-collapse: collapse;
        text-align: center;
    }

    td,
    th {
        padding: 5px;
        border: 1px solid black;
    }

    td {
        height: 80px;
        width: 120px;
    }

    .box {
        width: 100%;
    }

    img {
        height: 100%;
        width: 100%;
    }

    
</style>

<body>
    <div class="container1 mt-5">
        <form action="upload.php" method="post" enctype="multipart/form-data">
            <h3><strong>Add Product</strong></h3>

            <div class="container-fluid">
                <label for="">Student Id: </label>
                <input type="number" name="student_id" placeholder="" class="box" value="0<?php echo $nextID ?>" readonly>
            </div>

            <div class="container-fluid">
                <label for="">Student Name: </label>
                <input class="box" type="text" name="s_name" autocomplete="off">
            </div>
            <div class="container-fluid">
                <label for="">Student LRN: </label>
                <input class="box" type="number" name="s_lrn" autocomplete="off">
            </div>
            <div class="container-fluid">
                <label for="">Gender: </label>
                Male
                <input class="" type="radio" name="s_gender" value="Male" autocomplete="off">
                Female
                <input class="" type="radio" name="s_gender" value="Female" autocomplete="off">
            </div>
            <div class="container-fluid">
                <button class="btn btn-primary mt-4" name="submit">Add Student</button>
            </div>
        </form>

        <!-- <table border="0">
            <thead>
                <tr>
                    <th colspan="5">
                        <h3><strong>Student List</strong></h3>
                    </th>
                </tr>
                <tr>
                    <th>Student ID</th>
                    <th>Student Name</th>
                    <th>Student LRN</th>
                    <th>Gender</th>
                </tr>
            </thead>

            <tbody>
                <?php


                $query = "SELECT * FROM students";
                $result = $conn->query($query);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                ?>
                        <tr>
                            <td><?php echo $row["student_id"] ?></td>
                            <td><?php echo $row["name"] ?></td>
                            <td><?php echo $row["lrn"] ?></td>
                            <td><?php echo $row["gender"] ?></td>
                        </tr>

                <?php
                    }
                } else {
                    echo "<tr><td colspan='5'> No Students</td></tr>";
                }
                ?>
            </tbody>
        </table> -->
    </div>


    <script>
        fetch("fetch.php")
            .then(response => response.json())
            .then(data => {
                data.forEach(student => {
                    const tbody = document.createElement('tr');
                    row.innerHTML = `
                <td>${student.id}</td>
                <td>${student.name}</td>
                <td>${student.lrn}</td>
                <td>${student.gender}</td>
                `;
                    tbody.appendChild(row);
                });
            })
    </script>
</body>

</html>