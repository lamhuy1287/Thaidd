create database thaidaddy;
use thaidaddy;
-- Tạo bảng Users
CREATE TABLE Users (
    user_id INT PRIMARY KEY AUTO_INCREMENT,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('Admin', 'Giáo vụ', 'Giáo viên', 'Sinh viên') NOT NULL
);

-- Tạo bảng Semesters
CREATE TABLE Semesters (
    semester_id INT PRIMARY KEY AUTO_INCREMENT,
    semester_name VARCHAR(50) NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL
);

-- Tạo bảng Subjects
CREATE TABLE Subjects (
    subject_id INT PRIMARY KEY AUTO_INCREMENT,
    subject_name VARCHAR(100) NOT NULL,
    duration INT NOT NULL, -- Tổng số giờ học
    semester_id INT,
    FOREIGN KEY (semester_id) REFERENCES Semesters(semester_id)
);

-- Tạo bảng Classes
CREATE TABLE Classes (
    class_id INT PRIMARY KEY AUTO_INCREMENT,
    class_name VARCHAR(50) NOT NULL,
    semester_id INT,
    FOREIGN KEY (semester_id) REFERENCES Semesters(semester_id)
);

-- Tạo bảng ClassSubjects
CREATE TABLE ClassSubjects (
    class_subject_id INT PRIMARY KEY AUTO_INCREMENT,
    class_id INT NOT NULL,
    subject_id INT NOT NULL,
    teacher_id INT,
    schedule VARCHAR(100), -- Lịch giảng dạy
    FOREIGN KEY (class_id) REFERENCES Classes(class_id),
    FOREIGN KEY (subject_id) REFERENCES Subjects(subject_id),
    FOREIGN KEY (teacher_id) REFERENCES Users(user_id)
);

-- Tạo bảng Students
CREATE TABLE Students (
    student_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    class_id INT,
    FOREIGN KEY (user_id) REFERENCES Users(user_id),
    FOREIGN KEY (class_id) REFERENCES Classes(class_id)
);

-- Tạo bảng Attendance
CREATE TABLE Attendance (
    attendance_id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT NOT NULL,
    class_subject_id INT NOT NULL,
    date DATE NOT NULL,
    status ENUM('Có mặt', 'Nghỉ', 'Đi trễ') NOT NULL,
    FOREIGN KEY (student_id) REFERENCES Students(student_id),
    FOREIGN KEY (class_subject_id) REFERENCES ClassSubjects(class_subject_id)
);

-- Tạo bảng Reports
CREATE TABLE Reports (
    report_id INT PRIMARY KEY AUTO_INCREMENT,
    class_id INT NOT NULL,
    teacher_id INT NOT NULL,
    semester_id INT NOT NULL,
    attendance_summary TEXT, -- Tóm tắt điểm danh
    academic_performance_summary TEXT, -- Tóm tắt kết quả học tập
    FOREIGN KEY (class_id) REFERENCES Classes(class_id),
    FOREIGN KEY (teacher_id) REFERENCES Users(user_id),
    FOREIGN KEY (semester_id) REFERENCES Semesters(semester_id)
);

-- Tạo bảng Timetable
CREATE TABLE Timetable (
    timetable_id INT PRIMARY KEY AUTO_INCREMENT,
    class_id INT NOT NULL,
    subject_id INT NOT NULL,
    teacher_id INT NOT NULL,
    day_of_week ENUM('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday') NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    FOREIGN KEY (class_id) REFERENCES Classes(class_id),
    FOREIGN KEY (subject_id) REFERENCES Subjects(subject_id),
    FOREIGN KEY (teacher_id) REFERENCES Users(user_id)
);
