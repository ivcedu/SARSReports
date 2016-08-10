// get AD login info ///////////////////////////////////////////////////////////
function getLoginUserInfo(php_file, user, pass) {
    var result = new Array();
    $.ajax({
        type:"POST",
        datatype:"json",
        url:php_file,
        data:{username:user, password:pass},
        async: false,  
        success:function(data) {
            result = JSON.parse(data);
        }
    });
    return result;
}

// get DB //////////////////////////////////////////////////////////////////////
function db_getTbl_Term_Master() {   
    var result = new Array();
    $.ajax({
        type:"POST",
        url:"php/db_getTbl_Term_Master.php",
        async: false,  
        success:function(data) {
            result = JSON.parse(data);
        }
    });
    return result;
}

function db_getPositiveAttendanceGridTrak(StartDate, EndDate, LocationID, TermID) {   
    var result = new Array();
    $.ajax({
        type:"POST",
        url:"php/db_getPositiveAttendanceGridTrak.php",
        data:{StartDate:StartDate, EndDate:EndDate, LocationID:LocationID, TermID:TermID},
        async: false,  
        success:function(data) {
            result = JSON.parse(data);
        }
    });
    return result;
}

function db_getDropInCustomReport(StartDate, EndDate, SarsLocation) {   
    var result = new Array();
    $.ajax({
        type:"POST",
        url:"php/db_getDropInsCustomReport.php",
        data:{StartDate:StartDate, EndDate:EndDate, SarsLocation:SarsLocation},
        async: false,  
        success:function(data) {
            result = JSON.parse(data);
        }
    });
    return result;
}

function db_getZeroMinGridTrak(StartDate, EndDate, LocationID, TermID) {   
    var result = new Array();
    $.ajax({
        type:"POST",
        url:"php/db_getZeroMinGridTrak.php",
        data:{StartDate:StartDate, EndDate:EndDate, LocationID:LocationID, TermID:TermID},
        async: false,  
        success:function(data) {
            result = JSON.parse(data);
        }
    });
    return result;
}

//function db_getSurveyCourseCount(TermCode) {
//    var result = "";
//    $.ajax({
//        type:"POST",
//        url:"php/db_getSurveyCourseCount.php",
//        data:{TermCode:TermCode},
//        async: false,  
//        success:function(data) {
//            result = JSON.parse(data);
//        }
//    });
//    return result;
//}

// insert DB ///////////////////////////////////////////////////////////////////
//function db_insertAdmin(AdminName, AdminEmail) {
//    var ResultID = "";
//    $.ajax({
//        type:"POST",
//        url:"php/db_insertAdmin.php",
//        data:{AdminName:AdminName, AdminEmail:AdminEmail},
//        async: false,  
//        success:function(data) {
//            ResultID = JSON.parse(data);
//        }
//    });
//    return ResultID;
//}

// update DB ///////////////////////////////////////////////////////////////////
//function db_updateAdmin(AdminID, AdminName, AdminEmail) {
//    var Result = false;
//    $.ajax({
//        type:"POST",
//        url:"php/db_updateAdmin.php",
//        data:{AdminID:AdminID, AdminName:AdminName, AdminEmail:AdminEmail},
//        async: false,  
//        success:function(data) {
//            Result = JSON.parse(data);
//        }
//    });
//    return Result;
//}

// delete DB ///////////////////////////////////////////////////////////////////
//function db_deleteAdmin(AdminID) {
//    var Result = false;
//    $.ajax({
//        type:"POST",
//        url:"php/db_deleteAdmin.php",
//        data:{AdminID:AdminID},
//        async: false,  
//        success:function(data) {
//            Result = JSON.parse(data);
//        }
//    });
//    return Result;
//}