# 🔐 Secure File Vault

A secure PHP-based file storage application that allows users to upload, encrypt, download, and manage files safely. The application uses **AES-256-CBC encryption** to protect uploaded files and includes user authentication, profile management, and password management features.

---

## 🚀 Features

- 🔑 User Registration & Login
- 🔒 Secure Password Hashing
- 📂 Upload Files
- 🔐 AES-256-CBC File Encryption
- 📥 Secure File Download & Decryption
- 🗑️ Delete Uploaded Files
- 👤 User Profile Management
- 🔄 Change Password
- 📊 User Dashboard
- 🛡️ Session Security
- 📁 File Type Validation
- 📏 Upload Size Limit
- 🗄️ MySQL Database Support

---

## 🛠️ Technology Stack

- PHP 8+
- MySQL
- HTML5
- CSS3
- JavaScript
- AES-256-CBC Encryption
- Apache/XAMPP

---

## 📁 Project Structure

```
secure-file-vault/
│
├── assets/
│   ├── css/
│   ├── images/
│   └── js/
│
├── config/
│   ├── config.php
│   └── database.php
│
├── includes/
│   ├── auth.php
│   ├── encrypt.php
│   ├── decrypt.php
│   ├── functions.php
│   ├── header.php
│   └── footer.php
│
├── uploads/
│   ├── encrypted/
│   └── temp/
│
├── dashboard.php
├── upload.php
├── download.php
├── delete.php
├── profile.php
├── change-password.php
├── login.php
├── register.php
├── logout.php
├── database.sql
└── README.md
```

---

## ⚙️ Installation

### 1. Clone the Repository

```bash
git clone https://github.com/yogeshkumarsaini/secure-file-vault.git
```

or download the ZIP file.

---

### 2. Move Project

Copy the project into your web server directory.

**XAMPP**

```
htdocs/secure-file-vault
```

---

### 3. Create Database

Open phpMyAdmin and import

```
database.sql
```

or

```sql
mysql -u root -p < database.sql
```

---

### 4. Configure Database

Edit

```
config/database.php
```

Update:

```php
$dbHost = "localhost";
$dbName = "secure_file_vault";
$dbUser = "root";
$dbPass = "";
```

---

### 5. Configure Encryption Key

Open

```
config/config.php
```

Replace the encryption key with your own secure 32-byte key.

Example:

```php
define('APP_ENCRYPTION_KEY_HEX', 'YOUR_64_CHARACTER_HEX_KEY');
```

Generate a key:

```bash
php -r "echo bin2hex(random_bytes(32));"
```

---

### 6. Start Server

Run Apache and MySQL from XAMPP.

Open:

```
http://localhost/secure-file-vault
```

---

## 🔐 Security Features

- AES-256-CBC Encryption
- Password Hashing
- Secure PHP Sessions
- HTTPOnly Cookies
- SameSite Cookies
- File Extension Validation
- Upload Size Restrictions
- SQL Injection Protection (Prepared Statements)
- User Authentication Middleware

---

## 📋 Default Upload Limit

```
25 MB
```

Can be changed in:

```
config/config.php
```

```php
define('MAX_UPLOAD_SIZE', 25 * 1024 * 1024);
```

---

## 📂 Allowed File Types

```
jpg
jpeg
png
gif
pdf
doc
docx
xls
xlsx
ppt
pptx
txt
zip
```

Modify in:

```php
ALLOWED_EXTENSIONS
```

---

## 👤 User Workflow

1. Register
2. Login
3. Upload File
4. File gets encrypted
5. View Dashboard
6. Download (Automatic Decryption)
7. Delete Files
8. Update Profile
9. Change Password

---

## 🗄️ Database Tables

### users

| Column | Description |
|---------|-------------|
| id | User ID |
| username | Username |
| email | Email Address |
| password_hash | Hashed Password |
| created_at | Registration Date |

### files

| Column | Description |
|---------|-------------|
| id | File ID |
| user_id | Owner |
| original_name | Original Filename |
| stored_name | Encrypted Filename |
| iv | Encryption IV |
| filesize | File Size |
| mime_type | MIME Type |
| uploaded_at | Upload Date |

