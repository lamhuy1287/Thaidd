<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$database = "thaidaddy";

// Kết nối database
$conn = mysqli_connect($servername, $username, $password, $database);
if (!$conn) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}

// Kiểm tra người dùng đã đăng nhập chưa
if (!isset($_SESSION['user'])) {
    header("Location: login-logout/login.php");
    exit();
}

// Lấy thông tin người dùng
$user = mysqli_real_escape_string($conn, $_SESSION['user']);
$userQuery = "SELECT full_name FROM Users WHERE email = '$user'";
$userResult = $conn->query($userQuery);
$fullName = "";

if ($userResult && $userResult->num_rows > 0) {
    $userRow = $userResult->fetch_assoc();
    $fullName = $userRow['full_name'];
}

// Lấy thông tin học kỳ
$semesterQuery = "
SELECT 
    Semesters.semester_id,
    Semesters.semester_name,
    COUNT(DISTINCT Classes.class_id) AS total_classes,
    COUNT(DISTINCT Students.student_id) AS total_students,
    COUNT(DISTINCT Subjects.subject_id) AS total_subjects
FROM 
    Semesters
LEFT JOIN Classes ON Semesters.semester_id = Classes.semester_id
LEFT JOIN Students ON Classes.class_id = Students.class_id
LEFT JOIN Subjects ON Semesters.semester_id = Subjects.semester_id
GROUP BY 
    Semesters.semester_id, Semesters.semester_name;
";

$semesterResult = $conn->query($semesterQuery);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông tin học kỳ</title>
    <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
          body {
            margin: 0;
            font-family: 'Roboto', sans-serif;
}
.sideMenu {
	height: 100%;
	width: 0;
	position: fixed;
	z-index: 1;
	top: 0;
	left: 0;
	background:  #0c787d;
	overflow-x: hidden;
	transition: 0.5s;
	padding-top: 60px;
}
.main-menu h2 {
	text-align: center;
	letter-spacing: 7px;
	color: #fff;
	background: #111;
	padding: 20px 0;
}
.sideMenu a {
	padding: 8px 8px 8px 32px;
	text-decoration: none;
	color: #fff;
	display: block;
	transition: 0.3s;
	font-size: 18px;
	margin-bottom: 20px;
	text-transform: uppercase;
	
}
.sideMenu a i {
	padding-right: 15px;
}
.main-menu a:hover {
	color: #f1f1f1;
	background: #BBBBBB;
}
.sideMenu .closebtn {
	position: absolute;
	top: 0;
	right: 25px;
	font-size: 36px;
	margin-left: 50px;
}
#content-area {
	transition: margin-left .5s;
	padding: 16px;
}
.content-text {
	
	text-align: center;
}
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
        }
    </style>
</head>
<body>
    <div class="sideMenu" id="side-menu">
        <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">×</a>
        <div class="main-menu">
            <h2><?php echo htmlspecialchars($fullName); ?></h2>
            <a href="index.php"><i class="fa fa-home"></i> Trang chủ</a>
            <a href="information.php"><i class="fa fa-users"></i> Thông tin người dùng</a>
            <a href="semester.php" class="nav-link active text-black" style="background-color:#888888;"><i class="fa fa-calendar"></i> Thông tin học kỳ</a>
            <a href="classes.php"><i class="fa fa-building"></i> Thông tin lớp học</a>
            <a href="#"><i class="fa fa-check-circle"></i> Thông tin điểm danh</a>
            <a href="../login-logout/logout.php"><i class="fa fa-sign-out"></i> Đăng xuất</a>
        </div>
    </div>
    <div id="content-area">
        <span onclick="openNav()" style="font-size:30px;cursor:pointer">☰ Menu</span>
        <h1>Thông tin học kỳ</h1>
        <div class="content-text">
            <table>
                <tr>
                    <th>ID Học Kỳ</th>
                    <th>Tên Học Kỳ</th>
                    <th>Tổng Số Lớp</th>
                    <th>Tổng Số Sinh Viên</th>
                    <th>Tổng Số Môn Học</th>
                    <th>Chi tiết</th>
                </tr>
                <?php
                if ($semesterResult && $semesterResult->num_rows > 0) {
                    while ($row = $semesterResult->fetch_assoc()) {
                        $semester_id = $row["semester_id"];
                        echo "<tr>
                                <td>{$row['semester_id']}</td>
                                <td>{$row['semester_name']}</td>
                                <td>{$row['total_classes']}</td>
                                <td>{$row['total_students']}</td>
                                <td>{$row['total_subjects']}</td>
                                <td>
                                <form method='POST' action='semester_detail.php' style='display:inline;'>
                                <input name='semester_id' value='$semester_id' type='hidden'>
                                <button type='submit' class='btn btn-danger m-2' >Chi tiết</button>
                                </form>
                                </td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>Không có dữ liệu</td></tr>";
                }
                ?>
            </table>
        </div>
    </div>
    <script>
        function openNav() {
            document.getElementById("side-menu").style.width = "300px";
            document.getElementById("content-area").style.marginLeft = "300px";
        }
        function closeNav() {
            document.getElementById("side-menu").style.width = "0";
            document.getElementById("content-area").style.marginLeft = "0";
        }
    </script>
</body>
</html>
