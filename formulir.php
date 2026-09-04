<?php

require_once __DIR__ . '/config.php';

/*
|--------------------------------------------------------------------------
| AUTH
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$userId = (int) $_SESSION['user_id'];

/*
|--------------------------------------------------------------------------
| HELPER
|--------------------------------------------------------------------------
*/

function clean($value): string
{
    return trim((string) $value);
}

function h($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function jsonResponse(
    bool $success,
    string $message = '',
    array $extra = []
): void {
    header('Content-Type: application/json; charset=utf-8');

    echo json_encode(
        array_merge([
            'success' => $success,
            'message' => $message
        ], $extra),
        JSON_UNESCAPED_UNICODE
    );

    exit;
}

function validNik(string $nik): bool
{
    return preg_match('/^[0-9]{16}$/', $nik) === 1;
}

function validNisn(string $nisn): bool
{
    return preg_match('/^[0-9]{10}$/', $nisn) === 1;
}

function validPhone(string $phone): bool
{
    $phone = preg_replace('/[^0-9]/', '', $phone);

    return strlen($phone) >= 10 &&
           strlen($phone) <= 15;
}

function isLocked(string $status): bool
{
    return in_array($status, [
        'menunggu_verifikasi',
        'terverifikasi',
        'lulus',
        'tidak_lulus',
        'daftar_ulang'
    ], true);
}

/*
|--------------------------------------------------------------------------
| SEKOLAH
|--------------------------------------------------------------------------
*/

$schoolName = 'SMP Al-Khairiyah';

$schoolLat = -6.0122653;
$schoolLng = 106.0574654;

$schoolMapsUrl =
    'https://www.google.com/maps?q=' .
    $schoolLat . ',' .
    $schoolLng;

/*
|--------------------------------------------------------------------------
| DOKUMEN BERDASARKAN JALUR
|--------------------------------------------------------------------------
*/

$documentRules = [

    'domisili' => [

        'kk' => [
            'label' => 'Kartu Keluarga (KK)',
            'required' => true
        ],

        'akta' => [
            'label' => 'Akta Kelahiran',
            'required' => true
        ],

        'ijazah_skl' => [
            'label' => 'Ijazah / Surat Keterangan Lulus',
            'required' => true
        ],

        'identitas_siswa' => [
            'label' => 'Identitas Siswa',
            'required' => true
        ]

    ],

    'afirmasi' => [

        'kk' => [
            'label' => 'Kartu Keluarga (KK)',
            'required' => true
        ],

        'akta' => [
            'label' => 'Akta Kelahiran',
            'required' => true
        ],

        'ijazah_skl' => [
            'label' => 'Ijazah / Surat Keterangan Lulus',
            'required' => true
        ],

        'dokumen_afirmasi' => [
            'label' => 'Dokumen Bukti Afirmasi',
            'required' => true
        ]

    ],

    'prestasi' => [

        'kk' => [
            'label' => 'Kartu Keluarga (KK)',
            'required' => true
        ],

        'akta' => [
            'label' => 'Akta Kelahiran',
            'required' => true
        ],

        'ijazah_skl' => [
            'label' => 'Ijazah / Surat Keterangan Lulus',
            'required' => true
        ],

        'sertifikat_prestasi' => [
            'label' => 'Sertifikat / Bukti Prestasi',
            'required' => true
        ],

        'rapor' => [
            'label' => 'Rapor',
            'required' => false
        ]

    ],

    'mutasi' => [

        'kk' => [
            'label' => 'Kartu Keluarga (KK)',
            'required' => true
        ],

        'akta' => [
            'label' => 'Akta Kelahiran',
            'required' => true
        ],

        'ijazah_skl' => [
            'label' => 'Ijazah / Surat Keterangan Lulus',
            'required' => true
        ],

        'surat_penugasan' => [
            'label' => 'Surat Penugasan Orang Tua',
            'required' => true
        ],

        'dokumen_mutasi' => [
            'label' => 'Dokumen Pendukung Mutasi',
            'required' => false
        ]

    ]

];

/*
|--------------------------------------------------------------------------
| INFORMASI DAFTAR ULANG
|--------------------------------------------------------------------------
|
| Bagian ini hanya digunakan ketika status_pendaftaran = lulus.
|
*/

$jalurLabels = [

    'domisili' => 'Domisili',

    'afirmasi' => 'Afirmasi',

    'prestasi' => 'Prestasi',

    'mutasi' => 'Mutasi'

];

/*
|--------------------------------------------------------------------------
| BERKAS WAJIB SEMUA JALUR
|--------------------------------------------------------------------------
*/

$commonDaftarUlang = [

    [
        'title' => 'Bukti kelulusan / diterima SPMB',
        'desc' => 'Cetak bukti hasil seleksi atau kartu peserta.'
    ],

    [
        'title' => 'Kartu Keluarga (KK)',
        'desc' => 'Siapkan fotokopi dan dokumen asli untuk verifikasi.'
    ],

    [
        'title' => 'Akta Kelahiran',
        'desc' => 'Siapkan fotokopi dan dokumen asli.'
    ],

    [
        'title' => 'Ijazah SD/MI atau SKL',
        'desc' => 'Jika ijazah sudah terbit, bawa ijazah. Jika belum, siapkan Surat Keterangan Lulus (SKL).'
    ],

    [
        'title' => 'KTP orang tua/wali',
        'desc' => 'Siapkan fotokopi KTP orang tua atau wali.'
    ],

    [
        'title' => 'Pas foto',
        'desc' => 'Biasanya ukuran 3×4 atau 4×6, mengikuti ketentuan sekolah.'
    ],

    [
        'title' => 'Formulir daftar ulang',
        'desc' => 'Dicetak dari sistem atau disediakan oleh sekolah.'
    ]

];

/*
|--------------------------------------------------------------------------
| BERKAS TAMBAHAN PER JALUR
|--------------------------------------------------------------------------
*/

$postLulusFlow = [

    'domisili' => [

        'icon' => '🏠',

        'title' => 'Jalur Domisili',

        'description' =>
            'Siapkan dokumen yang berkaitan dengan domisili tempat tinggal yang digunakan pada proses pendaftaran.',

        'items' => [

            [
                'title' => 'KK sesuai ketentuan domisili',
                'desc' => 'Bawa KK asli dan fotokopi untuk proses verifikasi.'
            ],

            [
                'title' => 'Dokumen pendukung domisili',
                'desc' => 'Disiapkan apabila diminta oleh pihak sekolah.'
            ],

            [
                'title' => 'Bukti alamat tempat tinggal',
                'desc' => 'Disiapkan apabila diperlukan dalam proses verifikasi.'
            ]

        ],

        'note' =>
            'Jangan otomatis meminta surat domisili apabila KK sudah menjadi dasar utama verifikasi, karena ketentuan dapat berbeda menurut daerah.'
    ],

    'afirmasi' => [

        'icon' => '❤️',

        'title' => 'Jalur Afirmasi',

        'description' =>
            'Siapkan bukti bahwa calon peserta didik memenuhi kriteria afirmasi yang digunakan pada proses seleksi.',

        'items' => [

            [
                'title' => 'KIP',
                'desc' => 'Bawa kartu atau dokumen asli dan fotokopi jika digunakan sebagai bukti afirmasi.'
            ],

            [
                'title' => 'Kartu Indonesia Sehat / PBI',
                'desc' => 'Disiapkan apabila termasuk kriteria yang digunakan oleh daerah.'
            ],

            [
                'title' => 'Kartu atau program bantuan sosial',
                'desc' => 'Bawa kartu atau dokumen program bantuan sosial lain yang ditetapkan pemerintah daerah.'
            ],

            [
                'title' => 'Dokumen pendukung keluarga tidak mampu',
                'desc' => 'Disiapkan apabila dipersyaratkan oleh sekolah atau pemerintah daerah.'
            ]

        ],

        'note' =>
            'Bukti afirmasi yang dibawa sebaiknya sama dengan dokumen yang digunakan ketika proses pendaftaran.'
    ],

    'prestasi' => [

        'icon' => '🏆',

        'title' => 'Jalur Prestasi',

        'description' =>
            'Siapkan seluruh bukti prestasi yang digunakan sebagai dasar proses seleksi.',

        'items' => [

            [
                'title' => 'Sertifikat / piagam prestasi asli',
                'desc' => 'Bawa dokumen asli untuk pemeriksaan atau verifikasi.'
            ],

            [
                'title' => 'Fotokopi sertifikat / piagam',
                'desc' => 'Siapkan salinan sertifikat atau piagam untuk diserahkan apabila diperlukan.'
            ],

            [
                'title' => 'Surat keterangan / pengesahan',
                'desc' => 'Disiapkan dari pihak yang berwenang apabila dipersyaratkan.'
            ],

            [
                'title' => 'Dokumen nilai / prestasi akademik',
                'desc' => 'Siapkan apabila jalur prestasi menggunakan prestasi akademik sebagai dasar seleksi.'
            ]

        ],

        'note' =>
            'Untuk prestasi olahraga, seni, atau bidang lainnya, bawa sertifikat atau bukti kejuaraan yang digunakan ketika pendaftaran.'
    ],

    'mutasi' => [

        'icon' => '🚚',

        'title' => 'Jalur Mutasi',

        'description' =>
            'Siapkan dokumen perpindahan tugas orang tua/wali dan dokumen keluarga untuk proses verifikasi.',

        'items' => [

            [
                'title' => 'Surat penugasan orang tua/wali',
                'desc' => 'Bawa surat penugasan dari instansi atau perusahaan.'
            ],

            [
                'title' => 'Surat keterangan pindah tugas / mutasi',
                'desc' => 'Siapkan dokumen yang menerangkan perpindahan tugas orang tua/wali.'
            ],

            [
                'title' => 'Kartu Keluarga (KK)',
                'desc' => 'Bawa KK asli dan fotokopi.'
            ],

            [
                'title' => 'Akta Kelahiran',
                'desc' => 'Bawa akta kelahiran asli dan fotokopi.'
            ],

            [
                'title' => 'Ijazah / SKL',
                'desc' => 'Bawa ijazah atau Surat Keterangan Lulus.'
            ],

            [
                'title' => 'Dokumen perpindahan sekolah',
                'desc' => 'Disiapkan apabila calon peserta didik sebelumnya sudah bersekolah di tempat lain.'
            ]

        ],

        'note' =>
            'Dokumen mutasi harus sesuai dengan alasan perpindahan yang digunakan pada proses pendaftaran.'
    ]

];

/*
|--------------------------------------------------------------------------
| PEKERJAAN
|--------------------------------------------------------------------------
*/

$jobOptions = [

    'Tidak Bekerja',
    'PNS',
    'PPPK',
    'TNI',
    'POLRI',
    'Guru',
    'Dosen',
    'Tenaga Kesehatan',
    'Karyawan Swasta',
    'Wiraswasta',
    'Pedagang',
    'Petani',
    'Nelayan',
    'Buruh',
    'Sopir',
    'Peternak',
    'Pekerja Harian Lepas',
    'Perangkat Desa',
    'BUMN/BUMD',
    'Pensiunan',
    'Lainnya'

];

/*
|--------------------------------------------------------------------------
| PENGHASILAN
|--------------------------------------------------------------------------
*/

$incomeOptions = [

    'Tidak Berpenghasilan',
    'Kurang dari Rp500.000',
    'Rp500.000 - Rp1.000.000',
    'Rp1.000.001 - Rp2.000.000',
    'Rp2.000.001 - Rp3.000.000',
    'Rp3.000.001 - Rp4.000.000',
    'Rp4.000.001 - Rp5.000.000',
    'Rp5.000.001 - Rp7.500.000',
    'Rp7.500.001 - Rp10.000.000',
    'Lebih dari Rp10.000.000'

];

/*
|--------------------------------------------------------------------------
| AMBIL USER
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT *
    FROM users
    WHERE id = ?
    LIMIT 1
");

$stmt->execute([$userId]);

$user = $stmt->fetch();

if (!$user) {

    session_destroy();

    header('Location: login.php');
    exit;
}

/*
|--------------------------------------------------------------------------
| AMBIL PENDAFTARAN
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT *
    FROM pendaftaran
    WHERE user_id = ?
    LIMIT 1
");

$stmt->execute([$userId]);

$registration = $stmt->fetch();

if (!$registration) {

    $stmt = $pdo->prepare("
        INSERT INTO pendaftaran (
            user_id,
            status_pendaftaran
        )
        VALUES (?, 'belum_lengkap')
    ");

    $stmt->execute([$userId]);

    $registration = [

        'id' => $pdo->lastInsertId(),

        'user_id' => $userId,

        'status_pendaftaran' => 'belum_lengkap'

    ];
}

/*
|--------------------------------------------------------------------------
| AMBIL ORANG TUA
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT *
    FROM orang_tua
    WHERE user_id = ?
    LIMIT 1
");

$stmt->execute([$userId]);

$parents = $stmt->fetch();

if (!$parents) {

    $stmt = $pdo->prepare("
        INSERT INTO orang_tua (user_id)
        VALUES (?)
    ");

    $stmt->execute([$userId]);

    $parents = [
        'user_id' => $userId
    ];
}

/*
|--------------------------------------------------------------------------
| AMBIL DOKUMEN
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT *
    FROM dokumen
    WHERE user_id = ?
    ORDER BY id ASC
");

$stmt->execute([$userId]);

$documents = [];

foreach ($stmt->fetchAll() as $doc) {

    $documents[$doc['jenis_dokumen']] = $doc;
}

/*
|--------------------------------------------------------------------------
| STATUS
|--------------------------------------------------------------------------
*/

$status =
    $registration['status_pendaftaran']
    ?? 'belum_lengkap';

$locked = isLocked($status);

$isLulus =
    $status === 'lulus';

/*
|--------------------------------------------------------------------------
| DATA JALUR UNTUK HALAMAN LULUS
|--------------------------------------------------------------------------
*/

$selectedJalur =
    $registration['jalur']
    ?? '';

$selectedJalurLabel =
    $jalurLabels[$selectedJalur]
    ?? 'Belum tercatat';

$selectedPathInfo =
    $postLulusFlow[$selectedJalur]
    ?? null;

/*
|--------------------------------------------------------------------------
| AJAX
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $action = $_POST['action'] ?? '';

    /*
    |--------------------------------------------------------------------------
    | SAVE DATA SISWA
    |--------------------------------------------------------------------------
    */

    if ($action === 'save_siswa') {

        if ($locked) {

            jsonResponse(
                false,
                'Pendaftaran sudah dikirim dan tidak dapat diedit.'
            );
        }

        $jenisKelamin =
            clean($_POST['jenis_kelamin'] ?? '');

        $tempatLahir =
            clean($_POST['tempat_lahir'] ?? '');

        $tanggalLahir =
            clean($_POST['tanggal_lahir'] ?? '');

        $agama =
            clean($_POST['agama'] ?? '');

        $noKk =
            preg_replace(
                '/[^0-9]/',
                '',
                $_POST['no_kk'] ?? ''
            );

        $alamat =
            clean($_POST['alamat'] ?? '');

        $rt =
            clean($_POST['rt'] ?? '');

        $rw =
            clean($_POST['rw'] ?? '');

        $desa =
            clean($_POST['desa_kelurahan'] ?? '');

        $kecamatan =
            clean($_POST['kecamatan'] ?? '');

        $kabupaten =
            clean($_POST['kabupaten_kota'] ?? '');

        $provinsi =
            clean($_POST['provinsi'] ?? '');

        $asalSekolah =
            clean($_POST['asal_sekolah'] ?? '');

        $npsn =
            clean($_POST['npsn_sekolah_asal'] ?? '');

        if (
            !in_array(
                $jenisKelamin,
                ['Laki-laki', 'Perempuan'],
                true
            )
        ) {

            jsonResponse(
                false,
                'Jenis kelamin belum dipilih.'
            );
        }

        if ($tempatLahir === '') {

            jsonResponse(
                false,
                'Tempat lahir wajib diisi.'
            );
        }

        if ($tanggalLahir === '') {

            jsonResponse(
                false,
                'Tanggal lahir wajib diisi.'
            );
        }

        if ($agama === '') {

            jsonResponse(
                false,
                'Agama wajib dipilih.'
            );
        }

        if (
            !preg_match(
                '/^[0-9]{16}$/',
                $noKk
            )
        ) {

            jsonResponse(
                false,
                'Nomor KK harus 16 digit.'
            );
        }

        if ($alamat === '') {

            jsonResponse(
                false,
                'Alamat wajib diisi.'
            );
        }

        if ($desa === '') {

            jsonResponse(
                false,
                'Desa/Kelurahan wajib diisi.'
            );
        }

        if ($kecamatan === '') {

            jsonResponse(
                false,
                'Kecamatan wajib diisi.'
            );
        }

        if ($kabupaten === '') {

            jsonResponse(
                false,
                'Kabupaten/Kota wajib diisi.'
            );
        }

        if ($provinsi === '') {

            jsonResponse(
                false,
                'Provinsi wajib diisi.'
            );
        }

        if ($asalSekolah === '') {

            jsonResponse(
                false,
                'Asal sekolah wajib diisi.'
            );
        }

        try {

            $stmt = $pdo->prepare("
                UPDATE pendaftaran SET

                    jenis_kelamin = ?,
                    tempat_lahir = ?,
                    tanggal_lahir = ?,
                    agama = ?,
                    no_kk = ?,
                    alamat = ?,
                    rt = ?,
                    rw = ?,
                    desa_kelurahan = ?,
                    kecamatan = ?,
                    kabupaten_kota = ?,
                    provinsi = ?,
                    asal_sekolah = ?,
                    npsn_sekolah_asal = ?,
                    status_pendaftaran = 'draft',
                    updated_at = NOW()

                WHERE user_id = ?
            ");

            $stmt->execute([

                $jenisKelamin,
                $tempatLahir,
                $tanggalLahir,
                $agama,
                $noKk,
                $alamat,
                $rt,
                $rw,
                $desa,
                $kecamatan,
                $kabupaten,
                $provinsi,
                $asalSekolah,
                $npsn,
                $userId

            ]);

            jsonResponse(
                true,
                'Data siswa berhasil disimpan.'
            );

        } catch (Throwable $e) {

            jsonResponse(
                false,
                'Gagal menyimpan data siswa.'
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | SAVE ORANG TUA
    |--------------------------------------------------------------------------
    */

    if ($action === 'save_orangtua') {

        if ($locked) {

            jsonResponse(
                false,
                'Pendaftaran sudah dikirim dan tidak dapat diedit.'
            );
        }

        $ayahNama =
            clean($_POST['ayah_nama'] ?? '');

        $ayahNik =
            preg_replace(
                '/[^0-9]/',
                '',
                $_POST['ayah_nik'] ?? ''
            );

        $ayahPekerjaan =
            clean($_POST['ayah_pekerjaan'] ?? '');

        $ayahPenghasilan =
            clean($_POST['ayah_penghasilan'] ?? '');

        $ayahNoHp =
            preg_replace(
                '/[^0-9]/',
                '',
                $_POST['ayah_no_hp'] ?? ''
            );

        $ibuNama =
            clean($_POST['ibu_nama'] ?? '');

        $ibuNik =
            preg_replace(
                '/[^0-9]/',
                '',
                $_POST['ibu_nik'] ?? ''
            );

        $ibuPekerjaan =
            clean($_POST['ibu_pekerjaan'] ?? '');

        $ibuPenghasilan =
            clean($_POST['ibu_penghasilan'] ?? '');

        $ibuNoHp =
            preg_replace(
                '/[^0-9]/',
                '',
                $_POST['ibu_no_hp'] ?? ''
            );

        $waliNama =
            clean($_POST['wali_nama'] ?? '');

        $waliNik =
            preg_replace(
                '/[^0-9]/',
                '',
                $_POST['wali_nik'] ?? ''
            );

        $waliPekerjaan =
            clean($_POST['wali_pekerjaan'] ?? '');

        $waliPenghasilan =
            clean($_POST['wali_penghasilan'] ?? '');

        $waliNoHp =
            preg_replace(
                '/[^0-9]/',
                '',
                $_POST['wali_no_hp'] ?? ''
            );

        if ($ayahNama === '') {

            jsonResponse(
                false,
                'Nama Ayah wajib diisi.'
            );
        }

        if (!validNik($ayahNik)) {

            jsonResponse(
                false,
                'NIK Ayah harus 16 digit.'
            );
        }

        if ($ayahPekerjaan === '') {

            jsonResponse(
                false,
                'Pekerjaan Ayah wajib dipilih.'
            );
        }

        if ($ayahPenghasilan === '') {

            jsonResponse(
                false,
                'Penghasilan Ayah wajib dipilih.'
            );
        }

        if (
            $ayahNoHp !== '' &&
            !validPhone($ayahNoHp)
        ) {

            jsonResponse(
                false,
                'Nomor HP Ayah tidak valid.'
            );
        }

        if ($ibuNama === '') {

            jsonResponse(
                false,
                'Nama Ibu wajib diisi.'
            );
        }

        if (!validNik($ibuNik)) {

            jsonResponse(
                false,
                'NIK Ibu harus 16 digit.'
            );
        }

        if ($ibuPekerjaan === '') {

            jsonResponse(
                false,
                'Pekerjaan Ibu wajib dipilih.'
            );
        }

        if ($ibuPenghasilan === '') {

            jsonResponse(
                false,
                'Penghasilan Ibu wajib dipilih.'
            );
        }

        if (
            $ibuNoHp !== '' &&
            !validPhone($ibuNoHp)
        ) {

            jsonResponse(
                false,
                'Nomor HP Ibu tidak valid.'
            );
        }

        if (
            $waliNik !== '' &&
            !validNik($waliNik)
        ) {

            jsonResponse(
                false,
                'NIK Wali harus 16 digit jika diisi.'
            );
        }

        if (
            $waliNoHp !== '' &&
            !validPhone($waliNoHp)
        ) {

            jsonResponse(
                false,
                'Nomor HP Wali tidak valid jika diisi.'
            );
        }

        if ($waliNama === '') {

            $waliNik = '';
            $waliPekerjaan = '';
            $waliPenghasilan = '';
            $waliNoHp = '';
        }

        try {

            $stmt = $pdo->prepare("
                UPDATE orang_tua SET

                    ayah_nama = ?,
                    ayah_nik = ?,
                    ayah_pekerjaan = ?,
                    ayah_penghasilan = ?,
                    ayah_no_hp = ?,

                    ibu_nama = ?,
                    ibu_nik = ?,
                    ibu_pekerjaan = ?,
                    ibu_penghasilan = ?,
                    ibu_no_hp = ?,

                    wali_nama = ?,
                    wali_nik = ?,
                    wali_pekerjaan = ?,
                    wali_penghasilan = ?,
                    wali_no_hp = ?,

                    updated_at = NOW()

                WHERE user_id = ?
            ");

            $stmt->execute([

                $ayahNama,
                $ayahNik,
                $ayahPekerjaan,
                $ayahPenghasilan,
                $ayahNoHp,

                $ibuNama,
                $ibuNik,
                $ibuPekerjaan,
                $ibuPenghasilan,
                $ibuNoHp,

                $waliNama,
                $waliNik,
                $waliPekerjaan,
                $waliPenghasilan,
                $waliNoHp,

                $userId

            ]);

            jsonResponse(
                true,
                'Data orang tua berhasil disimpan.'
            );

        } catch (Throwable $e) {

            jsonResponse(
                false,
                'Gagal menyimpan data orang tua.'
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | SAVE JALUR
    |--------------------------------------------------------------------------
    */

    if ($action === 'save_jalur') {

        if ($locked) {

            jsonResponse(
                false,
                'Pendaftaran sudah dikirim dan tidak dapat diedit.'
            );
        }

        $jalur =
            clean($_POST['jalur'] ?? '');

        $jarak =
            clean($_POST['jarak_domisili_km'] ?? '');

        $latitudeRumah =
            clean($_POST['latitude_rumah'] ?? '');

        $longitudeRumah =
            clean($_POST['longitude_rumah'] ?? '');

        if (!array_key_exists(
            $jalur,
            $documentRules
        )) {

            jsonResponse(
                false,
                'Jalur pendaftaran tidak valid.'
            );
        }

        if ($jalur === 'domisili') {

            if (
                $latitudeRumah === '' ||
                $longitudeRumah === ''
            ) {

                jsonResponse(
                    false,
                    'Silakan klik lokasi rumah pada peta.'
                );
            }

            if (
                !is_numeric($latitudeRumah) ||
                !is_numeric($longitudeRumah)
            ) {

                jsonResponse(
                    false,
                    'Koordinat rumah tidak valid.'
                );
            }

            $latitudeRumah =
                (float) $latitudeRumah;

            $longitudeRumah =
                (float) $longitudeRumah;

            if (
                $latitudeRumah < -90 ||
                $latitudeRumah > 90 ||
                $longitudeRumah < -180 ||
                $longitudeRumah > 180
            ) {

                jsonResponse(
                    false,
                    'Koordinat rumah berada di luar batas.'
                );
            }

            if (
                $jarak === '' ||
                !is_numeric($jarak)
            ) {

                jsonResponse(
                    false,
                    'Jarak rumah ke sekolah belum berhasil dihitung.'
                );
            }

            $jarakFloat =
                round(
                    (float) $jarak,
                    2
                );

            if (
                $jarakFloat <= 0 ||
                $jarakFloat > 999.99
            ) {

                jsonResponse(
                    false,
                    'Jarak domisili tidak valid.'
                );
            }

        } else {

            $jarakFloat = null;
            $latitudeRumah = null;
            $longitudeRumah = null;
        }

        try {

            $stmt = $pdo->prepare("
                UPDATE pendaftaran SET

                    jalur = ?,
                    jarak_domisili_km = ?,
                    latitude_rumah = ?,
                    longitude_rumah = ?,
                    status_pendaftaran = 'draft',
                    updated_at = NOW()

                WHERE user_id = ?
            ");

            $stmt->execute([

                $jalur,
                $jarakFloat,
                $latitudeRumah,
                $longitudeRumah,
                $userId

            ]);

            jsonResponse(
                true,
                'Jalur dan lokasi rumah berhasil disimpan.',
                [
                    'jalur' => $jalur,
                    'jarak' => $jarakFloat,
                    'latitude' => $latitudeRumah,
                    'longitude' => $longitudeRumah
                ]
            );

        } catch (Throwable $e) {

            jsonResponse(
                false,
                'Gagal menyimpan jalur pendaftaran.'
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | UPLOAD DOKUMEN
    |--------------------------------------------------------------------------
    */

    if ($action === 'upload_dokumen') {

        if ($locked) {

            jsonResponse(
                false,
                'Pendaftaran sudah dikirim dan dokumen tidak dapat diganti.'
            );
        }

        $jenis =
            clean($_POST['jenis_dokumen'] ?? '');

        $jalur =
            $registration['jalur'] ?? '';

        if (!isset(
            $documentRules[$jalur][$jenis]
        )) {

            jsonResponse(
                false,
                'Jenis dokumen tidak sesuai dengan jalur pendaftaran.'
            );
        }

        if (!isset($_FILES['file'])) {

            jsonResponse(
                false,
                'File belum dipilih.'
            );
        }

        $file =
            $_FILES['file'];

        if (
            $file['error'] !==
            UPLOAD_ERR_OK
        ) {

            jsonResponse(
                false,
                'File gagal diupload.'
            );
        }

        if (
            $file['size'] >
            5 * 1024 * 1024
        ) {

            jsonResponse(
                false,
                'Ukuran file maksimal 5 MB.'
            );
        }

        $finfo =
            new finfo(FILEINFO_MIME_TYPE);

        $mime =
            $finfo->file(
                $file['tmp_name']
            );

        $allowedMime = [

            'application/pdf' => 'pdf',

            'image/jpeg' => 'jpg',

            'image/png' => 'png'

        ];

        if (!isset(
            $allowedMime[$mime]
        )) {

            jsonResponse(
                false,
                'Format file harus PDF, JPG, JPEG, atau PNG.'
            );
        }

        $extension =
            $allowedMime[$mime];

        $randomName =
            bin2hex(
                random_bytes(16)
            ) .
            '_' .
            time() .
            '.' .
            $extension;

        $uploadDirectory =
            __DIR__ .
            '/uploads/dokumen/' .
            $userId .
            '/';

        if (!is_dir(
            $uploadDirectory
        )) {

            if (!mkdir(
                $uploadDirectory,
                0755,
                true
            )) {

                jsonResponse(
                    false,
                    'Folder upload tidak dapat dibuat.'
                );
            }
        }

        $newPath =
            $uploadDirectory .
            $randomName;

        if (!move_uploaded_file(
            $file['tmp_name'],
            $newPath
        )) {

            jsonResponse(
                false,
                'Gagal menyimpan file.'
            );
        }

        $relativePath =
            'uploads/dokumen/' .
            $userId .
            '/' .
            $randomName;

        try {

            $pdo->beginTransaction();

            $stmt = $pdo->prepare("
                SELECT *
                FROM dokumen
                WHERE user_id = ?
                  AND jenis_dokumen = ?
                LIMIT 1
            ");

            $stmt->execute([

                $userId,
                $jenis

            ]);

            $oldDocument =
                $stmt->fetch();

            if ($oldDocument) {

                $stmt = $pdo->prepare("
                    UPDATE dokumen SET

                        nama_file = ?,
                        file_path = ?,
                        status = 'menunggu',
                        catatan_admin = NULL,
                        uploaded_at = NOW()

                    WHERE id = ?
                ");

                $stmt->execute([

                    $file['name'],
                    $relativePath,
                    $oldDocument['id']

                ]);

            } else {

                $stmt = $pdo->prepare("
                    INSERT INTO dokumen (

                        user_id,
                        jenis_dokumen,
                        nama_file,
                        file_path,
                        status,
                        uploaded_at

                    )

                    VALUES (
                        ?,
                        ?,
                        ?,
                        ?,
                        'menunggu',
                        NOW()
                    )
                ");

                $stmt->execute([

                    $userId,
                    $jenis,
                    $file['name'],
                    $relativePath

                ]);
            }

            $pdo->commit();

            if (
                $oldDocument &&
                !empty(
                    $oldDocument['file_path']
                )
            ) {

                $oldPhysicalPath =
                    __DIR__ .
                    '/' .
                    ltrim(
                        $oldDocument['file_path'],
                        '/'
                    );

                if (
                    is_file(
                        $oldPhysicalPath
                    ) &&
                    $oldPhysicalPath !==
                    $newPath
                ) {

                    @unlink(
                        $oldPhysicalPath
                    );
                }
            }

            jsonResponse(
                true,
                'Dokumen berhasil diupload.'
            );

        } catch (Throwable $e) {

            if (
                $pdo->inTransaction()
            ) {

                $pdo->rollBack();
            }

            if (
                is_file($newPath)
            ) {

                @unlink($newPath);
            }

            jsonResponse(
                false,
                'Gagal menyimpan dokumen.'
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | SUBMIT PENDAFTARAN
    |--------------------------------------------------------------------------
    */

    if ($action === 'submit_pendaftaran') {

        if ($locked) {

            jsonResponse(
                false,
                'Pendaftaran sudah dikirim sebelumnya.'
            );
        }

        $pernyataan =
            isset($_POST['pernyataan'])
                ? (int) $_POST['pernyataan']
                : 0;

        if ($pernyataan !== 1) {

            jsonResponse(
                false,
                'Anda harus menyetujui pernyataan pendaftaran.'
            );
        }

        $stmt = $pdo->prepare("
            SELECT *
            FROM pendaftaran
            WHERE user_id = ?
            LIMIT 1
        ");

        $stmt->execute([
            $userId
        ]);

        $p =
            $stmt->fetch();

        $stmt = $pdo->prepare("
            SELECT *
            FROM orang_tua
            WHERE user_id = ?
            LIMIT 1
        ");

        $stmt->execute([
            $userId
        ]);

        $o =
            $stmt->fetch();

        $requiredStudent = [

            'jenis_kelamin',
            'tempat_lahir',
            'tanggal_lahir',
            'agama',
            'no_kk',
            'alamat',
            'desa_kelurahan',
            'kecamatan',
            'kabupaten_kota',
            'provinsi',
            'asal_sekolah'

        ];

        foreach (
            $requiredStudent
            as $field
        ) {

            if (
                empty($p[$field])
            ) {

                jsonResponse(
                    false,
                    'Data siswa belum lengkap.'
                );
            }
        }

        if (
            empty($o['ayah_nama'])
        ) {

            jsonResponse(
                false,
                'Nama Ayah belum diisi.'
            );
        }

        if (
            !validNik(
                (string) $o['ayah_nik']
            )
        ) {

            jsonResponse(
                false,
                'NIK Ayah belum valid.'
            );
        }

        if (
            empty($o['ayah_pekerjaan'])
        ) {

            jsonResponse(
                false,
                'Pekerjaan Ayah belum diisi.'
            );
        }

        if (
            empty($o['ayah_penghasilan'])
        ) {

            jsonResponse(
                false,
                'Penghasilan Ayah belum diisi.'
            );
        }

        if (
            empty($o['ibu_nama'])
        ) {

            jsonResponse(
                false,
                'Nama Ibu belum diisi.'
            );
        }

        if (
            !validNik(
                (string) $o['ibu_nik']
            )
        ) {

            jsonResponse(
                false,
                'NIK Ibu belum valid.'
            );
        }

        if (
            empty($o['ibu_pekerjaan'])
        ) {

            jsonResponse(
                false,
                'Pekerjaan Ibu belum diisi.'
            );
        }

        if (
            empty($o['ibu_penghasilan'])
        ) {

            jsonResponse(
                false,
                'Penghasilan Ibu belum diisi.'
            );
        }

        $jalur =
            $p['jalur'] ?? '';

        if (
            !isset(
                $documentRules[$jalur]
            )
        ) {

            jsonResponse(
                false,
                'Jalur pendaftaran belum dipilih.'
            );
        }

        if (
            $jalur === 'domisili'
        ) {

            if (
                empty(
                    $p['latitude_rumah']
                ) ||
                empty(
                    $p['longitude_rumah']
                )
            ) {

                jsonResponse(
                    false,
                    'Lokasi rumah belum dipilih pada peta.'
                );
            }

            if (
                empty(
                    $p['jarak_domisili_km']
                ) ||
                (float)
                $p['jarak_domisili_km'] <= 0
            ) {

                jsonResponse(
                    false,
                    'Jarak rumah ke sekolah belum dihitung.'
                );
            }
        }

        foreach (
            $documentRules[$jalur]
            as $jenis => $rule
        ) {

            if (
                !$rule['required']
            ) {

                continue;
            }

            if (
                !isset(
                    $documents[$jenis]
                ) ||
                empty(
                    $documents[$jenis]['file_path']
                )
            ) {

                jsonResponse(

                    false,

                    'Dokumen wajib belum lengkap: ' .
                    $rule['label']

                );
            }
        }

        $nomorPendaftaran =
            'PPDB-' .
            date('Y') .
            '-' .
            str_pad(
                (string) $userId,
                6,
                '0',
                STR_PAD_LEFT
            );

        try {

            $stmt = $pdo->prepare("
                UPDATE pendaftaran SET

                    nomor_pendaftaran = ?,
                    status_pendaftaran = 'menunggu_verifikasi',
                    pernyataan = 1,
                    dikirim_at = NOW(),
                    catatan_admin = NULL,
                    updated_at = NOW()

                WHERE user_id = ?
            ");

            $stmt->execute([

                $nomorPendaftaran,
                $userId

            ]);

            jsonResponse(

                true,

                'Pendaftaran berhasil dikirim.',

                [
                    'nomor_pendaftaran' =>
                        $nomorPendaftaran
                ]

            );

        } catch (Throwable $e) {

            jsonResponse(
                false,
                'Gagal mengirim pendaftaran.'
            );
        }
    }

    jsonResponse(
        false,
        'Aksi tidak dikenali.'
    );
}

/*
|--------------------------------------------------------------------------
| DATA UNTUK HTML
|--------------------------------------------------------------------------
*/

$selectedJalur =
    $registration['jalur'] ?? '';

$latitudeRumah =
    $registration['latitude_rumah'] ?? '';

$longitudeRumah =
    $registration['longitude_rumah'] ?? '';

$jarakRumah =
    $registration['jarak_domisili_km'] ?? '';

/*
|--------------------------------------------------------------------------
| CEK KELENGKAPAN
|--------------------------------------------------------------------------
*/

$studentComplete =

    !empty(
        $registration['jenis_kelamin']
    ) &&

    !empty(
        $registration['tempat_lahir']
    ) &&

    !empty(
        $registration['tanggal_lahir']
    ) &&

    !empty(
        $registration['agama']
    ) &&

    !empty(
        $registration['no_kk']
    ) &&

    !empty(
        $registration['alamat']
    ) &&

    !empty(
        $registration['desa_kelurahan']
    ) &&

    !empty(
        $registration['kecamatan']
    ) &&

    !empty(
        $registration['kabupaten_kota']
    ) &&

    !empty(
        $registration['provinsi']
    ) &&

    !empty(
        $registration['asal_sekolah']
    );

$parentComplete =

    !empty(
        $parents['ayah_nama']
    ) &&

    !empty(
        $parents['ayah_nik']
    ) &&

    !empty(
        $parents['ayah_pekerjaan']
    ) &&

    !empty(
        $parents['ayah_penghasilan']
    ) &&

    !empty(
        $parents['ibu_nama']
    ) &&

    !empty(
        $parents['ibu_nik']
    ) &&

    !empty(
        $parents['ibu_pekerjaan']
    ) &&

    !empty(
        $parents['ibu_penghasilan']
    );

$pathComplete =
    !empty($selectedJalur);

if (
    $selectedJalur === 'domisili'
) {

    $pathComplete =

        $pathComplete &&

        !empty($latitudeRumah) &&

        !empty($longitudeRumah) &&

        !empty($jarakRumah);
}

$documentsComplete = false;

if (
    $selectedJalur !== '' &&
    isset(
        $documentRules[$selectedJalur]
    )
) {

    $documentsComplete = true;

    foreach (
        $documentRules[$selectedJalur]
        as $jenis => $rule
    ) {

        if (
            !$rule['required']
        ) {

            continue;
        }

        if (
            !isset(
                $documents[$jenis]
            ) ||
            empty(
                $documents[$jenis]['file_path']
            )
        ) {

            $documentsComplete = false;

            break;
        }
    }
}

/*
|--------------------------------------------------------------------------
| SLIDE AWAL
|--------------------------------------------------------------------------
*/

$initialSlide = 1;

if (!$locked) {

    if (!$studentComplete) {

        $initialSlide = 1;

    } elseif (!$parentComplete) {

        $initialSlide = 2;

    } elseif (!$pathComplete) {

        $initialSlide = 3;

    } elseif (!$documentsComplete) {

        $initialSlide = 4;

    } else {

        $initialSlide = 5;
    }

} else {

    $initialSlide = 5;
}

?>
<!DOCTYPE html>
<html lang="id">

<head>

<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0"
>

<title>
    <?= $isLulus
        ? 'Informasi Kelulusan'
        : 'Formulir Pendaftaran'
    ?>
    -
    <?= h($schoolName) ?>
</title>

<link
    rel="stylesheet"
    href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
/>

<script
    src="https://cdn.jsdelivr.net/npm/sweetalert2@11"
></script>

<style>

* {
    box-sizing: border-box;
}

body {
    margin: 0;
    font-family:
        Inter,
        -apple-system,
        BlinkMacSystemFont,
        "Segoe UI",
        sans-serif;
    background: #f4f7fb;
    color: #172033;
}

.container {
    width: min(1100px, 94%);
    margin: 30px auto 60px;
}

.header {
    background:
        linear-gradient(
            135deg,
            #2563eb,
            #4f46e5
        );
    color: white;
    padding: 30px;
    border-radius: 22px;
    margin-bottom: 22px;
    box-shadow:
        0 15px 35px
        rgba(37, 99, 235, .18);
}

.header h1 {
    margin: 0 0 7px;
    font-size: 27px;
}

.header p {
    margin: 0;
    opacity: .9;
}

.status {
    display: inline-flex;
    align-items: center;
    margin-top: 15px;
    padding: 8px 13px;
    background: rgba(255,255,255,.16);
    border-radius: 30px;
    font-size: 13px;
}

.card {
    background: white;
    border-radius: 20px;
    padding: 28px;
    box-shadow:
        0 8px 25px
        rgba(15,23,42,.06);
}

/*
|--------------------------------------------------------------------------
| FORM
|--------------------------------------------------------------------------
*/

.progress {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin: 25px 0;
    position: relative;
}

.progress::before {
    content: "";
    position: absolute;
    left: 7%;
    right: 7%;
    top: 21px;
    height: 3px;
    background: #dbe3ef;
    z-index: 0;
}

.progress-item {
    position: relative;
    z-index: 1;
    text-align: center;
    width: 20%;
}

.progress-circle {
    width: 43px;
    height: 43px;
    border-radius: 50%;
    margin: auto;
    background: white;
    border: 3px solid #dbe3ef;
    display: flex;
    justify-content: center;
    align-items: center;
    font-weight: 700;
    color: #64748b;
}

.progress-item.active .progress-circle,
.progress-item.done .progress-circle {
    background: #2563eb;
    border-color: #2563eb;
    color: white;
}

.progress-label {
    font-size: 12px;
    margin-top: 8px;
    color: #64748b;
}

.slide {
    display: none;
    animation: slideIn .25s ease;
}

.slide.active {
    display: block;
}

@keyframes slideIn {

    from {
        opacity: 0;
        transform: translateX(20px);
    }

    to {
        opacity: 1;
        transform: translateX(0);
    }

}

.section-title {
    margin-bottom: 22px;
}

.section-title h2 {
    margin: 0 0 6px;
    font-size: 22px;
}

.section-title p {
    margin: 0;
    color: #64748b;
    font-size: 14px;
}

.grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 17px;
}

.field {
    margin-bottom: 16px;
}

.field.full {
    grid-column: 1 / -1;
}

label {
    display: block;
    font-size: 13px;
    font-weight: 650;
    margin-bottom: 7px;
}

.required {
    color: #ef4444;
}

input,
select,
textarea {
    width: 100%;
    border: 1px solid #dbe2ea;
    border-radius: 11px;
    padding: 12px 13px;
    outline: none;
    font-size: 14px;
    background: white;
    transition: .2s;
}

input:focus,
select:focus,
textarea:focus {
    border-color: #2563eb;
    box-shadow:
        0 0 0 3px
        rgba(37,99,235,.08);
}

textarea {
    min-height: 95px;
    resize: vertical;
}

.info-box {
    padding: 14px 16px;
    border-radius: 12px;
    background: #eff6ff;
    color: #1e40af;
    font-size: 13px;
    margin-bottom: 20px;
}

.warning-box {
    padding: 14px 16px;
    border-radius: 12px;
    background: #fff7ed;
    color: #9a3412;
    font-size: 13px;
    margin-bottom: 20px;
}

.parent-section {
    border: 1px solid #e5eaf1;
    border-radius: 16px;
    padding: 20px;
    margin-bottom: 20px;
}

.parent-section h3 {
    margin-top: 0;
}

.optional {
    color: #64748b;
    font-weight: 400;
    font-size: 12px;
}

.path-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 15px;
}

.path-card {
    border: 2px solid #e5eaf1;
    border-radius: 17px;
    padding: 19px;
    cursor: pointer;
    transition: .2s;
}

.path-card:hover {
    border-color: #93c5fd;
}

.path-card.selected {
    border-color: #2563eb;
    background: #eff6ff;
}

.path-card input {
    display: none;
}

.path-title {
    font-weight: 750;
    margin-bottom: 5px;
}

.path-desc {
    color: #64748b;
    font-size: 13px;
}

.map-wrapper {
    margin-top: 20px;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    overflow: hidden;
}

#schoolMap {
    height: 430px;
    width: 100%;
}

.map-info {
    padding: 17px;
    background: white;
}

.distance-box {
    margin-top: 14px;
    border-radius: 13px;
    background: #eff6ff;
    padding: 17px;
    text-align: center;
}

.distance-label {
    font-size: 12px;
    color: #64748b;
}

.distance-value {
    color: #2563eb;
    font-size: 30px;
    font-weight: 800;
    margin-top: 3px;
}

.coordinate-box {
    margin-top: 12px;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
}

.coordinate {
    padding: 10px;
    background: #f8fafc;
    border-radius: 9px;
    font-size: 12px;
}

.document-card {
    border: 1px solid #e2e8f0;
    border-radius: 15px;
    padding: 18px;
    margin-bottom: 14px;
}

.document-card h3 {
    font-size: 15px;
    margin: 0 0 5px;
}

.document-status {
    font-size: 12px;
    margin-bottom: 12px;
}

.doc-valid {
    color: #16a34a;
}

.doc-wait {
    color: #d97706;
}

.doc-invalid {
    color: #dc2626;
}

.review-table {
    width: 100%;
    border-collapse: collapse;
}

.review-table td {
    padding: 11px 5px;
    border-bottom: 1px solid #eef2f7;
    vertical-align: top;
}

.review-table td:first-child {
    width: 35%;
    color: #64748b;
}

.statement {
    margin-top: 22px;
    padding: 17px;
    border: 1px solid #dbeafe;
    background: #eff6ff;
    border-radius: 14px;
}

.statement label {
    display: flex;
    gap: 10px;
    align-items: flex-start;
}

.statement input {
    width: auto;
    margin-top: 3px;
}

.actions {
    display: flex;
    justify-content: space-between;
    gap: 12px;
    margin-top: 25px;
}

.btn {
    border: none;
    border-radius: 11px;
    padding: 12px 18px;
    font-weight: 700;
    cursor: pointer;
    font-size: 14px;
}

.btn-primary {
    background: #2563eb;
    color: white;
}

.btn-primary:hover {
    background: #1d4ed8;
}

.btn-secondary {
    background: #e2e8f0;
    color: #334155;
}

.btn-success {
    background: #16a34a;
    color: white;
}

.btn-danger {
    background: #dc2626;
    color: white;
}

.btn:disabled {
    opacity: .6;
    cursor: not-allowed;
}

.hidden {
    display: none !important;
}

.locked-box {
    background: #ecfdf5;
    border: 1px solid #bbf7d0;
    color: #166534;
    padding: 17px;
    border-radius: 14px;
    margin-bottom: 20px;
}

/*
|--------------------------------------------------------------------------
| HALAMAN LULUS
|--------------------------------------------------------------------------
*/

.graduation-card {
    position: relative;
    overflow: hidden;
    background:
        linear-gradient(
            135deg,
            #0f766e,
            #2563eb
        );
    color: #fff;
    border-radius: 24px;
    padding: 30px;
    margin-bottom: 28px;
    box-shadow:
        0 15px 35px
        rgba(37,99,235,.18);
}

.graduation-card::after {
    content: "";
    position: absolute;
    width: 180px;
    height: 180px;
    right: -60px;
    top: -60px;
    border-radius: 50%;
    background: rgba(255,255,255,.08);
}

.success-icon {
    width: 72px;
    height: 72px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 18px;
    background: rgba(255,255,255,.16);
    font-size: 36px;
    font-weight: 800;
}

.graduation-card h2 {
    position: relative;
    z-index: 1;
    margin: 0 0 8px;
    font-size: 28px;
}

.graduation-card > p {
    position: relative;
    z-index: 1;
    margin: 0;
    opacity: .92;
    line-height: 1.6;
}

.student-summary {
    position: relative;
    z-index: 1;
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 12px;
    margin-top: 24px;
}

.summary-item {
    padding: 15px;
    background: rgba(255,255,255,.12);
    border-radius: 14px;
}

.summary-label {
    font-size: 11px;
    opacity: .75;
    margin-bottom: 5px;
}

.summary-value {
    font-weight: 750;
    word-break: break-word;
}

.flow-title {
    margin: 0 0 18px;
    font-size: 20px;
}

.flow {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 12px;
    margin-bottom: 30px;
}

.flow-step {
    position: relative;
    text-align: center;
}

.flow-circle {
    width: 48px;
    height: 48px;
    margin: 0 auto 9px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f1f5f9;
    color: #64748b;
    border: 3px solid #e2e8f0;
    font-weight: 800;
}

.flow-step.done .flow-circle {
    background: #16a34a;
    color: #fff;
    border-color: #16a34a;
}

.flow-step.active .flow-circle {
    background: #2563eb;
    color: #fff;
    border-color: #2563eb;
}

.flow-name {
    font-size: 13px;
    font-weight: 700;
}

.flow-desc {
    margin-top: 4px;
    color: #64748b;
    font-size: 11px;
}

.checklist-section {
    margin-top: 26px;
}

.checklist-section h3 {
    margin: 0 0 5px;
    font-size: 19px;
}

.checklist-section > p {
    margin: 0 0 17px;
    color: #64748b;
    font-size: 13px;
}

.checklist {
    display: grid;
    gap: 11px;
}

.checklist-item {
    display: flex;
    gap: 13px;
    align-items: flex-start;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    padding: 15px;
}

.checklist-icon {
    flex: 0 0 34px;
    width: 34px;
    height: 34px;
    border-radius: 10px;
    background: #eff6ff;
    color: #2563eb;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 800;
}

.checklist-content strong {
    display: block;
    font-size: 14px;
    margin-bottom: 3px;
}

.checklist-content span {
    display: block;
    color: #64748b;
    font-size: 12px;
    line-height: 1.5;
}

.jalur-box {
    margin-top: 25px;
    padding: 21px;
    border-radius: 17px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
}

.jalur-header {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 6px;
}

.jalur-icon {
    font-size: 24px;
}

.jalur-header h3 {
    margin: 0;
    font-size: 19px;
}

.jalur-description {
    margin: 0 0 17px;
    color: #64748b;
    font-size: 13px;
    line-height: 1.6;
}

.jalur-note {
    margin-top: 16px;
    padding: 13px 15px;
    border-radius: 12px;
    background: #fff7ed;
    color: #9a3412;
    font-size: 12px;
    line-height: 1.6;
}

.print-actions {
    display: flex;
    justify-content: flex-end;
    margin-top: 25px;
}

/*
|--------------------------------------------------------------------------
| PRINT
|--------------------------------------------------------------------------
*/

@media print {

    body {
        background: #fff;
    }

    .container {
        width: 100%;
        margin: 0;
    }

    .header {
        background: #fff;
        color: #172033;
        box-shadow: none;
        border: 1px solid #e2e8f0;
    }

    .status {
        background: #f1f5f9;
        color: #334155;
    }

    .progress,
    .locked-box,
    .print-actions {
        display: none !important;
    }

    .card {
        box-shadow: none;
        padding: 0;
    }

    .graduation-card {
        color: #172033;
        background: #fff;
        border: 1px solid #e2e8f0;
        box-shadow: none;
    }

    .graduation-card::after {
        display: none;
    }

    .summary-item {
        background: #f8fafc;
    }

    .summary-label {
        color: #64748b;
        opacity: 1;
    }

    .summary-value {
        color: #172033;
    }

}

/*
|--------------------------------------------------------------------------
| MOBILE
|--------------------------------------------------------------------------
*/

@media (max-width: 700px) {

    .container {
        width: 95%;
        margin-top: 15px;
    }

    .header {
        padding: 22px;
    }

    .header h1 {
        font-size: 22px;
    }

    .card {
        padding: 18px;
    }

    .grid,
    .path-grid,
    .coordinate-box {
        grid-template-columns: 1fr;
    }

    #schoolMap {
        height: 350px;
    }

    .progress-label {
        display: none;
    }

    .progress::before {
        left: 8%;
        right: 8%;
    }

    .actions {
        flex-direction: column-reverse;
    }

    .btn {
        width: 100%;
    }

    .graduation-card {
        padding: 23px;
    }

    .graduation-card h2 {
        font-size: 22px;
        line-height: 1.3;
    }

    .student-summary {
        grid-template-columns: 1fr;
    }

    .flow {
        grid-template-columns: 1fr 1fr;
        gap: 20px 10px;
    }

    .jalur-header {
        align-items: flex-start;
    }

}

</style>

</head>

<body>

<div class="container">

    <div class="header">

        <h1>

            <?php if ($isLulus): ?>

                Informasi Kelulusan

            <?php else: ?>

                Formulir Pendaftaran

            <?php endif; ?>

        </h1>

        <p>
            <?= h($schoolName) ?>
        </p>

        <div class="status">

            Status:
            &nbsp;

            <?= h(
                ucwords(
                    str_replace(
                        '_',
                        ' ',
                        $status
                    )
                )
            ) ?>

        </div>

    </div>


    <?php if ($isLulus): ?>

        <!-- ========================================================= -->
        <!-- HALAMAN KELULUSAN -->
        <!-- ========================================================= -->

        <div class="card">

            <div class="graduation-card">

                <div class="success-icon">
                    ✓
                </div>

                <h2>
                    SELAMAT, ANDA DINYATAKAN LULUS
                </h2>

                <p>

                    Selamat kepada

                    <strong>
                        <?= h(
                            $user['nama_lengkap']
                            ?? $user['name']
                            ?? 'Peserta Didik'
                        ) ?>
                    </strong>.

                    Anda dinyatakan lulus seleksi SPMB
                    melalui jalur

                    <strong>
                        <?= h($selectedJalurLabel) ?>
                    </strong>.

                </p>

                <div class="student-summary">

                    <div class="summary-item">

                        <div class="summary-label">
                            Nama Siswa
                        </div>

                        <div class="summary-value">

                            <?= h(
                                $user['nama_lengkap']
                                ?? $user['name']
                                ?? '-'
                            ) ?>

                        </div>

                    </div>

                    <div class="summary-item">

                        <div class="summary-label">
                            Nomor Pendaftaran
                        </div>

                        <div class="summary-value">

                            <?= h(
                                $registration[
                                    'nomor_pendaftaran'
                                ] ?? '-'
                            ) ?>

                        </div>

                    </div>

                    <div class="summary-item">

                        <div class="summary-label">
                            Jalur Pendaftaran
                        </div>

                        <div class="summary-value">

                            <?= h(
                                $selectedJalurLabel
                            ) ?>

                        </div>

                    </div>

                </div>

            </div>


            <!-- ===================================================== -->
            <!-- ALUR -->
            <!-- ===================================================== -->

            <h3 class="flow-title">

                Alur Setelah Dinyatakan Lulus

            </h3>

            <div class="flow">

                <div class="flow-step done">

                    <div class="flow-circle">
                        ✓
                    </div>

                    <div class="flow-name">
                        Lulus
                    </div>

                    <div class="flow-desc">
                        Hasil seleksi
                    </div>

                </div>


                <div class="flow-step active">

                    <div class="flow-circle">
                        2
                    </div>

                    <div class="flow-name">
                        Siapkan Berkas
                    </div>

                    <div class="flow-desc">
                        Lengkapi dokumen
                    </div>

                </div>


                <div class="flow-step">

                    <div class="flow-circle">
                        3
                    </div>

                    <div class="flow-name">
                        Daftar Ulang
                    </div>

                    <div class="flow-desc">
                        Verifikasi sekolah
                    </div>

                </div>


                <div class="flow-step">

                    <div class="flow-circle">
                        4
                    </div>

                    <div class="flow-name">
                        Selesai
                    </div>

                    <div class="flow-desc">
                        Menjadi siswa
                    </div>

                </div>

            </div>


            <!-- ===================================================== -->
            <!-- BERKAS SEMUA JALUR -->
            <!-- ===================================================== -->

            <div class="checklist-section">

                <h3>
                    📋 Berkas Wajib Semua Jalur
                </h3>

                <p>

                    Siapkan dokumen berikut untuk proses
                    daftar ulang. Dokumen asli digunakan
                    untuk verifikasi.

                </p>

                <div class="checklist">

                    <?php foreach (
                        $commonDaftarUlang
                        as $item
                    ): ?>

                        <div class="checklist-item">

                            <div class="checklist-icon">
                                ✓
                            </div>

                            <div class="checklist-content">

                                <strong>
                                    <?= h(
                                        $item['title']
                                    ) ?>
                                </strong>

                                <span>
                                    <?= h(
                                        $item['desc']
                                    ) ?>
                                </span>

                            </div>

                        </div>

                    <?php endforeach; ?>

                </div>

            </div>


            <!-- ===================================================== -->
            <!-- BERKAS TAMBAHAN JALUR -->
            <!-- ===================================================== -->

            <?php if ($selectedPathInfo): ?>

                <div class="jalur-box">

                    <div class="jalur-header">

                        <div class="jalur-icon">

                            <?= h(
                                $selectedPathInfo['icon']
                            ) ?>

                        </div>

                        <h3>

                            Berkas Tambahan
                            <?= h(
                                $selectedPathInfo['title']
                            ) ?>

                        </h3>

                    </div>

                    <p class="jalur-description">

                        <?= h(
                            $selectedPathInfo[
                                'description'
                            ]
                        ) ?>

                    </p>

                    <div class="checklist">

                        <?php foreach (
                            $selectedPathInfo['items']
                            as $item
                        ): ?>

                            <div class="checklist-item">

                                <div class="checklist-icon">
                                    ✓
                                </div>

                                <div class="checklist-content">

                                    <strong>
                                        <?= h(
                                            $item['title']
                                        ) ?>
                                    </strong>

                                    <span>
                                        <?= h(
                                            $item['desc']
                                        ) ?>
                                    </span>

                                </div>

                            </div>

                        <?php endforeach; ?>

                    </div>

                    <div class="jalur-note">

                        <strong>
                            Catatan:
                        </strong>

                        <?= h(
                            $selectedPathInfo['note']
                        ) ?>

                    </div>

                </div>

            <?php else: ?>

                <div class="warning-box">

                    Jalur pendaftaran belum tercatat.
                    Silakan hubungi pihak sekolah untuk
                    memastikan dokumen daftar ulang
                    yang harus dibawa.

                </div>

            <?php endif; ?>


            <!-- ===================================================== -->
            <!-- CATATAN -->
            <!-- ===================================================== -->

            <div
                class="info-box"
                style="margin-top:25px;margin-bottom:0;"
            >

                <strong>
                    Perhatian:
                </strong>

                <br>

                Jadwal, tempat, jumlah fotokopi,
                ukuran pas foto, dan dokumen tambahan
                dapat ditentukan oleh sekolah.

                Pastikan mengikuti pengumuman resmi
                sekolah untuk proses daftar ulang.

            </div>


            <!-- ===================================================== -->
            <!-- PRINT -->
            <!-- ===================================================== -->

            <div class="print-actions">

                <button
                    type="button"
                    class="btn btn-primary"
                    onclick="window.print()"
                >

                    🖨 Cetak Informasi Daftar Ulang

                </button>

            </div>

        </div>


    <?php else: ?>


        <!-- ========================================================= -->
        <!-- FORMULIR PENDAFTARAN LAMA -->
        <!-- ========================================================= -->

        <?php if ($locked): ?>

            <div class="locked-box">

                <strong>
                    Pendaftaran sudah dikirim.
                </strong>

                <br>

                Data saat ini dalam status

                <strong>
                    <?= h(
                        ucwords(
                            str_replace(
                                '_',
                                ' ',
                                $status
                            )
                        )
                    ) ?>
                </strong>.

                Formulir tidak dapat diedit
                sampai proses berikutnya.

            </div>

        <?php endif; ?>


        <div class="progress">

            <div
                class="progress-item active"
                data-progress="1"
            >

                <div class="progress-circle">
                    1
                </div>

                <div class="progress-label">
                    Siswa
                </div>

            </div>


            <div
                class="progress-item"
                data-progress="2"
            >

                <div class="progress-circle">
                    2
                </div>

                <div class="progress-label">
                    Orang Tua
                </div>

            </div>


            <div
                class="progress-item"
                data-progress="3"
            >

                <div class="progress-circle">
                    3
                </div>

                <div class="progress-label">
                    Jalur
                </div>

            </div>


            <div
                class="progress-item"
                data-progress="4"
            >

                <div class="progress-circle">
                    4
                </div>

                <div class="progress-label">
                    Dokumen
                </div>

            </div>


            <div
                class="progress-item"
                data-progress="5"
            >

                <div class="progress-circle">
                    5
                </div>

                <div class="progress-label">
                    Kirim
                </div>

            </div>

        </div>


        <div class="card">

            <!-- ===================================================== -->
            <!-- SLIDE 1 -->
            <!-- ===================================================== -->

            <div
                class="slide"
                data-slide="1"
            >

                <div class="section-title">

                    <h2>
                        Data Siswa
                    </h2>

                    <p>
                        Lengkapi data pribadi calon peserta didik.
                    </p>

                </div>


                <div class="grid">

                    <div class="field">

                        <label>
                            Jenis Kelamin
                            <span class="required">*</span>
                        </label>

                        <select id="jenis_kelamin">

                            <option value="">
                                -- Pilih --
                            </option>

                            <option
                                value="Laki-laki"
                                <?= (
                                    $registration[
                                        'jenis_kelamin'
                                    ] ?? ''
                                ) === 'Laki-laki'
                                    ? 'selected'
                                    : ''
                                ?>
                            >
                                Laki-laki
                            </option>

                            <option
                                value="Perempuan"
                                <?= (
                                    $registration[
                                        'jenis_kelamin'
                                    ] ?? ''
                                ) === 'Perempuan'
                                    ? 'selected'
                                    : ''
                                ?>
                            >
                                Perempuan
                            </option>

                        </select>

                    </div>


                    <div class="field">

                        <label>
                            Agama
                            <span class="required">*</span>
                        </label>

                        <select id="agama">

                            <option value="">
                                -- Pilih --
                            </option>

                            <?php

                            $religions = [

                                'Islam',
                                'Kristen',
                                'Katolik',
                                'Hindu',
                                'Buddha',
                                'Konghucu',
                                'Lainnya'

                            ];

                            ?>

                            <?php foreach (
                                $religions
                                as $religion
                            ): ?>

                                <option
                                    value="<?= h(
                                        $religion
                                    ) ?>"
                                    <?= (
                                        $registration[
                                            'agama'
                                        ] ?? ''
                                    ) === $religion
                                        ? 'selected'
                                        : ''
                                    ?>
                                >

                                    <?= h(
                                        $religion
                                    ) ?>

                                </option>

                            <?php endforeach; ?>

                        </select>

                    </div>


                    <div class="field">

                        <label>
                            Tempat Lahir
                            <span class="required">*</span>
                        </label>

                        <input
                            type="text"
                            id="tempat_lahir"
                            value="<?= h(
                                $registration[
                                    'tempat_lahir'
                                ] ?? ''
                            ) ?>"
                        >

                    </div>


                    <div class="field">

                        <label>
                            Tanggal Lahir
                            <span class="required">*</span>
                        </label>

                        <input
                            type="date"
                            id="tanggal_lahir"
                            value="<?= h(
                                $registration[
                                    'tanggal_lahir'
                                ] ?? ''
                            ) ?>"
                        >

                    </div>


                    <div class="field">

                        <label>
                            Nomor KK
                            <span class="required">*</span>
                        </label>

                        <input
                            type="text"
                            id="no_kk"
                            maxlength="16"
                            inputmode="numeric"
                            value="<?= h(
                                $registration[
                                    'no_kk'
                                ] ?? ''
                            ) ?>"
                        >

                    </div>


                    <div class="field">

                        <label>
                            Asal Sekolah
                            <span class="required">*</span>
                        </label>

                        <input
                            type="text"
                            id="asal_sekolah"
                            value="<?= h(
                                $registration[
                                    'asal_sekolah'
                                ] ?? ''
                            ) ?>"
                        >

                    </div>


                    <div class="field">

                        <label>
                            NPSN Sekolah Asal
                        </label>

                        <input
                            type="text"
                            id="npsn_sekolah_asal"
                            value="<?= h(
                                $registration[
                                    'npsn_sekolah_asal'
                                ] ?? ''
                            ) ?>"
                        >

                    </div>


                    <div class="field">

                        <label>
                            RT
                        </label>

                        <input
                            type="text"
                            id="rt"
                            value="<?= h(
                                $registration[
                                    'rt'
                                ] ?? ''
                            ) ?>"
                        >

                    </div>


                    <div class="field">

                        <label>
                            RW
                        </label>

                        <input
                            type="text"
                            id="rw"
                            value="<?= h(
                                $registration[
                                    'rw'
                                ] ?? ''
                            ) ?>"
                        >

                    </div>


                    <div class="field">

                        <label>
                            Desa / Kelurahan
                            <span class="required">*</span>
                        </label>

                        <input
                            type="text"
                            id="desa_kelurahan"
                            value="<?= h(
                                $registration[
                                    'desa_kelurahan'
                                ] ?? ''
                            ) ?>"
                        >

                    </div>


                    <div class="field">

                        <label>
                            Kecamatan
                            <span class="required">*</span>
                        </label>

                        <input
                            type="text"
                            id="kecamatan"
                            value="<?= h(
                                $registration[
                                    'kecamatan'
                                ] ?? ''
                            ) ?>"
                        >

                    </div>


                    <div class="field">

                        <label>
                            Kabupaten / Kota
                            <span class="required">*</span>
                        </label>

                        <input
                            type="text"
                            id="kabupaten_kota"
                            value="<?= h(
                                $registration[
                                    'kabupaten_kota'
                                ] ?? ''
                            ) ?>"
                        >

                    </div>


                    <div class="field">

                        <label>
                            Provinsi
                            <span class="required">*</span>
                        </label>

                        <input
                            type="text"
                            id="provinsi"
                            value="<?= h(
                                $registration[
                                    'provinsi'
                                ] ?? ''
                            ) ?>"
                        >

                    </div>


                    <div class="field full">

                        <label>
                            Alamat Lengkap
                            <span class="required">*</span>
                        </label>

                        <textarea id="alamat"><?= h(
                            $registration[
                                'alamat'
                            ] ?? ''
                        ) ?></textarea>

                    </div>

                </div>


                <div class="actions">

                    <div></div>

                    <button
                        type="button"
                        class="btn btn-primary"
                        onclick="nextStep()"
                        <?= $locked
                            ? 'disabled'
                            : ''
                        ?>
                    >
                        Simpan & Lanjutkan →
                    </button>

                </div>

            </div>


            <!-- ===================================================== -->
            <!-- SLIDE 2 -->
            <!-- ===================================================== -->

            <div
                class="slide"
                data-slide="2"
            >

                <div class="section-title">

                    <h2>
                        Data Orang Tua / Wali
                    </h2>

                    <p>
                        Data Ayah dan Ibu wajib diisi.
                        Data Wali bersifat opsional.
                    </p>

                </div>


                <div class="parent-section">

                    <h3>
                        Data Ayah
                    </h3>

                    <div class="grid">

                        <div class="field">

                            <label>
                                Nama Ayah
                                <span class="required">*</span>
                            </label>

                            <input
                                type="text"
                                id="ayah_nama"
                                value="<?= h(
                                    $parents[
                                        'ayah_nama'
                                    ] ?? ''
                                ) ?>"
                            >

                        </div>


                        <div class="field">

                            <label>
                                NIK Ayah
                                <span class="required">*</span>
                            </label>

                            <input
                                type="text"
                                id="ayah_nik"
                                maxlength="16"
                                inputmode="numeric"
                                value="<?= h(
                                    $parents[
                                        'ayah_nik'
                                    ] ?? ''
                                ) ?>"
                            >

                        </div>


                        <div class="field">

                            <label>
                                Pekerjaan Ayah
                                <span class="required">*</span>
                            </label>

                            <select id="ayah_pekerjaan">

                                <option value="">
                                    -- Pilih Pekerjaan --
                                </option>

                                <?php foreach (
                                    $jobOptions
                                    as $job
                                ): ?>

                                    <option
                                        value="<?= h(
                                            $job
                                        ) ?>"
                                        <?= (
                                            $parents[
                                                'ayah_pekerjaan'
                                            ] ?? ''
                                        ) === $job
                                            ? 'selected'
                                            : ''
                                        ?>
                                    >
                                        <?= h($job) ?>
                                    </option>

                                <?php endforeach; ?>

                            </select>

                        </div>


                        <div class="field">

                            <label>
                                Penghasilan Ayah
                                <span class="required">*</span>
                            </label>

                            <select id="ayah_penghasilan">

                                <option value="">
                                    -- Pilih Penghasilan --
                                </option>

                                <?php foreach (
                                    $incomeOptions
                                    as $income
                                ): ?>

                                    <option
                                        value="<?= h(
                                            $income
                                        ) ?>"
                                        <?= (
                                            $parents[
                                                'ayah_penghasilan'
                                            ] ?? ''
                                        ) === $income
                                            ? 'selected'
                                            : ''
                                        ?>
                                    >
                                        <?= h(
                                            $income
                                        ) ?>
                                    </option>

                                <?php endforeach; ?>

                            </select>

                        </div>


                        <div class="field">

                            <label>
                                No. HP Ayah
                            </label>

                            <input
                                type="text"
                                id="ayah_no_hp"
                                inputmode="numeric"
                                value="<?= h(
                                    $parents[
                                        'ayah_no_hp'
                                    ] ?? ''
                                ) ?>"
                            >

                        </div>

                    </div>

                </div>


                <div class="parent-section">

                    <h3>
                        Data Ibu
                    </h3>

                    <div class="grid">

                        <div class="field">

                            <label>
                                Nama Ibu
                                <span class="required">*</span>
                            </label>

                            <input
                                type="text"
                                id="ibu_nama"
                                value="<?= h(
                                    $parents[
                                        'ibu_nama'
                                    ] ?? ''
                                ) ?>"
                            >

                        </div>


                        <div class="field">

                            <label>
                                NIK Ibu
                                <span class="required">*</span>
                            </label>

                            <input
                                type="text"
                                id="ibu_nik"
                                maxlength="16"
                                inputmode="numeric"
                                value="<?= h(
                                    $parents[
                                        'ibu_nik'
                                    ] ?? ''
                                ) ?>"
                            >

                        </div>


                        <div class="field">

                            <label>
                                Pekerjaan Ibu
                                <span class="required">*</span>
                            </label>

                            <select id="ibu_pekerjaan">

                                <option value="">
                                    -- Pilih Pekerjaan --
                                </option>

                                <?php foreach (
                                    $jobOptions
                                    as $job
                                ): ?>

                                    <option
                                        value="<?= h(
                                            $job
                                        ) ?>"
                                        <?= (
                                            $parents[
                                                'ibu_pekerjaan'
                                            ] ?? ''
                                        ) === $job
                                            ? 'selected'
                                            : ''
                                        ?>
                                    >
                                        <?= h($job) ?>
                                    </option>

                                <?php endforeach; ?>

                            </select>

                        </div>


                        <div class="field">

                            <label>
                                Penghasilan Ibu
                                <span class="required">*</span>
                            </label>

                            <select id="ibu_penghasilan">

                                <option value="">
                                    -- Pilih Penghasilan --
                                </option>

                                <?php foreach (
                                    $incomeOptions
                                    as $income
                                ): ?>

                                    <option
                                        value="<?= h(
                                            $income
                                        ) ?>"
                                        <?= (
                                            $parents[
                                                'ibu_penghasilan'
                                            ] ?? ''
                                        ) === $income
                                            ? 'selected'
                                            : ''
                                        ?>
                                    >
                                        <?= h(
                                            $income
                                        ) ?>
                                    </option>

                                <?php endforeach; ?>

                            </select>

                        </div>


                        <div class="field">

                            <label>
                                No. HP Ibu
                            </label>

                            <input
                                type="text"
                                id="ibu_no_hp"
                                inputmode="numeric"
                                value="<?= h(
                                    $parents[
                                        'ibu_no_hp'
                                    ] ?? ''
                                ) ?>"
                            >

                        </div>

                    </div>

                </div>


                <div class="parent-section">

                    <h3>

                        Data Wali

                        <span class="optional">
                            (Opsional)
                        </span>

                    </h3>

                    <div class="info-box">

                        Data Wali tidak wajib diisi.
                        Jika tidak memiliki wali,
                        bagian ini boleh dikosongkan.

                    </div>


                    <div class="grid">

                        <div class="field">

                            <label>
                                Nama Wali
                            </label>

                            <input
                                type="text"
                                id="wali_nama"
                                value="<?= h(
                                    $parents[
                                        'wali_nama'
                                    ] ?? ''
                                ) ?>"
                            >

                        </div>


                        <div class="field">

                            <label>
                                NIK Wali
                            </label>

                            <input
                                type="text"
                                id="wali_nik"
                                maxlength="16"
                                inputmode="numeric"
                                value="<?= h(
                                    $parents[
                                        'wali_nik'
                                    ] ?? ''
                                ) ?>"
                            >

                        </div>


                        <div class="field">

                            <label>
                                Pekerjaan Wali
                            </label>

                            <select id="wali_pekerjaan">

                                <option value="">
                                    -- Pilih Pekerjaan --
                                </option>

                                <?php foreach (
                                    $jobOptions
                                    as $job
                                ): ?>

                                    <option
                                        value="<?= h(
                                            $job
                                        ) ?>"
                                        <?= (
                                            $parents[
                                                'wali_pekerjaan'
                                            ] ?? ''
                                        ) === $job
                                            ? 'selected'
                                            : ''
                                        ?>
                                    >
                                        <?= h($job) ?>
                                    </option>

                                <?php endforeach; ?>

                            </select>

                        </div>


                        <div class="field">

                            <label>
                                Penghasilan Wali
                            </label>

                            <select id="wali_penghasilan">

                                <option value="">
                                    -- Pilih Penghasilan --
                                </option>

                                <?php foreach (
                                    $incomeOptions
                                    as $income
                                ): ?>

                                    <option
                                        value="<?= h(
                                            $income
                                        ) ?>"
                                        <?= (
                                            $parents[
                                                'wali_penghasilan'
                                            ] ?? ''
                                        ) === $income
                                            ? 'selected'
                                            : ''
                                        ?>
                                    >
                                        <?= h(
                                            $income
                                        ) ?>
                                    </option>

                                <?php endforeach; ?>

                            </select>

                        </div>


                        <div class="field">

                            <label>
                                No. HP Wali
                            </label>

                            <input
                                type="text"
                                id="wali_no_hp"
                                inputmode="numeric"
                                value="<?= h(
                                    $parents[
                                        'wali_no_hp'
                                    ] ?? ''
                                ) ?>"
                            >

                        </div>

                    </div>

                </div>


                <div class="actions">

                    <button
                        type="button"
                        class="btn btn-secondary"
                        onclick="prevStep()"
                    >
                        ← Kembali
                    </button>

                    <button
                        type="button"
                        class="btn btn-primary"
                        onclick="nextStep()"
                        <?= $locked
                            ? 'disabled'
                            : ''
                        ?>
                    >
                        Simpan & Lanjutkan →
                    </button>

                </div>

            </div>


            <!-- ===================================================== -->
            <!-- SLIDE 3 -->
            <!-- ===================================================== -->

            <div
                class="slide"
                data-slide="3"
            >

                <div class="section-title">

                    <h2>
                        Pilih Jalur Pendaftaran
                    </h2>

                    <p>
                        Pilih jalur sesuai kondisi
                        dan dokumen yang Anda miliki.
                    </p>

                </div>


                <div class="path-grid">

                    <label
                        class="path-card
                        <?= $selectedJalur === 'domisili'
                            ? 'selected'
                            : ''
                        ?>"
                    >

                        <input
                            type="radio"
                            name="jalur"
                            value="domisili"
                            <?= $selectedJalur === 'domisili'
                                ? 'checked'
                                : ''
                            ?>
                            onchange="
                                selectPath('domisili')
                            "
                        >

                        <div class="path-title">
                            Domisili
                        </div>

                        <div class="path-desc">

                            Berdasarkan domisili
                            tempat tinggal calon
                            peserta didik.

                        </div>

                    </label>


                    <label
                        class="path-card
                        <?= $selectedJalur === 'afirmasi'
                            ? 'selected'
                            : ''
                        ?>"
                    >

                        <input
                            type="radio"
                            name="jalur"
                            value="afirmasi"
                            <?= $selectedJalur === 'afirmasi'
                                ? 'checked'
                                : ''
                            ?>
                            onchange="
                                selectPath('afirmasi')
                            "
                        >

                        <div class="path-title">
                            Afirmasi
                        </div>

                        <div class="path-desc">

                            Untuk calon peserta
                            didik sesuai ketentuan
                            afirmasi.

                        </div>

                    </label>


                    <label
                        class="path-card
                        <?= $selectedJalur === 'prestasi'
                            ? 'selected'
                            : ''
                        ?>"
                    >

                        <input
                            type="radio"
                            name="jalur"
                            value="prestasi"
                            <?= $selectedJalur === 'prestasi'
                                ? 'checked'
                                : ''
                            ?>
                            onchange="
                                selectPath('prestasi')
                            "
                        >

                        <div class="path-title">
                            Prestasi
                        </div>

                        <div class="path-desc">

                            Berdasarkan prestasi
                            akademik atau
                            non-akademik.

                        </div>

                    </label>


                    <label
                        class="path-card
                        <?= $selectedJalur === 'mutasi'
                            ? 'selected'
                            : ''
                        ?>"
                    >

                        <input
                            type="radio"
                            name="jalur"
                            value="mutasi"
                            <?= $selectedJalur === 'mutasi'
                                ? 'checked'
                                : ''
                            ?>
                            onchange="
                                selectPath('mutasi')
                            "
                        >

                        <div class="path-title">
                            Mutasi
                        </div>

                        <div class="path-desc">

                            Untuk calon peserta
                            didik jalur
                            perpindahan orang tua.

                        </div>

                    </label>

                </div>


                <div
                    id="domisiliMapSection"
                    class="<?= $selectedJalur === 'domisili'
                        ? ''
                        : 'hidden'
                    ?>"
                >

                    <div
                        class="info-box"
                        style="margin-top:20px;"
                    >

                        <strong>
                            Cara menentukan lokasi rumah:
                        </strong>

                        <br>

                        Klik posisi rumah Anda
                        pada peta.

                        Sistem akan memasang marker
                        rumah, membuat rute menuju
                        sekolah, lalu menghitung
                        jarak jalan secara otomatis.

                    </div>


                    <div class="map-wrapper">

                        <div id="schoolMap"></div>

                        <div class="map-info">

                            <strong>
                                <?= h(
                                    $schoolName
                                ) ?>
                            </strong>

                            <br>

                            <small>

                                Marker biru = sekolah
                                &nbsp; | &nbsp;
                                Marker merah = rumah

                            </small>


                            <div class="distance-box">

                                <div class="distance-label">
                                    Jarak Rumah ke Sekolah
                                </div>

                                <div
                                    class="distance-value"
                                    id="distanceDisplay"
                                >

                                    <?= $jarakRumah !== ''
                                        ? h(
                                            number_format(
                                                (float)
                                                $jarakRumah,
                                                2,
                                                '.',
                                                ''
                                            )
                                        ) . ' KM'
                                        : '-- KM'
                                    ?>

                                </div>

                            </div>


                            <div class="coordinate-box">

                                <div class="coordinate">

                                    <strong>
                                        Latitude Rumah
                                    </strong>

                                    <br>

                                    <span
                                        id="latitudeDisplay"
                                    >

                                        <?= $latitudeRumah !== ''
                                            ? h(
                                                $latitudeRumah
                                            )
                                            : '-'
                                        ?>

                                    </span>

                                </div>


                                <div class="coordinate">

                                    <strong>
                                        Longitude Rumah
                                    </strong>

                                    <br>

                                    <span
                                        id="longitudeDisplay"
                                    >

                                        <?= $longitudeRumah !== ''
                                            ? h(
                                                $longitudeRumah
                                            )
                                            : '-'
                                        ?>

                                    </span>

                                </div>

                            </div>


                            <input
                                type="hidden"
                                id="jarak_domisili_km"
                                value="<?= h(
                                    $jarakRumah
                                ) ?>"
                            >

                            <input
                                type="hidden"
                                id="latitude_rumah"
                                value="<?= h(
                                    $latitudeRumah
                                ) ?>"
                            >

                            <input
                                type="hidden"
                                id="longitude_rumah"
                                value="<?= h(
                                    $longitudeRumah
                                ) ?>"
                            >


                            <div
                                style="margin-top:15px;"
                            >

                                <a
                                    href="<?= h(
                                        $schoolMapsUrl
                                    ) ?>"
                                    target="_blank"
                                    rel="noopener"
                                    class="btn btn-secondary"
                                    style="
                                        display:inline-block;
                                        text-decoration:none;
                                    "
                                >

                                    📍 Lihat Sekolah
                                    di Google Maps

                                </a>

                            </div>

                        </div>

                    </div>

                </div>


                <div class="actions">

                    <button
                        type="button"
                        class="btn btn-secondary"
                        onclick="prevStep()"
                    >
                        ← Kembali
                    </button>

                    <button
                        type="button"
                        class="btn btn-primary"
                        onclick="nextStep()"
                        <?= $locked
                            ? 'disabled'
                            : ''
                        ?>
                    >
                        Simpan & Lanjutkan →
                    </button>

                </div>

            </div>


            <!-- ===================================================== -->
            <!-- SLIDE 4 -->
            <!-- ===================================================== -->

            <div
                class="slide"
                data-slide="4"
            >

                <div class="section-title">

                    <h2>
                        Upload Dokumen
                    </h2>

                    <p>
                        Dokumen akan menyesuaikan
                        dengan jalur yang dipilih.
                    </p>

                </div>


                <div
                    id="documentContainer"
                ></div>


                <div class="actions">

                    <button
                        type="button"
                        class="btn btn-secondary"
                        onclick="prevStep()"
                    >
                        ← Kembali
                    </button>

                    <button
                        type="button"
                        class="btn btn-primary"
                        onclick="nextStep()"
                        <?= $locked
                            ? 'disabled'
                            : ''
                        ?>
                    >
                        Lanjut ke Pemeriksaan →
                    </button>

                </div>

            </div>


            <!-- ===================================================== -->
            <!-- SLIDE 5 -->
            <!-- ===================================================== -->

            <div
                class="slide"
                data-slide="5"
            >

                <div class="section-title">

                    <h2>
                        Cek & Kirim Pendaftaran
                    </h2>

                    <p>
                        Periksa kembali seluruh data
                        sebelum dikirim.
                    </p>

                </div>


                <div
                    id="reviewContainer"
                ></div>


                <?php if (!$locked): ?>

                    <div class="statement">

                        <label>

                            <input
                                type="checkbox"
                                id="pernyataan"
                            >

                            <span>

                                Saya menyatakan bahwa
                                data yang saya isi adalah
                                benar dan dapat
                                dipertanggungjawabkan.

                                Saya memahami bahwa data
                                yang tidak benar dapat
                                menyebabkan pendaftaran
                                dibatalkan sesuai
                                ketentuan.

                            </span>

                        </label>

                    </div>

                <?php endif; ?>


                <div class="actions">

                    <button
                        type="button"
                        class="btn btn-secondary"
                        onclick="prevStep()"
                        <?= $locked
                            ? 'disabled'
                            : ''
                        ?>
                    >
                        ← Kembali
                    </button>


                    <?php if (!$locked): ?>

                        <button
                            type="button"
                            class="btn btn-success"
                            onclick="submitRegistration()"
                        >
                            ✓ Kirim Pendaftaran
                        </button>

                    <?php endif; ?>

                </div>

            </div>

        </div>

    <?php endif; ?>

