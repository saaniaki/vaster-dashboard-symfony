var cancelSearch = "<div class='input-group-btn dropup' id='cancelSearch' >" +
    "<button type='button' class='btn btn-default' style='height: 33px; width: 20px; padding: 2px;'><span class='glyphicon glyphicon-remove'></span></button>" +
    "</div>";


var mapLink = "http://maps.google.com/maps?z=12&t=m&q=loc:";

var total = 0;
var totalInter = 0;
var totalORG = 0;
var android = 0;
var ios = 0;

var limit = 2;//25
var offset = 0;
var sort = 'user.createdtime';
var order = 'DESC';
var internal = 1;
var keyword = null;

var path = 'api/user/page';
var pages = 0;

var users = [];
var originalUsers = [];

var currUserID = '';

function msToTime(duration) {
    var milliseconds = parseInt((duration%1000)/100)
        , seconds = parseInt((duration/1000)%60)
        , minutes = parseInt((duration/(1000*60))%60)
        , hours = parseInt((duration/(1000*60*60))%24);

    hours = (hours < 10) ? "0" + hours : hours;
    minutes = (minutes < 10) ? "0" + minutes : minutes;
    seconds = (seconds < 10) ? "0" + seconds : seconds;

    //return hours + ":" + minutes + ":" + seconds + "." + milliseconds;
    return hours + ":" + minutes;
}


function createPager(){

    pages = Math.ceil(total/limit);
    $('#totalUsers').text(total + " Users");
    $('#totalORG').text(totalORG + " Orange Hats");
    $('#android').text(android + " Android Devices");
    $('#ios').text(ios + " IOS Devices");

    if(internal){
        $('#totalInternal').text(totalInter + " Internal Users").show();
    }else{
        $('#totalInternal').hide();
    }

    $('.pageNumber').remove();
    for(var i = pages; i > 0; i--){
        $('#prev').after("<li class='pageNumber'><a href='#'>" + i + "</a></li>");
    }

    var at = pages - Math.ceil((total - offset)/limit) + 1 ;

    $('.pagination').children('*').filter(function() {
        return $(this).text() === at.toString();
    }).addClass('active');

    if(at === 1){
        $('#prev').addClass('disabled');
    }else if ($('#prev').hasClass('disabled')){
        $('#prev').removeClass('disabled');
    }

    if(at === pages){
        $('#next').addClass('disabled');
    }else if ($('#next').hasClass('disabled')){
        $('#next').removeClass('disabled');
    }




    return pages;
}

function next(){
    if( offset + limit < total ) {
        offset += limit;
        loadTable(keyword);
    }
}

function prev(){
    if( offset - limit >= 0 ) {
        offset -= limit;
        loadTable(keyword);
    }
}

function goTo(index) {
    if (index > 0 && index <= pages) {
        offset = limit * (index-1);
        loadTable(keyword);
    }
}

function setLimit(lim){
    limit = lim;
    offset = Math.floor((offset/limit)) * limit;
    loadTable(keyword);
}


