<?php
//复制一个课程
session_start();
include "../inc/config.php";
setcookie(session_name(),session_id(),time()+$sessionTime,"/");
if(isset($_SESSION["role"]) && ($_SESSION["role"]==ADMIN || $_SESSION["role"]==TEACHER)){//判断权限
	include "../inc/mysql.php";
	$id = $_POST["id"];
	$sql = "INSERT INTO course(title,description,`key`,starttime,endtime,userid,alwaysupdate,type,time) SELECT concat(title,' - 副本') as title,description,`key`,starttime,endtime,userid,alwaysupdate,type,NOW() as time FROM course WHERE id=".$id;

	$mysql->query($sql);
	$new_courseId = $mysql->insert_id();
	$sql = "INSERT INTO courseversion_rel_courseunitversion(courseunitversionids,courseunitgroupids,userid) SELECT courseunitversionids,courseunitgroupids,userid FROM courseversion_rel_courseunitversion WHERE courseid=".$id;
	$mysql->query($sql);
	$new_courseVersionId = $mysql->insert_id();
	
	$mysql->query("update courseversion_rel_courseunitversion set courseid='$new_courseId' where id=".$new_courseVersionId);
	
}else{
	die("What are you doing?");
}


?>