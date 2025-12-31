# 📅 Web Pengingat Deadline Akademik

Web ini adalah aplikasi berbasis PHP dan MySQL untuk membantudosen mengelola tugas, ujian, dan proyek akademik. Proyek ini juga terintegrasi dengan **Google Calendar** sehingga pengguna dapat menerima notifikasi otomatis.

---

## ⚡ Fitur

- Tambah, edit, dan hapus tugas akademik (Tugas, Ujian, Proyek)
- Integrasi dengan Google Calendar untuk setiap task
- Notifikasi email H-1 sebelum deadline
- Export data ke **CSV** untuk analisis
- Dashboard analitik dengan:
  - Total tugas
  - Total workload (jam)
  - Task mendekati deadline
  - Task yang terlambat
- Tampilan timeline (Gantt chart) & chart workload per bulan

---

## 🛠️ Teknologi

- PHP 8.x
- MySQL / MariaDB
- Composer (untuk dependency Google API)
- Bootstrap 5 (UI)
- Google API Client (Calendar integration)
- Chart.js & Frappe Gantt (visualisasi)

---

## 📦 Instalasi

1. Clone repository:

```bash
menuju okasi htdocs untuk xampp atau www untuk laragon
git clone https://github.com/RhmnVin/kudu-on-time.git
cd kudu-on-time

## Running
1. server local dapat menggunakan xampp ataupun laragon
2. aktifkan server apache dan mysql
3. import file sql ke database
4. akses website dengan link " localhost/schedule/index.php "
5. login sebagai admin atau dosen, dengan contoh akun adalah
admin :
      - email : admin@gmail.com
      - password : admin123
dosen :
      - email : clonningsh4@gmail.com
      - password : anakbandung11

