<?php
    require("config.php");
      
    $query = "SELECT TOP 3 * FROM [".$dbDatabase."].[dbo].[Tbl_Term_Master] ORDER BY Term_ID DESC";

    $cmd = $dbConn->prepare($query);
    $cmd->execute(); 
    $data = $cmd->fetchAll();

    echo json_encode($data);