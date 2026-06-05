# 🎮 PSphere
PSphere merupakan sistem manajemen rental PlayStation yang dibangun menggunakan PHP dan MySQL. Sistem ini digunakan untuk mengelola data pelanggan, mesin PS, booking, transaksi, serta laporan aktivitas rental. Proyek ini menerapkan berbagai materi basis data seperti View, SQL Join, Stored Procedure, Function, Transaction, Union All, Trigger, Fragmentasi serta Backup Database dan Task Scheduler.
<img width="1901" height="1090" alt="image" src="https://github.com/user-attachments/assets/463f60a1-f523-44ec-94cf-41919566135f" />


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

Stored Procedure digunakan untuk membungkus operasi CRUD (Create, Read, Update, Delete) pada tabel pelanggan agar eksekusi query menjadi lebih cepat, aman, dan modular.

1. `sp_insert_pelanggan`: Menambahkan data pelanggan baru beserta nomor HP dan alamat.
<img width="1900" height="711" alt="image" src="https://github.com/user-attachments/assets/c65a889f-bed1-410b-a85a-7c28c8377587" />

```sql
CREATE PROCEDURE sp_insert_pelanggan (
    IN p_nama VARCHAR(100), 
    IN p_nohp VARCHAR(20),
    IN p_alamat TEXT
)   
BEGIN
    INSERT INTO pelanggan(nama_pelanggan, no_hp, alamat)
    VALUES(p_nama, p_nohp, p_alamat);
END;

```

2. `sp_select_pelanggan`: Menampilkan seluruh data pelanggan yang terdaftar.
<img width="1861" height="682" alt="image" src="https://github.com/user-attachments/assets/04d692d1-b213-4eab-b449-60d79bd3ff38" />

```sql
CREATE PROCEDURE sp_select_pelanggan ()   
BEGIN
    SELECT * FROM pelanggan;
END;

```

3. `sp_update_pelanggan`: Memperbarui informasi nama, nomor HP, dan alamat pelanggan berdasarkan ID.
<img width="1512" height="553" alt="image" src="https://github.com/user-attachments/assets/e349c5ce-4649-49a7-b430-9d5b845f6763" />

```sql
CREATE PROCEDURE sp_update_pelanggan (
    IN p_id INT, 
    IN p_nama VARCHAR(100), 
    IN p_nohp VARCHAR(20),
    IN p_alamat TEXT
)   
BEGIN
    UPDATE pelanggan
    SET nama_pelanggan = p_nama,
        no_hp = p_nohp,
        alamat = p_alamat
    WHERE id_pelanggan = p_id;
END;

```

4. `sp_delete_pelanggan`: Menghapus data pelanggan tertentu berdasarkan ID.
<img width="1533" height="666" alt="image" src="https://github.com/user-attachments/assets/7f00f768-f505-42aa-8cd5-dd1b3d4bc35f" />

```sql
CREATE PROCEDURE sp_delete_pelanggan (IN p_id INT)   
BEGIN
    DELETE FROM pelanggan
    WHERE id_pelanggan = p_id;
END;

```

**Function**

`fn_total_bayar`: Ditujukan untuk menghitung total biaya sewa secara dinamis berdasarkan tarif harga per jam dari unit PS yang dipilih dan durasi (total jam) penyewaan.

```sql
CREATE FUNCTION fn_total_bayar (
    p_harga DECIMAL(10,2), 
    p_jam INT
) RETURNS DECIMAL(10,2)
DETERMINISTIC
BEGIN
    RETURN p_harga * p_jam;
END;

```

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
