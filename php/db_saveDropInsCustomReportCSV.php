<?php
    require("config.php");
    
    $StartDate = $_GET['StartDate'];
    $EndDate = $_GET['EndDate'];
    $SarsLocation = $_GET['SarsLocation'];
    
    $str_location_id = "";
    if ($SarsLocation != "0") {
        $str_location_id = " AND wkn.Location_ID = ".$SarsLocation."";
    }
    
    $dbConn->setAttribute(constant('PDO::SQLSRV_ATTR_DIRECT_QUERY'), true);
    
    $qry_create_table_dropin = "CREATE TABLE #DROPIN (Sch_Month int, Sch_Year nvarchar(255), WkStatus nvarchar(255), Waiting float)";
    $qry_create_table_dropin2 = "CREATE TABLE #DROPIN2 (Sch_Month int, Sch_Year nvarchar(255), WkStatus nvarchar(255), Waiting float, Counts int)";
    $qry_create_table_notseen = "CREATE TABLE #NOTSEEN (Sch_Month int, Sch_Year nvarchar(255), WkStatus nvarchar(255), Waiting float)";
    $qry_create_table_notseen2 = "CREATE TABLE #NOTSEEN2 (Sch_Month int, Sch_Year nvarchar(255), WkStatus nvarchar(255), Waiting float, Counts int)";
    $qry_create_table_deleted = "CREATE TABLE #DELETED (Sch_Month int, Sch_Year nvarchar(255), WkStatus nvarchar(255))";
    $qry_create_table_deleted2 = "CREATE TABLE #DELETED2 (Sch_Month int, Sch_Year nvarchar(255), WkStatus nvarchar(255), Counts int)";
    
    $qry_drop_table_dropin = "DROP TABLE #DROPIN";
    $qry_drop_table_dropin2 = "DROP TABLE #DROPIN2";
    $qry_drop_table_notseen = "DROP TABLE #NOTSEEN";
    $qry_drop_table_notseen2 = "DROP TABLE #NOTSEEN2";
    $qry_drop_table_deleted = "DROP TABLE #DELETED";
    $qry_drop_table_deleted2 = "DROP TABLE #DELETED2";
    
    $qry_get_dropin = "INSERT INTO #DROPIN SELECT MONTH(wkn.Sched_Date), YEAR(wkn.Sched_Date), 'Drop-Ins Seen', DATEDIFF(MINUTE, wkn.Arrival_Time, wkn.Serviced_Time) "
                        ."FROM [SARS].[dbo].[Tbl_WalkIns] AS wkn LEFT JOIN [SARS].[dbo].[Tbl_Student_Master] AS stu ON stu.Student_ID = wkn.Student_ID "
                        ."LEFT JOIN [SARS].[dbo].[Tbl_Student_ID_XRef] AS sxf ON sxf.Student_ID = stu.Student_ID "
                        ."WHERE wkn.Sched_Date BETWEEN '".$StartDate."' AND '".$EndDate."' AND wkn.Walkin_Status = 'Serviced'".$str_location_id."";
    $qry_get_dropin2 = "INSERT INTO #DROPIN2 SELECT Sch_Month, Sch_Year, WkStatus, SUM(Waiting) / COUNT(*), COUNT(*) FROM #DROPIN "
                        ."GROUP BY Sch_Month, Sch_Year, WkStatus";
    
    $qry_get_notseen = "INSERT INTO #NOTSEEN SELECT MONTH(wkn.Sched_Date), YEAR(wkn.Sched_Date), 'Drop-Ins Not Seen', "
                        ."CASE WHEN DATEDIFF(MINUTE, wkn.Arrival_Time, wkn.Serviced_Time) IS NULL THEN '0' ELSE DATEDIFF(MINUTE, wkn.Arrival_Time, wkn.Serviced_Time) END "
                        ."FROM [SARS].[dbo].[Tbl_WalkIns] AS wkn LEFT JOIN [SARS].[dbo].[Tbl_Student_Master] AS stu ON stu.Student_ID = wkn.Student_ID "
                        ."LEFT JOIN [SARS].[dbo].[Tbl_Student_ID_XRef] AS sxf ON sxf.Student_ID = stu.Student_ID "
                        ."WHERE wkn.Sched_Date BETWEEN '".$StartDate."' AND '".$EndDate."' AND wkn.History_ID = 0".$str_location_id."";
    $qry_get_notseen2 = "INSERT INTO #NOTSEEN2 "
                        ."SELECT Sch_Month, Sch_Year, WkStatus, SUM(Waiting) / COUNT(*), COUNT(*) FROM #NOTSEEN "
                        ."GROUP BY Sch_Month, Sch_Year, WkStatus";
    
    $qry_get_deleted = "INSERT INTO #DELETED SELECT MONTH(wkn.Sched_Date), YEAR(wkn.Sched_Date), 'Deleted (No Shows)' "
                        ."FROM [SARS].[dbo].[Tbl_WalkIns] AS wkn LEFT JOIN [SARS].[dbo].[Tbl_Student_Master] AS stu ON stu.Student_ID = wkn.Student_ID "
                        ."LEFT JOIN [SARS].[dbo].[Tbl_Student_ID_XRef] AS sxf ON sxf.Student_ID = stu.Student_ID "
                        ."WHERE wkn.Sched_Date BETWEEN '".$StartDate."' AND '".$EndDate."' AND wkn.Walkin_Status = 'Deleted'".$str_location_id."";
    $qry_get_deleted2 = "INSERT INTO #DELETED2 SELECT Sch_Month, Sch_Year, WkStatus, COUNT(*) FROM #DELETED "
                        ."GROUP BY Sch_Month, Sch_Year, WkStatus";
    
    $qry_get_main = "SELECT CASE WHEN dpn.Sch_Month = 1 THEN 'January' "
                    ."WHEN dpn.Sch_Month = 2 THEN 'February' "
                    ."WHEN dpn.Sch_Month = 3 THEN 'March' "
                    ."WHEN dpn.Sch_Month = 4 THEN 'April' "
                    ."WHEN dpn.Sch_Month = 5 THEN 'May' "
                    ."WHEN dpn.Sch_Month = 6 THEN 'June' "
                    ."WHEN dpn.Sch_Month = 7 THEN 'July' "
                    ."WHEN dpn.Sch_Month = 8 THEN 'Auguest' "
                    ."WHEN dpn.Sch_Month = 9 THEN 'September' "
                    ."WHEN dpn.Sch_Month = 10 THEN 'October' "
                    ."WHEN dpn.Sch_Month = 11 THEN 'November' "
                    ."WHEN dpn.Sch_Month = 12 THEN 'December' "
                    ."END AS [Month], "
                    ."dpn.Sch_Year AS [Year], dpn.Counts AS DropInsSeen, nts.Counts AS DropInsNotSeen, ROUND((dpn.Waiting + nts.Waiting) / 2, 0) AS AvgWaitTime, "
                    ."CASE WHEN del.Counts IS NULL THEN '0' ELSE del.Counts END AS Deleted "
                    ."FROM #DROPIN2 AS dpn LEFT JOIN #NOTSEEN2 nts ON dpn.Sch_Month = nts.Sch_Month AND dpn.Sch_Year = nts.Sch_Year "
                    ."LEFT JOIN #DELETED2 del ON dpn.Sch_Month = del.Sch_Month AND dpn.Sch_Year = del.Sch_Year "
                    ."ORDER BY dpn.Sch_Year, dpn.Sch_Month ASC";
    
    $dbConn->query($qry_create_table_dropin);
    $dbConn->query($qry_create_table_dropin2);
    $dbConn->query($qry_create_table_notseen);
    $dbConn->query($qry_create_table_notseen2);
    $dbConn->query($qry_create_table_deleted);
    $dbConn->query($qry_create_table_deleted2);
    
    $dbConn->query($qry_get_dropin);
    $dbConn->query($qry_get_dropin2);
    $dbConn->query($qry_get_notseen);
    $dbConn->query($qry_get_notseen2);
    $dbConn->query($qry_get_deleted);
    $dbConn->query($qry_get_deleted2);

    $cmd = $dbConn->prepare($qry_get_main);
    $cmd->execute();
    $data = $cmd->fetchAll();
    
    $dbConn->query($qry_drop_table_dropin);
    $dbConn->query($qry_drop_table_dropin2);
    $dbConn->query($qry_drop_table_notseen);
    $dbConn->query($qry_drop_table_notseen2);
    $dbConn->query($qry_drop_table_deleted);
    $dbConn->query($qry_drop_table_deleted2);

    $filename = "export_list.csv";  
    header("Content-Disposition: attachment; filename=\"$filename\"");
    header("Content-Type: text/csv;");
    $out = fopen("php://output", 'w+');

    // Write the spreadsheet column titles / labels
    fputcsv($out, array('Month','Year','DropInsSeen','DropInsNotSeen', 'AvgWaitTime', Deleted));
    // Write all the user records to the spreadsheet
    foreach($data as $row)
    {
        fputcsv($out, array($row['Month'], $row['Year'], $row['DropInsSeen'], $row['DropInsNotSeen'], $row['AvgWaitTime'], $row['Deleted']));
    }
    
    fclose($out);
    exit;