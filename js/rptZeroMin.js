var m_table;

var m_tu301_list = new Array();
////////////////////////////////////////////////////////////////////////////////
window.onload = function() {
    if (sessionStorage.key(0) !== null) {
        $('.splash').css('display', 'none');
        getLoginInfo();
        getSARSLocation();
        getSARSTerms();
    }
    else {
        window.open('Login.html', '_self');
    }
};

$(window).bind("load", function () {
    // Remove splash screen after load
    $('.splash').css('display', 'none');
});

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
    $('.hide-menu').on('click', function(event){
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
    $('.showhide').on('click', function (event) {
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
    $('.closebox').on('click', function (event) {
        event.preventDefault();
        var hpanel = $(this).closest('div.hpanel');
        hpanel.remove();
        if($('body').hasClass('fullscreen-panel-mode')) { $('body').removeClass('fullscreen-panel-mode');}
    });

    // Fullscreen for fullscreen hpanel
    $('.fullscreen').on('click', function() {
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
    $('.right-sidebar-toggle').on('click', function () {
        $('#right-sidebar').toggleClass('sidebar-open');
    });

    // Function for small header
    $('.small-header-action').on('click', function(event){
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
    // Fix Bootstrap backdrop issu with animation.css
    $('.modal').appendTo("body");
    
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    $('#nav_logout').click(function() {
        sessionStorage.clear();
        window.open('Login.html', '_self');
        return false;
    });
    
    // semester change event ///////////////////////////////////////////////////
    $('#sel_semester').change(function() {
        var sem_value = $('#sel_semester').val();
        var ar_value = sem_value.split("_");
        $('#start_date').html(ar_value[0]);
        $('#end_date').html(ar_value[1]);
    });
    
    // run button click ////////////////////////////////////////////////////////
    $('#btn_run').click(function() { 
        $('#spinner_attachment').show();
        
        var location_id = $('#sel_location').val();
        var start_date = $('#start_date').html();
        var end_date = $('#end_date').html();
        var sem_value = $('#sel_semester').val();
        var ar_value = sem_value.split("_");
        var term_id = ar_value[2];
        
        if (location_id === "" || start_date === "" || end_date === "" || term_id === "") {
            swal({title: "Warning", text: "Please select location, start date and end date", type: "warning"});
            return false;
        }
        else {
            setTimeout(function() { 
                getZeroMinList(start_date, end_date, location_id, term_id);
                $('#spinner_attachment').hide();
            }, 1000);
        }
    });
    
    // to excel button click ///////////////////////////////////////////////////
    $('#btn_to_excel').click(function() { 
        var location_id = $('#sel_location').val();
        var start_date = $('#start_date').html();
        var end_date = $('#end_date').html();
        var sem_value = $('#sel_semester').val();
        var ar_value = sem_value.split("_");
        var term_id = ar_value[2];
        
        if (location_id === "" || start_date === "" || end_date === "" || term_id === "") {
            swal({title: "Warning", text: "Please select location, start date and end date", type: "warning"});
            return false;
        }
        else {
            var url_html = "StartDate=" + start_date + "&EndDate=" + end_date + "&LocationID=" + location_id + "&TermID=" + term_id;
            location.href = "php/db_saveZeroMinGridTrakCSV.php?" + url_html;
        }
    });
    
    // to excel button click ///////////////////////////////////////////////////
//    $('#btn_pdf_drop').click(function() { 
//        for (var i = 0; i < m_tu301_list.length; i++) {
//            var stu_id = m_tu301_list[i]['IVC_ID'];
//            var stu_name = m_tu301_list[i]['Student_Name'];
//            var ticket = m_tu301_list[i]['Section_Name'];
//            var course = m_tu301_list[i]['Course_Title'];
//        }
//    });
    
    // jquery datatables initialize ////////////////////////////////////////////
    m_table = $('#tbl_zero_min_list').DataTable({ paging: false, bInfo: false, order: [[ 1, "asc" ]]});
    
    // bootstrap selectpicker
    $('.selectpicker').selectpicker();
    
    // bootstrap datepicker
//    $('#start_date').datepicker();
//    $('#end_date').datepicker();
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
function getLoginInfo() {
    var login_name = sessionStorage.getItem('ss_sarsr_loginName');
    $('#login_user').html(login_name);
}

////////////////////////////////////////////////////////////////////////////////
function getSARSLocation() {
    $('#sel_location').empty();
    
    var html = "<option value='47'>Language Lab</option>";
    html += "<option value='37'>Math Lab</option>";
    html += "<option value='44'>Student Success Center</option>";
    
    $('#sel_location').append(html);
    $('#sel_location').selectpicker('refresh');
}

function getSARSTerms() {
    var result = new Array(); 
    result = db_getTbl_Term_Master();
    
    var str_active_start_date = "";
    var str_active_stop_date = "";
    var str_term_value = "";
    $('#sel_semester').empty();
    var html = "";
    for (var i = 0; i < result.length; i++) {
        html += "<option value='" + convertDBDateToString(result[i]['Start_Date']) + "_" + convertDBDateToString(result[i]['Stop_Date']) + "_" + result[i]['Term_ID'] + "'>" + result[i]['Description'] + "</option>";
        if (result[i]['Active'] === "1") {
            term_id = result[i]['Term_ID'];
            str_active_start_date = convertDBDateToString(result[i]['Start_Date']);
            str_active_stop_date = convertDBDateToString(result[i]['Stop_Date']);
            str_term_value = str_active_start_date + "_" + str_active_stop_date + "_" + term_id;
        }
    }
    
    $('#start_date').html(str_active_start_date);
    $('#end_date').html(str_active_stop_date);
    
    $('#sel_semester').append(html);
    $('#sel_semester').val(str_term_value);
    $('#sel_semester').selectpicker('refresh');
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function getZeroMinList(start_date, end_date, location_id, term_id) {    
    var result = new Array(); 
    result = db_getZeroMinGridTrak(start_date, end_date, location_id, term_id);

    m_table.clear();
    m_table.rows.add(result).draw();
    
    $('.animate-panel').animatePanel();
    setTU301List(result);
}

function setTU301List(result) {
    m_tu301_list.length = 0;
    m_tu301_list = result;
    
    m_tu301_list = $.grep(m_tu301_list, function(v) { return v['Course_Title'] === "TU 301"; });
    m_tu301_list.sort(function(a, b) { 
        var aValue = a["Section_Name"] + a["Student_Name"];
        var bValue = b["Section_Name"] + b["Student_Name"];
        
        if (aValue > bValue) {
            return 1;
        } else if (aValue < bValue) {
            return -1;
        } else {
            return 0;
        } 
    });
}