var scopetmp = {};
$(document).ready(function () {

    scopetmp = angular.element(document.body).scope();


    scopetmp.getTasks();
    scopetmp.getUser();
    scopetmp.getGroup();
    scopetmp.getProjekt();


    $("body").on("click", "#save_new_task", function(e) {

        var titel = $("#inp_task_titel").val();
        var description = $("#inp_beschrieb_titel").val();
        var prio = $("#inp_prio").val();

        scopetmp.closeDialog();

        if (titel && description && prio) {

            $.ajax({
                type: 'POST',
                url: 'ajax/insert_new_task.php',
                data: {
                    titel: titel,
                    description: description,
                    prio: prio,
                },
                success: function (data) {
                },
                error: function (xhr, textStatus, error) {
                    console.log(xhr.statusText);
                    console.log(textStatus);
                    console.log(error);
                },

            }).done(function(result)
            {
                scopetmp.message('Der Neue Task wurde gespeichert', 'Der Status wurde automatisch auf open gesetzt')
                setTimeout(function() { scopetmp.clear();}, 2000);


            });
        }else
        {
            scopetmp.message('Task konnte nicht erstellt werden', 'Wurden sämtliche Pflichtfelder ausgefüllt')
        }

    });

    $("body").on("click", "#save_new_projekt", function(e) {

        var titel = $("#inp_projekt_titel").val();
        var group = $("#inp_group").val();
        var state = $("#inp_status").val();


        scopetmp.closeDialog();

        if (titel && group && state) {

            $.ajax({
                type: 'POST',
                url: 'ajax/insert_new_projekt.php',
                data: {
                    titel: titel,
                    group: group,
                    state: state,
                },
                success: function (data) {

                },
                error: function (xhr, textStatus, error) {
                    console.log(xhr.statusText);
                    console.log(textStatus);
                    console.log(error);
                },

            }).done(function(result)
            {
                scopetmp.message('Das neue Projekt wurde gespeichert', 'Es wurde ein Backlog für diese Projekt erstellt')
                setTimeout(function() { scopetmp.clear();}, 2000);

            });
        }else
        {
            scopetmp.message('Projekt konnte nicht erstellt werden', 'Wurden sämtliche Pflichtfelder ausgefüllt')
        }

    });

    $("body").on("click", "#insert_new_group", function(e) {

        var groupname = $("#inp_new_group_name").val();


        scopetmp.closeDialog();

        if (groupname) {

            $.ajax({
                type: 'POST',
                url: 'ajax/insert_new_group.php',
                data: {
                    groupname: groupname,

                },
                success: function (data) {

                    scopetmp.message('Die neue Gruppe wurde erstellt', 'Sie können nun User der Gruppe hinzufügen')

                },
                error: function (xhr, textStatus, error) {
                    console.log(xhr.statusText);
                    console.log(textStatus);
                    console.log(error);
                },

            }).done(function(result)
            {

                scopetmp.getGroup();


            });
        }else
        {
            scopetmp.message('Es wurd keine neue Gruppe erstellt', 'Eine Gruppe ohne Namen kann nicht existieren')
        }

    });


});
function drag(ev){
    ev.dataTransfer.setData("text", ev.target.id);
}

function dropped(ev){
    ev.preventDefault();
    ev.stopPropagation();
    var data = ev.dataTransfer.getData("text");
    var $tar = $(ev.target);
        if($tar.is("li")){
        tar=$tar.parent("ul").attr('id');
        }


}