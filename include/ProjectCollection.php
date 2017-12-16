<?php

class ProjectCollection extends openTaskTrackerSnippet
{
    function __construct()
    {
        parent::__construct();
    }



    function buildContent($filter)
    {
        $type='';

        if (is_array($filter) && key_exists('type', $filter)) {
            $type = $filter['type'];
        }



        switch ($type) {
            case "allTasks": {
                $statement = $this->pdo->prepare("SELECT * FROM Task
                LEFT JOIN Priority ON Priority.pri_id = Task.task_pri_id
                LEFT JOIN TagInTask ON tit_task_id = task_id 
                LEFT JOIN Tag ON tag_id = tit_tag_id
                LEFT JOIN Taskstatus ON tst_id = task_tst_id
                LEFT JOIN Backlog ON blog_id = task_blog_id
                LEFT JOIN Sprint ON  spr_blog_id = blog_id
                LEFT JOIN Project ON  pro_id = blog_pro_id
                LEFT JOIN Projectstatus ON pst_id = pro_pst_id
                LEFT JOIN `Group` ON grp_id = pro_grp_id
                LEFT JOIN  UserInGroup ON usrgrp_grp_id = grp_id
                LEFT JOIN  `User` ON usrgrp_grp_id = usr_id
");

                $statement->execute();
                $task = $statement->fetchAll(PDO::FETCH_ASSOC);
                $devjson = array();
                $devjson["tasks"] = $task;

                $frt = json_encode($devjson);
                $this->json = $frt;
            }
                break;
            case"project": {
                $statement = $this->pdo->prepare("SELECT * FROM Project");
                $statement->execute();
                $project = $statement->fetchAll(PDO::FETCH_ASSOC);
                $devjson = array();
                $devjson["projects"] = $project;

                $frt = json_encode($devjson);
                $this->json = $frt;
            }
                break;
            case"sprint": {
                $statement = $this->pdo->prepare("SELECT * FROM Sprint");
                $statement->execute();
                $project = $statement->fetchAll(PDO::FETCH_ASSOC);
                $devjson = array();
                $devjson["sprints"] = $project;

                $frt = json_encode($devjson);
                $this->json = $frt;
            }
                break;
            case"group": {
                $statement = $this->pdo->prepare("SELECT * FROM `Group`");
                $statement->execute();
                $group = $statement->fetchAll(PDO::FETCH_ASSOC);
                $devjson = array();
                $devjson["groups"] = $group;

                $frt = json_encode($devjson);
                $this->json = $frt;
            }
                break;
            case"user": {
                $statement = $this->pdo->prepare("SELECT * FROM `User`");
                $statement->execute();
                $user = $statement->fetchAll(PDO::FETCH_ASSOC);
                $devjson = array();
                $devjson["users"] = $user;

                $frt = json_encode($devjson);
                $this->json = $frt;
            }
                break;
            case"Tags": {
                $statement = $this->pdo->prepare("SELECT * FROM `Tag`");
                $statement->execute();
                $tag = $statement->fetchAll(PDO::FETCH_ASSOC);
                $devjson = array();
                $devjson["tags"] = $tag;

                $frt = json_encode($devjson);
                $this->json = $frt;
            }
                break;
            default: {
                return "errorCaseType";
            }
                break;
        }
    }
}