</div>


<?php if (!$isLulus): ?>

<script
    src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
></script>

<script>

/*
|--------------------------------------------------------------------------
| GLOBAL
|--------------------------------------------------------------------------
*/

let currentSlide =
    <?= (int) $initialSlide ?>;

const LOCKED =
    <?= $locked ? 'true' : 'false' ?>;

const SCHOOL_LAT =
    <?= json_encode($schoolLat) ?>;

const SCHOOL_LNG =
    <?= json_encode($schoolLng) ?>;

let schoolMap = null;

let schoolMarker = null;

let homeMarker = null;

let routeLine = null;


/*
|--------------------------------------------------------------------------
| DOCUMENT DATA
|--------------------------------------------------------------------------
*/

const documentRules =
    <?= json_encode(
        $documentRules,
        JSON_UNESCAPED_UNICODE |
        JSON_UNESCAPED_SLASHES
    ) ?>;

const existingDocuments =
    <?= json_encode(
        $documents,
        JSON_UNESCAPED_UNICODE |
        JSON_UNESCAPED_SLASHES
    ) ?>;


/*
|--------------------------------------------------------------------------
| SWEETALERT
|--------------------------------------------------------------------------
*/

function alertError(message)
{
    Swal.fire({

        icon: 'error',

        title:
            'Tidak dapat melanjutkan',

        text: message,

        confirmButtonText:
            'Mengerti'

    });
}

