# Handcomp - Hardware Complaint Management System

Handcomp is a comprehensive PHP-based web application designed for organizations to streamline the reporting, tracking, and resolving of IT hardware complaints, as well as managing the complete lifecycle of hardware inventory. The system features role-based access control, allowing different levels of staff to log complaints and the IT Support Cell (ITSC) to track inventory, reallocate devices, and manage support tickets.

## Features

* **Role-Based Dashboards (RBAC):** Customized interfaces and permissions based on user designation (Section Officer, Branch Officer, ITSC).
* **Smart Complaint Registration:** Dynamic forms that auto-fetch assigned devices and serial numbers using asynchronous JavaScript (Fetch API). Dynamic dropdowns suggest common issues based on the selected device type.
* **Complaint Registration:** Simple and intuitive forms for officers to log hardware issues.
* **Complaint Lifecycle Tracking:** ITSC can update complaint statuses from **Pending** -> **Ongoing** -> **Resolved** with automated timestamp tracking.
* **Hardware Inventory Management:** A robust data table with advanced pagination, search, and filtering to add or update hardware records. 
* **Smart Reallocation & Conflict Resolution:** Automatically detects if a device being assigned is already placed elsewhere, offering options to swap the device or move the old one to the "STORE ROOM".
* **Automated Disposal List Generation:** Automatically queries and generates a list of hardware devices that are older than 6 years from their date of purchase for easy disposal planning.

## User Roles

The system supports three primary user roles:
1. **ASSTT. ACCOUNTS OFFICER (Section Officer):** Can manually select a specific section, device, and serial number to report an issue to the ITSC.
2. **SR. ACCOUNTS OFFICER (Branch Officer):** Bound automatically to their own assigned hardware inventory (using their session name) to quickly log complaints without searching through sections.
3. **ITSC (IT Support Cell):** The administrative tier. Responsible for monitoring incoming complaints, tracking resolution times, adding/updating the global hardware inventory, and generating disposal reports.

## Technology Stack

* **Backend:** PHP (Vanilla)
* **Database:** MySQL
* **Frontend:** HTML5, CSS3, JavaScript (Fetch API)
* **Server:** Apache (via XAMPP)

## Project Structure

```text
Handcomp/
    branch_officer.php
    fetch_serial.php
    insert_complaint.php
    itsc.php
    login.php
    login_authenticate.php
    logout.php
    README.md
    section_officer.php
├── addupdate/
│   ├── add_hardware.php
│   ├── add_update_hardware.php
│   ├── duplicate_device.php
│   └── finalize_update.php
│   ├── generate_disposal.php
│   ├── move_to_store.php
│   ├── replace_hardware.php
│   └── update_hardware.php
├── assets/
│   ├── css/
│   │   └── style.css
│   └── js/
│       └── script.js
├── complaint/
│   ├── complaint_register.php
│   └── resolved_complaints.php
│   └── update_status.php
├── config/
│   └── db_connect.php
```

### Key Files & Directories
* `login.php` / `login_authenticate.php` / `logout.php`: Handles user sessions and authentication.
* `section_officer.php`: Dashboard for Assistant Accounts Officers.
* `branch_officer.php`: Dashboard for Senior Accounts Officers.
* `itsc.php`: Dashboard for the IT Support Cell.
* `insert_complaint.php`: Backend script to process and save new complaints into the database.
* `fetch_serial.php`: API endpoint to fetch hardware serial numbers dynamically based on the selected section and device type.
* `config/db_connect.php`: Database connection configuration.
* `addupdate/finalize_update.php`: Backend script for processing hardware record updates (such as moving items to the store).

## Database Schema (Required Tables)

To run this application, you will need a MySQL database named `handcomp` with the following tables:
* `users`: Stores `username`, `password`, `full_name`, and `designation`.
* `hw_inventory`: Stores `placed` (section/officer), `type` (device type), and `hw_number` (serial number).
* `complaint`: Stores `forwarded_by`, `device`, `serial_no`, `complaint` (issue type), `remarks`, and `status`.

## Installation & Setup

1. Install XAMPP or any standard LAMP/WAMP stack.
2. Clone or extract the project folder (`Handcomp`) into your web server's root directory (e.g., `c:\xampp\htdocs\Handcomp`).
3. Start Apache and MySQL from the XAMPP Control Panel.
4. Create a MySQL database named `handcomp`.
5. Import your database schema and initial data.
6. Verify the database credentials in `config/db_connect.php` (default: root with no password).
7. Navigate to `http://localhost/Handcomp/login.php` in your web browser.