<!DOCTYPE html
     PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>商飞学习管理系统</title>

<style type="text/css" media="screen, projection">
/*<![CDATA[*/
@import "../css/default.css";
@import "../css/main.css";
/*]]>*/
</style>

<link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

<script type="text/javascript" src="../js/jquery.js"></script>
<script type="text/javascript" src="../js/function.js"></script>
<script type="text/javascript" src="../js/slides.min.jquery.js"></script>
<script type="text/javascript">
        (function ($) {
           try {
                var a = $.ui.mouse.prototype._mouseMove; 
                $.ui.mouse.prototype._mouseMove = function (b) { 
                b.button = 1; a.apply(this, [b]); 
                } 
            }catch(e) {}
        } (jQuery));
    </script>
 
<script type="text/javascript">
    $(function(){
       $(window).scroll(function(){
         $("#footer").css({"left":"0","bottom":"0"});
       });       
    });
</script>
</head>
<body>

<div class="div_Mask" id="div_Mask"></div>
<div class="div_wait" id="div_wait"><div class="indexfile" id="indexfile"></div><div style="text-align:center"><button>确定</button> <button>取消</button></div></div>

<div id="wrapper">

<?php 
session_start();
setcookie(session_name(),session_id(),time()+600,"/");
if(!(isset($_SESSION["userid"]) && $_SESSION["userid"]!=0)){
	header("Location:index.php");
	exit();
}

include "../inc/mysql.php";
include "../inc/function.php";
include "../inc/student_header_in_studentdir.php";

$id = $_GET["id"];
?>

<div class="clear">&nbsp;</div>
<div id="main"> <!-- start of #main wrapper for #content and #menu divs -->
<!--   Begin Of script Output   -->
<div id="content" class="maxcontent">

	<div id="content_with_menu">
		<div id="container">
			<div class="info">课程单元详情</div>
			<form action="upload.php?type=file" method="post" enctype="multipart/form-data" onsubmit="return checkFile()">
				请选择课程单元包：<input type="file" name="file" id="file" onchange="checkZip()">				
				<span id="isUnzip">是否解压：<label><input type="radio" name="unzip" value="1" checked>是</label> <label><input type="radio" name="unzip" value="0">否</label></span>
				<button type="submit">上传</button>
				<input type="hidden" name="id" value="<?php echo $id;?>">
				<?php
					$res = $mysql->query("select * from courseunitversion_rel_attachment where deleted=0 and courseunitid=".$id);
					if($mysql->num_rows($res)>0){
				?>
				<div><label><input type="radio" name="rewrite" value="1" checked>覆盖掉</label><select name="versionid">
				<?php
					
					while($arr = $mysql->fetch_array($res)){
						echo "<option value='$arr[id]'>".$arr["versionname"]."</option>";
					}
				?>
				</select><label><input type="radio" name="rewrite" value="0" >生成新的版本</label><span id="newVersion"><input type="text" name="versionname" id="versionname"><span class="required">*</span></span></div>
				<?php }?>
			</form>

			<div class="wrap">
				<table border="1" cellspacing="0" cellpadding="0" width="100%">
					<tr>
						<th>编号</th>
						<th>版本名称</th>
						<th>类型</th>
						<th>课程包大小</th>
						<th>是否已解压</th>
						<th>起始文件</th>
						<th>操作</th>
					</tr>
				<?php
					$i=1;
					$res = $mysql->query("select * from courseunitversion_rel_attachment where deleted=0 and courseunitid=".$id);
					while($arr = $mysql->fetch_array($res)){
						$attachmentArr = getAttachmentinfoById($arr["attachmentid"]);
						if($attachmentArr["size"]>1048576){
							$size = round($attachmentArr["size"]/1048576,2)."GB";
						}else if($attachmentArr["size"]>1024){
							$size = round($attachmentArr["size"]/1024,2)."MB";
						}else{
							$size = $attachmentArr["size"]."KB";
						}
						if($attachmentArr["type"]=="zip"){
							echo "<tr><td>".$i++."</td><td>".$arr["versionname"]."</td><td>".$attachmentArr["type"]."</td><td>".$size."</td><td>".($attachmentArr["unzip"]?"是":"否")."</td><td>".($attachmentArr["indexfile"]?substr($attachmentArr["indexfile"],strpos($attachmentArr["indexfile"],"//")+2):"<a href='javascript:;' title='点击设置' onclick='setIndexFile(\"".pathinfo($attachmentArr["path"],PATHINFO_DIRNAME)."/unzip/\",$attachmentArr[id])'>未设置</a>")."</td>";
							if($attachmentArr["indexfile"]){
								echo "<td><a href='".$attachmentArr["indexfile"]."' target='_blank'><img src='../img/look.gif' alt='查看' title='查看'></a>";
							}else{
								echo "<td><a href='javascript:;' onclick='alert(\"请先设置起始文件\");setIndexFile(\"".pathinfo($attachmentArr["path"],PATHINFO_DIRNAME)."/unzip/\",$attachmentArr[id])' target='_blank'><img src='../img/look.gif' alt='查看' title='查看'></a>";
							}
							echo "<a href='".$attachmentArr["path"]."'><img src='../img/down.gif' alt='下载' title='下载'></a><a href='delete.php?type=6&id=".$arr["id"]."&cid=$id'><img src='../img/delete.png' alt='删除' title='删除'></a></td></tr>";
						}else{
							echo "<tr><td>".$i++."</td><td>".$arr["versionname"]."</td><td>".$attachmentArr["type"]."</td><td>".$size."</td><td>不可用</td><td>不可用</td>";
							echo "<td><a href='".$attachmentArr["path"]."'><img src='../img/look.gif' alt='下载' title='下载'></a><a href='delete.php?type=6&id=".$arr["id"]."&cid=$id'><img src='../img/delete.png' alt='删除' title='删除'></a></td></tr>";
						}
					}
				?>
				</table>
			</div>
		</div>
	</div>
</div> <div class="clear">&nbsp;</div> <!-- 'clearing' div to make sure that footer stays below the main and right column sections -->
</div> <!-- end of #main" started at the end of banner.inc.php -->

<div class="push"></div>
</div> <!-- end of #wrapper section -->

<?php include "../inc/footer.php";?>
</body>
</html>