function alertSuccess(message)
{
    return Swal.fire({

        icon: 'success',

        title: 'Berhasil',

        text: message,

        confirmButtonText:
            'Lanjut'

    });
}


/*
|--------------------------------------------------------------------------
| AJAX
|--------------------------------------------------------------------------
*/

async function postData(formData)
{
    const response =
        await fetch(
            'formulir.php',
            {
                method: 'POST',
                body: formData,
                credentials: 'same-origin'
            }
        );

    const text =
        await response.text();

    let data;

    try {

        data =
            JSON.parse(text);

    } catch (error) {

        console.error(
            'SERVER RESPONSE:',
            text
        );

        throw new Error(
            'Server mengembalikan respons yang tidak valid.'
        );
    }

    return data;
}


/*
|--------------------------------------------------------------------------
| UPDATE PROGRESS
|--------------------------------------------------------------------------
*/

function updateProgress()
{
    document
        .querySelectorAll(
            '.progress-item'
        )
        .forEach(item => {

            const step =
                parseInt(
                    item.dataset.progress
                );

            item.classList.remove(
                'active',
                'done'
            );

            if (
                step === currentSlide
            ) {

                item.classList.add(
                    'active'
                );

            } else if (
                step < currentSlide
            ) {

                item.classList.add(
                    'done'
                );
            }

        });
}


