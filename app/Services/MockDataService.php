<?php

namespace App\Services;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class MockDataService
{
    /**
     * Helper membuat LengthAwarePaginator dari array
     */
    public static function paginate(array $items, int $perPage = 10): LengthAwarePaginator
    {
        $page = (int) request()->get('page', 1);
        $offset = ($page - 1) * $perPage;
        return new LengthAwarePaginator(
            array_slice($items, $offset, $perPage),
            count($items),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );
    }

    /**
     * Daftar Lokasi Sasaran BNN Surabaya
     */
    public static function getLocations(): array
    {
        return [
            (object)[
                'id'            => 1,
                'nama_lokasi'   => 'SMAN 1 Surabaya',
                'alamat'        => 'Jl. Wijaya Kusuma No. 48',
                'kecamatan'     => 'Genteng',
                'jenis_sasaran' => 'sekolah',
                'events_count'  => 3,
                'created_at'    => Carbon::parse('2026-08-01'),
            ],
            (object)[
                'id'            => 2,
                'nama_lokasi'   => 'SMAN 5 Surabaya',
                'alamat'        => 'Jl. Kusuma Bangsa No. 21',
                'kecamatan'     => 'Genteng',
                'jenis_sasaran' => 'sekolah',
                'events_count'  => 2,
                'created_at'    => Carbon::parse('2026-08-05'),
            ],
            (object)[
                'id'            => 3,
                'nama_lokasi'   => 'SMKN 2 Surabaya',
                'alamat'        => 'Jl. Tentara Genie Pelajar No. 26',
                'kecamatan'     => 'Sawahan',
                'jenis_sasaran' => 'sekolah',
                'events_count'  => 2,
                'created_at'    => Carbon::parse('2026-08-10'),
            ],
            (object)[
                'id'            => 4,
                'nama_lokasi'   => 'Kecamatan Tegalsari',
                'alamat'        => 'Jl. Tanggulangin No. 12',
                'kecamatan'     => 'Tegalsari',
                'jenis_sasaran' => 'masyarakat',
                'events_count'  => 2,
                'created_at'    => Carbon::parse('2026-08-15'),
            ],
            (object)[
                'id'            => 5,
                'nama_lokasi'   => 'Kelurahan Jambangan',
                'alamat'        => 'Jl. Jambangan No. 80',
                'kecamatan'     => 'Jambangan',
                'jenis_sasaran' => 'masyarakat',
                'events_count'  => 1,
                'created_at'    => Carbon::parse('2026-08-20'),
            ],
            (object)[
                'id'            => 6,
                'nama_lokasi'   => 'Aula PDAM Surya Sembada',
                'alamat'        => 'Jl. Mayjen Prof. Dr. Moestopo No. 2',
                'kecamatan'     => 'Tambaksari',
                'jenis_sasaran' => 'instansi',
                'events_count'  => 2,
                'created_at'    => Carbon::parse('2026-08-25'),
            ],
            (object)[
                'id'            => 7,
                'nama_lokasi'   => 'Lapas Kelas I Surabaya',
                'alamat'        => 'Jl. Pemasyarakatan No. 1, Porong',
                'kecamatan'     => 'Porong',
                'jenis_sasaran' => 'lapas',
                'events_count'  => 1,
                'created_at'    => Carbon::parse('2026-09-01'),
            ],
            (object)[
                'id'            => 8,
                'nama_lokasi'   => 'Universitas Airlangga (Kampus B)',
                'alamat'        => 'Jl. Dharmawangsa Dalam',
                'kecamatan'     => 'Gubeng',
                'jenis_sasaran' => 'kampus',
                'events_count'  => 1,
                'created_at'    => Carbon::parse('2026-09-05'),
            ],
        ];
    }

