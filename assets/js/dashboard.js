
function renderModule(id) {
    var path = $("#modules").attr('data-module-render-url');
    path = path.replace("module_id", id);

    $.ajax({
        url: path,
        success: function (data) {
            $("#renderModule-" + id).html(data);
            $('#module-' + id + ' .module-title').text($('#module-' + id + ' .highcharts-title').text());



            //set default values
            var fromDateSelector = $('#option-module-' + id + ' .fromDatetimepicker');
            var toDateSelector = $('#option-module-' + id + ' .toDatetimepicker');

            $('#option-module-' + id).on('show.bs.modal', function () {
                $("#option-module-" + id + "-analytics" + " input[value='" + $("#option-module-" + id + "-analytics").attr('data-default') + "']").prop("checked", true);
                $("#option-module-" + id + "-userType" + " input[value='" + $("#option-module-" + id + "-userType").attr('data-default') + "']").prop("checked", true);
                $("#option-module-" + id + "-keyword").val($("#option-module-" + id + "-keyword").attr('data-default'));





                if( fromDateSelector.attr('data-default') === 'notSet' ) $('#option-module-' + id + ' .fromDatetimepicker input').val('2016-12-09 00:00');
                else if( fromDateSelector.attr('data-default') === '2000-01-01 00:00:00.000000' ){ $('#option-module-' + id + ' .fromDatetimepicker input').val("Yesterday"); $('.from button.day').addClass('active'); }
                else if( fromDateSelector.attr('data-default') === '2000-01-07 00:00:00.000000' ){ $('#option-module-' + id + ' .fromDatetimepicker input').val("A week ago"); $('.from button.week').addClass('active'); }
                else if( fromDateSelector.attr('data-default') === '2000-02-01 00:00:00.000000' ){ $('#option-module-' + id + ' .fromDatetimepicker input').val("A month ago"); $('.from button.month').addClass('active'); }
                else fromDateSelector.data("DateTimePicker").date(fromDateSelector.attr('data-default'));

                if( toDateSelector.attr('data-default') === 'notSet' ) $('#option-module-' + id + ' .toDatetimepicker input').val(); // turn real-time light on!
                else if( toDateSelector.attr('data-default') === '2000-01-01 00:00:00.000000' ){ $('#option-module-' + id + ' .toDatetimepicker input').val("Yesterday"); $('.to button.day').addClass('active'); }
                else if( toDateSelector.attr('data-default') === '2000-01-07 00:00:00.000000' ){ $('#option-module-' + id + ' .toDatetimepicker input').val("A week ago"); $('.to button.day').addClass('active'); }
                else if( toDateSelector.attr('data-default') === '2000-02-01 00:00:00.000000' ){ $('#option-module-' + id + ' .toDatetimepicker input').val("A month ago"); $('.to button.day').addClass('active'); }
                else toDateSelector.data("DateTimePicker").date(toDateSelector.attr('data-default'));

            });


            var fromDateDefault = null;
            if( fromDateSelector.attr('data-default') !== 'notSet' && fromDateSelector.attr('data-default') !== '2000-01-01 00:00:00.000000' && fromDateSelector.attr('data-default') !== '2000-01-07 00:00:00.000000' && fromDateSelector.attr('data-default') !== '2000-02-01 00:00:00.000000')
                fromDateDefault = fromDateSelector.attr('data-default');

            fromDateSelector.datetimepicker({
                format: "YYYY-MM-DD HH:mm",
                ignoreReadonly: true,
                defaultDate: fromDateDefault,
                useCurrent: false,
                showClear: true,
                minDate: new Date(2016, 11, 9),
                maxDate: new Date()
            }).on("dp.change", function (e) {
                if( e.date === null ) toDateSelector.data("DateTimePicker").minDate(new Date(2016, 11, 9));
                else toDateSelector.data("DateTimePicker").minDate(e.date);
                $(".dateButton.from button[data-module-id='" + id +"']").removeClass('active');
            });


            var toDateDefault = null;
            if( toDateSelector.attr('data-default') !== 'notSet' && toDateSelector.attr('data-default') !== '2000-01-01 00:00:00.000000' && toDateSelector.attr('data-default') !== '2000-01-07 00:00:00.000000' && toDateSelector.attr('data-default') !== '2000-02-01 00:00:00.000000')
                toDateDefault = toDateSelector.attr('data-default');

            toDateSelector.datetimepicker({
                format: "YYYY-MM-DD HH:mm",
                ignoreReadonly: true,
                defaultDate: toDateDefault,
                useCurrent: false,
                showClear: true,
                minDate: new Date(2016, 11, 9),
                maxDate: new Date()
            }).on("dp.change", function (e) {
                if( e.date === null ) fromDateSelector.data("DateTimePicker").maxDate(new Date());
                else fromDateSelector.data("DateTimePicker").maxDate(e.date);
                $(".dateButton.to button[data-module-id='" + id +"']").removeClass('active');
            });

        }
    });

}