/*
|--------------------------------------------------------------------------
| SHOW SLIDE
|--------------------------------------------------------------------------
*/

function updateSlide()
{
    document
        .querySelectorAll('.slide')
        .forEach(slide => {

            slide.classList.toggle(

                'active',

                parseInt(
                    slide.dataset.slide
                ) === currentSlide

            );

        });

    updateProgress();

    if (
        currentSlide === 3
    ) {

        setTimeout(() => {

            initMap();

            if (schoolMap) {

                schoolMap.invalidateSize();

            }

        }, 250);
    }

    if (
        currentSlide === 4
    ) {

        renderDocuments();

    }

    if (
        currentSlide === 5
    ) {

        renderReview();

    }

    window.scrollTo({

        top: 0,

        behavior: 'smooth'

    });
}


/*
|--------------------------------------------------------------------------
| NEXT
|--------------------------------------------------------------------------
*/

async function nextStep()
{
    if (LOCKED) {

        return;
    }

    try {

        if (
            currentSlide === 1
        ) {

            await saveStudent();

        } else if (
            currentSlide === 2
        ) {

            await saveParents();

        } else if (
            currentSlide === 3
        ) {

            await savePath();

        } else if (
            currentSlide === 4
        ) {

            const ok =
                await validateDocuments();

            if (!ok) {

                return;
            }
        }

        currentSlide++;

        if (
            currentSlide > 5
        ) {

            currentSlide = 5;
        }

        updateSlide();

    } catch (error) {

        console.error(error);

        alertError(

            error.message ||
            'Terjadi kesalahan.'

        );
    }
}


