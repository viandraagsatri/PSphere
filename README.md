# 🎮 PSphere
PSphere merupakan sistem manajemen rental PlayStation yang dibangun menggunakan PHP dan MySQL. Sistem ini digunakan untuk mengelola data pelanggan, mesin PS, booking, transaksi, serta laporan aktivitas rental. Proyek ini menerapkan berbagai materi basis data seperti View, SQL Join, Stored Procedure, Function, Transaction, Union All, Trigger, Fragmentasi serta Backup Database dan Task Scheduler.


# 📌 Detail Konsep
**View, SQL Join, Union All**

Pada proyek PSphere, SQL JOIN digunakan untuk menggabungkan data pelanggan, booking, transaksi, dan mesin PS. Hasilnya kemudian disatukan menggunakan UNION ALL untuk menampilkan aktivitas booking dan transaksi dalam satu laporan, lalu disimpan dalam VIEW v_laporan_aktivitas agar pembuatan laporan menjadi lebih mudah dan efisien.
```sql
CREATE VIEW v_laporan_aktivitas AS

SELECT
    b.id_booking AS id_data,
    p.nama_pelanggan,
    ps.nama_ps,
    b.jam_mulai AS tanggal,
    'BOOKING' AS aktivitas,
    b.status_booking AS status
FROM booking b
JOIN pelanggan p ON b.id_pelanggan = p.id_pelanggan
JOIN ps_unit ps ON b.id_ps = ps.id_ps

UNION ALL

SELECT
    t.id_transaksi AS id_data,
    p.nama_pelanggan,
    ps.nama_ps,
    t.tanggal_transaksi AS tanggal,
    'TRANSAKSI' AS aktivitas,
    t.status_pembayaran AS status
FROM transaksi t
JOIN booking b ON t.id_booking = b.id_booking
JOIN pelanggan p ON b.id_pelanggan = p.id_pelanggan
JOIN ps_unit ps ON b.id_ps = ps.id_ps;
```
**Stored Procedure**


**Function**


**Trigger**

`trg_update_status_ps`: Trigger digunakan untuk menjaga konsistensi status mesin PS.

Ketika booking selesai dan pembayaran lunas:
```sql
UPDATE ps_unit
SET status_ps = 'tersedia'
```

Ketika booking aktif:
```sql
UPDATE ps_unit
SET status_ps = 'dipakai'
```
Dengan demikian status mesin akan berubah otomatis sesuai kondisi transaksi.

**Fragmentasi**

Pada proyek PSphere, fragmentasi horizontal diterapkan pada tabel transaksi berdasarkan metode pembayaran. Tujuannya adalah memisahkan data transaksi ke dalam kelompok yang lebih spesifik sehingga memudahkan proses pencarian, pelaporan, dan analisis data.

Implementasi fragmentasi dilakukan menggunakan View sebagai berikut:
```sql
CREATE VIEW transaksi_cash AS
SELECT *
FROM transaksi
WHERE metode_bayar = 'cash';

CREATE VIEW transaksi_transfer AS
SELECT *
FROM transaksi
WHERE metode_bayar = 'transfer';

CREATE VIEW transaksi_qris AS
SELECT *
FROM transaksi
WHERE metode_bayar = 'qris';
```

**Backup Database**


**Task Scheduler**
