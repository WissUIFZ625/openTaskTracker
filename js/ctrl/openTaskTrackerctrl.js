var openTaskTracker_App = angular.module('openTaskTracker_App', ['ngMaterial','dndLists']);
//var baseUrl = 'http://localhost:54037/Home/'

var mytimer;


openTaskTracker_App.controller('openTaskTracker_Ctrl', function ($scope, $http, $mdDialog)
{
    $scope.models = {
        selected: null,
        lists: { "Produktebacklogs": [], "Sprints": [], "Archiv": []}
    };

    $scope.displayvieu = 12;

    $scope.status =  '';
    $scope.beareiter = 'Markus';
    $scope.backlogs = '';
    $scope.major = '';


    $scope.tasks = {
        selected: null,
        lists: { "Produktebacklogs": [], "Sprints": [], "Archiv": []},
        blog_id: '',
        blog_pro_id: '',
        grp_id:'',
        grp_name:'',
        is_register_user:'',
        pri_id:'',
        pri_priority:'',
        pro_grp_id:'',
        pro_id:'',
        pro_name:'',
        pro_pst_id:'',
        pst_id:'',
        pst_projectstatus:'',
        spr_blog_id:'',
        spr_id:'',
        tag_id:'',
        tag_tag:'',
        task_actTime:'',
        task_alloTime:'',
        task_blog_id:'',
        task_description:'',
        task_id:'',
        task_name:-1,
        task_pri_id:'',
        task_spr_id:'',
        task_tst_id:'',
        tit_id:'',
        tit_tag_id:'',
        tit_task_id:'',
        tst_id:'',
        tst_taskstatus:'',
        usr_aut_id:'',
        usr_id:'',
        usr_name:'',
        usr_password:'',
        usr_salt:'',
        usrgrp_grp_id:'',
        usrgrp_id:'',
        usrgrp_usr_id:''
    }


    // Generate initial model
        $scope.models.lists.Produktebacklogs.push({label:"Produktbacklogs " +$scope.tasks.task_name },{label:"Produktbacklogs" });



    for (var i = 1; i <= 2; ++i) {
        $scope.models.lists.Archiv.push({label: "Projekte " + i +  $scope.beareiter});
    }


    for (var i = 1; i <= 10; ++i) {
        $scope.models.lists.Sprints.push({label: "Sprints A" + i});
    }



    // Model to JSON for demo purpose
    $scope.$watch('models', function(model) {
        $scope.modelAsJson = angular.toJson(model, true);
    }, true);


    $scope.projectstatus = ('open closed running' +
        '').split(' ').map(function(state) {
        return {abbrev: state};
    });

    $scope.projectgruppe = ('Admin;Was auch immer;User' +
        '').split(';').map(function(state) {
        return {abbrev: state};
    });

    $scope.tasktime = ('1;2;3;4;5;6;7;8;9' +
        '').split(';').map(function(state) {
        return {abbrev: state};
    });

    $scope.taskprio = ('low;mid;hight' +
        '').split(';').map(function(state) {
        return {abbrev: state};
    });

    $scope.projectbearbeiter = ('Markus Stefan Ivo' +
        '').split(' ').map(function(state) {
        return {abbrev: state};
    });

    $scope.produktbacklogs = ('Backlog1 Backlog2 Backlog3' +
        '').split(' ').map(function(state) {
        return {abbrev: state};
    });

    $scope.majorprojekt = ('majorprojekt1 majorprojekt2 majorprojekt3' +
        '').split(' ').map(function(state) {
        return {abbrev: state};
    });

    $scope.closeDialog = function () {
        $mdDialog.hide();
    };




	$scope.closeDialog = function()
	{
		$mdDialog.hide();
	};


    $scope.showNewProjectDialog = function () {
        $mdDialog.show({
            contentElement: '#newProject',
            parent: angular.element(document.body),
            clickOutsideToClose: true,
            escapeToClose: true
        });
    };

    $scope.showNewTaskDialog = function () {
        $mdDialog.show({
            contentElement: '#newTaskProject',
            parent: angular.element(document.body),
            clickOutsideToClose: true,
            escapeToClose: true
        });
    };

    $scope.showSettings = function () {
        $scope.getUser();
        $scope.getGroup();
        $mdDialog.show({
            contentElement: '#openSettings',
            parent: angular.element(document.body),
            clickOutsideToClose: true,
            escapeToClose: true
        });
    };





    $scope.test = function () {
        alert("Work in Progress");
    }



    $scope.getTasks =function (){
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
                //flt.type='';
                $scope.tasks = response.tasks;
            })
            .error(function (data, status, header, config) {
                //alert(data);
            });
    }

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

                $scope.tasks = response.tasks;


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

                $scope.tasks = response.tasks;


            })
            .error(function (data, status, header, config) {
                //alert(data);
            });
    };
});
