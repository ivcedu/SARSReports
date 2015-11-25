<?php
    require("config.php");
      
    $query = "SELECT TOP 6 * FROM [SARS].[dbo].[Tbl_Term_Master] ORDER BY Term_ID DESC";

    $cmd = $dbConn->prepare($query);
    $cmd->execute(); 
    $data = $cmd->fetchAll();

    echo json_encode($data);