function renderTable(ajaxPath){
    $.ajax({
        url: ajaxPath,
        dataType:"JSON",
        success: function (data) {
            $("#ajaxTbody").html("");
            createPager(limit, offset);
            $('#loading').hide();

            if(data.users.length === 0)
                alert('No result founded! :(');

            $.each( data.users, function( key, user ) {


                originalUsers.push({
                    'id' : user.id,
                    'firstName' : user.firstName,
                    'lastName' : user.lastName,
                    'phone' : user.phone,
                    'email' : user.email,
                    'device' : user.device,
                    'balance' : user.balance,
                    'profile' : user.profile,
                    'cover' : user.cover,
                    'createdTime' : user.createdTime,
                    'type' : user.type,
                    'profession' : user.profession,
                    'socialNetworks' : user.socialNetworks,
                    'serviceTime' : user.serviceTime,
                    'languages' : user.languages,
                    'lastSeen' : user.lastSeen,
                    'location' : user.location,
                    'locHistory' : user.locHistory,
                    'searches' : user.searches
                });


                user.createdTime.date = user.createdTime.date.substring(0, 16);
                locationLink = "<strong style=' margin-left: 8.5px;' class='label label-danger'>N/A</strong>";
                locationTime = null;

                if( user.location !== null ){
                    user.location.createdtime.date = user.location.createdtime.date.substring(0, 16);
                    locationTime = user.location.createdtime.date;
                    locationLink = "<a data-toggle='tooltip' data-placement='bottom' title='" +
                        locationTime + "' style='margin-left: 7px;' target='_blank' class='label label-success' href='" +
                        mapLink + user.location.latitude + "," + user.location.longitude + "'>map</a>";
                }
                if(user.lastName === null) user.lastName = '';
                if(user.email === null) user.email = '';
                if(user.type === null) user.type = '';
                else if (user.type === 'Internal') user.type = "<span class='label label-info'>Internal</span>";
                //console.log(user.profession.available);
                if(user.profession.available === true) user.profession.available = "<img src='' style='width: 24px;' />";//{{ asset('images/orange-hat.png') }}
                else user.profession.available = '';

                $("#ajaxTbody").append(
                    "<tr data-userid='" + user.id + "' class='user'>" +
                    //"<td data-userid='"+user.id+"' class='fnameColumn'><span class='id'>" + user.id + "</span>" + user.firstName + "</td>" +
                    "<td class='fnameColumn'>" + user.firstName + "</td>" +
                    "<td class='lnameColumn'>" + user.lastName + "</td>" +
                    "<td class='phoneColumn'>" + user.phone + "</td>" +
                    "<td class='emailColumn'>" + user.email + "</td>" +
                    "<td class='createdColumn'>" + user.createdTime.date + "</td>" +
                    "<td class='typeColumn'>" + user.type + "</td>" +
                    "<td class='orgColumn' style='text-align: center;'>" + user.profession.available + "</td>" +
                    "<td class='osColumn'>" + user.device + "</td>" +
                    "<td class='lastSeenColumn'>" + user.lastSeen + "</td>" +
                    "<td>" + locationLink + "</td>" +
                    "</tr>"//$
                );


                users.push({
                    'id' : user.id,
                    'firstName' : user.firstName,
                    'lastName' : user.lastName,
                    'phone' : user.phone,
                    'email' : user.email,
                    'device' : user.device,
                    'balance' : user.balance,
                    'profile' : user.profile,
                    'cover' : user.cover,
                    'createdTime' : user.createdTime,
                    'type' : user.type,
                    'profession' : user.profession,
                    'socialNetworks' : user.socialNetworks,
                    'serviceTime' : user.serviceTime,
                    'languages' : user.languages,
                    'lastSeen' : user.lastSeen,
                    'location' : user.location,
                    'locHistory' : user.locHistory,
                    'searches' : user.searches
                });

            });
            $( "#ajaxTbody" ).trigger( "renderCompleted");
        }
    });
}

$('#userModal').on('hidden.bs.modal', function () {
    currUserID = '';
});

function getUser(id) {
    currUserID = id;
    return $.grep(users, function(e){ return e.id === id; })[0];
}

function getOriginalUser(id) {
    currUserID = id;
    return $.grep(originalUsers, function(e){ return e.id === id; })[0];
}

function count(type, searchedWord) {
    if( searchedWord === undefined ){
        return $.ajax({
            url: 'api/user/count/' + type,
            dataType: "JSON"
        });
    }
    return $.ajax({
        url: 'api/user/count/' + type + '/' + searchedWord,
        dataType: "JSON"
    });
}

function adjust(a1, ajaxPath){
    total = a1[0].count[0].total;
    totalInter = a1[0].count[0].totalInter;
    totalORG = a1[0].count[0].totalORG;
    android = a1[0].count[0].android;
    ios = a1[0].count[0].ios;
    originalUsers= [];
    users = [];
    renderTable(ajaxPath);
}

