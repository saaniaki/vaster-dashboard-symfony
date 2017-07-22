var beingEddited = "";
var pendingModuleRemove = "";

function removeButton(id){
    return  "<div class='btn-group btn-group-lg' role='group' style='float: right'>" +
        "<button id='remove' class='btn btn-danger' data-removeLink=" +
        id + '/remove' +
        "><span class='glyphicon glyphicon-trash' aria-hidden='true'></span></button>" +
        "</div>";
}


//setInterval(loadTable, 2000);

function loadTable() {

    $.ajax({
        url: 'api/pages',
        dataType:"JSON",
        success: function (data) {
            $("#ajaxTbody").html("");
            $.each( data.pages, function( key, value ) {
                //alert( value.name + value.rank );
                $("#ajaxTbody").append(
                    "<tr class='page'>" +
                    "<td><span class='id'>" + value.id + "</span>" + value.name + "</td>" +
                    "<td class='rank'>" + value.rank + "</td>" +
                    "</tr>"
                )
            });
        }
    });
}

function loadEdit(id) {
    $("#newPanel").hide();
    $.ajax({
        url: id + '/edit',
        success: function (data) {
            $("#editPage").html(data);
            if( $('#ajaxTbody tr.page').length > 1 )
                $("#editPageContent").append(removeButton(id));
            $("#editPanel").trigger( "Content_Loaded" ).show();
        }
    });


}

function loadNew() {
    $("#editPanel").hide();
    $.ajax({
        url: 'new',
        success: function (data) {
            $("#addPage").html(data);
            //alert(data);
        }
    });
    $("#newPanel").show();
}

$( "#loadNew" ).click(function() {
    loadNew();
});



$('#pagesTable').on('click', '.page', function (e) {
    e.preventDefault();

    beingEddited = $(this).find(".id").text();
    loadEdit( beingEddited );
});



$('#editPanel').on('submit', '#update', function (e) {
    e.preventDefault();

    $.ajax({
        type: 'post',
        url: beingEddited + '/edit',
        data: $('#update').serialize(),
        success: function () {
            loadTable();
            loadEdit( beingEddited );
        }
    });

});

$('#editPanel').on('click', '#remove', function (e) {
    e.preventDefault();
    $('#confirm').modal();
});

$( "#doRemove" ).click(function() {
    $.ajax({
        url: $( "#remove" ).attr("data-removeLink"),
        success: function (data) {
            loadTable();
            loadNew();
            $('#confirm').modal('hide');
        }
    });
});




$('#newPanel').on('submit', '#add', function (e) {
    e.preventDefault();

    $.ajax({
        type: 'post',
        url: 'new',
        data: $('#add').serialize(),
        success: function () {
            console.log("here");
            loadTable();
            loadNew();
        }
    });

});



function loadEditCallback(id, rank) {
    //$("#newPanel").hide();
    $.ajax({
        url: id + '/edit',
        success: function (data) {

            //$("#editPanel").show();
            $("#update #app_bundle_new_page_rank").val(rank);
            $.ajax({
                type: 'post',
                url: id + '/edit',
                data: $('#update').serialize(),
                success: function () {
                    loadTable();
                    //loadNew();
                }
            });
        }
    });


}


function configModule(id, data) {
    $.ajax({
        url: '../api/module/' + id,
        type: 'post',
        data: data,
        success: function (data) {
            //alert(data);
            loadEdit(beingEddited);
        }
    });
}

$( document ).ready(function() {
    loadNew();


    $( "#ajaxTbody" ).sortable({
        placeholder: "ui-state-highlight",
        start: function() {
            $( "#pagesTable" ).removeClass("table-striped");
        },
        stop: function() {
            $( "#pagesTable" ).addClass("table-striped");

        },
        update: function(event, ui) {
            beingEddited = $(ui.item).find(".id").text();


            var intPrev = parseInt($(ui.item).prev().find(".rank").text());
            var intNext = parseInt($(ui.item).next().find(".rank").text());

            var rank = 0;

            if(isNaN(intPrev) && isNaN(intNext)){
                console.log("FATAL ERROR");
            }else if( isNaN(intPrev) ){
                rank = intNext - 100;
            }else if( isNaN(intNext) ){
                rank = intPrev + 100;
            }else{
                rank = (intPrev + intNext)/2;
            }

            loadEditCallback( beingEddited, rank  );
        }
    }).disableSelection();






});



function loadNewModule() {
    $.ajax({
        url: beingEddited + '/new-module',
        success: function (data) {
            $("#addModuleModal").find(".modal-body").html(data);
        }
    });
}

function loadEditModule(id) {
    $.ajax({
        url: 'edit-module/' + id,
        success: function (data) {
            $("#editModuleModal").find(".modal-body").html(data);
            $( "#doEditModule" ).attr('data-module-id', id);
        }
    });
}

$('#addModuleModal').on('submit', '#addModule', function (e) {
    e.preventDefault();
    $("#addModuleModal").modal('hide');
    $.ajax({
        type: 'post',
        url: beingEddited + '/new-module',
        data: $('#addModule').serialize(),
        success: function () {

            loadEdit(beingEddited);

        }
    });

});

$( "#doAddModule" ).click(function() {
    $("#addModule").trigger( "submit" );
});

$( "#doEditModule" ).click(function() {
    var id = $( "#doEditModule" ).attr('data-module-id');


    configModule(id, {
        'info' : $("#editModuleModal #app_bundle_new_module_moduleInfo option[value='" + $('#app_bundle_new_module_moduleInfo').val() + "']").attr('data-info-id'),
        'rank' : $('#editModuleModal #app_bundle_new_module_rank').val(),
        'layout': {
            'title' : null,
            'size' : $('#editModuleModal #app_bundle_new_module_size').val()
        }
    });


    $("#editModuleModal").modal('hide');
});




$( "#editPanel" ).on( "Content_Loaded", function() {
    $( "#loadNewModule" ).click(function() {
        loadNewModule();
        $("#addModuleModal").modal();

    });

    $( ".removeModule" ).click(function() {
        $('#confirmModuleRemoval').modal();
        pendingModuleRemove = $(this).attr("data-removeLink");
    });

    $( ".module-edit-link" ).click(function() {
        var id = $(this).parent().find('.module-id').text();
        loadEditModule(id);
        $('#editModuleModal').modal();
    });

    $( "#modules_ajaxTbody" ).sortable({
        placeholder: "ui-state-highlight",
        start: function() {
            $( "#modulesTable" ).removeClass("table-striped");
        },
        stop: function() {
            $( "#modulesTable" ).addClass("table-striped");

        },
        update: function(event, ui) {
            var id = $(ui.item).find(".module-id").text();


            var intPrev = parseInt($(ui.item).prev().find(".rank").text());
            var intNext = parseInt($(ui.item).next().find(".rank").text());

            var rank = 0;

            if(isNaN(intPrev) && isNaN(intNext)){
                console.log("FATAL ERROR");
            }else if( isNaN(intPrev) ){
                rank = intNext - 100;
            }else if( isNaN(intNext) ){
                rank = intPrev + 100;
            }else{
                rank = (intPrev + intNext)/2;
            }

            configModule(id, {
                'rank' : rank
            });
        }
    }).disableSelection();

});



$( "#doRemoveModule" ).click(function() {
    $.ajax({
        url: pendingModuleRemove,
        success: function (data) {
            loadTable();
            loadEdit(beingEddited);
            $('#confirmModuleRemoval').modal('hide');
        }
    });
});