function configModule(id, data) {
    $.ajax({
        url: 'api/module/' + id,
        type: 'post',
        data: data,
        success: function (data) {
            //alert(data);
            renderModule(id);
        }
    });
}

function renderPage(id) {
    var path = $("#renderPage").attr('data-page-render-url');
    path = path.replace("page_id", id);

    $.ajax({
        url: path,
        dataType:"JSON",
        type: 'post',
        success: function (data) {
            $('#modules').html("");

            if(data.modules.length === 0)
                alert('No modules founded! :( please add modules to this page!');

            var used = 0;
            var lastRow = 0;
            $.each( data.modules, function( key, module ) {

                if( used + module.size > Math.ceil(used/12)*12 ){
                    lastRow++;
                    $('#modules').append("<div class='row' id='row-" + lastRow + "'></div>");
                }
                used += module.size;
                $('#row-' + lastRow).append("<div id='renderModule-" + module.id + "'></div>");
                renderModule(module.id);

            });

        }
    });
}

//======================================================================================================================


$("#renderPage").on( "click", ".option-module-save", function() {
    var id = $(this).attr('data-module-id');

    var analytics = $("#option-module-" + id + "-analytics" + " input[name='analytics-" + id + "']:checked").val();
    var userType = function () {
        var temp = "#option-module-" + id + "-userType" + " input[name='userType-" + id + "']";
        var result = [];
        if( $(temp + ":checked").length === $(temp).length ) return null;
        else if( $(temp + ":checked").length === 0 ) {console.log('please select something'); return null;}
        else $(temp + ":checked").each( function() { result.push($(this).val()); });
        return result;
    };
    var availability = $("#option-module-" + id + "-availability" + " input[name='availability-" + id + "']:checked").val();
    var deviceType = $("#option-module-" + id + "-deviceType" + " input[name='deviceType-" + id + "']:checked").val();
    var keyword = $("#option-module-" + id + "-keyword").val();
    var fromDate = $('#option-module-' + id + ' .fromDatetimepicker input').val();
    var toDate = $('#option-module-' + id + ' .toDatetimepicker input').val();


    configModule(id, {
        'settings' : {
            'analytics' : analytics,
            'userType' : userType(),
            'availability' : availability,
            'deviceType' : deviceType,
            'keyword' : keyword,
            'fromDate' : fromDate,
            'toDate' : toDate
        }
    });


    $('#option-module-' + id).modal('hide');


}).on( "click", '.dateButton button', function() {

    var id = $(this).attr('data-module-id');

    var fromDateSelector = $('#option-module-' + id + ' .fromDatetimepicker').data("DateTimePicker");
    var fromInputSelector = $('#option-module-' + id + ' .fromDatetimepicker input');

    var toDateSelector = $('#option-module-' + id + ' .toDatetimepicker').data("DateTimePicker");
    var toInputSelector = $('#option-module-' + id + ' .toDatetimepicker input');


    if($(this).parent().hasClass('from')){
        fromDateSelector.clear();
        if($(this).hasClass('day')){
            fromInputSelector.val('Yesterday');
            /*console.log($(this).closest('.row').find('.to button'));
            $(this).closest('.row').find('.to button').each(function() {
                var yesterday = new Date('2017-06-29');
                //yesterday.setDate(yesterday.getDate() - 1);
                toDateSelector.minDate(yesterday);
                $(this).addClass('disabled');
            });*/
        }
        if($(this).hasClass('week')){
            fromInputSelector.val('A week ago');
        }
        if($(this).hasClass('month')){
            fromInputSelector.val('A month ago');
        }
    }

    if($(this).parent().hasClass('to')){
        toDateSelector.clear();
        if($(this).hasClass('day')){
            toInputSelector.val('Yesterday');
        }
        if($(this).hasClass('week')){
            toInputSelector.val('A week ago');
        }
        if($(this).hasClass('month')){
            toInputSelector.val('A month ago');
        }
    }






    $(this).parent().children().each(function() {
        $(this).removeClass('active');
    });
    $(this).addClass('active');
});

$('.js_page_render').click(function() {
    renderPage($(this).attr('data-pageid'));
});

$(document).ready(function() {
    renderPage($("#renderPage").attr('data-page-id-first-load'));
});