function loadTable(searchedWord) {
    $('.sk-folding-cube').css('margin', ($(window).height()/2)-60 + 'px auto');
    $('#loading').show();

    var ajaxPath = '';
    if( searchedWord === undefined || searchedWord === null) {
        ajaxPath = path + '/' + limit + '/' + offset + '/' + sort + '/' + order + '/' + internal;

        if( internal ){
            $.when(count('all'), count('internal')).done(function(a1, a2){
                adjust(a1, ajaxPath);
            });
        } else {
            $.when(count('standard'), count('internal')).done(function(a1, a2){
                adjust(a1, ajaxPath);
            });
        }


    } else {
        keyword = searchedWord;
        ajaxPath = path + '/' + limit + '/' + offset + '/' + sort + '/' + order + '/' + internal + '/' + keyword;
        if( internal ){
            $.when(count('all', keyword), count('internal', keyword)).done(function(a1, a2){
                adjust(a1, ajaxPath);
            });
        } else {
            $.when(count('standard', keyword), count('internal', keyword)).done(function(a1, a2){
                adjust(a1, ajaxPath);
            });
        }

    }


}

//$('.createdColumn').css('background-color', 'rgba(189, 208, 221, 0.29)');
function makeSortable(id, fieldName) {
    var $table = '.sortableTable';
    var $item = "." + id;
    $( $table ).on( "click", $item, function() {
        if( sort === fieldName ){
            if( order === 'DESC' ){
                order = 'ASC';
                $($item).find("span").addClass('up');
            }else{
                order = 'DESC';
                $($item).find("span").removeClass('up');
            }
        }else{
            $( $table ).find('th span').removeClass('caret');
            $( $table ).find('th').css('background-color', '');
            $( $table ).find('td').css('background-color', '');
            order = 'DESC';
            $($item).find("span").addClass('caret');
        }
        sort = fieldName;
        loadTable(keyword);
    });
}

var columnToSelector = {
    'user.firstname' : '.fnameColumn',
    'user.lastname' : '.lnameColumn',
    'user.phone' : '.phoneColumn',
    'user.email' : '.emailColumn',
    'user.createdtime' : '.createdColumn',
    'user.accounttype' : '.typeColumn',
    'profession.available' : '.orgColumn',
    'account.devicetype' : '.osColumn',
    'lastseen.seconds' : '.lastSeenColumn'
};

$( "#ajaxTbody" ).on( "renderCompleted", function() {
    $(columnToSelector[sort]).css('background-color', 'rgba(189, 208, 221, 0.29)');
    $('[data-toggle="tooltip"]').tooltip();
});


$( ".editable" ).on( "dblclick", function() {
    $('#edit-selectable-modal-title').text($(this).attr('data-title'));
    $('#edit-selectable-modal').modal();
});


$( "#edit-selectable-modal-save" ).on( "click", function() {
    var value = $("input[name='user-type']:checked").val();
    $("#modal-type").text(value).css('color', 'rgb(243, 0, 255)').closest(".editable").css('color', 'rgb(243, 0, 255)');

    //set it in user as well
    $('#' + $("#modal-type").attr('data-connetToForm')).val(value);
    $('#edit-selectable-modal').modal('hide');
});

$( "#edit-selectable-modal-undo" ).on( "click", function() {
    var originalUser = getOriginalUser(currUserID);
    $("#modal-type").css('color', '').closest(".editable").css('color', '#333');
    if( originalUser.type === null )
        $("#modal-type").css('color', 'rgb(165, 165, 165)');
    $("#modal-type").text($("#modal-type").attr('data-original'));
    $('#' + $("#modal-type").attr('data-connetToForm')).val(originalUser.type);
    $('#edit-selectable-modal').modal('hide');
});


function loadUpdateForm(id) {
    $.ajax({
        url: 'users/update/' + id,
        success: function (data) {
            $("#user-update-hidden-from").html(data);
            $("#user-update-hidden-from button").remove();
        }
    });
}

