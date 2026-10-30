# Cara Deploy Aplikasi "Maju Jaya" ke InfinityFree (otomatis dari GitHub)

Panduan ini untuk pertama kali. Setelah selesai, setiap kali kamu menjalankan `git push`,
GitHub otomatis mengirim file aplikasinya ke hosting. Kerjakan Bagian A → B → C berurutan.
Kalau ada yang gagal, lompat ke Bagian D.

Siapkan Notepad untuk mencatat 7 nilai rahasia di Bagian A — semuanya dipakai di Bagian B.

---

## Bagian A — Siapkan hosting InfinityFree

1. Buka https://www.infinityfree.com, klik **Sign Up**, isi email dan password, buat akun.
2. Masuk ke **Client Area** (https://dash.infinityfree.com), klik **Create Account**.
3. Pilih **Free Subdomain**, isi nama situs (contoh: `majujaya`), pilih domain gratisnya
   (contoh: `free.nf`), lalu klik **Create Account**. Tunggu beberapa menit sampai akun aktif.
4. Setelah akun jadi, klik **Manage** / **Control Panel** pada akun itu. Panel ini disebut vPanel.
5. Catat data FTP: di Client Area, buka akun itu, lihat bagian **FTP Details**, klik
   **Show/Hide** untuk menampilkan password. Catat:
   - **FTP Hostname** (contoh: `ftpupload.net`)
   - **FTP Username** (contoh: `if0_12345678`)
   - **FTP Password**
6. Di vPanel, klik menu **MySQL Databases**.
7. Di bagian **Create New Database**, isi nama pendek (contoh: `majujaya`), klik **Create Database**.
8. Halaman itu langsung menampilkan detail database. Catat:
   - **MySQL Host Name** (contoh: `sql302.infinityfree.com`) — ingat, ini **bukan** `localhost`
   - **MySQL Database Name** (contoh: `if0_12345678_majujaya`)
   - **MySQL User Name** (contoh: `if0_12345678`)
   - **Password**: sama dengan FTP Password di langkah 5.
9. Masih di halaman MySQL Databases, klik tombol **phpMyAdmin** di baris database tadi.
10. Di phpMyAdmin, klik **nama database** di daftar sebelah KIRI. Jangan memakai tab Import
    dari halaman depan sebelum database dipilih — nanti salah tempat.
11. Klik tab **Import** di bagian atas → klik **Choose File** → pilih file
    `deploy/infinityfree/database.sql` dari folder project di komputermu → klik **Go**.
12. Kalau berhasil muncul tulisan hijau "Import has been successfully finished", dan di
    sidebar kiri ada **8 tabel**: profil_usaha, coa, barang, pelanggan, pemasok, karyawan,
    jurnal_umum, detail_jurnal.

> **Penting** ⚠️ Jangan import file `database.sql` di folder utama. File itu berisi perintah
> yang diblokir hosting gratis, hasilnya "access denied". Yang di-import selalu
> `deploy/infinityfree/database.sql`.

### Catatan nilai Bagian A (isi Notepad)

| Dari panel | Contoh isi |
|---|---|
| MySQL Host Name | `sql302.infinityfree.com` |
| MySQL Database Name | `if0_12345678_majujaya` |
| MySQL User Name | `if0_12345678` |
| Password (FTP / MySQL) | `password-akun-hostingmu` |
| FTP Hostname | `ftpupload.net` |
| FTP Username | `if0_12345678` (biasanya sama dengan MySQL User Name) |
| FTP Password | `password-akun-hostingmu` |

---

## Bagian B — Isi 7 rahasia (Secrets) di GitHub

1. Buka repo project ini di GitHub, klik tab **Settings** (menu atas).
2. Di sidebar kiri klik **Secrets and variables** → **Actions**.
3. Klik tombol hijau **New repository secret**.
4. Isi kolom **Name** dan **Secret** sesuai tabel di bawah, klik **Add secret**.
   Ulangi langkah 3–4 sampai ketujuh baris selesai.

| Name (tulis persis) | Secret (isi dengan apa) |
|---|---|
| `DB_HOST` | MySQL Host Name, contoh `sql302.infinityfree.com` |
| `DB_NAME` | MySQL Database Name, contoh `if0_12345678_majujaya` |
| `DB_USER` | MySQL User Name, contoh `if0_12345678` |
| `DB_PASS` | Password hosting (yang sama dengan FTP Password) |
| `FTP_SERVER` | FTP Hostname dari FTP Details, contoh `ftpupload.net` |
| `FTP_USERNAME` | FTP Username, biasanya sama dengan MySQL User Name (`if0_12345678`) |
| `FTP_PASSWORD` | FTP Password |

Nama-namanya harus sama persis huruf besar/kecilnya. Kalau salah ketik, workflow gagal.

5. Buka tab **Actions**.
6. Kalau muncul banner tentang workflow yang belum aktif, klik tombol hijau
   **I understand my workflows, go ahead and enable them**.
7. Di sidebar kiri klik **Deploy ke InfinityFree**, lalu tombol **Run workflow** di kanan →
   **Run workflow** lagi untuk memulai.
8. Tunggu sebentar, klik nama workflow yang berjalan. Tanda centang hijau = berhasil.
   Tanda silang merah = ada yang salah, lihat Bagian D.

---

## Bagian C — Kirim kode dari komputermu

Buka **Git Bash** di komputermu, lalu jalankan perintah ini satu per satu:

```bash
cd /e/Code/akuncihuy/akuncihuy
git remote add origin https://github.com/<USERNAME>/<REPO>.git
git push -u origin main
```

Ganti `<USERNAME>` dengan nama akun GitHub-mu dan `<REPO>` dengan nama repo. Contoh:
`https://github.com/karelriyan/akuncihuy.git`.

- Kalau `git remote add` menjawab **"remote origin already exists"**, lewati saja baris itu
  (berarti remote-nya sudah ada) dan lanjut ke `git push`.
- Saat push pertama, akan terbuka jendela browser GitHub → klik **Sign in with your browser** →
  login → klik **Authorize** → kembali ke Git Bash dan prosesnya lanjut sendiri.
- Setelah ini, setiap kali kamu `git push`, aplikasi otomatis ter-upload ke hosting.
- Alamat situsmu: `http://<subdomain-pilihanmu>` (contoh `http://majujaya.free.nf`).

---

## Bagian D — Kalau ada yang gagal, cek ini

| Yang kamu lihat | Penyebab | Yang harus dilakukan |
|---|---|---|
| Halaman web bertuliskan **"Database connection failed ... Details: ..."** | Ada secret `DB_*` yang salah (nama database, user, host, atau password) | Buka Settings → Secrets and variables → Actions, klik secret yang salah → **Update secret**, isi ulang sesuai halaman MySQL Databases di panel, lalu jalankan ulang workflow dari tab Actions |
| Workflow gagal di langkah **Upload ke htdocs** dengan pesan **421** atau **too many connections** | Hosting membatasi jumlah koneksi FTP bersamaan | Buka tab Actions → workflow yang gagal → **Re-run all jobs**. Biasanya berhasil di percobaan kedua |
| Workflow gagal dengan **"530 Login incorrect"** | `FTP_USERNAME` / `FTP_PASSWORD` / `FTP_SERVER` salah | Perbaiki secret FTP-nya (lihat Bagian B), lalu jalankan ulang workflow |
| Import phpMyAdmin gagal dengan **"access denied"** atau **"CREATE VIEW command denied"** | Kamu meng-import file `database.sql` di folder utama, bukan versi hosting | Ulangi Bagian A langkah 10–11 dengan file `deploy/infinityfree/database.sql` |
| Import berhasil tapi tabelnya **cuma sebagian** | Import terpotong / file salah | Hapus tabel yang ada (centang semua → Drop), lalu import ulang file `deploy/infinityfree/database.sql` |
| Halaman web tampil **"This site can't provide a secure connection"** atau peringatan "not secure" | SSL belum aktif | Di vPanel, buka menu **SSL Certificates** → **Add SSL Certificate** → pilih penyedia sertifikat gratis → ikuti instruksinya (biasanya perlu menambah record CNAME di menu DNS). Tunggu sampai statusnya aktif, lalu buka lagi dengan `https://` |
| Situs tiba-tiba tidak bisa dibuka sama sekali | Kuota harian habis (hits/hari) atau akun disuspend karena lama tidak ada pengunjung | Tunggu ~24 jam; kalau karena inactivity, buka Client Area dan aktifkan kembali akunnya |

Hal yang wajar di hosting gratis ini: tidak ada SSH, file wajib ada di folder `htdocs`,
dan ukuran file HTML/PHP/JS dibatasi 1 MB. Aplikasi ini jauh di bawah batas itu.

---

## Setelah berhasil

1. Buka alamat situsmu, pastikan dashboard aplikasi muncul dan tidak ada pesan error.
2. Buka satu menu, misalnya **Chart of Accounts** — kalau datanya muncul, berarti database
   dan aplikasinya sudah tersambung dengan benar.
3. Lain kali kamu cukup mengubah file di komputer → `git add -A` → `git commit -m "..."` →
   `git push`. Hosting ikut ter-update otomatis dalam beberapa detik.
