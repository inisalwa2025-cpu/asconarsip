ARSIPIN - PHP + MySQL + InfinityFree/XAMPP

FOLDER:
C:\xampp\htdocs\arsipin\asconarsip\
  index.html
  register.html
  authentication.html
  sucessverification.html
  koneksi.php
  api\
    documents.php
    upload.php
    edit.php
    delete.php
    restore.php
    file.php
    storage.php
  database\database_arsip.sql
  uploads\

DATABASE:
Import database_arsip.sql ke phpMyAdmin.

URL lokal:
http://localhost/arsipin/asconarsip/index.html

LOGIN DEMO FRONTEND:
Admin: password admin123
Pengawas: password pengawas123
Email cukup email yang valid.

CATATAN:
1. Pastikan Apache dan MySQL di XAMPP berwarna hijau/running.
2. Folder asconarsip/uploads harus writable oleh PHP.
3. Semua data dokumen sekarang dibaca dari MySQL, bukan localStorage.
4. File fisik disimpan di asconarsip/uploads.
5. Endpoint file.php menampilkan/mengunduh file berdasarkan ID.

INFINITYFREE:
1. Upload seluruh folder asconarsip ke public_html.
2. Buka index.html melalui URL hosting. Dashboard memakai endpoint di folder api/.
3. Import database_arsip.sql setelah memilih database if0_42923619_database_arsip di phpMyAdmin.
4. Konfigurasi database sudah berada di koneksi.php: sql201.infinityfree.com, port 3306, username if0_42923619, dan nama database hosting.
5. Pastikan folder uploads dapat ditulis oleh PHP. Folder ini dibuat otomatis saat upload pertama jika hosting mengizinkan.
6. Batas upload ditentukan oleh paket InfinityFree. Directive php_value sengaja tidak dipakai karena dapat menyebabkan HTTP 500.
