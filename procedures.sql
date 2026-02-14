DELIMITER $$

-- User managements
DROP PROCEDURE IF EXISTS create_user;
CREATE PROCEDURE IF NOT EXISTS create_user (
    IN name VARCHAR(63),
    IN email VARCHAR(127),
    IN identifier VARCHAR(31),
    IN role ENUM('STUDENT', 'TEACHER', 'VP', 'ADMIN'),
    IN class_id INT
)
BEGIN
    
END$$

DELIMITER ;