/*
|--------------------------------------------------------------------------
| PREVIOUS
|--------------------------------------------------------------------------
*/

function prevStep()
{
    if (
        currentSlide <= 1
    ) {

        return;
    }

    currentSlide--;

    updateSlide();
}


/*
|--------------------------------------------------------------------------
| SAVE SISWA
|--------------------------------------------------------------------------
*/

async function saveStudent()
{
    const fd =
        new FormData();

    fd.append(
        'action',
        'save_siswa'
    );

    fd.append(
        'jenis_kelamin',
        document.getElementById(
            'jenis_kelamin'
        ).value
    );

    fd.append(
        'tempat_lahir',
        document.getElementById(
            'tempat_lahir'
        ).value
    );

    fd.append(
        'tanggal_lahir',
        document.getElementById(
            'tanggal_lahir'
        ).value
    );

    fd.append(
        'agama',
        document.getElementById(
            'agama'
        ).value
    );

    fd.append(
        'no_kk',
        document.getElementById(
            'no_kk'
        ).value
    );

    fd.append(
        'alamat',
        document.getElementById(
            'alamat'
        ).value
    );

    fd.append(
        'rt',
        document.getElementById(
            'rt'
        ).value
    );

    fd.append(
        'rw',
        document.getElementById(
            'rw'
        ).value
    );

    fd.append(
        'desa_kelurahan',
        document.getElementById(
            'desa_kelurahan'
        ).value
    );

    fd.append(
        'kecamatan',
        document.getElementById(
            'kecamatan'
        ).value
    );

    fd.append(
        'kabupaten_kota',
        document.getElementById(
            'kabupaten_kota'
        ).value
    );

    fd.append(
        'provinsi',
        document.getElementById(
            'provinsi'
        ).value
    );

    fd.append(
        'asal_sekolah',
        document.getElementById(
            'asal_sekolah'
        ).value
    );

    fd.append(
        'npsn_sekolah_asal',
        document.getElementById(
            'npsn_sekolah_asal'
        ).value
    );

    const data =
        await postData(fd);

    if (!data.success) {

        throw new Error(
            data.message
        );
    }

    await alertSuccess(
        data.message
    );
}


