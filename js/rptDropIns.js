////////////////////////////////////////////////////////////////////////////////
window.onload = function() {
    if (sessionStorage.key(0) !== null) {
        $('.splash').css('display', 'none');
        getLoginInfo();
    }
    else {
        window.open('Login.html', '_self');
    }
};

$(window).bind("resize click", function () {
    // Add special class to minimalize page elements when screen is less than 768px
    setBodySmall();

    // Waint until metsiMenu, collapse and other effect finish and set wrapper height
    setTimeout(function () {
        fixWrapperHeight();
    }, 300);
});

////////////////////////////////////////////////////////////////////////////////
$(document).ready(function() {
    // Add special class to minimalize page elements when screen is less than 768px
    setBodySmall();
    
    // Handle minimalize sidebar menu
    $('.hide-menu').click(function(event){
        event.preventDefault();
        if ($(window).width() < 769) {
            $("body").toggleClass("show-sidebar");
        } else {
            $("body").toggleClass("hide-sidebar");
        }
    });
    
    // Initialize metsiMenu plugin to sidebar menu
    $('#side-menu').metisMenu();
    
    // Initialize iCheck plugin
    $('.i-checks').iCheck({
        checkboxClass: 'icheckbox_square-green',
        radioClass: 'iradio_square-green'
    });
    
    // Initialize animate panel function
    $('.animate-panel').animatePanel();
    
    // Function for collapse hpanel
    $('.showhide').click(function (event) {
        event.preventDefault();
        var hpanel = $(this).closest('div.hpanel');
        var icon = $(this).find('i:first');
        var body = hpanel.find('div.panel-body');
        var footer = hpanel.find('div.panel-footer');
        body.slideToggle(300);
        footer.slideToggle(200);

        // Toggle icon from up to down
        icon.toggleClass('fa-chevron-up').toggleClass('fa-chevron-down');
        hpanel.toggleClass('').toggleClass('panel-collapse');
        setTimeout(function () {
            hpanel.resize();
            hpanel.find('[id^=map-]').resize();
        }, 50);
    });
    
    // Function for close hpanel
    $('.closebox').click(function (event) {
        event.preventDefault();
        var hpanel = $(this).closest('div.hpanel');
        hpanel.remove();
    });
    
    // Fullscreen for fullscreen hpanel
    $('.fullscreen').click(function() {
        var hpanel = $(this).closest('div.hpanel');
        var icon = $(this).find('i:first');
        $('body').toggleClass('fullscreen-panel-mode');
        icon.toggleClass('fa-expand').toggleClass('fa-compress');
        hpanel.toggleClass('fullscreen');
        setTimeout(function() {
            $(window).trigger('resize');
        }, 100);
    });

    // Open close right sidebar
    $('.right-sidebar-toggle').click(function () {
        $('#right-sidebar').toggleClass('sidebar-open');
    });

    // Function for small header
    $('.small-header-action').click(function(event){
        event.preventDefault();
        var icon = $(this).find('i:first');
        var breadcrumb  = $(this).parent().find('#hbreadcrumb');
        $(this).parent().parent().parent().toggleClass('small-header');
        breadcrumb.toggleClass('m-t-lg');
        icon.toggleClass('fa-arrow-up').toggleClass('fa-arrow-down');
    });

    // Set minimal height of #wrapper to fit the window
    setTimeout(function () {
        fixWrapperHeight();
    });
    
    // Sparkline bar chart data and options used under Profile image on left navigation panel
    $("#sparkline1").sparkline([5, 6, 7, 2, 0, 4, 2, 4, 5, 7, 2, 4, 12, 11, 4], {
        type: 'bar',
        barWidth: 7,
        height: '30px',
        barColor: '#62cb31',
        negBarColor: '#53ac2a'
    });
    
    // Initialize tooltips
    $('.tooltip-demo').tooltip({
        selector: "[data-toggle=tooltip]"
    });

    // Initialize popover
    $("[data-toggle=popover]").popover();

    // Move modal to body
    // Fix Bootstrap backdrop issue with animation.css
    $('.modal').appendTo("body");
    
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    $('#nav_logout').click(function() {
        sessionStorage.clear();
        window.open('Login.html', '_self');
        return false;
    });
    
//    $('#btn_new_admin').click(function() {
//        admin_id = "";
//        resetModAdminInfo();
//        $('#mod_admin_header').html("New Admin Setting");
//    });
    
    // modal reader save button click //////////////////////////////////////////
//    $('#mod_btn_admin_save').click(function() { 
//        var admin_name = $.trim(textReplaceApostrophe($('#mod_admin_mame').val()));
//        var admin_email = $.trim(textReplaceApostrophe($('#mod_admin_email').val()));
//        
//        if (admin_id === "") {
//            addAdminToDB(admin_name, admin_email);
//        }
//        else {
//            updateAdminToDB(admin_name, admin_email);
//        }
//        
//        swal({title: "Saved!", text: "Admin has been saved", type: "success"});
//    });
    
    // rating user list edit button click //////////////////////////////////////
//    $(document).on('click', 'button[id^="btn_admin_edit_"]', function() {
//        admin_id = $(this).attr('id').replace("btn_admin_edit_", "");
//        $('#mod_admin_header').html("Edit Admin Setting");
//        
//        resetModAdminInfo();
//        getSelectedAdminInfo();
//    });
    
//    $(document).on('click', 'button[id^="btn_admin_delete_"]', function() {
//        admin_id = $(this).attr('id').replace("btn_admin_delete_", "");
//        if (db_deleteAdmin(admin_id)) {
//            $('#admin_id_' + admin_id).remove();
//        }
//    });
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
});

