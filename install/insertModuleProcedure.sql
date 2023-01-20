
DROP PROCEDURE IF EXISTS insert_module;
DELIMITER $$
CREATE PROCEDURE insert_module(
  IN my_data JSON
)
BEGIN
  -- Declare iterator variable to use it later on in the loop
  DECLARE i INT DEFAULT 0;
  DECLARE j INT DEFAULT 0;

  -- Retrieve values from JSON
  SET @subject = JSON_UNQUOTE(JSON_EXTRACT(my_data, '$.subject'));
  SET @description = JSON_UNQUOTE(JSON_EXTRACT(my_data, '$.description'));
  SET @due_date = JSON_UNQUOTE(JSON_EXTRACT(my_data, '$.due_date'));
  SET @start_date = JSON_UNQUOTE(JSON_EXTRACT(my_data, '$.start_date'));
  SET @status = JSON_UNQUOTE(JSON_EXTRACT(my_data, '$.status'));
  SET @priority = JSON_UNQUOTE(JSON_EXTRACT(my_data, '$.priority'));
  SET @created_by = JSON_UNQUOTE(JSON_EXTRACT(my_data, '$.created_by'));
  SET @notes = JSON_EXTRACT(my_data, '$.note');
  -- Insert module
  INSERT INTO `modules`(`subject`, `description`, `startDate`, `dueDate`, `status`, `priority`, `createdBy`) VALUES (
    @subject,
    @description,
    @start_date,
    @due_date,
    @status,
    @priority,
    @created_by
    );
  -- Retrieve inserted id to reuse it in module_note
  SET @last_module = LAST_INSERT_ID();

  -- Get notes length for the loop
  SET @notes_length = JSON_LENGTH(@notes);
  -- Execute loop over notes length
  WHILE i < @notes_length DO
    -- Retrieve current note from notes array
    SET @note_subject = JSON_UNQUOTE(JSON_EXTRACT(my_data, CONCAT('$.note[',i,'].subject')));
    SET @note = JSON_UNQUOTE(JSON_EXTRACT(my_data, CONCAT('$.note[',i,'].note')));
    SET @attachment = JSON_UNQUOTE(JSON_EXTRACT(my_data, CONCAT('$.note[',i,'].attachment')));
   
    -- Insert Note
   
    INSERT INTO `notes`(`subject`, `note`, `moduleId`, `createdBy`) VALUES (
      @note_subject,
      @note,
      @last_module,
      @created_by
    );

    SET @last_note = LAST_INSERT_ID();

    SET @attachment_length = JSON_LENGTH(@attachment);

    SET j = 0;
    
    WHILE j < @attachment_length DO
        SET @attachment_file = JSON_UNQUOTE(JSON_EXTRACT(@attachment, CONCAT('$[',j,']')));
        INSERT INTO `attachment`(`attachment`, `noteId`) VALUES  (
          @attachment_file,
          @last_note
        );
     -- -- Add step to iterator
      SELECT j + 1 INTO j;
    END WHILE;   
    -- -- Add step to iterator
    SELECT i + 1 INTO i;
  END WHILE;
END $$
DELIMITER ;