/*
|--------------------------------------------------------------------------
| SAVE ORANG TUA
|--------------------------------------------------------------------------
*/

async function saveParents()
{
    const fd =
        new FormData();

    fd.append(
        'action',
        'save_orangtua'
    );

    const fields = [

        'ayah_nama',
        'ayah_nik',
        'ayah_pekerjaan',
        'ayah_penghasilan',
        'ayah_no_hp',

        'ibu_nama',
        'ibu_nik',
        'ibu_pekerjaan',
        'ibu_penghasilan',
        'ibu_no_hp',

        'wali_nama',
        'wali_nik',
        'wali_pekerjaan',
        'wali_penghasilan',
        'wali_no_hp'

    ];

    fields.forEach(field => {

        const element =
            document.getElementById(
                field
            );

        fd.append(

            field,

            element
                ? element.value
                : ''

        );

    });

    const data =
        await postData(fd);

    if (!data.success) {

        throw new Error(
            data.message
        );
    }

    await alertSuccess(
        data.message
    );
}


/*
|--------------------------------------------------------------------------
| SELECT PATH
|--------------------------------------------------------------------------
*/

function selectPath(path)
{
    document
        .querySelectorAll(
            '.path-card'
        )
        .forEach(card => {

            card.classList.remove(
                'selected'
            );

        });

    const radio =
        document.querySelector(
            `input[name="jalur"][value="${path}"]`
        );

    if (radio) {

        radio.checked = true;

        radio
            .closest(
                '.path-card'
            )
            .classList.add(
                'selected'
            );
    }

    const mapSection =
        document.getElementById(
            'domisiliMapSection'
        );

    if (
        path === 'domisili'
    ) {

        mapSection.classList.remove(
            'hidden'
        );

        setTimeout(() => {

            initMap();

            if (schoolMap) {

                schoolMap.invalidateSize();

            }

        }, 200);

    } else {

        mapSection.classList.add(
            'hidden'
        );
    }
}