$( "#user-update" ).on( "click", function() {
    var originalUser = getOriginalUser(currUserID);
    $.ajax({
        url: 'users/update/' + originalUser.id,
        type: 'post',
        data: $('#user-update-from').serialize(),
        success: function () {
            loadTable(keyword);
        }
    });
    $(this).closest("#userModal").modal('hide');
});





$( document ).ready(function() {
    loadTable();


    $( "#prev" ).click(function() {
        prev();
    });


    $( "#pagination" ).on( "click", ".pageNumber", function() {
        goTo(parseInt($(this).text()));
    });

    $( "#next" ).click(function() {
        next();
    });

    $( ".limitNumber" ).click(function() {
        $('.limitNumber').removeClass('disabled');
        setLimit(parseInt($(this).text()));
        $(this).addClass('disabled');
        $('#limit-text').text($(this).text());
    });


    var tableOffset = $("#usersTable").offset().top - 40;
    var $header = $("#usersTable > thead").clone(true, true);
    $("#header-fixed > table").append($header).css('table-layout', 'fixed');
    var $fixedHeader = $("#header-fixed");

    $(window).on("scroll", function() {
        var offset = $(this).scrollTop();

        if (offset >= tableOffset && $fixedHeader.is(":hidden")) {
            $fixedHeader.show();
            $('.navbar-fixed-top').css('box-shadow', 'none');
        }
        else if (offset < tableOffset) {
            $fixedHeader.hide();
            $('.navbar-fixed-top').css('box-shadow', '0 0 25px -10px rgb(255, 162, 5)');
        }
    });


    $('#search-result-section').on("scroll", function() {
        var selector = $('div[aria-expanded="true"]');
        if(selector.length !== 0){
            var itemOffset = selector.prev().position().top;
            if (itemOffset < 0)
                $('#item-fixed').show().css('top', $(this).scrollTop());
            else
                $('#item-fixed').hide().css('top', $(this).scrollTop());
        }
    });



    $( "#modal-searches-result" ).on( "click", "div[id^='heading-']", function() {
        var itemHeader = $(this).clone(true, true);
        $("#item-fixed").html("").append(itemHeader);
    });

    //sorting
    var sortable = {
        'First-Name' : 'user.firstname',
        'Last-Name' : 'user.lastname',
        'Phone' : 'user.phone',
        'Email' : 'user.email',
        'Created-Date' : 'user.createdtime',
        'Type' : 'user.accounttype',
        'Orange-Hat' : 'profession.available',
        'OS' : 'account.devicetype',
        'Last-Seen' : 'lastseen.seconds'
    };

    for (var k in sortable) {
        makeSortable(k, sortable[k]);
    }


    $('#internalToggle').toggles({
        drag: true, // allow dragging the toggle between positions
        click: true, // allow clicking on the toggle
        text: {
            on: 'All', // text for the ON position
            off: 'External' // and off
        },
        on: true, // is the toggle ON on init
        animate: 250, // animation time (ms)
        easing: 'swing', // animation transition easing function
        checkbox: null, // the checkbox to toggle (for use in forms)
        clicker: null, // element that can be clicked on to toggle. removes binding from the toggle itself (use nesting)
        width: 67, // width used if not set in css
        height: 18, // height if not set in css
        type: 'compact' // if this is set to 'select' then the select style toggle will be used
    }).on('toggle', function(e, active) {
        if (active) {
            $('#internalToggle .toggle-slide').css('box-shadow', '0 0 10px -2px rgba(12, 255, 5, 1)');
            internal = 1;
            loadTable(keyword);
        } else {
            $('#internalToggle .toggle-slide').css('box-shadow', '0 0 0px 0px rgba(0, 0, 0, 1)');
            internal = 0;
            loadTable(keyword);
        }
    });


    $('#invalidToggle').toggles({
        drag: true, // allow dragging the toggle between positions
        click: true, // allow clicking on the toggle
        text: {
            on: 'All', // text for the ON position
            off: 'Valid' // and off
        },
        on: true, // is the toggle ON on init
        animate: 250, // animation time (ms)
        easing: 'swing', // animation transition easing function
        checkbox: null, // the checkbox to toggle (for use in forms)
        clicker: null, // element that can be clicked on to toggle. removes binding from the toggle itself (use nesting)
        width: 67, // width used if not set in css
        height: 18, // height if not set in css
        type: 'compact' // if this is set to 'select' then the select style toggle will be used
    })

    $('.toggle-slide').css('box-shadow', '0 0 10px -2px rgba(12, 255, 5, 1)');


    $('#searchType li').each(function() {
        $( this ).click(function() {
            $('#searchType li').each(function() {
                $(this).removeClass('active');
            });
            $(this).addClass('active');
        });
    });

    $( '#search' ).click(function() {
        //if input is not empty
        var option = $('#searchType .active a').attr('data-search');
        keyword = $('#keyword').val();
        if( option === 'contains' )
            keyword = '%25' + keyword + '%25';
        else if ( option === 'starts' )
            keyword = keyword + '%25';
        else if ( option === 'ends' )
            keyword = '%25' + keyword;

        loadTable(keyword);

        if( $('#cancelSearch').length !== 1)
            $('#searchBar').prepend(cancelSearch);
    });

    $( "#searchBar" ).on( "click", "#cancelSearch", function() {
        keyword = null;
        offset = 0;
        $('#keyword').val('');
        loadTable();
        $('#cancelSearch').remove();
    });


    function printField(field, selector, msg){
        if( field === "" || field === null )
            $(selector).html("<span style='color: rgb(165, 165, 165); font-weight: normal;'>" + msg + "</span>");
        else
            $(selector).text(field);
        $(selector).attr('data-original', $(selector).text());
        $(selector).closest(".editable").css('color', '#333');
        $(selector).css('color', '#333');
    }


    function printSearchField(title, field){
        return "<div><strong>" + title +": </strong><span>" + field + "</span></div>";
    }


    function printServiceTable(obj) {
        var numToDay = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        var rows = '';


        for(var i in obj){
            statusWord = "not available";
            if( obj[i].status )
                statusWord = "available";

            rows += "<tr>" +
                "<td>" + numToDay[obj[i].day] + "</td>" +
                "<td>" + msToTime(obj[i].from) + "</td>" +
                "<td>" + msToTime(obj[i].to) + "</td>" +
                "<td>" + statusWord + "</td>" +
                "</tr>";
        }

        return "<table id='modal-service-time-table' class='table table-striped table-hover' style=' margin-top: 15px;'>" +
            "<thead>" +
            "<tr>" +
            "<th><span>Day</span></th>" +
            "<th><span>Start</span></th>" +
            "<th><span>End</span></th>" +
            "<th><span>Availability</span></th>" +
            "</tr>" +
            "</thead>" +
            "<tbody id='service-time'>" +
            rows +
            "</tbody>" +
            "</table>"
    }

    $( "#tab-buttons li" ).each(function( ) {
        $( this ).click(function() {
            $('#tab-buttons li').each(function() {
                $(this).removeClass('active');
                $( '#' + this.id.replace('-button', '') ).hide();
            });
            $( this ).addClass('active');
            $( '#' + this.id.replace('-button', '') ).show( "blind", {direction: 'up'} , 500 );


            if(this.id === 'tab-locations-button'){
                $('#tab-locations-info').show();
            }else{
                $('#tab-locations-info').hide();
            }


        });
    });


    $( "#ajaxTbody" ).on( "click", ".user", function() {
        var user = getOriginalUser($(this).attr('data-userid'));  //var originalUser = getOriginalUser(currUserID);
        loadUpdateForm(currUserID);
        console.log(user);
        $('#modal-title').text(user.firstName + " " + user.lastName);


        $('#modal-profile').attr('src', user.profile);
        $('#modal-cover').attr('src', user.cover);


        printField(user.firstName, '#modal-first-name', 'no first name');
        printField(user.lastName, '#modal-last-name', 'no last name');
        printField(user.email, '#modal-email', 'no email');

        printField(user.phone, '#modal-phone', 'no phone number');

        $('#modal-user-id').text(user.id);
        $('#modal-created-time').text(user.createdTime.date);
        $('#modal-last-seen').text(user.lastSeen);

        printField(user.device, '#modal-device', 'no device info');

        printField(user.type, '#modal-type', 'user type not set yet');

        $('#modal-balance').text(user.balance);

        if( user.profession.available === "" ) {
            $('#modal-available').html("<span style='color: rgb(165, 165, 165); font-weight: normal;'>not available as Orange Hat</span>");
            $('.glyphicon-check').css('color', '#333');
        } else {
            $('#modal-available').html("<span style='color: #f6541c; font-weight: normal;'>Available as Orange Hat</span>");
            $('.glyphicon-check').css('color', '#f6541c');
        }



        printField(user.profession.name, '#modal-profession-name', 'no profession name');
        printField(user.profession.title, '#modal-user-title', 'no title');
        printField(user.profession.expertise, '#modal-expertise', 'no expertise');
        printField(user.profession.rate, '#modal-rate', 'rate not set yet');
        printField(user.profession.about, '#modal-about', 'about is not written yet');
        printField(user.profession.commission, '#modal-commission', 'no profession name');
        printField(user.profession.gender, '#modal-gender', 'no value');
        printField(user.profession.ranking, '#modal-ranking', 'no value');
        printField(user.profession.votes, '#modal-votes', 'no value');
        printField(user.profession.address, '#modal-address', 'no value');


        printField(user.profession.website, '#modal-website', 'no value');


        if( user.profession.website === "" || user.profession.website === null )
            $('#modal-website').html("<span style='color: rgb(165, 165, 165); font-weight: normal;'>no value</span>");
        else
            $('#modal-website').html("<a href='http://" + user.profession.website +"'>" + user.profession.website + "</a>"); //check for http


        printField(user.profession.city, '#modal-city', 'no value');
        printField(user.profession.region, '#modal-region', 'no value');
        printField(user.profession.postalCode, '#modal-postal-code', 'no value');
        printField(user.profession.country, '#modal-country', 'no value');


        if(user.languages !== null){
            printField(user.languages.first, '#modal-lang1', 'no value');
            printField(user.languages.second, '#modal-lang2', 'no value');
            printField(user.languages.other, '#modal-lang-other', 'no value');
        }else{
            console.log('user.languages is null');
        }




        printField(user.profession.homeLocation.latitude, '#modal-home', 'no value');
        printField(user.profession.homeLocation.longitude, '#modal-home', 'no value');

        latitude = user.profession.homeLocation.latitude;
        longitude = user.profession.homeLocation.longitude;
        if( latitude !== null && longitude !== null && latitude !== 0 && longitude !== 0) {
            locationLink = "<a data-toggle='tooltip' data-placement='bottom' title='" +
                locationTime + "' style='margin-left: 7px;' target='_blank' class='label label-success' href='" +
                mapLink + user.profession.homeLocation.latitude + "," + user.profession.homeLocation.longitude + "'>map</a>";

            $('#modal-home').html(locationLink);
        } else {
            $('#modal-home').html("<span style='color: rgb(165, 165, 165); font-weight: normal;'>not available</span>");
        }


        if( user.socialNetworks.length !== 0 ) {
            var networks = user.socialNetworks;
            for (var k in user.socialNetworks) {
                $('#modal-network').html("<span><a href='" + networks[k].url + "'>" + networks[k].name +
                    " (" + networks[k].type + ")" + "</a></span>");
            }
        } else {
            $('#modal-network').html("<span style='color: rgb(165, 165, 165); font-weight: normal;'>not available</span>");
        }

        if( user.serviceTime.length !== 0 ) {
            $('#service-time').html("");
            row = user.serviceTime;
            for (var i in row) {
                $('#service-time').append(
                    "<tr>" +
                    "<td>" + row[i].day + "</td>" +
                    "<td>" + row[i].start.substring(0,5) + "</td>" +
                    "<td>" + row[i].end.substring(0,5) + "</td>" +
                    "<td>" + row[i].availability + "</td>" +
                    "</tr>"
                );
            }
            $('#modal-service-time-table').show();
            $('#modal-service-time-span').hide();
        } else {
            $('#modal-service-time-table').hide();
            $('#modal-service-time-span').show();
        }




        //loation tab
        $('#modal-location').html("");

        var invalidCount = 0;
        var invalidRows = [];

        if( user.locHistory.length !== 0 ) {
            row = user.locHistory;
            for (index in row) {

                row[index].createdtime.date = row[index].createdtime.date.substring(0, 16);

                locationLink = "<a target='_blank' class='label label-success' href='" +
                    mapLink + row[index].latitude + "," + row[index].longitude + "'>map</a>";

                rowHTML = "<tr id='locationNumber-" + index + "' >" +
                    "<td>" + row[index].createdtime.date + "</td>" +
                    "<td>" + row[index].latitude + "</td>" +
                    "<td>" + row[index].longitude + "</td>" +
                    "<td>" + locationLink + "</td>" +
                    "</tr>";

                if( (row[index].latitude === 0 && row[index].longitude === 0) || (row[index].latitude === -180 && row[index].longitude === -180) ){
                    invalidRows.push("#locationNumber-" + index);
                    invalidCount++;
                }

                $('#modal-location').append(rowHTML);
            }
            $('#modal-location-table').show();
            $('#modal-location-span').hide();
        } else {
            $('#modal-location-table').show();
            $('#modal-location-span').hide();
        }

        $('#tab-locations-info span').text(invalidCount + " invalid out of " + user.locHistory.length);
        $('#invalidToggle').toggles({on:true});
        $('#invalidToggle .toggle-slide').css('box-shadow', '0 0 10px -2px rgba(12, 255, 5, 1)');
        $('#invalidToggle').on('toggle', function(e, active) {
            if (active) {
                $('#invalidToggle .toggle-slide').css('box-shadow', '0 0 10px -2px rgba(12, 255, 5, 1)');
                for( id in invalidRows ){
                    $(invalidRows[id]).show();
                }
            } else {
                $('#invalidToggle .toggle-slide').css('box-shadow', '0 0 0px 0px rgba(0, 0, 0, 1)');
                for( id in invalidRows ){
                    $(invalidRows[id]).hide();
                }
            }
        });



        //search tab
        $('#modal-searches').html("");
        $('#modal-searches-result').html("");
        var searches = [];
        if( user.searches.length !== 0 ) {
            row = user.searches;
            for (var index in row) {
                row[index].createdtime.date = row[index].createdtime.date.substring(0, 16);

                locationLink = "<a target='_blank' class='label label-success' href='" +
                    mapLink + row[index].latitude + "," + row[index].longitude + "'>map</a>";

                $('#modal-searches').append(
                    "<tr id='searchNumber-" + index +"'>" +
                    "<td>" + row[index].createdtime.date + "</td>" +
                    "<td>" + row[index].searchquery + "</td>" +
                    "<td>" + locationLink + "</td>" +
                    "</tr>"
                );

                results = JSON.parse(row[index].searchresult);

                var items = [];


                for (j in results) {
                    element = results[j];

                    locationLink = '';
                    if(element.hasOwnProperty('location')){
                        locationLink = "<a target='_blank' class='label label-success' href='" +
                            mapLink + element.location.lat + "," + element.location.lon + "'>map</a>";
                    }else{
                        console.log("[" + index + "] [" + j + "] " + row[index].searchquery + " " + element.name + " no location");
                    }


                    metaData = '';
                    if(element.hasOwnProperty('resultMetaData')){
                        metaData = "<div class='search-result-box'><span class='title'>Meta Data</span><hr>" +
                            printSearchField('Category', element.resultMetaData.category) +
                            printSearchField('CloudSearchScore', element.resultMetaData.cloudSearchScore) +
                            printSearchField('VasterSearchScore', element.resultMetaData.vasterSearchScore) +
                            "</div>";
                    }else{
                        console.log("[" + index + "] [" + j + "] " + row[index].searchquery + " " + element.name + " no meta data");
                    }

                    bilAdd = '';
                    if(element.hasOwnProperty('resultMetaData')){
                        bilAdd = "<div class='search-result-box'><span class='title'>Address</span><hr>" +
                            printSearchField('Address', element.billingAddress.address) +
                            printSearchField('City', element.billingAddress.city) +
                            printSearchField('Country', element.billingAddress.country) +
                            printSearchField('PostalCode', element.billingAddress.postalCode) +
                            printSearchField('Region', element.billingAddress.region) +
                            "</div>";
                    }else{
                        console.log("[" + index + "] [" + j + "] " + row[index].searchquery + " " + element.name + " no billing addresss");
                    }

                    compInfo = '';
                    if(element.hasOwnProperty('resultMetaData')){
                        compInfo = "<div class='search-result-box'><span class='title'>Company Info</span><hr>" +
                            printSearchField('Name', element.companyInfo.name) +
                            printSearchField('Website', element.companyInfo.website) +
                            "</div>";
                    }else{
                        console.log("[" + index + "] [" + j + "] " + row[index].searchquery + " " + element.name + " no company info");
                    }

                    items[j] = "<div class='panel panel-default'>" +
                        "<div class='panel-heading' role='tab' id='heading-" + element.vid + "'>" +
                        "<h4 class='panel-title'>" +
                        "<a class='collapsed' role='button' data-toggle='collapse' data-parent='#modal-searches-result' href='#collapse-" + results[j].vid + "' aria-expanded='false' aria-controls='collapse-" + results[j].vid + "'>" +
                        element.name +
                        "</a>" +
                        "</h4>" +
                        "</div>" +
                        "<div id='collapse-" + element.vid +"' class='panel-collapse collapse' role='tabpanel' aria-labelledby='heading-" + results[j].vid + "'>" +
                        "<div class='panel-body' style='padding: 8px;'>" +

                        metaData +

                        "<div class='search-result-box'><span class='title'>General</span><hr>" +
                        printSearchField('Title', element.title) +
                        printSearchField('Expertise', element.expertise) +
                        printSearchField('Gender', element.gender) +
                        printSearchField('Ranking', element.ranking.ranking) +
                        printSearchField('Votes', element.ranking.votes) +
                        printSearchField('About', element.about) +
                        printSearchField('Location', locationLink) +
                        "</div>" +

                        compInfo + bilAdd +

                        "<div class='search-result-box'><span class='title'>Service</span><hr>" +
                        printSearchField('Service Rate', element.serviceRate) +
                        printServiceTable(element.serviceTimes) +

                        "</div>" +

                        "</div></div></div>";

                }

                searches[index] = items;

                $( "#searchNumber-" + index ).click(function() {

                    $( "tr[id^='searchNumber-']" ).css('background-color', '');
                    $( "div[id='searchNumber-']" );

                    $(this).css('background-color', 'rgba(81, 168, 255, 0.2)');
                    p = parseInt(this.id.replace('searchNumber-', ''));
                    $('#modal-searches-result').html("");
                    // init result section
                    $("#item-fixed").html("").hide();
                    $('#search-result-section').scrollTop(0);

                    for (i in searches[p]) {
                        $('#modal-searches-result').append(searches[p][i]);
                    }
                });



                //console.log(results);
            }
            $('#modal-searches-table').show();
            $('#modal-searches-span').hide();
        } else {
            $('#modal-searches-table').show();
            $('#modal-searches-span').hide();
        }

        // init result section
        $("#item-fixed").html("").hide();
        $('#search-result-section').scrollTop(0);


        $('#userModal').modal();
        $('#tab-searches .col-md-6').css('max-height', parseInt($('#userModal').css('height')) * 0.5);
    });



});
