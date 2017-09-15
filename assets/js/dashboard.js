var today = new Date();
today.setDate(today.getDate() + 1);
today.setHours(0);
today.setMinutes(0);
today.setSeconds(0);

var yesterday = new Date();
yesterday.setDate(yesterday.getDate() - 2);
yesterday.setHours(23);
yesterday.setMinutes(59);
yesterday.setSeconds(59);

var weekAgo = new Date();
weekAgo.setDate(weekAgo.getDate() - 8);
weekAgo.setHours(23);
weekAgo.setMinutes(59);
weekAgo.setSeconds(59);

var monthAgo = new Date();
monthAgo.setDate(monthAgo.getDate() - 31);
monthAgo.setHours(23);
monthAgo.setMinutes(59);
monthAgo.setSeconds(59);

var monthsAgo = new Date();
monthsAgo.setDate(monthsAgo.getDate() - 91);
monthsAgo.setHours(23);
monthsAgo.setMinutes(59);
monthsAgo.setSeconds(59);

var yearAgo = new Date(2016, 11, 9);
//yearAgo.setDate(yearAgo.getDate() - 366);
//yearAgo.setHours(23);
//yearAgo.setMinutes(59);
//yearAgo.setSeconds(59);

var layoutSTR = '#tab-layout';
var presentationSTR = '#tab-presentation';
var filtersSTR = '#tab-filters';
var categoriesSTR = '#tab-categories';

/**
 * Sets minDate of a picker and clears it if it is not valid
 * @param picker    > DateTimePicker
 * @param value     > A Date obj which should be used to validate and limit the picker
 */
function setMinIfLess(picker, value){
    if(picker.date() !== null && picker.date() < value) picker.clear();
    picker.minDate(value);
}

/**
 * Sets maxDate of a picker and clears it if it is not valid
 * @param picker    > DateTimePicker
 * @param value     > A Date obj which should be used to validate and limit the picker
 */
function setMaxIfMore(picker, value){
    if(picker.date() !== null && picker.date() > value) picker.clear();
    picker.maxDate(value);
}

/**
 * TODO: include dateTime here
 * Sets the value of fields to their data-default attribute
 * @param parent    > The section
 * @param selector  > The field selector (type)
 * @param value     > The default value for radio boxes
 */
function setDataDefault(parent, selector, value){
    parent.find(selector).each( function() {
        if (value !== null && $(this).val() === value) $(this).attr("checked", "checked");
        else if ( $(this).attr('multiple') === 'multiple' ) {
            var selected = [];
            $(this).find('option').each( function() { if( $(this).attr('data-default') === 'checked' ) selected.push($(this).val()); });
            $(this).val(selected);
        }
        else if ( $(this).attr('data-default') === 'checked' ) $(this).attr("checked", "checked");
        else if ($(this).attr('disabled') !== 'disabled' && $(this).attr('data-default') !== undefined) {$(this).val($(this).attr('data-default'));}
    });
}

/**
 * Validate DateTimePicker default value
 * @param fromDateSelector  > From DateTimePicker selector
 * @param toDateSelector    > To DateTimePicker selector
 * @returns {{from, to}}
 */
function validateDateTimePicker(fromDateSelector, toDateSelector) {
    var fromDefault = fromDateSelector.attr('data-default');
    if(fromDefault === undefined || fromDefault === '2000-01-01' || fromDefault === '2000-01-07' || fromDefault === '2000-02-01') fromDefault = new Date(2016, 11, 9);
    var toDefault = toDateSelector.attr('data-default');
    if(toDefault === undefined || toDefault === '2000-01-01' || toDefault === '2000-01-07' || toDefault === '2000-02-01') toDefault = today;
    return {'from' : fromDefault, 'to' : toDefault};
}

/**
 * Initialising a DateTimePicker, depending on another picker. If the picker is
 * 'from' picker, minORmax should set to "setMin". If the picker is 'to' picker,
 * minORmax should set to "setMax".
 * @param picker        > DateTimePicker to initialise
 * @param defaultDate   > Default value of Picker
 * @param minDate       > Min date of picker
 * @param maxDate       > Max date of Picker
 * @param otherPicker   > Other DateTimePicker which this picker would have an effect on
 * @param minOrmax      > Min date or Max date of other picker would be effected by this picker
 */
function initPicker(picker, defaultDate, minDate, maxDate, otherPicker, minOrmax) {
    picker.datetimepicker({
        format: "YYYY-MM-DD HH:mm",
        ignoreReadonly: true,
        defaultDate: defaultDate,
        useCurrent: false,
        showClear: true,
        minDate: minDate,
        maxDate: maxDate,
        showTodayButton: true
    }).on("dp.change", function (e) {
        if(minOrmax === 'setMin'){
            if( e.date === null ) otherPicker.data("DateTimePicker").minDate(new Date(2016, 11, 9));
            else otherPicker.data("DateTimePicker").minDate(e.date);
        } else if(minOrmax === 'setMax'){
            if( e.date === null ) otherPicker.data("DateTimePicker").maxDate(new Date());
            else otherPicker.data("DateTimePicker").maxDate(e.date);
        }
        $(this).closest('.date').find('.dateButton button').each(function() { $(this).removeClass('active').removeClass('btn-success').addClass('btn-info'); });
        $(this).closest('.date').siblings('.date').find('.dateButton button').each(function() { $(this).removeAttr('disabled'); });

        if (picker.hasClass('toDatetimepicker')) $(this).closest('.date').find('.now').addClass('off');
        //$(this).closest('.date').find('.now').addClass('off');
        $(this).find('input').data('data-last_text', null);
    });
}

