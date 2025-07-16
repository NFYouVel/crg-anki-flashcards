<?php
    include "connection.php";

    mysqli_query($con, "
        CREATE TABLE stages_intervals (
            stage INT NOT NULL PRIMARY KEY, 
            interval_value INT NOT NULL,
            interval_unit VARCHAR(50) NOT NULL,
            card_tier CHAR(5)
        )
    ");

    mysqli_query($con,"
    INSERT INTO stages_intervals (stage, interval_value, interval_unit, card_tier) VALUES
        (0, 0, 'second', NULL),
        (1, 5, 'second', 'E'),
        (2, 10, 'second', 'E'),
        (3, 15, 'second', 'E'),
        (4, 30, 'second', 'E'),
        (5, 45, 'second', 'E'),
        (6, 1, 'day', 'D'),
        (7, 2, 'day', 'D'),
        (8, 4, 'day', 'D'),
        (9, 7, 'day', 'C'),
        (10, 11, 'day', 'C'),
        (11, 18, 'day', 'C'),
        (12, 1, 'month', 'B'),
        (13, 2, 'month', 'B'),
        (14, 4, 'month', 'A'),
        (15, 7, 'month', 'A'),
        (16, 12, 'month', 'S'),
        (17, 16, 'month', 'S'),
        (18, 24, 'month', 'SS');
    ");
?>