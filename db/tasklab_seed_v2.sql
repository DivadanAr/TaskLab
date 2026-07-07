-- =========================================================
-- TASKLAB — SCHEMA (v2, enum sudah dibenerin) + DUMMY DATA
-- Perubahan dari skema sebelumnya yang sudah disesuaikan di sini:
--  - task_status / task_priority (tabel terpisah) DIHAPUS
--    -> diganti kolom enum langsung di tasks & subtasks
--  - attachment_tasks DIHAPUS (task sekarang gak punya lampiran file)
-- Urutan INSERT mengikuti urutan dependency FK.
-- =========================================================

-- ---------------------------------------------------------
-- SCHEMA
-- ---------------------------------------------------------

CREATE TABLE `users` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `name` varchar(255),
  `email` varchar(255) UNIQUE,
  `password` varchar(255),
  `avatar` varchar(255) COMMENT 'path/url foto profil, null = pakai inisial nama',
  `created_at` timestamp,
  `updated_at` timestamp
);

CREATE TABLE `boards` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `board_name` varchar(255),
  `description` text,
  `cover_board` varchar(255),
  `owner` int,
  `created_at` timestamp,
  `updated_at` timestamp
);

CREATE TABLE `board_members` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `board_id` int,
  `user_id` int,
  `role` enum('editor','view','admin'),
  `created_at` timestamp,
  `updated_at` timestamp
);

CREATE TABLE `board_invitations` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `board_id` int,
  `sender_id` int,
  `receiver_id` int,
  `status` enum('pending','accepted','rejected','cancelled') DEFAULT 'pending',
  `invitation_token` varchar(255),
  `created_at` timestamp,
  `responded_at` timestamp NULL
);

CREATE TABLE `tasks` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `task_name` varchar(255),
  `description` text,
  `due_date` datetime,
  `board_id` int,
  `task_status` enum('TODO','DONE'),
  `task_priority` enum('LOW','MEDIUM','HIGH'),
  `created_at` timestamp,
  `updated_at` timestamp
);

CREATE TABLE `task_assign` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `task_id` int,
  `user_id` int,
  `created_at` timestamp,
  `updated_at` timestamp
);

CREATE TABLE `comment` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `comment` text,
  `task_id` int,
  `user_id` int,
  `created_at` timestamp,
  `updated_at` timestamp
);

CREATE TABLE `subtasks` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `subtask_name` varchar(255),
  `due_date` datetime,
  `task_id` int,
  `subtask_status` enum('TODO','DONE'),
  `subtask_priority` enum('LOW','MEDIUM','HIGH'),
  `created_at` timestamp,
  `updated_at` timestamp
);

CREATE TABLE `subtask_assign` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `subtask_id` int,
  `user_id` int,
  `created_at` timestamp,
  `updated_at` timestamp
);

CREATE TABLE `notifications` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `user_id` int,
  `type` enum('comment','task_assigned','board_added','mention','other'),
  `message` varchar(255),
  `reference_id` int,
  `is_read` boolean DEFAULT false,
  `created_at` timestamp
);

CREATE TABLE `chat_rooms` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `type` enum('board','personal'),
  `board_id` int COMMENT 'diisi kalau type = board, null kalau type = personal',
  `created_at` timestamp,
  `updated_at` timestamp
);

CREATE TABLE `chat_room_members` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `chat_room_id` int,
  `user_id` int,
  `created_at` timestamp
);

CREATE TABLE `chat_logs` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `message` text,
  `chat_room_id` int,
  `user_id` int,
  `created_at` timestamp,
  `updated_at` timestamp
);

CREATE TABLE `chat_attachments` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `chat_log_id` int,
  `attachment_type` enum('file','image'),
  `source` varchar(255),
  `created_at` timestamp
);

CREATE TABLE `chat_reads` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `chat_room_id` int,
  `user_id` int,
  `last_read_message_id` int,
  `updated_at` timestamp
);

CREATE UNIQUE INDEX `board_members_index_0` ON `board_members` (`board_id`, `user_id`);
CREATE INDEX `board_invitations_index_1` ON `board_invitations` (`board_id`, `receiver_id`, `status`);
CREATE UNIQUE INDEX `task_assign_index_2` ON `task_assign` (`task_id`, `user_id`);
CREATE UNIQUE INDEX `subtask_assign_index_3` ON `subtask_assign` (`subtask_id`, `user_id`);
CREATE INDEX `notifications_index_4` ON `notifications` (`user_id`, `is_read`);
CREATE UNIQUE INDEX `chat_room_members_index_5` ON `chat_room_members` (`chat_room_id`, `user_id`);
CREATE UNIQUE INDEX `chat_reads_index_6` ON `chat_reads` (`chat_room_id`, `user_id`);

