<?php
    require("config.php");
      
    $Term_ID = filter_input(INPUT_POST, 'Term_ID');
    
    $query = "SELECT  * FROM [SARS].[dbo].[Tbl_Term_Master] WHERE Term_ID = '".$Term_ID."'";

    $cmd = $dbConn->prepare($query);
    $cmd->execute(); 
    $data = $cmd->fetchAll();

    echo json_encode($data);