    /**
     * Butir Pertanyaan untuk Paket Soal
     */
    public static function getQuestionsForPackage(int $packageId): array
    {
        $raw = [
            [
                'id'         => 1,
                'nomor'      => 1,
                'pertanyaan' => 'Apa kepanjangan dari singkatan P4GN yang dicanangkan oleh BNN?',
                'pilihan_a'  => 'Pencegahan, Pemberantasan, Penyalahgunaan dan Peredaran Gelap Narkotika',
                'pilihan_b'  => 'Pengawasan, Penindakan, Pengobatan dan Rehabilitasi Narkotika',
                'pilihan_c'  => 'Penyuluhan, Pemulihan, Penertiban dan Pengawasan Narkotika',
                'pilihan_d'  => 'Pencegahan, Penuntutan, Pengadilan dan Pemasyarakatan Narkotika',
                'kunci'      => 'A',
                'bobot'      => 10,
            ],
            [
                'id'         => 2,
                'nomor'      => 2,
                'pertanyaan' => 'Zat atau obat yang dapat menurunkan kesadaran, menghilangkan rasa nyeri, dan menimbulkan ketergantungan disebut...',
                'pilihan_a'  => 'Psikotropika',
                'pilihan_b'  => 'Narkotika',
                'pilihan_c'  => 'Zat Adiktif Non-Narkotika',
                'pilihan_d'  => 'Minuman Keras',
                'kunci'      => 'B',
                'bobot'      => 10,
            ],
            [
                'id'         => 3,
                'nomor'      => 3,
                'pertanyaan' => 'Berikut ini yang BUKAN merupakan dampak buruk penyalahgunaan narkotika bagi kesehatan fisik adalah...',
                'pilihan_a'  => 'Kerusakan organ hati dan ginjal',
                'pilihan_b'  => 'Peningkatan konsentrasi dan stamina permanen',
                'pilihan_c'  => 'Gangguan pada sistem saraf pusat',
                'pilihan_d'  => 'Kerentanan tertular HIV/AIDS dan Hepatitis',
                'kunci'      => 'B',
                'bobot'      => 10,
            ],
            [
                'id'         => 4,
                'nomor'      => 4,
                'pertanyaan' => 'Berdasarkan Undang-Undang No. 35 Tahun 2009, Narkotika Golongan I hanya dapat digunakan untuk kepentingan...',
                'pilihan_a'  => 'Pelayanan kesehatan rawat jalan',
                'pilihan_b'  => 'Pengembangan ilmu pengetahuan dan teknologi serta reagensia diagnostik',
                'pilihan_c'  => 'Pengobatan penyakit saraf stadium akhir',
                'pilihan_d'  => 'Terapi psikologis dan penenang mandiri',
                'kunci'      => 'B',
                'bobot'      => 10,
            ],
            [
                'id'         => 5,
                'nomor'      => 5,
                'pertanyaan' => 'Tindakan awal yang paling tepat jika mengetahui teman sebaya menunjukkan gelagat penyalahgunaan narkoba adalah...',
                'pilihan_a'  => 'Menjauhi dan menyebarkan rumor ke seluruh sekolah',
                'pilihan_b'  => 'Melaporkan kepada guru BK atau petugas BNN untuk rehabilitasi',
                'pilihan_c'  => 'Mencoba ikut mengonsumsi agar tahu rasanya',
                'pilihan_d'  => 'Menghakimi secara sepihak di media sosial',
                'kunci'      => 'B',
                'bobot'      => 10,
            ],
            [
                'id'         => 6,
                'nomor'      => 6,
                'pertanyaan' => 'Layanan rehabilitasi BNN bagi korban penyalahguna narkotika bersifat...',
                'pilihan_a'  => 'Dipidana dan dikenai denda kurungan',
                'pilihan_b'  => 'Gratis, manusiawi, rahasia, dan tanpa hukuman pidana jika melapor sukarela',
                'pilihan_c'  => 'Dipublikasikan ke khalayak ramai sebagai sanksi sosial',
                'pilihan_d'  => 'Wajib membayar biaya rawat inap ratusan juta rupiah',
                'kunci'      => 'B',
                'bobot'      => 10,
            ],
            [
                'id'         => 7,
                'nomor'      => 7,
                'pertanyaan' => 'Narkotika jenis sabu-sabu (metamfetamin) termasuk dalam kategori stimulan yang bekerja dengan cara...',
                'pilihan_a'  => 'Menekan kerja sistem saraf dan menimbulkan kantuk',
                'pilihan_b'  => 'Mempercepat kerja sistem saraf pusat dan detak jantung',
                'pilihan_c'  => 'Membuat pengguna melihat halusinasi yang tidak nyata',
                'pilihan_d'  => 'Menstabilkan emosi secara alami',
                'kunci'      => 'B',
                'bobot'      => 10,
            ],
            [
                'id'         => 8,
                'nomor'      => 8,
                'pertanyaan' => 'Program Desa/Kelurahan Bersih Narkoba yang digalakkan BNN dikenal dengan istilah...',
                'pilihan_a'  => 'Desa Berseri',
                'pilihan_b'  => 'Kelurahan Bersinar',
                'pilihan_c'  => 'Kampung Tangguh Sehat',
                'pilihan_d'  => 'Lingkungan Madani',
                'kunci'      => 'B',
                'bobot'      => 10,
            ],
            [
                'id'         => 9,
                'nomor'      => 9,
                'pertanyaan' => 'Nomor call center darurat layanan pengaduan dan informasi BNN adalah...',
                'pilihan_a'  => '110',
                'pilihan_b'  => '112',
                'pilihan_c'  => '184',
                'pilihan_d'  => '119',
                'kunci'      => 'C',
                'bobot'      => 10,
            ],
            [
                'id'         => 10,
                'nomor'      => 10,
                'pertanyaan' => 'Faktor pemicu utama seorang remaja terjerumus dalam penyalahgunaan narkoba dari sisi psikososial adalah...',
                'pilihan_a'  => 'Tekanan teman sebaya dan keinginan coba-coba',
                'pilihan_b'  => 'Kecukupan gizi makanan keluarga',
                'pilihan_c'  => 'Prestasi akademik yang terlalu tinggi',
                'pilihan_d'  => 'Kegiatan ekstrakurikuler yang padat',
                'kunci'      => 'A',
                'bobot'      => 10,
            ],
        ];

        return array_map(function($q) {
            return (object)[
                'id'            => $q['id'],
                'nomor'         => $q['nomor'],
                'pertanyaan'    => $q['pertanyaan'],
                'pilihan_a'     => $q['pilihan_a'],
                'pilihan_b'     => $q['pilihan_b'],
                'pilihan_c'     => $q['pilihan_c'],
                'pilihan_d'     => $q['pilihan_d'],
                'kunci'         => $q['kunci'],
                'kunci_jawaban' => $q['kunci'],
                'opsi'          => [
                    'A' => $q['pilihan_a'],
                    'B' => $q['pilihan_b'],
                    'C' => $q['pilihan_c'],
                    'D' => $q['pilihan_d'],
                ],
                'bobot'         => $q['bobot'],
            ];
        }, $raw);
    }

