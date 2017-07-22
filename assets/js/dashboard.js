
function renderModule(id) {
    var path = $("#modules").attr('data-module-render-url');
    path = path.replace("module_id", id);

    $.ajax({
        url: path,
        success: function (data) {
            $("#renderModule-" + id).html(data);
            $('#module-' + id + ' .module-title').text($('#module-' + id + ' .highcharts-title').text());


            $('#filter-search-' + id + '-add').on( "click", function() {
                var index = $('#option-module-'+ id + '-search').attr('data-number');
                $.ajax({
                    url: "/web/app_dev.php/module/" + id + "/search/filter/" + index,
                    success: function (data) {
                        $('#option-module-'+ id + '-search').append(data).attr('data-number', ++index);
                    }
                });
            });

            $('#cat-search-' + id + '-add').on( "click", function() {
                var index = $('#option-module-'+ id + '-category-search').attr('data-number');
                $.ajax({
                    url: "/web/app_dev.php/module/" + id + "/search/cat/" + index,
                    success: function (data) {
                        $('#option-module-'+ id + '-category-search').append(data).attr('data-number', ++index);
                    }
                });
            });

            $('#filter-date-' + id + '-add').on( "click", function() {
                var index = $('#option-module-'+ id + '-date').attr('data-number');
                $.ajax({
                    url: "/web/app_dev.php/module/" + id + "/date/filter/" + index,
                    success: function (data) {
                        $('#option-module-'+ id + '-date').append(data).attr('data-number', ++index);

                        var fromDateSelector = $('#option-module-' + id + '-date .fromDatetimepicker');
                        var toDateSelector = $('#option-module-' + id + '-date .toDatetimepicker');

                        fromDateSelector.datetimepicker({
                            format: "YYYY-MM-DD HH:mm",
                            ignoreReadonly: true,
                            useCurrent: false,
                            showClear: true,
                            minDate: new Date(2016, 11, 9),
                            maxDate: new Date()
                        }).on("dp.change", function (e) {
                            if( e.date === null ) toDateSelector.data("DateTimePicker").minDate(new Date(2016, 11, 9));
                            else toDateSelector.data("DateTimePicker").minDate(e.date);
                            $(".dateButton.from button[data-module-id='" + id +"']").removeClass('active');
                        });

                        toDateSelector.datetimepicker({
                            format: "YYYY-MM-DD HH:mm",
                            ignoreReadonly: true,
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
            });

            $('#cat-date-' + id + '-add').on( "click", function() {
                var index = $('#option-module-'+ id + '-category-date').attr('data-number');
                $.ajax({
                    url: "/web/app_dev.php/module/" + id + "/date/cat/" + index,
                    success: function (data) {
                        $('#option-module-'+ id + '-category-date').append(data).attr('data-number', ++index);

                        var fromDateSelector = $('#option-module-' + id + '-category-date .fromDatetimepicker');
                        var toDateSelector = $('#option-module-' + id + '-category-date .toDatetimepicker');

                        fromDateSelector.datetimepicker({
                            format: "YYYY-MM-DD HH:mm",
                            ignoreReadonly: true,
                            useCurrent: false,
                            showClear: true,
                            minDate: new Date(2016, 11, 9),
                            maxDate: new Date()
                        }).on("dp.change", function (e) {
                            if( e.date === null ) toDateSelector.data("DateTimePicker").minDate(new Date(2016, 11, 9));
                            else toDateSelector.data("DateTimePicker").minDate(e.date);
                            $(".dateButton.from button[data-module-id='" + id +"']").removeClass('active');
                        });

                        toDateSelector.datetimepicker({
                            format: "YYYY-MM-DD HH:mm",
                            ignoreReadonly: true,
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
            });

            $('#option-module-'+ id).on( "click", '.multi-remove', function() {
                $(this).closest('fieldset').remove();
            });


            setDefaultOptions(id);
        }
    });
}

//var searchAddButton = "<button class='btn btn-primary filter-search-add' style='float: right;margin: 0 0 5px 0;'>Add more</button>";

function setDefaultOptions(id) {
    // presentation
    var presentationSelector = $("#option-module-" + id + "-presentation");
    presentationSelector.find("input[value='" + presentationSelector.attr('data-default') + "']").attr("checked", "checked");
    // user type
    var userTypeSelector = $("#option-module-" + id + "-userType");
    userTypeSelector.find("input").each( function() {
       if( $(this).attr('data-default') === 'checked' ) $(this).attr("checked", "checked");
    });
    // availability
    var availabilitySelector = $("#option-module-" + id + "-availability");
    availabilitySelector.find("input").each( function() {
        if( $(this).attr('data-default') === 'checked' ) $(this).attr("checked", "checked");
    });
    // device type
    var deviceTypeSelector = $("#option-module-" + id + "-deviceType");
    deviceTypeSelector.find("input").each( function() {
        if( $(this).attr('data-default') === 'checked' ) $(this).attr("checked", "checked");
    });

    // filter search
    var searchSelector = $("#option-module-" + id + "-search");
    searchSelector.find("input").each( function() {
        if( this.id.includes('negate') && $(this).attr('data-default') === "1") $(this).attr("checked", "checked");
        else $(this).val( $(this).attr('data-default'));
    });
    searchSelector.find("select").each( function() {
        if( this.id.includes('columns') ) {
            var selected = [];
            $(this).find('option').each( function() {
                if( $(this).attr('data-default') === 'checked' ) selected.push($(this).val());
            });
            $(this).val(selected);
        }
        else if(!this.id.includes('columns')) $(this).val( $(this).attr('data-default'));
    });

    // filter date
    var dateSelector = $("#option-module-" + id + "-date");
    dateSelector.find("input").each( function() {
        $(this).val( $(this).attr('data-default'));
    });
    dateSelector.find(".fromDatetimepicker").each( function() {
        var fromDateSelector = $(this);
        var toDateSelector = $(this).closest(".row").find(".toDatetimepicker");

        var fromDefault = fromDateSelector.attr('data-default');
        if(fromDefault === "") fromDefault = new Date(2016, 11, 9);
        var toDefault = toDateSelector.attr('data-default');
        if(toDefault === "") toDefault = null;

        fromDateSelector.datetimepicker({
            format: "YYYY-MM-DD HH:mm",
            ignoreReadonly: true,
            defaultDate: fromDefault,
            useCurrent: false,
            showClear: true,
            minDate: new Date(2016, 11, 9),
            maxDate: toDefault
        }).on("dp.change", function (e) {
            if( e.date === null ) toDateSelector.data("DateTimePicker").minDate(new Date(2016, 11, 9));
            else toDateSelector.data("DateTimePicker").minDate(e.date);
            $(".dateButton.from button[data-module-id='" + id +"']").removeClass('active');
        });

    });
    dateSelector.find(".toDatetimepicker").each( function() {
        var fromDateSelector = $(this).closest(".row").find(".fromDatetimepicker");
        var toDateSelector = $(this);

        var fromDefault = fromDateSelector.attr('data-default');
        if(fromDefault === "") fromDefault = new Date(2016, 11, 9);
        var toDefault = toDateSelector.attr('data-default');
        if(toDefault === "") toDefault = null;

        toDateSelector.datetimepicker({
            format: "YYYY-MM-DD HH:mm",
            ignoreReadonly: true,
            defaultDate: toDefault,
            useCurrent: false,
            showClear: true,
            minDate: fromDefault,
            maxDate: new Date()
        }).on("dp.change", function (e) {
            if( e.date === null ) fromDateSelector.data("DateTimePicker").maxDate(new Date());
            else fromDateSelector.data("DateTimePicker").maxDate(e.date);
            $(".dateButton.to button[data-module-id='" + id +"']").removeClass('active');
        });
    });

    //single cats
    var singleCatsSelector = $("#option-module-" + id + "-singleCat");
    singleCatsSelector.find("input").each( function() {
        if( $(this).attr('data-default') === 'checked' ) $(this).attr("checked", "checked");
    });

    // cat search
    var searchCatSelector = $("#option-module-" + id + "-category-search");
    searchCatSelector.find("input").each( function() {
        if( this.id.includes('negate') && $(this).attr('data-default') === "1") $(this).attr("checked", "checked");
        else if(!this.id.includes('negate')) $(this).val( $(this).attr('data-default'));
    });
    searchCatSelector.find("select").each( function() {
        if( this.id.includes('columns') ) {
            var selected = [];
            $(this).find('option').each( function() {
                if( $(this).attr('data-default') === 'checked' ) selected.push($(this).val());
            });
            $(this).val(selected);
        }
        else if(!this.id.includes('columns')) $(this).val( $(this).attr('data-default'));
    });

    // filter date
    var dateCatSelector = $("#option-module-" + id + "-category-date");
    dateCatSelector.find("input").each( function() {
        $(this).val( $(this).attr('data-default'));
    });
    dateCatSelector.find(".fromDatetimepicker").each( function() {
        var fromDateSelector = $(this);
        var toDateSelector = $(this).closest(".row").find(".toDatetimepicker");

        var fromDefault = fromDateSelector.attr('data-default');
        if(fromDefault === "") fromDefault = new Date(2016, 11, 9);
        var toDefault = toDateSelector.attr('data-default');
        if(toDefault === "") toDefault = null;

        fromDateSelector.datetimepicker({
            format: "YYYY-MM-DD HH:mm",
            ignoreReadonly: true,
            defaultDate: fromDefault,
            useCurrent: false,
            showClear: true,
            minDate: new Date(2016, 11, 9),
            maxDate: toDefault
        }).on("dp.change", function (e) {
            if( e.date === null ) toDateSelector.data("DateTimePicker").minDate(new Date(2016, 11, 9));
            else toDateSelector.data("DateTimePicker").minDate(e.date);
            $(".dateButton.from button[data-module-id='" + id +"']").removeClass('active');
        });

    });
    dateCatSelector.find(".toDatetimepicker").each( function() {
        var fromDateSelector = $(this).closest(".row").find(".fromDatetimepicker");
        var toDateSelector = $(this);

        var fromDefault = fromDateSelector.attr('data-default');
        if(fromDefault === "") fromDefault = new Date(2016, 11, 9);
        var toDefault = toDateSelector.attr('data-default');
        if(toDefault === "") toDefault = null;

        toDateSelector.datetimepicker({
            format: "YYYY-MM-DD HH:mm",
            ignoreReadonly: true,
            defaultDate: toDefault,
            useCurrent: false,
            showClear: true,
            minDate: fromDefault,
            maxDate: new Date()
        }).on("dp.change", function (e) {
            if( e.date === null ) fromDateSelector.data("DateTimePicker").maxDate(new Date());
            else fromDateSelector.data("DateTimePicker").maxDate(e.date);
            $(".dateButton.to button[data-module-id='" + id +"']").removeClass('active');
        });
    });

/*
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
*/

    $('#loading-' + id).hide();
    $('#module-' + id + '-container').show();

}

function configModule(id, data) {
    $.ajax({
        url: 'api/module/' + id,
        type: 'post',
        data: data,
        success: function (data) {
            //alert(data);
            //console.log(data);
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
    var inputSelectorStr = function(fieldName){return "#option-module-" + id + "-" + fieldName + " input[name='" + fieldName + "-" + id + "']";};
    var searchSelectorNew = function(section, fieldName, index){return $("#" + section + "-search-" + id + "-" + fieldName + "-" + index)};
    var dateSelector = function(section, fieldName){return $("#" + section + "-date-" + id + "-" + fieldName)};
    var dateSelectorNew = function(section, fieldName, index){return $("#" + section + "-date-" + id + "-" + fieldName + "-" + index)};

    var getMultipleSection = function(fieldName){
        var result = [];
        if( $(inputSelectorStr(fieldName) + ":checked").length === 0 ) return null;
        else $(inputSelectorStr(fieldName) + ":checked").each( function() { result.push($(this).val()); });
        return result;
    };


    //  Filters
    var presentation = $(inputSelectorStr('presentation') + ":checked").val();
    var filter_userType = getMultipleSection('userType');
    var filter_availability = getMultipleSection('availability');
    var filter_deviceType = getMultipleSection('deviceType');
    var filter_search = {};
    var filter_date = {};

    $("#option-module-" + id + "-search").find('fieldset').each( function() {
        var index = $(this).attr('data-index');
        if( searchSelectorNew('filter', 'title', index).val() !== "" && searchSelectorNew('filter', 'keyword', index).val() !== "" ){
            filter_search[searchSelectorNew('filter', 'title', index).val()] = {
                "columnOperator" : searchSelectorNew('filter', 'col-op', index).val(),
                "columns" : searchSelectorNew('filter', 'columns', index).val(),
                "expressionOperator" : searchSelectorNew('filter', 'expr-op', index).val(),
                "keyword" : searchSelectorNew('filter', 'keyword', index).val(),
                "negate" : $("#filter-search-" + id + "-negate-" + index + ":checked").val() === 'negate'
            };
        }
    });

    $("#option-module-" + id + "-date").find('fieldset').each( function() {
        var index = $(this).attr('data-index');
        if( dateSelectorNew('filter', 'title', index).val() !== "" ){
            filter_date[dateSelectorNew('filter', 'title', index).val()] = { //auto title
                "operator" : dateSelectorNew('filter', 'expr-op', index).val(),
                "column" : dateSelectorNew('filter', 'column', index).val(),
                "from" : dateSelectorNew('filter', 'from', index).val(),
                "to" : dateSelectorNew('filter', 'to', index).val(),
                "negate" : false
            };
        }
    });




    //  Categories
    var cat_single = getMultipleSection('singleCat');
    var cat_search = {};
    var cat_date = {};

    $("#option-module-" + id + "-category-search").find('fieldset').each( function() {
        var index = $(this).attr('data-index');
        if( searchSelectorNew('cat', 'title', index).val() !== "" && searchSelectorNew('cat', 'keyword', index).val() !== "" ){
            cat_search[searchSelectorNew('cat', 'title', index).val()] = {
                "columnOperator" : searchSelectorNew('cat', 'col-op', index).val(),
                "columns" : searchSelectorNew('cat', 'columns', index).val(),
                "expressionOperator" : 'and',
                "keyword" : searchSelectorNew('cat', 'keyword', index).val(),
                "negate" : $("#cat-search-" + id + "-negate-" + index + ":checked").val() === 'negate'
            };
        }
    });


    $("#option-module-" + id + "-category-date").find('fieldset').each( function() {
        var index = $(this).attr('data-index');
        if( dateSelectorNew('cat', 'title', index).val() !== "" ){
            cat_date[dateSelectorNew('cat', 'title', index).val()] = { //auto title
                "operator" : 'and',
                "column" : dateSelectorNew('cat', 'column', index).val(),
                "from" : dateSelectorNew('cat', 'from', index).val(),
                "to" : dateSelectorNew('cat', 'to', index).val(),
                "negate" : false
            };
        }
    });


    var data = {
        'categories' : {
            'single' : cat_single,
            'multi' : {
                'search' : cat_search,
                'date' : cat_date
            }
        },
        'filters' : {
            'user_type' : filter_userType,
            'availability' : filter_availability,
            'device_type' : filter_deviceType,
            'search' : filter_search,
            'date' : filter_date
        },
        'presentation' : presentation,
        'remove_zeros' : true
    };

    $('#module-' + id + '-container').hide();
    $('#loading-' + id).show();
    //console.log(data);
    configModule(id, data);


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