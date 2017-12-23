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
                //setTimeout(function() { scopetmp.clear();}, 2000);


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
                scopetmp.message('Das neue Projekt wurde gespeichert', 'Es wurde ein Backlog und der erste Sprint für diese Projekt erstellt')
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

    $("body").on("click", "#insert_new_sprint", function(e) {

        var sprintname = $("#inp_new_sprint_name").val();
        var projekt = $("#inp_add_projekt_sprint").val();


        scopetmp.closeDialog();

        if (sprintname,projekt) {

            $.ajax({
                type: 'POST',
                url: 'ajax/insert_new_sprint.php',
                data: {
                    sprintname: sprintname,
                    projekt: projekt,

                },
                success: function (data) {

                    scopetmp.message('Der Neue Sprint wurde erstellt.', 'Der Sprint wurde dem Backlog vom Projekt zugeordnet.')

                },
                error: function (xhr, textStatus, error) {
                    console.log(xhr.statusText);
                    console.log(textStatus);
                    console.log(error);
                },

            }).done(function(result)
            {


            });
        }else
        {
            scopetmp.message('Es wurd keine neuer Sprint erstellt', 'Es ist ein Fehler aufgetreten oder es Wurde keinem Projekt zugeordnet.')
        }

    });

    $("body").on("click", "#update_new_task", function(e) {

        var titel = $("#update_task_titel").val();
        var description = $("#update_beschrieb_titel").val();
        var prio = $("#update_prio").val();
        var sprint = $("#update_sprint").val();
        var status = $("#update_status").val();

        scopetmp.closeDialog();

        if (titel && description && prio && sprint && status  ) {

            $.ajax({
                type: 'POST',
                url: 'ajax/update_task.php',
                data: {
                    titel: titel,
                    description: description,
                    prio: prio,
                    sprint:sprint,
                    status:status

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
                scopetmp.message('Änderungen wurden gespeichert', '')
                //setTimeout(function() { scopetmp.clear();}, 2000);


            });
        }else
        {
            scopetmp.message('Änderungen konnten nicht gespeichert werden', '')
        }

    });



});
function drag(ev){
    ev.dataTransfer.setData("text", ev.target.id);
}

function dropped(ev){

    scopetmp.showDialoges('updateTaskProject');
    scopetmp.getSprint();
    ev.preventDefault();
    ev.stopPropagation();
    var data = ev.dataTransfer.getData("text");
    var $tar = $(ev.target);
        if($tar.is("li")){
        tar=$tar.parent("ul").attr('id');
        }


}