<?php
    include "connection.php";

    mysqli_query($con, "
    CREATE TABLE stages_intervals (
    stage INT NOT NULL PRIMARY KEY, 
    interval_value INT NOT NULL,
    interval_unit VARCHAR(50) NOT NULL)
    ");

    mysqli_query($con,"
    INSERT INTO stages_intervals (stage, interval_value, interval_unit) VALUES 
    (0, 0, 'seconds'),
    (1, 5, 'seconds'),
    (2, 10, 'seconds'),
    (3, 15, 'seconds'),
    (4, 30, 'seconds'),
    (5, 45, 'seconds'),
    (6, 1, 'day'),
    (7, 2, 'day'),
    (8, 4, 'day'),
    (9, 7, 'day'),
    (10, 11, 'day'),
    (11, 18, 'day'),
    (12, 1, 'month'),
    (13, 2, 'month'),
    (14, 4, 'month'),
    (15, 7, 'month'),
    (16, 12, 'month'),
    (17, 16, 'month'),
    (18, 24, 'month')
    ");
?>