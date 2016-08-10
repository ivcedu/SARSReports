<?php
    require("config.php");
    
    $StartDate = filter_input(INPUT_GET, 'StartDate');
    $EndDate = filter_input(INPUT_GET, 'EndDate');
    $LocationID = filter_input(INPUT_GET, 'LocationID');
    $TermID = filter_input(INPUT_GET, 'TermID');
    
    $dbConn->setAttribute(constant('PDO::SQLSRV_ATTR_DIRECT_QUERY'), true);
    
    $qry_create_table_tu301 = "CREATE TABLE #TU301 (Course_Title nvarchar(255), SectionID int, Section_Name nvarchar(255), StudentID nvarchar(255), StudentName nvarchar(255))";
    $qry_create_table_tu301count = "CREATE TABLE #TU301COUNT (StudentID nvarchar(255), TU301_COUNT int)";
    $qry_create_table_tu301stu = "CREATE TABLE #TU301STU (Course_Title nvarchar(255), Section_Name nvarchar(255), StudentID nvarchar(255), StudentName nvarchar(255), TU301_COUNT int)";
    $qry_create_table_result = "CREATE TABLE #RESULT (StudentID nvarchar(255), StudentName nvarchar(255), Hrs int, Mins int, Sars nvarchar(255))";
    $qry_create_table_result2 = "CREATE TABLE #RESULT2 (StudentID nvarchar(255), StudentName nvarchar(255), Hrs int, Mins int, TotalMins int, Sars nvarchar(255))";
    $qry_create_table_result3 = "CREATE TABLE #RESULT3 (StudentID nvarchar(255), StudentName nvarchar(255), TotalMins int)";
    
    $qry_drop_table_tu301 = "DROP TABLE #TU301";
    $qry_drop_table_tu301count = "DROP TABLE #TU301COUNT";
    $qry_drop_table_tu301stu = "DROP TABLE #TU301STU";
    $qry_drop_table_result = "DROP TABLE #RESULT";
    $qry_drop_table_result2 = "DROP TABLE #RESULT2";
    $qry_drop_table_result3 = "DROP TABLE #RESULT3";
    
    $qry_insert_tu301 = "DECLARE @LocationID int SET @LocationID = '".$LocationID."' "
                        . "DECLARE @TermID int SET @TermID = '".$TermID."' "
                        . "INSERT INTO #TU301 "
                        . "SELECT crms.[Subject] + ' ' + crms.Course AS Course_Title, "
                        . "scms.Section_ID, "
                        . "scms.Section_Name, "
                        . "stxr.Alt_ID, "
                        . "stms.Full_Name "
                        . "FROM [SARS].[dbo].[Tbl_Section_Master] AS scms INNER JOIN [SARS].[dbo].[Tbl_Course_Master] AS crms ON scms.Course_ID = crms.Course_ID "
                        . "INNER JOIN [SARS].[dbo].[Tbl_Student_Courses] AS stcr ON scms.Section_ID = stcr.Section_ID "
                        . "INNER JOIN [SARS].[dbo].[Tbl_Student_Master] AS stms ON stcr.Student_ID = stms.Student_ID "
                        . "INNER JOIN [SARS].[dbo].[Tbl_Student_ID_XRef] AS stxr ON stms.Student_ID = stxr.Student_ID "
                        . "INNER JOIN [SARS].[dbo].[Tbl_Button_Section_XRef] AS btsc ON scms.Section_ID = btsc.Section_ID "
                        . "INNER JOIN [SARS].[dbo].[Tbl_Button_Props] AS btpr ON btsc.Button_ID = btpr.Button_ID "
                        . "WHERE stcr.Dropped = 0 AND crms.[Subject] + ' ' + crms.Course = 'TU 301' "
                        . "AND scms.Term_ID = @TermID "
                        . "AND btpr.Location_ID = @LocationID";
    
    $qry_insert_tu301count = "INSERT INTO #TU301COUNT SELECT StudentID, COUNT(StudentID) FROM #TU301 GROUP BY StudentID";
    
    $qry_insert_tu301stu = "INSERT INTO #TU301STU "
                        . "SELECT tus.Course_Title, tus.Section_Name, tus.StudentID, tus.StudentName, tuc.TU301_COUNT "
                        . "FROM #TU301 AS tus LEFT JOIN #TU301COUNT AS tuc ON tus.StudentID = tuc.StudentID";
    
    $qry_insert_1 = "DECLARE @StartDate datetime SET @StartDate = '".$StartDate."' "
                        . "DECLARE @EndDate datetime SET @EndDate = '".$EndDate."' "
                        . "DECLARE @LocationID int SET @LocationID = '".$LocationID."' "
                        . "INSERT INTO #RESULT "
                        . "SELECT sxf.Alt_ID, stu.Full_Name, '0', gdc.Duration, 'GRID' "
                        . "FROM [SARS].[dbo].[Tbl_Student_History] AS sht INNER JOIN [SARS].[dbo].[Tbl_Grid_Current] AS gdc ON sht.Sched_ID = gdc.Sched_ID "
                        . "INNER JOIN [SARS].[dbo].[Tbl_Student_Master] AS stu ON stu.Student_ID = sht.Student_ID "
                        . "INNER JOIN [SARS].[dbo].[Tbl_Student_ID_XRef] AS sxf ON sxf.Student_ID = stu.Student_ID "
                        . "WHERE gdc.Sched_Date BETWEEN @StartDate AND @EndDate "
                        . "AND sht.Attend_Flag = 'Y' "
                        . "AND gdc.Location_ID = @LocationID ";
    
    $qry_insert_2 = "DECLARE @StartDate datetime SET @StartDate = '".$StartDate."' "
                        . "DECLARE @EndDate datetime SET @EndDate = '".$EndDate."' "
                        . "DECLARE @LocationID int SET @LocationID = '".$LocationID."' "
                        . "INSERT INTO #RESULT "
                        . "SELECT sxf.Alt_ID, stu.Full_Name, "
                        . "DATEDIFF(SECOND, sht.Start_Time, sht.Stop_Time) / 3600, CAST(ROUND((DATEDIFF(SECOND, sht.Start_Time, sht.Stop_Time) % 3600) / 60.0, 0) AS int), "
                        . "'TRAK' "
                        . "FROM [SARS].[dbo].[Tbl_Student_History] AS sht INNER JOIN [SARS].[dbo].[Tbl_Student_ID_XRef] AS sxf ON sht.Student_ID = sxf.Student_ID "
                        . "INNER JOIN [SARS].[dbo].[Tbl_Student_Master] AS stu ON stu.Student_ID = sxf.Student_ID "
                        . "WHERE sht.Sched_Date BETWEEN @StartDate AND @EndDate "
                        . "AND sht.[User_Name] = 'SARS·TRAK' AND sht.Trak_CheckIN = 0 " 
                        . "AND sht.Location_ID = @LocationID";
    
    $qry_insert_3 = "INSERT INTO #RESULT2 "
                        . "SELECT StudentID, StudentName, SUM(Hrs) AS Hrs, SUM(Mins) AS Mins, (SUM(Hrs) * 60) +  SUM(Mins) AS TotalMins, Sars "
                        . "FROM #RESULT GROUP BY StudentID, StudentName, Sars";
    
    $qry_insert_4 = "INSERT INTO #RESULT3 "
                        . "SELECT StudentID, StudentName, SUM(TotalMins) "
                        . "FROM #RESULT2 GROUP BY StudentID, StudentName";
    
    $qry_get_main = "SELECT tus.StudentID, tus.StudentName, tus.Course_Title, tus.Section_Name, (rst.TotalMins / tus.TU301_COUNT) / 60 AS Hrs, "
                        . "CEILING((CAST(rst.TotalMins AS decimal(10, 2)) / tus.TU301_COUNT) % 60) AS Mins, "
                        . "CAST((CEILING((CAST(rst.TotalMins AS decimal(10, 2)) / tus.TU301_COUNT) % 60) % 60) / 60.0 AS decimal(10, 2)) AS MinsNumber "
                        . "FROM #TU301STU AS tus LEFT JOIN #RESULT3 AS rst ON tus.StudentID = rst.StudentID "
                        . "WHERE rst.TotalMins IS NOT NULL "
                        . "ORDER BY tus.StudentName, tus.Section_Name ASC";
    
    // create table
    $dbConn->query($qry_create_table_tu301);
    $dbConn->query($qry_create_table_tu301count);
    $dbConn->query($qry_create_table_tu301stu);
    $dbConn->query($qry_create_table_result);
    $dbConn->query($qry_create_table_result2);
    $dbConn->query($qry_create_table_result3);
    
    // insert query
    $dbConn->query($qry_insert_tu301);
    $dbConn->query($qry_insert_tu301count);
    $dbConn->query($qry_insert_tu301stu);
    $dbConn->query($qry_insert_1);
    $dbConn->query($qry_insert_2);
    $dbConn->query($qry_insert_3);
    $dbConn->query($qry_insert_4);

    $cmd = $dbConn->prepare($qry_get_main);
    $cmd->execute();
    $data = $cmd->fetchAll();
    
    // drop table
    $dbConn->query($qry_drop_table_tu301);
    $dbConn->query($qry_drop_table_tu301count);
    $dbConn->query($qry_drop_table_tu301stu);
    $dbConn->query($qry_drop_table_result);
    $dbConn->query($qry_drop_table_result2);
    $dbConn->query($qry_drop_table_result3);

    $filename = "pos_attend_ticket_list.csv";  
    header("Content-Disposition: attachment; filename=\"$filename\"");
    header("Content-Type: text/csv;");
    $out = fopen("php://output", 'w+');

    // Write the spreadsheet column titles / labels
    fputcsv($out, array('StudentID','Student Name','Course Title','Ticket','Hrs','Mins','Mins in digit'));
    // Write all the user records to the spreadsheet
    foreach($data as $row) {
        fputcsv($out, array($row['StudentID'], $row['StudentName'], $row['Course_Title'], $row['Section_Name'], $row['Hrs'], $row['Mins'], $row['MinsNumber']));
    }
    
    fclose($out);
    exit;