    /**
     * Daftar Paket Bank Soal
     */
    public static function getQuestionPackages(): array
    {
        $questions1 = self::getQuestionsForPackage(1);
        $questions2 = self::getQuestionsForPackage(2);

        return [
            (object)[
                'id'              => 1,
                'nama_paket'      => 'Instrumen Pre-Test P4GN Remaja & Pelajar (SMA/SMK)',
                'tipe'            => 'pretest',
                'durasi'          => 30,
                'deskripsi'       => 'Paket pengukuran awal tingkat pengetahuan bahaya narkoba bagi pelajar.',
                'acak_urutan'     => true,
                'soal_count'      => count($questions1),
                'questions_count' => count($questions1),
                'events_count'    => 4,
                'questions'       => collect($questions1),
                'created_at'      => Carbon::parse('2026-08-10'),
            ],
            (object)[
                'id'              => 2,
                'nama_paket'      => 'Instrumen Post-Test P4GN Remaja & Pelajar (SMA/SMK)',
                'tipe'            => 'posttest',
                'durasi'          => 30,
                'deskripsi'       => 'Paket evaluasi akhir setelah materi sosialisasi bahaya narkoba.',
                'acak_urutan'     => true,
                'soal_count'      => count($questions2),
                'questions_count' => count($questions2),
                'events_count'    => 4,
                'questions'       => collect($questions2),
                'created_at'      => Carbon::parse('2026-08-10'),
            ],
            (object)[
                'id'              => 3,
                'nama_paket'      => 'Evaluasi Ketahanan Keluarga Anti Narkoba',
                'tipe'            => 'kombinasi',
                'durasi'          => 25,
                'deskripsi'       => 'Paket evaluasi komprehensif peran orang tua dan keluarga.',
                'acak_urutan'     => false,
                'soal_count'      => 8,
                'questions_count' => 8,
                'events_count'    => 2,
                'questions'       => collect([]),
                'created_at'      => Carbon::parse('2026-08-15'),
            ],
            (object)[
                'id'              => 4,
                'nama_paket'      => 'Sosialisasi Lingkungan Kerja Bebas Narkoba (BUMD/Swasta)',
                'tipe'            => 'kombinasi',
                'durasi'          => 30,
                'deskripsi'       => 'Evaluasi kesadaran K3 dan regulasi P4GN di lingkungan instansi.',
                'acak_urutan'     => true,
                'soal_count'      => 10,
                'questions_count' => 10,
                'events_count'    => 2,
                'questions'       => collect([]),
                'created_at'      => Carbon::parse('2026-08-20'),
            ],
        ];
    }

