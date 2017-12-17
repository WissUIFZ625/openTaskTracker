var openTaskTracker_App = angular.module('openTaskTracker_App', ['ngMaterial','dndLists']);
//var baseUrl = 'http://localhost:54037/Home/'


openTaskTracker_App.controller('openTaskTracker_Ctrl', function ($scope, $http, $mdDialog)
{
    $scope.models = {
        selected: null,
        lists: { "Produktebacklogs": [], "Sprints": [], "Archiv": []}
    };

    $scope.displayvieu = 12;
    $scope.ngfilterdiv = false;

    $scope.status =  '';
    $scope.beareiter = '';
    $scope.backlogs = '';
    $scope.major = '';

    $scope.tasks = '';
    $scope.users = '';
    $scope.projects = '';
    $scope.groups = '';




    // Model to JSON for demo purpose
    $scope.$watch('models', function(model) {
        $scope.modelAsJson = angular.toJson(model, true);
    }, true);


    $scope.projectstatus = ('open closed running' +
        '').split(' ').map(function(state) {
        return {abbrev: state};
    });


    $scope.taskprio = ('low;mid;hight' +
        '').split(';').map(function(state) {
        return {abbrev: state};
    });



    $scope.closeDialog = function () {
        $mdDialog.hide();
    };



    $scope.showDialoges = function (id_dialog) {
        var dialog_id =id_dialog;
        $mdDialog.show({
            contentElement: '#'+dialog_id,
            parent: angular.element(document.body),
            clickOutsideToClose: true,
            escapeToClose: true
        });
    };



    $scope.test = function (e) {
        alert("Work in Progress");
    };



    $scope.getTasks =function (){
        flt = new Object();
        flt.type ="allTasks";
        // use $.param jQuery function to serialize data from JSON
        var data = $.param({
            target_id: "ProjectCollection", //target_id: "WebSwitchCollection"
            filter_str: flt
        });

        var config = {
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded;charset=utf-8;'
            }
        };

        $http.post('ajax/getjsondata.php', data, config)
            .success(function (response, status, headers, config) {
               //alert(response.tasks[1].task_name);
                $scope.tasks = response.tasks;

                 for( var i = 0; i < $scope.tasks.length; ++i) {
                     if ($scope.tasks[i].task_tst_id != 3) {
                         if (!$scope.tasks[i].task_spr_id) {
                             $scope.models.lists.Produktebacklogs.push({
                                 label: "Titel: " + $scope.tasks[i].task_name,
                                 listId: "task_" + $scope.tasks[i].task_id
                             });
                         }
                         else if ($scope.tasks[i].task_spr_id) {
                             $scope.models.lists.Sprints.push({
                                 label: "Titel: " + $scope.tasks[i].task_name,
                                 listId: "task_" + $scope.tasks[i].task_id
                             });
                         }
                     }
                     if($scope.tasks[i].task_tst_id == 3) {
                         $scope.models.lists.Archiv.push({
                             label: "Titel: " + $scope.tasks[i].task_name,
                             listId: "task_" + $scope.tasks[i].task_id
                         });

                     }

                 }
            })
            .error(function (data, status, header, config) {
                //alert(data);
            });
    }

  /*  $scope.getTasksInSprint =function() {
        flt.type ="allTasks";
        // use $.param jQuery function to serialize data from JSON
        var data = $.param({
            target_id: "ProjectCollection", //target_id: "WebSwitchCollection"
            filter_str: flt
        });

        var config = {
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded;charset=utf-8;'
            }
        };

        $http.post('ajax/getjsondata.php', data, config)
            .success(function (response, status, headers, config) {
                //alert(response.tasks[1].task_name);
                $scope.tasks = response.tasks;
                for( var i = 0; i < $scope.tasks.length; ++i) {
                    if ($scope.tasks[i].task_spr_id) {
                        $scope.models.lists.Sprints.push({label: "Sprint " + $scope.tasks[i].task_name, listId: "task" + $scope.tasks[i].task_id
                        });
                    }
                }
            })
            .error(function (data, status, header, config) {
                //alert(data);
            });

    }*/

    $scope.getGroup =function() {
        flt.type ="group";
        var data = $.param({
            target_id: "ProjectCollection", //target_id: "WebSwitchCollection"
            filter_str: flt
        });

        var config = {
            headers : {
                'Content-Type': 'application/x-www-form-urlencoded;charset=utf-8;'
            }
        };

        $http.post('ajax/getjsondata.php', data, config)
            .success(function (response, status, headers, config) {

                $scope.groups = response.groups;


            })
            .error(function (data, status, header, config) {
                //alert(data);
            });
    };

    $scope.getUser =function() {
        flt.type ="user";
        var data = $.param({
            target_id: "ProjectCollection", //target_id: "WebSwitchCollection"
            filter_str: flt
        });

        var config = {
            headers : {
                'Content-Type': 'application/x-www-form-urlencoded;charset=utf-8;'
            }
        };

        $http.post('ajax/getjsondata.php', data, config)
            .success(function (response, status, headers, config) {

                $scope.users = response.users;


            })
            .error(function (data, status, header, config) {
                //alert(data);
            });
    };

    $scope.getProjekt =function() {
        flt.type ="project";
        var data = $.param({
            target_id: "ProjectCollection", //target_id: "WebSwitchCollection"
            filter_str: flt
        });

        var config = {
            headers : {
                'Content-Type': 'application/x-www-form-urlencoded;charset=utf-8;'
            }
        };

        $http.post('ajax/getjsondata.php', data, config)
            .success(function (response, status, headers, config) {

                $scope.projects = response.projects;


            })
            .error(function (data, status, header, config) {
                //alert(data);
            });
    };

    $scope.clear = function () {
        location.reload();
    }



    $scope.message = function (titel,subtitel) {
        var titel = titel;
        var subtitel = subtitel;

        $mdDialog.show(
            $mdDialog.alert()
                .parent(angular.element(document.querySelector('#popupContainer')))
                .clickOutsideToClose(true)
                .title(titel)
                .textContent(subtitel)
                .ariaLabel('Message Dialog')
                .ok('OK')
        );
    };
});
