CREATE DATABASE IF NOT EXISTS bus_pass_db_repaired CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE bus_pass_db_repaired;

CREATE TABLE users (
    id int(11) NOT NULL AUTO_INCREMENT,
    name varchar(100) NOT NULL,
    email varchar(100) NOT NULL,
    password varchar(255) NOT NULL,
    role enum('student','admin') NOT NULL DEFAULT 'student',
    created_at timestamp NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (id),
    UNIQUE KEY email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO users (id, name, email, password, role, created_at) VALUES
(1, 'System Administrator', 'tpandi412@gmail.com', '$2y$10$u59u2cfbJqmCBf/k5Mi9vu5BzszulfTbq/eijRzVk.wyq.FsG/rTO', 'admin', '2026-09-30 05:58:16'),
(2, 'John Doe', 'student@buspass.com', '$2y$10$VIat0zjPXWOkhWZNf4w3KOwXVJCyJ9p7bma79wQHR692BrAmCy7Uu', 'student', '2026-09-30 06:09:11'),
(3, 'Aarav Mehta', 'aarav.mehta.2026@example.com', '$2y$10$VXKVdHWGlvZsqPBjs1haFe9e52CKpxhM9ifi1Wm8b/qKgAHU0a34C', 'student', '2026-09-30 16:16:22'),
(4, 'Nila Krishnan', 'nila.krishnan.2026@example.com', '$2y$10$1W6ZigGDdTWOSSH9wGtabeyK.Now4yWdo/QB5EgmBa/Xm2nNn4IFO', 'student', '2026-09-30 16:16:36'),
(5, 'Rohan Iyer', 'rohan.iyer.2026@example.com', '$2y$10$apucBW/3tanrLSbXaYg3OOS48crsfEPMmLMNBZAu4GmuLFhGC2CfG', 'student', '2026-09-30 16:16:36'),
(6, 'Mira Das', 'mira.das.2026@example.com', '$2y$10$alZOPsLiX6GqxF/YVH7aJuj4I3ZM7giTliSWZytVhbrgF9qp/YYKm', 'student', '2026-09-30 16:16:36');

CREATE TABLE buses (
    id int(11) NOT NULL AUTO_INCREMENT,
    bus_number varchar(50) NOT NULL,
    bus_name varchar(100) NOT NULL,
    capacity int(11) NOT NULL,
    status enum('Active','Inactive') NOT NULL DEFAULT 'Active',
    created_at timestamp NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (id),
    UNIQUE KEY bus_number (bus_number)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO buses (id, bus_number, bus_name, capacity, status, created_at) VALUES
(1, 'BUS-101', 'Greenline Express A', 45, 'Active', '2026-09-30 00:28:16'),
(2, 'BUS-102', 'City Deluxe B', 50, 'Active', '2026-09-30 00:28:16'),
(3, 'BUS-103', 'Suburban Transit C', 40, 'Active', '2026-09-30 00:28:16'),
(4, 'BUS-104', 'Campus Shuttle D', 55, 'Active', '2026-09-30 00:28:16'),
(5, 'BUS-105', 'Metro Cruiser E', 40, 'Inactive', '2026-09-30 00:28:16');

CREATE TABLE routes (
    id int(11) NOT NULL AUTO_INCREMENT,
    route_name varchar(150) NOT NULL,
    start_point varchar(100) NOT NULL,
    end_point varchar(100) NOT NULL,
    stops text NOT NULL,
    fare decimal(10,2) NOT NULL DEFAULT 0.00,
    created_at timestamp NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO routes (id, route_name, start_point, end_point, stops, fare, created_at) VALUES
(1, 'Dindigul to Batlagundu', 'Dindigul Bus Stand', 'Batlagundu', 'Dindigul BS, Vadamadurai Cross, Sempatty, Batlagundu', 45.00, '2026-09-30 00:28:16'),
(2, 'Dindigul to Vedasandur', 'Dindigul Bus Stand', 'Vedasandur', 'Dindigul BS, Collectorate, Eriodu, Vedasandur', 35.00, '2026-09-30 00:28:16'),
(3, 'Dindigul to Nilakottai', 'Dindigul Bus Stand', 'Nilakottai', 'Dindigul BS, Chettinaickenpatti, Chinnalapatti, Nilakottai', 40.00, '2026-09-30 00:28:16'),
(4, 'Dindigul to Oddanchatram', 'Dindigul Bus Stand', 'Oddanchatram', 'Dindigul BS, Reddiarchatram, Oddanchatram BS', 50.00, '2026-09-30 00:28:16');

CREATE TABLE students (
    id int(11) NOT NULL AUTO_INCREMENT,
    user_id int(11) NOT NULL,
    student_id varchar(50) NOT NULL,
    phone varchar(20) NOT NULL,
    college varchar(150) NOT NULL,
    department varchar(100) NOT NULL,
    year varchar(20) NOT NULL,
    address text NOT NULL,
    created_at timestamp NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (id),
    UNIQUE KEY student_id (student_id),
    KEY user_id (user_id),
    CONSTRAINT students_ibfk_1 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO students (id, user_id, student_id, phone, college, department, year, address, created_at) VALUES
(1, 2, 'STU-2026-001', '9876543210', 'City Engineering College', 'Computer Science', '3rd Year', '123 Campus Road, Dindigul', '2026-09-30 00:39:11'),
(2, 3, 'STU-2026-002', '9840010001', 'Riverdale Institute of Technology', 'Computer Science', '1st Year', '12 Lake View Road, Chennai', '2026-09-30 16:16:22'),
(3, 4, 'STU-2026-003', '9840010002', 'Riverdale Institute of Technology', 'Information Technology', '2nd Year', '24 Green Park Avenue, Chennai', '2026-09-30 16:16:36'),
(4, 5, 'STU-2026-004', '9840010003', 'Riverdale Institute of Technology', 'Electrical Engineering', '3rd Year', '8 Palm Grove Street, Coimbatore', '2026-09-30 16:16:36'),
(5, 6, 'STU-2026-005', '9840010004', 'North Shore College', 'Mechanical Engineering', '4th Year', '19 Hillcrest Road, Madurai', '2026-09-30 16:16:36');

CREATE TABLE bus_pass_applications (
    id int(11) NOT NULL AUTO_INCREMENT,
    student_id int(11) NOT NULL,
    route_id int(11) NOT NULL,
    bus_id int(11) NOT NULL,
    pass_type enum('Daily','Monthly','Quarterly','Semester') NOT NULL,
    start_date date NOT NULL,
    end_date date NOT NULL,
    application_date timestamp NOT NULL DEFAULT current_timestamp(),
    status enum('Pending','Approved','Rejected') NOT NULL DEFAULT 'Pending',
    remarks text DEFAULT NULL,
    approved_at timestamp NULL DEFAULT NULL,
    PRIMARY KEY (id),
    KEY student_id (student_id),
    KEY route_id (route_id),
    KEY bus_id (bus_id),
    CONSTRAINT bus_pass_applications_ibfk_1 FOREIGN KEY (student_id) REFERENCES students (id) ON DELETE CASCADE,
    CONSTRAINT bus_pass_applications_ibfk_2 FOREIGN KEY (route_id) REFERENCES routes (id),
    CONSTRAINT bus_pass_applications_ibfk_3 FOREIGN KEY (bus_id) REFERENCES buses (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO bus_pass_applications (id, student_id, route_id, bus_id, pass_type, start_date, end_date, application_date, status, remarks, approved_at) VALUES
(1, 1, 1, 1, 'Monthly', '2026-09-30', '2026-10-30', '2026-09-30 00:39:44', 'Approved', 'Verified student credentials and approved.', '2026-09-30 00:39:44');