    /**
     * Daftar Kegiatan P2M Lengkap
     */
    public static function getEvents(): array
    {
        $locations = self::getLocations();
        $packages  = self::getQuestionPackages();

        return [
            (object)[
                'id'                  => 1,
                'nama_kegiatan'       => 'Sosialisasi P4GN SMAN 1 Surabaya',
                'kode_join'           => 'SMAN01',
                'status'              => 'selesai',
                'tanggal'             => '2026-09-05',
                'durasi_menit'        => 30,
                'catatan'             => 'Sosialisasi tatap muka interaktif diikuti 65 peserta siswa kelas X.',
                'lokasi_id'           => 1,
                'lokasi'              => $locations[0],
                'pretest_package_id'  => 1,
                'posttest_package_id' => 2,
                'pretestPackage'      => $packages[0],
                'posttestPackage'     => $packages[1],
                'peserta_count'       => 65,
                'participants_count'  => 65,
                'avg_gain'            => 0.78,
                'kategori_gain'       => 'Tinggi',
                'created_at'          => Carbon::parse('2026-09-01 08:00:00'),
            ],
            (object)[
                'id'                  => 2,
                'nama_kegiatan'       => 'Workshop Ketahanan Keluarga Anti Narkoba Kec. Tegalsari',
                'kode_join'           => 'TGLSRI',
                'status'              => 'selesai',
                'tanggal'             => '2026-09-10',
                'durasi_menit'        => 30,
                'catatan'             => 'Pemberdayaan masyarakat RW 01 s.d. RW 05 Kecamatan Tegalsari.',
                'lokasi_id'           => 4,
                'lokasi'              => $locations[3],
                'pretest_package_id'  => 3,
                'posttest_package_id' => 3,
                'pretestPackage'      => $packages[2],
                'posttestPackage'     => $packages[2],
                'peserta_count'       => 40,
                'participants_count'  => 40,
                'avg_gain'            => 0.71,
                'kategori_gain'       => 'Tinggi',
                'created_at'          => Carbon::parse('2026-09-06 09:00:00'),
            ],
            (object)[
                'id'                  => 3,
                'nama_kegiatan'       => 'Pembinaan Komunitas Pemuda Bersinar Kel. Jambangan',
                'kode_join'           => 'JMBG03',
                'status'              => 'berlangsung',
                'tanggal'             => '2026-09-14',
                'durasi_menit'        => 35,
                'catatan'             => 'Meja kerja sedang aktif untuk penginputan lembar OMR peserta.',
                'lokasi_id'           => 5,
                'lokasi'              => $locations[4],
                'pretest_package_id'  => 1,
                'posttest_package_id' => 2,
                'pretestPackage'      => $packages[0],
                'posttestPackage'     => $packages[1],
                'peserta_count'       => 80,
                'participants_count'  => 80,
                'avg_gain'            => 0.68,
                'kategori_gain'       => 'Sedang',
                'created_at'          => Carbon::parse('2026-09-12 10:00:00'),
            ],
            (object)[
                'id'                  => 4,
                'nama_kegiatan'       => 'Sosialisasi Bahaya Narkoba Lingkungan Kerja PDAM Surya Sembada',
                'kode_join'           => 'PDAM04',
                'status'              => 'dijadwalkan',
                'tanggal'             => '2026-09-22',
                'durasi_menit'        => 30,
                'catatan'             => 'Diselenggarakan di Aula Lantai 3 PDAM Surabaya.',
                'lokasi_id'           => 6,
                'lokasi'              => $locations[5],
                'pretest_package_id'  => 4,
                'posttest_package_id' => 4,
                'pretestPackage'      => $packages[3],
                'posttestPackage'     => $packages[3],
                'peserta_count'       => 50,
                'participants_count'  => 50,
                'avg_gain'            => null,
                'kategori_gain'       => '—',
                'created_at'          => Carbon::parse('2026-09-15 11:00:00'),
            ],
            (object)[
                'id'                  => 5,
                'nama_kegiatan'       => 'Deteksi Dini & Pembinaan Warga Binaan Lapas Kelas I Surabaya',
                'kode_join'           => 'LAPS05',
                'status'              => 'dijadwalkan',
                'tanggal'             => '2026-09-28',
                'durasi_menit'        => 45,
                'catatan'             => 'Penyuluhan dan skrining berkala P2M.',
                'lokasi_id'           => 7,
                'lokasi'              => $locations[6],
                'pretest_package_id'  => 1,
                'posttest_package_id' => 2,
                'pretestPackage'      => $packages[0],
                'posttestPackage'     => $packages[1],
                'peserta_count'       => 120,
                'participants_count'  => 120,
                'avg_gain'            => null,
                'kategori_gain'       => '—',
                'created_at'          => Carbon::parse('2026-09-16 13:00:00'),
            ],
        ];
    }