/**
 * Initialise dateTimePicker and sets its default value
 * @param selector  > The section
 */
function setDateTimePicker(selector){

    selector.find(".fromDatetimepicker").each( function() {
        var fromDateSelector = $(this);
        var toDateSelector = $(this).closest(".row").find(".toDatetimepicker");
        var defaultValues = validateDateTimePicker(fromDateSelector, toDateSelector);
        initPicker(fromDateSelector, defaultValues.from, new Date(2016, 11, 9), defaultValues.to, toDateSelector, 'setMin');
    });
    selector.find(".toDatetimepicker").each( function() {
        var fromDateSelector = $(this).closest(".row").find(".fromDatetimepicker");
        var toDateSelector = $(this);
        var defaultValues = validateDateTimePicker(fromDateSelector, toDateSelector);
        initPicker(toDateSelector, (defaultValues.to === today) ? null : defaultValues.to, defaultValues.from, today, fromDateSelector, 'setMax');
    });
    selector.on( "click", '.dateButton button', function() {
        var input = $(this).closest('.date').find('input');
        var picker = input.parent().data("DateTimePicker");
        var dateParent = input.closest('.date');
        var nowLED = dateParent.find('.now');
        var otherDateParent = dateParent.siblings('.date');
        var otherInput = otherDateParent.find('input');
        var otherButtons = otherDateParent.find('.dateButton');
        var otherPicker = otherInput.parent().data("DateTimePicker");
        var isFrom = input.parent().hasClass('fromDatetimepicker');

        otherButtons.find('button').each(function() { $(this).removeAttr('disabled'); });
        picker.clear();

        var year = otherButtons.find('.year');
        var months = otherButtons.find('.months');
        var month = otherButtons.find('.month');
        var week = otherButtons.find('.week');
        var day = otherButtons.find('.day');

        var makeDisable = [];

        /**
         * TODO: make a function for this part
         */
        if($(this).hasClass('day')){
            input.val('Yesterday').data('data-last_text', 'Yesterday');
            if(isFrom) {
                makeDisable.push(year, months, month, week, day);
                setMinIfLess(otherPicker, yesterday);
            }
            else {
                makeDisable.push(day);
                setMaxIfMore(otherPicker, yesterday);
            }
        }
        else if($(this).hasClass('week')){
            input.val('A week ago').data('data-last_text', 'A week ago');
            if(isFrom){
                makeDisable.push(year, months, month, week);
                setMinIfLess(otherPicker, weekAgo);
            }
            else {
                makeDisable.push(week, day);
                setMaxIfMore(otherPicker, weekAgo);
            }
        }
        else if($(this).hasClass('month')){
            input.val('A month ago').data('data-last_text', 'A month ago');
            if(isFrom) {
                makeDisable.push(year, months, month);
                setMinIfLess(otherPicker, monthAgo);
            }
            else {
                makeDisable.push(month, week, day);
                setMaxIfMore(otherPicker, monthAgo);
            }
        }
        else if($(this).hasClass('months')){
            input.val('3 months ago').data('data-last_text', '3 months ago');
            if(isFrom) {
                makeDisable.push(year, months);
                setMinIfLess(otherPicker, monthsAgo);
            }
            else {
                makeDisable.push(months, month, week, day);
                setMaxIfMore(otherPicker, monthsAgo);
            }
        }
        else if($(this).hasClass('year')){
            input.val('One year ago').data('data-last_text', 'One year ago');
            if(isFrom) {
                makeDisable.push(year);
                setMinIfLess(otherPicker, yearAgo);
            }
            else {
                makeDisable.push(year, months, month, week, day);
                setMaxIfMore(otherPicker, yearAgo);
            }
        }

        makeDisable.forEach(function(element) {
            if(element.hasClass('active')) otherInput.val(null);
            element.attr('disabled', 'disabled').removeClass('active').removeClass('btn-success').addClass('btn-info');
        });

        $(this).parent().children().each(function() { $(this).removeClass('active').removeClass('btn-success').addClass('btn-info'); });
        $(this).removeClass('btn-info').addClass('active').addClass('btn-success');
        nowLED.addClass('off');
    });
    selector.on( "click", "span.input-group-addon", function() {
        var input = $(this).siblings('input');
        var oldText = input.data('data-last_text');
        if(oldText !== null) $(this).siblings('input').val(oldText);

        $(this).siblings('.bootstrap-datetimepicker-widget').find("a[data-action='clear']").click(function() {
            /**
             * TODO: make a function for this part
             */
            $(this).closest('.date').find('.dateButton button').each(function() { $(this).removeClass('active').removeClass('btn-success').addClass('btn-info'); });
            var picker = $(this).closest('.date').find('input').parent().data("DateTimePicker");
            picker.clear();
            var otherDate= $(this).closest('.date').siblings('.date');
            otherDate.find('.dateButton button').each(function() { $(this).removeAttr('disabled'); });
            var otherPicker = otherDate.find('input').parent().data("DateTimePicker");
            otherPicker.maxDate(today);
            otherPicker.minDate(new Date(2016, 11, 9));

            input.data('data-last_text', null);

            if(input.parent().hasClass('toDatetimepicker')) input.closest('.date').find('.now').removeClass('off');
        });
    });
    selector.on( "click", ".now", function() {
        /**
         * TODO: make a function for this part
         */
        $(this).closest('.date').find('.dateButton button').each(function() { $(this).removeClass('active').removeClass('btn-success').addClass('btn-info'); });
        var picker = $(this).closest('.date').find('input').parent().data("DateTimePicker");
        picker.clear();
        var otherDate= $(this).closest('.date').siblings('.date');
        otherDate.find('.dateButton button').each(function() { $(this).removeAttr('disabled'); });
        var otherPicker = otherDate.find('input').parent().data("DateTimePicker");
        otherPicker.maxDate(today);
        otherPicker.minDate(new Date(2016, 11, 9));

        $(this).closest('.date').find('input').data('data-last_text', null);

        $(this).removeClass('off');
    });
    selector.find(".date input").each( function() {
        $(this).data('data-last_text', null);
        var value = $(this).attr('data-default');
        var buttonDiv = $(this).closest('.date').find('.dateButton');

        if( value === '2000-01-01' ) buttonDiv.find('.day').trigger('click');
        else if( value === '2000-01-07' ) buttonDiv.find('.week').trigger('click');
        else if( value === '2000-02-01' ) buttonDiv.find('.month').trigger('click');
        else if( value === '2000-03-01' ) buttonDiv.find('.months').trigger('click');
        else if( value === '2001-01-01' ) buttonDiv.find('.year').trigger('click');
        else if( value !== undefined && $(this).parent().hasClass('toDatetimepicker') && ($(this).val() !== undefined && $(this).val() !== "" && $(this).val() !== null )) $(this).closest('.date').find('.now').addClass('off');


        //var thisInput = $(this);
        //$('#option-module-' + id).on('show.bs.modal', function () {
        //    thisInput.val(value);
        //});
    });
}

