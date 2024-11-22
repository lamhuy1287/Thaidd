<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$database = "thaidaddy";
$conn = mysqli_connect($servername, $username, $password, $database);
if (!$conn) {
    die("Kết nối thất bại: " . mysqli_connect_error());
  }
  if (!isset($_SESSION['user'])) {
    header("Location: login-logout/login.php");
    exit();
}
$user = $_SESSION['user']; // This should be the logged-in user's ID or identifier
$userQuery = "SELECT full_name FROM Users WHERE email = '$user'"; // Modify to your login identifier if it's not 'email'
$userResult = $conn->query($userQuery);
$fullName = "";

if ($userResult->num_rows > 0) {
    $userRow = $userResult->fetch_assoc();
    $fullName = $userRow['full_name'];
}
if (isset($_POST['semester_id'])) {
    $semester_id = $_POST['semester_id'];
} else {
    // Lỗi, không có semester_id
    echo "Không có học kỳ được chọn.";
    exit();
}

$semesterQuery = "
    SELECT 
        semester_name, 
        start_date, 
        end_date 
    FROM Semesters 
    WHERE semester_id = $semester_id
";
$semesterResult = $conn->query($semesterQuery);
$semester = $semesterResult->fetch_assoc();

// Truy vấn danh sách lớp học trong học kỳ
$classesQuery = "
    SELECT class_name 
    FROM Classes 
    WHERE semester_id = $semester_id
";
$classesResult = $conn->query($classesQuery);

// Truy vấn danh sách môn học trong học kỳ
$subjectsQuery = "
    SELECT subject_name 
    FROM Subjects 
    WHERE semester_id = $semester_id
";
$subjectsResult = $conn->query($subjectsQuery);
?>
<!DOCTYPE html>
<html>
<head>
	<title>admin</title>
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
	padding: 100px 180px;
	text-align: center;
}
  
    </style>
</head>
<body>
	<div class="sideMenu" id="side-menu">
        
		<a class="closebtn" href="javascript:void(0)" onclick="closeNav()">×</a>
		<div class="main-menu">
    <h2>
    <?php echo $fullName; ?>
    </h2>
    <a href="index.php"><i class="fa fa-home"></i>Trang chủ</a>
    <a href="information.php"><i class="fa fa-users"></i>Thông tin người dùng</a>
    <a href="semester.php" class="nav-link active text-black" style="background-color:#888888;"><i class="fa fa-calendar"></i>Thông tin học kỳ</a>
    <a href="classes.php"><i class="fa fa-building"></i>Thông tin lớp học</a>
    <a href="#"><i class="fa fa-check-circle"></i>Thông tin điểm danh</a>
    <a href="../login-logout/logout.php"><i class="fa fa-sign-out"></i>Đăng xuất</a>
</div>
	</div>
	<div id="content-area">
		<span onclick="openNav()" style="font-size:30px;cursor:pointer">☰ Menu</span>
        <hr>
		<h1>Chi tiết học kỳ</h1>

    <?php if ($semester): ?>
        <h2>Tên học kỳ: <?php echo $semester['semester_name']; ?></h2>
        <p><strong>Thời gian bắt đầu:</strong> <?php echo $semester['start_date']; ?></p>
        <p><strong>Thời gian kết thúc:</strong> <?php echo $semester['end_date']; ?></p>

        <h3>Danh sách lớp học</h3>
        <table>
            
            <?php if ($classesResult->num_rows > 0): ?>
                <?php while ($class = $classesResult->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $class['class_name']; ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td>Không có lớp học nào</td></tr>
            <?php endif; ?>
        </table>

        <h3>Danh sách môn học</h3>
        <table>
            
            <?php if ($subjectsResult->num_rows > 0): ?>
                <?php while ($subject = $subjectsResult->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $subject['subject_name']; ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td>Không có môn học nào</td></tr>
            <?php endif; ?>
        </table>
    <?php else: ?>
        <p>Học kỳ không tồn tại.</p>
    <?php endif; ?>

    <button class="btn btn-danger m-2" onclick="window.location.href='semester.php'">Quay lại</button>

	</div>
	<script>
	function openNav() {
	 document.getElementById("side-menu").style.width = "300px";
	 document.getElementById("content-area").style.marginLeft = "300px"; 
	}

	function closeNav() {
	 document.getElementById("side-menu").style.width = "0";
	 document.getElementById("content-area").style.marginLeft= "0";  
	}
	</script>
</body>
</html>
            