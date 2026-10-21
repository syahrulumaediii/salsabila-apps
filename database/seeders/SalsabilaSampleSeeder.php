<?php

namespace Database\Seeders;

use App\Models\Billing;
use App\Models\DailyAgenda;
use App\Models\DailyChecklist;
use App\Models\Issue;
use App\Models\Project;
use App\Models\ProjectHistoryLog;
use App\Models\Subtask;
use App\Models\Task;
use App\Models\TaskQcReview;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SalsabilaSampleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $today = Carbon::today();
        $yesterday = Carbon::today()->subDays(2);
        $tomorrow = Carbon::today()->addDays(2);
        $lastWeek = Carbon::today()->subDays(7);
        $nextWeek = Carbon::today()->addDays(6);

        // 1. Members / Users
        $membersData = [
            [
                'name' => 'Nurul Ilmi, S.Ars',
                'email' => 'nurul@salsabila.com',
                'role' => 'Team Leader / Principal Architect',
                'divisi' => 'Arsitektur',
                'eval_notes' => 'Koordinasi lintas divisi dan kendali mutu dokumen perencanaan sangat terjaga.',
            ],
            [
                'name' => 'Ir. Hendra Wijaya, S.T.',
                'email' => 'hendra@salsabila.com',
                'role' => 'Tenaga Ahli Struktur',
                'divisi' => 'Struktur',
                'eval_notes' => 'Pemodelan SAP2000 akurat, perlu percepatan pada penyusunan laporan perhitungan akhir.',
            ],
            [
                'name' => 'Deden Pratama, A.Md.',
                'email' => 'deden@salsabila.com',
                'role' => 'Drafter CAD / BIM Modeler',
                'divisi' => 'Arsitektur',
                'eval_notes' => 'Detail gambar kerja rapi, responsif terhadap revisi potongan dan tampak.',
            ],
            [
                'name' => 'Rina Kartika, S.T.',
                'email' => 'rina@salsabila.com',
                'role' => 'Quantity Surveyor & Estimator RAB',
                'divisi' => 'Estimasi & RAB',
                'eval_notes' => 'Teliti dalam penerapan kode AHSP terbaru dan harga satuan Kabupaten Cirebon.',
            ],
            [
                'name' => 'Agus Salim, S.T.',
                'email' => 'agus@salsabila.com',
                'role' => 'Koordinator Pengawas Lapangan',
                'divisi' => 'Survey & Pengawasan',
                'eval_notes' => 'Laporan mingguan lapangan disiplin, dokumentasi visual lengkap.',
            ],
            [
                'name' => 'Siti Rahmawati, S.E.',
                'email' => 'siti@salsabila.com',
                'role' => 'Staf Administrasi Kontrak & Penagihan',
                'divisi' => 'Administrasi & Kontrak',
                'eval_notes' => 'Pemberkasan BAST dan dokumen penagihan termin tertata rapi.',
            ],
        ];

        $users = [];
        foreach ($membersData as $m) {
            $users[$m['name']] = User::updateOrCreate(
                ['email' => $m['email']],
                [
                    'name' => $m['name'],
                    'password' => Hash::make('password'),
                    'role' => $m['role'],
                    'divisi' => $m['divisi'],
                    'eval_notes' => $m['eval_notes'],
                ]
            );
        }

        // Test user for quick Breeze login
        $defaultUser = User::updateOrCreate(
            ['email' => 'admin@salsabila.com'],
            [
                'name' => 'Team Leader Salsabila',
                'password' => Hash::make('password'),
                'role' => 'Team Leader',
                'divisi' => 'Arsitektur',
                'eval_notes' => 'Superadmin Team Leader.',
            ]
        );

        // 2. Projects
        $p1 = Project::updateOrCreate(
            ['name' => 'Perencanaan Rehabilitasi Pustu Sarwadadi & Cempaka'],
            [
                'code' => 'P1',
                'client' => 'Dinas Kesehatan Kab. Cirebon',
                'contract_no' => '602.1/SPK-PRC.PUSTU/DINKES/2026',
                'start_date' => $lastWeek,
                'deadline' => $nextWeek,
                'pic_user_id' => $users['Nurul Ilmi, S.Ars']->id,
                'team_members' => 'Hendra Wijaya, Deden Pratama, Rina Kartika',
                'status' => 'Aktif Berjalan',
                'target_progress' => 80,
                'obstacle' => 'Penyesuaian rincian atap baja ringan Pustu Cempaka terhadap pagu anggaran.',
                'notes' => 'Pastikan sinkronisasi volume gambar DED dengan RAB Gabungan dan analisa SMKK.',
            ]
        );

        ProjectHistoryLog::updateOrCreate(
            ['project_id' => $p1->id, 'text' => 'Survei pendahuluan & pengukuran eksisting Pustu Sarwadadi dan Cempaka selesai 100%.'],
            ['log_date' => $lastWeek]
        );
        ProjectHistoryLog::updateOrCreate(
            ['project_id' => $p1->id, 'text' => 'Draf gambar kerja arsitektur & draf awal RAB diajukan ke meja pemeriksaan Team Leader.'],
            ['log_date' => $yesterday]
        );

        $p2 = Project::updateOrCreate(
            ['name' => 'DED Pembangunan Gedung Serbaguna (15x20 m)'],
            [
                'code' => 'P2',
                'client' => 'Pemerintah Kecamatan / Desa',
                'contract_no' => '024/KTR-GSG/X/2026',
                'start_date' => Carbon::today()->subDays(14),
                'deadline' => $yesterday,
                'pic_user_id' => $users['Ir. Hendra Wijaya, S.T.']->id,
                'team_members' => 'Hendra Wijaya, Deden Pratama',
                'status' => 'Terhambat',
                'target_progress' => 100,
                'obstacle' => 'Menunggu konfirmasi perubahan profil kuda-kuda baja WF dari pihak pengguna anggaran.',
                'notes' => 'Segera keluarkan surat konfirmasi teknis hari ini agar penetapan dimensi profil tidak menunda finalisasi DED.',
            ]
        );

        ProjectHistoryLog::updateOrCreate(
            ['project_id' => $p2->id, 'text' => 'Pemodelan struktur portal baja 15x20m selesai dianalisis di SAP2000.'],
            ['log_date' => Carbon::today()->subDays(10)]
        );
        ProjectHistoryLog::updateOrCreate(
            ['project_id' => $p2->id, 'text' => 'Klien meminta simulasi alternatif penutup atap dan fasad ACP + kisi WPC.'],
            ['log_date' => $yesterday]
        );

        $p3 = Project::updateOrCreate(
            ['name' => 'Pengawasan Teknis Rehabilitasi UPTD Puskesmas'],
            [
                'code' => 'P3',
                'client' => 'Dinas Kesehatan',
                'contract_no' => '602.2/SPK-PWS/2026',
                'start_date' => Carbon::today()->subDays(30),
                'deadline' => Carbon::today()->subDays(3),
                'pic_user_id' => $users['Agus Salim, S.T.']->id,
                'team_members' => 'Agus Salim, Siti Rahmawati',
                'status' => 'Selesai Teknis',
                'target_progress' => 100,
                'obstacle' => 'Pekerjaan teknis laporan akhir selesai 100%, namun pencairan termin akhir masih menunggu verifikasi BAST.',
                'notes' => 'Kawal terus proses penagihan termin 100% di bagian keuangan dinas.',
            ]
        );

        ProjectHistoryLog::updateOrCreate(
            ['project_id' => $p3->id, 'text' => 'Laporan Akhir Pengawasan dan dokumentasi 100% telah disetujui PPK.'],
            ['log_date' => Carbon::today()->subDays(5)]
        );

        // 3. Tasks
        // T1
        $t1 = Task::updateOrCreate(
            ['name' => 'Finalisasi RAB Gabungan, AHSP 2025/2026 & Analisa SMKK Pustu'],
            [
                'code' => 'T1',
                'project_id' => $p1->id,
                'stage' => 'B. PERENCANAAN',
                'sub_stage' => 'RAB dan analisis harga satuan',
                'divisi' => 'Estimasi & RAB',
                'pic_user_id' => $users['Rina Kartika, S.T.']->id,
                'support_team' => 'Deden Pratama, A.Md.',
                'start_date' => $lastWeek,
                'deadline' => $today,
                'priority' => 'Kritis',
                'status' => 'Menunggu Pemeriksaan',
                'weight' => 35,
                'manual_progress_mode' => false,
                'progress' => 80,
                'obstacle' => 'Perlu pengecekan silang satuan kusen aluminium dengan spesifikasi RKS.',
                'notes' => 'Periksa kembali pembulatan PPN 11% dan konsistensi kode AHSP.',
                'next_action' => 'Pemeriksaan meja Team Leader sore ini pukul 14.00 WIB.',
                'need_decision' => false,
                'client_follow_up' => true,
                'last_updated_date' => $today,
            ]
        );

        $t1Subtasks = [
            ['title' => 'Input volume pekerjaan bongkaran & pasangan Pustu Sarwadadi', 'is_done' => true, 'order' => 1],
            ['title' => 'Input volume pekerjaan atap, plafon, dan keramik Pustu Cempaka', 'is_done' => true, 'order' => 2],
            ['title' => 'Verifikasi Harga Satuan Pokok Kabupaten Cirebon 2026', 'is_done' => true, 'order' => 3],
            ['title' => 'Rekapitulasi biaya SMKK dan pembulatan sesuai pagu KAK', 'is_done' => true, 'order' => 4],
            ['title' => 'Persetujuan akhir & tanda tangan Team Leader', 'is_done' => false, 'order' => 5],
        ];
        foreach ($t1Subtasks as $st) {
            Subtask::updateOrCreate(['task_id' => $t1->id, 'title' => $st['title']], $st);
        }

        TaskQcReview::updateOrCreate(
            ['task_id' => $t1->id],
            [
                'reviewer_user_id' => $users['Nurul Ilmi, S.Ars']->id,
                'review_date' => $today,
                'qc_checklist' => [true, true, true, true, false, true, false],
                'qc_notes' => 'Menunggu pengecekan konsistensi item pekerjaan listrik antara gambar titik lampu dan BoQ.',
                'status' => 'Menunggu Pemeriksaan',
            ]
        );

        // T2
        $t2 = Task::updateOrCreate(
            ['name' => 'Analisis Struktur Portal Baja WF & Pondasi Telapak Gedung 15x20m'],
            [
                'code' => 'T2',
                'project_id' => $p2->id,
                'stage' => 'B. PERENCANAAN',
                'sub_stage' => 'Perhitungan struktur',
                'divisi' => 'Struktur',
                'pic_user_id' => $users['Ir. Hendra Wijaya, S.T.']->id,
                'support_team' => 'Deden Pratama, A.Md.',
                'start_date' => Carbon::today()->subDays(10),
                'deadline' => $yesterday,
                'priority' => 'Kritis',
                'status' => 'Terhambat',
                'weight' => 45,
                'manual_progress_mode' => false,
                'progress' => 75,
                'obstacle' => 'Perubahan spek penutup atap dari pihak pemilik proyek belum diputuskan secara tertulis.',
                'notes' => 'Jangan ubah dimensi kolom utama WF 250 sebelum ada keputusan resmi.',
                'next_action' => 'Team Leader memutuskan opsi profil gording dan mengirim berita acara teknis ke klien.',
                'need_decision' => true,
                'client_follow_up' => true,
                'last_updated_date' => $lastWeek,
            ]
        );

        $t2Subtasks = [
            ['title' => 'Pemodelan 3D Portal Baja bentang 15m di SAP2000', 'is_done' => true, 'order' => 1],
            ['title' => 'Kombinasi pembebanan gempa dan angin', 'is_done' => true, 'order' => 2],
            ['title' => 'Desain sambungan baut mutu tinggi & baseplate', 'is_done' => true, 'order' => 3],
            ['title' => 'Cetak lampiran nota perhitungan struktur final', 'is_done' => false, 'order' => 4],
        ];
        foreach ($t2Subtasks as $st) {
            Subtask::updateOrCreate(['task_id' => $t2->id, 'title' => $st['title']], $st);
        }

        TaskQcReview::updateOrCreate(
            ['task_id' => $t2->id],
            [
                'reviewer_user_id' => $users['Nurul Ilmi, S.Ars']->id,
                'review_date' => $yesterday,
                'qc_checklist' => [true, true, false, false, false, false, false],
                'qc_notes' => 'Tertahan konfirmasi beban penutup atap.',
                'status' => 'Revisi',
            ]
        );

        // T3
        $t3 = Task::updateOrCreate(
            ['name' => 'Gambar Kerja Detail Fasad ACP, Kisi WPC & Ornamen Mega Mendung'],
            [
                'code' => 'T3',
                'project_id' => $p2->id,
                'stage' => 'B. PERENCANAAN',
                'sub_stage' => 'Gambar perencanaan',
                'divisi' => 'Arsitektur',
                'pic_user_id' => $users['Deden Pratama, A.Md.']->id,
                'support_team' => 'Nurul Ilmi, S.Ars',
                'start_date' => $yesterday,
                'deadline' => $today,
                'priority' => 'Tinggi',
                'status' => 'Menunggu Pemeriksaan',
                'weight' => 35,
                'manual_progress_mode' => false,
                'progress' => 67,
                'obstacle' => 'Perlu kepastian ketebalan plat cutting ornamen untuk dihitung di RAB.',
                'notes' => 'Gunakan standar ACP PVDF 4mm eksterior.',
                'next_action' => 'Review gambar detail potongan fasad bersama Team Leader.',
                'need_decision' => true,
                'client_follow_up' => false,
                'last_updated_date' => $today,
            ]
        );

        $t3Subtasks = [
            ['title' => 'Gambar tampak depan dan samping skala 1:100', 'is_done' => true, 'order' => 1],
            ['title' => 'Detail rangka hollow dudukan modul ACP & kisi WPC', 'is_done' => true, 'order' => 2],
            ['title' => 'Detail pola cutting laser motif Mega Mendung', 'is_done' => false, 'order' => 3],
        ];
        foreach ($t3Subtasks as $st) {
            Subtask::updateOrCreate(['task_id' => $t3->id, 'title' => $st['title']], $st);
        }

        TaskQcReview::updateOrCreate(
            ['task_id' => $t3->id],
            [
                'reviewer_user_id' => $users['Nurul Ilmi, S.Ars']->id,
                'review_date' => $today,
                'qc_checklist' => [true, false, true, false, false, true, false],
                'qc_notes' => 'Detail sambungan sealant ACP perlu diperjelas skalanya.',
                'status' => 'Menunggu Pemeriksaan',
            ]
        );

        // T4
        $t4 = Task::updateOrCreate(
            ['name' => 'Penyusunan RKS / Spesifikasi Teknis Rehabilitasi Pustu'],
            [
                'code' => 'T4',
                'project_id' => $p1->id,
                'stage' => 'B. PERENCANAAN',
                'sub_stage' => 'Pemeriksaan internal',
                'divisi' => 'Arsitektur',
                'pic_user_id' => $users['Nurul Ilmi, S.Ars']->id,
                'support_team' => 'Rina Kartika, S.T.',
                'start_date' => $yesterday,
                'deadline' => $tomorrow,
                'priority' => 'Tinggi',
                'status' => 'Sedang Dikerjakan',
                'weight' => 35,
                'manual_progress_mode' => false,
                'progress' => 50,
                'obstacle' => '',
                'notes' => 'Pastikan seluruh item pekerjaan pada BoQ memiliki pasal spesifikasi di RKS.',
                'next_action' => 'Finalisasi pasal pekerjaan elektrikal dan sanitair.',
                'need_decision' => false,
                'client_follow_up' => false,
                'last_updated_date' => $today,
            ]
        );

        $t4Subtasks = [
            ['title' => 'Pasal spesifikasi material arsitektur & struktur', 'is_done' => true, 'order' => 1],
            ['title' => 'Sinkronisasi daftar merk material dengan RAB', 'is_done' => false, 'order' => 2],
        ];
        foreach ($t4Subtasks as $st) {
            Subtask::updateOrCreate(['task_id' => $t4->id, 'title' => $st['title']], $st);
        }

        // T5
        $t5 = Task::updateOrCreate(
            ['name' => 'Verifikasi Kelengkapan Dokumen Kualifikasi & BAST Kontrak Pustu'],
            [
                'code' => 'T5',
                'project_id' => $p1->id,
                'stage' => 'A. KONTRAK',
                'sub_stage' => 'Kontrak dan dokumen pendukung',
                'divisi' => 'Administrasi & Kontrak',
                'pic_user_id' => $users['Siti Rahmawati, S.E.']->id,
                'support_team' => '',
                'start_date' => $lastWeek,
                'deadline' => $lastWeek,
                'priority' => 'Sedang',
                'status' => 'Selesai',
                'weight' => 30,
                'manual_progress_mode' => true,
                'progress' => 100,
                'obstacle' => '',
                'notes' => 'Arsip digital sudah diunggah ke folder proyek.',
                'next_action' => 'Selesai.',
                'need_decision' => false,
                'client_follow_up' => false,
                'last_updated_date' => $lastWeek,
            ]
        );

        $t5Subtasks = [
            ['title' => 'Pengarsipan SPK & KAK Perencanaan', 'is_done' => true, 'order' => 1],
            ['title' => 'Berita Acara Serah Terima Lapangan Awal', 'is_done' => true, 'order' => 2],
        ];
        foreach ($t5Subtasks as $st) {
            Subtask::updateOrCreate(['task_id' => $t5->id, 'title' => $st['title']], $st);
        }

        TaskQcReview::updateOrCreate(
            ['task_id' => $t5->id],
            [
                'reviewer_user_id' => $users['Nurul Ilmi, S.Ars']->id,
                'review_date' => $lastWeek,
                'qc_checklist' => [true, true, true, true, true, true, true],
                'qc_notes' => 'Dokumen kontrak lengkap dan terverifikasi.',
                'status' => 'Approved',
            ]
        );

        // T6
        $t6 = Task::updateOrCreate(
            ['name' => 'Pengajuan Berkas Penagihan Termin 100% Pengawasan Puskesmas'],
            [
                'code' => 'T6',
                'project_id' => $p3->id,
                'stage' => 'D. PENAGIHAN',
                'sub_stage' => 'Tindak lanjut pembayaran yang belum diterima',
                'divisi' => 'Administrasi & Kontrak',
                'pic_user_id' => $users['Siti Rahmawati, S.E.']->id,
                'support_team' => 'Agus Salim, S.T.',
                'start_date' => Carbon::today()->subDays(5),
                'deadline' => $today,
                'priority' => 'Kritis',
                'status' => 'Sedang Dikerjakan',
                'weight' => 100,
                'manual_progress_mode' => false,
                'progress' => 75,
                'obstacle' => 'Menunggu verifikasi berkas SPP oleh staf perbendaharaan dinas.',
                'notes' => 'Proyek sudah selesai teknis 100%, prioritaskan follow-up pencairan hari ini.',
                'next_action' => 'Kunjungan follow-up ke bagian keuangan Dinas Kesehatan.',
                'need_decision' => false,
                'client_follow_up' => true,
                'last_updated_date' => $today,
            ]
        );

        $t6Subtasks = [
            ['title' => 'BAST Pekerjaan Pengawasan 100%', 'is_done' => true, 'order' => 1],
            ['title' => 'Faktur Pajak & e-Billing', 'is_done' => true, 'order' => 2],
            ['title' => 'Kwitansi & Surat Permohonan Pembayaran ke Bendahara', 'is_done' => true, 'order' => 3],
            ['title' => 'Konfirmasi penerbitan SP2D pencairan ke Bank BJB', 'is_done' => false, 'order' => 4],
        ];
        foreach ($t6Subtasks as $st) {
            Subtask::updateOrCreate(['task_id' => $t6->id, 'title' => $st['title']], $st);
        }

        TaskQcReview::updateOrCreate(
            ['task_id' => $t6->id],
            [
                'reviewer_user_id' => $users['Nurul Ilmi, S.Ars']->id,
                'review_date' => $today,
                'qc_checklist' => [true, true, true, true, true, true, false],
                'qc_notes' => 'Berkas tagihan lengkap.',
                'status' => 'Menunggu Pemeriksaan',
            ]
        );

        // T7
        $t7 = Task::updateOrCreate(
            ['name' => 'Penyusunan Kurva-S Rencana & WBS Pelaksanaan Gedung Serbaguna'],
            [
                'code' => 'T7',
                'project_id' => $p2->id,
                'stage' => 'C. PELAKSANAAN / PENGAWASAN',
                'sub_stage' => 'Persiapan lapangan',
                'divisi' => 'Survey & Pengawasan',
                'pic_user_id' => null,
                'support_team' => '',
                'start_date' => $today,
                'deadline' => null,
                'priority' => 'Sedang',
                'status' => 'Belum Dimulai',
                'weight' => 20,
                'manual_progress_mode' => true,
                'progress' => 0,
                'obstacle' => 'Belum ditetapkan PIC dan tanggal batas waktu.',
                'notes' => 'Segera tetapkan PIC begitu RAB gedung serbaguna dikunci.',
                'next_action' => 'Tetapkan PIC dan deadline.',
                'need_decision' => true,
                'client_follow_up' => false,
                'last_updated_date' => $lastWeek,
            ]
        );

        $t7Subtasks = [
            ['title' => 'Breakdown bobot per item pekerjaan struktur & arsitektur', 'is_done' => false, 'order' => 1],
            ['title' => 'Plotting jadwal durasi 120 hari kalender', 'is_done' => false, 'order' => 2],
        ];
        foreach ($t7Subtasks as $st) {
            Subtask::updateOrCreate(['task_id' => $t7->id, 'title' => $st['title']], $st);
        }

        // 4. Issues
        Issue::updateOrCreate(
            ['problem' => 'Perubahan jarak gording dan jenis penutup atap Gedung Serbaguna 15x20m'],
            [
                'project_id' => $p2->id,
                'category' => 'Menunggu keputusan Team Leader',
                'reporter_user_id' => $users['Ir. Hendra Wijaya, S.T.']->id,
                'urgency' => 'Mendesak (Blokir Progress)',
                'impact' => 'Finalisasi nota perhitungan struktur SAP2000 dan gambar detail sambungan baja tertunda 2 hari.',
                'reported_date' => $yesterday,
                'decision_needed' => 'Keputusan penguncian spesifikasi atap (UPVC Alderon vs Spandek Pasir) beserta jarak gording C-150.',
                'solution' => 'Tetapkan penggunaan atap UPVC berongga dengan jarak gording maksimal 1,2 m sesuai standar keamanan defleksi.',
                'target_date' => $today,
                'status' => 'Menunggu Keputusan TL',
            ]
        );

        Issue::updateOrCreate(
            ['problem' => 'Selisih pagu anggaran terhadap volume penggantian rangka atap Pustu Cempaka'],
            [
                'project_id' => $p1->id,
                'category' => 'Kendala teknis',
                'reporter_user_id' => $users['Rina Kartika, S.T.']->id,
                'urgency' => 'Tinggi (Berpotensi Terlambat)',
                'impact' => 'Total rekapitulasi RAB melebihi pagu KAK sebesar 4,2%.',
                'reported_date' => $yesterday,
                'decision_needed' => 'Persetujuan optimasi item pekerjaan non-struktural (penyesuaian luasan rabat beton halaman).',
                'solution' => 'Optimasi volume pekerjaan halaman luar agar fokus anggaran 100% mengamankan struktur atap dan ruang pelayanan.',
                'target_date' => $today,
                'status' => 'Sedang Ditindaklanjuti',
            ]
        );

        Issue::updateOrCreate(
            ['problem' => 'Pencairan Termin 100% Pengawasan Puskesmas belum terbit SP2D'],
            [
                'project_id' => $p3->id,
                'category' => 'Kendala pembayaran',
                'reporter_user_id' => $users['Siti Rahmawati, S.E.']->id,
                'urgency' => 'Tinggi (Berpotensi Terlambat)',
                'impact' => 'Arus kas masuk proyek selesai teknis tertahan lebih dari 7 hari sejak BAST.',
                'reported_date' => Carbon::today()->subDays(3),
                'decision_needed' => 'Arahan konfirmasi langsung kepada PPK dan bagian keuangan instansi.',
                'solution' => 'Siti melakukan pendampingan verifikasi berkas fisik di bagian keuangan hari ini.',
                'target_date' => $tomorrow,
                'status' => 'Menunggu Konfirmasi Instansi',
            ]
        );

        // 5. Billings
        Billing::updateOrCreate(
            ['term' => 'Termin 100% (Sekaligus)', 'project_id' => $p3->id],
            [
                'client' => 'Dinas Kesehatan',
                'contract_val' => 48500000,
                'invoice_val' => 48500000,
                'paid_val' => 0,
                'submit_date' => Carbon::today()->subDays(6),
                'doc_status' => 'Lengkap & Sudah Diajukan',
                'pay_status' => 'Terlambat Cair (Perlu Follow-Up)',
                'pic_user_id' => $users['Siti Rahmawati, S.E.']->id,
                'follow_up_date' => $today,
            ]
        );

        Billing::updateOrCreate(
            ['term' => 'Termin 100% (Setelah Penyerahan DED & RAB)', 'project_id' => $p1->id],
            [
                'client' => 'Dinas Kesehatan Kab. Cirebon',
                'contract_val' => 65000000,
                'invoice_val' => 65000000,
                'paid_val' => 0,
                'submit_date' => null,
                'doc_status' => 'Penyusunan BAST & Berkas',
                'pay_status' => 'Belum Diajukan',
                'pic_user_id' => $users['Siti Rahmawati, S.E.']->id,
                'follow_up_date' => $nextWeek,
            ]
        );

        Billing::updateOrCreate(
            ['term' => 'Termin 1 (Uang Muka 30%) & Pelunasan 70%', 'project_id' => $p2->id],
            [
                'client' => 'Pemerintah Kecamatan / Desa',
                'contract_val' => 42000000,
                'invoice_val' => 42000000,
                'paid_val' => 12600000,
                'submit_date' => Carbon::today()->subDays(12),
                'doc_status' => 'Menunggu Tanda Tangan PPK',
                'pay_status' => 'Dibayar Sebagian',
                'pic_user_id' => $users['Siti Rahmawati, S.E.']->id,
                'follow_up_date' => $tomorrow,
            ]
        );

        // 6. Daily Agenda for Today
        $agenda = DailyAgenda::updateOrCreate(
            [
                'user_id' => $users['Nurul Ilmi, S.Ars']->id,
                'agenda_date' => $today,
            ],
            [
                'top_task_ids' => [$t1->id, $t2->id, $t6->id],
                'eval_q1' => 'Verifikasi awal volume RAB Pustu Sarwadadi selesai.',
                'eval_q2' => 'Finalisasi nota perhitungan struktur Gedung Serbaguna masih menunggu kepastian penutup atap.',
                'eval_q3' => 'Perubahan spesifikasi dari pihak eksternal dan penyesuaian pagu anggaran.',
                'eval_q4' => 'PPK Dinas Kesehatan & Klien Gedung Serbaguna.',
                'eval_q5' => 'Selesaikan pemeriksaan akhir RAB Pustu dan kunci spesifikasi atap Gedung Serbaguna.',
            ]
        );

        $dailyChecklistItems = [
            ['text' => 'Briefing pagi 15 menit bersama tim struktur, arsitektur, dan estimator RAB', 'is_done' => true, 'order' => 1],
            ['text' => 'Periksa konsistensi gambar detail fasad dengan analisa harga satuan', 'is_done' => false, 'order' => 2],
            ['text' => 'Hubungi PPK Dinas Kesehatan terkait jadwal asistensi akhir Pustu Sarwadadi & Cempaka', 'is_done' => false, 'order' => 3],
        ];

        foreach ($dailyChecklistItems as $dci) {
            DailyChecklist::updateOrCreate(
                ['daily_agenda_id' => $agenda->id, 'text' => $dci['text']],
                $dci
            );
        }
    }
}