function makeTwoWayTitle(selector, defaultTitle) {
    selector.on('keyup', function(){
        var value = $(this).val();
        var title = $(this).closest('fieldset').find('.dynamicTitle');
        if(value !== "") { title.text(value); $(this).css('background-color', '#fff').css('border-color', '#ccc')}
        else { title.text(defaultTitle); $(this).css('background-color', '#fff1f1').css('border-color', '#980000'); }
    }).on('focusout', function(){
        var value = $(this).val();
        if(value !== "") { $(this).css('background-color', '#fff').css('border-color', '#ccc')}
        else { $(this).css('background-color', '#fff1f1').css('border-color', '#980000'); }
    });
}

function makeRequire(selector) {
    selector.on('keyup', function(){
        var value = $(this).val();
        if(value !== "") { $(this).css('background-color', '#fff').css('border-color', '#ccc')}
        else { $(this).css('background-color', '#fff1f1').css('border-color', '#980000'); }
    }).on('focusout', function(){
        var value = $(this).val();
        if(value !== "") { $(this).css('background-color', '#fff').css('border-color', '#ccc')}
        else { $(this).css('background-color', '#fff1f1').css('border-color', '#980000'); }
    });
}

function initSearchDate(tab) {
    tab.find('.js_search-init .title').each(function() { makeTwoWayTitle($(this), 'New Search'); });
    tab.find('.js_search-init .keyword').each(function() { makeRequire($(this)); });
    tab.find('.js_date-init .title').each(function() { makeTwoWayTitle($(this), 'New Date'); });
    /**
     * TODO: move date time init to here
     */
    tab.on( "click", '.multi-remove', function() { $(this).closest('fieldset').remove(); });
    var addSearchCategoryButton = tab.find('.js_search-add');
    var addDateCategoryButton = tab.find('.js_date-add');

    addSearchCategoryButton.on( "click", function() {
        var index = tab.find('.js_search-init').attr('data-number');
        var path = tab.attr('data-url-module_search');
        //path = path.replace("module_id", id);
        //path = path.replace("module_section", 'category');
        path = path.replace("module_index", index);

        $.ajax({
            url: path,
            success: function (data) {
                var section = tab.find('.js_search-init');
                section.append(data);
                makeTwoWayTitle(section.find("fieldset[data-index='" + index + "'] .title"), 'New Search');
                makeRequire($("fieldset[data-index='" + index + "'] .keyword"));
                section.attr('data-number', ++index);
            }
        });
    });

    addDateCategoryButton.on( "click", function() {
        var index = tab.find('.js_date-init').attr('data-number');
        var path = tab.attr('data-url-module_date');
        //path = path.replace("module_id", id);
        //path = path.replace("module_section", 'category');
        path = path.replace("module_index", index);

        $.ajax({
            url: path,
            success: function (data) {
                var section = tab.find('.js_date-init');
                section.append(data);
                makeTwoWayTitle(section.find("fieldset[data-index='" + index + "'] .title"), 'New Date');
                section.attr('data-number', ++index);
                var fieldSet = section.children().last();
                var fromDateSelector = fieldSet.find('.fromDatetimepicker');
                var toDateSelector = fieldSet.find('.toDatetimepicker');
                initPicker(fromDateSelector, new Date(2016, 11, 9), new Date(2016, 11, 9), today, toDateSelector, 'setMin');
                initPicker(toDateSelector, null, new Date(2016, 11, 9), today, fromDateSelector, 'setMax');
            }
        });
    });

    $( ".js_search-init" ).sortable({
        placeholder: "ui-state-highlight",
        forcePlaceholderSize: true
    }).disableSelection();

    $( ".js_date-init" ).sortable({
        placeholder: "ui-state-highlight",
        forcePlaceholderSize: true
    }).disableSelection();
}

