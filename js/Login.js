var m_name = "";
var m_email = "";

var m_username = "";
var m_password = "";

////////////////////////////////////////////////////////////////////////////////
window.onload = function() {  
    $('#logn_error').hide();
    var curBrowser = bowser.name;
    var curVersion = Number(bowser.version);
    
    switch (curBrowser) {
        case "Safari":
            if (curVersion < 6)
                window.open('browser_not_support.html', '_self');
            break;
        case "Chrome":
            if (curVersion < 7)
                window.open('browser_not_support.html', '_self');
            break;
        case "Firefox":
            if (curVersion < 22)
                window.open('browser_not_support.html', '_self');
            break;
        case "Internet Explorer":
            if (curVersion < 11)
                window.open('browser_not_support.html', '_self');
            break;
        default:     
            break;
    }
};

////////////////////////////////////////////////////////////////////////////////
$(document).ready(function() {      
    $('#btn_login').click(function() { 
        // ireport.ivc.edu validation //////////////////////////////////////////
        if(location.href.indexOf("ireport.ivc.edu") >= 0 && !ireportValidation()) {
            swal({  title: "Access Denied",
                    text: "This is a Development site. It will redirect to IVC Application site",
                    type: "error",
                    confirmButtonText: "OK" },
                    function() {
                        sessionStorage.clear();
                        window.open('https://services.ivc.edu/', '_self');
                        return false;
                    }
            );
        }
        ////////////////////////////////////////////////////////////////////////
        else {
            if(loginInfo()) {
                sessionData_login(m_name, m_email);
                window.open('main.html', '_self');
                return false;
            }
            else {
                $('#error_msg').html("Invalid username or password");
                $('#logn_error').show();
                return false;
            }
        }
    });
    
    $.backstretch(["images/sars_reports_back_web_2.jpg"], {duration: 3000, fade: 750});
});

////////////////////////////////////////////////////////////////////////////////
function loginInfo() {   
    var result = new Array();
    m_username = $('#username').val().toLowerCase().replace("@ivc.edu", "");
    m_password = $('#password').val();
    
    result = getLoginUserInfo("php/login.php", m_username, m_password);    
    if (result.length === 0) {
        return false;
    }
    else {
        m_name = objToString(result[0]);
        m_email = objToString(result[1]);
        
        if (location.href.indexOf("ireport.ivc.edu") >= 0) {
            sessionStorage.setItem('m_parentSite', 'https://ireport.ivc.edu');
        }
        else {
            sessionStorage.setItem('m_parentSite', 'https://services.ivc.edu');
        }
        
        return true;
    }
}

////////////////////////////////////////////////////////////////////////////////
function ireportValidation() {
    var username = $('#username').val().toLowerCase().replace("@ivc.edu", "").replace("@saddleback.edu", "");
    if (ireportDBgetUserAccess(username) !== null) {
        return true;
    }
    else {
        return false;
    }
}