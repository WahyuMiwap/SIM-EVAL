<?php

namespace Database\Seeders;

use App\Models\CustodyLog;
use App\Models\Event;
use App\Models\Location;
use App\Models\Participant;
use App\Models\Question;
use App\Models\QuestionPackage;
use App\Models\Scan;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Users (Roles: Super Admin, Operator, Magang — Blueprint v3.1 & v2.0)
        $superadmin = User::create([
            'name' => 'Super Admin P2M',
            'email' => 'admin@bnnsurabaya.go.id',
            'password' => Hash::make('password'),
            'role' => 'superadmin',
            'nip' => '198501152008011002',
            'jabatan' => 'Kepala Sub Bagian Umum & P2M',
            'bidang_wilayah' => 'BNN Kota Surabaya',
            'avatar' => null,
            'is_active' => true,
        ]);

        $wahyu = User::create([
            'name' => 'Wahyu',
            'email' => 'wahyu@bnnsurabaya.go.id',
            'password' => Hash::make('password'),
            'role' => 'operator',
            'nip' => '199203142015041001',
            'jabatan' => 'Penyuluh Narkoba Ahli Pertama',
            'bidang_wilayah' => 'Seksi Pencegahan dan Pemberdayaan Masyarakat (P2M)',
            'avatar' => null,
            'is_active' => true,
        ]);

        $magang1 = User::create([
            'name' => 'Bagas Pratama',
            'email' => 'bagas.magang@bnnsurabaya.go.id',
            'password' => Hash::make('password'),
            'role' => 'magang',
            'nip' => 'MGNG-2026-001',
            'jabatan' => 'Mahasiswa Magang UPN Surabaya',
            'bidang_wilayah' => 'Seksi P2M',
            'avatar' => null,
            'is_active' => true,
        ]);

        $magang2 = User::create([
            'name' => 'Dinda Kirana',
            'email' => 'dinda.magang@bnnsurabaya.go.id',
            'password' => Hash::make('password'),
            'role' => 'magang',
            'nip' => 'MGNG-2026-002',
            'jabatan' => 'Mahasiswa Magang Unair Surabaya',
            'bidang_wilayah' => 'Seksi P2M',
            'avatar' => null,
            'is_active' => true,
        ]);

        // 2. Locations (Master Lokasi — Blueprint v3.1)
        $loc1 = Location::create([
            'nama_lokasi' => 'SMA N 5 Surabaya',
            'alamat' => 'Jl. Pemuda No. 5',
            'kecamatan' => 'Genteng',
            'jenis_sasaran' => 'sekolah',
        ]);

        $loc2 = Location::create([
            'nama_lokasi' => 'SMAN 12 Surabaya',
            'alamat' => 'Jl. Semolowaru No. 45',
            'kecamatan' => 'Sukolilo',
            'jenis_sasaran' => 'sekolah',
        ]);

        $loc3 = Location::create([
            'nama_lokasi' => 'Lapas Kelas I Surabaya',
            'alamat' => 'Jl. Raya Medaeng No. 1',
            'kecamatan' => 'Waru',
            'jenis_sasaran' => 'lapas',
        ]);

        $loc4 = Location::create([
            'nama_lokasi' => 'SMA Hang Tuah 1 Sby',
            'alamat' => 'Jl. Balongsari Tama',
            'kecamatan' => 'Asemrowo',
            'jenis_sasaran' => 'sekolah',
        ]);

        $loc5 = Location::create([
            'nama_lokasi' => 'Balai RW 03 Kel. Jambangan',
            'alamat' => 'Jl. Jambangan Baru No. 12',
            'kecamatan' => 'Jambangan',
            'jenis_sasaran' => 'komunitas',
        ]);

        $loc6 = Location::create([
            'nama_lokasi' => 'Aula PDAM Surya Sembada',
            'alamat' => 'Jl. Prof. Dr. Moestopo No. 2',
            'kecamatan' => 'Tambaksari',
            'jenis_sasaran' => 'instansi',
        ]);

        // 3. Question Packages (Blueprint v2.0 Bab 8 & v3.1)
        $pkgPre = QuestionPackage::create([
            'nama_paket' => 'Pre-Test Anti Narkoba Umum',
            'tema' => 'P4GN Pelajar',
            'kategori_audiens' => 'SMA',
            'jumlah_opsi' => 4,
            'tipe' => 'pretest',
            'durasi' => 30,
            'acak_urutan' => true,
            'created_by' => $wahyu->id,
        ]);

        $pkgPost = QuestionPackage::create([
            'nama_paket' => 'Post-Test Anti Narkoba Umum',
            'tema' => 'P4GN Pelajar',
            'kategori_audiens' => 'SMA',
            'jumlah_opsi' => 4,
            'tipe' => 'posttest',
            'durasi' => 30,
            'acak_urutan' => true,
            'created_by' => $wahyu->id,
        ]);

        $pkgPelajar = QuestionPackage::create([
            'nama_paket' => 'Pre-Test P4GN Pelajar',
            'tema' => 'P4GN Pelajar',
            'kategori_audiens' => 'SD',
            'jumlah_opsi' => 4,
            'tipe' => 'pretest',
            'durasi' => 20,
            'acak_urutan' => false,
            'created_by' => $wahyu->id,
        ]);

        $pkgLapas = QuestionPackage::create([
            'nama_paket' => 'Pre-Test Lapas — Khusus',
            'tema' => 'Pembinaan Warga Binaan',
            'kategori_audiens' => 'LAPAS',
            'jumlah_opsi' => 4,
            'tipe' => 'pretest',
            'durasi' => 25,
            'acak_urutan' => false,
            'created_by' => $wahyu->id,
        ]);

        // 4. Questions (Standard 10 Butir P4GN BNN)
        $soalList = [
            [
                'pertanyaan' => 'Kepanjangan Narkoba adalah....',
                'opsi_a' => 'Narkotika Psikotropika dan Bahan yang membahayakan',
                'opsi_b' => 'Narkotika Psikotropika dan Bahan yang perlu diwaspadai',
                'opsi_c' => 'Narkotika Psikotropika dan Bahan Adiktif lainnya',
                'opsi_d' => 'Narkotika Psikotropika dan Bahan yang menyenangkan',
                'kunci' => 'C',
            ],
            [
                'pertanyaan' => 'Berikut adalah jenis-jenis Narkoba adalah...',
                'opsi_a' => 'Pil Koplo, ekstasi dan susu',
                'opsi_b' => 'Ganja, sabu dan lem',
                'opsi_c' => 'Ikan Pe, nasi dan buah jeruk',
                'opsi_d' => 'Rokok, alkohol dan Air putih',
                'kunci' => 'B',
            ],
            [
                'pertanyaan' => 'Dampak jangka panjang penyalahgunaan narkoba pada tubuh dan kesehatan adalah, Kecuali...',
                'opsi_a' => 'Kerusakan otak dan penurunan daya ingat',
                'opsi_b' => 'Kerusakan jantung, paru-paru, dan hati',
                'opsi_c' => 'Gangguan emosi, kecemasan, dan depresi',
                'opsi_d' => 'Meningkatkan kecerdasan dan kepercayaan diri',
                'kunci' => 'D',
            ],
            [
                'pertanyaan' => 'Faktor lingkungan yang dapat mendorong seseorang mencoba Narkoba adalah...',
                'opsi_a' => 'Dukungan keluarga yang harmonis',
                'opsi_b' => 'Tekanan teman sebaya dan pergaulan bebas',
                'opsi_c' => 'Mengikuti kegiatan positif di sekolah',
                'opsi_d' => 'Memiliki hobi yang bermanfaat',
                'kunci' => 'B',
            ],
            [
                'pertanyaan' => 'Sikap yang paling tepat jika ditawari Narkoba oleh teman atau orang lain adalah...',
                'opsi_a' => 'Menerima dan mencoba sedikit saja',
                'opsi_b' => 'Menolak dengan tegas dan menjauhi',
                'opsi_c' => 'Menerima lalu menyimpannya',
                'opsi_d' => 'Diam saja dan pergi tanpa berkata apa-apa',
                'kunci' => 'B',
            ],
            [
                'pertanyaan' => 'Pernyataan yang BENAR tentang obat keras dan obat bebas terbatas adalah...',
                'opsi_a' => 'Boleh dikonsumsi sesuka hati karena bukan Narkoba',
                'opsi_b' => 'Harus dengan resep/disetujui orang tua atau tenaga medis',
                'opsi_c' => 'Boleh dibeli dan diminum jika teman sedang sakit',
                'opsi_d' => 'Tidak berbahaya jika diminum melebihi dosis',
                'kunci' => 'B',
            ],
            [
                'pertanyaan' => 'Jika mengetahui teman atau orang lain menggunakan Narkoba, tindakan yang BENAR adalah...',
                'opsi_a' => 'Mengabaikannya agar tidak bermusuhan',
                'opsi_b' => 'Ikut mencoba agar diterima dalam kelompok',
                'opsi_c' => 'Melaporkan kepada guru atau orang tua atau pihak berwenang',
                'opsi_d' => 'Menyebarkan berita tersebut ke semua orang agar diketahui',
                'kunci' => 'C',
            ],
            [
                'pertanyaan' => 'Mengapa Masa Remaja sama dengan masa rawan? Karena remaja ...',
                'opsi_a' => 'Mulai pintar bicara dan bekerja',
                'opsi_b' => 'Penasaran, Teman sebaya dan ingin mencoba hal baru',
                'opsi_c' => 'Banyak teman dan dilirik teman',
                'opsi_d' => 'Mulai suka bersolek dan banyak bicara',
                'kunci' => 'B',
            ],
            [
                'pertanyaan' => 'Berikut adalah anggota tubuh yang harus dijaga/privasi...',
                'opsi_a' => 'Mata, mulut, dada dan kaki',
                'opsi_b' => 'Telinga, kaki, mulut, rambut',
                'opsi_c' => 'Mulut, dada, telinga dan rambut',
                'opsi_d' => 'Mulut, dada, alat kelamin, dan pantat',
                'kunci' => 'D',
            ],
            [
                'pertanyaan' => 'Sebagai Remaja, upaya mencegah penyalahgunaan narkoba yang dapat dilakukan adalah...',
                'opsi_a' => 'Memilih pergaulan yang positif dan mengisi waktu dengan kegiatan bermanfaat',
                'opsi_b' => 'Sering berada di tempat keramaian dan pulang larut malam',
                'opsi_c' => 'Menerima makanan atau minuman dari orang yang baru dikenal',
                'opsi_d' => 'Mengikuti ajakan teman untuk pergi ke tempat yang tidak diketahui',
                'kunci' => 'A',
            ],
        ];

        foreach ($soalList as $idx => $s) {
            Question::create(array_merge($s, [
                'question_package_id' => $pkgPre->id,
                'urutan' => $idx + 1,
            ]));
            Question::create(array_merge($s, [
                'question_package_id' => $pkgPost->id,
                'urutan' => $idx + 1,
            ]));
        }

        // 5. Events (Blueprint v2.0 Bab 8 + v3.1)
        $ev1 = Event::create([
            'kode_event' => 'EV-2026-09-10-SMAN5',
            'nama_kegiatan' => 'Sosialisasi Anti Narkoba — SMA N 5 Surabaya',
            'kode_join' => 'AB1C2D',
            'status' => 'berlangsung',
            'tanggal' => '2026-09-10',
            'durasi_menit' => 30,
            'kategori_audiens' => 'SMA',
            'mode_input' => 'HYBRID',
            'digital_submode' => 'TERBUKA',
            'enable_custody_tracking' => false,
            'status_fase' => 'POST_ACTIVE',
            'lokasi_id' => $loc1->id,
            'pretest_package_id' => $pkgPre->id,
            'posttest_package_id' => $pkgPost->id,
            'created_by' => $wahyu->id,
            'catatan' => 'Sesi tatap muka di Aula Utama, peserta kelas XI MIPA & IPS.',
        ]);

        $ev2 = Event::create([
            'kode_event' => 'EV-2026-09-08-SMAN12',
            'nama_kegiatan' => 'Sosialisasi P4GN — SMAN 12 Surabaya',
            'kode_join' => 'XY9Z8W',
            'status' => 'selesai',
            'tanggal' => '2026-09-08',
            'durasi_menit' => 45,
            'kategori_audiens' => 'SMA',
            'mode_input' => 'DIGITAL_PWA',
            'digital_submode' => 'TERBUKA',
            'enable_custody_tracking' => false,
            'status_fase' => 'COMPLETED',
            'lokasi_id' => $loc2->id,
            'pretest_package_id' => $pkgPre->id,
            'posttest_package_id' => $pkgPost->id,
            'created_by' => $wahyu->id,
        ]);

        $ev3 = Event::create([
            'kode_event' => 'EV-2026-09-28-LPS01',
            'nama_kegiatan' => 'Sosialisasi Narkoba — Lapas Kelas I Surabaya',
            'kode_join' => 'LP3K4X',
            'status' => 'dijadwalkan',
            'tanggal' => '2026-09-28',
            'durasi_menit' => 30,
            'kategori_audiens' => 'LAPAS',
            'mode_input' => 'MANUAL_KERTAS',
            'digital_submode' => 'TERDAFTAR',
            'enable_custody_tracking' => true,
            'status_fase' => 'DRAFT',
            'lokasi_id' => $loc3->id,
            'pretest_package_id' => $pkgLapas->id,
            'posttest_package_id' => $pkgPost->id,
            'created_by' => $wahyu->id,
            'catatan' => 'Area steril Lapas — Menggunakan amplop tersegel dan lembar kertas fisik.',
        ]);

        $ev4 = Event::create([
            'kode_event' => 'EV-2026-09-14-RW03',
            'nama_kegiatan' => 'P4GN Warga — Balai RW 03 Kel. Jambangan',
            'kode_join' => 'JB8K2L',
            'status' => 'selesai',
            'tanggal' => '2026-09-14',
            'durasi_menit' => 30,
            'kategori_audiens' => 'UMUM',
            'mode_input' => 'DIGITAL_PWA',
            'digital_submode' => 'TERBUKA',
            'enable_custody_tracking' => false,
            'status_fase' => 'COMPLETED',
            'lokasi_id' => $loc5->id,
            'pretest_package_id' => $pkgPre->id,
            'posttest_package_id' => $pkgPost->id,
            'created_by' => $wahyu->id,
        ]);

        $ev5 = Event::create([
            'kode_event' => 'EV-2026-09-22-PDAM',
            'nama_kegiatan' => 'Sosialisasi Bahaya Narkoba — Aula PDAM Sby',
            'kode_join' => 'PD7M9N',
            'status' => 'dijadwalkan',
            'tanggal' => '2026-09-22',
            'durasi_menit' => 30,
            'kategori_audiens' => 'INSTANSI',
            'mode_input' => 'DIGITAL_PWA',
            'digital_submode' => 'TERBUKA',
            'enable_custody_tracking' => false,
            'status_fase' => 'DRAFT',
            'lokasi_id' => $loc6->id,
            'pretest_package_id' => $pkgPre->id,
            'posttest_package_id' => $pkgPost->id,
            'created_by' => $wahyu->id,
        ]);

        // 6. Participants for Event 1 (Delta & N-Gain Calculation via Model Observer)
        $sampleParticipants = [
            ['name' => 'Ahmad Fauzi',    'class_grade' => 'XI-MIPA 1', 'school_origin' => 'SMA N 5 Surabaya', 'pre' => 60, 'post' => 85, 'method' => 'manual', 'ket' => 'Hadir'],
            ['name' => 'Budi Santoso',   'class_grade' => 'XI-MIPA 1', 'school_origin' => 'SMA N 5 Surabaya', 'pre' => 45, 'post' => 75, 'method' => 'manual', 'ket' => 'Hadir'],
            ['name' => 'Citra Dewi',     'class_grade' => 'XI-MIPA 2', 'school_origin' => 'SMA N 5 Surabaya', 'pre' => 70, 'post' => 90, 'method' => 'omr',    'ket' => 'Hadir'],
            ['name' => 'Dedi Kurniawan', 'class_grade' => 'XI-IPS 1',  'school_origin' => 'SMA N 5 Surabaya', 'pre' => 50, 'post' => 80, 'method' => 'omr',    'ket' => 'Hadir'],
            ['name' => 'Eva Marlina',    'class_grade' => 'XI-IPS 2',  'school_origin' => 'SMA N 5 Surabaya', 'pre' => 40, 'post' => 60, 'method' => 'online', 'ket' => 'Hadir'],
            ['name' => 'Fajar Pratama',  'class_grade' => 'XI-MIPA 3', 'school_origin' => 'SMA N 5 Surabaya', 'pre' => 55, 'post' => 85, 'method' => 'online', 'ket' => 'Hadir'],
            ['name' => 'Gita Anggraini', 'class_grade' => 'XI-MIPA 3', 'school_origin' => 'SMA N 5 Surabaya', 'pre' => 65, 'post' => 95, 'method' => 'manual', 'ket' => 'Hadir'],
            ['name' => 'Hendra Setiawan', 'class_grade' => 'XI-IPS 3',  'school_origin' => 'SMA N 5 Surabaya', 'pre' => 50, 'post' => 70, 'method' => 'manual', 'ket' => 'Hadir'],
        ];

        foreach ($sampleParticipants as $idx => $sp) {
            $participant = Participant::create([
                'event_id' => $ev1->id,
                'nomor_absen' => $idx + 1,
                'name' => $sp['name'],
                'class_grade' => $sp['class_grade'],
                'school_origin' => $sp['school_origin'],
                'keterangan' => $sp['ket'],
                'pretest_score' => $sp['pre'],
                'posttest_score' => $sp['post'],
                'input_method' => $sp['method'],
            ]);

            // 7. Seed Sample Scans Log for Audit Trail
            if ($sp['method'] === 'omr') {
                Scan::create([
                    'event_id' => $ev1->id,
                    'participant_id' => $participant->id,
                    'phase_type' => 'PRE',
                    'raw_answers' => ['1' => 'C', '2' => 'B', '3' => 'D', '4' => 'B', '5' => 'B'],
                    'score_raw' => 5,
                    'score_percent' => $sp['pre'],
                    'omr_confidence' => 'HIGH',
                    'captured_by' => $wahyu->id,
                    'captured_at' => now()->subMinutes(60),
                    'capture_method' => 'CAMERA_LIVE',
                ]);
                Scan::create([
                    'event_id' => $ev1->id,
                    'participant_id' => $participant->id,
                    'phase_type' => 'POST',
                    'raw_answers' => ['1' => 'C', '2' => 'B', '3' => 'D', '4' => 'B', '5' => 'B', '6' => 'B', '7' => 'C'],
                    'score_raw' => 7,
                    'score_percent' => $sp['post'],
                    'omr_confidence' => 'HIGH',
                    'captured_by' => $wahyu->id,
                    'captured_at' => now()->subMinutes(10),
                    'capture_method' => 'CAMERA_LIVE',
                ]);
            }
        }

        // 8. Custody Log untuk Lapas Event (ev3)
        CustodyLog::create([
            'event_id' => $ev3->id,
            'handed_by' => $wahyu->id,
            'handed_to' => 'Petugas Sipir Lapas Kelas I Medaeng',
            'location_note' => 'Pos Pengamanan Pintu Utama (P2U) Lapas Medaeng',
            'timestamp' => now(),
        ]);
    }
}