/**
 * Loads wizard tabs using ajax
 * @param infoID    > The module type ID
 * @param moduleID  > The module ID
 * @param cacheObj  > The cache object
 */
function loadTabs(infoID, moduleID, cacheObj) {
    var path = $(".mod-options").attr('data-url-module_tabs');
    path = path.replace("moduleInfo_id", infoID);
    if(path.includes('module_id')) path = path.replace("module_id", moduleID);

    $.ajax({
        type: 'POST',
        url: path,
        cache: cacheObj,
        success: function(data) {
            var base = $('#option-module-' + moduleID);
            base.find(".js_dynamic-tab").remove();
            base.find(".mod-options div.tab-content").append(data);
            if (this.cache !== undefined) loadCacheToDefault(this.cache, moduleID);
            setDefaultOptions(moduleID); //if module is not undefiend
            initializeOptions(moduleID);
            if (this.cache !== undefined) {
                var newType = $('#tab-layout-' + moduleID + ' .mod-graph-type').find("option[data-info-index='" + infoID + "']").val();
                $('#tab-layout-' + moduleID + ' .mod-graph-type select').val(newType);
            }
            base.find('.dateButton .btn').tooltip({container: 'body'});
        }
    });
}

function setDefaultOptions(id) {
    var layout = $(layoutSTR);
    var presentation = $(presentationSTR);
    var filters = $(filtersSTR);
    var categories = $(categoriesSTR);

    if(id !== undefined && id !== null && id !== ""){
        layout = $(layoutSTR + '-' + id);
        presentation = $(presentationSTR + '-' + id);
        filters = $(filtersSTR + '-' + id);
        categories = $(categoriesSTR + '-' + id);
    }

    setDataDefault(layout, 'input');
    setDataDefault(layout, 'select');

    setDataDefault(presentation, 'input');
    setDataDefault(presentation, '.mod-data input', presentation.find('.mod-data').attr('data-default'));
    setDataDefault(presentation, '.mod-interval input', presentation.find('.mod-interval').attr('data-default'));
    //set default remove zeroes value

    setDataDefault(filters, 'input');
    setDataDefault(filters, 'select');
    //move this to initialize
    setDateTimePicker(filters);

    setDataDefault(categories, 'input');
    setDataDefault(categories, 'select');
    //move this to initialize
    setDateTimePicker(categories);
}

function initializeOptions(id) {
    var filters = $(filtersSTR);
    var categories = $(categoriesSTR);

    if(id !== undefined && id !== null && id !== ""){
        filters = $(filtersSTR + '-' + id);
        categories = $(categoriesSTR + '-' + id);
    }

    initSearchDate(filters);
    initSearchDate(categories);
}

function cacheChanges(id) {
    var temp;

    var changesToBeLoad = {};

    var layout = $(layoutSTR);
    var presentation = $(presentationSTR);
    var filters = $(filtersSTR);
    var categories = $(categoriesSTR);

    if(id !== undefined && id !== null && id !== ""){
        layout = $(layoutSTR + '-' + id);
        presentation = $(presentationSTR + '-' + id);
        filters = $(filtersSTR + '-' + id);
        categories = $(categoriesSTR + '-' + id);
    }

    changesToBeLoad.layout = {
        'mod-module-type' : layout.find('select[name="mod-module-type"]').val(),
        'mod-graph-type' : layout.find('select[name="mod-graph-type"] option:selected').attr('data-info-index'),
        'mod-name' : layout.find('input[name="mod-name"]').val(),
        'mod-size' : layout.find('select[name="mod-size"]').val(),
        'mod-color' : layout.find('select[name="mod-color"]').val(),
        'mod-rank' : layout.find('input[name="mod-rank"]').val()
    };

    changesToBeLoad.presentation = {
        'mod-data' : {'value' : presentation.find('input[name="mod-data"]:checked').val(), 'type' : 'radio'},
        'mod-interval' : {'value' : presentation.find('input[name="mod-interval"]:checked').val(), 'type' : 'radio'},
        'mod-zero' : {'value' : presentation.find('input[name="mod-zero"]').is(':checked'), 'type' : 'checkbox'}
    };

    var filter_userType = [];
    var filter_deviceType = [];
    var filter_availability = [];

    filters.find('input[name="mod-filter-userType"]:checked').each( function() { filter_userType.push($(this).val()); });
    filters.find('input[name="mod-filter-availability"]:checked').each( function() { filter_availability.push($(this).val()); });
    filters.find('input[name="mod-filter-deviceType"]:checked').each( function() { filter_deviceType.push($(this).val()); });

    temp = cacheMultiFields(filters);


    changesToBeLoad.filters = {
        'mod-filter-userType' : {'value' : filter_userType, 'type' : 'checkbox'},
        'mod-filter-availability' : {'value' : filter_availability, 'type' : 'checkbox'},
        'mod-filter-deviceType' : {'value' : filter_deviceType, 'type' : 'checkbox'},
        'mod-filter-search' : {'value' : temp.search, 'type' : 'search'},
        'mod-filter-date' : {'value' : temp.date, 'type' : 'date'}
    };


    //  Categories
    var cat_splitting = [];

    categories.find('input[name="mod-splittingCat"]:checked').each( function() { cat_splitting.push($(this).val()); });
    temp = cacheMultiFields(categories);



    changesToBeLoad.categories = {
        'mod-splittingCat' : {'value' : cat_splitting, 'type' : 'checkbox'},
        'mod-category-search' : {'value' : temp.search, 'type' : 'search'},
        'mod-category-date' : {'value' : temp.date, 'type' : 'date'}
    };

    return changesToBeLoad;
}