ALTER TABLE `boards` ADD FOREIGN KEY (`owner`) REFERENCES `users` (`id`);
ALTER TABLE `tasks` ADD FOREIGN KEY (`board_id`) REFERENCES `boards` (`id`);
ALTER TABLE `board_members` ADD FOREIGN KEY (`board_id`) REFERENCES `boards` (`id`);
ALTER TABLE `board_members` ADD FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
ALTER TABLE `board_invitations` ADD FOREIGN KEY (`board_id`) REFERENCES `boards` (`id`);
ALTER TABLE `board_invitations` ADD FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`);
ALTER TABLE `board_invitations` ADD FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`);
ALTER TABLE `task_assign` ADD FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`);
ALTER TABLE `task_assign` ADD FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
ALTER TABLE `comment` ADD FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`);
ALTER TABLE `comment` ADD FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
ALTER TABLE `subtasks` ADD FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`);
ALTER TABLE `subtask_assign` ADD FOREIGN KEY (`subtask_id`) REFERENCES `subtasks` (`id`);
ALTER TABLE `subtask_assign` ADD FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
ALTER TABLE `notifications` ADD FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
ALTER TABLE `chat_rooms` ADD FOREIGN KEY (`board_id`) REFERENCES `boards` (`id`);
ALTER TABLE `chat_room_members` ADD FOREIGN KEY (`chat_room_id`) REFERENCES `chat_rooms` (`id`);
ALTER TABLE `chat_room_members` ADD FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
ALTER TABLE `chat_logs` ADD FOREIGN KEY (`chat_room_id`) REFERENCES `chat_rooms` (`id`);
ALTER TABLE `chat_logs` ADD FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
ALTER TABLE `chat_attachments` ADD FOREIGN KEY (`chat_log_id`) REFERENCES `chat_logs` (`id`);
ALTER TABLE `chat_reads` ADD FOREIGN KEY (`chat_room_id`) REFERENCES `chat_rooms` (`id`);
ALTER TABLE `chat_reads` ADD FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);


-- =========================================================
-- DUMMY DATA
-- =========================================================

-- ---------------------------------------------------------
-- USERS
-- id 1 = user yang sedang login di UI (Divadan, sesuai header.php)
-- ---------------------------------------------------------
INSERT INTO `users` (`id`, `name`, `email`, `password`, `avatar`, `created_at`, `updated_at`) VALUES
(1, 'Divadan',        'divadan@gmail.com', '$2y$10$hashedpassword1', NULL, '2026-05-01 08:00:00', '2026-05-01 08:00:00'),
(2, 'Rasyid Saputra', 'rasyid@example.com', '$2y$10$hashedpassword2', NULL, '2026-05-02 09:15:00', '2026-05-02 09:15:00'),
(3, 'Dwi Prasetyo',   'dwi@example.com',    '$2y$10$hashedpassword3', NULL, '2026-05-02 10:00:00', '2026-05-02 10:00:00'),
(4, 'Andi Wijaya',    'andi@example.com',   '$2y$10$hashedpassword4', NULL, '2026-05-03 11:30:00', '2026-05-03 11:30:00'),
(5, 'Siti Aminah',    'siti@example.com',   '$2y$10$hashedpassword5', NULL, '2026-05-04 13:00:00', '2026-05-04 13:00:00'),
(6, 'Budi Santoso',   'budi@example.com',   '$2y$10$hashedpassword6', NULL, '2026-05-05 14:45:00', '2026-05-05 14:45:00');


-- ---------------------------------------------------------
-- BOARDS
-- ---------------------------------------------------------
INSERT INTO `boards` (`id`, `board_name`, `description`, `cover_board`, `owner`, `created_at`, `updated_at`) VALUES
(1, 'Quilla',          'Board utama tim produk untuk fitur Quilla.', 'https://picsum.photos/400/200?1', 1, '2026-05-06 09:00:00', '2026-06-01 10:00:00'),
(2, 'LCD Management',  'Pengelolaan inventaris dan maintenance unit LCD.', NULL, 2, '2026-05-10 09:00:00', '2026-05-20 09:00:00'),
(3, 'My Trello Board', 'Board eksperimen migrasi dari Trello.', NULL, 1, '2026-05-15 09:00:00', '2026-05-15 09:00:00');


-- ---------------------------------------------------------
-- BOARD MEMBERS
-- Owner ikut dimasukkan di sini dengan role admin (lihat catatan di schema)
-- ---------------------------------------------------------
INSERT INTO `board_members` (`id`, `board_id`, `user_id`, `role`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'admin',  '2026-05-06 09:00:00', '2026-05-06 09:00:00'), -- Divadan, owner Quilla
(2, 1, 3, 'editor', '2026-05-07 10:00:00', '2026-05-07 10:00:00'), -- Dwi di Quilla
(3, 1, 4, 'view',   '2026-05-08 11:00:00', '2026-05-08 11:00:00'), -- Andi di Quilla
(4, 2, 2, 'admin',  '2026-05-10 09:00:00', '2026-05-10 09:00:00'), -- Rasyid, owner LCD Management
(5, 2, 1, 'editor', '2026-05-11 09:00:00', '2026-05-11 09:00:00'), -- Divadan di LCD Management
(6, 3, 1, 'admin',  '2026-05-15 09:00:00', '2026-05-15 09:00:00'); -- Divadan, owner My Trello Board


-- ---------------------------------------------------------
-- BOARD INVITATIONS
-- id 1 = undangan pending yang muncul di UI Inbox (Rasyid -> Divadan, board Quilla)
-- ---------------------------------------------------------
INSERT INTO `board_invitations` (`id`, `board_id`, `sender_id`, `receiver_id`, `status`, `invitation_token`, `created_at`, `responded_at`) VALUES
(1, 1, 2, 1, 'pending',   'inv_tok_a1b2c3', '2026-07-05 10:41:00', NULL),
(2, 2, 2, 5, 'accepted',  'inv_tok_d4e5f6', '2026-06-01 08:00:00', '2026-06-01 09:30:00'),
(3, 1, 1, 6, 'rejected',  'inv_tok_g7h8i9', '2026-06-10 08:00:00', '2026-06-11 08:00:00'),
(4, 3, 1, 4, 'cancelled', 'inv_tok_j1k2l3', '2026-06-15 08:00:00', NULL);


-- ---------------------------------------------------------
-- TASKS
-- id 1 = "Bug Fixing" yang sudah tampil di UI board-detail
-- task_status & task_priority sekarang ENUM langsung (bukan tabel terpisah)
-- ---------------------------------------------------------
INSERT INTO `tasks` (`id`, `task_name`, `description`, `due_date`, `board_id`, `task_status`, `task_priority`, `created_at`, `updated_at`) VALUES
(1, 'Bug Fixing',          'Testing error pada modul login.',              '2026-12-16 17:00:00', 1, 'TODO', 'HIGH',   '2026-06-01 09:00:00', '2026-07-04 15:00:00'),
(2, 'Buat Wireframe',      'Wireframe halaman dashboard baru.',            '2026-07-20 17:00:00', 1, 'TODO', 'MEDIUM', '2026-06-02 09:00:00', '2026-06-02 09:00:00'),
(3, 'Review Pull Request', 'Review PR fitur inbox dari Dwi.',              '2026-07-10 17:00:00', 1, 'DONE', 'LOW',    '2026-06-03 09:00:00', '2026-07-01 10:00:00'),
(4, 'Cek Stok Unit LCD',   'Audit stok fisik unit LCD gudang utama.',      '2026-07-15 17:00:00', 2, 'TODO', 'MEDIUM', '2026-05-12 09:00:00', '2026-05-12 09:00:00'),
(5, 'Maintenance Rutin',   'Servis rutin unit LCD ruang meeting lt. 3.',   NULL,                   2, 'DONE', 'LOW',    '2026-05-13 09:00:00', '2026-06-20 09:00:00'),
(6, 'Migrasi Data Trello', 'Pindahkan seluruh card dari Trello ke sini.',  '2026-07-25 17:00:00', 3, 'TODO', 'MEDIUM', '2026-05-16 09:00:00', '2026-05-16 09:00:00');


-- ---------------------------------------------------------
-- TASK ASSIGN
-- ---------------------------------------------------------
INSERT INTO `task_assign` (`id`, `task_id`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '2026-06-01 09:05:00', '2026-06-01 09:05:00'),
(2, 1, 3, '2026-06-01 09:05:00', '2026-06-01 09:05:00'),
(3, 2, 4, '2026-06-02 09:05:00', '2026-06-02 09:05:00'),
(4, 3, 3, '2026-06-03 09:05:00', '2026-06-03 09:05:00'),
(5, 4, 2, '2026-05-12 09:05:00', '2026-05-12 09:05:00'),
(6, 5, 1, '2026-05-13 09:05:00', '2026-05-13 09:05:00'),
(7, 6, 1, '2026-05-16 09:05:00', '2026-05-16 09:05:00');


-- ---------------------------------------------------------
-- COMMENT
-- id 1 = komentar yang memicu notifikasi di UI Inbox
-- ---------------------------------------------------------
INSERT INTO `comment` (`id`, `comment`, `task_id`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 'Sudah aku cek, bug-nya muncul cuma di browser Safari.', 1, 3, '2026-07-05 05:30:00', '2026-07-05 05:30:00'),
(2, 'Oke, aku tambahin polyfill-nya sekarang.',               1, 1, '2026-07-05 05:45:00', '2026-07-05 05:45:00'),
(3, 'Wireframe versi 1 udah aku share di grup ya.',           2, 4, '2026-06-05 08:00:00', '2026-06-05 08:00:00');


-- ---------------------------------------------------------
-- SUBTASKS
-- subtask_status & subtask_priority sekarang ENUM langsung
-- ---------------------------------------------------------
INSERT INTO `subtasks` (`id`, `subtask_name`, `due_date`, `task_id`, `subtask_status`, `subtask_priority`, `created_at`, `updated_at`) VALUES
(1, 'Reproduksi bug di Safari',    '2026-07-06 17:00:00', 1, 'DONE', 'HIGH',   '2026-07-05 06:00:00', '2026-07-05 06:00:00'),
(2, 'Tambah polyfill fetch',       '2026-07-07 17:00:00', 1, 'TODO', 'HIGH',   '2026-07-05 06:05:00', '2026-07-05 15:00:00'),
(3, 'Riset komponen UI referensi', '2026-07-18 17:00:00', 2, 'TODO', 'MEDIUM', '2026-06-02 09:10:00', '2026-06-02 09:10:00');


-- ---------------------------------------------------------
-- SUBTASK ASSIGN
-- ---------------------------------------------------------
INSERT INTO `subtask_assign` (`id`, `subtask_id`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 1, 3, '2026-07-05 06:02:00', '2026-07-05 06:02:00'),
(2, 2, 1, '2026-07-05 06:06:00', '2026-07-05 06:06:00'),
(3, 3, 4, '2026-06-02 09:12:00', '2026-06-02 09:12:00');


-- ---------------------------------------------------------
-- NOTIFICATIONS
-- Cocok dengan tab "Notifikasi" di UI Inbox (komentar & penambahan anggota)
-- ---------------------------------------------------------
INSERT INTO `notifications` (`id`, `user_id`, `type`, `message`, `reference_id`, `is_read`, `created_at`) VALUES
(1, 1, 'comment',       'Dwi Prasetyo mengomentari tugas Bug Fixing di board Quilla', 1, false, '2026-07-05 05:30:00'),
(2, 1, 'board_added',   'Andi Wijaya ditambahkan ke board LCD Management',            2, true,  '2026-06-04 08:00:00'),
(3, 3, 'task_assigned', 'Kamu ditugaskan ke task Bug Fixing di board Quilla',         1, true,  '2026-06-01 09:05:00'),
(4, 4, 'mention',       'Kamu disebut oleh Divadan di komentar task Buat Wireframe',  2, false, '2026-06-05 08:05:00');


-- ---------------------------------------------------------
-- CHAT ROOMS
-- id 1 = board chat Quilla (group)
-- id 2,3,4 = personal chat 1-on-1
-- ---------------------------------------------------------
INSERT INTO `chat_rooms` (`id`, `type`, `board_id`, `created_at`, `updated_at`) VALUES
(1, 'board',    1,    '2026-05-06 09:30:00', '2026-05-06 09:30:00'),
(2, 'personal', NULL, '2026-07-05 12:00:00', '2026-07-05 12:41:00'),
(3, 'personal', NULL, '2026-07-04 09:00:00', '2026-07-04 09:20:00'),
(4, 'personal', NULL, '2026-06-30 10:00:00', '2026-06-30 10:15:00');


-- ---------------------------------------------------------
-- CHAT ROOM MEMBERS
-- room 1 (board Quilla) -> semua member board Quilla
-- room 2 (personal)     -> Divadan(1) & Rasyid(2)
-- room 3 (personal)     -> Divadan(1) & Dwi(3)
-- room 4 (personal)     -> Divadan(1) & Andi(4)
-- ---------------------------------------------------------
INSERT INTO `chat_room_members` (`id`, `chat_room_id`, `user_id`, `created_at`) VALUES
(1, 1, 1, '2026-05-06 09:30:00'),
(2, 1, 3, '2026-05-07 10:00:00'),
(3, 1, 4, '2026-05-08 11:00:00'),
(4, 2, 1, '2026-07-05 12:00:00'),
(5, 2, 2, '2026-07-05 12:00:00'),
(6, 3, 1, '2026-07-04 09:00:00'),
(7, 3, 3, '2026-07-04 09:00:00'),
(8, 4, 1, '2026-06-30 10:00:00'),
(9, 4, 4, '2026-06-30 10:00:00');


-- ---------------------------------------------------------
-- CHAT LOGS
-- Cocok dengan bubble dummy yang sudah tampil di UI Personal Chat & Board Chat
-- ---------------------------------------------------------
INSERT INTO `chat_logs` (`id`, `message`, `chat_room_id`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 'Halo tim, gimana progress task-nya?',        1, 2, '2026-07-05 09:00:00', '2026-07-05 09:00:00'),
(2, 'Lagi aku kerjain, sebentar lagi selesai 👍', 1, 1, '2026-07-05 09:02:00', '2026-07-05 09:02:00'),
(3, 'Ini progress terakhirnya',                    1, 3, '2026-07-05 09:05:00', '2026-07-05 09:05:00'),
(4, 'Halo, gimana progress task Bug Fixing?',      2, 2, '2026-07-05 12:10:00', '2026-07-05 12:10:00'),
(5, 'Lagi aku kerjain, sebentar lagi selesai 👍', 2, 1, '2026-07-05 12:15:00', '2026-07-05 12:15:00'),
(6, 'Ini progress terakhirnya',                    2, 2, '2026-07-05 12:41:00', '2026-07-05 12:41:00'),
(7, 'Board Quilla udah aku update deskripsinya',  3, 3, '2026-07-04 09:10:00', '2026-07-04 09:10:00'),
(8, 'Oke siap, nanti aku cek',                     3, 1, '2026-07-04 09:20:00', '2026-07-04 09:20:00'),
(9, 'Meeting jam berapa hari ini?',                4, 4, '2026-06-30 10:15:00', '2026-06-30 10:15:00');


-- ---------------------------------------------------------
-- CHAT ATTACHMENTS
-- ---------------------------------------------------------
INSERT INTO `chat_attachments` (`id`, `chat_log_id`, `attachment_type`, `source`, `created_at`) VALUES
(1, 3, 'image', 'https://placehold.co/300x180/1a1e28/9099b0?text=Screenshot', '2026-07-05 09:05:00');


-- ---------------------------------------------------------
-- CHAT READS
-- room 4, user 1 -> last_read_message_id NULL (contoh belum pernah dibaca sama sekali)
-- ---------------------------------------------------------
INSERT INTO `chat_reads` (`id`, `chat_room_id`, `user_id`, `last_read_message_id`, `updated_at`) VALUES
(1, 1, 1, 3,    '2026-07-05 09:05:00'),
(2, 1, 3, 3,    '2026-07-05 09:05:00'),
(3, 1, 4, 2,    '2026-07-05 09:02:00'),
(4, 2, 1, 6,    '2026-07-05 12:41:00'),
(5, 2, 2, 5,    '2026-07-05 12:15:00'),
(6, 3, 1, 7,    '2026-07-04 09:10:00'),
(7, 3, 3, 8,    '2026-07-04 09:20:00'),
(8, 4, 1, NULL, '2026-06-30 10:00:00'),
(9, 4, 4, 9,    '2026-06-30 10:15:00');
