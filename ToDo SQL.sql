CREATE TABLE `subtasks` (
  `id` int(11) NOT NULL,
  `todo_id` int(11) NOT NULL,
  `subtask` varchar(255) NOT NULL,
  `status` enum('Pending','Completed') DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `todos` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `subtasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `todo_id` (`todo_id`);

ALTER TABLE `todos`
  ADD PRIMARY KEY (`id`);


ALTER TABLE `subtasks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `todos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `subtasks`
  ADD CONSTRAINT `subtasks_ibfk_1` FOREIGN KEY (`todo_id`) REFERENCES `todos` (`id`) ON DELETE CASCADE;
COMMIT;