function cacheMultiFields(section) {
    var search = [];
    var date = [];

    section.find('.js_search-init fieldset').each( function() {
        if( $(this).find('input[name="title"]').val() !== "" && $(this).find('input[name="keyword"]').val() !== "" ){ //columns should not be null too
            search[ $(this).find('input[name="title"]').val() ] = {
                "columnOperator" : $(this).find('select[name="col-op"]').val(),
                "columns" : $(this).find('select[name="columns"]').val(),
                "expressionOperator" : $(this).find('select[name="expr-op"]').val(),
                "keyword" : $(this).find('input[name="keyword"]').val(),
                "negate" : $(this).find('input[name="negate"]').is(':checked')
            };
        }
    });

    section.find('.js_date-init fieldset').each( function() {
        if( $(this).find('input[name="title"]').val() !== "" ){
            date[ $(this).find('input[name="title"]').val() ] = { //auto title
                "operator" : $(this).find('select[name="expr-op"]').val(),
                "column" : $(this).find('select[name="column"]').val(),
                "from" : $(this).find('input[name="from"]').val(),
                "to" : $(this).find('input[name="to"]').val(),
                "negate" : false
            };
        }
    });

    return {'search' : search, 'date' : date};
}

function loadCacheToDefault(cacheObj, id){
    var filters = $(filtersSTR);
    var categories = $(categoriesSTR);

    if(id !== undefined && id !== null && id !== ""){
        filters = $(filtersSTR + '-' + id);
        categories = $(categoriesSTR + '-' + id);
    }

    //remove all checkbox data-defaults
    filters.find('.checkbox input').attr('data-default', null);
    categories.find('.checkbox input').attr('data-default', null);

    for (var property in cacheObj.presentation) {
        if( cacheObj.presentation[property].type === 'checkbox' )
            $("input[name='" + property + "']").attr('data-default', cacheObj.presentation[property].value === true ? 'checked' : null).attr('checked', cacheObj.presentation[property].value === true ? 'checked' : null);
        else if ( cacheObj.presentation[property].type === 'radio' )
            $('.' + property).attr('data-default', cacheObj.presentation[property].value);
    }

    for (var property in cacheObj.filters) {

        var type = cacheObj.filters[property].type;

        if( type === 'checkbox' ) {
            cacheObj.filters[property].value.forEach(function(value) {
                $("input[name='" + property + "'][value='" + value + "']").attr('data-default', 'checked');
            });

        }else if ( type === 'search' || type === 'date' ){

            var searchFieldsets = $('.mod-filter-search fieldset');
            var length_search = searchFieldsets.length;

            var dateFieldsets = $('.mod-filter-date fieldset');
            var length_date = dateFieldsets.length;

            var values = cacheObj.filters[property].value;
            for (var key in values) {

                if( type === 'search' ) {
                    if ( length_search > 0 ){
                        length_search--;
                        loadCacheToDefaultAfterAajx_search($('.mod-filter-search').find("fieldset[data-index='" + length_search + "']"), key, values[key], false);
                    }else {
                        var index = filters.find('.js_search-init').attr('data-number');
                        var path = filters.attr('data-url-module_search');
                        path = path.replace("module_index", index);

                        $.ajax({
                            url: path,
                            index: index,
                            title: key,
                            values: values[key],
                            success: function (data) {
                                var section = filters.find('.js_search-init');
                                section.append(data);
                                makeTwoWayTitle(section.find("fieldset[data-index='" + this.index + "'] .title"), 'New Search');
                                makeRequire($("fieldset[data-index='" + this.index + "'] .keyword"));
                                section.attr('data-number', ++index);
                                loadCacheToDefaultAfterAajx_search($('.mod-filter-search').find('fieldset').last(), this.title, this.values, true);
                            }
                        });
                    }
                }else if( type === 'date' ) {
                    if ( length_date > 0 ){
                        length_date--;
                        loadCacheToDefaultAfterAajx_date($('.mod-filter-date').find("fieldset[data-index='" + length_date + "']"), key, values[key], false);
                    }else {
                        var index = filters.find('.js_date-init').attr('data-number');
                        var path = filters.attr('data-url-module_date');
                        path = path.replace("module_index", index);

                        $.ajax({
                            url: path,
                            index: index,
                            title: key,
                            values: values[key],
                            success: function (data) {
                                var section = filters.find('.js_date-init');
                                section.append(data);
                                makeTwoWayTitle(section.find("fieldset[data-index='" + this.index + "'] .title"), 'New Date');
                                section.attr('data-number', ++index);
                                loadCacheToDefaultAfterAajx_date($('.mod-filter-date').find('fieldset').last(), this.title, this.values, true);
                            }
                        });

                    }




                }
            }
        }

    }

    for (var property in cacheObj.categories) {

        var typeCat = cacheObj.categories[property].type;

        if( typeCat === 'checkbox' ) {
            cacheObj.categories[property].value.forEach(function(value) {
                $("input[name='" + property + "'][value='" + value + "']").attr('data-default', 'checked');
            });

        }else if ( type === 'search' || type === 'date' ) {

            var searchFieldsetsCat = $('.mod-category-search fieldset');
            var length_searchCat = searchFieldsetsCat.length;

            var dateFieldsetsCat = $('.mod-category-date fieldset');
            var length_dateCat = dateFieldsetsCat.length;

            var valuesCat = cacheObj.categories[property].value;

            for (var keyCat in valuesCat) {

                if (typeCat === 'search') {

                    if (length_searchCat > 0) {
                        length_searchCat--;
                        loadCacheToDefaultAfterAajx_search($('.mod-category-search').find("fieldset[data-index='" + length_searchCat + "']"), keyCat, valuesCat[keyCat], false);
                    } else {
                        var index = categories.find('.js_search-init').attr('data-number');
                        var path = categories.attr('data-url-module_search');
                        path = path.replace("module_index", index);

                        $.ajax({
                            url: path,
                            index: index,
                            title: keyCat,
                            values: valuesCat[keyCat],
                            success: function (data) {
                                var section = categories.find('.js_search-init');
                                section.append(data);
                                makeTwoWayTitle(section.find("fieldset[data-index='" + this.index + "'] .title"), 'New Search');
                                makeRequire($("fieldset[data-index='" + this.index + "'] .keyword"));
                                section.attr('data-number', ++index);
                                loadCacheToDefaultAfterAajx_search($('.mod-category-search').find('fieldset').last(), this.title, this.values, true);

                            }
                        });
                    }
                } else if (typeCat === 'date') {
                    if (length_dateCat > 0) {
                        length_dateCat--;
                        loadCacheToDefaultAfterAajx_date($('.mod-category-date').find("fieldset[data-index='" + length_dateCat + "']"), keyCat, valuesCat[keyCat], false);
                    } else {
                        var index = categories.find('.js_date-init').attr('data-number');
                        var path = categories.attr('data-url-module_date');
                        path = path.replace("module_index", index);

                        $.ajax({
                            url: path,
                            index: index,
                            title: keyCat,
                            values: valuesCat[keyCat],
                            success: function (data) {
                                var section = categories.find('.js_date-init');
                                section.append(data);
                                makeTwoWayTitle(section.find("fieldset[data-index='" + this.index + "'] .title"), 'New Date');
                                section.attr('data-number', ++index);
                                loadCacheToDefaultAfterAajx_date($('.mod-category-date').find('fieldset').last(), this.title, this.values, true);
                            }
                        });

                    }
                }
            }
        }
    }
}

