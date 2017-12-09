<?php
class ProjectCollection extends openTaskTrackerSnippet
{
    function __construct()
    {
        parent::__construct();
    }

    function buildContent($filter)
    {
        $type="allTasks";

        $statement = $this->pdo->prepare("SELECT * FROM Task
LEFT JOIN Priority ON Priority.pri_id = Task.task_pri_id
LEFT JOIN TagInTask ON tit_task_id = task_id 
LEFT JOIN Tag ON tag_id = tit_tag_id
LEFT JOIN Taskstatus ON tst_id = task_tst_id
LEFT JOIN Backlog ON blog_id = task_blog_id
Left JOIN Sprint ON  spr_blog_id = blog_id
LEFT JOIN Project ON  pro_id = blog_pro_id
LEFT JOIN Projectstatus ON pst_id = pro_pst_id
LEFT JOIN `Group` ON grp_id = pro_grp_id
LEFT JOIN  UserInGroup ON usrgrp_grp_id = grp_id
LEFT JOIN  `User` ON usrgrp_grp_id = usr_id

");

        $statement->execute();
        $task = $statement->fetchAll(PDO::FETCH_ASSOC);

        switch ($type) {
            case "allTasks": {
                $devjson = array();
                $devjson["tasks"] = $task;

                $frt = json_encode($devjson);
                $this->json = $frt;
            }break;
            case"":{

            }break;
    }
    }
}