/*
|--------------------------------------------------------------------------
| SAVE PATH
|--------------------------------------------------------------------------
*/

async function savePath()
{
    const selected =
        document.querySelector(
            'input[name="jalur"]:checked'
        );

    if (!selected) {

        throw new Error(
            'Silakan pilih jalur pendaftaran.'
        );
    }

    const jalur =
        selected.value;

    const fd =
        new FormData();

    fd.append(
        'action',
        'save_jalur'
    );

    fd.append(
        'jalur',
        jalur
    );

    fd.append(
        'jarak_domisili_km',
        document.getElementById(
            'jarak_domisili_km'
        ).value
    );

    fd.append(
        'latitude_rumah',
        document.getElementById(
            'latitude_rumah'
        ).value
    );

    fd.append(
        'longitude_rumah',
        document.getElementById(
            'longitude_rumah'
        ).value
    );

    const data =
        await postData(fd);

    if (!data.success) {

        throw new Error(
            data.message
        );
    }

    await alertSuccess(
        data.message
    );
}


/*
|--------------------------------------------------------------------------
| INIT MAP
|--------------------------------------------------------------------------
*/

function initMap()
{
    if (schoolMap) {

        return;
    }

    const mapElement =
        document.getElementById(
            'schoolMap'
        );

    if (!mapElement) {

        return;
    }

    schoolMap =
        L.map(
            'schoolMap',
            {
                zoomControl: true
            }
        ).setView(

            [
                SCHOOL_LAT,
                SCHOOL_LNG
            ],

            15

        );

    L.tileLayer(

        'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',

        {
            maxZoom: 19,
            attribution:
                '&copy; OpenStreetMap contributors'
        }

    ).addTo(
        schoolMap
    );


    schoolMarker =
        L.marker([

            SCHOOL_LAT,
            SCHOOL_LNG

        ])
        .addTo(
            schoolMap
        )
        .bindPopup(

            '<strong><?= h(
                $schoolName
            ) ?></strong><br>' +

            'Lokasi sekolah'

        );


    const oldLat =
        parseFloat(

            document.getElementById(
                'latitude_rumah'
            ).value

        );

    const oldLng =
        parseFloat(

            document.getElementById(
                'longitude_rumah'
            ).value

        );

    if (
        !isNaN(oldLat) &&
        !isNaN(oldLng)
    ) {

        setHomeLocation(

            oldLat,
            oldLng,
            false

        );
    }


    schoolMap.on(

        'click',

        function(event) {

            if (LOCKED) {

                return;
            }

            setHomeLocation(

                event.latlng.lat,
                event.latlng.lng,
                true

            );

        }

    );
}


/*
|--------------------------------------------------------------------------
| SET HOME LOCATION
|--------------------------------------------------------------------------
*/

async function setHomeLocation(
    lat,
    lng,
    calculateRoute = true
)
{
    if (!schoolMap) {

        return;
    }

    if (
        lat < -90 ||
        lat > 90 ||
        lng < -180 ||
        lng > 180
    ) {

        alertError(
            'Koordinat lokasi rumah tidak valid.'
        );

        return;
    }

    if (homeMarker) {

        schoolMap.removeLayer(
            homeMarker
        );
    }

    if (routeLine) {

        schoolMap.removeLayer(
            routeLine
        );
    }

    homeMarker =
        L.marker(

            [lat, lng],

            {
                draggable: false
            }

        )
        .addTo(
            schoolMap
        )
        .bindPopup(

            '<strong>Lokasi Rumah</strong><br>' +

            lat.toFixed(7) +

            ', ' +

            lng.toFixed(7)

        );

    document.getElementById(
        'latitude_rumah'
    ).value =
        lat.toFixed(7);

    document.getElementById(
        'longitude_rumah'
    ).value =
        lng.toFixed(7);

    document.getElementById(
        'latitudeDisplay'
    ).textContent =
        lat.toFixed(7);

    document.getElementById(
        'longitudeDisplay'
    ).textContent =
        lng.toFixed(7);

    if (!calculateRoute) {

        const oldDistance =
            document.getElementById(
                'jarak_domisili_km'
            ).value;

        if (oldDistance) {

            document.getElementById(
                'distanceDisplay'
            ).textContent =

                parseFloat(
                    oldDistance
                ).toFixed(2) +

                ' KM';
        }

        return;
    }

    await calculateRoadRoute(
        lat,
        lng
    );
}


/*
|--------------------------------------------------------------------------
| CALCULATE ROAD ROUTE
|--------------------------------------------------------------------------
*/

