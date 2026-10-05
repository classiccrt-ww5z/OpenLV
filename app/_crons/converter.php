<?php
function random_string($c="0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ",$l=11){$s='';$cl=mb_strlen($c);for($i=0;$i<$l;$i++)$s.=$c[mt_rand(0,$cl-1)];return $s;}
define('BASE_DIR',__DIR__.'/../');
// CLI only, and the shared secret comes from the CONVERTER_KEY environment variable.
// This endpoint used to be reachable over HTTP with the hardcoded key "key".
require_once BASE_DIR."_includes/_cli_only.php";
if(php_sapi_name()==='cli'){foreach($argv as $a){if(strpos($a,'key=')===0)$supplied_key=substr($a,4);}}
if(!isset($supplied_key)||!converter_key_valid($supplied_key))exit("unauthorized");
require_once BASE_DIR."_includes/_classes/Database.class.php";
require_once BASE_DIR."_includes/_classes/ffmpeg.class.php";
require_once BASE_DIR."_includes/_classes/Video.class.php";
require_once BASE_DIR."_includes/_classes/Upload.class.php";

define("DB_HOST",getenv('DB_HOST')?:'localhost');
define("DB_DATABASE",getenv('DB_DATABASE')?:'db');
define("DB_USER",getenv('DB_USER')?:'db');
define("DB_PASSWORD",getenv('DB_PASSWORD')?:'db');
define("DB_CHARSET",getenv('DB_CHARSET')?:'utf8mb4');
$DB=new Database(true);
set_time_limit(0);error_reporting(E_ALL);
$S=function($c){return shell_exec($c);};
$G=function($a,$b)use(&$G){return$b?$G($b,$a%$b):$a;};
$LOG=function($m){file_put_contents(BASE_DIR."_crons/worker_errors.log",date("Y-m-d H:i:s")." ".$m."\n",FILE_APPEND);};
$DQ=function($u,$DB){$DB->modify("DELETE FROM converting WHERE url='".addslashes($u)."'");$DB->modify("UPDATE converting SET queue=GREATEST(0,queue-1)");};
try{
$R=$DB->execute("SELECT url,file FROM videos WHERE (aspect_ratio='' OR aspect_ratio IS NULL) AND status=2 LIMIT 20",true);
if($DB->RowNum>0){$u=$R["url"];$f=$R["file"];$p=BASE_DIR."usfi/v/$u.$f.mp4";
if(!file_exists($p)){$LOG("AR: missing file $p for $u");$DB->modify("UPDATE videos SET aspect_ratio='missing' WHERE url='".addslashes($u)."'");}
else{$j=json_decode($S("ffprobe -v error -select_streams v:0 -show_entries stream=width,height -of json \"$p\""),true);
if(!isset($j["streams"][0]["width"],$j["streams"][0]["height"])){$LOG("AR: ffprobe empty for $p ($u)");$DB->modify("UPDATE videos SET aspect_ratio='needs_fix' WHERE url='".addslashes($u)."'");}
else{$W=(int)$j["streams"][0]["width"];$H=(int)$j["streams"][0]["height"];
if($W<1||$H<1){$LOG("AR: invalid dim $W x $H for $p ($u)");$DB->modify("UPDATE videos SET aspect_ratio='invalid' WHERE url='".addslashes($u)."'");}
else{$d=$G($W,$H);$ar=($W/$d).":".($H/$d);$t="$p.tmp.mp4";$S("ffmpeg -i \"$p\" -c copy -aspect $ar \"$t\" -y 2>/dev/null");
if(!file_exists($t)||filesize($t)<500){$LOG("AR: ffmpeg rewrite failed for $p ($u)");$DB->modify("UPDATE videos SET aspect_ratio='rewrite_fail' WHERE url='".addslashes($u)."'");@unlink($t);}
else{@unlink($p);rename($t,$p);$DB->modify("UPDATE videos SET aspect_ratio='".addslashes($ar)."' WHERE url='".addslashes($u)."'");}}}}}
}catch(Exception $e){$LOG("AR exception: ".$e->getMessage());}
$Z=$DB->execute("SELECT url FROM converting WHERE convert_status='1' AND uploaded_on<(NOW()-INTERVAL 30 MINUTE)",true);
if($DB->RowNum>0){foreach($Z as $x){$u=$x;$DB->modify("DELETE FROM converting WHERE url='".addslashes($u)."'");$DB->modify("UPDATE converting SET queue=GREATEST(0,queue-1)");$DB->modify("UPDATE videos SET status='-2' WHERE url='".addslashes($u)."'");}}
$Q=$DB->execute("SELECT url FROM converting WHERE convert_status='0' ORDER BY uploaded_on ASC LIMIT 1",true);
if($DB->RowNum==0)die("nothing");
$u=$Q["url"];
$I=$DB->execute("SELECT users.partner,videos.status,videos.file FROM videos,users WHERE videos.url='".addslashes($u)."' AND users.username=videos.uploaded_by LIMIT 1",true);
if($DB->RowNum==0){$DB->modify("DELETE FROM converting WHERE url='".addslashes($u)."'");$DB->modify("UPDATE converting SET queue=GREATEST(0,queue-1)");die("gone");}
$p=$I["partner"];$st=$I["status"];$F=$I["file"];
$DB->modify("UPDATE converting SET convert_status=1 WHERE url='".addslashes($u)."'");
if($st!=2)$DB->modify("UPDATE videos SET status=1 WHERE url='".addslashes($u)."'");
$s=glob(BASE_DIR."usfi/conv/$u.*");
if(count($s)==0){$DB->modify("DELETE FROM converting WHERE url='".addslashes($u)."'");$DB->modify("UPDATE converting SET queue=GREATEST(0,queue-1)");$DB->modify("UPDATE videos SET status='-2' WHERE url='".addslashes($u)."'");die("no src");}
$s=$s[0];
$V=new ffmpeg();$V->Location=$s;$V->Get_Info();
$Lr=$V->Info->format->duration??0;$Ln=is_numeric($Lr)?floatval($Lr):0;
if($p==0&&$Ln>2100){(new Video($u,$DB))->delete();$DB->modify("DELETE FROM converting WHERE url='".addslashes($u)."'");die("long");}
if($p!=0&&$Ln>3600){(new Video($u,$DB))->delete();$DB->modify("DELETE FROM converting WHERE url='".addslashes($u)."'");die("long2");}
if($p==0){$V->Resize(480);$V->SampleRate=44100;$V->Framerate="30";$V->AudioBitrate="128k";$V->CRF=25;}
else{
$j2=json_decode($S("ffprobe -v error -select_streams v:0 -show_entries stream=height -of json ".escapeshellarg($V->Location)),true);
$height=(isset($j2['streams'][0]['height'])?(int)$j2['streams'][0]['height']:0);
$V->Resize(1080);
$V->SampleRate=44100;
$V->Framerate="30";
$V->AudioBitrate="128k";
$V->CRF=($height >= 720)?25:23;
$HDflag=($height >= 720)?1:0;
}
$V->Output=BASE_DIR."usfi/v/$u.mp4";
for($i=0;$i<3;$i++){@unlink($V->Output);if($V->Convert())break;}
if(!file_exists($V->Output)){@unlink($s);$DB->modify("UPDATE videos SET status='-2' WHERE url='".addslashes($u)."'");$DB->modify("DELETE FROM converting WHERE url='".addslashes($u)."'");$DB->modify("UPDATE converting SET queue=GREATEST(0,queue-1)");die("fail1");}
$NF="";while($NF==$F)$NF=random_string("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ-_",80);
if(isset($HDflag)&&$HDflag){rename($V->Output,BASE_DIR."usfi/v/$u.$NF.720.mp4");$H2=1;$V->Resize(480);$V->CRF=25;for($i=0;$i<3;$i++){@unlink($V->Output);if($V->Convert())break;}if(!file_exists($V->Output)){@unlink($s);$DB->modify("UPDATE videos SET status='-2' WHERE url='".addslashes($u)."'");$DB->modify("DELETE FROM converting WHERE url='".addslashes($u)."'");$DB->modify("UPDATE converting SET queue=GREATEST(0,queue-1)");die("fail2");}}else $H2=0;
if($st!=2&&!file_exists(BASE_DIR."usfi/thmp/$u.jpg")){$ts=$Ln<=6?0:mt_rand(0,(int)$Ln);$V->Make_Thumbnails($ts,$u);if(!file_exists(BASE_DIR."usfi/thmp/$u.jpg")){file_put_contents(BASE_DIR."usfi/error_logs/thumbnail_errors.log",date("Y-m-d H:i:s")." $u\n",FILE_APPEND);@unlink($s);$LOG("thumb fail $u");$DB->modify("UPDATE videos SET status='-2' WHERE url='".addslashes($u)."'");$DB->modify("DELETE FROM converting WHERE url='".addslashes($u)."'");$DB->modify("UPDATE converting SET queue=GREATEST(0,queue-1)");die("thumb");}}
@unlink($s);
if($st==2){foreach(glob(BASE_DIR."usfi/v/$u.$F.*") as $a)@unlink($a);}
$fin=BASE_DIR."usfi/v/$u.$NF.mp4";rename($V->Output,$fin);
$try=0;$j=null;
do{if($try)usleep(200000);$j=json_decode($S("ffprobe -v error -select_streams v:0 -show_entries stream=width,height -of json \"$fin\""),true);$try++;}while($try<6&&(!isset($j["streams"][0]["width"])||!isset($j["streams"][0]["height"])));
if(!isset($j["streams"][0]["width"],$j["streams"][0]["height"])){@unlink($fin);$LOG("ffprobe final fail $fin ($u)");$DB->modify("UPDATE videos SET status='-2' WHERE url='".addslashes($u)."'");$DB->modify("DELETE FROM converting WHERE url='".addslashes($u)."'");$DB->modify("UPDATE converting SET queue=GREATEST(0,queue-1)");die("ar1");}
$W=(int)$j["streams"][0]["width"];$H=(int)$j["streams"][0]["height"];
if($W<1||$H<1){@unlink($fin);$LOG("ffprobe bad dims $W x $H for $fin ($u)");$DB->modify("UPDATE videos SET status='-2' WHERE url='".addslashes($u)."'");$DB->modify("DELETE FROM converting WHERE url='".addslashes($u)."'");$DB->modify("UPDATE converting SET queue=GREATEST(0,queue-1)");die("ar2");}
$d=$G($W,$H);$ar=($W/$d).":".($H/$d);
$tmp="$fin.tmp.mp4";
$S("ffmpeg -i \"$fin\" -c copy -aspect $ar \"$tmp\" -y 2>/dev/null");
if(!file_exists($tmp)||filesize($tmp)<500){@unlink($fin);@unlink($tmp);$LOG("ffmpeg aspect copy failed $fin ($u)");$DB->modify("UPDATE videos SET status='-2' WHERE url='".addslashes($u)."'");$DB->modify("DELETE FROM converting WHERE url='".addslashes($u)."'");$DB->modify("UPDATE converting SET queue=GREATEST(0,queue-1)");die("ar3");}
@unlink($fin);rename($tmp,$fin);
$DB->modify("UPDATE videos SET status='2',hd='".intval($H2)."',length='".floatval($Ln)."',thumbs=1,file='".addslashes($NF)."',aspect_ratio='".addslashes($ar)."' WHERE url='".addslashes($u)."'");
$DB->modify("DELETE FROM converting WHERE url='".addslashes($u)."'");
$DB->modify("UPDATE converting SET queue=GREATEST(0,queue-1)");
echo"OK";