////////////////////////////////////////////////////////////////////////////////
function fixWrapperHeight() {
    // Get and set current height
    var headerH = 62;
    var navigationH = $("#navigation").height();
    var contentH = $(".content").height();

    // Set new height when contnet height is less then navigation
    if (contentH < navigationH) {
        $("#wrapper").css("min-height", navigationH + 'px');
    }

    // Set new height when contnet height is less then navigation and navigation is less then window
    if (contentH < navigationH && navigationH < $(window).height()) {
        $("#wrapper").css("min-height", $(window).height() - headerH  + 'px');
    }

    // Set new height when contnet is higher then navigation but less then window
    if (contentH > navigationH && contentH < $(window).height()) {
        $("#wrapper").css("min-height", $(window).height() - headerH + 'px');
    }
}

function setBodySmall() {
    if ($(this).width() < 769) {
        $('body').addClass('page-small');
    } else {
        $('body').removeClass('page-small');
        $('body').removeClass('show-sidebar');
    }
}

// Animate panel function
$.fn['animatePanel'] = function() {
    var element = $(this);
    var effect = $(this).data('effect');
    var delay = $(this).data('delay');
    var child = $(this).data('child');

    // Set default values for attrs
    if(!effect) { effect = 'zoomIn';}
    if(!delay) { delay = 0.06; } else { delay = delay / 10; }
    if(!child) { child = '.row > div';} else {child = "." + child;}

    //Set defaul values for start animation and delay
    var startAnimation = 0;
    var start = Math.abs(delay) + startAnimation;

    // Get all visible element and set opacity to 0
    var panel = element.find(child);
    panel.addClass('opacity-0');

    // Get all elements and add effect class
    panel = element.find(child);
    panel.addClass('stagger').addClass('animated-panel').addClass(effect);

    var panelsCount = panel.length + 10;
    var animateTime = (panelsCount * delay * 10000) / 10;

    // Add delay for each child elements
    panel.each(function (i, elm) {
        start += delay;
        var rounded = Math.round(start * 10) / 10;
        $(elm).css('animation-delay', rounded + 's');
        // Remove opacity 0 after finish
        $(elm).removeClass('opacity-0');
    });

    // Clear animation after finish
    setTimeout(function(){
        $('.stagger').css('animation', '');
        $('.stagger').removeClass(effect).removeClass('animated-panel').removeClass('stagger');
    }, animateTime);
};

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
function getLoginInfo() {
    var login_name = sessionStorage.getItem('ss_sarsr_loginName');
    $('#login_user').html(login_name);
}

//function getSelectedAdminInfo() {
//    var result = new Array();
//    result = db_getSelectedAdmin(admin_id);
//    
//    if (result.length === 1) {
//        $('#mod_admin_mame').val(result[0]['AdminName']);
//        $('#mod_admin_email').val(result[0]['AdminEmail']);
//    }
//}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//function setAdminHTML(id) {
//    var html = "<div class='row' id='admin_id_" + id + "'>";
//    html += "<div class='col-xs-12 col-sm-12 col-md-12'>";
//    html += "<div class='hpanel hblue contact-panel'>";
//    html += "<div class='panel-body'>";  
//    
//    html += "<div class='row'>";
//    html += "<div class='col-xs-6 col-sm-4 col-md-3 col-lg-2'>Admin Name:</div>";
//    html += "<div class='col-xs-6 col-sm-8 col-md-9 col-lg-10' id='admin_name_" + id + "'></div>";
//    html += "</div>";
//    
//    html += "<br/>";
//    
//    html += "<div class='row'>";
//    html += "<div class='col-xs-6 col-sm-4 col-md-3 col-lg-2'>Admin Email:</div>";
//    html += "<div class='col-xs-6 col-sm-8 col-md-9 col-lg-10' id='admin_email_" + id + "'></div>";
//    html += "</div>";
//    
//    html += "<br/>";
//    
//    html += "<p>";
//    html += "<button type='button' class='btn btn-primary w-xs' data-toggle='modal' data-target='#mod_admin' id='btn_admin_edit_" + id + "'>Edit</button>";
//    html += "<span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>";
//    html += "<button type='button' class='btn btn-danger2 w-xs' id='btn_admin_delete_" + id + "'>Delete</button>";
//    html += "</p>";
//    
//    html += "</div>";
//    html += "</div>";
//    html += "</div>";
//    html += "</div>";
//    
//    $('#admin_list').append(html);
//}

//function setAdminValues(id, admin_name, admin_email) {        
//    $('#admin_name_' + id).html(admin_name);
//    $('#admin_email_' + id).html(admin_email);
//}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//function getAdminList() {
//    var result = new Array();
//    result = db_getAdminList();
//    
//    $('#admin_list').empty();
//    for (var i = 0; i < result.length; i++) {
//        setAdminHTML(result[i]['AdminID']);
//        setAdminValues(result[i]['AdminID'], result[i]['AdminName'], result[i]['AdminEmail']);
//    }
//    
//    $('.animate-panel').animatePanel();
//}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//function addAdminToDB(admin_name, admin_email) {
//    admin_id = db_insertAdmin(admin_name, admin_email);
//    setAdminHTML(admin_id);
//    setAdminValues(admin_id, admin_name, admin_email);
//}

//function updateAdminToDB(admin_name, admin_email) {
//    if (db_updateAdmin(admin_id, admin_name, admin_email)) {
//        setAdminValues(admin_id, admin_name, admin_email);
//    }
//}