async function calculateRoadRoute(
    homeLat,
    homeLng
)
{
    const distanceDisplay =
        document.getElementById(
            'distanceDisplay'
        );

    distanceDisplay.textContent =
        'Menghitung...';

    const url =

        'https://router.project-osrm.org/route/v1/driving/' +

        SCHOOL_LNG +
        ',' +
        SCHOOL_LAT +

        ';' +

        homeLng +
        ',' +
        homeLat +

        '?overview=full&geometries=geojson';

    try {

        const response =
            await fetch(

                url,

                {
                    method: 'GET'
                }

            );

        if (!response.ok) {

            throw new Error(
                'Server rute tidak dapat diakses.'
            );
        }

        const data =
            await response.json();

        if (

            data.code !== 'Ok' ||

            !data.routes ||

            !data.routes.length

        ) {

            throw new Error(
                'Rute jalan tidak ditemukan.'
            );
        }

        const route =
            data.routes[0];

        const meters =
            Number(
                route.distance
            );

        const kilometers =
            meters / 1000;

        const rounded =
            Math.round(
                kilometers * 100
            ) / 100;

        distanceDisplay.textContent =

            rounded.toFixed(2) +
            ' KM';

        document.getElementById(
            'jarak_domisili_km'
        ).value =

            rounded.toFixed(2);

        if (routeLine) {

            schoolMap.removeLayer(
                routeLine
            );
        }

        const coordinates =
            route.geometry.coordinates;

        const latLngs =
            coordinates.map(
                coordinate => [

                    coordinate[1],
                    coordinate[0]

                ]
            );

        routeLine =
            L.polyline(

                latLngs,

                {
                    weight: 5,
                    opacity: .8
                }

            ).addTo(
                schoolMap
            );

        const bounds =
            L.latLngBounds([

                [
                    SCHOOL_LAT,
                    SCHOOL_LNG
                ],

                [
                    homeLat,
                    homeLng
                ]

            ]);

        schoolMap.fitBounds(

            bounds,

            {
                padding: [40, 40]
            }

        );

    } catch (error) {

        console.error(
            'OSRM ERROR:',
            error
        );

        distanceDisplay.textContent =
            '-- KM';

        document.getElementById(
            'jarak_domisili_km'
        ).value = '';

        alertError(

            'Jarak jalan tidak dapat dihitung. ' +

            'Pastikan lokasi rumah berada ' +
            'dekat jaringan jalan.'

        );
    }
}


/*
|--------------------------------------------------------------------------
| RENDER DOCUMENTS
|--------------------------------------------------------------------------
*/

function renderDocuments()
{
    const container =
        document.getElementById(
            'documentContainer'
        );

    const selected =
        document.querySelector(
            'input[name="jalur"]:checked'
        );

    if (!selected) {

        container.innerHTML = `

            <div class="warning-box">

                Silakan kembali ke langkah
                sebelumnya dan pilih jalur
                pendaftaran.

            </div>

        `;

        return;
    }

    const jalur =
        selected.value;

    const rules =
        documentRules[jalur];

    let html = '';

    Object.keys(rules)
        .forEach(jenis => {

            const rule =
                rules[jenis];

            const doc =
                existingDocuments[jenis];

            const hasFile =
                doc &&
                doc.file_path;

            let statusHtml = '';

            if (hasFile) {

                if (
                    doc.status === 'valid'
                ) {

                    statusHtml = `

                        <div
                            class="document-status doc-valid"
                        >

                            ✓ Dokumen sudah
                            divalidasi

                        </div>

                    `;

                } else if (
                    doc.status ===
                    'tidak_valid'
                ) {

                    statusHtml = `

                        <div
                            class="document-status doc-invalid"
                        >

                            ✕ Dokumen tidak valid

                            ${
                                doc.catatan_admin

                                    ? '<br>' +
                                      escapeHtml(
                                          doc.catatan_admin
                                      )

                                    : ''
                            }

                        </div>

                    `;

                } else {

                    statusHtml = `

                        <div
                            class="document-status doc-wait"
                        >

                            ⏳ Menunggu verifikasi

                        </div>

                    `;
                }

            } else {

                statusHtml = `

                    <div
                        class="document-status doc-invalid"
                    >

                        ${
                            rule.required

                                ? '⚠ Dokumen wajib diupload'

                                : 'Dokumen opsional'
                        }

                    </div>

                `;
            }

            html += `

                <div class="document-card">

                    <h3>

                        ${escapeHtml(
                            rule.label
                        )}

                        ${
                            rule.required

                                ? '<span class="required">*</span>'

                                : '<span class="optional">(Opsional)</span>'
                        }

                    </h3>

                    ${statusHtml}

                    ${
                        hasFile

                            ? `

                                <div
                                    style="margin-bottom:12px;"
                                >

                                    <a
                                        href="${escapeHtml(
                                            doc.file_path
                                        )}"
                                        target="_blank"
                                        rel="noopener"
                                    >

                                        📄 Lihat Dokumen

                                    </a>

                                </div>

                            `

                            : ''
                    }

                    ${
                        LOCKED

                            ? ''

                            : `

                                <input
                                    type="file"
                                    id="file_${escapeHtml(
                                        jenis
                                    )}"
                                    accept=".pdf,.jpg,.jpeg,.png"
                                >

                                <button
                                    type="button"
                                    class="btn btn-primary"
                                    style="margin-top:10px;"
                                    onclick="uploadDocument('${escapeHtml(
                                        jenis
                                    )}')"
                                >

                                    ${
                                        hasFile

                                            ? 'Ganti Dokumen'

                                            : 'Upload Dokumen'
                                    }

                                </button>

                            `
                    }

                </div>

            `;
        });

    container.innerHTML =
        html;
}


/*
|--------------------------------------------------------------------------
| UPLOAD DOCUMENT
|--------------------------------------------------------------------------
*/

async function uploadDocument(jenis)
{
    if (LOCKED) {

        return;
    }

    const input =
        document.getElementById(
            'file_' + jenis
        );

    if (
        !input ||
        !input.files.length
    ) {

        alertError(
            'Silakan pilih file terlebih dahulu.'
        );

        return;
    }

    const fd =
        new FormData();

    fd.append(
        'action',
        'upload_dokumen'
    );

    fd.append(
        'jenis_dokumen',
        jenis
    );

    fd.append(
        'file',
        input.files[0]
    );

    try {

        const data =
            await postData(fd);

        if (!data.success) {

            alertError(
                data.message
            );

            return;
        }

        await alertSuccess(
            data.message
        );

        location.reload();

    } catch (error) {

        alertError(
            error.message
        );
    }
}


/*
|--------------------------------------------------------------------------
| VALIDATE DOCUMENTS
|--------------------------------------------------------------------------
*/

async function validateDocuments()
{
    const selected =
        document.querySelector(
            'input[name="jalur"]:checked'
        );

    if (!selected) {

        alertError(
            'Jalur pendaftaran belum dipilih.'
        );

        return false;
    }

    const jalur =
        selected.value;

    const rules =
        documentRules[jalur];

    for (
        const jenis
        of Object.keys(rules)
    ) {

        const rule =
            rules[jenis];

        if (!rule.required) {

            continue;
        }

        const doc =
            existingDocuments[jenis];

        if (
            !doc ||
            !doc.file_path
        ) {

            alertError(

                'Dokumen wajib belum diupload: ' +
                rule.label

            );

            return false;
        }
    }

    return true;
}


/*
|--------------------------------------------------------------------------
| REVIEW
|--------------------------------------------------------------------------
*/

function renderReview()
{
    const container =
        document.getElementById(
            'reviewContainer'
        );

    const selected =
        document.querySelector(
            'input[name="jalur"]:checked'
        );

    const jalur =
        selected
            ? selected.value
            : 'Belum dipilih';

    const jalurLabel = {

        domisili: 'Domisili',

        afirmasi: 'Afirmasi',

        prestasi: 'Prestasi',

        mutasi: 'Mutasi'

    };

    const nama =
        <?= json_encode(
            $user['nama_lengkap'] ?? ''
        ) ?>;

    const nisn =
        <?= json_encode(
            $user['nisn'] ?? ''
        ) ?>;

    const nik =
        <?= json_encode(
            $user['nik'] ?? ''
        ) ?>;

    let locationHtml = '';

    if (
        jalur === 'domisili'
    ) {

        locationHtml = `

            <tr>

                <td>
                    Jarak Rumah
                </td>

                <td>

                    <strong>

                        ${
                            document.getElementById(
                                'jarak_domisili_km'
                            ).value || '-'
                        }
                        KM

                    </strong>

                </td>

            </tr>


            <tr>

                <td>
                    Latitude Rumah
                </td>

                <td>

                    ${
                        document.getElementById(
                            'latitude_rumah'
                        ).value || '-'
                    }

                </td>

            </tr>


            <tr>

                <td>
                    Longitude Rumah
                </td>

                <td>

                    ${
                        document.getElementById(
                            'longitude_rumah'
                        ).value || '-'
                    }

                </td>

            </tr>

        `;
    }

    container.innerHTML = `

        <table class="review-table">

            <tr>

                <td>
                    NISN
                </td>

                <td>
                    ${escapeHtml(nisn)}
                </td>

            </tr>


            <tr>

                <td>
                    NIK
                </td>

                <td>
                    ${escapeHtml(nik)}
                </td>

            </tr>


            <tr>

                <td>
                    Nama Lengkap
                </td>

                <td>
                    ${escapeHtml(nama)}
                </td>

            </tr>


            <tr>

                <td>
                    Jenis Kelamin
                </td>

                <td>

                    ${escapeHtml(

                        document.getElementById(
                            'jenis_kelamin'
                        ).value

                    )}

                </td>

            </tr>


            <tr>

                <td>
                    Tempat / Tanggal Lahir
                </td>

                <td>

                    ${escapeHtml(

                        document.getElementById(
                            'tempat_lahir'
                        ).value

                    )}

                    /

                    ${escapeHtml(

                        document.getElementById(
                            'tanggal_lahir'
                        ).value

                    )}

                </td>

            </tr>


            <tr>

                <td>
                    Asal Sekolah
                </td>

                <td>

                    ${escapeHtml(

                        document.getElementById(
                            'asal_sekolah'
                        ).value

                    )}

                </td>

            </tr>


            <tr>

                <td>
                    Jalur Pendaftaran
                </td>

                <td>

                    <strong>

                        ${
                            jalurLabel[jalur] ||
                            jalur
                        }

                    </strong>

                </td>

            </tr>


            ${locationHtml}

        </table>

    `;
}


/*
|--------------------------------------------------------------------------
| SUBMIT REGISTRATION
|--------------------------------------------------------------------------
*/

async function submitRegistration()
{
    if (LOCKED) {

        return;
    }

    const statement =
        document.getElementById(
            'pernyataan'
        );

    if (!statement.checked) {

        alertError(
            'Anda harus menyetujui pernyataan terlebih dahulu.'
        );

        return;
    }

    const confirm =
        await Swal.fire({

            icon: 'warning',

            title:
                'Kirim pendaftaran?',

            text:
                'Setelah dikirim, data tidak dapat diedit selama proses verifikasi.',

            showCancelButton: true,

            confirmButtonText:
                'Ya, Kirim',

            cancelButtonText:
                'Periksa Lagi'

        });

    if (
        !confirm.isConfirmed
    ) {

        return;
    }

    try {

        const fd =
            new FormData();

        fd.append(
            'action',
            'submit_pendaftaran'
        );

        fd.append(
            'pernyataan',
            '1'
        );

        const data =
            await postData(fd);

        if (!data.success) {

            alertError(
                data.message
            );

            return;
        }

        await Swal.fire({

            icon: 'success',

            title:
                'Pendaftaran Berhasil',

            html:

                'Pendaftaran Anda berhasil dikirim.<br><br>' +

                '<strong>Nomor Pendaftaran:</strong><br>' +

                '<span style="font-size:20px;">' +

                escapeHtml(
                    data.nomor_pendaftaran
                ) +

                '</span>',

            confirmButtonText:
                'OK'

        });

        location.reload();

    } catch (error) {

        console.error(error);

        alertError(
            error.message
        );
    }
}


/*
|--------------------------------------------------------------------------
| ESCAPE HTML
|--------------------------------------------------------------------------
*/

function escapeHtml(value)
{
    return String(
        value ?? ''
    )
    .replace(
        /&/g,
        '&amp;'
    )
    .replace(
        /</g,
        '&lt;'
    )
    .replace(
        />/g,
        '&gt;'
    )
    .replace(
        /"/g,
        '&quot;'
    )
    .replace(
        /'/g,
        '&#039;'
    );
}


/*
|--------------------------------------------------------------------------
| NUMERIC INPUT
|--------------------------------------------------------------------------
*/

document.addEventListener(

    'input',

    function(event) {

        const id =
            event.target.id;

        const numericFields = [

            'no_kk',

            'ayah_nik',

            'ibu_nik',

            'wali_nik',

            'ayah_no_hp',

            'ibu_no_hp',

            'wali_no_hp',

            'npsn_sekolah_asal'

        ];

        if (
            numericFields.includes(id)
        ) {

            event.target.value =

                event.target.value
                    .replace(
                        /[^0-9]/g,
                        ''
                    );
        }

    }

);


/*
|--------------------------------------------------------------------------
| WALI CLEAR
|--------------------------------------------------------------------------
*/

document.addEventListener(

    'DOMContentLoaded',

    function() {

        const waliNama =
            document.getElementById(
                'wali_nama'
            );

        if (waliNama) {

            waliNama.addEventListener(

                'blur',

                function() {

                    if (
                        this.value.trim() === ''
                    ) {

                        document.getElementById(
                            'wali_nik'
                        ).value = '';

                        document.getElementById(
                            'wali_pekerjaan'
                        ).value = '';

                        document.getElementById(
                            'wali_penghasilan'
                        ).value = '';

                        document.getElementById(
                            'wali_no_hp'
                        ).value = '';

                    }

                }

            );
        }


        /*
        |--------------------------------------------------------------------------
        | INITIAL PATH
        |--------------------------------------------------------------------------
        */

        const selected =
            document.querySelector(
                'input[name="jalur"]:checked'
            );

        if (selected) {

            selectPath(
                selected.value
            );
        }

        updateSlide();

    }

);

</script>

<?php endif; ?>

</body>

</html>
