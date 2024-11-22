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
// Lấy học kỳ hiện tại
$currentSemesterQuery = "
    SELECT semester_id, semester_name 
    FROM Semesters 
    WHERE CURRENT_DATE BETWEEN start_date AND end_date
";
$currentSemesterResult = $conn->query($currentSemesterQuery);

if ($currentSemesterResult->num_rows > 0) {
    $currentSemester = $currentSemesterResult->fetch_assoc();
    $semester_id = $currentSemester['semester_id'];
    $semester_name = $currentSemester['semester_name'];
} else {
    die("Không tìm thấy học kỳ hiện tại.");
}

// Lấy danh sách lớp học trong học kỳ hiện tại
$classesQuery = "SELECT class_id, class_name FROM Classes WHERE semester_id = $semester_id";
$classesResult = $conn->query($classesQuery);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Danh sách lớp học</title>
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
        table {
            border-collapse: collapse;
            width: 100%;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            
        }
        th {
            background-color: #f2f2f2;
        }
        .details {
            display: none;
            width: 100%;
        }
        
        .container {
            display: flex;
            justify-content:center;
            align-items: flex-start;
        }
        .details-table {
            margin-right: 20px;
            
        }
        
    </style>
    <script>
        function toggleDetails(classId) {
            const detailsDiv = document.getElementById(`details-${classId}`);
            if (detailsDiv.style.display === "none") {
                detailsDiv.style.display = "block";
            } else {
                detailsDiv.style.display = "none";
            }
        }
    </script>
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
    <a href="semester.php"><i class="fa fa-calendar"></i>Thông tin học kỳ</a>
    <a href="classex.php" class="nav-link active text-black" style="background-color:#888888;"><i class="fa fa-building"></i>Thông tin lớp học</a>
    <a href="#"><i class="fa fa-check-circle"></i>Thông tin điểm danh</a>
    <a href="../login-logout/logout.php"><i class="fa fa-sign-out"></i>Đăng xuất</a>
</div>
	</div>
	<div id="content-area">
		<span onclick="openNav()" style="font-size:30px;cursor:pointer">☰ Menu</span>
		<h1>Danh sách các lớp học</h1>
    <h2>Học kỳ hiện tại: <?php echo $semester_name; ?></h2>

    <table>
        <tr>
            <th>Tên lớp học</th>
            <th>Hành động</th>
        </tr>
        <?php if ($classesResult->num_rows > 0): ?>
            <?php while ($class = $classesResult->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $class['class_name']; ?></td>
                    <td>
                        <button onclick="toggleDetails(<?php echo $class['class_id']; ?>)">Hiển thị chi tiết</button>
                    </td>
                </tr>
                <tr id="details-<?php echo $class['class_id']; ?>" class="details">
                    <td colspan="2" class="detailss">
                        
                        <?php
                        $classId = $class['class_id'];

                        // Lấy danh sách sinh viên
                        $studentsQuery = "
                            SELECT Users.full_name 
                            FROM Students 
                            INNER JOIN Users ON Students.user_id = Users.user_id 
                            WHERE Students.class_id = $classId
                        ";
                        $studentsResult = $conn->query($studentsQuery);

                        // Lấy danh sách môn học
                        $subjectsQuery = "
                            SELECT subject_name 
                            FROM Subjects 
                            WHERE semester_id = $semester_id
                        ";
                        $subjectsResult = $conn->query($subjectsQuery);
                        ?>
                        <div class="container">
                        <div class="details-table">
                        <h3>Danh sách sinh viên</h3>
                        <table>
                            <tr><th>Tên sinh viên</th></tr>
                            <?php if ($studentsResult->num_rows > 0): ?>
                                <?php while ($student = $studentsResult->fetch_assoc()): ?>
                                    <tr><td><?php echo $student['full_name']; ?></td></tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td>Không có sinh viên nào.</td></tr>
                            <?php endif; ?>
                        </table>
                        </div>
                        <div class="details-table">
                        <h3>Danh sách môn học</h3>
                        <table>
                            <tr><th>Tên môn học</th></tr>
                            <?php if ($subjectsResult->num_rows > 0): ?>
                                <?php while ($subject = $subjectsResult->fetch_assoc()): ?>
                                    <tr><td><?php echo $subject['subject_name']; ?></td></tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td>Không có môn học nào.</td></tr>
                            <?php endif; ?>
                        </table>
                        </div>
                        </div>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="2">Không có lớp học nào trong học kỳ này.</td></tr>
        <?php endif; ?>
    </table>
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