    /**
     * Cari Kegiatan Berdasarkan ID
     */
    public static function getEventById($id)
    {
        $events = self::getEvents();
        foreach ($events as $e) {
            if ($e->id == $id) {
                return $e;
            }
        }
        return $events[0];
    }

    /**
     * Daftar Peserta untuk Kegiatan Tertentu
     */
    public static function getParticipantsForEvent($eventId): Collection
    {
        $names = [
            'Aditya Pratama Putra', 'Aisyah Nur Rahmawati', 'Bima Satria Wicaksana',
            'Cantika Dewi Lestari', 'Dimas Bagus Pangestu', 'Eka Putri Handayani',
            'Fajar Nugraha Ramadhan', 'Gita Permata Sari', 'Hafizh Al-Farizi',
            'Indah Kusuma Wardani', 'Jovan Ardiansyah', 'Keisha Amanda Putri',
            'Lukman Hakim', 'Maya Anggraini', 'Naufal Rizky Fauzan',
            'Olivia Salsabila', 'Pandu Dwi Saputra', 'Qonita Salma Azzahra',
            'Rangga Raditya', 'Siti Fatimah Azzahra'
        ];

        $classes = ['X MIPA 1', 'X MIPA 2', 'X MIPA 3', 'X IPS 1', 'X IPS 2', 'X IPS 3'];
        $methods = ['omr', 'online', 'manual'];

        $list = [];
        foreach ($names as $idx => $name) {
            $pre  = 30 + (($idx * 7) % 45);
            $post = min(100, $pre + 25 + (($idx * 11) % 40));
            $gain = round(($post - $pre) / (100 - $pre), 2);
            $categoryKey = $gain >= 0.70 ? 'paham' : ($gain >= 0.30 ? 'cukup' : 'kurang');
            $kategoriLabel = $gain >= 0.70 ? 'Paham (Tinggi)' : ($gain >= 0.30 ? 'Cukup (Sedang)' : 'Kurang (Rendah)');

            $list[] = (object)[
                'id'             => 100 + $idx + 1,
                'event_id'       => $eventId,
                'name'           => $name,
                'class_grade'    => $classes[$idx % count($classes)],
                'pretest_score'  => $pre,
                'posttest_score' => $post,
                'n_gain'         => $gain,
                'category'       => $categoryKey,
                'kategori'       => $kategoriLabel,
                'status'         => 'selesai',
                'input_method'   => $methods[$idx % count($methods)],
            ];
        }

        return collect($list);
    }

