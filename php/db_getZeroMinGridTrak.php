<?php
    require("config.php");
    
    $StartDate = filter_input(INPUT_POST, 'StartDate');
    $EndDate = filter_input(INPUT_POST, 'EndDate');
    $LocationID = filter_input(INPUT_POST, 'LocationID');
    $TermID = filter_input(INPUT_POST, 'TermID');
    
    $dbConn->setAttribute(constant('PDO::SQLSRV_ATTR_DIRECT_QUERY'), true);
    
    $qry_create_table_1 = "CREATE TABLE #BUTTON (Button_ID int)";
    $qry_create_table_2 = "CREATE TABLE #STUCOURSES (Course_Title nvarchar(255), Section_ID int, Section_Name nvarchar(255), Course_Descrip nvarchar(255), Student_ID nvarchar(255), IVC_ID nvarchar(255), Student_Name nvarchar(255))";
    $qry_create_table_3 = "CREATE TABLE #SARSZERO (StudentID nvarchar(255), StudentName nvarchar(255), Sars nvarchar(255), TotalMins int)";
    $qry_create_table_4 = "CREATE TABLE #SARSZERO2 (StudentID nvarchar(255))";
    $qry_create_table_5 = "CREATE TABLE #SARSZERO3 (StudentID nvarchar(255))";
    $qry_create_table_6 = "CREATE TABLE #RESULT (StudentID nvarchar(255), StudentName nvarchar(255), Hrs int, Mins int, Sars nvarchar(255), Section_ID int)";
    $qry_create_table_7 = "CREATE TABLE #RESULT2 (StudentID nvarchar(255), StudentName nvarchar(255), Hrs int, Mins int, TotalMins int, Sars nvarchar(255), Section_ID int)";
    
    $qry_drop_table_1 = "DROP TABLE #BUTTON";
    $qry_drop_table_2 = "DROP TABLE #STUCOURSES";
    $qry_drop_table_3 = "DROP TABLE #SARSZERO";
    $qry_drop_table_4 = "DROP TABLE #SARSZERO2";
    $qry_drop_table_5 = "DROP TABLE #SARSZERO3";
    $qry_drop_table_6 = "DROP TABLE #RESULT";
    $qry_drop_table_7 = "DROP TABLE #RESULT2";
    
    $qry_insert_1 = "INSERT INTO #BUTTON SELECT	Button_ID FROM [SARS].[dbo].[Tbl_Button_Props] WHERE Location_ID = '".$LocationID."' and Disable_Button = 0";
    
    $qry_insert_2 = "INSERT INTO #STUCOURSES "
                    . "SELECT crms.[Subject] + ' ' + crms.Course AS Course_Title, scms.Section_ID, scms.Section_Name, scms.[Description], stxr.Student_ID, stxr.Alt_ID AS Student_ID, stms.Full_Name "
                    . "FROM #BUTTON AS butn INNER JOIN [SARS].[dbo].[Tbl_Button_Section_XRef] AS btsx ON butn.Button_ID = btsx.Button_ID "
                    . "INNER JOIN [SARS].[dbo].[Tbl_Section_Master] AS scms ON btsx.Section_ID = scms.Section_ID "
                    . "INNER JOIN [SARS].[dbo].[Tbl_Course_Master] AS crms ON scms.Course_ID = crms.Course_ID "
                    . "INNER JOIN [SARS].[dbo].[Tbl_Student_Courses] AS stcr ON btsx.Section_ID = stcr.Section_ID "
                    . "INNER JOIN [SARS].[dbo].[Tbl_Student_Master] AS stms ON stcr.Student_ID = stms.Student_ID "
                    . "INNER JOIN [SARS].[dbo].[Tbl_Student_ID_XRef] AS stxr ON stms.Student_ID = stxr.Student_ID "
                    . "WHERE stcr.Dropped = 0 AND scms.Term_ID = '".$TermID."'";   
    
    $qry_insert_3 = "INSERT INTO #RESULT "
                        . "SELECT sxf.Alt_ID, stu.Full_Name, '0', gdc.Duration, 'GRID', 0 "
                        . "FROM [SARS].[dbo].[Tbl_Student_History] AS sht LEFT JOIN [SARS].[dbo].[Tbl_Grid_Current] AS gdc ON sht.Sched_ID = gdc.Sched_ID "
                        . "LEFT JOIN [SARS].[dbo].[Tbl_Student_Master] AS stu ON stu.Student_ID = sht.Student_ID "
                        . "LEFT JOIN [SARS].[dbo].[Tbl_Student_ID_XRef] AS sxf ON sxf.Student_ID = stu.Student_ID "
                        . "WHERE gdc.Sched_Date BETWEEN '".$StartDate."' AND '".$EndDate."' AND sht.Attend_Flag = 'Y' AND gdc.Location_ID = '".$LocationID."'";    
    
    $qry_insert_4 = "INSERT INTO #RESULT "
                        . "SELECT sxf.Alt_ID, stu.Full_Name, DATEDIFF(SECOND, sht.Start_Time, sht.Stop_Time) / 3600, CAST(ROUND((DATEDIFF(SECOND, sht.Start_Time, sht.Stop_Time) % 3600) / 60.0, 0) AS int), 'TRAK', aprs.Section_ID "
                        . "FROM [SARS].[dbo].[Tbl_Student_History] AS sht LEFT JOIN [SARS].[dbo].[Tbl_Appt_Reasons] AS aprs ON sht.History_ID = aprs.History_ID "
                        . "LEFT JOIN [SARS].[dbo].[Tbl_Student_ID_XRef] AS sxf ON sht.Student_ID = sxf.Student_ID "
                        . "LEFT JOIN [SARS].[dbo].[Tbl_Student_Master] AS stu ON stu.Student_ID = sxf.Student_ID "
                        . "WHERE sht.Sched_Date BETWEEN '".$StartDate."' AND '".$EndDate."' AND sht.[User_Name] = 'SARS·TRAK' AND sht.Trak_CheckIN = 0 AND sht.Location_ID = '".$LocationID."'";
    
    $qry_insert_5 = "INSERT INTO #RESULT2 "
                        ."SELECT StudentID, StudentName, SUM(Hrs) AS Hrs, SUM(Mins) AS Mins, (SUM(Hrs) * 60) +  SUM(Mins) AS TotalMins, Sars, Section_ID "
                        ."FROM #RESULT GROUP BY StudentID, StudentName, Sars, Section_ID";
    
    $qry_insert_6 = "INSERT INTO #SARSZERO2 SELECT StudentID FROM #RESULT2 WHERE Sars = 'GRID' GROUP BY StudentID";
    
    $qry_insert_7 = "INSERT INTO #SARSZERO2 SELECT StudentID FROM #RESULT2 WHERE Sars = 'TRAK' AND TotalMins > 5 GROUP BY StudentID";
    
    $qry_insert_8 = "INSERT INTO #SARSZERO3 SELECT StudentID FROM #SARSZERO2 GROUP BY StudentID";
    
    $qry_get_main = "SELECT stcr.IVC_ID, stcr.Student_Name, stcr.Course_Title, stcr.Section_Name "
                    . "FROM #STUCOURSES AS stcr LEFT JOIN #SARSZERO3 AS zero ON stcr.IVC_ID = zero.StudentID "
                    . "WHERE zero.StudentID IS NULL "
                    . "ORDER BY stcr.Student_Name ASC";
    
    // create table
    $dbConn->query($qry_create_table_1);
    $dbConn->query($qry_create_table_2);
    $dbConn->query($qry_create_table_3);
    $dbConn->query($qry_create_table_4);
    $dbConn->query($qry_create_table_5);
    $dbConn->query($qry_create_table_6);
    $dbConn->query($qry_create_table_7);
    
    $dbConn->query($qry_insert_1);
    $dbConn->query($qry_insert_2);
    $dbConn->query($qry_insert_3);
    $dbConn->query($qry_insert_4);
    $dbConn->query($qry_insert_5);
    $dbConn->query($qry_insert_6);
    $dbConn->query($qry_insert_7);
    $dbConn->query($qry_insert_8);

    $cmd = $dbConn->prepare($qry_get_main);
    $cmd->execute();
    $data = $cmd->fetchAll();
    
    // drop table
    $dbConn->query($qry_drop_table_1);
    $dbConn->query($qry_drop_table_2);
    $dbConn->query($qry_drop_table_3);
    $dbConn->query($qry_drop_table_4);
    $dbConn->query($qry_drop_table_5);
    $dbConn->query($qry_drop_table_6);
    $dbConn->query($qry_drop_table_7);

    echo json_encode($data);