function loadCacheToDefaultAfterAajx_search(selector, title, value, isNew) {
    selector.find('.dynamicTitle').text(title);

    if( isNew ){
        selector.find("input[name='title']").val(title);
        selector.find("input[name='keyword']").val(value.keyword);
        selector.find("select[name='columns']").val(value.columns);
        selector.find("select[name='col-op']").val(value.columnOperator);
        selector.find("select[name='expr-op']").val(value.expressionOperator);
        if( value.negate === true ) selector.find("input[name='negate']").attr('checked', 'checked');
    }//else {
    selector.find("input[name='title']").attr('data-default', title);
    selector.find("input[name='keyword']").attr('data-default', value.keyword);
    selector.find("select[name='columns']").find('option').each( function() { if( value.columns.includes($(this).val()) ) $(this).attr('data-default', 'checked'); });
    selector.find("select[name='col-op']").attr('data-default', value.columnOperator);
    selector.find("select[name='expr-op']").attr('data-default', value.expressionOperator);
    if( value.negate === true ) selector.find("input[name='negate']").attr('data-default', 'checked');
    //}
}

function validate(from, to) {
    /*
     if( date === 'Yesterday' ) date = '2000-01-01';
     else if( date === 'A week ago' ) date = '2000-01-07';
     else if( date === 'A month ago' ) date = '2000-02-01';
     else if( date === '3 months ago' ) date = '2000-03-01';
     else if( date === 'One year ago' ) date = '2001-01-01';
     */

    if(from === undefined || from === 'Yesterday' || from === 'A week ago' || from === 'A month ago' || from === '3 months ago'  || from === 'One year ago' ) from = new Date(2016, 11, 9);
    if(to === undefined || to === 'Yesterday' || to === 'A week ago' || to === 'A month ago' || to === '3 months ago'  || to === 'One year ago' ) to = today;
    return {'from' : from, 'to' : to};

    //return date;
}