    /**
     * Statistik Detail Kegiatan
     */
    public static function getEventStats($eventId): array
    {
        $participants = self::getParticipantsForEvent($eventId);
        $total = $participants->count();
        $avgPre = $participants->avg('pretest_score');
        $avgPost = $participants->avg('posttest_score');
        $avgGain = $participants->avg('n_gain');

        return [
            'total'         => $total,
            'avg_pre'       => round($avgPre, 1),
            'avg_post'      => round($avgPost, 1),
            'avg_gain'      => round($avgGain, 2),
            'selesai_count' => $total,
        ];
    }

    /**
     * Daftar Staf Pengguna
     */
    public static function getUsers(): array
    {
        return [
            (object)[
                'id'            => 1,
                'name'          => 'Super Admin BNN',
                'email'         => 'superadmin@bnn.go.id',
                'role'          => 'superadmin',
                'nip'           => '197905142005011002',
                'jabatan'       => 'Kepala Seksi P2M BNN Kota Surabaya',
                'is_active'     => true,
                'last_login_at' => Carbon::parse('2026-09-18 14:20:15'),
            ],
            (object)[
                'id'            => 2,
                'name'          => 'Wahyu Prasetyo',
                'email'         => 'wahyu@bnnsurabaya.go.id',
                'role'          => 'operator',
                'nip'           => '199208212019031005',
                'jabatan'       => 'Penyuluh Narkoba Ahli Pertama',
                'is_active'     => true,
                'last_login_at' => Carbon::parse('2026-09-18 15:10:00'),
            ],
            (object)[
                'id'            => 3,
                'name'          => 'Rizky Maulana (Magang)',
                'email'         => 'rizky.magang@mhs.unair.ac.id',
                'role'          => 'magang',
                'nip'           => 'NIM. 152111513042',
                'jabatan'       => 'Mahasiswa Magang Sistem Informasi',
                'is_active'     => true,
                'last_login_at' => Carbon::parse('2026-09-18 13:45:00'),
            ],
            (object)[
                'id'            => 4,
                'name'          => 'Siti Aminah, S.Sos.',
                'email'         => 'siti.aminah@bnnsurabaya.go.id',
                'role'          => 'operator',
                'nip'           => '199411032020122008',
                'jabatan'       => 'Pengelola Evaluasi Program P2M',
                'is_active'     => true,
                'last_login_at' => Carbon::parse('2026-09-17 16:30:20'),
            ],
            (object)[
                'id'            => 5,
                'name'          => 'Bayu Irawan',
                'email'         => 'bayu.magang@mhs.its.ac.id',
                'role'          => 'magang',
                'nip'           => 'NIM. 5025211024',
                'jabatan'       => 'Mahasiswa Magang Teknik Informatika',
                'is_active'     => false,
                'last_login_at' => Carbon::parse('2026-09-12 11:00:00'),
            ],
        ];
    }
}
