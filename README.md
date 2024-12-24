# Digital Classroom  

This repository contains the codebase for the **Digital Classroom**, a comprehensive platform designed to streamline academic and administrative tasks in a university environment. The system includes features for user registration, role-based access, and dedicated dashboards for administrators, teachers, and students.

---

## Features  
### 1. **User Registration & Authentication Module**  
- **Landing Page**: Welcomes users to the platform with an intuitive interface.  
- **Registration Page**:  
  - Collects essential user details like name, email, and phone number.  
  - Implements input validation for data accuracy.  
  - Requires email verification for account activation.  
- **Login Page**:  
  - Supports role-based login for Admins, Teachers, and Students, granting access based on their roles.  

### 2. **Admin Dashboard Module**  
- **Department Management**:  
  - Add or delete departments and manage department heads.  
- **Class & Teacher Management**:  
  - Assign teachers to classes and manage class details.  
- **Attendance Monitoring**:  
  - Monitor student attendance and generate alerts for low attendance.  
- **Profile Management**:  
  - Edit personal information and change the admin password.  

### 3. **Teacher Dashboard Module**  
- **Class Management**:  
  - Create new classes and manage existing ones.  
- **Attendance Monitoring**:  
  - Track attendance for students in their classes.  
- **Assignments and Announcements**:  
  - Assign tasks, review submissions, and share announcements with students.  

### 4. **Class Management Module**  
- **Assignment Management**:  
  - Assign tasks and review submissions from students.  
- **Announcements**:  
  - Create and share announcements with students.  
- **Attendance Management**:  
  - Mark student attendance for each class.  
- **Student Enrollment**:  
  - Add or remove students from classes.  

### 5. **Student Dashboard Module**  
- **Class Enrollment**:  
  - Join classes using a class code.  
- **Attendance Records**:  
  - View attendance records for enrolled classes.  
- **Access to Class Materials**:  
  - Access assignments and announcements shared by teachers.  

---

## Project Structure  
📂 Digital-Classroom  
├── 📁 Home/            # Home page and core logic  
├── 📁 images/          # Images and static visual assets  
├── 📁 media/           # Media files such as audio or video resources  
├── 📁 phpmailer/       # Email verification and mailing functionalities  
├── 📁 Student/         # Student-specific features and components  
├── 📁 SuperAdmin/      # Admin-level management features  
├── 📁 Teacher/         # Teacher-specific features and components  
├── 📁 uploads/         # Uploaded files and documents  
├── 📄 .gitignore       # Git ignore file  
└── 📄 LICENSE          # License file  
