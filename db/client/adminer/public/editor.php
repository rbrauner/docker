<?php
/** Adminer Editor - Compact database editor
* @link https://www.adminer.org/
* @author Jakub Vrana, https://www.vrana.cz/
* @copyright 2009 Jakub Vrana
* @license https://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
* @license https://www.gnu.org/licenses/gpl-2.0.html GNU General Public License, version 2 (one or other)
* @version 6.2.0
*/namespace
Adminer;const
VERSION="6.2.0";error_reporting(24575);set_error_handler(function($Ic,$Lc){return!!preg_match('~^Undefined (array key|offset|index)~',$Lc);},E_WARNING|E_NOTICE);$hd=!preg_match('~^(unsafe_raw)?$~',ini_get("filter.default"));if($hd||ini_get("filter.default_flags")){foreach(array('_GET','_POST','_COOKIE','_SERVER')as$W){$Gj=filter_input_array(constant("INPUT$W"),FILTER_UNSAFE_RAW);if($Gj)$$W=$Gj;}}$_COOKIE=array_filter($_COOKIE,'is_scalar');if(function_exists("mb_internal_encoding"))mb_internal_encoding("8bit");function
connection($g=null){return($g?:Db::$instance);}function
adminer(){return
Adminer::$instance;}function
driver(){return
Driver::$instance;}function
connect(){$Jb=adminer()->credentials();$F=Driver::connect($Jb[0],$Jb[1],$Jb[2]);return(is_object($F)?$F:null);}function
idf_unescape($r){if(!preg_match('~^[`\'"[]~',$r))return$r;$Xe=substr($r,-1);return
str_replace($Xe.$Xe,$Xe,substr($r,1,-1));}function
q($P){return
connection()->quote($P);}function
idx($va,$t,$i=null){return($va&&array_key_exists($t,$va)?$va[$t]:$i);}function
number($W){return
preg_replace('~[^0-9]+~','',$W);}function
int_type(){return'(tiny|small|medium|big)?int(eger|\d)?';}function
number_type(){return'(^('.int_type().'|decimal|numeric|number|real|(binary_|half_|scaled_)?float\d?|(binary_)?double( precision)?|(small)?money)$)';}function
text_type(){return'char|text'.(JUSH=="sql"?'|enum|set':'');}function
is_user_type($T){return
in_array($T,idx(driver()->structuredTypes(),lang(0),array()));}function
full_type_sql(array$k){$T=$k["type"];return(is_user_type($T)?idf_escape($T).substr($k["full_type"],strlen($T)):$k["full_type"]);}function
is_searchable(array$k,array$W){if(!isset($k["privileges"]["where"]))return
false;if(preg_match('~NULL$~',$W["op"]))return
true;$T=$k["type"];$Qh=$W["val"];$La='binary$|bytea|raw|image|bfile|^vector$'.(JUSH=="mssql"?'|^timestamp$':'|^bit').(JUSH=="oracle"?'|^blob|^long|rowid':'');if(preg_match("~$La~",$T))return
false;if(preg_match(number_type(),$T)){$z='-?\d+(\.\d+)?';$Lf='';if(preg_match('~IN$~',$W["op"])){$Qh=preg_replace('~^\s*\(\s*(.*?)\s*\)\s*$~s','\1',$Qh);$Lf="( *, *$z)*";}elseif($W["op"]=="BETWEEN")$Lf="\\s+AND\\s+$z";return(bool)preg_match('~^'.$z.$Lf.'$~i',$Qh);}if(preg_match('~^(small)?date|^timestamp~',$T))return(bool)preg_match('~^\d+-\d+-\d+~',$Qh);if(preg_match('~^time~',$T))return(bool)preg_match('~^\d+:\d+~',$Qh);if(preg_match('~^bool~',$T)||(JUSH=="mssql"&&$T=="bit"))return(bool)preg_match('~^(t|f|true|false|[01])$~i',$Qh);return
true;}function
remove_slashes(array$Y,$hd=false){$F=array();foreach($Y
as$t=>$W)$F[stripslashes($t)]=(is_array($W)?remove_slashes($W,$hd):($hd?$W:stripslashes($W)));return$F;}function
bracket_escape($r,$Ea=false){static$mj=array(':'=>':1',']'=>':2','['=>':3','"'=>':4','='=>':5');return
strtr($r,($Ea?array_flip($mj):$mj));}function
url_escape($P){static$mj=array();if(!$mj){$mj=array(' '=>'+');foreach(str_split("\"'<>#%&+=?".ini_get("arg_separator.input"))as$Va)$mj[$Va]=sprintf('%%%02X',ord($Va));for($p=0;$p<256;$p++){if($p<32||$p>126)$mj[chr($p)]=sprintf('%%%02X',$p);}}return
strtr((string)$P,$mj);}function
min_version($ak,$pf="",$g=null){$g=connection($g);$mi=$g->server_info;if($pf&&preg_match('~([\d.]+)-MariaDB~',$mi,$x)){$mi=$x[1];$ak=$pf;}return$ak&&version_compare($mi,$ak)>=0;}function
charset(Db$f){return(min_version("5.5.3",0,$f)?"utf8mb4":"utf8");}function
ini_set($lg,$X){return(function_exists('ini_set')?\ini_set($lg,$X):false);}function
ini_bool($ve){$W=ini_get($ve);return(preg_match('~^(on|true|yes)$~i',$W)||(int)$W);}function
ini_bytes($ve){$W=ini_get($ve);switch(strtolower(substr($W,-1))){case'g':$W=(int)$W*1024;case'm':$W=(int)$W*1024;case'k':$W=(int)$W*1024;}return$W;}function
max_input_vars($G,$xg){$tf=(int)ini_get("max_input_vars");return($tf?(int)floor(($tf-$xg)/$G):0);}function
max_input_vars_error(){$ve="max_input_vars";return
lang(1,"<b>$ve = ".ini_get($ve)."</b>");}function
sid(){static$F;if($F===null)$F=(SID&&!($_COOKIE&&ini_bool("session.use_cookies")));return$F;}function
set_password($Zj,$L,$U,$B){$_SESSION["pwds"][$Zj][$L][$U]=($_COOKIE["adminer_key"]&&is_string($B)?array(encrypt_string($B,$_COOKIE["adminer_key"])):$B);}function
get_password(){$F=get_session("pwds");if(is_array($F))$F=($_COOKIE["adminer_key"]?decrypt_string($F[0],$_COOKIE["adminer_key"]):false);return$F;}function
get_val($D,$k=0,$tb=null){$tb=connection($tb);$E=$tb->query($D);if(!is_object($E))return
false;$G=$E->fetch_row();return($G?$G[$k]:false);}function
get_vals($D,$d=0){$F=array();$E=connection()->query($D);if(is_object($E)){while($G=$E->fetch_row())$F[]=$G[$d];}return$F;}function
get_key_vals($D,$g=null,$pi=true){$g=connection($g);$F=array();$E=$g->query($D);if(is_object($E)){while($G=$E->fetch_row()){if($pi)$F[$G[0]]=$G[1];else$F[]=$G[0];}}return$F;}function
get_rows($D,$g=null,$j="<p class='error'>"){$tb=connection($g);$F=array();$E=$tb->query($D);if(is_object($E)){while($G=$E->fetch_assoc())$F[]=$G;}elseif(!$E&&!$g&&$j&&(defined('Adminer\PAGE_HEADER')||$j=="-- "))echo$j.adminer()->error()."\n";return$F;}function
unique_array($G,array$qe){foreach($qe
as$s){if(preg_match("~^(PRIMARY|UNIQUE)$~",$s["type"])&&!$s["partial"]&&!$s["invalid"]){$F=array();foreach($s["columns"]as$t){if(!isset($G[$t]))continue
2;$F[$t]=$G[$t];}return$F;}}}function
where_function($Fd,$d,array$k){if($Fd=="md5")return
driver()->md5($d,$k)?:$d;return(in_array($Fd,driver()->functions)||in_array($Fd,driver()->grouping)?apply_sql_function($Fd,$d):$d);}function
where(array$Z,array$l=array()){$F=array();foreach((array)$Z["where"]as$t=>$W){$t=bracket_escape($t,true);$d=idf_escape($t);$k=idx($l,$t,array());$cd=$k["type"];$Ge=$k&&(is_blob($k)||preg_match('~binary~',$cd));$F[]=$d.($Ge&&!is_utf8($W)?" = ".driver()->quoteBinary($W):(JUSH=="sql"&&$cd=="json"?" = CAST(".q($W)." AS JSON)":(JUSH=="pgsql"&&preg_match('~^jsonb?$~',$k["full_type"])?"::jsonb = ".q($W)."::jsonb":(JUSH=="pgsql"&&preg_match('~\.~',$cd)?"::text = ".q($W):(JUSH=="sql"&&is_numeric($W)&&preg_match('~\.~',$W)?" LIKE ".q($W):(JUSH=="mssql"&&strpos($cd,"datetime")===false?" LIKE ".q(preg_replace('~[_%[]~','[\0]',$W)):" = ".unconvert_field($k,q($W))))))));if(JUSH=="sql"&&preg_match('~char|text~',$cd)&&preg_match("~[^ -@]~",$W))$F[]="$d = ".q($W)." COLLATE ".charset(connection())."_bin";}foreach((array)$Z["null"]as$t)$F[]=idf_escape($t)." IS NULL";foreach((array)$Z["col"]as$p=>$hb){$W=idx($Z["val"],$p);$F[]=where_function(idx($Z["fun"],$p),idf_escape($hb),idx($l,$hb,array())).($W!==null?" = ".q($W):" IS NULL");}return
implode(" AND ",$F);}function
where_columns(array$l){$F=array();foreach((array)$_GET["null"]as$t)$F[$t]=true;foreach(array_keys((array)$_GET["where"])as$t)$F[bracket_escape($t,true)]=true;foreach((array)$_GET["col"]as$hb)$F[$hb]=true;return
array_intersect_key($F,$l);}function
where_check($W,array$l=array()){parse_str($W,$Xa);remove_slashes(array(&$Xa));return
where($Xa,$l);}function
where_link($p,$d,$X,$kg="="){$jg=($X!==null?$kg:"IS NULL");return"&where[$p][col]=".url_escape($d).($jg!=first(adminer()->operators())?"&where[$p][op]=".url_escape($jg):"")."&where[$p][val]=".url_escape($X);}function
convert_fields(array$e,array$l,array$J=array()){$F="";foreach($e
as$t=>$W){if($J&&!in_array(idf_escape($t),$J))continue;$wa=convert_field($l[$t]);if($wa)$F
.=", $wa AS ".idf_escape($t);}return$F;}function
cookie_path(){return
strtr(preg_replace('~\?.*~','',$_SERVER["REQUEST_URI"]),array(";"=>"%3B",","=>"%2C"));}function
cookie($y,$X,$df=2592000){header("Set-Cookie: $y=".rawurlencode($X).($df?"; expires=".gmdate("D, d M Y H:i:s",time()+$df)." GMT":"")."; path=".cookie_path().(HTTPS?"; secure":"").($y=="adminer_import"?"":"; HttpOnly")."; SameSite=lax",false);}function
php_error_text($j){return(ini_bool("html_errors")?html_entity_decode(strip_tags($j)):$j);}function
get_url($Nj,$Ab){$http_response_header=null;$Kc=array();set_error_handler(function($Ic,$j)use(&$Kc){$Kc[]=preg_replace('~^file_get_contents\([^)]*\)( \[[^]]*\])?:\s*~','',php_error_text($j));return
true;});$F=file_get_contents($Nj,false,$Ab);restore_error_handler();$Xd=(function_exists('http_get_last_response_headers')?http_get_last_response_headers():$http_response_header);return
array($F,(preg_match('~^HTTP/[\d.]+ (\d+)~',idx($Xd,0,''),$x)?$x[1]:''),(array)$Xd,($F===false?implode("\n",$Kc):''),);}function
json_decode_exact($Ke){$Ke=preg_replace('~"(\\\\u0001(?:[^"\\\\]|\\\\.)*+")|"(?:[^"\\\\]|\\\\.)*+"(*SKIP)(*FAIL)~','"\\\\u0001$1',$Ke);return
json_decode(preg_replace('~"(?:[^"\\\\]|\\\\.)*+"(*SKIP)(*FAIL)|-?\d[-+.\deE]*+~','"\\\\u0001$0"',$Ke));}function
json_scalar($W){return(is_string($W)&&substr($W,0,1)=="\1"?substr($W,1):$W);}function
json_encode_exact($W,$nd=0){return
preg_replace('~"\\\\u0001(-?\d[^"\\\\]*)"|(")\\\\u0001(\\\\u0001(?:[^"\\\\]|\\\\.)*+")|"(?:[^"\\\\]|\\\\.)*+"(*SKIP)(*FAIL)~','$1$2$3',json_encode($W,$nd));}function
get_settings($Eb){parse_str($_COOKIE[$Eb],$qi);return$qi;}function
get_setting($t,$Eb="adminer_settings",$i=null){return
idx(get_settings($Eb),$t,$i);}function
save_settings(array$qi,$Eb="adminer_settings"){$X=http_build_query($qi+get_settings($Eb));cookie($Eb,$X);$_COOKIE[$Eb]=$X;}function
restart_session(){if(!ini_bool("session.use_cookies")&&(!function_exists('session_status')||session_status()==PHP_SESSION_NONE))session_start();}function
stop_session($qd=false){$Pj=ini_bool("session.use_cookies");if(!$Pj||$qd){session_write_close();if($Pj&&ini_set("session.use_cookies",'0')===false)session_start();}}function&get_session($t){return$_SESSION[$t][DRIVER][SERVER][$_GET["username"]];}function
set_session($t,$W){$_SESSION[$t][DRIVER][SERVER][$_GET["username"]]=$W;}function
auth_url($Zj,$L,$U,$h=null){$Mj=remove_from_uri(implode("|",array_keys(SqlDriver::$drivers))."|username|ext|".($h!==null?"db|":"").($Zj=='mssql'||$Zj=='pgsql'?"":"ns|").session_name());preg_match('~([^?]*)\??(.*)~',$Mj,$x);return"$x[1]?".(sid()?SID."&":"").($_GET["ext"]?"ext=".url_escape($_GET["ext"])."&":"").($Zj!="server"||$L!=""?url_escape($Zj)."=".url_escape($L)."&":"")."username=".url_escape($U).($h!=""?"&db=".url_escape($h):"").($x[2]?"&$x[2]":"");}function
is_ajax(){return($_SERVER["HTTP_X_REQUESTED_WITH"]=="XMLHttpRequest");}function
redirect($lf,$Df=null){if($Df!==null){restart_session();$_SESSION["messages"][preg_replace('~^[^?]*~','',($lf!==null?$lf:$_SERVER["REQUEST_URI"]))][]=$Df;}if($lf!==null){if($lf=="")$lf=".";header("Location: $lf");exit;}}function
query_redirect($D,$lf,$Df,$wh=true,$Sc=true,$Yc=false,$bj=""){if($Sc){$Di=microtime(true);$Yc=!connection()->query($D);$bj=format_time($Di);}$Ai=($D?adminer()->messageQuery($D,$bj,$Yc):"");if($Yc){adminer()->error
.=adminer()->error().$Ai.script("messagesPrint();")."<br>";return
false;}if($wh)redirect($lf,$Df.$Ai);return
true;}class
Queries{static$queries=array();static$start=0;}function
remember_query($D){if(!Queries::$start)Queries::$start=microtime(true);Queries::$queries[]=(driver()->delimiter!=';'?$D:(preg_match('~;$~',$D)?"DELIMITER ;;\n$D;\nDELIMITER ":$D).";");}function
queries($D){remember_query($D);return
connection()->query($D);}function
apply_queries($D,array$S,$Mc='Adminer\table'){foreach($S
as$Q){if(!queries("$D ".$Mc($Q)))return
false;}return
true;}function
queries_redirect($lf,$Df,$wh){$ph=implode("\n",Queries::$queries);$bj=format_time(Queries::$start);return
query_redirect($ph,$lf,$Df,$wh,false,!$wh,$bj);}function
format_time($Di){return
lang(2,max(0,microtime(true)-$Di));}function
relative_uri($Mj=''){return
preg_replace_callback('~^[^?]*~',function($x){return
str_replace(":","%3A",$x[0]);},preg_replace('~^[^?]*/([^?]*)~','\1',($Mj?:$_SERVER["REQUEST_URI"])));}function
remove_from_uri($Cg=""){return
substr(preg_replace("~(?<=[?&])($Cg".(SID?"":"|".session_name()).")=[^&]*&~",'',relative_uri()."&"),0,-1);}function
get_files($y,$Tb=false){$dd=$_FILES[$y];if(!$dd)return
null;foreach($dd
as$t=>$W)$dd[$t]=(array)$W;$F=array();foreach($dd["error"]as$t=>$j){if($j)return$j;$m=$dd["name"][$t];$ij=$dd["tmp_name"][$t];$_b=file_get_contents($Tb&&preg_match('~\.gz$~',$m)?"compress.zlib://$ij":$ij);if($Tb){$Di=substr($_b,0,3);if(function_exists("iconv")&&preg_match("~^\xFE\xFF|^\xFF\xFE~",$Di))$_b=iconv("utf-16","utf-8",$_b);elseif($Di=="\xEF\xBB\xBF")$_b=substr($_b,3);}$F[]=array($m,$_b);}return$F;}function
get_file($t,$Tb=false,$Yb=""){$gd=get_files($t,$Tb);if(!is_array($gd))return$gd;$F='';foreach($gd
as$dd){$_b=$dd[1];$F
.=$_b;if($Yb)$F
.=(preg_match("($Yb\\s*\$)",$_b)?"":$Yb)."\n\n";}return$F;}function
upload_error($j){$yf=($j==UPLOAD_ERR_INI_SIZE?ini_get("upload_max_filesize"):0);return($j?lang(3).($yf?" ".lang(4,$yf):""):lang(5));}function
is_utf8($W){return(preg_match('~~u',$W)&&!preg_match('~[\0-\x8\xB\xC\xE-\x1F]~',$W));}function
utf8_length($W){return
strlen(preg_replace('~[\x80-\xBF]~','',$W));}function
format_number($W){preg_match('~^#+([^#0]+)(?:(#+)\1)?(#*0)$~u',lang(6),$x);$si=strlen($x[3]);$F=number_format($W,0,".","");$F=preg_replace('~\B(?=(\d{'.(strlen($x[2])?:$si).'})*\d{'.$si.'}$)~',$x[1],$F);return
strtr($F,preg_split('~~u',lang(7),-1,PREG_SPLIT_NO_EMPTY));}function
format_status(array$R,$t){$W=idx($R,$t,'?');if(!is_numeric($W))return
h($W);if($W<0)return'?';$ra=($t=="Rows"&&(JUSH=="sqlite"||$R["Engine"]==(JUSH=="pgsql"?"table":"InnoDB")));$W=in_array($t,array("Data_length","Index_length","Data_free"))?adminer()->formatSizeValue($W):format_number($W);return($ra?"~ ":"").$W;}function
friendly_url($W){return
preg_replace('~\W~i','-',$W);}function
table_status1($Q,$Zc=false){$F=table_status($Q,$Zc);return($F?reset($F):array("Name"=>$Q));}function
column_foreign_keys($Q){$F=array();foreach(adminer()->foreignKeys($Q)as$ud){foreach($ud["source"]as$W)$F[$W][]=$ud;}return$F;}function
fields_from_edit(){$F=array();foreach((array)$_POST["field_keys"]as$t=>$W){if($W!=""){$W=bracket_escape($W);$_POST["function"][$W]=$_POST["field_funs"][$t];$_POST["fields"][$W]=$_POST["field_vals"][$t];}}foreach((array)$_POST["fields"]as$t=>$W){$y=bracket_escape($t,true);$F[$y]=array("field"=>$y,"full_type"=>"","type"=>"","privileges"=>array("insert"=>1,"update"=>1,"where"=>1,"order"=>1),"null"=>true,"auto_increment"=>($y==driver()->primary),);}return$F;}function
dump_headers($ie,$Pf=false){$F=adminer()->dumpHeaders($ie,$Pf);$yg=$_POST["output"];if($yg!="text"||$F=="tar"){$pb=($yg!="text"&&$yg!="file"&&preg_match('~^[0-9a-z]+$~',$yg)?".$yg":"");header("Content-Disposition: attachment; filename=".adminer()->dumpFilename($ie).".$F$pb");}session_write_close();if(!ob_get_level())ob_start(null,4096);ob_flush();flush();return$F;}function
dump_csv(array$G){$xj=$_POST["format"]=="tsv";foreach($G
as$t=>$W){if(preg_match('~["\n]|^0[^.]|\.\d*0$|'.($xj?'\t':'[,;]|^$').'~',$W))$G[$t]='"'.str_replace('"','""',$W).'"';}echo
implode(($_POST["format"]=="csv"?",":($xj?"\t":";")),$G)."\r\n";}function
parse_csv($Mb,$K){$F=array();preg_match_all('~(?>"[^"]*"|[^"\r\n]+)+~',$Mb,$rf);foreach($rf[0]as$G){preg_match_all("~((?>\"[^\"]*\")+|[^$K]*)$K~",$G.$K,$sf);$F[]=$sf[1];}return$F;}function
csv_value($W){return(preg_match('~^".*"$~s',$W)?idf_unescape($W):$W);}function
apply_sql_function($n,$d){return($n?($n=="unixepoch"?"DATETIME($d, '$n')":($n=="count distinct"?"COUNT(DISTINCT ":strtoupper("$n("))."$d)"):$d);}function
get_temp_dir(){return
ini_get("upload_tmp_dir")?:sys_get_temp_dir();}function
file_open_lock($m){if(is_link($m))return;$zd=@fopen($m,"c+");if(!$zd)return;@chmod($m,0660);if(!flock($zd,LOCK_EX)){fclose($zd);return;}return$zd;}function
file_write_unlock($zd,$Pb){rewind($zd);fwrite($zd,$Pb);ftruncate($zd,strlen($Pb));file_unlock($zd);}function
file_unlock($zd){flock($zd,LOCK_UN);fclose($zd);}function
first(array$va){return
reset($va);}function
password_file($Hb){$m=get_temp_dir()."/adminer.key";if(!$Hb&&!file_exists($m))return'';$zd=file_open_lock($m);if(!$zd)return'';$F=stream_get_contents($zd);if(!$F){$F=rand_string();file_write_unlock($zd,$F);}else
file_unlock($zd);return$F;}function
rand_string(){return(function_exists('random_bytes')?bin2hex(random_bytes(16)):md5(uniqid(strval(mt_rand()),true)));}function
select_value($W,$w,array$k,$Zi,array$Qg=array()){if(is_array($W)){$F="";if(array_filter($W,'is_array')==array_values($W)){$Oe=array();foreach($W
as$V)$Oe+=array_fill_keys(array_keys($V),null);foreach(array_keys($Oe)as$Me)$F
.="<th>".h($Me);foreach($W
as$V){$F
.="<tr>";foreach(array_merge($Oe,$V)as$Vj)$F
.="<td>".select_value($Vj,$w,$k,$Zi,$Qg);}}else{foreach($W
as$Me=>$V)$F
.="<tr>".($W!=array_values($W)?"<th>".h($Me):"")."<td>".select_value($V,$w,$k,$Zi,$Qg);}return"<table>$F</table>";}if(!$w)$w=adminer()->selectLink($W,$k);if($w===null){if(is_mail($W))$w="mailto:$W";if(is_url($W))$w=$W;}$W=driver()->value($W,$k);$F=adminer()->editVal($W,$k);if($F!==null){if(!is_utf8($F))$F="\0";elseif($Zi!=""&&is_shortable($k))$F=shorten_utf8($F,max(0,+$Zi),"",$Qg);else$F=highlight_matches($F,$Qg);}return
adminer()->selectVal($F,$w,$k,$W);}function
is_blob(array$k){return
preg_match('~blob|bytea|raw|file'.(JUSH=="mssql"?'|binary|image':'').'~',$k["type"])&&!in_array($k["type"],idx(driver()->structuredTypes(),lang(0),array()));}function
is_identity_always(array$k){return$k["auto_increment"]&&(JUSH=="mssql"||$k["default"]=="GENERATED ALWAYS AS IDENTITY");}function
is_mail($_c){$xa='[-a-z0-9!#$%&\'*+/=?^_`{|}~]';$oc='[a-z0-9]([-a-z0-9]{0,61}[a-z0-9])';$Pg="$xa+(\\.$xa+)*@($oc?\\.)+$oc";return
is_string($_c)&&preg_match("(^$Pg(,\\s*$Pg)*\$)i",$_c);}function
is_url($P){$oc='[a-z0-9]([-a-z0-9]{0,61}[a-z0-9])';return
preg_match("~^((https?):)?//($oc?\\.)+$oc(:\\d+)?(/\\S*)?(\\?\\S*)?(#\\S*)?\$~iD",$P);}function
is_ipv6($ja){$o='[\da-f]{1,4}';$Fe='\d{1,3}(\.\d{1,3}){3}';return(bool)preg_match("~^(($o:){7}$o|($o:){6}$Fe|(($o:)*$o)?::(($o:)*($o|$Fe))?)$~iD",$ja);}function
is_shortable(array$k){return!preg_match('~'.number_type().'|date|time|year~',$k["type"]);}function
url_host($ee){return(strpos($ee,":")!==false?"[$ee]":$ee);}function
server_parts(array$Kg){return
array("scheme"=>(string)$Kg["scheme"],"host"=>(string)$Kg["host"],"port"=>(string)$Kg["port"],"socket"=>(string)$Kg["socket"],"path"=>(string)$Kg["path"],);}function
parse_server($L){if($L=="")return
server_parts(array());if($L[0]==":"&&!is_ipv6($L)){$Eh=substr($L,1);if(preg_match('~^\d+$~D',$Eh))return
server_parts(array("port"=>$Eh));return(preg_match('~^/[-\w.:/]*$~D',$Eh)?server_parts(array("socket"=>$Eh)):null);}$Oh="";if(preg_match('~^([-+.\w]+)://~',$L,$x)){$Oh=strtolower($x[1]);$L=substr($L,strlen($x[0]));}if(preg_match('~^\[(.+)](:(\d+))?(/[-\w./]*)?$~D',$L,$x))return(is_ipv6($x[1])?server_parts(array("scheme"=>$Oh,"host"=>$x[1],"port"=>$x[3],"path"=>$x[4])):null);if(is_ipv6($L))return
server_parts(array("scheme"=>$Oh,"host"=>$L));if(preg_match('~^(/[-\w./]*)(:(\d+))?$~D',$L,$x))return
server_parts(array("scheme"=>$Oh,"host"=>$x[1],"port"=>$x[3]));return(preg_match('~^([-\w.]*)(:(\d+))?(/[-\w./]*)?$~D',$L,$x)?server_parts(array("scheme"=>$Oh,"host"=>$x[1],"port"=>$x[3],"path"=>$x[4])):null);}function
count_rows($Q,array$Z,$He,array$o){$D=" FROM ".table($Q).($Z?" WHERE ".implode(" AND ",$Z):"");return($He&&(JUSH=="sql"||count($o)==1)?"SELECT COUNT(DISTINCT ".implode(", ",$o).")$D":"SELECT COUNT(*)".($He?" FROM (SELECT 1$D GROUP BY ".implode(", ",$o).") x":$D));}function
slow_query($D){$h=adminer()->database();$cj=adminer()->queryTimeout();$ui=driver()->slowQuery($D,$cj);$g=null;if(!$ui&&support("kill")){$g=connect();if($g&&($h==""||$g->select_db($h))){$Pe=number(get_val(connection_id(),0,$g));echo
script("const timeout = setTimeout(() => { ajax('".js_escape(ME)."script=kill', function () {}, 'kill=$Pe&token=".get_token()."'); }, 1000 * $cj);");}}ob_flush();flush();$F=@get_key_vals(($ui?:$D),$g,false);if($g){echo
script("clearTimeout(timeout);");ob_flush();flush();}return$F;}function
get_token(){$th=rand(1,1e6);return($th^$_SESSION["token"]).":$th";}function
verify_token(){list($jj,$th)=explode(":",$_POST["token"]);return($th^$_SESSION["token"])==$jj&&in_array($_SERVER["HTTP_SEC_FETCH_SITE"],array("","same-origin"));}function
compress_alphabet(){return
strtr(implode(range('"','~')),"'\\","!\n");}function
decompress_string($P,$ec=""){$pa=array_flip(str_split(compress_alphabet()));$u=strlen($P);$Yj=($u?13*($u-1)/2-$pa[$P[0]]:0);$La="";$Eh=0;$Fh=0;for($p=1;$p<$u;$p+=2){$Eh=($Eh<<13)+$pa[$P[$p]]*93+$pa[$P[$p+1]];$Fh+=13;while($Fh>=8&&$Yj>=8){$Fh-=8;$Yj-=8;$La
.=chr($Eh>>$Fh);$Eh&=(1<<$Fh)-1;}}if($La=="")return"";if($ec!=""&&function_exists('inflate_init'))return
inflate_add(inflate_init(ZLIB_ENCODING_RAW,array('dictionary'=>$ec)),$La,ZLIB_FINISH);return($ec==""&&function_exists('gzinflate')?gzinflate($La):inflate($La,$ec));}function
inflate($La,$ec=""){$af=array(3,4,5,6,7,8,9,10,11,13,15,17,19,23,27,31,35,43,51,59,67,83,99,115,131,163,195,227,258);$bf=array(0,0,0,0,0,0,0,0,1,1,1,1,2,2,2,2,3,3,3,3,4,4,4,4,5,5,5,5,0);$hc=array(1,2,3,4,5,7,9,13,17,25,33,49,65,97,129,193,257,385,513,769,1025,1537,2049,3073,4097,6145,8193,12289,16385,24577);$jc=array(0,0,0,0,1,1,2,2,3,3,4,4,5,5,6,6,7,7,8,8,9,9,10,10,11,11,12,12,13,13);$F=$ec;$C=0;do{$id=inflate_bits($La,$C,1);$T=inflate_bits($La,$C,2);if(!$T){$C=($C+7)&~7;$u=inflate_bits($La,$C,16);$C+=16;$F
.=substr($La,$C>>3,$u);$C+=$u<<3;}else{if($T==1){$jf=array_merge(array_fill(0,144,8),array_fill(0,112,9),array_fill(0,24,7),array_fill(0,8,8));$kc=array_fill(0,30,5);}else{$if=inflate_bits($La,$C,5)+257;$ic=inflate_bits($La,$C,5)+1;$og=array(16,17,18,0,8,7,9,6,10,5,11,4,12,3,13,2,14,1,15);$Gf=array_fill(0,19,0);$Ff=inflate_bits($La,$C,4)+4;for($p=0;$p<$Ff;$p++)$Gf[$og[$p]]=inflate_bits($La,$C,3);$Hf=inflate_table($Gf);$cf=array();while(count($cf)<$if+$ic){$Mi=inflate_symbol($La,$C,$Hf);if($Mi==16)$cf=array_merge($cf,array_fill(0,inflate_bits($La,$C,2)+3,end($cf)));elseif($Mi==17)$cf=array_merge($cf,array_fill(0,inflate_bits($La,$C,3)+3,0));elseif($Mi==18)$cf=array_merge($cf,array_fill(0,inflate_bits($La,$C,7)+11,0));else$cf[]=$Mi;}$jf=array_slice($cf,0,$if);$kc=array_slice($cf,$if);}$kf=inflate_table($jf);$mc=inflate_table($kc);while(($Mi=inflate_symbol($La,$C,$kf))!=256){if($Mi<256)$F
.=chr($Mi);else{$u=$af[$Mi-257]+inflate_bits($La,$C,$bf[$Mi-257]);$lc=inflate_symbol($La,$C,$mc);$eg=strlen($F)-$hc[$lc]-inflate_bits($La,$C,$jc[$lc]);for($p=0;$p<$u;$p++)$F
.=$F[$eg+$p];}}}}while(!$id);return($ec==""?$F:substr($F,strlen($ec)));}function
inflate_bits($La,&$C,$Gb){$F=0;for($p=0;$p<$Gb;$p++){$F+=((ord($La[$C>>3])>>($C&7))&1)<<$p;$C++;}return$F;}function
inflate_table(array$cf){$Q=array();$gb=0;for($Ma=1;$Ma<=max($cf);$Ma++){foreach($cf
as$Mi=>$u){if($u==$Ma){$Q[$Ma][$gb]=$Mi;$gb++;}}$gb<<=1;}return$Q;}function
inflate_symbol($La,&$C,array$Q){$gb=0;$Ma=0;do{$gb=($gb<<1)+inflate_bits($La,$C,1);$Ma++;}while(!isset($Q[$Ma][$gb]));return$Q[$Ma][$gb];}function
script($zi,$lj="\n"){return"<script".nonce().">$zi</script>$lj";}function
script_src($Nj,$Vb=false){return"<script src='".h($Nj)."'".nonce().($Vb?" defer":"")."></script>\n";}function
nonce(){return' nonce="'.get_nonce().'"';}function
on($Nc,$Od,$sa=null){$ua=array();foreach(array_slice(func_get_args(),2)as$W)$ua[]=json_encode($W,256);return" data-on$Nc='".str_replace(array('&','<',"'"),array('&amp;','&lt;','&#039;'),"$Od(".implode(", ",$ua).")")."'";}function
input_hidden($y,$X=""){return"<input type='hidden' name='".h($y)."' value='".h($X)."'>\n";}function
input_token(){return
input_hidden("token",get_token());}function
target_blank(){return' target="_blank" rel="noreferrer noopener"';}function
h($P){return
str_replace(array('&','<','"',"'","\0"),array('&amp;','&lt;','&quot;','&#039;','&#0;'),$P);}function
nl_br($P){return
str_replace("\n","<br>",$P);}function
checkbox($y,$X,$Za,$Se="",$c="",$db="",$Ue=""){$F="<input type='checkbox' name='$y' value='".h($X)."'".($Za?" checked":"").($Se==""&&$db?" class='$db'":"").($Ue?" aria-labelledby='$Ue'":"").$c.">";return($Se!=""?"<label".($db?" class='$db'":"").">$F".h($Se)."</label>":$F);}function
optionlist($_,$Xh=null,$Qj=false){$F="";foreach($_
as$Me=>$V){$ng=array($Me=>$V);if(is_array($V)){$F
.='<optgroup label="'.h($Me).'">';$ng=$V;}foreach($ng
as$t=>$W)$F
.='<option'.($Qj||is_string($t)?' value="'.h($t).'"':'').($Xh!==null&&($Qj||is_string($t)?(string)$t:$W)===$Xh?' selected':'').'>'.h($W);if(is_array($V))$F
.='</optgroup>';}return$F;}function
group_system(array$Tf,$Nh=false){$F=array();$Ni=array();foreach($Tf
as$y){if($Nh?driver()->isSystem(DB,$y):driver()->isSystem($y))$Ni[]=$y;else$F[]=$y;}if($Ni)$F[lang(8,'')]=$Ni;return$F;}function
html_select($y,array$_,$X="",$c="",$Ue=""){static$Se=0;$Te="";if(!$Ue&&substr($_[""],0,1)=="("){$Se++;$Ue="label-$Se";$Te="<option value='' id='$Ue'>".h($_[""]);unset($_[""]);}return"<select name='".h($y)."'".($Ue?" aria-labelledby='$Ue'":"")."$c>".$Te.optionlist($_,$X)."</select>";}function
html_radios($y,array$_,$X="",$K=""){$F="";foreach($_
as$t=>$W)$F
.="<label><input type='radio' name='".h($y)."' value='".h($t)."'".($t==$X?" checked":"").">".h($W)."</label>$K";return$F;}function
confirm($Df=""){return
on('click','confirmClick',$Df?:lang(9));}function
print_fieldset($q,$Ze,$dk=false){echo"<fieldset><legend>","<a href='#fieldset-$q' class='toggle'>$Ze</a>","</legend>","<div id='fieldset-$q'".($dk?"":" class='hidden'").">\n";}function
bold($Na,$db=""){return($Na?" class='active $db'":($db?" class='$db'":""));}function
js_escape($P){return
str_replace("<","\\x3C",addcslashes($P,"\r\n'\\"));}function
js_escape_re($P){return
addcslashes(preg_quote($P,"/"),"\r\n");}function
pagination_href($A){return
remove_from_uri("page|next").($A?"&page=$A".($_GET["next"]!=""?"&next=".url_escape($_GET["next"]):""):"");}function
pagination($A,$Nb){return" ".($A==$Nb?($A?"<b>".($A+1)."</b>":$A+1):'<a href="'.h(pagination_href($A)).'">'.($A+1)."</a>");}function
hidden_fields(array$kh,array$le=array(),$ch=''){$F=false;foreach($kh
as$t=>$W){if(!in_array($t,$le)){if(is_array($W))hidden_fields($W,array(),$t);else{$F=true;echo
input_hidden(($ch?$ch."[$t]":$t),$W);}}}return$F;}function
hidden_fields_get(){echo(sid()?input_hidden(session_name(),session_id()):''),($_GET["ext"]?input_hidden("ext",$_GET["ext"]):""),(isset($_GET[DRIVER])?input_hidden(DRIVER,SERVER):""),input_hidden("username",$_GET["username"]);}function
on_upload_progress(&$Lj){$Lj=(ini_bool("session.upload_progress.enabled")&&ini_get("session.upload_progress.name")?rand_string():"");return($Lj?on('submit','uploadProgress',ME."upload=$Lj",SESSION_NAME."=$Lj"):"");}function
file_input($c,$Eh=""){$uf="max_file_uploads";$vf=ini_get($uf);$yf="upload_max_filesize";$zf=ini_bytes($yf);$Zg=ini_bytes("post_max_size");if($Zg&&$Zg<$zf){$yf="post_max_size";$zf=$Zg;}$_f=ini_get($yf);return(ini_bool("file_uploads")?"<input type='file'$c".on('change','fileChange',(int)$vf,lang(10,"$uf = $vf"),$zf,lang(10,"$yf = $_f")).">$Eh":lang(11));}function
enum_input($T,$c,array$k,$X,$Cc=""){preg_match_all("~".driver()->enumLength."~",$k["length"],$rf);$ch=($k["type"]=="enum"?"val-":"");$Za=(is_array($X)?in_array("null",$X):$X===null);$F=($k["null"]&&$ch?"<label><input type='$T'$c value='null'".($Za?" checked":"")."><i>$Cc</i></label>":"");foreach($rf[0]as$W){$W=stripcslashes(idf_unescape($W));$Za=(is_array($X)?in_array($ch.$W,$X):$X===$W);$F
.=" <label><input type='$T'$c value='".h($ch.$W)."'".($Za?' checked':'').'>'.h(adminer()->editVal($W,$k)).'</label>';}return$F;}function
input(array$k,$X,$n,$Ca=false,$Kj=false){$y=h(bracket_escape($k["field"]));echo"<td class='function'>";$Hc=driver()->enumLength($k);if($Hc){$k["type"]="enum";$k["length"]=$Hc;}$_=($k["type"]=="enum"||$k["type"]=="set");if(is_array($X)&&!$n&&!$_)$n="json";$Ke=($n=="json"||preg_match('~^jsonb?$~',$k["full_type"]));if($Ke&&$X!=''&&(JUSH!="pgsql"||$k["type"]!="json")&&(is_array($X)||!$_POST["save"]))$X=(is_array($X)?json_encode($X,128|64|256):json_encode_exact(json_decode_exact($X),128|64|256));$Dh=($Kj&&is_identity_always($k));if($Dh&&!$_POST["save"])$n=null;$Gd=(isset($_GET["select"])||$Dh?array("orig"=>lang(12)):array())+adminer()->editFunctions($k);$c=" name='fields[$y]".($_?"[]":"")."'".($Ca?" autofocus":"");echo
driver()->unconvertFunction($k)." ";$Q=$_GET["edit"]?:$_GET["select"];if($k["type"]=="enum")echo
h($Gd[""])."<td>".adminer()->editInput($Q,$k,$c,$X);else{$Rd=(in_array($n,$Gd)||isset($Gd[$n]));$jd=0;foreach($Gd
as$t=>$W){if($t===""||!$W)break;$jd++;}echo(count($Gd)>1?"<select name='function[$y]'".on('change','functionChange').on_help_value('^SQL$').">".optionlist($Gd,$n===null||$Rd?$n:"")."</select>":h(reset($Gd)))."<td".($jd&&count($Gd)>1?on('input','skipOriginal',$jd):"").">";$xe=adminer()->editInput($Q,$k,$c,$X);if($xe!="")echo$xe;elseif(preg_match('~bool~',$k["type"]))echo"<input type='hidden'$c value='0'>"."<input type='checkbox'".(preg_match('~^(1|t|true|y|yes|on)$~i',$X)?" checked":"")."$c value='1'>";elseif($k["type"]=="set")echo
enum_input("checkbox",$c,$k,(is_string($X)?explode(",",$X):$X));elseif(is_blob($k)&&ini_bool("file_uploads"))echo"<input type='file' name='fields-$y'>";elseif($Ke)echo"<textarea$c cols='50' rows='12' class='jush-json'>\n".h($X).'</textarea>';elseif(($Yi=preg_match('~text|lob|memo~i',$k["type"]))||preg_match("~\n~",$X)){if($Yi&&JUSH!="sqlite")$c
.=" cols='50' rows='12'";else{$H=min(12,substr_count($X,"\n")+1);$c
.=" cols='30' rows='$H'";}echo"<textarea$c>\n".h($X).'</textarea>';}else{$_j=driver()->types();$yj=$_j[$k["type"]];$va=preg_match('~\[]~',$k["full_type"]);if($va)$Af=0;elseif(preg_match('~date|time|year~',$k["type"])){$bh=($k["length"]==""&&JUSH=="pgsql"?6:$k["length"]);$_d=(preg_match('~time~',$k["type"])&&preg_match('~^[1-9]\d*$~',$bh)?$bh+1:0);$Af=($yj?$yj+$_d:0);}elseif(!preg_match('~int|vector~',$k["type"])&&preg_match('~^(\d+)(,(\d+))?$~',$k["length"],$x))$Af=(preg_match("~binary~",$k["type"])?2:1)*$x[1]+($x[3]?1:0)+($x[2]&&!$k["unsigned"]?1:0);else$Af=($yj?$yj+($k["unsigned"]?0:1):0);echo"<input".((!$Rd||$n==="")&&preg_match('~^'.int_type().'$~',$k["type"])&&!$va?" type='number'":"")." value='".h($X)."'".($Af?" data-maxlength='$Af'":"").(preg_match('~char|binary~',$k["type"])&&$Af>20?" size='".($Af>99?60:40)."'":"")."$c>";}echo
adminer()->editHint($Q,$k,$X),(count($Gd)>1?script("fire(qs('select', qsl('td').previousSibling), 'change');",""):"");}}function
process_input(array$k){$r=bracket_escape($k["field"]);$n=idx($_POST["function"],$r);if($n=="orig")return(preg_match('~^CURRENT_TIMESTAMP~i',$k["on_update"])?idf_escape($k["field"]):false);if($n=="NULL")return"NULL";if(is_blob($k)&&ini_bool("file_uploads")){$dd=get_file("fields-$r");if(!is_string($dd))return
false;return
driver()->quoteBinary($dd);}$X=idx($_POST["fields"],$r);if($X===null)return
false;if($k["type"]=="enum"||driver()->enumLength($k)){$X=idx($X,0);if($X=="orig"||!$X)return
false;if($X=="null")return"NULL";$X=substr($X,4);}if($k["auto_increment"]&&$X=="")return
null;if($k["type"]=="set")$X=implode(",",(array)$X);if($n=="json"){$X=json_decode($X,true);if(!is_array($X))return
false;return$X;}return
adminer()->processInput($k,$X,$n);}function
search_tables(){$_GET["where"][0]["val"]=$_POST["query"];$ai="<ul>\n";foreach(table_status('',true)as$Q=>$R){$y=adminer()->tableName($R);if(isset($R["Engine"])&&$y!=""&&(!$_POST["tables"]||in_array($Q,$_POST["tables"]))){$E=connection()->query("SELECT".limit("1 FROM ".table($Q)," WHERE ".implode(" AND ",adminer()->selectSearchProcess(fields($Q),array(),$R)),1));if(!$E||$E->fetch_row()){$hh="<a href='".h(ME."select=".url_escape($Q)."&where[0][op]=".url_escape($_GET["where"][0]["op"])."&where[0][val]=".url_escape($_GET["where"][0]["val"]))."'>$y</a>";echo"$ai<li>".($E?$hh:"<p class='error'>$hh: ".adminer()->error())."\n";$ai="";}}}echo($ai?"<p class='message'>".lang(13):"</ul>")."\n";}function
on_help($Yi,$ri=0){return
on('mouseover','helpMouseover',$Yi,$ri).on('mouseout','helpMouseout');}function
on_help_value($yh="",$Ch=""){return
on('mouseover','helpValueMouseover',$yh,$Ch).on('mouseout','helpMouseout');}function
edit_form($Q,array$l,$G,$Kj,$j='',$D='',$bj=''){$Si=adminer()->tableName(table_status1($Q,true));page_header(($Kj?lang(14):lang(15)),$j,array("select"=>array($Q,$Si)),$Si);adminer()->editRowPrint($Q,$l,$G,$Kj,$D,$bj);if($G===false){echo"<p class='error'>".lang(16)."\n";return;}echo"<form action='' method='post' enctype='multipart/form-data' id='form'>\n";$zc=false;$jk=($Kj&&!isset($_GET["select"])?where_columns($l):array());$Bb=(count($jk)!=count($l));if(!$Bb)$jk=array();if(!$l)echo"<p class='error'>".lang(17)."\n";else{echo"<table class='layout nowrap'".on('keydown','editingKeydown').">\n";$Ca=!$_POST;foreach($l
as$y=>$k){echo"<tr".($jk[$y]?on('change','whereChange'):"")."><th>".adminer()->fieldName($k);$i=idx($_GET["set"],bracket_escape($y));if($i===null){$i=$k["default"];if($k["type"]=="bit"&&preg_match("~^b'([01]*)'\$~",$i,$_h))$i=$_h[1];if(JUSH=="sql"&&preg_match('~binary~',$k["type"]))$i=bin2hex($i);}$X=($G!==null?($k["type"]=="set"&&is_array($G[$y])?implode(",",$G[$y]):(is_bool($G[$y])?+$G[$y]:$G[$y])):(!$Kj&&$k["auto_increment"]?"":(isset($_GET["select"])?false:$i)));if(!$_POST["save"]&&is_string($X))$X=adminer()->editVal($X,$k);if(($Kj&&!isset($k["privileges"]["update"]))||$k["generated"])echo"<td class='function'><td>".select_value($X,'',$k,null);else{$zc=true;$n=($_POST["save"]?idx($_POST["function"],bracket_escape($y),""):($Kj&&preg_match('~^CURRENT_TIMESTAMP~i',$k["on_update"])?"now":($X===false?null:($X!==null?'':'NULL'))));if(!$_POST&&!$Kj&&$X==$k["default"]&&($k["default_sql"]||preg_match('~^[\w.]+\(~',$X)))$n="SQL";if(preg_match("~time~",$k["type"])&&preg_match('~^CURRENT_TIMESTAMP~i',$X)){$X="";$n=(in_array("now",adminer()->editFunctions($k))?"now":"current_timestamp");}if(preg_match("~date~",$k["type"])&&preg_match('~^CURRENT_DATE~i',$X)){$X="";$n=(in_array("now",adminer()->editFunctions($k))?"now":"current_date");}if($k["type"]=="uuid"&&$X=="uuid()"){$X="";$n="uuid";}if($Ca!==false)$Ca=($k["auto_increment"]||$n=="now"||$n=="uuid"?null:true);input($k,$X,$n,$Ca,$Kj);if($Ca)$Ca=false;}}if(!fields($Q)&&driver()->primary!="")echo"<tr>"."<th><input name='field_keys[]'".on('input','fieldChange').">"."<td class='function'>".html_select("field_funs[]",adminer()->editFunctions(array("null"=>isset($_GET["select"]))))."<td><input name='field_vals[]'>";echo"</table>\n";}echo"<p>\n";if($zc){echo"<input type='submit' value='".lang(18)."'>\n";if(!isset($_GET["select"])&&$Bb){$fc=($jk&&($j!=""||adminer()->error!="")?" disabled":"");echo"<input type='submit' name='insert' value='".($Kj?lang(19):lang(20))."' title='Ctrl+Shift+Enter'$fc".($Kj?on('click','ajaxForm',lang(21)):"").">\n";}}echo($Kj?"<input type='submit' name='delete' value='".lang(22)."'".confirm().">\n":"");if(isset($_GET["select"]))hidden_fields(array("check"=>(array)$_POST["check"],"clone"=>$_POST["clone"],"all"=>$_POST["all"]));echo
input_hidden("referer",(isset($_POST["referer"])?$_POST["referer"]:$_SERVER["HTTP_REFERER"])),input_hidden("save",1),input_token(),"</form>\n";}function
repeat_pattern($Pg,$u){return
str_repeat("$Pg{0,65535}",$u/65535)."$Pg{0,".($u%65535)."}";}function
shorten_utf8($P,$u=80,$Ji="",array$Qg=array()){if(!preg_match("(^(".repeat_pattern("[\t\r\n -\x{10FFFF}]",$u).")($)?)u",$P,$x))preg_match("(^(".repeat_pattern("[\t\r\n -~]",$u).")($)?)",$P,$x);$u=strlen(isset($x[2])?$x[1]:preg_replace('~\n[^\n]*\z~',"\n",$x[1]));return
highlight_matches($P,$Qg,$u).$Ji.(isset($x[2])?"":"<i>…</i>");}function
highlight_matches($P,array$Qg,$u=null){if($u===null)$u=strlen($P);$F="";$C=0;if($Qg&&@preg_match_all("((?|".implode("|",$Qg)."))su",$P,$rf,PREG_OFFSET_CAPTURE)){foreach($rf[0]as$x){list($Yi,$Di)=$x;if($Yi!=""&&$Di<$u){$Ec=min($Di+strlen($Yi),$u);$F
.=h(substr($P,$C,$Di-$C))."<mark>".h(substr($P,$Di,$Ec-$Di))."</mark>";$C=$Ec;}}}return$F.h(substr($P,$C,$u-$C));}function
icon($he,$y,$ge,$ej,$c=""){return"<button ".($y?"type='submit' name='$y'":"draggable='true' tabindex='-1'")." title='".h($ej)."' class='icon icon-$he".($y?"":" jsonly")."'$c><span>$ge</span></button>";}function
copy_icon(){$Fb=lang(23);return"<a href='' class='jsonly icon-copy' title='$Fb'><span>$Fb</span></a>";}if(isset($_GET["file"])){if($_SERVER["HTTP_IF_MODIFIED_SINCE"]){header("HTTP/1.1 304 Not Modified");exit;}header("Expires: ".gmdate("D, d M Y H:i:s",time()+365*24*60*60)." GMT");header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");header("Cache-Control: immutable");ini_set("zlib.output_compression",'1');if($_GET["file"]=="default.css"){header("Content-Type: text/css; charset=utf-8");echo
decompress_string(',c01di@ZK2N?gV#<@dr<@sp%6V"rsYO1=0nk@JwdV16^
TQ):*.T=S&whxcn$DR4J).$&ncsMgZ7(X{M*/M32Dzssbb4sGon+G^s<Ux*$iO>3T,Yy@R=tsFtUuX,zv?x8WSU1qJT7UB;<kbJcq6lc<IKZAuF}4}2[w.mYi<e^5u`{v,oP%.4WgU@K)nU8Zl5V3:g2;y"~m*m/QBf~.yl.3Fopr"wHXZmkmFLa&7nidLs0[A]!gN3pGPH.9:d|E>dqW+#Hf)/z)zk!v2/U!0(M){nWL,K#ZE@NwvNGiL=N"*rl#6mt^FDDj}Th0>NQbG#=&/+jtmf+">>hkp6#;.,j%,JS28F*>{dr@EHS/-K1J,"KU`5t$x3$+?8O`_)*E3c5i18D3?m`SQBPYWV_OT[}y[+VWWi:=pYchu_-`Ol[Q
D,_[vEH_,14qBWA!@x
~SBsjcGw?Y136fv+Mtk[-OBJI^OwM)h4h/q2UYr"[?sVumXGIr70"1$dq5ww~ao/%_}T`wR]tACLl/Y:qI|C7mqXQ`H`AOx:Mak_[S]-9]7"ScLq&)D/]P^+6kELS9=rxPyDHc<IiaO`D
kLi*aFvkI5c]jtlemN>nTSbg|hE(-`8*LFA,nSD/JmC!g@t
2V#fuX8p3d)j3rr4S2ssO?uk6HAh>@rw&(!qoDEFL4jD3M7^*p12k5TFQGti#toT6R5(l[L0nkyOxu1wjBX`{hYbVWfQnT#D^Ugua]YHE2/.1l!W0&yHwgpS^g5rK1jY+BIbHN/%F%
",2fr"k-],[i-6LtKW`x5[]vP2!G(97|t<;08sN]aL)I`!``+pOM[Uwo]iNYWdC))`sK(|n9[K&;-GVx37(^s_hrJQdv:BPZsu_8TAM.@>O=]glPvaL;*el]YesWo8/WCAR3&7[crqRY9|y"k%gR>u0R8<5j,J=equ5QgCW.Opu6#5vYFH;*=qyp0N5Em9"I<EGVgB.D?[WMHEXnqbWu>{u%!&!w3.uJ
+7Bt]Z`0p^PTnrk:Xx&AIYx<HG[>s@i,9:O)(F/xb88,m5PW3N)Ri-3l9;K-3ykS|TCfzQQNv:kjC<A*pk4em$S@*8r.[csnK,DCq#OF,TJ:gkbK-f`al/Bh~3Omb!r7jv6ExY}Vbey;A)m^[S?OSchC.>s0L:DR)g3fw:hb,]MEa
UAIgwevrZ?cr"dfoPF5hw!45,svaH(quqg9ZN9{j`QJ7
_C;Eby#Vw~!Zdyyx)Qc)5z].*:-wEP.6N0RVN&k/V5t8j
PT+xp.qcSudcXqF""c<zg@!2k`/TYOoyYw$QDTWk=&4zx,hDCC-J#L*{TnTZTvxn4$2peTTH@FPT$~^&^=fp%iV51v^KW@AN>)U/p_ONw,PW;f_R9,>PH[C-r^J1p$9=EF#p?FVojIE|T*fkG]sPO"ipGjA}LJ)w@|)>(;v#CjHmF;vN_{hk`|]sunTd#3Yrtm)SLC<EZr,H=VLa<e"A#K,6V{<yShGV3JZp_3W4P6q}r0jK/RHPa[VPik.Z,+`,>i?j61]pZk>ad"ArS7<1IFVCQ{yGbGNt0,D.l(o*c%8F<`uGGkv[I5J+45kk,|1^>*U-2xZy){6!;.wYkdqY[dR+H1garE@MK^-`Unc8ZdB5%x+4IvQmUF50u9nDCCP/oS1"^1]5g)KlX5RAa+`g#K8YO"f;Hd?MQ*&D"29e5=50!%H:qA%5NTe6^lUp_LSR14;gU(hF&hR/![F;"gg-IC59"("dB7Y,m}ep&/kQrSu25/q8K;fG3ug[V{:kq,<4DimX!,-{Wn6gki.+;2H[h|J?E/mFZ;N5AABwDl`JI`:K&msoVeCCrSWmKB$XGd
5)0xZ%K2MiZVGN@H(H;%@!YACyj$&P)?afD)|;.kql.8A/ueZ[pqu<;@HTAhNGy;z_?1Hd+o]-VhJXDE2I2[.dnwS-~cfJ0=X6%K4ZP)Q-,UDdsAjR8[W@qsL4p_$cyx}8B;."e7l=)Xp`ld"f};[4#fH0*j]dCxET"i*-|,5P*5`NY=,omVvs+
/-CIpE@aP7!+Odh4R!dh;Kh&:vzJm*ivuT5Jp:B:5"`GDQB5{c.?,3JTY[3pl/1dJH;GkF?COiYK$ZV9q(5]73}]QJT]_]@fj#&Yd3.+nH
^+gp[zMDO!pREqrb+fS-ZU5?h%E}!h&2_/xg"4)JG46?499^a#A0DC(zvi)+y^];Tslkx-+Qh(f"iM4
n,P`jF(7`9$|pF^g-Uh<T,X%^r0?CM]f]Mj!XXEA=w7r3|rm`;j"6&[}-Wc/KKL<N&$qr6v&S0bwH9nb.tXueRdnv|2k&fL5ey5"vuWB/Vd
r596G3Y2vAAFxY$M,.mnbB!RU2TLP4LL*14H4R+F_Tc$^bqyQG331vc__]xJ4bA@s+3?j%Y`pxd(9/a2@^1*AwTo@:fX:DNya,Z/68U~Ta)9"o:k4U9sZQ%4]fA.*R,:v4$#i%.]**@<]bHD5bXSW$HjZAGr/GH(:Bi.%TfPN0c9*G
Lv>,3+(
Ya_%KIJ<beI<viec];,?|mJM&lS8mEZBMeQ^6m$`-4pRugqgQ?|5DUASHD<PQ"=upgdV4)S=Tq`K@/|GCp:,Dk
4#1nd!u1).deA.])q,BWxaMKr>?nEJfaxl7yyCBYuwOvs<Xrp{fUjwBfHPL6C
L~DxsShbfsm_FMkM&Zfui&x}xKW^hkw@v]7OtO0+%ex@KTL6,jx>j:3,AEm#(fR^/9s(!h6yKZ_7+D^XU]p~;~J}h~Kyh-Hr,Cuq,L;KLah)B$oN7Wdoy%[lK*hj672]w^nw_t%JE:%,q*ury|t|j~(bqLpjxv,/J|;k`JKT"Ka;o9mA%?MP1pC(E0WTTR5*i=%?$"i7b0pfR&ho^RfgQ{na^fnEUy]-=YrvuWv$rZ(}Snl(ps@p`Zg>2B4pP8+mc](*b[(FLiK_N;^>OaIQ@hNg%gGwL~ah&VlFf:.#B~z(W<4u_,/3nvuhf8iJIs-5tQk]1}]cA~Zhbp4pIwqfjSS/21Xm
@I`_L*>DC
+Av2jK9vEgBA6^%UL,pBWt`x{V0tr;wdBozz(w`hOa"5hyF<&8`Mbx#KK_P
r`[)Ep*4fUs,K/ot_8]EUn#bcJ=]sRRQ)@p7*45NwvBNJ0~
E,?0|OeZ,&"TAp2Ps@%LK]
BKRYQOUcy
r6G0uo*>Qa<0P?oI[DIN%KB7+v@B$`lDIveQ&:8|%vHdf3TCqZF<I$q0h-CT0}.Mrz9W"SZY;cxR$$>7/=bZ_qrW_fK`!CQreZ35"RjPm$2719TF<6hO1qA&yi7n^;P1_{*V%`"r&O7{!AcugR4+g/+_6zb8"@cr7arGNeAyR1x^z"hPPBG_qY=#Fb1wfi5|)xi%!Yt>/$lgT!2);]Y65KDAKq#(4C<5?qkP4F:WO_^2*L%zt`s5Y%){,G+<_;?{CgI$-*:R9J^vL;8qj>Qsl7snFy;&a[%8?#S+9w,(1;Tx1Pp/
tJ:ih,NT90fv})02p
tjE]+sqhPS]bZp[ygY!');}elseif($_GET["file"]=="dark.css"){header("Content-Type: text/css; charset=utf-8");echo
decompress_string('$OsWO6/V>iQ0U*-2v8,"EPe+1oaU8]vYHI~JEs,`RS(%eB<
edzp?GDTrqJa&,byu%Qo
RN#YcDBt37%=UM2XW3[y=wB4+1CJ0}>9fg$)oQ&zfCBsZi?1Le0|$I;W)8ZpxIB-x49>c2QfWz0O?ibwDoF}E%C,vd(0OTjx`mgOd7
0n~F~X(pt
g
u>9U=`3(A+#!x.=4->HO(LL3srh&pDm-{G|_YWFZ1j0JnB*K*:V4_"S!V;@7XZRS]Z{RJB5@|EK0lxYGiH[kg.L<@>guR9Mf[]0>^,VYVG!P/I3Gar6UNSj)`gTl<J7]wP$4NXEa<3mfR^=y;!`Mlbk<(NB1;:K3lTebA]C!;%_hMb`J"uedgrN:`:Vy}sag{m6$gSAgRZ8q4GM+H]jWP6#+b8
LWk$:NrAi7/9uHn{^eWbAghGBcqwyuJ6T;!JrnY7:5&&oXaKL
C_1qn*?D?I-j7_3:NtD*S_`kD=P?5.
jCj_u
W31;PuL8.Nq)E-0?zkq"o&C-!tbvnwfeUKdk|<VJh]]LHfghXq{,fwGV#A/:FcA<SInA26IB|`7$/mra73~>Y+vSbS^MTYaknr_D!qi
2vdwG`p8(KA`kk#N:K@^lw&l3yYJ`[M;?kHntt=y)`S[@^{I=N[$1E]OmU4:<@sClBME2v-7D]h]m4tZS2BvV2%OHE7*MhVh*LO<.nSp#281Nca[Hu`b
Gw@g*psms
3Iy,I$3.aZghNUHYxPjbP:2_ZI(u*X<">GfZ:uUnR4Zb?2#k4K@AHt+
`6V@)$utkbDbFoO(GMq9Pz&",&"#V$tuDnZ|>zSmB3aZr]e)HMaNM{D74JlPZx`P+sdyt3Re3to1');}elseif($_GET["file"]=="functions.js"){header("Content-Type: text/javascript; charset=utf-8");echo
decompress_string('-X+G^iE+F1nx.g2lkSX2|;WvhD
*eGD2KotEhg
BKb7Yu!yGR*u98g25j9x+
cd[x-<(z>d7Z$t$nO6$9H:W7
{1f??h(j,1F
|AHT*w_;,`Fn3JA<[-:=,4!9a/Rwl["i_bqM-n+UFQtX(.E]AS*sJH[FF7x=2:
&]X|J8FZi~6SxlxY_T.E.`"%$uMO4V1[V{qiM=$jN%cfHJE^d^[!=(Da1hJe-4N2Te;fGO#1EA`~R4g?ufJXD{b6&/Z9C@DYn2SlB"9=qtJ1)AXqnua2o.@tDZb;sPvE3[ldG_S3HUhn=7fPe;sGOJ^(8RI_3>J;n=x1pp7_xIIyEFT.h*b>%Ba/a/rIlP`D1<a{"iC*6}[o4i)4tUNWOswseerzLF;i/84NJ$l:^O;4QABo-KCyhYT]k6Up-"w5vu%{_^NRsANG.pw!g;SBsj5,F_MJMh.b`spC_*vXRYh;Mhdf5FeIu!z)$MxTrq]BxaK;70S0ux3`7~RqyH.BX#$75_K@wxkcS@wam6w{:~X^KTO{0Nw`dg$(cld[+<r(?Ybe$ad!P9&ubI8x94o22{9<VakCpHa]p*2QK
oVv%2SClAVgO)~8edlS?$I+$bY7Y9|bvtL@._NZ(b+Tg,`91?Q`!]3,8.ocNQhpuhl40"kP^K_"@Kp9f))vpwC3YIY=tM1!SVJROWnS:0eR[TA;ttALv@qw@FsCYM/*:hqb-sW;JJ,Ltc:b!0ep[t$*]nu%m$}7`^/96kgTEWShaF~RG7q7/vAMg/lJ+@yf$AU:VOUu$Dhz"<MTs#SI[vgQ0"
25IzO+k=!^R-LZ?XuQxqD3:(nHpaItsK9wY*weC{][GmdhZaxSG~YCM)^i#p,4ZE0fJeuF40tbII.`n}+kR%Z?To$9`Xg,o8G,b"^4q=<:7uGw8L]e(;+#6(;QA>GOCOB:Dqe[AAg7h7].gJ<^#9PKI,q#<6T6$llM%Zb`;*Hq@.
Dpb83[BcR3Lm4+],MGX
y?F!#cNi-0H/
4HWHV#:QH>w*0cK4uF@"Zs!]05+i0p
Q.#DX`UxC^~$XcFIvr:wtAPoBGNsrs2B+97CP5rlB6Lv/CU9~05[sX!b?@U2KFO[TxaxB/v-tJNTv-K-Vt.x5NY-Df#(:,DCLUYBULt+|xu(FP&81s8PHD+_|kiI10C8^=X/i/",
MO]Q:~`{oEuLrMMT!|)[;4Hwi(7.x%"g"j<z/%JY*[9IJ(`:F?dtR;W34SB]x[senc<WlL`;<A#yWZ!NptG`Cj4AQY0/./Ki0H?a5-P<0f4:
2(%k6Y{Y/`]>^P!T4b"e@,LY+pU?ZPb-.`pT["kXma9Z)p0au,jt|.tbr=N2[i^jq,=^Or-KyE&7|Tj7=J(c1v+yEA~yOL^nzwU<u&mj$D+m&
"#;-|GHw)VH3&x0e`/)(&Li)|ls;FrMaj2=]1ML@*0*x#7j,6(3+a$JH*FNRNa4++2a>dM@ua5j^uAVcFA03qN>w5L|I]h-%Y>_W:P5QF=2p5MxVY9aH:63gnN@V_<h6CIGp4)Bud)^)pp6QW3n$1-;.@G+ZNQVN,
!RK3DWl;g0v6419o]Es6)8MLC?%+hcXm#E,OQ/QwA(c*vx<,`-Z4l`t]
y><PSxvQ;2^^JIv?#/Zj2&50@JiG>eOx(6jU@P17`1e?9A6TrJc=KKVy%1QDP>fSM!(a"Q;
d>/*c+HrCojKiXE37Wf|!NI+tQ*|=tI6(GkoDC%JJPHBJ-mzZU$xGa&SX(TDqbj}m4leNrEBIuBmaGbJ<uE[BDxU,]XoEjws:Z`$%GJW@}qpjlZ_OO?{u9cfmGVNs<5[s%"HpW)ou
idEArVTg9PEAB:MexCj}f7a$px[=7FGu@^SDB?1l@ZMyx-*t8@=YnYR_w5q
LB=t$?@/55b`x#*v7PG1)B^6nr6a<,RB.@nWFY*}o#&oo$L&X}B1Fctuq._<B#sr#ws_6fuY%GnxPQW@H5delk2"(hBek(<+%vW<"|56*C6KSD=^%
b+AP*3)1Smn^I/)eJK/n.x&
""B:<e>HB=_5%i>^iyBrgy7Gg}T`lcwy8KC@8$wQ/-*K"bayO>j<]SE8x<3~f?5[9)i9d6FeFtU&W~b/quk[7r..^WOtSNE
7L+:-2!1k~p|T]AAR(O>`/N(N&jZ<d$(<Bh]KwouO_$EDSI-cL/&V[0hGC]nGKLs^cCIA+nw4s%HN3M0Gh5hpogWmH(?+f]re$l"IasX)kDeI%p~udcFg7)VmXcTf.sx33RjR;DvLj/:KUqfZ>VsHf9w&%>B8Zer]&QF".J0Y~D#R"2KlxSy&QZ/BG$Hk#V[W]h#vY.LkO3_SXV#r
6RN^Z=sSBhY&8|CaGm+Qbph.(})`]/Gm$W%-Jn""J5%_9$Q1`<!n,mi2m[hJO6)G1?9Y(C(8mFIo"Oj%9Lki?1xzFxxXjGs1ieOk-NJY4e@i-beMeWTfe(`|[[Td::4.5YKU:M1&,`wIt!pBRNl#1`TvR"646-qk
@gNN_M^H[O=)3<b^(7qNq+gDfyrbAt<OqO-xrBawO+s!rAb"(jQ,;,@IW?#pJcm=>^*C_K}Oe5h3I%?:a
BN?1DWtOebvUQK6m_lQav21@w@mLrK(0D,?p>JHG~lDxx^tGG5Z#0]Hk@.?l*2Ob7opP-)PY<$WNl*n?e=8lnhdHD;L0(=1?=2<$=O:-<Z
dB$Q(9t]NGVK#qS)Xu/cZ#_Z8eh58%&^M4jg5`S#q?-[Vf?.pGbv8?g)BUuzeCwdqvV/y(eOx+=1j@5ICW-yj&Bvse"kW<>*f!H9#>Kooiw_nkU@!.k$C@PCR_CnSkiFcb8^ML[SdA7H^HefXP@:LT#vhU;sm-2I3M]JkJA9bc<fD2m>Y.QNW6R![0Y.19%g8@Lfk]8Pa#ARBVeooKvsUcI|wI`s,w"bj$ip+kLZX:+lPwM.59/iVr7VeDQ(iUN>O{Z;"WD.F%%#gs?9G[wC%aIu?A1_,[qQm9(]kkSCg6o(Vn%p0/c=,tn?s~TV@bv87|dF;,kw[cNwh1bpS>ecp8>N`>
fc6K
S1&ClZAe@6Tem{H8r4kG5)&iV[SYvgm*!?f4RqC8tg[,.C8p+zCZbQP:DYv<>i
c<LhAQxKHRefaWXimJhQP&*=kLj>%881fg4[EPL&e5{X(<}`f*eU_Z3c@a+sTyxMo.nMeEBJz/`*s;#nQPRb
OD%7ecqBKm"$-(;HJUJhv<V##n10VicGm*d*?=G1_FC*40Z2N7$p:rxPWMf-)rHz1,)~pA%l"jjJoD@b!VI%mXic+,P.]K"m7T0F3bK96QNUWmu.yU+_*+aH9;EQi?J1FA<
hDdn1!J@_(7O$T$O7n4b&Yr9*~6t^j6N,?@qcji)=3?Qz&KJ;.h.MF]#JY@kI?B+e_#w5FC#f/QTpB)sr>)+a1-@A^%,RjY{(#vcPBDNPQs*>pMgd(1!TD-Br~=*5jCQsN;~6EE6=p*aiCm!#if"Q".xu_1(?h[J]Wgi`j.VN0(K&Ufr$7QD/hwW=kgUy`Pn!S,SH=#K.9eNS,6#Qyd/NtO$=lefEV!lJm,80&LM4jm-)95xoG,2(u$91<7Y-#1b5_C`E`IG/e705!9l$t-en*0f835g
eHZv$l#`22[o`v`<B8,$MmWuVl.`h]2YK6,U`,vX
DofhE#[jgZ-Kjy4w[;u(?"b+O8$q:*+oWREFEy=gn|-#9~uSn(qu4V<]!w+
R=7#QJCWHwsK.gdiS*t:t=;K-ac&2{BEdA5[NRF,sDJ5stjrrapI8J&OcAK[%5c7&(&Jl"w%PBc/y0Pek{=sv64>G]9KSLL;;r6C%@#:6dLo9}0?etc-8u*9CE0|u}f8aCaQ_V,Rd;k-PZm8RSjxl;"cn^CnU;p`v5
Kw%qe^A2f[c3O`*]a.z"{4l.F70
cSo<WfMp84otV>5$ZoXR6I6Y**8+dF!xbyhQK5PThAG
6g
x>N~Ce!|a|c/D_HAu?OX%ZY%tfZuVLC24N.m-;?`f8r
RM0]G%+9"{Qq3}6h8qkIM!dY*A_~b7Vxs8!kob^/mAv[y?UE7"VEDSb`7?@xv5yTpKHIinD/*cr1BkhBw(,)&>,0MZP%x:t^%`hqRZ"vlb<]H+CA05^Q<^Huo(q3R**?1X/pVN.Oe^u=galu@G-f
PK](C,G>Wc=Q:95!$?4#juC3*Zt0YJIHp7YTa3/IT[g;rIZSg?4?T
NfVN]HtFUP)U3AQD0j/UfQX#FCEe,Hl[08H13=A:k+O,::dP>&o4bdi!QB^C&KEr,E@aPUfAol*
P?W`v"P_wVj@>$L%8CXU9f+,"01*a
e5<[)[O7eyr+fGZ)Xj!#.Wr-h/T3A?r[G_rtQF;D1A>I,4MqkcChWZ4+`0RmqrH#l_|$6UFhRH8fG$`hQc>"2UB/;p.M2tAe$+UW
+L1tS$4W6F-Ya;K^-K*ATJ`KFb5GZkkQ.q#*vA>Zy[:0sR3~@hsk92xGVKoxIP$<1dWdyo@uyc<kN6D-s-Fz0dvpfIK%E!wcG87GHDtp%UCE
JJFLCdew8eDxp[aCKHX"e*Y1+tCh*,/:?$kpmU&VEC~ee
/3UG+M9>D:&#[KelIloo,t#@|_F$d;O`fYG.{0@W3kV96.uOHg~WbZ1h#E35V*5?C;L6vU)?2fCep3z+GBJ22eo8lT+Z`T36([HHq#pp1<-=l0f?#w$)8+7P8<XXBJee:dao9[#ATrdZaA]$k_oCs(]CsysurFeOci`M}J
_R&LsCk!>f?tAaO?eSN]5/DAg{"J5j_@2qKBmh_kAZ5j7ta1idvc81&Bf4m{hG&Ld6U2_?qI)|mk0Lt~Pv2/-zkx[dnNZm<Np$bJGjd5Xj(EKPZc.S7G9o-`[?3q)-FKJD?8&ty$#Bq&A5;,i|WJcTo^s:!8K~_zL^ZIr?j[<k0:5zIdwWl6x8g"^5azo-g]&_FH8}St%[8IM-9^QZ7
8z!d+.fNuabFQmBI2qII=0
Eo)/U=/:k+Q9jBy(*6+]LLQywmJ+OAf
HP#(2p%nd1/ToUyy>DA=k"tDn+cMr[&0JlMt%1|ZOxmw.Xi(32UiY6{5rAVn$e?y@O`3t]q465=GKLaFiMCN7E+W<%;O5L-.H^=eod@]GvJRJX.-._-NSirk|WGd/6RGZa3C<K!4]aB
>rG?EU)uJ&"$Woh.p+HUH
3liJ9<SI_?Ira&sNlt+`4S,Q1VmdP8>lGH#RyaV>-1[cla=n+rHiUCij7^I/STXG&m57kEaV+F_?fn~
6Peu{^8#D,53pl@th<[o:0=ZXYhYPI>rlu9X7q~`Z=,uKL@KckF#bp*&.q[YYxXTEBE;/03M"NH?E%M^<6mNXK3HCv
XTcJGSv_fX^hZyDv0ZM"nH-x2lI(5YhE:Pn~P/DBHSmXkpYaLUwt&4QeB.+g,:m#IHJWe|SXWO2FgsHRFS3K!Q(6gA*1oRo$iWD^B"YkL>XGTw[eg5I{rXeLi^?acQj$(KRbIj+m98
(1=#-
6y~=%P%`m;qG21L+>54.M[a5Y;_`<Kw=1Aqy:;70LM~;ZDm;LNc8Xjre8h9`Hi`#uyJ#!k
K8ww-K&NccFPX2>)!KhQFzdl-O6S,n`v$"cJpjrK#{Gj*.BeMr6#=jWw+W52&::Ny"DEHf^2+>M7[gXWL9?bcGqoY=,_Z--%Es%gmcdh,RZzYTQ_(uyT99.q)j@E++FUl~]0!i5KQHBX.|VqVm<+MUG$TUi@`u#7wz1^WELVS7k,k"s].5<Q+?T-Urx>E!(HoWa)
bm&OpMChRQr0cwr^EphDC,wO@yFc|]c0x-IoUg~]y_z[~
F1Ws,:h<h-oy[;Vu{
2
U*6]a3ci1*g76U.HC-c?lb509_/`TO~THFLKU?fU]W+d`
}"$a7%x+)eobP4{Em(#]@MT84kU>qNQ,3em-hB<oDtBvY^>NbGY*jSAmjbm*An5&r14K6fZCy?gY]jH!ds:K6q!s&Kr;-AE9*8$wSu<@F;@X6x=+aQcS<Cx5GWm
Gh9XhMnX];nOp6QKiws!XHRSx`cKciha(0FhY!_>b0u1DXp+de(bD4+VQK_$zt)I(^*SMLzKt9*L.X|DX>M
2b.NQMu357Ify@<qA)(p&
7yKMyM+H#l@X}@?z)td+Y+gmnem<=&Atoes_iA6C?HUR@h.Z%7|:P)><2%h@U((T#Y*_WB;wRQ|=U>z@p)ncJ3},H+h$!mKG30$k"-(WGCuhlXrgQ+`rssUh_g8A&FVtOe-Bpeo]0Hs?V.8*t#G/PC$7G@PcM1|+aiF<1h,9-3xY|jbDDn#qOZs4Fp
nYw>r=TvSJD|s<@`J8R)<XatN,*`&qVlfxFCVQYiFV<oo|AbRuK:/*n$f{ghj{I}s4,,e0Sc>3GC-Q).p0J?xihiE64R,]rQI+<:Ft3@
i1@1hP^iQ2qUo]UMo#U6EyR.WAsMtbu-lekP#:VN_.^8^T/*GS0pl@b/K.
H$ONxHL><qf6t{Xq.
y75-DM"nZwCtq42:?f:
>[2X+)lt/
oef3mKr?N1BRUg];>c]T9h_a+DeZNhpwKD"q=A!8v&:ROE*Lg1=mJf6z128/v4JL2)e>Zpqq
^l8c:ojf
f&"6kPGA/3LvDaB(,Jg*jmS`-nJBMqex:$?@h#h9*N2oK2nfqdS@n7HQ97o6LRRa2]$`#aUg]?4tun7c,~A}W_KMw^DueXG8F7d5bp>xV_P{wkYt5-luZz5Etg[c:XaR(XxF0RMKR0in/~BE3a-V03OJ]kbM&#NnVS<DOc*4D=S*J9nCt;ya7Q[?Ul=37#$R
=`vPJ
V
oNDaP,IqD,V!xM0uyl:xLPZNed4Wg&dHVfTDlaEBc$V`<2iW^viDF.1&2MHasRtm``T!EJwt1X7Rh-EmF[Orsy/6+&umDvv45.z-weD^qytuQPJd&<,0#p&]*oK1;bQ
PD>&"UFGAh3gb9B6V=wdxC`Mw1e*<b
D=oU+u%%8*K(nImMN*`O,8%&Y5W{,{,#Fo7:qyVI"w_&g;`R^4fV$X8t,])z;wZ*worm<fV?,6i6[ms5rahiD5iSb[b*tsy{EM:SO2xf!~T=O.c@6:+abG&rwYLWc7jk_4y|,Ui(F+="JS0U$QN8t$G>>@/".hD0ny.=;";asIf%)Oam)/u6]vQ]a"xT_2MLQ9L.BNQs!8({a-)|..<.5MqJ5MmSO=megG$^,*5`V"v{a)^7/H#r];+2TDLFo2[T!%fF,N,DH{sjXSp!m0)aIc&
;ByID~U@bzmn<8t6d,qv`(xIM(cD7,+Dw<I[!VuFMM1Bci9%ULt?W~B1ASw,mB9!;n^?(9yi
Bdb!l[gjhD}"#kXt7sdd(');}elseif($_GET["file"]=="jush.js"){header("Content-Type: text/javascript; charset=utf-8");echo
decompress_string('');}elseif($_GET["file"]=="worker.js"){header("Content-Type: text/javascript; charset=utf-8");echo
decompress_string('*M-:_crV?&ivhwW00>hyk#FBR?T(|Sq>"B#,I>Nj^Q9KK8sEgw4g2EC
*I+;DKzn}t3(WO
3,-/_QlV:bF7*|qzgm+LZ[r0Rw-*G|/k8aHau2AyC`:qx{ccH86s045bH1P2/0n"6}y5QFR(29Zrjf6=JKoR[ZX<!#*8i<5q.ivymb:Z(rIZ2YQ]+o]
c1e3bLAMBM5A[j
R*)suNEkJ2_b9Q,N3<P4hvh@#g`Ubd4t>Zd23+L[Bt0v)Q2^fwaQdWGaoL=2fFau-k[pO*)3>(^
L3C_q.O7n@ic/h_PH^?3P(D2iqS^rMAuO_swO`)*TiGN,)~(n+#u:.`d2HKi,UCsH@.]A%*j08LgKJ|@kX+bbB8Zu(St;xKfJ=}=9(nou8]":5u&EO~jfR@9f.[9)!m!m>c&!6F6vOL1`]MawSXj)67Xp@ygy7)9*q,X#[h/L6mfb@W@"#ngdi7B#e$3RlPyr[q*H-nfIGG."G="QLE1bbI!fYOm7erh[>m4s7/_nwA');}elseif($_GET["file"]=="logo.svg"){header("Content-Type: image/svg+xml");echo
decompress_string('+s_V,6KY!i0q4Hy33F
w(7q6"=W$}v~
~?$RQC~?m;/*ps>Iv,j>Qa-"^N4;--|R{q(TBi4H%y6oh0+HJ[(976K[a>X*:e?/fG|
r`Vo:=h+p=>q]XmJ/$)[,eqtEm=d]G
JD7Vrf]6y36;y^2QxZi}]*y^5&U@o!Eaxu!>xz)~Wd_hUK06aOH^)[cpi{&>eZ2+j1*D87"p-6ND_Yv2(3A/b{b_3iTu>nV6DVJ.(z%kd(
@C[.,/CCc;`,M!%v1RY0P=g+vIwig2%=",PqCr1Wy,>$y3
@>/XdA01-W=!9:-,3#NJg.*E2ZOuYI3+;qr;hgVRCHQu$8RCCV>9F,:`ec0C,$o=p7_3^01K9-Q;d-$(@zNoNb.gMh*au@jd={],C[=yl9J&sV&8:Af0j>/PdN!bl03<XlUQ2V9Ki*R@``7_+N
3[.2EKy![)ZimBD)40cf//)m^e$1:3-.XUFf~2~Ry73HdOUXG(x^Z68hlH,Y}syJ8l,*V*3*dipUYp#chq~j,7,)$Dw7^Xnd!oSGyxF8{[70.tey7tD@6v{AUskhbs0&o=C2G
|m[`Z:G)%%YYs;?*H%bJWl^1<cj5u&]y|@wk;G)^5ZaW37Pw,aIW9X4
Ah@62ejtRf)GfxCebyiTt$!s#eHtX');}exit;}if(preg_match('~^/[-\w.]~',$_SERVER["HTTP_X_FORWARDED_PREFIX"]))$_SERVER["REQUEST_URI"]=$_SERVER["HTTP_X_FORWARDED_PREFIX"].$_SERVER["REQUEST_URI"];define('Adminer\HTTPS',($_SERVER["HTTPS"]&&strcasecmp($_SERVER["HTTPS"],"off"))||ini_bool("session.cookie_secure"));ini_set("session.use_trans_sid",'0');ini_set("arg_separator.output","&");define('Adminer\SESSION_NAME',session_name());if(isset($_GET["upload"])){$lh=null;if(!defined("SID")&&$_COOKIE[SESSION_NAME]!=""){session_start();$lh=$_SESSION[ini_get("session.upload_progress.prefix").$_GET["upload"]];}header("Content-Type: application/json; charset=utf-8");echo
json_encode(isset($lh["bytes_processed"])?array($lh["bytes_processed"],$lh["content_length"]):array());exit;}if(function_exists('session_status')?session_status()==PHP_SESSION_NONE:!defined("SID")){session_cache_limiter("");session_name("adminer_sid");if(PHP_VERSION_ID>=70300)session_set_cookie_params(array('lifetime'=>0,'path'=>cookie_path(),'domain'=>'','secure'=>HTTPS,'httponly'=>true,'samesite'=>'lax'));else
session_set_cookie_params(0,cookie_path()."; SameSite=lax","",HTTPS,true);session_start();}if(function_exists("get_magic_quotes_gpc")&&get_magic_quotes_gpc()){$_GET=remove_slashes($_GET,$hd);$_POST=remove_slashes($_POST,$hd);$_COOKIE=remove_slashes($_COOKIE,$hd);}if(function_exists("get_magic_quotes_runtime")&&get_magic_quotes_runtime())set_magic_quotes_runtime(false);if(function_exists('set_time_limit'))set_time_limit(0);ini_set("precision",PHP_VERSION_ID>=70100?-1:16);function
lang($r,$z=null){$ua=func_get_args();$ua[0]=Lang::$translations[$r]?:$r;return
call_user_func_array('Adminer\lang_format',$ua);}function
lang_format($oj,$z=null){if(is_array($oj)){$C=($z==1?0:(LANG=='cs'||LANG=='sk'?($z&&$z<5?1:2):(LANG=='fr'?(!$z?0:1):(LANG=='pl'?($z%10>1&&$z%10<5&&$z/10%10!=1?1:2):(LANG=='sl'?($z%100==1?0:($z%100==2?1:($z%100==3||$z%100==4?2:3))):(LANG=='lt'?($z%10==1&&$z%100!=11?0:($z%10>1&&$z/10%10!=1?1:2)):(LANG=='lv'?($z%10==1&&$z%100!=11?0:($z?1:2)):(LANG=='ro'?(!$z||($z%100>0&&$z%100<20)?1:2):(in_array(LANG,array('bs','hr','ru','sr','uk'))?($z%10==1&&$z%100!=11?0:($z%10>1&&$z%10<5&&$z/10%10!=1?1:2)):1)))))))));$oj=$oj[$C];}$oj=str_replace("'",'’',$oj);$ua=func_get_args();array_shift($ua);$wd=str_replace("%d","%s",$oj);if($wd!=$oj)$ua[0]=format_number($z);return
vsprintf($wd,$ua);}function
langs(){return
array('en'=>'English','id'=>'Bahasa Indonesia','ms'=>'Bahasa Melayu','bs'=>'Bosanski','ca'=>'Català','cs'=>'Čeština','da'=>'Dansk','de'=>'Deutsch','et'=>'Eesti','es'=>'Español','fr'=>'Français','gl'=>'Galego','hr'=>'Hrvatski','it'=>'Italiano','lv'=>'Latviešu','lt'=>'Lietuvių','ro'=>'Limba Română','hu'=>'Magyar','nl'=>'Nederlands','no'=>'Norsk','uz'=>'Oʻzbekcha','pl'=>'Polski','pt'=>'Português','pt-br'=>'Português (Brazil)','sk'=>'Slovenčina','sl'=>'Slovenski','fi'=>'Suomi','sv'=>'Svenska','vi'=>'Tiếng Việt','tr'=>'Türkçe','bg'=>'Български','el'=>'Ελληνικά','ru'=>'Русский','sr'=>'Српски','uk'=>'Українська','he'=>'עברית','ar'=>'العربية','fa'=>'فارسی','hi'=>'हिन्दी','bn'=>'বাংলা','ta'=>'த‌மிழ்','th'=>'ภาษาไทย','ka'=>'ქართული','ja'=>'日本語','zh'=>'简体中文','zh-tw'=>'繁體中文','ko'=>'한국어',);}function
switch_lang(){echo"<form action='' method='post'>\n<div id='lang'>","<label>".lang(24).": ".html_select("lang",langs(),LANG,on('change','formSubmit'))."</label>"," <input type='submit' value='".lang(25)."' class='hidden'>\n",input_token(),"</div>\n</form>\n";}if(isset($_POST["lang"])&&verify_token()){cookie("adminer_lang",$_POST["lang"]);$_SESSION["lang"]=$_POST["lang"];redirect(remove_from_uri());}$aa="en";if(idx(langs(),$_COOKIE["adminer_lang"])){cookie("adminer_lang",$_COOKIE["adminer_lang"]);$aa=$_COOKIE["adminer_lang"];}elseif(idx(langs(),$_SESSION["lang"]))$aa=$_SESSION["lang"];else{$da=array();preg_match_all('~([-a-z]+)(;q=([0-9.]+))?~',str_replace("_","-",strtolower($_SERVER["HTTP_ACCEPT_LANGUAGE"])),$rf,PREG_SET_ORDER);foreach($rf
as$x)$da[$x[1]]=(isset($x[3])?$x[3]:1);arsort($da);foreach($da
as$t=>$oh){if(idx(langs(),$t)){$aa=$t;break;}$t=preg_replace('~-.*~','',$t);if(!isset($da[$t])&&idx(langs(),$t)){$aa=$t;break;}}}define('Adminer\LANG',$aa);class
Lang{static$translations;}function
get_compressed($Ve){switch($Ve){case"en":return'"X.mXaM+.<-B)tk?T(E&A;6#g>JQiJpNnF94b.N-Hf_S.n8CavxX]TQ^%(
KC(r(k6ymISwj$/>.]MbfjM_q^Wz05$;[AenMXRo2k6;=hcJY4k/iBl4RGS9J<S(<9`1-N5qv.Q3u9
0Mv*K0rkOF!*rv;<U.Y**JWvG;;*-g{Z]x7Q].hwaGYPvBLh<([m%L161s84z0TScvaf_o;S8iVqa?SJXz&xFn7lXQ"J09q3uq!d`h;8?u*=@nl9b:i;M^:`-73A68dvjgx1F)4V/5wOH1X^^%](#)zoCqRtZkBa)W=(+`]AgLt0cIS%sTz1;25@dZ^@DE6x,pb`Rt}p,jB(Vp6l,GzPHR@Gs9FR)K0z(.qu=SwI4Xr440Bp7=XCsAg,nx{uh.V]6&pR$u&G!Sg5poXJanM4CDIn9>i>OLR)"WkgNSybdB?gAj1R=c1hr<$t_88YkHvAC.SaOsZ`=rqPowbjD5b2k&NZ<SH"ib^=#S`KG@j.>9KBHba)x2Ql&#WB%xF
}(T:
"-P}u<H3hNL:aK<2>d&Papdi.}5Y4:BN/wam8IFLM_SCRBKEWu-m,O!zFeAi1Aah:;+3;+^?]^v^/&T!FsGt90/g(:Fd]td3S0*y0I_NAPW*G:7$Efhj6r5PPgok<V;!ABZU_Sp=fVfWajU-r=ykFpO,y&)xpK^u7|!LoS)kPwI|JI
x.21JH}Y/,A/7<@oYL0]@s8WqYKu)e~Jx,7tIrn1GUWHRM9mbl=]
HV5cP22+&d01Xy;rt`U+iZIhcuPAqn!Vs]9^d.@I@p-zooC{9xHY1+Rm"hd6gP["!v./,kV5lC4m(n!M#evG3h=PoC!!s?E)O+8(D:pbL16=R[Va*x:o@O9~$^EN!;dx:jVX(i.a7h%
8w:yaga5sqFX8(VKa_6#gq&1m4JH6`uqB8aF)!"
8Ulb09g7NT)8AdlG&&a/sN%
JWo#tyAO,@m$<zD*nTF;4q$-d6>[(D(0Vao8"OnF/Mmpdv#88KN~G?jiAoDO:ca815;QY+_^7Q)?=}JB7`_N,)2(H4p%%A;>0b+dBhMfBm(7bZfi*]F`Yqsw@r4ksz?"@|gU7<]0)OT8&VlM1(jO_zW>yAmwxC,ad@2<uR
a,fdE!XK~L4qU<3xIw[78+7/TNw5I&Z5"a2E1W&98/-f*bI)cgHC!VBV=)R8qo0w12e=*A
8g&t9!0/C@+6^=1)L=!WF4xVn?lT`2uYD~ZdDn(V0RC..-1Zs;qX0n+r?1IpyKH0f{_WNLQY`1Sm`mUf%"TEt$CT(m1kJZ06H(6SuYLFu9LQy|1PTnX4bNE%ZI=]//Ih)(MXh_KKyh?=vza#nA9nkPG?UK]])glu54KK!K&t^/%`g)U_*P_[^tv4skVd1oe2<|rn
9%#UKOymCbW`%mUJqjRf=34.cT_HQffP|VfbUa?*e#"hFqcSnrLUho08XP>Zu@~pw4|dYK=iT<
F99HWKJ5>0eoA~i/kdrHQRh&2uqM).&+j"iKhX84,PdM@Ej[$my6!883SMpbU)C_XVeb!`d.eWc?u#[=4Fp)G+q4HtK&sQ[
IQ4,IEAjTw]QYYvu>2tv.&^@Us]m!x
|by
[F|Xa]k[36Gf"3Rl59UOf,o?w]|qp:A01P)D;_F<A3d*<CZh?u_(KawbcL9ROt^TxhCQ7B0f1wE:}%70VdhC}`3yiqiaA`<5V%`%xp.*V[Y*0[Dk!y9DUF[xHgUx}eJ1S>XisAUTQogo`*Q[4,piS#rkX_Ku#2Q!mf5i^';case"id":return'%Z}%HcvZ*h?o!Ojl,,MIww<0/9"C5C8EnDJqe6nrk
)@>.V3vxa7ntJ.K*RmGF"5skppPqaI53,crC>`y!%r^K^L9QFY
Bmmo]L6|L%U44rSn&bG.8ZC+uw&#^urDAKf0@=pPVULV^PUv!g*)*V#$3(XJ"=3["5M``*IZ0vgn(hE{D:Gjo(#31[u++Bx@%yni0gHpVn)SBE=M]mrV!DHAaI<$jr[BWN$rIZr@oE/q["Rc*RuWC=qU6<AVcK2{Yih!q_3lGjvd5PN1v,
bcjt
$G_@^btDQA78<SZUmCTmHN7_:=:CoN0zg(<nybe"2dPh31]Io+._0#L(Dns;3.wr401LmD[;?=y/[q@8Xon
nC:E:zY>`$xBMBG$S5Y^C>%uB(E=HgF{m;l5v3Y&>W:k/5vpmt26.xp0czM|D6N{iyQsMoR@*|e$>}=,2`_OoloWawl<3v)Lo,NA2SY!)1d&e=pa0tS{7@!=/2>LD]0smqGj#~]+Iu@y`.5&[lAX`#.Z/!m136.Bc+:cY="i/:Zz:WK,s`P(T*;=w66Hpb?gVPvQ":TW8uc6E,[s$}J0F"HaUxX;d$32t__=yLV!y((QI
yLwjpI!2xwBEU_qh
Wl{pTQ5o9
{8sTmK]+}stg(a,wunZsS&-+/u1x|RvZWoLxab6]MvM0cvds46>gmpABona),TDCiXv<=QCZn2}<YIX#"q.O]S6jTU45CFM"L,=CFk*l,)!+%$0wUyT#+WN!:l#:X:fQ?Bn?Odb6bbHU9hT]uHZl=sxH9_ruJ4iyQ&qM~1Ec<>Wro:}kFNW1P>g$m4yB
(=v#!`XUe.==s*(K>9f$!<Zw(R@J,[n-vet?wtk&RonNC3B])PZJ+Zt.X9m:y>j3`uZ5f~DwNKBY*Kiz)9-qa(gI8LyF;BC7I]Lycs7xVt&Xx{?4:63%$PI2kLb
y>mB,JvAsq"S`
.1RY[/]`OB?0*D6c%jA-5}o19@ECT{
l$nS%wcOay.pD<D;wt%H{9`YVS!0XLhhGbq(L+hP&
nCm=b@JckV$5rP+<q*(5}:#wx^F
zYo!4!]Z|S?"W
>atN7:Ma/tZ=%mVA8xYe1l>t]0Wi-:^x5].mh
CmD_on|mLMz`/&NN:7C[;O@CjdAg,4W3%u3GwS%#uKWSxw
=aiN*iF0tum_-E4+vu4k,8>9n79<,yfvGIn5s9.T]kl.>f,9S=+|5X5Mt]8>tE9#:<9;*offDTu<J.U26p;gF}=Dz#t"i/
<0{0,K-R"od*BXJ%S;V@F>?m.wUZskZT-%aHWwabOV1N[hO#5)4
RO1`x)-DbNXhxs(3w3Cm>9bli&SulMm[TV&pCY%o^<-4yAJKfW3X9X(m;w8!7U0!n"<;S5<A<4.i0`O#J4p`E)Zb+aL$WZ$lPFkhyJrH!e-4AxK0eh<]uct,9/VcpK_D*]~(.7acTC,t@k
DxcJTEP;xixNqGnnn$a<kzPJSlEL/AI2m9m&`qITu?g/
1s:m^r,f5t#)u+:[Vy,3FOpLY"7^U;=/_Z#$0Q{qmKXK|9/';case"ms":return'!Zu%@boWR&)qDxE&g.fAHRIY|-K+(%,O3)~a|r-][])?i;0&Vc|nE4`?RBV2d,UO-uiEKM:tG4RN"v-sqm_
u#/Z3#):nNvsT%<r15+8QCC74bNj-qA26`tHu:s0lQwH52;*E"o@Sy##VQ`u/Bl5R3|-v;2ZHY"C%iYTNq^S"ZZ=B4]dc?}m`.z=B3qtCebc>>tQNwlx[PGZm;yr1ORxXomj6J^auLE1z`:)T@?!x8A^mKoj3K`U!nR+.71
7_x)2igEu#=Xd`2o<[9Xd`nKPNY^RqzyV@0M?e_7
@$Dd&DGiyshk^.k:Pl@]Dy17s&!
5xCMWL,^gFd;
5!L/n"Kf-VFPkedFVfGw{#aDa5&pZCXe0>D9J/K!rx;F!:=;NO12o=a@mgy")NOf#Ef!rdM!+
P]iH@b^^1$f23[=yDsogw5dZ!VKJ
":fW/Mrb8p
VbzV!8b&WYyxAb6om)^liTUW;$88_Z$D:gM[#BHj{_)"iVEvZxtnb*~A7?L023l5~5M+{,l%;nw*f@0j^7J8paBoa>d?tZ?goAC=T[50yNZw*.poXLXt+f;-$tQg0&vWRt4#
Mg0)s|<p&JJb27"#fKK7-%x|l&!rdmVZ1a;|M{o,i&4qM5@uv>,[[cN$^`1!P`iL&1v(0"*J6=XJ;rcJGC3LOK)r+5+hWvib"SDe#DrL687EHn>|]l[lTI8v->#,/7[e1B#+;Lvty<w&W3[i^2!_*%tN#AA-wO;(E-IBu/U(4X2,Bbx=D|
FCi.]7fFn=_!r(Zt0km=gJXC07*Pwa9(e[|Y/HH!-rS647Fp.>m
jT"Cm8^dYBr.VR1IYj$aS"uC)^%Z64JMro2oY!D=pyD%D_s5j_|9OvUbx_dRv;m=WOLrRyP7~*?T6O!<<a/-fwIN?&ONGY{%Fo
wks<a;;N9mR1wy&7=|&2+[:jY4bKlT7FnDsTKpoHeH*k2NWNVH`#7k2w7
:n,R7a&A[rW%;|gl"}/mGy$LGW`a]LxxpG#ai3V;9xyj;qD?hM>_45f08IbA4%*Cax!zFiFMYpQ85p%.rTj/N.3?"(f2tAXTwV1eb="OLf(=TcZwgr3Rx57~r0!Q9)XHJ6t-!f9uBIYW
py(^,Bw7F,**[sl
5e%5R]I
HUlO~:p"/O79u*s-<`#?=F#oFt]$l"WfTVHM!c
LLo18{$]o^#e!8nJTm:nLu-KT<_dR?DM&7
Nb#6>hus/#uj0sfg-at9z2/U7uTxpnRm@p@
GZa6x!V45u1DPYsa+s@y3
;Q3?qM%OjUQy}<?Yef`)z%[40d/33Uf)9g}*(v7[oZ0rRXx<FfKIg0f@
[2.pU"S5k!V0]Hq
7+&:2Q&>wktc:?/8?Xh,U`EI*3v}h(ZG@wk]ZYW68CF`5_;{444BXQ?wne.v@_.C**N6!`70QRl{7okt!#5cX?HYdN[#:j=[ih&<IA!Cy1Dt$dlYHr9%]`nP*Sb`x}4Sf7#,w%`c^~pokmag+hQ<EB/^^,+~@al"r]K3;<qFS-)re)qPMds{^s.IG_QB$O*)srDSd%%k';case"bs":return'#Zu*!h".!%$%viY#,4-<BAhO@F@hY;sX6@Plc#1tEj&IU%s?Y-{"^YXHXyH,L!(v5b.t4$6(0`4tunME9=1wrjsOs[Wj3MEd19RL|WEl!c<2wgN.v`9OC&H>
otd%G%.jVbrpB];]JkO7RT2Pdbb-T7@hM"<7(m/d<]bK;tPJVBGf(F<Ql;6HO^h91m_ntyQ|R](tBmbVY]FM*ge+2TJ]hLB!0Me-o$AWYWeG]0PI>F-Ubjm]!Yb10ssP>l(pl[@WK:u9Lvj=O54+aP
+<_s:Wt$^8%o"j.eLs7h(@KnNJWf7So=)=SDg)}KyouG|uxv5BDr.($TIBqS&iSh&ws;<K~&[!/I847Q.Quvvn$b^FK!V*2I/%xi4"!c`-f_p_vmABUe,HI^?otw8IiT]%iDh-zav4.E"7@a$
J=sXF9@?aj:uaT$ThdTYrCc""]:8*M#@Am#$OP+$n>&Co;KvU"Y$fpaE,J(N-:;!L,SDZ#7%V"cf/k$+]dq?)q8+-7k"2Kn+}k}=+8t:rp5H]G$Oy(27f]/GmIsP^L[^0tbwO*j,;L9%jb?=*yQ,M
-%[Y8RfiC>cwjdfM
Ta(-aEPO-5LXHwHR>lE]t.wORG!%k]7G+^"[JP=.oL1,($pi>2<X]OX^-FJ/7Hh~3Xg
9_H1qz(k!T.NxX:yVIwyFD.n<s32";"
A^)>;8yH-^*;UPR(9E2+w9bIGr+0yt2n?gb}*b,!x_OB!0`(Ex$P6R$%t*=pYNI4hMfJ;sj]&c%[q~9aCx":QmQ/of_V>UI.NMrLI((*O&q0b@o.8
ptP/;C+=UZD6dHC(RLg*
8sL-^v?3N$*kQ]8?u?:+JXChL8Di.J*wR$r/cB
(DsEFY6@,47]i^mTrT
[EF@uAsWBi~mD_`t<=xAWJ#k}YtfqOL;XM
P&Py.?$itvU%Q2tZQg+!Q&g{O
mN-.SUn0ZE)}cm>Zp0lYb+`t#"c4Kx[hb
m#ri<!--.=qWFL9U"/c<EU&E?nH+`5Q&5n8od>BoNL*>6BJ}Hg
Z]
JM%)S,_5QV6h7ZYl@~Pt-_;sRJGl`|md*
Ezo!GltkRS?:ekmV:[KCF!a>31stMT1c"!ZfbL>m*LN(!v$oU@;,m0BjJPs[>[h<<zseR~#Xz%wm&<:(P+J}Oi-^8%Y_)p7C
#!j8WaW>:YO_GvtEV-igMylE9h(";NOn25LfVKv/Zj{9n)`4uZ9jTiRk2P~6;Ys
O:VIy.EVfI$S%aeD}ut$[kS>tQVdbfG7C^d4jo~P
:=Q>]e7h0:R;,W&hl:I"*Q6E!~.Y/O^Lk65A$LaeB;WWj
Q$`w:oD%EC<#<v0#?5qvv6.ZI`Af"&N#s&ippi<@
3w~WW8)@]Fp*}A"]>rXFe_j$]
q^:Mz-K$i*,_s,
J%p,VWZzbu9.5|@35A.iMmgn<SR}=U9o?y4P!#@lw~%{(JGo>&)8>Y5#MH?}H2qy7%c:1Pc_EHqQ5&p=4b3fp?O!).s:m==*#8cKb3/rP5W2@Hk+iWb%D>6C00
Z"fwXZlFUI":
VfR@Q]Ow0RB;)YuS.Ib(<A+[PD]yy8r3[ZqO4gF-*fY*1#7q0HBS$c=
a`r6rL<KLamzrPX*GUR$N)^!Q

<l|=u+Q.JF4
0)*gQFK:R>EN!Ly)Q(K5uNrr;kc_y:t;1
;ruq?o_FhA}2kB<n:.Zj[RBsH6)^QSgW-dm)U/d(BrWh|@6hSkrufq_i-g"P0$
q}_l$;Fd".:Av%:}(ABYtpUGKmsp.[^oM3b>VY_lK6/N#EZ=(9HTPp7eGRBz9$oIa.Y1';case"ca":return'!]^*gbT-d:%,otlA!c82"ig+?eL=UG5:.:lDXTZI#EWI|G`GWgk]^ovJfN~9P1?d1_1:i*Vx]Feb+(5oOQx5&]miUKyEPmSlC3@]k@TYv+9u!syP~B<GpNVlt*uH)s:r/tP)aqHg8C
1XJ-)YS.Q3raO$O*bva94DL+i"`_,Pc]O}-0]WmKPrZNoN.
w3boH.ZAXxu:stAlv$]k6QC$jPq(jV?I@o^oPJ#iO2K647)Iasti4BQ>!AGhjXERjQOI%)d*t]p!og.).5ml8MaVQ&Z;..fU)+BXe=bz[J]?Hu`&KU/5Ax64Jd>k$9)GEj-HOQ-sgABAPM7j7u
_hlB7F@rBy4
Z]y?$C>Msst.1oA?udXH@<(`P^,^Ns/lGF{L:licH3ewoh}7jq/6^-Rr:XDs`xU^%b@"[Y[j{jTP,pP3w8cRY9K?<37JcY<*w%OZ.Hw/@_VP~d6YsT8)RoTMHuG"r2f:=0)SU3&RZ-;Uz)]6B/,(>WfQba{+OfYTXm|?Idq!m%jU~.?rldh(d,52onot]I/HA1l?@]Ky7CD;H,}-w5DoJ)_
,cL9J(VsME8%c0V5LCfDf$sTaENM}Mrp7iI^]51O+7<tbF&u]n0jPVRxk/p#]>HyW#-0rPZsNA~)c?tS6rr!n:{-detxf%P/;VLFPu,`6[~"M=B7KY`P*/wBcZ@IC6E/^u$@-UMW=9s+
u+U&rguqg&jLB3NbVNUSYPdR!f4<![PrAgb)hcH-w19FcxhhiNP%dl*kQ03o%1vXP"Nq=z6benNx)7qo
k6@ZtQ)n>z(0P;GS&lJ]TC9%xmIR*i]Ai>?,,dG,@`xZP9qxD,lv$:"-T+*lrx2+pT=^D&~$<aikEe1/Bdfrng]I7$Qdq4
2gyF*zptt+,}1mpdL.]
"[.r-#w+j+X[%ca&Tg0gBJDnn.0^"VWP!<=,b9maPE662B$dZSDzU`x]hvr
kIU>C}H}w])*ta*x9yw>87w1Vj2q)pa9Z;C1237:98t2Vn^WY1*zSj!"HIFZg<Y1"Se@-85`YL6)Vq0%QFfQ2hH3@:7qUf:iy^6C$V8,q*om&POx1rI3)s4f]7g.x<k{veO{DArm<9LS)TT[?nL^;.uyjKye?2qsx5lYy(^_.I!/cW
7BHL31+9Yk]>TDx]Egf"{12)yt^cw#6/ibR?ow~u}5W&lGOtdq@:Ir.tjVB*mGiyuNQ#1.790x^RPro+u1dTIY,`(Kj/Y7f(#158ZR5ix%LIiV8oi$+_)-q!Jb`ajm..[74T*Jg%xZpW?/}
pf^PY3x0&Ek][:H8SofCiQMWtdyE32K]mHq)B?m2?nm4-!foen_H-88oCBo.=Z,G3k9kYma>N-sDCa4f5SqBy9,PqZegZLl
Y
mcjP)=6<ft&.^CL8&1m<i.{8%V*P/5JU+bDXSuBb
&>n(&[+Vqpb48^G8BSj5BD6,P[RtM`?|Z#*]f
(>mnHA$I0TFpjGXO+L67U{a]<G;<eh.y.`DTBs]=/^;Z>FgJPz;S55O!YyXQ.VaK[[hsWye@h0G8$<R|?&9zA@[]Y);LB(9;loi
R!<*d+(`-L0FVmaYPbsp-n>fY9<g%bUYqH/;[CwwXbT54[rB6}AWC)e0${#"Z.(:K8^nfGZdH[1B%>=PO
EO4*Re?$4`JVV<6~e>DAyYSMYiE2pk5N/=mEd2R];gJ<JFA;^SN/P]4Z
3f+!|>yhYIeDm_Y:BarPWwsJ1nn#$=wFu`07<BQ,K=gF3dXrJh1e!_Jvg3>uDb+u1lNv0x;3-?HOO
%A35z"B';case"cs":return'$]]qdcvZ+%fW8i`$&hx&o$0!ulAg3aCEC$g%E)XQQ`~"v>Q=
MC1A?(Pn4r*OgsUC![R"Bb4DSO:h;8gDbaT;7$$TR$D9@*Jhi.T]qfd[U!1#P;7+mE1%ccSS]cXwH>Zct5ol-eI~B1RhmSM}@46{a:eMIP9qx)fI$ghi?fmQ#X/Fyb-qmXsG
1^trY3-b]MH6;Fx>=*#LhK
n|3oR&.?,%Is6K[$j@BfHS$GV?5w$M$<-"^%HDsSCcASa(].=Z70jcQ`PhnYK-ToV+Q.?Sx/$c%R5<bH:cl0>DJ;j>6WqblVS<CMOU`LdO,y#wN>OT@@_,!+CbXQ%!?{EC
DIt49a>Y3D_v1%*3s=umMD]^rrDpKm/D1UL7{6Y
wtFH3J_]SI1IR0*dz.2AN7f#!WrY*W*=ZKd!bOovKjG4w7e:yJFF;Lpa4(=Y75V9JFGr!x?j#g-Cvd{yGp!do<DcoR`j?;^R:#0lRk]RJ9E;4ZGHOa96`lW7%hsX}6P=BUA1n=pWs.G@$m4pYqWw=v[",-s<F<ub@m^[6LU`5YT9V&dv1IsVi4M-n!n2H
k
qZ:ij2UP^NEm(SY+uf6$?DG4%e=fM@|LY6vfa?St{S$/yK,7:x!t|4N[*4]bWjd#jbfH4Y(,-9.$])e1/@$m!qwrG7CQE3qBi(Sh&@yZr#=q*q=$Br)@W"G>Hr9)B^eKA5~VRKM_ds_t,F+[sm)0W0|:<wJ-oB~j(-+Kb*=r24p_`sdP
yS".Els)RpGo!Ja729^&3%ik"=#H2m8jJ9%oXp#Hq:gQB<<T(7,bi-GbbT,tML_9Pr;K%9,_^CiKaKHVp3^`So`TOB(YFj%7YBI;
Xwe9?Z0wKIXOnnL<^+=G,/]A5&G<V*XTGIB8{:P0D:50k@;^do
KKwONi8OYQ)HO#5uV98,EJV{k=gy:dU7PVaWGv.8;UkeMV1rY+y.dC*/sU+Rq{*O8YWP,H`;kf@1n*j$IuOHu}3FK:$IC6mb_sY~8;Z/U;&Gl>-qy7c7NRtc4x;GjT/cQ#$=6"W.dhSwl!#EY^(:y^qcypVhv]5tU<?ui],G79eP!spcY[Rq"+th!?_[>LXgVM"6.XTZ*J.nLA,[3,NFqbP47`y0h@@a>9bT1E`~YYer^4sZyEhH+|N"7TCVLeSmL0im=yPX>"C|l[Wf"=E1(XwkGh@"]F#HBN+zje8"
*
_`Ui[PSX8MFG^f2&KDD^4<E8-`0d
6AHp5<&WpHr^GdtX"zF(A%3X:Jca#bw*03#=1LpG8E_0_z_-@J[:W/"S)|5;r$2kGeC^->]2Eu6O9j-D/JEgq*1wl5%-#m,C*d><NXs,V%U??v*`s28n:2GR2W`fn!%M/a4:T`Ysb~4+?A1g*TY[hw%aI%WMF}[MpC"2LS9]X:eq;4rE&lL/JzuHJzs6R+,C!_4"n-:unge,O-!dGtK]/.,IDlp^$JQ=b/NUe@0u;3)XN;)!iEfX]|LLuQ6H"I"#USb^:l/?p-##Ga9ZUjuO@2Nr>pRR
~@%&1@ZC62|hK0Q:*eM2<!pwJY;58KX9f#K/zGePd2^aA^rC>.j)$]*4@bFuM3K=INXp9Q`*$W~E|9O1D>i4pWY
r%Wu0c<pFh!?:6>p+%1p)2@P1%6Jy2jIa`m*)(h[<>7>;hb^g#kYPs)B}3u8.i-0Y7&L6gli(=4H>lt:L4n3uHKUpw!3e-2Z}$^sQ41DDL/<cegx"LvXsBC!a0UD)*i,D&"^0f1
D+8>/-aeSk}-(&"
YMo)MEl?!7S>4F=ZeO[soMq(mC)rm7d::Q7ndLh)ePKJYG:T`LGIsDc(O.414!f,h;gdFy,&9#HBK$!"?aCIeZ}YY63=Ds6KSKN:R,7*UZ#3/b^[e,EAo*[H.kL$w2B/"K:DDu0dhv!3k)d2&,fHA=CW`#6`]esB)gqyfuOC)D#=Rd<';case"da":return'"Z}*p6KWB:u^N?4:KsB8@[2>$Q[NJ>?3e@/Z
GB#vW%gw9ZF^_y,>s*<mRhn5JVnz;<@tU=lS4+.3"f)0Y
0;]27mIvJnp9"#j]^@<$NZP"cr_#`SnXd6]}_5;nl_18E1AP_~^Cj^FFDWV:yGR:]B2TDP/J@2Fp5f01E*20SKy$j?bXZ8P:Ul4N3[bj?,82Y&xCa~7Ay(k=+C3iHB/gcpNbAWP~[8G3c:p4[<K"f;1m#T_"&hGd7mZ)O3s5vSDBa^7oxr?|1-]^(HfHZS-XK2+jr#dRu$r)[2KSY{!!J2/9/dKMH,&MoWjy)s(&sqGUq2mt9Cl0ou0(^E$"azsh$L7U,{>r5Al!@=9>Qg3@c-tt]+B1YEnU]1M0R6dr*CmF"r6wKiUV3tN/cvKUUA*<+dn-#yNdt`K7ZNJ{Yc96DB_@K@yKi?c52N86j<>n9|qBW#;XBk6:9B6DsaUa6HY&QCxlHZLN/l;x92Sduk1T]8-eXjD;NnZnKH<jiO@-C,4(_rqNcX%=
iKeFJ#$naeh.e(JVJ,XU6^q2M0-GVB"->rgYUg6D~6Z26qAupE,w_%Ud_+X_fi/"^K/<cF
iyJl:>.&RJt.YVjteIBg?Hn;K
lWQlu*)<L((<]rv+:FI%7wPYo5E!^}W"@qMTql6P=VwZ]`UT$?:(L20cxjh/%jfd({P*K2$V3hW~_k^!t{xBQ"k4S`d1tw[u.S9nmLR#eLk$Kc/oxE`7DL&v<&
*I}u!,8w=1f^Dd*Wm`w*wy^Zf6^1CqkwWj2?ACrV,cF;|w2MS@6gw,8Du!F"`0WK7>r++R88>jN=r_n7(GJP19;:prgjA5RftE1m])h^bR4VeH_qft?feYTE`#pqgsa8-wOU)yzU
Hz(bd@([;{ia,ES>
pB33~(B.:M|Z!Q(+H,_>Sudl]tGFpffIfc&B)NcT>J]8H.aVB],uH?0iKMRR|R^
MDx6%DElFSX8iHGC<lUq^w!?,R+$pw&+N!hEwEn;%0PQ/tAEx<Zo]+B-,hc*VqKIzn$uy_B#HY@f?5JDG=.S+u22~^8H>[oS*4`ZMTReS^lMp
Op
M4W/-_j60yugT1L&<Jwe`S_(gY8LxN7:e?7#12
3vhX/1PT|dJ.8?GRw=_%[P|n+Q]!)ybI$Xfhr[.:GRxpGoIE:Dca);dyxERE-"s)#G4A+r}KGc"fJp_7VHuxsL8TSTdB%cr.9Y**Ut`(R;%%05Y@s^v&sNgw_2GZrP"LQY}8|Qh$n.5`8cGXI$o)b_Ik+XL4K/CtA9cH5D911=J:i)S+Ag7MOax/L;
/6hDpNKi]{&
w#D:Tt$iyX,v2E%.J+XM><J3E_wUR(V^Lew)XHdL7v)zfu%+Lw$D=+=bKCY(p,TCa=5Tb,=1Cc,j"WdJYSb8&r:IIQD1F*mLCG"6"VsNeY*.7G_%_x))h.eTrDGNU;a>16[!``dtB#7=.U[FCC&6NWqh9|nQS]kU$.Gz!H:tXWfZ-#[D4=``k%vfVtd?oborn-qgfXBS)-Bze,AF-_vbL(+rN#d0cg,y63-X,O#85M0yt.:waAK|crn_wabKQ{D$VyH78Mq-etI(z"bA;SlZSVB(Pc/2v0,LQiVf:{o<Fx!<ZuMKUiqN';case"de":return'-]^*!bSZ+;hPWl@#l_Y]c8v.O(t$#!4E2f;?e*H>8%e-uC}ipL{"$]=Lxvy
9n#1=Mu:).bx`qOS`e,dhAz$1W}L
qj5Yp<^nFw]Q
Z]DLLdfaOMcs7eW>BS`)3U?2sTa>[Vs`guA?2*I"=Ry,yVxoHf}hogfJ#[YX$d!0T?x2NIhg+MdM~AVUMdVr,7];jU`-njhtZ$F9Kh_*YWDZ@9}^SIvr8.$in=HDt2
(|2I95Tw=Nz(y*?NM<)9o?k=i5$.!K4?h[S>00j.+cvv91hT[|(Z?_d`uKY$IoE1-S<[0Uk_dIlu/vp:J%6Kj8o1Gm;4lwA[S@C*wrV:D|FE_X+X"]awfz<kn57F,#*
VrN:mMty79v*KJlLX
9LBzQ5ey>t2Ys+o!yipZ-/.f4OxelL;+m?j/55LOI8R>500d!=n=vHK<79-"9gkjoj.y.%S:?qAJ2NYtT$6E;]eW`j:TJ.-^f-VRv|"+$mEGG]Wr1|Ed?2v)F0P.#x-?c,r)kn1n)@n)(LBlYrxC=
^!5NpTR69F.mDT`<#*GFA1[>;R;4NjVRbya2+%=|.J8^e,]dsW
yCUSlNae<`RX/9`g5uJbr?<I(@Ivcv<=:i%I]@|kfd%YD$nY_sklu"TSmskrY-t2WB32.qn!-qTG$Rv5hTtFoBccsdaDhpc=s4tV)nR>5A?*38^dmb,.F_,Z.2qPT5(;Zi%A-rVGT[?JR`R(=SE
27~TC0l6eBkl{x0i2in1ZGIbw$:-*UF_[j^Eo3L1etk-K`]m)5.&c(4ph`|h=2A:,r5j#b"RX`ga5xK^Qwy1h498.T8ojE+W{=d)#%_Mz+FT{hEt+/ERlDmnv[UY-bink)xV|Q6Z8?SNM+No($vmcdMgYrpgw&XDhO:Kvlr!O"1SiCvlRO+r#aCI*chHyFA:Z*SCoKhsCC:1412M!ZTP6tW?f)24nqrTPn@f:-]J4;p/cO3q:hn.wh#Z%9#IW
%[ti</0Q%
L"qIJ3eDCAoTe1p2|EUP%)+-,@~405q$X@vz&=CxO/=P;%2dk<YU$phc3SRMA_:(cXWaC3%]gPjEo;(s@Ym<+[^UU[3I"1nH.s[<=eewRBS:{%5E[>JwBs5oyZc;LHT&$aWdp9jsvar>B9T+?Z6L:!)iRn5[N`9NXc)OIsJh$_/O5^HsRw4f@s*jl5S(WgPI_Q"(^nF_O/D8+#H3KQs4V-1PDqwtJ$h]LQtuk&#-:
ZEAo?6[07BKYk4S>Kdp&8(WPyVtve%Dk;2_t;UarjrRz!;nV*pc"yX.6e?tWpF$J@^lFX96eogw.a):r2j-,$a2V<@Ms%(kBFdDr=VE;L!mp[5,8LF;W"!s_;#$m3O=>_8=&GA2eg[{8d!)^XiX)IuX"O$
P_R?(Hq0:xNd[J-OFI?Oc4E.D6=<s1<pD6OFmmNuU/B|^"/`oeRO2LggO?eW`WQeZK?MBN(mj^$D7if3VR2RKPFFeny2F^gYQ8K0[<pavk-}2:MWL>+3Ee^b;|Wy1c17j?,aF2VO3xTrIGGb.|XOIN)V#sS/0ANuUH%|v-aydl`T1F,WU|Vr_J*}hWCn[qF8g&<!y+p"(c5|iPjD1;C(a2U&2(iT^zH%`a_I++fkA&D
-o7s8xJ3!k)AU]IYW7C,m,gKlA"~8?H;U#ZK3zJ.;>$kxN-(Gtt17{r;A~1m^D!d&Xu}_p9joKkJ[~kCtv$.tiHvWB(73bCLigm#[)8>:~*z)Sw[B%P"(-[OR$n2*Vsw7P]5fhMMB?3Qq~#~)Kef.
W+2u?"OV>)+-O[*?DVR)6d6o_:<[/p5<&7%Rk_@wwKbb)@-0K9jV_/0nfwk1le%=C)c
;?nz+-tXa4.Hf+b93$4QQHTV@__x]23@3J#):O"|`j<!3;030
oo./J~MxqW
[St-e7xyGN&';case"et":return'*sh*?bKY($uY")1em;*`"A!$hM`gq-}#yv%m(b+pfq6V5c1h;gD
]b@xxl0/(P:mP(9*{:r![9
>]*]bXEJiUwgY=lUF74Zy|@"Jrd;HK.X044-IVPP
vZ"J52N)N=TR;Z..!eRq>;8QkGb=P/!Vis%WB$L86N!
Z!Oe7^dG?In4o3EbF]YVDriHWN=+v=2X|jju9nVX21NCS3^ooXEMqin.x,}L<.HSDZ_n*+JoGms)+Nq;|st[UU@ucf%!gl:nm-G<tA=;@
ttQcSN3G"d%SzyVBYiYEWxF%.ADpSpi!oF*ots@sVM#(NVr?CJf.G;}.LL3h?t_j@
`Zb
9k
bKk&<WHVTUAzXrLcL$tyM0;>)v`;fZHZY*yoY6eKy0W@4o!/9/_N"V^sQ)%;orAmUDJu/,5{LFNTLQf9-[vKK`v+x8m`L0JWWB=}[{sC!%>-VH;1o-_M%EWHm~twx7r3V}5$J*@<HcP>z#x$BBtwbkCcKJAZ)b$,<.e?`**~U/R*$B6Ku_GR&LL<U_#tkw]KNm__W=&zg`U{1{#n79P-Q5^I2=N/f;,=o-uHIBjV$IxRR1^mpKCT`)CpLZb)quP,f[_8R/ve%=)za2`%;N^,cgGsd.Q@&oj&c}./@1H5y=Y41
&GCn&0np^=mC>ExHW4PIw`9kB"U%!WEI8JkaFB-Ql5ST-RsfjKSH?3EWJF9KG.P{OHGP>|J[*aq.U@kW8}4"dN(oOH0H5vgp@[rt&1O2:r^z6$"j?cSOF:#gFr2hA4Y|f"]*+BJ>@7aMN(h02n9COz2F=2/d2)y;S`7N!}RIH~BceLip896Z_b[r(.TS*8$PcVyZ0`VjAi15siY6+uiau<mplYGQcFkNLPB2Jy(#kI(^.h>dtY3v<z!BJ)IwTbEK2ydn3ha1,w>LhtLfWtI0E=3QtNFVj.MVf.Q#V*aRh4A=ggR-LpxxEt1~NKdJd-<J(os^0_F
S.X#&XL#HMSw(7e.;?t3435;';case"es":return'(]^*obQ-d:%^)xn;1bG%P#{G;#-+,e[Es8jp"1<c>-<DWEdpB9^Ah?r36)A
mvg$I5700p?tAWDMw"J[!g-u6`hmzL|L]fRz%Gb$KZotW^b^,y=@Rh;*aN!Tb^xjJi7Qn.+do1m#A!k98"y@47X-:fsQG!-CUr@qO^?RDU)P@uHd0`+W%F^%
iua,1m%--3_tsASMH=*b+t;Cnhczxq1"+Z2YYGTwx*L?nmW<jXFH)Y]Ryjtrfm-[CE":A~@4vMI#@fBqpzn{
/(VJX9
HSZl514?f%
lQ`Q,PTrE@4rp"C_7PtEKCf+H.jboSBinL
?QvwU!2sH}mcS@q;h<DBMg7VV5(8+&Tey"mPNAo7EsRV,9F@BGQ(CH&&:-<kQ!i~0u&)YDJA59!4qLKW*alCUT75OjO?]5&ZK2=eYcaBK,`iLzn.N3(r6b7ubU`^LQZQ*Koj32+]+]5CuI[Ew{PUQ`M_M(YHS2:/-](&*$CPN5#F&d;SEbYqd-bl4Ym3bH,P0-_PyW5}pNpPn/:k-
$GTJhW4.%Jg>BsV}v5dcU=W#>?PvN3R@#:!2wVp_h|atrkm<[Z+?/}lpq`*`h^brl0Xm"eP{2@H7xaF4@VNX/zub2ru|Oj1O"}6km>5V%-I.maOZ""G1:Oj1s_mSSj"Qu|Ayi;SqS3/5d#Hv2bue-JfmDDImXb=gZaQcw4=*"AZslOmsrmH[W|h=k~o-y>#PN=/-_6DT??FA?!a
c!But,c3Ua&=->O--v8R]:Z3(.RR-~*`r86O?&tF3YgC&d+vhT)WTUf2I^pNu&.$D1S$?r8|v@^qH_bRaK@F;&8XLPU7sEvS2"#Q[d"RUicJX,j[_wIvv,kf^L-+u1w!E7b($r=8"NN3#ksW<|SMY)]LmdOuu]t,OPoHGSkPn$ab_L(z8k%3!s<Sp
Gq8P"bR36UQ+CS*ayaE~.#dF0/qRc8bnh3x,vIY<!=wDshKXVMe"x2@/S=hL17V*&dBP%FCPG]0a_*d2Wq1M[;w:"iCY,y+0kI^^M]d2g/Bw=|+{M>,hie.f+2U:[aNEc7Y6qBZqsw+S6M<l:>0ajQH+GCRS0_@0ySz)^aa@tMSu=D7Xqsot;^],8"oh`T%gNw]cPzFj."cW1c*LVI.^+:kWN,rwtZu<`eg+%)4>2ba2@/=3B%`?/WP|cJMy0+Y$"KO]jZ0FkSn6aSI8fgxT!Y/,dCIVZVjxkDlLhYdx[x,-
:afOZI*I:v&kn&Pa1GqrqLad`@xpY
&&?Y`)6RB-6AxGeVBh#;@/$B
l]G[M+=A=pYt<|
OR#+L?UQ*x?&Im7?
U8%N-{R8K~(uWvtmKOVJ*hYSD*RiQ_:tFz58XU1,9+a/>%,RJuDIdQUh"n1|h-4hmyTuLe#=,CU$*L?j
f>oIk#Y^?Sr,sL]C9D8,kl1rU%A^qo2<h*=+3,[Knnp2Z)1CzTg1
hNY5dpfkH$^
Tn]GZ;%M)_7ZCva6!sCHsWlEX,TdP~Bv?od0#/bp)`F$%|f}-XRe]jJ7aM].]kd}#ml(c6^30-kmMn)=Cvk)NQ3lOU*w(
pI_<>+U7??1?edb-rckM9}Tl0e`gmmWZ/.Sqf)
JRFxPxhM"?F5NZjb+4;ivISaA+"0x=He-`6Ty<%jO/G)=7xRv"Le6(_W
=D+S]0l_9YLFwe6T!XGp:LW36"y`ScWJ5?C#](FL7gOWHW0n@G:C+/#{*REUA|O^N>3U,QQw4t*q%|4Y?]I5&Zn~-WU4Pikx2zaeX/aA;{]e0HM$(;h^k>?bdPBr1o';case"fr":return'&Zu/_6kWR%fA6s?&|SV]&bR"hX<$itz9:;/.9HV)5P&ULtst`Ai7CZo<og&09h(S{<]B|=Q%6ujPU;Tf~.
d,&[/{:8iN^9i&cY/kcfFy>!3KfGPH[vK)mgASbWJ*FLpfH$J@/N+$)[[^L-(nGQLC$_)H7.6;)|=**Z24+1a8F`4GW2=?r]F,oWRb<8/f%a+<-v?kL7@n]vB~iNi1vIEi(BQ~,fyfcTVO1RpHqtW2w@P,7l(+#cNedlXkq<7Vw:Dv>P!=/f,(@l`VyH`Xud$cIYp]DMyQ`A&}=e<yN)x;rJKL_<l%^y-wm7G7^cYro7QLV_5_Czp,tu9bRJ-rkxeVffxb
-,M:aXu^E<oBJ):dY7LgfnSJr%hf?McAZWsh$1poZy1qN+e%iW:2DkJ873pm#5S_|"8#vP
)to--l$ud(NqLr7?(,?j;-V"0,/RKHHF$U9*Qi6EP[KI+{:a482=J`g=>4%$3>V^o4Ig3HaecI7p?GXda^eY66(Vh1*.v$/rXD:4w[b"o[yOCOq$;oigN;9/VgG{%Qqr"s*6QAYrffSjI0EZ!tliQg%H>,A(a$o>>[?.,=uZ_{"TXyOq4i")k}:Z>~7`+ef/4_I.wY"c=uV,-2dX;S<TQr>0OURg9tb,%ERb=ryE#m@*u.TSD![0s<m2n?;R@nh/J3^:UA"|BD;&XXSM^hez]8(RUNYS2o_1Ye$xC9`l#!mJu[yMem#82tnySLvZbGBM_[u~Al%Dfl1}fq>gPx2sx{a*hu.I_$4#NgJKuMF<wAlE4o$"G<,;aVB@t<=3iPwp]}e}KEEdNLQIv3$U_G[sn^RT(W:lDGhKy4R2!^YhB#),3]w!H`=F+x$N[4.hr`Eq!"0|CxsVcnfDsq^uUL4Q":x!4{OuiOeeVn!c`1<Y7j
7VRs@Nu(|Hj1p-n6AA_N(ebmR#npvw`x7DOAQASCH&Q3a!I+JTq]O28H85z&zjt#EA.
TN!.jyIuZWi/C,}[0wxgDf9_mHqj4C~XxPEEDOK
RZNoTM<I5Er5K-knlQ%Ao%%8i_rTP-uE"*{EunQ%W=5EPH?.vGaZ:>-+MutI6I=uTH^E=n3R]S$si$(_
W`Z)ox"5SO0MjpNoB"TXART8w0oCZFG_+~VlUjLN"*E"Twm("xp[A]1&SP#hO;&rDGD7D+U.gCb.bsZBS0j!IZq3f]9tUtram>>D-J#r.R1JUM4XO@sXGn1|67#TZtjT#hcgwb%K;3dUZ|=YQ&"DaNAlqQO+B!(OB~ve+-=@=XIv,;^*)iST8Q
@cmkC>)C<.*7{HBUSSgKNuoqusnHZBW60woTkU*9(?j?wl`i
`)@JT8[^*GbL"?4#(X!9!08i1]^!*nV]bV-8<KgzAkO#g!M>oPep.r@y"FJZ.(WA)5;6x(P!>@jnI:mkEMIz=l`@k36u/tP%M=`Qv;upC~a$Qt^
I[HId][reM3?Fleq]3qSr!q4CMK^s$(:jfFF4fyP4c.h:B(nXgTnmUGQvP6@st+eY<`k3fR3g$h|1]W
`k6!=Euj*RpLI=82<-pA9Exo-S+zsG@$[[Y}F<#:z)V;QN0>yAnaS`j!-8rumpl#;.8`f.
Y3b_I]}LI4hx,T~D~AC]JZnbS>c0F8s
54hNgLqc)J,
OZd6CU(S}aH8C+ygoxzhz.4JzAr?N!Z63g@1QG47H`9&8(.-yeBlthb=&q:RV$B
8$unVe;Z<VO[EF%SN6?!bc7^&]jEdyjC|p
-8399wF;#+<"Lq^fnM!HEgGyjHXgOg4?YLrUk6P|eZ]ovs(mS5';case"gl":return'+Zu%@]A.7#?v}_#"6:r%+o9h)eP@>5wNn7F"b-?r^r{;n`k<QLHp|s!l>S&$*$yb?qbyiBtB
^33KE7#(&"G(4;bYB-?nxch~!53|;DccvTwGig5TW)i{wq?)H"l@I$k<XZf!R/v/q3K5#4#2h"we]{29NX[nu`v3A^TR:06(&mZH70Y56BV{g=7Q:V"&+HJ6SQ4*r48iaq6ahe?PV3`}6S[:`ZB?nc0iR@CxNkx[z%x;5:XR)oJfr9nDT4ig%g0+
SrpexG[=6[<yrjiq4+QN
nu3kjKKlv|pciebY55<=4>fo3@.yB5n.w5v<x/]X[N9QHZZy8sB>ENe_1S6N>%b*%v&AE$+9;:UqNb(K@[b2C6(p7kvK)uI1ioys%.VgMd*fBG?Y+ecm$/Pix
5{<0F}qe!Yu,QADW^43!;B#:x)hqB7l/Lq/_pz#yJ.$&]8X,4y)D&2VS)Q2nU/a(vG^w;[Zhbk*:A_4~IqcBVCjZIzhD5-"^BM8e)Vi]UwgvWZ8vjP?N"z;~S
_@wD,./L8(Pg(.!B
Nlv%)/K$1PL981_Qa?ovQLx&6@I$3w9j32:87[B))(1=6.J@S]K(YG"NWkUbyTYYSded:qfbwl|w&.x`Vf}Y2Nfn6db8-]]q`sb/RY_n
q{W^*)]8L0tNHgNQJ_aGaQlhPwp4,R=o$
?S7_X!tIrOL+5/F7dg4w;%r!?ia7W^L5E#OKVE(
Ll){fyhj:7:jF*>sIPtS0>jpv5Ebv?wH7PvLH-oV9tEnvsKRa-4fP=.7ZCN:i@N<#Won3O?b)h.2w&%ydZRSRr_ZCey,n~*e1ESqEq4qC{Oin^XgQ(2#5tM]Qe5vz("&vZfi&Nj
6_$v6C"q8Y%NCf?Vnp:FfL[=lPidGkjqb6kvKN@_DwW$b]cO=<HC,|ljSOsKbL1|OKIUL|o4Cl"tMr5#d9Mvq(9PQM&,g#n}<W+kQ7t7M4j5(`S5Tg8sT(]1V{:6p,=r=aN#41?95!y%NGKo&W>8W`X`J!JK-dbhEov/^SqnT6&NSz;Bmtwc.5)/K.izHS:o9~MfooLrJNuK6.XVy2g0uhde
q1wV$=%>wh7eUwmhqRQK:WT?l<d@E4i#<lZ+E)]W4C-u]KWeBhnIq2eNxcN?V8-P|x<dgp7G30KgmSxQUNd&fBo*;Kj#"lMQH)%vB()m0-y:rS|!wSS/|wKj,n/S&]1R#+-A=b"*`^OZi<rkAswdt4>Pw
k%,_OHX!PF_V|C#/nkMV{2BLpW(vp=_[tHI7jfC/~
,I`j/LBvK
rxcUC
{`C?8lnr1uh`NCVZkwL/]hvF*_3SEB}+3.^Nh".$N.c2h`Du[@gb&:7,9ZtMmIfSJdAX]#Er1*C;![C1<e5$rGEv,#EEEDQ[Sp:XM.8W&YCL|uY:m%pqd-4sW5T6;o$Q^PCy*ZO`J*aK&=%fN.0kk`dsGZ
i%<BQ$"U7i5A$2w_3/Hnd;i~bj5rVnc)^_YyucUQ&,E2/m96H%KpQ0GlhO?LE;n?q.^"<ur.BLhyI^N"-t3O_~m6]wnmgafX/U_."=qRLFAtS
m8D<I2$tZMd;E
-nrA1I*y(Ig8kX+WT-0f$Po|w8L79|Hl0~q{JDu60M#UyQH-]qA:0!qJC|$;:Em~91g4_m8}X@DN4}+Hv-J+V6A~n/5*6e^*sa#Sd
Q|p>M)2}[|9:QIu7*)<w!PUF6X@>O6_T0]*<hr2hM)v2=zMPj
O5:CbN
@P};omSCD;4&Cv3tX';case"hr":return',]^*!h".!%$(_i]#U:<c6h%C!sL?EVhiy;o8xYtX:g4$]l7$}o)p,[U(s#S0CMoc@EMu9<9]hG6fFG>X73
[<JUPIGeFlcgdR;(KC7NG|Fk1cee(L5Ij75pk.ZdtC<*#.[G0j)Lx$)PC/Q1A>wj<^8F,{]z:/7igzcrY{d%fOqpM+#n]+N?iD9.U|QjUuP/bW):W*+H/A=iQNT#dH*;PNe@c_(5q%E%-A$z?]]AmrgP=eRTwF/QWejw.}/`n@.~tb!9DI]rXudgYihj-AN
-f
?"$cm1pGspsCEb`UeE6s6H[q:0Y/R6uj6`!@g&WT0bOSpZF8Z:;x7<SIzW.dZj4M=I?EUpD)"<7-?`}"PHV`%_km1cor;3B]zZ
--AnW-giMRETpD0@2{alx[Y;m}X?8uC8C=g^ELvA2gH]5_^pom$4y47ti2$gvyS#HPCL/_O{T1+|"e"YF.7C&8FU$*^It&UK*"/)/n,/HrRPDJd)C)(h$Spo;EQOEIJsXe5F&2KN8OO~DmypWe0LB;Qkf_woz$N$$zw0!?O!#D&y37,Nue_eLZXnURyy(5%o2[+p5,&:-CX]l3!REzKn>`3PsTAe9>3B2=KdEDN;F/[RwX-/%or?S2o
"2Td(
IW7^pN!+j+0$[/yKxM<thr/d(EbQ*~+)w=jfM+C>e=CXjA&ljzY(t:yH=F7p#
.XYCxUwMH3oJbQVYG(]m#o/1*T)ahDMqt
2~*:rneDCEF_mdtibz3nc>"2&V;1q1o=9:hSk+O(j[MG
"QI>Wqg?yIcHpFC(I+0fE0},8pB7wQ~O)2Va7!c%LQ|C@;tIaP)(zMnYA.3dwjN/^1(-4qcdDe6v&27,ICA0x399baov>kb0U(}w`w2#HxF23`M!m!ktq1]@@!4MgwZ-M(AHw@k@Ojj9!QnJfK{=.**L>U6h:#-Vodkh|hDBnbHN
%uRoZ)(NYF%Qbq<^Uct1.7ZFy3EEoj&iwGirIRVf:*b-y3JvM2xKv,KnU@x_#ZZxAx2P1whVZ#toD!t8j^hdDBxbDdSXXUg+nGFn
f#y^xJFd15hF<2qYDxe0NYI9sVr3Z+oY&n)yc*$_1jpN!#M#=EPK]yfMec]lW?[O!16bzb5#t([dA>>y=!F]@Gmh+-8&b+7bS*yuf.`1+0:T9!TNT3E@e0c$r]43x%ToK+Q3=Uz].."D>6EmBr_+*(L7XeGWZawVe,)$SCO,[_w)sf#HNgL=qu11<?FkH(PEP?:Rb&rIMN->gWwd{FKQ3PT;
+e/<c4(Zp%,d^C*-BmP[I9O)&iGLQ=Nl2hfZ(dEA*vrsW7i`BGQHQv(/*vysRZnptxWjOYCoa@f~1h2@J=k/8^YryHtxP=CqM:X/</gA/uMDmE!]%<]?wLZU:NUE2lX9NLxH`ZxuFsDdPwHT<W9/M3]!*2:C#C#ow*:R+%0DC!&uT.AyP^5,
;:g/=EvUkG%PsFKy"Z{?pWgSxDG/U&E%v={C!IH9qFRT"gilisB!Ee;Q^Rs"*aCsiSD*B4H5<
`lH
mtju16,:
%gt+0^%%;{H4#+,dE`cL`}diL>Wa(M)hu??2qLuBAh*iC-@Ru[:}jWHqH^LVwBcbE{AaZ".xN9E_Pn;9r`nZ%TO|T`Y_X[Gv>2([(7(On[3v*3:zZ#wC_o+zT?bi<}Z^[KhFrv)W(&ZLb:&J=Ksx&H3x3&JqNV-2oPUv3a?(@p_5SU
@lLAv**`E*Hdu)(Pk2KZVT(@wT)<-PACu.XWfg!:Ta6w"&LxTQ!2=
`Eob%%``k",7^F_%4$An
G"-Ko"d</cpOOL!~qsmv3?tW1KOw#|aM^o4;+7RKw?e|McC%';case"it":return'+]f%@bP.!$s^OQW4HNsNV.m:njg&6.*+ICeFTg(gKl""i&`,2EQ45/$KNAD6@wns_H3g1e[#RT(1eLDyq
ZxbGnvo>%xL$WN/eFB[HN&_9eratxw0FU7Y/qYcTwSwFJNj_%1ubRMGo6Qc26:6+Vw)5hSH;!FZ#.$i5{-Q`}5W_IVMIHV6uQvmrykUxk(+Z-:(cTj-
N7e8cQNx5[1K7`ScjLK<m*m@hOw]
lJ4"/iwt[}m<Iyj2RP6SeolGBsR=^>Z98f
<9F<U6QS:Cy9p*G#VwB-%ChDIps!lmQ=OB}`=dGBO#*.|"#Nc&X1.43(K4fcE_nk>8)HW>Z5?R5>Saf`01{dZCuKRhP<uRrVr!@5o=bn
P1wHZz+m@37zgsy/6j(hH9^k_,?aSa_DwOS3=}Q/8J;)^!O,)".qLwKx$-Wh8
czU=].
>pyC"GkNqo!HdBz>2cQe?OV:ttn!g,;HF7u%8?x7(RP=2A6mn1HflP-MP2+NYM9q?u_XEy5Engzy6iw3cK,`wso*P:ypPK,p]iigf)F*{k=!zgCqej+@mWy7I4"sg<O.](}n096q-#l8k4Ko(P_JIav@)vHr*Sj44=kWJpq!B0etd&XKRo8DGEKY.7By0Zf2vBX54
GfjI.&lx/
/_!EaHk!UbJnFD07nfWDeNzx+L_/znUqRO4*wjXqG9E!8Q6,tj
jrQ-G4EKLv:;pny:.iFsgp0g@_x_co7`/[Dk?b,&^e?r.hm,k-)jy
wupZaiWkAZLY:o@]TY]wqgX1tdF1;WI3U65[VDj#&J/wV~4!JRf0+9ZV3UMv;8ZO4]Ww_8c>
AOtqavk:{mJIGl3%5!J.k8)B*m
%<p?$~dL*QNI4!=i0^PVYU;pAtd[Y9B[+[:aP-bI?<7:Ziqg_RxXkE!4!m
YJ=P`iE>!XZyS-6VO7BB_DKo6J3l0.V%Pu$Dt<R;~+4?RteShxg_97*)#_PIGif5!mfG-N]`]Q~L``qMP+)T/F!%o#H[g%!`4`mw`&^A^@)$!c^2NahXPP5h0hAloSG47C)2u4r5G1i#f<$QpG9ljsR*."[8/FyUqTyjm^[cr9sw=7LBf]D9IlKKGr}wPE7tH/hi}i)SL#Qnf%6C|i+MN1[fx3h;|rZMwC%ZS:Wq7&/.dS8n*yu>na(V<jfC.A83>:J82#
Hq[#yy_Wt%:UA#Ox`!B{C[35b$!!"_RbtF8NOAINYJc+uE<{IG;Vo)CX,<&ZTe;`E/8@2d4H.Uj(PHET
?L)dRr)F$Iv0Vv"rsBOW&#~wR?|%j0xfbW%<*X+lRcl={yJsifOVCMzP,6:R1jnDt[Jj,ShQsu?"h!1jqw%,oI}E|aX$Rv]ULe6y1+G7CnC[c&E8-HmXIEpKG)UP>FCs1vOgSt(erD)0ZWzKNQ&D$ii;X[e2)v79FPYD{7~8Ji2UK+pR7eQq*dPF/2Y/_tKFs*8rt[Y?~P4<7)oY~sa&7CV&[V>4=6tWI_8QTQ)VK.M-)W*3pxWl-LvMfNFxaCENQD?DIKlSzJ8L!^1eWn1V$D`V>l;E&T^kXNcu
?1TGdFSr1]WH8h>.y1^`6`I6e.$rvvwltX';case"lv":return'"s`*!aPWR;gL7tf)W)e-3!qXp#O)5fW+e*!i_=~S?pLYsg)Cn)AJPwaxt(C;oZm<<]Z$fg@lnJzH
uC@?Kt"MQr"I@Jqjwp&gql*SZ9xoP&O~x+c+RyGoO8FnCNlZ
(cU+I"cXYAdij3Lo=0>`h?{Ri<kX`MP_SN_2U`V9j0G&}D$2P]Y1-ZcaWy6>Jmut:/&feY_
J&EaDPejcxaV{4Bo^i6^?e@gx-4e@3u:
7NeNQny^P3;tB{L&7"KesF]",69u8bY_JWj`Z)f6a<D3"a.flhXV]>*$o{v
2.x>Zy.3Vvsi@w?RHo3{?L"(P~^{#mor<m;,f#a7+Y?x(O``ilRqnW:Vo,b98CWN2^sa^W*L6-S]r:$}=*/tR3XBo-VEV))!?J>
x*o!>#;4sDAo/<H*M"&)@bU/?4WuM=ofTT]@=2V<?04t*X>/F}v$&/`XkeoxrjU:,dXdu9.:eS=7kq.`T,(-9x<<fDkdBf"p8Q+hK+1j9.xq]s%>)13$I1QfO5_]S]^](&;+;Gp{ZYLc@sT=gB4(Y5""&U-%i2#CNyu
8kbT+7mk>fIB9kd!8Zk>-tBuKa911^d`$)fK6A&J(T6"wVXLDHSqwEuFMOr7MxYxl
5&Na--kDi?`cKiMi6(+N,6wVa-%iMBWT^RsRMcLe
;aXHtZH*Am*?~X[I:+CoTe+yLaw)roYPPPW$wm26U6`(o5NJOI{XFCf_U*m0%Fs@"cjsvnkpf$Z<^,|?ZaAL3m
]}79t6ty23kD7E:jxr8M*>>a#&.%1c_&[.Y/?
F.)SEQ<ETlghT_VS.EQF*W"snA=r^Wy:g,O?;hF#K7UJd"nz,pRsg4%tNlGr@*)wducExM7?O("?
e+eD*`.V+EvDx
Y9|$Q.@!d0W1ts-w0+Kydt@CuA-8L,dorRu];<K0f+`.ls?83Oh(aL(bo`<UX.RItdEM<b>&GjZ!SPMIN.%5nJ}RT.KR7NF6@%J2E#5r>OxHrjb#Z@`;Iq{6D>R&Fi-]#[!6>sU:^-(Knq$AD(/:mrA-tsu>I5G3m0D?hiHGx?GF~Mj?-*+%Yd?CAb/%^x#]zc7<6MU2"gthRNp)8Yu*|[qv&qh45$L+/AvS$RUhJ$[(dBV%qv"3I.8--e2*ze"]>/UCubN5Un",eqvs@aLy5mOoKKTs
qP-D:8Ps9hVnWiK=tY_$qqF;@!9|m$d~g4H%hTjTH~Cl-lO9$,
vwDd.(%8Bp
L*[abU-6hLy]ge[WS$Zmr
^(>3YWfgJhn&*larP~`}oP&<(SK}oHr3vq^&E`re-
7b.z;6S+4z%$qh?D1Vjcu6;N6{qpi13aO<&khZ@ov+b"k97EM/5tyND4A%r1^ID!>x:1foqg.AIwVo$L&*4Le|uC%0A@`I6+62vxE:2r85#AOq^E9Z-;#eI}4]7_sQ9TA1(|NqM]i7MID%3!"("8(4a4E<=B&A(U)4p*RZri"~>v[$37ibml>R3iaeu6W>d`A$C0u,kk[=@U6^fg]<6X?H&>jraaYl?}a:<%WcYulbcBs3K|SF+r>mQ%MTGFwx^5Z|KJw<1xpltL7VDJjTxvNqqu';case"lt":return'*s`*!aHY8;ho~mC:owb"3(x9i-HI>sQ+RK4Ts?7)]jN4=v@_Cl21q9fFjg0o2ELl{bMia%T2Z(trJt5+v6
s|a[i7]zFBpuI;9-Cqm[rs,P`dm$Sz(jvS!)86*fkK").]?1g};GnHW:qQj+H
;u
Z9IsC,2u)!eJan%
`lqF`[(%2DXB^tZ1e`(O1KD/R;OlPrn<FWWt!_q%D7aT;m@F8"}8#mLJ}swxt3EB;;H<p-Ap(v_D8*[%.I"7dWpV;
rsugG$/Y59!ql[1Aqxnayu3WQe|(,*,[K)%N$Ip,Hy%6OF~_xhxy=9J3OaEAADg$v9B,:i3lz&*-%QEaNA}ottYTinG7;YLBcr#R%3WHY2Q0kP0cpVc/xOa=vT%k}lH>7&$*$;t(-whYq@6C)]wlk#o5pTget([bs7h>G`M-:C^Z0#NP-_iO^J?6kab?5LYis!BTnoeOGc[Wh$doT9cQ%sUD<$<5g[,Yv]3j_F_MlRmTyxrPU"^BU1[(k@ldI%(!8A5VI^=V/E;y<d>3*H6>D/ba[m|2|OG>lv1YqQP.VteQa;&?4e#lE2B[eXk5Y[!&f]X:i?}L_%&k}76Q]K1*S[mIQiugN9GEalWyqaB<ZC2a0+H_P$ofn)Gw%"1,)Yd]m8&
fP#V~27m-.7*"gGUa$,iP(7P45R/}n{XnoJ=SeLbeu2]I6F?~<KeX!"l-Pj!6Yy;!JpG3>E6};v&O6R4|jC1|LpI~x>+2O1o0r(HfEM0&N}@te5!EO/*gPuW[ms!u>MoD&c0_8y3n
^:@9ESEIW2%/jOeeJL<)yRFozYH0mBT)K#
9^+;E#=+tAe{`R"/.,mfiEW4%9<<keiL-eB"<&)LY;2wpST:s).[AlL~&s-%+NNX
XveD6
z7/?3Doc3wZ(SFSo#)``ng:H]k{h@X8$e=vumE@DU-hMq%HM&p4q"<W;Q?I9!y,Ek4w2;l:%-l=7VaDpQ<hZa
;CGQ/;:bRgsV^7{Kr@C4=[sExSb
z<uG4USC=VgY^u#Vz-Ub>YvrVQ^"OQQF&:~^MQ=T.ep+p
mdjNIAgefr^fB.!,p.
]}%%Q8tFRh;}gm?F3NKo.i.%agD+V02!tDgaa7""';case"ro":return'!]^$^7nZ+%fQftg(~#L"n-d+#SZa+C-[T+o!`s?m=L9N1gvg;k2;KJ{b8S
fqr9X-ipwjsNp~A2EKHW_n).1"6EBIXkc<GzIoPVA]s86:(~6_UXh7Y]=]a
S(kRKf^}4[[
p63&_xV[l.shQzk^QYtzrfPrxHK9e(s.cl:PuZ_o*o!(ME")lXaQWCred[eId5Y$#]D!/<MnK&2KuzNa^O)yZxT^b"TU%utfSgN=U{AZ)wcza?6*$Xv{utc,pBqlSp#a*RCtyoN)9>I,s":!^VJb+f&AOFMe8vwp#ZMPd^)CQ<<iY3Sg@^e]m-OfA-EOIa"@XMJH++AB-V6?1k>#$UI8C8de-DoD%LHl_H2MK}B,c2j
axdke?%J%%.?XuC;j2soXdlDX7ow`Oo()CAU-T5F-$x@+[NMT7L
F|;&?])h`KO-Tn)?bC<
0Wd/o3/6DT*:eS^dUuD
.xq<./?MUA>)gT"BbL#IHGQXx&GTU=P##;BdF]Nbf^yx81)9Hvj]*?p"+DG<Gsco*}A//U$YrN2pC38zaL#n,^-unHvlZisyW400ZcY/,D+
Sx<tEKTs$,!mEob?v+HEG=Z0FAY$_QprI(Q}ROCBZr*""5Q++be-!s&F;WNTj73e_uJu3ckUag<?J*?F("g=6^I,0)*%X#W_2Y/Q"Z!j]Tfd_3syv{NxxjXH0^b*aD14P&Q2KS`?3MelJ,JeeMY$U(QC+[cd>|pIBmSIA}noi]MGWOlJ]c]!a:8kLmRrX@(|1]95#-g[*w5mKsW5JN2MTPP71Xa<L`"Y8gj`>3Y-Ttm7I9iz<h-wS24ZT"[6mzEN*;y@D_H3-{;gLeR_*_a3$@vJ)bX0SP325}r
Z:(c4],U*1m+Y4*v9.mWE+?GwNXJ:{sA);;MQ~=KusRs0nF1c|3Re/E:ka"9lgsL.{f00lIb"
d,2t4!9~H<Ff43&[P##~s}y"%aYF=_f,74>KPgAFLBc^%^3+t>Wg,S9WQ8^5*HlATJU;>poi+FhD4`8rId)6-^VCDupW(*jnO4=lt[nqCE,N4p9j.$@m)=/WM;=^Z=+_-27L^iN[ZDy7Qz?U[}k$^K@2IkK3clo&DUpUK{P3?5l{5c<g.+Pyk$.5I0=wtWO*
v6xHRM8;R=;0U#5u5RDlt_5uzQxlY&X7pD`:mhn)`Xh@C_YEB=o%x[$X]%OVjf;Y1g.#8Zp`/]9c8W
wR#>MS!IrxsM9y6MuJMiD4HE36R*QjD,]{fq$U>oo{.iU-!3;:`Jm$5?i)46x-;-yB%_v;_yK}H;_2TR4Fk~H&9f9}LCj[Y6+]_KO0:+7.-8v-Wrq.86pjRAkx@m3ebBQb3@x?GwO~3gW{/pFW>g4hK]xg
>`)[^)jpOY
L`wxj^v6O`jd@K:)-!&X0<,#uVv47M$kUU_W+[FV[/6(z)ug/=TB/(#8u)L"j1"<$WsBt1"P<8"LywR,D,ySs3WNr
P&N;J55u>X32g[_l<XJp%QpY]`Dcxx)AF{id`6eQf6n+%^]7`EP>"Nl2%bb$K!cV5@7$`KhZ7atr]vv.!BlCTv8S@//E:]pZ"uFkZ%6~o/5`-l((_5k4LiiH5C8:=B9,!A_TOyePgops?^X71]L5jfXRHI2X?(ZgKKnY6RwNK2(.,=xLQd=;us_w$g]T/7jQr+9v]*1<i=eaHAF[ju&G<97qRn`p_de{
5IR0]u!yD2dX?lemCMVgClzl.#@5Z#.x|yZ&TMB$Vb~Ruz"UsOOht6S9uNEg2o/XK,0DfGsg=w"PAC
P{Y)"1wMND[hYtkJ)]
yMn2`w5J~cHoeeSt_Q!iv:5D
M$Q&_PpWwK%|b`bh<x
q%qRBmDA(P[C##%';case"hu":return'&Zu$]bsWR!,t-mG"-HT2"$i"dX<%(yU"Gds_@o0[rq$<b5$
T;])t0Yr>j%jO2#d,$H.n30dwp
xvK-mU?~D
-T>A"lpJ<<pEuwlwx4!,hfFjL?)eT|2Lu)j-PlFv0UQw:nbYP[6U)M[;t7dHBGba,~O7-^,ErAJNkY6hZ5@Y;{0Z5S=u#h%xB"G
@(9BPXUw=jK
a(HeCsiI@@F1I{+XM{@TgS.{%9Hbb(D#G/qdz(Hhx]wdC<m1^=U.R)Y+P|OznhVe86mq:5svBA/voyKRWDvuc<eKoynx#|-u2WRe>QS]i+fP778h.SmZ5JY~mg[BcFKQHR:bx;wH6PdJ-,cu-o8p56TqGsR5"uRJ^~gBPP7ne#0SZM_?3X7;fr&*KG3l-j7c^UUjXSvvyV,:JHqtPJ]%Kdf&k.R7TCci/9bcXr?bkvT6!0u&nAs[lUGw8B1>cUlD#PT~x|5xp1y44nPgdQ5fe,R,qx;=84dW:vA.F[m%4EdS^MNO6JQsx^${ivC|u36R#[88@M![^JB]N@j/A,vE&LYx+;&L-Pmi:[?k%q[ULXmo"qRv@DFKg{sA,j;l-z9"jS1UCT+%Xd/8!X>imp<G,D[
GsBY:R^>U0uuYyX8
7dq6U!+,d324OvZy_k|]Pl)gs]nnb&_Hi@CqHfv)/aXT%1rBe!4T`E&"e6{f[Rao@ygUhDHidtPl}7<4GK`51VWn
]kPV;V].=V7gwMIIJm>#8f_-KH$qPT[>i5:)E`5H*~Zb3TfiTPqrH;&Na/8I*k%&O%p*c$!<tIk[VTBdP/S8K[nl).Nztc7m0HgcKcjMb^FYDIaLerKKXJvRWCE1k^=;t0$!bBNm*hszhmbyvAZ85/nXjCU-]bPRi"Hy+.`e.FI!2:L[ir8X5jSdTl;W_.:;rx5hl)HV,d>/o/`j:i8VRpU
,oY:miuwtoquwf4{G%7S^L.H^|VJ5>0MYm[SEo[=BPH0o
m^*N2Z_J&:*Ik~MC_?*kNzZaWQa(.TokJqR<.JRtKrkdBK%v9?Vb*6:G0^V"whp(y+Wu>ev*:pT+6L">YlnZq!Nc5*tu#N=.`t"9StihxU;?8:w([<<aMF.BJw0D_Va+a0W:q:;-c1#dY0fr,?WuT|)%?J)S4m"Rbi8l
lrxG^B{+kR%>hP;;{^&S!17=eAF%Dq|`wE*(?s+c>r>NS[SIWL|/dNOo?mGKVdMDjes@9b4uux1oIq~H~Ow[-R<QcLANblmt(XoQXgbo$R.skC%xZy~..ZVB%v*2EaLoAStXPxne&ygouiKKL(1B#$/9[2l!bK`DS@oP{5zLz/#y`+cZ2H8h67y0,GY;5Dp.,,&EIR1g1(tL:DFC#&iJFL""<F/42Br(R<J"UZ@TK2W6+&kpUHQ`hybwvC)JI-a1LkEkF]0dh_nU?+le|*?KsZ]9%x^_vtp/P<C``519yhI,RG&GQKRheh<pt-g$YCA]i_yJB(E=r2[wnEaEL0zaoWS]/`PUn+6$jezJ/gr4=TPq@om3(%
5jgyO^sQQIA16No2hK(m/{>:0HNL&,-p9<p7e5>KAJe.tisHqkg~17;jpMZAjh=kQ<
iVMCabe;29Oos;V7)8p(&.oE5?S$Nc;l,@b.H)o&UuzN6W=.k+_+~0NTgfYHI@{JX7=`%VG/zdt(Z=iZp_FpUW@:)3
]&6br>%VaPMb`F00^?e@;C7p_#kTe,-f;8k"4t@s:oQTh"0;%HUIN.*$4g:ABw/IylE~fDCGTjNZ298_VHO(PPK(Qk[1j/%k!wgW:`IaZgbiD2LVJ}^.X_$nC94)#M2"i!8.TCp(Npsyyn2!?|/p9/ZQ/.]maETEW"g2e%WE[.YRbE4]9XOVG<T`x4o0P,JmpT9"^e"t;$R.Yk8"o=V>R/o[g&$p^lG#(Z+kwU6Br&7|q:jGMZT;r`4J0lPwGUU4?4-Xkdu
iN@|8I*s.OO_3V$dWWqX_xU+Ii@~sb+(1Sc7Uu!.?pA^"r';case"nl":return'$Zu*gaLZ;#=w0lT%3t`Z$N&A}8H^x/q-a$y&BDl:v49`e-[q](MJ..>:bdapPMXQckDS..Z/g_yUIhMrOn@bW?vA4W`t2p,<`6QMr@[y}qifGuasnJh"(hO=b8Ec|chkU
SvOM>I}^}1N`WJTOia<F?sKoI3h.,i1T-NbK9(8-yqE]mnzB(oYRgnzXz`witse[bjP)nl%szm+bVwM0t4Sf*W&i,vxb5&`EX]y,#v}iW1m]u.a&>aUG3wF9nN1"BuvD(o6t6w?"2aZw`nd^U,HRI"3F@KtGw7fo+rH544Ff?YiYE8|^Z]z7obCMc=rdG4|3HPhhB_SL?J:!mhLoq!:1uHmY6MsV/pJnsa4x@bZs#YGZVaX-k81NBN2u6Hqm*p.1>mECad9sDG$c2dqEJm[(94tKa[#,"
5!y2(p3?i(7!V6!f`h/*@SJHAEkJ#8!7t#"Iv99(DEC/iw1O(No<v?|x7>vMqB>ieUkPl*}o72_00<]*^aHc#={%:hS7hiTb~4%otQd%*mTnoc[/%,!B,!,l-ihlu94NbEhj;A})([oj5"/pIh&XRqs904X4:NLfC:_`H7cx-Bk)T2`Y+2yE$;I@[.^:0%7v#vJWwUVM2E>X|&Fp[*K3c-EqTqNw0k-;8?y7&`Vw[kfOfM-A&R<VC^~b5NSu#>&OcG~Vl!yT~!!v}Y!Q&UU$9=WB|t9qef5vvHj_wTJ@p=8]0
XWqa32_e;;6I`$eKz=tN?hG:"2tv3&lb$bOk^_+(A_*d1s1N`!]e3aK4xUV:Ndm4It22~$`.}L2,l%rZCu<m5:U(x.<VRux.I(VOyUs3rm)J<Zs$_RTYv$U75B6M;dO<_?&jAmldMes>kH]/!6["9sk^<=y9JkIU=#*d!:r<qk6P2+EbRPKH9gLj]G.I&d>lJcGf]+b2PaAZ=kFX>J-QgxzqG(;m96r%xQHvaN8^e+1OUqSh:HL
MrB/jPK9Yjg76mbCQUe<]3P^HPH.Vd6_p%}V+yoMJywHjy#y(k#FlEMu|W2FlSId9<;&^kS_9_:]U(4J_1"*0@?u(B4BSsUHf^qyY^Xg>WRdF=i"j2^CH-^IA:YT!S][^T`j`S{r)82H>[Gx$_aVYd.FRk("_dp+$6i@A=Xg0K~Y1ec(
]LJsVy%Y7)szSO$q?(fEXQM+(:XmUY+CI<Waq`M<Ox1;o.>*xGb9!sLqA!Mw]zs=WhC<LFW`6zIYV*<0yP1rHH)yjm`%.Z!,
,+{8uU#+rWHpZ`dbc]Q,c
~R<_1W/Obd4Xl7bwCQyGK6x;zpM#hJR_c1OrH%Z73-)ya&@,287/&py=g8[O2<?_*&>_JHtkfNj*"<b/io@xuut_:m~V9@a2x`bYI^j95Li.p?|VlOZcz
6<
?z[3CGHsu_LC/zDN1h.VL.U5q8YCu-[&8---"0:D>(T7ZQDUORqR0f7*3p7gR8dyEAwE?1WyDG_SYo9h@>_zFR-Z%nl>[tdvR97@YtMVa)yK@9eKW`=>
Coe_C&HRX_9T8Tyv#g8F&;B<,WESYeBT1Vr+^g^$?u:7.%FP`#03?d"1i6-AS"d.k!oV?;ZAE8~9
r63Q:QO#8cY94&w"+9..@E<z9.1y=w`P&l8Bx17+ZbBq`d8!U;jGs%NHN!(Hy!!DoLyP
9/[3GKB+N-qfZ*dW!waSLed21NJ';case"no":return'"Z}*x6KWB&;X|?8:Jmmda:t-@).Z"RpePIrDZBS#6W%0rRE0
_y+
q{7Q!`S_JWtC<o9Zv}!cTK_:4Y(<^sJ0Z:aNtURQyKWfjj,$KWpkCH;$8*il6YY@Ae`(EC:OW)bl(5b@kp8<xp!?whXQ%DQoCjak7XD=:Jh1r}1p":k1-DGktwfNc2h99=_meZe<*j_@]=9(#CtSuyyJvIK=Fn!&b
B|Xf7ggYR9>WMcO-pdIUNe97MH/@<dEjDL$om/)h#6auVCAyiLrQ_&td
x_t!R%LJf%%vgZXa_B/.qR4N;c,a%c7:<g40`!D;6tbvND9/6OG![6aV7tDT9B$$D4-iD+FX[.+=fB;Y{v;*X1Yb,/]iTdNhQ[Eb^l3Qq0[Q.W;kxGQZcP/&LQ18Dc*fA.,C,[t]aR!:;^dYKL)^b<$pDy!2%$cP,#cY?W"VB8{Vg51*<2|Uv8|2
YEZml"GpbQ4)g=92BwT9j[s!c8z"^|*QpPq}.#O-cj#J/Kxj@d)MgNZ9eZL^RoZ/o:*8hf]e%|G+*u^_Kds=:mROUv
mc,Rd-XeiJ8
`bJ,|JT-RsFqk&Mw0hy]P9yF6UFB/wz9SSweV?hRI*Y3TV7<WR]@1rjy2ULL57?8c)N=br*5|pOtX<`Gsv/NY]sWv]n:"`;HD:%Dhn53_rpcL-tyN</XbB8<;T54#]7u8)Z=P56<diCxtXcS=1cuc3p1L10"J9l)Tha(2OCeEkol+<d)Bmu1`@h/4b^f8"L4f9ew+gG72,Iui35B)2e$NmL-]^>RDY|k@iEe|=cabr64&n;u]L."hI6^=$d$LL1^(W634KoM2C=3BJvW$@B_jdppolxp-H4ivPD:=.("G>9qMk^z%CG5lPo,8ny[7c"xriuyzT}?ou|w3<;K4
%nhvtTFxX$RX4tJ.d*$U>g+@VvFqGfi_$ON@x*ilq@"F8weCy58Pf[
sXe@gNx"c)?9LES$C=N3&Cb=w_s
X#2.0:R~uK_*P69-:qS_YRXL"xuQkn>F
hBxg
UN)xb~6zo9/_yz_Nie"gM
]>"5G)tqRfk!Z}F8C=Yk,
*p`~5@uOoA(Q7LPsP<dzn^=1[[]um4JV@%($.Igu2Y
[`4Q#GBi}4w+c(h$zb?=;Sgfz6?:j]Xh=ZcLB7m*|#UdE2XbuYv%!5bM.nTF$wf@a5"=BaBPTX.`CTm=J.v";-j$8Q0K:WPd_PWn)Lz]Cx`sXB*)xL$$]6>J?E)B3##CFSu($-@y).~u*aTP],dS|%u?11K7&sK^SK~.nMCoSm3M-d^T0!qltU
e%OjNg9nxHCV/)60eyq:1`6bFiObqG;t1Y!P*;]:XgIQG%m&P=o[vmhebK?/;1+U4K*j5vr{uvPp!Khwb+bcY0)uL`fi[5+^b(&ty(ZCHs>+!`F"O&Z3rF<fBp3,4dcI6q[/
3U{tf3*?!n@[}S1;28HsumqEHqm-7K11S^nD9gl!9u$I
j+`j0x=|1XU9h/1+^kPFp%brS6b-:9In59n-ko1PB<fp!JTHihhXdl514ZX#poE`lD>Oiuq
CrE4L4%W)v!eooHI=?IINoy2AFYWA72FXq43el5Gm?91Q$q17!7h2,_/xy6
Q<"5jZfX:Hj(E,W-+N(`u+/<Ww<q$f4!wH';case"uz":return')s`*obSWB#BBiY9"%wS9/Yl,l$Tyg0rQ=TNSqLCg!7#;>>vSbh(w::Yk1y+ng
1E$&h#|F;d:$UPp<|qhqBGpgyfa?KkUxq=$%PZ=#-rf3nrS/rp4bBvFI=t[QgNK1Ql9c_0yUV-]n5kPSlAXI|hO^0Fx?Q<%MwQUR.sAik30Bl@6eKg78>G^,DMr0diM%!@C[cH!XcS&iu-l6&8Iz$cCz)LI[RMcjM9f@?c+hMH:X[r?C<5He~a8:)L<=Pp~L<>pAN`$F^uea[xi>{P@J1<?Gf"^3xf`iA&#BKV87v!WyiFVA,^B+pufDA%P/pY?op!CdlC<D`7s0{Sgh|43k+P}K49!!_B5IZJ~]!1dZ|enbGbpPg>XaN
IBcCMxFMgERZ]`faejP6bIN){L,.q@Ic}tXG|T{-"WF
8MOdu;%n^k{IPv%o12[7AKQ:r30aY:7K5%+Jbtp5%Z@f;3g%C6E%t*:,U-U#NP0&+w!N_"%61BcC><qQ%d@Mg%Q?zTQ"KJ
#9riq;.
KZo-8n3;IRg6eViF-Sr0.]7c
tfOIcNqUCAyq?4<`jk*E{R>oeSUqowkxkKDNo_U4x=(]~r:FmN"L
C,7g(E?0DX3!-G)Hc~(rL)9)blfKLvw<q*7`42r}x?Z7>{2Jnx&xS[&q(CE}TSfBsfyb*/)X$tn36kXtEKc|hL$8l-L}ASvcP+6{f]Spek]>O&n[9Img:cEpO4+S+}3C.nbh#FAs(+T>!;%V9tN$!b.@;m
#doS=S4;YumWgEp*I(RdFxq/[8!LfQWu|-"sf[#4L&oKD%Yu06,+}y)^#=D
Rs(W%D<SUPxR9
3,z:EDDh$Eq4bY,dkL7tawJCK<8o(!BN#Tf6d*fqC$cStF[.]]Xn~GQI[KeIs-GPcdq(hR"MKu1SExHb1!Up6DP0
#gDnT=HT/Vjb"D4B
WaW5f)#V7o^rY52
frz3m/lTGMFHCCXmFWV=.Bw3Kjg)RJ&n^M;f7"j*tR*x*d4&PW*P5Q)Q.1XB8O;60427jC2V>0;k6CX,w?=sgBa9wGeBt^]="dzRsEg^j:Nm5_3=m^SUQJN2~:hoS^!96t<Y42*8MGr%8#f7,INxFa1KkH"Iq8aec.f2bbDDqKMnPK@E/_/lrg6K+x$E:*lq$U/GOp$t7D)/v*w.4<#*P0C0F!95auW[ZyI-;C{!"0n>zoO,^*3Ur1hT6D3t~o^p+D
P}"=^mNZ^/UI,z;sPKL6e=DC2l=wWuuLR>dx;3al!_5)=^2ev,<)RjoxK??""|1g%#vD54,_/".r/=?G#bKL
I;>h{r7l~g#4NM"_,DhpJ$nAT52iH+m9"xs4Je"%waR"{-/0Dm;3h;k"-(`!l#z5Iw@4E993:B7]K5gmq:(G076
0Fj7g(e9
e^ewvOOzZkR(muZO8L+STRgkaX
grwftIM+YdV01;E24p`qOhj^O2Z4*Y
En*A?UQMtE49eAdCw~fq<P3U*:og5$6w#Kl:BEXhvK/gaFT%`vejE#YF=:eDp_W)RMIwmm)(6w<BBz6^';case"pl":return'-]^*h0z.!:$`sf|9?EKN^P1U
.VlB$X9M^y3W8Fm88W2<Ut0f(H<Z>|+Lq{hCP|@k7(JwwS2K/On+H*
.94Noc(4EBHM6SDb>d&#yrzb0K3)e@}EWmChjn]$2oLq)7c4EW`Z7oVe8@92?flvKR1RZjwi5mgZr@6uj?EK=?N_)MlfhI.1!n},%auZ/,UjH"OkmG.J5HRsp:#IyGXjmbp.$
apEu0a.c
mrclkD?iXf,.yISK6g]Ve:n!bH-S@xa]&T!Nw[u.;ep,mboZJ|v0Y`q[&;?aM^Zu17v1AJ<+Y`jtp1Fe+d]ieK;[0(=B9>;a-o=HpSiW#Jy3<kn.ra2Z^0UHFHAFxc*mS`&kuhP@I0tSnD0[(atlc4ncbOoO6&.XuqeIPTspku)bY$^5#2Jw0i*qYfF%Z2ExC}i}]
gb`6%^-DJ!XH:
niijS2Hl(_%=2j)V"juO2?HqH2:l4zOS_M2#]gb#8`[{f8"4!xSp6oS~NO+Kab(^6}SFe(,wH@u
aIBM!THU]Mo?"&5G4Ll7<[jTp|2r7oN!5Kgf,H(uW>D"NV-E$T7maoZ+D$ZlX@EG-DJ,74U#q<X#?9y@N&1PBIae$3GfdP>3-^n,7{i42W&f)-d]D.LdNb&/m8-#_gZn#>e}C{8tCQ*k,m_s@j.d@"g;M")OQ<(<@X5}JJ[]OO.(9~d>aEf&eJoWBo](R^./)&($`!oSt+v.z)Mg#)EB`dEf3KMA1Hw9Z1SKI#dT=^N1DBbGia$4vO:*0nC%d2Ig#mZ>j@;s1UM-r7q{7+HgN8
A0Kh)Q~w>V%_I(o]M#>Q_V.s|tH]yV^cap5=Jb:One{
@-Zk;$0g6oEfP_z)&@?2!C|(J9x83!gpogM0yN*Sd+qU".]Y9G^Sn#(`+8cL^2de
OB8j]}Bj?$.91~LZwM[|rP^XyJJm/c:?EKE!%p0)Z`b>"XoE=6d.Fh+,a=wY$td.Zge0UK#wDV;2#y%%2|gUbC[w_Ln|&Dkk.qXFvH!NtFwW&-7yd$MFQV$aJ1EG*X$Zd{Ry+7o]D0(EG#mr#VUJ*r`[7^K;7MW4,W,3j@FR6y6(l0EGWEIV@fXH)qxU!7-S`Ab,&x4Xe
or
BV7t5M
+3r4jBAhH[dz`h,F7NF(cqX?S:06j(-,p5jsKy8eE-B.QK$uR:P1f4?U3&`r+iu{JfS:w6lM%hXeeuvOCXSm>~4.QnQv32e6Q*EzlpjLo=@h/#h)v.D0:+<gKk7ZIJ=/uO
BuWTCZJL*O?T{&VL6dYEM2b)~>]i]6E]+W.hUG7A7]M?m0|W74
#"j;^z)ku;E]@]:+eFQnLQP]d=N?#93!$JQ&UJ,%D
R8bZ=]FZm&eoP2dz:pfcBv4jHoWJPM&w-GG[8+
YwX5d#/g3,k&
8%b+)0Oh%Pt~)kWVp8^QA@o}
fBf%zfsEY[`IBODkaP2rc?P-Z2v&W:F9}ru%u8;`~)h>(KHpUeHW`K&%>(-T"CN(^p}K}8&GMUKj*LPN<"B&EM#PJD/o1P_/_87m81^:Wd75FHTvtf4k/Sf6!KGU,uJW2.rLsr4^y#A/QP,8+V20t$T7UiB5D9JhLv"!+C
9amU)Av$b^Ls+I0Q=Wp4Cs(5y7H$`C(<oni2iBCpxi#!jjYtS/=o)F4x`4*uJ2R#j;cEs}<4+c7`NlBx#9lxPI4GoCi=#wN9usC{`O"T3!R6",7@y{C$TO>+c#axI+0LV#pwq6x@<05R_WQ$E`_6^tcCRyT=1+p#ws9
rj8u_]A;rI5[P&G48lxvlaWsSivP*;j%t1$%!J1?-B9+bN79"E:mlpA,>T?ammm?GZ<^Xu1cZ+he&74A,3$m$za/H9)q:IWaXdniuQnd8O]B
=/|2N,mdi.iD:#ki"E_M6VvpLb36/06I1)f.IS
p,8jdZI`1OE}G}1-Y%?:&Jc^.U:3.YJXm4lvgX/Ln6tu2A9ei[")#!*j.XNTmBfnE;61,!>_i^d(';case"pt":return'(]^)]6KZ+%fPWfnOn_g]u%%40/o:+%~To8^N:[PI@rz&XDsb!dR(ILh]tP;N8#Rf!UjuCcic84.ZXPEN4Q0L]QZszn#K[L|v4MFCmgOvQL]RL66keij]rtx`BQecSid=(>:E*v&X%cz!2J5K2oZ#P:ek+$?,0,8$V]pXJyA>@>
.%vKwNDwoEJf
?vanLV^ceON-$8ymB3X86NHD}!05K"0W3t=b-6-XHP3JBQ1G<z!iJtPvHL5knYsoz/z08P~2auGGvq(,8alrMswiDfJD*FPMQx/1CoRy$lf?4"FDwXe0])6ogXKp+I|8X#Bf{0FC}w/p{75.o9ks
<7_I1P.li5uwA~6O$8nmcyZ8HTD2[hs.QMB`G{)"t:[rg=?ofvto&POE=o^.,NgkfhiX_?1*")KF6Aw7UI:QAeYc*L>-l>l]+SEM""(G%0@8.!pbsH,p>QCDM^PY+uZy?}5ceyHW/nt,*9*I[sTb5."13TS]2YsS+7!e<X;)3Z!_,u,KVi6n"`4@yN6jNBe/8B/=ktNp`PGNv|w^85HH-1OeinXK?=#tt&FhQg]KAgi
u3?/Ly+AMhR$7r-l*K1@Q4&+6!8K:_hU_*Wt_>q-&-=9dbAC@9hJb,gdN/[a@k"T4hHHE^f0Y;ISSb+0$!G,a8xQeig
<p/T#g"9oTrRXn.7wv&W/G*)UAE)cx!Fm{c:GjQ#,/q[c(T:PdswDq=sb>8bR:^*8{Ab8<>}C.Oz-(Lypf%2@7Z7P)wLyf]zg]7T]mPoH?@&)kL#81r/EjTKfro-"@#W&|h;D]G3Mq-QlEjO_i
i<@aDM`c%<Gq8I+Y26)=p!PU|EpI!28.2"aj8$pUTe(K~@`(WfZC]Yd
SK30,suL%4wNo-@4:kaq!&u-g:h#d;7Y9=+:Nt*T(C;1%B|".5tV|Q5<S@zwiZIS|PCZ-40ZwU:%I?Xz)XbOjF(/pHmxQsNWtuT`hE3^Jq$6`a$s&?Oq|H
E8NerSV)AeOyN5s>(}GS_)f%djPx/qu;u3dj-s@^C=KA
Q>uDpy2(CHWxYK%"QtHaUy+ZT/d?SBpZ6j)hJtWR"feg?S@LC=W-h7V]>#eV>DHac+SfES^hrQ8iiZ@rgbL+@
#X_*xp/y@UOIM3{fJ7JVY,}&8d=Awt;)S&4
&Sm^X0-^Q/{i
AFozb$w4ws4BX7Abb-`?&z+,<B-y;z,>Y
;5*i0~;`8YD.4jC^yrUF$OIw8tlYB[D>sly"7k""X-N<]Lw>maNpI|)hAcx=68!xc/+4BoMP9d-1?U8f[h;d3|I^TTi}G?>?J;uhe+km%
y+/#+{I>I1v)wBQ_vvjLph!@rM
$*wN.7Q1+A9%3WI`#H5ppeA4di1b10nU!4XsCv^3ed3+~34%p[]*{5nfk?{Tws+mr7z
+84wUy.L;EF,u3`WC_@i,VM[won@N<awHK@ekKfcs9v@p!uq}j^r}iO-,COZe94wrr{*KTn3x</s5!e[sfzb_83iJH"^=fAE!g"`ejM%ORfPa/B20;@X~O+lTAROG3:GT
qc]+d+IuDsQxp)q[>Ta?u@yUE*}yStT_DcrC^qp1Vr8Pk=>8m&`6Xs?T&_kR"*k/R257C`};VH[l@j`P]Z?A2wwpBU1dw,Uy8T&pY<c"LQDP/3egsD;7zmqhFgFbaMu%Jc{h5pXMVr"Mfvr*K<*>[7f&UnmS=S67?tt=}#gw3[Ra)SmEmah7I3273LzO9uJa!VR^Q9Z0|WAqKb@w(d]$j>GBgo>1v%zStBTcE';case"pt-br":return',]^*?bT-d#?EBlL=7`e4hN=1t#uR*^i3V#!wSOzbXCZ+TCCn$e>Xf>~e@?E`8?
R{`[thcun`[Avg^.(9S]KUhj]*JIMac1ujdleeIq20ON$Rp6x!P@f5d%PmMB*}YU`T$<uFn.GF#C@bn7,7vc"gq4M.nH1%#:P)IbMtmtZhU,Oxu`yN0gq|)j;Zvyx*VvAGdj>yD(tEaO.bEp;b_z.S!{&G`jfq#T`34r:0e/-Q=P[
n}ni_/W~-hnqXtD=cPFSHnd;^=7
AOKS0Q4epro[.$sb9_-l!{eRdNc>9<q`6MVI_|)Yk;Zutyc|ernks|9X0wI%_GT*
wqn<AP!^$uOGT*G2n(4]leV=<vaKNBIpuM1"4uno~i~h`En?AiwtGsAu4.2N,E/,@eqeMlGNBMk0c8!`)dMG-SEIi/co9(jnlxoHNGx5nYSK=%n+?/Ro*j!nJ.Gc;#$W$s+26=IFP^Cn:%L:Y84V9okHm#T-1%c&<hR3~Ll#YOv
!N$Vd5SSc0;Kdt_%i"Er_E,G%=0)c4Cuc8{ZV4NORTY?W+Fmxm^S*&ec0=oq;n.BmgDQ2$9(S,Fi~m!JbOKpoW1/2O*fFT9D<>0+#!n_v]c_mOAf&nSA="#H&G`K$W.30u(3uw~;bw~AFI+Uj":m#/^2FECmAtrxk[A2gmCTqF
c
$KqmI%=Fe`:iApb]l(J#*Nm
+jG4ydZ+q;UU$oqQ>X?BiYCQ@<`Xn/5]i}Z!`YwZ6ecxdea(nc#]HHrnObQ~Zi^Sb5frw}v@xES-QF3>M$=SPfQ31Y>c4v#p%=N
Y:*@Dx?>g6$N09CF*W=(k&8dM#QCu9yWeM+yTAY=i$(1J`$v/+Y_L#Q2^&6&7oO-u2
RbufdR7OX!`[mQ|,:GC;2fq8Hj6E9q/?z;{X:D2gAyi5wz&,j5m)9(cFu/1e&V8xtV@ar>Nr-%v@QYHx#2:(.j&/uM>lo`M?EG9lR
1;T#6O4OM40<`!RCgcs8shtkdpM:@Qf<((m:CvU"=
O5iX"j
E6A8^pR<4R<w0#MjW6="_Ghbv2oN`NK|dDYUs`*z,lb9MTCX&vR
(fl$14KsZF>i
:K*x}jq(ja^4g-z^~aA1`44ClNT8jMedc"B^;0!F`1>D[!9cKySR,kjGPM`&dH{:+g1H57"hgT$(yZA]Elt
dWsqb7_U2>S9#?swr"$>yAoidUJ2,Go=AO/9w,2M/xQWk`ARU`soqMx*HN*qEuEx-ArYO.nxM9UP=*lR{)%:?K1.zQBc!MG$O(Y:pw,3^2:*<nb&*?]eF+mnn+et[!bN(wW+W7>-%vndeK%C?QB3sy2
e#Gn;3Q;ytEta&-&@E2/rDK$I2w.Vf[EXXF<8hyB)nP%X_Hn0-qKT_pRTO%0.*M1>>"3NWd_i3?:2ZEM
<?[Redu=GS3&L/9{[_LJ;Khb
8h
.2m1fZp0uBAW_yr$6&_7gTmMr"F/,(iy4hte)&^,u5&T<P0QC]?vT0y?jD&Gk:bo1pa;XW)7_v"-/RHhW~!z@+NcF
ogBSy9X&<x@}W.Z!7qRSX@iR^+&_5_H8IzYpF8:}a};T#H"1Z2Oc6K_<fG1&,wPL$>mtuSYB[D]^68m4^*#lK)6!MS;,`fZ1y<G`A1@50L1"EMxz#APjVPW<-WZ"-r&L/-U>+fSo.`g/-~cb6,//eVOBdX@$VU4a8k7!KqBJ@7YjX=`MJU,*Qx$l4+/"rB=}Yc(~v$o)';case"sk":return'.]^$^7s-d%&^EthTs*]B&85!U*>)[%JGN`9N8137-F;Rfq`<{,ebF9fd0*sVn8TWm$l%tZ]`;a^o;nH^3GjpFHT5TM<5it5kWr
b7spWZt3T@/g,9jXlb(RfIrZ5Gg/
5&^t)cTqs6_C@EdW"oVy!VpHWh`dW/opj)u51er_.x{H~LC<!gL]ra0OpQk/wXV&70bIjQms^:>w}"Fvk],1i7]Xme5]+iqq8oMm]W(EB`cvs,C#x9~$gs&e@z)&aJy+kR9^a;|U)]
sWm&U-X;HX!,+S
O</OZT)ygEG):fV,1P[m&q9?/9&dBK=p3r}QQ&df^sWETbpCcd{ovx
fW_#XnXKSGAl*@f599`TGk=/tEl+-rASAYJ1Dx)vhu>G^([0NGR:A,s8t=
^2Gi3D`@{O|gix^L1B;jQ%G;qZMI6k9r`$es[d{g;eUc_6R]DuV`alp*6K,B!7cvRs%[hBv"*C!ikbZAKo>a1!X]W&zCNrL!R,$]r:87d:9?>eVBF#-_du)%K?~4Oelb0VjQoxFBdw!wCVO${"/0iZpf#ayj}2-)Ee0CLxHn?Q5_yuGV+F4Z-D0irAcLAKY1a!,I>tw$*um?72v,Z=YFj&(6;[7QJw*/_tO(t?^Bllb&H.V)Vq9*jF.d,loHS%qV-1ZH^eSwS#;XBp`b%*FVa[Byx,zv_9&ZzZdE)DYEq
8;f[zE[8?/l5OA*R~Na"dQ7u<s[_U-$hie:9DfV9"O9GQ8eFRYl&evPd&s;,lOT_LeTw&xZKTkpf,
=NVVwevOe*@Z@d6J,==M4,:/5Y?J"D}pGid<E
}N;2~5kLJ[I]w3@)MwVwWMzu^le(
$wGyT#;[]Ml<!&rbS[r;sUs3i-TiP.<(G!ZB<p5xCHN|0`1Tk_6G]wGX)N!YHK(ijc&=%)"}v-CSW-b?.D=Jot0t(>5&_?NbtZuEri)t/mMN<In}?16~g=8}e:(TtL@FA^@3Q@/B-c&<)eX"&cne/IWZ>j.U8<o=-Yj9?o
B&ePgq4e=>>/d@bu)]:2lHLC>R=$
:%rnY2RLr(CU6
6!Tih8J|#*xg33ank6bQ,ChAa@fkehYb:Y"^qd>0pH;="RGqguSIWY=H:DK.o)X^?e
r)n&beCZ:wS;3sHz#z(AsO+"N8XoYp*<C<s3sbP=rMBy~ukINsmCD"PL
r0;m;93-#I)oG(#dhL49GS.^iaAiQi,9pCcR_xU>jLx.,h(x
y/r8Tkc1+4N_qPmxyD=t"C,@>X79Z%-%;#(5Ei:([m0]tPv!@(`l%G]7CcSc-p94^N[6iy,_Yi<oela.,t1kj]w=WqS1C4>"AZ}xV2l4/[e0@g&s~2:b%*V.!t!Ur06"^OI9+v{.k6;q9dy
4kn*<*[d-tuA=f~J1GUa(f7W+(0=n#1>;Jh[1H"a&-(!DI6Ol0q!8o8V0tbO79[Fvr4!tn"]:0.;$QINv*MMf?@Mos
RaE`Fo5GOjHyt:"FTm)4*8(^+@XrDZgG49$hqAh;;Z&Jl"G&?k*+)#Q
_u;
&65`e#GIu6?<DafffDqsvI,Mj+T;y]X7){sao}-+
UCo)NdN,gPia`LP7f&<
k(dk=&Ym7s$9pn*DcFB_MbDwJ/{>kNYoUrX
QA=^jEBFZ3w_WIV(#(2uMQ3LWub&&9w<VVnCAmY)w#gA-2<m#EY-Bq=?kR*T7mv,mP4D=V_Gd<8f{(6^";5r8J^+XD!GCjdoTPig>wyWW8M=70"l#%<bMPaL6AJ._XGNB/
$.8P@IE1QEt>2J;9",.At2Fxm2V5!ao@vq0FS<"5w
16FO(|C!Dkn3ZTYD38c+hY`O^r.X]Z?|_,niwylJKquc@Z,`eC_UgX&Mx`kLl:-(8%)dYvj$s[6V"&7y"m-81I&?9vy,m`3TC/U&N`iUPd=3+fT`5H7OT2Nr4TDnMCH7n}cq:H-2U%uBF$`lJKv2yrb53zq3<j"^PpMHeN72G.xe""';case"sl":return'+Z}$mbPWB$u^KQ
[]37_;R&=|^ZmC&NH>#Sbo)
V-N~9jplud:y;vZ4Nc:Y=fu{mGpzuAL2HDe^jhEH`%*,gC0~5IFLz)bknD3LLa5Bi+6IbPL6@4lgm
sbiHov*5Z)9_5ym[+8ZIyiZhr[;u`6CAGB$+LiYnKNWGD}7H<9toq:s3b%yZfhCBjGG95ljV)8hB1*AXkUi$DZIHanmn4$&WP3H!NXnj6Yz!vzqk)u%;(rs:O4w~
A
1Ae#st/kX?7n4<9rJ,7q&){4WS/NW,]xyFAdUr%f93$*WW}?AGW<9ipXthyglJhCCR5Veq%Bg/jf4)
tDvO@bS
ZFy_%~uCEs;34O[lrO?.+nv=KLEe?_LBP-lS[EmN2J/C24mZ#91~>kZYO*MAa0M1
ZFNe3V*"+aXb
D/9LxqQ69;,yZZ7-*%++Vr<j/TvWQ^yfPB6_&zdz2b&+5zTzr+Q0Ga@_t}G6m(["
Ow@DM_(D!yML[SQl$20;BddO2+02vsB]0!M)iHu+(_ik?4URa3-n{h1,|TCDtmf
o3kI1=L@z6tTO0RI(,m<Q3$sp%*3-27eaHQ#buF@(wS_/Y4&IQ{f/_Gu$jul!EpX`&
vAYV7+iLR=5zqI70R#J@3A.s)oNX3|@jv_o&P}Qse%IXH3R1OKQKoC_2*AXwjsc)g{T8EK1}1D
naFemyh[Re}_UoTkwU
#wh0nQ2E@kXnUXm3#l8~FOqlZJW5MHM+iYiXe^9!
eZ?4>Vp`Jo._24/o
uy<^LGcpGa%C5T6(E[/[sXQ%9BO.)/m2Elj:Rq*c
o7J%2V(hbbM/xEk*%HU:~s5U?R*78#oBs_t81!yGux+aR7C*/c;"Or:r/[D^J
ma)3e$_/7o-Pz9yDBUpV$YD.<3,Qn1S.H0!:BP(Z"i*jf$Bp7a4Y,=`wW;pc=/Y5vxK;N
:L$U-+tCuXaQ!jVNU$&^@:fmmS@n2y7kllKFa$hs
%Fx~A?SaIb&,yP1L&7EGE"uUpVDNnv:XS`8zC,/ASm&hdNgAa|88Y0E]Db.|>,1}3?@3K_LE"Z%
d@17[1Ln]2BvxbX;-wTfNg8*n;4V+vMJy<vuGyhPDE+*:2XPX(HT1m9<j]QpkYUz8p"qJl>X#AH`E(,cfdUSn-B?0}YH#sML3v&^uQ;L8epVC=(GW>Avc8692(JX3BEw=m40.b*%D+%.-%I=L)h9
HQTd~qo8V5%8p8_(ATH1vKp6%(qa(W+mE6@[)s.<dZ(TxJ*Y;naP@V)D("U<!9%FP9}uL5gJG!Xb@s#pm:8U
F)Co!l`FVjost%&6#(S3=3dZ-Hh{D$pnvT9=P/MDXWWHWr
?9DTq>-4m%s]{=jSIifHzv8)+J"MP=q>_:U:A@-w3ZD/%%RHvSMTFvF=(b~95P/p]j:]XNG;?Oa0B?L$MUaP)59Vwm+MRA#)D0Z7v30,u5@9#2MJ:`/yb4:X*
G.qUsZl62TFAQVxxi
Ts^g?3;GlQqk&U?1`@VJnij&bcFUG<tX-3NjHRx?(/~:?w!4T5UrGEVH}`Ul
iWF33v_`hxuruXa(a%X}c7KtRpI]jfaBb()mOStVV]9@YafX0lW8[heWIuJz]>>FWOX:+?F(a:H_vgi0Ze3Wx?#J?Mb
9<6y@<ugSK$j($1"kf2OYZNmV]7+ZCAi_~&qJu1A
?4AKUSj>~qaep+lej*
Fcoq*o)kC/
$VhG12=EEZ)A:iw7`DbQPU"^$fj^}bM`vx9>0d<p2FuR/aTNpeop6G**#rXt=%6r,OQ"l=r)M;b3yw3<eq<tL?`UGGOwWtX';case"fi":return'+X7*p/vZ;:u^Q
K9Oc(CJ%oD1b:#u::VQ/)DMS}V>>J
zkwC|4VP$7Rc
H:#cZcorcSJH^(VON_`mQ)b9@7_vi>X[K*2NqUV#Lw(epiE]K6aS=4:zdj2VB+Dn8d5^q*VVwx(fLwurG~"um1vT6DrJs#XXwHK2L8Kqpx0<_vEopRt
QeGATuDcLpQ"`CHbF&X3V2bxw%2EUy)V`G&1BaM>Mbz)U4NEt]^Y?V$W^xDGtlxr@WT(norFkG(aGw+{(?v;>n7[bhFK>jUnRgJ%*Ze[(*4OoE$tXCi7:r9@QI/KZOD<<NZ|/
i8+Vw,[ApZoP9:TWukh6e$k.prM%*AIRd5Pd7_y3B}=bx6IySnJTYqx
y>.smd55N!S@98sCv.LfWAtUSn<<-N4_j2U7h{o9oZ?(2.1i>E6Z;91wW^4YowIa>"53z%phpk6`;d/L&h5`O`5;]f/V]$uGaKZEA-)<&F(;dNjwTVLPu`a`L$PBi=2k&n>,]_C!)coHvJ)7!2aCkdh+-p"gv<k_T+^}.lIhgdl
fU2;4/g9QRS#N+Bud}$N<Y"*!(i&D209EuE0,d[I[!qEff9,#F4-^:E]s-7X,r>C^POb32hXKp/1
}6:eC1gJ-d,"fwtmuFDlyb)l%El_m4$lW03*!D&X~qHaN7fc*ZEVt*Sf=(M-iO++&VxJXHPWeLvc(xJf)aIAf7r2T8*N0$0i"tuj&RKUr[QZRe>LvX9u(fXNWYf5(SPJvy=HKr1w0TGX"wGT=^yGT.WJwdgV,9mB:"1#;
dMKG%jAA,];#;ur8^Cx
KG=mJpkxb0cM+3mnpwEHMAS8Xmm8~t?W3T]@v^X:7a]4_Niy}ZCZ.d|2~KTu<GI?@1ZGqeURC<v+[ppq
C&.ZgQ6%6u-$hQ!bOI.#$op)j*o2P2V2%xgH%R`UQPQ-mmHLV:h-J;n8;u3"k)b>aVH~5Bew+YRow<#oB,OBB2aV]7^?UB$|?D9(B%_~El>z
c_!?#koeM5s0h&}fF""+q&MD*CbT2LC-5@8Pxn?SfhCEA4)_os/"T7;@~s;)(S`aP]E&gD0.{9|FD.zPg9iPMINW(ia0q"SHY99Qu/.MTpz#ZXg:ra$^f1b9h:`,eB>
QPws&H?"obHj4BBbj0chj&rg.=6VnvaK|u,XbRepBh]@aojn^NR]7c]O:@t7I?#cdvBcRKkQ18^N_?4kHjD^S2A-aSjo1g*7M<(oJ$=rsANJ7yZyzT&=BXe&MY"#BpyJE3{4=3w35%9
87^t2#5!AG}0M#_kZ&oYuQzH*8oD2
k^8,6)GC#,pu{$oWS%u]dY@dX0YUzB$B.&YT/_Z`G:f%?a,"hl%&%SqA:-:pNmVr(;g;VjK&sU&wKKEuQKVMS_=l
ncHu#OP(Jmq!kX>dAJu&4UKYNa^OI31si]Q1c3Y}n
r0h"Un(
?|(14HRxC$:?"&Ryw;W[dh0d;ZS3LRBV%krnjovo?veaZ;5m?r9<sAGGoE2QRUO6/-uCi[lFndB{t@bQJ*Ks`7RAR2gW4>f|`d!WkM/^N/#0Y.1o6RA[JXH!*F>Qf,tsZpsi0yk)?ATjqsK#.o<cB_*yr-7k?q9wR>".ZQ+Vib^+$|BDk@[P.D603G3%*}4i*sZE>YO,;p-/F`i5gjr)&z3z!8N^fn0bl1=TuYy-p$+8?go6xHvKaWb(XTy4(5vJ)q`uX@m))n$Z,a@:EmP0WG8]nvhJ9IQ=<.(E>kT!$kCfW/hr3Z12GG[!Y?U{RV]6m:W+6,DlO]^u.S_dmN,zyodi@BA)+-HbS|Oo;yac.rAX(#:HA[?AL;63=D02gYN|(f[[4"pk]tf"4EQAJxc=jC>?^?a)rAb[><Q8*~bE85n$#,k*#P;wyVh&D|8u,``Yi-x|ncu^tX';case"sv":return')Zu*gaLWR:$iFVN`e]u>6b;U0gZ&.hmN6*50XJO,".P(@pl$U_B!OOxS_mZ[0xfM}.mc$g)b/iW(`]A6Zm|MoxGW_oZX/U4+nx?+O)LY%(E-a[$F$enq*K=`4LGrm<tLMaM?;C-i4.%a"sxG8,_n~R`+P?Qyqntmj%}IYN.;I7F&~c
CJ/S0AEXkcC*7-vr9iO$O@5XWYT9NcX}w>y+bbK
"]^YH5sSS8=<ch2&:+
S1mPX)9:<4V6!pJc{WvJar/`y.[`2Oy.0(lj.2GvSgnf+
Qnubf_!Vz1;^fu[46`}%g6Y;60xY8S_Mw$7.I4py~%WAtXaY_<
%@aKD3bi<Jw:d<)TYUWe4cp+@_jw/D>LRpXya^oz[2Hz;=1eKC7c)@G"V8,?@Ct(V,K:f7,WNt>]WFvP"fdW@cBk-T<|$oC*y29~-0n^bAq?T5r`Mx><.UAS$Xd,<P^"7y!Rd,g>Tdq(
WfCV1g,WdMp.tD&d7rJ%G.)TkC5&6/hgLt`@Pe?>MI+e{N|U.Sq)O/;yJ)D[Zf.=8?<Z*2]&Fv
Q2OJGO
ISx.MdUq4`pU{HJ1JF[_SyQgz"2d
c`PY4c0%ii((
<I7#/)E`|vo<7d(W"/YP
vB-.bv*/g<:ZJ^dz)_X.%e;QRzZN*M[3,g5@WC[Pv2k%**`=]2`iU1ZRQQrM"<M(Xs*sQ4t`N%9?$^R5<&jZ-C@w-*0/Ht+CJn-.
V?3+s"mj3cl%JukFN>9Sk!N>gZ+KAYI=0S{43Pn@l*^_z9jv|
pb^eCbul^0/=.RY$7p=ks4P!nT
e).hJZ)E83b=lQSCoG)`)qDnjmsd(r-$v<<keZIP7w/_"<lB
dph619jj;km$cM[/5.%ru6GTDw?a$PXq2Qa"*JlCJO~>K:*ORNT@Snk.Gm#16[Nl#h-X-iY02w~MZ/=i$40iaFXwjNLTDA6(K4~T9g`8|`4#aY?c!OsUU,wH~=CEOn*C:THTQanfPR$/%Qwz&r}UEbT2W.Eou!{`M#RNSA]L47}48Y%
[?hd
$ZqSiE"3Wa=]E~G607v/P+>NC55|>>uFa*OGoztyg=52jlb4
I
B"cP6y?fH7Kk6<U
Z:mNe`|KZ1[tMcBxlaiwU3Oh5I9AP;>u7Cuh`8mpT
M3]POi%MK%s,DbGa/1`g,Rig}oH3248#v_<>j^@]$4,V*N?]h>P.RM),ZQOxd"IZkm%eKn&NtyU4jD%e";I35-meST@!,6q8`IMDnSR.Po>;Eyz0EH.!}u>Qi?U24PSPT3h:qTxIs<y6pCHo(U7:&fR*B0<WoIo1z:>0FNcGd={ggTkX%-xp)[A"E$Xo"m5R}NZ=xyMB<F>^C"7W|9ZwfPLMQAa#Q+hB(hr$Vq/fP@Y>BF<:p5lpS&!
SOoCU>VetY04(tfx<&v)-.E
MpU=D`|+dy#?{Lrc8kr!@dp.@_Bia!xRaQN^^gq6M4E:!1FX32.gQ:9)-:/I;3e<eHLnfESQ
>qTlQ;]N"FU,rb,tTxO04X`EMeL2<!b|LVd@C}T2IY?M5oH58{Q-/,C.!):4TaH6^$[U8rP.PqM?nJe,kUXEVg!uNpQWgUUDPq5^dl@]>U1TFFe+T5?42xIct>F~X`"1y=:@CpDXxG.lMhIj7J=QViFNDgwba2#1P]0T.H9^#cB&R2Wiio23TY6dR.YBiS#E';case"vi":return'%X//n]=1@&+tCnF5R(5)f^eLZN)%u1sOLLSXT_gqI:`<BB@8@e{DxPx-g9PVL-DNW-Okk2>EF/wHM-"Yfl}vKr0yYG=%zHz=Gs*=<?Hcd?CaQx;>gCc@$r-m"mWvD_"4#p!4h2(([BtB]Ugc,9/1,J)CA=+Cz@GDWjkgwF,mT6Dw?G>Hzap12^|_!1#5Tn3]BC]dEcAR}uGo]&Y$t7$6MZR/YK..{`GAsAWF1^O-^(l^U+:SR;|WeOo;V5D&UO/GW7)f<bNrK%3P5m]*1
.`mZQ
_mBU{tIW7?~q7ExTmN(J/kQ"]5@B}m<OQbbWX^z/_"n6=UGR?gt/MIO"Gh9_kI:(a5caA9I%+4{K]Zcb[PY#HEQ8p#bG6hOs^Fl:Nvj*;aV4Q6*c?<^MwsPyi>kvY5{,+Nwa[D5PK,[Or*tx4RjWoqb<|y~ISLexz*!.,[S]HLc^os"75HCmt&1lNX1fIf43|p=C=p35<3E9O+XT/N2eO/-/7!Rx(l{v
ZuZ{1Z7HCUE&#0:NLxi4J;q6qP1y*ob;ZAY$NPX%e4<.8pA|]w2!NP4[b19mXywf72aVD]f=iXg+%omUOrO)5>mE>U"aaWa0(<5zZxO!$It{G;0MX<.n#@*cF!tRLA
R(K7?9k7qy.Jh4[xkr`a9.[(tTv?eT4MC/*K#9GY`bK2Uh7]Wm:g6vMKGem;*S,R;Sm6aqxjc.wJ,hb6)hNj]WGa$`$*.(;,@pkGufBytL[qF@mlQ"mb?bm:uu<><N&L`X396#wv,*x*()S,^QYbg6"HS5xbGMADd2^K8Wf8E<#jvT}e@K=k6*NbhQDg"N-x$o]BC8"$T_Mc"dmJ^Worwky`2Jld5!`v"c`UH+OL2jfoWSUYW>=CE9BWcH-B,xGYG#q2xSD<CmFnt#Y$M$$"ga2mk,%]MNm6yrvawE/wn1z"
!bLq(~IoBjvW%_m.K<?ABr(xvzVjH&f%(}
!U9`PbLnDB$?-F@BPQoac`FeqTc!{%D>AQ=Iy,uXKP%SfQ_V$m?4l(CniDEX$<Lp}UOKa1%sEV3y-Y"#Q4WmOV9"LX]o>@P(heXJG%Sh6xE7W-yfIa?/9$#IxG/-78dD_2@Wz7{56p"P`N[PD;^GsS-59$}S^=K(ybe
nCAdta$7
:dG~3u[V9MZ#a3L=>e-+/hM/G6HQtrC
^peUVn=5(v6:^V/3NFkyF9rmK(vtlSoPP3YcAA4[F1dm7{@Tr1+G6<112w-wtqMbF~CGBHOfH
f#F;nR5"B~^ZbAdSn#8BQ/b4W8^
k.c+-G4JpogT8TJ7lm0)>Kr|AN9&E]<-Eg)vb"R5q8;?*2GeI:lU&*v(xOfxBNKuhga8(!HI:%fjh|7m
>0/gd0HiDAAx:nZ@l
LlH*Joj7%:@
hjp4IsG^x0W)}OHLd_.1%t)<f?h-^ZPyyXvdEZG,C;+#!6Qv;Av$L>y$0<W_!Sr.f4",=sSDYGnj86=8nug1(5O%+ptCDIJ.TqcGU0KN+%,UOM=iuB2,k1U^xHC%`i&cgwQ*6Ml)Vh|Oik3dsIPZS!tQo4R6":mo?#B`sK!m$2n5C,a-Oq{M69PM;ITt`2<KPcO`2*J&qxNySd2o`@r9&3gbIL>(Inu;.O]^-u#6SBBM-GB`
W+#2#%2P`[=AfXUE4yxLo[</md!$<#a|@kV4j5t4VG0&vA-hQ,/Q3fB9tvVySJA>SsySo<d6MMxhnVD$ZwS<J(#[q-<SAbVG=N&le/e$t<Z~DO2!1CV|]8$;G-.@EY2GDuhzno<&[}ptYASbpg9S=F%K[(X"Z+iO`4317:/Y-Nljpa6kZ2jLU6l7Cr
S&_!#X[H~^$D((*,QInv=6U7=]:#G!a*,N*9,&}O}i.=#sqOQgK%}r./b*r3`QuUJ0PW41ay$J;;@*p@u[%FAH!=4Cip!mA^s,^^iD"pqV"=-p?a;ZW</&kPd';case"tr":return'!UF%@crZ+&)qDeP9J`l$lN
r*2pA:%NEl*!"&(vadg#?wGPoYUgN)fux*MTj$n5QX5%C#^MqAo{U(DH.6CvLRJhi.XkhWLTll>n`]LgyBS1,KSnboPHob^B]62Z.UfL
u%Y8`-x
Sc]@!)M7^^ox-P;opyt!/
.i,NY15XfZp)8NcG4cHcX$;uL?^YQo!q(!fs,1SZiq_v6h(FaDc*}9:H6ePMhbr0}y48aE^35F9v0B!H4;WyR
I`Lo86+vkEBq*HqmU^DCp
}<V3W,XMeR97Y;_"#)bMp!$#uBe6OBy^c<-W`HnQ5@.,bYb,,3kJh4>Ms@q@b<lrqQ~UAcf*7eIXd`fq%wu/cYEX{`[0m)([}G$l/T=FT;!9o1hZ#J;vP7!Y77Cn8%{-[S~I?*/>+C5;k7y.+"(kGVrrri.85%Qai+;p!yx;N[
^x8p$OGRS?3?Rkt&)S<BUi9)jT2S0n.cWpI}raYCaA:qfow}@s-nLfn-")P-wcJ^,
`/Z=Z1pt(_0pI]fnj/6*JV(lQOH];"^A([
.SmqNY:dR.
mEE`V!:0LRRC]{V?xG`8JTr;ml/+#S+`O],Aq9+@,+(Rh>6rAh%wlz$kYl+CE9ASV&Y"4#Nf"5[+E07G6mj
6OOsOwj$(EJ[LCkOEHdv,Vvz:}4,4%B*j8"-2%[I-C?]`JgC_*y7+7f/Z{ND
fT4R4J8a@VUBA_jIugVh.gT
"]<R#tqG!
4"9)Y%$w_&Gw]J8#.nnXHG-k7F%5,D^5%Y|`%QfQRN}t#)9j.r]QT(v!A=~h6CS>3I,iHJ.l_^+?zygR>ORN$9Sy+<z@sJQl:[+`Y>888
5rly}Pd;#9("seh>S!|/zE#TJB?m@](N(=~H$RV)y
oq4TR1#qG#{4+X
a_peq_8yR>gUiX5[tHT%CpTW4XWbp.^cUSO3"#G=<G1;
(C>n^V@,)xmOFm}^tnT+q1g+@DI`5<RV!BE:if!5[fMd;7A=+dio`;%3`=Z%}#$Zk_*JK#+3$9&?5HsIw-T?l6@+%s"P(2J9<@xTp[CR#_OO]`.
FNV#|29U^#@+!60rUu^Nc#,Ouk#T>(aVg6"QHlkBJb"8VI)6tUG`iSakh.`SjpBAp10BW!=V=
CTL!
Mpq`wT1^h-0t+/]nBn%BI8h4^8v#_Wm#O3g`f9y&B^V7r}uxNrI@vPw|h9,IQQm/uH4GTBCNtYKCX4u=CQTbCO0v09NrHW524HI3s5:~$0gXZAk`u+tC%Rl;]t9)TgR|e%rof9"f/ibW[/bETm$M)YoClv!+QeI.[K/`#hQc/n=2bic~L>5_*Km/hVR7h8F
%s.Sk`hG$.0%0.(_iW7NVdS6Xp5V0g0j8~%AiP?=P*"|061W7&c_Y*B3-!Er&eeNuE[L$="hQ1u|@m`[-lZ,(zo?CnN]*pU1,!BtUhpdBB[_Q}oU3!(
6MnaS]=3055!Z/#I^w5&[
Df[Y5aHPI5ri9}::HG(BF&m9lS=uP-SvVBK0[(:oZ<`c*k:BT=<dnVyZmGdL9L&d?Hqry`ckq4fYI
^R:AF
K"4=u
"~jHXlV#qO
J;gGkGd$}fLf0y;!bOCCiI-h6ni;IPf5b3,ADykQ
ZN82TZ8<GaE0,JJGU.g,Kh<d/q8O0am84gT
>fv,&^@{"IntKSsT2nT.Z)nuMPi_K60E3N[@Kf+&XT,]TBH4-vf)rs7NhKkeT|2O3cF<x"R)-O#lG5T`DACp1]s"fX-%kf=ni*@^j98)9XC(ms%iQNR.>Bvj#R16,,<7r2m(0+9$
0e"raD[0HAIv"DS>#L%s-$YQYA%=nR"G>1?mjO3tNRgf[w6TQ)KAIG`^"H+mL=CN,>^U7._.i+8f0C*ecx=x-=U4Q:8NJ,|_i"Lw>$H';case"bg":return'*ev%8bP.!:"vq^b/jH@N-.o1M8dNyZRBNim<AZzTS-=4x9qgZ"!wPOO/@ZG5EhYLpTlUDKxltX&,$P,8(F!&In{x^ntp9s,HFH1eun@/BMItNh"q54;1!FIaIjPb/AzL=oOni;i!*r~n)w+b{A[<S]R3b]#rW97]L]1$"E}bEmjo(Rr*?sR)Q67JzU-ehv:tC9Y_Tqf#+`:o]d5yVH]sd!^*?BBNHL?cACVuir;nMl)a7gY4s&FoRBKQM2V%<59?1f!g*SXA"25$S^es#&)]IB5n14Odf8:Y=Y1F85<:F;Lq>j/G5bRGq2&asJE91KATS8Z:NAx9py:1Jm"JQ;>a"HQam4^K64QCI[!rsDmJ%mkm3F&]@UD7+mK52J$1#HJex<)s!&|w8$4A*HkTygjR(scbRTEUG#E<|JB+9Mf_P(S)
I)b/#{w"[TkzO2F&3I1{#/?Wf)=SF5S87UjA)py(yXqfDODN1"!ib(yI("DpQ4wd7m62kgxd@AG!EK&%I?y8?cD+DmPW7eX19TwxIE^?IxxN$[qT&|/.ysLIKdj#0<oiu*IyLDFiTB)J,)vED2)BW_HW[&a;G.iMh[_t;D^-)}BRhP,-EZ+V8Jeq+dv!MsjsAdd,oMLUxTDYK-ioIM?m9ma>!uvI<PuIeQ(M9Lcv4v:hC)w!,T+B6^<8%jqa:$h2$p4}r9m6-:x;iTTP$Cets;M)mV1t]r4]_SEf!QeslaL,g]-G."1;--],n-w
NC+rBHrV=DiMSC[2m<b8N]xDAuNv%n,nN1C/>^HmA{AyFau7las>vy9]<6gNMm=I>od%<#84E:gi[)@Wr3"c_5=v%t$#13B02SX1dFMpGxNUTnYM[4EmjBll_z=~A)4|N=/Fi%W}OYu7t|==XNG0k)n7F|a=1*e(d2AyqqUnl,qwhKNcP#qmqWLt.Z+
k%-ux@oOJ~<*Sr.k[|gt4SG9QNM`xFENq
QH&)4wC_2OMBp.h*!67_/bu6Ja,S5s).<*CcwmNVJfiSbcC74[/P0^JsD8m;FC!<*DS}agDd3s)>b?QL_/$jwZAfyPWSv6dIuS[ofkx7iEKmo5^j"KRhY.i}-iZr2974_:TK_jn9%<-"<tK,8/7OTW_U.=RKW!XkSY>f:TLXEOY_P8%t:>wEveqvWaQlX":LR^5da?6*Zf#%,X[{v!hR&r@[hvfm`R-Ydw%60U!*1JU?boPLr89DAX,eCRlHX*ZIQ5*^u!_E/CH^c`u&;u"k/aCDyvIC@^GU`dI-xc9KJ.5NDw2fn}^F$3Q#)K_&IT$4[=47Hv^L6S/#PEoq#hP%v`o5y#BnBn5JXk%:@qh8IR"Jp0I:Az*WrrCS0])Y3M%6;wHx,IblV.S_So.siNgd)[[Cv+=R;^:}w
!-s2Q<fm^@rEg/d0!ZpmW-B=`p>S=pq|x%AtnY*N^(VZ0>jkni82Lvgx?$;/3DM[6r06nHCi;KNe+xymY*+-FkOnYlH?OAkgO:^z5fKoKqb6yYj$HqSyUsg;wlg3wzF2/d<(rokAOaX}x}Ox/4@(+YNMJfd6#e5:8z$Zk"@s"DD{8Q>6ot#]jUZv4*)[8i<b%&XHU/
Jf<55E@0@Uz0.F{3Eg)xVTA1nXC2?iADUm[E6k7kN_(9&7#)
RAP_YVZ1U(bE=(IO`mj&_KT1.n-qDU;yJ
`Ej/n-gDbYW9
rM*xZ$os<q5rXv!hKQ7aWS5tH2V@Gs>C;&XJGY9kMmegUe=M:4:XXjT(qk!1CRql;mN#la`W$DE#TIzlP7+
Nr3Z"yxe4mq[i
-Nl3AZ<!1g<"DN@]"[v9]a*A70"9A%YM]iqZ>:dY|7_r.u@p]Bu0+@@A(w+;sqJ0<L80+dW#zC/W9aA[Hp=(=Wf3{.1Y^:FA6wG%#b"7Ms/X_p?v8lpFLchO>ons%E$"8_i7d@PK,mxI1&OK4ls(%Q&OZ*4ol]6:4p~t}JQkA^lablDUNLvMqR(H^3|.1aueaD
Ijk1+-+&u^;(RS!m`?YROv3hMUvy4>Z;aJ!I,lj(o>P[>jwFbR_rF#FkLG-X/A&dP#.M9,ZU%.th4ymEEW4?#1CpqNIB/5q&Bg5yIdp^gBkfT-0NDLV5lJ*?R0e^cCi>(G5E)TJisahb0Ou/n%KiWukMq^UlDJ+D.*/
&[Tk5yue]=mKxaxQj.48.RNpP>i2Y7`
>R*z>Xhi=XU7l}yAC2E}ARa`_B6#=`A*?qOH,A"9:kx.i"t&I$/f&d]9XnO0<>3{kwM!pAY]I8fT5mg0O.j%rL2xe..JEK=p:|&5M5wA';case"el":return'$h_0q[{0}=Ms`r#w507:JOhPR8j91xd4lTZPs2B9_!)-$jreEY+TNt-]mn;O3h#1KIrtDkWA3OMC-CJ[W<^qRnt!opRuQE`k_pu27KDG{-
0dst?RST$S,C`UKi:WjPpiWXD6B7nXNu(fs6*ifi!^MuQP_^H@r:YFImw[XUBFgqB^spIPBY&"y?w<;FIzMysKiN!@R)M_`OLXXBM`1ii2CaIpD%;Q$1`Apw/M_|bqaDDm=prjX`I[Tzv-M;!&sW-a/w+?B"t^21Ap!J8fH+^gJHsn3uH+.jY:mjyVM<x%M}]RwOvakc:!KcF$LQ^"-v:w73j:#LYJe
oqpoLx9l@|mZ1&a?Do(H*F:Y%
bDIMbw0r3nki?;G)LTo:N,@_G<;9.w>qs{$X4p3)Xa5:$:o`fm4Kl7nmjc1(;X]4Y]B>+!H/`)e-9W7H,.&)g6Is32hxg~*;I/=W$Wq{w3Bf096rkJwBpB"T(vuTO}kZS{M>8
,k5%(&=H!$XgYot]uCqzD6PHcrE6f
BcN|r5,M"k/Ua|K!Ah/|nK$K378<3?JSY)b3k6ur6QVe^%^9/U82$_DMg%_bX@7%J()c7GLyWX.|_~C`g
F?7R4xhAQN&8s;5~Dl/$w$oRHt1i.zkQMK?L99m
nH@Ga[u/MDy=XfYIhqi$FI;9vfS2dM,B;=,]5S@r1)5,S$[{*wJ.r=djdosv@qiiQ]FCe
wAB7l7)E%kEf9Z_=Z/_"_bp~mD=>UL);4J1Gk-kCQUHaF5IGd
t[s@JR,K<#bZYP^ws=u7DE-7Vp69llawLiI~:myK[5(P6Lk+Yy7/qC6?(`xOZiil201NS.w/d$$vn.xP"4UnA31qWfk|)UfC3s
k8mG7@R[5+qi#l&A=]
k,1?00%m={&5i.c5+r`Kw]8+:f+p01".5@)*wb6N^qG/pA]@Ly2h>~pMmOn"@=#_B&ON7>)(>fv+KfRJ@6e4&ec=-&v,pm,}""
f_pbAKj+{XKw]yn7lP^J)af:[r";9LpYXS5RYsloE8D=1:rZT-O7$2]`8])PG`j`Fd>DV9></PG*}O5odI|${^[U|wic|p}2ASD,D!bM=Pwe"/`,;bT3fh
Mrk@7hs|huw!fii"$,[EgY9O/%97JA3p)z!5L|lv>0m!BuEOoHthwe*=wl
V7ADbKJ;`4G85ha76NTkQN0#7@OQo1n7/(
s}f~b"=#`L"|hi-N.u*e=d>?;?(NatwE[u!v:[[I<S_s:02uhhW_oS#rI7$p+]G~[n,OlH2w[}oav~6Ae$w08l`B8kkZMlafPBR$v_[2c/!C7KIjh!k2btNQi<,fuuX#3Y;=kS[g9bP]Zz/#Vd8UhJA/[BU+)fg3G.xj?LR+FX&.+Q:xTh-}&k)^@v+bL+qWhx-JXHY`,mBVN$a7:MEJTs<PXA.0U-tgnAZ>qmj{C+K,,#[^5RH0=;KU2S%U0CN,?"f%Z&"v,dg=McNx0}j#*OA5:i)i*3G-D
#ui2&BmKg+q?q[PAKJ%D:HV}.Itv13@g9s<<U#:t_rCO,I%kT:)D7+rU(Le_W/1V?1Pi&lq?KmniJ^xa;T$Kq46(8kXAf;O+;!^BeejX%
jj7{ntGv%,?7FhKfBs2bxW:AsC/-e#xCFBU)xI:nCGBgoCyqJWDJUakGk
rriz%a5,SIw1i+DvDsaO63m3h/(Iq6ukq@*PGL0zE.0)1)eiS"]Ua:3>I8li0}epuPx^j.;_wrh8AkqS^C^@PwXo*M;bSthQQcP&3Zn!>%H:&;5^/#JgXfb9_UDyL%XpBB:I?RX{@&GdHrF9?G0Q#$<-,i:[O7XSh@RXM2=)1AgS,l_2(!W-@oe6:Fu?E`[zbJK(C?pdV3_-b@`LEPS
@TNVkBYt+oE.l&(84o!}1ea!EVJkh+BnKKEyZyu4
jSEo$b{?d+Rizks:o(oCg>
`"wzc$@|9Nx?FasCiGV3k17^nq/0_qrM)"<-]S5qJu:fe&m@+_"DL&5#o.[D0P]9;R6ei]UTaXr+/`-x)@Q4wrH+MmYjl.AQe[Ng/4OHWs"7,Yx#^@7~8qE2fJm]btT0Js%2k?hIVlQ>uU.}#`;uIAl[$r`NI,b4G-gy26#R?OR=G@[Wn/7?kqY;c@)Sr>sZANAk*NrgH}O:,NL<hz,^B(l/KD+oVS?<Z;gTbvgR)7"s!si(v!?v`E!r`I;~EWp<p0x`JEE17TGVU4$5&7vV(hW{$~i[c:SG

)HIc_n63<>awrgKllg0rS0L-Wwn%<mXXn6yT?29LRF_V4Ib>8C$W:6m(:`
gqc>9ro2y4GV~6<Z+Tcew)BqM/FZ1bdGv]KE}!F]V>aHRLN/`vr?nOZQ4Nry!QQAzg>2L*F=7<xA_`+]m[]!OR}4}!Cb(!<aKK@:Qa/A"6g]I&&U7Xl>GCbiq,C=LZ-b%e1%umMkg9)M#JN
eL&0y4B5OX#>N_dWs,j@l`ng*asN@!-v"TpyQo$Bu4p5T!{L0
CbW5)YWCh6dmA@ldi*H*`?tK{BY/1=Ivo>aS/7!w:L-ja<Nfi3FX;[QtB:B&YB-<ND$';case"ru":return'"ev%8aLZ;:#*#q31Ej:%E8jW
TxGADz%YSvI9;?pf(LOlN6mq,(rJ"3OI-A>JPStYXx,l0+.`^*s(y!CN.)o7*jheiDK
[hl57^jOH1d{w/[l!LT}7o8qx_XWK,;slcdxb3cBnt;bc|70a4e`7yk:l3;wqOq6&%]<Npdhj^S[tbjcpQ`.+n*G)nI95NO"E3m~<?y7m+GPIE3Dbhq3hr@ova0=51)aSb]!,N$&[
*P<pe}="76P#MHJ8=A0f:96nMsF*e#DRpv!&Q,g__puliEfDntxZ_ZQ+1Jp!t|z#h+O/xG1Jf)(5UBcb]&E<I,ek8/H0(gSU(8&
70729a!3ZiwE31SLn<vO$J^*2?7Q]A^"(UO`cIw.4Q>I]jo(V1PTum=5EJ3)bG8.O@X8/"GUG}Lt^v?aYH9doY4tI|#892/h.xL-@T_;Z~Or#fgQAbxskL!WTQs5RJNCoea|Zn[eDT1wSuSX<1LZ^NCPU4nCKMF`^g"{#60"T.!
daV<VU&@ZS4ojy=&`Sx)t-xrkJ:].u`%1Sj+bsOL]*@hH3KO_v;uDpn%rwuiQQGKHno[M4=Y>3fQ_gmVt804x1f&fJS]9o+~EP<sg<I
qp6zV}dd.FRzs?,@Nu(j$EiH;ggPf^!z+
hZJ0vdIjHnS)4:Ut&)+$_Kd=r/a?7B.Z8|n]tXG,en8H:M-9uDdnt{0~z$N)`$0,-ve2OZv03EA6[~bectlQ]hjLGo?#K=>=Q$$s_T!yTsPSEE9@:{u`gt/mpI$,e5*_N+d!mW-__jwO]Rqi7/"JTfH=X7+"-%ntg"7$NI.njO6V(5YnPVY?&eqmQN@i
*!.#-k_9?8}0!jj7Zl.U|><$nNb*qSteyr^
T6A%fg"q59@)MQ>11ox[x+dDoP0!f6r(:]>Y>b=<`hX9Ap#;t*va4%bT*@Qo7.;+8*Q@.8C<:$XrLRvj=sR&&hVeQoL<O2AF=V(g.0n%o</Oz&X2BK)+a^r,#>-6n&*c7tIlbu,`X1&NE6Svuwdn%qnMa_oR;+mml9#LW$hM&1H;@YR=keVJP`KkObb5JN<^c#gkn?+E93!tjj:W|7dV]?G*Qvb,frs$v@]E^HP-NvZl/m`J)d}/2:M@g&h
7_yU1rb%cnnr7*z+>LdX9hg+Amd[nLR]26Nl5Z6^?tv!
Ua@Vp+Q=NWAf"3vY&LaByG!?I%c3ZRu/M!30B|q%_Bq#1J[x,x(ffO$L9Q[uLP]D8Loyi"=`f=e[k7cp[:Xl"UvM.+q}Orp=+`92s>6ay5)qUm4Au~D9+@G)R)I|sm$~R@U_djdsvMx2k/2twV[v
a8Nc2T:&Ob@+<>D=x!|MGcm9Bn*X[BKe=/GU^QT^D./qx)4I#Q&tZ#6cSwhl48#YapZx8IwLk55N4!^e0e"c|bis8!:%03;9qn.J"pEGt=;O.Zt[~`r@bu
rgMW$Crx9Wqj*<b,m=*|^1h0=mOmjBHXcog5r%]F.4,X%,q"DNtASiQ]Z{9l#f
4p@X;5t`csDLK.ODmi^D&h:Fa-A1.+}W6w`iV57U&M^_U^+rw+gp~&I;-O}2g%k,YlXr%SYnLV(S%P?/}ouU"6coO=gXU`vT#BsbRwS8)?^65819oL%xAYD7NFfw*"l&/7g"{+t]6Bk5DkIFl(W
x.Zls2{dn
/m9FWhV=%Cw?QlV6UqS+Ox<C9DV+#6k@cy9Z$Lm,Tm5g8cgAVr<x5qJI4lg=u$/nVTEfy4nN1&TsV,5=D8._JF)?!.<U)BBFT]RpX:<ffptPpmG&fhYBKX~hvmp&1QTV%`G(X)gB*K*Et5MbY;|b,q;!mDxG5ba<lGbRY^]fjGHJ24GX@
o4Fw0t>0sZ*MheDnMZuG]=bvajQ][#r4IfcU~(iEz=g.oqzWC"F5M!)EaFPMH/B?=FmLEa#24g/B4Da-OIg=@elc#N0+L^`0o9)CU4NMC%Fh.#>he$=^u(3g_YJd|M>B:](yqUv%&s`CXk6dg>suQ<d>qW:5vFR"8KNteAyOpJ;aqTlISWdEcV9?9MVI/={k$)y.d@}MfW.ei1#(%sNZN@
1b6Gs?[wj?+c1cRLdSbwsuiwWP!?28JK,:4Y$/j/sn<WZFrnkG$+Sm7&D.mg1eFK/FMB63W>C]AvG#*k&Q#408Qj<k_+P}ao<RCKM8Y9LK"Vh.U=e($
fc]H2GN3c:9?:ps1k)q.RyjLn&#q9n?-T?-NJg!#kSSOHCYOKA9@JujTq%S0ESZQvvdZ9_>:@$a]9@gs+Z%{SPmJL!y$Ku(Gw>f`sl=%o(uqBic;jg,#C8$KrbuX#X^os[bB=0>0)"^WkQB5:,h}aQH[#,P2uZT}RZ9uUG2_L}mlE9GViLBd(6]a%vA,Am
X!fq99Ep.7JOJ?BX2YanEwg8FrUnK`xm2GkjsaUdx6E</Y~n,Ez+#Zoi>FN$SUOv&[eEu8~cG*/"GES^fvi@Aa~AR(O1E0%:Wy"ZGR(0Ja+^{3h/$Z$!#Ba`#8=CvA"g6nt=Kgus1DG^2XJ]AmNp4R3-D`o->%m[`(h+dU$]k?G,dWOv}';case"sr":return'#c0*gg~Z+#>4oo5+k%`>g=jA}:8w~;Vx$T*6tRHn8
ygG(~[6*nN&9Yg?##@{QUrO_q",N-aZV7C(6TK+i5nXtsGy;Z6?t-qw$gV203issiQy2kQCCGM,Eg:9TMlPv+vdywvOJ
UI7`oJ)D]qJeTn)1Ypa,0m9[B9@WkmM_#(`Zhk#Nxo6_DDfLu80db2
c>e_8CP,+44(3mk)<Y^6slS+^JL!U0XHUs^;$Hd;0f<pQ/
tlyc/vfU>wYXbupv][G=a9kv[zoXbd
@?2B=f^aXBRZ+wd,~Nm9$;m)8#avfV{#clioa_n0?CCY6jmo%QjN<o5D{${S5
04ZK<=mRW2v`{"!$k%wW<e{F~mW_WD%;:R=Hfiw=`89uv3_4KI0gI/]eC]Zu$?:Ad^@1/N65{.Y#t%_u_k8,NV`6{<hl?Aw]!c#Xx
#]!@0:oaMc(Xg/M*,8yWb$4xNhhl?2nu"pj6UJzN02FQ<KTsC3N_C0O,[kC"M0;:U$h-#6%yK+anbHA67t<t(6?tjPP6?"UK9-<]+tU%XNF%r;w
:%%*=i5*uEXcV-%/03*$rwTtAew1S/QA+@Eg.o^l.N^<:ShXghuP/wT0CNe4.Q/Q$WL7aYwpA&GOWngZP
%l?R"(fP^e=_qTWfGYu3;I9bd-"A.umF![uwSg5u3WDR5RgHqx<nn@(C+H7Z]mf,4")oPcB_V::YKVcfi,eWtt~*`>h*IIM5=JBXeK@T=Ouo66vcN?LD.p+0vECgRSTF$FUoJ3XYl)/XfAB<]kg@aVk:TvR-aue8R1:$
wz2=m7SKdy3Z<|Zh&%Cv>l/,ghd6)zx2#68WD5u@[QM|N|:TK?hk(jb&nj
,Z[QJrw*?H
NNSWfF&M/$TxG/3utG-Bs<EZsQY&_KMaqbnH(?oXo0ucy|aE^#nq6d$#-,i+2fNag3B!Bp7%G_KRBJL0oT!h-V=WQ1U*_vulD!qTh/V)UY(=gF&;!og5;ffb#q[_Mb;/2dtu`e.#4I9+7ww&hTZyg,LAA@Vm0VJws#iY9?tv<Cl&+:Zjb|k[u#RvtCcQ#)xMVA&~"3/eN`U5Ko.PPt>wa3PEn}Cuqj.{W7LXVwQ
Xw@t6p2}i|Zw$_tR).7r`BBtKl!"`(+oq6Hm(Ny51df"w.%hq"ELX*KE;Au<UTL[H*UH]C*RXL
;!rVP${P<1~R28$.GVZ/>i_..jDW}XfKe-YwcMAiN5
D7a9<SAt;aO:fdK~fN115!q[w>V/5x6X#g(]0U>ALQ]}CCiWo2[?qd0Bat[#BwKDTk
.z%KriFRldi>qhHKnEw+VAer;qfkIYlbs?$I$1_a~LuCs*gvIXK^tX*un`*!ivlknx(Pky/]qFF45b;#GFff?Yv>4oM5nJviyEijetbqAWv@JqvG|6`c+BraB;{HRMACj9S0$k0El(V2fbRCvt?Xbr@e
bl0Yi!jw>Y9-!#MQITJ6.gg
S|`{_dApuETr,7?c[u,k=6/HJA0"2G!trZl;*_o8_Zlj%cjWy1P.*Z?B&bv{
]1eb?T0BlE7_,`%-sMLKw7.8M3c5d5%aqPqP=>CyK5F*7Qf<4Fnk?2*T$-sOvQ@;$kkkhD*E=2wP-gMa@]^FhEV5.i`5pQ8jUA9P(fh>5]F?m4Mccuv[LbOE8d3U>o~46u2,tutg(Yx3U;%H#E1eCG!/6`P?:2m+4_Q.9Q@o#iD:GPI]!kA$2OiWD1~tMO{x^%N-]R/TstvQ18&Q0Tl)dax[(>rE.`J&3D~?}M!44hec*7pB~T8LQe>AFKP7+08RbtCk(pVg6:
uW[PZyB_L5
SSmwD0q]73i:?+C0CsC/B[pMJmbn0Y?a;="L83/+4luFx^y+x`J=
Hq[fx8o.J}TqHkE(Qnj:m7(#spcIj9$;4Q>U@@7f)kn[C#ZPJW;ii49n]aV4d(ax%Nj1BxNwtJ.r?DBM30)k@Q[kpy5u^!m]a]tsxe+&p[S86n<7wRfKd{vAl$qCi^EJ*<B)jX.e.!E"=oy:0aVW/pX3B&]!%rtCQ]yOGjbuS@6/Dmi(]6*"*_]wH6S!Zk1lTgS}TjyPjw-&;^b];xF)e9[,.Nco3&u[<oN}e%m[$^%QD0RBIy(B6IGw[$-[qjVV*Vh9Mb1$Pg9,tZh$G^p}>OI)4H5%sL;Z:PmB%cffv}J9ShVtiUaOyCtA*51{gg<`)dylD:=QU1vL6&b+9EWG6VeYxSb".a,w!@tZ';case"uk":return'#ev%8aLZ;$#!:q3/iHAN/6&:=IiSy)6-%ZW*^f~:s&G-E*C6/+aLEph!ul$SX]1OF#xp_ccvGt+912s^b*JgBgd-"@.]0Fdot@;3nHPniyq$=s{pkxMFmr6u_yBUVK*?|f9+,mtb`a+y!30i=`al^Q.0{&d&Dn9pXMB=X7ObkWTIDc$NckrXuE.*+?EW84&H0)Blrq&8^c|A|7O,L1jao6W%~P}R{,l#@sWl"kVK8)e%o;hJb03Lr&h6i+"oOB~=rG!y,mQ?K7e]7_pGR>awjbZm_q0+_NaIhIlqj$oS]1&O[C#h7&.p|gf)15TYznm:!6[#hI=]HC*o("Gs:bRh68^fdH_X|6vw{H!?;3o
1NE`vVGRpS8?>NG
GouH)X9"}C*u._HoCF]C?COX$A%R(LTnGX^S2l_
/KC5YiDN..mv]?nTKO[[cZ%x
$uIf*%ZM;SNCN#@Z:x_Wy!J/I8EiUk5oOBU$%42,47>i&v3(vyUhDP0rl[aN09QM,@"q*<b7uGb)p+G.GAmq^KxLr{3"0-,tRa^OVxNyENa=/D)!A*(;hk_t.w7K6[PHt3QxhU)d8s1T37N`+K9E^3_y0U3w"LNA3ST:cyLEd%I!:oe2$PEtDArB7S%S(}ox$n$;x
+,P"N"Vu&G-<x{$vLl8+XX#PKxr
&E]D`(wb2(6ICRoXtha!`T]SxS#D3!hR8tZ>m
.?fPEl>}]ffwW}69+Sn!_i`mW&
glBg`dTV$PsFA9Kl%/"q?w>lBx<^lm;9~NWsE#]%WKWr/Ij.U[k%>w^#l,.bOd_EO7Iujn$i[MkiwDGvK(rfBhhs[m6bXTeh=C{yQZb5oi#a
=w2n-MgyoM*F-h2HU^bxXJwvh]ee+*O02t]Me&W=DyiC!m;Q/:JWlI4Gj!"-N<Nxq_.g0RR<pLrd)ru`-[ojNryz&v[|-R94>q+PA0=8C_hr<!V"w?.>$P1%l|0"Jcw<_Hcb>nvq!3cP_9*9C(w|oqriJG?MY:L;JZTlQ$)0VKdr%3E;uH%T4,c7$-V{-_KY<*rmqn$(WBj4Kxa[F{vfeAcDXei/,+Y=Zk@T+^]1;OyP,TD*a6WWUL.34yTT^!)|2$f1s(ks)FgE7~5%4)31(/k~1:g_q[eHrpy"6m,6J]JfU6@N0:3XQd2ES32NK2JtnI;pD8-WUa8M@jCqG"n>tp&SC1::O"cN?;<!Jooe<0[vC~Yf]o2r%nhq4oX8>]KvSN_8Uu<[!n.4w/9[.!L#)p%l(IhA[6GYg_2y1lUU8GEXQoWg79a4E@SQWg5Zo)v+#_wx%0n@*@8"F`grdBdMx4,~"2X<n5wxs$vLc=lIaus5#(tQoyr,Yil`6enmE39N9n
dd]B@C)$vQ:6u4+6pIBND]1/CgX;:T/^=tp;$.HD}W67Diz$Mbs4&7mrX5Q.l(Z2"$vBuC7Dag3/]H/a]E.DgLs.t:sW6.Y_Le_%Q-#n~DMaLn^y6b$yoUbL*XxZ:W9bXN6Vu=]i2SbJ03;))-da#M=]I:}[r"ijRpe-n-[roI>K6KXNX`T#|.Q/HISM09*/V9#N0nGT_mv:rK`
abyQ,NrRG3:1BDuE0]{?R([/!C2U]M.18H0I1A}J8uTm?]3LTgt9j&Z8<5)pP:6tG5^XJ@x<Bq,<f=kd)FmQW+3C<S!uS`-b<rgRAj2yX4((CFBh
CzIK.jgv=Dua^R)AVSrk5%XaNKB$v~[av+N;!V)p.SlJM,XFvlRz=LS",vi3>"F*x^
zDxAAskQHCTd^&Q+d&E+iVRYxE:L2pegtQt$bT8Yc/mJ)9,Dtn>[+5vU.:2d3A)^]$bgPk2g%Nu!a@US#riT6@NTHKiWfou6"8t]}Iie?*cII,1we*KcM(?;:
UCT)J&P7gj>a]0jOm2X*FQ(?QGxc&+w$--lU6s@1#^t8S4LE``NU$%#LP..d5y=)c
rBjP:1;_W&sDZ<".3
1kIu+*_*Co]HnrshfVOib[aKqrrFrqH-CT$X
tR3T*];6#L]^204DPk(}b&@~-+Va)Y&/i}l<sBh~NJJ[LI+p6/d/(84o(HyC6"6O6sUpxY@bK`H@$-D{a?`ip?tE_#
;1abOJ;u4
si}ij^/jt#
0/<}8$K8VJOV4!@_%#_sr
Ws`dbM
d)3k~qXDLqGy1]>;W#Y>0[YvNT{;cA/q_ttQLC
(,C$_U,$"y`+>u@H9H,}&OW#<uHxHZ[IZK4Hwj/[H`Tl-y2jjD2iFER3Gzz&2BYMBQqFkA%?%;a0:>ZF*2gNX>tM!jhOPk5$WgV2wa7Op>^<^aaR/;!*BlN,UkOtdGaowU>T8c&Y;u9;,7OjOF^._Yr<=%B=2Tn
rp8`*+39^(gBJ-+wfCoM=m:YGiBhU74p!kux.LS,Hs4uoGJsDn
%5_=L+V;0m5hIUJZ(';case"he":return'"s_qugzV>hohy5--"W!
mP/%]^cn*9Nn=FHE;a1m_VHwHK0n8n8N*+)Bz)Z[6Z;/4=IBR=nlXcS3-OXx_)wq`x)4shA+hk9qhAG_8?Bhyn_p+YM4mCR^_Th?EMWF[]>mSwNbj)fnO4D4vf)@DNQ#aW(x52k
yXP&7/8hyf4,:S@Y|jzB(5W$5J!TEw$yr6D:rBlr>1w8-BCCYbkohoh%M]o^YOKXJd(S7x!JtyAK}0?<s)!,a&elr?7$4;d[*Nb5JRdN.yVwCWiA2LO$C_"%B;V/.^)fSXGdT=Gho#,%,PE9(u#`BgXhwwYe+Y|0:.v6e,uojaeUZXgxWwsrYz!#!Qsr^C#J1O-41!3DSbwZ!oNfLKy9cAP>m6Dp>l;6V-4biKO%(!(0($?$Os;G}X-n&3u9UZF,FcAhM=+*}AwGT0d6xi`I!i0TB;:+w7A&fsls:
0F}_pU|!xum+F@)`,my=SuTAAtxT96-qFsiQ3oF_i9iQ,+yoSUgP/jDl0D#VV<ThtSmqzp2Klne/o!qh5rkvfJM9fu9fUo^uja^TvC@P0DS%w.9T{z).2=z9egi<}sqz((.#=^DP9]yIy"FqRONWTW;M?j!t5*gdpbcG
Gf(fV7):o{Rn"90nX/f6+$H"FvBe(zA8O}f!gHN$i<=?e)k^-|?ZZ(N=!nUjU1(+9fK%^I)J;c/s3Y>8MjKK4%RqGlE^U~Ji:@E,fV@W6S:9u*x58o*@F=0py#mzVQ7X%Vy0
*^;"`z#5;&OgB!s5Q?eZseU%K$g.S#1E,RFpy_mg<32A>6_7=^W?].#vmY+Rq9d!nR3(jHlT|6EXUMNRMji74`WiLpwTu(<;E)?<)LFqb?4k0*LD^D#k+Vw&hm}ZA0RF[kg[>V!kzy}orc"p"/zIM[#>DYquEP)43tV>mosk?-=F?<4S<Kk=qdq=ROAc^@QZ|CoVDFpU02d_rYR==de+r+R9ohY7#8:7?X1&U%l4&w|GFH-yoi.Rs*DRoLk/4#wp6
%^NEp/M`5`rx-9
4nSrfQJf
EkFc1;(rpD+2Ii#Nf0l*+y]@8Wqa{;#;w!UU@v,m9qzr$)&8(oFEfw},7
Z[.94kf32Prs(pBJm@P3a1~Bm551eO8^tDcU$lR=uq-N~x>WGK>G{uK34:IDK6^S~a-"mOrg5h&=<I;rVgcT.X@$oX?oc<#A|Tz@:^/>o=dd]:=-]!{_q;Po%6,/(V<oD&;,:*#]cM&35J}/sU|m~3KNj>im=1U.bVR!%W<ip@pyJN$.Rmh]IHQlR-.:XS$^k';case"ar":return'"c0%96KZ+$#!:m#d;->1RN(GO8xR7N3S]R5$+Pz!S4:4;DZF
!X![+,Q#B).WasppKPK.BlUnFX3=iU.Q3/c7b9Ee`J].*prnA#rxB=0IU/><r_mLNysCZXcoyimnb_2&<j;84RXfrth+w*fZ<dc0+KnJ`+Auj;+H1LIqW>dWPI`OKO^tLI;.r%fLa.;D9vv1GCQ/(t
JwjAbxantqImB+zS<O,h!+hdVvVp5t|.GnOW9#rI#u"!VJyTw-_ZVS5`F]}vXxgK9^R?OKFQD4DNMK&4x+{T9^PPy5|=RYGH(FEkx`)18]A,
6GH=t1=%U`v3vO$-D,;{rLHV_4kBrd_*1Q
!-1nv.Bh-$jBs?#b1l@l;2`%laC)#>cRLWE+kn[1qXMO}fzds.K:|]6F9+[HL[t
>#hD<hGv2aq2^^gZ??"WC66i8q-WeO$t?IWTuXgU-LLx#eJgld?S`1<Y!%LMaDwk4;Xtg2%1Zj/pNTUe9;X#J:#4Y+uR~OVm<>{h4);i7*@bC<!XAp#Gv/O"75g%O8:VnRgph8YFA+vH78.!nYyC>#A_Zo283TQSZ5
>AP=vc?jxXD"C8[}m!SCPLH@1xgT2G=[n1t*0T+GgO4OjVGU.)r9Yk*+BDWAecFOawbT/i:/Z7%?@Jsd5UX|A0Ha
Gn!QbQK"CVO]v,cTg:{>hg5j@"scU,N._>)w+OtY)1sMQWl1fE
*95IE>joqw7#wA*Xp=%O3dhyx4MQc0XJevQ[D"`(^<CZ[Z$1Yl8:A
Q4Gf/4rI@gCm1N!t?Mgd_UWk^;e}uYBiQJe&g<`!$u6rO1k,!V${W)
LnfLT27Q@eD(JaS;RxD=Km`Wf#Hfe$IeAX]h&uYctH!q`1niK&0a]SRVXqU[]Fsa%:yGIE3LM>B!W!iS%r`lhKYq9)y-V+fgCdl8,I9WdCpR~%W#K0C9gyNc.LFZZa#g|P$^_RDJ(wqaJe;6HMfeaZh$4sCpXM/!s-QHouej>=*<B9Ep:$oi1,%5WAJ
1i0AlG^-qK.85k<&=8#_<orE~44TIc1:![#d7TFTUJ&A5;Ik}@~Y1H#uTYkhUe6P,#
VP
=&=$eNllF_DY*9wgn/L;yElszD)$Z1-2qL{yc[8p.6!=CdBg(V&$m1&%h5R$<6zB9pU0dbGNiJb0MGI<JszPgX~=2EG3E0usQF0AusPAK3.mA@QYd)6I@_c(dS|&kG%0O(]RX[a2}Y9M]%be2s4wCQB
jkk@k=yvLM=gDhg8qcGd
_BIu]0Bx22N^j8MFZB6CK0W"0b)(v,Ikmx39jV1;66`OeARiC:l[g!^.8VD6:VgLur!oWq"wZP_$8s>Z;Wx"^,b:)O)e$j56YC4EJQI6?zY|X!Wxd|]R%,,QN5VTALysULBY-4(d.QR0G.Rw6}?Yb-9,8xr*sZu>C
#=PiF%r3Q!x*QL<AP-gM,GM6L4A-;*DgI!p70u@gJv!e4-@H%JvIrNl3kp
VFI#Gw@Mn@6$6*OmHd
>s6ek
U:b)1amZ%,@IVtun2BUIg,00noP`@aT$>B8{t{OE(":dB0978E-B=<f!H0Smw2#evUkysQN,W13*6|kRpe7GlVc]3+
R57^GsH(2I3XQv.Q*/VZzo{@a`buXmQm4pCf8T|iR^/:g4h/
4)ysJ#hdamZL>o6V4KA!!j@(cw"a=@/?G&2XB_+o?p1_Rb
)w<$^I}y:"g?:"h*ikje5z#;w_^ju,"o3^88>ILI!(pXBV;D/2YYiJxMb@[L7cGG/MR:JGu]7GxKfVb?oR!Ct]nC_DFi{[Bi,-?n:*ps0687QH@)"@iUnT12Jm,jd)`r+%
M4PdGO$_[T
,((jlBVl?A"R5w%QKlXRvODUCADoDn
tGAzQ;Vyp%N6I2)4q&>FReN"_sZ@w_4N@70!dntkDkDqY?AJ2-(pAL?kx~nh8_:a_sFJa3/:rSIwcR4_FcPa/$VJ:yIlbXWjVJDxXETYSbVqd1JNyt7~J*#W!eG[,swC';case"fa":return'.s_r@;z*;&ivc0b>t2$]hA3]s"6*<xXOs@mT-SMG8ONSm_j!*V72AJs(76&?@uD)@1/+B-yyriSKSs{p9G]/&WMI1LK;h<Kn6RWcHQX`tm>laDE_*Gw7<I
+Cq6)s@zqIkl+YE)L3OY>L&QaEWZhmONdID,*52U,G>lfJ>6-t4$w.r5fL?P>*9kv{&^STbvt]!H>KegfWkv;ni%m~at?Z*dJl$4J7E)2EAJLl&Ob!`2D#l"F02Yj5a+:DLB7hu1MfK|P5KN(L%]i6$Zp:C<Qn$!<y5A24`yM[kg>B1Y1l)5HvZ61,ILGwBbc}n<pj?rc<Zj!&GT$eTm$J1Ll%k^6K&Ty4_5R$C<af>GU{Q*rNj/6pC6imx!t?%Ag3A!2t?0!!uMRZmRTOZ/*j#1DTIwVCrL9}heJsx(M#8P_VWa.2X$h.Ph4#!`1E;.6`
,E;)kFKQfe@J}JOb#H<OW#<:IyGiN&0Rr4rWZUR!|uqeEME?Yx[#0lFX!6dL8_Ub=/nAp$/h
seZ:aTqC7{+e%Z#b/Q(/CIcYlp0kqpgaJF8x(>U]$k)Y=Co:"jB
&Iw1[C$/S%O4N#`JDUs;I<;n4Hr<Qh>W9qwd3a$K8:U#g{(RwCw|6*;qU-PvoWcUlC,Ra>*$4<p%[rf>^xw"i)]CC4;<$6@N0qFGIJF13U=^$s$!dm/{Xj3CDkOT52z")x;]w.i^3]3#<_VDkSL>0^4X^z`,;Vome~
PrLSQ(e5J6d@XK)_0vbEI)%xe2@B^2vX$:CY@ZcQQsCcx!`5qtWVxf-d~#%k6N~))di46SUL9aOynms$q6|*XgvdlJx3s)W:qJh.@:
;`IPL2^!5oU<@kAJ"C.+Yy-5_JX_._rs5%]CRNJf5,qkrvF+=eLY"3-&ca%%^LZ65Ig7AQBiXuJ-/XpJ*
(oED@EC14F_x-2Bb&CL$_)Qu"^[?afL670q]Cm@fVc$OeQW{ld#5rG:vOPbcr);c=Yv8.ojY@H+)MOnp$Io~a>06R8.<)b5}S~/5s+*U-n@0<?*C4^*eR+V0g2WLv=;a@W?S385~!e!D:}Z3FyG7[J<zk*wy(l6wmkI99KSItBHo">nQ/=.r"8J,XFv1J(v5l9]e)AF>`#0Ge}>S=a,zW"Sw+)`I))Tm!j:G<cR3]nm7h^EH8Xf1uw>o+]hGT"1X6X@u<~17hxuBJrNbJRWK*X*wdkw!-,>;!pt
sjO:?302M?f<LBjV
)1HoC8,S~IZ:<G9>]*4X$AUW8W~IH?Q>B?{[y!O[;f
kA?.mQ$*yx9Y>SnbN^.hIX51#`sd+}`8pvCF"|o0@Y?PURSn@PGaY$^T/b$af,n4Co80[@Sfx4uj@$jwPZF3^x[Y/&K742,*a>g9RRA1PT#D.a3>?DvH#w_+Y4/mS0OK3Am&36Bat~*mQ0//%NL`""';case"hi":return')s`6K5LZ;$ccCCxh}+LuWL`@8"c#W0/e
i
8sd#&:_)fQKbCR:&gpr;r9?5^-u,rr?`W{*4q4<_L3UY
jIcav%X3r`X`xl~GuW7]hc[*WH,l>;=LKL{*&MMW?M0VYB]4eg[/O;0snq$[MAc?XmNB%H=R^8C]b5@p/am?WmN:4iI#wVz[oqLmB5O
45aV4fEV7s?^(SlHNe}`XugjyI3gkD"e/5@oD_5PQ.OOx9meXgh,.Qt#m3c
ysbp%EMAP_?:{gGf06%3}@7Z.!k5_dmib1a#/)b2+.tQHavryiAmGs{_D:N2DU0B"E7
`G?1-%Rko%6m#<ctfDqxew@MtmH%wiLE>EIu5l!2NYE_q.l]W-",.]s+tS)!V,Ku<GLeK;=!47BH|fkT8r=g5XkE-IsV1#5eiH&U{M{1W/:P#);0ttJXm`p-L:YJ9p5L+3;hPp/e*HVmo`+D0Qh^Ez(?jz#4+b~_.o?+9Yb)n/{qNE9>,pA<@K8n2R;Z|fHyG0^ib>:0#<U#sq}Lm%HJ;gLp=j5rnw-I{M_5m^%$dOmCnE
aS;F^(26sLx.GO%&)xuhu1r_;DhL88TQ*c^VGGp4U}["7EOqq9"vI(SZhJPXCuJ{_xZ?=dlZ2d+*%wPc]9EQR`),
+@EbnW+hXme?^Yvn1O7CiGhrqQihr>=>U#T&+)gKrr8:g.g/,H`(0"yntI{U.p@f@n}T!Vx-d=gJ6E!#BPYV4!"u]!iA9/aiQ7Z.z0Nnoja*Lnr8;>Zk=66*g@}m
?@B3.w2TxY(F(ei,e.>4;u>n:Ov;%pC-Mnu=eSseSe!^pR
.%9F[TVauXs5mQ!65[W]
XR2x#fBFoLrtlVoRQS/0`@tWSyaOK-DW!@a"G"G9]3GEKD1p&Qf*^2XkW#<81gqGTLW!]0Tm+*Hph:9NxCIk
zOjVXk)I2"9_r,L>TbL&!1+,K39G;nrS">7MLvt]lKN"1nk@#!,lMZ(U/xniSn&`jC3Gb
c1Ov3w5<D<!2zk46F`onvhZK:?`SL#r"&_|(C9WO@khY1*iv.ntfspee7N
9A"HPYVK5Wisd3D3$wa-SVpe]zYd^HTS&Br
m1)EsSk2(x"P/;Q_3_Jim`1rPce-z$b-7W;y0tK]NzyNg@OhkMyJ7thbHWmD(I<)%ohg2^sz,xF2W.JG#%&Wm
t:dWB|Md$CF.9|H,TS^sPB[r4^]KsxtNW1+xT(<88"c2,OT.p{6
BZKlI2vhqPD!NUc)V)y>V?.Hui(FAPkhLiVDk(j8kXbrcu<2
|x}bsfDZh4z)>sR40^U?(G+wMlCyb+4.|xu-!rCJsSN/{?5/a;8H+HL^pPRc(+$0GuPxOMrOb5{^=,D4y7nGWg"n
q~/2X(kL_1G,:?V:6$gwoY&/NTIQ[@SqUKi;p
nGxu])q&e>CZ1^U7WzHf>?D%Q^3p<MURaRLIVSi-Nntza[tkV)X!2DG$Zu?M0fb{<M6.GmtA5Hq
&&c"<|@yCFrf$%&|IjyfFiVQ39`7W$0tLh-jd%TR.6?n(Oa$2&2U_@AgRL42yH%vn*nY)71b1_liM1;"&M[XRud9kJidnf?A^+f_TAkP/RweYRH|/Yng#|d=@$lZL<
t%x!A]|qJ0o/KH6USgS+y?lC8.D+R*HF^s2@Z!7^5NQE8i1b:izSAG)r;ms"1?n>BCB(jpIs?8[Eww6sNUJ2Tp+FsH<RbA!j0"m6R]W"RdFN,^&^LBDxOBNeU:X$x+[`Cp&5~ifC969V+Vokq+sqka6sI+SZ)4[QupHiZV<5Hg]oT/AGqAwSg`?bL#cy$13aisQ:R5!,=CnD?V0hkwm@h8wm
px/.x(pJd<HS<hT0jBGybCupG)0{uXtQNN[e.4.n`3iG!I"?s@ya
UNT<7iva%S[5&S$J;.zW&?b!U
cXUvU1%T<iM@"d5D|1ScSt|XY1.nr6^';case"bn":return'!s`09bSZ+$",Sv&@k(@&.NF:v^_IG-*D:";H[3w8N*D8flXnf$zg2)b4,-:4YN=;76g@%Kwx8v8`#VbgjK
&S@gn}^@
emXl*
UIv[>7Obvb7hfrKULK4x9n114W1vR
!B97+
8]0E%G,_ITC)f@`S,AH#2^SejvPZoy.DK!sb;4TZ)X2bt6Smh@]M{6qy3JONkgz%b@2HIMn^;,?h$cNjFpc[h"??Us0LIlec=Dkv.`zTIjRacIAey.O5=qI!@
NAVb|<"F(+iwK,`+:AC(vU9`:!eK4wXv,JE_=C;bR$j8n/Q?Fm~Q[cfT%lEAj0pRX!gG<.
mI972A!>YI#F/civv&&"UKk0]HMat5B6xes+UKy5Y|A4sTAiOZY}*1qeM,K,*>69nKc-Dw8=:uKMY(^QXWS7Xd7F0AM#exLljc/,j^=.u|>cj
8U9z0{hL2oHE`8W?DPK^U@nCJ4#)FqZTA2b{oxsn*XK.`*+zNKt42vQ.5h&h<#%O-$.~C10/:H!>?HGT#:T1Fo:D]aL5,fNP+Kd>h@Ar6i5w<mqy%bQa92-Zf/uQEh8g[t_yl:#^r*d3esx?p[8A1
UAikR/$5xY#U1BdKv,2htKg@)e)gRRVEEELG+[ldLpL-aARJ8~O9qW]4Pw#jM%&G#}y/3{UK6
i6$""TSm^u`iYT7Fe^@V,`Ez_r-_p~j"2^%?rQw7bHHCo>Z.JAmOH`vg
e[c7T.boYo;Kv!k?xRN)7YmvF=z?r2ro)s:USf9<7QGKV6eR;Iwinth*@_<G^o#j`.{OR;boqE.6OT[+jYw2?2wKAKdcd;x8l//2THKaw,~I%),i/2eC213`XI/dko[S.35.C?K%mJC.p`X!/it/(K9Ca-0]7R]Z7.T"|uFgf$}??7)D"MTc!$wt?]gCMq]./U.!ZiOr0y&,ig2e,vD;BPJw$fJ<4*q[J@l=>!@&hVbT2.Z
aReojT[`uC_<>XR;|k"e0[:[n%`EGM?yf;|[Tz(]2yc-EdB<JA9PBawojlunGgslDP_Lx1~CcYikc_.`_7@)@
+-?[N5N=_;5=kdRGT+Wtzhwb.QMT"r1y*!"Z>?]")k9I#f$4dRh7Eem9R26$%%6oNKvF_yRoC(uP!pD;;u%;IJa?C%@l@]>D!-SNxVv^`"ThbY-.
_]cVgGAcMk>QsqxEt[D#G*/LI}5&O@)1u56fc#cXI(<!Xf^HeVFLh&KSUujW%Up9l<L0(}yju(u`1/nRh]i=ga^:MR&m?;1@>Al@Dlr>_NH@Rm)vuyF"(*/!3iKp_{I)3x[#C6<qIivU`mT%J_
N5Q-R"+=<gUk~:zU
-%I
NN1uqb)C$gu8kr/9CS:fg;&)v9?tO,6p)pe`>PGEUJ7bExk@jrs&jE99J+
Vm!*=b/(,7lm-DYID6HPjnw,h/^UB`}jgg&Tis[B^&%H@7cUlIch)4(E$9jdtbf1`74a&09`E3o0c)}q;CR@PH@oq,Po!VAx7rUq,8&MV5YRNQ51S>d[`>IL7)E4gt:+q"<
@26O@@JL/ivFC?An3"SNO8@M,LLhQ+e&5bTg/*D0Z"x2"=rFb68ZIr`*M<_=V)cM[@;2C;zVM#-]QOw[a!
J*O|u`Bg"~b-T`=EWMTrqq&S-|(H^N]=3.`0:s.W_AnX!Ju8L2b0HLm"11@AYjAP`XRE8NKaOF>u-?7>_X`?cjK{8"!fU,&ZYOvmXcn+r[/!*ML>2YHnFaH-=VCKG5f==W_uVNid,v]ctJ02,);8Q3UA5&GOb.6^2TO<MI:!c6n0Eih9Q4;i*H%z(7Fs[`mI*+ZDcI<.WA`ei}yd-HZ4!"WVro]`3y]T2<.tT,XWu]Ha2l%8R&hzCix|nR>pWL/ilPM_pKsruMAt%;B["o<6">r.
.mgqbfE0fuUo^yf*&jY=@&^k6aF0KcoFN;Q`%Vn4&`HRod"Il?MO#J[mYpaAg1Da^9xScAb(@K>51b,S1mwX$J8B^2Sk
9aHM:,8s6Tbi$d`qRlC=:LV|9ms`Epdl(KG[HPF;PtiGxV.!rGc|/zCkOz0<.a4vn/xVbdnkCQ
V8C.yZN*WKpT(9k,3k>)d+.[|#XhDx%,x22';case"ta":return'&s`*/bOY!gN:Ssv&1#gPF0]*7$7T^
YKM&_3GVu84"_e|C>sy,Ike=#]rjO&}TGMK.._n67;HYw`9#TZ=-r6o+lLqW
uWM1i?(IhbS4Rqi`t=;Z7Kqr9@W4)edSn-xH7Eq|Jxua:7H4sNM#vt<]6R+T0+d-mhy9v_XsfDaHUYG"Ok]~*;Y7QatGrxRd2:q,O+06
}!GWWmw@$sm+S5>Kf_/G+qw$BccJXW4MN+<8P?v3giBdVb>goJ`s3NSw"VzcSUrT>qWX?PIdhfEEi>zFAU5@FDP!JLRiFbi<O"),m`rDCV~W/6[Xq]4-+j1y{.n5yglEVJ4Fa`<3t8f@#]D#7a?a[oY0_)i*bN;
#H_xdoeEMc=%we$9ep/yayaru!S6iJ{SE.W[6"m&;#=+>j8CJ>)O_t]AidZb$3:BK9iF6`hR6.Z+Pw6YDA$ikE3lr=,JDRi]gXZqq^O*@s-)Gw.:s/>:&:ur3)=Xg[ykVk]5
W}T_f|3[+aY]@[>&JwIj"/.,t5]WX40$q#
lNNul0@v?:s0xUy)$ZfW$gCaJ+a49g_[^)#!I9a(tsl#NIalo;#b.*f"QdgUuZWTh)H+wg{D33<USk*cehGbi8f5Wj|n=&Xq`3as`E}25jEquS@IRKv?mu?NOL~F;"`S)"J5Ie]4$;;$5&K%w4Hav^n!`d0Xgf$7]"p.N7B@mB2Sn=QTsnaEgDlN;V7ek*Ie5i(]ydbMX#G5CMPEc/5>|:pAgs$/essWTa:Um1^Rf)w5fYQ1QZ:0U2~ZGuF+kKl%A0A#rdmU+R%FHoFM;lpW/(c1iNPQi?iK#9
:b&<.gJyM)=m0hmlRkbuOIPMr~mkD55TT5YD9FP"oX6TbUp=+Qf{w~dZVLfIFOJ_5G-0Pa2G+H.uCtr.MlMgn#kC:NP5l}/93Qk6x,6tg*&pxHx"F?y&EOOnBxz&Y!;*S=sQ%u1;Oqlxe=o.6FG>VB9zU1[2[@6~tvMv!31,HnE%hNf_MMo_e@7J&Z-ZJRK]Y}l[FnGoZz6+c=mYUKH~t}]IM2CV)2#.dhNNZd0hh5=s?3xx.A`>1WK~7l8{mh6~i!EG,rspA&ffDA02
)<2+VjW02tE)_CFXy-A.G$g?L8_W-1H9(YJJV&ys,2rXum:#Mf1i-20.q=9R#K0L|x-$}jwbrld%:h;=t%uF"s}<x7jc!I=.4V.p&]c6a`5ZS":GTFm@jA2M]XM,d5rYk<5dF^Gdl2`Js,yaYW)Nztm6dUjP`S%[6(&8;E#Nuy;S*WD1`&}$o]Td,VWVLaDsh
)<i9dk<YTsyw|k5&QRdkYtG(m0F)$n2B5S}A<c>&$/Zj$Wp!j$2SGdD.ns;O[X=#UGZ=0":UKQ!7*3C+{/owwF`Chih=5"aZsg
TGcm%A(c3pQujtOFUE+2bq=V.pV]$g]8IV9s0zom"`2`d)JlwFsRK=K5Pm1v[e*}=o#LDM?[_0B`Xv<7o!"b';case"th":return'+s`&#bKY($et"Mi"%6f%[Op8WN02<(P-vW*;.kJ<u:t;/P|T=-l/xB]sO6?qbLSjy+suK-%I#AD^BsNXcy$Vo-;g7-xQ
ker]u#^~]G[S:T?J>J?J))YwhJK#<[/a[?`po&>j=bY0D[D-@M;R04_>..odMx3-s}#P+bqx9O6T0f]BTSPwt(`Or>)[)}B3>*=Ti"+gdYR|5LU`+WXTB$#<v{inuDb()z`&[Je7O-)nPJ^z=u_VY{Z>2(:Y)?aJtc"oY1Oh8iD&_~`"IA=BFjcV(u0eQiXx[rY,^^
<1cfSwUVzH6W<aOs{Sd8Ya9eah,*h89Kg-wsYuJs1&|+VId$SU|yNC3o5hka@9C%aN5NE%Pbhr
4$$S%LFcIh&!!<Z;b:,3+,?2The0e]W"<rT
P-yM/yFqhW`T0(Mx9#hu;p&~s~>L+o$:eV/rmEW=]T&x<[sS9A8l:(f=UPNysSUN/Km@ClF{Xv+%6O[}>]Cz(wi^PGC;"y6UvGWj1mbcW)WFN
QhR3+v$y;jNtu2!]-~.=EpVzO=W,"[;KW5%qdzxyvMw.9<r.:G$-Kc*VB^+/&-#}#?IX+9p,0r"&lafP^v+C`@Y(4~PoN;a8.M
9GLwjC>GG:IebwKc&C+I(tMnA"p:7BN+)W.lon!Ifm,3ZG.+h3,nF3i@TE#2#FJ
?+(i.rY/1cs+J9*fiTmiz%:&A:)bNO0Gtic&-xz4Uo]fSL@*`1d"@O3;&rYkSWPx9qh@k7I!_@%7P.~w_u6+q/G$kx+Rq0{$*XL(IERl>S39W#>f#3MmJE.*%7Kj)r%EE@/+J@A`b26*{36gkxWA&M0:K!!JgRm*AmD4ZurBJMrIDftrLN%p{djsVY_F1"<#)P=5y=gy<(sP2VY4+PnH*2^-6w:w<7q=6HX>#j.xl#8%LTql5(sB>CRshY/PXUs8zUsNlv9^)W6VCb93`kCx
Hb9pP>rK%-O^_3Gk>+=3uu9ijHssZR0gUu
qG$mT
|J=R,!v,f/VfqvU(fUYk)AN;kdw3X9-=54tj+/`P=[8Y|Glu?e_LQ<{upsOLM<=G<e=e{t(F5g`(0294PU5yLvgSb6NX"sjOx]a#fm6dP@41w1r`<5vlK"
Ipm?ZURbw{IpL#$;ZPB>D&mt`^C<3Y3?AX>e6uaPbl$8S{JJ5AG[o1,]SJZUc[h[fJ,F,Yha]O]+G(D.a7-yrBp-FJ[D)>EnJ9Ru0hDSHJwhP$ofgJH4$FsMW>ow
Yk%LN_Rd
[E^U<U"VB_TD#vyLa!kC/@:cQL5Qo1Jk`2j`Dx"nsD1b+y4Y^=it92)j_*7]0.v>i1P$=mh4&aXF-.xZF{t#AC0)m*hd-c41UKFp=GHPcu""';case"ka":return'#s`0:6KZ+&iq42>fSPt"corkM<cuC"-d);RY[[1RzQTRgW"42#1X"GI(dUN
{t,jfM@RD9iN+alv2tUIqvL3R`ykgGOEJKosks+<FBhn<hZgjE$]YypD5Xq>|TVX~,Ez)[&LY[>p$m
M[L.4Ig{j}W<$AD5^r!5fg%q.uf!YfK)w
j4@q(1v[@MD~6]I_3^6Fd0oTJ:6+V@sGeV-[Fd)oXdZu[(C)O1S.8`9>9vj`4-tJc}-vq[lZ3yi-(P%rs~EM&ry&9$v7apMbd%fBf@!fgB4",7&CbG9rQL8qBQ3FX6kNKt!x/v+kZH^t&.B7,^>MqI!MW_7(Qy:Dng(;mT_."qYuU,ps-jG77?-y+KJGu%^aag;O;]GuPz1NwL9cE`je;ujI@*C"=X,43ObZNVb,0>f8h)H`>KmUgd7%c)I"IX0!`/Gaq<((4#mvf.2hDTyO@kQD6Z$*MdlwM+.|MWZ`X=[&Z]d+R+)FF2[WW_U",D!!Q[ll9%if&:X)]ZkDNCgxEt!W?L!rR]&:1WE!vfR.7FDf%v1N`ZvJ#{RFtfESeMB"?:^4_U+1BDGYEiBb1Ao#_9xXHZVqfo.4r69)
rLk$Vf!V6f@y$En9Lc%rp+M-*ly+7a$%KlDo47lwP?R:WtQk>Wsa8:2kR1e+uk*#&`]UBc:aY?fc+8n6l:|rY,8`)?*S1`#U0GX6hd{5g=6@Vx$QtZU[CN%/wNLH:PD21wzSUwZto7O/yI
%mWe>#*]F6/(nUV.0;ceq-xfSWarI&i~)=z$`1PCNvEPZZ8Q"uuHE4o,d^"Jj7*aQguQd0bY_{wWBko+@lB}6=epxt"][fFAa$27(<yk
gn9m
R<z%`C.X8?^[U=0iwks8&e;h`LQ5U95r+>gv&zb"Kmq}3j+ol2ig@
pLkcT|/3kw?j1G!yn>a<)v+vx7L]Q(D>9|h#:$5f<7bBV!os<XINfg#YsO0K3tUlSt9/^Ti&iPV9%ELpDO2eXs4.1@9H.P_dNDUmH_8K%i"lYh%sLMXZV;k>L50vOv2f>&nmLC?KGEUgqSNGXLub`*[I*/`]6W?.ATop$kN>F-ea/*>KbOh(+Q46>4B%m9;W)z=`8Wdt>gVuZ=WkmE(ZJ
J]G!2RvSc>4E<7kNxH@Jn{m4Es;x)D%`1^C~uExa;v1ftqCAmLGq
qiI*7l~dpGo]hm!][g3A5rS`Wi)B+SJ,*0J32O&
[R*F+p,EuO=h:+fyETIdfX]lcQ0vk!VSR_()-@&8m&uj_9ecJK7rSfk,
-H&oYm<BD]49WCTC6M;lrJD^UPjMJN!{3R"|DPsjU/[/Ty_!Qri)hYnryCZLH?7VeXQa??fB"Yq[E<4C3k&*Rc(t(0dK
{N~..=pIM!Mm&&9Omv~8v^%f@Z,s1[_d7JnBN![0KmUD}"E0Z71nR_&,Qqzc/N_tyhTL?diX(FF8qNw0D=`oI5x(iQOcQ0B6r:fhxx8=^tu9Y!b-^1<ad_:t)82Ftph5(W+^,%SS(9XGmPrjjLFZ2-qs
VNguhx^x`?V.oK574HAn%{AaIF%DB$o>k]D%,%50&0^7`ZL/qXB[
Zk?iJkmqk]cc90Q`p$Gz#Czt2PKo"#<]W^EZFfV-V5-
NWX<1]
)b<z=oH!f0EE]R6p2j]GODLq>:_YbV?:_7gurfle"
`*C!KR]C/+JUQ<CSUsf=6iTK9O5_RB3(TMv{.|H)R;r:O~a+q[q}?]jiHNGxZ%Y"jXR+]Tq=T_#|cJLXc-HXjX8|V{@%Dm%4CMy?*u1/V/XtTtpAFSyV!
RjisKuc:5&$TLf:37l[D_OfP&ZvPQxq`q6%w#[/*E>8A2u_Wv/*c-chxWNGo';case"ja":return'(X/*of{.Vh1t,wd"y.xw#gmuT&u7>]5nASK+;Ip"I8Bss&
-TC&PENWq3tXK<Ys;>ua%B6d(oxhUU[8&H,luo[xe9UwTCBnT6RhQz:w.}!h7[Wh8r6;:M5956W#i&><up
V62uF
t*N[d+L]WUINNB+JaWO>h9cTG.Kr7L)VN*K"[x9-G9zOClRE_UX2A$kg-4e$ccTK0*_^z/}:|e~dxXm@f9r.[[kYK.muWt}r4`@wqHlQjdzEJoKyP$a!n7y1L0hGJR?SO^S#/]i<XQ!U/pl9uvC*c1X,-6!N3
8Kn
Cd=)fV2pC*Sx#/>7sd]LITfbv0VS1o?]iet$.`!Ixdx1C
8G<nCy<Dze>T*`wE
NYH.[L.%=`rdIc5m]aX@8:]<`6M>3ZL}@K=POVf9y{q.Z9;zn<Pkv-*rqj-_
:W
Gr]%>SL?cqeuE8;fcFWkm2G4&gRa,^J}*pM96Qv9<mvi6>V*#
)D]TjbDe"?IliT1c+3EdbxnBNG_Drs+7#D6U#gi4e1C(^uGf1o*"DLfvRb/q
[Ojx(W{>]<s2J?8o;wk=_?=)X;V4D1Nla;h%8-WYG9@NR[d>DO^?i8~s1pMmq],:H"#LqUG7%4iZ$U(`Chzr,[/3W!Nf%qc[f*^o{QKV5<F&Vozj-q1.H[G&u41tN<>ABLbUeV*df?!_,eKGWvuY9%hI*VA3[MjoWy&%I[u2y9:!y187m@u)exiG,%%y3DMN9!XG|di#<J82l#Gr4]Z26:3MMhP/|DrQC38*OqUKi0IE_[N-$(u:Je&$EYyR~[/Dp*KXh)_doHX%[k}_Qh:/NFed2O
$`>nFAt=vUdECj=}l3dU+sV8<..aYo7T`a99"]"l3+9b6B3TO^NZGn[^J[=onY]JA;XjJyf/[kn_6|O8E
Z,*sAOr}w[ClW:cgJcX*EGmsZJfrlEj!mx@[2nl+Eb)M7PWC$|V%;`RmDI4"lne&:j4S]-,v!Z`EsB`[)}NJ)(a^2,&.My7hG4%_0-oBa^+X^Q^uK}ZGM+Q:/b+7*D#l_;/i]L8%%eUA8JSo>eIq9FxM(<dpm~e_7/^g+LW~996.V~45s+aX"Xhr5J>#t#S}e?yIYe
JE^wMT?KWS9y_1gHdFPN5dpFFk)Rw]m6L
1nGblje;(BWPpolQ:KQfwy!Spb@`u=;e3IA(/Wm3Z`M<zeI[_:;-v$~fy<zwv2%[y-`Wmreh|I"-YJ6x~xdRe<@d~bM/&<u<V#jFvP]2)Ej_,?OqV9**Y*<hM(6m;VND
?M
[vKu<GqC&P"9wPaMC.2!e)A&NI,P{@D(}cC13N$1@IA]5:,kwitF1QW^v%0KSfARTMA3Yd,ky_rt611*nlI
pa2FJ9EP%Dg$T:*sw(a7ge%,76=`ktooDRnvEC//@#0N,V^t!ZZGr9Z]HKO+~@76[$+AU6<%~gB0*z$q:`=QNbij|&!ka%J$(cnZSKlC8q)fmAcAhE,
^_ytm-5Xz+H,B<E3@H0^?JqSJ/TDmyYE1Sf*ube0h&-W]w14T9_=AsS=-?XYmN+9&A(?/#?MV_a?@W)uJ^G
.^*c$FM^"pt7{&s3Eu@ud;jb35MB:m1(PnWP8AaIqg%O(->6C"svtVO:.z#NOkyEQfjwH%%o&I_2
9Al25BqYW-d`EWPV?S`9
unF78D^j2(IcUte$zb~"")yWbCswhU(lu?N0Z`oV)3u4(UzN{#S5kfhm.TrQMCfA:JHWN5v%y4?K9@_
6M+V`e?P5)#[!f{;Uj29OI=$0L(3KbhvJVVU{bGp9I!qIYYrViD;%R~a/PzEw7R(;Cs<4sWT~^+"TtM$D+1,g2@o(y{fFHD+,z)l~Mz/EGlML&#a{I$7IAY.dj(6ZMLOz6.Y[QbZCblvKI>a;>/LRjvaQ>HauYWq`Mt%yCuv6ija^@&^L:!C?joI_E;fJ`=.9et*2-0-rlyhT;^_$EWy[IzFdE@!IrkvDCn][PmNMr[h|/FdJ`#mJnda$w&a%d-1b[[tMukYSVs:|;7/HX^sWt>SGgy.]g6d_VYslmvK.eyxQx%an"TJmd-qpG8Sg.5UJYrY6[yS2:;K%XLF=2S<!2]1{rV<J^KL}C,#B,>)$8""b';case"zh":return'*UEwn@M.W&i^+sxP"XJL/tW<]Xq?8d*[Q<RYOQPKcOjF/C9_OY6!B3(_?FiwbG}hE85q=2I%aDpRK2vH+?CY?h-Zt4nD;rd;Qa.0JyYL"D#4ZL2re0a0(hncY]:3|,lcZI*X]xv:QBrUR?w?N>([Mg3]2MR7!#t]|;fR{Q{gzJl.5E1MrRP-B
~rONUELKL:`U]aK$nl:O;CMd#O=vAUAY)fPr0!;RN)z)1W[@RqfX-JwfaG>*bt+n[n*_=
Lo3>#b;tGg>Z=FuJ2lk>o
JsJ7~Ip=!w-`N48_@)`m*#<evc,2y
P4lnOgg+T>Xv{3H(g#5xX7g^K:SBj[vX=Tn<dfSf#SZJUYCd5<a0NVLs,_6rtZmgR/qE`-_2}@?V.6PRgx24.bCqr[6Kof.Ze:#=)Z1/I#/*%w7q9>Iq8R|i34cr_;4xokRI19i?(JeE4A8LQGn:pNuK)=,4ae{1lFA+<?n?c0XSOGcd}
F41@!rJ=yk0#35!)+^Y_A(-,2!7UAxzl!xq65tWDZfx5@8kep:=_QHRE9i_]>8_eVQ#*
fj.-
cXY=qj/<X"ke%9>HV:D65L:P{_R%90R6yKSk%jragtIm.PL8`*G7Dw8%+Ji#(xb<X_9#%7xq#2<4$`_,NV=<
h|:#l4O?1jdZu10hS?.!=-HaJ2k]m!s8wFBRZ&"TuyJY+].$2"J4FI,_A@oA!ew;Co
b]{TlcTFJ1ld{Gu%d4/[S:5y7?60=N5a~7HXkjn#TwznaPSpK87%bI<Ua7.37R]IUjo13A.&:4
_a&oo$oaLoSVjj4AwP7pu,=6$^H,yNAr38ns,kWtBybI!sAIT0l2KxA2E.gUtg,lbvTdFdEa(yiO_-/s<|4o`ti=bK5mU]^msvyb,*g83[[=I+e<UV:9Z8DxvDCFaPA4P}QuDW<o%AALXm
|y{dzYnk07.
>XK:n"KPa2eIH?yn5<c)bqMY{"tXIX29VniajG$qfLX(Q0<#~fsgZ9Rr#CP6@,@u5GaxOQo-)P<J~7W<0sa&S3m(_2zsnfjE:=]?]Zy!}eq%,DRG^)Jwe5GX/h1N]Jh#J8#G)iuLtwdn<xMg6O=ud*^LcIE^hx;^RV)muq.F*AVsTP!Tt#w]
d_vmWL;~V,7PlL
1jkZ!GG^ZhD,@XsuIFs3!%J^1_MH"<v&yS<LOW.vK_dMu-Um;t6C0WG@!tzK_yT&.aK!KtxK,t07jt:=$D;5hz&:5<!7satF&5UX=,E3[0A;~F!@zmnWQ/`T(_Q()sn!ySH"#/m:v.BDbFgOiYskw:vh
A}/p.y50+7]9?Uh)p+E+A/ow73y7O
$v/l7L3,AWsks/mmsBCCfnFUNRe2_P[>N]JDCeC.]-![V%:a)W:U`JR4ncf?naGLr(j#sI1U*EN@fsw_.X1PvYL;jGp;Up>+"D(IIZmnWettI
9nk+G)G2*Fm[C}ydZ#$_Q!h7p2GSMf3NE0u5!|?_HUi7GZbr=?a|8fA#y.1q36N1/89_;[D4,ubO`|xda:`/$x""d5N[Clb`:.)A339PVIMtGc]C0"5oy_jMxV#XCN3|vRVx0+fAtNaV&8^VO32<W2liW`2i@w@+(#f[Eq,>oo`go+C+>dd6?7&9rz`f5{>ZpyQxg-U{V$V%=,96Ub+V%Tab@o*FC7;pF,b8]-_T
|8
u~Gz]x
&.Ve_6mrg!
Dc/P_]YLFfwqoq%4rb+!9}"xH";2[nGiduq7J3cvXd-($^sg0NE6@<.<px6nR[
{51,O!g&14G@4n~""';case"zh-tw":return')UEr7lM.W&ih~GSP+EXyXL;xWpfnuLp/sh9!z#f8,(Y/M#!:6TK+"PI!yKhB6!JX/!c:IlE3JE7gB.B8CC$n?DvL:8K=!>eEf
k_z*}DbLQ)P%Kv/c:4;)K1=+#S,Up>Z,IM;qYMWW~sDgJ@+3ORY8`P*@O<%L7)~.hJ^m2h3h~IHKxp<f)Dow=^c/Fe7rb`P_"1((_]]i-[eI0C2cwL?<]n~PVD?oo5deb@9]wqP^2[6M@Te_WpDJI8F6G2tFK*&mm5L!=D24Q/HVnejB0:&a2Z_roUHcN!g78.:hc=?&kJZs*Q+sUa-v2uWM|y.4PxTAf0ag*51emlaU_!pMr.s@ilWEq1r^5_"Mv7GBI&0F?z)2moArX?$=s0g`4sYxwkdp`#Js<@zUbaEop4%IOt#8>k}7yZLR9na4Vvg=m[SOI+
_Ni=abF74:6~A>>l<G&BUd$;R|Rpp6uHFd>;P-^Rp}ytZuX7561HKP@`)E2@o#bDCpR>bwSpHq"OII
xMCW]:+Pn#PSGNciFGdeWc.-D<(hYim%B*
YEo]9+,0u#E|PLP;)LWzG[=;Wc(h`e
`mmnl$aEh?PQo!S)
[w_9r0h24Di=,lRo"tnZlqK
19H?6[a
N^my6z32!j8YJ93#xPN6,wuT68XAe{h)^EEzO(C@$D!7%jl=biS~fSH`#IgU
Zh+?<=jSTpI;,,_!|WaB_MQnqZPB+j(KcVbfV^P1g3(6`g/F05!:[mg`ShZL
x5R<)6To,JI;U2TaWrnfLClIN$mz1;TCeEf>vFk;X`JD?2=|2&+:-p>T.7Fw4dF=@0=2nol=L0;@O1HNJXMCkBp5q.PjW|?73nD[K&UjkNoYP|1_I<b_hnM$<k7$8yCj2Yp~T^uAwGOsb5#>A.v#<zD3oj0UteY>,R#7)2064Cp+WTk);&PV8yk+u9R*B@4B2AF7(1n0L(p}]2,*,o""riFC^JjD;Bya792oFKo}ogsj4Utd/E+3>+%v=|kC!f7KL[?}Uihs4Ix]*vo
`$NY8N8&ia/b1qP#w!@~NqxydK]b+XE#VJ[gnS$`lp9$$j)C*qo-?G(
T@S*[2umn.H<`M1FX4H(_65fbkxdG=bOsoq
pAH&l7_2&3elu1S!TaS9G7?]9ptW]Wsj(S`:1jw2qG3w?)5uGL)MC_l2c]wWaw)O)@X1w1wb"0(G*6R%uKcSM5#tw>xP>
:9V0T9OfFVR0mWVO0L`]lU"3d9c^99S;kw(%sL?_b1qU,nXyH9D3"ujU&-h#Xv%ROR,"RYmI:{mZhABvmZj"*sk`-)X7gB%s:k+%"rb*%^`@b6HrY85iKC-}>3^A@g4[OB+sQceGUsDxZ;mIBU9_tbL6E*@sqO&TRzd=tI=lih/F^BibyUA2/|jnYnK8^v!$ApTq":gl`RV7s]s*[LW(QZai9_>k`fQ?"196@7ii,}-h^Bcro9(~<pBS[G.Lbl[f<TNj)<#CJPY)SAdUX$5K1*&A2/E=qG<R(9@d=qZ;Z"u%OmkF$QoKIMv{0,pBI8Pls}(U>o*<T)rV+Ltu:k*>,jN}_`vh&/b<;1lYC-;x2SBX+Y!!u4*_k)Wa^iQ@O[^F"kZe
lnTd.4wkNkI-3ni4e$jv02-,C,_usIApGdP*^MzDUhP6VvFI-U**"]gBcU/aRO;8
Z3"Wj23aDtO9
eiY&H#iSvp.>znyWin{
CNSm0oE[pw/dz&6e(Kn1O[JbG#OhLK9-<D{Ft+ibO2PBm`AYvczM+4)d{C9vtS#:g&Bs0M]cx&UO[i7t#1@N3,ANz0+Q/U+8Ku{">*wRCY##E';case"ko":return'$UExQ;zZK$c^+u]+/UERg:Dn<w4T7T_tx7m!^2^siU39}d;.u[n(&/=ONa@>&,tOoqRy`jAl-S-<Ct.Cq=>rLMz>cjD6)m$_M)(hW
%Om1v,g7bAjAV=0Va8>0BN)ks?%A~Y<M1^Ouu=e^,iQdYV->Sr1m@mti3$Q1"@&cA]RK5xS
7tmv>MMS3;II:Ru"SZGd/g!<w,`kN<6CNC<N$exf`[Xy08SI]Usv|aTQ`Iz;P_mZ-pf`~T-r,OZa9Lzv:A+/Q)D8"pTdF/pxD7Z0bX"h1o>y@b{6i7W3+wN5/s7_VA$-_KQhcSLpmPclu
/5IP5=hra,:5=1R>?q@`ixW+(uc=pk,45:O9yaod!=e18K8(0AJj#6Ca+J*-H^s&9E?t=!<(V`tb`n+xelmMhMp6QI7;@8_Y(-(fcC0@6
$,&9([hOu10ZDqL"i-a5dRT@V!5id%K!yLSfy(.hLxJUZAp5<7NeLBy4jT]"b":b{u!g_;P-GKbwSELH4_e8b6mV3Ot4|;DJQY*1=H6l*5j>eW%T(Pi-[Mz"b*#DH"BZM+uf}]/
ba]#zhB5^4ZeO,^f6]-k}<$^`;V:+B"9D!JXD865!/aGfqR$An@M{2UOR3qxd<U>4OV?:2Vta]PZ=F=B;]eHN[BxYHX:W2FRU]{H@qL14Gb9wIp>Hupl^c[h5#gO{c/-.T0ZLr89(ogi4"eI$Q{`@yiFiN3glId%W/:ByrLuunF"XuME6RTfX7P^iWQuY@92@2H2{$r";tl=Xvi#b:3,o>mdt`p#&:4nsgaL!m$Lq9*P<]$qaF!f>5I<-AkW7h]+C]Uc%M[s_I,Oe!4^Kq?Bj<I&<NTq1NNu7o
C.<Sm6:Y!Tg:II&5fQHK^52k/?*MZy7Y8R?i5Nl@mdx&U&e8xSD?,@@!#dlfg)w_YA-/O%lDGehpwmwpnJI*d}70Scc.xUBJ)y59yCM|Gi!{b|`Z1+q,]|VkpMyvGWfObt;{hp.paG<bg|apEajYr0>amY#`$J:8Vk3^&b"R]A79)i>X>L
:.xx^p>)I2;_@,ZKzkc6,7J[lZN23hXe
c
K7ec_s45;Pu"tcGwu!*`5b+6p#Ikk5l#E{`:7J&vv)xdB`Im0hW-I+Y?A`aZC<gQ>~yq[*e@%aqGWv3&I%d7*:o.X*DA

Y8/0b
T$>+61n[ql,<N(m<";qK?(J+Os+q5a8Ea,y3#69_Ws;Xwn_QjZnaM+p3F%fpEgG<^.F.bkaZBJE4NM4MgCNO)Fb~%A&y<YRh-UH+y#ijxBh@"nXBi?Dvj&`q&<y^JO6@G&M}c5UQb1"j<w0kEh>c*s0S5$@eH]A=,u6fd*@cVQb{1fEgrVNT^?mEie&eY*VLul9/0LtuXe:rC-q-!$nb^XfZYoj?i@q^36k"a)7C&obmx_:!96dJx;Xcy
q:Z/m^4j"UPFKc1xGU31(pP|gF^yb1fb4nt/Un?BeAVhJ/xD6LZQh=*
)kpy)h@vY$@k6GaA
3G5CFCWPXSfZ0O!JVb&2.Y{Tc4e"Zj<*^<wB--;l/MAh:oqbP.,y,SCYP;FklZkh`.@65e&:CV%N0]-&lI;
NXg/@.P@rh/WH_(pu[5BOyd%tfPSWA{]5E}&.
x(|Psnc[!Cmy%D/]GtE==Tz$22R9gLRLNx*By?kBhPkfLp*eU&GW::7GB_F/g@a0alk*<D[8p6!0aI,L>MUwAO$0r1y@;)8Vz_Zve*p[2
m[+wwUQaZ$<On4|2Hyj=N(5E^aOhp<QA8YicDG6f94Bf]67ob2kn2R|SS1CP71vF_nxS(nV>lwx6?IAQ6T:p(^.
-Kk^<3ATFqBGTAcE$I]<)igH(9`Bl$Ae#jR<A<W-FV_+tZN6>?G]4m#4ec>)fJ6h7#CQsQ(eTf%rSTr>-GFdmRmaFDz?"c%7nh5j&bfYGCrbTPJI*L9*+bOJ7gnl);"l3==-[aq597cjGs@hw`^x#ko
Bh-?E@"VttAwjUjQno1';}return"";}$qj=LANG.crc32(get_compressed(LANG));$pj=$_SESSION["translations"];if(!is_string($pj)||$_SESSION["translations_version"]!=$qj){$pj=decompress_string(get_compressed(LANG),(LANG!="en"?decompress_string(get_compressed("en")):""));$_SESSION["translations"]=$pj;$_SESSION["translations_version"]=$qj;}Lang::$translations=array();foreach(explode("\n",$pj)as$W)Lang::$translations[]=(strpos($W,"\t")?explode("\t",$W):$W);abstract
class
SqlDb{static$instance;static$untrusted=false;var$extension;var$flavor='';var$server_info;var$affected_rows=0;var$info='';var$errno=0;var$error='';protected$multi;abstract
function
attach(array$L,$U,$B);abstract
function
quote($P);abstract
function
select_db($Qb);abstract
function
query($D,$Aj=false);function
multi_query($D){return$this->multi=$this->query($D);}function
store_result(){return$this->multi;}function
next_result(){return
false;}function
inTransaction(){return
false;}function
begin(){return!!$this->query("BEGIN");}function
commit(){return!!$this->query("COMMIT");}function
rollback(){return!!$this->query("ROLLBACK");}}if(extension_loaded('pdo')){abstract
class
PdoDb
extends
SqlDb{protected$pdo;function
dsn($uc,$U,$B,array$_=array(),$db='PDO'){$_[\PDO::ATTR_ERRMODE]=\PDO::ERRMODE_SILENT;$_[\PDO::ATTR_STATEMENT_CLASS]=array('Adminer\PdoResult');try{$this->pdo=new$db($uc,$U,$B,$_);}catch(\Exception$Pc){return$Pc->getMessage();}$this->server_info=@$this->pdo->getAttribute(\PDO::ATTR_SERVER_VERSION);return'';}function
quote($P){return$this->pdo->quote($P);}function
query($D,$Aj=false){$E=$this->pdo->query($D);$this->error="";if(!$E)return$this->store_error(false);$this->store_result($E);return$E;}private
function
store_error($F){if(!$F){list(,$this->errno,$this->error)=$this->pdo->errorInfo();if(!$this->error)$this->error=lang(26);}return$F;}function
store_result($E=null){if(!$E){$E=$this->multi;if(!$E)return
false;}if($E->columnCount()){$E->num_rows=$E->rowCount();return$E;}$this->affected_rows=$E->rowCount();return
true;}function
next_result(){$E=$this->multi;if(!is_object($E))return
false;$E->_offset=0;return@$E->nextRowset();}function
inTransaction(){return$this->pdo->inTransaction();}function
begin(){return$this->store_error($this->pdo->beginTransaction());}function
commit(){return!$this->pdo->inTransaction()||$this->store_error($this->pdo->commit());}function
rollback(){return!$this->pdo->inTransaction()||$this->store_error($this->pdo->rollBack());}}class
PdoResult
extends
\PDOStatement{var$_offset=0,$num_rows;function
fetch_assoc(){return$this->fetch_array(\PDO::FETCH_ASSOC);}function
fetch_row(){return$this->fetch_array(\PDO::FETCH_NUM);}private
function
fetch_array($Kf){$F=$this->fetch($Kf);return($F?array_map(array($this,'normalize'),$F):$F);}private
function
normalize($W){if(is_bool($W))return(JUSH=='pgsql'?($W?"t":"f"):+$W);if(PHP_VERSION_ID<70100&&is_float($W)&&is_finite($W)){for($bh=15;$bh<17;$bh++){$F=sprintf("%.$bh"."G",$W);if((float)$F===$W)return$F;}return
sprintf("%.17G",$W);}return(is_resource($W)?stream_get_contents($W):$W);}function
fetch_field(){return(object)$this->getColumnMeta($this->_offset++);}function
seek($eg){for($p=0;$p<$eg;$p++)$this->fetch();}}}function
add_driver($q,$y){SqlDriver::$drivers[$q]=$y;}function
get_driver($q){return
SqlDriver::$drivers[$q];}abstract
class
SqlDriver{static$instance;static$drivers=array();static$extensions=array();static$jush;static$passwords=true;static$serverSchemes=array();static$serverPorts=array();static$serverSocket=false;static$serverPath=false;static$serverFile=false;protected$conn;protected$types=array();var$delimiter=";";var$insertFunctions=array();var$editFunctions=array();var$unsigned=array();var$fulltextOperator="AGAINST";var$functions=array();var$grouping=array();var$onActions="RESTRICT|NO ACTION|CASCADE|SET NULL|SET DEFAULT";var$partitionBy=array();var$inout="IN|OUT|INOUT";var$enumLength="'(?:''|[^'\\\\]|\\\\.)*'";var$generated=array();var$primary="";var$query="";static
function
jushModule(){return"";}static
function
jushAutocomplete(array$S,$Fi){$Ui=array();foreach($S
as$Q=>$O){if(!$O["dependent"])$Ui[$Q]=array();}foreach(driver()->allFields()as$Q=>$l){foreach($l
as$k)$Ui[$Q][]=$k["field"];}return"jush.autocompleteSql('".idf_escape("")."', ".json_encode($Ui).", ".json_encode($Fi).")";}static
function
connect($L,$U,$B){if(static::$serverFile)$Kg=server_parts(array("path"=>$L));else{$Kg=parse_server($L);if(!$Kg||($Kg["scheme"]&&!in_array($Kg["scheme"],static::$serverSchemes))||($Kg["socket"]&&!static::$serverSocket)||($Kg["path"]&&!static::$serverPath)||(substr($Kg["host"],0,1)=="/"&&!static::$serverSocket))return
lang(27);if($Kg["port"]!=""&&($Kg["port"]>65535||($Kg["port"]<1024&&!in_array($Kg["port"],static::$serverPorts))))return
lang(28);}$f=new
Db;return($f->attach($Kg,$U,$B)?:$f);}static
function
disconnect(){}function
__construct(Db$f){$this->conn=$f;}function
types(){return
call_user_func_array('array_merge',array_values($this->types));}function
structuredTypes(){return
array_map('array_keys',$this->types);}function
enumLength(array$k){}function
unconvertFunction(array$k){}function
select($Q,array$J,array$Z,array$o,array$og=array(),$v=1,$A=0,$hh=false){$He=(count($o)<count($J));$D=adminer()->selectQueryBuild($J,$Z,$o,$og,$v,$A);if(!$D)$D="SELECT".limit(($_GET["page"]!="last"&&$v&&$o&&$He&&JUSH=="sql"?"SQL_CALC_FOUND_ROWS ":"").implode(", ",$J)."\nFROM ".table($Q),($Z?"\nWHERE ".implode(" AND ",$Z):"").($o&&$He?"\nGROUP BY ".implode(", ",$o):"").($og?"\nORDER BY ".implode(", ",$og):""),$v,($A?$v*$A:0),"\n");$this->query=$D;$Di=microtime(true);$F=$this->conn->query($D,(!$v&&!$hh?1:0));if($hh)echo
adminer()->selectQuery($D,$Di,!$F);return$F;}function
delete($Q,$qh,$v=0){$D="FROM ".table($Q);return
queries("DELETE".($v?limit1($Q,$D,$qh):" $D$qh"));}function
update($Q,array$M,$qh,$v=0,$K="\n"){$Y=array();foreach($M
as$t=>$W)$Y[]="$t = $W";$D=table($Q)." SET$K".implode(",$K",$Y);return
queries("UPDATE".($v?limit1($Q,$D,$qh,$K):" $D$qh"));}function
insert($Q,array$M){return
queries("INSERT INTO ".table($Q).($M?" (".implode(", ",array_keys($M)).")\nVALUES (".implode(", ",$M).")":" DEFAULT VALUES").$this->insertReturning($Q));}function
insertReturning($Q){return"";}function
insertUpdate($Q,array$H,array$fh){foreach($H
as$M){$Z=array();foreach($M
as$t=>$W){if(isset($fh[idf_unescape($t)]))$Z[]="$t = $W";}if(!($Z&&$this->update($Q,$M," WHERE ".implode(" AND ",$Z))&&$this->conn->affected_rows)&&!$this->insert($Q,$M))return
false;}return
true;}function
begin(){remember_query("BEGIN");return$this->conn->begin();}function
commit(){remember_query("COMMIT");return$this->conn->commit();}function
rollback(){remember_query("ROLLBACK");return$this->conn->rollback();}function
slowQuery($D,$cj){}function
operators($Pi){return
array();}function
convertSearch($r,array$W,array$k){return$r;}function
value($W,array$k){return(method_exists($this->conn,'value')?$this->conn->value($W,$k):$W);}function
quoteBinary($Mh){return
q($Mh);}function
md5($d,array$k){}function
typeName(\stdClass$k){return(isset($k->native_type)?$k->native_type:"");}function
warnings(){}function
errorLine($D){return
0;}function
tableHelp($y,$Je=false){}function
inheritsFrom($Q){return
array();}function
inheritedTables($Q){return
array();}function
partitionsInfo($Q){return
array();}function
hasCStyleEscapes(){return
false;}function
hasEstimatedRows(){return
false;}function
isSystem($h,$I=""){return
information_schema($h,$I);}function
lineComment(){return"--";}function
engines(){return
array();}function
supportsIndex(array$R){return!is_view($R);}function
supportsAlterIndex(array$R){return
true;}function
supportsAlterTable(array$Pi){return
true;}function
indexAlgorithms(array$Pi){return
array();}function
indexOpclasses(){return
array();}function
shadowTables($Q){return
array();}function
routineTriggers($y){return
array();}function
fulltextSql($y,array$s,$D,$Oa){return"MATCH (".implode(", ",array_map('Adminer\idf_escape',$s["columns"])).") AGAINST (".q($D).($Oa?" IN BOOLEAN MODE":"").")";}function
checkConstraints($Q){return
get_key_vals("SELECT c.CONSTRAINT_NAME, CHECK_CLAUSE
FROM INFORMATION_SCHEMA.CHECK_CONSTRAINTS c
JOIN INFORMATION_SCHEMA.TABLE_CONSTRAINTS t
	ON c.CONSTRAINT_SCHEMA = t.CONSTRAINT_SCHEMA AND c.CONSTRAINT_NAME = t.CONSTRAINT_NAME".($this->conn->flavor=='maria'?" AND c.TABLE_NAME = ".q($Q):"")."
WHERE c.CONSTRAINT_SCHEMA = ".q($_GET["ns"]!=""?$_GET["ns"]:DB)."
AND t.TABLE_NAME = ".q($Q).(JUSH=="pgsql"?"
AND CHECK_CLAUSE NOT LIKE '% IS NOT NULL'":""),$this->conn);}function
allFields(){$F=array();if(JUSH!='sql'&&JUSH!='mssql'){foreach(tables_list()as$Q=>$T)$F[$Q]=array_values(fields($Q));}elseif(DB!=""){foreach(get_rows("SELECT c.TABLE_NAME AS tab, c.COLUMN_NAME AS field, c.IS_NULLABLE AS nullable,
	c.DATA_TYPE AS type, c.CHARACTER_MAXIMUM_LENGTH AS length,
	".(JUSH=='sql'?"c.COLUMN_KEY = 'PRI'":"k.COLUMN_NAME")." AS ".idf_escape("primary")."
FROM INFORMATION_SCHEMA.COLUMNS c".(JUSH=='sql'?"":"
LEFT JOIN INFORMATION_SCHEMA.TABLE_CONSTRAINTS t ON c.TABLE_SCHEMA = t.TABLE_SCHEMA AND c.TABLE_NAME = t.TABLE_NAME AND t.CONSTRAINT_TYPE = 'PRIMARY KEY'
LEFT JOIN INFORMATION_SCHEMA.KEY_COLUMN_USAGE k
	ON t.CONSTRAINT_SCHEMA = k.CONSTRAINT_SCHEMA AND t.CONSTRAINT_NAME = k.CONSTRAINT_NAME AND c.TABLE_SCHEMA = k.TABLE_SCHEMA AND c.TABLE_NAME = k.TABLE_NAME AND c.COLUMN_NAME = k.COLUMN_NAME")."
WHERE c.TABLE_SCHEMA = ".q($_GET["ns"]!=""?$_GET["ns"]:DB)."
ORDER BY c.TABLE_NAME, c.ORDINAL_POSITION",$this->conn)as$G){$G["null"]=($G["nullable"]=="YES");$F[$G["tab"]][]=$G;}}return$F;}}add_driver("pgsql","PostgreSQL");if(isset($_GET["pgsql"])){define('Adminer\DRIVER',"pgsql");if(extension_loaded("pgsql")&&$_GET["ext"]!="pdo"){class
PgsqlDb
extends
SqlDb{var$extension="PgSQL";var$timeout=0;private$link,$string;function
_error($Ic,$j){$this->error=preg_replace('~^[^:]*: ~','',php_error_text($j));}function
attach(array$L,$U,$B){$h=adminer()->database();set_error_handler(array($this,'_error'));$Wg=$L["port"];$ee=($L["host"]?:$L["socket"]);$this->string="host='$ee'".($Wg?" port=$Wg":"")." user='".addcslashes($U,"'\\")."' password='".addcslashes($B,"'\\")."'";$N=adminer()->connectSsl();if(isset($N["mode"]))$this->string
.=" sslmode=$N[mode]";$this->link=@pg_connect("$this->string dbname='".($h!=""?addcslashes($h,"'\\"):"postgres")."'",PGSQL_CONNECT_FORCE_NEW);if(!$this->link&&$h!="")$this->link=@pg_connect("$this->string dbname='postgres'",PGSQL_CONNECT_FORCE_NEW);restore_error_handler();if($this->link)pg_set_client_encoding($this->link,"UTF8");return($this->link?'':$this->error);}function
quote($P){return(function_exists('pg_escape_literal')?pg_escape_literal($this->link,$P):"'".pg_escape_string($this->link,$P)."'");}function
value($W,array$k){return($k["type"]=="bytea"&&$W!==null?pg_unescape_bytea($W):$W);}function
select_db($Qb){if($Qb==pg_dbname($this->link))return
true;set_error_handler(array($this,'_error'));$F=@pg_connect("$this->string dbname='".addcslashes($Qb,"'\\")."'",PGSQL_CONNECT_FORCE_NEW);restore_error_handler();if($F)$this->link=$F;return$F;}function
close(){$this->link=@pg_connect("$this->string dbname='postgres'");}function
query($D,$Aj=false){if(self::$untrusted)$E=(@pg_query($this->link,"BEGIN READ ONLY")?@pg_query_params($this->link,$D,array()):false);else$E=@pg_query($this->link,$D);$this->error="";if(!$E){$this->error=pg_last_error($this->link);$F=false;}elseif(!pg_num_fields($E)&&pg_result_status($E)!=PGSQL_TUPLES_OK){$this->affected_rows=pg_affected_rows($E);$F=true;}else$F=new
Result($E);if(self::$untrusted)@pg_query($this->link,"COMMIT");if($this->timeout){$this->timeout=0;$this->query("RESET statement_timeout");}return$F;}function
warnings(){if(PHP_VERSION_ID>=70100){$F=implode("\n",pg_last_notice($this->link,PGSQL_NOTICE_ALL));pg_last_notice($this->link,PGSQL_NOTICE_CLEAR);}else$F=pg_last_notice($this->link);return
nl_br(h($F));}function
inTransaction(){$O=pg_transaction_status($this->link);return$O==PGSQL_TRANSACTION_INTRANS||$O==PGSQL_TRANSACTION_INERROR;}function
copyFrom($Q,array$H){$this->error='';set_error_handler(function($Ic,$j){$this->error=php_error_text($j);return
true;});$F=pg_copy_from($this->link,$Q,$H);restore_error_handler();return$F;}}class
Result{var$num_rows;private$result,$offset=0;function
__construct($E){$this->result=$E;$this->num_rows=pg_num_rows($E);}function
fetch_assoc(){return
pg_fetch_assoc($this->result);}function
fetch_row(){return
pg_fetch_row($this->result);}function
fetch_field(){$d=$this->offset++;$F=new
\stdClass;$F->orgtable=pg_field_table($this->result,$d);$F->name=pg_field_name($this->result,$d);$F->native_type=pg_field_type($this->result,$d);return$F;}}}elseif(extension_loaded("pdo_pgsql")){class
PgsqlDb
extends
PdoDb{var$extension="PDO_PgSQL";var$timeout=0;private$string,$username,$password,$database;function
attach(array$L,$U,$B){$h=adminer()->database();$Wg=$L["port"];$ee=($L["host"]?:$L["socket"]);$this->string="pgsql:host='$ee'".($Wg?" port=$Wg":"")." client_encoding=utf8";$N=adminer()->connectSsl();if(isset($N["mode"]))$this->string
.=" sslmode=$N[mode]";$this->username=$U;$this->password=$B;$this->database=($h!=""?$h:"postgres");return$this->dsn("$this->string dbname='".addcslashes($this->database,"'\\")."'",$U,$B);}function
select_db($Qb){if($Qb!=$this->database){$j=$this->dsn("$this->string dbname='".addcslashes($Qb,"'\\")."'",$this->username,$this->password);if($j!=""){$this->error=$j;return
false;}$this->database=$Qb;}return
true;}function
query($D,$Aj=false){$F=(self::$untrusted?$this->readOnlyQuery($D):parent::query($D,$Aj));if($this->timeout){$this->timeout=0;parent::query("RESET statement_timeout");}return$F;}private
function
readOnlyQuery($D){$this->error="";if(!$this->pdo->query("BEGIN READ ONLY")){list(,$this->errno,$this->error)=$this->pdo->errorInfo();return
false;}$E=$this->pdo->prepare($D);$F=false;if($E&&$E->execute()){$this->store_result($E);$F=$E;}else{list(,$this->errno,$this->error)=($E?$E->errorInfo():$this->pdo->errorInfo());if(!$this->error)$this->error=lang(26);}$this->pdo->query("COMMIT");return$F;}function
warnings(){}function
copyFrom($Q,array$H){$F=$this->pdo->pgsqlCopyFromArray($Q,$H);$this->error=idx($this->pdo->errorInfo(),2)?:'';return$F;}function
close(){}}}if(class_exists('Adminer\PgsqlDb')){class
Db
extends
PgsqlDb{function
multi_query($D){if(preg_match('~\bCOPY\s+(.+?)\s+FROM\s+stdin;\n?(.*)\n\\\\\.$~is',str_replace("\r\n","\n",$D),$x)){$H=explode("\n",$x[2]);$this->multi=false;$this->affected_rows=count($H);return$this->copyFrom($x[1],$H);}return
parent::multi_query($D);}}}class
Driver
extends
SqlDriver{static$extensions=array("PgSQL","PDO_PgSQL");static$jush="pgsql";static$serverSocket=true;var$functions=array("char_length","lower","round","to_hex","to_timestamp","upper");var$grouping=array("avg","count","count distinct","max","min","sum");var$nsOid="(SELECT oid FROM pg_namespace WHERE nspname = current_schema())";private$userTypes=array();function
operators($Pi){return
array("=","<",">","<=",">=","!=","~","~*","!~","LIKE","LIKE %%","ILIKE","ILIKE %%","IN","BETWEEN","IS NULL","NOT LIKE","NOT ILIKE","NOT IN","IS NOT NULL","SQL");}static
function
connect($L,$U,$B){$f=parent::connect($L,$U,$B);if(is_string($f))return$f;$ak=get_val("SELECT version()",0,$f);$f->flavor=(preg_match('~CockroachDB~',$ak)?'cockroach':'');$f->server_info=preg_replace('~^\D*([\d.]+[-\w]*).*~','\1',$ak);if(min_version(9,0,$f))$f->query("SET application_name = 'Adminer'");if($f->flavor=='cockroach')add_driver(DRIVER,"CockroachDB");return$f;}function
__construct(Db$f){parent::__construct($f);$this->types=array(lang(29)=>array("smallint"=>5,"integer"=>10,"bigint"=>19,"boolean"=>1,"numeric"=>0,"real"=>7,"double precision"=>16,"money"=>20),lang(30)=>array("date"=>10,"time"=>8,"timestamp"=>19,"timestamptz"=>25,"interval"=>0),lang(31)=>array("character"=>0,"character varying"=>0,"text"=>0,"tsquery"=>0,"tsvector"=>0,"uuid"=>0,"xml"=>0),lang(32)=>array("bit"=>0,"bit varying"=>0,"bytea"=>0),lang(33)=>array("cidr"=>43,"inet"=>43,"macaddr"=>17,"macaddr8"=>23,"txid_snapshot"=>0),lang(34)=>array("box"=>0,"circle"=>0,"line"=>0,"lseg"=>0,"path"=>0,"point"=>0,"polygon"=>0),);if(min_version(9.2,0,$f)){$this->types[lang(31)]["json"]=4294967295;$this->types[lang(35)]=array("int4range"=>0,"int8range"=>0,"numrange"=>0,"daterange"=>0,"tsrange"=>0,"tstzrange"=>0);if(min_version(9.4,0,$f))$this->types[lang(31)]["jsonb"]=4294967295;}$this->insertFunctions=array("char"=>"md5","date|time"=>"now",);$this->editFunctions=array(number_type()=>"+/-","date|time"=>"+ interval/- interval","char|text"=>"||",);if(min_version(12,0,$f)){$this->generated[]="STORED";if(min_version(18,0,$f))$this->generated[]="VIRTUAL";}$this->partitionBy=array("RANGE","LIST");if(!$f->flavor)$this->partitionBy[]="HASH";}function
enumLength(array$k){$fg=$this->userTypes[$k["type"]];return($fg&&!preg_match('~]$~',$k["length"])?type_values($fg):"");}function
setUserTypes(array$_j){$this->userTypes=array_flip($_j);$this->types[lang(0)]=array_fill_keys(array_keys($this->userTypes),0);}function
insertReturning($Q){$Aa=array_filter(fields($Q),function($k){return$k['auto_increment'];});return(count($Aa)==1?" RETURNING ".idf_escape(key($Aa)):"");}function
insertUpdate($Q,array$H,array$fh){$e=array_keys(reset($H));$sb=array();$Kj=array();foreach($e
as$t){if(isset($fh[idf_unescape($t)]))$sb[]=$t;else$Kj[]="$t = EXCLUDED.$t";}if(!$sb||!min_version(9.5)||count($sb)!=count($fh))return
parent::insertUpdate($Q,$H,$fh);$ch="INSERT INTO ".table($Q)." (".implode(", ",$e).") VALUES\n";$Ji="\nON CONFLICT (".implode(", ",$sb).")".($Kj?" DO UPDATE SET ".implode(", ",$Kj):" DO NOTHING");$Y=array();$u=0;foreach($H
as$M){$X="(".implode(", ",$M).")";if($Y&&strlen($ch)+$u+strlen($X)+strlen($Ji)>1e6){if(!queries($ch.implode(",\n",$Y).$Ji))return
false;$Y=array();$u=0;}$Y[]=$X;$u+=strlen($X)+2;}return
queries($ch.implode(",\n",$Y).$Ji);}function
slowQuery($D,$cj){$this->conn->query("SET statement_timeout = ".(1000*$cj));$this->conn->timeout=1000*$cj;return$D;}function
convertSearch($r,array$W,array$k){$Pg=preg_match('(LIKE|^!?~)',$W["op"]);$Uf=preg_match('~^(character( varying)?|text|citext|bpchar|name)$~',$k["type"])||(!$Pg&&preg_match('~'.number_type().'|^(date|time|timetz|timestamp|timestamptz|boolean)$~',$k["type"]));return($Uf&&!preg_match('~\[]$~',$k["full_type"])?$r:"CAST($r AS text)");}function
quoteBinary($Mh){return"decode('".bin2hex($Mh)."', 'hex')";}function
md5($d,array$k){if(is_blob($k)||preg_match('~'.text_type().'~',$k["type"]))return"MD5($d)";}function
warnings(){return$this->conn->warnings();}function
errorLine($D){return(preg_match('~^LINE (\d+):~m',$this->conn->error,$x)?(int)$x[1]:0);}function
tableHelp($y,$Je=false){$hf=array("information_schema"=>"infoschema","pg_catalog"=>($Je?"view":"catalog"),);$w=$hf[$_GET["ns"]];if($w)return"$w-".str_replace("_","-",$y).".html";}function
inheritsFrom($Q){return
get_rows("SELECT relname AS table, nspname AS ns FROM pg_class JOIN pg_inherits ON inhparent = oid JOIN pg_namespace ON relnamespace = pg_namespace.oid WHERE inhrelid = ".$this->tableOid($Q)." ORDER BY 2, 1");}function
inheritedTables($Q){return
get_rows("SELECT relname AS table, nspname AS ns FROM pg_inherits JOIN pg_class ON inhrelid = oid JOIN pg_namespace ON relnamespace = pg_namespace.oid WHERE inhparent = ".$this->tableOid($Q)." ORDER BY 2, 1");}function
partitionsInfo($Q){$G=(min_version(10)?$this->conn->query("SELECT * FROM pg_partitioned_table WHERE partrelid = ".$this->tableOid($Q))->fetch_assoc():null);if($G){$c=get_vals("SELECT attname FROM pg_attribute WHERE attrelid = $G[partrelid] AND attnum IN (".str_replace(" ",", ",$G["partattrs"]).")");$Qa=array('h'=>'HASH','l'=>'LIST','r'=>'RANGE');return
array("partition_by"=>$Qa[$G["partstrat"]],"partition"=>implode(", ",array_map('Adminer\idf_escape',$c)),);}return
array();}function
routineTriggers($y){return
get_rows("SELECT nspname AS ns, relname AS table, tgname AS trigger
FROM pg_trigger
JOIN pg_class ON tgrelid = pg_class.oid
JOIN pg_namespace ON relnamespace = pg_namespace.oid
WHERE NOT tgisinternal AND tgfoid = substring(".q($y).", '[0-9]+\$')::oid
ORDER BY 1, 2, 3");}function
tableOid($Q){return"(SELECT oid FROM pg_class WHERE relnamespace = $this->nsOid AND relname = ".q($Q)." AND relkind IN ('r', 'm', 'v', 'f', 'p'))";}function
allFields(){$F=array();$H=get_rows("SELECT c.relname AS tab, a.attname AS field, a.attnotnull::int,
	format_type(a.atttypid, a.atttypmod) AS full_type, i.indrelid AS primary
FROM pg_class c
JOIN pg_attribute a ON a.attrelid = c.oid AND a.attnum > 0 AND NOT a.attisdropped
LEFT JOIN pg_index i ON i.indrelid = c.oid AND i.indisprimary AND a.attnum = ANY(i.indkey)
WHERE c.relnamespace = $this->nsOid
AND c.relkind IN ('r', 'm', 'v', 'f', 'p')".(min_version(10)?"
AND c.relispartition IS NOT TRUE":"")."
ORDER BY c.relname, a.attnum",$this->conn);foreach($H
as$G){parse_full_type($G);$G["null"]=!$G["attnotnull"];$F[$G["tab"]][]=$G;}return$F;}function
indexAlgorithms(array$Pi){static$F=array();if(!$F)$F=get_vals("SELECT amname FROM pg_am".(min_version(9.6)?" WHERE amtype = 'i'":"")." ORDER BY amname = '".($this->conn->flavor=='cockroach'?"prefix":"btree")."' DESC, amname");return$F;}function
indexOpclasses(){static$F=array();if(!$F&&$this->conn->flavor!='cockroach')$F=get_vals("SELECT DISTINCT opcname FROM pg_catalog.pg_opclass WHERE NOT opcdefault ORDER BY opcname");return$F;}function
supportsIndex(array$R){return$R["Engine"]!="view";}function
hasCStyleEscapes(){static$Sa;if($Sa===null)$Sa=(get_val("SHOW standard_conforming_strings",0,$this->conn)=="off");return$Sa;}function
hasEstimatedRows(){return
true;}function
isSystem($h,$I=""){return($I!=""?information_schema($h,$I)||preg_match('~^pg_~',$I):in_array($h,array("postgres","template1"))||($this->conn->flavor=='cockroach'&&$h=="system"));}}function
idf_escape($r){return'"'.str_replace('"','""',$r).'"';}function
table($r){return($_POST["schema_style"]===""&&$_GET["ns"]!=""?idf_escape($_GET["ns"]).".":"").idf_escape($r);}function
get_databases($pd){return
get_vals("SELECT datname FROM pg_database
WHERE datallowconn = TRUE AND has_database_privilege(datname, 'CONNECT')
ORDER BY datname");}function
limit($D,$Z,$v,$eg=0,$K=" "){return" $D$Z".($v?$K."LIMIT $v".($eg?" OFFSET $eg":""):"");}function
limit1($Q,$D,$Z,$K="\n"){return(preg_match('~^INTO~',$D)?limit($D,$Z,1,0,$K):" $D".(is_view(table_status1($Q))?$Z:$K."WHERE (tableoid, ctid) = (SELECT tableoid, ctid FROM ".table($Q).$Z.$K."LIMIT 1)"));}function
db_collation($h,array$jb){return
get_val("SELECT datcollate FROM pg_database WHERE datname = ".q($h));}function
logged_user(){return
get_val("SELECT user");}function
tables_list(){$D="SELECT table_name, table_type FROM information_schema.tables WHERE table_schema = current_schema()";if(support("materializedview"))$D
.="
UNION ALL
SELECT matviewname, 'MATERIALIZED VIEW'
FROM pg_matviews
WHERE schemaname = current_schema()";$D
.="
ORDER BY 1";return
get_key_vals($D);}function
count_tables(array$Rb){$F=array();foreach($Rb
as$h){if(connection()->select_db($h))$F[$h]=count(tables_list());}return$F;}function
table_status($y="",$Zc=false){static$Ud;if($Ud===null)$Ud=get_val("SELECT 'pg_table_size'::regproc");$ei=(!$Zc&&min_version(10));$F=array();foreach(get_rows("SELECT
	relname AS \"Name\",
	CASE relkind WHEN 'v' THEN 'view' WHEN 'm' THEN 'materialized view' ELSE 'table' END AS \"Engine\"".($Ud?",
	pg_table_size(c.oid) AS \"Data_length\",
	pg_indexes_size(c.oid) AS \"Index_length\"":"").",
	obj_description(c.oid, 'pg_class') AS \"Comment\",
	".(min_version(12)?"''":"CASE WHEN relhasoids THEN 'oid' ELSE '' END")." AS \"Oid\",
	reltuples AS \"Rows\",
	EXISTS (SELECT 1 FROM pg_index WHERE indrelid = c.oid AND NOT indisvalid)::int AS invalid_index,
	".($ei?"seq.last_value":"NULL")." AS \"Auto_increment\"".(min_version(10)?",
	relispartition::int AS dependent":"")."
FROM pg_class c
".($ei?"LEFT JOIN (
	SELECT d.refobjid, max(s.last_value) AS last_value
	FROM pg_depend d
	JOIN pg_class sc ON sc.oid = d.objid AND sc.relkind = 'S' AND sc.relnamespace = ".driver()->nsOid."
	JOIN pg_sequences s ON s.schemaname = current_schema() AND s.sequencename = sc.relname
	WHERE d.classid = 'pg_class'::regclass AND d.refclassid = 'pg_class'::regclass AND d.deptype IN ('a', 'i')
	".($y!=""?"AND d.refobjid = ".driver()->tableOid($y):"")."
	GROUP BY d.refobjid
) seq ON seq.refobjid = c.oid
":"")."WHERE relkind IN ('r', 'm', 'v', 'f', 'p')
AND relnamespace = ".driver()->nsOid."
".($y!=""?"AND relname = ".q($y):"ORDER BY relname"))as$G)$F[$G["Name"]]=$G;return$F;}function
is_view(array$R){return
in_array($R["Engine"],array("view","materialized view"));}function
fk_support(array$R){return
true;}function
parse_full_type(array&$G){static$oa=array('timestamp without time zone'=>'timestamp','timestamp with time zone'=>'timestamptz','time without time zone'=>'time','time with time zone'=>'timetz',);preg_match('~([^([]+)(\((.*)\))?([a-z ]+)?((\[[0-9]*])*)$~',$G["full_type"],$x);list(,$T,$u,$G["length"],$ia,$va)=$x;$G["length"].=$va;$Ya=$T.$ia;if(isset($oa[$Ya])){$G["type"]=$oa[$Ya];$G["full_type"]=$G["type"].$u.$va;}else{$Bj=idf_unescape($T);$G["type"]=(is_user_type($Bj)?$Bj:$T);$G["full_type"]=$G["type"].$u.$ia.$va;}}function
fields($Q){$F=array();foreach(get_rows("SELECT
	a.attname AS field,
	format_type(a.atttypid, a.atttypmod) AS full_type,
	pg_get_expr(d.adbin, d.adrelid) AS default,
	a.attnotnull::int,
	i.indrelid AS primary,
	t.typcategory,
	col_description(a.attrelid, a.attnum) AS comment".(min_version(10)?",
	a.attidentity".(min_version(12)?",
	a.attgenerated":""):"")."
FROM pg_attribute a
JOIN pg_type t ON t.oid = a.atttypid
LEFT JOIN pg_attrdef d ON a.attrelid = d.adrelid AND a.attnum = d.adnum
LEFT JOIN pg_index i ON a.attrelid = i.indrelid AND a.attnum = ANY(i.indkey) AND i.indisprimary
WHERE a.attrelid = ".driver()->tableOid($Q)."
AND NOT a.attisdropped
AND a.attnum > 0
ORDER BY a.attnum")as$G){parse_full_type($G);$G["default_sql"]=($G["default"]!==null&&!$G["attidentity"]&&!$G["attgenerated"]&&!preg_match("~^(('(?:[^']|'')*'|NULL)(::[^,)]+)?|[-+]?[\\d.]+|true|false)$~s",$G["default"]));if(in_array($G['attidentity'],array('a','d')))$G['default']='GENERATED '.($G['attidentity']=='d'?'BY DEFAULT':'ALWAYS').' AS IDENTITY';$G["generated"]=idx(array("s"=>"STORED","v"=>"VIRTUAL"),$G["attgenerated"],"");$G["composite"]=($G["typcategory"]=="C");$G["null"]=!$G["attnotnull"];$G["auto_increment"]=$G['attidentity']||preg_match('~^nextval\(~i',$G["default"])||preg_match('~^unique_rowid\(~',$G["default"]);$G["privileges"]=array("insert"=>1,"select"=>1,"update"=>1,"where"=>1,"order"=>1);if(!$G['generated']&&preg_match('~(.+)::[^,)]+(.*)~',$G["default"],$x)&&($x[2]!=""||preg_match("~^('.*'|NULL)\$~s",$x[1])))$G["default"]=($x[1]=="NULL"?null:idf_unescape($x[1]).$x[2]);$F[$G["field"]]=$G;}return$F;}function
indexes($Q,$g=null){$g=connection($g);$F=array();$Ti=driver()->tableOid($Q);$e=get_key_vals("SELECT attnum, attname FROM pg_attribute WHERE attrelid = $Ti AND attnum > 0",$g);foreach(get_rows("SELECT relname, indisunique::int, indisprimary::int, indisvalid::int, indkey, indoption, amname,
	pg_get_expr(indpred, indrelid, true) AS partial, pg_get_expr(indexprs, indrelid) AS indexpr".($g->flavor=='cockroach'?"":",
	(SELECT string_agg(CASE WHEN opcdefault THEN '' ELSE opcname END, ' ' ORDER BY s)
		FROM generate_subscripts(indclass, 1) AS s JOIN pg_catalog.pg_opclass ON pg_opclass.oid = indclass[s]) AS opclasses")."
FROM pg_index
JOIN pg_class ON indexrelid = oid
JOIN pg_am ON pg_am.oid = pg_class.relam
WHERE indrelid = $Ti
ORDER BY indisprimary DESC, indisunique DESC",$g)as$G){$Ah=$G["relname"];$F[$Ah]["type"]=($G["indisprimary"]?"PRIMARY":($G["indisunique"]?"UNIQUE":"INDEX"));$F[$Ah]["columns"]=array();$F[$Ah]["descs"]=array();$F[$Ah]["algorithm"]=$G["amname"];$F[$Ah]["partial"]=$G["partial"];$F[$Ah]["invalid"]=!$G["indisvalid"];$re=preg_split('~(?<=\)), (?=\()~',$G["indexpr"]);foreach(explode(" ",$G["indkey"])as$se)$F[$Ah]["columns"][]=($se?$e[$se]:array_shift($re));foreach(explode(" ",$G["indoption"])as$te)$F[$Ah]["descs"][]=(intval($te)&1?'1':null);$F[$Ah]["opclasses"]=($G["opclasses"]!=""?explode(" ",$G["opclasses"]):array());$F[$Ah]["lengths"]=array();}return$F;}function
foreign_keys($Q){$F=array();foreach(get_rows("SELECT conname, condeferrable::int AS deferrable, condeferred::int AS deferred, pg_get_constraintdef(oid) AS definition
FROM pg_constraint
WHERE conrelid = ".driver()->tableOid($Q)."
AND contype = 'f'::char
ORDER BY conkey, conname")as$G){$G['deferrable']=($G['deferrable']?'':'NOT ').'DEFERRABLE'.($G['deferred']?' INITIALLY DEFERRED':'');if(preg_match('~FOREIGN KEY\s*\((.+)\)\s*REFERENCES (.+)\((.+)\)(.*)$~iA',$G['definition'],$x)){$G['source']=array_map('Adminer\idf_unescape',array_map('trim',explode(',',$x[1])));if(preg_match('~^(("([^"]|"")+"|[^"]+)\.)?"?("([^"]|"")+"|[^"]+)$~',$x[2],$qf)){$G['ns']=idf_unescape($qf[2]);$G['table']=idf_unescape($qf[4]);}$G['target']=array_map('Adminer\idf_unescape',array_map('trim',explode(',',$x[3])));$G['on_delete']=(preg_match("~ON DELETE (".driver()->onActions.")~",$x[4],$qf)?$qf[1]:'NO ACTION');$G['on_update']=(preg_match("~ON UPDATE (".driver()->onActions.")~",$x[4],$qf)?$qf[1]:'NO ACTION');$F[$G['conname']]=$G;}}return$F;}function
view($y){return
array("select"=>trim(get_val("SELECT pg_get_viewdef(".driver()->tableOid($y).")")));}function
collations(){return(isset($_GET["database"])&&DB==""&&connection()->flavor!='cockroach'&&min_version(13)?get_vals("SELECT DISTINCT collcollate FROM pg_collation WHERE collprovider = 'c' ORDER BY 1"):array());}function
information_schema($h,$I=""){$Ni=array("information_schema","pg_catalog","pg_toast");if(connection()->flavor=='cockroach'){$Ni[]="crdb_internal";$Ni[]="pg_extension";}return
in_array($I!=""?$I:get_schema(),$Ni);}function
error(){$F=h(connection()->error);if(preg_match('~^(.*\n)?([^\n]*)\n( *)\^(\n.*)?$~s',$F,$x))$F=$x[1].preg_replace('~((?:[^&]|&[^;]*;){'.strlen($x[3]).'})(.*)~','\1<b>\2</b>',$x[2]).$x[4];return
nl_br($F);}function
create_database($h,$ib){return
queries("CREATE DATABASE ".idf_escape($h).($ib?" TEMPLATE template0 LOCALE ".q($ib):""));}function
drop_databases(array$Rb){connection()->close();return
apply_queries("DROP DATABASE",$Rb,'Adminer\idf_escape');}function
rename_database($y,$ib){connection()->close();return!!queries("ALTER DATABASE ".idf_escape(DB)." RENAME TO ".idf_escape($y));}function
auto_increment(){return"";}function
alter_table($Q,$y,array$l,array$rd,$mb,$Fc,$ib,$Aa,$Ig){$b=array();$ph=array();if($Q!=""&&$Q!=$y)$ph[]="ALTER TABLE ".table($Q)." RENAME TO ".table($y);$bi="";$ug=($Q!=""?fields($Q):array());foreach($l
as$k){$d=idf_escape($k[0]);$W=$k[1];if(!$W)$b[]="DROP $d";else{$tg=$ug[$k[0]];$rg=($tg?process_field($tg,$tg):array());$Xj=$W[5];unset($W[5]);if($k[0]==""){if(isset($W[6]))$W[1]=($W[1]==" bigint"?" big":($W[1]==" smallint"?" small":" "))."serial";$b[]=($Q!=""?"ADD ":"  ").implode($W);if(isset($W[6]))$b[]=($Q!=""?"ADD":" ")." PRIMARY KEY ($W[0])";}else{if($d!=$W[0])$ph[]="ALTER TABLE ".table($y)." RENAME $d TO $W[0]";if($W[1]!=$rg[1])$b[]="ALTER $d TYPE$W[1]";if($W[3]!=$rg[3]||isset($W[6])!=isset($rg[6])){$ci=$Q."_".idf_unescape($W[0])."_seq";$b[]="ALTER $d ".($W[3]?"SET".preg_replace('~GENERATED ALWAYS(.*) (STORED|VIRTUAL)~','EXPRESSION\1',$W[3]):(isset($W[6])?"SET DEFAULT nextval(".q($ci).")":"DROP DEFAULT"));if(isset($W[6]))$bi="CREATE SEQUENCE IF NOT EXISTS ".idf_escape($ci)." OWNED BY ".idf_escape($Q).".$W[0]";}if($W[2]!=$rg[2])$b[]="ALTER $d ".($W[2]==" NULL"?"DROP NOT":"SET").$W[2];}if($Xj!=$rg[5])$ph[]="COMMENT ON COLUMN ".table($y).".$W[0] IS ".($Xj!=""?substr($Xj,9):"''");}}if($Q==""){$b=array_merge($b,$rd);$O="";if($Ig){$fb=(connection()->flavor=='cockroach');$O=" PARTITION BY $Ig[partition_by]($Ig[partition])";if($Ig["partition_by"]=='HASH'){$Jg=+$Ig["partitions"];for($p=0;$p<$Jg;$p++)$ph[]="CREATE TABLE ".idf_escape($y."_$p")." PARTITION OF ".idf_escape($y)." FOR VALUES WITH (MODULUS $Jg, REMAINDER $p)";}else{$dh="MINVALUE";foreach($Ig["partition_names"]as$p=>$W){$X=$Ig["partition_values"][$p];$Fg=" VALUES ".($Ig["partition_by"]=='LIST'?"IN ($X)":"FROM ($dh) TO ($X)");if($fb)$O
.=($p?",":" (")."\n  PARTITION ".(preg_match('~^DEFAULT$~i',$W)?$W:idf_escape($W))."$Fg";else$ph[]="CREATE TABLE ".idf_escape($y."_$W")." PARTITION OF ".idf_escape($y)." FOR$Fg";$dh=$X;}$O
.=($fb?"\n)":"");}}array_unshift($ph,"CREATE TABLE ".table($y)." (\n".implode(",\n",$b)."\n)$O");}else{if($b)array_unshift($ph,"ALTER TABLE ".table($Q)."\n".implode(",\n",$b));if($rd)$ph[]="ALTER TABLE ".table($y)."\n".implode(",\n",$rd);}if($bi)array_unshift($ph,$bi);if($mb!==null)$ph[]="COMMENT ON TABLE ".table($y)." IS ".q($mb);foreach($ph
as$D){if(!queries($D))return
false;}if($Aa!=""){foreach(fields($y)as$bd=>$k){if($k["auto_increment"])return!!queries("SELECT setval(pg_get_serial_sequence(".q(table($y)).", ".q($bd)."), $Aa)");}}return
true;}function
alter_indexes($Q,$b){$Hb=array();$rc=array();$ph=array();$qb=(support("concurrent_indexes")&&$_POST["concurrently"]?" CONCURRENTLY":"");foreach($b
as$W){if($W[0]=="UNIQUE"&&$W[2]=="DROP"&&!get_val("SELECT 1 FROM pg_constraint JOIN pg_class ON conindid = pg_class.oid WHERE conrelid = ".driver()->tableOid($Q)." AND relname = ".q($W[1])))$W[0]="INDEX";if($W[0]!="INDEX")$Hb[]=($W[2]=="DROP"?"\nDROP CONSTRAINT ".idf_escape($W[1]):"\nADD".($W[1]!=""?" CONSTRAINT ".idf_escape($W[1]):"")." $W[0] ".($W[0]=="PRIMARY"?"KEY ":"")."(".implode(", ",$W[2]).")");elseif($W[2]=="DROP")$rc[]=idf_escape($W[1]);else$ph[]="CREATE INDEX$qb ".idf_escape($W[1]!=""?$W[1]:uniqid($Q."_"))." ON ".table($Q).($W[3]?" USING $W[3]":"")." (".implode(", ",$W[2]).")".($W[4]?" WHERE $W[4]":"");}if($Hb)array_unshift($ph,"ALTER TABLE ".table($Q).implode(",",$Hb));if($rc){$ph=array_merge(($qb?array_map(function($r){return"DROP INDEX CONCURRENTLY $r";},$rc):array("DROP INDEX ".implode(", ",$rc))),$ph);}foreach($ph
as$D){if(!queries($D))return
false;}return
true;}function
truncate_tables(array$S){return!!queries("TRUNCATE ".implode(", ",array_map('Adminer\table',$S)));}function
drop_kinds(array$S){$F=array("MATERIALIZED VIEW"=>array(),"VIEW"=>array(),"TABLE"=>array());foreach($S
as$y=>$R)$F[strtoupper($R["Engine"])][]=table($y);return
array_filter($F);}function
drop_views(array$ck){return
drop_tables($ck);}function
drop_tables(array$S){$Gi=array();foreach($S
as$Q)$Gi[$Q]=table_status1($Q);foreach(drop_kinds($Gi)as$Qe=>$Tf){if(!queries("DROP $Qe ".implode(", ",$Tf)))return
false;}return
true;}function
move_tables(array$S,array$ck,$Vi){foreach(array_merge($S,$ck)as$Q){$O=table_status1($Q);if(!queries("ALTER ".strtoupper($O["Engine"])." ".table($Q)." SET SCHEMA ".idf_escape($Vi)))return
false;}return
true;}function
trigger($y,$Q){if($y=="")return
array("Statement"=>"EXECUTE PROCEDURE ()");$e=array();$Z="WHERE trigger_schema = current_schema() AND event_object_table = ".q($Q)." AND trigger_name = ".q($y);foreach(get_rows("SELECT * FROM information_schema.triggered_update_columns $Z")as$G)$e[]=$G["event_object_column"];$F=array();foreach(get_rows('SELECT trigger_name AS "Trigger", action_timing AS "Timing", event_manipulation AS "Event", \'FOR EACH \' || action_orientation AS "Type", action_statement AS "Statement"
FROM information_schema.triggers'."
$Z
ORDER BY event_manipulation DESC")as$G){if($e&&$G["Event"]=="UPDATE")$G["Event"].=" OF";$G["Of"]=implode(", ",$e);if($F)$G["Event"].=" OR $F[Event]";$F=$G;}return$F;}function
triggers($Q){$F=array();$Gd=array();foreach(get_rows('SELECT t.tgname, r.routine_schema AS ns, r.specific_name AS function, r.routine_name AS name
FROM pg_catalog.pg_trigger t
JOIN information_schema.routines r ON substring(r.specific_name, \'[0-9]+$\')::oid = t.tgfoid
WHERE NOT t.tgisinternal AND t.tgrelid = (
	SELECT c.oid FROM pg_catalog.pg_class c JOIN pg_catalog.pg_namespace n ON n.oid = c.relnamespace WHERE n.nspname = current_schema() AND c.relname = '.q($Q).'
)')as$G)$Gd[array_shift($G)]=$G;foreach(get_rows("SELECT * FROM information_schema.triggers WHERE trigger_schema = current_schema() AND event_object_table = ".q($Q))as$G){$tj=trigger($G["trigger_name"],$Q);$F[$tj["Trigger"]]=array($tj["Timing"],$tj["Event"]);if($Gd[$tj["Trigger"]])$F[$tj["Trigger"]][]=$Gd[$tj["Trigger"]];}return$F;}function
trigger_options(){return
array("Timing"=>array("BEFORE","AFTER"),"Event"=>array("INSERT","UPDATE","UPDATE OF","DELETE","INSERT OR UPDATE","INSERT OR UPDATE OF","DELETE OR INSERT","DELETE OR UPDATE","DELETE OR UPDATE OF","DELETE OR INSERT OR UPDATE","DELETE OR INSERT OR UPDATE OF",),"Type"=>array("FOR EACH ROW","FOR EACH STATEMENT"),);}function
routine($y,$T){$_=routine_options($T);$Yh=array_intersect_key(array("VOLATILITY"=>"CASE p.provolatile WHEN 'i' THEN 'IMMUTABLE' WHEN 's' THEN 'STABLE' ELSE 'VOLATILE' END","NULL_INPUT"=>"CASE WHEN p.proisstrict THEN 'RETURNS NULL ON NULL INPUT' ELSE 'CALLED ON NULL INPUT' END","SECURITY"=>"CASE WHEN p.prosecdef THEN 'SECURITY DEFINER' ELSE 'SECURITY INVOKER' END","PARALLEL"=>"CASE p.proparallel WHEN 's' THEN 'PARALLEL SAFE' WHEN 'r' THEN 'PARALLEL RESTRICTED' ELSE 'PARALLEL UNSAFE' END",),$_);foreach($Yh
as$t=>$J)$Yh[$t]="$J AS \"$t\"";$H=get_rows('SELECT r.routine_definition AS definition, LOWER(r.external_language) AS language, '.($Yh?implode(', ',$Yh).', ':'').'r.*
FROM information_schema.routines r
LEFT JOIN pg_catalog.pg_proc p ON p.oid::text = substring(r.specific_name, \'[0-9]+$\')
WHERE r.routine_schema = current_schema() AND r.specific_name = '.q($y));if(!$H)return
array();$F=$H[0];$F["definition"]=trim("$F[definition]","\r\n");$F["options"]=array_intersect_key($F,$_);$F["returns"]=array("type"=>preg_replace('~^_(.*)~','\1[]',"$F[type_udt_name]"));$F["fields"]=get_rows("SELECT COALESCE(parameter_name, ordinal_position::text) AS field,
	CASE data_type WHEN 'USER-DEFINED' THEN udt_name WHEN 'ARRAY' THEN substr(udt_name, 2) || '[]' ELSE data_type END AS type,
	character_maximum_length AS length, parameter_mode AS inout
FROM information_schema.parameters
WHERE specific_schema = current_schema() AND specific_name = ".q($y)."
ORDER BY ordinal_position");return$F;}function
routines(){return
get_rows('SELECT specific_name AS "SPECIFIC_NAME", routine_type AS "ROUTINE_TYPE", routine_name AS "ROUTINE_NAME", type_udt_name AS "DTD_IDENTIFIER"
FROM information_schema.routines
WHERE routine_schema = current_schema()'.(connection()->flavor=='cockroach'?'':"
AND substring(specific_name, '[0-9]+\$')::oid NOT IN (SELECT objid FROM pg_catalog.pg_depend WHERE classid = 'pg_proc'::regclass AND deptype = 'e')").'
ORDER BY SPECIFIC_NAME');}function
routine_languages(){$F=array();foreach(get_vals("SELECT LOWER(lanname) FROM pg_catalog.pg_language")as$We)$F[$We]=(preg_match('~sql$~',$We)?"pgsql":"txt");return$F;}function
routine_options($Kh){$fb=(connection()->flavor=='cockroach');$Th=($fb?array():array("SECURITY"=>array("SECURITY INVOKER","SECURITY DEFINER")));if($Kh=="PROCEDURE")return$Th;return
array("VOLATILITY"=>array("VOLATILE","STABLE","IMMUTABLE"),"NULL_INPUT"=>array("CALLED ON NULL INPUT","RETURNS NULL ON NULL INPUT"),)+$Th+($fb?array():array("PARALLEL"=>array("PARALLEL UNSAFE","PARALLEL RESTRICTED","PARALLEL SAFE"),));}function
routine_id($y,array$G){$F=array();foreach($G["fields"]as$k){$u=$k["length"];$F[]=$k["type"].($u?"($u)":"");}return
idf_escape($y)."(".implode(", ",$F).")";}function
last_id($E){$G=(is_object($E)?$E->fetch_row():array());return($G?$G[0]:0);}function
explain(Db$f,$D){return$f->query("EXPLAIN $D");}function
found_rows(array$R,array$Z){if(preg_match("~ rows=([0-9]+)~",get_val("EXPLAIN SELECT * FROM ".idf_escape($R["Name"]).($Z?" WHERE ".implode(" AND ",$Z):"")),$_h))return$_h[1];}function
types($Wc=false){$fb=connection()->flavor=='cockroach';$Re=($fb?"'e'":"'b','c','d','e'".(min_version(9.2)?",'r'":""));return
get_key_vals("SELECT t.oid, t.typname
FROM pg_type t
WHERE t.typnamespace = ".driver()->nsOid."
AND t.typtype IN ($Re)".($fb?"
AND t.typelem = 0":"
AND (t.typrelid = 0 OR (SELECT c.relkind FROM pg_class c WHERE c.oid = t.typrelid) = 'c')"."
AND NOT EXISTS (SELECT 1 FROM pg_type e WHERE e.typarray = t.oid)".($Wc?'':"
AND t.oid NOT IN (SELECT objid FROM pg_catalog.pg_depend WHERE classid = 'pg_type'::regclass AND deptype = 'e')"))."
ORDER BY t.typname");}function
type_values($q){$Hc=get_vals("SELECT enumlabel FROM pg_enum WHERE enumtypid = $q ORDER BY enumsortorder");return($Hc?"'".implode("', '",array_map('addslashes',$Hc))."'":"");}function
collation_name($fg){return(min_version(9.1)?"(SELECT collname FROM pg_collation WHERE oid = $fg AND collname != 'default')":"NULL");}function
type_definition($q){$T=first(get_rows("SELECT typtype, typisdefined::int AS defined, typrelid FROM pg_type WHERE oid = $q"));$F=array("kind"=>($T?$T["typtype"]:""),"definition"=>"");if(!$T||!$T["defined"])return$F;switch($F["kind"]){case'e':$Y=get_vals("SELECT enumlabel FROM pg_enum WHERE enumtypid = $q ORDER BY enumsortorder");$F["definition"]="AS ENUM (".implode(", ",array_map('Adminer\q',$Y)).")";break;case'c':$e=array();foreach(get_rows("SELECT attname, format_type(atttypid, atttypmod) AS full_type, ".collation_name("attcollation")." AS collation
FROM pg_attribute
WHERE attrelid = $T[typrelid] AND attnum > 0 AND NOT attisdropped
ORDER BY attnum")as$G)$e[]=idf_escape($G["attname"])." $G[full_type]".($G["collation"]?" COLLATE ".idf_escape($G["collation"]):"");$F["definition"]="AS (\n\t".implode(",\n\t",$e)."\n)";break;case'd':$oc=first(get_rows("SELECT format_type(typbasetype, typtypmod) AS base, typnotnull::int AS notnull, typdefault, ".collation_name("typcollation")." AS collation
FROM pg_type WHERE oid = $q"));$F["definition"]="AS $oc[base]".($oc["collation"]?" COLLATE ".idf_escape($oc["collation"]):"").($oc["typdefault"]!=""?" DEFAULT $oc[typdefault]":"").($oc["notnull"]?" NOT NULL":"");foreach(get_rows("SELECT conname, pg_get_constraintdef(oid) AS definition FROM pg_constraint WHERE contypid = $q AND contype != 'n' ORDER BY conname")as$G)$F["definition"].=" CONSTRAINT ".idf_escape($G["conname"])." $G[definition]";break;case'r':$uh=first(get_rows("SELECT format_type(rngsubtype, NULL) AS subtype,
(SELECT opcname FROM pg_opclass WHERE oid = rngsubopc) AS subtype_opclass,
".collation_name("rngcollation")." AS collation,
NULLIF(rngcanonical, 0)::regproc::text AS canonical,
NULLIF(rngsubdiff, 0)::regproc::text AS subtype_diff".(min_version(14)?",
(SELECT typname FROM pg_type WHERE oid = rngmultitypid) AS multirange_type_name":"")."
FROM pg_range WHERE rngtypid = $q"));$_=array();foreach(array("subtype"=>0,"subtype_opclass"=>1,"collation"=>1,"canonical"=>0,"subtype_diff"=>0,"multirange_type_name"=>1)as$t=>$Mc){if($uh[$t]!="")$_[]=strtoupper($t)." = ".($Mc?idf_escape($uh[$t]):$uh[$t]);}$F["definition"]="AS RANGE (".implode(", ",$_).")";}return$F;}function
schemas(){return
get_vals("SELECT nspname FROM pg_namespace ORDER BY nspname");}function
get_schema(){return(string)get_val("SELECT current_schema()");}function
set_schema($I,$g=null){$_GET["ns"]=$I;$F=get_val("SELECT set_config('search_path', ".q(idf_escape($I)).", false) FROM pg_namespace WHERE nspname = ".q($I),0,$g);driver()->setUserTypes(types(true));return!!$F;}function
schema_comment($I){return(string)get_val("SELECT obj_description(oid, 'pg_namespace') FROM pg_namespace WHERE nspname = ".q($I));}function
set_schema_comment($I,$mb){return!!queries("COMMENT ON SCHEMA ".idf_escape($I)." IS ".q($mb));}function
drop_sql(array$S){$F="";foreach(drop_kinds($S)as$Qe=>$Tf)$F
.="DROP $Qe IF EXISTS ".implode(", ",$Tf).";\n";return($F?"$F\n":"");}function
foreign_keys_sql($Q){$F="";$md=foreign_keys($Q);ksort($md);foreach($md
as$ld=>$kd){$F
.="ALTER TABLE ONLY ".table($Q)." ADD CONSTRAINT ".idf_escape($ld)." ".preg_replace_callback('~( REFERENCES )([^(.]+)\(~',function(array$x){return$x[1].table(idf_unescape($x[2]))."(";},$kd["definition"]).";\n";}return($F?"$F\n":$F);}function
indexes_sql($Q,$fh=""){$F="";$D="SELECT indexdef, quote_ident(schemaname) || '.' || quote_ident(tablename) AS qualified, quote_ident(current_database()) AS db
FROM pg_catalog.pg_indexes
WHERE schemaname = current_schema() AND tablename = ".q($Q).($fh!=""?" AND indexname != ".q($fh):"");foreach(get_rows($D,null,"-- ")as$G)$F
.="\n\n".str_replace(array(" $G[db].$G[qualified] USING "," $G[qualified] USING ")," ".table($Q)." USING ",$G["indexdef"]).";";return$F;}function
create_sql($Q,$Aa,$Ii){$Ih=array();$ei=array();$fi=array();$di=array();$O=table_status1($Q);if(is_view($O)){$bk=view($Q);$Hb="CREATE ".strtoupper($O["Engine"])." ".table($Q)." AS ".rtrim($bk["select"],";").";";return
rtrim($Hb.indexes_sql($Q),';');}$l=fields($Q);if(count($O)<2||empty($l))return"";$F="CREATE TABLE ".table($O['Name'])." (\n    ";$Ri=q(table($O['Name']));foreach($l
as$k){$gi="";if($k['default']=="nextval('$O[Name]_$k[field]_seq')"){$gi=table("$O[Name]_$k[field]_seq");$k['default']=null;$k['full_type']=preg_replace('~int(eger)?~','serial',$k['full_type']);}$Eg=idf_escape($k['field']).' '.full_type_sql($k).preg_replace_callback('~(nextval\(\')([^.\']+)\'~',function(array$x){return$x[1].str_replace("'","''",table(idf_unescape($x[2])))."'";},default_value($k)).($k['null']?"":" NOT NULL");$Ih[]=$Eg;if(preg_match('~nextval\(\'([^\']+)\'\)~',$k['default'],$rf)){$ci=$rf[1];$_i=first(get_rows((min_version(10)?"SELECT *, cache_size AS cache_value FROM pg_sequences WHERE schemaname = current_schema() AND sequencename = ".q(idf_unescape($ci)):"SELECT * FROM $ci"),null,"-- "));$bi=table(idf_unescape($ci));$ei[]=($Ii=="DROP+CREATE"?"DROP SEQUENCE IF EXISTS $bi;\n":"")."CREATE SEQUENCE $bi INCREMENT $_i[increment_by] MINVALUE $_i[min_value] MAXVALUE $_i[max_value]"." CACHE $_i[cache_value];";if(get_val("SELECT pg_get_serial_sequence($Ri, ".q($k['field']).")"))$fi[]="\n\nALTER SEQUENCE $bi OWNED BY ".table($O['Name']).".".idf_escape($k['field']).";";if($Aa)$di[]=$bi;}elseif($Aa&&$k['auto_increment']){$bi=($gi?"":get_val("SELECT pg_get_serial_sequence($Ri, ".q($k['field']).")::regclass"));$di[]=($bi?table(idf_unescape($bi)):$gi);}}if(!empty($ei))$F=implode("\n\n",$ei)."\n\n$F";$fh="";foreach(indexes($Q)as$oe=>$s){if($s['type']=='PRIMARY'){$fh=$oe;$Ih[]="CONSTRAINT ".idf_escape($oe)." PRIMARY KEY (".implode(', ',array_map('Adminer\idf_escape',$s['columns'])).")";}}foreach(driver()->checkConstraints($Q)as$ub=>$wb)$Ih[]="CONSTRAINT ".idf_escape($ub)." CHECK ($wb)";$F
.=implode(",\n    ",$Ih)."\n)";$Fg=driver()->partitionsInfo($O['Name']);if($Fg)$F
.="\nPARTITION BY $Fg[partition_by]($Fg[partition])";$F
.=(min_version(12)?"":"\nWITH (oids = ".($O['Oid']?'true':'false').")").";";$F
.=implode($fi);if($O['Comment'])$F
.="\n\nCOMMENT ON TABLE ".table($O['Name'])." IS ".q($O['Comment']).";";foreach($l
as$bd=>$k){if($k['comment'])$F
.="\n\nCOMMENT ON COLUMN ".table($O['Name']).".".idf_escape($bd)." IS ".q($k['comment']).";";}$F
.=indexes_sql($Q,$fh);foreach(array_filter($di)as$bi){$_i=first(get_rows("SELECT last_value, is_called::int FROM $bi",null,"-- "));if($_i['is_called'])$F
.="\n\nDO \$\$ BEGIN PERFORM setval(".q($bi).", $_i[last_value]); END \$\$;";}return
rtrim($F,';');}function
truncate_sql($Q){return"TRUNCATE ".table($Q);}function
truncate_all_sql(array$S){return($S?"TRUNCATE ".implode(", ",array_map('Adminer\table',$S)).";\n\n":"");}function
trigger_sql($Q){$O=table_status1($Q);$F="";foreach(triggers($Q)as$sj=>$rj){$tj=trigger($sj,$O['Name']);$F
.="\nCREATE TRIGGER ".idf_escape($tj['Trigger'])." $tj[Timing] $tj[Event] ON ".table($O['Name'])." $tj[Type] $tj[Statement];;\n";}return$F;}function
use_sql($Qb,$Ii=""){$y=idf_escape($Qb);$F="";if(preg_match('~CREATE~',$Ii)){if($Ii=="DROP+CREATE")$F="DROP DATABASE IF EXISTS $y;\n";$F
.="CREATE DATABASE $y;\n";}return"$F\\connect $y";}function
use_schema_sql($I,$Ii){$y=idf_escape($I);$F="";if(preg_match('~CREATE~',$Ii)){if($Ii=="DROP+CREATE")$F="DROP SCHEMA IF EXISTS $y CASCADE;\n";$F
.="CREATE SCHEMA IF NOT EXISTS $y;\n";$mb=schema_comment($I);if($mb!="")$F
.="COMMENT ON SCHEMA $y IS ".q($mb).";\n";}return$F."SET search_path TO $y";}function
show_variables(){return
get_rows("SHOW ALL");}function
process_list(){return
get_rows("SELECT * FROM pg_stat_activity ORDER BY ".(min_version(9.2)?"pid":"procpid"));}function
convert_field(array$k){if(preg_match('~^(geometry|geography)$~',$k["type"])&&strpos($k["full_type"],"[")===false)return"ST_AsEWKT(".idf_escape($k["field"]).")";}function
unconvert_field(array$k,$F){return($k["composite"]?"$F::".full_type_sql($k):$F);}function
support($ad){return
preg_match('~^(check|columns|comment|database|drop_col|dump|descidx|fast_status|indexes|kill|partial_indexes|routine|scheme|sequence|sql|table|trigger|type|variables|view'.(min_version(9.3)?'|materializedview':'').(min_version(11)?'|procedure':'').(connection()->flavor=='cockroach'?'':'|deferrable').(connection()->flavor=='cockroach'?'':'|transaction_ddl').(connection()->flavor=='cockroach'||!min_version(9.1)?'':'|extension').(connection()->flavor=='cockroach'||!min_version(9.2)?'':'|concurrent_indexes').(connection()->flavor=='cockroach'?'':'|processlist').')$~',$ad);}function
kill_process($q){return
queries("SELECT pg_terminate_backend(".number($q).")");}function
connection_id(){return"SELECT pg_backend_pid()";}function
max_connections(){return
get_val("SHOW max_connections");}}add_driver("sqlite","SQLite");if(isset($_GET["sqlite"])){define('Adminer\DRIVER',"sqlite");if(class_exists("SQLite3")&&$_GET["ext"]!="pdo"){abstract
class
SqliteDb
extends
SqlDb{var$extension="SQLite3";private$link;function
attach(array$L,$U,$B){$this->link=new
\SQLite3($L["path"]);if(method_exists($this->link,'setAuthorizer'))$this->link->setAuthorizer(array($this,'authorize'));$ak=\SQLite3::version();$this->server_info=$ak["versionString"];return'';}function
query($D,$Aj=false){$E=@$this->link->query($D);$this->error="";if(!$E){$this->errno=$this->link->lastErrorCode();$this->error=$this->link->lastErrorMsg();return
false;}elseif($E->numColumns())return
new
Result($E);$this->affected_rows=$this->link->changes();return
true;}function
quote($P){return(is_utf8($P)?"'".$this->link->escapeString($P)."'":"x'".bin2hex($P)."'");}}class
Result{var$num_rows;private$result,$offset=0;function
__construct($E){$this->result=$E;}function
fetch_assoc(){return$this->result->fetchArray(SQLITE3_ASSOC);}function
fetch_row(){return$this->result->fetchArray(SQLITE3_NUM);}function
fetch_field(){$_j=array(1=>"integer","real","text","blob","null");$d=$this->offset++;return(object)array("name"=>$this->result->columnName($d),"native_type"=>$_j[$this->result->columnType($d)],);}}}elseif(extension_loaded("pdo_sqlite")){abstract
class
SqliteDb
extends
PdoDb{var$extension="PDO_SQLite";function
attach(array$L,$U,$B){$F=$this->dsn(DRIVER.":".$L["path"],"","",array(),(class_exists('Pdo\Sqlite')?'Pdo\Sqlite':'PDO'));if(!$F&&method_exists($this->pdo,'setAuthorizer'))$this->pdo->setAuthorizer(array($this,'authorize'));return$F;}function
quote($P){return(is_utf8($P)?parent::quote($P):"x'".bin2hex($P)."'");}function
store_result($E=null){$na=$this->affected_rows;$F=parent::store_result($E);if(!$E&&$F===true)$this->affected_rows=$na;return$F;}}}if(class_exists('Adminer\SqliteDb')){class
Db
extends
SqliteDb{private$attaching=false;private$total_changes=0;function
attach(array$L,$U,$B){parent::attach($L,$U,$B);$this->total_changes=0;$this->query("PRAGMA foreign_keys = 1");$this->query("PRAGMA busy_timeout = 500");return'';}function
query($D,$Aj=false){$F=parent::query($D,$Aj);if($this->affected_rows){$E=parent::query("SELECT total_changes()");$G=($E?$E->fetch_row():false);if($G){if($G[0]==$this->total_changes)$this->affected_rows=0;$this->total_changes=$G[0];}}return$F;}function
select_db($m){$D="ATTACH ".$this->quote(preg_match("~(^[/\\\\]|:)~",$m)?$m:dirname($_SERVER["SCRIPT_FILENAME"])."/$m")." AS a";$this->attaching=true;$ya=is_readable($m)&&$this->query($D);$this->attaching=false;if($ya)return!self::attach(server_parts(array("path"=>$m)),'','');return
false;}function
authorize($ea,$ta){return($ea!=24||$ta===''||$this->attaching?0:1);}}}class
Driver
extends
SqlDriver{static$extensions=array("SQLite3","PDO_SQLite");static$jush="sqlite";static$passwords=false;static$serverFile=true;protected$types=array(array("integer"=>0,"real"=>0,"numeric"=>0,"text"=>0,"blob"=>0));var$insertFunctions=array("date"=>"current_date","time"=>"current_timestamp",);var$editFunctions=array("^(?!.*(char|clob|text|blob))."=>"+/-","char|clob|text"=>"||",);var$fulltextOperator="MATCH";var$functions=array("hex","length","lower","round","unixepoch","upper");var$grouping=array("avg","count","count distinct","group_concat","max","min","sum");function
operators($Pi){$F=array("=","<",">","<=",">=","!=","&","LIKE","LIKE %%","IN","BETWEEN","IS NULL","NOT LIKE","NOT IN","IS NOT NULL");if(preg_match('~^fts\d+$~i',(string)idx($Pi,"Engine")))$F[]="MATCH";$F[]="SQL";return$F;}static
function
connect($L,$U,$B){return
parent::connect(":memory:","","");}function
__construct(Db$f){parent::__construct($f);if(min_version(3.31,0,$f))$this->generated=array("STORED","VIRTUAL");if(min_version(3.37,0,$f))$this->types[0]["any"]=0;}function
structuredTypes(){return
array_keys($this->types[0]);}function
quoteBinary($Mh){return"x".q(bin2hex($Mh));}function
typeName(\stdClass$k){$F=strtolower(idx((array)$k,'sqlite:decl_type',parent::typeName($k)));return
idx(array("string"=>"text","double"=>"real"),$F,$F);}function
engines(){$F=array("table");if(min_version("3.8.2")){if(min_version(3.37)){$F[]="STRICT";$F[]="STRICT, WITHOUT ROWID";}$F[]="WITHOUT ROWID";}return$F;}private
function
isVirtual(array$R){$Fc=$R["Engine"];return$Fc!=""&&!in_array($Fc,array_merge(array("view"),$this->engines()));}function
supportsIndex(array$R){return!is_view($R)&&!$this->isVirtual($R);}function
supportsAlterIndex(array$R){return$this->supportsIndex($R);}function
supportsAlterTable(array$Pi){return!$this->isVirtual($Pi);}function
shadowTables($Q){$F=array();if(min_version(3.37)){foreach(get_vals("SELECT name FROM pragma_table_list WHERE schema = 'main' AND type = 'shadow' ORDER BY name")as$y){if(preg_match('(^'.preg_quote($Q).'_[^_]*$)',$y))$F[]=array("table"=>$y,"ns"=>"");}}return$F;}function
fulltextSql($y,array$s,$D,$Oa){return
idf_escape($y)." MATCH ".q($D);}function
insertUpdate($Q,array$H,array$fh){$Y=array();foreach($H
as$M)$Y[]="(".implode(", ",$M).")";return
queries("REPLACE INTO ".table($Q)." (".implode(", ",array_keys(reset($H))).") VALUES\n".implode(",\n",$Y));}function
tableHelp($y,$Je=false){if(preg_match('~^sqlite_(seq|stat.)~',$y,$x))return"fileformat2.html#$x[1]tab";if(preg_match('~^sqlite(_temp)?_(master|schema)$~',$y))return"schematab.html";}function
checkConstraints($Q){$Hb=get_val("SELECT sql FROM sqlite_master WHERE type = 'table' AND name = ".q($Q),0,$this->conn);preg_match_all('~\s(?:CONSTRAINT\s+("(?:[^"]|"")*"|`(?:[^`]|``)*`|\[[^]]*]|\S+)\s+)?CHECK\s*(\(((?>[^()]+)|(?2))*\))~',$Hb,$rf,PREG_SET_ORDER);$F=array();foreach($rf
as$x){$eb=trim(substr($x[2],1,-1));$F[$x[1]!=""?idf_unescape($x[1]):$eb]=$eb;}return$F;}function
allFields(){$F=array();if(min_version(3.16)){$H=get_rows('SELECT m.name AS tab, p.name AS field, p.type, p."notnull", p.pk AS '.idf_escape("primary")."
FROM sqlite_master m, pragma_table_".(min_version(3.31)?"x":"")."info(m.name) p
WHERE m.type IN ('table', 'view')".(min_version(3.31)?"
AND p.hidden != 1":"").(min_version(3.37)?"
AND m.name NOT IN (SELECT name FROM pragma_table_list WHERE type = 'shadow')":"")."
ORDER BY (m.name LIKE 'sqlite_%'), m.name, p.cid",$this->conn);foreach($H
as$G){$G["type"]=type_affinity($G["type"]);$G["null"]=!$G["notnull"];$F[$G["tab"]][]=$G;}}else$F=parent::allFields();return$F;}}function
idf_escape($r){return'"'.str_replace('"','""',$r).'"';}function
table($r){return
idf_escape($r);}function
get_databases($pd){return
array();}function
limit($D,$Z,$v,$eg=0,$K=" "){return" $D$Z".($v?$K."LIMIT $v".($eg?" OFFSET $eg":""):"");}function
limit1($Q,$D,$Z,$K="\n"){return(preg_match('~^INTO~',$D)||get_val("SELECT sqlite_compileoption_used('ENABLE_UPDATE_DELETE_LIMIT')")?limit($D,$Z,1,0,$K):" $D WHERE rowid = (SELECT rowid FROM ".table($Q).$Z.$K."LIMIT 1)");}function
db_collation($h,array$jb){return
get_val("PRAGMA encoding");}function
logged_user(){return
get_current_user();}function
virtual_module($Ai){return(preg_match('~^CREATE\s+VIRTUAL\s+TABLE\s+(?:IF\s+NOT\s+EXISTS\s+)?(?:"[^"]*+"|`[^`]*+`|\[[^\]]*+\]|[^\s(]+)\s+USING\s+([a-z0-9_]+)~i',$Ai,$x)?$x[1]:"");}function
parse_comments($Ai){$F=array();if(virtual_module($Ai)!="")return$F;preg_match_all('~\'(?:[^\']|\'\')*\'|"(?:[^"]|"")*"|`(?:[^`]|``)*`|\[[^]]*]|--[^\n]*|/\*.*?\*/|[(),\n]|[^-/\'"`[(),\n]+|[-/]~s',$Ai,$rf);$Zb=0;$Tf=array();$Xe="";$Wf=true;foreach($rf[0]as$jj){if(preg_match('~^--~',$jj)){if($Zb==1&&!$Wf){$y=($Xe=="("?"":($Xe==","?idx($Tf,count($Tf)-2):end($Tf)));if(is_string($y))$F[$y]=trim(substr($jj,2));}}elseif($jj=="\n")$Wf=true;elseif(trim($jj)!=""&&!preg_match('~^/\*~',$jj)){$Wf=false;$Xe=($Zb==1&&$jj==","?",":($Zb==0&&$jj=="("?"(":""));if($Xe)$Tf[]=false;elseif($Zb==1&&end($Tf)===false&&$jj!="("&&$jj!=")"){$sh=preg_match('~^[\'"`[]~',$jj);$lk=($sh?idf_unescape($jj):preg_replace('~\s.*~s','',trim($jj)));$Tf[count($Tf)-1]=($sh||!preg_match('~^(CONSTRAINT|PRIMARY|UNIQUE|CHECK|FOREIGN)$~i',$lk)?$lk:null);}$Zb+=($jj=="("?1:($jj==")"?-1:0));}}return$F;}function
tables_list(){return
get_key_vals("SELECT name, type FROM sqlite_master WHERE type IN ('table', 'view')".(min_version(3.37)?" AND name NOT IN (SELECT name FROM pragma_table_list WHERE type = 'shadow')":"")."
ORDER BY (name LIKE 'sqlite_%'), name");}function
count_tables(array$Rb){return
array();}function
db_status(){$Ag=get_val("PRAGMA page_size");$Ad=get_val("PRAGMA freelist_count")*$Ag;return
array("Data_length"=>get_val("PRAGMA page_count")*$Ag-$Ad,"Index_length"=>0,"Data_free"=>$Ad,);}function
table_status($y="",$Zc=false){$F=array();$H=array();if(!$Zc&&$y==""){connection()->query("PRAGMA optimize = 0x10002");$H=get_key_vals("SELECT tbl, MAX(CAST(stat AS integer)) FROM sqlite_stat1 GROUP BY tbl");}foreach(get_rows("SELECT name AS Name, type AS Engine, sql, 'rowid' AS Oid, '' AS Auto_increment".(min_version(3.37)?", name IN (SELECT name FROM pragma_table_list WHERE type = 'shadow') AS dependent":"")." FROM sqlite_master WHERE type IN ('table', 'view') ".($y!=""?"AND name = ".q($y):"ORDER BY (name LIKE 'sqlite_%'), name"))as$G){if($G["Engine"]=="table"){$Ai=preg_replace('~(?:\s|--[^\n]*|/\*.*?\*/)+$~s','',$G["sql"]);$Ji=preg_replace('~.*\)~s','',$Ai);$G["Engine"]=virtual_module($G["sql"])?:(implode(", ",array_filter(array((preg_match('~\bSTRICT\b~i',$Ji)?"STRICT":0),(preg_match('~\bWITHOUT\s+ROWID\b~i',$Ji)?"WITHOUT ROWID":0),)))?:"table");$G["Comment"]=idx(parse_comments($G["sql"]),"","");}unset($G["sql"]);$G["Rows"]=idx($H,$G["Name"],0);$F[$G["Name"]]=$G;}if(!$Zc){foreach(get_rows("SELECT * FROM sqlite_sequence".($y!=""?" WHERE name = ".q($y):""),null,"")as$G)$F[$G["name"]]["Auto_increment"]=$G["seq"];}return$F;}function
is_view(array$R){return$R["Engine"]=="view";}function
fk_support(array$R){return!get_val("SELECT sqlite_compileoption_used('OMIT_FOREIGN_KEY')");}function
type_affinity($T){$T=strtolower($T);return(preg_match('~int~i',$T)?"integer":(preg_match('~char|clob|text~i',$T)?"text":(preg_match('~blob~i',$T)?"blob":(preg_match('~real|floa|doub~i',$T)?"real":(preg_match('~any~i',$T)?"any":"numeric")))));}function
fields($Q){$F=array();$Ai=get_val("SELECT sql FROM sqlite_master WHERE type = 'table' AND name = ".q($Q));$jh=array("select"=>1,"where"=>1,"order"=>1);if(!preg_match('~^sqlite(_temp)?_(master|schema)$~',$Q))$jh+=array("insert"=>1,"update"=>1);$Cd=preg_match('~^fts\d+$~i',virtual_module($Ai));$ob=parse_comments($Ai);foreach(get_rows("PRAGMA table_".(min_version(3.31)?"x":"")."info(".table($Q).")")as$G){if($G["hidden"]==1)continue;$y=$G["name"];$T=strtolower($G["type"]);$i=$G["dflt_value"];preg_match('~^(.+?)\s*(?:\((.*)\))?$~s',$T,$Kg);$sh=preg_match("~^'(?:[^']|'')*'$~",$i);$F[$y]=array("field"=>$y,"type"=>($Cd?"text":($Kg?$Kg[1]:type_affinity($T))),"full_type"=>$T,"length"=>($Cd?"":idx($Kg,2,"")),"default"=>($sh?idf_unescape($i):($i=="NULL"?null:$i)),"default_sql"=>($i!==null&&!$sh&&$i!="NULL"&&!is_numeric($i)),"null"=>!$G["notnull"],"privileges"=>$jh,"primary"=>$G["pk"],"comment"=>idx($ob,$y,""),);if($G["pk"]&&preg_match('~\bAUTOINCREMENT\b~i',$Ai))$F[$y]["auto_increment"]=true;}$r='[(,]\s*(("[^"]*+")+|[a-z0-9_]+)';$Eh='(?:[^,()\']|\'[^\']*+\'|\([^)]*+\))*?';preg_match_all('~'.$r.'\s+text\b'.$Eh.'COLLATE\s+(\'[^\']+\'|[a-z0-9_]+)~i',$Ai,$rf,PREG_SET_ORDER);foreach($rf
as$x){$y=idf_unescape($x[1]);if($F[$y])$F[$y]["collation"]=trim($x[3],"'");}preg_match_all('~'.$r.'\s'.$Eh.'GENERATED\s+ALWAYS\s+AS\s*\((.+?)\)\s+(STORED|VIRTUAL)~i',$Ai,$rf,PREG_SET_ORDER);foreach($rf
as$x){$y=idf_unescape($x[1]);if($F[$y]){$F[$y]["default"]=$x[3];$F[$y]["generated"]=strtoupper($x[4]);}}return$F;}function
indexes($Q,$g=null){$g=connection($g);$F=array();$Ai=get_val("SELECT sql FROM sqlite_master WHERE type = 'table' AND name = ".q($Q),0,$g);if(preg_match('~^fts\d+$~i',virtual_module($Ai)))return
array($Q=>array("type"=>"FULLTEXT","columns"=>array_keys(fields($Q)),"lengths"=>array(),"descs"=>array()));if(preg_match('~\bPRIMARY\s+KEY\s*\((([^)"]+|"[^"]*"|`[^`]*`)++)~i',$Ai,$x)){$F[""]=array("type"=>"PRIMARY","columns"=>array(),"lengths"=>array(),"descs"=>array());preg_match_all('~((("[^"]*+")+|(?:`[^`]*+`)+)|(\S+))(\s+(ASC|DESC))?(,\s*|$)~i',$x[1],$rf,PREG_SET_ORDER);foreach($rf
as$x){$F[""]["columns"][]=idf_unescape($x[2]).$x[4];$F[""]["descs"][]=(preg_match('~DESC~i',$x[5])?'1':null);}}if(!$F){foreach(fields($Q)as$y=>$k){if($k["primary"])$F[""]=array("type"=>"PRIMARY","columns"=>array($y),"lengths"=>array(),"descs"=>array(null));}}$Ci=get_key_vals("SELECT name, sql FROM sqlite_master WHERE type = 'index' AND tbl_name = ".q($Q),$g);foreach(get_rows("PRAGMA index_list(".table($Q).")",$g)as$G){$y=$G["name"];$s=array("type"=>($G["unique"]?"UNIQUE":"INDEX"));$s["lengths"]=array();$s["descs"]=array();foreach(get_rows("PRAGMA index_info(".idf_escape($y).")",$g)as$Lh){$s["columns"][]=$Lh["name"];$s["descs"][]=null;}if(preg_match('~^CREATE( UNIQUE)? INDEX '.preg_quote(idf_escape($y).' ON '.idf_escape($Q),'~').' \((.*)\)$~i',$Ci[$y],$_h)){preg_match_all('/("[^"]*+")+( DESC)?/',$_h[2],$rf);foreach($rf[2]as$t=>$W){if($W)$s["descs"][$t]='1';}}if(!$F[""]||$s["type"]!="UNIQUE"||$s["columns"]!=$F[""]["columns"]||$s["descs"]!=$F[""]["descs"]||!preg_match("~^sqlite_~",$y))$F[$y]=$s;}return$F;}function
foreign_keys($Q){$F=array();foreach(get_rows("PRAGMA foreign_key_list(".table($Q).")")as$G){$ud=&$F[$G["id"]];if(!$ud)$ud=$G;$ud["source"][]=$G["from"];$ud["target"][]=$G["to"];}return$F;}function
view($y){return
array("select"=>preg_replace('~^(?:[^`"[]+|`[^`]*`|"[^"]*")* AS\s+(?=\S)~iU','',get_val("SELECT sql FROM sqlite_master WHERE type = 'view' AND name = ".q($y))));}function
collations(){return(isset($_GET["create"])?get_vals("PRAGMA collation_list",1):array());}function
information_schema($h,$I=""){return
false;}function
error(){return
h(connection()->error);}function
check_sqlite_name($y){$Wc="db|sdb|sqlite";if(!preg_match("~^[^\\0]*\\.($Wc)\$~",$y)){connection()->error=lang(36,str_replace("|",", ",$Wc));return
false;}return
true;}function
create_database($h,$ib){if(file_exists($h)){connection()->error=lang(37);return
false;}if(!check_sqlite_name($h))return
false;try{$w=new
Db();$w->attach(server_parts(array("path"=>$h)),'','');}catch(\Exception$Pc){connection()->error=$Pc->getMessage();return
false;}$w->query('PRAGMA encoding = "UTF-8"');$w->query('CREATE TABLE adminer (i)');$w->query('DROP TABLE adminer');return
true;}function
drop_databases(array$Rb){connection()->attach(server_parts(array("path"=>":memory:")),'','');foreach($Rb
as$h){if(!check_sqlite_name($h))return
false;if(!@unlink($h)){connection()->error=lang(37);return
false;}}return
true;}function
rename_database($y,$ib){if(!check_sqlite_name($y))return
false;connection()->attach(server_parts(array("path"=>":memory:")),'','');connection()->error=lang(37);return@rename(DB,$y);}function
auto_increment(){return" PRIMARY KEY AUTOINCREMENT";}function
alter_table($Q,$y,array$l,array$rd,$mb,$Fc,$ib,$Aa,$Ig){$Oj=($Q==""||$rd||$Fc||$mb!==null);foreach($l
as$k){if($k[0]!=""||!$k[1]||$k[2]||$k[1][5]){$Oj=true;break;}}$b=array();$wg=array();foreach($l
as$k){if($k[1]){$b[]=($Oj?$k[1]:"ADD ".implode($k[1]));if($k[0]!="")$wg[$k[0]]=$k[1][0];}}if(!$Oj){foreach($b
as$W){if(!queries("ALTER TABLE ".table($Q)." $W"))return
false;}if($Q!=$y&&!queries("ALTER TABLE ".table($Q)." RENAME TO ".table($y)))return
false;}elseif(!recreate_table($Q,$y,$b,$wg,$rd,$Aa,array(),"","",$Fc,$mb))return
false;if($Aa){queries("BEGIN");queries("UPDATE sqlite_sequence SET seq = $Aa WHERE name = ".q($y));if(!connection()->affected_rows)queries("INSERT INTO sqlite_sequence (name, seq) VALUES (".q($y).", $Aa)");queries("COMMIT");}return
true;}function
recreate_table($Q,$y,array$l,array$wg,array$rd,$Aa="",$qe=array(),$sc="",$ha="",$Fc="",$mb=null){if($Q!=""){if(!$l){foreach(fields($Q)as$t=>$k){if($qe)$k["auto_increment"]=0;$l[]=process_field($k,$k);$wg[$t]=idf_escape($t);}}$gh=false;foreach($l
as$k){if($k[6])$gh=true;}$tc=array();foreach($qe
as$t=>$W){if($W[2]=="DROP"){$tc[$W[1]]=true;unset($qe[$t]);}}foreach(indexes($Q)as$Ne=>$s){$e=array();foreach($s["columns"]as$t=>$d){if(!$wg[$d])continue
2;$e[]=$wg[$d].($s["descs"][$t]?" DESC":"");}if(!$tc[$Ne]){if($s["type"]!="PRIMARY"||!$gh)$qe[]=array($s["type"],$Ne,$e);}}foreach($qe
as$t=>$W){if($W[0]=="PRIMARY"){unset($qe[$t]);$rd[]="  PRIMARY KEY (".implode(", ",$W[2]).")";}elseif(preg_match('~^sqlite_autoindex_~',$W[1])){unset($qe[$t]);$rd[]="  UNIQUE (".implode(", ",$W[2]).")";}}foreach(foreign_keys($Q)as$Ne=>$ud){foreach($ud["source"]as$t=>$d){if(!$wg[$d])continue
2;$ud["source"][$t]=idf_unescape($wg[$d]);}if(!isset($rd[" $Ne"]))$rd[]=" ".format_foreign_key($ud);}queries("PRAGMA foreign_keys = 0");queries("PRAGMA legacy_alter_table = 1");queries("BEGIN");}$Ua=array();$ob=array();foreach($l
as$k){if(preg_match('~GENERATED~',$k[3]))unset($wg[array_search($k[0],$wg)]);if(preg_match("~^ COMMENT ('.*')$~s",$k[5],$x))$ob[count($Ua)]=idf_unescape($x[1]);unset($k[5]);$Ua[]="  ".implode($k);}$Ua=array_merge($Ua,array_values(array_filter($rd)));foreach(driver()->checkConstraints($Q)as$t=>$Xa){if($t!=$sc)$Ua[]=" ".($t!=$Xa?" CONSTRAINT ".idf_escape($t):"")." CHECK ($Xa)";}if($ha)$Ua[]="  $ha";$Xi=($Q!=""&&$Q==$y?"adminer_$y":$y);if($Q!=""&&(!$Fc||$mb===null)){$O=table_status1($Q);$Fc=$Fc?:idx($O,"Engine");$mb=($mb===null?idx($O,"Comment",""):$mb);}$gf=function($Yi){return($Yi!=""?" -- ".preg_replace('~[\r\n]+~',' ',$Yi):"");};$Hb="CREATE TABLE ".table($Xi)." (".$gf($mb)."\n";foreach($Ua
as$p=>$Ta)$Hb
.=$Ta.($p<count($Ua)-1?",":"").$gf(idx($ob,$p))."\n";if(!queries("$Hb)".($Fc!="table"&&in_array($Fc,driver()->engines())?" $Fc":"")))return
false;if($Q!=""){if($wg&&!queries("INSERT INTO ".table($Xi)." (".implode(", ",$wg).") SELECT ".implode(", ",array_map('Adminer\idf_escape',array_keys($wg)))." FROM ".table($Q)))return
false;$wj=array();foreach(triggers($Q)as$uj=>$dj){$tj=trigger($uj,$Q);$wj[]="CREATE TRIGGER ".idf_escape($uj)." ".implode(" ",$dj)." ON ".table($y)."\n$tj[Statement]";}$Aa=$Aa?"":get_val("SELECT seq FROM sqlite_sequence WHERE name = ".q($Q));if(!queries("DROP TABLE ".table($Q))||($Q==$y&&!queries("ALTER TABLE ".table($Xi)." RENAME TO ".table($y)))||!alter_indexes($y,$qe))return
false;if($Aa)queries("UPDATE sqlite_sequence SET seq = $Aa WHERE name = ".q($y));foreach($wj
as$tj){if(!queries($tj))return
false;}if(get_rows("PRAGMA foreign_key_check(".table($y).")")){connection()->error="FOREIGN KEY constraint failed";return
false;}queries("COMMIT");queries("PRAGMA foreign_keys = 1");queries("PRAGMA legacy_alter_table = 0");}return
true;}function
index_sql($Q,$T,$y,$e){return"CREATE $T ".($T!="INDEX"?"INDEX ":"").idf_escape($y!=""?$y:uniqid($Q."_"))." ON ".table($Q)." $e";}function
alter_indexes($Q,$b){foreach($b
as$fh){if($fh[0]=="PRIMARY")return
recreate_table($Q,$Q,array(),array(),array(),"",$b);}foreach(array_reverse($b)as$W){if(!queries($W[2]=="DROP"?"DROP INDEX ".idf_escape($W[1]):index_sql($Q,$W[0],$W[1],"(".implode(", ",$W[2]).")")))return
false;}return
true;}function
truncate_tables(array$S){return
apply_queries("DELETE FROM",$S);}function
drop_views(array$ck){return
apply_queries("DROP VIEW",$ck);}function
drop_tables(array$S){return
apply_queries("DROP TABLE",$S);}function
move_tables(array$S,array$ck,$Vi){return
false;}function
trigger($y,$Q){if($y=="")return
array("Statement"=>"BEGIN\n\t;\nEND");$r='(?:[^`"\s]+|`[^`]*`|"[^"]*")+';$vj=trigger_options();preg_match("~^CREATE\\s+TRIGGER\\s*$r\\s*(".implode("|",$vj["Timing"]).")\\s+([a-z]+)(?:\\s+OF\\s+($r))?\\s+ON\\s*$r\\s*(?:FOR\\s+EACH\\s+ROW\\s+)?(.*)~is",get_val("SELECT sql FROM sqlite_master WHERE type = 'trigger' AND name = ".q($y)),$x);if(!$x)return
array();$dg=$x[3];return
array("Timing"=>strtoupper($x[1]),"Event"=>strtoupper($x[2]).($dg?" OF":""),"Of"=>idf_unescape($dg),"Trigger"=>$y,"Statement"=>$x[4],);}function
triggers($Q){$F=array();$vj=trigger_options();foreach(get_rows("SELECT * FROM sqlite_master WHERE type = 'trigger' AND tbl_name = ".q($Q))as$G){preg_match('~^CREATE\s+TRIGGER\s*(?:[^`"\s]+|`[^`]*`|"[^"]*")+\s*('.implode("|",$vj["Timing"]).')\s*(.*?)\s+ON\b~i',$G["sql"],$x);$F[$G["name"]]=array($x[1],$x[2]);}return$F;}function
trigger_options(){return
array("Timing"=>array("BEFORE","AFTER","INSTEAD OF"),"Event"=>array("INSERT","UPDATE","UPDATE OF","DELETE"),"Type"=>array("FOR EACH ROW"),);}function
last_id($E){return
get_val("SELECT LAST_INSERT_ROWID()");}function
explain(Db$f,$D){return$f->query("EXPLAIN QUERY PLAN $D");}function
found_rows(array$R,array$Z){}function
types($Wc=false){return
array();}function
create_sql($Q,$Aa,$Ii){$F=get_val("SELECT sql FROM sqlite_master WHERE type IN ('table', 'view') AND name = ".q($Q));foreach(indexes($Q)as$y=>$s){if($y==''||$s['type']=='FULLTEXT')continue;$F
.=";\n\n".index_sql($Q,$s['type'],$y,"(".implode(", ",array_map('Adminer\idf_escape',$s['columns'])).")");}return$F;}function
truncate_sql($Q){return"DELETE FROM ".table($Q);}function
use_sql($Qb,$Ii=""){return"";}function
trigger_sql($Q){return
implode(get_vals("SELECT sql || ';;\n' FROM sqlite_master WHERE type = 'trigger' AND tbl_name = ".q($Q)));}function
show_variables(){$F=array();foreach(get_rows("PRAGMA pragma_list")as$G){$y=$G["name"];if($y!="pragma_list"&&$y!="compile_options"){$F[$y]=array($y,'');foreach(get_rows("PRAGMA $y")as$G)$F[$y][1].=implode(", ",$G)."\n";}}return$F;}function
show_status(){$F=array();foreach(get_vals("PRAGMA compile_options")as$lg)$F[]=explode("=",$lg,2)+array('','');return$F;}function
convert_field(array$k){}function
unconvert_field(array$k,$F){return$F;}function
support($ad){return
preg_match('~^(check|columns|comment|database|drop_col|dump|fast_status|indexes|descidx|move_col|sql|status|table|transaction_ddl|trigger|variables|view|view_trigger)$~',$ad);}}add_driver("mssql","MS SQL");if(isset($_GET["mssql"])){define('Adminer\DRIVER',"mssql");if(extension_loaded("sqlsrv")&&$_GET["ext"]!="pdo"){class
Db
extends
SqlDb{var$extension="SQLSRV";private$link,$result,$warnings,$transaction=false;private
function
get_error(){$this->error="";foreach(sqlsrv_errors()as$j){$this->errno=$j["code"];$this->error
.="$j[message]\n";}$this->error=rtrim($this->error);}function
attach(array$L,$U,$B){sqlsrv_configure("WarningsReturnAsErrors",0);$vb=array("UID"=>$U,"PWD"=>$B,"CharacterSet"=>"UTF-8","ReturnDatesAsStrings"=>true);if(isset($_GET["sql"])&&!self::$instance)$vb["MultipleActiveResultSets"]=false;$N=adminer()->connectSsl();if(isset($N["Encrypt"]))$vb["Encrypt"]=$N["Encrypt"];if(isset($N["TrustServerCertificate"]))$vb["TrustServerCertificate"]=$N["TrustServerCertificate"];$h=adminer()->database();if($h!="")$vb["Database"]=$h;$Wg=$L["port"];$this->link=@sqlsrv_connect($L["host"].($Wg?",$Wg":""),$vb);if($this->link){$ue=sqlsrv_server_info($this->link);$this->server_info=$ue['SQLServerVersion'];}else$this->get_error();return($this->link?'':$this->error);}function
quote($P){return
unicode_prefix($P)."'".str_replace("'","''",$P)."'";}function
select_db($Qb){return$this->query(use_sql($Qb));}function
query($D,$Aj=false){$E=sqlsrv_query($this->link,$D);$this->error="";if(!$E){$this->get_error();return
false;}return$this->store_result($E);}function
multi_query($D){$this->result=sqlsrv_query($this->link,$D);$this->error="";if(!$this->result){$this->get_error();return
false;}return
true;}function
store_result($E=null){if(!$E)$E=$this->result;if(!$E)return
false;$this->warnings=sqlsrv_errors(SQLSRV_ERR_WARNINGS);if(sqlsrv_field_metadata($E))return
new
Result($E);$this->affected_rows=sqlsrv_rows_affected($E);return
true;}function
next_result(){if(!$this->result)return
false;$F=sqlsrv_next_result($this->result);if($F===false){$this->get_error();$this->result=null;return
true;}return!!$F;}function
warnings(){$F=array();foreach((array)$this->warnings
as$fk)$F[]=$fk["message"];return$F;}function
inTransaction(){return$this->transaction||(isset($_GET["sql"])&&get_val("SELECT @@TRANCOUNT",0,$this));}function
begin(){$this->transaction=sqlsrv_begin_transaction($this->link);if(!$this->transaction)$this->get_error();return$this->transaction;}function
commit(){if($this->transaction&&!sqlsrv_commit($this->link)){$this->get_error();return
false;}$this->transaction=false;return
true;}function
rollback(){if(!$this->transaction&&isset($_GET["sql"]))return!!$this->query("IF @@TRANCOUNT > 0 ROLLBACK");if($this->transaction&&!sqlsrv_rollback($this->link)){$this->get_error();return
false;}$this->transaction=false;return
true;}}class
Result{var$num_rows;private$result,$offset=0,$fields;function
__construct($E){$this->result=$E;}function
fetch_assoc(){return
sqlsrv_fetch_array($this->result,SQLSRV_FETCH_ASSOC);}function
fetch_row(){return
sqlsrv_fetch_array($this->result,SQLSRV_FETCH_NUMERIC);}function
fetch_field(){if(!$this->fields)$this->fields=sqlsrv_field_metadata($this->result);$_j=array(-155=>"datetimeoffset","time",-152=>"xml","varbinary","sql_variant",-11=>"uniqueidentifier","ntext","nvarchar","nchar","bit","tinyint","bigint","image","varbinary","binary","text",1=>"char","numeric","decimal","int","smallint","float","real","float",12=>"varchar",91=>"date","time","datetime",);$k=$this->fields[$this->offset++];$F=new
\stdClass;$F->name=$k["Name"];$F->native_type=idx($_j,$k["Type"],"");return$F;}function
seek($eg){for($p=0;$p<$eg;$p++)sqlsrv_fetch($this->result);}}function
last_id($E){return(string)get_val("SELECT SCOPE_IDENTITY()");}function
explain(Db$f,$D){$f->query("SET SHOWPLAN_ALL ON");$F=$f->query($D);$f->query("SET SHOWPLAN_ALL OFF");return$F;}}else{abstract
class
MssqlDb
extends
PdoDb{function
quote($P){return
unicode_prefix($P).parent::quote($P);}function
select_db($Qb){return$this->query(use_sql($Qb));}function
lastInsertId(){return$this->pdo->lastInsertId();}function
inTransaction(){return
parent::inTransaction()||(isset($_GET["sql"])&&get_val("SELECT @@TRANCOUNT"));}function
rollback(){return(parent::inTransaction()||!isset($_GET["sql"])?parent::rollback():!!$this->query("IF @@TRANCOUNT > 0 ROLLBACK"));}function
warnings(){$E=$this->multi;if(!is_object($E))return
array();$j=$E->errorInfo();return
array((string)$j[2]);}}function
last_id($E){return
connection()->lastInsertId();}function
explain(Db$f,$D){}if(extension_loaded("pdo_sqlsrv")){class
Db
extends
MssqlDb{var$extension="PDO_SQLSRV";function
attach(array$L,$U,$B){$Wg=$L["port"];$uc="sqlsrv:Server=$L[host]".($Wg?",$Wg":"").(isset($_GET["sql"])&&!self::$instance?";MultipleActiveResultSets=0":"");$N=adminer()->connectSsl();foreach(array("Encrypt","TrustServerCertificate")as$t){if(isset($N[$t]))$uc
.=";$t=".($N[$t]?1:0);}return$this->dsn($uc,$U,$B,array(\PDO::SQLSRV_ATTR_DIRECT_QUERY=>true));}}}elseif(extension_loaded("pdo_dblib")){class
Db
extends
MssqlDb{var$extension="PDO_DBLIB";function
attach(array$L,$U,$B){$Wg=$L["port"];$vi=$L["socket"];$_=array(1002=>true);$F=$this->dsn("dblib:charset=utf8;host=$L[host]".($Wg!=""?";port=$Wg":($vi!=""?";unix_socket=$vi":"")),$U,$B,$_);if(!$F){$this->query("SET ANSI_NULLS, QUOTED_IDENTIFIER, CONCAT_NULL_YIELDS_NULL, ANSI_WARNINGS, ANSI_PADDING ON");$this->server_info=get_val("SELECT CAST(SERVERPROPERTY('ProductVersion') AS varchar(20))",0,$this);}return$F;}}}}class
Driver
extends
SqlDriver{static$extensions=array("SQLSRV","PDO_SQLSRV","PDO_DBLIB");static$jush="mssql";static$serverSocket=true;var$insertFunctions=array("date|time"=>"getdate");var$editFunctions=array("int|decimal|real|float|money|datetime"=>"+/-","char|text"=>"+",);var$functions=array("len","lower","round","upper");var$grouping=array("avg","count","count distinct","max","min","sum");var$generated=array("PERSISTED","VIRTUAL");var$onActions="NO ACTION|CASCADE|SET NULL|SET DEFAULT";var$inout="|OUTPUT";private$unknownTypes=array();function
operators($Pi){return
array("=","<",">","<=",">=","!=","LIKE","LIKE %%","IN","BETWEEN","IS NULL","NOT LIKE","NOT IN","IS NOT NULL");}static
function
connect($L,$U,$B){if($L=="")$L="localhost:1433";return
parent::connect($L,$U,$B);}function
__construct(Db$f){parent::__construct($f);$this->types=array(lang(29)=>array("tinyint"=>3,"smallint"=>5,"int"=>10,"bigint"=>20,"bit"=>1,"decimal"=>0,"numeric"=>0,"real"=>12,"float"=>53,"smallmoney"=>10,"money"=>20,"vector"=>0,),lang(30)=>array("date"=>10,"smalldatetime"=>19,"datetime"=>23,"datetime2"=>19,"time"=>8,"datetimeoffset"=>26),lang(31)=>array("char"=>8000,"varchar"=>8000,"text"=>2147483647,"nchar"=>4000,"nvarchar"=>4000,"ntext"=>1073741823,"uniqueidentifier"=>36,"xml"=>2147483647,"json"=>2147483647,"sql_variant"=>8000,"hierarchyid"=>892,),lang(32)=>array("binary"=>8000,"varbinary"=>8000,"image"=>2147483647),lang(34)=>array("geometry"=>0,"geography"=>0),);$_j=array_flip(get_vals("SELECT name FROM sys.types WHERE is_user_defined = 0 ORDER BY name"));if($_j){foreach($this->types
as$o=>$Ld){foreach($Ld
as$T=>$u){if(isset($_j[$T]))unset($_j[$T]);else
unset($this->types[$o][$T]);}if(!$this->types[$o])unset($this->types[$o]);}$this->unknownTypes=array_keys($_j);}}function
types(){return
parent::types()+array_fill_keys($this->unknownTypes,0);}function
structuredTypes(){return
array_merge(parent::structuredTypes(),$this->unknownTypes);}function
typeName(\stdClass$k){return
idx((array)$k,'sqlsrv:decl_type',parent::typeName($k));}function
insertUpdate($Q,array$H,array$fh){$l=fields($Q);$Kj=array();$Z=array();$M=reset($H);$e="c".implode(", c",range(1,count($M)));$Ra=0;$ye=array();foreach($M
as$t=>$W){$Ra++;$y=idf_unescape($t);if(!$l[$y]["auto_increment"])$ye[$t]="c$Ra";if(isset($fh[$y]))$Z[]="$t = c$Ra";else$Kj[]="$t = c$Ra";}$Y=array();foreach($H
as$M)$Y[]="(".implode(", ",$M).")";if($Z){$je=queries("SET IDENTITY_INSERT ".table($Q)." ON");$F=queries("MERGE ".table($Q)." USING (VALUES\n\t".implode(",\n\t",$Y)."\n) AS source ($e) ON ".implode(" AND ",$Z).($Kj?"\nWHEN MATCHED THEN UPDATE SET ".implode(", ",$Kj):"")."\nWHEN NOT MATCHED THEN INSERT (".implode(", ",array_keys($je?$M:$ye)).") VALUES (".($je?$e:implode(", ",$ye)).");");if($je)queries("SET IDENTITY_INSERT ".table($Q)." OFF");}else$F=queries("INSERT INTO ".table($Q)." (".implode(", ",array_keys($M)).") VALUES\n".implode(",\n",$Y));return$F;}function
begin(){remember_query("BEGIN TRANSACTION");return$this->conn->begin();}function
convertSearch($r,array$W,array$k){return(preg_match('~^(bit|n?text|xml|json|vector|uniqueidentifier|sql_variant|hierarchyid|geography|geometry)$~',$k["type"])?"CAST($r AS nvarchar(max))":$r);}function
quoteBinary($Mh){return"0x".bin2hex($Mh);}function
warnings(){$F=array();foreach($this->conn->warnings()as$Df){$Df=trim(preg_replace('~^(\[[^]]+])+~','',$Df));if($Df!="")$F[]=$Df;}return
nl_br(h(implode("\n",$F)));}function
tableHelp($y,$Je=false){$hf=array("sys"=>"catalog-views/sys-","INFORMATION_SCHEMA"=>"information-schema-views/",);$w=$hf[get_schema()];if($w)return"relational-databases/system-$w".preg_replace('~_~','-',strtolower($y))."-transact-sql";}function
isSystem($h,$I=""){return($I!=""?information_schema($h,$I)||preg_match('~^(guest|db_(owner|accessadmin|securityadmin|ddladmin|backupoperator|(deny)?data(reader|writer)))$~',$I):in_array($h,array("master","tempdb","model","msdb")));}}function
unicode_prefix($P){return(strlen($P)!=utf8_length($P)?"N":"");}function
idf_escape($r){return"[".str_replace("]","]]",$r)."]";}function
table($r){return($_GET["ns"]!=""?idf_escape($_GET["ns"]).".":"").idf_escape($r);}function
get_databases($pd){return
get_vals("SELECT name FROM sys.databases WHERE name NOT IN ('master', 'tempdb', 'model', 'msdb')");}function
limit($D,$Z,$v,$eg=0,$K=" "){return($v?" TOP (".($v+$eg).")":"")." $D$Z";}function
limit1($Q,$D,$Z,$K="\n"){return
limit($D,$Z,1,0,$K);}function
db_collation($h,array$jb){return
get_val("SELECT collation_name FROM sys.databases WHERE name = ".q($h));}function
logged_user(){return
get_val("SELECT SUSER_NAME()");}function
tables_list(){return
get_key_vals("SELECT name, type_desc FROM sys.all_objects WHERE schema_id = SCHEMA_ID(".q(get_schema()).") AND type IN ('S', 'U', 'V') ORDER BY name");}function
count_tables(array$Rb){$F=array();foreach($Rb
as$h){connection()->select_db($h);$F[$h]=get_val("SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES");}return$F;}function
table_status($y="",$Zc=false){$F=array();$ti=array();foreach(get_rows("SELECT object_id, SUM(CASE WHEN index_id < 2 THEN row_count ELSE 0 END) AS [Rows],
SUM(CASE WHEN index_id < 2 THEN used_page_count ELSE 0 END) * 8192 AS Data_length,
SUM(CASE WHEN index_id > 1 THEN used_page_count ELSE 0 END) * 8192 AS Index_length,
SUM(reserved_page_count - used_page_count) * 8192 AS Data_free
FROM sys.dm_db_partition_stats
GROUP BY object_id",null,"")as$G){$cg=$G["object_id"];unset($G["object_id"]);$ti[$cg]=$G;}foreach(get_rows("SELECT ao.object_id, ao.name AS Name, ao.type_desc AS Engine,
	(SELECT cast(value as varchar(max)) FROM fn_listextendedproperty('MS_Description', 'SCHEMA', schema_name(schema_id), 'TABLE', ao.name, null, null)) AS Comment,
	CASE WHEN EXISTS (SELECT 1 FROM sys.indexes i WHERE i.object_id = ao.object_id AND i.is_disabled = 1) THEN 1 END AS invalid_index
FROM sys.all_objects AS ao
WHERE schema_id = SCHEMA_ID(".q(get_schema()).") AND type IN ('S', 'U', 'V') ".($y!=""?"AND name = ".q($y):"ORDER BY name"))as$G){$cg=$G["object_id"];unset($G["object_id"]);$F[$G["Name"]]=$G+idx($ti,$cg,array());}return$F;}function
is_view(array$R){return$R["Engine"]=="VIEW";}function
fk_support(array$R){return
true;}function
type_length($T,array$G){return(preg_match("~char|binary~",$T)?($G["max_length"]==-1?"max":intval($G["max_length"])/($T[0]=='n'?2:1)):($T=="decimal"?"$G[precision],$G[scale]":(preg_match('~^(datetime2|datetimeoffset|time)$~',$T)?$G["scale"]:($T=="vector"?(intval($G["max_length"])-8)/4:""))));}function
fields($Q){$ob=get_key_vals("SELECT objname, cast(value as varchar(max)) FROM fn_listextendedproperty('MS_DESCRIPTION', 'schema', ".q(get_schema()).", 'table', ".q($Q).", 'column', NULL)");$F=array();$Qi=get_val("SELECT object_id FROM sys.all_objects WHERE schema_id = SCHEMA_ID(".q(get_schema()).") AND type IN ('S', 'U', 'V') AND name = ".q($Q));foreach(get_rows("SELECT c.max_length, c.precision, c.scale, c.name, c.is_nullable, c.is_identity, c.collation_name,
	COALESCE(bt.name, t.name) type, d.definition [default], d.name default_constraint, i.is_primary_key
FROM sys.all_columns c
JOIN sys.types t ON c.user_type_id = t.user_type_id
LEFT JOIN sys.types bt ON t.system_type_id = bt.user_type_id AND t.is_user_defined = 1
LEFT JOIN sys.default_constraints d ON c.default_object_id = d.object_id
LEFT JOIN sys.index_columns ic ON c.object_id = ic.object_id AND c.column_id = ic.column_id
LEFT JOIN sys.indexes i ON ic.object_id = i.object_id AND ic.index_id = i.index_id
WHERE c.object_id = ".q($Qi))as$G){$T=$G["type"];$u=type_length($T,$G);$i=$G["default"];$Ub=false;if(preg_match("~^\\(N?('(?:[^']|'')*')\\)$~s",$i,$x))$i=idf_unescape($x[1]);elseif(preg_match('~^\(\(([-+]?[\d.]+)\)\)$~',$i,$x))$i=$x[1];elseif($i!==null){$i=preg_replace('~^\((.*)\)$~s','\1',$i);$Ub=true;}$F[$G["name"]]=array("field"=>$G["name"],"full_type"=>$T.($u!=""?"($u)":""),"type"=>$T,"length"=>$u,"default"=>$i,"default_sql"=>$Ub,"default_constraint"=>$G["default_constraint"],"null"=>$G["is_nullable"],"auto_increment"=>$G["is_identity"],"collation"=>$G["collation_name"],"privileges"=>($T=="timestamp"?array("select"=>1,"where"=>1,"order"=>1):array("insert"=>1,"select"=>1,"update"=>1,"where"=>1,"order"=>1)),"primary"=>$G["is_primary_key"],"comment"=>$ob[$G["name"]],);}foreach(get_rows("SELECT * FROM sys.computed_columns WHERE object_id = ".q($Qi))as$G){$F[$G["name"]]["generated"]=($G["is_persisted"]?"PERSISTED":"VIRTUAL");$F[$G["name"]]["default"]=$G["definition"];}return$F;}function
indexes($Q,$g=null){$F=array();foreach(get_rows("SELECT i.name, key_ordinal, is_unique, is_primary_key, is_disabled, c.name AS column_name, is_descending_key
FROM sys.indexes i
INNER JOIN sys.index_columns ic ON i.object_id = ic.object_id AND i.index_id = ic.index_id
INNER JOIN sys.columns c ON ic.object_id = c.object_id AND ic.column_id = c.column_id
WHERE OBJECT_NAME(i.object_id) = ".q($Q),$g)as$G){$y=$G["name"];$F[$y]["type"]=($G["is_primary_key"]?"PRIMARY":($G["is_unique"]?"UNIQUE":"INDEX"));$F[$y]["lengths"]=array();$F[$y]["invalid"]=!!$G["is_disabled"];$F[$y]["columns"][$G["key_ordinal"]]=$G["column_name"];$F[$y]["descs"][$G["key_ordinal"]]=($G["is_descending_key"]?'1':null);}return$F;}function
view($y){return
array("select"=>preg_replace('~^(?:[^[]|\[[^]]*])*\s+AS\s+~isU','',get_val("SELECT VIEW_DEFINITION FROM INFORMATION_SCHEMA.VIEWS WHERE TABLE_SCHEMA = SCHEMA_NAME() AND TABLE_NAME = ".q($y))));}function
collations(){$F=array();foreach(get_vals("SELECT name FROM fn_helpcollations()")as$ib)$F[preg_replace('~_.*~','',$ib)][]=$ib;return$F;}function
information_schema($h,$I=""){return
in_array($I!=""?$I:get_schema(),array("INFORMATION_SCHEMA","sys"));}function
error(){return
nl_br(h(preg_replace('~^(\[[^]]*])+~m','',connection()->error)));}function
create_database($h,$ib){return
queries("CREATE DATABASE ".idf_escape($h).(preg_match('~^[a-z0-9_]+$~i',$ib)?" COLLATE $ib":""));}function
drop_databases(array$Rb){return!!queries("DROP DATABASE ".implode(", ",array_map('Adminer\idf_escape',$Rb)));}function
rename_database($y,$ib){if(preg_match('~^[a-z0-9_]+$~i',$ib))queries("ALTER DATABASE ".idf_escape(DB)." COLLATE $ib");queries("ALTER DATABASE ".idf_escape(DB)." MODIFY NAME = ".idf_escape($y));return
true;}function
auto_increment(){return" IDENTITY".($_POST["Auto_increment"]!=""?"(".number($_POST["Auto_increment"]).",1)":"")." PRIMARY KEY";}function
alter_table($Q,$y,array$l,array$rd,$mb,$Fc,$ib,$Aa,$Ig){$b=array();$ob=array();$ug=fields($Q);foreach($l
as$k){$d=idf_escape($k[0]);$W=$k[1];if(!$W)$b["DROP"][]=" COLUMN $d";else{$sg=idx($ug[$k[0]],"comment");if($W[5]!=($sg!=""?" COMMENT ".q($sg):""))$ob[idf_unescape($W[0])]=array($sg!="",substr($W[5],9));unset($W[5]);if(preg_match('~ AS ~',$W[3]))unset($W[1],$W[2]);if($k[0]=="")$b["ADD"][]="\n  ".implode("",$W).($Q==""?substr($rd[$W[0]],16+strlen($W[0])):"");else{$i=$W[3];unset($W[3]);unset($W[6]);if($d!=$W[0])queries("EXEC sp_rename ".q(table($Q).".$d").", ".q(idf_unescape($W[0])).", 'COLUMN'");$b["ALTER COLUMN ".implode("",$W)][]="";$tg=$ug[$k[0]];if(default_value($tg)!=$i){if($tg["default"]!==null)$b["DROP"][]=" ".idf_escape($tg["default_constraint"]);if($i)$b["ADD"][]="\n $i FOR $d";}}}}if($Q==""){$ga=(array)$b["ADD"];foreach($rd
as$t=>$W){if(!is_string($t))$ga[]="\n$W";}if(!queries("CREATE TABLE ".table($y)." (".implode(",",$ga)."\n)"))return
false;}else{if($Q!=$y)queries("EXEC sp_rename ".q(table($Q)).", ".q($y));if($rd)$b[""]=$rd;foreach($b
as$t=>$W){if(!queries("ALTER TABLE ".table($y)." $t".implode(",",$W)))return
false;}}$mh="@name = N'MS_Description', @level0type = N'Schema', @level0name = ".q(get_schema()).", @level1type = N'Table', @level1name = ".q($y);foreach($ob
as$t=>$W){list($rc,$nb)=$W;$nh="$mh, @level2type = N'Column', @level2name = ".q($t);if($rc)queries("EXEC sp_dropextendedproperty $nh");if($nb!="")queries("EXEC sp_addextendedproperty $nh, @value = $nb");}if($mb!==null){if(idx(table_status1($y),"Comment")!="")queries("EXEC sp_dropextendedproperty $mh");if($mb!="")queries("EXEC sp_addextendedproperty $mh, @value = ".q($mb));}return
true;}function
alter_indexes($Q,$b){$s=array();$rc=array();$ph=array();foreach($b
as$W){if($W[2]=="DROP"){if($W[0]=="PRIMARY"||($W[0]=="UNIQUE"&&get_val("SELECT is_unique_constraint FROM sys.indexes WHERE object_id = OBJECT_ID(".q(table($Q)).") AND name = ".q($W[1]))))$rc[]=idf_escape($W[1]);else$s[]=idf_escape($W[1])." ON ".table($Q);}else$ph[]=($W[0]!="PRIMARY"?"CREATE $W[0] ".($W[0]!="INDEX"?"INDEX ":"").idf_escape($W[1]!=""?$W[1]:uniqid($Q."_"))." ON ".table($Q):"ALTER TABLE ".table($Q)." ADD PRIMARY KEY")." (".implode(", ",$W[2]).")";}if($rc)array_unshift($ph,"ALTER TABLE ".table($Q)." DROP ".implode(", ",$rc));if($s)array_unshift($ph,"DROP INDEX ".implode(", ",$s));foreach($ph
as$D){if(!queries($D))return
false;}return
true;}function
found_rows(array$R,array$Z){}function
foreign_keys($Q){$F=array();$ig=array("CASCADE","NO ACTION","SET NULL","SET DEFAULT");$I=get_schema();foreach(get_rows("EXEC sp_fkeys @fktable_name = ".q($Q).", @fktable_owner = ".q($I))as$G){$ud=&$F[$G["FK_NAME"]];$ud["db"]=($G["PKTABLE_QUALIFIER"]==DB?"":$G["PKTABLE_QUALIFIER"]);$ud["ns"]=($G["PKTABLE_OWNER"]==$I?"":$G["PKTABLE_OWNER"]);$ud["table"]=$G["PKTABLE_NAME"];$ud["on_update"]=$ig[$G["UPDATE_RULE"]];$ud["on_delete"]=$ig[$G["DELETE_RULE"]];$ud["source"][]=$G["FKCOLUMN_NAME"];$ud["target"][]=$G["PKCOLUMN_NAME"];}return$F;}function
truncate_tables(array$S){return
apply_queries("TRUNCATE TABLE",$S);}function
drop_views(array$ck){return
queries("DROP VIEW ".implode(", ",array_map('Adminer\table',$ck)));}function
drop_tables(array$S){return
queries("DROP TABLE ".implode(", ",array_map('Adminer\table',$S)));}function
move_tables(array$S,array$ck,$Vi){return
apply_queries("ALTER SCHEMA ".idf_escape($Vi)." TRANSFER",array_merge($S,$ck));}function
trigger($y,$Q){if($y=="")return
array();$H=get_rows("SELECT s.name [Trigger],
CASE WHEN OBJECTPROPERTY(s.id, 'ExecIsInsertTrigger') = 1 THEN 'INSERT'
	WHEN OBJECTPROPERTY(s.id, 'ExecIsUpdateTrigger') = 1 THEN 'UPDATE'
	WHEN OBJECTPROPERTY(s.id, 'ExecIsDeleteTrigger') = 1 THEN 'DELETE' END [Event],
CASE WHEN OBJECTPROPERTY(s.id, 'ExecIsInsteadOfTrigger') = 1 THEN 'INSTEAD OF' ELSE 'AFTER' END [Timing],
c.text
FROM sysobjects s
JOIN syscomments c ON s.id = c.id
WHERE s.xtype = 'TR' AND s.name = ".q($y));$F=reset($H);if($F)$F["Statement"]=preg_replace('~^.+\s+AS\s+~isU','',$F["text"]);return($F?:array());}function
triggers($Q){$F=array();foreach(get_rows("SELECT sys1.name,
CASE WHEN OBJECTPROPERTY(sys1.id, 'ExecIsInsertTrigger') = 1 THEN 'INSERT'
	WHEN OBJECTPROPERTY(sys1.id, 'ExecIsUpdateTrigger') = 1 THEN 'UPDATE'
	WHEN OBJECTPROPERTY(sys1.id, 'ExecIsDeleteTrigger') = 1 THEN 'DELETE' END [Event],
CASE WHEN OBJECTPROPERTY(sys1.id, 'ExecIsInsteadOfTrigger') = 1 THEN 'INSTEAD OF' ELSE 'AFTER' END [Timing]
FROM sysobjects sys1
JOIN sysobjects sys2 ON sys1.parent_obj = sys2.id
WHERE sys1.xtype = 'TR' AND sys2.name = ".q($Q))as$G)$F[$G["name"]]=array($G["Timing"],$G["Event"]);return$F;}function
trigger_options(){return
array("Timing"=>array("AFTER","INSTEAD OF"),"Event"=>array("INSERT","UPDATE","DELETE"),"Type"=>array("AS"),);}function
routine($y,$T){$Wb=get_val("SELECT m.definition
FROM sys.objects o
JOIN sys.sql_modules m ON m.object_id = o.object_id
WHERE o.schema_id = SCHEMA_ID(".q(get_schema()).") AND o.name = ".q($y)." AND o.type = ".q($T=="PROCEDURE"?"P":"FN"));if(!$Wb)return
array();$F=array("definition"=>preg_replace('~^(?:[^[]|\[[^]]*])*\s+AS\s+(?=\S)~isU','',$Wb),"fields"=>array());foreach(get_rows("SELECT p.name, TYPE_NAME(p.user_type_id) [type], p.max_length, p.precision, p.scale, p.is_output
FROM sys.parameters p
JOIN sys.objects o ON p.object_id = o.object_id
WHERE o.schema_id = SCHEMA_ID(".q(get_schema()).") AND o.name = ".q($y)."
ORDER BY p.parameter_id")as$G){$cd=$G["type"];$u=type_length($cd,$G);$k=array("field"=>preg_replace('~^@~','',$G["name"]),"type"=>$cd,"length"=>$u,"full_type"=>$cd.($u!=""?"($u)":""),"null"=>true,"inout"=>($G["is_output"]?"OUTPUT":""),);if($k["field"]=="")$F["returns"]=$k;else$F["fields"][]=$k;}return$F;}function
routines(){return
get_rows("SELECT o.name SPECIFIC_NAME, o.name ROUTINE_NAME,
	CASE o.type WHEN 'P' THEN 'PROCEDURE' ELSE 'FUNCTION' END ROUTINE_TYPE, TYPE_NAME(p.user_type_id) DTD_IDENTIFIER
FROM sys.objects o
LEFT JOIN sys.parameters p ON o.object_id = p.object_id AND p.parameter_id = 0
WHERE o.schema_id = SCHEMA_ID(".q(get_schema()).") AND o.type IN ('P', 'FN')
ORDER BY o.name");}function
routine_languages(){return
array();}function
routine_options($Kh){return
array();}function
routine_id($y,array$G){return
table($y);}function
schemas(){return
get_vals("SELECT name FROM sys.schemas");}function
get_schema(){if($_GET["ns"]!="")return$_GET["ns"];return
get_val("SELECT SCHEMA_NAME()");}function
set_schema($I,$g=null){$_GET["ns"]=$I;return!!get_val("SELECT 1 FROM sys.schemas WHERE name = ".q($I),0,$g);}function
schema_comment($I){return(string)get_val("SELECT CAST(value AS nvarchar(max)) FROM fn_listextendedproperty('MS_Description', 'SCHEMA', ".q($I).", NULL, NULL, NULL, NULL)");}function
set_schema_comment($I,$mb){$mh="@name = N'MS_Description', @level0type = N'Schema', @level0name = ".q($I);return(schema_comment($I)==""||queries("EXEC sp_dropextendedproperty $mh"))&&($mb==""||queries("EXEC sp_addextendedproperty $mh, @value = ".q($mb)));}function
create_sql($Q,$Aa,$Ii){$O=table_status1($Q);if(is_view($O)){$bk=view($Q);return"CREATE VIEW ".table($Q)." AS $bk[select]";}$mh="EXEC sp_addextendedproperty @name = N'MS_Description', @level0type = N'Schema', @level0name = ".q(get_schema()).", @level1type = N'Table', @level1name = ".q($Q);$ob=($O["Comment"]!=""?";\n\n$mh, @value = ".q($O["Comment"]):"");$l=array();$fh=false;foreach(fields($Q)as$y=>$k){$W=process_field($k,$k);if($W[6])$fh=true;unset($W[5]);$l[]=implode("",$W);if($k["comment"]!="")$ob
.=";\n\n$mh, @level2type = N'Column', @level2name = ".q($y).", @value = ".q($k["comment"]);}foreach(indexes($Q)as$y=>$s){if(!$fh||$s["type"]!="PRIMARY"){$e=array();foreach($s["columns"]as$t=>$W)$e[]=idf_escape($W).($s["descs"][$t]?" DESC":"");$y=idf_escape($y);$l[]=($s["type"]=="INDEX"?"INDEX $y":"CONSTRAINT $y ".($s["type"]=="UNIQUE"?"UNIQUE":"PRIMARY KEY"))." (".implode(", ",$e).")";}}foreach(driver()->checkConstraints($Q)as$y=>$Xa)$l[]="CONSTRAINT ".idf_escape($y)." CHECK ($Xa)";return"CREATE TABLE ".table($Q)." (\n\t".implode(",\n\t",$l)."\n)$ob";}function
foreign_keys_sql($Q){$l=array();foreach(foreign_keys($Q)as$rd)$l[]=ltrim(format_foreign_key($rd));return($l?"ALTER TABLE ".table($Q)." ADD\n\t".implode(",\n\t",$l).";\n\n":"");}function
truncate_sql($Q){return"TRUNCATE TABLE ".table($Q);}function
use_sql($Qb,$Ii=""){return"USE ".idf_escape($Qb);}function
use_schema_sql($I,$Ii){$y=idf_escape($I);$Hb="EXEC(".q("CREATE SCHEMA $y").")";$mb=schema_comment($I);if($mb!="")$Hb="BEGIN\n$Hb\nEXEC sp_addextendedproperty @name = N'MS_Description', @level0type = N'Schema', @level0name = ".q($I).", @value = ".q($mb)."\nEND";return($Ii=="DROP+CREATE"?"DROP SCHEMA IF EXISTS $y;\n":"")."IF SCHEMA_ID(".q($I).") IS NULL $Hb";}function
trigger_sql($Q){$F="";foreach(triggers($Q)as$y=>$tj)$F
.=create_trigger(" ON ".table($Q),trigger($y,$Q)).";";return$F;}function
convert_field(array$k){}function
unconvert_field(array$k,$F){return$F;}function
support($ad){return
preg_match('~^(check|comment|columns|database|drop_col|dump|fast_status|indexes|descidx|procedure|routine|scheme|sql|table|transaction_ddl|trigger|view|view_trigger)$~',$ad);}}add_driver("oracle","Oracle");if(isset($_GET["oracle"])){define('Adminer\DRIVER',"oracle");function
easy_connect(array$L){return
url_host($L["host"]).($L["port"]!=""?":$L[port]":"").$L["path"];}if(extension_loaded("oci8")&&$_GET["ext"]!="pdo"){class
Db
extends
SqlDb{var$extension="OCI8";var$error_offset=0;private$link,$transaction=false;function
_error($Ic,$j){$this->error=preg_replace('~^[^:]*: ~','',php_error_text($j));}function
attach(array$L,$U,$B){$this->link=@oci_new_connect($U,$B,easy_connect($L),"AL32UTF8");if($this->link){$this->server_info=oci_server_version($this->link);return'';}$j=oci_error();return($j?$j["message"]:lang(26));}function
quote($P){return"'".str_replace("'","''",$P)."'";}function
select_db($Qb){return$this->query("ALTER SESSION SET CURRENT_SCHEMA = ".idf_escape($Qb));}function
query($D,$Aj=false){$E=oci_parse($this->link,$D);$this->error="";$this->error_offset=0;if(!$E){$j=oci_error($this->link);$this->errno=$j["code"];$this->error=$j["message"];return
false;}set_error_handler(array($this,'_error'));$F=@oci_execute($E,($this->transaction?OCI_NO_AUTO_COMMIT:OCI_COMMIT_ON_SUCCESS));restore_error_handler();if(!$F){$j=oci_error($E);$this->error_offset=($j?$j["offset"]:0);}if($F){if(oci_num_fields($E))return
new
Result($E);$this->affected_rows=oci_num_rows($E);oci_free_statement($E);}return$F;}function
timeout($Nf){return
function_exists('oci_set_call_timeout')&&oci_set_call_timeout($this->link,$Nf);}function
inTransaction(){return$this->transaction;}function
begin(){$this->transaction=true;return
true;}function
commit(){return$this->end_transaction(@oci_commit($this->link));}function
rollback(){return$this->end_transaction(@oci_rollback($this->link));}private
function
end_transaction($F){$this->transaction=false;if(!$F){$j=oci_error($this->link);$this->errno=$j["code"];$this->error=$j["message"];}return$F;}}class
Result{var$num_rows;private$result,$offset=1;function
__construct($E){$this->result=$E;}private
function
convert($G){foreach((array)$G
as$t=>$W){if(is_a($W,'OCILob')||is_a($W,'OCI-Lob'))$G[$t]=$W->load();}return$G;}function
fetch_assoc(){return$this->convert(oci_fetch_assoc($this->result));}function
fetch_row(){return$this->convert(oci_fetch_row($this->result));}function
fetch_field(){$d=$this->offset++;$F=new
\stdClass;$F->name=oci_field_name($this->result,$d);$T=oci_field_type($this->result,$d);$F->native_type=idx(array(100=>"binary_float","binary_double"),$T,$T);return$F;}}}elseif(extension_loaded("pdo_oci")){class
Db
extends
PdoDb{var$extension="PDO_OCI";function
attach(array$L,$U,$B){return$this->dsn("oci:dbname=//".easy_connect($L).";charset=AL32UTF8",$U,$B);}function
select_db($Qb){return$this->query("ALTER SESSION SET CURRENT_SCHEMA = ".idf_escape($Qb));}}}class
Driver
extends
SqlDriver{static$extensions=array("OCI8","PDO_OCI");static$jush="oracle";static$serverPath=true;var$insertFunctions=array("date"=>"current_date","timestamp"=>"current_timestamp",);var$editFunctions=array("number|float|double"=>"+/-","date|timestamp"=>"+ interval/- interval","char|clob"=>"||",);var$functions=array("length","lower","round","upper");var$grouping=array("avg","count","count distinct","max","min","sum");function
operators($Pi){return
array("=","<",">","<=",">=","!=","LIKE","LIKE %%","IN","BETWEEN","IS NULL","NOT LIKE","NOT IN","IS NOT NULL","SQL");}static
function
connect($L,$U,$B){$f=parent::connect($L,$U,$B);if(is_object($f))$f->query("ALTER SESSION SET CURSOR_SHARING = FORCE"." NLS_DATE_FORMAT = 'YYYY-MM-DD HH24:MI:SS'"." NLS_TIMESTAMP_FORMAT = 'YYYY-MM-DD HH24:MI:SS.FF'"." NLS_TIMESTAMP_TZ_FORMAT = 'YYYY-MM-DD HH24:MI:SS.FF TZH:TZM'");return$f;}function
__construct(Db$f){parent::__construct($f);$this->types=array(lang(29)=>array("number"=>38,"binary_float"=>12,"binary_double"=>21),lang(30)=>array("date"=>19,"timestamp"=>29,"interval year"=>12,"interval day"=>28),lang(31)=>array("char"=>2000,"varchar2"=>4000,"nchar"=>2000,"nvarchar2"=>4000,"clob"=>4294967295,"nclob"=>4294967295),lang(32)=>array("raw"=>2000,"long raw"=>2147483648,"blob"=>4294967295,"bfile"=>4294967296),lang(34)=>array("sdo_geometry"=>0),);}function
begin(){return$this->conn->begin();}function
convertSearch($r,array$W,array$k){$T=$k["type"];$Pg=strpos($W["op"],"LIKE")!==false;if($T=="xmltype")return"XMLSERIALIZE(CONTENT $r AS VARCHAR2(4000))";if($T=="json")return"JSON_SERIALIZE($r)";if(preg_match('~^(date|timestamp)~',$T))return"TO_CHAR($r, 'YYYY-MM-DD HH24:MI:SS')";if(preg_match('~char~',$T)||(preg_match('~clob~',$T)&&$Pg))return$r;return(!$Pg&&preg_match(number_type(),$T)?$r:"TO_CHAR($r)");}function
quoteBinary($Mh){return"HEXTORAW(".q(bin2hex($Mh)).")";}function
typeName(\stdClass$k){return
strtolower(parent::typeName($k));}function
hasCStyleEscapes(){return
true;}function
errorLine($D){$eg=(isset($this->conn->error_offset)?$this->conn->error_offset:0);return($eg?substr_count($D,"\n",0,min($eg,strlen($D)))+1:0);}function
tableHelp($y,$Je=false){if(DB=="SYS"){if(preg_match('~^G?V_\$(.+)~',$y,$x))return"refrn/V-$x[1].html";if(preg_match('~^(ALL|DBA|USER)_~',$y))return"refrn/$y.html";}}function
select($Q,array$J,array$Z,array$o,array$og=array(),$v=1,$A=0,$hh=false){if(in_array("*",$J)){$Cb=array();$xd=false;foreach(fields($Q)as$y=>$k){$wa=convert_field($k);$xd=($xd||$wa);$Cb[]=($wa?"$wa AS ":"").idf_escape($y);}if($xd)$J=$Cb;}return
parent::select($Q,$J,$Z,$o,$og,$v,$A,$hh);}function
allFields(){$F=array();$H=get_rows('SELECT c.table_name "tab", c.column_name "field", c.data_type "type", c.nullable "nullable",
	c.data_precision "precision", c.data_scale "scale", c.char_col_decl_length "char_length"
FROM all_tab_columns c
WHERE '.where_owner("c.owner").'
ORDER BY c.table_name, c.column_id',$this->conn);foreach($H
as$G){$u="$G[precision],$G[scale]";$G["length"]=(strpos($G["type"],"(")?"":($u==","?$G["char_length"]:$u));$G["type"]=strtolower($G["type"]);$G["null"]=($G["nullable"]=="Y");$F[$G["tab"]][]=$G;}return$F;}}function
idf_escape($r){return'"'.str_replace('"','""',$r).'"';}function
table($r){return
idf_escape($r);}function
get_databases($pd){$F=get_vals("SELECT username FROM all_users WHERE oracle_maintained = 'N' ORDER BY 1");return($F?:get_vals("SELECT username FROM all_users ORDER BY 1"));}function
limit($D,$Z,$v,$eg=0,$K=" "){return($eg?" * FROM (SELECT t.*, rownum AS rnum FROM (SELECT $D$Z) t WHERE rownum <= ".($v+$eg).") WHERE rnum > $eg":($v?" * FROM (SELECT $D$Z) WHERE rownum <= ".($v+$eg):" $D$Z"));}function
limit1($Q,$D,$Z,$K="\n"){return" $D$Z";}function
db_collation($h,array$jb){return
get_val("SELECT value FROM nls_database_parameters WHERE parameter = 'NLS_CHARACTERSET'");}function
logged_user(){return
get_val("SELECT USER FROM DUAL");}function
where_owner($zg="owner"){return"$zg = ".q(DB);}function
views_table($e){return"(SELECT $e FROM all_views WHERE ".where_owner().")";}function
objects_table(){return"(SELECT object_name, DECODE(object_type, 'VIEW', 'view', 'table') object_type FROM all_objects WHERE ".where_owner()." AND object_type IN ('TABLE', 'VIEW'))";}function
tables_list(){return
get_key_vals("SELECT * FROM ".objects_table()." ORDER BY 1");}function
count_tables(array$Rb){$F=array();foreach($Rb
as$h)$F[$h]=get_val("SELECT COUNT(*) FROM all_objects WHERE object_type IN ('TABLE', 'VIEW') AND owner = ".q($h));return$F;}function
table_status($y="",$Zc=false){$F=array();$Qh=q($y);if($Zc||$y!=""){foreach(get_rows('SELECT object_name "Name", object_type "Engine" FROM '.objects_table().($y!=""?" WHERE object_name = $Qh":"").' ORDER BY 1')as$G)$F[$G["Name"]]=$G;return$F;}foreach(get_rows('SELECT t.table_name "Name", \'table\' "Engine", s.bytes "Data_length", i.bytes "Index_length", t.num_rows "Rows"
FROM all_tables t
LEFT JOIN (SELECT segment_name, SUM(bytes) bytes FROM user_segments WHERE segment_type LIKE \'TABLE%\' GROUP BY segment_name) s ON s.segment_name = t.table_name
LEFT JOIN (SELECT i.table_name, SUM(s.bytes) bytes FROM user_indexes i
	JOIN user_segments s ON s.segment_name = i.index_name AND s.segment_type LIKE \'INDEX%\' GROUP BY i.table_name) i ON i.table_name = t.table_name
WHERE '.where_owner("t.owner")."
UNION SELECT view_name, 'view', 0, 0, 0 FROM ".views_table("view_name")."
ORDER BY 1")as$G)$F[$G["Name"]]=$G;foreach(get_vals("SELECT table_name FROM all_indexes ai WHERE ".where_owner("table_owner")." AND (status = 'UNUSABLE'
	OR EXISTS (SELECT 1 FROM all_ind_partitions aip WHERE aip.index_owner = ai.owner AND aip.index_name = ai.index_name AND aip.status = 'UNUSABLE')
	OR EXISTS (SELECT 1 FROM all_ind_subpartitions ais WHERE ais.index_owner = ai.owner AND ais.index_name = ai.index_name AND ais.status = 'UNUSABLE'))")as$Q){if($F[$Q])$F[$Q]["invalid_index"]="1";}return$F;}function
is_view(array$R){return$R["Engine"]=="view";}function
fk_support(array$R){return
true;}function
fields($Q){$F=array();$je=null;foreach(get_rows("SELECT * FROM all_tab_columns WHERE table_name = ".q($Q)." AND ".where_owner()." ORDER BY column_id")as$G){$T=$G["DATA_TYPE"];$u="$G[DATA_PRECISION],$G[DATA_SCALE]";if($u==","){$u=($T=="RAW"?$G["DATA_LENGTH"]:(preg_match('~CHAR~',$T)?$G["CHAR_COL_DECL_LENGTH"]:""));if($G["CHAR_USED"]=="C"&&preg_match('~^(VARCHAR2|CHAR)$~',$T))$u="$G[CHAR_LENGTH] CHAR";}elseif(strpos($T,"("))$u="";$i=$G["DATA_DEFAULT"];$Ub=false;if($i!==null){$i=rtrim($i);if(preg_match("~^'(?:[^']|'')*'\$~s",$i))$i=idf_unescape($i);elseif(preg_match('~^NULL$~i',$i))$i=null;else$Ub=!is_numeric($i);}if($G["IDENTITY_COLUMN"]=="YES"){if($je===null)$je=get_key_vals("SELECT column_name, generation_type FROM all_tab_identity_cols WHERE table_name = ".q($Q)." AND ".where_owner());$i="GENERATED ".$je[$G["COLUMN_NAME"]].($G["DEFAULT_ON_NULL"]=="YES"?" ON NULL":"")." AS IDENTITY";}$jh=array("insert"=>1,"select"=>1,"update"=>1,"order"=>1);if($G["DATA_TYPE_OWNER"]==""||$T=="XMLTYPE")$jh["where"]=1;$F[$G["COLUMN_NAME"]]=array("field"=>$G["COLUMN_NAME"],"full_type"=>$T.($u?"($u)":""),"type"=>strtolower($T),"length"=>$u,"default"=>$i,"default_sql"=>($Ub&&$G["IDENTITY_COLUMN"]!="YES"),"null"=>($G["NULLABLE"]=="Y"),"auto_increment"=>($G["IDENTITY_COLUMN"]=="YES"),"privileges"=>$jh,);}return$F;}function
table_constraints($Q,$g=null){$F=array();foreach(get_rows('SELECT c.constraint_name "name", c.constraint_type "type", c.r_owner "r_owner", c.r_constraint_name "r_constraint", c.delete_rule "delete_rule", cc.column_name "column"
FROM all_constraints c
JOIN all_cons_columns cc ON cc.owner = c.owner AND cc.constraint_name = c.constraint_name
WHERE c.constraint_type IN (\'P\', \'U\', \'R\') AND '.where_owner("c.owner")." AND c.table_name = ".q($Q).'
ORDER BY cc.position',$g)as$G){$y=$G["name"];$F[$y]["type"]=$G["type"];$F[$y]["r_owner"]=$G["r_owner"];$F[$y]["r_constraint"]=$G["r_constraint"];$F[$y]["delete_rule"]=$G["delete_rule"];$F[$y]["columns"][]=$G["column"];}return$F;}function
indexes($Q,$g=null){$F=array();$yb=array();$Hg=array();foreach(table_constraints($Q,$g)as$y=>$xb)$yb[$y]=$xb["type"];foreach(get_rows("SELECT aic.*, atc.data_default, ai.status, ai.partitioned
FROM all_ind_columns aic
JOIN all_indexes ai ON ai.owner = aic.index_owner AND ai.index_name = aic.index_name
LEFT JOIN all_tab_cols atc ON aic.column_name = atc.column_name AND aic.table_name = atc.table_name AND aic.index_owner = atc.owner
WHERE aic.table_name = ".q($Q)." AND ".where_owner("aic.table_owner")."
ORDER BY aic.column_position",$g)as$G){$oe=$G["INDEX_NAME"];$lb=$G["DATA_DEFAULT"];$lb=($lb?trim($lb,'"'):$G["COLUMN_NAME"]);$T=idx($yb,$oe);$F[$oe]["type"]=($T=="P"?"PRIMARY":($T=="U"?"UNIQUE":"INDEX"));$F[$oe]["invalid"]=($G["STATUS"]=="UNUSABLE");$F[$oe]["columns"][]=$lb;$F[$oe]["lengths"][]=($G["CHAR_LENGTH"]&&$G["CHAR_LENGTH"]!=$G["COLUMN_LENGTH"]?$G["CHAR_LENGTH"]:null);$F[$oe]["descs"][]=($G["DESCEND"]&&$G["DESCEND"]=="DESC"?'1':null);if($G["PARTITIONED"]=="YES")$Hg[$oe]=q($oe);}if($Hg){$me=where_owner("index_owner")." AND index_name IN (".implode(", ",$Hg).") AND status = 'UNUSABLE'";foreach(get_rows("SELECT index_name FROM all_ind_partitions WHERE $me UNION SELECT index_name FROM all_ind_subpartitions WHERE $me",$g)as$G)$F[$G["INDEX_NAME"]]["invalid"]=true;}uasort($F,function($ca,$Da){$og=array("PRIMARY"=>0,"UNIQUE"=>1,"INDEX"=>2);return$og[$ca["type"]]-$og[$Da["type"]];});return$F;}function
view($y){$H=get_rows('SELECT text "select" FROM '.views_table("view_name, text").' WHERE view_name = '.q($y));return($H?$H[0]:array());}function
collations(){return
array();}function
information_schema($h,$I=""){return
in_array($I!=""?$I:$h,array("INFORMATION_SCHEMA","SYS","SYSTEM"));}function
error(){return
h(connection()->error);}function
explain(Db$f,$D){$f->query("EXPLAIN PLAN FOR $D");return$f->query("SELECT * FROM plan_table");}function
found_rows(array$R,array$Z){}function
auto_increment(){return"";}function
alter_table($Q,$y,array$l,array$rd,$mb,$Fc,$ib,$Aa,$Ig){$b=$rc=array();$ug=($Q?fields($Q):array());foreach($l
as$k){$W=$k[1];if($W&&$k[0]!=""&&idf_escape($k[0])!=$W[0])queries("ALTER TABLE ".table($Q)." RENAME COLUMN ".idf_escape($k[0])." TO $W[0]");$tg=$ug[$k[0]];if($W&&$tg){$gg=process_field($tg,$tg);if($W[2]==$gg[2])$W[2]="";}if($W){list($W[2],$W[3])=array($W[3],$W[2]);$b[]=($Q!=""?($k[0]!=""?"MODIFY (":"ADD ("):"  ").implode($W).($Q!=""?")":"");}else$rc[]=idf_escape($k[0]);}if($Q=="")return
queries("CREATE TABLE ".table($y)." (\n".implode(",\n",array_merge($b,$rd))."\n)");return(!$b||queries("ALTER TABLE ".table($Q)."\n".implode("\n",$b)))&&(!$rc||queries("ALTER TABLE ".table($Q)." DROP (".implode(", ",$rc).")"))&&($Q==$y||queries("ALTER TABLE ".table($Q)." RENAME TO ".table($y)));}function
alter_indexes($Q,$b){$rc=array();$ph=array();foreach($b
as$W){if($W[0]!="INDEX"){$W[2]=preg_replace('~ DESC$~','',$W[2]);$Hb=($W[2]=="DROP"?"\nDROP CONSTRAINT ".idf_escape($W[1]):"\nADD".($W[1]!=""?" CONSTRAINT ".idf_escape($W[1]):"")." $W[0] ".($W[0]=="PRIMARY"?"KEY ":"")."(".implode(", ",$W[2]).")");array_unshift($ph,"ALTER TABLE ".table($Q).$Hb);}elseif($W[2]=="DROP")$rc[]=idf_escape($W[1]);else$ph[]="CREATE INDEX ".idf_escape($W[1]!=""?$W[1]:uniqid($Q."_"))." ON ".table($Q)." (".implode(", ",$W[2]).")";}if($rc)array_unshift($ph,"DROP INDEX ".implode(", ",$rc));foreach($ph
as$D){if(!queries($D))return
false;}return
true;}function
foreign_keys($Q){$F=array();$Wi=array();foreach(table_constraints($Q)as$y=>$xb){if($xb["type"]=="R"){$F[$y]=array("source"=>$xb["columns"],"target"=>array(),"on_delete"=>$xb["delete_rule"],"on_update"=>null,);$Wi[$y]=array($xb["r_owner"],$xb["r_constraint"]);}}if($Wi){$Z=array();foreach($Wi
as$Vi)$Z[]="(owner = ".q($Vi[0])." AND constraint_name = ".q($Vi[1]).")";foreach(get_rows("SELECT owner, constraint_name, table_name, column_name FROM all_cons_columns WHERE ".implode(" OR ",array_unique($Z))." ORDER BY position")as$G){foreach($Wi
as$y=>$Vi){if($Vi==array($G["OWNER"],$G["CONSTRAINT_NAME"])){$F[$y]["db"]=$G["OWNER"];$F[$y]["table"]=$G["TABLE_NAME"];$F[$y]["target"][]=$G["COLUMN_NAME"];}}}}return$F;}function
trigger($y,$Q){if($y=="")return
array();$H=get_rows('SELECT trigger_name "Trigger", trigger_type "Type", triggering_event "Event", trigger_body "Statement"
FROM all_triggers
WHERE trigger_name = '.q($y)." AND ".where_owner());$F=reset($H);if($F){$T=$F["Type"];$F["Timing"]=(preg_match('~^(BEFORE|AFTER|INSTEAD OF)~',$T,$x)?$x[1]:$T);$F["Type"]=(preg_match('~EACH ROW~',$T)||$T=="INSTEAD OF"?"FOR EACH ROW":"");}return($F?:array());}function
triggers($Q){$F=array();foreach(get_rows("SELECT trigger_name, trigger_type, triggering_event FROM all_triggers WHERE table_name = ".q($Q)." AND ".where_owner())as$G)$F[$G["TRIGGER_NAME"]]=array(preg_replace('~ (STATEMENT|EACH ROW)$~','',$G["TRIGGER_TYPE"]),$G["TRIGGERING_EVENT"]);return$F;}function
trigger_options(){return
array("Timing"=>array("BEFORE","AFTER","INSTEAD OF"),"Event"=>array("INSERT","UPDATE","DELETE","INSERT OR UPDATE","INSERT OR DELETE","UPDATE OR DELETE","INSERT OR UPDATE OR DELETE"),"Type"=>array("FOR EACH ROW",""),);}function
truncate_tables(array$S){return
apply_queries("TRUNCATE TABLE",$S);}function
drop_views(array$ck){return
apply_queries("DROP VIEW",$ck);}function
drop_tables(array$S){return
apply_queries("DROP TABLE",$S);}function
last_id($E){return"0";}function
create_database($h,$ib){$F=queries("CREATE USER ".idf_escape($h)." NO AUTHENTICATION");return($F?queries("GRANT UNLIMITED TABLESPACE TO ".idf_escape($h)):$F);}function
drop_databases(array$Rb){$F=true;foreach($Rb
as$h)$F=!!queries("DROP USER ".idf_escape($h)." CASCADE")&&$F;return$F;}function
rename_database($y,$ib){return!!queries("ALTER USER ".idf_escape(DB)." RENAME TO ".idf_escape($y));}function
show_variables(){return
get_rows('SELECT name, display_value FROM v$parameter');}function
show_status(){$F=array();$H=get_rows('SELECT * FROM v$instance');foreach(reset($H)as$t=>$W)$F[]=array($t,$W);return$F;}function
process_list(){return
get_rows('SELECT
	sess.process AS "process",
	sess.username AS "user",
	sess.schemaname AS "schema",
	sess.status AS "status",
	sess.wait_class AS "wait_class",
	sess.seconds_in_wait AS "seconds_in_wait",
	sql.sql_text AS "sql_text",
	sess.machine AS "machine",
	sess.port AS "port"
FROM v$session sess
LEFT JOIN v$sql sql ON sql.sql_id = sess.sql_id
WHERE sess.type = \'USER\'
ORDER BY PROCESS
');}function
convert_field(array$k){if($k["type"]=="sdo_geometry")return"SDO_UTIL.TO_WKTGEOMETRY(".idf_escape($k["field"]).")";}function
unconvert_field(array$k,$F){return($k["type"]=="sdo_geometry"?"SDO_UTIL.FROM_WKTGEOMETRY($F)":$F);}function
support($ad){return
preg_match('~^(columns|database|drop_col|fast_status|indexes|descidx|processlist|sql|status|table|trigger|variables|view|view_trigger)$~',$ad);}}class
Adminer{static$instance;var$error='';private$values=array();private$described=array();function
name(){return"<a href='https://www.adminer.org/editor/'".target_blank()." id='h1'><img src='".h(preg_replace("~\\?.*~","",ME)."?file=logo.svg&version=6.2.0+a36d1ecb")."' width='24' height='24' alt='' id='logo'>".lang(38)."</a>";}function
credentials(){return
array(SERVER,$_GET["username"],get_password());}function
connectSsl(){}function
permanentLogin($Hb=false){return
password_file($Hb);}function
bruteForceKey(){return$_SERVER["REMOTE_ADDR"];}function
verifyLoginToken(){return
true;}function
serverName($L){return'';}function
database(){if(connection()){$Rb=adminer()->databases(false);if(!$Rb)return
get_val("SELECT SUBSTRING_INDEX(CURRENT_USER, '@', 1)");foreach($Rb
as$h){if(!information_schema($h))return$h;}return$Rb[0];}return
null;}function
operators($Pi=null){return
array("<=",">=");}function
schemas(){return
schemas();}function
databases($pd=true){return
get_databases($pd);}function
pluginsLinks(){}function
queryTimeout(){return
5;}function
afterConnect(){}function
headers(){}function
csp(array$Kb){return$Kb;}function
verifyVersion(){return
true;}function
serviceWorker(){service_worker();}function
manifest(){$ee=$_SERVER["HTTP_HOST"]?:$_SERVER["SERVER_NAME"];$Zh=preg_replace('~\?.*~','',ME)?:'.';return
array('name'=>"Adminer Editor".($ee!=""?" - $ee":""),'short_name'=>'Adminer Editor','description'=>lang(39),'start_url'=>$Zh,'scope'=>$Zh,'display'=>'minimal-ui','icons'=>array(array('src'=>preg_replace("~\\?.*~","",ME)."?file=logo.svg&version=6.2.0+a36d1ecb",'sizes'=>'any','type'=>'image/svg+xml')),);}function
head($Ob=null){return
true;}function
bodyClass(){echo" editor";}function
css(){$F=array();foreach(array("","-dark")as$Kf){$m="adminer$Kf.css";if(file_exists($m)){$dd=file_get_contents($m);$F["$m?v=".crc32($dd)]=($Kf?"dark":(preg_match('~prefers-color-scheme:\s*dark~',$dd)?'':'light'));}}return$F;}function
loginForm(){echo"<table class='layout'>\n",adminer()->loginFormField('username','<tr><th>'.lang(40).'<td>',input_hidden("auth[driver]","server").'<input name="auth[username]" autofocus value="'.h($_GET["username"]).'" autocomplete="username" autocapitalize="off">'),adminer()->loginFormField('password','<tr><th>'.lang(41).'<td>','<input type="password" name="auth[password]" autocomplete="current-password">'),"</table>\n","<p><input type='submit' value='".lang(42)."'>\n",checkbox("auth[permanent]",1,$_COOKIE["adminer_permanent"],lang(43))."\n";}function
loginFormField($y,$Yd,$X){return$Yd.$X."\n";}function
login($mf,$B){if($B=="")return
lang(44);if(!Driver::$passwords)return
lang(45);if(!password_required())return
lang(46);return
true;}function
tableName(array$Pi){return
h(isset($Pi["Engine"])?($Pi["Comment"]!=""?$Pi["Comment"]:$Pi["Name"]):"");}function
fieldName(array$k,$og=0){return
h(preg_replace('~\s+\[.*\]$~','',($k["comment"]!=""?$k["comment"]:$k["field"])));}function
selectLinks(array$Pi,$M=""){$a=$Pi["Name"];if($M!==null)echo'<p class="tabs"><a href="'.h(ME.'edit='.url_escape($a).$M).'">'.lang(47)."</a>\n";}function
foreignKeys($Q){return
foreign_keys($Q);}function
backwardKeys($Q,$Oi){$F=array();foreach(get_rows("SELECT TABLE_NAME, CONSTRAINT_NAME, COLUMN_NAME, REFERENCED_COLUMN_NAME
FROM information_schema.KEY_COLUMN_USAGE
WHERE TABLE_SCHEMA = ".q(adminer()->database())."
AND REFERENCED_TABLE_SCHEMA = ".q(adminer()->database())."
AND REFERENCED_TABLE_NAME = ".q($Q)."
ORDER BY ORDINAL_POSITION",null,"")as$G)$F[$G["TABLE_NAME"]]["keys"][$G["CONSTRAINT_NAME"]][$G["COLUMN_NAME"]]=$G["REFERENCED_COLUMN_NAME"];foreach($F
as$t=>$W){$y=adminer()->tableName(table_status1($t,true));if($y!=""){$Qh=preg_quote($Oi);$K="(:|\\s*-)?\\s+";$F[$t]["name"]=(preg_match("(^$Qh$K(.+)|^(.+?)$K$Qh\$)iu",$y,$x)?$x[2].$x[3]:$y);}else
unset($F[$t]);}return$F;}function
backwardKeysPrint(array$Ga,array$G){foreach($Ga
as$Q=>$Fa){foreach($Fa["keys"]as$kb){$w=ME.'select='.url_escape($Q);$p=0;foreach($kb
as$d=>$W)$w
.=where_link($p++,$d,$G[$W]);echo"<a href='".h($w)."'>".h($Fa["name"])."</a>";$w=ME.'edit='.url_escape($Q);foreach($kb
as$d=>$W)$w
.="&set[".url_escape(bracket_escape($d))."]=".url_escape($G[$W]);echo"<a href='".h($w)."' title='".lang(47)."'>+</a> ";}}}function
selectQuery($D,$Di,$Yc=false){return
sql_comment($D,format_time($Di));}function
rowDescription($Q){foreach(fields($Q)as$k){if(preg_match("~char|text~",$k["type"]))return
idf_escape($k["field"]);}return"";}function
rowDescriptions(array$H,array$td){$F=$H;foreach($H[0]as$t=>$W){if(list($Q,$q,$y)=$this->_foreignColumn($td,$t)){$ke=array();foreach($H
as$G){if(isset($G[$t]))$ke[$G[$t]]=q($G[$t]);}if(!$ke)continue;$dc=$this->values[$Q];if(!$dc)$dc=get_key_vals("SELECT $q, $y FROM ".table($Q)." WHERE $q IN (".implode(", ",$ke).")");$this->described[$t]=true;foreach($H
as$Rf=>$G){if(isset($G[$t]))$F[$Rf][$t]=(string)$dc[$G[$t]];}}}return$F;}function
selectLink($W,array$k){}function
selectVal($W,$w,array$k,$vg){$F="$W";if(isset($this->described[$k["field"]])&&$vg!==null)$F=shorten_utf8($vg,max(0,+adminer()->selectLengthProcess()));$w=h($w);if(is_blob($k)&&!is_utf8($W)){$F=lang(48,strlen($vg));$si=(function_exists('getimagesizefromstring')?@getimagesizefromstring($vg):array());if($si)$F="<img src='$w' alt='$F' $si[3] loading='lazy'>";}if(like_bool($k)&&$F!="")$F=(preg_match('~^(1|t|true|y|yes|on)$~i',$W)?lang(49):lang(50));if($w)$F="<a href='$w'".(is_url($w)?target_blank():"").">$F</a>";if(preg_match('~date~',$k["type"]))$F="<div class='datetime'>$F</div>";return$F;}function
editVal($W,array$k){if(preg_match('~date|timestamp~',$k["type"])&&$W!==null)return
preg_replace('~^(\d{2}(\d+))-(0?(\d+))-(0?(\d+))~',lang(51),$W);return$W;}function
config(){return
array();}function
selectColumnsPrint(array$J,array$e){}private
function
searchColumns(array$l){$F=array();$Q=$_GET["select"];if($Q=="")return$F;$p=0;foreach($l
as$y=>$k){if(isset($k["privileges"]["where"])&&$this->fieldName($k)!=""&&($k["type"]=="enum"||like_bool($k)||is_array($this->foreignKeyOptions($Q,$y))))$F[--$p]=$y;}return$F;}function
selectSearchPrint(array$Z,array$e,array$qe,$Pi=null){$Z=(array)$_GET["where"];echo'<fieldset id="fieldset-search"><legend>'.lang(52)."</legend><div>\n";$l=fields($_GET["select"]);foreach($this->searchColumns($l)as$p=>$y){$k=$l[$y];$W=idx($Z[$p],"val");echo"<div>".h($e[$y]);if($k["type"]=="enum"||like_bool($k))echo": ",(like_bool($k)?"<select name='where[$p][val]' data-default=''>".optionlist(array(""=>"",lang(50),lang(49)),$W,true)."</select>":enum_input("checkbox"," name='where[$p][val][]'",$k,(array)$W,lang(53)));else{$_=$this->foreignKeyOptions($_GET["select"],$y);if($k["null"])$_[0]='('.lang(53).')';echo": <select name='where[$p][val]' data-default=''>".optionlist($_,$W,true)."</select>";}echo"</div>\n";unset($e[$y]);}$p=0;foreach($Z
as$t=>$W){if($t>=0&&($W["col"]==""||$e[$W["col"]])&&"$W[col]$W[val]"!=""){echo"<div><select name='where[$p][col]' data-default=''><option value=''>(".lang(54).")".optionlist($e,$W["col"],true)."</select>",html_select("where[$p][op]",array(-1=>"")+adminer()->operators($Pi),$W["op"]," data-default=''"),"<input type='search' name='where[$p][val]' value='".h($W["val"])."' data-default=''".on('keydown','selectSearchKeydown').on('search','selectSearchSearch')."></div>\n";$p++;}}echo"<div><select name='where[$p][col]' data-default=''".on('change','selectAddRow')."><option value=''>(".lang(54).")".optionlist($e,null,true)."</select>",html_select("where[$p][op]",array(-1=>"")+adminer()->operators($Pi),null," data-default=''"),"<input type='search' name='where[$p][val]' data-default=''".on('change','selectFirstChange').on('keydown','selectSearchKeydown').on('search','selectSearchSearch')."></div>\n","</div></fieldset>\n";}function
selectOrderPrint(array$og,array$e,array$qe){$qg=array();foreach($qe
as$t=>$s){$og=array();foreach($s["columns"]as$W)$og[]=$e[$W];if(count(array_filter($og,'strlen'))>1&&$t!="PRIMARY")$qg[$t]=implode(", ",$og);}if($qg)echo'<fieldset><legend>'.lang(55)."</legend><div>","<select name='index_order' data-default=''>".optionlist(array(""=>"")+$qg,(idx($_GET["order"],0)!=""?"":$_GET["index_order"]),true)."</select>","</div></fieldset>\n";if($_GET["order"])echo"<div hidden>".hidden_fields(array("order"=>array(1=>reset($_GET["order"])),"desc"=>($_GET["desc"]?array(1=>1):array()),))."</div>\n";}function
selectLimitPrint($v){echo"<fieldset><legend>".lang(56)."</legend><div>",html_select("limit",array("","50","100"),(string)$v," data-default='50'"),"</div></fieldset>\n";}function
selectLengthPrint($Zi){}function
selectActionPrint(array$qe){echo"<fieldset><legend>".lang(57)."</legend><div>","<input type='submit' value='".lang(58)."'>","</div></fieldset>\n";}function
selectCommandPrint(){return
true;}function
selectImportPrint(){return
true;}function
selectEmailPrint(array$Ac,array$e){}function
selectColumnsProcess(array$e,array$qe){return
array(array(),array());}function
selectSearchProcess(array$l,array$qe,$Pi=null){$F=array();$Rh=$this->searchColumns($l);if($_GET["select"]!=""&&!$_POST&&!is_ajax()){$Oe=array();$w="";foreach((array)$_GET["where"]as$t=>$Z){$Xg=($t>=0?array_search($Z["col"],$Rh,true):false);$k=idx($l,$Z["col"],array());if($Xg!==false&&$k["type"]!="enum"&&!is_array($Z["val"])&&$Z["val"]!=""&&$Z["op"]==(like_bool($k)?"":"=")){$Oe[]=$t;$w
.="&where[$Xg][val]=".url_escape($Z["val"]);}}if($Oe)redirect(remove_from_uri("where(%5B|\[)(".implode("|",$Oe).")(%5D|\])[^=]*").$w);}foreach((array)$_GET["where"]as$t=>$Z){if($t<0){$Z["col"]=idx($Rh,$t,"");$k=idx($l,$Z["col"],array());$Z["op"]=($k&&($k["type"]=="enum"||like_bool($k))?"":"=");$_GET["where"][$t]=$Z;}$Z+=array("col"=>"","op"=>"","val"=>"");$hb=$Z["col"];$jg=$Z["op"];$W=$Z["val"];if(($t>=0&&$hb!="")||$W!=""){$rb=array();foreach(($hb!=""?array($hb=>$l[$hb]):$l)as$y=>$k){if($hb!=""||is_searchable($k,$Z)){$y=idf_escape($y);if($hb!=""&&$k["type"]=="enum"){$me=array();foreach(preg_grep('~^val-~',$W)as$Wj)$me[]=q(substr($Wj,4));$rb[]=(in_array("null",$W)?"$y IS NULL OR ":"").($me?"$y IN (".implode(", ",$me).")":"0");}else{$aj=preg_match('~'.text_type().'~',$k["type"]);$X=q(!$jg&&$aj&&preg_match('~^[^%]+$~',$W)?"%$W%":$W);$rb[]=driver()->convertSearch($y,$Z,$k).($X=="NULL"?" IS".($jg==">="?" NOT":"")." $X":(in_array($jg,adminer()->operators($Pi))||$jg=="="?" $jg $X":($aj?" LIKE $X":" IN (".($X[0]=="'"?str_replace(",","', '",$X):$X).")")));if($t<0&&$W=="0")$rb[]="$y IS NULL";}}}$F[]=($rb?"(".implode(" OR ",$rb).")":"1 = 0");}}return$F;}function
selectOrderProcess(array$l,array$qe){$pe=$_GET["index_order"];if($pe!="")unset($_GET["order"][1]);if($_GET["order"])return
array(idf_escape(reset($_GET["order"])).($_GET["desc"]?" DESC":""));foreach(($pe!=""?array($qe[$pe]):$qe)as$s){if($pe!=""||$s["type"]=="INDEX"){$Qd=array_filter($s["descs"]);$ac=false;foreach($s["columns"]as$W){if(preg_match('~date|timestamp~',$l[$W]["type"])){$ac=true;break;}}$F=array();foreach($s["columns"]as$t=>$W)$F[]=idf_escape($W).(($Qd?$s["descs"][$t]:$ac)?" DESC":"");return$F;}}return
array();}function
selectLimitProcess(){return(isset($_GET["limit"])?intval($_GET["limit"]):50);}function
selectLengthProcess(){return"100";}function
selectEmailProcess(array$Z,array$td){return
false;}function
selectQueryBuild(array$J,array$Z,array$o,array$og,$v,$A){return"";}function
messageQuery($D,$bj,$Yc=false){return" <span class='time'>".@date("H:i:s")."</span>".sql_comment($D,$bj);}function
error(){return
error();}function
editRowPrint($Q,array$l,$G,$Kj,$D='',$bj=''){echo($D!=""?sql_comment($D,$bj):"");}function
editFunctions(array$k){$F=array();if($k["null"]&&preg_match('~blob~',$k["type"]))$F["NULL"]=lang(53);$F[""]=($k["null"]||$k["auto_increment"]||like_bool($k)?"":"*");if(preg_match('~date|time~',$k["type"]))$F["now"]=lang(59);if(preg_match('~_(md5|sha1)$~i',$k["field"],$x))$F[]=strtolower($x[1]);return$F;}function
editInput($Q,array$k,$c,$X){if($k["type"]=="enum")return(isset($_GET["select"])?"<label><input type='radio'$c value='orig' checked><i>".lang(12)."</i></label> ":"").enum_input("radio",$c,$k,$X,lang(53));$_=$this->foreignKeyOptions($Q,$k["field"],$X);if($_!==null){if(!$k["null"]&&is_array($_))unset($_[""]);return(is_array($_)?"<select$c>".optionlist($_,(string)$X,true)."</select>":"<input value='".h($X)."'$c class='hidden'>"."<input value='".h($_)."' class='jsonly'".on('input','whisper',ME."script=complete&source=".url_escape($Q)."&field=".url_escape($k["field"])."&value=").">"."<div".on('click','whisperClick')."></div>");}if(like_bool($k))return'<input type="checkbox" value="1"'.(preg_match('~^(1|t|true|y|yes|on)$~i',$X)?' checked':'')."$c>";$ce="";if(preg_match('~time~',$k["type"]))$ce=lang(60);if(preg_match('~date|timestamp~',$k["type"]))$ce=lang(61).($ce?" [$ce]":"");if($ce)return"<input value='".h($X)."'$c> ($ce)";if(preg_match('~_(md5|sha1)$~i',$k["field"]))return"<input type='password' value='".h($X)."'$c>";return'';}function
editHint($Q,array$k,$X){return(preg_match('~\s+(\[.*\])$~',($k["comment"]!=""?$k["comment"]:$k["field"]),$x)?h(" $x[1]"):'');}function
processInput(array$k,$X,$n=""){if($n=="now")return"$n()";$F=$X;if(preg_match('~date|timestamp~',$k["type"])&&preg_match('(^'.str_replace('\$1','(?P<p1>\d*)',preg_replace('~(\\\\\\$([2-6]))~','(?P<p\2>\d{1,2})',preg_quote(lang(51)))).'(.*))',$X,$x))$F=($x["p1"]!=""?$x["p1"]:($x["p2"]!=""?($x["p2"]<70?20:19).$x["p2"]:gmdate("Y")))."-$x[p3]$x[p4]-$x[p5]$x[p6]".end($x);$F=q($F);if($X==""&&like_bool($k))$F="'0'";elseif($X==""&&($k["null"]||!preg_match('~char|text~',$k["type"])))$F="NULL";elseif(preg_match('~^(md5|sha1)$~',$n))$F="$n($F)";return
unconvert_field($k,$F);}function
dumpOutput(){return
array();}function
dumpFormat(){return
array('csv'=>'CSV,','csv;'=>'CSV;','tsv'=>'TSV');}function
dumpDatabase($h){}function
dumpTable($Q,$Ii,$Je=0){echo"\xef\xbb\xbf";}function
dumpData($Q,$Ii,$D){$E=connection()->query($D,1);if($E){while($G=$E->fetch_assoc()){if($Ii=="table"){dump_csv(array_keys($G));$Ii="INSERT";}dump_csv($G);}}}function
dumpFilename($ie){return
friendly_url($ie);}function
dumpHeaders($ie,$Pf=false){$Uc="csv";header("Content-Type: text/csv; charset=utf-8");return$Uc;}function
dumpFooter(){}function
importServerPath(){return'';}function
homepage(){return
true;}function
navigation($Jf){echo"<h1>".adminer()->name()." <span class='version'>".VERSION;$Vf=$_COOKIE["adminer_version"];echo" <a href='https://www.adminer.org/editor/#download'".target_blank()." id='version'>".(version_compare(VERSION,$Vf)<0?h($Vf):"").version_iframe()."</a>","</span></h1>\n";switch_lang();if($Jf=="auth"){$jd=true;foreach((array)$_SESSION["pwds"]as$Zj=>$ni){foreach($ni[""]as$U=>$B){if($B!==null){if($jd){echo"<ul id='logins'".on('mouseover','menuOver').on('mouseout','menuOut').">";$jd=false;}echo"<li><a href='".h(auth_url($Zj,"",$U))."'>".($U!=""?h($U):"<i>".lang(53)."</i>")."</a>\n";}}}}else{adminer()->databasesPrint($Jf);$fa=adminer()->menuActions(array(),$Jf);echo($fa?"<p class='links'>\n".implode("\n",$fa)."\n":"");if($Jf!="db"&&$Jf!="ns"){$R=table_status('',true);if(!$R)echo"<p class='message'>".lang(13)."\n";else
adminer()->tablesPrint($R);}}}function
syntaxHighlighting(array$S){}function
databasesPrint($Jf){}function
menuActions(array$fa,$Jf){return$fa;}function
tablesPrint(array$S){echo"<ul id='tables'".on('mouseover','menuOver').on('mouseout','menuOut').">";foreach($S
as$G){echo'<li>';$y=adminer()->tableName($G);if($y!="")echo"<a href='".h(ME).'select='.url_escape($G["Name"])."'".bold($_GET["select"]==$G["Name"]||$_GET["edit"]==$G["Name"],"select")." title='".lang(62)."'>$y</a>\n";}echo"</ul>\n";}function
_foreignColumn(array$td,$d){foreach((array)$td[$d]as$sd){if(count($sd["source"])==1){$y=adminer()->rowDescription($sd["table"]);if($y!=""){$q=idf_escape($sd["target"][0]);return
array($sd["table"],$q,$y);}}}}private
function
foreignKeyOptions($Q,$d,$X=null){if(list($Vi,$q,$y)=$this->_foreignColumn(column_foreign_keys($Q),$d)){$F=&$this->values[$Vi];if($F===null){$R=table_status1($Vi);$F=($R["Rows"]>1000?"":array(""=>"")+get_key_vals("SELECT $q, $y FROM ".table($Vi)." ORDER BY 2"));}if(!$F&&$X!==null)return
get_val("SELECT $y FROM ".table($Vi)." WHERE $q = ".q($X));return$F;}}}class
Plugins{private
static$append=array('dumpFormat'=>true,'dumpOutput'=>true,'editRowPrint'=>true,'editFunctions'=>true,'config'=>true);var$plugins;var$disabled=array();var$drivers=array();var$driverFiles=array();var$error='';private$hooks=array();function
__construct($Vg){$qc=SqlDriver::$drivers;$Zd=" href='https://www.adminer.org/plugins/#use'".target_blank();if($Vg===null){$Vg=array();$Ja="adminer-plugins";if(is_dir($Ja)){foreach(glob("$Ja/*.php")as$m){$ed=SqlDriver::$drivers;$this->includeOnce($m);foreach(array_diff_key(SqlDriver::$drivers,$ed)as$q=>$y)$this->driverFiles[$q]=$m;}}if(file_exists("$Ja.php")){$ne=$this->includeOnce("$Ja.php");if(is_array($ne)){foreach($ne
as$t=>$Tg)$Vg[is_object($Tg)?get_class($Tg):$t]=$Tg;}else$this->error
.=lang(63,"<b>$Ja.php</b>",$Zd)."<br>";}foreach(get_declared_classes()as$db){if(!$Vg[$db]&&(preg_match('~^Adminer\w~i',$db)||is_subclass_of($db,'Adminer\Plugin'))){$xh=new
\ReflectionClass($db);$zb=$xh->getConstructor();if($zb&&$zb->getNumberOfRequiredParameters())$this->error
.=lang(64,$Zd,"<b>$db</b>","<b>$Ja.php</b>")."<br>";else$Vg[$db]=new$db;}}}$Be=array_filter($Vg,function($Tg){return!is_object($Tg);});if($Be){$this->error
.=lang(65,$Zd)."<br>";$Vg=array_diff_key($Vg,$Be);}$Li=get_setting("plugins","adminer_config");foreach($Vg
as$t=>$Tg){if($Tg
instanceof
Plugin&&$Tg::$optional){$Ei=idx($Li,get_class($Tg));if($Ei!==null?!$Ei:!$Tg->enabled){$this->disabled[]=$Tg;unset($Vg[$t]);}}}$this->drivers=array_diff_key(SqlDriver::$drivers,$qc);$this->plugins=$Vg;$ka=new
Adminer;$Vg[]=$ka;$xh=new
\ReflectionObject($ka);foreach($xh->getMethods()as$If){foreach($Vg
as$Tg){$y=$If->getName();if(method_exists($Tg,$y))$this->hooks[$y][]=$Tg;}}}function
includeOnce($m){return
include_once"./$m";}static
function
describe($Tg){$cc=(method_exists($Tg,'description')?$Tg->description():"");if(!$cc){$xh=new
\ReflectionObject($Tg);if(preg_match('~^/[\s*]+(.+?)(?:\s*\*/)?$~m',$xh->getDocComment(),$x))$cc=$x[1];}$Ph=(method_exists($Tg,'screenshot')?$Tg->screenshot():"");return
h($cc).($Ph?($cc?" ":"")."(<a href='".h($Ph)."'".target_blank().">".lang(66)."</a>)":"");}static
function
checksum($m){$dd=str_replace("\r","",file_get_contents($m));$dd=preg_replace('~\n\tprotected \$translations = array\(.*?\n\t\);~s','',$dd);return
dechex(crc32($dd));}function
checksums(){$fd=array_values($this->driverFiles);foreach($this->plugins
as$Tg){$xh=new
\ReflectionObject($Tg);$fd[]=$xh->getFileName();}$F=array();foreach($fd
as$m)$F[basename($m,'.php')]=self::checksum($m);return$F;}static
function
officialChecksums(){return
array('adminer.js'=>'a0599090','backward-keys'=>'1c017d59','before-unload'=>'9674d220','config'=>'bdac2201','dark-switcher'=>'8c201319','database-hide'=>'e304a899','designs'=>'5903cccc','dump-alter'=>'896b579e','dump-bz2'=>'c9229da6','dump-date'=>'512aad63','dump-json'=>'f8aa6b99','dump-xml'=>'aedd2d3a','dump-zip'=>'735d7e2','edit-foreign'=>'d04a0ac8','edit-textarea'=>'a178d7d9','editor-setup'=>'a7dc3a37','editor-views'=>'5c12b185','enum-option'=>'e9005cb0','file-upload'=>'10add0e8','foreign-system'=>'634ce119','frames'=>'b0e1d11a','highlight-codemirror'=>'ada4b569','highlight-monaco'=>'ec1da606','highlight-prism'=>'19933b19','import-csv'=>'bcecfad2','login-ip'=>'4d174fea','login-otp'=>'66bd013d','login-passkey'=>'89783b50','login-password-less'=>'e150daac','login-reverse-proxy'=>'24558ea2','login-servers'=>'19c42e45','login-ssl'=>'2b06a5b7','login-table'=>'811f8cef','menu-links'=>'55b9b15c','name-patterns'=>'84c10d09','remote-color'=>'267580a3','row-numbers'=>'1ad1b6cf','select-email'=>'c33a2661','select-expression'=>'b6a4b3f1','select-foreign'=>'577c07b2','select-image'=>'39ed1607','slugify'=>'7b422f0e','sql-gemini'=>'c60ab309','sql-log'=>'8e435000','sql-url-length'=>'cd70f1a0','table-indexes-structure'=>'e0b1433','table-structure'=>'f71ec61b','tables-filter'=>'2729bf7','timeout'=>'97321caf','version-github'=>'627cadf9','version-noverify'=>'966937e9','clickhouse'=>'51a3d7d5','elastic'=>'9b83a12a','firebird'=>'5f3d9493','igdb'=>'3b00190b','imap'=>'3ba195cd','mongo'=>'235e3ba','redis'=>'41709f61','simpledb'=>'b8e2cc7d',);}function
__call($y,array$Dg){$ua=array();foreach($Dg
as$t=>$W)$ua[]=&$Dg[$t];$F=null;foreach($this->hooks[$y]as$Tg){$X=call_user_func_array(array($Tg,$y),$ua);if($X!==null){if(!self::$append[$y])return$X;$F=$X+(array)$F;}}return$F;}}abstract
class
Plugin{static$optional=false;var$enabled=true;protected$translations=array();function
disable(){$this->enabled=false;return$this;}function
description(){return$this->lang('');}function
screenshot(){return"";}protected
function
lang($r,$z=null){$ua=func_get_args();$ua[0]=idx($this->translations[LANG],$r)?:$r;return
call_user_func_array('Adminer\lang_format',$ua);}}class
Password{private$password_hash;private$password_matches=null;function
__construct($Lg){$this->password_hash=$Lg;}function
description(){return
lang(67);}function
credentials(){$B=get_password();return
array(SERVER,$_GET["username"],($this->passwordMatches($B)&&!password_required()?"":$B));}function
login($mf,$B){if($this->passwordMatches($B))return
true;if(!Driver::$passwords||!password_required())return
false;}protected
function
passwordMatches($B){if($this->password_matches===null)$this->password_matches=(function_exists('password_verify')&&password_verify(strval($B),$this->password_hash));return$this->password_matches;}}Adminer::$instance=(function_exists('adminer_object')?adminer_object():(is_dir("adminer-plugins")||file_exists("adminer-plugins.php")?new
Plugins(null):new
Adminer));SqlDriver::$drivers=array("server"=>"MySQL / MariaDB")+SqlDriver::$drivers;if(!defined('Adminer\DRIVER')){define('Adminer\DRIVER',"server");if(extension_loaded("mysqli")&&$_GET["ext"]!="pdo"){class
Db
extends
\mysqli{static$instance;var$extension="MySQLi",$flavor='';function
__construct(){parent::init();}function
attach(array$L,$U,$B){mysqli_report(MYSQLI_REPORT_OFF);$Wg=$L["port"];$Cc=("$L[host]$Wg$L[socket]"=="");$N=adminer()->connectSsl();$Rj=($N&&($N['key']||$N['cert']||$N['ca']||isset($N['verify'])));if($Rj)$this->ssl_set($N['key'],$N['cert'],$N['ca'],'','');$F=@$this->real_connect((!$Cc?$L["host"]:ini_get("mysqli.default_host")),(!$Cc||$U!=""?$U:ini_get("mysqli.default_user")),(!$Cc||$U.$B!=""?$B:ini_get("mysqli.default_pw")),null,($Wg!=""?intval($Wg):ini_get("mysqli.default_port")),($Wg!=""?null:$L["socket"]),($Rj?($N['verify']!==false?MYSQLI_CLIENT_SSL:64):0));$this->options(MYSQLI_OPT_LOCAL_INFILE,0);return($F?'':$this->error);}function
set_charset($Wa){if(parent::set_charset($Wa))return
true;parent::set_charset('utf8');return$this->query("SET NAMES $Wa");}function
next_result(){return
self::more_results()&&parent::next_result();}function
quote($P){return"'".$this->escape_string($P)."'";}function
inTransaction(){return
false;}function
begin(){return$this->begin_transaction();}}}elseif(extension_loaded("mysql")&&!((ini_bool("sql.safe_mode")||ini_bool("mysql.allow_local_infile"))&&extension_loaded("pdo_mysql"))){class
Db
extends
SqlDb{private$link;function
attach(array$L,$U,$B){if(ini_bool("mysql.allow_local_infile"))return
lang(68,"'mysql.allow_local_infile'","MySQLi","PDO_MySQL");$Wg="$L[port]$L[socket]";$y=$L["host"].($Wg!=""?":$Wg":"");$this->link=@mysql_connect(($y!=""?$y:ini_get("mysql.default_host")),($y.$U!=""?$U:ini_get("mysql.default_user")),($y.$U.$B!=""?$B:ini_get("mysql.default_password")),true,131072);if(!$this->link)return
mysql_error();$this->server_info=mysql_get_server_info($this->link);return'';}function
set_charset($Wa){return
mysql_set_charset($Wa,$this->link)||mysql_set_charset('utf8',$this->link);}function
quote($P){return"'".mysql_real_escape_string($P,$this->link)."'";}function
select_db($Qb){return
mysql_select_db($Qb,$this->link);}function
query($D,$Aj=false){$E=@($Aj?mysql_unbuffered_query($D,$this->link):mysql_query($D,$this->link));$this->error="";if(!$E){$this->errno=mysql_errno($this->link);$this->error=mysql_error($this->link);return
false;}if($E===true){$this->affected_rows=mysql_affected_rows($this->link);$this->info=mysql_info($this->link);return
true;}return
new
Result($E);}}class
Result{var$num_rows;private$result;private$offset=0;function
__construct($E){$this->result=$E;$this->num_rows=mysql_num_rows($E);}function
fetch_assoc(){return
mysql_fetch_assoc($this->result);}function
fetch_row(){return
mysql_fetch_row($this->result);}function
fetch_field(){$F=mysql_fetch_field($this->result,$this->offset++);$F->orgtable=$F->table;$F->native_type=idx(array("string"=>"varchar","real"=>"double"),$F->type,$F->type);return$F;}}}elseif(extension_loaded("pdo_mysql")){class
Db
extends
PdoDb{var$extension="PDO_MySQL";function
attach(array$L,$U,$B){$_=array(\PDO::MYSQL_ATTR_LOCAL_INFILE=>false);if(isset($_GET["select"]))$_[\PDO::MYSQL_ATTR_MULTI_STATEMENTS]=false;$N=adminer()->connectSsl();if($N){if($N['key'])$_[\PDO::MYSQL_ATTR_SSL_KEY]=$N['key'];if($N['cert'])$_[\PDO::MYSQL_ATTR_SSL_CERT]=$N['cert'];if($N['ca'])$_[\PDO::MYSQL_ATTR_SSL_CA]=$N['ca'];if(isset($N['verify']))$_[\PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT]=$N['verify'];}$ee=$L["host"];$Wg=$L["port"];$vi=$L["socket"];return$this->dsn("mysql:charset=utf8".($ee!=""?";host=$ee":'').($Wg!=""?";port=$Wg":($vi!=""?";unix_socket=$vi":"")),$U,$B,$_);}function
set_charset($Wa){return$this->query("SET NAMES $Wa");}function
select_db($Qb){return$this->query("USE ".idf_escape($Qb));}function
query($D,$Aj=false){$this->pdo->setAttribute(\PDO::MYSQL_ATTR_USE_BUFFERED_QUERY,!$Aj);return
parent::query($D,$Aj);}}}class
Driver
extends
SqlDriver{static$extensions=array("MySQLi","MySQL","PDO_MySQL");static$jush="sql";static$serverSocket=true;var$unsigned=array("unsigned","zerofill","unsigned zerofill");var$functions=array("char_length","date","from_unixtime","lower","round","floor","ceil","sec_to_time","time_to_sec","upper");var$grouping=array("avg","count","count distinct","group_concat","max","min","sum");var$partitionBy=array("HASH","LINEAR HASH","KEY","LINEAR KEY","RANGE","LIST");function
operators($Pi){return
array("=","<",">","<=",">=","!=","&","LIKE","LIKE %%","REGEXP","IN","BETWEEN","FIND_IN_SET","IS NULL","NOT LIKE","NOT REGEXP","NOT IN","IS NOT NULL","SQL");}static
function
connect($L,$U,$B){$f=parent::connect($L,$U,$B);if(is_string($f)){if(function_exists('iconv')&&!is_utf8($f)&&strlen($Mh=iconv("windows-1252","utf-8//IGNORE",$f))>strlen($f))$f=$Mh;return$f;}$f->set_charset(charset($f));$f->query("SET sql_quote_show_create = 1, autocommit = 1");$f->flavor=(preg_match('~MariaDB~',$f->server_info)?'maria':'mysql');add_driver(DRIVER,($f->flavor=='maria'?"MariaDB":"MySQL"));return$f;}function
__construct(Db$f){parent::__construct($f);$this->types=array(lang(29)=>array("tinyint"=>3,"smallint"=>5,"mediumint"=>8,"int"=>10,"bigint"=>20,"decimal"=>66,"float"=>12,"double"=>21),lang(30)=>array("date"=>10,"datetime"=>19,"timestamp"=>19,"time"=>10,"year"=>4),lang(31)=>array("char"=>255,"varchar"=>65535,"tinytext"=>255,"text"=>65535,"mediumtext"=>16777215,"longtext"=>4294967295),lang(69)=>array("enum"=>65535,"set"=>64),lang(32)=>array("bit"=>20,"binary"=>255,"varbinary"=>65535,"tinyblob"=>255,"blob"=>65535,"mediumblob"=>16777215,"longblob"=>4294967295),lang(34)=>array("geometry"=>0,"point"=>0,"linestring"=>0,"polygon"=>0,"multipoint"=>0,"multilinestring"=>0,"multipolygon"=>0,"geometrycollection"=>0),);$this->insertFunctions=array("char"=>"md5/sha1/password/encrypt/uuid","binary"=>"md5/sha1","date|time"=>"now",);$this->editFunctions=array(number_type()=>"+/-","date"=>"+ interval/- interval","time"=>"addtime/subtime","char|text"=>"concat",);if(min_version('5.7.8',10.2,$f))$this->types[lang(31)]["json"]=4294967295;if(min_version('',10.7,$f)){$this->types[lang(31)]["uuid"]=128;$this->insertFunctions['uuid']='uuid';}if(min_version('',10.5,$f)){$this->types[lang(33)]["inet6"]=39;if(min_version('','10.10',$f))$this->types[lang(33)]["inet4"]=15;}if(min_version(9,11.7,$f))$this->types[lang(29)]["vector"]=16383;if(min_version(5.7,10.2,$f))$this->generated=array("STORED","VIRTUAL");}function
unconvertFunction(array$k){return(preg_match("~binary~",$k["type"])?"<code class='jush-sql'>UNHEX</code>":($k["type"]=="bit"?doc_link(array('sql'=>'bit-value-literals.html'),"<code>b''</code>"):($k["type"]=="vector"?"<code class='jush-sql'>".($this->conn->flavor=='maria'?"VEC_FromText":"STRING_TO_VECTOR")."</code>":(preg_match("~geom|point|linestring|polygon~",$k["type"])?"<code class='jush-sql'>GeomFromText</code>":""))));}function
insert($Q,array$M){return($M?parent::insert($Q,$M):queries("INSERT INTO ".table($Q)." ()\nVALUES ()"));}function
insertUpdate($Q,array$H,array$fh){$e=array_keys(reset($H));$ch="INSERT INTO ".table($Q)." (".implode(", ",$e).") VALUES\n";$Y=array();foreach($e
as$t)$Y[$t]="$t = VALUES($t)";$Ji="\nON DUPLICATE KEY UPDATE ".implode(", ",$Y);$Y=array();$u=0;foreach($H
as$M){$X="(".implode(", ",$M).")";if($Y&&(strlen($ch)+$u+strlen($X)+strlen($Ji)>1e6)){if(!queries($ch.implode(",\n",$Y).$Ji))return
false;$Y=array();$u=0;}$Y[]=$X;$u+=strlen($X)+2;}return
queries($ch.implode(",\n",$Y).$Ji);}function
slowQuery($D,$cj){if(min_version('5.7.8','10.1.2')){if($this->conn->flavor=='maria')return"SET STATEMENT max_statement_time=$cj FOR $D";elseif(preg_match('~^(SELECT\b)(.+)~is',$D,$x))return"$x[1] /*+ MAX_EXECUTION_TIME(".($cj*1000).") */ $x[2]";}}function
convertColumn($r,array$k){if(preg_match("~binary~",$k["type"]))return"HEX($r)";if($k["type"]=="bit")return"BIN($r + 0)";if($k["type"]=="vector")return($this->conn->flavor=='maria'?"VEC_ToText":"VECTOR_TO_STRING")."($r)";if(preg_match("~geom|point|linestring|polygon~",$k["type"]))return(min_version(8)?"ST_":"")."AsWKT($r)";return"";}function
convertSearch($r,array$W,array$k){return($this->convertColumn($r,$k)?:(preg_match('~'.text_type().'~',$k["type"])&&!preg_match("~^utf8~",$k["collation"])&&preg_match('~[\x80-\xFF]~',$W['val'])?"CONVERT($r USING ".charset($this->conn).")":$r));}function
typeName(\stdClass$k){$y=parent::typeName($k);if($y!=""){$_j=array("TINY"=>"tinyint","SHORT"=>"smallint","LONG"=>"int","INT24"=>"mediumint","LONGLONG"=>"bigint","NEWDECIMAL"=>"decimal","VAR_STRING"=>"varchar","STRING"=>"char",);return
idx($_j,$y,strtolower($y));}$_j=array("decimal","tinyint","smallint","int","float","double",7=>"timestamp","bigint","mediumint","date","time","datetime","year",15=>"varchar","bit",242=>"vector",245=>"json","decimal","enum","set","tinytext","mediumtext","longtext","text","varchar","char","geometry",);$F=idx($_j,$k->type,"");return($k->charsetnr==63?str_replace(array("text","varchar","char"),array("blob","varbinary","binary"),$F):$F);}function
quoteBinary($Mh){return"X".q(bin2hex($Mh));}function
md5($d,array$k){if(is_blob($k)||preg_match('~'.text_type().'~',$k["type"]))return"MD5(".(is_blob($k)||preg_match("~^utf8~",$k["collation"])?$d:"CONVERT($d USING ".charset($this->conn).")").")";}function
warnings(){$E=$this->conn->query("SHOW WARNINGS");if($E&&$E->num_rows){ob_start();print_select_result($E);return
ob_get_clean();}}function
errorLine($D){return(preg_match('~ at line (\d+)$~',$this->conn->error,$x)?(int)$x[1]:0);}function
tableHelp($y,$Je=false){$of=($this->conn->flavor=='maria');if(information_schema(DB))return
strtolower(str_replace("_","-",DB)."-".($of?"$y-table/":str_replace("_","-",$y)."-table.html"));if(DB=="sys")return($of?"sys-schema/":strtolower("sys-".str_replace("_","-",preg_replace('~^x\$~','',$y)).".html"));if(DB=="mysql")return($of?"mysql$y-table/":"system-schema.html");}function
partitionsInfo($Q){$Bd="FROM information_schema.PARTITIONS WHERE TABLE_SCHEMA = ".q(DB)." AND TABLE_NAME = ".q($Q);$E=$this->conn->query("SELECT PARTITION_METHOD, PARTITION_EXPRESSION, PARTITION_ORDINAL_POSITION $Bd ORDER BY PARTITION_ORDINAL_POSITION DESC LIMIT 1");$G=($E?$E->fetch_row():null);if(!$G)return
array();$F=array();list($F["partition_by"],$F["partition"],$F["partitions"])=$G;$Jg=get_key_vals("SELECT PARTITION_NAME, PARTITION_DESCRIPTION $Bd AND PARTITION_NAME != '' ORDER BY PARTITION_ORDINAL_POSITION");$F["partition_names"]=array_keys($Jg);$F["partition_values"]=array_values($Jg);return$F;}function
checkConstraints($Q){$F=parent::checkConstraints($Q);return($this->conn->flavor=='maria'?$F:array_map('stripslashes',$F));}function
hasCStyleEscapes(){static$Sa;if($Sa===null){$Bi=get_val("SHOW VARIABLES LIKE 'sql_mode'",1,$this->conn);$Sa=(strpos($Bi,'NO_BACKSLASH_ESCAPES')===false);}return$Sa;}function
hasEstimatedRows(){return
true;}function
isSystem($h,$I=""){return
information_schema($h,$I)||in_array($h,array("mysql","sys"));}function
lineComment(){return"#|-- ";}function
engines(){$F=array();foreach(get_rows("SHOW ENGINES")as$G){if(preg_match("~YES|DEFAULT~",$G["Support"]))$F[]=$G["Engine"];}return$F;}function
indexAlgorithms(array$Pi){return(preg_match('~^(MEMORY|NDB)$~',$Pi["Engine"])?array("HASH","BTREE"):array());}}function
idf_escape($r){return"`".str_replace("`","``",$r)."`";}function
table($r){return
idf_escape($r);}function
get_databases($pd){$F=get_session("dbs");if($F===null){$D="SELECT SCHEMA_NAME FROM information_schema.SCHEMATA ORDER BY SCHEMA_NAME";$Di=microtime(true);$F=($pd?slow_query($D):get_vals($D));if(microtime(true)-$Di>0.1){restart_session();set_session("dbs",$F);stop_session();}}return$F;}function
limit($D,$Z,$v,$eg=0,$K=" "){return" $D$Z".($v?$K."LIMIT $v".($eg?" OFFSET $eg":""):"");}function
limit1($Q,$D,$Z,$K="\n"){return
limit($D,$Z,1,0,$K);}function
db_collation($h,array$jb){$F=null;$Hb=get_val("SHOW CREATE DATABASE ".idf_escape($h),1);if(preg_match('~ COLLATE ([^ ]+)~',$Hb,$x))$F=$x[1];elseif(preg_match('~ CHARACTER SET ([^ ]+)~',$Hb,$x))$F=$jb[$x[1]][-1];return$F;}function
logged_user(){return
get_val("SELECT CURRENT_USER()");}function
tables_list(){return
get_key_vals("SELECT TABLE_NAME, TABLE_TYPE FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() ORDER BY TABLE_NAME");}function
count_tables(array$Rb){$F=array();foreach($Rb
as$h)$F[$h]=count(get_vals("SHOW TABLES IN ".idf_escape($h)));return$F;}function
table_status($y="",$Zc=false){$F=array();$D="SELECT ENGINE AS Engine, TABLE_NAME AS Name, TABLE_COMMENT AS Comment FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() ".($y!=""?"AND TABLE_NAME = ".q($y):"ORDER BY Name");$I=array();foreach(($Zc?array():get_rows($D))as$G)$I[$G["Name"]]=$G;$eh=null;foreach(get_rows($Zc?$D:"SHOW TABLE STATUS".($y!=""?" LIKE ".q(addcslashes($y,"%_\\")):""))as$G){$vg=idx($I,$G["Name"]);if($vg){if($G["Comment"]!==$vg["Comment"]&&$G["Comment"]!==$eh)$G["Error"]=$G["Comment"];$eh=$G["Comment"];$G["Comment"]=$vg["Comment"];$G["Engine"]=$vg["Engine"];}if($G["Engine"]=="InnoDB")$G["Comment"]=preg_replace('~(?:(.+); )?InnoDB free: .*~','\1',$G["Comment"]);if(!isset($G["Engine"]))$G["Comment"]="";if($y!="")$G["Name"]=$y;$F[$G["Name"]]=$G;}return$F;}function
is_view(array$R){return$R["Engine"]===null;}function
fk_support(array$R){return
preg_match('~InnoDB|IBMDB2I'.(min_version(5.6)?'|NDB':'').'~i',$R["Engine"]);}function
parse_type($Dd){preg_match('~^([^( ]+)(?:\((.+)\))?( unsigned)?( zerofill)?$~',$Dd,$x);return
array($x[1],$x[2],ltrim($x[3].$x[4]));}function
fields($Q){$of=(connection()->flavor=='maria');$F=array();foreach(get_rows("SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ".q($Q)." ORDER BY ORDINAL_POSITION")as$G){$k=$G["COLUMN_NAME"];$T=$G["COLUMN_TYPE"];$Id=$G["GENERATION_EXPRESSION"];$Xc=$G["EXTRA"];preg_match('~^(VIRTUAL|PERSISTENT|STORED)~',$Xc,$Hd);list($zj,$u,$Ij)=parse_type($T);$i=$G["COLUMN_DEFAULT"];$Ub=false;if($i!=""){$sh=$of;if($of)$Ub=!preg_match("~^('(?:[^'\\\\]|\\\\.|'')*'|NULL|[-+]?[\\d.]+)$~s",$i);elseif(preg_match('~text|json~',$zj)||preg_match('~\bDEFAULT_GENERATED\b~',$Xc)){$i=stripslashes($i);$sh=preg_match("~^(_\\w+)?('(?:[^'\\\\]|\\\\.|'')*')$~s",$i,$x);if($sh)$i=$x[2];else$Ub=true;}elseif(preg_match("~^[bx]'[\\da-f]*'$~i",$i))$Ub=true;if($sh)$i=($i=="NULL"?null:(preg_match("~^'.*'$~s",$i)?stripslashes(idf_unescape($i)):$i));if(preg_match('~binary~',$zj)&&preg_match("~^(?:0x(\\w*)|x'(\\w*)')$~",$i,$x)){$i=pack("H*",$x[1].idx($x,2));$Ub=false;}}$F[$k]=array("field"=>$k,"full_type"=>$T,"type"=>$zj,"length"=>$u,"unsigned"=>$Ij,"default"=>($Hd?($of?$Id:stripslashes($Id)):$i),"default_sql"=>(!$Hd&&$Ub),"null"=>($G["IS_NULLABLE"]=="YES"),"auto_increment"=>($Xc=="auto_increment"),"on_update"=>(preg_match('~\bon update current_timestamp\b~i',$Xc)?"CURRENT_TIMESTAMP":""),"collation"=>$G["COLLATION_NAME"],"privileges"=>array_flip(explode(",","$G[PRIVILEGES],where,order")),"comment"=>$G["COLUMN_COMMENT"],"primary"=>($G["COLUMN_KEY"]=="PRI"),"generated"=>($Hd[1]=="PERSISTENT"?"STORED":$Hd[1]),);}return$F;}function
indexes($Q,$g=null){$F=array();foreach(get_rows("SHOW INDEX FROM ".table($Q),$g)as$G){$y=$G["Key_name"];$F[$y]["type"]=($y=="PRIMARY"?"PRIMARY":($G["Index_type"]=="FULLTEXT"?"FULLTEXT":($G["Non_unique"]?(preg_match('~^(SPATIAL|VECTOR)$~',$G["Index_type"])?$G["Index_type"]:"INDEX"):"UNIQUE")));$F[$y]["columns"][]=$G["Column_name"];$F[$y]["lengths"][]=($G["Index_type"]=="SPATIAL"?null:$G["Sub_part"]);$F[$y]["descs"][]=null;$F[$y]["algorithm"]=$G["Index_type"];}return$F;}function
foreign_keys($Q){static$Pg='(?:`(?:[^`]|``)+`|"(?:[^"]|"")+")';$F=array();$Ib=get_val("SHOW CREATE TABLE ".table($Q),1);if($Ib){preg_match_all("~CONSTRAINT ($Pg) FOREIGN KEY ?\\(((?:$Pg,? ?)+)\\) REFERENCES ($Pg)(?:\\.($Pg))? \\(((?:$Pg,? ?)+)\\)(?: ON DELETE (".driver()->onActions."))?(?: ON UPDATE (".driver()->onActions."))?~",$Ib,$rf,PREG_SET_ORDER);foreach($rf
as$x){preg_match_all("~$Pg~",$x[2],$zi);preg_match_all("~$Pg~",$x[5],$Vi);$F[idf_unescape($x[1])]=array("db"=>idf_unescape($x[4]!=""?$x[3]:$x[4]),"table"=>idf_unescape($x[4]!=""?$x[4]:$x[3]),"source"=>array_map('Adminer\idf_unescape',$zi[0]),"target"=>array_map('Adminer\idf_unescape',$Vi[0]),"on_delete"=>($x[6]?:"RESTRICT"),"on_update"=>($x[7]?:"RESTRICT"),);}}return$F;}function
view($y){return
array("select"=>preg_replace('~^(?:[^`]|`[^`]*`)*\s+AS\s+~isU','',get_val("SHOW CREATE VIEW ".table($y),1)));}function
collations(){$F=array();foreach(get_rows("SHOW COLLATION")as$G){if($G["Default"])$F[$G["Charset"]][-1]=$G["Collation"];else$F[$G["Charset"]][]=$G["Collation"];}ksort($F);foreach($F
as$t=>$W)sort($F[$t]);return$F;}function
information_schema($h,$I=""){return($h=="information_schema")||(min_version(5.5)&&$h=="performance_schema");}function
error(){return
h(preg_replace('~^You have an error.*syntax to use~U',"Syntax error",connection()->error));}function
create_database($h,$ib){return
queries("CREATE DATABASE ".idf_escape($h).($ib?" COLLATE ".q($ib):""));}function
drop_databases(array$Rb){$F=apply_queries("DROP DATABASE",$Rb,'Adminer\idf_escape');restart_session();set_session("dbs",null);return$F;}function
rename_database($y,$ib){$F=false;if(create_database($y,$ib)){$S=array();$ck=array();foreach(tables_list()as$Q=>$T){if($T=='VIEW')$ck[]=$Q;else$S[]=$Q;}$F=(!$S&&!$ck)||move_tables($S,$ck,$y);drop_databases($F?array(DB):array());}return$F;}function
auto_increment(){$Ba=" PRIMARY KEY";if($_GET["create"]!=""&&$_POST["auto_increment_col"]){foreach(indexes($_GET["create"])as$s){if(in_array($_POST["fields"][$_POST["auto_increment_col"]]["orig"],$s["columns"],true)){$Ba="";break;}if($s["type"]=="PRIMARY")$Ba=" UNIQUE";}}return" AUTO_INCREMENT$Ba";}function
alter_table($Q,$y,array$l,array$rd,$mb,$Fc,$ib,$Aa,$Ig){$b=array();foreach($l
as$k){if($k[1]){$i=$k[1][3];if(preg_match('~ GENERATED~',$i)){$k[1][3]=(connection()->flavor=='maria'?"":$k[1][2]);$k[1][2]=$i;}$b[]=($Q!=""?($k[0]!=""?"CHANGE ".idf_escape($k[0]):"ADD"):" ")." ".implode($k[1]).($Q!=""?$k[2]:"");}else$b[]="DROP ".idf_escape($k[0]);}$b=array_merge($b,$rd);$O=($mb!==null?" COMMENT=".q($mb):"").($Fc?" ENGINE=".q($Fc):"").($ib?" COLLATE ".q($ib):"").($Aa!=""?" AUTO_INCREMENT=$Aa":"");if($Ig){$Jg=array();if($Ig["partition_by"]=='RANGE'||$Ig["partition_by"]=='LIST'){foreach($Ig["partition_names"]as$t=>$W){$X=$Ig["partition_values"][$t];$Jg[]="\n  PARTITION ".idf_escape($W)." VALUES ".($Ig["partition_by"]=='RANGE'?"LESS THAN":"IN").($X!=""?" ($X)":" MAXVALUE");}}$O
.="\nPARTITION BY $Ig[partition_by]($Ig[partition])";if($Jg)$O
.=" (".implode(",",$Jg)."\n)";elseif($Ig["partitions"])$O
.=" PARTITIONS ".(+$Ig["partitions"]);}elseif($Ig===null)$O
.="\nREMOVE PARTITIONING";if($Q=="")return
queries("CREATE TABLE ".table($y)." (\n".implode(",\n",$b)."\n)$O");if($Q!=$y)$b[]="RENAME TO ".table($y);if($O)$b[]=ltrim($O);return($b?queries("ALTER TABLE ".table($Q)."\n".implode(",\n",$b)):true);}function
alter_indexes($Q,$b){$Ua=array();foreach($b
as$W)$Ua[]=($W[2]=="DROP"?"\nDROP INDEX ".idf_escape($W[1]):"\nADD $W[0] ".($W[0]=="PRIMARY"?"KEY ":"").($W[1]!=""?idf_escape($W[1])." ":"")."(".implode(", ",$W[2]).")");return
queries("ALTER TABLE ".table($Q).implode(",",$Ua));}function
truncate_tables(array$S){return
apply_queries("TRUNCATE TABLE",$S);}function
drop_views(array$ck){return
queries("DROP VIEW ".implode(", ",array_map('Adminer\table',$ck)));}function
drop_tables(array$S){return
queries("DROP TABLE ".implode(", ",array_map('Adminer\table',$S)));}function
move_tables(array$S,array$ck,$Vi){$Bh=array();foreach($S
as$Q)$Bh[]=table($Q)." TO ".idf_escape($Vi).".".table($Q);if(!$Bh||queries("RENAME TABLE ".implode(", ",$Bh))){$Xb=array();foreach($ck
as$Q)$Xb[table($Q)]=view($Q);connection()->select_db($Vi);$h=idf_escape(DB);foreach($Xb
as$y=>$bk){if(!queries("CREATE VIEW $y AS ".str_replace(" $h."," ",$bk["select"]))||!queries("DROP VIEW $h.$y"))return
false;}return
true;}return
false;}function
copy_tables(array$S,array$ck,$Vi){queries("SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO'");foreach($S
as$Q){$y=($Vi==DB?table("copy_$Q"):idf_escape($Vi).".".table($Q));if(($_POST["overwrite"]&&!queries("\nDROP TABLE IF EXISTS $y"))||!queries("CREATE TABLE $y LIKE ".table($Q))||!queries("INSERT INTO $y SELECT * FROM ".table($Q)))return
false;foreach(get_rows("SHOW TRIGGERS LIKE ".q(addcslashes($Q,"%_\\")))as$G){$tj=$G["Trigger"];list($Nc,$dg)=trigger_event($G);if(!queries("CREATE TRIGGER ".($Vi==DB?idf_escape("copy_$tj"):idf_escape($Vi).".".idf_escape($tj))." $G[Timing] $Nc".($dg!=""?" $dg":"")." ON $y FOR EACH ROW\n$G[Statement];"))return
false;}}foreach($ck
as$Q){$y=($Vi==DB?table("copy_$Q"):idf_escape($Vi).".".table($Q));$bk=view($Q);if(($_POST["overwrite"]&&!queries("DROP VIEW IF EXISTS $y"))||!queries("CREATE VIEW $y AS $bk[select]"))return
false;}return
true;}function
trigger_event(array$G){$Oc=explode(",",$G["Event"]);$F=array();foreach(array("DELETE","INSERT","UPDATE")as$Nc){if(in_array($Nc,$Oc))$F[]=$Nc;}$F=implode(" OR ",$F);if(in_array("UPDATE",$Oc)&&min_version('','12.0.1')&&preg_match('~\s(?:BEFORE|AFTER)\s+(.+?)\s+ON\s~is',get_val("SHOW CREATE TRIGGER ".idf_escape($G["Trigger"]),2),$x)&&preg_match('~\bOF\s+(.+)~is',$x[1],$dg))return
array("$F OF",$dg[1]);return
array($F,"");}function
trigger($y,$Q){if($y=="")return
array();$H=get_rows("SHOW TRIGGERS WHERE `Trigger` = ".q($y));$F=reset($H);if($F)list($F["Event"],$F["Of"])=trigger_event($F);return($F?:array());}function
triggers($Q){$F=array();foreach(get_rows("SHOW TRIGGERS LIKE ".q(addcslashes($Q,"%_\\")))as$G){list($Nc)=trigger_event($G);$F[$G["Trigger"]]=array($G["Timing"],$Nc);}return$F;}function
trigger_options(){return
array("Timing"=>array("BEFORE","AFTER"),"Event"=>(min_version('','12.0.1')?array("INSERT","UPDATE","UPDATE OF","DELETE","INSERT OR UPDATE","INSERT OR UPDATE OF","DELETE OR INSERT","DELETE OR UPDATE","DELETE OR UPDATE OF","DELETE OR INSERT OR UPDATE","DELETE OR INSERT OR UPDATE OF",):array("INSERT","UPDATE","DELETE")),"Type"=>array("FOR EACH ROW"),);}function
routine($y,$T){$H=get_rows("SELECT PARAMETER_NAME, DTD_IDENTIFIER, PARAMETER_MODE, COLLATION_NAME
FROM information_schema.PARAMETERS
WHERE SPECIFIC_SCHEMA = DATABASE() AND ROUTINE_TYPE = '$T' AND SPECIFIC_NAME = ".q($y)."
ORDER BY ORDINAL_POSITION");$l=array();foreach($H
as$G){$Dd=$G["DTD_IDENTIFIER"];list($zj,$u,$Ij)=parse_type($Dd);$l[]=array("field"=>$G["PARAMETER_NAME"],"type"=>$zj,"length"=>$u,"unsigned"=>$Ij,"null"=>true,"full_type"=>$Dd,"inout"=>($T=="FUNCTION"?"":$G["PARAMETER_MODE"]),"collation"=>$G["COLLATION_NAME"],);}$F=connection()->query("SELECT
	ROUTINE_COMMENT comment,
	ROUTINE_DEFINITION definition,
	LOWER(EXTERNAL_LANGUAGE) language,
	IF(DEFINER = CURRENT_USER(), '', DEFINER) definer,
	IF(IS_DETERMINISTIC = 'YES', 'DETERMINISTIC', 'NOT DETERMINISTIC') is_deterministic,
	SQL_DATA_ACCESS data_access,
	CONCAT('SQL SECURITY ', SECURITY_TYPE) security
FROM information_schema.ROUTINES
WHERE ROUTINE_SCHEMA = DATABASE() AND ROUTINE_TYPE = '$T' AND ROUTINE_NAME = ".q($y))->fetch_assoc();if(!$F)return
array();$F['options']=array("DEFINER"=>$F['definer'],"DETERMINISTIC"=>$F['is_deterministic'],"SQL_DATA_ACCESS"=>$F['data_access'],"SQL_SECURITY"=>$F['security'],"COMMENT"=>$F['comment'],);if($l&&$l[0]['field']=='')$F['returns']=array_shift($l);$F['fields']=$l;return$F;}function
routines(){return
get_rows("SELECT SPECIFIC_NAME, ROUTINE_NAME, ROUTINE_TYPE, DTD_IDENTIFIER FROM information_schema.ROUTINES WHERE ROUTINE_SCHEMA = DATABASE()");}function
routine_languages(){return(min_version(9,99)?array("sql"=>"sql","javascript"=>"js"):array());}function
routine_options($Kh){return
array("DEFINER"=>array(),"DETERMINISTIC"=>array("NOT DETERMINISTIC","DETERMINISTIC"),"SQL_DATA_ACCESS"=>array("CONTAINS SQL","NO SQL","READS SQL DATA","MODIFIES SQL DATA"),"SQL_SECURITY"=>array("SQL SECURITY DEFINER","SQL SECURITY INVOKER"),"COMMENT"=>array(),);}function
routine_id($y,array$G){return
idf_escape($y);}function
last_id($E){return
get_val("SELECT LAST_INSERT_ID()");}function
explain(Db$f,$D){return$f->query("EXPLAIN ".(min_version(5.7)?"":"PARTITIONS ").$D);}function
found_rows(array$R,array$Z){return($Z||$R["Engine"]!="InnoDB"?null:$R["Rows"]);}function
create_sql($Q,$Aa,$Ii){$F=get_val("SHOW CREATE TABLE ".table($Q),1);if(!$Aa)$F=preg_replace('~(\n\)[^\n]*?) AUTO_INCREMENT=\d+~','\1',$F);return$F;}function
truncate_sql($Q){return"TRUNCATE ".table($Q);}function
use_sql($Qb,$Ii=""){$y=idf_escape($Qb);$F="";if(preg_match('~CREATE~',$Ii)&&($Hb=get_val("SHOW CREATE DATABASE $y",1))){set_utf8mb4($Hb);if($Ii=="DROP+CREATE")$F="DROP DATABASE IF EXISTS $y;\n";$F
.="$Hb;\n";}return$F."USE $y";}function
trigger_sql($Q){$F="";foreach(get_rows("SHOW TRIGGERS LIKE ".q(addcslashes($Q,"%_\\")),null,"-- ")as$G){list($G["Event"],$G["Of"])=trigger_event($G);$F
.="\n".create_trigger(" ON ".table($G["Table"]),$G+array("Type"=>"FOR EACH ROW")).";\n";}return$F;}function
show_variables(){return
get_rows("SHOW VARIABLES");}function
show_status(){return
get_rows("SHOW STATUS");}function
process_list(){return
get_rows("SHOW FULL PROCESSLIST");}function
convert_field(array$k){return
driver()->convertColumn(idf_escape($k["field"]),$k);}function
unconvert_field(array$k,$F){if(preg_match("~binary~",$k["type"]))$F="UNHEX($F)";if($k["type"]=="bit")$F="CONVERT(b$F, UNSIGNED)";if($k["type"]=="vector")$F=(connection()->flavor=='maria'?"VEC_FromText":"STRING_TO_VECTOR")."($F)";if(preg_match("~geom|point|linestring|polygon~",$k["type"])){$ch=(min_version(8)?"ST_":"");$F=$ch."GeomFromText($F, $ch"."SRID($k[field]))";}return$F;}function
support($ad){return
preg_match('~^(comment|columns|copy|database|drop_col|dump|event|indexes|kill|privileges|move_col|procedure|processlist|routine|sql|status|table|trigger|variables|view'.(min_version(8)?'|descidx':'').(min_version('8.0.16','10.2.1')?'|check':'').(min_version(8,99)?'|fast_status':'').')$~',$ad);}function
kill_process($q){return
queries("KILL ".number($q));}function
connection_id(){return"SELECT CONNECTION_ID()";}function
max_connections(){return
get_val("SELECT @@max_connections");}function
types($Wc=false){return
array();}function
type_values($q){return"";}function
type_definition($q){return
array("kind"=>"","definition"=>"");}function
schemas(){return
array();}function
get_schema(){return"";}function
set_schema($I,$g=null){return
true;}function
schema_comment($I){return"";}function
set_schema_comment($I,$mb){return
true;}}define('Adminer\JUSH',Driver::$jush);define('Adminer\SERVER',"".$_GET[DRIVER]);define('Adminer\DB',"$_GET[db]");define('Adminer\ME',preg_replace('~\?.*~','',relative_uri()).'?'.(sid()?SID.'&':'').($_GET["ext"]?"ext=".url_escape($_GET["ext"]).'&':'').(isset($_GET[DRIVER])?DRIVER."=".url_escape(SERVER).'&':'').(isset($_GET["username"])?"username=".url_escape($_GET["username"]).'&':'').(isset($_GET["db"])?'db='.url_escape(DB).'&'.(isset($_GET["ns"])?"ns=".url_escape($_GET["ns"])."&":""):''));if(isset($_GET["manifest"])){header("Content-Type: application/manifest+json; charset=utf-8");header("Cache-Control: no-cache");echo
json_encode(adminer()->manifest(),64|256);exit;}function
page_header($ej,$j="",$Pa=array(),$fj="",$Zf=false,$nc=""){if($Zf){header("HTTP/1.1 404 Not Found");$j=($j?:lang(70));}page_headers();if(is_ajax()&&$j){page_messages($j);exit;}if(!ob_get_level())ob_start('ob_gzhandler',4096);$gj=$ej.($fj!=""?": $fj":"");$hj=strip_tags($gj.(SERVER!=""&&SERVER!="localhost"?h(" - ".SERVER):"")." - ".adminer()->name());echo'<!DOCTYPE html>
<html lang=\'',LANG,'\' dir=\'',lang(71),'\' class=\'',lang(71),' nojs\'>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="robots" content="noindex">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>',$hj,'</title>
<link rel="stylesheet" href="',h(preg_replace("~\\?.*~","",ME)."?file=default.css&version=6.2.0+a36d1ecb"),'">
';$Lb=adminer()->css();if(is_int(key($Lb)))$Lb=array_fill_keys($Lb,'light');$Sd=in_array('light',$Lb)||in_array('',$Lb);$Pd=in_array('dark',$Lb)||in_array('',$Lb);$Ob=($Sd?($Pd?null:false):($Pd?:null));$Bf=" media='(prefers-color-scheme: dark)'";if($Ob!==false)echo"<link rel='stylesheet'".($Ob?"":$Bf)." href='".h(preg_replace("~\\?.*~","",ME)."?file=dark.css&version=6.2.0+a36d1ecb")."'>\n";echo"<meta name='color-scheme' content='".($Ob===null?"light dark":($Ob?"dark":"light"))."'>\n",script_src(preg_replace("~\\?.*~","",ME)."?file=functions.js&version=6.2.0+a36d1ecb");if(adminer()->head($Ob))echo"<link rel='icon' href='data:image/gif;base64,"."R0lGODlhEAAQAJEAAAQCBPz+/PwCBAROZCH5BAEAAAAALAAAAAAQABAAAAI2hI+pGO1rmghihiUdvUBnZ3XBQA7f05mOak1RWXrNq5nQWHMKvuoJ37BhVEEfYxQzHjWQ5qIAADs='>\n","<link rel='apple-touch-icon' href='".h(preg_replace("~\\?.*~","",ME)."?file=logo.svg&version=6.2.0+a36d1ecb")."'>\n";if(adminer()->manifest())echo"<link rel='manifest' href='".h(preg_replace('~\?.*~','',ME)."?manifest=")."' crossorigin='use-credentials'>\n";foreach($Lb
as$Nj=>$Kf){$c=($Kf=='dark'&&!$Ob?$Bf:($Kf=='light'&&$Pd?" media='(prefers-color-scheme: light)'":""));echo"<link rel='stylesheet'$c href='".h($Nj)."'>\n";}echo"\n<body class='";adminer()->bodyClass();echo"'>\n",script((isset($_COOKIE["adminer_version"])||!adminer()->verifyVersion()?"":"onload = partial(verifyVersion, '".VERSION."');\n")."
const offlineMessage = '".js_escape(lang(72))."';
const numberFormat = '".js_escape(lang(6))."';
const numberDigits = '".js_escape(lang(7))."';
const urlSeparators = '".js_escape(ini_get("arg_separator.input"))."';"),"<div id='help' class='jush-".JUSH." jsonly hidden'".on('mouseover','helpKeep').on('mouseout','helpMouseout')."></div>\n","<div id='content'>\n","<span id='menuopen' class='jsonly'".on('click','menuToggle')."><button title='".lang(73)."' class='icon icon-move' aria-expanded='false'></button></span>\n";if($Pa!==null){$w=substr(preg_replace('~\b(username|db|ns)=[^&]*&~','',ME),0,-1);echo'<p id="breadcrumb"><a href="'.h($w?:".").'">'.get_driver(DRIVER).'</a> » ';$w=substr(preg_replace('~\b(db|ns)=[^&]*&~','',ME),0,-1);$L=adminer()->serverName(SERVER);$L=($L!=""?$L:lang(74));if($Pa===false)echo"$L\n";else{echo"<a href='".h($w.(DB!=""&&support("single_db")?"&db=":""))."' accesskey='1' title='Alt+Shift+1'>$L</a> » ";$Sh="";if(is_string($Pa)){$Sh=$Pa;$Pa=array();}if($_GET["ns"]!=""||(DB!=""&&is_array($Pa))){$Sb="$w&db=".url_escape(DB).(support("scheme")?"&ns=":"").(support("single_table")?"&select=":"");echo'<a href="'.h($Sb.($_GET["ns"]==""?$Sh:"")).'">'.h(DB).'</a> » ';}if(is_array($Pa)){if($_GET["ns"]!="")echo'<a href="'.h(substr(ME,0,-1).$Sh).'">'.h($_GET["ns"]).'</a> » ';foreach($Pa
as$t=>$W){$ac=(is_array($W)?$W[1]:h($W));if($ac!="")echo"<a href='".h(ME."$t=").url_escape(is_array($W)?$W[0]:$W)."'>$ac</a> » ";}}echo"$ej\n";}}echo"<h2>$gj$nc</h2>\n","<div id='ajaxstatus' role='status' class='jsonly'></div>\n";restart_session();page_messages($j);adminer()->serviceWorker();$Rb=&get_session("dbs");if(DB!=""&&$Rb&&!in_array(DB,$Rb,true))$Rb=null;stop_session();define('Adminer\PAGE_HEADER',1);ob_flush();flush();if($Zf){page_footer($Zf===true?"":$Zf);exit;}}function
service_worker(){$zh=has_passwords();$gb=($zh?"navigator.serviceWorker.register('".js_escape(preg_replace('~\?.*~','',ME)."?file=worker.js&version=6.2.0+a36d1ecb")."', {scope: location.pathname}).catch(() => {});":"navigator.serviceWorker.getRegistration().then(registration => registration && registration.unregister());
	caches.keys().then(keys => keys.forEach(key => key.startsWith('adminer-') && caches.delete(key)));");echo
script("if (navigator.serviceWorker) {\n\t$gb\n}");}function
has_passwords(){foreach((array)$_SESSION["pwds"]as$ni){foreach($ni
as$Uj){foreach($Uj
as$B){if($B!==null)return
true;}}}return
false;}function
page_headers(){header("Content-Type: text/html; charset=utf-8");header("Cache-Control: no-cache");header("X-Frame-Options: deny");header("X-XSS-Protection: 0");header("X-Content-Type-Options: nosniff");header("Referrer-Policy: origin-when-cross-origin");foreach(adminer()->csp(csp())as$Kb){$Wd=array();foreach($Kb
as$t=>$W)$Wd[]="$t $W";header("Content-Security-Policy: ".implode("; ",$Wd));}adminer()->headers();}function
csp(){return
array(array("script-src"=>"'self' 'unsafe-inline' 'nonce-".get_nonce()."' 'strict-dynamic'","connect-src"=>"'self' https://www.adminer.org","frame-src"=>"https://www.adminer.org","object-src"=>"'none'","base-uri"=>"'none'","form-action"=>"'self'",),);}function
design_checksums(){$Sj=array();foreach(array_keys(adminer()->css())as$Nj)$Sj[preg_replace('~\?.*~','',$Nj)]=true;$F=array();foreach(array("adminer.css","adminer-dark.css")as$m){if($Sj[$m]&&file_exists($m)){preg_match('~^/\* Adminer design ([-\w]+) \*/~',file_get_contents($m),$x);$F[$m]=array((string)$x[1],Plugins::checksum($m));}}return$F;}function
official_design_checksums(){return
array('adminer-dark/adminer-dark.css'=>'a26bcd7b','brade/adminer.css'=>'be4161f0','bueltge/adminer.css'=>'1a8f00b4','cpanel/adminer.css'=>'59ce604e','dracula/adminer-dark.css'=>'cfaf61dd','esterka/adminer.css'=>'1f805f36','flat/adminer.css'=>'49a61af9','galkaev/adminer-dark.css'=>'16c46f94','haeckel/adminer.css'=>'147a3565','hever/adminer.css'=>'c9d1d11c','konya/adminer.css'=>'2b409696','lavender-light/adminer.css'=>'bf03f5d7','lucas-sandery/adminer.css'=>'6596353','mancave/adminer-dark.css'=>'d9169f12','mvt/adminer.css'=>'ebd3afdc','nette/adminer.css'=>'1b8ac749','ng9/adminer.css'=>'488583cf','nicu/adminer.css'=>'a38b3b6a','pappu687/adminer.css'=>'b58d128c','paranoiq/adminer.css'=>'64d27e5','pepa-linha/adminer.css'=>'baf25f0','pokorny/adminer.css'=>'ee9eea6d','price/adminer.css'=>'81be9a85','rmsoft/adminer.css'=>'6cd4a237','rmsoft_blue-dark/adminer.css'=>'32102a8','rmsoft_blue/adminer.css'=>'7d8d5b18','win98/adminer.css'=>'e82d63c3',);}function
version_iframe(){return(isset($_COOKIE["adminer_version"])||!adminer()->verifyVersion()?"":"<noscript><iframe sandbox src='https://www.adminer.org/version/?current=".VERSION."&amp;noscript=1'></iframe></noscript>");}function
get_nonce(){static$Yf;if(!$Yf)$Yf=base64_encode(rand_string());return$Yf;}function
page_messages($j){$Mj=preg_replace('~^[^?]*~','',$_SERVER["REQUEST_URI"]);$Ef=idx($_SESSION["messages"],$Mj);if($Ef){echo"<div class='message'>".implode("</div>\n<div class='message'>",$Ef)."</div>".script("messagesPrint();");unset($_SESSION["messages"][$Mj]);}if($j)echo"<div class='error'>$j</div>\n";if(adminer()->error)echo"<div class='error'>".adminer()->error."</div>\n";}function
page_footer($Jf=""){echo"</div>\n\n<div id='foot' class='foot'>\n<div id='menu'>\n";adminer()->navigation($Jf);echo"</div>\n";if($Jf!="auth")echo'<form action="" method="post">
<p class="logout">
<span title="',lang(40),'">',h($_GET["username"])."\n",'</span>
<input type=\'submit\' name=\'logout\' value=\'',lang(75),'\' id=\'logout\'>
',input_token(),'</form>
';echo"</div>\n\n",script("setupSubmitHighlight(document);");}function
int32($Rf){while($Rf>=2147483648)$Rf-=4294967296;while($Rf<=-2147483649)$Rf+=4294967296;return(int)$Rf;}function
long2str(array$V,$ek){$Mh='';foreach($V
as$W)$Mh
.=pack('V',$W);if($ek)return
substr($Mh,0,end($V));return$Mh;}function
str2long($Mh,$ek){$V=array_values(unpack('V*',str_pad($Mh,4*ceil(strlen($Mh)/4),"\0")));if($ek)$V[]=strlen($Mh);return$V;}function
xxtea_mx($nk,$mk,$Ki,$Me){return
int32((($nk>>5&0x7FFFFFF)^$mk<<2)+(($mk>>3&0x1FFFFFFF)^$nk<<4))^int32(($Ki^$mk)+($Me^$nk));}function
encrypt_string($Hi,$t){if($Hi=="")return"";$t=array_values(unpack("V*",pack("H*",md5($t))));$V=str2long($Hi,true);$Rf=count($V)-1;$nk=$V[$Rf];$mk=$V[0];$oh=floor(6+52/($Rf+1));$Ki=0;while($oh-->0){$Ki=int32($Ki+0x9E3779B9);$vc=$Ki>>2&3;for($_g=0;$_g<$Rf;$_g++){$mk=$V[$_g+1];$Qf=xxtea_mx($nk,$mk,$Ki,$t[$_g&3^$vc]);$nk=int32($V[$_g]+$Qf);$V[$_g]=$nk;}$mk=$V[0];$Qf=xxtea_mx($nk,$mk,$Ki,$t[$_g&3^$vc]);$nk=int32($V[$Rf]+$Qf);$V[$Rf]=$nk;}return
long2str($V,false);}function
decrypt_string($Hi,$t){if($Hi=="")return"";if(!$t)return
false;$t=array_values(unpack("V*",pack("H*",md5($t))));$V=str2long($Hi,false);$Rf=count($V)-1;$nk=$V[$Rf];$mk=$V[0];$oh=floor(6+52/($Rf+1));$Ki=int32($oh*0x9E3779B9);while($Ki){$vc=$Ki>>2&3;for($_g=$Rf;$_g>0;$_g--){$nk=$V[$_g-1];$Qf=xxtea_mx($nk,$mk,$Ki,$t[$_g&3^$vc]);$mk=int32($V[$_g]-$Qf);$V[$_g]=$mk;}$nk=$V[$Rf];$Qf=xxtea_mx($nk,$mk,$Ki,$t[$_g&3^$vc]);$mk=int32($V[0]-$Qf);$V[0]=$mk;$Ki=int32($Ki-0x9E3779B9);}return
long2str($V,true);}$Sg=array();if($_COOKIE["adminer_permanent"]){foreach(explode(" ",$_COOKIE["adminer_permanent"])as$W){list($t)=explode(":",$W);$Sg[$t]=$W;}}function
add_invalid_login(){$Ia=get_temp_dir()."/adminer-invalid";foreach(glob("$Ia*")?:array($Ia)as$m){$zd=file_open_lock($m);if($zd)break;}if(!$zd)$zd=file_open_lock("$Ia-".rand_string());if(!$zd)return;$De=json_decode(stream_get_contents($zd),true);$bj=time();if($De){foreach($De
as$Ee=>$W){if($W[0]<$bj)unset($De[$Ee]);}}$Be=&$De[adminer()->bruteForceKey()];if(!$Be)$Be=array($bj+30*60,0);$Be[1]++;file_write_unlock($zd,json_encode($De));}function
check_invalid_login(array&$Sg){$De=array();foreach(glob(get_temp_dir()."/adminer-invalid*")as$m){$zd=file_open_lock($m);if($zd){$De=json_decode(stream_get_contents($zd),true);file_unlock($zd);break;}}$t=adminer()->bruteForceKey();$Be=idx($De,$t,array());$Xf=($Be[1]>29?$Be[0]-time():0);if($Xf>0){$j=lang(76,ceil($Xf/60));if($_SERVER["HTTP_X_FORWARDED_FOR"]!=""&&$t==$_SERVER["REMOTE_ADDR"])$j
.='<br>'.lang(77,'<b>login-reverse-proxy</b>'," href='https://www.adminer.org/plugins/?version=".VERSION."'".target_blank());auth_error($j,$Sg,false);}}function
password_required(){static$F;if($F===null){$F=(bool)get_session("password_required");if(!$F){$Jb=adminer()->credentials();$F=!is_object(Driver::connect($Jb[0],$Jb[1],""));if($F)set_session("password_required",true);}}return$F;}function
require_password_link($B){$Mf="<a href='https://www.adminer.org/password/'".target_blank().">".lang(78)."</a>";if(!function_exists('password_hash'))return" $Mf";$Ug=($B!==null?$B:base64_encode(substr(pack("H*",rand_string()),0,12)));$Vd=password_hash($Ug,PASSWORD_DEFAULT);$m="adminer-plugins.php";$Tc=file_exists("adminer-plugins.php");if($Tc)$Ae=($B!==null?lang(79,"<b>$m</b>"):lang(80,"<b>$m</b>","<b>$Ug</b>"));else{$m="<button name='password_less' value='".h($Vd)."' class='link'>$m</button>";$Ae=($B!==null?lang(81,$m):lang(82,$m,"<b>$Ug</b>"));}$ff="\t<a>new</a> Adminer\\Password(<span class='jush-apo'>'".h($Vd)."'</span>),";$F="<p>$Ae
<pre><code class='jush'>".($Tc?$ff:"&lt;?php\n<a>return</a> <a>array</a>(\n$ff\n);")."</code></pre>
<p>$Mf
";return" <a href='#password-less' class='toggle'>".lang(83)."</a>
<div id='password-less' class='hidden'>".($Tc?$F:"<form action='' method='post'>\n".$F.input_token()."</form>")."</div>";}if(preg_match('~^[-\w$./]+$~',$_POST["password_less"])&&verify_token()){header("Content-Type: application/octet-stream");header("Content-Disposition: attachment; filename=adminer-plugins.php");echo"<?php\nreturn array(\n\tnew Adminer\\Password('$_POST[password_less]'),\n);\n";exit;}$_a=$_POST["auth"];if($_a&&(!adminer()->verifyLoginToken()||verify_token())){session_regenerate_id();$Zj=$_a["driver"];$L=$_a["server"];$U=$_a["username"];$B=(string)$_a["password"];$h=$_a["db"];set_password($Zj,$L,$U,$B);$_SESSION["db"][$Zj][$L][$U][$h]=true;if($_a["permanent"]){$t=implode("-",array_map('base64_encode',array($Zj,$L,$U,$h)));$ih=adminer()->permanentLogin(true);$Sg[$t]="$t:".base64_encode($ih?encrypt_string($B,$ih):"");cookie("adminer_permanent",implode(" ",$Sg));}if(!array_diff(array_keys($_POST),array("auth","token"))||$Zj!=DRIVER||$L!=SERVER||$U!==$_GET["username"]||$h!=DB)redirect(auth_url($Zj,$L,$U,$h));}elseif($_POST["logout"]&&(!$_SESSION["token"]||verify_token())){Driver::disconnect();foreach(array("pwds","db","dbs","queries")as$t)set_session($t,null);unset_permanent($Sg);redirect(substr(preg_replace('~\b(username|db|ns)=[^&]*&~','',ME),0,-1),lang(84).' '.str_replace('<a ','<a'.target_blank().' ',lang(85)));}elseif($Sg&&!$_SESSION["pwds"]){session_regenerate_id();$ih=adminer()->permanentLogin();foreach($Sg
as$t=>$W){list(,$cb)=explode(":",$W);list($Zj,$L,$U,$h)=array_map('base64_decode',explode("-",$t));set_password($Zj,$L,$U,decrypt_string(base64_decode($cb),$ih));$_SESSION["db"][$Zj][$L][$U][$h]=true;}}function
unset_permanent(array&$Sg){foreach($Sg
as$t=>$W){list($Zj,$L,$U,$h)=array_map('base64_decode',explode("-",$t));if($Zj==DRIVER&&$L==SERVER&&$U==$_GET["username"]&&$h==DB)unset($Sg[$t]);}cookie("adminer_permanent",implode(" ",$Sg));}function
auth_error($j,array&$Sg,$Ce=true){$oi=session_name();if(isset($_GET["username"])){header("HTTP/1.1 403 Forbidden");if(($_COOKIE[$oi]||$_GET[$oi])&&!$_SESSION["token"])$j=lang(86);elseif($Ce&&($B=get_password())!==null){restart_session();add_invalid_login();if($B===false)$j
.=($j?'<br>':'').lang(87,target_blank(),'<code>permanentLogin()</code>');set_password(DRIVER,SERVER,$_GET["username"],null);unset_permanent($Sg);}}if(!$_COOKIE[$oi]&&$_GET[$oi]&&ini_bool("session.use_only_cookies"))$j=lang(88);$Dg=session_get_cookie_params();cookie("adminer_key",($_COOKIE["adminer_key"]?:rand_string()),$Dg["lifetime"]);if(!$_SESSION["token"])$_SESSION["token"]=rand(1,1e6);page_header(lang(42),$j,null);echo"<form action='' method='post'>\n","<div>";if(hidden_fields($_POST,array("auth","token")))echo"<p class='message'>".lang(89)."\n";echo
input_token(),"</div>\n";adminer()->loginForm();echo"</form>\n";page_footer("auth");exit;}if(isset($_GET["username"])&&!class_exists('Adminer\Db')){unset($_SESSION["pwds"][DRIVER]);unset_permanent($Sg);page_header(lang(90),lang(91,implode(", ",Driver::$extensions)),false);page_footer("auth");exit;}$f='';if(isset($_GET["username"])&&is_string(get_password())){check_invalid_login($Sg);$Jb=adminer()->credentials();$f=Driver::connect($Jb[0],$Jb[1],$Jb[2]);if(is_object($f)){Db::$instance=$f;Driver::$instance=new
Driver($f);if($f->flavor)save_settings(array("vendor-".DRIVER."-".SERVER=>get_driver(DRIVER)));}}$mf=null;if(!is_object($f)||($mf=adminer()->login($_GET["username"],get_password()))!==true){$j=(is_string($f)?nl_br(h($f)):(is_string($mf)?$mf:lang(92))).(preg_match('~^ | $~',get_password())?'<br>'.lang(93):'');auth_error($j,$Sg);}if($_POST["logout"]&&$_SESSION["token"]&&!verify_token()){page_header(lang(75),lang(94));page_footer("db");exit;}if(!$_SESSION["token"])$_SESSION["token"]=rand(1,1e6);stop_session(true);if($_a&&$_POST["token"])$_POST["token"]=get_token();$j='';if($_POST){if(!verify_token()){header("HTTP/1.1 403 Forbidden");$j=lang(94).' '.lang(95);}}elseif($_SERVER["REQUEST_METHOD"]=="POST"){header("HTTP/1.1 413 Content Too Large");$j=lang(96,"<b>post_max_size</b>");if(isset($_GET["sql"]))$j
.=' '.lang(97);}function
doc_link(array$Og,$Yi=""){return"";}function
like_bool(array$k){return$k["type"]=="bool"||(preg_match('~bit|tinyint~',$k["type"])&&$k["length"]==1);}function
sql_comment($D,$bj=""){return"<!--\n".str_replace("--","--><!-- ",$D)."\n".($bj!=""?"($bj)\n":"")."-->\n";}connection()->select_db(adminer()->database());if(support("scheme")){$I=get_schema();if($I!="")set_schema($I);}adminer()->afterConnect();add_driver(DRIVER,lang(42));if(isset($_GET["select"])&&($_POST["edit"]||$_POST["clone"])&&!$_POST["save"])$_GET["edit"]=$_GET["select"];if(isset($_GET["download"])){$a=$_GET["download"];$l=fields($a);header("Content-Type: application/octet-stream");$Y=array_merge((array)$_GET["where"],(array)$_GET["val"]);header("Content-Disposition: attachment; filename=".friendly_url("$a-".implode("_",$Y)).".".friendly_url($_GET["field"]));$J=array(idf_escape($_GET["field"]));$E=driver()->select($a,$J,array(where($_GET,$l)),$J);$G=($E?$E->fetch_row():array());echo
driver()->value($G[0],$l[$_GET["field"]]);exit;}elseif(isset($_GET["edit"])){$a=$_GET["edit"];$l=fields($a);$Z=(isset($_GET["select"])?($_POST["check"]&&count($_POST["check"])==1?where_check($_POST["check"][0],$l):""):where($_GET,$l));$Kj=(isset($_GET["select"])?$_POST["edit"]:$Z);foreach($l
as$y=>$k){if((!$Kj&&!isset($k["privileges"]["insert"]))||adminer()->fieldName($k)=="")unset($l[$y]);}if($_POST&&!$j&&!isset($_GET["select"])){$lf=relative_uri((string)$_POST["referer"]);if($_POST["insert"])$lf=($Kj?null:relative_uri());elseif(!preg_match('~^.+&select=.+$~',$lf))$lf=ME."select=".url_escape($a);$qe=indexes($a);$Dj=unique_array($_GET["where"],$qe);$rh="\nWHERE $Z";if(isset($_POST["delete"]))queries_redirect($lf,lang(98),driver()->delete($a,$rh,$Dj?0:1));else{$M=array();foreach($l
as$y=>$k){$W=process_input($k);if($W!==false&&$W!==null)$M[idf_escape($y)]=$W;}if($Kj){if(!$M)redirect($lf);queries_redirect($lf,lang(99),driver()->update($a,$M,$rh,$Dj?0:1));if(is_ajax()){page_headers();page_messages($j);exit;}}else{$E=driver()->insert($a,$M);$Ye=($E?last_id($E):0);queries_redirect($lf,lang(100,($Ye?" $Ye":"")),$E);}}}$G=null;$D="";$bj="";if($Z){$J=array();$Wh=array("*");foreach($l
as$y=>$k){if(isset($k["privileges"]["select"])){$wa=($_POST["clone"]&&$k["auto_increment"]?"''":convert_field($k));$d=($wa?"$wa AS ":"").idf_escape($y);$J[]=$d;if($wa)$Wh[]=$d;}}$G=array();if(!support("table")){$J=array("*");$Wh=$J;}if($J){$Di=microtime(true);$E=driver()->select($a,$J,array($Z),$J,array(),(isset($_GET["select"])?2:1));$D=str_replace("SELECT ".implode(", ",$J),"SELECT ".implode(", ",$Wh),driver()->query);$bj=format_time($Di);if(!$E)$j=adminer()->error();else{$G=$E->fetch_assoc();if(!$G)$G=false;}if(isset($_GET["select"])&&(!$G||$E->fetch_assoc()))$G=null;}}if(!$l&&driver()->primary!=""){if(!$Z){$E=driver()->select($a,array("*"),array(),array("*"));$G=($E?$E->fetch_assoc():false);if(!$G)$G=array(driver()->primary=>"");}if($G){foreach($G
as$t=>$W){if(!$Z)$G[$t]=null;$l[$t]=array("field"=>$t,"null"=>($t!=driver()->primary),"auto_increment"=>($t==driver()->primary));}}}if($_POST["save"]){$Yg=array();foreach((array)$_POST["fields"]as$t=>$W)$Yg[bracket_escape($t,true)]=$W;$G=$Yg+($G?$G:array());}edit_form($a,$l,$G,$Kj,$j,$D,$bj);}elseif(isset($_GET["select"])){$a=$_GET["select"];$R=table_status1($a);$qe=indexes($a);$l=fields($a);$vd=column_foreign_keys($a);$fg=$R["Oid"];$Jh=array();$e=array();$Rh=array();$pg=array();$Zi=null;foreach($l
as$t=>$k){$y=adminer()->fieldName($k);$Sf=html_entity_decode(strip_tags($y),ENT_QUOTES);if(isset($k["privileges"]["select"])&&$y!=""){$e[$t]=$Sf;if(is_shortable($k))$Zi=adminer()->selectLengthProcess();}if(isset($k["privileges"]["where"])&&$y!="")$Rh[$t]=$Sf;if(isset($k["privileges"]["order"])&&$y!="")$pg[$t]=$Sf;$Jh+=$k["privileges"];}list($J,$o)=adminer()->selectColumnsProcess($e,$qe);$J=array_unique($J);$o=array_unique($o);$He=count($o)<count($J);$Z=adminer()->selectSearchProcess($l,$qe,$R);$og=adminer()->selectOrderProcess($l,$qe);$v=adminer()->selectLimitProcess();if($_GET["val"]&&is_ajax()){header("Content-Type: text/plain; charset=utf-8");foreach($_GET["val"]as$Ej=>$G){$wa=convert_field($l[key($G)]);$J=array($wa?:idf_escape(key($G)));$Z[]=where_check(bracket_escape($Ej,true),$l);$F=driver()->select($a,$J,$Z,$J);if($F)echo
first($F->fetch_row());}exit;}$fh=$Hj=array();foreach($qe
as$s){if($s["type"]=="PRIMARY"){$fh=array_flip($s["columns"]);$Hj=($J?$fh:array());foreach($Hj
as$t=>$W){if(in_array(idf_escape($t),$J))unset($Hj[$t]);}break;}}if($fg&&!$fh){$fh=$Hj=array($fg=>0);$qe[]=array("type"=>"PRIMARY","columns"=>array($fg));}if($_POST&&!$j){$ik=$Z;if(!$_POST["all"]&&is_array($_POST["check"])){$ab=array();foreach($_POST["check"]as$Xa)$ab[]=where_check($Xa,$l);$ik[]="((".implode(") OR (",$ab)."))";}$kk=$ik;$ik=($ik?"\nWHERE ".implode(" AND ",$ik):"");if($_POST["export"]){save_settings(array("output"=>$_POST["output"],"format"=>$_POST["format"]),"adminer_import");dump_headers($a);adminer()->dumpTable($a,"");$Vh=($J?:array("*"));$Db=convert_fields($e,$l,$J);if($Db)$Vh[]=substr($Db,2);$D="";if(is_array($_POST["check"])&&!$fh){$Bd=implode(", ",$Vh)."\nFROM ".table($a);$Kd=($o&&$He?"\nGROUP BY ".implode(", ",$o):"").($og?"\nORDER BY ".implode(", ",$og):"");$Cj=array();foreach($_POST["check"]as$W)$Cj[]="(SELECT".limit($Bd,"\nWHERE ".($Z?implode(" AND ",$Z)." AND ":"").where_check($W,$l).$Kd,1).")";$D=implode(" UNION ALL ",$Cj);}adminer()->dumpData($a,"table",$D,$Vh,$kk,($He?$o:array()),$og);adminer()->dumpFooter();exit;}if(!adminer()->selectEmailProcess($Z,$vd)){if($_POST["save"]||$_POST["delete"]){$E=true;$ma=0;$Ka=false;$M=array();if(!$_POST["delete"]){foreach($l
as$y=>$W){$r=bracket_escape($y);if(isset($_POST["fields"][$r])||$_FILES["fields-$r"]){$W=process_input($l[$y]);if($W!==null&&($_POST["clone"]||$W!==false))$M[idf_escape($y)]=($W!==false?$W:idf_escape($y));}}}if($_POST["delete"]||$M){$D=($_POST["clone"]?"INTO ".table($a)." (".implode(", ",array_keys($M)).")\nSELECT ".implode(", ",$M)."\nFROM ".table($a):"");if($_POST["all"]||($fh&&is_array($_POST["check"]))||$He){$E=($_POST["delete"]?driver()->delete($a,$ik):($_POST["clone"]?queries("INSERT $D$ik".driver()->insertReturning($a)):driver()->update($a,$M,$ik)));$ma=connection()->affected_rows;if(is_object($E))$ma+=$E->num_rows;}else{$Ka=count((array)$_POST["check"])>1&&driver()->begin();foreach((array)$_POST["check"]as$W){$hk="\nWHERE ".($Z?implode(" AND ",$Z)." AND ":"").where_check($W,$l);$E=($_POST["delete"]?driver()->delete($a,$hk,1):($_POST["clone"]?queries("INSERT".limit1($a,$D,$hk)):driver()->update($a,$M,$hk,1)));if(!$E)break;$ma+=connection()->affected_rows;}if($Ka&&$E&&!driver()->commit())$E=false;}}$Df=lang(101,$ma);if($_POST["clone"]&&$E&&$ma==1){$Ye=last_id($E);if($Ye)$Df=lang(100," $Ye");}queries_redirect(remove_from_uri($_POST["all"]&&$_POST["delete"]?"page|next":""),$Df,$E);if($Ka)driver()->rollback();if(!$_POST["delete"]){$Yg=(array)$_POST["fields"];edit_form($a,array_intersect_key($l,$Yg),$Yg,!$_POST["clone"],$j);page_footer();exit;}}elseif(!$_POST["import"]){$E=true;$ma=0;$Ka=count((array)$_POST["val"])>1&&driver()->begin();foreach((array)$_POST["val"]as$Ej=>$G){$M=array();foreach($G
as$t=>$W){$t=bracket_escape($t,true);$M[idf_escape($t)]=(preg_match('~char|text~',$l[$t]["type"])||$W!=""?adminer()->processInput($l[$t],$W):"NULL");}$E=driver()->update($a,$M," WHERE ".($Z?implode(" AND ",$Z)." AND ":"").where_check(bracket_escape($Ej,true),$l),($He||$fh?0:1)," ");if(!$E)break;$ma+=connection()->affected_rows;}if($Ka)$E=$E&&driver()->commit();queries_redirect(remove_from_uri(),lang(101,$ma),$E);if($Ka)driver()->rollback();}else{save_settings(array("format"=>$_POST["separator"]),"adminer_import");$dd=get_file("csv_file",true);if(!is_string($dd))$j=upload_error($dd);elseif(!preg_match('~~u',$dd))$j=lang(102);else{$kb=array_keys($l);$K=($_POST["separator"]=="csv"?",":($_POST["separator"]=="tsv"?"\t":";"));$Mb=parse_csv($dd,$K);$ma=count($Mb);driver()->begin();$H=array();foreach($Mb
as$t=>$Y){if(!$t&&!array_diff($Y,$kb)){$kb=$Y;$ma--;}else{$M=array();foreach($Y
as$p=>$hb)$M[idf_escape($kb[$p])]=($hb==""&&$l[$kb[$p]]["null"]?"NULL":q(csv_value($hb)));$H[]=$M;}}$E=(!$H||driver()->insertUpdate($a,$H,$fh));if($E)driver()->commit();queries_redirect(remove_from_uri("page|next"),lang(103,$ma),$E);driver()->rollback();}}}}$Si=adminer()->tableName($R);if(is_ajax()){page_headers();ob_start();}else
page_header(lang(58).": $Si",$j,array(),"",(!$l&&support("table")),($l?doc_link(array(JUSH=>driver()->tableHelp($a,is_view($R)))):""));$M=null;if(isset($Jh["insert"])||!support("table")){$M="";foreach((array)$_GET["where"]as$W){$X=$W["val"];if(is_array($X))$X=(count($X)==1&&preg_match('~^val-(.*)~s',reset($X),$x)?$x[1]:"");if($W["col"]!=""&&$X!=""&&($W["op"]=="="||(!$W["op"]&&(is_array($W["val"])||!preg_match('~[_%]~',$X)))))$M
.="&set[".url_escape(bracket_escape($W["col"]))."]=".url_escape($X);}}adminer()->selectLinks($R,$M);if(!$e&&support("table"))echo"<p class='error'>".lang(104)."\n";else{echo"<form action='' id='form'>\n","<div hidden>";hidden_fields_get();echo(DB!=""?input_hidden("db",DB).(isset($_GET["ns"])?input_hidden("ns",$_GET["ns"]):""):""),input_hidden("select",$a),"</div>\n";adminer()->selectColumnsPrint($J,$e);adminer()->selectSearchPrint($Z,$Rh,$qe,$R);adminer()->selectOrderPrint($og,$pg,$qe);adminer()->selectLimitPrint($v);if($Zi!==null)adminer()->selectLengthPrint($Zi);adminer()->selectActionPrint($qe);echo"</form>\n";foreach((array)$_GET["where"]as$W){if($W["op"]=="SQL"&&!in_array($_SERVER["HTTP_SEC_FETCH_SITE"],array("","same-origin"))){echo"<p class='error'>".lang(94).' '.lang(95)."\n";page_footer();exit;}}$A=$_GET["page"];$yd=null;if($A=="last"){$yd=get_val(count_rows($a,$Z,$He,$o));$A=floor(max(0,intval($yd)-1)/$v);}$Uh=$J;$Jd=$o;if(!$Uh){$Uh[]="*";$Db=convert_fields($e,$l,$J);if($Db)$Uh[]=substr($Db,2);}foreach($J
as$t=>$W){$k=$l[idf_unescape($W)];if($k&&($wa=convert_field($k)))$Uh[$t]="$wa AS $W";}if(JUSH=="pgsql"||JUSH=="mssql"){foreach((array)$_GET["columns"]as$t=>$W){if(isset($Uh[$t])&&$W["fun"])$Uh[$t].=" AS ".idf_escape(apply_sql_function($W["fun"],($W["col"]!=""?$W["col"]:"*")));}}if(!$He&&$Hj){foreach($Hj
as$t=>$W){$Uh[]=idf_escape($t);if($Jd)$Jd[]=idf_escape($t);}}$E=driver()->select($a,$Uh,$Z,$Jd,$og,$v,$A,true);if(!is_object($E))echo"<p class='error'>".(adminer()->error()?:lang(26))."\n";else{if(JUSH=="mssql"&&$A)$E->seek($v*$A);$Bc=array();$H=array();while($G=$E->fetch_assoc()){if($A&&JUSH=="oracle")unset($G["RNUM"]);$H[]=$G;}$Td=($v&&(support("cursor")?$_GET["next"]!="":count($H)>=$v));if(is_ajax()&&$Td)header("X-Next-Page: ".pagination_href($A+1));if($_GET["modify"]&&$H){$xf=max_input_vars(count($H[0])+1,20);echo($xf&&count($H)>$xf?"<p class='error'>".max_input_vars_error()."\n":"");}echo"<form action='' method='post' enctype='multipart/form-data'".on_upload_progress($Lj).">\n";if($_GET["page"]!="last"&&$v&&$o&&$He&&JUSH=="sql")$yd=get_val(" SELECT FOUND_ROWS()");if(!$H)echo"<p class='message'>".lang(16)."\n";else{$Ha=adminer()->backwardKeys($a,$Si);$Hh=array();reset($J);foreach($H[0]as$t=>$W){if(!isset($Hj[$t])){$W=idx($_GET["columns"],key($J))?:array();$Hh[$t]=array("fun"=>$W["fun"],"col"=>($J?$W["col"]:$t));next($J);}}echo"<div class='scrollable'>","<table id='table' class='nowrap checkable odds'".on('click','tableClick').on('dblclick','tableClick').on('keydown','editingKeydown').">\n","<thead><tr>".(!$o&&$J?"":"<td class='hover check sticky'><input type='checkbox' id='all-page' class='jsonly' title='".lang(105)."'".on('click','formCheck','^check').">");$Tf=array();$vh=1;foreach($Hh
as$t=>$W){$k=$l[$W["col"]];$y=($k?adminer()->fieldName($k,$vh):($W["fun"]?($W["col"]!=""?h($W["col"]):"*"):h($t)));if($y!=""){$vh++;$Tf[$t]=$y;$d=idf_escape($t);$fe=remove_from_uri('(order|desc)[^=]*|page|next').'&order[0]='.url_escape($t);$ac="&desc[0]=1";$wi=preg_replace('~ DESC( NULLS LAST)?$~','',$og[0]);$yi=($wi==$d||$wi==$t);echo"<th id='th[".h(bracket_escape($t))."]'".($yi?" aria-sort='".($wi==$og[0]?"ascending":"descending")."'":"").">";$Fd=apply_sql_function(h($W["fun"]),$y);$xi=isset($k["privileges"]["order"])||$W["fun"];echo($xi?"<a href='".h($fe.($yi&&$wi==$og[0]?$ac:''))."'>$Fd</a>":$Fd),doc_link(array(JUSH=>$k["doc"]));$Cf=($xi?"<a href='".h($fe.$ac)."' title='".lang(106)."' class='text'> ↓</a>":'');if(!$W["fun"]&&isset($k["privileges"]["where"]))$Cf
.="<a href='#fieldset-search' title='".lang(52)."' class='text jsonly'".on('click','selectSearch',$t)."> =</a>";echo($Cf?"<span class='column'>$Cf</span>":"");}}$cf=array();if($_GET["modify"]){foreach($H
as$G){foreach($G
as$t=>$W)$cf[$t]=max($cf[$t],min(40,utf8_length((string)$W)));}}$be=array();$ae=array();foreach((array)$_GET["where"]as$W){$W+=array("col"=>"","op"=>"","val"=>"");$hb=$W["col"];$Qh=$W["val"];if(!is_array($Qh)&&($Qh!=""||preg_match('~NULL$~',$W["op"]))&&(!$W["op"]||in_array($W["op"],adminer()->operators($R)))){$ef=strtr(preg_quote($Qh),array("%"=>".*?","_"=>"."));$Qg=array("LIKE %%"=>$ef,"ILIKE %%"=>$ef,"REGEXP"=>$Qh)+(JUSH=="pgsql"?array("~"=>$Qh,"~*"=>$Qh):array())+($hb!=""?array():array("="=>'^'.preg_quote($Qh).'\z',"IN"=>'^(?:'.implode("|",array_map('preg_quote',array_map('trim',explode(",",$Qh)))).')\z',"LIKE"=>"^$ef\\z","ILIKE"=>"^$ef\\z","FIND_IN_SET"=>'(?<=^|,)'.preg_quote($Qh).'(?=,|\z)',));foreach(($hb!=""?array($hb=>$l[$hb]):$l)as$y=>$k){if($hb!=""||is_searchable($k,$W)){$jg=$W["op"]?:(!preg_match('~'.text_type().'~',$k["type"])?"IN":(preg_match('~%~',$Qh)?"LIKE":"LIKE %%"));if(isset($Qg[$jg])){$bb=preg_match('~^ILIKE|\*$~',$jg)||($jg!="~"&&preg_match('~^(sql|mssql|sqlite)$~',JUSH));$be[$y][]="(?".($bb?"i":"").":$Qg[$jg])";}elseif($jg=="IS NULL"&&$hb=="")$ae[$y]=true;}}}}echo($Ha?"<th>".lang(107):"")."<tbody>\n";if(is_ajax())ob_end_clean();foreach(adminer()->rowDescriptions($H,$vd)as$Rf=>$G){$Dj=unique_array($H[$Rf],$qe);if(!$Dj){$Dj=array();foreach($H[$Rf]as$t=>$W){if(!in_array(idx(idx($Hh,$t,array()),"fun"),driver()->grouping))$Dj[$t]=$W;}}$Ej="";$p=0;foreach($Dj
as$t=>$W){$Gh=idx($Hh,$t,array());if($J&&$Gh&&!isset($l[$Gh["col"]]))continue;$Fd=idx($Gh,"fun","");$hb=($Fd?$Gh["col"]:$t);$k=(array)$l[$hb];$Ge=is_blob($k);if(!$Fd&&strlen($W)>64&&driver()->md5(idf_escape($hb),$k)){$Fd="md5";$W=md5($Ge?(string)driver()->value($W,$k):$W);}if($Fd){$Ej
.="&fun[$p]=".url_escape($Fd)."&col[$p]=".url_escape($hb).($W!==null?"&val[$p]=".url_escape($W===false?"f":$W):"");$p++;}else$Ej
.="&".($W!==null?"where[".url_escape(bracket_escape($hb))."]=".url_escape($W===false?"f":$W):"null[]=".url_escape($hb));}echo"<tr>".(!$o&&$J?"":"<td class='hover check sticky'>".($He||information_schema(DB)?"":"<a href='".h(ME."edit=".url_escape($a).$Ej)."' class='edit'>".lang(108)."</a> ").checkbox("check[]",substr($Ej,1),in_array(substr($Ej,1),(array)$_POST["check"])));foreach($G
as$t=>$W){if(isset($Tf[$t])){$Fd=$Hh[$t]["fun"];$hb=$Hh[$t]["col"];$k=(array)$l[$t];if($W!=""&&(!isset($Bc[$t])||$Bc[$t]!=""))$Bc[$t]=(is_mail($W)?$Tf[$t]:"");$w="";if(is_blob($k)&&$W!="")$w=ME.'download='.url_escape($a).'&field='.url_escape($t).$Ej;if(!$w&&$W!==null){foreach((array)$vd[$t]as$ud){if(count($vd[$t])==1||end($ud["source"])==$t){$w="";foreach($ud["source"]as$p=>$zi)$w
.=where_link($p,$ud["target"][$p],$H[$Rf][$zi]);$w=($ud["db"]!=""?preg_replace('~&db=[^&]*~',"&db=".url_escape($ud["db"]),ME):ME).'select='.url_escape($ud["table"]).$w;if($ud["ns"]!="")$w=preg_replace('~&ns=[^&]*~',"&ns=".url_escape($ud["ns"]),$w);if(count($ud["source"])==1)break;}}}if($Fd=="count"&&$hb==""){$w=ME."select=".url_escape($a);$p=0;foreach((array)$_GET["where"]as$V){if(!array_key_exists($V["col"],$Dj))$w
.=where_link($p++,$V["col"],$V["val"],$V["op"]);}foreach($Dj
as$Me=>$V){if(idx(idx($Hh,$Me,array()),"fun")){$w="";break;}$w
.=where_link($p++,$Me,$V);}}$ge=select_value($W,$w,$k,$Zi,($Fd?array():idx($be,$t,array())));if($W===null&&!$Fd&&isset($ae[$t]))$ge="<mark>$ge</mark>";$r=bracket_escape($Ej);$q=h("val[$r][".bracket_escape($t)."]");$ah=idx(idx($_POST["val"],$r),bracket_escape($t));$Kj=idx($k["privileges"],"update")&&!is_identity_always($k);$yc=$H[$Rf][$t];$zc=!is_array($G[$t])&&!is_blob($k)&&is_utf8($yc)&&!$Fd&&!$k["generated"]&&$Kj;$T=($Fd=="min"||$Fd=="max"?$l[$hb]["type"]:$k["type"]);$Yi=preg_match('~text|json|lob~',$T);$Ie=preg_match(number_type(),$T)||preg_match('~^(avg|ceil|char_length|count|count distinct|floor|len|length|round|sum|time_to_sec)$~',$Fd);echo"<td id='$q'".($Ie&&($W===null||is_numeric(strip_tags($ge))||$T=="money")?" class='number'":"");if(($_GET["modify"]&&$zc&&$yc!==null)||$ah!==null){$Nd=h($ah!==null?$ah:$yc);echo">".($Yi?"<textarea name='$q' cols='30' rows='".(substr_count($yc,"\n")+1)."'>\n$Nd</textarea>":"<input name='$q' value='$Nd' size='$cf[$t]'>");}else{$nf=strpos($ge,"<i>…</i>");$xc=adminer()->editVal($yc,$k);echo($Kj?" data-text='".($nf?2:($Yi?1:0))."'".($zc?"":" data-warning='".lang(109)."'").($zc&&!$nf&&html_entity_decode(strip_tags($ge),ENT_QUOTES,"UTF-8")!==$xc?" data-value='".h($xc)."'":""):"").">$ge";}}}if($Ha)echo"<td>";adminer()->backwardKeysPrint($Ha,$H[$Rf]);echo"</tr>\n";}if(is_ajax())exit;echo"</table>\n","</div>\n";}if(!is_ajax()){$la=get_settings("adminer_import");if($H||$A||$Td){$Rc=true;if($_GET["page"]!="last"){if(!$v||(count($H)<$v&&($H||!$A)))$yd=($A?$A*$v:0)+count($H);elseif(JUSH!="sql"||!$He){$yd=($He?null:found_rows($R,$Z));$Rc=!driver()->hasEstimatedRows();if($yd===null||(!$Rc&&$yd<max(1e4,2*($A+1)*$v))){$yd=first(slow_query(count_rows($a,$Z,$He,$o)));$Rc=true;}}}if(!support("cursor"))$Td=(($yd===false?count($H)+1:$yd-$A*$v)>$v);$Bg=($v&&($Td||$A));if($Bg)echo($Td?'<p><a href="'.h(pagination_href($A+1)).'" class="loadmore"'.on('click','selectLoadMore',lang(110)).'>'.lang(111).'</a>':''),"\n";echo"<div class='footer'><div>\n";if($Bg){$wf=($yd===false?$A+($H?(count($H)>=$v?2:1):0):floor(($yd-1)/$v));echo"<fieldset><legend>".lang(112)."</legend>";if(!support("cursor")){echo
pagination(0,$A).($A>5?" …":"");for($p=max(1,$A-4);$p<min($wf,$A+5);$p++)echo
pagination($p,$A);if($wf>0)echo($A+5<$wf?" …":""),($Rc&&$yd!==false?pagination($wf,$A):" <a href='".h(remove_from_uri("page")."&page=last")."' title='~$wf'>".lang(113)."</a>");}else
echo
pagination(0,$A).($A>1?" …":""),($A?pagination($A,$A):""),($Td?pagination($A+1,$A)." …":"");echo"</fieldset>\n";}echo"<fieldset>","<legend>".lang(114)."</legend>";$gc=($Rc?"":"~ ").$yd;$Se=($yd!==false?($Rc?"":"~ ").lang(115,$yd):"");echo
checkbox("all",1,0,$Se,on('click','countRows',$gc))."\n","</fieldset>\n";if(adminer()->selectCommandPrint())echo'<fieldset',($_GET["modify"]?'':" title='".lang(116)."'"),'>
<legend><a href=\'',h($_GET["modify"]?remove_from_uri("modify"):relative_uri()."&modify=1"),'\'>',lang(117),'</a></legend><div>
<input type=\'submit\' id=\'save\' value=\'',lang(18),'\'',($_GET["modify"]||$_POST["val"]?'':" class='jsonly' disabled"),'>
</div></fieldset>

<fieldset><legend>',lang(118),' <span id="selected"></span></legend><div>
<input type=\'submit\' name=\'edit\' value=\'',lang(14),'\'>
<input type=\'submit\' name=\'clone\' value=\'',lang(119),'\'>
<input type=\'submit\' name=\'delete\' value=\'',lang(22),'\'',confirm(),'>
</div></fieldset>
';$wd=adminer()->dumpFormat();foreach((array)$_GET["columns"]as$d){if($d["fun"]||($d["col"]!=""&&!isset($l[$d["col"]]))){unset($wd['sql']);break;}}if($wd){print_fieldset("export",lang(120)." <span id='selected2'></span>");$yg=adminer()->dumpOutput();echo($yg?html_select("output",$yg,$la["output"])." ":""),html_select("format",$wd,$la["format"])," <input type='submit' name='export' value='".lang(120)."'>\n","</div></fieldset>\n";}adminer()->selectEmailPrint(array_filter($Bc,'strlen'),$e);echo"</div></div>\n";}if(adminer()->selectImportPrint())echo"<p>","<a href='#import' class='toggle'>".lang(121)."</a>","<span id='import'".($_POST["import"]?"":" class='hidden'").">: ",($Lj?input_hidden(ini_get("session.upload_progress.name"),$Lj):""),file_input(" name='csv_file'"," ".html_select("separator",array("csv"=>"CSV,","csv;"=>"CSV;","tsv"=>"TSV"),$la["format"])." <input type='submit' name='import' value='".lang(121)."'>".($Lj?" <progress class='jsonly hidden' max='1' value='0'></progress>":"")),"</span>";echo
input_token(),"</form>\n",(!$o&&$J?"":script("tableCheck();"));}}}if(is_ajax()){ob_end_clean();exit;}}elseif(isset($_GET["script"])){if($_GET["script"]=="kill"){if(!$j)connection()->query("KILL ".number($_POST["kill"]));}elseif(list($Q,$q,$y)=adminer()->_foreignColumn(column_foreign_keys($_GET["source"]),$_GET["field"])){$v=11;$Qc=(preg_match('~^[0-9]+$~',$_GET["value"])?"$q = $_GET[value]":"");$E=connection()->query("SELECT".limit("$q, $y FROM ".table($Q)," WHERE ".($Qc?"$Qc OR ":"")."$y LIKE ".q("$_GET[value]%")." ORDER BY ".($Qc?"CASE WHEN $Qc THEN 0 ELSE 1 END, ":"")."2",$v));for($p=1;($G=$E->fetch_row())&&$p<$v;$p++)echo"<a href='".h(ME."edit=".url_escape($Q)."&where[".url_escape(bracket_escape(idf_unescape($q)))."]=".url_escape($G[0]))."'>".h($G[1])."</a><br>\n";if($G)echo"...\n";}exit;}else{page_header(lang(74),"",false);if(adminer()->homepage()){echo"<form action='' method='post'>\n","<p>".lang(122).": <input type='search' name='query' value='".h($_POST["query"])."'> <input type='submit' value='".lang(52)."'>\n";if($_POST["query"]!="")search_tables();echo"<div class='scrollable'>\n","<table class='nowrap checkable odds'".on('click','tableClick').on('dblclick','tableClick').">\n",'<thead><tr>','<td class="hover"><input id="check-all" type="checkbox" class="jsonly"'.on('click','formCheck','^tables\[').'>','<th>'.lang(123),'<th>'.lang(124),"<tbody>\n";foreach(table_status()as$Q=>$G){$y=adminer()->tableName($G);if($y!="")echo'<tr><td class="hover">'.checkbox("tables[]",$Q,in_array($Q,(array)$_POST["tables"],true)),"<th><a href='".h(ME).'select='.url_escape($Q)."'>$y</a>","<td align='right'><a href='".h(ME."edit=").url_escape($Q)."'>".format_status($G,"Rows")."</a>";}echo"</table>\n","</div>\n","</form>\n",script("tableCheck();");adminer()->pluginsLinks();}}page_footer();