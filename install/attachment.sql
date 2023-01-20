--
-- Table structure for table `attachment`
--

CREATE TABLE `attachment` (
  `id` int NOT NULL,
  `attachment` text COLLATE utf8mb4_general_ci NOT NULL,
  `noteId` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



--
-- Indexes for table `attachment`
--
ALTER TABLE `attachment`
  ADD PRIMARY KEY (`id`);


--
-- AUTO_INCREMENT for table `attachment`
--
ALTER TABLE `attachment`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;
COMMIT;
