CREATE DATABASE poliklinik_ghina;
USE poliklinik_ghina;

-- Tabel pasien
CREATE TABLE pasien (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(255),
    alamat VARCHAR(255),
    no_ktp VARCHAR(25),
    no_hp VARCHAR(50),
    no_rm CHAR(10)
);

-- Tabel dokter
CREATE TABLE dokter (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(150),
    alamat VARCHAR(255),
    no_hp VARCHAR(50),
    id_poli INT
);

-- Tabel poli
CREATE TABLE poli (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nama_poli VARCHAR(25),
    keterangan TEXT
);

-- Tabel jadwal_periksa
CREATE TABLE jadwal_periksa (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_dokter INT,
    hari ENUM('Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'),
    jam_mulai TIME,
    jam_selesai TIME
);

-- Tabel daftar_poli
CREATE TABLE daftar_poli (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_pasien INT,
    id_jadwal INT,
    keluhan TEXT,
    no_antrian INT
);

-- Tabel periksa
CREATE TABLE periksa (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_daftar_poli INT,
    tgl_periksa DATE,
    catatan TEXT,
    biaya_periksa INT
);

-- Tabel obat
CREATE TABLE obat (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nama_obat VARCHAR(50),
    kemasan VARCHAR(35),
    harga INT
);

-- Tabel detail_periksa
CREATE TABLE detail_periksa (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_periksa INT,
    id_obat INT
);