function validateForDefault(date) {

    if( date === 'Yesterday' ) date = '2000-01-01';
    else if( date === 'A week ago' ) date = '2000-01-07';
    else if( date === 'A month ago' ) date = '2000-02-01';
    else if( date === '3 months ago' ) date = '2000-03-01';
    else if( date === 'One year ago' ) date = '2001-01-01';

    return date;
}

function loadCacheToDefaultAfterAajx_date(selector, title, value, isNew) {
    selector.find('.dynamicTitle').text(title);

    var validated = validate(value.from, value.to);


    if( isNew ){
        selector.find("input[name='title']").val(title);
        selector.find("select[name='column']").val(value.column);
        //selector.find("input[name='from']").attr('data-default', value.from);
        //selector.find("input[name='to']").attr('data-default', value.to);


        var fromDateSelector = selector.find('.fromDatetimepicker');
        var toDateSelector = selector.find('.toDatetimepicker');
        initPickerSpecialEdition(fromDateSelector, validated.from, new Date(2016, 11, 9), today, toDateSelector, 'setMin', value.from);
        initPickerSpecialEdition(toDateSelector, (validated.to === today) ? null : validated.to, new Date(2016, 11, 9), today, fromDateSelector, 'setMax', value.to);

        applyDynamicDate(fromDateSelector, value.from);
        applyDynamicDate(toDateSelector, value.to);

        selector.find("select[name='expr-op']").val(value.operator);
    }//else {
    selector.find("input[name='title']").attr('data-default', title);
    selector.find("select[name='column']").attr('data-default', value.column);

    selector.find("input[name='from']").attr('data-default', validateForDefault(value.from));
    selector.find("input[name='to']").attr('data-default', validateForDefault(value.to));



    selector.find("select[name='expr-op']").attr('data-default', value.operator);
    //}

}

function initPickerSpecialEdition(picker, defaultDate, minDate, maxDate, otherPicker, minOrmax) {
    picker.datetimepicker({
        format: "YYYY-MM-DD HH:mm",
        ignoreReadonly: true,
        defaultDate: defaultDate,
        useCurrent: false,
        showClear: true,
        minDate: minDate,
        maxDate: maxDate,
        showTodayButton: true
    }).on("dp.change", function (e) {
        if(minOrmax === 'setMin'){
            if( e.date === null ) otherPicker.data("DateTimePicker").minDate(new Date(2016, 11, 9));
            else otherPicker.data("DateTimePicker").minDate(e.date);
        } else if(minOrmax === 'setMax'){
            if( e.date === null ) otherPicker.data("DateTimePicker").maxDate(new Date());
            else otherPicker.data("DateTimePicker").maxDate(e.date);
        }
        $(this).closest('.date').find('.dateButton button').each(function() { $(this).removeClass('active').removeClass('btn-success').addClass('btn-info'); });
        $(this).closest('.date').siblings('.date').find('.dateButton button').each(function() { $(this).removeAttr('disabled'); });

        if (picker.hasClass('toDatetimepicker')) $(this).closest('.date').find('.now').addClass('off');
        //$(this).closest('.date').find('.now').addClass('off');
        $(this).find('input').data('data-last_text', null);
    });

    if( defaultDate !== "" && picker.hasClass('toDatetimepicker') ) picker.closest('.date').find('.now').addClass('off');


    picker.find('input').data('data-last_text', defaultDate);
}

function applyDynamicDate(picker, rawValue) {
    var buttonDiv = picker.closest('.date').find('.dateButton');

    if( rawValue === 'Yesterday' ) buttonDiv.find('.day').trigger('click');
    else if( rawValue === 'A week ago' ) buttonDiv.find('.week').trigger('click');
    else if( rawValue === 'A month ago' ) buttonDiv.find('.month').trigger('click');
    else if( rawValue === '3 months ago' ) buttonDiv.find('.months').trigger('click');
    else if( rawValue === 'One year ago' ) buttonDiv.find('.year').trigger('click');
}



















function renderModule(id) {
    var path = $("#modules").attr('data-module-render-url');
    path = path.replace("module_id", id);

    $.ajax({
        url: path,
        success: function (data) {
            $("#renderModule-" + id).html(data);


            var path = $("#modules").attr('data-module-edit-url');
            path = path.replace("module_id", id);

            $.ajax({
                url: path,
                success: function (data) {
                    $("#renderModule-" + id).find('.js_options-init').html(data);



                    $('#option-module-' + id).on('show.bs.modal', function () {
                        var moduleOptions = $(this).find('.mod-options');
                        //the first time initialization
                        loadTabs(moduleOptions.find('.mod-graph-type select').find('option:selected').attr('data-info-index'), moduleOptions.attr('data-module_id'));

                        moduleOptions.on( 'change', '.mod-graph-type select', function() {
                            var id = moduleOptions.attr('data-module_id');
                            loadTabs($(this).find('option:selected').attr('data-info-index'), id, cacheChanges(id));
                        });

                        //select the tab
                        moduleOptions.find(".nav-tabs a:first").tab('show');
                    });


                }
            });

        }
    });
}

