# Face Detection and Recognition System using PHP, MySQL and Face-API.js

<div align="center">

![PHP](https://img.shields.io/badge/PHP-8.x-blue?style=for-the-badge&logo=php)
![MySQL](https://img.shields.io/badge/MySQL-Database-orange?style=for-the-badge&logo=mysql)
![JavaScript](https://img.shields.io/badge/JavaScript-ES6-yellow?style=for-the-badge&logo=javascript)
![face-api.js](https://img.shields.io/badge/FaceAPI.js-AI-green?style=for-the-badge)
![Status](https://img.shields.io/badge/Status-Completed-success?style=for-the-badge)
![License](https://img.shields.io/badge/License-MIT-lightgrey?style=for-the-badge)
![GitHub Repo stars](https://img.shields.io/github/stars/mahi-tech-dev/FACE_DETECTION_SYSTEM?style=for-the-badge)

![GitHub forks](https://img.shields.io/github/forks/mahi-tech-dev/FACE_DETECTION_SYSTEM?style=for-the-badge)

A web-based Face Detection and Recognition System developed using **PHP, JavaScript, face-api.js, and MySQL**.

</div>


# 📖 Project Overview

Face Detection and Recognition System is a web-based application developed using PHP, MySQL, JavaScript, and face-api.js. It provides image-based and live camera face recognition with role-based authentication, person management, and recognition history tracking.

The project demonstrates the integration of web technologies with AI-powered face recognition for real-world surveillance and identity verification applications.


---

# 📌 Features

### 👨‍💼 Admin Module
- Admin Login
- Dashboard
- Manage Users
- Recognition History
- Face Matching
- Profile Management

### 👤 User Module
- User Login
- User Dashboard
- Upload Images
- Match Face
- View Match History
- Profile Management

### 🤖 AI Face Recognition
- Face Detection using face-api.js
- Image-based Matching
- Live Camera Detection
- Recognition Logs
- Person Management

### 📂 Person Management
- Add Person
- View Persons
- Edit Person
- Delete Person
- Bulk Delete Support

---

# 🛠 Technologies Used

| Technology | Purpose |
|-------------|---------|
| 🐘 PHP | Backend |
| 🗄 MySQL | Database |
| ⚡ JavaScript | Client-side Logic |
| 🤖 face-api.js | Face Detection |
| 🎨 HTML/CSS | User Interface |
| 📷 Webcam API | Live Camera Detection |
| 🔥 XAMPP | Local Server |

---

# 📊 Project Information

| Property | Details |
|-----------|---------|
| Project Type | Web Application |
| Status | Completed |
| Version | 1.0 |
| Release Date | 2026 |
| Author | Mahesh Ugale |

---

# 🎯 Objectives

- Develop a secure face recognition web application.
- Enable image-based and live camera face matching.
- Maintain recognition history and logs.
- Provide separate Admin and User modules.
- Demonstrate AI integration using face-api.js.

---

# 📁 Folder Structure

```text
FACE_DETECTION_SYSTEM
│
├── config/
├── js/
├── models/
├── sql/
├── uploads/
│
├── add_person.php
├── admin_dashboard.php
├── admin_match_history.php
├── dashboard.php
├── delete_person.php
├── edit_person.php
├── get_faces.php
├── live_detection.php
├── login.php
├── logout.php
├── manage_users.php
├── match_face.php
├── profile.php
├── save_match.php
├── user_dashboard.php
├── user_match_face.php
├── user_match_history.php
├── view_persons.php
│
├── README.md
└── .gitignore
```

---

# 🚀 Installation Guide

## 1️⃣ Clone Repository

```bash
git clone https://github.com/mahi-tech-dev/FACE_DETECTION_SYSTEM.git
```

---

## 2️⃣ Move Project

Place the folder inside:

```text
C:\xampp\htdocs\
```

---

## 3️⃣ Start XAMPP

Start:

- Apache ✅
- MySQL ✅

---

## 4️⃣ Create Database

Open:

```text
http://localhost/phpmyadmin
```

Create database:

```sql
face_recognition
```

Import the SQL file from:

```text
sql/
```

---

## 5️⃣ Configure Database

Edit:

```php
config/db.php
```

Example:

```php
<?php
$conn = new mysqli("localhost", "root", "", "face_recognition");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
```

---

## 6️⃣ Run Project

Open:

```text
http://localhost/FACE_DETECTION_SYSTEM/
```

---

---

# 🎬 Application Modules

- Image-based Face Matching
- Live Camera Face Detection
- Recognition History
- Admin Dashboard
- User Dashboard
- Person Management System

---

# 🏗 System Architecture

```text
User Image / Camera
          ↓
Face Detection (face-api.js)
          ↓
Feature Extraction
          ↓
Face Matching
          ↓
MySQL Database
          ↓
Recognition History
```

---

# ⭐ Key Highlights

✅ Face Detection using face-api.js

✅ Image-based Face Recognition

✅ Live Camera Detection

✅ Recognition History Logging

✅ Admin and User Authentication

✅ Person Management System

✅ Session Security

✅ Responsive Web Interface

---

# 📸 Screenshots

### Login Page

<img width="1920" height="1080" alt="login" src="https://github.com/user-attachments/assets/5f0d1223-2f39-4dda-baab-f3393a0a0eba" />


---

### Admin Dashboard

<img width="1920" height="1080" alt="dashboard" src="https://github.com/user-attachments/assets/5a1f6431-ee8c-42d4-8cbe-9444228390d6" />


---

### Face Matching

<img width="1920" height="1080" alt="Match Face" src="https://github.com/user-attachments/assets/425d11b8-1a9e-41e0-90fe-bdcb2aff7fb8" />


---

### Match History

<img width="1920" height="1080" alt="Match History" src="https://github.com/user-attachments/assets/c00c7305-9ace-46e3-ad86-a505a9fe2710" />

---

### Manage Users

<img width="1927" height="1077" alt="Manage Users" src="https://github.com/user-attachments/assets/501cccc3-42c3-4ef8-b6a2-5a28efe8c44f" />

---

# 🤖 AI Features

- Real-time Face Detection using face-api.js
- Image-based Face Recognition
- Live Camera Integration
- Face Matching and Verification
- Recognition History Logging
- Person Dataset Management
- Admin and User Authentication

---

# 🔒 Security

- Session Authentication
- Admin/User Separation
- Database Validation
- Secure Login System
- SQL Injection Prevention
- Role-Based Access Control
  
---

# 📈 Future Improvements

- SmartVision AI Integration
- Unknown Face Detection
- Threat-Level Alert System
- Automatic Snapshot Logging
- Real-Time Surveillance Dashboard
- Analytics and Reporting Module
- Multi-Camera Support
  
---

# 👨‍💻 Developer

Mahesh Ugale

🎓 Bachelor of Computer Science (BCS)

🚀 Interests

- Artificial Intelligence
- Data Analytics
- Application Development
- Computer Vision

🌐 GitHub:
https://github.com/mahi-tech-dev

---

# ⭐ Support

If you like this project, consider giving it a ⭐ on GitHub.

---

<div align="center">

### Made with ❤️ using PHP, JavaScript, face-api.js and MySQL

</div>
