<?php
namespace App;

class Statistic
{
    public function getCheckList($data)
    {
        try {
           $StatisticRepo = new StatisticRepository();
           $result = $StatisticRepo->getCheckList($data);
            echo $result;
        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
            return null;
        }
    }

    public function getCheckListDetailed($data)
    {
        try {
            $StatisticRepo = new StatisticRepository();
            $result = $StatisticRepo->getCheckListDetailed($data);
            echo $result;
        }catch (PDOException $e) {
            echo "". $e->getMessage();
        }
    }

}