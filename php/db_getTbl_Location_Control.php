<?php
    require("config.php");
      
    $query = "SELECT Location_ID, Location_Code FROM [".$dbDatabase."].[dbo].[Tbl_Location_Control]";

    $cmd = $dbConn->prepare($query);
    $cmd->execute(); 
    $data = $cmd->fetchAll();

    echo json_encode($data);