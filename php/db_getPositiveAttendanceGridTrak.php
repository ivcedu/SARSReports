<?php
    require("config.php");
    
    $StartDate = $_POST['StartDate'];
    $EndDate = $_POST['EndDate'];
    $LocationID = $_POST['LocationID'];
    
    $dbConn->setAttribute(constant('PDO::SQLSRV_ATTR_DIRECT_QUERY'), true);
    
    $qry_create_table_result = "CREATE TABLE #RESULT (StudentID nvarchar(255), StudentName nvarchar(255), Hrs int, Mins int, Sars nvarchar(255))";
    $qry_create_table_result2 = "CREATE TABLE #RESULT2 (StudentID nvarchar(255), StudentName nvarchar(255), Hrs int, Mins int, TotalMins int, Sars nvarchar(255))";
    
    $qry_drop_table_result = "DROP TABLE #RESULT";
    $qry_drop_table_result2 = "DROP TABLE #RESULT2";
    
    $qry_insert_1 = "INSERT INTO #RESULT "
                        ."SELECT sxf.Alt_ID, stu.Full_Name, '0', gdc.Duration, 'GRID' "
                        ."FROM [SARS].[dbo].[Tbl_Student_History] AS sht LEFT JOIN [SARS].[dbo].[Tbl_Grid_Current] AS gdc ON sht.Sched_ID = gdc.Sched_ID "
                        ."LEFT JOIN [SARS].[dbo].[Tbl_Student_Master] AS stu ON stu.Student_ID = sht.Student_ID "
                        ."LEFT JOIN [SARS].[dbo].[Tbl_Student_ID_XRef] AS sxf ON sxf.Student_ID = stu.Student_ID "
                        ."WHERE gdc.Sched_Date BETWEEN '".$StartDate."' AND '".$EndDate."' AND sht.Attend_Flag = 'Y' AND gdc.Location_ID = '".$LocationID."'";    
    
    $qry_insert_2 = "INSERT INTO #RESULT "
                        ."SELECT sxf.Alt_ID, stu.Full_Name, DATEDIFF(SECOND, sht.Start_Time, sht.Stop_Time) / 3600, CAST(ROUND((DATEDIFF(SECOND, sht.Start_Time, sht.Stop_Time) % 3600) / 60.0, 0) AS int), 'TRAK' "
                        ."FROM [SARS].[dbo].[Tbl_Student_History] AS sht LEFT JOIN [SARS].[dbo].[Tbl_Student_ID_XRef] AS sxf ON sht.Student_ID = sxf.Student_ID "
                        ."LEFT JOIN [SARS].[dbo].[Tbl_Student_Master] AS stu ON stu.Student_ID = sxf.Student_ID "
                        ."WHERE sht.Sched_Date BETWEEN '".$StartDate."' AND '".$EndDate."' AND sht.[User_Name] = 'SARS·TRAK' AND sht.Trak_CheckIN = 0 AND sht.Location_ID = '".$LocationID."'";
    
    $qry_insert_3 = "INSERT INTO #RESULT2 "
                        ."SELECT StudentID, StudentName, SUM(Hrs) AS Hrs, SUM(Mins) AS Mins, (SUM(Hrs) * 60) +  SUM(Mins) AS TotalMins, Sars "
                        ."FROM #RESULT GROUP BY StudentID, StudentName, Sars";
    
    $qry_get_main = "SELECT StudentID, StudentName, SUM(TotalMins) / 60 AS Hrs, SUM(TotalMins) % 60 AS Mins, CAST((SUM(TotalMins) % 60) / 60.0 AS decimal(10, 2)) AS MinsNumber "
                        ."FROM #RESULT2 GROUP BY StudentID, StudentName ORDER BY StudentName ASC";
    
//    $qry_test = "SELECT * FROM #RESULT2";
    
    // create table
    $dbConn->query($qry_create_table_result);
    $dbConn->query($qry_create_table_result2);
    
    $dbConn->query($qry_insert_1);
    $dbConn->query($qry_insert_2);
    $dbConn->query($qry_insert_3);

    $cmd = $dbConn->prepare($qry_get_main);
    $cmd->execute();
    $data = $cmd->fetchAll();
    
    // drop table
    $dbConn->query($qry_drop_table_result);
    $dbConn->query($qry_drop_table_result2);

    echo json_encode($data);