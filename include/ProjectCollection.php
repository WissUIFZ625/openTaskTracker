<?php
class ProjectCollection extends openTaskTrackerSnippet
{
    function __construct()
    {
        parent::__construct();
    }

    function buildContent($filter)
    {
        $statement = $this->pdo->prepare("SELECT task_id, task_name ,task_pri_id from Task");
        $statement->execute();
        $task = $statement->fetchAll(PDO::FETCH_ASSOC);

      

        $devjson = array();
        $devjson["tasks"] = $task;

        $frt = json_encode($devjson);
        $this->json = $frt;
    }
}