function configModule(id, data) {
    var path = $("#renderPage").attr('data-set-module-conf');
    path = path.replace("module_id", id);

    $.ajax({
        url: path,
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

function grabDataMultiFields(section) {
    var search = {};
    var date = {};

    section.find('.js_search-init fieldset').each( function() {
        if( $(this).find('input[name="title"]').val() !== "" && $(this).find('input[name="keyword"]').val() !== "" ){ //columns should not be null too
            search[ $(this).find('input[name="title"]').val() ] = {
                "columnOperator" : $(this).find('select[name="col-op"]').val(),
                "columns" : $(this).find('select[name="columns"]').val(),
                "expressionOperator" : ($(this).find('select[name="expr-op"]').val() === undefined) ? 'and' : $(this).find('select[name="expr-op"]').val(),
                "keyword" : $(this).find('input[name="keyword"]').val(),
                "negate" : $(this).find('input[name="negate"]').is(':checked')
            };
        }
    });

    section.find('.js_date-init fieldset').each( function() {
        if( $(this).find('input[name="title"]').val() !== "" ){
            date[ $(this).find('input[name="title"]').val() ] = { //auto title
                "operator" : ($(this).find('select[name="expr-op"]').val() === undefined) ? 'and' : $(this).find('select[name="expr-op"]').val(),
                "column" : $(this).find('select[name="column"]').val(),
                "from" : validateForDefault($(this).find('input[name="from"]').val()),
                "to" : validateForDefault($(this).find('input[name="to"]').val()),
                "negate" : false
            };
        }
    });

    return {'search' : search, 'date' : date};
}


$("#renderPage").on( "click", ".option-module-save", function() {
    var id = $(this).attr('data-module-id');

    var temp;

    var data = {};

    var layout = $(layoutSTR);
    var presentation = $(presentationSTR);
    var filters = $(filtersSTR);
    var categories = $(categoriesSTR);

    if(id !== undefined && id !== null && id !== ""){
        layout = $(layoutSTR + '-' + id);
        presentation = $(presentationSTR + '-' + id);
        filters = $(filtersSTR + '-' + id);
        categories = $(categoriesSTR + '-' + id);
    }


    data.info = layout.find('select[name="mod-graph-type"] option:selected').attr('data-info-index');
    data.rank = layout.find('input[name="mod-rank"]').val();

    data.layout = {
        //'mod-module-type' : layout.find('select[name="mod-module-type"]').val(),
        //'info' : layout.find('select[name="mod-graph-type"] option:selected').attr('data-info-index'),
        'title' : layout.find('input[name="mod-name"]').val(),
        'size' : layout.find('select[name="mod-size"]').val(),
        'color' : layout.find('select[name="mod-color"]').val()
        //'rank' : layout.find('input[name="mod-rank"]').val()
    };

    data.presentation = {
        'data' : presentation.find('input[name="mod-data"]:checked').val(),
        'interval' : presentation.find('input[name="mod-interval"]:checked').val(),
        'zero' : presentation.find('input[name="mod-zero"]').is(':checked')
    };

    var filter_userType = [];
    var filter_deviceType = [];
    var filter_availability = [];

    filters.find('input[name="mod-filter-userType"]:checked').each( function() { filter_userType.push($(this).val()); });
    filters.find('input[name="mod-filter-availability"]:checked').each( function() { filter_availability.push($(this).val()); });
    filters.find('input[name="mod-filter-deviceType"]:checked').each( function() { filter_deviceType.push($(this).val()); });

    temp = grabDataMultiFields(filters);


    data.filters = {
        'user_type' : filter_userType,
        'availability' : filter_availability,
        'device_type' : filter_deviceType,
        'search' : temp.search,
        'date' : temp.date
    };


    //  Categories
    var cat_splitting = [];

    categories.find('input[name="mod-splittingCat"]:checked').each( function() { cat_splitting.push($(this).val()); });
    temp = grabDataMultiFields(categories);



    data.categories = {
        'single' : cat_splitting,
        'multi' : {
            'search' : temp.search,
            'date' : temp.date
        }
    };


    $('#module-' + id + '-container').hide();
    $('#loading-' + id).show();
    //console.log(data);
    configModule(id, data);


    $('#option-module-' + id).modal('hide');


});

$('.js_page_render').click(function() {
    renderPage($(this).attr('data-pageid'));
});

function clock() {
    $.ajax({
        type: 'POST',
        url: './clock',
        timeout: 15000,
        success: function(data) {
            $("#clock").html("Server Time (UTC): " + data.month + "/" + data.day + "/" +  data.year + " @ " + data.hour + ":" + data.minute);
            window.setTimeout(clock, 15000);
        }
    });
}


$(document).ready(function() {
    renderPage($("#renderPage").attr('data-page-id-first-load'));
    clock();
    $("#clock").show({ effect: "fade", easing: 'easeOutQuint', duration: 1000});
});