<?php
/**
 * Created by PhpStorm.
 * User: Ivo
 * Date: 02.12.2017
 * Time: 14:29
 */

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

        vardump($task);
    }
}