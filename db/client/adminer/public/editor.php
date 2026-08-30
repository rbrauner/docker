<?php
/** Adminer Editor - Compact database editor
* @link https://www.adminer.org/
* @author Jakub Vrana, https://www.vrana.cz/
* @copyright 2009 Jakub Vrana
* @license https://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
* @license https://www.gnu.org/licenses/gpl-2.0.html GNU General Public License, version 2 (one or other)
* @version 6.0.1
*/namespace
Adminer;const
VERSION="6.0.1";error_reporting(24575);set_error_handler(function($jc,$lc){return!!preg_match('~^Undefined (array key|offset|index)~',$lc);},E_WARNING|E_NOTICE);$Fc=!preg_match('~^(unsafe_raw)?$~',ini_get("filter.default"));if($Fc||ini_get("filter.default_flags")){foreach(array('_GET','_POST','_COOKIE','_SERVER')as$W){$ki=filter_input_array(constant("INPUT$W"),FILTER_UNSAFE_RAW);if($ki)$$W=$ki;}}$_COOKIE=array_filter($_COOKIE,'is_scalar');if(function_exists("mb_internal_encoding"))mb_internal_encoding("8bit");function
connection($g=null){return($g?:Db::$instance);}function
adminer(){return
Adminer::$instance;}function
driver(){return
Driver::$instance;}function
connect(){$vb=adminer()->credentials();$F=Driver::connect($vb[0],$vb[1],$vb[2]);return(is_object($F)?$F:null);}function
idf_unescape($q){if(!preg_match('~^[`\'"[]~',$q))return$q;$ne=substr($q,-1);return
str_replace($ne.$ne,$ne,substr($q,1,-1));}function
q($O){return
connection()->quote($O);}function
idx($sa,$t,$i=null){return($sa&&array_key_exists($t,$sa)?$sa[$t]:$i);}function
number($W){return
preg_replace('~[^0-9]+~','',$W);}function
int_type(){return'(tiny|small|medium|big)?int(eger|\d)?';}function
number_type(){return'(^('.int_type().'|decimal|numeric|number|real|(binary_|half_|scaled_)?float\d?|(binary_)?double( precision)?|(small)?money)$)';}function
text_type(){return'char|text'.(JUSH=="sql"?'|enum|set':'');}function
is_searchable(array$k,array$W){if(!isset($k["privileges"]["where"]))return
false;$S=$k["type"];$Mg=$W["val"];$Fa='binary$|bytea|raw|image|bfile|^vector$'.(JUSH=="mssql"?'|^timestamp$':'|^bit').(JUSH=="oracle"?'|^blob|^long|rowid':'');if(preg_match("~$Fa~",$S))return
false;if(preg_match(number_type(),$S)){$z='-?\d+(\.\d+)?';return(bool)preg_match('~^'.$z.(preg_match('~IN$~',$W["op"])?"( *, *$z)*":'').'$~',$Mg);}if(preg_match('~^(small)?date|^timestamp~',$S))return(bool)preg_match('~^\d+-\d+-\d+~',$Mg);if(preg_match('~^time~',$S))return(bool)preg_match('~^\d+:\d+~',$Mg);if(preg_match('~^bool~',$S)||(JUSH=="mssql"&&$S=="bit"))return(bool)preg_match('~^(t|f|true|false|[01])$~i',$Mg);return
true;}function
remove_slashes(array$Y,$Fc=false){$F=array();foreach($Y
as$t=>$W)$F[stripslashes($t)]=(is_array($W)?remove_slashes($W,$Fc):($Fc?$W:stripslashes($W)));return$F;}function
bracket_escape($q,$za=false){static$Th=array(':'=>':1',']'=>':2','['=>':3','"'=>':4','='=>':5');return
strtr($q,($za?array_flip($Th):$Th));}function
url_escape($O){static$Th=array();if(!$Th){$Th=array(' '=>'+');foreach(str_split("\"'<>#%&+=?".ini_get("arg_separator.input"))as$Na)$Th[$Na]=sprintf('%%%02X',ord($Na));for($o=0;$o<256;$o++){if($o<32||$o>126)$Th[chr($o)]=sprintf('%%%02X',$o);}}return
strtr((string)$O,$Th);}function
min_version($Bi,$Ce="",$g=null){$g=connection($g);$Zg=$g->server_info;if($Ce&&preg_match('~([\d.]+)-MariaDB~',$Zg,$x)){$Zg=$x[1];$Bi=$Ce;}return$Bi&&version_compare($Zg,$Bi)>=0;}function
charset(Db$f){return(min_version("5.5.3",0,$f)?"utf8mb4":"utf8");}function
ini_set($yf,$X){return(function_exists('ini_set')?\ini_set($yf,$X):false);}function
ini_bool($Ld){$W=ini_get($Ld);return(preg_match('~^(on|true|yes)$~i',$W)||(int)$W);}function
ini_bytes($Ld){$W=ini_get($Ld);switch(strtolower(substr($W,-1))){case'g':$W=(int)$W*1024;case'm':$W=(int)$W*1024;case'k':$W=(int)$W*1024;}return$W;}function
max_input_vars($G,$Gf){$Ge=(int)ini_get("max_input_vars");return($Ge?(int)floor(($Ge-$Gf)/$G):0);}function
max_input_vars_error(){$Ld="max_input_vars";return
lang(0,"<b>$Ld = ".ini_get($Ld)."</b>");}function
sid(){static$F;if($F===null)$F=(SID&&!($_COOKIE&&ini_bool("session.use_cookies")));return$F;}function
set_password($Ai,$K,$U,$B){$_SESSION["pwds"][$Ai][$K][$U]=($_COOKIE["adminer_key"]&&is_string($B)?array(encrypt_string($B,$_COOKIE["adminer_key"])):$B);}function
get_password(){$F=get_session("pwds");if(is_array($F))$F=($_COOKIE["adminer_key"]?decrypt_string($F[0],$_COOKIE["adminer_key"]):false);return$F;}function
get_val($D,$k=0,$hb=null){$hb=connection($hb);$E=$hb->query($D);if(!is_object($E))return
false;$G=$E->fetch_row();return($G?$G[$k]:false);}function
get_vals($D,$d=0){$F=array();$E=connection()->query($D);if(is_object($E)){while($G=$E->fetch_row())$F[]=$G[$d];}return$F;}function
get_key_vals($D,$g=null,$ch=true){$g=connection($g);$F=array();$E=$g->query($D);if(is_object($E)){while($G=$E->fetch_row()){if($ch)$F[$G[0]]=$G[1];else$F[]=$G[0];}}return$F;}function
get_rows($D,$g=null,$j="<p class='error'>"){$hb=connection($g);$F=array();$E=$hb->query($D);if(is_object($E)){while($G=$E->fetch_assoc())$F[]=$G;}elseif(!$E&&!$g&&$j&&(defined('Adminer\PAGE_HEADER')||$j=="-- "))echo$j.adminer()->error()."\n";return$F;}function
unique_array($G,array$s){foreach($s
as$r){if(preg_match("~^(PRIMARY|UNIQUE)$~",$r["type"])&&!$r["partial"]){$F=array();foreach($r["columns"]as$t){if(!isset($G[$t]))continue
2;$F[$t]=$G[$t];}return$F;}}}function
escape_key($t){if(preg_match('(^([\w(]+)('.str_replace("_",".*",preg_quote(idf_escape("_"))).')([ \w)]+)$)',$t,$x))return$x[1].idf_escape(idf_unescape($x[2])).$x[3];return
idf_escape($t);}function
where(array$Z,array$l=array()){$F=array();foreach((array)$Z["where"]as$t=>$W){$t=bracket_escape($t,true);$d=escape_key($t);$k=idx($l,$t,array());$Ac=$k["type"];$Wd=$k&&(is_blob($k)||preg_match('~binary~',$Ac));$F[]=$d.($Wd&&!is_utf8($W)?" = ".driver()->quoteBinary($W):(JUSH=="sql"&&$Ac=="json"?" = CAST(".q($W)." AS JSON)":(JUSH=="pgsql"&&preg_match('~^jsonb?$~',$k["full_type"])?"::jsonb = ".q($W)."::jsonb":(JUSH=="sql"&&is_numeric($W)&&preg_match('~\.~',$W)?" LIKE ".q($W):(JUSH=="mssql"&&strpos($Ac,"datetime")===false?" LIKE ".q(preg_replace('~[_%[]~','[\0]',$W)):" = ".unconvert_field($k,q($W)))))));if(JUSH=="sql"&&preg_match('~char|text~',$Ac)&&preg_match("~[^ -@]~",$W))$F[]="$d = ".q($W)." COLLATE ".charset(connection())."_bin";}foreach((array)$Z["null"]as$t)$F[]=escape_key($t)." IS NULL";return
implode(" AND ",$F);}function
where_columns(array$l){$F=array();foreach((array)$_GET["null"]as$t)$F[$t]=true;foreach((array)$_GET["where"]as$t=>$W){$t=bracket_escape($t,true);foreach($l
as$y=>$k){if($t==$y||strpos($t,idf_escape($y))!==false)$F[$y]=true;}}return$F;}function
where_check($W,array$l=array()){parse_str($W,$Pa);remove_slashes(array(&$Pa));return
where($Pa,$l);}function
where_link($o,$d,$X,$wf="="){$vf=($X!==null?$wf:"IS NULL");return"&where[$o][col]=".url_escape($d).($vf!=first(adminer()->operators())?"&where[$o][op]=".url_escape($vf):"")."&where[$o][val]=".url_escape($X);}function
convert_fields(array$e,array$l,array$I=array()){$F="";foreach($e
as$t=>$W){if($I&&!in_array(idf_escape($t),$I))continue;$ta=convert_field($l[$t]);if($ta)$F
.=", $ta AS ".idf_escape($t);}return$F;}function
cookie_path(){return
strtr(preg_replace('~\?.*~','',$_SERVER["REQUEST_URI"]),array(";"=>"%3B",","=>"%2C"));}function
cookie($y,$X,$te=2592000){header("Set-Cookie: $y=".rawurlencode($X).($te?"; expires=".gmdate("D, d M Y H:i:s",time()+$te)." GMT":"")."; path=".cookie_path().(HTTPS?"; secure":"").($y=="adminer_import"?"":"; HttpOnly")."; SameSite=lax",false);}function
get_url($qi,$nb){$http_response_header=null;$kc=array();set_error_handler(function($jc,$j)use(&$kc){$kc[]=preg_replace('~^file_get_contents\([^)]*\):\s*~','',$j);return
true;});$F=file_get_contents($qi,false,$nb);restore_error_handler();$rd=(function_exists('http_get_last_response_headers')?http_get_last_response_headers():$http_response_header);return
array($F,(preg_match('~^HTTP/[\d.]+ (\d+)~',idx($rd,0,''),$x)?$x[1]:''),(array)$rd,($F===false?implode("\n",$kc):''),);}function
get_settings($qb){parse_str($_COOKIE[$qb],$dh);return$dh;}function
get_setting($t,$qb="adminer_settings",$i=null){return
idx(get_settings($qb),$t,$i);}function
save_settings(array$dh,$qb="adminer_settings"){$X=http_build_query($dh+get_settings($qb));cookie($qb,$X);$_COOKIE[$qb]=$X;}function
restart_session(){if(!ini_bool("session.use_cookies")&&(!function_exists('session_status')||session_status()==PHP_SESSION_NONE))session_start();}function
stop_session($Nc=false){$si=ini_bool("session.use_cookies");if(!$si||$Nc){session_write_close();if($si&&ini_set("session.use_cookies",'0')===false)session_start();}}function&get_session($t){return$_SESSION[$t][DRIVER][SERVER][$_GET["username"]];}function
set_session($t,$W){$_SESSION[$t][DRIVER][SERVER][$_GET["username"]]=$W;}function
auth_url($Ai,$K,$U,$h=null){$pi=remove_from_uri(implode("|",array_keys(SqlDriver::$drivers))."|username|ext|".($h!==null?"db|":"").($Ai=='mssql'||$Ai=='pgsql'?"":"ns|").session_name());preg_match('~([^?]*)\??(.*)~',$pi,$x);return"$x[1]?".(sid()?SID."&":"").($_GET["ext"]?"ext=".url_escape($_GET["ext"])."&":"").($Ai!="server"||$K!=""?url_escape($Ai)."=".url_escape($K)."&":"")."username=".url_escape($U).($h!=""?"&db=".url_escape($h):"").($x[2]?"&$x[2]":"");}function
is_ajax(){return($_SERVER["HTTP_X_REQUESTED_WITH"]=="XMLHttpRequest");}function
redirect($ze,$Re=null){if($Re!==null){restart_session();$_SESSION["messages"][preg_replace('~^[^?]*~','',($ze!==null?$ze:$_SERVER["REQUEST_URI"]))][]=$Re;}if($ze!==null){if($ze=="")$ze=".";header("Location: $ze");exit;}}function
query_redirect($D,$ze,$Re,$yg=true,$rc=true,$xc=false,$Jh=""){if($rc){$qh=microtime(true);$xc=!connection()->query($D);$Jh=format_time($qh);}$nh=($D?adminer()->messageQuery($D,$Jh,$xc):"");if($xc){adminer()->error
.=adminer()->error().$nh.script("messagesPrint();")."<br>";return
false;}if($yg)redirect($ze,$Re.$nh);return
true;}class
Queries{static$queries=array();static$start=0;}function
queries($D){if(!Queries::$start)Queries::$start=microtime(true);Queries::$queries[]=(driver()->delimiter!=';'?$D:(preg_match('~;$~',$D)?"DELIMITER ;;\n$D;\nDELIMITER ":$D).";");return
connection()->query($D);}function
apply_queries($D,array$R,$mc='Adminer\table'){foreach($R
as$P){if(!queries("$D ".$mc($P)))return
false;}return
true;}function
queries_redirect($ze,$Re,$yg){$sg=implode("\n",Queries::$queries);$Jh=format_time(Queries::$start);return
query_redirect($sg,$ze,$Re,$yg,false,!$yg,$Jh);}function
format_time($qh){return
lang(1,max(0,microtime(true)-$qh));}function
relative_uri($pi=''){return
preg_replace_callback('~^[^?]*~',function($x){return
str_replace(":","%3A",$x[0]);},preg_replace('~^[^?]*/([^?]*)~','\1',($pi?:$_SERVER["REQUEST_URI"])));}function
remove_from_uri($Mf=""){return
substr(preg_replace("~(?<=[?&])($Mf".(SID?"":"|".session_name()).")=[^&]*&~",'',relative_uri()."&"),0,-1);}function
get_files($y,$Db=false){$Bc=$_FILES[$y];if(!$Bc)return
null;foreach($Bc
as$t=>$W)$Bc[$t]=(array)$W;$F=array();foreach($Bc["error"]as$t=>$j){if($j)return$j;$m=$Bc["name"][$t];$Qh=$Bc["tmp_name"][$t];$mb=file_get_contents($Db&&preg_match('~\.gz$~',$m)?"compress.zlib://$Qh":$Qh);if($Db){$qh=substr($mb,0,3);if(function_exists("iconv")&&preg_match("~^\xFE\xFF|^\xFF\xFE~",$qh))$mb=iconv("utf-16","utf-8",$mb);elseif($qh=="\xEF\xBB\xBF")$mb=substr($mb,3);}$F[]=array($m,$mb);}return$F;}function
get_file($t,$Db=false,$Gb=""){$Ec=get_files($t,$Db);if(!is_array($Ec))return$Ec;$F='';foreach($Ec
as$Bc){$mb=$Bc[1];$F
.=$mb;if($Gb)$F
.=(preg_match("($Gb\\s*\$)",$mb)?"":$Gb)."\n\n";}return$F;}function
upload_error($j){$Le=($j==UPLOAD_ERR_INI_SIZE?ini_get("upload_max_filesize"):0);return($j?lang(2).($Le?" ".lang(3,$Le):""):lang(4));}function
is_utf8($W){return(preg_match('~~u',$W)&&!preg_match('~[\0-\x8\xB\xC\xE-\x1F]~',$W));}function
format_number($W){return
strtr(number_format($W,0,".",lang(5)),preg_split('~~u',lang(6),-1,PREG_SPLIT_NO_EMPTY));}function
format_status(array$Q,$t){$W=idx($Q,$t,'?');if(!is_numeric($W))return
h($W);if($W<0)return'?';$pa=($t=="Rows"&&(JUSH=="sqlite"||$Q["Engine"]==(JUSH=="pgsql"?"table":"InnoDB")));return($pa?"~ ":"").format_number($W);}function
friendly_url($W){return
preg_replace('~\W~i','-',$W);}function
table_status1($P,$yc=false){$F=table_status($P,$yc);return($F?reset($F):array("Name"=>$P));}function
column_foreign_keys($P){$F=array();foreach(adminer()->foreignKeys($P)as$Rc){foreach($Rc["source"]as$W)$F[$W][]=$Rc;}return$F;}function
fields_from_edit(){$F=array();foreach((array)$_POST["field_keys"]as$t=>$W){if($W!=""){$W=bracket_escape($W);$_POST["function"][$W]=$_POST["field_funs"][$t];$_POST["fields"][$W]=$_POST["field_vals"][$t];}}foreach((array)$_POST["fields"]as$t=>$W){$y=bracket_escape($t,true);$F[$y]=array("field"=>$y,"full_type"=>"","type"=>"","privileges"=>array("insert"=>1,"update"=>1,"where"=>1,"order"=>1),"null"=>true,"auto_increment"=>($y==driver()->primary),);}return$F;}function
dump_headers($_d,$cf=false){$F=adminer()->dumpHeaders($_d,$cf);$Hf=$_POST["output"];if($Hf!="text"||$F=="tar"){$eb=($Hf!="text"&&$Hf!="file"&&preg_match('~^[0-9a-z]+$~',$Hf)?".$Hf":"");header("Content-Disposition: attachment; filename=".adminer()->dumpFilename($_d).".$F$eb");}session_write_close();if(!ob_get_level())ob_start(null,4096);ob_flush();flush();return$F;}function
dump_csv(array$G){$ci=$_POST["format"]=="tsv";foreach($G
as$t=>$W){if(preg_match('~["\n]|^0[^.]|\.\d*0$|'.($ci?'\t':'[,;]|^$').'~',$W))$G[$t]='"'.str_replace('"','""',$W).'"';}echo
implode(($_POST["format"]=="csv"?",":($ci?"\t":";")),$G)."\r\n";}function
parse_csv($yb,$J){$F=array();preg_match_all('~(?>"[^"]*"|[^"\r\n]+)+~',$yb,$Ee);foreach($Ee[0]as$G){preg_match_all("~((?>\"[^\"]*\")+|[^$J]*)$J~",$G.$J,$Fe);$F[]=$Fe[1];}return$F;}function
csv_value($W){return(preg_match('~^".*"$~s',$W)?str_replace('""','"',substr($W,1,-1)):$W);}function
apply_sql_function($n,$d){return($n?($n=="unixepoch"?"DATETIME($d, '$n')":($n=="count distinct"?"COUNT(DISTINCT ":strtoupper("$n("))."$d)"):$d);}function
get_temp_dir(){return
ini_get("upload_tmp_dir")?:sys_get_temp_dir();}function
file_open_lock($m){if(is_link($m))return;$Vc=@fopen($m,"c+");if(!$Vc)return;@chmod($m,0660);if(!flock($Vc,LOCK_EX)){fclose($Vc);return;}return$Vc;}function
file_write_unlock($Vc,$Ab){rewind($Vc);fwrite($Vc,$Ab);ftruncate($Vc,strlen($Ab));file_unlock($Vc);}function
file_unlock($Vc){flock($Vc,LOCK_UN);fclose($Vc);}function
first(array$sa){return
reset($sa);}function
password_file($tb){$m=get_temp_dir()."/adminer.key";if(!$tb&&!file_exists($m))return'';$Vc=file_open_lock($m);if(!$Vc)return'';$F=stream_get_contents($Vc);if(!$F){$F=rand_string();file_write_unlock($Vc,$F);}else
file_unlock($Vc);return$F;}function
rand_string(){return(function_exists('random_bytes')?bin2hex(random_bytes(16)):md5(uniqid(strval(mt_rand()),true)));}function
select_value($W,$w,array$k,$Hh){if(is_array($W)){$F="";if(array_filter($W,'is_array')==array_values($W)){$fe=array();foreach($W
as$V)$fe+=array_fill_keys(array_keys($V),null);foreach(array_keys($fe)as$de)$F
.="<th>".h($de);foreach($W
as$V){$F
.="<tr>";foreach(array_merge($fe,$V)as$xi)$F
.="<td>".select_value($xi,$w,$k,$Hh);}}else{foreach($W
as$de=>$V)$F
.="<tr>".($W!=array_values($W)?"<th>".h($de):"")."<td>".select_value($V,$w,$k,$Hh);}return"<table>$F</table>";}if(!$w)$w=adminer()->selectLink($W,$k);if($w===null){if(is_mail($W))$w="mailto:$W";if(is_url($W))$w=$W;}$W=driver()->value($W,$k);$F=adminer()->editVal($W,$k);if($F!==null){if(!is_utf8($F))$F="\0";elseif($Hh!=""&&is_shortable($k))$F=shorten_utf8($F,max(0,+$Hh));else$F=h($F);}return
adminer()->selectVal($F,$w,$k,$W);}function
is_blob(array$k){return
preg_match('~blob|bytea|raw|file'.(JUSH=="mssql"?'|binary|image':'').'~',$k["type"])&&!in_array($k["type"],idx(driver()->structuredTypes(),lang(7),array()));}function
is_mail($cc){$ua='[-a-z0-9!#$%&\'*+/=?^_`{|}~]';$Sb='[a-z0-9]([-a-z0-9]{0,61}[a-z0-9])';$Xf="$ua+(\\.$ua+)*@($Sb?\\.)+$Sb";return
is_string($cc)&&preg_match("(^$Xf(,\\s*$Xf)*\$)i",$cc);}function
is_url($O){$Sb='[a-z0-9]([-a-z0-9]{0,61}[a-z0-9])';return
preg_match("~^((https?):)?//($Sb?\\.)+$Sb(:\\d+)?(/.*)?(\\?.*)?(#.*)?\$~i",$O);}function
is_shortable(array$k){return!preg_match('~'.number_type().'|date|time|year~',$k["type"]);}function
host_port($K){return(preg_match('~^(:([^:].*)|(\[(.+)\]|(([^:]+://)?[^:]+))(:(\d+))?)$~',$K,$x)?array($x[4].$x[5],$x[2].$x[8]):array($K,''));}function
count_rows($P,array$Z,$Xd,array$dd){$D=" FROM ".table($P).($Z?" WHERE ".implode(" AND ",$Z):"");return($Xd&&(JUSH=="sql"||count($dd)==1)?"SELECT COUNT(DISTINCT ".implode(", ",$dd).")$D":"SELECT COUNT(*)".($Xd?" FROM (SELECT 1$D GROUP BY ".implode(", ",$dd).") x":$D));}function
slow_query($D){$h=adminer()->database();$Kh=adminer()->queryTimeout();$hh=driver()->slowQuery($D,$Kh);$g=null;if(!$hh&&support("kill")){$g=connect();if($g&&($h==""||$g->select_db($h))){$ge=get_val(connection_id(),0,$g);echo
script("const timeout = setTimeout(() => { ajax('".js_escape(ME)."script=kill', function () {}, 'kill=$ge&token=".get_token()."'); }, 1000 * $Kh);");}}ob_flush();flush();$F=@get_key_vals(($hh?:$D),$g,false);if($g){echo
script("clearTimeout(timeout);");ob_flush();flush();}return$F;}function
get_token(){$vg=rand(1,1e6);return($vg^$_SESSION["token"]).":$vg";}function
verify_token(){list($Rh,$vg)=explode(":",$_POST["token"]);return($vg^$_SESSION["token"])==$Rh&&in_array($_SERVER["HTTP_SEC_FETCH_SITE"],array("","same-origin"));}function
compress_alphabet(){return
strtr(implode(range('"','~')),"'\\","!\n");}function
decompress_string($O,$Jb=""){$na=array_flip(str_split(compress_alphabet()));$u=strlen($O);$_i=($u?13*($u-1)/2-$na[$O[0]]:0);$Fa="";$Fg=0;$Gg=0;for($o=1;$o<$u;$o+=2){$Fg=($Fg<<13)+$na[$O[$o]]*93+$na[$O[$o+1]];$Gg+=13;while($Gg>=8&&$_i>=8){$Gg-=8;$_i-=8;$Fa
.=chr($Fg>>$Gg);$Fg&=(1<<$Gg)-1;}}if($Fa=="")return"";if($Jb!=""&&function_exists('inflate_init'))return
inflate_add(inflate_init(ZLIB_ENCODING_RAW,array('dictionary'=>$Jb)),$Fa,ZLIB_FINISH);return($Jb==""&&function_exists('gzinflate')?gzinflate($Fa):inflate($Fa,$Jb));}function
inflate($Fa,$Jb=""){$qe=array(3,4,5,6,7,8,9,10,11,13,15,17,19,23,27,31,35,43,51,59,67,83,99,115,131,163,195,227,258);$re=array(0,0,0,0,0,0,0,0,1,1,1,1,2,2,2,2,3,3,3,3,4,4,4,4,5,5,5,5,0);$Mb=array(1,2,3,4,5,7,9,13,17,25,33,49,65,97,129,193,257,385,513,769,1025,1537,2049,3073,4097,6145,8193,12289,16385,24577);$Ob=array(0,0,0,0,1,1,2,2,3,3,4,4,5,5,6,6,7,7,8,8,9,9,10,10,11,11,12,12,13,13);$F=$Jb;$eg=0;do{$Gc=inflate_bits($Fa,$eg,1);$S=inflate_bits($Fa,$eg,2);if(!$S){$eg=($eg+7)&~7;$u=inflate_bits($Fa,$eg,16);$eg+=16;$F
.=substr($Fa,$eg>>3,$u);$eg+=$u<<3;}else{if($S==1){$xe=array_merge(array_fill(0,144,8),array_fill(0,112,9),array_fill(0,24,7),array_fill(0,8,8));$Pb=array_fill(0,30,5);}else{$we=inflate_bits($Fa,$eg,5)+257;$Nb=inflate_bits($Fa,$eg,5)+1;$_f=array(16,17,18,0,8,7,9,6,10,5,11,4,12,3,13,2,14,1,15);$Ue=array_fill(0,19,0);$Te=inflate_bits($Fa,$eg,4)+4;for($o=0;$o<$Te;$o++)$Ue[$_f[$o]]=inflate_bits($Fa,$eg,3);$Ve=inflate_table($Ue);$se=array();while(count($se)<$we+$Nb){$xh=inflate_symbol($Fa,$eg,$Ve);if($xh==16)$se=array_merge($se,array_fill(0,inflate_bits($Fa,$eg,2)+3,end($se)));elseif($xh==17)$se=array_merge($se,array_fill(0,inflate_bits($Fa,$eg,3)+3,0));elseif($xh==18)$se=array_merge($se,array_fill(0,inflate_bits($Fa,$eg,7)+11,0));else$se[]=$xh;}$xe=array_slice($se,0,$we);$Pb=array_slice($se,$we);}$ye=inflate_table($xe);$Rb=inflate_table($Pb);while(($xh=inflate_symbol($Fa,$eg,$ye))!=256){if($xh<256)$F
.=chr($xh);else{$u=$qe[$xh-257]+inflate_bits($Fa,$eg,$re[$xh-257]);$Qb=inflate_symbol($Fa,$eg,$Rb);$qf=strlen($F)-$Mb[$Qb]-inflate_bits($Fa,$eg,$Ob[$Qb]);for($o=0;$o<$u;$o++)$F
.=$F[$qf+$o];}}}}while(!$Gc);return($Jb==""?$F:substr($F,strlen($Jb)));}function
inflate_bits($Fa,&$eg,$sb){$F=0;for($o=0;$o<$sb;$o++){$F+=((ord($Fa[$eg>>3])>>($eg&7))&1)<<$o;$eg++;}return$F;}function
inflate_table(array$se){$P=array();$Wa=0;for($Ga=1;$Ga<=max($se);$Ga++){foreach($se
as$xh=>$u){if($u==$Ga){$P[$Ga][$Wa]=$xh;$Wa++;}}$Wa<<=1;}return$P;}function
inflate_symbol($Fa,&$eg,array$P){$Wa=0;$Ga=0;do{$Wa=($Wa<<1)+inflate_bits($Fa,$eg,1);$Ga++;}while(!isset($P[$Ga][$Wa]));return$P[$Ga][$Wa];}function
script($lh,$Sh="\n"){return"<script".nonce().">$lh</script>$Sh";}function
script_src($qi,$Eb=false){return"<script src='".h($qi)."'".nonce().($Eb?" defer":"")."></script>\n";}function
nonce(){return' nonce="'.get_nonce().'"';}function
on($nc,$id,$qa=null){$ra=array();foreach(array_slice(func_get_args(),2)as$W)$ra[]=json_encode($W,256);return" data-on$nc='".str_replace(array('&','<',"'"),array('&amp;','&lt;','&#039;'),"$id(".implode(", ",$ra).")")."'";}function
input_hidden($y,$X=""){return"<input type='hidden' name='".h($y)."' value='".h($X)."'>\n";}function
input_token(){return
input_hidden("token",get_token());}function
target_blank(){return' target="_blank" rel="noreferrer noopener"';}function
h($O){return
str_replace(array('&','<','"',"'","\0"),array('&amp;','&lt;','&quot;','&#039;','&#0;'),$O);}function
nl_br($O){return
str_replace("\n","<br>",$O);}function
checkbox($y,$X,$Ra,$je="",$c="",$Ua="",$le=""){$F="<input type='checkbox' name='$y' value='".h($X)."'".($Ra?" checked":"").($je==""&&$Ua?" class='$Ua'":"").($le?" aria-labelledby='$le'":"").$c.">";return($je!=""?"<label".($Ua?" class='$Ua'":"").">$F".h($je)."</label>":$F);}function
optionlist($_,$Rg=null,$ti=false){$F="";foreach($_
as$de=>$V){$zf=array($de=>$V);if(is_array($V)){$F
.='<optgroup label="'.h($de).'">';$zf=$V;}foreach($zf
as$t=>$W)$F
.='<option'.($ti||is_string($t)?' value="'.h($t).'"':'').($Rg!==null&&($ti||is_string($t)?(string)$t:$W)===$Rg?' selected':'').'>'.h($W);if(is_array($V))$F
.='</optgroup>';}return$F;}function
html_select($y,array$_,$X="",$c="",$le=""){static$je=0;$ke="";if(!$le&&substr($_[""],0,1)=="("){$je++;$le="label-$je";$ke="<option value='' id='$le'>".h($_[""]);unset($_[""]);}return"<select name='".h($y)."'".($le?" aria-labelledby='$le'":"")."$c>".$ke.optionlist($_,$X)."</select>";}function
html_radios($y,array$_,$X="",$J=""){$F="";foreach($_
as$t=>$W)$F
.="<label><input type='radio' name='".h($y)."' value='".h($t)."'".($t==$X?" checked":"").">".h($W)."</label>$J";return$F;}function
confirm($Re=""){return
on('click','confirmClick',$Re?:lang(8));}function
print_fieldset($p,$pe,$Ei=false){echo"<fieldset><legend>","<a href='#fieldset-$p' class='toggle'>$pe</a>","</legend>","<div id='fieldset-$p'".($Ei?"":" class='hidden'").">\n";}function
bold($Ha,$Ua=""){return($Ha?" class='active $Ua'":($Ua?" class='$Ua'":""));}function
js_escape($O){return
str_replace("<","\\x3C",addcslashes($O,"\r\n'\\"));}function
js_escape_re($O){return
addcslashes(preg_quote($O,"/"),"\r\n");}function
pagination_href($A){return
remove_from_uri("page|next").($A?"&page=$A".($_GET["next"]!=""?"&next=".url_escape($_GET["next"]):""):"");}function
pagination($A,$zb){return" ".($A==$zb?($A?"<b>".($A+1)."</b>":$A+1):'<a href="'.h(pagination_href($A)).'">'.($A+1)."</a>");}function
hidden_fields(array$pg,array$Cd=array(),$jg=''){$F=false;foreach($pg
as$t=>$W){if(!in_array($t,$Cd)){if(is_array($W))hidden_fields($W,array(),$t);else{$F=true;echo
input_hidden(($jg?$jg."[$t]":$t),$W);}}}return$F;}function
hidden_fields_get(){echo(sid()?input_hidden(session_name(),session_id()):''),($_GET["ext"]?input_hidden("ext",$_GET["ext"]):""),(isset($_GET[DRIVER])?input_hidden(DRIVER,SERVER):""),input_hidden("username",$_GET["username"]);}function
on_upload_progress(&$oi){$oi=(ini_bool("session.upload_progress.enabled")&&ini_get("session.upload_progress.name")?rand_string():"");return($oi?on('submit','uploadProgress',ME."upload=$oi",SESSION_NAME."=$oi"):"");}function
file_input($c,$Fg=""){$He="max_file_uploads";$Ie=ini_get($He);$Le="upload_max_filesize";$Me=ini_bytes($Le);$hg=ini_bytes("post_max_size");if($hg&&$hg<$Me){$Le="post_max_size";$Me=$hg;}$Ne=ini_get($Le);return(ini_bool("file_uploads")?"<input type='file'$c".on('change','fileChange',(int)$Ie,lang(9,"$He = $Ie"),$Me,lang(9,"$Le = $Ne")).">$Fg":lang(10));}function
enum_input($S,$c,array$k,$X,$fc=""){preg_match_all("~'((?:[^']|'')*)'~",$k["length"],$Ee);$jg=($k["type"]=="enum"?"val-":"");$Ra=(is_array($X)?in_array("null",$X):$X===null);$F=($k["null"]&&$jg?"<label><input type='$S'$c value='null'".($Ra?" checked":"")."><i>$fc</i></label>":"");foreach($Ee[1]as$W){$W=stripcslashes(str_replace("''","'",$W));$Ra=(is_array($X)?in_array($jg.$W,$X):$X===$W);$F
.=" <label><input type='$S'$c value='".h($jg.$W)."'".($Ra?' checked':'').'>'.h(adminer()->editVal($W,$k)).'</label>';}return$F;}function
input(array$k,$X,$n,$ya=false,$T=false){$y=h(bracket_escape($k["field"]));echo"<td class='function'>";if(is_array($X)&&!$n)$n="json";$be=($n=="json"||preg_match('~^jsonb?$~',$k["full_type"]));if($be&&$X!=''&&(JUSH!="pgsql"||$k["type"]!="json")&&(is_array($X)||!$_POST["save"]))$X=json_encode(is_array($X)?$X:json_decode($X),128|64|256);$Eg=(JUSH=="mssql"&&$T&&$k["auto_increment"]);if($Eg&&!$_POST["save"])$n=null;$ad=(isset($_GET["select"])||$Eg?array("orig"=>lang(11)):array())+adminer()->editFunctions($k);$ic=driver()->enumLength($k);if($ic){$k["type"]="enum";$k["length"]=$ic;}$c=" name='fields[$y]".($k["type"]=="enum"||$k["type"]=="set"?"[]":"")."'".($ya?" autofocus":"");echo
driver()->unconvertFunction($k)." ";$P=$_GET["edit"]?:$_GET["select"];if($k["type"]=="enum")echo
h($ad[""])."<td>".adminer()->editInput($P,$k,$c,$X);else{$ld=(in_array($n,$ad)||isset($ad[$n]));$Hc=0;foreach($ad
as$t=>$W){if($t===""||!$W)break;$Hc++;}echo(count($ad)>1?"<select name='function[$y]'".on('change','functionChange').on_help_value('^SQL$').">".optionlist($ad,$n===null||$ld?$n:"")."</select>":h(reset($ad)))."<td".($Hc&&count($ad)>1?on('input','skipOriginal',$Hc):"").">";$Nd=adminer()->editInput($P,$k,$c,$X);if($Nd!="")echo$Nd;elseif(preg_match('~bool~',$k["type"]))echo"<input type='hidden'$c value='0'>"."<input type='checkbox'".(preg_match('~^(1|t|true|y|yes|on)$~i',$X)?" checked":"")."$c value='1'>";elseif($k["type"]=="set")echo
enum_input("checkbox",$c,$k,(is_string($X)?explode(",",$X):$X));elseif(is_blob($k)&&ini_bool("file_uploads"))echo"<input type='file' name='fields-$y'>";elseif($be)echo"<textarea$c cols='50' rows='12' class='jush-json'>".h($X).'</textarea>';elseif(($Gh=preg_match('~text|lob|memo~i',$k["type"]))||preg_match("~\n~",$X)){if($Gh&&JUSH!="sqlite")$c
.=" cols='50' rows='12'";else{$H=min(12,substr_count($X,"\n")+1);$c
.=" cols='30' rows='$H'";}echo"<textarea$c>".h($X).'</textarea>';}else{$ei=driver()->types();$Oe=(!preg_match('~int~',$k["type"])&&preg_match('~^(\d+)(,(\d+))?$~',$k["length"],$x)?((preg_match("~binary~",$k["type"])?2:1)*$x[1]+($x[3]?1:0)+($x[2]&&!$k["unsigned"]?1:0)):($ei[$k["type"]]?$ei[$k["type"]]+($k["unsigned"]?0:1):0));if(JUSH=='sql'&&min_version(5.6)&&preg_match('~time~',$k["type"]))$Oe+=7;echo"<input".((!$ld||$n==="")&&preg_match('~^'.int_type().'$~',$k["type"])&&!preg_match('~\[]~',$k["full_type"])?" type='number'":"")." value='".h($X)."'".($Oe?" data-maxlength='$Oe'":"").(preg_match('~char|binary~',$k["type"])&&$Oe>20?" size='".($Oe>99?60:40)."'":"")."$c>";}echo
adminer()->editHint($P,$k,$X),(count($ad)>1?script("fire(qs('select', qsl('td').previousSibling), 'change');",""):"");}}function
process_input(array$k){$q=bracket_escape($k["field"]);$n=idx($_POST["function"],$q);if($n=="orig")return(preg_match('~^CURRENT_TIMESTAMP~i',$k["on_update"])?idf_escape($k["field"]):false);if($n=="NULL")return"NULL";if(is_blob($k)&&ini_bool("file_uploads")){$Bc=get_file("fields-$q");if(!is_string($Bc))return
false;return
driver()->quoteBinary($Bc);}$X=idx($_POST["fields"],$q);if($X===null)return
false;if($k["type"]=="enum"||driver()->enumLength($k)){$X=idx($X,0);if($X=="orig"||!$X)return
false;if($X=="null")return"NULL";$X=substr($X,4);}if($k["auto_increment"]&&$X=="")return
null;if($k["type"]=="set")$X=implode(",",(array)$X);if($n=="json"){$X=json_decode($X,true);if(!is_array($X))return
false;return$X;}return
adminer()->processInput($k,$X,$n);}function
search_tables(){$_GET["where"][0]["val"]=$_POST["query"];$Sg="<ul>\n";foreach(table_status('',true)as$P=>$Q){$y=adminer()->tableName($Q);if(isset($Q["Engine"])&&$y!=""&&(!$_POST["tables"]||in_array($P,$_POST["tables"]))){$E=connection()->query("SELECT".limit("1 FROM ".table($P)," WHERE ".implode(" AND ",adminer()->selectSearchProcess(fields($P),array())),1));if(!$E||$E->fetch_row()){$mg="<a href='".h(ME."select=".url_escape($P)."&where[0][op]=".url_escape($_GET["where"][0]["op"])."&where[0][val]=".url_escape($_GET["where"][0]["val"]))."'>$y</a>";echo"$Sg<li>".($E?$mg:"<p class='error'>$mg: ".adminer()->error())."\n";$Sg="";}}}echo($Sg?"<p class='message'>".lang(12):"</ul>")."\n";}function
on_help($Gh,$eh=0){return
on('mouseover','helpMouseover',$Gh,$eh).on('mouseout','helpMouseout');}function
on_help_value($_g="",$Dg=""){return
on('mouseover','helpValueMouseover',$_g,$Dg).on('mouseout','helpMouseout');}function
edit_form($P,array$l,$G,$T,$j='',$D='',$Jh=''){$Bh=adminer()->tableName(table_status1($P,true));page_header(($T?lang(13):lang(14)),$j,array("select"=>array($P,$Bh)),$Bh);adminer()->editRowPrint($P,$l,$G,$T,$D,$Jh);if($G===false){echo"<p class='error'>".lang(15)."\n";return;}echo"<form action='' method='post' enctype='multipart/form-data' id='form'>\n";$bc=false;$Ki=($T&&!isset($_GET["select"])?where_columns($l):array());$ob=(count($Ki)!=count($l));if(!$ob)$Ki=array();if(!$l)echo"<p class='error'>".lang(16)."\n";else{echo"<table class='layout nowrap'".on('keydown','editingKeydown').">\n";$ya=!$_POST;foreach($l
as$y=>$k){echo"<tr".($Ki[$y]?on('change','whereChange'):"")."><th>".adminer()->fieldName($k);$i=idx($_GET["set"],bracket_escape($y));if($i===null){$i=$k["default"];if($k["type"]=="bit"&&preg_match("~^b'([01]*)'\$~",$i,$Ag))$i=$Ag[1];if(JUSH=="sql"&&preg_match('~binary~',$k["type"]))$i=bin2hex($i);}$X=($G!==null?($G[$y]!=""&&JUSH=="sql"&&preg_match("~enum|set~",$k["type"])&&is_array($G[$y])?implode(",",$G[$y]):(is_bool($G[$y])?+$G[$y]:$G[$y])):(!$T&&$k["auto_increment"]?"":(isset($_GET["select"])?false:$i)));if(!$_POST["save"]&&is_string($X))$X=adminer()->editVal($X,$k);if(($T&&!isset($k["privileges"]["update"]))||$k["generated"])echo"<td class='function'><td>".select_value($X,'',$k,null);else{$bc=true;$n=($_POST["save"]?idx($_POST["function"],bracket_escape($y),""):($T&&preg_match('~^CURRENT_TIMESTAMP~i',$k["on_update"])?"now":($X===false?null:($X!==null?'':'NULL'))));if(!$_POST&&!$T&&$X==$k["default"]&&preg_match('~^[\w.]+\(~',$X))$n="SQL";if(preg_match("~time~",$k["type"])&&preg_match('~^CURRENT_TIMESTAMP~i',$X)){$X="";$n="now";}if($k["type"]=="uuid"&&$X=="uuid()"){$X="";$n="uuid";}if($ya!==false)$ya=($k["auto_increment"]||$n=="now"||$n=="uuid"?null:true);input($k,$X,$n,$ya,$T);if($ya)$ya=false;}}if(!fields($P)&&driver()->primary!="")echo"<tr>"."<th><input name='field_keys[]'".on('input','fieldChange').">"."<td class='function'>".html_select("field_funs[]",adminer()->editFunctions(array("null"=>isset($_GET["select"]))))."<td><input name='field_vals[]'>";echo"</table>\n";}echo"<p>\n";if($bc){echo"<input type='submit' value='".lang(17)."'>\n";if(!isset($_GET["select"])&&$ob){$Kb=($Ki&&($j!=""||adminer()->error!="")?" disabled":"");echo"<input type='submit' name='insert' value='".($T?lang(18):lang(19))."' title='Ctrl+Shift+Enter'$Kb".($T?on('click','ajaxForm',lang(20)):"").">\n";}}echo($T?"<input type='submit' name='delete' value='".lang(21)."'".confirm().">\n":"");if(isset($_GET["select"]))hidden_fields(array("check"=>(array)$_POST["check"],"clone"=>$_POST["clone"],"all"=>$_POST["all"]));echo
input_hidden("referer",(isset($_POST["referer"])?$_POST["referer"]:$_SERVER["HTTP_REFERER"])),input_hidden("save",1),input_token(),"</form>\n";}function
repeat_pattern($Xf,$u){return
str_repeat("$Xf{0,65535}",$u/65535)."$Xf{0,".($u%65535)."}";}function
shorten_utf8($O,$u=80,$vh=""){if(!preg_match("(^(".repeat_pattern("[\t\r\n -\x{10FFFF}]",$u).")($)?)u",$O,$x))preg_match("(^(".repeat_pattern("[\t\r\n -~]",$u).")($)?)",$O,$x);return
h($x[1]).$vh.(isset($x[2])?"":"<i>…</i>");}function
icon($zd,$y,$yd,$Mh,$c=""){return"<button ".($y?"type='submit' name='$y'":"draggable='true' tabindex='-1'")." title='".h($Mh)."' class='icon icon-$zd".($y?"":" jsonly")."'$c><span>$yd</span></button>";}function
copy_icon(){$rb=lang(22);return"<a href='' class='jsonly icon-copy' title='$rb'><span>$rb</span></a>";}if(isset($_GET["file"])){if(substr(VERSION,-4)!='-dev'){if($_SERVER["HTTP_IF_MODIFIED_SINCE"]){header("HTTP/1.1 304 Not Modified");exit;}header("Expires: ".gmdate("D, d M Y H:i:s",time()+365*24*60*60)." GMT");header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");header("Cache-Control: immutable");}ini_set("zlib.output_compression",'1');if($_GET["file"]=="default.css"){header("Content-Type: text/css; charset=utf-8");echo
decompress_string('(c01diDWB2N@+*{pZ+O40SzmMXgW+AUSyV1E>/C6W40MS%--E$3wIB.OmN%xAbl(v))btwo1lI8Iek^D>XsY(,2q5c;c&w49zleRgs6T5K!dm15dC=|<7-Ov6lDdTi<E>C0ysZqauv5pugTy8AaN"PRF@Y:
BhaqKZS7(Jw3nHzi%]rvBKe"jg@IOi8osuPc9DOe/Aq)5fshO1KCn,V@Mi"VRc]RwZnRiCYc;&<h]<F$<L}Ip9ypcJdbF!KEtg;@O2;-o/zH=CP@e-+TEf|j7b~Z{s->-J-qKQ(&JUgFL&>:u-b48Q=eBuO)jN>wA^SUX_G5CE^[">~sF,N`5QsYm^aQz;jU!T`p@]um*;<V_-+@>:r[X3upie39,OyH$aIb["^DyU#?$f5sTkh*n!zmC*7Z5mvaNA8b&x`LS935
F$kcQmMoJ;>E*)(A`NR$Uf#RG~st*)Giwj%_8sK#315fPTxCBlp:SZ3RN&L-<719p6rj8WmUTua*KcBp4-5#"Att5[/O5bE9Y)e;s/iB*3N
L<q+2_;GQAk$TMPR`"Ss(lUHX9"R`7r!+[H:pb/ahDebj5!X8NaT-yon>6D{4b,8qiorZL/FGVDt-}wKD8:e*d[d3EgUiU$V"cDx_JIY+fZ-7N>DI..l>$@FTuGpWUBvWfcdYZD9b[Uh6,C7A%?0^%4JBL#zdYNq.m+]^|,o>(;WNm;;Pk*YSkNN-br$/;GLN7y-%
uP.hY^EyCGFYD*NyFRdr
2Cp<Dad
&S>c,+mxzs^*JfHF_cSUs<{u?y!onA{j>(:dN1QGe:6,:>;[V*!G8mHj#g:1+e.k@(<yQS"[8y>a^0=;MH^AYo)
)/XHU-c
r%*mCrmqqX2jIW=.YJthFvBkHCo[D@=Uijv(J)Be><G?Im]he.CC30onYfSNFoCq"L@Y2%<n;U0-Rr7cMorCW#}5z&c!vqxpRh.-@+e>8/_9V1LR:6ZO708r=oY9M#d-8
75?S#s;e
XBGx8X,+AlMk=GfWnL?jrwQez!VLbmC"jt.9LN9&_gIzS.r.E<.Nr,RR1zG1w%$g+A<%s,bYh^M~#!1inl#W]H>c[=q&E[0lk>/[=,Cy=|nygsLTVqX==P+S=A41<(nop^_7]DU0bdL$3IbtNs+o.Guu+FyH*tW#@s[Xeo*p6Pj8e9=%CeNT7>x`J39;nzoWI9<HCr^I?na(t$g@=8dJ9NY)Jh138&"7[FrH&jAi0,QVNHNm*{Tn(6Ttx.J./XfOg=/n;=$xF;0@gQ(G5x<(l<qFkxgwJlbfQ24R9}
Q0w/veuTKhVhJi
r(70YA_-e/QgkbSc&aX7$hjG6D(]EF`Ain^vMCQw&yjS6pa$p}`LLV!q(EqV.N;V_;lU[mvdVaA16fVBGT=}8_:uW*?XhVg)@BPhi8
iU}2e:-0.k`AYpjE$:R)h*cVz3@h%1P8l;!bSOp450?69W"B!W>;Kf*uW%|3m;3pB$isF*
5}Rra[MqU`U{.k[6koyiFaZLZ{011)akYV1).8oEVD8P7%V{)8"
8ogZ,YHGgK<5
FBjpod%=U*gU=XAvzV{nk%nJpa*#w3*BFDXq<UJ(WX,+F@4Ra_kRC*ziC9-Q]iZ9QA2Xk6QO>*g9%AAB9,f@X0J_&:[&fE)
Qk*Y8)K(e,gdkdX.y`tt?AoDxNuRIY"I[h1E8m+>b;#Yx4sjs-AIT"^1t(=b45@#1(~BBUhp64d<`KtH`:6TnVg1XX>I);fJti2Tr"H4#KbTXCVGo%=u6`UuyvGj:Ug_y/sduZT$h/G884/%P<=8;0g+t<fxejJf6vn"+kW;k<L/3a=DJG-o{X%NS?%)R)^Hl):P;Z;QnDdXs2|`t)#W<i-*}T=$c/$guF<otB^JbrOC#E]SwHTG6]:F~;3_?,]0qb?/oZ=3U4zfRK]#}cm8(Y1X!Luk=8=!iL.d~X0DPF?h"0f*1Az$d8*QD)tr|pI6tyAKF=_o_6g-dJ:*A,M-D=EOY.1U}n*ya
aO"K_IqnrC!f.V(QQZ:g"HUhv;9f<n;:4p.k%
6]@P^%G-)_6,Q!Y^Kj/4uP!(hyLLcn3K)@ORW<H?
TNEs%QLc4=N@0<o[)PE#<n?&(TF{pqD:1Iw|><[N2^>5=qfdFf[_A,F7^,`#!]C+XjMHly+&@=pj.db3EF-Id.IK".`?<vLJ)ZElc%B~+q!6Z,jm.kVFv~rxsHBhRWO6l}(-`h`3aupT&!Fr6fG,L~
ENHiW,Q)yRhES9QFZ;T!21IqpU()*Z>sA@(9uJ/*m
|"3g|.w<YmlOU4]&$Q|lvFa]PcC:a[},J26jm%9!V3`e>gR4pe=rh"m!k-NU}Ewy3,@2T5Z`&am9}vLO)^b2C[{xyfP"6U_C-7w7aVETDk5Hp1#oO7[yI8=v_+"xHJH#!r^1<[$KJP@7<vs,Y5.D
$Vb{7LO)Y98-C4T"t2E7ZJ]xdG/[]&[g!o!Zq`L#4c6gwWl$FMa^UxnewoOTI5]vl3C{^?n4^.L7]-TgoRMDw@c4qHxCd7Mhp)Q>qib1BzE*BOv-AFm"mH^c_h_xaBkFd3TwwtySLuv]z(w^6-HKvidMb9t~lUx^c
RbLj>!Gk/x:@jrE*Q
cf^ax#,3lGYW^bTtB:tmE!h+K
,cp@,LrLFQh+"*t,MIokS3P_K+i-6PW&bhz#l}N=YZjBgjdccgwTFp;DImuHBK4_+V7/*QLkO^@j=aHBO[)~V.i[5,t$p"X?upQKN9>n2#au_V*h]24wCo+XK_+TuW4/M#FT=8jUK"Pa_6vs44"TjB^+H|y-0Qb<u>0TfL9?^2k65k/lga@lYiu%"PSw8k=-owf=;p93)Zl8hWdgt"wts%l~ISNyv|<|m>u)4t79H|ted}U*,l$E5NKx"u7jDWVly&=D$>W63e%Z9wt0IW:YS>Lqx>y5J.gPcn@8yEBKgP
Q^6G"@9x#N%ZeSQa5whm"CbMhbaZzc0?NwXJ|hjJ0o-RZGAI~WtA94jqjg-m(i|wTT/)nxx>O&LbG6$L0+;w.la<F/1Nw?BfC>ZCyZw=M7[VhY$:T]ZURGeg|B6M_1,oSon)>JK8{q5lmg*KHYW]G]gQ:fx
:Qz<&j?06t]:U
CQ:+D.vS7M[PscI-vQ{SYkgM]<!e9w*B<Q-6BC_=g44YS=Q+V:+mmifq*pVWY#@GGDjOWy!YVQ@6FyY[CDMFwH4CDy<Hy$Ja.%6]p(Cu?EEnlic^]*PX,r=e(kA<g2JoV,>c<7)YWT(HFKm7QF]2"$%XANimKoZ7r4?&Y:~e^xlJ,Z&Xea
L2/gcN>u)!i)2EUTXBuO?O::ib`i82axBKSJ;<>@&[HXocT^LRY5kq_H2gsfi3ZC`+Gx_|FLv6j/5oO[=.lxDudvd)&ScTvBnB3,MsBB');}elseif($_GET["file"]=="dark.css"){header("Content-Type: text/css; charset=utf-8");echo
decompress_string('$OsQu6/V?&((xSSr$-;!RDo0CGdO=gqgAVtU&I5Oj0qYYD(O[G/4G`x`I_e=Jf+nX&fEL6?DwIa(58e3h(N)hTa)[TjT[G:-h(*?irSOGAb*kQ`q8cb2q<"N0VffxPJW^UI^S9(t6_>BqY8!$CSZ$=-cVLx2?R2M)l.y6Y9M#,+d?e8;7(70aB&`P;PTCK2d)(e,3b5)G,dkjy)Wh[&GD*skioUQ7i;A}]j^WuNmPcEC]$0:
]<u7"Jox-Kha87Y=NB
SLg$Rg[N1^jZ<k%fBo>M5EplTH@HuL?S0_145"Flhvm//@O?d
n!F=3kc[iC,*AjRP+
vM*m,fK$:2X^#;0d"d+vmlmYf6;MBVznqYDkZl`3znN]fPX>|p;^A.6^
#sx8sQB!lz$:XUZGa%k@Nn9pS=k#7QlC_~j|#8lXSe:U&%rmHlw9fbAg[;JNgm!tXc>;NtCvT*`iD=Ny4yh
_u*lx)2{?(oK"<o4pZ-0ELUo+rHx/M^`vnjb@UZbTm1Jm-`C;[,e-8[]Lcr=Ax%L)iJ{)&i$`JJpcZQE&=a^Mu
"[30.-L86y*9<.ipfgHPa[oeUw[`pN*jk6}]0859:i2mjMPxUlHVa<n;?xKxSpQ]o4MZ5..x086J2dq<J42eeQ,0-X#j&wP>aL
9AT
cLk~67X}rBYg]<
ihm2Kb}j`AlV}M<<gq>K1A{as3"x]`j9O64qI=vGhT!9]o4vw[>&JC@:h:lI+!feNS3T!A-"*U|(`#]?eN_h4Fle9GdXS^<@qu
Y.&+Pc7Md0;sF~-(-XPWKu.Q=e%oIyZ5]bgzBhqlymX@pw"kW{Fw09POc;ua-"d<');}elseif($_GET["file"]=="functions.js"){header("Content-Type: text/javascript; charset=utf-8");echo
decompress_string('!X/GfnsWL2O6ulK74$36$jkxJsSD:yLYu_RbXTlp8Xa6fgjsi$sF>YP(FWxM!d!n#5+#E[IneKCA#P,DHdT,C3%?"Fyc/qH?44]@%KHF@C|)`tVb@]aE`a$)@Q;Qwx`j21cwopc&UvYjSUc].>87{xNFmlK-Yn9w5o?*f,~MF>o
C9ct/u8t,gg:d8Z8*2lx|s1?3DVXmw)2[z(xeyZ+zH7n*Qc<BmzM=%#:+3J<m^:Cr*,m5Ob19:YcAjS?Yg%Hr+inU^m)Rd4?6#c[R_L<Yz(yuY>quQB]PnDOwJu.ct)k,"[TGqg-sMB.dD=Y8As[pW^O41-m?pS?7qM
]lRPHnbxI]xxC:vK[A8fx?kvo]3.d6/LJ#Q_@5BPWEM
|gsG&YdVgHgb|lz-nrqo~7*Z%F/dz[J@b+~Q<>uc,&{wx1+=Uk]m&!{iA9Z
Nj5>^qI$evZSDUeMbBJ2dj6rDtTV-4|y%Gzs.`twxo(kpqf6m4WfUumyEW.X}mALqog="RYWs6_sPry:_xtF7sR.P2G+le()EMRoHO9,sC>]bu~.&BKOEtWgwP/lsYH-Yi[U/YahEriMbAnKl*8Hc^tx7qo^i]np+%^Y=dTS?,0<SX?W}-vnPK81(B,nTB&gHSE-X3RAXkm!)(Hnjr>uOqK0XNJeD6o!`6wZYQ
M,M$*d6KixG[(6j~OLO8^70eR[>?;s
.MZ@qjvJL^p7W&0EJmhvg.cb+cOX^lbVjuB!K&Ay{*jOSXsl4]*WF;3hmC[798.o!#0_a7x$k,,BPI$U4T+YZh?L0N"/7
MNmlrI[eedbckeL#ZuS2f)+tTZDpyw_f4<,`#Q{[dha%
=Vt`exA0AXO~e(XGgAR"vPx.lY,)"~kP-~V)q>_q.&sZh+l(5W*)BR)"[4[l&)lMC@LT_UGmwEGYAkj+>[[S/5IK`0yC@hjP_);Z^zT=jSWak4jjVZ:[,<ot
xWmYs8%YBt?=RUuE5bH>_Q9;khGGN8sk@/+d"b!Umr|ZnL]B"+[153^3CtJ[<n3^,B1stv8ZW$*(]?KaC8n?kKwhU
ksoC5,uLbp[TMweJmi(lzA4
jPT8z@&q81Q6Edi`4YNYAAasjp:s~BoAbq^R0w?(NL;jL!1?:P;A>p
!C1%&VT(oZ%GtNhMoIxcs=&B60#3oA7G5neCeZmH!9-m#l"1HZaN=~7{IRG$ut$frPNVG:n.gZ/6%~nf$kF=DH^aOLyDBJn)Q%%iKQP]6B<Jl}kv]6nxdWv$V-5u7!C8>s`Aw3deIg>`X"ppJ4,C"o)QLE92Ai)A[]D;Nt
ddR>,4YAH52.CI8PZ5yO[Sz5?,XdV_L<d%1Kco#
R;@uT1GfsSkhreiY1a95v46D90))/Tzx4AI0d7,;UZYU*&!IuuTs@b{^-tOdKqn#}ipalTFQeUu@JQ
k+w/xMO]>lfDYSlUO5ikeVlhL#J!9PQBFPj:lZOaBn,Lq6XrN+.
m{m~5p&<F+ewRg"c5^OtBx5o6H+6<_?5p1*>Kl5]9+NK?qtgb&m:J{JUZX#<
-$9t8Sc=C
8GRk{Jr6b@B#x]?FAa1(J
zP_uF)ML0MRRW7ulE9KWU;
LI^>1(kvbijE3SV{(5UKG7J/>[6<Hjk,g]>=Ik7&FkepW0LDxnNiM&N.j4MSe}7.Z/cP>JZH2d_$^;nLN&i>pi2{aGoZ-2iZ^U"6Dxa|.hf?U0S^#]=,5C1>wo^CCb?v0cP4GZ##rR!j$[r`^GiIynu=2f!NPktP)u*
_<[5:NqUx.^CBsj9u/D8S-1fUny9JK*jISa"D-;HIdxYw_sPjJ^#?U&-y}.2[zB^>$?lk$1Xo"dQa81Ii6na(keK`?h2S#lrHIn&FXs49DD|pv2PRZ)NjP<z/7gE[Ku>IxpJ%Wd)+mF"C:A#r,v:*<S4TtWg$ra@+6!+I1A[tpv`07%yN-eR3UOq^Y<d;b8/$=&AM[j5-fD;sN1r90Cw].UQ@_Ks2Y=-[Q*J
wvM3
WE>*HFdDvf_3EUC6sOMY(P(*","#R3"SW2Ct.,Mj;=E3cAmn,`0mhHy2#,L4sk0B9`4G%K_heULfs533_8h5*xQsW^h=1=.|w1EVFKWW&xj+fBsnGm>ey9W%yus$X}U^Yud*ji+vA~(|Z(x"/UlfL<-4C7,4VV<fqYV0GN,.UOBPa1o4tf^zG4:BglQOo
Rq6DwKo&JbRg?%jn!DV+0%u;jK=9B1W0y(r:tolMkvaTpT.QEHu0t[Wii2ih%CG68!jL9l8(4_xWa%u6*{bhd=8F/r6!"oSFd0MS*^0fEBE4)uRhiPkFU]u#L"r(E2c,.U^nBz/DpaDpr9OgD^n(8Hb;5Xu)N*&oj(H4a-sW67W(r&!:w[ISBIg&y(2!pHJj2Q$Q9R1O!ppN@X(71,nn/NC&pKyV,:`|+%RA&/+F;CGE!moS&$yfox7+Foj@3|C^r3[wC,c.(u2anF8&IVAJVYg~w/f:hKMosYhq;(R6pVypZwAzbR&MU5,Mk$137;hQCW@YvVvw_W!%jBKAX,m_ph!wEOb7Te*MLb;&ib/"j:jZ!hH%*..
m!0XTgN]hs-Fk0b~o=Vh;.a@Y*f&`j6zFDbseqi/"o/feKx"SJ!Q8wIvIEwd
ex_wY"|Mc/XKJ+mGj-NFTH7[H(HRW
#Z.iJOeB8hzLn,cT
S"ZMFfv^JDobPtjHy@Hpbf%<bm<x$&A0IhUJEB8-Ns%NM7D!FsQhy=0w@|)6E1GfYk6!Lz^Gc+A?2_=]mcqkkc37w/KuEgVRd*2?A+PVBhUx"aEo%m]T>.qe
KH,wQV8VL9k#^I5R]d<%L^(%TV@l1(0[":Xod.4V.0C_KjGM-N47c+I*cXm6eL8U7yPtj5Mx38L=locKRdd]!m~XdCGM~QhhwfSUw7HxNIc`-3_QelnE%-FdAX>)b^`NXG
PxPAo{ClH5bU,2"@
J9wP[kZ^90ZeIG#:eZZ0Gfy6QO`[fn##WFuH
RMUpG;Ns^U>qVy)?!?Qq8zd"&nhhY6m6(x7=$zMS6$YMT&wnFRsrbUK<L{xdXl6r7+sW=;5t:j&kn89B
t6[I17!Dx2HN1/6oFm,3_;d.jP<Bpwi@G#ns6f0;N!]0=K~8F(#=muZ4h&#/qe{7t1>fX*{#^g
;;.U
&"@a)#W)&&pr3;=e2[/&OmXl;(SlYp3y#mE23l2o[_~A-HZOrJh`8hM3yClPW
si3(2oH3d
[U=BD>+R<<d-.>-l~,/IRO
fc(.g^$1($!*oc
T]p8R"x@@;[B%O{#7WYsVVqJ(3A6yGi&H$dO/cj@>*jF>P;/*MNL>gXtb/mm0b6_6:8,8@C#}I(j2Sip!6Vs`G-Y?E|,&_W.oKhiEtq(K">vdeuK#)!xeKynI#ep
DRQx1GMU=~<Vw<feCVJwZkUXggEZ$
n#N=Ry#GX9d{V|gD=G0PU
Sh.cZ|DqD1n]s+xmy=3!R(u1e%@H89evcb?PF9@EPrh@-5$<!5*P*_tOc+.sjToL@l:G[do!eV`Z
M7]V-1WO2J|V]bq>$W_5^f`JMJB<O
dPFgfW8cpbw/aQgl+q;EJ@N^k^uB4skRtHVp?r%Y&fzI+:kIfRuK9&#-3fg.mT)dQ4O1:^Afb>}Q%
j"PnnP0_3r]jz
=Y<pC3fC=,KCxxVB};1^oj|jY$B!KP>$+?q.E?HO09SsJBi"}&ulL!/k!4*$TEO]
n}6M^E8dZo^pt__nHyPlgHnI/ELk6u>C!T#FboShtnMCF..4n)6Rf@XESBmBG^HlnIY>aSR;2H5|4ls8>$QQhz2%TsO*BiY.JtxR:4B:Rh`Bc=R(A|OI1|A1+4T)Fk2"&H^x.iv@-`)l[7+:_i<ewa9oprm-0@w{y8Kf^{G;#y9CJ)7pIM]{ccV5eXN#/es:L9l}U&F0%!b5lXLHiKX`">,mwkMX]4PAIfos-84`)J2<d(qT0`&7fgejxxHI(T:8L"VrG4i%)%e^u=%TW]^j5~h4wa.F+2>WawQ:.4!$AjOnu#3NS?0YJ1w[,l;1sT6TRv
BS2&@%TWK/^3Mqo4I&3ZDVkh8
(4k*;E(.^d4lXF<*M9/Je$yAjCkpF@MxE?,9"mE%:"b"/yC5sKokyE.0D5*B,"w4@meq2Zat1!TNFtI6fuAro2u(M7#Yd&S%6*jZ"FeeeZ]!:U(QSQBjxO1l,aR3mR5iNwIe6=7brbq4kd~v"tYUg&VsXO9m4nU6g4RiCLfH`d,ObJS_?VQ3}`:Ad0Kl[KPIYvh^4>!,:cEd
q8oUd@AaO|Ib!y=j<E
I>v`&JIT6T"uha9Dha*]3flfhAWT5t[iigRNeNTpZ3)su;>UoD)m]32[^4nC3K[DfQ^&Ix"2TDZ5dIT@&>fW^t"u:,IoB<
$o"wcOrKa017B.k>#AYh%n$VVJm7t0C2oLxv"d2;e`m!f<&-!oFHSu+7cW=d_+:P1;=?Ix]}Ni,0cUp(*Jm?3Frk4zZ(0m;pD|EXk#N}@orC$pQfZiUBkhvxb/$o?=My+k-6QLG:kU[hKR6OB3kh
gE2eYT/2/GG&n0e4
i(Rr
,8Ve:@@%"&xFNT%.y?6y^),!Y_BtWX*[;7Xr68ruRbxRmiOEo7nPTVG^~-5aT.b8:W9y@fxT?D%`(8Av6*RvN"?`miUgi7T_BTu!txQ2Icq5+)vJA<[A]beNBt:Ytem`XgVaw?EYM:},R5jn0tr"9!!=9&!cODZs#r6V_[/+aq+w*M/-hZ?,wsO2MVIFgN5!`Zo]me$tcZ7&_%;<sdIJcseGfle$Vf4!I:1sk[4?TLJ(+?ssNZDw#jPkAH&Nh#id"+1]d]gl*l&&[:J;2XqWhQ,2W-J${p4Fi/c4@+("Q=tZSN,=WfItkt5cDa(]=&ybxE/So0"<JE$ZD/H1.;jI48iA|AOySRoT_sEEAtD>ledv3e?f*"|wO4)w]"J-R/n9sgu..@P(Nql;lnQiI*!nqtM>5"+6@=Z6G?,OG@j-brvW5"w>8vooQo*J//qPPDI`66UPaL7[u/GNhoai<IKH2QO(CXods3;M*H!ED2zp!6gd>7;z#xk>J+*!++P$uAy19*umCB3XA:Z1Y5Gp";d.ORHghd($r*d6gm$0pvi7w(zN@-HI:?4*~vnYPAOsNsbL;T4mUd1mP1DLdpz0_N~dkm6cB9{cHyq#mcm3j_y/#.Jk;r]wbSb*)]hkyiR#RX:fg4D`?CjN/`hCt@LKrtf%X1m3e^n0SmTlF9J"^Va@L3LROTSiR5r_
X|FpM~PH,T@tkL;qqtPZm{w#mi38]hJ~m9I8i;6m/{LA3}h]=-8?AMl3;R5$p&U)`kJ#Z12Bw6,M/wj"B7I=Fq$@Cj(*f80-`tbmP-Gh&vRb]g<V&l
>eTO/grjY&?X<avJPRrWxvSga,0osQ#7/w|N|<jwdrBj;DkJ{uBQwjC_p@*$wM>n!/~r8pM)dN-#w@Al.09
&PZMKsI*tT-?Ik6_Cd)qK$7kX+:YL[7@ef4.a#qRc$H_|MijfS:oSdJ94>.4;0O`
9TSGWWk1p$lE-EW]D
*o&_5^P|rVxFKZ_ga[BO=_#t1_-2BHbsTbTDEWc[K=,njhM#xM@0eds},V8d.cba)DXNr2],IZRt]3Y!Vn7D*sbxC5(CoGWEA
ghKGZDy,hUSYI2,
jad:JU8+pfG,Wsxj>{6Q){j<p(0"u:p[ifa@yy#7cL/aw_WxE.BMmV9MRyuI9u1qqDWzpC@t/I0YIh%P!RY]O%:CN3rw3Rc{k^0y/EDHt8AG(*#datay]c@49W5rtY)Gfla%vph#^Zll)ZNTjg&
E>%$w*Lx;pTuC2.cL0/8:G:m4zD8++03bVe{=07.Cv:Uf$RV<yaae^3/7DmTW/8!m%.S&6lI$V+0)gjilV]D>T1LnA4e,7Dz@4]HP=46Z{e<0)3gPBNu9b5Y;l-K&tOQSi%m^IU~p(@C0!R8n?jyj{GkN%,)Z-]0OAntCq0;lC(<N0WTwijcxCP
f##+pHt1VNL&Dn,IPiX`*bRiGjPY*.$Ktbw|Y8":N{X(oi=nYM80;
N|H{N^:g*>UdQgNK9<*umm,>w0GkS<lBTUV*R{=c0ODbHJm;c!6]^>yjQ#@@8<!^uS-GRy[@C3jnJS6@vYKjtpDN$^HnQrt|/SoHju&Wu}[|9
p>(U?lS[4sE80L4
R#iy
~XIa!D[a&vWr6+fp)RPQ;*b4Yd|-!g`WyQ/yxpzha,0fG+um$wQFA2%:z9Bk114urn],cW~_hLq[$cxUTp7E$g95yJ7(`bh2Abkz#9EZBeC4HULYemgFhd5RR0U9W=XQ%pr+;COjN+bL!jm.:X2;/GbK7ciE*Nq9G>fiD&EeTg1H*@5Dw3F>=9Ko>D%<~I1XWxTM=M}gk*%EXDc(i.!?8A
`h,{lij5Ec</9|
YqYd&GU`i!jo`k0w$K_mk_)U|25rs*2;?]K10qNyB$oH
>W*YAl!U>yh_VZbFsd9DWjU;T3`Q,iGWO9n"yB,NS^v9#zVMJZHvc_
/jyQfOkyqJ>y+e~._u}%ij)SpB>)uPp6WDvgeS_i%o!Z3WA]]<O6;]$.H[ui8w65-A"Q83eL(F7
O_{sK"Dg{L|q}ZxMND}UAw(8*Z^e7^=+4?i907=&?GW#lGfb~"UO:5die
GuuVELs+.pXbiC[Xad,ACgrg8:4bi%EgSj4ba>m3oEr!pA$oC)*8A+x^6qWIsg=ZITa5WoU?4_]LlQsTN]@p-7JgEP9MV8R;Gk,t$l!wA.;RNbk"USEC3X:gsT0<no41lrI/`16:jki2_*S)%:OgP_f7KCq7%]WEq[H<5MZt]bHQ~*ui2PH=O3YhP<*IYuVdnG+*^7sVw9dW|G:3#nxhlV*+[g,=BD!@6a<aS*>3f7
T!-j(~B=^sp?PF1N=1_|/PD-tQsV9Vgj3]b+FGyc?y?Id]I2K<%k');}elseif($_GET["file"]=="jush.js"){header("Content-Type: text/javascript; charset=utf-8");echo
decompress_string('');}elseif($_GET["file"]=="logo.png"){header("Content-Type: image/png");echo
base64_decode('iVBORw0KGgoAAAANSUhEUgAAADkAAAA5BAMAAAB+Np62AAAAMFBMVEUAAACDl60rTnZZdJNziaOerr60vszI0tr8jZH8c3X8SUr309T8Ly78Bgf8r7H6/PpDBKXXAAAAAXRSTlMAQObYZgAAAAlwSFlzAAALEwAACxMBAJqcGAAAAbRJREFUOI3VlM1OwkAQx/sGG0Xh7GwTz7b1AaRwNhqIRy4kPRKjpcc+geEJDHc1chYPfYJ6N7I+gJFQE+UjJIyzS6FqqzeN/A/dtr/Mzsx/PzRtlYSI0fd0Ju5+wDMhHjCTMIqaXoS9QWYw3iLlvRHtLMrwKqDnNLyM4m+lReizCOjXWCgqWdPzvLgJNgnvUGNPV6IVyc7cim2SrHKDMMN+L6DhTKgBDVhqCyPWFW3KwfpqwEOAXUembeYAtn0W3ssErN+RdbxBOcBYowrU2Di8VrEdWcQrx0QjqGlx3m5LUThK4DFRNhGy5lkwp2CVHZ9Qs2ICUY1cGmiUfj7zOnBTyYAdo6a8otjzR0X1UT3uSc97kiqfFzPrMqM39woVZcoUTOhCin7QL1IoJLAOKcrniyCXwUhRboBplTYPSrYJPJ3XLS6Wd8fJqmrqVm2r6vxtvz9T3kigm3bDzPvxxqmn3QDg1l7VcasbtgEpqg+X2133ixlVuTky0Sw7/8eNF+4ncPi1oyFYy4Pk2tz/TPFELrt0w6aX/S93FMPT5OwXUvcbnQl3rWTT1nIy78akqjRbPb0DRTX3Uyvxl2MAAAAASUVORK5CYII=');}exit;}if(preg_match('~^/[-\w.]~',$_SERVER["HTTP_X_FORWARDED_PREFIX"]))$_SERVER["REQUEST_URI"]=$_SERVER["HTTP_X_FORWARDED_PREFIX"].$_SERVER["REQUEST_URI"];define('Adminer\HTTPS',($_SERVER["HTTPS"]&&strcasecmp($_SERVER["HTTPS"],"off"))||ini_bool("session.cookie_secure"));ini_set("session.use_trans_sid",'0');ini_set("arg_separator.output","&");define('Adminer\SESSION_NAME',session_name());if(isset($_GET["upload"])){$qg=null;if(!defined("SID")&&$_COOKIE[SESSION_NAME]!=""){session_start();$qg=$_SESSION[ini_get("session.upload_progress.prefix").$_GET["upload"]];}header("Content-Type: application/json; charset=utf-8");echo
json_encode(isset($qg["bytes_processed"])?array($qg["bytes_processed"],$qg["content_length"]):array());exit;}if(function_exists('session_status')?session_status()==PHP_SESSION_NONE:!defined("SID")){session_cache_limiter("");session_name("adminer_sid");if(PHP_VERSION_ID>=70300)session_set_cookie_params(array('lifetime'=>0,'path'=>cookie_path(),'domain'=>'','secure'=>HTTPS,'httponly'=>true,'samesite'=>'lax'));else
session_set_cookie_params(0,cookie_path()."; SameSite=lax","",HTTPS,true);session_start();}if(function_exists("get_magic_quotes_gpc")&&get_magic_quotes_gpc()){$_GET=remove_slashes($_GET,$Fc);$_POST=remove_slashes($_POST,$Fc);$_COOKIE=remove_slashes($_COOKIE,$Fc);}if(function_exists("get_magic_quotes_runtime")&&get_magic_quotes_runtime())set_magic_quotes_runtime(false);if(function_exists('set_time_limit'))set_time_limit(0);ini_set("precision",'16');function
lang($q,$z=null){$ra=func_get_args();$ra[0]=Lang::$translations[$q]?:$q;return
call_user_func_array('Adminer\lang_format',$ra);}function
lang_format($Uh,$z=null){if(is_array($Uh)){$eg=($z==1?0:(LANG=='cs'||LANG=='sk'?($z&&$z<5?1:2):(LANG=='fr'?(!$z?0:1):(LANG=='pl'?($z%10>1&&$z%10<5&&$z/10%10!=1?1:2):(LANG=='sl'?($z%100==1?0:($z%100==2?1:($z%100==3||$z%100==4?2:3))):(LANG=='lt'?($z%10==1&&$z%100!=11?0:($z%10>1&&$z/10%10!=1?1:2)):(LANG=='lv'?($z%10==1&&$z%100!=11?0:($z?1:2)):(LANG=='ro'?(!$z||($z%100>0&&$z%100<20)?1:2):(in_array(LANG,array('bs','hr','ru','sr','uk'))?($z%10==1&&$z%100!=11?0:($z%10>1&&$z%10<5&&$z/10%10!=1?1:2)):1)))))))));$Uh=$Uh[$eg];}$Uh=str_replace("'",'’',$Uh);$ra=func_get_args();array_shift($ra);$Tc=str_replace("%d","%s",$Uh);if($Tc!=$Uh)$ra[0]=format_number($z);return
vsprintf($Tc,$ra);}function
langs(){return
array('en'=>'English','id'=>'Bahasa Indonesia','ms'=>'Bahasa Melayu','bs'=>'Bosanski','ca'=>'Català','cs'=>'Čeština','da'=>'Dansk','de'=>'Deutsch','et'=>'Eesti','es'=>'Español','fr'=>'Français','gl'=>'Galego','hr'=>'Hrvatski','it'=>'Italiano','lv'=>'Latviešu','lt'=>'Lietuvių','ro'=>'Limba Română','hu'=>'Magyar','nl'=>'Nederlands','no'=>'Norsk','uz'=>'Oʻzbekcha','pl'=>'Polski','pt'=>'Português','pt-br'=>'Português (Brazil)','sk'=>'Slovenčina','sl'=>'Slovenski','fi'=>'Suomi','sv'=>'Svenska','vi'=>'Tiếng Việt','tr'=>'Türkçe','bg'=>'Български','el'=>'Ελληνικά','ru'=>'Русский','sr'=>'Српски','uk'=>'Українська','he'=>'עברית','ar'=>'العربية','fa'=>'فارسی','hi'=>'हिन्दी','bn'=>'বাংলা','ta'=>'த‌மிழ்','th'=>'ภาษาไทย','ka'=>'ქართული','ja'=>'日本語','zh'=>'简体中文','zh-tw'=>'繁體中文','ko'=>'한국어',);}function
switch_lang(){echo"<form action='' method='post'>\n<div id='lang'>","<label>".lang(23).": ".html_select("lang",langs(),LANG,on('change','formSubmit'))."</label>"," <input type='submit' value='".lang(24)."' class='hidden'>\n",input_token(),"</div>\n</form>\n";}if(isset($_POST["lang"])&&verify_token()){cookie("adminer_lang",$_POST["lang"]);$_SESSION["lang"]=$_POST["lang"];redirect(remove_from_uri());}$aa="en";if(idx(langs(),$_COOKIE["adminer_lang"])){cookie("adminer_lang",$_COOKIE["adminer_lang"]);$aa=$_COOKIE["adminer_lang"];}elseif(idx(langs(),$_SESSION["lang"]))$aa=$_SESSION["lang"];else{$da=array();preg_match_all('~([-a-z]+)(;q=([0-9.]+))?~',str_replace("_","-",strtolower($_SERVER["HTTP_ACCEPT_LANGUAGE"])),$Ee,PREG_SET_ORDER);foreach($Ee
as$x)$da[$x[1]]=(isset($x[3])?$x[3]:1);arsort($da);foreach($da
as$t=>$rg){if(idx(langs(),$t)){$aa=$t;break;}$t=preg_replace('~-.*~','',$t);if(!isset($da[$t])&&idx(langs(),$t)){$aa=$t;break;}}}define('Adminer\LANG',$aa);class
Lang{static$translations;}Lang::$translations=(array)$_SESSION["translations"];if($_SESSION["translations_version"]!=LANG.
1918454282){Lang::$translations=array();$_SESSION["translations_version"]=LANG.
1918454282;}if(!Lang::$translations){Lang::$translations=get_translations(LANG);$_SESSION["translations"]=Lang::$translations;}function
get_compressed($me){switch($me){case"en":return'*X.mXaM+.<-B)tm?S(E&@@e#g>JQiJpNn;.6C=W!cs.f:i02pLV$^sal!NZ7$%J%F,]sh:{/z(GTcXopJd&Ili!a#jhrFO7sGO!T->aM#kp_?1@jDb7^zjnZoq#ZnF1R!fqsOeT@zZ
:RnM7UjL;>KDBa?-U|Ky_#H~61cOYIgEMk%=)Xgz>igUxE;Xq1JLCqUB"dM"yu1B2%tWl:lfxmJ
3+X:[231!5k^q75{ng4ft!IBj@r|.q-WQLTJb$:hE55GaTGHQHu~wn<`:tY.BGff&_iS*oFxijAq2Fd]k"h7Cl.x^_"Xhtt-k)8)$&DBYuh/@)Tf-,0M_!4GvdDLL+9}t|7:xcKwyi)B04S|7Lu&iI
x4M-tdz[K5Rs{;.K?*?*4wiF$im43twaSsXW&`]GzgF^BM,37i<4*i(Bs4jqE07van|/<-
L3N&?<KN)M(Jbv`of^Z1ejM+oM*e5KRdBC$gS@+c$E;2Ly/~!mIA24AfbQX#g{vi]Tl)eqCC0W(x%0awv3!co{rDZTBTqLw_r+PP@xd,-_w$T-?QZm$MDZ/y%yhSR[4LM;)6b!G5`UHA#lgSh}f(7B3!rwopi)WN5D#8/^,3sGGD=ZWtC8_<fDl
D-/;$6x6/5g}`w9HwlwUA^PTbq,SqhrK6dQb^z"y
;/vL/A?/SFY"_2o7rGx9Hd4>M;*pQQBO=Z5f`qL+hRf@M&,m_M"A@k`]
HV5cP22+&d01Xy;rt`U+mc8%V?y0ss?~s]9^d-:w@p0So/>L.wHh1!Rm"hd6gPZ?!vp4MnURlC4mI_a76a:EA|E
?)(RoW1?YtbeBp2hxf%]C5H-m{er
`
2$?d94hbS
~>qq@->0<AQ"~Ar;%/>)YpG29E$71H}%NYJ73-obd$j
:?I>GH`Qz%YSj,kdgt:(R"<jqGMSk5Gq+T]Wl?Yem?>NG%<fn^`nB#-hnmgC<3uLEempqvW"~Hc+[_QE=;Ogv>s"%NF[lTsvD(.VoEI1PSapO&%C]3kHS46T<7xRQ^v-8FZ[I/
;z3QhfN
0+u@rmJ$os8j.26rq8Jt$5`}xR>s6a;KnS[G=h2urkhm5yl{e^3eyE*Aw,?]Dx?@#Fiuw7tdflCM2lqAAjTtv&V
o.3^!G51P!7b&>Hnn
@YI:$o8|"DEDL9cl
z$;)Fq}kSnAaCIzI*.x]mZH(K20$[
w1jbjr:r|.Y?quyvdGmV3a^89-qRYC^Dh2H2z.`B:uU/?6>u~jsEI>1X)[kEM[d8#>#&A`9FEFsh[x79m2Eet0n^#IvxcP&S
IQ>
sWXnhj7K8zaJe7)Jp~af:}*qpQ?}Cp!]"{QgL^>lkyN,bri(VvaY8f2w-TFEe9PQ!`DuSI`MmoH^6"nef0p3FZUx<|"0]4K+8Ojb:Zt#`|,"j`;W]`%X&MU{:&%qt+5NA/O-43v1JS@R7tu.P$4a>sKlf_6]Ul<n/B4)O?)ZlX!|#O-P%J1o7pw2aLTIRN+2lY?Z57#}hK[K
DtZ
l7&0ee93Sq.+qnZ3;?WLWl4t[1XE(i+I-6vexj]^yLf==wj/QSOn)bIy8LnIh:y5o;0N5K9i.HJE->[QVDw%EsF?=G8h!+((Ty_9
q8+}nDG!xep
4:,J7j_BBG,D8o+2^c@B#gx

N`_]+,Dd<otC<P3%2$!0q`Ccq*u>JLXuT.lIvgtVU`3)[Y}KP]`PQaxh.3Q-REyGGv(G$Xp?_7Q5;';case"id":return'#]f*o6PZ+#Rt7NyM5hC^W_J2d/kN/(5P6b7y8Ho`mc
`!HupIh~x8lC*K):DN$.ncB<c
iAhOX"W[63u8:,myP_tqg#B*R:lYc}%s>#MOq)$GAg^$CZ@X8-febny)(^8<V~=>;.:87TQ)K?qy]J<SX<){ye,+=kWElO,o%6UbCY7D`G$wm]y=Y95No5P)@gC![a[PwnM^t?jUW}"9ex"n+v?-`DeFVYQ!(e;81_Akx+:FER,Db=_"RN.>p6qj`rN4_*pq;E9ZoVH#N`55TUZ7
ti8iQlPPehSQkQby)j>FBr13U`JV7c`lxV$sFym6oUY#M5]2([s)R%UIqCogg*%Ny5uO%(VS:wbm:]*L>`n0P1L(/6)MpJCnZ4zN]D_<I3Q*TmQ_6s3:Qy[dzZY-~Ie=:SgmU"Py"kI^&Irdy")b+(eYQGF*3wgJpE?@8B0I,uDci`Z>V4b?k;a,Gs#<oE_fy+1/PpsvyPG@Kav;yI@8/iqCZk~HM@Zqoe=E8&A^m/Wy>I,U=OZi6TiTy`r?(EbU#U
J~-h;hr$:`?s=7/e4+GoV}ukXv4:Y{@_*.Pqa4,y@$[5w![e8>PC3fCzo"i<:-*9#oW2j)@-R~B5wAq}n}RkfZ^a:CA
l
0,XV$:2Li>hiVz^*WZraw&P5py
!f
<qE;9TCLcjO$gn/-K[vN[%Yg"]`N,-Jle8iXk*8P5Mya^kI+G0oH-ay+?Vxe/e1_3,vq]"+870g}(`T7ef`uvQvR/
njUJT:l"6^_Anr1@T/=LX~Kiyn<X^_DDUFdd#5kJMdI`y_wQj7Q#/"xh(>;t]XxV8ak0V)T|Q&i}OEk7AzaOnhBiS+$zwx?5[4j[KF45W0rkbUqfS#v1sr
qNf#0)f=D@}$T
0vqX$(Va1I)O}8?EFHu"{#"pj4+@Sp;whg{@%t%I!H5/g*Y44DW<CTV+2Hj&C
)g?8a
U
O7qnNK<%GH34[7Dp7Bd?s]A0Rsz;qGu,k7>I"!$OP=4rJ,%`|a"w$R?^1o7>a+yV~u|
asxDQqykXG4F,c
owds2tU?j.HHnbBk^%u]&q[)F_b%Dg4BPdbv7"umB|!A!,tZ(?=5c{V[dz,[QQ-RI~jpKLYs[6,/M7Ax3psYJ/i|V<__rYQ%
RdnNi
)]ze/E]AH%4LY-xXVq.h.p,5>5!b72BP?CGS9J@%P[-D1wDM;..;f9<LZ
T<D5yN3@U4!M~VAO+0/K&
}hc2
MeeR]2##/B>aE~6F/o,w]."t7`[@c$l,P/8[Jl_!1*<92-[CD&H)ibsY9OG6l-v80rK2nguOU}A(%2=,2t+*ae(K(E]*-8a(^xW1xeggI_4+%!(vI^D1p#qlk{"{99@-o|x^3BmxUCb#j_w=1%R-mL9%az_$]vFZAF=YaYs"@7$#UI=4Q;c5AV)DB!L%d&-~.1_P2BD-<JE
(UHqeXKRp87S#C<0x{_Gk{';case"ms":return')sh&36KY($sz%g
pMq0gQOQ0&`-Y!p1QX/6EY:%]%OCx[qIrM/.`F1+8a8b-<!.l{3bx]un&ZjdlH:KY]MKr$1>C=
{mh3@Dr(qf{ZdnL,iO|^Xaim6_W-([)tU10%,==
|t)`vZ!5#:~Mog@d9=^fiD"06F>Z3WRut4:57J<B
[Io7n}sm$2k~bK^bo~ir+[,~Y1o@q
2"Wg64>?r,P`
k?hGU;4bd5h6E<{g-/4UJV()]4niKy.#i`/P~]bNO6^?$th`7ou;Cd
V2)n8]K&."!x^&-EwuLj;=fm@@wSM`JH5/aB88cGaI@=A2fG(nNo+PUH+z=fa-A9"5RihL1-Cc8WBg1E$EXDb=ptdJ[}.~[s<&t/8@Q,TXw;GwTHLzEQFR,9(|^<)~6T4z/+"f0u@W.Jerd9<Ja&>R-.TgjSK#joenEroUHK3mh}ymcG3$,%rDKCpxU6;8xt5z)C&zqjyl(C`"H
5gGC+9YTyce<<Udc
3Qfqt)v9n6Poi7Iv5wGZEEyLor#uKY{C@Z5h|+|F=83Y3ROQdl11SN6[HT4@Eg9fWpEq0.<-,b]6#@FI

`;r>W6~8Ib<ik2A
PYU:UdgT_uJ/Nq@"Mp/i,FfWi]psy[oS48|h>@a(."mWr-PU
_f:8LDcDt{^BAQ*PXR1&2@JW.)QccPIX3p)Q&wV=gaU??N^Nm2K@v9#;K1u[
Ti3eqS[tHw-!9KD7g&:3s)j-CZ:CO7~JhqiO8/^7aGKpoumGu=<kaYBYmO,c6ky91BdMwc6;UH0IXv&sBo9x;R{VFnRv(kW=ZX/r">kFwl-h.v&L|uBwW#{7zb6S826)ui6ZCj"lBoj<WCmRa[fV`d%4*ROyMfwWIP(5pmOX6lS/:dqgy;6`f3.V3<FB,"1oPhN=l,*@i1h2s&1J?9dpBrUsoLyNAAb.gE/:;p
(ssk>u.8I^E>X5/P6B=Au<M"H0bK<Nt:kiXS$9`+6`DNqmggS!c"@4]WT6M$O-oU:
UD;iyGtJRU4&@&Ul,n3>s7bW,o$(J8pt[p)_r"2{Y>r4@24Jw~h+V/B)(k65=)21/E*z!
d^d</
^cDY+j4MGO1XF0pLx[]g(+N@%(hmu!(!PxupT&IV=Ca.6mG.nZ1/';case"bs":return'(]^*!bPZ+%fWLlG%]-E"3[V"g!YHwaYeS11c@W&#{Xv`<adU{!H%U9nqwx5JX]/o
bKMKEoPpJlrMwlToEgjlDdj6beFJci97?IjOQi?l;HW$oF@
?+mGkgv<eFr(k7<Acsu0`UX}cKiAF>WrM=
1BVgB"j=aEXF6i&A+oS",V!XaQBo[RY[f#6`s_M/>7;1!-8,~oehyf9MW5*M#W9odsjP),1K3@LVNg;0fP
:|5vx0H/YNUjn!3jw0i:EKC;3Rp=5agZu)Jfe7"#ucc[TQ$L?~)@p<<
Y=4yJo@av7[uAv@-.]qSqnCEPdH_dq4G/0$Or@k
D^nC"dZPrKU5,E7BS4`!Xjml5ge}oswqAoJZ=uh"=HJa-Vm_g,EVnDIB4WW9b:Of;E@/L6i+(s5.+|,-)pq16q]FBX3^>$K!]}%mIwddC/K<>we"Iw,$qC-E#&LvBfu+&B^K:TH_C:sw(_c=v73>e&Qk&",oHpC(!>AbV`HXbvK)YY+]6~0ypl&1ICaKkNOAKS+k$Mm~a7hYa`0^&Hww<.hv89Lz>s.ik#7K"
lrr./dy#,,U
uAc36n#]+DY^-qP"$#2%X86ULb5OOpn?u.plmKp(]BS@T~F`B`2a@}yN]?K=$Wj>MG/Uf;le<|YzN)>IcVvhXv@6Npvg;&IFc
nhv
wV^Mo?2ZGa7u
EOFSux3C"I{Z/jn&125lC;*[$%3HE0!Gsoji/GCRz=N3JWIm!K`sFBC56B%Fij0;!3yhAEk=r`u0KH2eod;xtKF6Ws7x$]S`P(4,96[5<S)K#aRq.ZhoxoCO$N..jReICN>+|)6je?+%:UjJK?Kub%MN9m~e}D#ZSYzvAZ)oAI-D]WgDd
ggPU$9G(0O/10WI1a
X0a_3#t"/R
77IC!TX[LFeY(5Xc)lh2h"BXh+x/RP.SB_tGV%"i,Hgt`@AJ;]<*<-(vs%KM`
:sS/5,0<p|v]DUx`:|9L(1H<18mCDNh+w4qdfkQFCA]IkOyjAS5c-&-vT:WEcb$8X^sBAQ]8;&x>Q`FMd&Dbfu_/._-XG1k^lB&4fBgm5!S^;N+Ug#)hM84)K^KPb`R&j5Jg(0]*p|mqYiXcJsp_1c<elV[W-|4RrDjJi"rVT=x(JLHEvBsQ"{rAl><2B>^c(}D-$=={Dx!xLA1"[vM3VQ[x.pGScLFbFsd#Jm3Wwmn_eD.PwJI?sfl6#b2*J3x7y7s<A.0.%xhk
79qs]htBmN/=$8PL
3L!;T:vRRUJmNn+hh_hoDmpd7iv@H=H!r.8_+I0@R0DM!s."?h9BP
m+4{4./[

%rgpWABRBG@4u:D-)%NN)O&Y(.]KAuOf%F/e/=4o)b&ylKNB94f"+yqQ+Ghx0u5rbjn5YodyAn*R+ZSttwFF+i.IoL>}/5VQkU._9Q"-Y~m#3KU%+w5#cT)9
V.}/a+UbfQn),X&oAk~`HY0i(ET"fx)_Y`
^Dgrvw:{s`-&2n=3(q
.SSCK[qp_ua+Z5DNW&3rQ6pBX8bc|
:?ODIDfF973qs;C`+yJYCBm#OTJ&9yGRS_,;BQ).J3H=$]D8QK?v6Cp
U6qW+NtBx9r[oQfgIM~-4atT4Sw^|DF5:RM4Yv";
iu6y_k2[[5k1Y=WRf"3n.Zl_&;>,*Wy7%LU[^m74+S<^wey!GuGs9T"23|9tHhVBBH4Si?BB';case"ca":return'#]]vcaQ-t%%L7hB%TWTW>%O8d;PQ_*><CG5OZ!y=4X:mVIhEHj_KLA{CVJJSc$mh<wg6X^KJ3Wk&^Agb~b}K4K!
jv[L=tMk.(9b,nB/f2W
FjT/#,=pR2RN.$=J
x>UP160&A<<YK=T^)|;|.0fb7]ak30Z!%>9~A9<VPrFN8gB6[`Tt+L!~1%rQfI^<k@B^n1wTm%siB|Q}oz&8K5]WD+M;&C9R=D@zSOu-gURTC*_*?v<)Do96S$,|"Q"c1m7cYG@9fN$qs/PXVO@b)o`lp=+zhy4roP;(Zq(6BG]R2vV(aS2ZI:2K*o-eT}<PtR"*x/y+j8<zs&.a[2uqr<hL?Fp3K-`xHHk<iX^iCaoskSR_f9yb5cQ}st)SAN$
rnA2
^c}"$8Ma!%I2?AZhr^Pt[yXU5!;v0#oYY9$xr6(;ay:pHW"FA]4:dqU$Fv)u%4Xh]b-b9LdtQ613Is6T*d/Vf<saWd"!5<_h!+g!cF3-&:
lM#w2bLm@GHXO]7%Do]7$yXK$)W,a}u"rjqH!n-0;pTqa:fbi7JMbvvLgl
G[saQe)8h6YD$.XIU4^G(qxNamsICe#UsXkGekPgg_fmlLE!A*WYb7
`UvLZqav7M8i!r^ns^6vdw>q-Ak[/-HJPv9O$tHMn#YFfVrira<{iLutJD4][k(H?d$k;zKC*/CPcc.hG;_w6#T7_SSZE@%O$|O#<KZys!M:mUM?(s7fPGE
P%ihVo@
#?VCUYEY#i"BcQN1R$"Zf*HO4
-dDu<tLF6Gg+Nri/%u(?kf-U*j=}X
RM3CT+G*YxvzcXOUw-]ou$%9vj*J&ld1h`%+J)X6Hjt(+4V:
O`=j&Dt!L&o
bbp%!V|<@.xbRe8j}izj#[]wusSs1cmx/[wWa4)->"P_@-"*:73)kR{xOREAP#uiyEb)[JN_dC7=xSSh
7FdzMvM&a
M8M"@5Xl#7!G]I*Dcfl&)1^JQnup"_iJ1a-hpUg_lW+kZ_HA6beEs4qgnm.SMi9FrT;1hr%WMm3lIQ
wiO)RLIs()Z3:Jlw-5G$+41DhO&Em
Am^^QuLZ-SQGkCeQ9e2yRa3X?,FGioGPWnZ#>",9F/i7h#
q[S_&B*rI9Be9A$|8Qd5T}Y1v!(W?.DV:p#=nNtkSet/s[5ec3P,Rk0]UQ>ljY-(HVy5-BE2pvhCRjPmqyOP3v4g$c,t5Y$XC7B,?Afc1&DBi,5BEQ>p2SG6>EUzru6`v@)kpVx[wO`Njf6OAkAPh#I}HD6#6b)mf9VwjxB-k/0!t5otu<^N+^p!_b&;-aHa8Xwj!H.&WmMbY`s)9<_`4t+U(5jf+<M_n50Ij<78%IMZ%mQ!XKJ,>,b`ia%_N
1VF,@WKSg#OfKV1@otd#-X[5.c:F"I

m1[5QW4R->j|v@R0;1Io)^F;xpeF5w+B?{Y;>"hs;QECa%;;tK4.Er/keE9
@3<4=Z>Sy][~V59&Af-cuI8=:huPq&
Dg5FKnr<?;%BUJ209^22T,@PSaW=lkR,}_@T{[>,jWELFf!7^n<UY3T*hn8]EA~4.<=>a6A02rX/sorK&lqSI"8nJfr>-][(/>$8&<#ic88lv.Ho>?F)Xq`F-+DQ4gRQ@T+jX]PAnp,D`;)dRLkM#Ez[ETETPT?TRC}.n#OWC*/3DRTkj-NRqo1';case"cs":return'*]]qdcs.!%fQfsA2.%+O9!er29Wa@#v!jSd^l$r7=H_,-wbGr2B,p3}r6DiQWYS>yQ]N+i]n%uQn_[%whmT!VCkboi"l{3Ym
HChd;UsLb,q;w"%s0-6hwfxSiZ.qCY,{yJ0ICG#8))yI2}H1lUhE@Py+6R0BUT>)T.Xael@MEbIjM[I
Cz:@[O^Iq2K}m<724p`~`%,>AHsxM{2Y^ckJm!?LOlcBP2bbCoB%x,T?NLaXt%Dkrbs6jC<{``HN^K+Pn*7Y.y&$rBCW6@WN-zNU_2tV,C.6UD0&hS2Y2cM]bkd[+su
tV/it/%Mpx.uJ,SPoY3&B|<H*Q9r(K,M??+|57N"9wRkmxfQc;c(yWPk^s`75-V9[V#_m$h%w*#qeXx&(-Rk;ypo0}
Yb:Kv*[AZ0gF*+1_al>$))o2l<a@rn3.U3e#2;jruw8fyRKlc.BZhv%JKOxpn1kM,:f0zwX[i>:T9U:PQ$/I[(/$e?5XO8ZQ]^MLRQlQDy_7yj~LiNk_~milDs^tns+%u,1?s@08xe#sR-t"t$aUNK*AWRcp8A0ihiw-b
t:/uLUw.oQD0h,w5x8EEPyMc<Yx;@st/gWk66J&qvol*E$MnTsk3XF.]K2THkcKcoOgMkiwW[X=+x`T&^@6=3>yDZamiAOC>QJ)%-mWGYWFXJEWp-Z{aC@[O
aaB$PU-bFxgd<+cjV}ma2YxDN+bc+%!8nvb}MFuur5tc3=s]/2)s(_Y+KQxD-:_X>^BNsdT,gg<:s:m8e.kl3<CHB|G"w4L_Le7LO``I(j&jmvx5%{^RUujd^m3<*eyS%"TKmo
&F>rQm.B_7SvUi1s4QdRyVg[q?l!:qGLPmvS9@}PXn/C[H&]+krffCsy@F#&)99)2hz?Adq0,jjn]xQ>%g,_(Z;pO%4cyP0`^M*KsJ*:$1)%22WY,g#1qg@a*Jgh_NR/N]1)J^R?6qTLE$Tu&/9[c+7/_Q7)hx"`!X,iw1}$iCx.S>eh}tr8Ol<to>xWSJ:0;LRS(pAfs..WZ$Vo)!.37iz.mMboVgXhxD6
PVfoA=O%"(0It@;yVp-fjil5@K!^`?E-9JaJIn<utnvpow{,Vp5MN^]PQ."I<)q.BQx`r%A#M>.or^U8*_U!
LQ3lh`yjNnmEac"dHY4gNVUw.?_b,}JfH7d?8d5^Q:T:O9x~=;[-lr#%s5PS!
1<+cbR+Sme&J!AR)Xs39@s=g-$"q5!TB[1Y{=du#dmS*I+7oN#^yGtg]gUAqwTFsAh^@+(DhKM<,ZW%knrD!?"ASE&SF,>&&x!C,GFb#)#=CFr^!hP)O01s5?20%T<+rNd5zGc_1TF%[00Z*6|jTr&gx[Ia]BEn@7Tmc(V)5[ki{Cg)~j<5`d**X^{6z)L1Z>JMkB^ukKX"I)Qj@eY?@rNpVdv$fUhM?0i2|O88(SY4XkVS@0IQ=/-7&DQ<}0_U#W]ExE;kT0v`}YvEr_./vZ4%[U$$_%:y3*H4u660([8%<FXR=S]?;=oED:VM
[I;GxufzpEn4!P+?>OO5k5k!-to.w}5>?+ELYu8S]^+7_&iZ[n?9Z9-nw;A<?p!H1
43*OM367EA),_<%&+0Q>u"H%K)(1^gX046FXh`JQOkV`wvQwXhnzhtMUl(ydsW>-,CLSE
qJ)xpFD&1~X+xGwuY?b,csmN"7s{$rl[]hPUvD2(IP7zg1#2C3?>*}.@%5]l-0@k3*uOYp]zXto@yK-W%qf59R?t%JY6[jI1eJ00P!cBQXDlrwaM(Z>vSjFqZIj;0L&YDJu#<l@di<GPQ`599Ok(qU>+pNw$+NaS&Tp5/p<&[{69c7me,Co2ITx<u}G)N`+3Iz&E*T
rQ^[Sb(egljPgHr9:dw!C?BG-wcwA';case"da":return',Z}*p6KWB:u^N?4:KsB8@[2>$Q[NJ>C3eb_.YDJ2.NFAQr5<3Ygce*7(cD/VUR0_l1t2|J>f(9Q-V)
3#gPH#azuvM8r6C!s]Ggb6m?7!$kDdf0_S
"m:p8=~Tma,8fElkzE8e"vFgD^kb&BlUtN@YJt(:]a`xrR-Gm/t`On.DleWJBNuF;?/
Y@3ythyW)1ov{8
8!BPCzyknYnz6i*DsIx^U5sd+23lB<,TcE.5*;GV^"Iir~#(7NcpfyrA:eKqcO<o6TF8kg[b:t"X4#C1aiFh((nqVH-XtKsf$wH<+RoXo?Z9%BU]2-_JuG[k
d@w?fl3PY7H]T7+Lt#?>sLn[NlQsVF$t%qi"*]
d6n^JpMp#}AbakWg!WP4[#ui3[pXX0)uoV_";Q9>,i"=kV#eHh5{m%a/dD/Wu7dOIxckH)n>$d-+K^(3-{uc;-.K[Y=:tejspZ=A4k8#>_yKS[A$e8Z,Mh<Gy{Roi=+gDhSih#>
sl44NOF
^iBEoo=xCp3Lbv:[m2ly#sQzaXqsb&Y}l-OaGGAnE!)Uf[KS0xJ!q(9NBC0<.?lt@|g*:jyRxj=Tk
"U$cm:/^BGa<x>Ljq$L5O?;m)HoGid:epH6{Wd"3#ihL1qt<6F(3L7+V_NN0*mOOvZ6Hjy()z)9ObF%ho]
m=Z#1UBc-<8^:olnRok@1sux"!Up"D}W#s7*Q5,5NAshX$`^TO(xYdKu[f
bU_^Ibe2jQeXtUM)$dsPYl(8]rp[2N><${U!mUf^*xS1va"w;,4"GkB]j;^yD8h9b?Whhk[oRyfB,)DBJ2-IkHV=t??yKf9
"JtNt$$mk+A}yRiXb}o`Y/
Do`i_BuEU`^;;&x6v2=HQ$;k><i?(KxbE_vn*wx$NxO,@bsp~-.+.-+A7;]2eE_*
#Cy5DW!~/WQF`}?9)/Qa,20HcqB~c`wod).^jsTD"5qG+O#Y2
mAl8m}FU?s0RU!iog,+9,^Ijv
,>!)M.VqmEt$@CEdb4/H.&_><|(%9km<LsA;+revM{2N
<Rv5_*z`x4avu/2Uz.[s/V<e*0SC~kRhl"Zx(PTAF_mOL>Gd/w-rJXxKAR
7}PVp[)aBqKSBt)kGUR|6-
+.>/KWttz$3"[l/uk#vD~n(n
e8yqmc)5$=?8s"b(rfOp7DZjIzP
S(Pmj$??:s$MF}Ce%F:-u0BqG"0wE~pQQp088%&,[PbNnb^RCgS==uF1l4U2_.*/3wS^CW1i7y2Na+;A9,h|[I;JWWT;U#C?T(-M+nWi<TC0j>ykR|/.o{DZP8lig"I;R(edaBhUXHj60Z;
">.G]Q!c7(T41)4|=#!_Wxdf4Q>Lr3c5^yGQ(-E!y#Oi&o=LpoQ{Fr^f%#JfkH>u(hr9Q>lb7?@Rh6$
@(s
"UuS$C[V<v"^/?OW43&QdHR{7`0"c=a?R_N}i4VW.fIpJ7[&rOOqGbay5iEH2Sf|*Dr&y%R6ZSYA]/#IEVC,;9:POA52nOL]"/;G<PohH[7:-:nc:ZMchz+[m?23dQ*uD,p%bXW(hUkj=C=2kUxOmS:2ea>yc+2Bd=&A*
Ss)=DqdUF#HM+4xoo)';case"de":return',]^*gbSZ+#>!(h<&JWE]5&>98dLSw/M6K]51n@|8Ue[q@4--i$+qIN_ym3Tu|[hqRSI`Esx$Zu!oI7B)"Wt7<Zmb;@8Ac+YU{a??/^G)w!sa-=op#P5!1+&::wYR&tTOsnB^-:EJc#/M
Bx_ryzWn::Glydo%Qa4!W)qP781(QjW+pM`Zg&XS32`A;9gnN$pK_E;LZ%`}Gm]5(^/xpI<I0lG*RKmSJO=0)hkK_uN)YD`LQZe(So6Z#X7iZ7bvnP9yYQ5EL4l:hDQ..3BXe&Bo#-E]mq7)V{AiV)#SrVYzWWiksxHSN{Mh)5D[B|.)N1LA2*q(X)6}VMQZLA;!VMW7M6PR"Zh*d4=mty,lL66lsByw$:V19ZEB0J*=w@o!aYsCwq9KjV%`Dy]l3*
k1Ck|s8VVHBru7?R
G$noRyM=)u.
u/I?QW@>S"B[==BW?t.>Jw
V&Px1,,(da*&Loli^oNWJh!q(5:5j.UZ1J}2rR5#G0+78aHi!^%B/0qYQ"9j]<R]tp%>&dx@lV<BL:sI=P&
;@`RDRDTBOR7,&l^:k-C{EgaBYXi_I+8Ui7cQ@>(."ec)Crjhk
_yv_MOn4e/Y;0R
~FffW.&+ijySJaHD&g2WSo[6Ok1ggDDYjdR.V+qNaWaL1lAUH9/Y+E3-1@t0~O(>WVp^Dl$5Qjn7J4V2>Na)bn#^Xjod7Z:sb:Nx`afnk
OK_s&i}.[9}QP^xfFZcA#As)-`tE`I_+wR
U2.HWnNcZ}`f^(7oA95#i|t70kw@,oGHfJKq[jK>g?.<(}a_/FB}UrE5g5I[9R6
XM8H1m
,r/ecX@F]-7edX#9@;^IG9&xD:de`2?I[mc8M&wVM^WgYhw&STy?a_-1h6gc{
,RMmo&tuhG.Q`,%#j-NZYIGG7*)/rOM!$"L8#(6VZ
|Q!UUrHlL_TFY5}e`%Bokh"Y.wNj
T8E=,qT3D+:"Za-h*;KU%r#kdd6sC$r<$Dt"N=mg/lDT8Iw
usiv^>n+gwn?5K7,$B*Gs]@=Pg`d%4?Q0}:Jh>ZHsKy-)$eeIb2Q+y5TVPdV%>8[8en|"K"sB&
8Zrj$C3IGTkODE?TdX"ec;)_zJQtHBke47ahsT8:5c^(e`#"q)rn56k.ubH>]#)xsi)U}i
H-V7O^@=""B"4$<w^Y=y;dqj#0Sz`FJ@!~ZN?9
u7Yrfh.s-k!Um+%TPlPXk2:lSA<0E3FHlktQV)KtM#DR
39k)J~=fEFXlmVnp%VhV.Y?a2:
aDLWG[kY<.-ZXjtGv"_$6K!*Z1t4N@k*aV&Uu$qenp<$m=;N?
xD&gWCl8j<zOoQUAo"p%oZJ$<QC"/1HObuw9g9ST0x4v$kU
]LNy-^.p3*Ty`k{.T3,8ct%K`X.)x
@OE@KRLYqjGJSD"IY^z%P##C3O:ktdaN
0$YW(oV?P),|Ef[/8-&#7nX]$fA!U6-?
)xr]M&@5PI*V[ZKpf#o)C.i!JO1b0:x!]]jXOG>BG9n.4J
XW@ws1XR=[Q<HnB)lue1CWZ<%vZL%9U!0e7.w?ZTB>dwNQqak!wEMyuebAm|7/1=kSO{>Yi+s"5W>R>|_`?;V`tZwj[|ZW5|pHY2xw=gOJfJHVwb&F
5osQ?N4Cx$gj-G
y^6%Vw1Id%($4>X[tbh00K$~Q{9{JONvA.hg8O<>M/+N0FOOic0i%+P8l@+Hp8Jh4lBDW31XIgj
[vJ[a?W)vIS2:a6p$WO};9vR//[x"|-Hc)OFlFAdDW=ko|Jb-8o)&Ve_NImD.ay"c=%WScA
F<KwooT:JA4]FZPq[zbjK*P/6`0?%sZx2!m.dVjX)nU_sTog<bh=41)E88^Igne[;u`3v!"l>Q,y#uYvq.iwY"yg8$';case"et":return'*sh*wbKY(&;Y")6dxZhm$-G9G7op|0v#ypSm;bF_FLBh/y[=E%b6uVIu4bEb/!`0[%<%4-kQ2XgnA3XqNxwO8g!h(8!4S7SFfT(b|F%JQL0LQ;4v#c=Zvd}s&GSML69b2/fI6GQ+h2:L6(mkKnrAf"QBHs4T=t"!!;OG4]8:U7A_&S1/TW|8uZll]yrJz
zq7^mVrt{<pbq/tD!$l:J.L*).X,&7.tfm+#38@ZisDjF;_wu_TPw6[HGSf[O.e._kOc~WgOVrsZHfxt?jGv.QH7YNm-[wby.83W1aRx$D_7Q#w<3gf6N(/.~j=(vyG3Mn/(k9p7Uw$L=/]22A!
[IWtKsmP@!y?4,fy=nMEgc%!<)wJ>!;<NvPa]u>?I[ne{pTj#fP;iI
<&le8z?I5M9PWw"q3MucM0;"kr#l@qk?YxBznNFS3Ru@:s1w1`-)6:ts`grAJ8WSb1d;PNxptGNeav__L>i0s3DOlV=WW,4[["JD.XWQl9ni.4$p
xo@A1RsS.Qw(&L_PHiPAA#=3Ysy+w6nTRK[nl5LMSm`qIB4Izw_BaQ.g$oDT1qJ
X^=H"yIVJ&]a.u"0$#KFQJ+k^5%_ch&8]Vk;--Zx=/f$
F_Ycbph9O^YQs=p,!H?66D3zM)%B,_UCUg.ND{u(YDDJJH#Jg^NDVU/)r=)SUIVvXoCJ(b-tq|[Hrj)+!sG|r<B%]8T@`";4*AjKeFn#j*x8YA79^7/-O1N?+6=|1QP}<<PQ;5`&"7LahX[Bh3?dk})NQsZQlK"zeNox_vMF>$>r`1%"=6+U5KQR*:XSl-0-Y~BsCD9$,{3&9tO.=KU`)Z?uid6vaI
Qmhj}t(=5)qXX!;+zjGjOe.?)$t4X--1(HQo9TX-7;,f3H_N$,]<,sGw}&0&(E12+E0mae,5~S-VB)7Srf](sDqpaf57&QK7ad~xfJ]k"?RuP]LT]:*t+VSp%Z~:2.WC>_EV6eqtH!Q';case"es":return'&]^$]cs.!;fW8tb*$#H_iNLhX%-BIi
C)QKDa.>fSe]4|4~*h04X|n1j_$+HhlY$dD;29sUiE-gp4Sfj!V5MrLG<cK#a1*v#^KA2K*xCk,N$A&7-00lX0(fPZR?]4Iwh$Kb^5o?QDXRSoGOT)n&am2mN0*{jWR
LAt;WJsxYf9,M`A!nMGk<>x9kD_01[H^2/PAW:^Dyc^:g<G~^Y!9in/7j]${H?c
Y$4iXP#pfU[OcZ$%BEfJQ0SHb^4lw,O|J++XLE9t+vG~om&:5M>U9|%a)}s-O068;%d(yEj2gNtyQi3`#O/?f`gM!mv;8KhnCyd.c,s(roHe:[F%;|M6qCunN=m3g&fbjNL_#(")gz+*!7($Z86?$S-WZq
&Mzska4N~Lgi"1Xrtf/vW#^qT=&e+w~f]qWJ4!E:bSrM5GplIx*!mI>K>3UK=G9dWhnA2w-&T;RvPhY_{5humAT0H&p.O!LH_n8yZuy![cfozY5k5]2RS8JT7`1?-2zo+q:dQNkqwrBNfF&45iDpa.D<|)dr[A7G>9W-m!pji![Z:m<YIC*"De)g)6Zv
mH[-Yp%O#UT]Fao=,MeJ;/h}kc@a]8x8F#=T<[SrT4FXTlLb,{PfYH*,Eq-bu^OD6ZV
Fc6qy=5z2#iP<rUl@|^Nj:=UIp$GA%d*ZiL[g:7R=Q8UQAL
r($F,"5%p=#2a.[jqQwH7XrhY[EMP80l<J8M*"*bc]&5=;ar:rh5c"xT#a8s[E*M<0*~SA%>kN.AXX?gCSZsX$1K!!`E<_eN:Lf}sz."&iDkZ(lA?|yxgN"mJj-,,T0^lk?qpa
[+-
v:d@E?+nI0"[.3`4#jcHu@-<BRUuDigD.!,e.ju%lsy7=HlUT<%TQ"po(_81aV.)k%JX}gJ37hP7HpS(9yUD@I#+(-DYqNds&W%:y#,K~cE(yyn(jRnkOrrJ{,eW5]lwa:V"a=5MuY_KzJn!!v~
_s8Y78HuZ5Y`</c!<ASb/aH%C;yq97|tJS@ZSG:7PH](>te[hB8@.N
]Zk
X#ceJa#D=@k0e_fH:&QsVpV`cRampnhd<7oM+8lXUbYO[~HzkUjebn1Rqf$U!,==0"DF_F+y2ypq%,SmPgYg^
I6QGL3cFfn]k?q_L][nAJK2^+IB5#68J-j_tc.Dhy;5QA4;h-WBD@V`CMe9?sf11"u>VPx/GQ:d}R`*$oUK-4l@j9=o3MZoFA^URI*3k)IcO/<y88^+GSxo*65G=%]L:+;hnf#5003@7RmMKI|<KpLu]Xq#,+Y0j,,2VuL_+QUrWqC*o^ivbxykAE$P&KKY}vDm&W1
e4OBYTu7<D03U#`NE6n:.`i0_w!=!td;#5dm/+HwXQU$IZt,4uVEM7a`4O92W8rm<oG
$--D97NW{b_p00R<-c"2c$Z8:Q$Oz-eg}ef..H6QV%YR+S~9Zt0TQYg2zqo
*dPBSP_1A6`*B]BH&l[2]%,_MRG<jSSU1/a[Lx>$e#.9[Dk--oxf4&4PHI5WB
*g_%L/1A;c*a__M&fT,;kvFA#!C8.CZZ@j-VkpEH<_=]su"0PI+0t=+DP=ZBL)gPWLEH--MTHk@f+HE:JXlx5MTWt9MRxw6>=QiuHY@Dx7StMW+XK1S^;q{a9hc+mbD
OZcc$,78hbcM@(>-op5"|R_jx-ty8=;&M6Sw_%KagWA
_K[vu(Sk;;Xh"w/VR1ASigr.u?d
L!)[tH|B*Xv8.*2%{';case"fr":return'$Zu0AboWR#?SDo90(/i0|Ger&_V%k:?sm7#RjaaPje!<$v^La0m?`way9a4rK_{Vm5xpK]pVsg&.iE}Em#W(LQ+XC7NW?uCGvDgD6%@gB;jYlXA`yC*k%3oW,/p<lHip?!{WdmFD/>Mm8>IEI$R<6LU_M[gC(JDe@q`4LGvvUVBb{MM@DY]TraxkH[Lp[XSE&Gn+j5ot
PecYX@n>v?aZufD#%<y71vp>l(t(2:9Qqjgh[HKT?~7+v@7Z4
VaB%oFBQ9JtNjg!|q.%]`MImK7AU3%JEv+"Up
J<JZDJm5RRJ[`%G2fu^:.2R6dbu~sBa(D,%(cMy%U9qbs-]qqKJXtHFs"hKC/bLlmOdlMi+"1<$;5t1NO%)8,|cJI0N[gAOx?~CfTH>|M;:$f}8IK4O](f:c8*+0e/BZSyqy7(.rOTSo-4UY=H<)V=+!pGbi<.CAuJ]i(YLH4aoziagcx%,`5No^y@_sG/JCk;s(xhZ=N
2Iju,_<tIg#76t61t$d+*DlfQ;@~(vZW!61h8L4?o3Cd.BY8-Je(PhiXLx-[aC(|h|tvdQ^e,KN>-Hnr%B@H90Rdx!a(u-!$9>$cd)(|=oi]<8lQC0AS]W
UZkx_I<8sJ{WsmEs
$T)QKjf^&ZrShnqJNI?9).Q"3`VIjVh&_N$1,9Cvn2+EjK;Y=mIa8YWMB{OG,"S8fHNSh{l&uxc42++[&<(xq:wW6*1@f2i`pHB]Y@#QBzGEht#nfg;
A["vU4mmsk)uyeB8cDVUQvl8b90vg[o[DHW&Tb4":CD
!NCkh6RORkqU3s`_?n44"Ar3?OAGowWhk]f?RZs}H?a>n;6cMP
p%*8][O6]%k6*d@:G*Y8IjPm{>L1p<@.ox5s94Z"+yDrrKTm`G9(}I?Gj)imptjp?TuOE]O/wE@5b&dd/md`%[YI(`B&0U6Sn[g#HZ5a`@nQ:uoJVgZJyX$,txixe?z<6HaYHw~0L;$
x@fP$mw!!:/M,U6)Y#YZx?.27Oo*+37J?T=3Cj+YB]kwvc$aBKjbgWJ@@e`hcsr:$c4V<.5uF#xi938j0YsNU8.nWt6c,aVN/?9t_E?gC1)8Z:=9&gO=,Sxym]B,^5#"&y&e:ee)bwwT
y#@?/N%9ZDvih|iM^S/tv5wx?g,A"`pX/BUMo-d&G.Ab0]b!#.ZQ_NSX7Sy,$(%3g2Z
2XQ%Sw[zS;i[twUd8~@;DyJo(R@5;p=6xN%e:Y
&weV3+q51K]ou`39UjB37;oSCW$@{G@;G2FLc.&poipC
q/3Z*zS&
ZjR)3.!a,8!q_e#OL#iEt2GUM0sTDK+Sy
;wl]#!R%.i.N"PP,yQ
_=&~&:?e"t;1c}SlFW+",HLQ1K:zj]4nlPPfpmI.2I?@cXG}^)4<_%`]q/o9TH3GYxVc096{?aMUIAim4
4`psQrFD6|A$l]E}43I:8auFWJ<%f|LrkxO!Wb:;MJ6_m)<l$WZ;L9pg5tUH.A"6&&%$ll7!$kkVq-1BwCL7%*UmbSf|cHk|`cv@B6e^F]MG5e
XJ[Q?K$
7N6WId?Yp5T7x0%Wz94d~%{VsuO@.3BpxHVwf0b&9;[l$UlUVsI&zIDU[)X#c%|D7PqNT7Bh*UhsL9<=S1Hq&Ul2_,Wk8&xSw-[d3x#09BrBW!Yt%6D!K+~TW9bNPnv!H,uIXdc/MEKxXgDz$8TZZHe39eZg>#kV(:<]#`g&$l8G_j|=rT|v#8Iok`ea1d6&b5r(M[i';case"gl":return',]^$]cs.!%fW8tb(9"c#aCEhW:m@c/R)!Av"B8v<24GZJS<IXw&#8j1
[Zj90)5@{UysNldi?8-+d:"`aSPbHDca<s[4TP7"(l]=LujB*NaR3op(trRFSC7U23uF{Px&zN?<^qPyz0,qRX%_SbEfk!FTTwI`]8ISr-0RlJa^k?S9)0xCt:MIN,y
ug_r{^cMiqfYo^)+$.~BWMD7P:uH3`YO1YWJL5LYov20vY%xl&s:FtnMXwe<q1xW(tQ?pd^)<j?b-Sro"JF
9:dDm6W+dwl4Bh&(LW3U=
>ZIl<xqi&O[PF`6)xXJQRPeo+T}+tC%ri0*5[!@5_7J1af@MG&p5)ZB>0`*)7wGL
UN[+A"Jz@zdoKML]o2=~jBiFu7EH6n$Xhzva7oI*4d!Fhz3^={G_-gnkb`3|g#LN?}do$(;ue$k!;,)goq%]B%8U>_P*.=+Q;]Bk,#(o,
Y.9#vUJAqg
)#7o69KJ)E:%R@qhYaq:pY{Z~G2E{I)"N8ml+L0fUxI%11j;)3R`K^^/4:R/C2/H`+sNpHQ:oe_:-Ur,Xe2$evHV#pvx;q4R-NK!>ViDFtA?>A8Ud=B#Lb;N_N6aTt+m==6D9crip4r2.*x;lY6^edW.+aWFpl$SJqFx]/wd#rnV
h}SQpibwb^4loHO8[9AA^>A3B.PV0X>^:xu-7H&!`WI!BO/jvZ(WL9et7on|GHEbsVwOy&gon9dbN@i^"LjuH0A|7"XG*)"#Wp")%-eKKq]E1Q:B,J)hgH"ZFNvw$nU;`DX+^Y2#"_4"y{5uye"CQMCh`l%<7k%uCu+|&)%SlAfo]Ogjs_]/)Ca02f`DlrczKQ_XtPFEf%1nBOEO2Q@C^=*|yiLnCTV?2WX8?"?NcmF9Z4W8qgOY<=
uIID^LRBxfw#H-0i]af9n^U3p%Z:4G?HW(4,>0B2QfQ2MAdlJBeo*^h_Qs}>0;65nhA[{o-"cos75SjL`B!m3Od1=pK`?0i;aMgVOODR?!FN5<EY+3N1=>q[D75;6NO"F,t^]#SMa5*tr
rQ~_Ds=-tG@w:Xo:,?$"AMEG8qr>ik-:@Vr;f-EefDc7nYkYQf6`B6DZP1bEml:l@ToyLi<HjQ/v4+Jlv8A!Y;jT{Y4L[?)hzy;Lv]Xi;MzUq=e`.No84y00z.nVk:]R>=>ur.(BYz"b!f[
JZ.uz=Q,@6|[2({oSXW5DZ<=Ao%XzJ;&^%S"VWse?D"k3R2%Jil3ZYs1$y/k4!Nmk(M#1rZ@M;&WU&/f}L:FVvl"4E<Dqw.hp1zHt%<+Jxv>QbX<#wLaimr5Jm*l&rc<U?4g"ZR<,h_aqfV49V~TY2Y.96I3c1/8mdKhue{3BUK!$ikv]$3s{;1/fu[Nc<tV.sM=%41b$"HJ}ef:UiSE91cH?mXB,WTofcZ_Gm1c}@>Cm/j`An/b1`MC)7ZCO8[f5vjVm-24?V&o_<i:pd_2P<gjIV|3oYMx3QX%}l2niYb2"%uMf_qCluTTxm.LtD{&Ltp&
R8nbj;?rX)YNhq884Kfwl65ELcrAL^W$9pfJsm7ifbmIJK]A;-/525&"jE2dOLmZer1*KkQw2]bTpN!2@MJ`V28Nw-(,T,MLR}Y8Q$>W@
9]gGBXLmygd(';case"hr":return',]^*!bT.!%$QzlE"r/5#&6,&1ogdZxvhvnC-,Xbq!,(P7V|%bd+rSj:Xx(s9Y7[y5rU/bTYd/kJ9~:UTrL7?G^+b?6S$O$XQ=tQ"tJ)><p5crH^,(<<J<vSx%tMA
U2/X8yYC.#VI^$!MMkAxM=DqKD!)wG=SLIZ"";D@keKV/S_<KX)Ekyx#FrB.WW>%K;2W8FK?#sSPdT_eDvuRSnh*!Oge.9OSIbu<($<
?.!8B!j7vlFYC$y1Tp,@=/-^Y^5BU:g+6oWBD-b$jz-]>"*_k*=Q%y
$`
KU!5Jh]/TCVw7"UMmLEz=&.I.%SxpAc)QznR">j5PMXmwbpBp|7DS6>e!eosB&$_N;Z!<{Fhc,ZsPl.0*v1vhX?*r?6`fT$;58^$`;,t>]G?1N4iOcvL!:$vr{pi(AkYR6@r6+q"j#o%`b_TNB=$s6Z<K]nGdl+J-+jlLxRsOk,{Y|8)a(Jw*kn"M!UhbMV3$DbNGQi|VH
UH#AyuMnXZnQS?K?p"b)4eMo/in^RwqC)9@xKO4VH(IQCe4n^:BOhLE7mpAP7UPbRHJ^sHsS40DWSVj/u:GmU([Z0Z]b(S_mz:/t7*u#7)m.[4us<NgvdRL59aNtk+_DWI3EW+xJ`7=-<=ybOp.:vF{(pe/6L98Yc2aTTwsM{.sD
RB^UwwwXpgp+R_Hbr"C-F^t)7edOU{]qg`Ex1ii0-;>$E~I4U,_BiEOiD+W/Y1&sqQx{eg%P)qXot~:><vY"Y9U@-FmE8NO1`67hBV_X)Hy+BZ,%w1I?_y;mBmfH>/W5L:b+rqq.l^$sJ&IxOnjpv/j[LRyv?RTRc1u%[q7v_{!NQ&qI#M#Nb]jad!f;wZbkNO<vxe+feo"N(aRTCX$rt{u;rDqw!%LRQoWGOwEpSgvyVTgC#8:,*s-ui:!IbI]?5evhSlVm:bDYlP356J)1njAZLd03jDZ*Sot`F}!oCBbk,fbDZn;AIsxzs4n9yb)SotO4eK,85_qXK_y&Mc`tlW?^$T@8k~4<dVD.SZJhM9!9U[V"Y|FS@?vt
l%vq9_oFl_^@<"uDIeZ
g11
1?PKUCG6R3
!|_L]_D21UmRxOH/0L&;-uQ~_t0mi;#+Y/^1TqxJ5Vj@#-G9Re,-mgNgR9=m^QNcSn8G6Jc!OAhPs{Ul8A>;T1[fAc&jb!i6rakZ*CGgPX^v`[DiV~!n%zO@0(gL#JI(+CtbjsRZKs@JdbqUmTZ#E[bk/K8KB|r(-GJWi2a{;T>{M{,"$u);ZUKx`06i#mb?,4.C.BM+1wucF*Ts94
K@>HJ;xiP$>?bf~`|#_I=+:R[2uOVS
o+^DU)NZNyNfLL9VEDyb,`k^Eymc(Ytu$-;1Az329.,_RafAZr;.U|F[5##dB>]ex8WnvOEi!x+`RXQ5quOI2.TnojcQ-%l^#~O=FIBc#X>jU!Tpq]elo2jZy1]62fYx]qB=OZHq9t"F(9[jQhZULY,.1=nydc7s#6v}]4A"y-1=^h/MG3>sW0#w0HNBp*&;^yV;%J-7qGb>SH[M
g.[
XGHK6/YCv3X/ka%Ams
F5=C!AFP,lhd]x3wJ-CU%:Uo8-/85Ga=A2jqhY5OF3<)."(iyMXk^~5q;RN%3@+f*VkU*bc]S+/uUJY@Q{JDL_N#QlH+2As$WlDrN(G
DNln%QWbx!v"tzAR/5p88<+`Jb[s/SF$Ak/#H-1Ne{Rgo7j[,@deN&Qhw?n7Mc8$';case"it":return'*]f*wbP+>&;Xx?8:JYywB*cL-.
dn/F#
.N(!ICQjj,N0rfUSP[_dmNBF0kvvcRtq>JTh*R:/oBBS)%,yut]pive"!z;@WiYO)TBm$6`&aU[r)J$-j(sm;#C}mdg=48jZo-RyK~k4$I-!$*Je*N/|l$r+Aj21e0C$[pn^i4)~fZb6y4su]Lfm-#2:B|xchSiwQfBfZGWiBA4nsOPY
qb/vhl*@kR&FPEC7E4sblyuTvKV[NFF"<&TLQd4Cw!"vfk-=M@d0e-DVF16%/">oLO~r]xK;VnEafD7rxH*Adg>wJh7dzvDC;p!JcGL2("yY@+VQoI39%g(A<_=GsR[lFG3!55lV[=WkK&zyvE6o_"9"7c4PP#ehr_Wcr$s&J>W^oCw5LYY<UOXs?4xe)=SXMo)b
&;8OB}-P`QJ%0*Wr;qWeR*pb+I-Pdeh0kZ%z%gloL;RWxmfAIOGv;gMO]uPf?zHeKB!7G~7KoL"/Qrc;iDjS>^,^&k7eK!2.1IY3#sY(4<*Cl7=AQZtw-YPw=UbsLf(+3ejlwdwFVfNetAiQq+n1@5_kL(d2CVC2[T(tLmL]6l*n"45lhg`Mh33n]ap=-Q-^t4nhtphzuwT;3P2Nk]oLOg"Z:62Yf,g%(0>4$VBZ/Mt9oJWRKX-Ql|1aMCrDp`Si.>tc"@jf;TAvhQrLa#cQ`DuR(ay3D:RW/b?9Rkl7=OZ,uTp1L+%Vq6;ffw+GCNw]*PdF[D.ve17N@n%CevB
,E%sYdNu18?Du|iXm"v8_;Q)sav
xs[A$d=PsoW32f^JYIT[VE
"O4#qZ)Z@.::h)peR<I*tAP%-Ht%-UESu
{kPrd`0dxCDr!A@De*8xShC+Y
_J2:W?`>&A&eD*StAhMmX$8HJsG<h6-;J>o!Y^^(vGocWa6/LbQ/R"Un`Tik,_nL-*_l%D[N/6Ji:"4n;W0!X<&B-WR2LFx:S/2Iy$5=p1)6aBStDDV]iDwwz.b2Ulof>Hp*hUy9?fRNh*#PcNt[a(Jj|m<8H_r4}/xH&J5FUfKA6Te`>_q`;2AWw9i%#4D1clX5%;pd)se1jZkfl&_r+TnZx3W_*(sP=mU58
!&oQv0t]Z:+"+.+fe@sy>JdvraNr1.e`>PlG6QOL1O.w@uQAUi8/7O*,g3bDxlY&U$-,tu~O"_,>wO-?0N
WWt"AANp<P<"U5o6Z]G:+S_ZuId{uDxg"#VmTAG)=D)z6GZAezB;iC$_;oei1tH$T#Nc>p6b$$?,
j[N"NJ<yo6ZVHII[j2^0].5r5OSgKhv/a6fJ|ZQ_D5%y=09v%$=#&&R1IvhljR-?4>$oF`}d*6~C
F!1w872]W(70nJ(^r6OPJ_TPo]q6RQ<2PE2zI4.v./P!aHC^mBp
W(?XAm1tW@hK`Zpo,o+wl^tBj>k-A=ew@HY7E2%MGbS(/m-KIwZ>ZC-um~wIr-3UXT-/nj.|r{7|;Qba]qHRd3o]tXo)sgNBU|w?L<6v7*@eZ;gC&+RGGVy,%NqQQ=e]u[qPkHEe=VPrKH9PwrW[Hsv"/yHKceEk';case"lv":return'!s`$-bWZ+%fW5mF!1*(-3!qPZ"g&-B~CEyU"pSa<A`^Qs9le!)LU#hH&%#jjz+)D!1TuE=EB`*_:)5D(}WA&sRc+zgL3TM"4%t0
o65!Ru{/`ZuRq(.@*!R*eCFSPmEVmGpvCL#r:6zY1f#%Fm
fEm1b7kyt4DBIe5)3t+^N.Gu+&X$;mNiGIq>SSG%DOP?N$y#b@TT^j7VG}6qFJ15:6Bp!H$MbScmk?h3n6[{I9"[G|X
O07AhBv7spPKdag+w0A^qy3$e@C#09YCwQDD@$4qSZs_u:/SxQ?+ipV,6])W?O<;mD(i$Yg}HaS.Rm`@iC!&pQFGnL@i[t7_n;2e098Fy|9e523M?$HqXT!k^X%6GkR,"SXR&IO25KDU%p70[#Mu;6>Qo^Ix@&1nqg8Ji+aS^qQR,.q*(~w
OFiS]~qnBMQMrPKY%N*|%:ua?3KZWe)Zx`<?KZUn3y&Fq3M%fgsm/>)J].su0s_jyLC5=2+KO9H1]?+<k5^6k
@pd2WagRENLEb48"1]S^OC^[^K$IOJAQ",g@!I$j3e/9Y<GqalE%y=QE*zAdJvZfOw]iQ=i^d=_U>V&/l]g~hw<lJiGL-~w!j#vI>{49]}"=8qr`J+;|j:LNL9&iRQB:aNl|[K27l1Kkbr-[`6Kw,k6K0+9eMYvy-3WX$D!Tv{uBjU!5etR6uAF@,Rtaurt~([fw+;S)uMJ%7zY|TTx{5JM*3]L),xMyU]yRqBB)mBkuyx>IFAX&q!.nVv=0!d!i:nj%;idt38Hu[}#C#*oqkw3X:{14PK:"M.NI0QyNZeysR4yS>,m(9F1Jt6d%OAf^C<R&#8ceh#%{pf@MB&XkNZ$u(~`fJj1mCv8nk58?X.P#.=(5%Mmf9#FcfIbLy$c5%UI=n"eQ!7yWI33Zft?unS5INC5+=z7x_Yki.JDFfM[O7?+t%K.U8U%O/Hs]QSO<1%eMHtM@NuR-9c_<-RmjsVP~]!P{MF3>ONNj/Y:"41,8;]8~!_D0?ubmQJ*}xiYHsxAq4L.?%c$7gMm?gUhjiNKsNldLK?s8V`88x3pDG=`Rw9@zFWE]
_&E+l#uQP!S0H=?87H(pWkZt|Ko0v?l(U"uWqP.OI9H@qD%EtVoDeQW-~S+27PV4+yInHZ]Dvvt+hvW
pSi>3N??1L,0of.6gZT:J`@WOsGkB+bGC3JXn+BgS5<lRY58>UE7a@<DB=XqWy;?vV<G3H!&$n(sT>/t(ro1#`)[y`j<;jt7n3=RVM;Ljo_d
,x6I^|/wb?PB6p(N
`3>fKimkQP$04ueuTy47@F60w@
,Ua&fu3.jG36H8HN#Wst/6I3JWaR+8JPH/mWM55+WePc42Y/QB!_8[p/H_7uTH>l+?nzjoHQ_E?<u}`OZS<_h:gR;7#]yKW3X7`oWk8d!Z$K_W#}F
98r0wA^0ebTg_h*RL5;w=^M)mM976OHl4p;]kfTZE}NEW}VuI!xN3rVoRCDnLXU9#l=H0V6cTarnS%RVpch6sS00pU`aP@p?yTG~q(X]LYQ33nsq2)BvQ^a}FCH.9;^^
9ZE"]^>22';case"lt":return'*sh$]crV?$uiQ)R93Pjg->jfxC6#NbhIK]A3~T}MF%LlGS-lY6~v@,v<LST]0gS>L>jOG[USy#4v/6e,nw
i%L(ehc"O0pL;X&di]oNkK.A(=a3^,wiZvBD=e0_Se_tN[AheQ2{pOr9A5,*?0CYxw%3xtTV^Bv2Ev/RFskMHS.qhNn|vJui=M5,JQ>PbgwmZyEc:M`V?mc(Zo8pm[SMZ@k^9k`@Rf!o[3T?$"5CWAc]*g-in`2Nw$W0A??55HNWLeZz:)-G0$WT^9Y~P1$vd[U?^rL^unW[!I;HQzP_nT/e^Zb4f&^y#,KEExlIrrdws;5;+PbZ66yT22*WD?
[JE<
[iBFT}*x(Ky$J
"a=c:uXE)Oy.(z8d@eKJm-[rcvDV@2>N6K.Q"d>l1!i+
2d6
coc@D_]?}3*:JT(Az+]Q^]7&d.0V~p<6`
2JyS"2Yin&(/Q:ag0yW#H)a?j/%i9X/ZU:VCAcIw^cfI4HG$4!33ldV!*:iYI?-:_h-w$b,LDmh;E(gu4DN^A"irzu+-uUU.
;+gjp`Ytt42+2B[%By3Fdkx|?#eF$Z_|vmX3doBfA~9n]<y=J3CNs<lKxb:hv=fKstQet<ysMT"7(_j2OO7Z0+Fm8S4Bk3ut#ebKk3*)2?O{OO3]k0DyXY``b
d&LV?1FOGQX1l#(pG6;IQeyisemTDKPPnw=ec9>E6=h%01c?w8IJT.VJ)8cq=M3rXSLk/J.U./bwGdo7NCR0aPK-hgyfNnwahgII^[Y`)AhOlCizoy6ho288g[0syC!x+zrT5dx[#2"QN(M+tg0WJ0Z*=P"smkj|[k6|coTAB>JN9V063T8Ltk]sQC`
+R#lYZk+C{>6<$*}DYOuV<e[[!kLbk5ZiGX(]wY
@4xI3>UKkJjs-TJyqpHl?rygZs.i"zL,6$2*(@Ia-uOL]-8["o8q^D:ZBsj}y+9c//)=qP^*
@%`W0)-gJ-U>rTX+41)1B8URfFWexNoc1)z2{*@FPfNRGqR%JEo5+(tij-fe0B><mE0!OI5G!Ns%Er]%{gKNs<3XNWwHeE!Q=w+gx5G4eBp4!3<Dy=NW!2+6gwOPQ_{a?';case"ro":return'(]^$^7nZ+%fQftg(~#
"cdi+#SZS7C-[T+y!`s?m=L9N1pNo>Fh`;6Nn18!pMv5geq{xzvjt*A2EKHW_n)%qSsRSKyCJ^15X5:KxATYDOSRr%hP[*e/O^*0saFi3:&52hF25s4:ck5!m>uoLNi-A
NWFuXRRr&#D
2]TAYvSK"<S2)3sr<uy
IhGj$f$BtP
0,:;/qqq4vqa9BH&itCX#[?BYHn?;^MnW?Vh7.<t?Y!"):PdWBu_2N%hAmc:/qZw;.5!q+"eHVR/iHXC}"IdabR?1V}fRniEyP4oPM:.U>x/-?KAc-xvqVv,"[X^Gho!$)2$q%eR1I;QlXD.,vI#HgNC]cF(
9NWEv8],4k>gfJS0ZDy3;hn#Q0`uiChr/9NCkh3xX#a
L;-B;EnFyAdB_yu$tSZwoL&>L;vo!ETHv3$d)*Ny@Cr;851@W$vQ(YjT$zY9@eZV63;^x7MG;w-"5fwMH?4-d*k,=e(<Qzx}[A"aBEvb#uC~d3;)CBtpGOalianQ]@u;R}n$fOSG"tDQdHPX8(*kCTWW/.-2od1D%z<Kgo,cL0k@J3)]W.e`F/:"
:=/C/dD"4(A*y<x5f:Ynb,Gq[F@mjp@=~*_+J=h*IF{-I>"GlRtJgwy6pZ?0L8v"k^wkw(V[0<yJ&$BV88%FUr[6.6nT|AOSJME"FK^(q%bvl3Y-;0kLk]0u%]~sGc,ZFpSEF?vNqM9FQk}bG#HdvO;d300cVU@^:pRfZQV?T=z)ZCHa<X;F+rTc>7;8lA`s|q!ZN/.)B8^im(a0/0YPdq2$0$;GdAv#]mOA[JL<LD9S`&B:/GI:vxH&#Epj:DME:,qNdMih?>=5CjWZG)WQ,%1RL
qjWU~@-J#)K54X24,dEN2I!,:,%olESg,i@`)vi9JC&HJQ.WA%0+;lpFS@F?KviTWOSVFF$$C]e_GM:?c,;pqKl]wkm/~S*/e0Y[!Vl$+$0Uyu2n[7a$g/yaascwL2-:w$PinJQN|BBASGQ"VB;^ji7Vp&Y8q)|d9$WmYp6/yyE9(=}UP=IE>bqBl^BqYtEMfc~-&IUBN
sOSCpS1PKXV$]h1Tol@bdJiyWj=W]587=Zsh#q!NaL=$0.[&+0A8N*PAM30V_eK8%awCV?qRI*o%%FirN*6Nu/C)Iu@81j4S(6!k_5$s.x/H8_l#Y4;Jg.?&cy.+0-W-k^-;I
-"Sx(,QQ@g]("(>3<kPTCEHN{a}.BvDnHH(7Op%YKik7-4J?)lc+tbR^/-ze*c6rBSUxdqH1oY!XzSc0RN2r9=eKdCX4JlM::)d8M_/y=`y8|T-h^q,a#siP>L*wH>Sts,QT@YSTIWdq=9Z11FFx,;"3MeJ4I.Ek)mJm<th(B=?h0?[k.EoJ/y~:C=%Q7fjS4<#im"z$=jxD(q<!5OfhZmkZL6&q=yol_TC`qKdPAP(*kPW;3]x5
>f3zg,==gH
6hHTS^RefW!9K=wVE?WBz8f3#8UgqK@JdQOr?L!J9DTKPnw^"(D5A%ZtQ"^7;)W7X-[+*]*0z_*ZvD"ORZ5DA
=r$@~AoNnX[$$,a[$xEPwXAhBs@1U9c_$[F6jXy6Q=]OUx*Z{E$Me]);:p7;u)Twy*c%q>|Z391k?4q0cG`qLOFS9fiCsI5
tQU<X6pA1ibo9U4IB.I^BX92eR-^vq9MFg>m]q]3la]p9x|N)&Ud!PS`9SRz"TOZW>yjDEp76SXr
GNJxToEtT;]9)lfMdg"iodo[Z+!/R(UO@P]Xm/fYjkmEx+h-S6ByQ!8y5~GQK+Q"(IF)fxe<LR`#-k:o&hQ]A4@E^MC#"b';case"hu":return'&]^$]bsWR!,t-h8#P6F#aNF:v.adZdaHeh}cu+q*,kDgLP=!~dGCb;jAq0FT}kUo-aE+M:g2IUzSl>DHuP~-ulv7aq>c4,M*miWDYe86bc"ityD%ZWQg
BUO@*xJ-pGAGGZP*>jfA7cqzs*QDI}abORB"4YJE81;8p8H?j^3Yb56+TPL[SRjE#H1(OYdWw~ZVNd95)tj8W<4mb4O3ys:[%zmVNjmNC|=FhY]HP{X4-([)ob>QbBS#uotW/9Lhi@egaVsAd@,Z%38rpMi(x;+8<u?_+"ARZUm+iMT]s{MprI0Xq{MXL_QH7VV^x"@tM|_v$-Ajan5Na|
xa&2g;wOJ*V>Bk"P2yQ.jXEY-se-:]#G`8dEi5*Otx&Hi^RqdMPn.^}y)RTv4W43A4eI)9CR(T[gjI=3>.@y
wtcL^K-Z)
rlm&#5eKL2kku4nY,uEA_@7J?QB1+^b-K>=6o=5vw(^-(g1U]J8TsrJ]]M&PEo%P^PHbwB3!.
,X?@pa4z,4A5mR=27)Ym_|;thkNnvF/tHIWkPg?y?Oo.lVS3J![By[D8#8p~@~f*Iy6nx(D&]zS]9A]r+>`q`$0vmb9rXgMvUAHN_#A.b<7ndTr&WDYKJWBgXQWKu$jXK[o1pU5$jeMF6]";7`"Q
$]2HcE<@tEPQl@v,_wQ
rW9bo9xug:Pa`2!vb.@Ubf*N0y&[qgEXJ,"OjtlYhL}0JUr";bZF.me@hGI?k@3*6s17hcja|Kcjd$)N<y%Xb0+)DY;n^$a`@O6G`@vqv4%]<kUxZAyUO3`Dbvk:RKe4C[M`2QFH=hmc,<:95)KVxdq$^X;Qv${"8Z4V2ZzLHE84^
CY+Jq:{R-^3A>
PbH.t=zYIv"hvB{7x-ia[9c7[rcESC<&jw^tR4F;8kwWODms9#(<R*h$D;z^O=#J
=J]_7`3,w.L`lW(2E$8g$`V!QPJZ-[-p:I$TlSDIH]m+&ml=>I2@ok`u]i/4AA:dk>q#.LKlb/)u;Mdt*^[]2>G;<Im#L$@1pf$Lqyks/ii1uoE[?gYvEowt`wi%1-%20eb
%a$`/fqhI&y!y^,&$3LmmgN6[#$Ddo/9>RW;C<k_fE5
hrR6k5XP((N5=@EVGtd<IRmd0zUNTnKJ[bf`k#z!/y;BVJIFdX!;N@8(PbubE0I4v)u
DTfKCB^_.8#G&eeMwF*4G[@K@[Fpb:d#P1KK
cAps)plww`
Q,>v0oj_%kV{SX3e(E+<(2n
Yac%OIBGVBr6$e*bLE:k#V#MHz:t8W7LFmx6^TS1M#m7Rgw&U
?7Ss]b^>EA1;jfWb1B4|!.<gPS-W$%C!LEA+%;s
xYilFjmOeQ>v-c2hPEfTd.?"$<%
.bXYOKua
G2e
eFe`.l+;+T&?%r^CGu`&8,JYZ@h0>T{aG<^s**ga}/q$$U+AD(B9}79/HcE<dJL(TYoT{#&mkX++VCw"^Tv/#6(bTbW#Um,m73WW#r)"z@wJegd&f0%@6@",S_zjQ]:=)iiY<m5FroQNSN@^<DHefpy040(WKAocL@+U$xjv;#5i/gqP.Y@A+?k@c*&`-103Iq?miPT&WMopWZTaj[2p:`W?b7TO33quwnZ/PP/9eT4LJFfZ)Tk>I`y>:(s2-""2I]lVAE;RuWqAw6y84NBR_f)&J8xA-aLhJ9l^4p"5[%,>&c,Zl$aCK$fI]("X{L;j0*)f"0;W3m.N|Ad[o_528"eJ(
wt]x<@(?4;~F"l%&c,$]$K|*ZcCkKJST`gjKsN4ABx*$
SUI7mSKOS~ag-=/c!%ZeR=ELUEZS9y`SY=C%tA>li)>-7L]{-9JfGj+[ET*_^zQB
c#PL{ca@u,Aip:/fp^{Q)dbqFK<]uGTm?JdQpxEqYS`Y+^s#A
sY&"B';case"nl":return'%Z}*gaLZ;#P^=]/eP"osE$m:njg#&:,?yOy0n@|Nkp4G"@GW{jrP]-cB{9QM3SQ_t
sx0>@G9WK8ME6uQn;:TT~C/FM*Jd3[u1j&j94[GcyO*
z7_0W,iJyx^jkK7h7Dd)28oDmBt!<gABoOf2&xs*uKDrF1
^JHGLEF]K"F-LUOs
7@w@]vm`DD,%.DtHCtT[NT-(eMeQ?(~_0672o?7
.5heLv=!HI4mD7dp(-xqW3dj"qe(:_=p/a|hHTX/Xa]/aG(s-7l#<MB-%lA!mL!ny5Nc8G[9QLmWe`M%`r5RJO06}I!6%Q:Mk+5VH%dr|nOpqMAd<a8YE-Zk/]kqH79d3fky|$]@;J!I018e5&OGs#Kpn$T&{!+4Nh*R@42rkUbJq[1&e;6&i)-cT";94:Dvs7"?%F|3irR3K"uZXPVM]oQb4u5Q"M65lGw2J-y@%;k)h?u&+
6?m^%AjqrV`ye6PY)11=@FRkiOa(9Vnxx>EI)2IjBP_P^EjA=Xr?Sx4`Fn@#jd4=9;j=NNZ[Pasq,pSt%/9)clkCDs+I1]Zc#T"nM[2uWNr;}+1t+!5(ie!lnI"[4gn5)DjiMyxfA_PX0R7Gto:+BJ@@8=AV:h>_Fey^%gWv}?0tD0
5]/Dg}<^-d`fvEI
8Aojd@x6maN3q>J5PHY$,GBsv_`t2nf,vEdm&Sdo6xed7^e0I7,x5wPSBMtjX
K-(@,}QmN5^A_taU?((uJB&Y>[N}EC;hD6PPQ
->*HMmM4&sb%h6K$Q9*7.kB:#]I;LkwG,^mK#!N-wpy^WV):tD"~_
N114&<m*Ce-3HzacH6OV.<EucS->i
N*v_*fBqmo8l4::v"24.+
n1R/met%kZ"E5!iF9gLm/1]@Ni
C*NHj*!jLh2;h)?LhP~+A+062E{hAqBK
4QCWacj+#DE"k,r8y8s3eo*S5uQ]MPrEm%#!>]7B@;t[vyxEOxxKs;[2AWWld.S_da%lNr:o;><vRRd;u>$:r|wb&!Pe.|<@u*$qN{&mp{MK:sCcWc(O"^Uu`M
N8CiL!*k+I@4>-u)E_hGxq#bm>-v"#j"/j"K_gI){$PdaOe+G(,M<#UhW)+En0,3sI[nmih;.u~=YS/]Bv[.@]^K(p(6`1yw<].TkWIjBAi(8.:`}C9um8c#t?}=twcDA*Hn3@`LFFXFm<#&%qpyY-{Za:5wPRBoNhF+(Nh2>sn4tB
4;uy3iHp`g&P]r1/?%uu%cG:RLoEusCOt]]|J^@B(]@rr9t}wV`PZ.#+G(i2?:0F"_("QUdRJ02YAIF%_$^<@)%hg?0ugH9d@CXc@qQ<Y<`D_~jg
4mufa`*-j%4ZFFQIe:0mw?z.^i+jSDS#|;#Hcu$cST,WsCaUVCy_rSouNs?3FKa
DElDkUsk/DDf"gCpy=5Qa9qDSt{jLYQA_$)htM&xL>SkQwijDqnwK:fxIL"1+dw:&^Ap)jH[n-`2|WUilg7I>on@.+&>C,7Q0NU0H2^
tH.2IT+70u`9AN{%1EiEiB^l}87"H4O?%XH<eYKePqwM
f
&-e?8i:oab
*R_YZwH<d8{h{Y[n&I^dc/~=(K&gC_ENe@E"hmo%sN)#fr^wd%*u(M4iA)BvjjK5^&Z.shBh5mRw[SS$@';case"no":return'-Z}*gbP+>:w^N?48Ulk-qSi8N0B"B:SecJvZ
H%#v]xS[Wv8%9h3UnzO}gPO!tBflyy*"oP"3K?+;j
RnWzHRxUY"B
.FM[8N1-lY)nO>9{]6_SDS9x5F
>
L3PfVl,,u]sy??_e!22UF%|5A+{Z~bp=duoRbGBJ(S9.&+nh_!1F{#YX-PO+rQ<
jd;vnx%Em%|#<=YV>foW7KJr,y}-rJJ_jo+Rep&C`^6,l<?9h*Rq2,;."OF4Y:|/(Tp9A-uM2LZn-Ffkc-($hvj$SJcTaNF>{.OR4d=ZlLA_/9rTli!4%%4wLF-us(Z:I#IS[s1uqwb)8oA-dYE*ai6.&?0M^>n=]IWc0$*.C^vpW]"Exf-kIttTk-rsoI-w1=ye,NT.W!aAip5Vq2URUdI4%2_MhK_Q2I_=gBio@qO_&,6*F5+$LlIR6/bU-xE"DU8n"Hh)@72,{on/XJ0@5&6D3JPCcCZphtpMKLpEO`K+wyS<r!|fhR!^$K1iX4|$@=7ZLNF?Guq-"bXbv13KL1-ljFa#mwG(
509c7APKxZG>nJ%YW3^}=@7F*B[3fj<M,i5l#?jsjo*psPhiwXW7n27E>L/_
SigihC&v_@FQIM/l@1Gh)%i`91__nIKLwP$kKni_#yj.ucy`%Qp]ATMh:IqQ1HS7rn>WpP/5b)DA]eS<w/[^p*;]%!qa"5
nG<ahNs?Qtkki]::P}^jEP:5IQ`>H;f(V~XdsO.PyP&=u+y)&~+|-H$fqoi8/*>`M._~q51,9Sh}s=[RZg6VEa?nOmMk
K0|PyXASM)A<9tf9qDVDM(CEta[Mb?@V*#Mgd434VG5sInJFCxaag?iF7*DDoElrc+
n306IqOfFgf/xLr1+kc>K%!^h_[wv2PX-)J~x2WMVe:z%4xVQ_O/Lo337]A>Xoop*a7)>h$^-xPX@-iNEJ*/yJjoO.L5H
7NR-"kEPeRt~([Gf`NP}P(AYT*N9-f,a,`;pz#MeW[UHc167S5n%=:/LVha|TR);shO0Q
AhutDhP~p*Ah7Icipq^YQ%p"d}#t7kQ3XI"gM2X
Drf_/[g]A0p[JkisKdDt2fV<s!!mTyZ74v.n&36>5{x
!Gv<jNBF(!H%n6YFuNBg]LiV!AWh3q)&N5viJVdWvwsm5s7W-&##3W[RMZgP&@n;hvuMY+I2G@9bL{,@xl@8XM?})2(haX2F++V=_;F|%41O.7vQ_@yR=YgRrYh_Grp6.P9*9ZF^:e"i*~"i3hYN*LArZ^]W_RI~[ykC;0he)qB*Ne0!cJhZoBV,U"8CvxaFcbB.SuUg)n
0<-muS.[y4"*X*ZdkC:b^5kB8;45NLT&B]
ln@g$CW+$Pf51y1E0Pc/mVRD_[O*;kHw@nTi&R3.:OWUolVQg~Hzt(#an@]MCL.-,Y?[em`H0KdURFnamMQ]O[A9ERB7XUUGJq)dpPKRe(CVSK%8WVl2SEU_a@-8Y*3yvjtQ1$iFuKa[v_S{vYO~=y2v6s#2"IeP2[V{>1GE^h$RkvWW#K&-5&hVr<`
b=Pv5@JNS*Y$a,?4yQR.k2dA$9+r#4H*hT5_%{gB[t3Wkit8#<)^tYN&';case"uz":return'*s_rGboWR#?i6dPKCJ(-7=scy%?<P*S.Gssm&S9)M_L.Xm~ahF,c4sh.Tq}({k4-{d>)!Ufh|uin}S;3h=M)m!,#A&=Yi&>+SY-Fx`ZHRQIE)q}_TJFpS5Y1P<Vpbb<TJTQ,y3Fp+3W4M0jl9R`^v#^i5WK:[,Kt/&,eXJ=ju7&hkp>Ja$ni)(a0parxZDA
iFjM)qWOsY+]mb7IfM"Kor5^O=5WwrOLu5.pyAYfM0Bi$[EH,?sWP[rrjO#vm7SFNNPA.[4LUHC^d*}pE-8*OqTw6(/g
M3_K?2KdfJe]n$+j8epR*Ve)KQDP,r/[=t)a4?))VGvE#C*h+bt%s=t04NZl`CVbM1a8=ULo]pa_Slc]X}4Qr;skXDD`[
F::-[;Hl]^hktot&&=MWBWZ$T
vBD!94M<J<&Y.>#X$%j>"?vv(jBzeVp.9=8B)>b(F)P{e&5B85H3?)Q~(o8&mFhTHT$v[BN>_69):z!/h5P7--Iw0Zdh=0B:/s)v0+vX^:^2deqku-J
QRPWg"(RK^E5UD/lh+?vX)%ieEvnD["f"RYfQ=/<QE!o1PwHiQZXbiFEMSk@yTdBHA>jD%T?irmx7H6b2}:UH+g~L^HMtQ`[@}l9&`=dyjG{6H:z5H0)8m5ZWX`cwk7U!B.0kMu1yW@|w__S)V^,w~_Bszuq,_Y^rXikU>G`=8@<Ti?T>F.rx
9O<&VC$wmw0/S[i@"i9n7v_miOTM
QlQm].&jM6px%PS:-ebA&ch5wS}uP,@(F6gLvfDg+g(vS,@fVd@7z;}Q>*q^<,p(7D0)W7G*ZQ^t+IhmHNQsBUC(!ji51%3$rmR)Ryyyny)ubvYPs#i3yPW=>%F!>sWpP@dIs-Uidd-jnR#K%u1R"xXM.y-(%5q(|!82_g3Yj("Pt#UL4Ojauaj)#B;yz
PInQarz2j5>TCM-,o^}XK2<X9co1
Z,0xr+sexURL"l3g!Bu@N;A#>I@!u+h*GRA=e/eoBvI4nWIljVrDV$3#
imIaMQ7mMcjC3]RFj&sEY^Z9V`A<dTmAf0xFw_*,7PqH~W%oPO1Ui+`I#Fs]8w@qO5+u/uJE8i$LktJ:l=n$rOo=BPxE"S}C^bWR}(/R+nIuS!CT~QJb[h/p/RU-G98%t,DI0/-Xk$(9Uw@g$4dU|g13:KOC(5kIgag_1H0o&(R;67*=z.f>~$Kq!%mo+M@LYmQ&JFy><Q
YwU1JMP$1`$9,VFpt#hr9#NWlq]Q0{O%N9@cEr(r/Ik;LB6|4ZFk=3;d$bUZ+IT
W~nR`A?PH7*m>d=3PP6)uuyw:j6ZeRMi)OmXsMU#O1_?@-Wy>f;ePOOa)@c9cNa]+n99vKDm
ws&?a,?2^Q/A;c`$FQ#Q5QhrU%n;J_aRE)[c%(fTFgouGO|t3h(I2*4Qk>?mSA+lEopV+BtC-F29*^
1=],3rj4EvPWN
juT>Vz)flOOg4q2kC3l8A`nhy4/
*AJGt-
Os%gOVlP^r8<A*Po2aS<REF3Z7JwA';case"pl":return'#]^*h0z.!:$`sq}1!EN98$s1A;-VF4XN,f:h5$s(NT
AtGk@$N[+FUsKPI`xg&!Vya>loXr)>A6m3+=TgmuB+eZutqfmz,*!,i)aCk~%VnE>QAzd+o";MIr6}cHxAZhx2qE)tvJ:(
GJmRZ4
^
^/(Wi5vY,9q[l_`hdqf<fS7A&]G
IuK,(b@avV&!M3Odc&(tnwB3]noV]~njA%5byj#f6/
gDooGLQCzJ9+[h=j,&PyjZ~rJMo[D]L-IVLt=&95Rb"OPX>5plijlft9Tpp.DVKGd;wk=rQJ5=[kUD*DE,[FI#EXOq}xtybrjW0b#k;C+mI`Q/By$sgpj;:O_st+5fU^sw5rN?PdZtPpBGg?7r21ri0SSM2_U`LjRv{`
6M_
G-D6e)e:::ioF_i]YP[>`6%^-DIDX"/cnUtcS2Hl(_%<!i)V"ju72/HqH2K?,bOS_M1xsi
P8W:x/3"+&"0IK`-sN7vyu{KNxlHcl8jwKTGVgbtp8/;,B@[%7A$i2aFCt/@0,9Hcd6y!%Y-5QUEpO/
h`|C%<Q8ksIxa
o?y,9r0r2Wx^|eu,?dX-U*1aKtL8wZ#UV$3Q)^!LA,C2o$hY
Bk5Y,F1B)9?O%8H;xHx:ApG2hf`0m*um/)W7aEO.C.YLcuvF:R(zPJY9C#!0qD5sk_Ul/:)_9[vKrN;
tGU^wb=nU4`
cbxjqVpGKt(]J]
(1wAA#X6U?G!1R7c01t4xJC9cT4gER
R%@Y?WEy*y>z1BMXqIt>w2o&xH@*:lL5jGr-
&5H>txiu/:mu"&x9Fk2N=v|ZVf=>=dquJ3g,*k$VAicL7
l8H$HNR!y+e/d^VO@0L[L,47Rxq.k9+ce%XU-(I?YnqVDsl+n=P1u-w#1jVx,fZZ=BZSGi71kv}M(m|e[fQi]
!N2<B
v/oOkPl
$5qVfZ02a2^`ru-g*@<RnA57~PJ8#M5Y$yU*Z,Gq]s"h^E_$~er1=3J(Tl_+Tu"!1QI]V46c
7"yKAr3XBUh29teQc%bJX=#p4J/"gmE0KM$sG!2jCgc#acVf]J([$p`_>ZxaxJG,OQ;,aQJ)HD`e*ncb*TcqnAZ.9t!BTxd8bfXSw$hy*$+ZUfd0d9+I6vF=Z-0{9;!?$RfUMhBEHJy&;o=`O<,ti{FQtfhC5Ds|>}mg"GQ")ocNM8J@xb&UjDr?bHh-V#L{)x?L&AJ?bJe-
_gA"=N;1:+%-f-OHxm|u54kxyp*;pf,Tl(LpvP#NVG38P)dQf,H+M;qD`4yI"iA0*tjY(-?f0;&=XTIo512*jQ>L(wjXpAaTKBhF*mbkz%PWgppCJD@@-9axhGFY8L4Rx0^-`^k9K+QT)P!tl,jMA:AB0s[`&,;H)&d8:yw?cp~e%#
Oy>$aTKNWX+r>ZBFBxi
4=,Q;yY<c*dnqPh`tA"e&WUyC[;qYXKm8$GMt`dX3%Nx#%Rry)F,.!tBy:xH%N
?7i#kP/P+"prVs"<9]:X:I4&X-s/]Kw%;ZF;Xp+OIYb`c8uS*d*j=hBYqV66[PT6;#lJ"2&bPZ3G0iS5M-k@^n
Jc`@BE$G#S^~RvnCQs`avv<W-BkFN_&o(=tv_6wi^y
/(Zx`!/SnN6bax(Zjs{&/VEj#wjWyDs/j[PPgLIbv)qX
gv8gtKV=jyLeM2i3?Fgy.|`6Kbn
*6pro]
A0DY6]
]Wq(vl;K5dU]Xyb&^(rR^)-qtP5d.NR}iiyk`d[_(6Ww91;&EQiNeI^nNlillp@#,J-8[.Q#%pDo91mH]91Y.TcwTS!e`Pg%J^Z%"d>w4RJD&,!WHT7o+&8=DVCbZtjP4I^OIz:PBj)qmwS)wW_Zq4HV`Ni!CsWy9N=zq@N3#5uKlXdNm;Ul!4H6`ECH:xfS/z4Ng@`iv+T=oZ[ja}*1-@rqsU8U)0)@CdCTpL_Q1qVe#,i_';case"pt":return'%]^$^6kWR;f:AsC1T_g]u%%40/o:+%~To8YdU;+K<-k4Fr$EY%Ky{GAe"!]a@AUq],yKnsc?z8ZC(Ys@o8Il<LSqHc!nffrVXF0,[eO*JdkP[2YDlR24oC!;JSoa!&M$ZB`nf]4]QJ2jQpGxiF]ShHy`;gqt#:ZM[O78@6SfM3W8&8F.z%:5W"B
PD:HNxZp}p}9rH
F|Zru2nNvPI5tR]ft#T$UI7>FM:jH9mfN*Zt/MNegzI%]|FSfVi6Hr5Y@2Admiw<r>QaCLuyNWPD${1!x_>~/cSIuz*5Z448M)3dJm#ALoG*xq0V.uZLtoQSWe/_?oBG^FE}+"_px;GsBCY,oCIEMIM6_{%c[!+?
kAK.>W_
RgY0-=972s"p49`";sW
`uRf;>eApOmaiI1#Hn|D>64x]?B5{"A<
v#CaR(g9L]1`%zy/IH.[l_fC[EO.tz9JhU/|u8:-u@E1<c9BvuZWPlIP_a9j`_ty/5_F^GZ3;t8W8?j-,A:
i0Bn;Qy|#FV<tNZ+Fdm*mrFm,>;SRI>=";aD-P&&92ZRwi"Snq"
$rE!3f^|m%L]<"U|7
qWGB7t4^aid41QnVE!f!J!V`MA-UPNC9PTW)&-kP7Px
0bjS
Nq~UIe)$Wa?esL!jVp0x``84/,cPeI&by)|p&m{rR6=LEdG(Rvk[y4NHH8L[00c")-F?c=}-*_*-0@9Mky[l!_VD{taz)S<i#]|nMM#6!ZRQYsZYYJ5&,=ROKK{0V.P=Pi<D]GsKhtwGcb8&]&QO3y]MYayWiQVk|(OBRw[B]HR+&p?O@raq3!).ilAs#Mjb(1Y1{0IdfHuBil)5Dr*wJD#0|[/=w(#*A9X*qtnidkzu4<OsIH#f+*`f2*gwYMQHScihj(G"b8T$fw/1HST,[(vaTm}prg!gNaNB%fK9Lj3,^Y<%>=;3?#&OV;s=rVF7uL|NbP2TjgJCkQ#8+(Lgv.MsP^B<-s53-
s-[YHXwqI<EHOU;cbjgP1NHXkjit.g$q]@*?SQlgD2_Y9b."1qD=]u>,t.,/Gc--tWZ6Z[1cH@?sT+M=IZ=n?@E;nM"E0s<kA)w.TCzR=HY*~8n>N%#NZsO>j$~]/=lCd%JxEahV)xP9AF6^Fl(@M@v?I-,4:
Bt^8*E_=.q`@?5lp+2+wbv0!w;mZX?OmpN$L<%{39pM+[MJtYv3rHOJZvQNlEbgGP0j0$1r3Xu`s{U*djQC.E.PEL,j:=Q
!BF0vwN.bnUIk.(5mYkr1I1(%?YL7s2^eWtL&@x$-88&_#qsg]YA3:OB!%lQu_GyC|
t?0NJ#!Pdo9(?YeMEg@o/7OG"x7UGm=p!!@x&+52q9epkWM4{kBjE:!VtF4Bl)LS#"YE:Rx#
88T]xu=)J:;!T/e@
spfGb
"%pcApFd2.*r)cm=`N4),,X]JZi=4ujiHqi?AMrQVg|mt3c/s?3aVE&02MBB.:lQ>_EnNc9JG4_"lD/s~4KAQd-[u*`X.hM]HAZ!g.csh+lFwi@c|/Ua&x!(6g4^w?A?!pwz"M+;E;5iU]Wu^KCHTF=8s>+X%6wINJ#*mepKCZKWrUQ-(=x2jL.*[<4V=3kkO5]`hHRC`S(mKL}S0]T?U7[8Tq&aa7?ql3K1mSp^En&,b?z4GtJQWOZ6TOt"nw9?".#$Hy]Uk=g`JQ_aml%fcr?be=%B+#x>Gjoq|p/&,x"N^';case"pt-br":return'.]^$^6kWR;f:AsA1]bCqP$5+)+g-G%<u"cYNKIo;pg$ODU-g{Oq,}*^2kd*^6:Gd$D;287o2+lZ2Su70{.keJ/Lc$4erNlBNokh)!v,SmCgg!$$dEOo3u@r[vUvu<&~*htQZEyTdBfU9t*7/(99ZBOD^zi6s`^e/jLJ,:YZEz3r8%O,e3(r=[$%>~ee?eY^:YryW)fFIzJcH:bAL;iN
yn}XAXY]@9M.7;%x^,ba9)3nPlUQ54Yy"t:x:hk3~Zhu+6gjrng6g"=M?=.,MAs;LGe<Dm#:djHX/B`y~Zon[cL8uwxTVpPO::$husb7/his^hAd(9Ufppm(fLi=8WHaJ^%pm>YX]h*qH2c^=J;e%sdiBh=uEL0S[W
t"iB8q8%Zh?{,LtpNLvW+(*.?kdHDmYvfVl8H+bUdsV>,1OyT(-agg;2x+n_,sZkvU$X?8-BF{8qy"rA**Wzi9^=aEK]S1!Ih7En+)X}"^<Z1b[Kf%/{%So&/L$z0SQBR}fSbSQq`y]yw81-?++RR%;NPPJt(Mo6A#$PH{.=B,J&om<b%*I%39
iw[6/38M_Q!F8wIQFJFx&EI4_F,_0i=^HI3(m)FL2APKXY}0#NgqB[pemo-V=T`7SfP:eq!o^l_iCk[Ie^Hl|<DMx8>M@4Xe
Q=msO3#$J7).39b5tHYQS^fs1/t^hg;emHc{
m`Yx?beMxsScysOX|A,5x=P9Bg4sT@;waX[wdW}_*7yZS&qEqZI6R+$@{bu@AOMP-2Q]]F4"3cOyq@<[i4RMrNVRy5[a!"ST`"8]@8i"_G9S5JN@E9?,pl}^gs5sOf)f.1[Q_U;&fQSq:%yXAt)P89Mi9:cJYx+v!TIPuQ~BggJJ{sP3?p"T[*U$fH>)gTN%{v^K)&;JiPW2GYQcC8rYq$DQse%nFpS^Ue#=BMb07pLycOU/#)~/q`rX__zB4wIPWw8457)S"6.5GkO>LKe[
,>UjB+3LA
0hS1DT!#6B,,c4"U_:)UeEs6-9qx6GIIMN1^CHn0Vu/E,S`Ib$w[GA^0?e"see=[m|*z3<"PojN|U-,U$~6Ph=NS&9xtb<]@WQ7^^b5+ds6yYKBk_K/oOy>kTS49)*QkjqnXP,n}U8DplprUTL+?p~E{wgmRi^F!F$#;2P=,EgsZD(WvlTM]Nq=aHpqMjB25$
,)KUXPO=pXhN.2:Tjg?qy>YIC9%S!hEpmK`uF:!T7e9>=wNaKNn^)+u>!XNb]=JZQgf~Z>+p2/1x=jq6l+`AA!.MI5Nnp?</Z_v-O[O])[&`ZZVN<Sl=avqfnN0uc>2hlfZD
AV/K_#q9F:[$FJ|!zDJi%#A83`=3fP^)ICkG9qS/32y)]J%/8:ZZYWGn*xVc{hJDP]yLL:50_c!:S)]ldAKhUhXe@D$:D,AYx4RB)8Pge)?OP%_I;[hR
[c-vcuCUG`/cg91F;&DU/sTZNk0h^Zn//
sr!61)0+h~0}`!fg%@.8X?4B[#.%iW?*FB?cTlF<TDd}-&pF)MRjr
[9`9s/1(mwlP3%c,-~y2.aLzhX%2+E2EMFX9F$f,&F8"
e29F~HRDSqPu/6K!-^{LV0t849dlpiP%/0//e5`(,D=x)rJ>
."X`"b4^mVHf*ETgx!2w=k+bACl_MF&7J,#IZ+hk-qLA&oDkQnN&';case"sk":return'(]^*!bT+>;hAJlC&%P<"X$r4(-k`]&9"=HbdP+qWc2U:R>&[a-!!F&ALpCOB+[.?B&=#.1.mAJVZmvi#V#P/G@^evg/qbiEcdrtWUOt9%oGp%a="8=kUt1Sb/#<oJRgN9Fu#dBVX
BG_o1l&YaU3Hm=2H^0=Qt(3g;n7<5uT@_B
XZhm%&ss.e4wX.;H$v}Ji;_wO1CL~4MlX
Mw>CP5n0bt:+Be[X
LGLF:kRqV!>q.I-]v2b4a.cFD

umjP"f<fsS|v4sUSI$=p8^r:OY_&otjlbZ;Rw;n,[A},^8!oec%UH>}3
_*s:MM-^t$BgB*fh<!NS<pv=;wW1NrG(FC;RUHpvv<LCfHwI"W3JYVC3m!&Q&2M7`KfjbMWt*]yows,!=?0+>{9S!k%S<(iFLGR/t
t!sJ6`hu=I<e>N4EF6,64v)nc!Te`ugw:h,-#]Ck8s#w;;K:NHG#BJpQ70L;ig:uv-DGugvh>vnB9Mh^pTyk%5>_KO75VQ2(xZ#[,j@,FhXZ.H!~`@miw(>VD{:>9L/QxH`I!d;=NO1:)+xB%<9?@ksX^"rr!pp2A4T[mN?TTP;d0|Ig--%z5bu05b*_u^)nghTb88uOJ;nCEJsoosv88M4%(v2Ac4ptb@ikC_u(j>[Ziy#]"wbIe9ER&P!>:p_p/H@4A;uueOXx$K`lQo_Ia!X-[g:xr
_>>TtMDwydR3xL(`@,4}h2[LPfs3,hJ3B`n$DpV^VdAS$=8Lvlih6kKMI%eA9&u08Wgi3}0A.Ui_RFszyiRk!@erfgJUeY"~.eGh/=rO4(+H=b@4YV<nV7=-f+
tmxb~=*J~Heo/u!y?wP@B[O2%Bq7#wv#8T`Q%W-1H83.;FetHGM(R&jq(ym&u,5E<,[jMEmj$VnLlv4gB<OJK(:f^&L0]vAyjv&9m"+DC<"K?9S&$q*#[M#.z3+por?0xnFm$k-/ypFa4uQJI,TUb43sMCm=TPUsc
BU=JMeadExQ3~ABT0g2UUI/xABkRBmzV+*NM@wT135<s5W^4a0`$*3u0YisxMq8_00u%J,>d).]XxPA>w3Voj93Q6*DK6a>y3YjR[YZ2ePV-o+g5nuvGXo"uyz(+tui`Ke]N4k0OaouR:=$WE4UPC(JFs:=")c*FG9f%,c`69ye6gYQVkZL[oS;tc!NYN`%4%o"
P"n?:Tm5-Jj;B)l7o^C*@M#5%)RUunZV~N@^y+(QM-bQn*x_bgC=Sa-!O*sZ0U.IL9QQ^1"7T!y!B.rNMj{pXWzC?(nD6p}&O"EE5[>BD3%$OnKU;,M9AG_;M-pyv(JZW;2aG"2;K#$,XO<ZZ-{2[Gta
3/A/rv&O89n`YR@N^&Et9-iU)#46ap3qAjj"!MZk&QU#9V3G^u.O3!UsSI]=7"x
`RtU9va
qu/!Sq)YB@i5Kv6l"5Q_r65iZs?2/<Z*$|>g?j1a7rG{
i]~3TZr4]TPeCYz:m:QTqOk)I`"U1vA"5?%KRv$$wh6X-)QppeXY5Et9W77d4yz%4ab`pHW1Gf&FT7Y7U>(hqok0-?m9_M5w28aw2n"=:#(>.`(*|O@b
#(LifR[MR0:F/`cXP_^2I"kp:/4Q#nSGr*?<b~jrZt.&cYPkRa+}X}4adp+"r]XiEsF89/VBT/]qhwoo083p6}mj"r*LGSLV>
htcgT;;jokp"<"v50m`X]8qg*>Ign;$u$LNX]ZuBd[F.;d9)go]+kFCXP~?mw)5h#<bS^c>}oyIuZlIFlP>`T5%O)tk`EQBvDTUH*XuH_pPkDpT@tE8ABkUcn~d2,~4Ly0UcNO-D+ZQ~dI=5#6"f2HWiP1yC;7y[_hY+wyPzB!%D>A*?hsK1$
^KU)Sn96$qLt!>LNdg#Fj&H{[yGw*RqGX_U/+A_
,
&FHU1LSLC5dUW>,@';case"sl":return'!]f*wbT-d&;Y#?8e[D>!c!m5?*?0q]4)R-pN`1KL,;gVkiFJ+ss5B6~H<*--r@jv)Bl^5GJQ/Rb;nve<&@&b@MZnr
Kp0nqiM`cO>+b]Cc&/3GWuqGrGXS!x1.F89
LM9&&%oLcAO_hlrU*,G&#iLX~le$U[N`67Y[:!oRLAaA6f>]f
]*RhI==)%570Qj[;=^y0!RX
i8S*`rng^qXl_+=RAHqR
&d(+3*&}LkK/qJJfgy2.KpL6.aBMA%LO!7B7i")w<K]F]=v^2r[|VD$ME:M)*#,R+vn)%V2N_,y+j*Ol>ss_*GYwN87N`bX"T8"
>iL8MA+vJ:8#Buc;76.~SoI?ISvluDL)ZeH;<i_nLX(q"y+h,}c7G#$8gyqHi-p2vrf3$iw
++5k7NQ6L3]_
Jq0es<Gh$CBK|PCkf(mQ3q/2NqminP,:uTZ@UQ:oOi$1q=&b]
A;K3s_RXs7tH8M?D/<""oJ@CvYB/c#|+2[}R6gAH%(RebW=2Hsssgg:^:YYO0PFS2[mobu&$t/@xf:&!fGJP">C(q
(]1Y4p7%9iE<<D@W
lxU
Yob?ny+CJY<iMbE#W"L992h^/!!=YtFaSJ!_EP[,faQBF?JZA($rsA0cp@Lo40+/#9r79T99TZ<}h.Unp^uXMd0Y8q8J]0y5^-vHbAkkpvY7>hGawImRLF/kp)I_7er2enTCI7P%K#W6^lg|rltMG*w~c92Lp`%6+p^s,3+n);Z;KRy*G?wY<isuPb*Ptw#_IJ9;(RWz3Z.,=eb}eew?C;hJ;q^@5$6g*$+-m{L;LS<Y?"dSr=nT
e&7
2t]t2/(sV@3n/9*_G;DV@%.1I$<Gein+P@MJwl!=JJ_TjhMQJ&=H@r!MiT
(hO1Fj!~/KLuPyqa9@q|l:pbM;i>G]ulw0p,VMYv8C+&w{3Kc)>R)N(|C80
&E-*g6[G#ZF-$U@YhTU9WB+
w>5z[g5EOrQ$mG
C`}tsnxS2Mz:V
vGGcLZo9M6rgO6a({o*>:4/#zPzt`at%MpaKx51U#rPDp=6j)3MVD&XV(fn(W$Sc)Yr
76bxx$U?G/Sl)ahUfa}%qhw8(qs#I%yYHP]V.GwP4KBSMQON{8$2BJzKuNF-j+tcJK^4w(qa(qxcFg42Ab1O8E~%>Zk<uK&m]e/KQ5z/"BPxd`OK%L40do,S`Jq@,9!gFJ1fpaY,W#"@wGGAl,8qeTj8@lQq2[5>!.A2k8%w[gyhFk9l&7GO&JEThT2?;(udEADvdB6tx#<<|j;AKF%0cfgB?/v2p&Q3#083},@`BpHXxJiC*(&avR#l(]C;h%a&!b&
]*dck&wqXr*`te)(O7#M$VUki^k@<$&plnNjd!7Q>+P3lrd%$W&V9eoVQvC3%h-k]:qj0D]q3pEgr3=Jbv5UCSi5#:X@v4Q>0gbAfr0GeZEcjQ}]m[WrmZQ9w@R_6Inn-St`*a|K34rtb&hD,`HwxFSw6ikfj-DemO=QW8_4u!H!iL:K(1:CumQlv*
Fja<H[`^j@[8]+S!NG3.V<DwxO#]BkF=*Ed./;a[(}a:Mw15CdV:k=U0ER&hOb%CH}buszZ:i~IZwjGey#=M;j3x
]C5>%]pFn,XI,:M;FfSF|VIsjs"D9v+Kar(Ee:(n8dQC}o{ow$Qp-`&6GitZ|p_w_%ypU[G<>o:2=4N3E&r_H0R34"B';case"fi":return'&]f*p0}Z+:u^>rPOkEeqI(<EAgu)8+eLI8|oj$s"wnFZ|D]R
#T9Cy(n~$&@st"Udt(5<3YE2;|=(0aj9qbiDbYk&2{()Y(2]08^^JaDF_C*Y2,vll)EbfN>i:El!:D45pehCc|#$C;WNffU&7[R>M2"v&%rK4nRi
!A8((qSR)`uA*g@uEu5G#7hJKBww8=@nD[7UUBU<^)ZTfWj`;N?8@^m9harkFQb$:lLFL(=6{>B^1bHr>;8B0?^d^E@(,$@?xYo-Oa&7G!nxW+Q<s:p:RV|;hRu7f(?a,[3pC-b9:TVukh7]7_2u"y1!WIroR$`^$M1,]F)u#6#@>a
=wM?M^w~nF2UY!kh98sCJzLMhjK+yw[g=p_[:K&q":y9Af)h/$O@!MY,iFvVF73d-S6qw_(7x^vO[YZ1@-%?5>k1j{0{,oZ+eO7Onx0B(yZhg4=H1Iw&q7H&pU!1S+5SW$mm>P+TO|0In
N=M&s!k|^9Qm2iRms![.fL`UTC1Ep0/Fcq0
Z+gSJ_lctP:TU+_j"M
w%*)QHah)ymr
wi0w,DmjP{J&_Lt,"`n[7h0A>%DgD}Ga"}(Q/L+;P>k7[msQWGTj`3Xkn>LxU~x*"hGM/22Zbo)8"m$>spaNIRC:/TsVdZD:sC,n+MrJ$xe|=N#*1=*lV0Z
n9$d`;^!;
Gz7rx9!8seK|GttS"Z^PBTN?0^99-|YzCCAwD0v:C@@<@%t]ijT=+H=#;C4WGz?u:Z6+]@I6A>/8nra
nlN+vj*r!qfp%O36+rtTwIDR"F57X]*m#U5BDMBl`6Y7>i"aKZxg
81J/Lbp8}HJL=>K!%i[,XlD3`cAZ:@hiLreFoQrGv#2Tc
OC9/{4MNFx)3D${=hHm&PYjO&TOlJbIDH/Y;%4%QFZZ#}Vq[9
J.7h4b
?}"F??"h
59*h_[VssoODo3H#}<QD(Du.EPtfKMy?e_/#8=`PfSCx1;]06)EYJ8T]F/5@WwKZm!wR90b#(P}XNEDxaxJ4vl_PkC2@q3DVko5ErpHw.[FudQ.w.y^Pjc,e^K0KV>[[(.L]d-B>N?!D%N}@
iE?#<UGenDJ8;0x
.tJJr,nVe$X]&7F"OqNpQXX:/X9<8~sD&!oK,pOHHNJ=WM@kZ|BcbI,df/9FeJ,H%Q_W;:)Ni^QLNzlmvX"/`#he^`3~C[Z3y(/7el2xowdl4^bj0E#vpDUb%O/]-rQh+BRv1hB~E)`tTdD5Jrfl-@u5eYp6,(al^H`q?h^8N(k[]FOx
#VP0.h9h1kp6up~ee)Re(3Eh:9E`Yu6dKH,T/teEeH!5#qcm$(2m
p<Q4c
a@4"p[.Ni,1zM[d27^B*G%WC2yx:M=t.iQrA$-w.]p<`i3DDE1>!
FO>_9vr[F7-"cxA"[HV7#nll;^bCNQ}*$93Tq4tsh=Z9p/1/zW1Sz`iLTE9p6v&<+&^Kv7&-
miNCJB9is;YD5T!chHc8
*H{gRLG+nB;#$SY@G*JD7Flw&cP#rB9Y)6_ouy94]Jyn[SMLR,KD%o!teA3a<=,b@7L.WM.9{
}DW4qhg8_V16]Zhj1j35-K9^[^z<{(4O_qKI}[.@P?A?hb8n<lR?,"z!H-?8IhM:TE~T6Ed>{TEg{
ld$H:VA,1#eat"20=0r
=.jQcQn#ydwUqv!&dS4>mhNO+/>pgQHQ.1GvUwoML[|4k[Y5-?!=Lx~b-DfhB9QR2]hY)tS-=3s4-SO9]y"-h(0%lk6=McBmIXt';case"sv":return'$Z}*h6KWBPw^J?49XsA83`u<b.PDZ5C^YJ+B2pVky
XjJr"1!X{fxMc7aA]"9gx7}bs(A/)"oWyia6m_XckMB!!iV">wp@wgV_IvKnvI.%nLcE(Py4~dli(*i[Xvf?}oFL;,_M;Gn@]x9cb^e?yf#OP=/1t&~]soN$:/^qJ_R^h5T]_R?%Xq
ursw8:x%`wM_R2H%
"5Cv9tZ%[+LP]L!S,[LRq=f,^eaxKH5$.lkrF=6uFs
0hn~Jq`-EEA#d)h<h%YRSBYayLkqB-qfUyVu]cU$7["JmQ*GQ~K{GJ*nG28Z"]n,UI7_,QoT27E:HU0g"p4yN3>nqbc~C?%Yin<r$Lu*RBu9Ux]BJ7b{d6H|e-aRGcD;HR"P[Cr0^3w1MX%:rQaX56I@n&IinY[xAi.&w-ZqgBa@1gXXw1OpUkgxO=M`5^[G>gZ[l&8I<3%w)2!F"Bih#MB]Dz9Xu(,6LsO-HR.{b%oib;fvC(6rABd,v4*Z5%tK:a(x/|bpu.gVHLKKc:6|(yowJ1g_04,D15.wqC-qZ%RI+<X~G,h,-2:G5Ia,uVA3XlPmi[t?UX0#W=N@ppDHt](.t.OEwJ=zlSkT*"Q"MS>oZ/Z[5GIS=$,z614Qi<x7RlK+1"oejcy3+V.-CSP(e2U*U6t54I.|%*ykL_JHNMJtd-^[r{7U/^IUAdrJ8w)@
Ci]b1dgjG$gFN?sQyqmY_jjc7^HucDA&TChc+oX^q+of</Z[cf=8}I/,r({fz"Tr9r3lydm&Mt3>[NI]>Sn-y$0).d"i~p
NnSPSpYbuHy#j)pT1U,|JcS-Jh.<#CWEiVp/WH%1SixKg<&03`=xnT+v7zvO9@Hdja+u7GL_6u@=,&/(@#w7Id"v)LPMJl6C2Vez)kZ[,9Yo_Lj`,H"-.H@x9?=
D-1pQ0IT0?u|dsdjQxvjg,LhSKd!X@r|]}QE:l5M,Gp[o
@aA^XQ$g+u?8HI]8m}I|G$D_"nQO27.Ng2K/p"9)F7>|c6=L4juIn$wIlUVS*uq-fW-El0X?YIX">%7qv*qG?:#Fw,asXkBlLZnOwe5P8w,[)&Oi5l;.r0Yws`UcQWK5G"Ba8U?yDl(XFuA1Y=#yRm=9f[qV-`EB-k27>Z=E[]`&Oj<@bwCAY@y=(KSuZFK_pNCeF,.fD,-!leZ_$M$==F;>
0t;vWpv&OUP1nMPZv;5`b0%="L0KxRb8V_Zw(B^-$a)2~Y*P~Cny2yooKZd
&/Ih6YI<9fNad5Z/SxKZW:7F0<nr!!d*!1T({+dMQk1JF"-Qn8vTK7F2,$s_W;Wb8s5;F2Z,XKrIRc}/YW^+fCRQ@0h=f#)18V9We*?O}^M%8Y0_RYep)Z[E,5mLQ>_p6OKp?TF_*Kfj7F^z&.IXUTF#UPNFJK9x*ZK5t
z0$E!s.H.5Fti0PCxYg@?^E2za~iY0:LCJ;"MU=[_NdrTHfRb2}o>GPOW7TmA_fWBoJ#?G]w9^nJTOc.djR6`BC*kepaiCWUB&%@@+r8eToPRV@C72[S}!5T?Q]mu-i-:.TthbTU9AcTpo(#;Tg<AhcKp;Q]C&sv{4CUM[x3!>VuR"UqKMn4rh&xhS$N&AKk*TyKh0.t[c;SW%@;$e(-ud$3|q4M*o-';case"vi":return'$]^*?[zZ[&)XYy-PrZAb~C2ln"j;5-BagSGj_$7;#hR3/f1q7-COR$lD09W.7_>R"b}dhgrlhI$y*ySW^^M;,<iI;&FbfTFroB-c

zUNLg>)8"0_g`WI<r;lT+shD=:J8sZBnpDvg+7+oEloDn0E>~;i?,;=RBwfDn/l!BCEJwSJj@Eq1k^u8c=ltAC[_#>R)or2Zq!7Bs.euma&m2P;>5q$kN%-Bbq@ipIrFvumR"W0M-;8b~VPn+x8jpv8,#jM)^A8CQnJkmlE5#V)WYdE_UCTWEt}CF=
e&4+U9uCahBmwLv7C$AZcBG|K`80)c6e(_p`VXAPfxD9*zlF*r6,p<`r4!9GXK>MHHRiph[uT4seHo0=Ga-o8W6RvUyh>uGca?VOdJ_dI-,Ny54u]>p$ceI2<w2c].x`u__Fyq"604Dze`b5o&S0@xB_nIWXSWn1,5Nlr?^)j~@7P}(<[KqB#+OJI//^x3!zFjRwEI:
]1,=I<L"%|X-:9wShQ?6Bbt{E:%eRoKsdi_5Qz3/O"*MVJ2KNP32IV)4s:nScDL7C8ek0|HOO"Yd-M..J{t.3"fMLX?y)_4WP*aMy;tick1R9tR.!?O[@~mbX|L5"Ma-CyGiM?LJ!BMdv}gjWdOI(rUjU!,d`<LS"f=gUO^-pdp^8=@QM$;gCyO]weP{8&
DTI`cj$EgGM,q@xQW[w8>`EW*,:cdd40(<._`ft-TyslQ_8^"%0$OvePn"AwfbEyu-e=C;qnYXR0r&(X)]dK>W"!c$NQA-E8Ij{C;w+!UK7Y.@q[-=H9_>T/72wMq]BBQLy9%[@==Qz!!.7s?64$32i(];iQACv/^!1>1E
ik^]t"Y-Fao11q!$O7&*F[-FUsn>DElDl[1hff@`j!,BQ^armAlAVW;^J{8-yS4?Aj.[WyNgX82)(i.ns:AvGAvOf}rVVRNp_^wbtNKAg!Hkp7lZj{:W8xM$q&@LWOO0D4uF`BrHl_M)A
,e+b@YlMr&C^8fB>&VQ,^WWeh)&(?s!;f@4`4C#ckPNUj%;na*jBPI-M[d3
Y02V=KgLl+4b*BK](6_S>5Dk8+/g#n5]*aV3c~T;4V^5SI4N,E-~s8;FW
R^8jUTQE:_HMVWC&Q^KDw!aE[Pxie4mgC7v[CgvVxoe(qi!$P@t`P6XLYnB3=/NDGw_0:w)r)XLFezLI#{DZg,tU0.,|d&^7hCY,_BTxd%j[
L%{_&9`a[juHc@`n7w&`g`c$xp(SAdBI|,u>D*UJ}6<G.gS!YXne^D=YDj:E|NHHP6;t^iX=F/@bv:)k20DtLi>+.D:*rIfy0G$kwcC3IVY]!8q+8!Ao{N&^RSENX4$bpVI<$?+e}I,Az7MQ$@TR?mZiuXMcY[<W3(!_`G[>wQZ5)X0J
f]J/7Iv[]80g3<%g?~l_idBwg![>UWT(Tp#ER6N@q|=se;V?skL<gjSGr{">.SNFAe5m1&sooV$0[?3c
9"W&dc_tVo)D=M3[Bp320rwgim1#3ag;z@gM5/j;r$?&~!!O]Y+0w02KoCROlCi
{ycA9t{D.Q-]b!9nBbMGv!er-0.<a(6<IH&)my+2{L_@}ZRWjL~HT=<3tVkgndpm=?
?$t#r(?*@dQ-_th6<0o!0R8_bv<M&C
g]@4grS5Ap+XIo^^Pv
MN2tn:iVLYPB.0,/c)D6nX)7Tn);K*4lX&u|&f&)SRgmmAZ?"b,_(_7rX:
kMX(tHsT*4f]T=}T6KOf8oda0sk0hw)75b;/sD<<B+z>iDZpGRkH]W[`^fE?mabrhq8/{*MS/
r`@Qa%xA":L3bZ*0SYg8[^5YC';case"tr":return'"]^%A1<Z;&)Xox*+w.
"Ro-X"djNwTh!l:r#LNW[*7&GOiKa82kXv$f$)nuJHcJ/DRQjYMXw"VY/jJrN~LJq>K!rgA=c4J1PHoO7nW&:x)f@*k?+}"KILb%YZ>//xbN5~W"SrDFr#y^A7FF3/PCZo<fE=k2FUO@0|frwwv4?>Fdbah>MGL,g}0oDx2x_f&$kCP9@UlXu$AMH8/1KX!X"`hD5.?HcBjb>5PdC>HIMeR.Q*hS=Y%wk|J1r0="0pq-u`?l6IE)+}F3wp>9T>
X*^skJ2w|T<GnaMz%[9p&d!6+.65K`LKVPHVPSH`gvgvaE0`9cvG!.zdbxl98=
E1L/QPx~#+s1HE@.>PHTg/6Z>%(G:4GSFzb1Y6)8j-$](>+)-6Y,6+E0!tFqPxw</V"(oQW8WGjrW!Q1(wIk5>eBvsA@iB^x8p$ABcS76)SNq94TsGV,9-Qi5<0n)2;,q|k<9yHXSd/lusIe9XG5y<6UdyxFJ^,
4+_lZ1Zr%~Fs>[hDle0XJ&(dv^H}r!
+6Fo/0aU
$yc]5hZYnI(W&(g(9?5DHy,,"]$RKPG!,uf|.3ft6?-DdHECP;<Rr-xJR"8)Iu5gk2euPa2uaWB&k*(5/uo`Fa]VA6(/aYWV0TTXvUE?#%$vB)7>edI_WBSbeHt84+bc4+Bxi+)G]!CAwVj3a~eqfm+xl1Rhin5J,"YAuK.VdL@G&Sy5,sF/u6#KyL6g?"RlkItJwkrg9~Fq,/G@_O^-&O>DA(N&#e%q@BvPe0kC1fc6qiy[m.vAf4D`d2qnNot[u^a~MX55ay_63/iFNkrHnx79*BBUiPU6W4=WJ.C}oW@9lI8?]
`
/.-;vjY5]B,B+:j%+$y{6y"|X,2(<sdx!CWLPDaC2b"*:,mUg[J74x]m=P/EkocIqRuC">uSg$x3xh=sb&G+%hGBG%:Z!u^Vt{V?yL<"O5"J2uMaL$ws+jG{"v@h]B!f3O`L9c/X`0Y!)j;XVi;X7`1,*{>H>hUBVLcv?^Tqix(~_~9mD#y%jJ+j0wT-K31ZnmgI?e^QXtVGB7iB8%"QmB-,cx!=#nI4hnqIPA=-KWo%goUriJBs^<LkR&/by1FdQ@ha<T^BLZ3e$^vNr-!@"DFHa?Vl3-Tzpl!AJEq]/1?YTaeU6}E=,itDiiyVNj({ULeO?{n`pv`:S+"{!*%[S=eLx$P$3JG:E,:)mf?Fs@),_Zl%Au!0q,L|Cq]
-gO=8h3
<T$H`Z9wodJWD)$JT2so?P<C4gE`!#Nhb4ETaOmADzI
qlKtJ1P
>cET;]&;kNqCUI7a!Kuf_#4vGQE?SE5f6u*vYhMud(WYlKkKC^Vyq[EZN9okJJT$k$%6&w^%gX5a$PDc^[(v(MOd^)c;x)ZhEbrY`B1}<GYR$;&G3+]e%`9]A?:P$uw)J$0(F+&&8Y`@;hGhYZ:
e{:{-fEH,z5uHc`)OW?wgnyXc3U:=@&"dh
R"fo7xXdY4i4/HoE-!$co
WG1A?A_/74qW:pU&Gu`W7/TDnDiDPOQAa@eUNY5e-LnRbAXAN6&3V?s.FUV7$g#H*HqREtY6?D#td(1yy"t`0rQt><Z#vyEaC;3kg#(]_;g3oa(1_#PxK,,&gwCjV(2.u
]P}`PgjfxifM#0ed3.o5Mjn)5pn?#rfkZU5DltX`51E7)-Bp}PSRY>B/)i%JDv1ZZRAjXU?M}3|CiDg0}9`a&@DZlywa.",2|2jQ4LO&RQy5Dy{<mjPuFJ<DT`I!~ATp?,Uko9@(!;1[6SvU$F&<0P|,kM|-T9~(?NAN4;K%a5A
7';case"bg":return'#ev%8bP.!$"kp^brI_:jz_2:l$W3]@kr(?]@jd0.O*d%?.|*SQTay8]e)F)q)UFc8WIEP-Y!T$(p2_h>Mcorga6`b*px|D6t:F:F{^S?Ue(w/LOCpE9yii/
iy=9sevw$9s
?^sDEys=sdOES^7klwOrtc]Gp:7y/olmVKnvok:`TLYi%r<TZf4Uz#H(lkvhUet7u/(B_RMZ%"5#
o_KH@}-h(t"^FvYSJ]=|.x3Q:q1fcz/ty3_vX"@qyHu;`,`,>`>3QyIT^ea<<|
o8|0|=UkIFB*Ie2Eosay_9f
u$PB+__-=*r
hynJcwg&ZOHmIdAE%)nwoVD4"jXk=[+e@T2;X)<R-8[5)^NR<$oB9XU>CJafsn{RW>fLE/6skWXm.N+,u$p*HHbK|QPC87$
pY~1]hoe4)L_[>s@|C!nV?<7mGg,J-r@o(#Km!3
@WpZ$=D"}vFg7nr1ODVVV8VL^dgR0cy(N*EO7$-AA6Nef?N04HLdg0nsA53t;7e;O#4$)u>:Ki+:)w`QLnk)+g"Y[3RxA*W
(D-H
[{EW41kvxz!+_s`9c!y2R((j].D9I]w9lPxK6VBC%<9he)5Vy$.!dD"oU$`k_Ua^u5*@?chXbzwah21U#|x@7?6C6/DhFec!-]*E>|h#*J%GBHM,B6^!%K:1;;u[UZDDvkB:UCZyv7g8gq_--l]zJypJ75!"aJY2Sx+up&k{QtGKi.C~!U5S(v<{BH,l$iGr?}=XalI%9|LcQG,m^M;Qv&`upH>5M6Pt-OZ
4aewVMrS93pJJo"FV#W#1N^VML3~c$m
#M4YI`4q%g=$C91<85
31T.M"nczaiOU[tSFv!X
al^R/cdY=%M[712/&h(B;A3NCsg)lx+}1i`"k%(0<)D|#!.g9oIZ5L#<Yd0#?*?_9AlIT<ytj@!
jq&d33g`@XV<MrYPM]a+Ma/`gZ2`Rd3^BB#w
>i^^DVn&*+m2{)gX^U#g^($Uwm%$;Y%
`]Fe4vxd?j9wvC+je`0,tc2B)^@]6/~;k:n73vOUG7M].H|7,;U%U^j28[@t|[=":-BYn>H:5f0:7vXQgiDcy2,7&D+"NS{hMZ4Qi-V7KS9o1:,/l0iV(,n9)B}0{V6N^vuSbp{`!mM=P0q_.Ze2e.Upz!7`660S%qM#_8mlt*a-s#XwtEX*^%T0uoHw0I5<$XBy2Zk?A1o1K15/Y0I7-.Y&K>]=%b-)D5IUEo8lX$%dvd2rz"smt7C;QRm?YYs$[y6O->ZbZ:JKbBjdh`n!!L>nWz!Ck<L;K;(T"0t>(G)H99VN=_D+*"zLDj{>LVnH)RG&4iuaV:2Z7
V5BM["/$&O^0659uzKIHz4=,&9MUI)+Rw97?dJ^I8yk&7B6_^3Y/C_aUS4!L?`-xVT@KJ71X2BSIWiNyykI>a$mM
GId^y1!$6$!:@:R)Yh:Fv|WN#<qJ(E3MO=N;#
-EZ8c+JN5IgB(V]5[ngXT>4B?EvGXi8>G]rxDvHwOjX"9@?|@V>3a/Re0X
zDr4`DN)?4f&aRG%,Wgqm^f!v/EwJW5A=yF,{BeROIxssjee3+qw_h6LBns[}RWi4<6-[/h&G&nueQ<h
W|=gc9]#F6Rqt9rB$m?7wyw;xHc`&88c#HkVl,v+d4)^Cii{x,<Wbg^?V0:Uj0iJ&"((Rhi7OJ(hF$GCk4C0*%C8`54$Tn:xI1i=_EBh>tKzjkq|<2cF;lwE@
w7^~s1)/7+)$x=lkF-vmj~VtiD?-EX=.$S7XEGr)DX`*K:6Ik+-dlqpEmWG",cjAoJh$[&d>]}xoJ{MwEdi(gyuc.$[jKdTZ*0w7$]g2*^-2_%sm:u&YV[O/@mghw)c+Hom%30R`+C;Ma|rd[~nzFh9,JsjF#E<^l`%:XjvyfXUheg6u7<@tSc(`$;D^svm"qJ:U3Y&z$K4SfG)2R7NT3|oUC.X5)L1bQ@JA[1^uu!PRnV<iEB)w-,cT*/<c(8c3l`aG1;gts+Dp]mssB=K96:i(h?ch&kul`E;+$~q,)_,:@(E&+(%)Asc?b$_sC
(/R<7$U>Zu-OkRDP#V^5gY.f:?@cQ+RA4xna!NqxsZv>lu&J]}GD
@<g5!xwYyB-`6b
?#F-CrEK^&"tjhQUiBeStI>a*=I+F$T6#QE)D@CpRk6pEko=EM+&F-Jx';case"el":return'!h_6K[z]$:en%2{qSj3(G;Xx)4-yxH*gFlP/9eKTr>IWBC!3Wr[U-9]E
wzudG&^L5gso;]TP8HOR/WnD]i[*fevQ5j
hyDcPs*0zX{H:B_X)M0Zhat>pX[C-rw6v9@vs@7%Mp*UyBsD%$Go}kWpslJw,4O
umTx.n1E"X;:%x[v$B@M`O4M.W22+s,iRRPa5lO,=@S>g",GjM.h1?JyNlR_S_GvJHJ"U0m:+0z,5MmP{QbPD@$+^
H1)YE04GW)v^#n;/9RdpakkLRz"o"FRgF+M7]bAD7/sa9O!9!A$jImdX5RwY`8MR"/CeR-JqryN3O=^Vvo&Y]0Dj
=g5S?&rrkw!cP~ufEr1IjCM*.
%)D-Vtd
T[ghQ.H|IYGD5}"UdJjW53Mkl|x`3t4*6rq)dBmPnIZXstD$3)aBorJ20;DN2Doqbr:B^`P}=$7,_,n/We`Ru./&q5&r&ZY~?m7UG=wr(ORrfKbvhBDqw9D=;,OS?10fE;mzt:Sfn]T`so:1U[b*w[%:I{e}QHp<o/<x&40Z
DC|>0V|K,Ig[ZY"j:wI&nuG8n%R4QXOm2:/<7fUmhbT5f8z,<Y=Y^lve^6DL#?T&=YSZnb@u9$?!sd,`6`+lmE;pbjn-nR)+{&5u:`m-=2[Hw5t]_p_g19~dpCP=+a$hzhLK&nl*

KIMo7C?(rtZsn#n^K)Pk|"%99s]c=s/8.MkjvQ7g=(`x`D^qFil-xCQgbHy-WhD[92,g=Z|j2!?$Eox<:o7/m5y/J_:Qr/flhG;:akvq_g{[^F
ALM9d$%AG$k(_q`Rr+Oj6{g^>(]!_bcV%%(s)Pd<fyiV,[dxBpiSB3.emu:+Z%8*e]18)=CaK:suq,]2fN;YyJTY&~#lp";L`3b-LU^E68$GhA%xC4tvmQqOd3)ipr-Y^:ZTv%Z5i#E-c[W)1lsv83YbU?<!$6$;H`6KVbPqt#xjLs,iuzu3M1:aNlO#wae4R6.%`{Ti#KW`-%7#%$cx#Nac@91bGn(EOqh+^CXyX:3T)COVjP-/-2#&vW!Bs*^Qc3XDE7^^d>qn)Qx^Ites7xqBWzbS%{IpI5umi(0.3G2W2i;(,)"@vHI4tQsjDk75<jl>w%yx$I5@&UX
]Que0ETXs7hJm[+!@}aI#R?{jRc|a51;bk-6(
;=H]kgiAGA
H
GkN0LZE`q%"Q31:4O&PG{]8V66Hh<OS*Vk!VGpB2
7YP
sKMnoA0L)ZQup;#";]/L@u
-/=,P-Rklxr==b>OML^7IEC7.0R<YbnA|4,;9wl,-*AXEAqus<3HZJf[aWY&G=-;"M.BEHeJ)*|T}u-]s*]hJgS]m21xq^shy?
*~P"*<H!$R(vh}_aYVWmcT1
F.8Vd9=%_Gd5ZOTB;-gqm?["_F!;>@Z0OQ.aEq(^dyr<LyY5tFRG3rxIWtP0(qJ<k~MN[#D1x5uCvM22
#D2x?t%el-MXQL/F9i}!o8s+3"
a$,ZkP!=
F:%@?&SWok,7]P_Ue8?M0=r1b?#
Hq}*-i&0^Tp)MvaF-F].rh3IV;!L(L8Nh]jN2yLFae/0qMuC_OPy/`eepuRxZ_-;_wv$51tIm@2a$9L2!h7`G_Xq=F7D+xwq7LghK+DZn/<f^nCrO3]h^d5<@.<jF/U<?49
*9|rfJ4"oZp`+YSS~@RvTM[5QAW^3CuMw"@mDB)t@AHOtA5qW.BVo7uUz]n&O5!GG]HNuj|2dO(

8L<"k=hu<hrFmB5h>o^5
x
?lR+SmlD%IL#j/[4_5oUq_B?s3mARoZ%<C|(fj^c4,j]Dv{`xOO3lf9jd!pI6@PN[p@Cz,RxOE9/&a0wu_wJ($hOF*MZ:,s@M>LVsJ7)g4+#9f&f}aA?-5<.|t>!KV^rY7?#n$u(<#]M]wGP2H"!>D{[vZyTR:U++db;5dcGEc,/4Yia:`(cpg7^BUN:
f6pX4MI``[TCNQ8R9OkZGGw9q;_oKInZP@R9-;.:fK@[k9[4&#J_vcPz0W&H
nfQ]WH|tJrgm`Vw<t`Gof
>4QCHdnY8-c><9zTu(I!G
t$U?sovlxeCB~Y)S2d7[H;z6eRWQHb4J+W")q+tRJwkCe1>S+UtkGTlS4DVcdg2k+yrZ.s<c$3vLdu$qM.S37h*kX
sR%+j#|r!7NJWW2$wG&CSL_3UG^:-P?_"KnlEn@A-p4Xk#lLhW(Qd];
iOgGjHFVS
Y$I)G;,Y007+F&OtZOeq4dsdFKbP<s7-6Q^o$c+)*`4xko^LcWP9na3@>u{l)P$hO,`;/_]G-i/PIt}hYCF(MJ3c3Oa:R,++r.JkKpQ?~>VpQ;S=Jxw1Cj#0?Hqv=QMxx_8<d524s[1Fak"<OmlbWR52eP[]HVG!aL.wZy=G!x~%+tC)}5PO>3,OD5N$Lfuz)"RE/fXfm+2Y|@Xjy""';case"ru":return'&ev+JaL
a:e,/lPw3VOQ_8;cc!_th=4)UNiv`Jm.]*K0P`A?MTzO[!Y3#hepJ&%[xi6-9x&A<DDiP__etaIUE
kLORJMAsL`v*(y;mBIvb>r?mZF]te3Cq9UXh9tT.Jxzy_?PJ}/<3dPMtly@0Kf-p|8BJ0^RZX.RB44~/Mx&Jv)}8?x~oIKz5)XPy@XR&[]jxdb
E>?t[d&)nIXFQsc#%vQ}satTBF4Ox3+mMsr/e%E5Zl!&
Mr
_pu
ntpanoxTu
Q+0!odvDz%]2O/x!&Mf)(EOnz$V4lLoDgQN<Ag@#.FpL#"L^+G<d,e;Fv=a5=%%C51W[[s5Hz":xkitB&rOdp{vTM}1t6
8AGE/Gv^K{spWmeFUj&C#
Ysk~TcV<DSAJ7~!QU]%R%A
Ke,C|iW%WRnT3[@&8f5$}hXC?Ur$iJ)3,@{8^${L#"s"]Vzf+Zn[eG?1{"B^Y<1LX
*C0U4nCKMF`^g"{#6.d(()&Y`o(1;*i-5GpN)%B8-OpLOpBcs6w;~[8qu
UpWVS?Ti:@+hS+{6+0REZB1W5tgG#"ILgo38VV,$%mEl^xVIWJ6"~&oRW)(f~ICdPX;_KYCdpM{I>ZB9Gn!o97r402JhV^Bcy%NU|`nlgQ^Fsbisko*!~GEhD#<4G1?sF-ywa$iB;)yKLu$[+0f5]=Z9OLPBb`S=>T{wiO&N.WcD`Xm.a#y_IAY3t,,t6AD+*pwDX)EI!Su
Z2(_FZ&w&hA0<yYC:_ii":whzQ?>vABgHfar{*js0XO^Ao4)7CRm^(kT8&f#{x2d~0v%.q?t-v.etGE.~[1jon7dz?#5"YH6vaI@93ibU=i4OsrV(-m+W[c"tB$OY8{@:WtlEY-F8.S*PDtJ0bO8"6SvFT&I8Fm)491K!8<qW5#HZYsogw]Plh"HCS|Vz)sOgPm@bV`B:`;^+ZB>z.^W_QQ32Bbt$5Cj2++
dwA*2weXob)ol0|B*N;[[+,uSI6i8OBD!$t]ET0qk-
.[G
1C$zdV?kc|Kk.epL`
)NN=ib.pMv;36H4&^XmDU(MR^x].K_!Zcb$,!mIAnlB:9&qh
oPgFgexmt:0f8+6>).cA58kn5Sq&Jf0vfLCZ/3^lX.TxFZ,c5*%bPYiSk,Kka5Sp+;<S}Af-,u5&FRjygff^lByL"vFr:jS4ua?P($
cz8fC6cKdKoP;d&8iqg]9bJ*D3Ef`o+_)>7fl)`=3!("o*[p969*.0$y>4O#a%gqm#_y!j?w
4_o
C$k,/8B?%FaICD<B/j,h,Pqg2b<6cx(Z(s)vWtc`,fb^e=vgP%xaI3BNH-zQLQ0z"o08TxB$WV9;M3|qW!m%=d]R9w"u89RAW=G!rILagYo1~I04^!f^H^K=x>Zh91$uQF|hb)aP8CQqWnfCR5&1/SN&FL],;"a4CjGqsF@FomeSs@r-)_&rv=HK`04)4CqX(_"b1[]yBjS.15%B=77<A>e/J:bF,4B[iyp^<c`v{S9<?@kE@1=+7T}]TNq@fJ
ij9CNJu/t,DL4Fen)r(-oV%=OuxMcX4?KCUfdn#:70^D8&Y~vihf[!xmoHCoCo5?6_XqoYDzLJ:!O#r$*e,L]M@M1o`eXZcmTuTLU=(9S2_t+<6|TB&p[RbD>/HLcRVY?g5CuTl9)T+8%`)UR8LIv6apt
6MCh<}lAeS9CHa=/Y5
~@jRZglfO`u+76F?8gCJ5*Ou+PX.,>,53_hE0AP"PvF_EUVG}Np)r%(c>3U<Ng>y0X"^,R(Gb+Js&1egEE2DUfgJDfIKTP+63I4stLLQS,}a|xRye]A0Owzk7rgM-jc+T@Z01?ivG<z,@lr/+e,0*Ok/52NjWOq8y>4BH9%Axq)/BYge;OHE0f*?]?4M#8O+L>ONZ_W.Um)=r,J-)M3Seyv`=,B.ClSfYY6?0lHnoAXW5nHShHNb.Yzp&gJBlHT_1SMISkB+MRkU,rbj]s#os],Wnj"@B0!$Zu=Vi"aqx&c7BLb_&Km@GY<[eFcfO%e9ZXq_lA7G!UxO#/|HEyLo2u.
^dh[d6W5dc7JwAab"dG5A#p5&Ix^cyCBQ7HJN=yajOr=V2sLAG5hQ>waY7:+T2S_HOv
O
vv+0Xb?HxuJx@@{vn66"[lr+qVl.CJ_B{D5#oa_Uc>k;&!k)NESlo@dc7pD)>os5rGf(cB*G`+eIeh4oN&[-eH~t<0)_qhjapcBAU[,t#R5
pn
IfAKwjx#W^xa+j$An6oY8]]B=+H>PDhQ.3VSNj
"CMFl`u&RWr#),F,ER?kP0WWP;f0dC/odx^rjlx,jxh/>V"!!Jxk~?(YBRFM0PFLp%]re4l9,]2x3Crh8cPc@aPA=a4G]s]GuUr9tb">$)"=>XJjD!(/6pa<Pp"J?bw2*"Li0F*+cR=JXK*4L@H3+PF4?L_(`3rEDCN[WBxeo1Uba]IQ?eLk$UecQMvTZ**o(9$LIk:a&vo(v"%Fr8Z=|9+[d$u2%mMb?7*,Y]rNV';case"sr":return'-c0%8bP.!$"kp^b1E%K.U-6<eh@a6lIvoEX5_%$@Rd{);*`lcT0KVtt1%Rn>3@.]F<^=cNiH
?q+sK!tMrl`[&OVnkh^WJTq8z)JKE.*f7`C?@r!DF1M^#x^hA#r~u]!br
!fY#wTc1%BqR7bd;q*7b&-WZR&
rh<?46Q/`D~D$4aiBh,(x6;u^5S>oVWH@9K&Yh56rF0eT(Tt>s;61tjcq(zp?]Cl1rekcuXh328Bv`xkxa5o_ne/YrW2-31MlV68YDDxIvXUsMko]c7h@pW^.ZvtEaL-;2k5-7M#6:!O,QsV74yNX:w9(,ToXF(d~yXXO?o?/^:+rTHQ_Z(k8k_J
4WUCNpf(_=#/;9#z>rrG$qi;=SMv$ICM/l?(8I9bV`djKMr~kY9ZU?8x`V^jhiL_/-@q29V=3e`iE$.IlW^6E+t".?."hu9*,
cQRqVdK`uYARF^"&*58.`3y/Dc#Lu/M+Y{bsKU2,^ndee*%73b(NwKL%o1!L``deSIOpF~D?%!(4kDjK0V($1]X@J@H1yNEt%-P:-nh{c*7r13GcNV-jG<Ovj!$8*ICt,3ea@Cd;+m/^dAJ.!v]Wxh0Ns1#`P5XQ:~<#@;)|c*$M=ETlTi*38n3?t<^ZC#twQTnWu8NHc-ve*B&2?Otd^OAGV6pVbI<9G:N4th)b4r,hOV8/?]LjG{CeHKwVb;-vj#pkFxy*tiU;KOK9Xz)M))PEY/kMCBZNrl6f7Z
!aCCS6-h.fL12.z_B>)1~Q!s)6a5Q^CQ{)gMXW}C+>75dZJ2P95j6IA-0%F8!Ej%V=<jH2~vR,@?"8lNF@oZ]7ax;s_)5Y2xHo"7&7}v6yzjhl!sWv,:so%cs$/;d+CG*8D$5dn"^dQ7K#"0;^tU)Ca$~VSSnZZ%O(h2r1QW[&*yno(e[s((
7C0IKNXP1H*1onWzC3lQOa-;ByU<;egm8=vg`[a:3lY)M[t=@Z-aZH`%,DH7U
r/7/9b^kWIQm4n$D=<b^/BsS3EV
i{Iw*+F?jfFnHVpI?%$65k<b/)@%>Tj0M{RQ6dy2@Ds?0y7_Hf:#26s#3~1__sQ
FNm/(if}49E%5UohTDN?6ZgL#$-gG8m.ej
>o==[eO@iC"^{2(m#c`a?=ORMqSx_WoU,nCno1ld[(+j`FhJ!:ycp<6gf
%[:]@g"Pf)iUXDU?O3}b_QKk6;V
)ZSbUm^
RMnG)=M<`X[NQ74TgeG^rCqtNh94_X*DZBJ:kRL?[9e(_:KASZ
SHuPVn.Q83UQyb;Gg
m*>MN][=7ly6;`"-9XU$6Vg~PJHs=&J<E4E/CGUc
&m9#)-G2a]qfjFG*}B8U6(29dlnmRamT:!*gE0{`w(BG~]4&,lbC*Zs9>rEA{&;aV9wY|0[]EGbD[JhJsM.9#[M=PmL_ycgaay|={P[fqvH-Kz(Ac6+?g4=,)pf>bL[?f91RB1T$}M=NLonfy.)*OM))>:1T0@:OqLipAJE@1RO5GT,fiIzP9MX62G"<>6aL:3"Kx:IG;Kj:tl78|ag7N%#t;E)UimE$j/>%I@7bB;UjQ]jIcS.N(r?(O:yXu#,B^p%xk(Y1ZN?ye3%Pi^;n<vO9apbx;Wfcg<~Y4uJu4>[yV5CFAHHQmp*_Z^/6hECo8!}hniln1MZpx=RD<mh3Bw^O
lR[?u&/beW:6#2G6AI
U,`Zw_Pgf
{Dt*G<Q$[&w=+&=Ys?$?P!8
wp|Q,6?iRTx.6_A]FCp3QT~.esq87)=1zg0^)@msfy}M1@lc{
[W!EvuZFB_lr>NivjIvP0?X#.LMeS+v*^G:VaInj
s;jU&FaiP8,{`Pw
B}AYkbd+_T[TT4mdHU2oD4
xhCk(<=iAY.nd
BM&X8.]3iD?^kI}gVZg*_A2t+u34OIBYr*/iO99Ha^z^xt@Gi=ISgeA%Jc-c*H`v[kC0(ml6h3{x$5ha0,Z:nB0<Df*#>4c#KugWER"[(+2*$_:`otnx|sW[`9lq8q5m
p<[h-
S=!5woNIJ8^/`(jN4/&{TYbVVOq>_gVsk{eu?98Hr3ECh/:jw
kQ1/7([#g|-?Amkmc-KRZAxe8$';case"uk":return')ev*h5D]$$#[Dv*AhlP#Y;?o[E"1j0!onM[,Xe,S]ku++1c!qgATyTCmOLmo&R0qj&-3?I0vKt5a(QG&2OM:4m`bOl55eS!
~pUA^hUb`7ny<7;sFbUZyIZL]e`7nc{V+x"KW7{N}yiRil?3c^(yvxN!($5h!mvoQI$B3W/sznIO05x`JN=T,^MIMt+<9_;x$!iZ[(bn]C<aU^)bZEg]MbT#u>r0@#Ck~dI=PB^c}.$l-xO/W?JK7Ue5kM4
HnOp&amc?tT5YV+I%?u=C[W*?J:r3ysP/N2A{ED9mRY;wKFctCKgnq{41&Ic{qlTUJH&[3Sp<s&E{,mVH#9Uanr]m;9HPIBEr=L_&eW2BLK#q0GhUr]I%<d%zNTxq_x%LXqf%OKQoEI]A;1H=;j;.=2tp,9yNCLrMTR]lPq?L_<vM>S".?X#,X0"jw"eBorx
KFHW9{LS%2k+N^3xm~;VRhkqw4wOWqXjXXH9NK6YbSI?m5of">(U@.YR^,9++Kj^K+[ky
RVX"U;o#jX^IT7IiqH2P+L[9:?.5sUvypyZt3CoW%8fIy,(q-~
5_kZ1($QwX%D(sW:t#p"LRPv1//z&LBcN@X*<"bPVr[^~wy,)Dv$m2t8g$g4v(F9ETl+vls(t5EDoqt2pL0"O)fZ2i@TN]:3<`37i1fXp$w`)^]H1K=n~Msbj!{).(Xn!.,_d8^EDk:12Rka+2}$sH->8pKNRG%UdgijNfbleF^cY/lM9[*Q]o47tY|]Xf=ni$3<Qe-JxtM9.@O9(f}8UrlOz[2&oN``O9%,[+CP1((U=rlq)#q!T`4#mr&Iz$sbwS!d
!,t53sTg2GJ;Tb/-m9CzBNtVop4^N/G-viq-V_3wtQb6KEg685"K`p2"#MuUeO&63B,f30#agRt^48Il4g>d4[]auF7X.Qt{7gLsU.u/B5Teq[Tg6BddChQ5=:/leC&hsx2;B5gV?B/fPJ`.
`hDU-JAdV>tC^eg.c;XMCu[XN1rw+l7vb"7KpQ"i3NWs4T5P)K5L!HDrS#zwK&no<@z>leoMV?cXZF=3~?2NB?~#IahJx4)@o`<R0hUlYb7`?MP2O2e<l[;W.yd-"abO@xSC[rKc~oZk#P08DC
2GR5YoD`?GH$8<%$N~m
kSZ<0Md=;PpKW65PA]8Ns%CK
V.<qEC.=%vtlg
oc8V^wu!^kG_*j0%_/|.Be65)>~OSa;=7xURW^t3A[9oZcd+,d2JYS<b@Qa`
ro#~E$&rJQ?.cI79v{iJ/&t6]Jc,t[
kK]kMy&E}Dp!^J{wyKg3w,5F+CKL]HkN!Nb.lgm(<(<hX(2vVR%Wb&_:0c$.!#"_oPH
apZlvVhE"UBS<sG-j=39,BPBl3RXdTo@Q!
#)n+to!)xb,EI?.sI):#:|<[AF/hkDGam9$dJ4[l<Fw7V}]9JG+C[N@/CE1H]CG+^x_}R4$<T@dBpA@r8D>P,LGcG#)5=D(%xshf?d+qBr;oH+4BDxyBm)7I64Pxk=T#d@aWA4DTtG((UHRLk_IZId;OJB(AK-dcNWFY]_YEb;$UM|B<6CE;un
4L6E{`qq|B`8i1mw4S~
tQSV_;AFs<<#[^.X!
v#=r@]>:~OxVv9TWpp_:k/gW=T|[DFNsAurGH42g]fA)*>Jq@]cDpD!C6<JRbUM#fabiN#u[bp+gbaXULi-cqs|lf!0co+{@@HfLHUm)s%m7$D?e3qgpqNdMaXewF`:?U@$cIfC&O"QFH*3%dhR2=9f*w*["CF8YCUEs1iuvp6i?]">j9gfK+5;T.8$^)e}9Lep!bguwi0MU2EXd>%
b;WbDpG`9s2<_[Cl`z8/XI!!ZP<{&?+;=aqNYJb$e/7!W^gvII*b^8aF^q`A9XRM[m4Df=j;G
t}8)3;yV1WD>;.b$UtSqn}*gS&UxiR4z2nk%w]V=F;rhH[?sJ]EpS7htrC?iiq4h.<`NsxJdMs%kmJ8(:}0ndIY-/Gmydhpe(BoFEkT[j2V.&58er^3WnYWti0VFPBg8!|ANKh"wd}ub%am1]XYqw@e/G`DrYtr(vxZt%9>7r{N&tO-;Q)CCI=#uvs63)PHEHY?FVI+dWEw!)qJl
y>H.(&c3c^Rn[<-q*PI9@.?e9#Do!ucB!VtF_[;3G$9Xp59qt$|km5MTCQV?XorT[8FVWURP1N43nP49-J-poq(
qyER.]s@|Es;aWC%,Ll-`if1E`>nXAQy4FDq<ZyU^ryZdl
sp=0CCAm_U$92
Ec7kCb5ON6M>>di_[mi7pK+Cwl]lqQDYJi)LFHce@Cou;8Z<C@.(4IG):}`-b}aRvrwGk/%en093iRQcMf:X?CrH#^x*MKB49E
2Q?n~1/';case"he":return'#s_wo6KV>h-vhn-<YhgiFb9Q]Qb="c{,.Zs_iop;AjiD!j?yZVr4(oKc;Qr1o^~)29K9l
4;)7Rf[nJ?LQ~Z=Q!iE7+e&CxEwA}
rw$([CC,?qJf/Uix-
Aw7->8yqhXSfM8J*oFIh4PptUlxf1b5s))QN0[412sA,!erM{yQi0Tn5-
[%}!S@,@I1qneuy$K$5GP./H]s"$g20V,N:GPx7oB]ioLnA9g)sBjXSBmh8%W_wgu#A-XmV.qo7`Gwgf/CYI<;}L@UQ9"W,=Z^Gr!
Y;s<TU"so/3D7/2k^cNG1d*i@D~2
ay$afa)?YOHy]/I6+zl0ksTBMb^AH2JXT65Kq[ewwla]0lV]1C%dBg.b2u&!lApaQ
>0a6sp["dqIoIh#"pfI8
ZOdL`X:Vi62/GN6#hb[eZ%IEeCYDE&RE"&c@UGy#}e1[+/$
Hky->Q"StK+(%Bckp;1[V^J>EVk6oMSg3X=ubya1A/shloi`JRQHiFte+ZoD`vtt^%t3e.L5}v(yla2k=dl/}6(v>I
3nNqM"8{<LmZ<Gb^2_(`5_OW(3iH23p?0|L-0bX[iGSoqEs.Yuigbjls]>"@BPIA56.i_-80-He0n)IMJ|.J>?L==G."S-D7A@;)KVGakbZf6*+eQZyku5ySn39k2O8*29Vo!0C^%h0z<!U9t)A#Z#MT*dSex+pA,l>==06zKPR-4iSY4Z:Sn1eJ%zk(C}
K9?1FqUq+e_x@0:J>qYxl(ivkr*=_<XfCdZ_N(@)gi_2J..6M2VsBLOX*Q(S@kOd5t9Nd^q*!It!x+w$TPYC=R!wEE$kYv&TDH0+5uIl(MbTSp=RJC%l9]B^DwNG/;^
6T
1[k3VJy<kJgC6l_5pV/&L|BYr}/hi%6AQ6AA:OEU.W.Ny^;/M8@8/Vii1SVrdhxY)|K)*/c%,/tMF`=z/Se#^hgT065uJJo4XUPOaS)Cgbj>Fue3@,^Xb4$/&afdh<Lori2qRS,Vm<2i<K)SeXGv:SKb[9`3_?&W4,_75Ve0<

=gyt6?l0+L>3Q$h@|beshcYk"hVP*gb0pWHZApS3T84p>!x#PMbl%eH?P3?
vTAR~-b@OX7ZhfO<aJ2wZlyD|+PPc-?D&k.Q&Br(:fbwt>A>=Iy?s5:._C)b+KmTF-p;3AuL*!xI[pyJ!*xP|RmpBV$az/mTuB4[_^yIaRX<$-xE[VUc}J6</HQl7VSbW^)^7k%h5mo`dA6I[gl%<<{H<OQO;CuL:C;9,E?xq7iN-IMuLa6p4-L*ta}d6';case"ar":return'(s_xQbKY($ct-rtOZo-Yl%I$F/kH))$<Rxl"jX>Uzo+O.=&_v
BL[;$hSj1X*@*6TY(%38y-m>C52Jhi0VtE8X:4$J>3g;1cI:@>I3%$tZ~?LW|d2SzHAP|&b[0S"]J&nlK&Y.^kp0v6PJpetX2^:n8*`R!-Y]SU<,<0:;-SU={VSC1yvXrseRUoT^zt!/,iyGBk0F?I.ExTeJS;8Wlr;UnN?:t&XV{dH)]8TX`I%IsV;kRa&;5=oj-pn@I93j4IXDU+i#&NfrZL]$)w);/bqU0Ro_Z-1>kv"`as$g+#R4g/(gFx+.Nh|24a%&n@&(W9[IcM"e);#RLip#ZrU</W6ZLs}QnZX=GYb;30]c
&A^!aZ:b#,(B!v=s_*I{-ur
4kPm(w-$1L*1wD5[o_dv37%Ch/.FKQo
ft9lCVvita(u@PUC*C<0H9d77O9*5E&$Y1&eC/Q1^7J~b|81]hL,d7RII9D=-pQh^Lk]VR0AHvDa0q!sANn_p0l2$2Yc_:d<[9p]?UwTNg)Bf{?h$&:k(`bTFe!0gm6X1/Oq@t&l`gB:e#*Yi}[FDdD.J#d
XMZz1BP,weId4OkBtb,)i(H`,4]N4m
;xcKK&otv]"uYd>n6rMBgyg)g
Wm7GbG}g4plK
o$x2tL:>b&4p`u8P)Jck&[<VJ)Y&sn5JFwo,FbmuI#td-$4b_;SSy;r.0-_qrIqVwIos?@l*Gwm:Q+6+R;L3W0(CF-`b
0r&3`>
f4?PWR]^IV^C`Z+NnuH{;dSRg52PsWuZ&ieEsHp=Ew)TT-D#G~TPBvX.b7hp!yw,UQ-A)Rr#2Y"vUpebF+2x!EFkD>&8>mj,fp;/?YJ!;a^h5+X[/8bW#fHiSFN{Bo$6),@Q#B46cu1)6*lpBZ39elSzL-S"XEhw#y@r+/gz0#;p-4"f$_1sjNbDJb.GB?Kvr)h$^kIQ,ENUYt_Tdf4~cpj^?uyJE~>Oc|w&!DFVT9B5JzF`+;?P2,d"2Xp%2*kcKhlQ1psxBzG+Wxr`A]yC4z:xiRJU-,MQ@a.WT;lFA:SCTS-W77XH(:1KVZvR>?rDA_I.H5kIH1VMrkrzj2Ix)UZzx~Af][%XT>/#9SumDmIJF
M.baibFi9=P;(6_CNCw<c,q74.On_&:@&tZ%
Znv&.';case"fa":return'$s_q
g~V?!+ok[S#
;`Dr<5ZL4:?6-aE#2W0$;3h
rV/xN`"~/Fy=$h)3`&.ou=C/;1"6_tKtHCA-t.4-eFiLksb=AVo]<^_(e$kZIhW[oAK7v]Iww4aTByUBf,@A_esz[CG/orI~8~x{Cs6l[XRnZJ9yD*p;r5fL?X3)?=v{%{STbvt]!P>Mj|fQmU_|ph?tat?-c!F^q4RFn?47n}80wY-nLJ9xYM7QP1f`)6,HJydo2F6iYLX6UAfzs00fe5CcpJL`14Tl9radi$A^Dx,n#{UnASNa-zv`>#O|Js^N@w"ifKyluQj3wfNDZFP?t$+^Q%K(Xo#bI}W_a+rVgCB_7S16))e<qH

Z*S)eE-"*CcX.zO+.q?.MhrG;tuF`(Xe#Th5TG(%%sSDh:,^cm!Oo4UJbf"*#ss#k$oYH$wms[%[red^N<e4s>7TbW*oso#6<THUhUz)"Hs8#hN4`BwZcg1#vORb;(%IsEfd!8E:#$_%ai`f;d0OO<%>Dqo7&)[|v5!QXEb;i[K5Vm>bd)Gq^b9!Ty8l`>m,Ew&H*+s=>UU-8}Bx8#,Vy"CpPc2i^q)YU`hB))4!NZu<uW%`dT(mX
&+rX?
ETH]PY)jd5DiJAJKK7]y-^e;l5)FC)tCQ36H3V_rP
^oA`3keiNRj{:RZF*6v#eM%<61pFL~7Uq|iM%WSYAiBdN$_:BWCI3YmhC:6v9DHb%,sCxlW%30*)i~S-[8+
CW6WffCBorAu#[v&i;0>SHD%W6I(=B1Lyov-Ll:;T`J[Y6P"O4erTVItnB7&N*.e-OBcUPS}
iwL"Mf)h9pa4jH(c"ZEdbDl>lHfj:8e!yj!##@omNQNE1g^8:,>iyb`L{]?WJL+F.((CYrR1QuO3);c?+-ISZE,k"7;B%Mo70#r&T,T9oiR)@(ojAZL@l!645qmO;w2@".K`Z>x8@!k!<OJ*Jiag=H[D%ii;[Hiu0<#q"O7jVuax85J&[_dA3CV,0_S*d)+E6b{w/>YnXXj4`)$H5k%w#0EFLLxf~4Ufo+x(,Y8gZmMbV;JK!PPn+i1<0o/5na#j~OZJ7x/.tT^3N490l#xY{toZeU2Z"nl[5kF[/n#@c5T/^d`H=Sq-k6>[B+oJ2Q1=/S(tTE%A3vJJW-_yIuGIsD!
$I3E4mL>/<~Lfd1_@B]H2kQPTV__8Y-H88r=[-RwIlK:MY]3As1<sqeV#:35J)i"fQ^X5ECd=+J,Qkpc+!,R^jv2U2
>k+]9Tq
WP0.ZwmS2iv)s?l.tt"FdJDQw1WV
s0&I&@2-t*yS*uVEO^K/}2-dKi?y`u>yY/
_H-g&?2uOd[|3|GCBO+P;:KR5}H(yy6yQey@;,<@.GWe0<6hQGZE;Kku%2rWp?%%?{TNP3Up6`KJ
I-Of.8CY7';case"hi":return')s`09bSZ+$",StbG!cP[&N[^baKf<"/!bC&VFZ3gR9z108:>F8(&$Ubg;tGJXSqSS&EVq*C?7^K^@x9T-V8e|`Z^rmo4,ktWIs#G]F;tMT_PcvSsRaUpb-.rsi"_?,>/&FcZ/EdO"<O]:4}AtxZ&I^5Jpl;kl
F_Um^>974Pt&V;ph_AJy]AxMQApJgG)J{JVrx1?pPlt/Q/1cB?:1tDis*ce:AEL;|+/RD3AR{A2$D)
A:!0G*);R;2gK~$}LRj:h)ld
aav/hDA
mGNfLFVo.IJUFYsl|1
qeh/D5#dhk]=kvn5^CczT-pJ&`D7LjHfp@m[lhtRP{gi:FYQoD.&$;w)^SJz#;JjN$,l!0&cAl*H`XY~LoU@gpFDKy(;G=!&&
]w,W!%*DI"3>FHO)u[rx;tU<*gTOY1g9,feOqh,f)8gP?.D)z&][/BNH/qs~qU:^mM(8Zq`ku$L73I]C3UX2o>UhF,$)*Yc$mXBIMjKiULf<A1Em%}0zj&P(gmotp5V>h+c]i>FpCRxtGUY}_y?H0V(%jVVa+Zsxjudmf-rU5$5)9$8k.$HsaC8hk|4L@/0]4wh]LP0?g>_U5XyMh_[{lw"-(?-d>6?.FS%i*}>r;/P:"!fQ#fB{aGgUfl(VwHvK*k-D"XinfuIRE4$jvGlBkSX=:~1H<(e/<uz$PYg(>3m6pw@cjEqzNCpz-j^Dci$oZa.^&{O7N=`WV_Znv]SB;3CR8{#`6&#2(61Xmi(!5R-B`5Nc_DIHP:R$;ms[`#lUP5#wN.ls&g$m6HS8Szz$
bLkN4AXn.qtVSoL)aLWv?96=J#):cQkN&+@Uo5GG=2;0EBQX0u>m`nI
aZY@Uu}Dve8R$h0>VqX,W(N(knT)r"=h!6cl)O3I$PyC2?#fSdKZDbg`4qH1qCJEr)ZhZj_J~S}8rNyK!Y^=MU@>F.K;cQrR%Qz6oq429`=btyHo
oo1^pTde2uuj`F0V1Luxx/5lx^Z,Kr&bR}8BP6kK6t015++,Xc51s/8o7F)o#75U"~NHt%VMz#<7W1)qq7CS@Y/q:m^rDx=kI7aQ
TG(^__R*UN>2UvCQR,nV~RqJoVPU=6`Y,3H-qG*scKz:Sp?eJZ8OWGAPviu*2a}RDMl7eFM`fH:5[!*76v<lyTr&63lU)SyT%4?_G*mIuvYLh<
vl2zNCLHOw7)PZ#|]Gj=)-Cb.j*`Y./WqJC<v,6<24G*nTpa_7&,41%/ysG^1&a~k
91ML6b@AG49^x`<t?]BSvkmWP+NR)BEgMq12@NV]R4XzEqA6n3L7a7Br#DsNJ*+l-[$E_5p
x)u@PqH=+JnhuA3Jo@.1Bg/f?Us=[I8;ZTa5Pr_`TaBZSnrH&[=iicbzodZk@!tgfRf^!WP;6P<W7bQ6%w<y)O+VW5sZM5GmimMRdvEc%4.vhwx}F4JoF|-Djh0V]SY_9u/U7g`K5zh9hER`l4kYg^r;P7g`;E:Xg(Qhv;dCsY1X:%-LklM>.-0oE3P=I_Ds&J#m]fgFYn,<[8?gA<9!6B;Ec~
+V:!j`,<m("#q_@$pUUpg@Ox2awLcAD?
cA%X$"b@>6)mDu"y/d*D*s3-NU_z<nRcMB?MT%YE4%30M{g7Vw.t(IP3$$d.h#T+MF0h%]l&ZC_LHib{c@)}L:q$`89^t.2kY?r8=WPG2ysz2-^AJ26
G]h4KO`uqXw/)*]NOTiT<)#R2YN+Z~0w65JN^$J^ZMYq"@]Lw6,A_HaLblPW_bTUKg^Cr:,KI)&f>D6Af{p6_wk{6k`pnq0d9!A_ShA@X."pMQRt:_1Hp65&,?CZD>*0eZwj@kNy@t?E/>f2nDe>^U;ET(jA1v$Is*(,-~xJV2*/fmjL^jA*V9std/MfBD#s_}G*y,^opQRmoi&Z<[[>#v9fZ"s%M/C~[8_cX?Dz_K,RmgaU=5$!y{+]';case"bn":return'#s`5p[~Z;#BnU!yx`:v$,!r24Gy%K^%/j1/tD;OF*+m9tVH_QW+1@DW6)$2((
oZmZgJHba0HvNcB[.saqcv<s2?Sw><*e%rAI}L#kJ+Q4S^Da6AY[;Xz-xfDrnq~<_abB8y"7lMO+D0wXAjh^c@lcl65lFtKwus7s85:,irxgVixQ3[SFZB}ZG:YrQXY/oA#kty,rfhs4YXPMK%h4Gu=m];T:JpMg4*xTr9FWy7*[gGJ_b1xZ2C
ptL`G%n&C)SKDXOx_7:)_p]P=^)#mfd;rbL"[Fh-[^ic)t.%Z!CKIR/qh<Aubz_Q3hf9KK]T$n%rW#ASCC&[lq!Px*4xvs#3!:gGr*O|dR#(pARtwOPOwm*(m?+xPT@=F*6-Hm$>M;(5a4QB<$nN3e)u-K"iQv`or-VjQYf?N.TT@iafb
DR4>#&#tW6<:_X5h4eIB76cjigcLf}0jbs
#aMTzTqf/aj3D7Md{6scuo5r9YX*"?I&XbS_O!p*A?sc9Gq[JBsTHSBS@*CwwFQpzbV%KeA$Mg{t0#8hzjYP$Tt]W)=*8F{0vIKocOH81M#=OT$?A=bWfw:$zPE6M&6^.)e6vHVK,7iB"M
pkRXudE@:m!D9w=NG#"jMAeO.2YL9pt
$6gU%]_+#Q-kJqVtp$@{-2]0Le[m<#n)KL<aI313RwA"p*:xOsJ$RE"n5IM<^3W
wHGvX%qH,CM5E?F7*HfuaK-8Xr#WWOeyIO=u/-/{h>KIIYKo;fF1[8&F5GP<[#h#drRMbl2=bq<!.7)pmV>VN@KH5S2c:#-/qB(J8g=ZtL
..H>X"FIrWbyl)x;isx#j"Rd~s;(71k*^sP5-cY@d
w>)?DnFtE!#Bxa2%T#2qKA(/r-[?pTUug#/Z:)R?lu@sMn55XF."q6N*|PKIEMjEbpDL>:v#LV_9252aY2tB"6Z_#ht@uLX:.00$).)4`1F!^V[_$%Up@unj[?G*f3BC5=^7knmw;A
5yyTM~u
yzl.uCh2+5E65==jK
.NCo,Bd9a[EeP7+/fB6Jw5;3-uZ@(8.#:4.G0{"S44G:f7wVj+nL#sCCxb0u5w!`gBDX[8a?W#=k;LIQZ@X|>{IXK4%m9d?$15Co;R-RN@w`*io;^@+o^sh}ka)sfHU3[tWj2ljN;-Dne<"o[GuDP&ha6Fo.111=j)70`v0
t?ewpX9.R?GUc&Jt^I`s=*$_,pyeM,]u1"3oDAI=0|V19=3a
u"er8rk4KE
A&(bf%y^W(fZ5]t5VF&NWm&^vjVb.9b~UxmXk!y07OKbqzd3>@8{2@A31!:JWqN0rb@z[XT/oxK(LO=g-qDgv"aubKf+c>-pB])@!`a.P^_`";yE+EQ6B3J#g=]`_L#Uc
S"S1i)WcLwenD!HOwL3O6DkD3#rUg)vq-=f)=E#6e-
>JT!xkyZ3^zBDkf,Zls]Nax2lC[SsDdc03GE7ke*lo!lz$;x&OBN*vUncrJyS9/[JH!.jc1
*DGka5c,V>QBK$V^r]2V|jd
Y,6)m#i_bT%h&Tl(81MGwZBCM]hTPx>98
`eQY#FFCh7380K^xEBnZQ1$-WRU_s$&li8adx)fhQLt!Ci%GRYaG"c0-K4%;Q;c[O3/l%i_-OTNfp:v29:_x,<iyjEh^o-5R?t"f^3R8}obS.=;O9w6lwj@b/tPy`Qm0XQ1OpQpLPy]XJa>Yto9ivdQ`iWDdc@w4N3:P;2[;J2Svh.TtyTYisK1TI>YINHK[.YD&=t3_+7oG=;`,dCQF:;-M;AYTs*[5ZaOTG*O_3@HxHu=O~]|5j*J`(n3>cFk]XEtF_2F<
s<m&o2Nw9#<T>YhJ)[tjfF^*dOrot/BIEZ?K#eh7K5#-EI6A=$9]_("@s&maNG%&WJF53r`eVVK|W>(ejg)"@j>[_$*c[tD/Aa3:JB1LZB?Q[,3_=o7:BIL-RPkWOtmB1u
F;
TpVl`2fHhI#|4C"T8#YE(v%z:,rBAFQCwSxV
BGB)"BouJeFG}#dQ)Ru2=Rq-wQrkbQ0Qt
Ft2=LUW)sZ;*
7Ew%M/)+r+WjYBC?Up"`Em
8x8NFOO%*dF';case"ta":return'-s`*/bOY!gN:Sv"P-"s9(*"W]
E>AeKG:UOhn#F!?1:$5HtuI_;>{HRUCIsTsu<,E6Q]CX>[}=#:$5u/[g"qbgzvBFe+HWvH(^SE(FX]amM`V+0JHtddWn3mhxgxVa$7q[XI.^89iA;C=bTfn0$4-7y<e(jH6tK?NP.]aZw1!Bu<M,-H)H,N_]TgfwrFW=5]#Gj[
hi?3`Sq1P"mH4hbdqc<eyuGh
"nR+@e
r?acGNF(aDj}!>)Zv7i-#QhT<X
JEwjw@-rWS[4V+BJ_9puhe82oww0_qXL@Ux-?!wfq].Z+NK+05&BvTHW:]VOie5^mm0<kw~Nt5glD3?NFe0gG&.`,e&8.wX4Xfl(T<G$N"R]B^_s#F:JUA%&i0}O4K$e;e0`Xfh@o$(&GEe&#Xb#`NS*ZNM.ZDdK_TCfA
PfoThtSiDO#OIORYJtC,#o$7-rfYqceDc^6J,D2"k1TM;UUShjCY)S2KZ8)U0Y8Hb5g<a#mr8$:EiV>IxR$%vC@jsn>QeAvPK"jy8
#0Y!c?r1k[#3YCA;bdxwd[{-N5C.6BF/9CDJR.qUueEhYe1i70z$g]h#}>wX=n&H{J#pIA`65
-U&5)=X+ko{^WF{U.t$c8T}ow6N
A
jZgXyL#h=k,e-S}=OHi2S6t"8#B*O0x2~]aS`x!41SNpg,.=Z&j,PZS^.E7Y&;Hw,.%VTqqpy@]e,Hq<()otc4R+MJU*/1>)1:z04Nw):v=VMjxMaH})LIi#([ODM"U9[b{/5N51ovUg&o_.sB4h:4A9^),]tK8Re%/SYtcF8
@/]>Z^[ry"*G6g+Uyu,/{uZ=(kOXh=n5%!m36Z)OQ
fwsmeDC/(9XcEe==iLPq%7>3_"z$6*?AHYQrUMPA!WZ47^uNE3i#V
1D>G<aK`13SB7TJ3<P*h1,8pJy<8@<&ODb^N80mKJAjE/hDQ!bZv:GUC2
G%S_[x:at#uK
!pucb&y57)AIh0E{l/7o5nYwXF*qWtS90IQU6$kpHZ[
aRDy_YFJ=7Ywn^CjL0CMdSg-U[>TXx/3P3R7M9e$[|8OxH@w$%C:^aWpyD,eZ#LfWwQy&2oV`XtJ>alqP:)/E}!3<Ayl*!+6DJ.ZYQIn@}d)Z@qh9n<xCVl;Wh(Avt&PD$qL5yj;tLxR86<2=F"Sdf1CZA-*-B[KQ6*;w`e<F]Nj,4e_;5g|!+JL9AW_k;<O-*M)-nhEg2+ae@tBV]czY;%0BbY_00sB<-FRJ=ZKFWQSGX:zrH(C)4HT*T,@;>SGrb,_6/)F`!mL6/q}/bmGeHW^ZsqH5ZdLj-!i_gN.3MGnfoaf=$JRHdfT>{V:oIi?1KTUVG$"X#d)[P$y8^a+pKogMt`&jd)gR*nkO
<z<@MzF3^fH_xmd{>ymx>P,U7)RDQcMfyM)HM%XMbS!mr&!="uGn&i)q"#I)(?RK^
eC5l=!%@&jn)$7g8,owBN&';case"th":return'"s`&#bKY($et"MiN-KQ%Q)3$F-Kag#5Q]F39N?MaT-N4"-u88Q3N[K,_axiWnt7C"I2QW
8(Ip$vm<Onsy$ap..h*-yQYH|q>OkTL=o^>8M%P*#;c@[.q)mQR4M@=U
CfI[lU+"=meGT$Dn]<]Dk4-C1yE
4xbVqWr-qFvsr^D7-OU4Z?`=eMnN?s:!?PovDB9PD82(HB%^;$S["|Qe4JrlB:G_$^P;&RK3]|g]HVO"]<m,d
sfCZGX?.pIYih?I7[9WYZUJ{da[zpNa.KP)(aBYF!#Ezqss$lWYV.[wFD91PB$07,P"Rln"F5]"#y)rw`0S7-}:fu-3.[qu-AvrX#_F|qj?"M-c%,xGB)}RdVL$ksB"R
loKg4l)FkCsRWn1ETg|2v)8kK.#Zh#FCj]Q"<bae8-a)qsI"M:uy&Qh:v;x?^U:$^lf/~r5W#
mcM9
SgM7BgFn
W&1hOQ{J:2,mJs;XMCO?*Wj?@h|0Lse:G<>2OAF(?M1/VXQP,m&RF*Wg9PMd~nL3NWa3@T`2KSVkj1/U?wNVKbB<.K;L[4Im[IZ3x6[3-YzHafws;p-rG1;_K
g2S-L"hTiN9.}/9
G2]+mRchXy(94DtTP3P1@coB~FlOz*FdbI2_pIw<l"6m{GA8E%.S/?EVe!}EXNT"H`3eOohA86KQZHBW+T7pQh}M|*msY"Ygb^oAgHoRk`]N4x}Qzb%9U:`#oCnInWnE}W8#0CCy{i"fK5b$s+H?0+pbqs-s?`"tqw>yUt}y98o(}kXPWlG&50
q1k:DuB=#N^5!q^J<Y`2q%+4(*dER4<vY`+7PgBIY9Yxs:ei6J$$4UsLN=s#n;$E5B/h^:-AiY@TNK:LU4vWh##vp0T433n7gI-IbE:*q.L?yJusu26c$E+,lb4H-<5hLA7faE!/n>"&LDBDOc;s3P(jIF(*?/rn^u@[t;>&kA
;[WZR><a*B+kwID6yix/Vnk:d]^Gk^
anVOWcI(+%&P4]&g+[BdIGPFf3_}^Pceixgdmn_-^Zw1aSZ@0biV0XP;2siVJ&+,FRRHf*5g@JrhTf3oOrKr[4[Vj)_EO:C#@3Bn-@Ga0<ifel<AaOaEj"i5oN%^EIl"I3@hX!7(YGFH9,nL2<@WCK%~4[.kbw38h0ZUjjii//^q$E
Z&0B#vT9Br~L
F/p-CAUuVhbhb03`Lq#|h{@DS:4.x<W
q:;&;p0gGh@84;#3`gK?2)/@,n%|>DZTmWP:&wG;icjJ*~t1q/5Zm>B/>8G%C=[@J(*QDc/v5A,z&;R!_Lbxd[qV+1?n/5I@a[.s;@svEo+3nVlxoh#zb:`z["_MS&ZF@[:xX6^cCnv+cHwB';case"ka":return'*s`09h%WB&iq4)Ze4_^>M/|1#UeG~wI00Nl9m/#9a.?-|>/u;JhCtKO38JW::=gx}DZ0"
c36-dv?xZc$rHGgM-cdFyXScZES_nl|m=@_$bhUiRs+sKC|`2_V`
jZp;%OG"+Fe<65QQ;la-P%,3xS9p-_:y:7=;KA)hwNVe<lypK-gN0:q.M4#"gaO-x~]8^}dM:SJs%-iz_IKMt)2L9mUXuRuH#6e:XH1O=J&
.ut%Dj!,q(OdhjEwH(oIm
We2R6AXJ%fA&+)+6t
.ras$t@wG>$}6;%%S2p<h>p_bGmEjxH&?s5HA[DdlH:p^UH
pRnvN=(zSRTl=GVWat*YYqU,pc3:G7kTY4:Ey2mp)WbUB&4+9c$px<y2W-qV^Q_<9yb@,^Fo`,HFU&s7O#_*T=?$_-E=^I<yY$WeSM7bw#Gk1IpB`pHf5GRf:{Bu)>3#ciEPsu%rt9k;dbhnjl6,v0DJLwNlprkN(]nNoqXc=
ACT?]Cw^i1)34nw$XT!2YWZ"F2i`X2OOU&dU;Hj6/mk,B/7!x
_Km)BZ5jf:)y"g.1I)p^bA!~94xKQ+9}vT-BHVRWf`"biq/yeuedk_n?Dep}/(60$9%z;Ebho1!GI=`IgSV4%C)fl>,UFeBspZ^Q#hSyc_1Z.=SlgG46uQZ:d_vtQvwx->aTJ:I,+2bSND4l#b%9tkh&KW9]ic>#.7&_qk04#
m2scBA37$mindlu5B3Q_.PHz?IqhUCX`QKxf@7tfBqdQ"E#|*7(e=~y2W9Fu/p"K/-Qu+x>`Tc#c&Z*wS;VrB8UQ-hnC-r?Lw_"FPqmV^T>Ug1,;7L<MagKeT[y*?WyFmoD!y8F&q#W>y3VPyvQPecGkL(jbZL>B1<;1e/d+^9_qgm)iN8P]*K-gi[ae1%7KR;6..nt]j2`AUr(7PV>iR`-qF+#,I,6)I8
1t0i!IBiL#csIhs2l!$SnO)@;iDSNT+%EG!E22eSc4N4i8$V|Z3O(?m3to<.tSj=sO}72=><nr]TOI=YQvC(~K:L8?JF{/N.w/rq@M`r5.m$!HD"X?8=0@O"bo?%PteQ??$HOIOgn?HT{4@krQYOakD?%tk#_>C
$qQ`tDgX1X$ro]sK2HB2YIXvLy`(7_6$<>8(Npw8s]+/}MU_PB];:me5ZGe
0?Iqh&,pKC+4t=-r|kqDY.1I{m@tA1sfTSJ(c*XdVk2e!VEK(2;=bRqe-ytqlctmb5_I#3MZEV]m%>+;9WEmEI^>Pxa?=t4&<=pNp=l+$v8-ljl;AFBUYv27LS#Fyb=0u8;O.j8ECS/@M;M5f*lr*6;FF6}CR`V!>:PpPG
[B3xap,mGQR/"qEAPo%9"%hJ-w5pYuQ@*7F7i+igaGhb>}D$b|5b(!RaV>K(UOelqMl}!Vg#S=3j^1E[Em^?lMJ#jj!FI;w$&;(_9I;{X/8,!@#^E~]>"pNK_X.AcJ1Im)>jj~82PoA,Ga0~?[W&u#$bw7Xv#gaIe1W(%X`*7RNA&?Qrw&5S$b;=A@]-K^%M.%s$#}3x*][$Bz
-AOW8$JF+".]/G`!&tTySP)L0Vg[
M2<nUQ/]PT4rT1ycs>?Tj.h3.T`HP(vqaP#Lql)8kIag4O-|;J<sR:Zf<I)Jw19J)QWpGs6&>BfeYShpF4;S08,TU--.Q>Z]HfW>t>pE9mO.EG2
:md,D*TF]ZTuPv50>70qhmYi3LO;cll_=I[jw3t2T
m|;YpPrP
loXj>E~^~T*@,k^as;%>%!8Jdm|e/omldyb%:jI!KSoq>1Vpv$H]^XMfxQpnyi3dg]a3fj4b~RwI7);S}l9VL#9A}>.2sdQ#XF:Ms2R';case"ja":return'&X/*_f{.W&)^+yH"glBpxjYL~&u5tR2VqXj%iN$"W92Ot"5*;Cw-c;>!"F(11e)&u)1bXIq#~5n]*B#a_S8T~8F1$L<BNl=(z)DL9@(g!jrURRM@MkJQ=RBC}>_<db$@_CGlY<"Jub+g-IB6pV"UZ1s81<6qL@0Sw;Ns#;@TKwhKE,HZ(iuWN]&RFUsT,=w;/2Ja#i]41;UCm&V<ymU<M-`0d
nu1@xAVu]o%:YTIu=Jyj!cOwOA_k<044^]-pNQMQ/6Fb(QyYStT4t<rF??>PZa)<k)pIAW0jaBpfAYFmiW$
EFv9+Zf^#rj!Tt0X&vBLT%l-kP-PFx^gv@mE?n%]#;6)z4oZioTrp_%<llw[31`rTJ>h=i4ER4ss.2yi].b7|:q^-4ekH5LjxX070re/Bo+cUJ+L/H/a(O_;rah4*otD1,0Na3k.4X:@SSCB0Z+R1I(q-#EQ69Op5HnrJY.6a&%:VV3aw)^?e8.leu3<L"BMB#mKl"IV^jhn"0t#}V_jAjft`%gE?Y?Vu=%1GJ9QBxn<t;nTIO";:7TNGe6-N2CYhNW8i%,ebY{u/++b)%.FpQf?XOYoIkr)@&c0BOl?AFCbHa0jJTqtcMM1>Sw?t1bQ>KDu08j4Xy/fX>/SbPR3S,f19%GJR:Z9J3W<8?WDdnnM3^[<5K
5I%/>8)i:kayEw1O(6auUCjauHO!)2R?E8<(1`C-7D&lx-
uy3r&!y",X_eK?YbxjLfxY=9VN+0lBJNOG:S`1399IK_V9g9xKBWdd$e^nwhL_%Lrp!&gUmwQxg2;!pS=Q_I2_:(PsF6h%!Xe/JkL>eEV_s_"IZ4eb)1LAZX9_qKc3~Ecu0"i1YMOs*(@Zs>",MGd/`qM8wEWRL@5io]LEI1.,0vy/$kTmBMdeRbD_X:pX=+?z#H`3U&;cjwC"&,L=LYf^a$YD88~MAq}"`m:aVXA<@NVc{JA!?hP"hPfv{jTJawgI})(AJL:&w4(8NU?;]1a3I,Mb*N#Fp;8.ZePdc3o[c[{3$##R%Fm$}P6H{K$GXL<O)>!dgNArVYZ9GI+0j(=DmmlIctT$_<>jy;+5EpRBk^(a3fu0Kw#$?"0E!etx|,V]+R~Zw8eP*"rPN^IgVTIdyKh:>]=2v6<Amo%*<>{T,?b.{Rm1]p&`HvN_94CZ"1|[^.UN)aBr{+*(?AJr!c.4jF[VYUL;Vjz#pOm#S!E/;<K(uLc,Wd
Shi]S7Y8%+afq=^]q$@7pG:8Wodw-1uMbA=9,PEcPC7Nb;neP[Iq*/Ec7mr)Q.dO1Ze
O%@06]lNm3=bgdv{u;y&w|/sd|8JwAfy]~h0&~b>3wT1>T/%X2%N*>*9DF%~gbGlVxw,vG:$KD)2LX>%ls.Fg*R3KLC4l:823%->1R^+VT[Vd3QnwZ++g%()?O?AnsZyYO$HHm8X688*.RJcsl7H*tv
PCr-a{5V%<<!_z!_<Y<_fhk8s
,%_$XQ
U4i:irRy|y7sj0~CDq5Ut/fWZ]FE+d}%Foa!vhq*<hL**J*jjnfaF-Vwc_ap$C;v@+Jq<D/IuL3<=DVwm!V,@AR+C1)e#f&-{w)>pv}Z?%k(?Atj"(K^!^ZtjJ(!s$yS.YMMVrh)Q]&meH
*~k$qfsM1m"&5!nX4#LE!!:@Qep-VJADU]55C@iEn#;0su^$U]/1r_*<8O_^`X.#vieR40/H6teKc"j(+TqdC}Uddlv:dxDZk>L[/k..xJ:sb<F[O]<H$$JZRFmcE>X`xbI6M$GbDQvStSvUH.L;9Qr-2=gbx}O@=0?|Ym@3SPh8O<NXc1^is_h?O;`oxi2/=6MjK*4LSDy(s{Nl;xrd#<sp
t:@OF"oXDr",K+ij(P.hD@_$,59QDi_IRV$Kgh@8YFu3SW6/![Q;?GA-W6Vn>:n[%/>/atRJgpOq]=3)r4cxT7y1Org
Q+=oGv<H2RcW<u`Wsw0<
A$XB7;hg44k0Tg^,+g]{vScg1y@|;(o6h"0#*<rU1&={B<G+(;E.f_D|22i6ed]"ke"C)&
vnYZIN&';case"zh":return')UEwn@M.W&i^+sxP"XJL/tW<]Xq?8d*[Q-K_M/z8S/9YT-O(bAO4#NWTZ3[l9<.E8R2V&vwv#U]RK=u4tKHcZ1V@;6C0tMt^;_H:[=>p?;bV:9AhOZi6`Mi=kq,>SjW/w0N%!WKvMsm/5l8wfCods1vBNg(u,BeubR9rUyV(
*jA"0i`S]6"{Ya-}m%.EHi>KJ?"Pb|BcH;9m_,ZErk5IPa:L+"Cd]fDCi,z$w:`R7&H|n`y
B$g,2UkH=%7?a<kO?SR&]
q#b
JVlcNtao.e]Adjd#D5AxfR]1AwY]ds2D@GgLkz!gF,WwFqJ{a>I~onf,ptKYgF_L`a]MVogy+:v&@u+/(~X*T>nzK67CR*9,Zzp-6p4CN@%Rk@k.?
:T2(-_G(#uGP0_
98UlAs:V_rPUM^
?,):PzV8b@-d*/FlGA;b%ISj`v_XjI?g,@c0YNEFqae_JQB)KhZ[VSa&Vgn(067N);[b7L:dSElL@q5c$TQ+I;)ZsVFK]htW*HtCvyZ|>|"shgf{Lqv*==DOb8-7k.TXf|L8IeBVnL?(LQ*i@McJ:1s+PgVh@G/lNOYo^m1w$a_3xerA97uN"5k%Z<HDJhLNgwE;rKr
M_^$W&RCQZ-=OuW0WGoUJA5C/KP+pfr<SWYLPqv(7"BA$;$gDD6>>lB2Z}iAKz16TZ9*d<Pe0o!;gJ[U`_$2.j5^F=&PWu[Nuu
6"/[cc$Lg=&"Q$?MoXSfa,u^#YBA^k<B,`Is*%@%<>44uL^@y`WSDS6yuL[:m)Rx@z)Ub4$nwP?yTIuh?"vD.Hat&m|9pRd=$Axq[G~c/rh<qrgkz3K2$fD>/2uWXWJlYZ~YSAiD+^]xPF=*w1)QV)b3.^cM%vM)D)[t0::D!qI1}JsAX#zI

7x;6_L}9PFt)cdYfrM.;e37
K<)WI@fFIx@:IafIIn
kxa36XWQi!=o%Xo<HvuOKALzXl^Y/$hRN_I:^f6!+`_i-$h45--ijWxZS9#cY52K+9E3QKfx>I@;i3/z.*b@B67{mgi
z([0#BuNpkED<WDL!"h6pimk;GkjsOz$kIIba8[^tW_s!
9K4);Zau6}S0=B;>M
f!!&*nLTp-[vKH"AX)y`4FVMpfM0_%r,Dm*`G`lccB5/`a<WlSg>.9I!JwY4^O;IOH4}"~3{Da_6c|-Bd$=`g|VBHSrx?da~R-=DO:YfTj>
D;?0knUN]kX[7PKc%oNIBYU]fkpZOm"H4)H~KqE-_R6IwC2AH~7VVaQ-kI>KqA]Eug!+f8=i7?DfS]_Y%@>glWK82]`yLOFfR~%6+kmFvg0G;MA}rB#u3:o3
+<YuTe2,4K)`ndOhmlzm,_j*(
|>]pM>n*?E8.`1fZ?H0``x`l%EoCjfP0dmW)~FB44<)HI7v!w+l2_N_D;K%K8yKq=y7Sdq};&gw&gk0ma>Jvm7H;:N5:#sw(R.I4F>(&/(Z<0eMIFCOxZALX|%zWJ5L""&.lC?
WXJ&8NZ=s=Vn:)%hX_XL_5o!f6SNSYWB0A7eQYtP7~
QGO1p*gv6ZGkCO?>[M?7_^jnQ**QI+0);
=[a#[1;q&9[c;WgH6NXPWG;`NsNRXls,ji*m+1ymE<a,YM+IB)"6FO.!"jC4gVRKy#0[hQ-G<oX]B,rYl*3$-iB;KA$SuSp10/Z8!givfJgIP.y#caGB@g$Vmu-fr.NuX=gCqc@""Jus;lQZOFxq$i98rq{kXApL`';case"zh-tw":return'!UEwf@i1@;Hcpmf#rUDR"]}b}r_<6Ke5YOSeE48SUQQYt-)Tbpf7ifhqUk*xtbPl|9TIA3m+KwyqliMFd7Egg^vDaS.Xu!}(:4?o$),d<YVDA(`LZ7z6Mupp*?t+hUv[G8Gh=[?mBSH+?=wcd@4Aa>gLWf_X2f9sq5(4ud~J<?slVOpRGkwno@v<:M%t2c<^,0yZ?b1opUYv0:<xln.!&H>Dn+K:5UYemA;29<c5/78Jk!vZbR*vhuY>KNM<C*~0By@P0xcp5`Nubww]msH8rfbE!lIpFOp`zA}(+UWHRnri,/PP!>qi:"
uXArV-GC185^mxxEHXC}H,ahlOxOc1[s5a,A(HCy0[7zVC-VPR@I?PV[U2Uer~W$UxXr5D.!biYv`Su?S$Daio/uSVnb1J)db|
F811C2ZpwcL%%_SCjJk4>[[VZrb%#ERS04%v{oG@6[8&Q&:e~I3ZsaNa..^LSag*[2@memDCw>wVm-Yo/OC.k;^u=/0R8Y/*h7K/8/f^5(IoKOSP]9=88%;*Zcch#3X9Z(fe3Xy4<`XE:O7(Yj?]!s_<y7
O[EguJK{$om&
RO}K830hpZZ0fnsD?xEy8w2Uc9m&OE0A6&)RlxSP_fBr0+$7>AZ<"F{Td_lZ!0|l6(Q$2Rtm|q2-p?,)(]wTUHKl3Y%,>"xb26Vw]ynN+LS>PGKqB#!^~0<f:u#Nj:/JsS8OVkuZb2DMG(E]u<jGWCu/%Wmk+o*`Rx/tJK{HzSpnd5y?FGnr7?=sASf+2Yw"W=K)ZA"D(jpL@z!v
7d17xp0V,ql9tY?8)gh2K34/_""c4A@8^m*zY:hDpW1C+My*+C=nVQr)*6vhvD
80B67B,(d]NlV897924Vc;lGHP+$K.RQ50Y&6RCV[#Yp6B3a@%g[+duJ>s42fvgV<Q:xx,t+W3`vEe(@*q1jTt8AOQ9W2%XVpOrvqi1y+uIGrR0H0@/@*#9n`%}PL"s2{v4hl7S?.>R/7*qknVuo:PE]`dX62Wf/i8/3u,a(:plJ#ZW+w8}6{pR.>[vkTtvM;C-VYNEHB83AXK;yh]%w=pqp8Vl<)ZN`p1Pgbs4#yVo>qyRZyb!cR6l8"o]i[NOj!6LgBFvWNK{lLR%J__
<-pu4_V>[^g4tUNbU6qhMtb]XPWrab4VWIj/8ssd3?3A9H9#u~ec!-h[C8lT
}Zxl_l&nWv|ves(#fllrU_rBRy]@.sWAX&tEN2HjP:YyX>$>`S=_E^y#V+;^:oplpCkdX8?<1(ESV0k!KL0D_^QP5d&<c,#vmrS)N6KU!ggfku1.%MCG)FCVpx1=Z"%`YJ5Jgdl,!Y@WIKU]B%!n+Xcg165sp833zabiXy22!k;7.94J%j^y/GFDrQ6+AY>9B4&u*`=2ui_A*o%^2Yr.4O!QvI/sc_MkBNg!
>wwp`<)%h"##Sn83G?9J$QVy^ic-y500+Ni*e#:?&C6]BGoSmnpx6h^&$Ys5N:i})GJ]+EC[d8kTe2PdV(yOdC_1!C;_Ga+9c"E0mh-<hB`]dA-5@9%nKd&HUGCSA,m6GUv{5qcjS.CMP7W5kQ#?hsCt;+yjO?T[ca@W)GBcL4-!M;".qf["[J,nDg6r^o,w
Zd3C
RJ"MD:F.D$`_8:=UbjBU2T[g57y$1!vBoSlgB_VGlb+VnG(N>CiJS}1HPjCEgahAUq58GH#z]1kVXrr~S`,pJc-_S
ouw$l1FMs@V}8[NC00_-#x"NAcXS=?f^96"8&Z-^iH,`';case"ko":return'"UEs*;zZK$e]}Kn&W;b?sZ7H/Iofypdnj"hlb>Ngm8m.Q=ZU%Q`&;Q$CFDQ
6O$<*t.&-7GMf!)<ClFFZ=AtUMBrFk/NB;S?E/Qp(
T"ok&[!dp]ku=HL49b}!|/luYFd<`qArZu5(-=1lG*EQj_Wtn.@,yDpbj]@.!;
GlI0%QK`wRHJ%>8[T4olDs-O*%obGJW5t9s]7~?$&DWe@-K#OBi%C{0m1V8-%Sf"1}jciC^32!/b9b"BZo0MO9E~`KYo^f1t>M%B6[Kyy~)U[&PfghWBs$88tUkY%+){;!$fBLM_O_UGU=Hs5N5`3ek=#bg=L}]wnlLP>Q,8K.t%$Ih!EYoNvR1B64u`wV
()*l;iB"W%#6q,Y[,^eYlPICBw_U|6J4SURZ!Gl"Fenlt9;<iv`3ZH_;~j
vxcy9Xh&IWCWrl)mNZa82&ZB)9&ySZiYuYOJ)FmFTXT`pTt#_T;vYGN3@hhL[4kM845uhw4q5clqNflge?gF6W$694wBCUj=4F.0o>xLUiHmZJPjo=$E5k&xNn5Zd,G`uR^%_"Hwi!8
@d(1q;%b1k-zT58}>HK&!rYF1)"$!/f</cMa?f+:nLwXwO6{0v1.
`Oy^ep1!AAj%[lMyC@f6(X8:$x(_eIo5b`K
k&tvPj-aR-8wIJLR]orI61>1?t]R$M7(ZiK8=0*]es%S;JYEUJ]jjj*V[<:D;5da,X#mdD8so,
MfDG"q8;Ik@AM.+rf77}OdWzfb+6#OnSg%IS5`6rgBHX>Bw9Y*.CU]U3a.0CD1V%cmo:,L6IR<B2qh5ec~0,5!&zd4O[Q?H>[pO[?;!=/{(EI{Z|K5udAYri]?D=E>Zcg_S
mKVC5{;ux$9iv8:5]KDl?0>@*/>B80=i"9E6(XRXF]d!X]AW!s_>InyHc{,h!Lm;XC7pBdD7&(t<^E_R<APuDwMLcOtpsc*I]aEMm/qBGOa`Je2Jj21?)yfDsi!P]tiv:*^()(NaVeFhHvILx:/P]Z0G9$]wT
5G1=K#vwl[t*nNw,CFGV=2)RWV)<TN9l2Ct#2,?@0L2+dbF!%Cm&t-Df^sNpAf1,xW2:&68w[TIHUbE1!uCXFh]yTB%;_sT-;M^CifKttJD5Iwf0N;43.=J+9%0H5Go,`VsiYCG,Y7khG>aXYW].LH!X;bDj/[DG/{F^axi,+#N}6SCh
EE*9BJ:`XrS0JB(,2M6t?RQl2MmkiGs9-mMp2_cbQiLnqrz`xcpxq+HH@I~4-7]s#="<d`b^@m28B:4qTY
uN-,U6oW<$?VBeZ"OzKhG@gI$GN*.=X.+"!`O?!~J<,w7PCds{9sK!.#$a?RdCdx%uY;
7vzAwL?.$Q898(c6
M!U,O]x[4P"9,3v$m=mMe=!+/|[Y8:seAoehb_J5?0&o#bu16.ddRO^pmi?/s)_{c]XMh,wS&W/vdi!g#mMLRL0EcNP@$#WM^0?Fc.IF#ukBXM,Tg[6hs}5nW{qqs<j-l/
O_]tI7JOPtqsNB/L/f~b2BaU6l$=N&fg.7#iZZmn3H-rYLrDEK!2
7w^^qxYv4_*{P-+/<S=&XnmAI+E"W7q-X}%5$VN41/6.D:h2fi=a
tyvy?m]9BO}#>a4OD-nUEX~w|?>bN@kR#
&X;^7!7alMZQeWKgPjfGb@9,Yvs^wS(1Gu7E=`Yx1bK6gR{gV;5+d
X7J`yP8D?k{]y/?
"KN_HaTD}i-I4y?Ksbg4e.DAX(@=_yPV0w8nXBPKCWOnp%zSeod?y(Bxjl76,Hk_#b^IV<pgpcd?FKDI_JbaN-7wrq|(RY-$i6CaCC}4E<5l`4OZKtBKqii/ns}v9&2+cL|y5AlijH
D6s|G
X>M++R)orgs6m{(zj^e25DcsV``YJ&TW`B
/c^)q%CwvF&IF6}J~ZWnqmwmM)uN`
0@h`S`y`NB~nMan';}}function
get_translations($me){$Jb=($me!="en"?decompress_string(get_compressed("en")):"");$Vh=array();foreach(explode("\n",decompress_string(get_compressed($me),$Jb))as$W)$Vh[]=(strpos($W,"\t")?explode("\t",$W):$W);return$Vh;}abstract
class
SqlDb{static$instance;static$untrusted=false;var$extension;var$flavor='';var$server_info;var$affected_rows=0;var$info='';var$errno=0;var$error='';protected$multi;abstract
function
attach($K,$U,$B);abstract
function
quote($O);abstract
function
select_db($Bb);abstract
function
query($D,$fi=false);function
multi_query($D){return$this->multi=$this->query($D);}function
store_result(){return$this->multi;}function
next_result(){return
false;}function
inTransaction(){return
false;}}if(extension_loaded('pdo')){abstract
class
PdoDb
extends
SqlDb{protected$pdo;function
dsn($Yb,$U,$B,array$_=array()){$_[\PDO::ATTR_ERRMODE]=\PDO::ERRMODE_SILENT;$_[\PDO::ATTR_STATEMENT_CLASS]=array('Adminer\PdoResult');try{$this->pdo=new
\PDO($Yb,$U,$B,$_);}catch(\Exception$pc){return$pc->getMessage();}$this->server_info=@$this->pdo->getAttribute(\PDO::ATTR_SERVER_VERSION);return'';}function
quote($O){return$this->pdo->quote($O);}function
query($D,$fi=false){$E=$this->pdo->query($D);$this->error="";if(!$E){list(,$this->errno,$this->error)=$this->pdo->errorInfo();if(!$this->error)$this->error=lang(25);return
false;}$this->store_result($E);return$E;}function
store_result($E=null){if(!$E){$E=$this->multi;if(!$E)return
false;}if($E->columnCount()){$E->num_rows=$E->rowCount();return$E;}$this->affected_rows=$E->rowCount();return
true;}function
next_result(){$E=$this->multi;if(!is_object($E))return
false;$E->_offset=0;return@$E->nextRowset();}function
inTransaction(){return$this->pdo->inTransaction();}}class
PdoResult
extends
\PDOStatement{var$_offset=0,$num_rows;function
fetch_assoc(){return$this->fetch_array(\PDO::FETCH_ASSOC);}function
fetch_row(){return$this->fetch_array(\PDO::FETCH_NUM);}private
function
fetch_array($Ye){$F=$this->fetch($Ye);return($F?array_map(array($this,'unresource'),$F):$F);}private
function
unresource($W){return(is_resource($W)?stream_get_contents($W):$W);}function
fetch_field(){$G=(object)$this->getColumnMeta($this->_offset++);$S=$G->pdo_type;$G->type=($S==\PDO::PARAM_INT?0:15);$G->charsetnr=($S==\PDO::PARAM_LOB||(isset($G->flags)&&in_array("blob",(array)$G->flags))?63:0);return$G;}function
seek($qf){for($o=0;$o<$qf;$o++)$this->fetch();}}}function
add_driver($p,$y){SqlDriver::$drivers[$p]=$y;}function
get_driver($p){return
SqlDriver::$drivers[$p];}abstract
class
SqlDriver{static$instance;static$drivers=array();static$extensions=array();static$jush;static$passwords=true;protected$conn;protected$types=array();var$delimiter=";";var$insertFunctions=array();var$editFunctions=array();var$unsigned=array();var$operators=array();var$functions=array();var$grouping=array();var$onActions="RESTRICT|NO ACTION|CASCADE|SET NULL|SET DEFAULT";var$partitionBy=array();var$inout="IN|OUT|INOUT";var$enumLength="'(?:''|[^'\\\\]|\\\\.)*'";var$generated=array();var$primary="";var$query="";static
function
jushModule(){return"";}static
function
jushAutocomplete(array$R,$rh){$Dh=array_fill_keys(array_keys($R),array());foreach(driver()->allFields()as$P=>$l){foreach($l
as$k)$Dh[$P][]=$k["field"];}return"jush.autocompleteSql('".idf_escape("")."', ".json_encode($Dh).", ".json_encode($rh).")";}static
function
connect($K,$U,$B){list($wd,$dg)=host_port($K);if(preg_match('~[^-\w.:/]~',$wd.$dg))return
lang(26);if(preg_match('~^-?\d+~',$dg,$x)&&($x[0]<1024||$x[0]>65535))return
lang(27);$f=new
Db;return($f->attach($K,$U,$B)?:$f);}function
__construct(Db$f){$this->conn=$f;}function
types(){return
call_user_func_array('array_merge',array_values($this->types));}function
structuredTypes(){return
array_map('array_keys',$this->types);}function
enumLength(array$k){}function
unconvertFunction(array$k){}function
select($P,array$I,array$Z,array$dd,array$_f=array(),$v=1,$A=0,$mg=false){$Xd=(count($dd)<count($I));$D=adminer()->selectQueryBuild($I,$Z,$dd,$_f,$v,$A);if(!$D)$D="SELECT".limit(($_GET["page"]!="last"&&$v&&$dd&&$Xd&&JUSH=="sql"?"SQL_CALC_FOUND_ROWS ":"").implode(", ",$I)."\nFROM ".table($P),($Z?"\nWHERE ".implode(" AND ",$Z):"").($dd&&$Xd?"\nGROUP BY ".implode(", ",$dd):"").($_f?"\nORDER BY ".implode(", ",$_f):""),$v,($A?$v*$A:0),"\n");$this->query=$D;$qh=microtime(true);$F=$this->conn->query($D,(!$v&&!$mg?1:0));if($mg)echo
adminer()->selectQuery($D,$qh,!$F);return$F;}function
delete($P,$tg,$v=0){$D="FROM ".table($P);return
queries("DELETE".($v?limit1($P,$D,$tg):" $D$tg"));}function
update($P,array$L,$tg,$v=0,$J="\n"){$Y=array();foreach($L
as$t=>$W)$Y[]="$t = $W";$D=table($P)." SET$J".implode(",$J",$Y);return
queries("UPDATE".($v?limit1($P,$D,$tg,$J):" $D$tg"));}function
insert($P,array$L){return
queries("INSERT INTO ".table($P).($L?" (".implode(", ",array_keys($L)).")\nVALUES (".implode(", ",$L).")":" DEFAULT VALUES").$this->insertReturning($P));}function
insertReturning($P){return"";}function
insertUpdate($P,array$H,array$C){foreach($H
as$L){$Z=array();foreach($L
as$t=>$W){if(isset($C[idf_unescape($t)]))$Z[]="$t = $W";}if(!($Z&&$this->update($P,$L," WHERE ".implode(" AND ",$Z))&&$this->conn->affected_rows)&&!$this->insert($P,$L))return
false;}return
true;}function
begin(){return
queries("BEGIN");}function
commit(){return
queries("COMMIT");}function
rollback(){return
queries("ROLLBACK");}function
slowQuery($D,$Kh){}function
convertSearch($q,array$W,array$k){return$q;}function
value($W,array$k){return(method_exists($this->conn,'value')?$this->conn->value($W,$k):$W);}function
quoteBinary($Kg){return
q($Kg);}function
typeName(\stdClass$k){return(isset($k->native_type)?$k->native_type:"");}function
warnings(){}function
tableHelp($y,$ae=false){}function
inheritsFrom($P){return
array();}function
inheritedTables($P){return
array();}function
partitionsInfo($P){return
array();}function
hasCStyleEscapes(){return
false;}function
lineComment(){return"--";}function
engines(){return
array();}function
supportsIndex(array$Q){return!is_view($Q);}function
supportsAlterIndex(array$Q){return
true;}function
indexAlgorithms(array$zh){return
array();}function
indexOpclasses(){return
array();}function
checkConstraints($P){return
get_key_vals("SELECT c.CONSTRAINT_NAME, CHECK_CLAUSE
FROM INFORMATION_SCHEMA.CHECK_CONSTRAINTS c
JOIN INFORMATION_SCHEMA.TABLE_CONSTRAINTS t
	ON c.CONSTRAINT_SCHEMA = t.CONSTRAINT_SCHEMA AND c.CONSTRAINT_NAME = t.CONSTRAINT_NAME".($this->conn->flavor=='maria'?" AND c.TABLE_NAME = ".q($P):"")."
WHERE c.CONSTRAINT_SCHEMA = ".q($_GET["ns"]!=""?$_GET["ns"]:DB)."
AND t.TABLE_NAME = ".q($P).(JUSH=="pgsql"?"
AND CHECK_CLAUSE NOT LIKE '% IS NOT NULL'":""),$this->conn);}function
allFields(){$F=array();if(DB!=""){foreach(get_rows("SELECT c.TABLE_NAME AS tab, c.COLUMN_NAME AS field, c.IS_NULLABLE AS nullable,
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
SqlDb{var$extension="PgSQL";var$timeout=0;private$link,$string,$database=true;function
_error($jc,$j){if(ini_bool("html_errors"))$j=html_entity_decode(strip_tags($j));$j=preg_replace('~^[^:]*: ~','',$j);$this->error=$j;}function
attach($K,$U,$B){$h=adminer()->database();set_error_handler(array($this,'_error'));list($wd,$dg)=host_port($K);$this->string="host='$wd'".($dg?" port=$dg":"")." user='".addcslashes($U,"'\\")."' password='".addcslashes($B,"'\\")."'";$M=adminer()->connectSsl();if(isset($M["mode"]))$this->string
.=" sslmode=$M[mode]";$this->link=@pg_connect("$this->string dbname='".($h!=""?addcslashes($h,"'\\"):"postgres")."'",PGSQL_CONNECT_FORCE_NEW);if(!$this->link&&$h!=""){$this->database=false;$this->link=@pg_connect("$this->string dbname='postgres'",PGSQL_CONNECT_FORCE_NEW);}restore_error_handler();if($this->link)pg_set_client_encoding($this->link,"UTF8");return($this->link?'':$this->error);}function
quote($O){return(function_exists('pg_escape_literal')?pg_escape_literal($this->link,$O):"'".pg_escape_string($this->link,$O)."'");}function
value($W,array$k){return($k["type"]=="bytea"&&$W!==null?pg_unescape_bytea($W):$W);}function
select_db($Bb){if($Bb==adminer()->database())return$this->database;$F=@pg_connect("$this->string dbname='".addcslashes($Bb,"'\\")."'",PGSQL_CONNECT_FORCE_NEW);if($F)$this->link=$F;return$F;}function
close(){$this->link=@pg_connect("$this->string dbname='postgres'");}function
query($D,$fi=false){if(self::$untrusted)$E=(@pg_query($this->link,"BEGIN READ ONLY")?@pg_query_params($this->link,$D,array()):false);else$E=@pg_query($this->link,$D);$this->error="";if(!$E){$this->error=pg_last_error($this->link);$F=false;}elseif(!pg_num_fields($E)){$this->affected_rows=pg_affected_rows($E);$F=true;}else$F=new
Result($E);if(self::$untrusted)@pg_query($this->link,"COMMIT");if($this->timeout){$this->timeout=0;$this->query("RESET statement_timeout");}return$F;}function
warnings(){if(PHP_VERSION_ID>=70100){$F=implode("\n",pg_last_notice($this->link,PGSQL_NOTICE_ALL));pg_last_notice($this->link,PGSQL_NOTICE_CLEAR);}else$F=pg_last_notice($this->link);return
nl_br(h($F));}function
inTransaction(){$N=pg_transaction_status($this->link);return$N==PGSQL_TRANSACTION_INTRANS||$N==PGSQL_TRANSACTION_INERROR;}function
copyFrom($P,array$H){$this->error='';set_error_handler(function($jc,$j){$this->error=(ini_bool('html_errors')?html_entity_decode($j):$j);return
true;});$F=pg_copy_from($this->link,$P,$H);restore_error_handler();return$F;}}class
Result{var$num_rows;private$result,$offset=0;function
__construct($E){$this->result=$E;$this->num_rows=pg_num_rows($E);}function
fetch_assoc(){return
pg_fetch_assoc($this->result);}function
fetch_row(){return
pg_fetch_row($this->result);}function
fetch_field(){$d=$this->offset++;$F=new
\stdClass;$F->orgtable=pg_field_table($this->result,$d);$F->name=pg_field_name($this->result,$d);$S=pg_field_type($this->result,$d);$F->native_type=$S;$F->type=(preg_match(number_type(),$S)?0:15);$F->charsetnr=($S=="bytea"?63:0);return$F;}}}elseif(extension_loaded("pdo_pgsql")){class
PgsqlDb
extends
PdoDb{var$extension="PDO_PgSQL";var$timeout=0;function
attach($K,$U,$B){$h=adminer()->database();list($wd,$dg)=host_port($K);$Yb="pgsql:host='$wd'".($dg?" port=$dg":"")." client_encoding=utf8 dbname='".($h!=""?addcslashes($h,"'\\"):"postgres")."'";$M=adminer()->connectSsl();if(isset($M["mode"]))$Yb
.=" sslmode=$M[mode]";return$this->dsn($Yb,$U,$B);}function
select_db($Bb){return(adminer()->database()==$Bb);}function
query($D,$fi=false){$F=(self::$untrusted?$this->readOnlyQuery($D):parent::query($D,$fi));if($this->timeout){$this->timeout=0;parent::query("RESET statement_timeout");}return$F;}private
function
readOnlyQuery($D){$this->error="";if(!$this->pdo->query("BEGIN READ ONLY")){list(,$this->errno,$this->error)=$this->pdo->errorInfo();return
false;}$E=$this->pdo->prepare($D);$F=false;if($E&&$E->execute()){$this->store_result($E);$F=$E;}else{list(,$this->errno,$this->error)=($E?$E->errorInfo():$this->pdo->errorInfo());if(!$this->error)$this->error=lang(25);}$this->pdo->query("COMMIT");return$F;}function
warnings(){}function
copyFrom($P,array$H){$F=$this->pdo->pgsqlCopyFromArray($P,$H);$this->error=idx($this->pdo->errorInfo(),2)?:'';return$F;}function
close(){}}}if(class_exists('Adminer\PgsqlDb')){class
Db
extends
PgsqlDb{function
multi_query($D){if(preg_match('~\bCOPY\s+(.+?)\s+FROM\s+stdin;\n?(.*)\n\\\\\.$~is',str_replace("\r\n","\n",$D),$x)){$H=explode("\n",$x[2]);$this->multi=false;$this->affected_rows=count($H);return$this->copyFrom($x[1],$H);}return
parent::multi_query($D);}}}class
Driver
extends
SqlDriver{static$extensions=array("PgSQL","PDO_PgSQL");static$jush="pgsql";var$operators=array("=","<",">","<=",">=","!=","~","~*","!~","LIKE","LIKE %%","ILIKE","ILIKE %%","IN","IS NULL","NOT LIKE","NOT ILIKE","NOT IN","IS NOT NULL","SQL");var$functions=array("char_length","lower","round","to_hex","to_timestamp","upper");var$grouping=array("avg","count","count distinct","max","min","sum");var$nsOid="(SELECT oid FROM pg_namespace WHERE nspname = current_schema())";private$userTypes=array();static
function
connect($K,$U,$B){$f=parent::connect($K,$U,$B);if(is_string($f))return$f;$Bi=get_val("SELECT version()",0,$f);$f->flavor=(preg_match('~CockroachDB~',$Bi)?'cockroach':'');$f->server_info=preg_replace('~^\D*([\d.]+[-\w]*).*~','\1',$Bi);if(min_version(9,0,$f))$f->query("SET application_name = 'Adminer'");if($f->flavor=='cockroach')add_driver(DRIVER,"CockroachDB");return$f;}function
__construct(Db$f){parent::__construct($f);$this->types=array(lang(28)=>array("smallint"=>5,"integer"=>10,"bigint"=>19,"boolean"=>1,"numeric"=>0,"real"=>7,"double precision"=>16,"money"=>20),lang(29)=>array("date"=>13,"time"=>17,"timestamp"=>20,"timestamptz"=>21,"interval"=>0),lang(30)=>array("character"=>0,"character varying"=>0,"text"=>0,"tsquery"=>0,"tsvector"=>0,"uuid"=>0,"xml"=>0),lang(31)=>array("bit"=>0,"bit varying"=>0,"bytea"=>0),lang(32)=>array("cidr"=>43,"inet"=>43,"macaddr"=>17,"macaddr8"=>23,"txid_snapshot"=>0),lang(33)=>array("box"=>0,"circle"=>0,"line"=>0,"lseg"=>0,"path"=>0,"point"=>0,"polygon"=>0),);if(min_version(9.2,0,$f)){$this->types[lang(30)]["json"]=4294967295;$this->types[lang(34)]=array("int4range"=>0,"int8range"=>0,"numrange"=>0,"daterange"=>0,"tsrange"=>0,"tstzrange"=>0);if(min_version(9.4,0,$f))$this->types[lang(30)]["jsonb"]=4294967295;}$this->insertFunctions=array("char"=>"md5","date|time"=>"now",);$this->editFunctions=array(number_type()=>"+/-","date|time"=>"+ interval/- interval","char|text"=>"||",);if(min_version(12,0,$f)){$this->generated[]="STORED";if(min_version(18,0,$f))$this->generated[]="VIRTUAL";}$this->partitionBy=array("RANGE","LIST");if(!$f->flavor)$this->partitionBy[]="HASH";}function
enumLength(array$k){$rf=$this->userTypes[$k["type"]];return($rf?type_values($rf):"");}function
setUserTypes(array$ei){$this->userTypes=array_flip($ei);$this->types[lang(7)]=array_fill_keys(array_keys($this->userTypes),0);}function
insertReturning($P){$wa=array_filter(fields($P),function($k){return$k['auto_increment'];});return(count($wa)==1?" RETURNING ".idf_escape(key($wa)):"");}function
insertUpdate($P,array$H,array$C){$e=array_keys(reset($H));$gb=array();$T=array();foreach($e
as$t){if(isset($C[idf_unescape($t)]))$gb[]=$t;else$T[]="$t = EXCLUDED.$t";}if(!$gb||!min_version(9.5)||count($gb)!=count($C))return
parent::insertUpdate($P,$H,$C);$jg="INSERT INTO ".table($P)." (".implode(", ",$e).") VALUES\n";$vh="\nON CONFLICT (".implode(", ",$gb).")".($T?" DO UPDATE SET ".implode(", ",$T):" DO NOTHING");$Y=array();$u=0;foreach($H
as$L){$X="(".implode(", ",$L).")";if($Y&&strlen($jg)+$u+strlen($X)+strlen($vh)>1e6){if(!queries($jg.implode(",\n",$Y).$vh))return
false;$Y=array();$u=0;}$Y[]=$X;$u+=strlen($X)+2;}return
queries($jg.implode(",\n",$Y).$vh);}function
slowQuery($D,$Kh){$this->conn->query("SET statement_timeout = ".(1000*$Kh));$this->conn->timeout=1000*$Kh;return$D;}function
convertSearch($q,array$W,array$k){$Xf=preg_match('(LIKE|^!?~)',$W["op"]);$hf=preg_match('~^(character( varying)?|text|citext|bpchar|name)$~',$k["type"])||(!$Xf&&preg_match('~'.number_type().'|^(date|time|timetz|timestamp|timestamptz|boolean)$~',$k["type"]));return($hf&&!preg_match('~\[]$~',$k["full_type"])?$q:"CAST($q AS text)");}function
quoteBinary($Kg){return"'\\x".bin2hex($Kg)."'";}function
warnings(){return$this->conn->warnings();}function
tableHelp($y,$ae=false){$ve=array("information_schema"=>"infoschema","pg_catalog"=>($ae?"view":"catalog"),);$w=$ve[$_GET["ns"]];if($w)return"$w-".str_replace("_","-",$y).".html";}function
inheritsFrom($P){return
get_rows("SELECT relname AS table, nspname AS ns FROM pg_class JOIN pg_inherits ON inhparent = oid JOIN pg_namespace ON relnamespace = pg_namespace.oid WHERE inhrelid = ".$this->tableOid($P)." ORDER BY 2, 1");}function
inheritedTables($P){return
get_rows("SELECT relname AS table, nspname AS ns FROM pg_inherits JOIN pg_class ON inhrelid = oid JOIN pg_namespace ON relnamespace = pg_namespace.oid WHERE inhparent = ".$this->tableOid($P)." ORDER BY 2, 1");}function
partitionsInfo($P){$G=(min_version(10)?$this->conn->query("SELECT * FROM pg_partitioned_table WHERE partrelid = ".$this->tableOid($P))->fetch_assoc():null);if($G){$c=get_vals("SELECT attname FROM pg_attribute WHERE attrelid = $G[partrelid] AND attnum IN (".str_replace(" ",", ",$G["partattrs"]).")");$Ja=array('h'=>'HASH','l'=>'LIST','r'=>'RANGE');return
array("partition_by"=>$Ja[$G["partstrat"]],"partition"=>implode(", ",array_map('Adminer\idf_escape',$c)),);}return
array();}function
tableOid($P){return"(SELECT oid FROM pg_class WHERE relnamespace = $this->nsOid AND relname = ".q($P)." AND relkind IN ('r', 'm', 'v', 'f', 'p'))";}function
indexAlgorithms(array$zh){static$F=array();if(!$F)$F=get_vals("SELECT amname FROM pg_am".(min_version(9.6)?" WHERE amtype = 'i'":"")." ORDER BY amname = '".($this->conn->flavor=='cockroach'?"prefix":"btree")."' DESC, amname");return$F;}function
indexOpclasses(){static$F=array();if(!$F&&$this->conn->flavor!='cockroach')$F=get_vals("SELECT DISTINCT opcname FROM pg_catalog.pg_opclass WHERE NOT opcdefault ORDER BY opcname");return$F;}function
supportsIndex(array$Q){return$Q["Engine"]!="view";}function
hasCStyleEscapes(){static$La;if($La===null)$La=(get_val("SHOW standard_conforming_strings",0,$this->conn)=="off");return$La;}}function
idf_escape($q){return'"'.str_replace('"','""',$q).'"';}function
table($q){return
idf_escape($q);}function
get_databases($Mc){return
get_vals("SELECT datname FROM pg_database
WHERE datallowconn = TRUE AND has_database_privilege(datname, 'CONNECT')
ORDER BY datname");}function
limit($D,$Z,$v,$qf=0,$J=" "){return" $D$Z".($v?$J."LIMIT $v".($qf?" OFFSET $qf":""):"");}function
limit1($P,$D,$Z,$J="\n"){return(preg_match('~^INTO~',$D)?limit($D,$Z,1,0,$J):" $D".(is_view(table_status1($P))?$Z:$J."WHERE ctid = (SELECT ctid FROM ".table($P).$Z.$J."LIMIT 1)"));}function
db_collation($h,array$Za){return
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
count_tables(array$Cb){$F=array();foreach($Cb
as$h){if(connection()->select_db($h))$F[$h]=count(tables_list());}return$F;}function
table_status($y="",$yc=false){static$od;if($od===null)$od=get_val("SELECT 'pg_table_size'::regproc");$Wg=(!$yc&&min_version(10));$F=array();foreach(get_rows("SELECT
	relname AS \"Name\",
	CASE relkind WHEN 'v' THEN 'view' WHEN 'm' THEN 'materialized view' ELSE 'table' END AS \"Engine\"".($od?",
	pg_table_size(c.oid) AS \"Data_length\",
	pg_indexes_size(c.oid) AS \"Index_length\"":"").",
	obj_description(c.oid, 'pg_class') AS \"Comment\",
	".(min_version(12)?"''":"CASE WHEN relhasoids THEN 'oid' ELSE '' END")." AS \"Oid\",
	reltuples AS \"Rows\",
	".($Wg?"seq.last_value":"NULL")." AS \"Auto_increment\",
	".(min_version(10)?"relispartition::int AS partition,":"")."
	current_schema() AS nspname
FROM pg_class c
".($Wg?"LEFT JOIN (
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
is_view(array$Q){return
in_array($Q["Engine"],array("view","materialized view"));}function
fk_support(array$Q){return
true;}function
fields($P){$F=array();$ma=array('timestamp without time zone'=>'timestamp','timestamp with time zone'=>'timestamptz','time without time zone'=>'time','time with time zone'=>'timetz',);foreach(get_rows("SELECT
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
WHERE a.attrelid = ".driver()->tableOid($P)."
AND NOT a.attisdropped
AND a.attnum > 0
ORDER BY a.attnum")as$G){preg_match('~([^([]+)(\((.*)\))?([a-z ]+)?((\[[0-9]*])*)$~',$G["full_type"],$x);list(,$S,$u,$G["length"],$ha,$sa)=$x;$G["length"].=$sa;$Qa=$S.$ha;if(isset($ma[$Qa])){$G["type"]=$ma[$Qa];$G["full_type"]=$G["type"].$u.$sa;}else{$G["type"]=$S;$G["full_type"]=$G["type"].$u.$ha.$sa;}if(in_array($G['attidentity'],array('a','d')))$G['default']='GENERATED '.($G['attidentity']=='d'?'BY DEFAULT':'ALWAYS').' AS IDENTITY';$G["generated"]=idx(array("s"=>"STORED","v"=>"VIRTUAL"),$G["attgenerated"],"");$G["composite"]=($G["typcategory"]=="C");$G["null"]=!$G["attnotnull"];$G["auto_increment"]=$G['attidentity']||preg_match('~^nextval\(~i',$G["default"])||preg_match('~^unique_rowid\(~',$G["default"]);$G["privileges"]=array("insert"=>1,"select"=>1,"update"=>1,"where"=>1,"order"=>1);if(!$G['generated']&&preg_match('~(.+)::[^,)]+(.*)~',$G["default"],$x))$G["default"]=($x[1]=="NULL"?null:idf_unescape($x[1]).$x[2]);$F[$G["field"]]=$G;}return$F;}function
indexes($P,$g=null){$g=connection($g);$F=array();$Ch=driver()->tableOid($P);$e=get_key_vals("SELECT attnum, attname FROM pg_attribute WHERE attrelid = $Ch AND attnum > 0",$g);foreach(get_rows("SELECT relname, indisunique::int, indisprimary::int, indkey, indoption, amname,
	pg_get_expr(indpred, indrelid, true) AS partial, pg_get_expr(indexprs, indrelid) AS indexpr".($g->flavor=='cockroach'?"":",
	(SELECT string_agg(CASE WHEN opcdefault THEN '' ELSE opcname END, ' ' ORDER BY s)
		FROM generate_subscripts(indclass, 1) AS s JOIN pg_catalog.pg_opclass ON pg_opclass.oid = indclass[s]) AS opclasses")."
FROM pg_index
JOIN pg_class ON indexrelid = oid
JOIN pg_am ON pg_am.oid = pg_class.relam
WHERE indrelid = $Ch
ORDER BY indisprimary DESC, indisunique DESC",$g)as$G){$Bg=$G["relname"];$F[$Bg]["type"]=($G["indisprimary"]?"PRIMARY":($G["indisunique"]?"UNIQUE":"INDEX"));$F[$Bg]["columns"]=array();$F[$Bg]["descs"]=array();$F[$Bg]["algorithm"]=$G["amname"];$F[$Bg]["partial"]=$G["partial"];$Hd=preg_split('~(?<=\)), (?=\()~',$G["indexpr"]);foreach(explode(" ",$G["indkey"])as$Id)$F[$Bg]["columns"][]=($Id?$e[$Id]:array_shift($Hd));foreach(explode(" ",$G["indoption"])as$Jd)$F[$Bg]["descs"][]=(intval($Jd)&1?'1':null);$F[$Bg]["opclasses"]=($G["opclasses"]!=""?explode(" ",$G["opclasses"]):array());$F[$Bg]["lengths"]=array();}return$F;}function
foreign_keys($P){$F=array();foreach(get_rows("SELECT conname, condeferrable::int AS deferrable, condeferred::int AS deferred, pg_get_constraintdef(oid) AS definition
FROM pg_constraint
WHERE conrelid = ".driver()->tableOid($P)."
AND contype = 'f'::char
ORDER BY conkey, conname")as$G){$G['deferrable']=($G['deferrable']?'':'NOT ').'DEFERRABLE'.($G['deferred']?' INITIALLY DEFERRED':'');if(preg_match('~FOREIGN KEY\s*\((.+)\)\s*REFERENCES (.+)\((.+)\)(.*)$~iA',$G['definition'],$x)){$G['source']=array_map('Adminer\idf_unescape',array_map('trim',explode(',',$x[1])));if(preg_match('~^(("([^"]|"")+"|[^"]+)\.)?"?("([^"]|"")+"|[^"]+)$~',$x[2],$De)){$G['ns']=idf_unescape($De[2]);$G['table']=idf_unescape($De[4]);}$G['target']=array_map('Adminer\idf_unescape',array_map('trim',explode(',',$x[3])));$G['on_delete']=(preg_match("~ON DELETE (".driver()->onActions.")~",$x[4],$De)?$De[1]:'NO ACTION');$G['on_update']=(preg_match("~ON UPDATE (".driver()->onActions.")~",$x[4],$De)?$De[1]:'NO ACTION');$F[$G['conname']]=$G;}}return$F;}function
view($y){return
array("select"=>trim(get_val("SELECT pg_get_viewdef(".driver()->tableOid($y).")")));}function
collations(){return
array();}function
information_schema($h,$Lg=""){return
in_array($Lg!=""?$Lg:get_schema(),array("information_schema","pg_catalog","pg_toast"));}function
error(){$F=h(connection()->error);if(preg_match('~^(.*\n)?([^\n]*)\n( *)\^(\n.*)?$~s',$F,$x))$F=$x[1].preg_replace('~((?:[^&]|&[^;]*;){'.strlen($x[3]).'})(.*)~','\1<b>\2</b>',$x[2]).$x[4];return
nl_br($F);}function
create_database($h,$Ya){return
queries("CREATE DATABASE ".idf_escape($h).($Ya?" ENCODING ".idf_escape($Ya):""));}function
drop_databases(array$Cb){connection()->close();return
apply_queries("DROP DATABASE",$Cb,'Adminer\idf_escape');}function
rename_database($y,$Ya){connection()->close();return!!queries("ALTER DATABASE ".idf_escape(DB)." RENAME TO ".idf_escape($y));}function
auto_increment(){return"";}function
alter_table($P,$y,array$l,array$Oc,$cb,$gc,$Ya,$wa,$Rf){$b=array();$sg=array();if($P!=""&&$P!=$y)$sg[]="ALTER TABLE ".table($P)." RENAME TO ".table($y);$Tg="";foreach($l
as$k){$d=idf_escape($k[0]);$W=$k[1];if(!$W)$b[]="DROP $d";else{$zi=$W[5];unset($W[5]);if($k[0]==""){if(isset($W[6]))$W[1]=($W[1]==" bigint"?" big":($W[1]==" smallint"?" small":" "))."serial";$b[]=($P!=""?"ADD ":"  ").implode($W);if(isset($W[6]))$b[]=($P!=""?"ADD":" ")." PRIMARY KEY ($W[0])";}else{if($d!=$W[0])$sg[]="ALTER TABLE ".table($y)." RENAME $d TO $W[0]";$b[]="ALTER $d TYPE$W[1]";$Ug=$P."_".idf_unescape($W[0])."_seq";$b[]="ALTER $d ".($W[3]?"SET".preg_replace('~GENERATED ALWAYS(.*) (STORED|VIRTUAL)~','EXPRESSION\1',$W[3]):(isset($W[6])?"SET DEFAULT nextval(".q($Ug).")":"DROP DEFAULT"));if(isset($W[6]))$Tg="CREATE SEQUENCE IF NOT EXISTS ".idf_escape($Ug)." OWNED BY ".idf_escape($P).".$W[0]";$b[]="ALTER $d ".($W[2]==" NULL"?"DROP NOT":"SET").$W[2];}if($k[0]!=""||$zi!="")$sg[]="COMMENT ON COLUMN ".table($y).".$W[0] IS ".($zi!=""?substr($zi,9):"''");}}$b=array_merge($b,$Oc);if($P==""){$N="";if($Rf){$Va=(connection()->flavor=='cockroach');$N=" PARTITION BY $Rf[partition_by]($Rf[partition])";if($Rf["partition_by"]=='HASH'){$Sf=+$Rf["partitions"];for($o=0;$o<$Sf;$o++)$sg[]="CREATE TABLE ".idf_escape($y."_$o")." PARTITION OF ".idf_escape($y)." FOR VALUES WITH (MODULUS $Sf, REMAINDER $o)";}else{$kg="MINVALUE";foreach($Rf["partition_names"]as$o=>$W){$X=$Rf["partition_values"][$o];$Pf=" VALUES ".($Rf["partition_by"]=='LIST'?"IN ($X)":"FROM ($kg) TO ($X)");if($Va)$N
.=($o?",":" (")."\n  PARTITION ".(preg_match('~^DEFAULT$~i',$W)?$W:idf_escape($W))."$Pf";else$sg[]="CREATE TABLE ".idf_escape($y."_$W")." PARTITION OF ".idf_escape($y)." FOR$Pf";$kg=$X;}$N
.=($Va?"\n)":"");}}array_unshift($sg,"CREATE TABLE ".table($y)." (\n".implode(",\n",$b)."\n)$N");}elseif($b)array_unshift($sg,"ALTER TABLE ".table($P)."\n".implode(",\n",$b));if($Tg)array_unshift($sg,$Tg);if($cb!==null)$sg[]="COMMENT ON TABLE ".table($y)." IS ".q($cb);foreach($sg
as$D){if(!queries($D))return
false;}if($wa!=""){foreach(fields($y)as$_c=>$k){if($k["auto_increment"])return!!queries("SELECT setval(pg_get_serial_sequence(".q(table($y)).", ".q($_c)."), $wa)");}}return
true;}function
alter_indexes($P,$b){$tb=array();$Vb=array();$sg=array();foreach($b
as$W){if($W[0]!="INDEX")$tb[]=($W[2]=="DROP"?"\nDROP CONSTRAINT ".idf_escape($W[1]):"\nADD".($W[1]!=""?" CONSTRAINT ".idf_escape($W[1]):"")." $W[0] ".($W[0]=="PRIMARY"?"KEY ":"")."(".implode(", ",$W[2]).")");elseif($W[2]=="DROP")$Vb[]=idf_escape($W[1]);else$sg[]="CREATE INDEX ".idf_escape($W[1]!=""?$W[1]:uniqid($P."_"))." ON ".table($P).($W[3]?" USING $W[3]":"")." (".implode(", ",$W[2]).")".($W[4]?" WHERE $W[4]":"");}if($tb)array_unshift($sg,"ALTER TABLE ".table($P).implode(",",$tb));if($Vb)array_unshift($sg,"DROP INDEX ".implode(", ",$Vb));foreach($sg
as$D){if(!queries($D))return
false;}return
true;}function
truncate_tables(array$R){return!!queries("TRUNCATE ".implode(", ",array_map('Adminer\table',$R)));}function
drop_kinds(array$R){$F=array("MATERIALIZED VIEW"=>array(),"VIEW"=>array(),"TABLE"=>array());foreach($R
as$y=>$Q)$F[strtoupper($Q["Engine"])][]=idf_escape($Q["nspname"]).".".table($y);return
array_filter($F);}function
drop_views(array$Di){return
drop_tables($Di);}function
drop_tables(array$R){$sh=array();foreach($R
as$P)$sh[$P]=table_status1($P);foreach(drop_kinds($sh)as$he=>$gf){if(!queries("DROP $he ".implode(", ",$gf)))return
false;}return
true;}function
move_tables(array$R,array$Di,$Eh){foreach(array_merge($R,$Di)as$P){$N=table_status1($P);if(!queries("ALTER ".strtoupper($N["Engine"])." ".table($P)." SET SCHEMA ".idf_escape($Eh)))return
false;}return
true;}function
trigger($y,$P){if($y=="")return
array("Statement"=>"EXECUTE PROCEDURE ()");$e=array();$Z="WHERE trigger_schema = current_schema() AND event_object_table = ".q($P)." AND trigger_name = ".q($y);foreach(get_rows("SELECT * FROM information_schema.triggered_update_columns $Z")as$G)$e[]=$G["event_object_column"];$F=array();foreach(get_rows('SELECT trigger_name AS "Trigger", action_timing AS "Timing", event_manipulation AS "Event", \'FOR EACH \' || action_orientation AS "Type", action_statement AS "Statement"
FROM information_schema.triggers'."
$Z
ORDER BY event_manipulation DESC")as$G){if($e&&$G["Event"]=="UPDATE")$G["Event"].=" OF";$G["Of"]=implode(", ",$e);if($F)$G["Event"].=" OR $F[Event]";$F=$G;}return$F;}function
triggers($P){$F=array();foreach(get_rows("SELECT * FROM information_schema.triggers WHERE trigger_schema = current_schema() AND event_object_table = ".q($P))as$G){$Yh=trigger($G["trigger_name"],$P);$F[$Yh["Trigger"]]=array($Yh["Timing"],$Yh["Event"]);}return$F;}function
trigger_options(){return
array("Timing"=>array("BEFORE","AFTER"),"Event"=>array("INSERT","UPDATE","UPDATE OF","DELETE","INSERT OR UPDATE","INSERT OR UPDATE OF","DELETE OR INSERT","DELETE OR UPDATE","DELETE OR UPDATE OF","DELETE OR INSERT OR UPDATE","DELETE OR INSERT OR UPDATE OF",),"Type"=>array("FOR EACH ROW","FOR EACH STATEMENT"),);}function
routine($y,$S){$H=get_rows('SELECT routine_definition AS definition, LOWER(external_language) AS language, *
FROM information_schema.routines
WHERE routine_schema = current_schema() AND specific_name = '.q($y));$F=idx($H,0,array());$F["returns"]=array("type"=>preg_replace('~^_(.*)~','\1[]',"$F[type_udt_name]"));$F["fields"]=get_rows("SELECT COALESCE(parameter_name, ordinal_position::text) AS field,
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
routine_languages(){return
get_vals("SELECT LOWER(lanname) FROM pg_catalog.pg_language");}function
routine_id($y,array$G){$F=array();foreach($G["fields"]as$k){$u=$k["length"];$F[]=$k["type"].($u?"($u)":"");}return
idf_escape($y)."(".implode(", ",$F).")";}function
last_id($E){$G=(is_object($E)?$E->fetch_row():array());return($G?$G[0]:0);}function
explain(Db$f,$D){return$f->query("EXPLAIN $D");}function
found_rows(array$Q,array$Z){if(preg_match("~ rows=([0-9]+)~",get_val("EXPLAIN SELECT * FROM ".idf_escape($Q["Name"]).($Z?" WHERE ".implode(" AND ",$Z):"")),$Ag))return$Ag[1];}function
types($vc=false){$Va=connection()->flavor=='cockroach';$ie=($Va?"'e'":"'b','c','d','e'".(min_version(9.2)?",'r'":""));return
get_key_vals("SELECT t.oid, t.typname
FROM pg_type t
WHERE t.typnamespace = ".driver()->nsOid."
AND t.typtype IN ($ie)".($Va?"
AND t.typelem = 0":"
AND (t.typrelid = 0 OR (SELECT c.relkind FROM pg_class c WHERE c.oid = t.typrelid) = 'c')"."
AND NOT EXISTS (SELECT 1 FROM pg_type e WHERE e.typarray = t.oid)".($vc?'':"
AND t.oid NOT IN (SELECT objid FROM pg_catalog.pg_depend WHERE classid = 'pg_type'::regclass AND deptype = 'e')"))."
ORDER BY t.typname");}function
type_values($p){$ic=get_vals("SELECT enumlabel FROM pg_enum WHERE enumtypid = $p ORDER BY enumsortorder");return($ic?"'".implode("', '",array_map('addslashes',$ic))."'":"");}function
collation_name($rf){return(min_version(9.1)?"(SELECT collname FROM pg_collation WHERE oid = $rf AND collname != 'default')":"NULL");}function
type_definition($p){$S=first(get_rows("SELECT typtype, typisdefined::int AS defined, typrelid FROM pg_type WHERE oid = $p"));$F=array("kind"=>($S?$S["typtype"]:""),"definition"=>"");if(!$S||!$S["defined"])return$F;switch($F["kind"]){case'e':$Y=get_vals("SELECT enumlabel FROM pg_enum WHERE enumtypid = $p ORDER BY enumsortorder");$F["definition"]="AS ENUM (".implode(", ",array_map('Adminer\q',$Y)).")";break;case'c':$e=array();foreach(get_rows("SELECT attname, format_type(atttypid, atttypmod) AS full_type, ".collation_name("attcollation")." AS collation
FROM pg_attribute
WHERE attrelid = $S[typrelid] AND attnum > 0 AND NOT attisdropped
ORDER BY attnum")as$G)$e[]=idf_escape($G["attname"])." $G[full_type]".($G["collation"]?" COLLATE ".idf_escape($G["collation"]):"");$F["definition"]="AS (\n\t".implode(",\n\t",$e)."\n)";break;case'd':$Sb=first(get_rows("SELECT format_type(typbasetype, typtypmod) AS base, typnotnull::int AS notnull, typdefault, ".collation_name("typcollation")." AS collation
FROM pg_type WHERE oid = $p"));$F["definition"]="AS $Sb[base]".($Sb["collation"]?" COLLATE ".idf_escape($Sb["collation"]):"").($Sb["typdefault"]!=""?" DEFAULT $Sb[typdefault]":"").($Sb["notnull"]?" NOT NULL":"");foreach(get_rows("SELECT conname, pg_get_constraintdef(oid) AS definition FROM pg_constraint WHERE contypid = $p AND contype != 'n' ORDER BY conname")as$G)$F["definition"].=" CONSTRAINT ".idf_escape($G["conname"])." $G[definition]";break;case'r':$wg=first(get_rows("SELECT format_type(rngsubtype, NULL) AS subtype,
(SELECT opcname FROM pg_opclass WHERE oid = rngsubopc) AS subtype_opclass,
".collation_name("rngcollation")." AS collation,
NULLIF(rngcanonical, 0)::regproc::text AS canonical,
NULLIF(rngsubdiff, 0)::regproc::text AS subtype_diff".(min_version(14)?",
(SELECT typname FROM pg_type WHERE oid = rngmultitypid) AS multirange_type_name":"")."
FROM pg_range WHERE rngtypid = $p"));$_=array();foreach(array("subtype"=>0,"subtype_opclass"=>1,"collation"=>1,"canonical"=>0,"subtype_diff"=>0,"multirange_type_name"=>1)as$t=>$mc){if($wg[$t]!="")$_[]=strtoupper($t)." = ".($mc?idf_escape($wg[$t]):$wg[$t]);}$F["definition"]="AS RANGE (".implode(", ",$_).")";}return$F;}function
schemas(){return
get_vals("SELECT nspname FROM pg_namespace ORDER BY nspname");}function
get_schema(){return(string)get_val("SELECT current_schema()");}function
set_schema($Lg,$g=null){$F=connection($g)->query("SET search_path TO ".idf_escape($Lg));driver()->setUserTypes(types(true));return!!$F;}function
drop_sql(array$R){$F="";foreach(drop_kinds($R)as$he=>$gf)$F
.="DROP $he IF EXISTS ".implode(", ",$gf).";\n";return($F?"$F\n":"");}function
foreign_keys_sql($P){$F="";$N=table_status1($P);$lf=idf_escape($N['nspname']);$Kc=foreign_keys($P);ksort($Kc);foreach($Kc
as$Jc=>$Ic)$F
.="ALTER TABLE ONLY $lf.".idf_escape($N['Name'])." ADD CONSTRAINT ".idf_escape($Jc)." ".preg_replace('~( REFERENCES )([^(.]+\()~',"\\1$lf.\\2",$Ic["definition"]).";\n";return($F?"$F\n":$F);}function
indexes_sql($P,$C=""){$F="";$D="SELECT indexdef FROM pg_catalog.pg_indexes WHERE schemaname = current_schema() AND tablename = ".q($P).($C!=""?" AND indexname != ".q($C):"");foreach(get_rows($D,null,"-- ")as$G)$F
.="\n\n$G[indexdef];";return$F;}function
create_sql($P,$wa,$uh){$Hg=array();$Wg=array();$Xg=array();$Vg=array();$N=table_status1($P);$lf=idf_escape($N['nspname']);if(is_view($N)){$Ci=view($P);$tb="CREATE ".strtoupper($N["Engine"])." $lf.".idf_escape($P)." AS ".rtrim($Ci["select"],";").";";return
rtrim($tb.indexes_sql($P),';');}$l=fields($P);if(count($N)<2||empty($l))return"";$F="CREATE TABLE $lf.".idf_escape($N['Name'])." (\n    ";$Ah=q("$lf.".idf_escape($N['Name']));foreach($l
as$k){$Yg="";if($k['default']=="nextval('$N[Name]_$k[field]_seq')"){$Yg="$lf.".idf_escape("$N[Name]_$k[field]_seq");$k['default']=null;$k['full_type']=preg_replace('~int(eger)?~','serial',$k['full_type']);}$Of=idf_escape($k['field']).' '.$k['full_type'].preg_replace('~(nextval\(\')([^.\']+\')~','\1'.str_replace("'","''",$N['nspname']).'.\2',default_value($k)).($k['null']?"":" NOT NULL");$Hg[]=$Of;if(preg_match('~nextval\(\'([^\']+)\'\)~',$k['default'],$Ee)){$Ug=$Ee[1];$mh=first(get_rows((min_version(10)?"SELECT *, cache_size AS cache_value FROM pg_sequences WHERE schemaname = current_schema() AND sequencename = ".q(idf_unescape($Ug)):"SELECT * FROM $Ug"),null,"-- "));$Wg[]=($uh=="DROP+CREATE"?"DROP SEQUENCE IF EXISTS $lf.$Ug;\n":"")."CREATE SEQUENCE $lf.$Ug INCREMENT $mh[increment_by] MINVALUE $mh[min_value] MAXVALUE $mh[max_value]"." CACHE $mh[cache_value];";if(get_val("SELECT pg_get_serial_sequence($Ah, ".q($k['field']).")"))$Xg[]="\n\nALTER SEQUENCE $lf.$Ug OWNED BY $lf.".idf_escape($N['Name']).".".idf_escape($k['field']).";";if($wa)$Vg[]="$lf.$Ug";}elseif($wa&&$k['auto_increment'])$Vg[]=($Yg?:get_val("SELECT pg_get_serial_sequence($Ah, ".q($k['field']).")"));}if(!empty($Wg))$F=implode("\n\n",$Wg)."\n\n$F";$C="";foreach(indexes($P)as$Fd=>$r){if($r['type']=='PRIMARY'){$C=$Fd;$Hg[]="CONSTRAINT ".idf_escape($Fd)." PRIMARY KEY (".implode(', ',array_map('Adminer\idf_escape',$r['columns'])).")";}}foreach(driver()->checkConstraints($P)as$ib=>$kb)$Hg[]="CONSTRAINT ".idf_escape($ib)." CHECK ($kb)";$F
.=implode(",\n    ",$Hg)."\n)";$Pf=driver()->partitionsInfo($N['Name']);if($Pf)$F
.="\nPARTITION BY $Pf[partition_by]($Pf[partition])";$F
.="\nWITH (oids = ".($N['Oid']?'true':'false').");";$F
.=implode($Xg);if($N['Comment'])$F
.="\n\nCOMMENT ON TABLE $lf.".idf_escape($N['Name'])." IS ".q($N['Comment']).";";foreach($l
as$_c=>$k){if($k['comment'])$F
.="\n\nCOMMENT ON COLUMN $lf.".idf_escape($N['Name']).".".idf_escape($_c)." IS ".q($k['comment']).";";}$F
.=indexes_sql($P,$C);foreach(array_filter($Vg)as$Tg){$mh=first(get_rows("SELECT last_value, is_called::int FROM $Tg",null,"-- "));if($mh['is_called'])$F
.="\n\nDO \$\$ BEGIN PERFORM setval(".q($Tg).", $mh[last_value]); END \$\$;";}return
rtrim($F,';');}function
truncate_sql($P){return"TRUNCATE ".table($P);}function
truncate_all_sql(array$R){return($R?"TRUNCATE ".implode(", ",array_map('Adminer\table',$R)).";\n\n":"");}function
trigger_sql($P){$N=table_status1($P);$F="";foreach(triggers($P)as$Xh=>$Wh){$Yh=trigger($Xh,$N['Name']);$F
.="\nCREATE TRIGGER ".idf_escape($Yh['Trigger'])." $Yh[Timing] $Yh[Event] ON ".idf_escape($N["nspname"]).".".idf_escape($N['Name'])." $Yh[Type] $Yh[Statement];;\n";}return$F;}function
use_sql($Bb,$uh=""){$y=idf_escape($Bb);$F="";if(preg_match('~CREATE~',$uh)){if($uh=="DROP+CREATE")$F="DROP DATABASE IF EXISTS $y;\n";$F
.="CREATE DATABASE $y;\n";}return"$F\\connect $y";}function
show_variables(){return
get_rows("SHOW ALL");}function
process_list(){return
get_rows("SELECT * FROM pg_stat_activity ORDER BY ".(min_version(9.2)?"pid":"procpid"));}function
convert_field(array$k){}function
unconvert_field(array$k,$F){return($k["composite"]?"$F::$k[type]":$F);}function
support($zc){return
preg_match('~^(check|columns|comment|database|drop_col|dump|descidx|fast_status|indexes|kill|partial_indexes|routine|scheme|sequence|sql|table'.'|transaction_ddl|trigger|type|variables|view'.(min_version(9.3)?'|materializedview':'').(min_version(11)?'|procedure':'').(connection()->flavor=='cockroach'?'':'|deferrable').(connection()->flavor=='cockroach'?'':'|processlist').')$~',$zc);}function
kill_process($p){return
queries("SELECT pg_terminate_backend(".number($p).")");}function
connection_id(){return"SELECT pg_backend_pid()";}function
max_connections(){return
get_val("SHOW max_connections");}}add_driver("sqlite","SQLite");if(isset($_GET["sqlite"])){define('Adminer\DRIVER',"sqlite");if(class_exists("SQLite3")&&$_GET["ext"]!="pdo"){abstract
class
SqliteDb
extends
SqlDb{var$extension="SQLite3";private$link;function
attach($m,$U,$B){$this->link=new
\SQLite3($m);$Bi=\SQLite3::version();$this->server_info=$Bi["versionString"];return'';}function
query($D,$fi=false){$E=@$this->link->query($D);$this->error="";if(!$E){$this->errno=$this->link->lastErrorCode();$this->error=$this->link->lastErrorMsg();return
false;}elseif($E->numColumns())return
new
Result($E);$this->affected_rows=$this->link->changes();return
true;}function
quote($O){return(is_utf8($O)?"'".$this->link->escapeString($O)."'":"x'".bin2hex($O)."'");}}class
Result{var$num_rows;private$result,$offset=0;function
__construct($E){$this->result=$E;}function
fetch_assoc(){return$this->result->fetchArray(SQLITE3_ASSOC);}function
fetch_row(){return$this->result->fetchArray(SQLITE3_NUM);}function
fetch_field(){$ei=array(1=>"integer","real","text","blob","null");$d=$this->offset++;$S=$this->result->columnType($d);return(object)array("name"=>$this->result->columnName($d),"type"=>($S==SQLITE3_TEXT?15:0),"native_type"=>$ei[$S],"charsetnr"=>($S==SQLITE3_BLOB?63:0),);}}}elseif(extension_loaded("pdo_sqlite")){abstract
class
SqliteDb
extends
PdoDb{var$extension="PDO_SQLite";function
attach($m,$U,$B){return$this->dsn(DRIVER.":$m","","");}function
quote($O){return(is_utf8($O)?parent::quote($O):"x'".bin2hex($O)."'");}}}if(class_exists('Adminer\SqliteDb')){class
Db
extends
SqliteDb{function
attach($m,$U,$B){parent::attach($m,$U,$B);$this->query("PRAGMA foreign_keys = 1");$this->query("PRAGMA busy_timeout = 500");return'';}function
select_db($m){$D="ATTACH ".$this->quote(preg_match("~(^[/\\\\]|:)~",$m)?$m:dirname($_SERVER["SCRIPT_FILENAME"])."/$m")." AS a";if(is_readable($m)&&$this->query($D))return!self::attach($m,'','');return
false;}}}class
Driver
extends
SqlDriver{static$extensions=array("SQLite3","PDO_SQLite");static$jush="sqlite";static$passwords=false;protected$types=array(array("integer"=>0,"real"=>0,"numeric"=>0,"text"=>0,"blob"=>0));var$insertFunctions=array();var$editFunctions=array("integer|real|numeric"=>"+/-","text"=>"||",);var$operators=array("=","<",">","<=",">=","!=","LIKE","LIKE %%","IN","IS NULL","NOT LIKE","NOT IN","IS NOT NULL","SQL");var$functions=array("hex","length","lower","round","unixepoch","upper");var$grouping=array("avg","count","count distinct","group_concat","max","min","sum");static
function
connect($K,$U,$B){return
parent::connect(":memory:","","");}function
__construct(Db$f){parent::__construct($f);if(min_version(3.31,0,$f))$this->generated=array("STORED","VIRTUAL");if(min_version(3.37,0,$f))$this->types[0]["any"]=0;}function
structuredTypes(){return
array_keys($this->types[0]);}function
quoteBinary($Kg){return"x".q(bin2hex($Kg));}function
engines(){$F=array("table");if(min_version("3.8.2")){if(min_version(3.37)){$F[]="STRICT";$F[]="STRICT, WITHOUT ROWID";}$F[]="WITHOUT ROWID";}return$F;}function
insertUpdate($P,array$H,array$C){$Y=array();foreach($H
as$L)$Y[]="(".implode(", ",$L).")";return
queries("REPLACE INTO ".table($P)." (".implode(", ",array_keys(reset($H))).") VALUES\n".implode(",\n",$Y));}function
tableHelp($y,$ae=false){if(preg_match('~^sqlite_(seq|stat.)~',$y,$x))return"fileformat2.html#$x[1]tab";if(preg_match('~^sqlite(_temp)?_(master|schema)$~',$y))return"schematab.html";}function
checkConstraints($P){preg_match_all('~ CHECK *(\( *(((?>[^()]*[^() ])|(?1))*) *\))~',get_val("SELECT sql FROM sqlite_master WHERE type = 'table' AND name = ".q($P),0,$this->conn),$Ee);return
array_combine($Ee[2],$Ee[2]);}function
allFields(){$F=array();foreach(tables_list()as$P=>$S){foreach(fields($P)as$k)$F[$P][]=$k;}return$F;}}function
idf_escape($q){return'"'.str_replace('"','""',$q).'"';}function
table($q){return
idf_escape($q);}function
get_databases($Mc){return
array();}function
limit($D,$Z,$v,$qf=0,$J=" "){return" $D$Z".($v?$J."LIMIT $v".($qf?" OFFSET $qf":""):"");}function
limit1($P,$D,$Z,$J="\n"){return(preg_match('~^INTO~',$D)||get_val("SELECT sqlite_compileoption_used('ENABLE_UPDATE_DELETE_LIMIT')")?limit($D,$Z,1,0,$J):" $D WHERE rowid = (SELECT rowid FROM ".table($P).$Z.$J."LIMIT 1)");}function
db_collation($h,array$Za){return
get_val("PRAGMA encoding");}function
logged_user(){return
get_current_user();}function
tables_list(){return
get_key_vals("SELECT name, type FROM sqlite_master WHERE type IN ('table', 'view') ORDER BY (name LIKE 'sqlite_%'), name");}function
count_tables(array$Cb){return
array();}function
db_status(){$Kf=get_val("PRAGMA page_size");$Wc=get_val("PRAGMA freelist_count")*$Kf;return
array("Data_length"=>get_val("PRAGMA page_count")*$Kf-$Wc,"Index_length"=>0,"Data_free"=>$Wc,);}function
table_status($y="",$yc=false){$F=array();$H=array();if(!$yc&&$y==""){connection()->query("PRAGMA optimize = 0x10002");$H=get_key_vals("SELECT tbl, MAX(CAST(stat AS integer)) FROM sqlite_stat1 GROUP BY tbl");}foreach(get_rows("SELECT name AS Name, type AS Engine, sql, 'rowid' AS Oid, '' AS Auto_increment FROM sqlite_master WHERE type IN ('table', 'view') ".($y!=""?"AND name = ".q($y):"ORDER BY (name LIKE 'sqlite_%'), name"))as$G){if($G["Engine"]=="table"){$vh=preg_replace('~.*\)~s','',$G["sql"]);$G["Engine"]=implode(", ",array_filter(array((preg_match('~\bSTRICT\b~i',$vh)?"STRICT":0),(preg_match('~\bWITHOUT\s+ROWID\b~i',$vh)?"WITHOUT ROWID":0),)))?:"table";}unset($G["sql"]);$G["Rows"]=idx($H,$G["Name"],0);$F[$G["Name"]]=$G;}if(!$yc){foreach(get_rows("SELECT * FROM sqlite_sequence".($y!=""?" WHERE name = ".q($y):""),null,"")as$G)$F[$G["name"]]["Auto_increment"]=$G["seq"];}return$F;}function
is_view(array$Q){return$Q["Engine"]=="view";}function
fk_support(array$Q){return!get_val("SELECT sqlite_compileoption_used('OMIT_FOREIGN_KEY')");}function
fields($P){$F=array();$nh=get_val("SELECT sql FROM sqlite_master WHERE type = 'table' AND name = ".q($P));$og=array("select"=>1,"where"=>1,"order"=>1);if(!preg_match('~^sqlite(_temp)?_(master|schema)$~',$P))$og+=array("insert"=>1,"update"=>1);foreach(get_rows("PRAGMA table_".(min_version(3.31)?"x":"")."info(".table($P).")")as$G){$y=$G["name"];$S=strtolower($G["type"]);$i=$G["dflt_value"];$F[$y]=array("field"=>$y,"type"=>(preg_match('~int~i',$S)?"integer":(preg_match('~char|clob|text~i',$S)?"text":(preg_match('~blob~i',$S)?"blob":(preg_match('~real|floa|doub~i',$S)?"real":(preg_match('~any~i',$S)?"any":"numeric"))))),"full_type"=>$S,"default"=>(preg_match("~^'(.*)'$~",$i,$x)?str_replace("''","'",$x[1]):($i=="NULL"?null:$i)),"null"=>!$G["notnull"],"privileges"=>$og,"primary"=>$G["pk"],);if($G["pk"]&&preg_match('~\bAUTOINCREMENT\b~i',$nh))$F[$y]["auto_increment"]=true;}$q='(("[^"]*+")+|[a-z0-9_]+)';preg_match_all('~'.$q.'\s+text\s+COLLATE\s+(\'[^\']+\'|\S+)~i',$nh,$Ee,PREG_SET_ORDER);foreach($Ee
as$x){$y=str_replace('""','"',preg_replace('~^"|"$~','',$x[1]));if($F[$y])$F[$y]["collation"]=trim($x[3],"'");}preg_match_all('~'.$q.'\s.*GENERATED ALWAYS AS \((.+)\) (STORED|VIRTUAL)~i',$nh,$Ee,PREG_SET_ORDER);foreach($Ee
as$x){$y=str_replace('""','"',preg_replace('~^"|"$~','',$x[1]));$F[$y]["default"]=$x[3];$F[$y]["generated"]=strtoupper($x[4]);}return$F;}function
indexes($P,$g=null){$g=connection($g);$F=array();$nh=get_val("SELECT sql FROM sqlite_master WHERE type = 'table' AND name = ".q($P),0,$g);if(preg_match('~\bPRIMARY\s+KEY\s*\((([^)"]+|"[^"]*"|`[^`]*`)++)~i',$nh,$x)){$F[""]=array("type"=>"PRIMARY","columns"=>array(),"lengths"=>array(),"descs"=>array());preg_match_all('~((("[^"]*+")+|(?:`[^`]*+`)+)|(\S+))(\s+(ASC|DESC))?(,\s*|$)~i',$x[1],$Ee,PREG_SET_ORDER);foreach($Ee
as$x){$F[""]["columns"][]=idf_unescape($x[2]).$x[4];$F[""]["descs"][]=(preg_match('~DESC~i',$x[5])?'1':null);}}if(!$F){foreach(fields($P)as$y=>$k){if($k["primary"])$F[""]=array("type"=>"PRIMARY","columns"=>array($y),"lengths"=>array(),"descs"=>array(null));}}$ph=get_key_vals("SELECT name, sql FROM sqlite_master WHERE type = 'index' AND tbl_name = ".q($P),$g);foreach(get_rows("PRAGMA index_list(".table($P).")",$g)as$G){$y=$G["name"];$r=array("type"=>($G["unique"]?"UNIQUE":"INDEX"));$r["lengths"]=array();$r["descs"]=array();foreach(get_rows("PRAGMA index_info(".idf_escape($y).")",$g)as$Jg){$r["columns"][]=$Jg["name"];$r["descs"][]=null;}if(preg_match('~^CREATE( UNIQUE)? INDEX '.preg_quote(idf_escape($y).' ON '.idf_escape($P),'~').' \((.*)\)$~i',$ph[$y],$Ag)){preg_match_all('/("[^"]*+")+( DESC)?/',$Ag[2],$Ee);foreach($Ee[2]as$t=>$W){if($W)$r["descs"][$t]='1';}}if(!$F[""]||$r["type"]!="UNIQUE"||$r["columns"]!=$F[""]["columns"]||$r["descs"]!=$F[""]["descs"]||!preg_match("~^sqlite_~",$y))$F[$y]=$r;}return$F;}function
foreign_keys($P){$F=array();foreach(get_rows("PRAGMA foreign_key_list(".table($P).")")as$G){$Rc=&$F[$G["id"]];if(!$Rc)$Rc=$G;$Rc["source"][]=$G["from"];$Rc["target"][]=$G["to"];}return$F;}function
view($y){return
array("select"=>preg_replace('~^(?:[^`"[]+|`[^`]*`|"[^"]*")* AS\s+~iU','',get_val("SELECT sql FROM sqlite_master WHERE type = 'view' AND name = ".q($y))));}function
collations(){return(isset($_GET["create"])?get_vals("PRAGMA collation_list",1):array());}function
information_schema($h,$Lg=""){return
false;}function
error(){return
h(connection()->error);}function
check_sqlite_name($y){$vc="db|sdb|sqlite";if(!preg_match("~^[^\\0]*\\.($vc)\$~",$y)){connection()->error=lang(35,str_replace("|",", ",$vc));return
false;}return
true;}function
create_database($h,$Ya){if(file_exists($h)){connection()->error=lang(36);return
false;}if(!check_sqlite_name($h))return
false;try{$w=new
Db();$w->attach($h,'','');}catch(\Exception$pc){connection()->error=$pc->getMessage();return
false;}$w->query('PRAGMA encoding = "UTF-8"');$w->query('CREATE TABLE adminer (i)');$w->query('DROP TABLE adminer');return
true;}function
drop_databases(array$Cb){connection()->attach(":memory:",'','');foreach($Cb
as$h){if(!check_sqlite_name($h))return
false;if(!@unlink($h)){connection()->error=lang(36);return
false;}}return
true;}function
rename_database($y,$Ya){if(!check_sqlite_name($y))return
false;connection()->attach(":memory:",'','');connection()->error=lang(36);return@rename(DB,$y);}function
auto_increment(){return" PRIMARY KEY AUTOINCREMENT";}function
alter_table($P,$y,array$l,array$Oc,$cb,$gc,$Ya,$wa,$Rf){$ri=($P==""||$Oc||$gc);foreach($l
as$k){if($k[0]!=""||!$k[1]||$k[2]){$ri=true;break;}}$b=array();$Ff=array();foreach($l
as$k){if($k[1]){$b[]=($ri?$k[1]:"ADD ".implode($k[1]));if($k[0]!="")$Ff[$k[0]]=$k[1][0];}}if(!$ri){foreach($b
as$W){if(!queries("ALTER TABLE ".table($P)." $W"))return
false;}if($P!=$y&&!queries("ALTER TABLE ".table($P)." RENAME TO ".table($y)))return
false;}elseif(!recreate_table($P,$y,$b,$Ff,$Oc,$wa,array(),"","",$gc))return
false;if($wa){queries("BEGIN");queries("UPDATE sqlite_sequence SET seq = $wa WHERE name = ".q($y));if(!connection()->affected_rows)queries("INSERT INTO sqlite_sequence (name, seq) VALUES (".q($y).", $wa)");queries("COMMIT");}return
true;}function
recreate_table($P,$y,array$l,array$Ff,array$Oc,$wa="",$s=array(),$Wb="",$ga="",$gc=""){if($P!=""){if(!$l){foreach(fields($P)as$t=>$k){if($s)$k["auto_increment"]=0;$l[]=process_field($k,$k);$Ff[$t]=idf_escape($t);}}$lg=false;foreach($l
as$k){if($k[6])$lg=true;}$Xb=array();foreach($s
as$t=>$W){if($W[2]=="DROP"){$Xb[$W[1]]=true;unset($s[$t]);}}foreach(indexes($P)as$ee=>$r){$e=array();foreach($r["columns"]as$t=>$d){if(!$Ff[$d])continue
2;$e[]=$Ff[$d].($r["descs"][$t]?" DESC":"");}if(!$Xb[$ee]){if($r["type"]!="PRIMARY"||!$lg)$s[]=array($r["type"],$ee,$e);}}foreach($s
as$t=>$W){if($W[0]=="PRIMARY"){unset($s[$t]);$Oc[]="  PRIMARY KEY (".implode(", ",$W[2]).")";}}foreach(foreign_keys($P)as$ee=>$Rc){foreach($Rc["source"]as$t=>$d){if(!$Ff[$d])continue
2;$Rc["source"][$t]=idf_unescape($Ff[$d]);}if(!isset($Oc[" $ee"]))$Oc[]=" ".format_foreign_key($Rc);}queries("BEGIN");}$Ma=array();foreach($l
as$k){if(preg_match('~GENERATED~',$k[3]))unset($Ff[array_search($k[0],$Ff)]);$Ma[]="  ".implode($k);}$Ma=array_merge($Ma,array_filter($Oc));foreach(driver()->checkConstraints($P)as$Pa){if($Pa!=$Wb)$Ma[]="  CHECK ($Pa)";}if($ga)$Ma[]="  CHECK ($ga)";$Fh=($P!=""&&$P==$y?"adminer_$y":$y);if(!$gc&&$P!="")$gc=idx(table_status1($P),"Engine");if(!queries("CREATE TABLE ".table($Fh)." (\n".implode(",\n",$Ma)."\n)".($gc!="table"&&in_array($gc,driver()->engines())?" $gc":"")))return
false;if($P!=""){if($Ff&&!queries("INSERT INTO ".table($Fh)." (".implode(", ",$Ff).") SELECT ".implode(", ",array_map('Adminer\idf_escape',array_keys($Ff)))." FROM ".table($P)))return
false;$bi=array();foreach(triggers($P)as$Zh=>$Lh){$Yh=trigger($Zh,$P);$bi[]="CREATE TRIGGER ".idf_escape($Zh)." ".implode(" ",$Lh)." ON ".table($y)."\n$Yh[Statement]";}$wa=$wa?"":get_val("SELECT seq FROM sqlite_sequence WHERE name = ".q($P));if(!queries("DROP TABLE ".table($P))||($P==$y&&!queries("ALTER TABLE ".table($Fh)." RENAME TO ".table($y)))||!alter_indexes($y,$s))return
false;if($wa)queries("UPDATE sqlite_sequence SET seq = $wa WHERE name = ".q($y));foreach($bi
as$Yh){if(!queries($Yh))return
false;}queries("COMMIT");}return
true;}function
index_sql($P,$S,$y,$e){return"CREATE $S ".($S!="INDEX"?"INDEX ":"").idf_escape($y!=""?$y:uniqid($P."_"))." ON ".table($P)." $e";}function
alter_indexes($P,$b){foreach($b
as$C){if($C[0]=="PRIMARY")return
recreate_table($P,$P,array(),array(),array(),"",$b);}foreach(array_reverse($b)as$W){if(!queries($W[2]=="DROP"?"DROP INDEX ".idf_escape($W[1]):index_sql($P,$W[0],$W[1],"(".implode(", ",$W[2]).")")))return
false;}return
true;}function
truncate_tables(array$R){return
apply_queries("DELETE FROM",$R);}function
drop_views(array$Di){return
apply_queries("DROP VIEW",$Di);}function
drop_tables(array$R){return
apply_queries("DROP TABLE",$R);}function
move_tables(array$R,array$Di,$Eh){return
false;}function
trigger($y,$P){if($y=="")return
array("Statement"=>"BEGIN\n\t;\nEND");$q='(?:[^`"\s]+|`[^`]*`|"[^"]*")+';$ai=trigger_options();preg_match("~^CREATE\\s+TRIGGER\\s*$q\\s*(".implode("|",$ai["Timing"]).")\\s+([a-z]+)(?:\\s+OF\\s+($q))?\\s+ON\\s*$q\\s*(?:FOR\\s+EACH\\s+ROW\\s)?(.*)~is",get_val("SELECT sql FROM sqlite_master WHERE type = 'trigger' AND name = ".q($y)),$x);$pf=$x[3];return
array("Timing"=>strtoupper($x[1]),"Event"=>strtoupper($x[2]).($pf?" OF":""),"Of"=>idf_unescape($pf),"Trigger"=>$y,"Statement"=>$x[4],);}function
triggers($P){$F=array();$ai=trigger_options();foreach(get_rows("SELECT * FROM sqlite_master WHERE type = 'trigger' AND tbl_name = ".q($P))as$G){preg_match('~^CREATE\s+TRIGGER\s*(?:[^`"\s]+|`[^`]*`|"[^"]*")+\s*('.implode("|",$ai["Timing"]).')\s*(.*?)\s+ON\b~i',$G["sql"],$x);$F[$G["name"]]=array($x[1],$x[2]);}return$F;}function
trigger_options(){return
array("Timing"=>array("BEFORE","AFTER","INSTEAD OF"),"Event"=>array("INSERT","UPDATE","UPDATE OF","DELETE"),"Type"=>array("FOR EACH ROW"),);}function
last_id($E){return
get_val("SELECT LAST_INSERT_ROWID()");}function
explain(Db$f,$D){return$f->query("EXPLAIN QUERY PLAN $D");}function
found_rows(array$Q,array$Z){}function
types($vc=false){return
array();}function
create_sql($P,$wa,$uh){$F=get_val("SELECT sql FROM sqlite_master WHERE type IN ('table', 'view') AND name = ".q($P));foreach(indexes($P)as$y=>$r){if($y=='')continue;$F
.=";\n\n".index_sql($P,$r['type'],$y,"(".implode(", ",array_map('Adminer\idf_escape',$r['columns'])).")");}return$F;}function
truncate_sql($P){return"DELETE FROM ".table($P);}function
use_sql($Bb,$uh=""){return"";}function
trigger_sql($P){return
implode(get_vals("SELECT sql || ';;\n' FROM sqlite_master WHERE type = 'trigger' AND tbl_name = ".q($P)));}function
show_variables(){$F=array();foreach(get_rows("PRAGMA pragma_list")as$G){$y=$G["name"];if($y!="pragma_list"&&$y!="compile_options"){$F[$y]=array($y,'');foreach(get_rows("PRAGMA $y")as$G)$F[$y][1].=implode(", ",$G)."\n";}}return$F;}function
show_status(){$F=array();foreach(get_vals("PRAGMA compile_options")as$yf)$F[]=explode("=",$yf,2)+array('','');return$F;}function
convert_field(array$k){}function
unconvert_field(array$k,$F){return$F;}function
support($zc){return
preg_match('~^(check|columns|database|drop_col|dump|indexes|descidx|move_col|sql|status|table|transaction_ddl|trigger|variables|view|view_trigger)$~',$zc);}}add_driver("mssql","MS SQL");if(isset($_GET["mssql"])){define('Adminer\DRIVER',"mssql");if(extension_loaded("sqlsrv")&&$_GET["ext"]!="pdo"){class
Db
extends
SqlDb{var$extension="sqlsrv";private$link,$result,$warnings;private
function
get_error(){$this->error="";foreach(sqlsrv_errors()as$j){$this->errno=$j["code"];$this->error
.="$j[message]\n";}$this->error=rtrim($this->error);}function
attach($K,$U,$B){sqlsrv_configure("WarningsReturnAsErrors",0);$jb=array("UID"=>$U,"PWD"=>$B,"CharacterSet"=>"UTF-8");$M=adminer()->connectSsl();if(isset($M["Encrypt"]))$jb["Encrypt"]=$M["Encrypt"];if(isset($M["TrustServerCertificate"]))$jb["TrustServerCertificate"]=$M["TrustServerCertificate"];$h=adminer()->database();if($h!="")$jb["Database"]=$h;list($wd,$dg)=host_port($K);$this->link=@sqlsrv_connect($wd.($dg?",$dg":""),$jb);if($this->link){$Kd=sqlsrv_server_info($this->link);$this->server_info=$Kd['SQLServerVersion'];}else$this->get_error();return($this->link?'':$this->error);}function
quote($O){$gi=strlen($O)!=strlen(utf8_decode($O));return($gi?"N":"")."'".str_replace("'","''",$O)."'";}function
select_db($Bb){return$this->query(use_sql($Bb));}function
query($D,$fi=false){$E=sqlsrv_query($this->link,$D);$this->error="";if(!$E){$this->get_error();return
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
as$Gi)$F[]=$Gi["message"];return$F;}}class
Result{var$num_rows;private$result,$offset=0,$fields;function
__construct($E){$this->result=$E;}private
function
convert($G){foreach((array)$G
as$t=>$W){if(is_a($W,'DateTime'))$G[$t]=$W->format("Y-m-d H:i:s");}return$G;}function
fetch_assoc(){return$this->convert(sqlsrv_fetch_array($this->result,SQLSRV_FETCH_ASSOC));}function
fetch_row(){return$this->convert(sqlsrv_fetch_array($this->result,SQLSRV_FETCH_NUMERIC));}function
fetch_field(){if(!$this->fields)$this->fields=sqlsrv_field_metadata($this->result);$k=$this->fields[$this->offset++];$F=new
\stdClass;$F->name=$k["Name"];$F->type=($k["Type"]==1?254:15);$F->charsetnr=(in_array($k["Type"],array(-2,-3,-4))?63:0);return$F;}function
seek($qf){for($o=0;$o<$qf;$o++)sqlsrv_fetch($this->result);}}function
last_id($E){return(string)get_val("SELECT SCOPE_IDENTITY()");}function
explain(Db$f,$D){$f->query("SET SHOWPLAN_ALL ON");$F=$f->query($D);$f->query("SET SHOWPLAN_ALL OFF");return$F;}}else{abstract
class
MssqlDb
extends
PdoDb{function
select_db($Bb){return$this->query(use_sql($Bb));}function
lastInsertId(){return$this->pdo->lastInsertId();}function
warnings(){$E=$this->multi;if(!is_object($E))return
array();$j=$E->errorInfo();return
array((string)$j[2]);}}function
last_id($E){return
connection()->lastInsertId();}function
explain(Db$f,$D){}if(extension_loaded("pdo_sqlsrv")){class
Db
extends
MssqlDb{var$extension="PDO_SQLSRV";function
attach($K,$U,$B){list($wd,$dg)=host_port($K);$Yb="sqlsrv:Server=$wd".($dg?",$dg":"");$M=adminer()->connectSsl();foreach(array("Encrypt","TrustServerCertificate")as$t){if(isset($M[$t]))$Yb
.=";$t=".($M[$t]?1:0);}return$this->dsn($Yb,$U,$B,array(\PDO::SQLSRV_ATTR_DIRECT_QUERY=>true));}}}elseif(extension_loaded("pdo_dblib")){class
Db
extends
MssqlDb{var$extension="PDO_DBLIB";function
attach($K,$U,$B){list($wd,$dg)=host_port($K);return$this->dsn("dblib:charset=utf8;host=$wd".($dg?(is_numeric($dg)?";port=":";unix_socket=").$dg:""),$U,$B);}}}}class
Driver
extends
SqlDriver{static$extensions=array("SQLSRV","PDO_SQLSRV","PDO_DBLIB");static$jush="mssql";var$insertFunctions=array("date|time"=>"getdate");var$editFunctions=array("int|decimal|real|float|money|datetime"=>"+/-","char|text"=>"+",);var$operators=array("=","<",">","<=",">=","!=","LIKE","LIKE %%","IN","IS NULL","NOT LIKE","NOT IN","IS NOT NULL");var$functions=array("len","lower","round","upper");var$grouping=array("avg","count","count distinct","max","min","sum");var$generated=array("PERSISTED","VIRTUAL");var$onActions="NO ACTION|CASCADE|SET NULL|SET DEFAULT";static
function
connect($K,$U,$B){if($K=="")$K="localhost:1433";return
parent::connect($K,$U,$B);}function
__construct(Db$f){parent::__construct($f);$this->types=array(lang(28)=>array("tinyint"=>3,"smallint"=>5,"int"=>10,"bigint"=>20,"bit"=>1,"decimal"=>0,"real"=>12,"float"=>53,"smallmoney"=>10,"money"=>20),lang(29)=>array("date"=>10,"smalldatetime"=>19,"datetime"=>19,"datetime2"=>19,"time"=>8,"datetimeoffset"=>10),lang(30)=>array("char"=>8000,"varchar"=>8000,"text"=>2147483647,"nchar"=>4000,"nvarchar"=>4000,"ntext"=>1073741823),lang(31)=>array("binary"=>8000,"varbinary"=>8000,"image"=>2147483647),);}function
insertUpdate($P,array$H,array$C){$l=fields($P);$T=array();$Z=array();$L=reset($H);$e="c".implode(", c",range(1,count($L)));$Ka=0;$Od=array();foreach($L
as$t=>$W){$Ka++;$y=idf_unescape($t);if(!$l[$y]["auto_increment"])$Od[$t]="c$Ka";if(isset($C[$y]))$Z[]="$t = c$Ka";else$T[]="$t = c$Ka";}$Y=array();foreach($H
as$L)$Y[]="(".implode(", ",$L).")";if($Z){$Ad=queries("SET IDENTITY_INSERT ".table($P)." ON");$F=queries("MERGE ".table($P)." USING (VALUES\n\t".implode(",\n\t",$Y)."\n) AS source ($e) ON ".implode(" AND ",$Z).($T?"\nWHEN MATCHED THEN UPDATE SET ".implode(", ",$T):"")."\nWHEN NOT MATCHED THEN INSERT (".implode(", ",array_keys($Ad?$L:$Od)).") VALUES (".($Ad?$e:implode(", ",$Od)).");");if($Ad)queries("SET IDENTITY_INSERT ".table($P)." OFF");}else$F=queries("INSERT INTO ".table($P)." (".implode(", ",array_keys($L)).") VALUES\n".implode(",\n",$Y));return$F;}function
begin(){return
queries("BEGIN TRANSACTION");}function
convertSearch($q,array$W,array$k){return(preg_match('~^(bit|n?text|xml|uniqueidentifier|sql_variant|hierarchyid|geography|geometry)$~',$k["type"])?"CAST($q AS nvarchar(max))":$q);}function
quoteBinary($Kg){return"0x".bin2hex($Kg);}function
warnings(){$F=array();foreach($this->conn->warnings()as$Re){$Re=trim(preg_replace('~^(\[[^]]+])+~','',$Re));if($Re!="")$F[]=$Re;}return
nl_br(h(implode("\n",$F)));}function
tableHelp($y,$ae=false){$ve=array("sys"=>"catalog-views/sys-","INFORMATION_SCHEMA"=>"information-schema-views/",);$w=$ve[get_schema()];if($w)return"relational-databases/system-$w".preg_replace('~_~','-',strtolower($y))."-transact-sql";}}function
idf_escape($q){return"[".str_replace("]","]]",$q)."]";}function
table($q){return($_GET["ns"]!=""?idf_escape($_GET["ns"]).".":"").idf_escape($q);}function
get_databases($Mc){return
get_vals("SELECT name FROM sys.databases WHERE name NOT IN ('master', 'tempdb', 'model', 'msdb')");}function
limit($D,$Z,$v,$qf=0,$J=" "){return($v?" TOP (".($v+$qf).")":"")." $D$Z";}function
limit1($P,$D,$Z,$J="\n"){return
limit($D,$Z,1,0,$J);}function
db_collation($h,array$Za){return
get_val("SELECT collation_name FROM sys.databases WHERE name = ".q($h));}function
logged_user(){return
get_val("SELECT SUSER_NAME()");}function
tables_list(){return
get_key_vals("SELECT name, type_desc FROM sys.all_objects WHERE schema_id = SCHEMA_ID(".q(get_schema()).") AND type IN ('S', 'U', 'V') ORDER BY name");}function
count_tables(array$Cb){$F=array();foreach($Cb
as$h){connection()->select_db($h);$F[$h]=get_val("SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES");}return$F;}function
table_status($y="",$yc=false){$F=array();$gh=array();foreach(get_rows("SELECT object_id, SUM(CASE WHEN index_id < 2 THEN row_count ELSE 0 END) AS [Rows],
SUM(CASE WHEN index_id < 2 THEN used_page_count ELSE 0 END) * 8192 AS Data_length,
SUM(CASE WHEN index_id > 1 THEN used_page_count ELSE 0 END) * 8192 AS Index_length,
SUM(reserved_page_count - used_page_count) * 8192 AS Data_free
FROM sys.dm_db_partition_stats
GROUP BY object_id",null,"")as$G){$of=$G["object_id"];unset($G["object_id"]);$gh[$of]=$G;}foreach(get_rows("SELECT ao.object_id, ao.name AS Name, ao.type_desc AS Engine,
	(SELECT cast(value as varchar(max)) FROM fn_listextendedproperty(default, 'SCHEMA', schema_name(schema_id), 'TABLE', ao.name, null, null)) AS Comment
FROM sys.all_objects AS ao
WHERE schema_id = SCHEMA_ID(".q(get_schema()).") AND type IN ('S', 'U', 'V') ".($y!=""?"AND name = ".q($y):"ORDER BY name"))as$G){$of=$G["object_id"];unset($G["object_id"]);$F[$G["Name"]]=$G+idx($gh,$of,array());}return$F;}function
is_view(array$Q){return$Q["Engine"]=="VIEW";}function
fk_support(array$Q){return
true;}function
fields($P){$db=get_key_vals("SELECT objname, cast(value as varchar(max)) FROM fn_listextendedproperty('MS_DESCRIPTION', 'schema', ".q(get_schema()).", 'table', ".q($P).", 'column', NULL)");$F=array();$_h=get_val("SELECT object_id FROM sys.all_objects WHERE schema_id = SCHEMA_ID(".q(get_schema()).") AND type IN ('S', 'U', 'V') AND name = ".q($P));foreach(get_rows("SELECT c.max_length, c.precision, c.scale, c.name, c.is_nullable, c.is_identity, c.collation_name,
	t.name type, d.definition [default], d.name default_constraint, i.is_primary_key
FROM sys.all_columns c
JOIN sys.types t ON c.user_type_id = t.user_type_id
LEFT JOIN sys.default_constraints d ON c.default_object_id = d.object_id
LEFT JOIN sys.index_columns ic ON c.object_id = ic.object_id AND c.column_id = ic.column_id
LEFT JOIN sys.indexes i ON ic.object_id = i.object_id AND ic.index_id = i.index_id
WHERE c.object_id = ".q($_h))as$G){$S=$G["type"];$u=(preg_match("~char|binary~",$S)?intval($G["max_length"])/($S[0]=='n'?2:1):($S=="decimal"?"$G[precision],$G[scale]":""));$F[$G["name"]]=array("field"=>$G["name"],"full_type"=>$S.($u?"($u)":""),"type"=>$S,"length"=>$u,"default"=>(preg_match("~^\('(.*)'\)$~",$G["default"],$x)?str_replace("''","'",$x[1]):$G["default"]),"default_constraint"=>$G["default_constraint"],"null"=>$G["is_nullable"],"auto_increment"=>$G["is_identity"],"collation"=>$G["collation_name"],"privileges"=>array("insert"=>1,"select"=>1,"update"=>1,"where"=>1,"order"=>1),"primary"=>$G["is_primary_key"],"comment"=>$db[$G["name"]],);}foreach(get_rows("SELECT * FROM sys.computed_columns WHERE object_id = ".q($_h))as$G){$F[$G["name"]]["generated"]=($G["is_persisted"]?"PERSISTED":"VIRTUAL");$F[$G["name"]]["default"]=$G["definition"];}return$F;}function
indexes($P,$g=null){$F=array();foreach(get_rows("SELECT i.name, key_ordinal, is_unique, is_primary_key, c.name AS column_name, is_descending_key
FROM sys.indexes i
INNER JOIN sys.index_columns ic ON i.object_id = ic.object_id AND i.index_id = ic.index_id
INNER JOIN sys.columns c ON ic.object_id = c.object_id AND ic.column_id = c.column_id
WHERE OBJECT_NAME(i.object_id) = ".q($P),$g)as$G){$y=$G["name"];$F[$y]["type"]=($G["is_primary_key"]?"PRIMARY":($G["is_unique"]?"UNIQUE":"INDEX"));$F[$y]["lengths"]=array();$F[$y]["columns"][$G["key_ordinal"]]=$G["column_name"];$F[$y]["descs"][$G["key_ordinal"]]=($G["is_descending_key"]?'1':null);}return$F;}function
view($y){return
array("select"=>preg_replace('~^(?:[^[]|\[[^]]*])*\s+AS\s+~isU','',get_val("SELECT VIEW_DEFINITION FROM INFORMATION_SCHEMA.VIEWS WHERE TABLE_SCHEMA = SCHEMA_NAME() AND TABLE_NAME = ".q($y))));}function
collations(){$F=array();foreach(get_vals("SELECT name FROM fn_helpcollations()")as$Ya)$F[preg_replace('~_.*~','',$Ya)][]=$Ya;return$F;}function
information_schema($h,$Lg=""){return
in_array($Lg!=""?$Lg:get_schema(),array("INFORMATION_SCHEMA","sys"));}function
error(){return
nl_br(h(preg_replace('~^(\[[^]]*])+~m','',connection()->error)));}function
create_database($h,$Ya){return
queries("CREATE DATABASE ".idf_escape($h).(preg_match('~^[a-z0-9_]+$~i',$Ya)?" COLLATE $Ya":""));}function
drop_databases(array$Cb){return!!queries("DROP DATABASE ".implode(", ",array_map('Adminer\idf_escape',$Cb)));}function
rename_database($y,$Ya){if(preg_match('~^[a-z0-9_]+$~i',$Ya))queries("ALTER DATABASE ".idf_escape(DB)." COLLATE $Ya");queries("ALTER DATABASE ".idf_escape(DB)." MODIFY NAME = ".idf_escape($y));return
true;}function
auto_increment(){return" IDENTITY".($_POST["Auto_increment"]!=""?"(".number($_POST["Auto_increment"]).",1)":"")." PRIMARY KEY";}function
alter_table($P,$y,array$l,array$Oc,$cb,$gc,$Ya,$wa,$Rf){$b=array();$db=array();$Df=fields($P);foreach($l
as$k){$d=idf_escape($k[0]);$W=$k[1];if(!$W)$b["DROP"][]=" COLUMN $d";else{$W[1]=preg_replace("~( COLLATE )'(\\w+)'~",'\1\2',$W[1]);$db[$k[0]]=$W[5];unset($W[5]);if(preg_match('~ AS ~',$W[3]))unset($W[1],$W[2]);if($k[0]=="")$b["ADD"][]="\n  ".implode("",$W).($P==""?substr($Oc[$W[0]],16+strlen($W[0])):"");else{$i=$W[3];unset($W[3]);unset($W[6]);if($d!=$W[0])queries("EXEC sp_rename ".q(table($P).".$d").", ".q(idf_unescape($W[0])).", 'COLUMN'");$b["ALTER COLUMN ".implode("",$W)][]="";$Cf=$Df[$k[0]];if(default_value($Cf)!=$i){if($Cf["default"]!==null)$b["DROP"][]=" ".idf_escape($Cf["default_constraint"]);if($i)$b["ADD"][]="\n $i FOR $d";}}}}if($P==""){$fa=(array)$b["ADD"];foreach($Oc
as$t=>$W){if(!is_string($t))$fa[]="\n$W";}return
queries("CREATE TABLE ".table($y)." (".implode(",",$fa)."\n)");}if($P!=$y)queries("EXEC sp_rename ".q(table($P)).", ".q($y));if($Oc)$b[""]=$Oc;foreach($b
as$t=>$W){if(!queries("ALTER TABLE ".table($y)." $t".implode(",",$W)))return
false;}foreach($db
as$t=>$W){$cb=substr($W,9);queries("EXEC sp_dropextendedproperty @name = N'MS_Description', @level0type = N'Schema', @level0name = ".q(get_schema()).", @level1type = N'Table', @level1name = ".q($y).", @level2type = N'Column', @level2name = ".q($t));queries("EXEC sp_addextendedproperty
@name = N'MS_Description',
@value = $cb,
@level0type = N'Schema',
@level0name = ".q(get_schema()).",
@level1type = N'Table',
@level1name = ".q($y).",
@level2type = N'Column',
@level2name = ".q($t));}return
true;}function
alter_indexes($P,$b){$r=array();$Vb=array();foreach($b
as$W){if($W[2]=="DROP"){if($W[0]=="PRIMARY")$Vb[]=idf_escape($W[1]);else$r[]=idf_escape($W[1])." ON ".table($P);}elseif(!queries(($W[0]!="PRIMARY"?"CREATE $W[0] ".($W[0]!="INDEX"?"INDEX ":"").idf_escape($W[1]!=""?$W[1]:uniqid($P."_"))." ON ".table($P):"ALTER TABLE ".table($P)." ADD PRIMARY KEY")." (".implode(", ",$W[2]).")"))return
false;}return(!$r||queries("DROP INDEX ".implode(", ",$r)))&&(!$Vb||queries("ALTER TABLE ".table($P)." DROP ".implode(", ",$Vb)));}function
found_rows(array$Q,array$Z){}function
foreign_keys($P){$F=array();$uf=array("CASCADE","NO ACTION","SET NULL","SET DEFAULT");$Lg=get_schema();foreach(get_rows("EXEC sp_fkeys @fktable_name = ".q($P).", @fktable_owner = ".q($Lg))as$G){$Rc=&$F[$G["FK_NAME"]];$Rc["db"]=($G["PKTABLE_QUALIFIER"]==DB?"":$G["PKTABLE_QUALIFIER"]);$Rc["ns"]=($G["PKTABLE_OWNER"]==$Lg?"":$G["PKTABLE_OWNER"]);$Rc["table"]=$G["PKTABLE_NAME"];$Rc["on_update"]=$uf[$G["UPDATE_RULE"]];$Rc["on_delete"]=$uf[$G["DELETE_RULE"]];$Rc["source"][]=$G["FKCOLUMN_NAME"];$Rc["target"][]=$G["PKCOLUMN_NAME"];}return$F;}function
truncate_tables(array$R){return
apply_queries("TRUNCATE TABLE",$R);}function
drop_views(array$Di){return
queries("DROP VIEW ".implode(", ",array_map('Adminer\table',$Di)));}function
drop_tables(array$R){return
queries("DROP TABLE ".implode(", ",array_map('Adminer\table',$R)));}function
move_tables(array$R,array$Di,$Eh){return
apply_queries("ALTER SCHEMA ".idf_escape($Eh)." TRANSFER",array_merge($R,$Di));}function
trigger($y,$P){if($y=="")return
array();$H=get_rows("SELECT s.name [Trigger],
CASE WHEN OBJECTPROPERTY(s.id, 'ExecIsInsertTrigger') = 1 THEN 'INSERT'
	WHEN OBJECTPROPERTY(s.id, 'ExecIsUpdateTrigger') = 1 THEN 'UPDATE'
	WHEN OBJECTPROPERTY(s.id, 'ExecIsDeleteTrigger') = 1 THEN 'DELETE' END [Event],
CASE WHEN OBJECTPROPERTY(s.id, 'ExecIsInsteadOfTrigger') = 1 THEN 'INSTEAD OF' ELSE 'AFTER' END [Timing],
c.text
FROM sysobjects s
JOIN syscomments c ON s.id = c.id
WHERE s.xtype = 'TR' AND s.name = ".q($y));$F=reset($H);if($F)$F["Statement"]=preg_replace('~^.+\s+AS\s+~isU','',$F["text"]);return$F;}function
triggers($P){$F=array();foreach(get_rows("SELECT sys1.name,
CASE WHEN OBJECTPROPERTY(sys1.id, 'ExecIsInsertTrigger') = 1 THEN 'INSERT'
	WHEN OBJECTPROPERTY(sys1.id, 'ExecIsUpdateTrigger') = 1 THEN 'UPDATE'
	WHEN OBJECTPROPERTY(sys1.id, 'ExecIsDeleteTrigger') = 1 THEN 'DELETE' END [Event],
CASE WHEN OBJECTPROPERTY(sys1.id, 'ExecIsInsteadOfTrigger') = 1 THEN 'INSTEAD OF' ELSE 'AFTER' END [Timing]
FROM sysobjects sys1
JOIN sysobjects sys2 ON sys1.parent_obj = sys2.id
WHERE sys1.xtype = 'TR' AND sys2.name = ".q($P))as$G)$F[$G["name"]]=array($G["Timing"],$G["Event"]);return$F;}function
trigger_options(){return
array("Timing"=>array("AFTER","INSTEAD OF"),"Event"=>array("INSERT","UPDATE","DELETE"),"Type"=>array("AS"),);}function
schemas(){return
get_vals("SELECT name FROM sys.schemas");}function
get_schema(){if($_GET["ns"]!="")return$_GET["ns"];return
get_val("SELECT SCHEMA_NAME()");}function
set_schema($Lg,$g=null){$_GET["ns"]=$Lg;return
true;}function
create_sql($P,$wa,$uh){if(is_view(table_status1($P))){$Ci=view($P);return"CREATE VIEW ".table($P)." AS $Ci[select]";}$l=array();$C=false;foreach(fields($P)as$y=>$k){$W=process_field($k,$k);if($W[6])$C=true;$l[]=implode("",$W);}foreach(indexes($P)as$y=>$r){if(!$C||$r["type"]!="PRIMARY"){$e=array();foreach($r["columns"]as$t=>$W)$e[]=idf_escape($W).($r["descs"][$t]?" DESC":"");$y=idf_escape($y);$l[]=($r["type"]=="INDEX"?"INDEX $y":"CONSTRAINT $y ".($r["type"]=="UNIQUE"?"UNIQUE":"PRIMARY KEY"))." (".implode(", ",$e).")";}}foreach(driver()->checkConstraints($P)as$y=>$Pa)$l[]="CONSTRAINT ".idf_escape($y)." CHECK ($Pa)";return"CREATE TABLE ".table($P)." (\n\t".implode(",\n\t",$l)."\n)";}function
foreign_keys_sql($P){$l=array();foreach(foreign_keys($P)as$Oc)$l[]=ltrim(format_foreign_key($Oc));return($l?"ALTER TABLE ".table($P)." ADD\n\t".implode(",\n\t",$l).";\n\n":"");}function
truncate_sql($P){return"TRUNCATE TABLE ".table($P);}function
use_sql($Bb,$uh=""){return"USE ".idf_escape($Bb);}function
trigger_sql($P){$F="";foreach(triggers($P)as$y=>$Yh)$F
.=create_trigger(" ON ".table($P),trigger($y,$P)).";";return$F;}function
convert_field(array$k){}function
unconvert_field(array$k,$F){return$F;}function
support($zc){return
preg_match('~^(check|comment|columns|database|drop_col|dump|fast_status|indexes|descidx|scheme|sql|table|transaction_ddl|trigger|view|view_trigger)$~',$zc);}}add_driver("oracle","Oracle beta");if(isset($_GET["oracle"])){define('Adminer\DRIVER',"oracle");if(extension_loaded("oci8")&&$_GET["ext"]!="pdo"){class
Db
extends
SqlDb{var$extension="oci8";var$_current_db;private$link;function
_error($jc,$j){if(ini_bool("html_errors"))$j=html_entity_decode(strip_tags($j));$j=preg_replace('~^[^:]*: ~','',$j);$this->error=$j;}function
attach($K,$U,$B){$this->link=@oci_new_connect($U,$B,$K,"AL32UTF8");if($this->link){$this->server_info=oci_server_version($this->link);return'';}$j=oci_error();return($j?$j["message"]:lang(25));}function
quote($O){return"'".str_replace("'","''",$O)."'";}function
select_db($Bb){$this->_current_db=$Bb;return
true;}function
query($D,$fi=false){$E=oci_parse($this->link,$D);$this->error="";if(!$E){$j=oci_error($this->link);$this->errno=$j["code"];$this->error=$j["message"];return
false;}set_error_handler(array($this,'_error'));$F=@oci_execute($E);restore_error_handler();if($F){if(oci_num_fields($E))return
new
Result($E);$this->affected_rows=oci_num_rows($E);oci_free_statement($E);}return$F;}function
timeout($af){return(function_exists('oci_set_call_timeout')?oci_set_call_timeout($this->link,$af):false);}}class
Result{var$num_rows;private$result,$offset=1;function
__construct($E){$this->result=$E;}private
function
convert($G){foreach((array)$G
as$t=>$W){if(is_a($W,'OCILob')||is_a($W,'OCI-Lob'))$G[$t]=$W->load();}return$G;}function
fetch_assoc(){return$this->convert(oci_fetch_assoc($this->result));}function
fetch_row(){return$this->convert(oci_fetch_row($this->result));}function
fetch_field(){$d=$this->offset++;$F=new
\stdClass;$F->name=oci_field_name($this->result,$d);$S=oci_field_type($this->result,$d);$F->native_type=$S;$F->type=$S;$F->charsetnr=(preg_match("~raw|blob|bfile~",$S)?63:0);return$F;}}}elseif(extension_loaded("pdo_oci")){class
Db
extends
PdoDb{var$extension="PDO_OCI";var$_current_db;function
attach($K,$U,$B){return$this->dsn("oci:dbname=//$K;charset=AL32UTF8",$U,$B);}function
select_db($Bb){$this->_current_db=$Bb;return
true;}}}class
Driver
extends
SqlDriver{static$extensions=array("OCI8","PDO_OCI");static$jush="oracle";var$insertFunctions=array("date"=>"current_date","timestamp"=>"current_timestamp",);var$editFunctions=array("number|float|double"=>"+/-","date|timestamp"=>"+ interval/- interval","char|clob"=>"||",);var$operators=array("=","<",">","<=",">=","!=","LIKE","LIKE %%","IN","IS NULL","NOT LIKE","NOT IN","IS NOT NULL","SQL");var$functions=array("length","lower","round","upper");var$grouping=array("avg","count","count distinct","max","min","sum");function
__construct(Db$f){parent::__construct($f);$this->types=array(lang(28)=>array("number"=>38,"binary_float"=>12,"binary_double"=>21),lang(29)=>array("date"=>10,"timestamp"=>29,"interval year"=>12,"interval day"=>28),lang(30)=>array("char"=>2000,"varchar2"=>4000,"nchar"=>2000,"nvarchar2"=>4000,"clob"=>4294967295,"nclob"=>4294967295),lang(31)=>array("raw"=>2000,"long raw"=>2147483648,"blob"=>4294967295,"bfile"=>4294967296),);}function
begin(){return
true;}function
convertSearch($q,array$W,array$k){$S=$k["type"];$Xf=strpos($W["op"],"LIKE")!==false;if($S=="xmltype")return"XMLSERIALIZE(CONTENT $q AS VARCHAR2(4000))";if($S=="json")return"JSON_SERIALIZE($q)";if(preg_match('~^(date|timestamp)~',$S))return"TO_CHAR($q, 'YYYY-MM-DD HH24:MI:SS')";if(preg_match('~char~',$S)||(preg_match('~clob~',$S)&&$Xf))return$q;return(!$Xf&&preg_match(number_type(),$S)?$q:"TO_CHAR($q)");}function
quoteBinary($Kg){return"HEXTORAW(".q(bin2hex($Kg)).")";}function
hasCStyleEscapes(){return
true;}}function
idf_escape($q){return'"'.str_replace('"','""',$q).'"';}function
table($q){return
idf_escape($q);}function
get_databases($Mc){return
get_vals("SELECT DISTINCT tablespace_name FROM (
SELECT tablespace_name FROM user_tablespaces
UNION SELECT tablespace_name FROM all_tables WHERE tablespace_name IS NOT NULL
)
ORDER BY 1");}function
limit($D,$Z,$v,$qf=0,$J=" "){return($qf?" * FROM (SELECT t.*, rownum AS rnum FROM (SELECT $D$Z) t WHERE rownum <= ".($v+$qf).") WHERE rnum > $qf":($v?" * FROM (SELECT $D$Z) WHERE rownum <= ".($v+$qf):" $D$Z"));}function
limit1($P,$D,$Z,$J="\n"){return" $D$Z";}function
db_collation($h,array$Za){return
get_val("SELECT value FROM nls_database_parameters WHERE parameter = 'NLS_CHARACTERSET'");}function
logged_user(){return
get_val("SELECT USER FROM DUAL");}function
get_current_db(){$h=connection()->_current_db?:DB;connection()->_current_db=null;return$h;}function
where_owner($jg,$If="owner"){if(!$_GET["ns"])return'';return"$jg$If = sys_context('USERENV', 'CURRENT_SCHEMA')";}function
views_table($e){$If=where_owner('');return"(SELECT $e FROM all_views WHERE ".($If?:"rownum < 0").")";}function
tables_list(){$Ci=views_table("view_name");$If=where_owner(" AND ");return
get_key_vals("SELECT table_name, 'table' FROM all_tables WHERE tablespace_name = ".q(DB)."$If
UNION SELECT view_name, 'view' FROM $Ci
ORDER BY 1");}function
count_tables(array$Cb){$F=array();foreach($Cb
as$h)$F[$h]=get_val("SELECT COUNT(*) FROM all_tables WHERE tablespace_name = ".q($h));return$F;}function
table_status($y="",$yc=false){$F=array();$Mg=q($y);$h=get_current_db();$Ci=views_table("view_name");$If=where_owner(" AND ","t.owner");foreach(get_rows('SELECT t.table_name "Name", \'table\' "Engine", s.bytes "Data_length", i.bytes "Index_length", t.num_rows "Rows"
FROM all_tables t
LEFT JOIN (SELECT segment_name, SUM(bytes) bytes FROM user_segments WHERE segment_type LIKE \'TABLE%\' GROUP BY segment_name) s ON s.segment_name = t.table_name
LEFT JOIN (SELECT i.table_name, SUM(s.bytes) bytes FROM user_indexes i
	JOIN user_segments s ON s.segment_name = i.index_name AND s.segment_type LIKE \'INDEX%\' GROUP BY i.table_name) i ON i.table_name = t.table_name
WHERE t.tablespace_name = '.q($h).$If.($y!=""?" AND t.table_name = $Mg":"")."
UNION SELECT view_name, 'view', 0, 0, 0 FROM $Ci".($y!=""?" WHERE view_name = $Mg":"")."
ORDER BY 1")as$G)$F[$G["Name"]]=$G;return$F;}function
is_view(array$Q){return$Q["Engine"]=="view";}function
fk_support(array$Q){return
true;}function
fields($P){$F=array();$If=where_owner(" AND ");foreach(get_rows("SELECT * FROM all_tab_columns WHERE table_name = ".q($P)."$If ORDER BY column_id")as$G){$S=$G["DATA_TYPE"];$u="$G[DATA_PRECISION],$G[DATA_SCALE]";if($u==",")$u=$G["CHAR_COL_DECL_LENGTH"];$og=array("insert"=>1,"select"=>1,"update"=>1,"order"=>1);if($G["DATA_TYPE_OWNER"]==""||$S=="XMLTYPE")$og["where"]=1;$F[$G["COLUMN_NAME"]]=array("field"=>$G["COLUMN_NAME"],"full_type"=>$S.($u?"($u)":""),"type"=>strtolower($S),"length"=>$u,"default"=>$G["DATA_DEFAULT"],"null"=>($G["NULLABLE"]=="Y"),"privileges"=>$og,);}return$F;}function
indexes($P,$g=null){$F=array();$If=where_owner(" AND ","aic.table_owner");foreach(get_rows("SELECT aic.*, ac.constraint_type, atc.data_default
FROM all_ind_columns aic
LEFT JOIN all_constraints ac ON aic.index_name = ac.constraint_name AND aic.table_name = ac.table_name AND aic.index_owner = ac.owner
LEFT JOIN all_tab_cols atc ON aic.column_name = atc.column_name AND aic.table_name = atc.table_name AND aic.index_owner = atc.owner
WHERE aic.table_name = ".q($P)."$If
ORDER BY ac.constraint_type, aic.column_position",$g)as$G){$Fd=$G["INDEX_NAME"];$bb=$G["DATA_DEFAULT"];$bb=($bb?trim($bb,'"'):$G["COLUMN_NAME"]);$F[$Fd]["type"]=($G["CONSTRAINT_TYPE"]=="P"?"PRIMARY":($G["CONSTRAINT_TYPE"]=="U"?"UNIQUE":"INDEX"));$F[$Fd]["columns"][]=$bb;$F[$Fd]["lengths"][]=($G["CHAR_LENGTH"]&&$G["CHAR_LENGTH"]!=$G["COLUMN_LENGTH"]?$G["CHAR_LENGTH"]:null);$F[$Fd]["descs"][]=($G["DESCEND"]&&$G["DESCEND"]=="DESC"?'1':null);}return$F;}function
view($y){$Ci=views_table("view_name, text");$H=get_rows('SELECT text "select" FROM '.$Ci.' WHERE view_name = '.q($y));return
reset($H);}function
collations(){return
array();}function
information_schema($h,$Lg=""){return($Lg!=""?$Lg:get_schema())=="INFORMATION_SCHEMA";}function
error(){return
h(connection()->error);}function
explain(Db$f,$D){$f->query("EXPLAIN PLAN FOR $D");return$f->query("SELECT * FROM plan_table");}function
found_rows(array$Q,array$Z){}function
auto_increment(){return"";}function
alter_table($P,$y,array$l,array$Oc,$cb,$gc,$Ya,$wa,$Rf){$b=$Vb=array();$Df=($P?fields($P):array());foreach($l
as$k){$W=$k[1];if($W&&$k[0]!=""&&idf_escape($k[0])!=$W[0])queries("ALTER TABLE ".table($P)." RENAME COLUMN ".idf_escape($k[0])." TO $W[0]");$Cf=$Df[$k[0]];if($W&&$Cf){$sf=process_field($Cf,$Cf);if($W[2]==$sf[2])$W[2]="";}if($W)$b[]=($P!=""?($k[0]!=""?"MODIFY (":"ADD ("):"  ").implode($W).($P!=""?")":"");else$Vb[]=idf_escape($k[0]);}if($P=="")return
queries("CREATE TABLE ".table($y)." (\n".implode(",\n",array_merge($b,$Oc))."\n)");return(!$b||queries("ALTER TABLE ".table($P)."\n".implode("\n",$b)))&&(!$Vb||queries("ALTER TABLE ".table($P)." DROP (".implode(", ",$Vb).")"))&&($P==$y||queries("ALTER TABLE ".table($P)." RENAME TO ".table($y)));}function
alter_indexes($P,$b){$Vb=array();$sg=array();foreach($b
as$W){if($W[0]!="INDEX"){$W[2]=preg_replace('~ DESC$~','',$W[2]);$tb=($W[2]=="DROP"?"\nDROP CONSTRAINT ".idf_escape($W[1]):"\nADD".($W[1]!=""?" CONSTRAINT ".idf_escape($W[1]):"")." $W[0] ".($W[0]=="PRIMARY"?"KEY ":"")."(".implode(", ",$W[2]).")");array_unshift($sg,"ALTER TABLE ".table($P).$tb);}elseif($W[2]=="DROP")$Vb[]=idf_escape($W[1]);else$sg[]="CREATE INDEX ".idf_escape($W[1]!=""?$W[1]:uniqid($P."_"))." ON ".table($P)." (".implode(", ",$W[2]).")";}if($Vb)array_unshift($sg,"DROP INDEX ".implode(", ",$Vb));foreach($sg
as$D){if(!queries($D))return
false;}return
true;}function
foreign_keys($P){$F=array();$D="SELECT c_list.CONSTRAINT_NAME as NAME,
c_src.COLUMN_NAME as SRC_COLUMN,
c_dest.OWNER as DEST_DB,
c_dest.TABLE_NAME as DEST_TABLE,
c_dest.COLUMN_NAME as DEST_COLUMN,
c_list.DELETE_RULE as ON_DELETE
FROM ALL_CONSTRAINTS c_list, ALL_CONS_COLUMNS c_src, ALL_CONS_COLUMNS c_dest
WHERE c_list.CONSTRAINT_NAME = c_src.CONSTRAINT_NAME
AND c_list.R_CONSTRAINT_NAME = c_dest.CONSTRAINT_NAME
AND c_list.CONSTRAINT_TYPE = 'R'
AND c_src.TABLE_NAME = ".q($P);foreach(get_rows($D)as$G)$F[$G['NAME']]=array("db"=>$G['DEST_DB'],"table"=>$G['DEST_TABLE'],"source"=>array($G['SRC_COLUMN']),"target"=>array($G['DEST_COLUMN']),"on_delete"=>$G['ON_DELETE'],"on_update"=>null,);return$F;}function
truncate_tables(array$R){return
apply_queries("TRUNCATE TABLE",$R);}function
drop_views(array$Di){return
apply_queries("DROP VIEW",$Di);}function
drop_tables(array$R){return
apply_queries("DROP TABLE",$R);}function
last_id($E){return"0";}function
schemas(){$F=get_vals("SELECT DISTINCT owner FROM dba_segments WHERE owner IN (SELECT username FROM dba_users WHERE default_tablespace NOT IN ('SYSTEM','SYSAUX')) ORDER BY 1");return($F?:get_vals("SELECT DISTINCT owner FROM all_tables WHERE tablespace_name = ".q(DB)." ORDER BY 1"));}function
get_schema(){return
get_val("SELECT sys_context('USERENV', 'SESSION_USER') FROM dual");}function
set_schema($Lg,$g=null){return!!connection($g)->query("ALTER SESSION SET CURRENT_SCHEMA = ".idf_escape($Lg));}function
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
FROM v$session sess LEFT OUTER JOIN v$sql sql
ON sql.sql_id = sess.sql_id
WHERE sess.type = \'USER\'
ORDER BY PROCESS
');}function
convert_field(array$k){}function
unconvert_field(array$k,$F){return$F;}function
support($zc){return
preg_match('~^(columns|database|drop_col|fast_status|indexes|descidx|processlist|scheme|sql|status|table|variables|view)$~',$zc);}}class
Adminer{static$instance;var$error='';private$values=array();function
name(){return"<a href='https://www.adminer.org/editor/'".target_blank()." id='h1'><img src='".h(preg_replace("~\\?.*~","",ME)."?file=logo.png&version=6.0.1")."' width='24' height='24' alt='' id='logo'>".lang(37)."</a>";}function
credentials(){return
array(SERVER,$_GET["username"],get_password());}function
connectSsl(){}function
permanentLogin($tb=false){return
password_file($tb);}function
bruteForceKey(){return$_SERVER["REMOTE_ADDR"];}function
serverName($K){return'';}function
database(){if(connection()){$Cb=adminer()->databases(false);if(!$Cb)return
get_val("SELECT SUBSTRING_INDEX(CURRENT_USER, '@', 1)");foreach($Cb
as$h){if(!information_schema($h))return$h;}return$Cb[0];}return
null;}function
operators(){return
array("<=",">=");}function
schemas(){return
schemas();}function
databases($Mc=true){return
get_databases($Mc);}function
pluginsLinks(){}function
queryTimeout(){return
5;}function
afterConnect(){}function
headers(){}function
csp(array$wb){return$wb;}function
verifyVersion(){return
true;}function
head($_b=null){return
true;}function
bodyClass(){echo" editor";}function
css(){$F=array();foreach(array("","-dark")as$Ye){$m="adminer$Ye.css";if(file_exists($m)){$Bc=file_get_contents($m);$F["$m?v=".crc32($Bc)]=($Ye?"dark":(preg_match('~prefers-color-scheme:\s*dark~',$Bc)?'':'light'));}}return$F;}function
loginForm(){echo"<table class='layout'>\n",adminer()->loginFormField('username','<tr><th>'.lang(38).'<td>',input_hidden("auth[driver]","server").'<input name="auth[username]" autofocus value="'.h($_GET["username"]).'" autocomplete="username" autocapitalize="off">'),adminer()->loginFormField('password','<tr><th>'.lang(39).'<td>','<input type="password" name="auth[password]" autocomplete="current-password">'),"</table>\n","<p><input type='submit' value='".lang(40)."'>\n",checkbox("auth[permanent]",1,$_COOKIE["adminer_permanent"],lang(41))."\n";}function
loginFormField($y,$sd,$X){return$sd.$X."\n";}function
login($_e,$B){if($B=="")return
lang(42);if(!Driver::$passwords)return
lang(43);if(!password_required())return
lang(44);return
true;}function
tableName(array$zh){return
h(isset($zh["Engine"])?($zh["Comment"]!=""?$zh["Comment"]:$zh["Name"]):"");}function
fieldName(array$k,$_f=0){return
h(preg_replace('~\s+\[.*\]$~','',($k["comment"]!=""?$k["comment"]:$k["field"])));}function
selectLinks(array$zh,$L=""){$a=$zh["Name"];if($L!==null)echo'<p class="tabs"><a href="'.h(ME.'edit='.url_escape($a).$L).'">'.lang(45)."</a>\n";}function
foreignKeys($P){return
foreign_keys($P);}function
backwardKeys($P,$yh){$F=array();foreach(get_rows("SELECT TABLE_NAME, CONSTRAINT_NAME, COLUMN_NAME, REFERENCED_COLUMN_NAME
FROM information_schema.KEY_COLUMN_USAGE
WHERE TABLE_SCHEMA = ".q(adminer()->database())."
AND REFERENCED_TABLE_SCHEMA = ".q(adminer()->database())."
AND REFERENCED_TABLE_NAME = ".q($P)."
ORDER BY ORDINAL_POSITION",null,"")as$G)$F[$G["TABLE_NAME"]]["keys"][$G["CONSTRAINT_NAME"]][$G["COLUMN_NAME"]]=$G["REFERENCED_COLUMN_NAME"];foreach($F
as$t=>$W){$y=adminer()->tableName(table_status1($t,true));if($y!=""){$Mg=preg_quote($yh);$J="(:|\\s*-)?\\s+";$F[$t]["name"]=(preg_match("(^$Mg$J(.+)|^(.+?)$J$Mg\$)iu",$y,$x)?$x[2].$x[3]:$y);}else
unset($F[$t]);}return$F;}function
backwardKeysPrint(array$Aa,array$G){foreach($Aa
as$P=>$_a){foreach($_a["keys"]as$ab){$w=ME.'select='.url_escape($P);$o=0;foreach($ab
as$d=>$W)$w
.=where_link($o++,$d,$G[$W]);echo"<a href='".h($w)."'>".h($_a["name"])."</a>";$w=ME.'edit='.url_escape($P);foreach($ab
as$d=>$W)$w
.="&set[".url_escape(bracket_escape($d))."]=".url_escape($G[$W]);echo"<a href='".h($w)."' title='".lang(45)."'>+</a> ";}}}function
selectQuery($D,$qh,$xc=false){return
sql_comment($D,format_time($qh));}function
rowDescription($P){foreach(fields($P)as$k){if(preg_match("~varchar|character varying~",$k["type"]))return
idf_escape($k["field"]);}return"";}function
rowDescriptions(array$H,array$Qc){$F=$H;foreach($H[0]as$t=>$W){if(list($P,$p,$y)=$this->_foreignColumn($Qc,$t)){$Bd=array();foreach($H
as$G){if(isset($G[$t]))$Bd[$G[$t]]=q($G[$t]);}if(!$Bd)continue;$Ib=$this->values[$P];if(!$Ib)$Ib=get_key_vals("SELECT $p, $y FROM ".table($P)." WHERE $p IN (".implode(", ",$Bd).")");foreach($H
as$ef=>$G){if(isset($G[$t]))$F[$ef][$t]=(string)$Ib[$G[$t]];}}}return$F;}function
selectLink($W,array$k){}function
selectVal($W,$w,array$k,$Ef){$F="$W";$w=h($w);if(is_blob($k)&&!is_utf8($W)){$F=lang(46,strlen($Ef));$fh=(function_exists('getimagesizefromstring')?@getimagesizefromstring($Ef):array());if($fh)$F="<img src='$w' alt='$F' $fh[3] loading='lazy'>";}if(like_bool($k)&&$F!="")$F=(preg_match('~^(1|t|true|y|yes|on)$~i',$W)?lang(47):lang(48));if($w)$F="<a href='$w'".(is_url($w)?target_blank():"").">$F</a>";if(preg_match('~date~',$k["type"]))$F="<div class='datetime'>$F</div>";return$F;}function
editVal($W,array$k){if(preg_match('~date|timestamp~',$k["type"])&&$W!==null)return
preg_replace('~^(\d{2}(\d+))-(0?(\d+))-(0?(\d+))~',lang(49),$W);return$W;}function
config(){return
array();}function
selectColumnsPrint(array$I,array$e){}private
function
searchColumns(array$l){$F=array();$P=$_GET["select"];if($P=="")return$F;$o=0;foreach($l
as$y=>$k){if(isset($k["privileges"]["where"])&&$this->fieldName($k)!=""&&($k["type"]=="enum"||like_bool($k)||is_array($this->foreignKeyOptions($P,$y))))$F[--$o]=$y;}return$F;}function
selectSearchPrint(array$Z,array$e,array$s){$Z=(array)$_GET["where"];echo'<fieldset id="fieldset-search"><legend>'.lang(50)."</legend><div>\n";$l=fields($_GET["select"]);foreach($this->searchColumns($l)as$o=>$y){$k=$l[$y];$W=idx($Z[$o],"val");echo"<div>".h($e[$y]);if($k["type"]=="enum"||like_bool($k))echo": ",(like_bool($k)?"<select name='where[$o][val]' data-default=''>".optionlist(array(""=>"",lang(48),lang(47)),$W,true)."</select>":enum_input("checkbox"," name='where[$o][val][]'",$k,(array)$W,lang(51)));else{$_=$this->foreignKeyOptions($_GET["select"],$y);if($k["null"])$_[0]='('.lang(51).')';echo": <select name='where[$o][val]' data-default=''>".optionlist($_,$W,true)."</select>";}echo"</div>\n";unset($e[$y]);}$o=0;foreach($Z
as$t=>$W){if($t>=0&&($W["col"]==""||$e[$W["col"]])&&"$W[col]$W[val]"!=""){echo"<div><select name='where[$o][col]' data-default=''><option value=''>(".lang(52).")".optionlist($e,$W["col"],true)."</select>",html_select("where[$o][op]",array(-1=>"")+adminer()->operators(),$W["op"]," data-default=''"),"<input type='search' name='where[$o][val]' value='".h($W["val"])."' data-default=''".on('keydown','selectSearchKeydown').on('search','selectSearchSearch')."></div>\n";$o++;}}echo"<div><select name='where[$o][col]' data-default=''".on('change','selectAddRow')."><option value=''>(".lang(52).")".optionlist($e,null,true)."</select>",html_select("where[$o][op]",array(-1=>"")+adminer()->operators(),null," data-default=''"),"<input type='search' name='where[$o][val]' data-default=''".on('change','selectFirstChange').on('keydown','selectSearchKeydown').on('search','selectSearchSearch')."></div>\n","</div></fieldset>\n";}function
selectOrderPrint(array$_f,array$e,array$s){$Bf=array();foreach($s
as$t=>$r){$_f=array();foreach($r["columns"]as$W)$_f[]=$e[$W];if(count(array_filter($_f,'strlen'))>1&&$t!="PRIMARY")$Bf[$t]=implode(", ",$_f);}if($Bf)echo'<fieldset><legend>'.lang(53)."</legend><div>","<select name='index_order' data-default=''>".optionlist(array(""=>"")+$Bf,(idx($_GET["order"],0)!=""?"":$_GET["index_order"]),true)."</select>","</div></fieldset>\n";if($_GET["order"])echo"<div hidden>".hidden_fields(array("order"=>array(1=>reset($_GET["order"])),"desc"=>($_GET["desc"]?array(1=>1):array()),))."</div>\n";}function
selectLimitPrint($v){echo"<fieldset><legend>".lang(54)."</legend><div>",html_select("limit",array("","50","100"),(string)$v," data-default='50'"),"</div></fieldset>\n";}function
selectLengthPrint($Hh){}function
selectActionPrint(array$s){echo"<fieldset><legend>".lang(55)."</legend><div>","<input type='submit' value='".lang(56)."'>","</div></fieldset>\n";}function
selectCommandPrint(){return
true;}function
selectImportPrint(){return
true;}function
selectEmailPrint(array$dc,array$e){}function
selectColumnsProcess(array$e,array$s){return
array(array(),array());}function
selectSearchProcess(array$l,array$s){$F=array();$Ng=$this->searchColumns($l);if($_GET["select"]!=""&&!$_POST&&!is_ajax()){$fe=array();$w="";foreach((array)$_GET["where"]as$t=>$Z){$fg=($t>=0?array_search($Z["col"],$Ng,true):false);$k=idx($l,$Z["col"],array());if($fg!==false&&$k["type"]!="enum"&&!is_array($Z["val"])&&$Z["val"]!=""&&$Z["op"]==(like_bool($k)?"":"=")){$fe[]=$t;$w
.="&where[$fg][val]=".url_escape($Z["val"]);}}if($fe)redirect(remove_from_uri("where(%5B|\[)(".implode("|",$fe).")(%5D|\])[^=]*").$w);}foreach((array)$_GET["where"]as$t=>$Z){if($t<0){$Z["col"]=idx($Ng,$t,"");$k=idx($l,$Z["col"],array());$Z["op"]=($k&&($k["type"]=="enum"||like_bool($k))?"":"=");$_GET["where"][$t]=$Z;}$Z+=array("col"=>"","op"=>"","val"=>"");$Xa=$Z["col"];$vf=$Z["op"];$W=$Z["val"];if(($t>=0&&$Xa!="")||$W!=""){$fb=array();foreach(($Xa!=""?array($Xa=>$l[$Xa]):$l)as$y=>$k){if($Xa!=""||is_searchable($k,$Z)){$y=idf_escape($y);if($Xa!=""&&$k["type"]=="enum"){$Dd=array();foreach(preg_grep('~^val-~',$W)as$yi)$Dd[]=q(substr($yi,4));$fb[]=(in_array("null",$W)?"$y IS NULL OR ":"").($Dd?"$y IN (".implode(", ",$Dd).")":"0");}else{$Ih=preg_match('~'.text_type().'~',$k["type"]);$X=q(!$vf&&$Ih&&preg_match('~^[^%]+$~',$W)?"%$W%":$W);$fb[]=driver()->convertSearch($y,$Z,$k).($X=="NULL"?" IS".($vf==">="?" NOT":"")." $X":(in_array($vf,adminer()->operators())||$vf=="="?" $vf $X":($Ih?" LIKE $X":" IN (".($X[0]=="'"?str_replace(",","', '",$X):$X).")")));if($t<0&&$W=="0")$fb[]="$y IS NULL";}}}$F[]=($fb?"(".implode(" OR ",$fb).")":"1 = 0");}}return$F;}function
selectOrderProcess(array$l,array$s){$Gd=$_GET["index_order"];if($Gd!="")unset($_GET["order"][1]);if($_GET["order"])return
array(idf_escape(reset($_GET["order"])).($_GET["desc"]?" DESC":""));foreach(($Gd!=""?array($s[$Gd]):$s)as$r){if($Gd!=""||$r["type"]=="INDEX"){$kd=array_filter($r["descs"]);$Hb=false;foreach($r["columns"]as$W){if(preg_match('~date|timestamp~',$l[$W]["type"])){$Hb=true;break;}}$F=array();foreach($r["columns"]as$t=>$W)$F[]=idf_escape($W).(($kd?$r["descs"][$t]:$Hb)?" DESC":"");return$F;}}return
array();}function
selectLimitProcess(){return(isset($_GET["limit"])?intval($_GET["limit"]):50);}function
selectLengthProcess(){return"100";}function
selectEmailProcess(array$Z,array$Qc){return
false;}function
selectQueryBuild(array$I,array$Z,array$dd,array$_f,$v,$A){return"";}function
messageQuery($D,$Jh,$xc=false){return" <span class='time'>".@date("H:i:s")."</span>".sql_comment($D,$Jh);}function
error(){return
error();}function
editRowPrint($P,array$l,$G,$T,$D='',$Jh=''){echo($D!=""?sql_comment($D,$Jh):"");}function
editFunctions(array$k){$F=array();if($k["null"]&&preg_match('~blob~',$k["type"]))$F["NULL"]=lang(51);$F[""]=($k["null"]||$k["auto_increment"]||like_bool($k)?"":"*");if(preg_match('~date|time~',$k["type"]))$F["now"]=lang(57);if(preg_match('~_(md5|sha1)$~i',$k["field"],$x))$F[]=strtolower($x[1]);return$F;}function
editInput($P,array$k,$c,$X){if($k["type"]=="enum")return(isset($_GET["select"])?"<label><input type='radio'$c value='orig' checked><i>".lang(11)."</i></label> ":"").enum_input("radio",$c,$k,$X,lang(51));$_=$this->foreignKeyOptions($P,$k["field"],$X);if($_!==null){if(!$k["null"]&&is_array($_))unset($_[""]);return(is_array($_)?"<select$c>".optionlist($_,(string)$X,true)."</select>":"<input value='".h($X)."'$c class='hidden'>"."<input value='".h($_)."' class='jsonly'".on('input','whisper',ME."script=complete&source=".url_escape($P)."&field=".url_escape($k["field"])."&value=").">"."<div".on('click','whisperClick')."></div>");}if(like_bool($k))return'<input type="checkbox" value="1"'.(preg_match('~^(1|t|true|y|yes|on)$~i',$X)?' checked':'')."$c>";$ud="";if(preg_match('~time~',$k["type"]))$ud=lang(58);if(preg_match('~date|timestamp~',$k["type"]))$ud=lang(59).($ud?" [$ud]":"");if($ud)return"<input value='".h($X)."'$c> ($ud)";if(preg_match('~_(md5|sha1)$~i',$k["field"]))return"<input type='password' value='".h($X)."'$c>";return'';}function
editHint($P,array$k,$X){return(preg_match('~\s+(\[.*\])$~',($k["comment"]!=""?$k["comment"]:$k["field"]),$x)?h(" $x[1]"):'');}function
processInput(array$k,$X,$n=""){if($n=="now")return"$n()";$F=$X;if(preg_match('~date|timestamp~',$k["type"])&&preg_match('(^'.str_replace('\$1','(?P<p1>\d*)',preg_replace('~(\\\\\\$([2-6]))~','(?P<p\2>\d{1,2})',preg_quote(lang(49)))).'(.*))',$X,$x))$F=($x["p1"]!=""?$x["p1"]:($x["p2"]!=""?($x["p2"]<70?20:19).$x["p2"]:gmdate("Y")))."-$x[p3]$x[p4]-$x[p5]$x[p6]".end($x);$F=q($F);if($X==""&&like_bool($k))$F="'0'";elseif($X==""&&($k["null"]||!preg_match('~char|text~',$k["type"])))$F="NULL";elseif(preg_match('~^(md5|sha1)$~',$n))$F="$n($F)";return
unconvert_field($k,$F);}function
dumpOutput(){return
array();}function
dumpFormat(){return
array('csv'=>'CSV,','csv;'=>'CSV;','tsv'=>'TSV');}function
dumpDatabase($h){}function
dumpTable($P,$uh,$ae=0){echo"\xef\xbb\xbf";}function
dumpData($P,$uh,$D){$E=connection()->query($D,1);if($E){while($G=$E->fetch_assoc()){if($uh=="table"){dump_csv(array_keys($G));$uh="INSERT";}dump_csv($G);}}}function
dumpFilename($_d){return
friendly_url($_d);}function
dumpHeaders($_d,$cf=false){$tc="csv";header("Content-Type: text/csv; charset=utf-8");return$tc;}function
dumpFooter(){}function
importServerPath(){return'';}function
homepage(){return
true;}function
navigation($Xe){echo"<h1>".adminer()->name()." <span class='version'>".VERSION;$if=$_COOKIE["adminer_version"];echo" <a href='https://www.adminer.org/editor/#download'".target_blank()." id='version'>".(version_compare(VERSION,$if)<0?h($if):"").version_iframe()."</a>","</span></h1>\n";switch_lang();if($Xe=="auth"){$Hc=true;foreach((array)$_SESSION["pwds"]as$Ai=>$ah){foreach($ah[""]as$U=>$B){if($B!==null){if($Hc){echo"<ul id='logins'".on('mouseover','menuOver').on('mouseout','menuOut').">";$Hc=false;}echo"<li><a href='".h(auth_url($Ai,"",$U))."'>".($U!=""?h($U):"<i>".lang(51)."</i>")."</a>\n";}}}}else{adminer()->databasesPrint($Xe);$ea=adminer()->menuActions(array(),$Xe);echo($ea?"<p class='links'>\n".implode("\n",$ea)."\n":"");if($Xe!="db"&&$Xe!="ns"){$Q=table_status('',true);if(!$Q)echo"<p class='message'>".lang(12)."\n";else
adminer()->tablesPrint($Q);}}}function
syntaxHighlighting(array$R){}function
databasesPrint($Xe){}function
menuActions(array$ea,$Xe){return$ea;}function
tablesPrint(array$R){echo"<ul id='tables'".on('mouseover','menuOver').on('mouseout','menuOut').">";foreach($R
as$G){echo'<li>';$y=adminer()->tableName($G);if($y!="")echo"<a href='".h(ME).'select='.url_escape($G["Name"])."'".bold($_GET["select"]==$G["Name"]||$_GET["edit"]==$G["Name"],"select")." title='".lang(60)."'>$y</a>\n";}echo"</ul>\n";}function
_foreignColumn(array$Qc,$d){foreach((array)$Qc[$d]as$Pc){if(count($Pc["source"])==1){$y=adminer()->rowDescription($Pc["table"]);if($y!=""){$p=idf_escape($Pc["target"][0]);return
array($Pc["table"],$p,$y);}}}}private
function
foreignKeyOptions($P,$d,$X=null){if(list($Eh,$p,$y)=$this->_foreignColumn(column_foreign_keys($P),$d)){$F=&$this->values[$Eh];if($F===null){$Q=table_status1($Eh);$F=($Q["Rows"]>1000?"":array(""=>"")+get_key_vals("SELECT $p, $y FROM ".table($Eh)." ORDER BY 2"));}if(!$F&&$X!==null)return
get_val("SELECT $y FROM ".table($Eh)." WHERE $p = ".q($X));return$F;}}}class
Plugins{private
static$append=array('dumpFormat'=>true,'dumpOutput'=>true,'editRowPrint'=>true,'editFunctions'=>true,'config'=>true);var$plugins;var$drivers=array();var$driverFiles=array();var$error='';private$hooks=array();function
__construct($cg){$Ub=SqlDriver::$drivers;$td=" href='https://www.adminer.org/plugins/#use'".target_blank();if($cg===null){$cg=array();$Da="adminer-plugins";if(is_dir($Da)){foreach(glob("$Da/*.php")as$m){$Cc=SqlDriver::$drivers;$this->includeOnce($m);foreach(array_diff_key(SqlDriver::$drivers,$Cc)as$p=>$y)$this->driverFiles[$p]=$m;}}if(file_exists("$Da.php")){$Ed=$this->includeOnce("$Da.php");if(is_array($Ed)){foreach($Ed
as$t=>$ag)$cg[is_object($ag)?get_class($ag):$t]=$ag;}else$this->error
.=lang(61,"<b>$Da.php</b>",$td)."<br>";}foreach(get_declared_classes()as$Ua){if(!$cg[$Ua]&&(preg_match('~^Adminer\w~i',$Ua)||is_subclass_of($Ua,'Adminer\Plugin'))){$zg=new
\ReflectionClass($Ua);$lb=$zg->getConstructor();if($lb&&$lb->getNumberOfRequiredParameters())$this->error
.=lang(62,$td,"<b>$Ua</b>","<b>$Da.php</b>")."<br>";else$cg[$Ua]=new$Ua;}}}$Sd=array_filter($cg,function($ag){return!is_object($ag);});if($Sd){$this->error
.=lang(63,$td)."<br>";$cg=array_diff_key($cg,$Sd);}$this->drivers=array_diff_key(SqlDriver::$drivers,$Ub);$this->plugins=$cg;$ia=new
Adminer;$cg[]=$ia;$zg=new
\ReflectionObject($ia);foreach($zg->getMethods()as$We){foreach($cg
as$ag){$y=$We->getName();if(method_exists($ag,$y))$this->hooks[$y][]=$ag;}}}function
includeOnce($m){return
include_once"./$m";}static
function
checksum($m){$Bc=str_replace("\r","",file_get_contents($m));$Bc=preg_replace('~\n\tprotected \$translations = array\(.*?\n\t\);~s','',$Bc);return
dechex(crc32($Bc));}function
checksums(){$Dc=array_values($this->driverFiles);foreach($this->plugins
as$ag){$zg=new
\ReflectionObject($ag);$Dc[]=$zg->getFileName();}$F=array();foreach($Dc
as$m)$F[basename($m,'.php')]=self::checksum($m);return$F;}static
function
officialChecksums(){return
array('adminer.js'=>'a0599090','backward-keys'=>'ed1ef78f','before-unload'=>'2a613523','config'=>'722eb4af','dark-switcher'=>'3d490dea','database-hide'=>'e304a899','designs'=>'d1515f34','dump-alter'=>'896b579e','dump-bz2'=>'f0d0e336','dump-date'=>'adc7f1c7','dump-json'=>'767dd321','dump-xml'=>'4fc3cd60','dump-zip'=>'93817d96','edit-foreign'=>'72ad1562','edit-textarea'=>'a24c3cc','editor-setup'=>'a7dc3a37','editor-views'=>'5c12b185','enum-option'=>'96ee8718','file-upload'=>'10add0e8','foreign-system'=>'ebb4c654','frames'=>'b0e1d11a','highlight-codemirror'=>'f4baf411','highlight-monaco'=>'edd1b0af','highlight-prism'=>'267948e5','import-csv'=>'d429c77','login-ip'=>'4d174fea','login-otp'=>'5b5a68af','login-passkey'=>'f69f2f06','login-password-less'=>'e150daac','login-reverse-proxy'=>'24558ea2','login-servers'=>'19c42e45','login-ssl'=>'6ed147bc','login-table'=>'811f8cef','menu-links'=>'7f3d5020','remote-color'=>'86a39047','row-numbers'=>'eec8698c','select-email'=>'f84fbd2c','select-image'=>'f55c0231','slugify'=>'dec64713','sql-gemini'=>'c60ab309','sql-log'=>'8e435000','table-indexes-structure'=>'a90cc0c9','table-structure'=>'a8458e02','tables-filter'=>'ec2bcd6e','timeout'=>'97321caf','version-github'=>'627cadf9','version-noverify'=>'966937e9','clickhouse'=>'b0f6631c','elastic'=>'27503b8b','firebird'=>'5499d1a','igdb'=>'59055fd3','imap'=>'ac143217','mongo'=>'c3b8f5a4','redis'=>'ba56e72e','simpledb'=>'92f050ad',);}function
__call($y,array$Nf){$ra=array();foreach($Nf
as$t=>$W)$ra[]=&$Nf[$t];$F=null;foreach($this->hooks[$y]as$ag){$X=call_user_func_array(array($ag,$y),$ra);if($X!==null){if(!self::$append[$y])return$X;$F=$X+(array)$F;}}return$F;}}abstract
class
Plugin{protected$translations=array();function
description(){return$this->lang('');}function
screenshot(){return"";}protected
function
lang($q,$z=null){$ra=func_get_args();$ra[0]=idx($this->translations[LANG],$q)?:$q;return
call_user_func_array('Adminer\lang_format',$ra);}}class
Password{private$password_hash;private$password_matches=null;function
__construct($Tf){$this->password_hash=$Tf;}function
description(){return
lang(64);}function
credentials(){$B=get_password();return
array(SERVER,$_GET["username"],($this->passwordMatches($B)&&!password_required()?"":$B));}function
login($_e,$B){if($this->passwordMatches($B))return
true;}protected
function
passwordMatches($B){if($this->password_matches===null)$this->password_matches=(function_exists('password_verify')&&password_verify(strval($B),$this->password_hash));return$this->password_matches;}}Adminer::$instance=(function_exists('adminer_object')?adminer_object():(is_dir("adminer-plugins")||file_exists("adminer-plugins.php")?new
Plugins(null):new
Adminer));SqlDriver::$drivers=array("server"=>"MySQL / MariaDB")+SqlDriver::$drivers;if(!defined('Adminer\DRIVER')){define('Adminer\DRIVER',"server");if(extension_loaded("mysqli")&&$_GET["ext"]!="pdo"){class
Db
extends
\mysqli{static$instance;var$extension="MySQLi",$flavor='';function
__construct(){parent::init();}function
attach($K,$U,$B){mysqli_report(MYSQLI_REPORT_OFF);list($wd,$dg)=host_port($K);$M=adminer()->connectSsl();$ui=($M&&($M['key']||$M['cert']||$M['ca']||isset($M['verify'])));if($ui)$this->ssl_set($M['key'],$M['cert'],$M['ca'],'','');$F=@$this->real_connect(($K!=""?$wd:ini_get("mysqli.default_host")),($K.$U!=""?$U:ini_get("mysqli.default_user")),($K.$U.$B!=""?$B:ini_get("mysqli.default_pw")),null,(is_numeric($dg)?intval($dg):ini_get("mysqli.default_port")),(is_numeric($dg)?null:$dg),($ui?($M['verify']!==false?MYSQLI_CLIENT_SSL:64):0));$this->options(MYSQLI_OPT_LOCAL_INFILE,0);return($F?'':$this->error);}function
set_charset($Oa){if(parent::set_charset($Oa))return
true;parent::set_charset('utf8');return$this->query("SET NAMES $Oa");}function
next_result(){return
self::more_results()&&parent::next_result();}function
quote($O){return"'".$this->escape_string($O)."'";}function
inTransaction(){return
false;}}}elseif(extension_loaded("mysql")&&!((ini_bool("sql.safe_mode")||ini_bool("mysql.allow_local_infile"))&&extension_loaded("pdo_mysql"))){class
Db
extends
SqlDb{private$link;function
attach($K,$U,$B){if(ini_bool("mysql.allow_local_infile"))return
lang(65,"'mysql.allow_local_infile'","MySQLi","PDO_MySQL");$this->link=@mysql_connect(($K!=""?$K:ini_get("mysql.default_host")),($K.$U!=""?$U:ini_get("mysql.default_user")),($K.$U.$B!=""?$B:ini_get("mysql.default_password")),true,131072);if(!$this->link)return
mysql_error();$this->server_info=mysql_get_server_info($this->link);return'';}function
set_charset($Oa){return
mysql_set_charset($Oa,$this->link)||mysql_set_charset('utf8',$this->link);}function
quote($O){return"'".mysql_real_escape_string($O,$this->link)."'";}function
select_db($Bb){return
mysql_select_db($Bb,$this->link);}function
query($D,$fi=false){$E=@($fi?mysql_unbuffered_query($D,$this->link):mysql_query($D,$this->link));$this->error="";if(!$E){$this->errno=mysql_errno($this->link);$this->error=mysql_error($this->link);return
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
fetch_field(){$F=mysql_fetch_field($this->result,$this->offset++);$F->orgtable=$F->table;$F->charsetnr=($F->blob?63:0);return$F;}}}elseif(extension_loaded("pdo_mysql")){class
Db
extends
PdoDb{var$extension="PDO_MySQL";function
attach($K,$U,$B){$_=array(\PDO::MYSQL_ATTR_LOCAL_INFILE=>false);if(isset($_GET["select"]))$_[\PDO::MYSQL_ATTR_MULTI_STATEMENTS]=false;$M=adminer()->connectSsl();if($M){if($M['key'])$_[\PDO::MYSQL_ATTR_SSL_KEY]=$M['key'];if($M['cert'])$_[\PDO::MYSQL_ATTR_SSL_CERT]=$M['cert'];if($M['ca'])$_[\PDO::MYSQL_ATTR_SSL_CA]=$M['ca'];if(isset($M['verify']))$_[\PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT]=$M['verify'];}list($wd,$dg)=host_port($K);return$this->dsn("mysql:charset=utf8".($wd!=""?";host=$wd":'').($dg?(is_numeric($dg)?";port=":";unix_socket=").$dg:""),$U,$B,$_);}function
set_charset($Oa){return$this->query("SET NAMES $Oa");}function
select_db($Bb){return$this->query("USE ".idf_escape($Bb));}function
query($D,$fi=false){$this->pdo->setAttribute(\PDO::MYSQL_ATTR_USE_BUFFERED_QUERY,!$fi);return
parent::query($D,$fi);}}}class
Driver
extends
SqlDriver{static$extensions=array("MySQLi","MySQL","PDO_MySQL");static$jush="sql";var$unsigned=array("unsigned","zerofill","unsigned zerofill");var$operators=array("=","<",">","<=",">=","!=","LIKE","LIKE %%","REGEXP","IN","FIND_IN_SET","IS NULL","NOT LIKE","NOT REGEXP","NOT IN","IS NOT NULL","SQL");var$functions=array("char_length","date","from_unixtime","lower","round","floor","ceil","sec_to_time","time_to_sec","upper");var$grouping=array("avg","count","count distinct","group_concat","max","min","sum");var$partitionBy=array("HASH","LINEAR HASH","KEY","LINEAR KEY","RANGE","LIST");static
function
connect($K,$U,$B){$f=parent::connect($K,$U,$B);if(is_string($f)){if(function_exists('iconv')&&!is_utf8($f)&&strlen($Kg=iconv("windows-1252","utf-8//IGNORE",$f))>strlen($f))$f=$Kg;return$f;}$f->set_charset(charset($f));$f->query("SET sql_quote_show_create = 1, autocommit = 1");$f->flavor=(preg_match('~MariaDB~',$f->server_info)?'maria':'mysql');add_driver(DRIVER,($f->flavor=='maria'?"MariaDB":"MySQL"));return$f;}function
__construct(Db$f){parent::__construct($f);$this->types=array(lang(28)=>array("tinyint"=>3,"smallint"=>5,"mediumint"=>8,"int"=>10,"bigint"=>20,"decimal"=>66,"float"=>12,"double"=>21),lang(29)=>array("date"=>10,"datetime"=>19,"timestamp"=>19,"time"=>10,"year"=>4),lang(30)=>array("char"=>255,"varchar"=>65535,"tinytext"=>255,"text"=>65535,"mediumtext"=>16777215,"longtext"=>4294967295),lang(66)=>array("enum"=>65535,"set"=>64),lang(31)=>array("bit"=>20,"binary"=>255,"varbinary"=>65535,"tinyblob"=>255,"blob"=>65535,"mediumblob"=>16777215,"longblob"=>4294967295),lang(33)=>array("geometry"=>0,"point"=>0,"linestring"=>0,"polygon"=>0,"multipoint"=>0,"multilinestring"=>0,"multipolygon"=>0,"geometrycollection"=>0),);$this->insertFunctions=array("char"=>"md5/sha1/password/encrypt/uuid","binary"=>"md5/sha1","date|time"=>"now",);$this->editFunctions=array(number_type()=>"+/-","date"=>"+ interval/- interval","time"=>"addtime/subtime","char|text"=>"concat",);if(min_version('5.7.8',10.2,$f))$this->types[lang(30)]["json"]=4294967295;if(min_version('',10.7,$f)){$this->types[lang(30)]["uuid"]=128;$this->insertFunctions['uuid']='uuid';}if(min_version('',10.5,$f)){$this->types[lang(32)]["inet6"]=39;if(min_version('','10.10',$f))$this->types[lang(32)]["inet4"]=15;}if(min_version(9,11.7,$f))$this->types[lang(28)]["vector"]=16383;if(min_version(5.7,10.2,$f))$this->generated=array("STORED","VIRTUAL");}function
unconvertFunction(array$k){return(preg_match("~binary~",$k["type"])?"<code class='jush-sql'>UNHEX</code>":($k["type"]=="bit"?doc_link(array('sql'=>'bit-value-literals.html'),"<code>b''</code>"):($k["type"]=="vector"?"<code class='jush-sql'>".($this->conn->flavor=='maria'?"VEC_FromText":"STRING_TO_VECTOR")."</code>":(preg_match("~geom|point|linestring|polygon~",$k["type"])?"<code class='jush-sql'>GeomFromText</code>":""))));}function
insert($P,array$L){return($L?parent::insert($P,$L):queries("INSERT INTO ".table($P)." ()\nVALUES ()"));}function
insertUpdate($P,array$H,array$C){$e=array_keys(reset($H));$jg="INSERT INTO ".table($P)." (".implode(", ",$e).") VALUES\n";$Y=array();foreach($e
as$t)$Y[$t]="$t = VALUES($t)";$vh="\nON DUPLICATE KEY UPDATE ".implode(", ",$Y);$Y=array();$u=0;foreach($H
as$L){$X="(".implode(", ",$L).")";if($Y&&(strlen($jg)+$u+strlen($X)+strlen($vh)>1e6)){if(!queries($jg.implode(",\n",$Y).$vh))return
false;$Y=array();$u=0;}$Y[]=$X;$u+=strlen($X)+2;}return
queries($jg.implode(",\n",$Y).$vh);}function
slowQuery($D,$Kh){if(min_version('5.7.8','10.1.2')){if($this->conn->flavor=='maria')return"SET STATEMENT max_statement_time=$Kh FOR $D";elseif(preg_match('~^(SELECT\b)(.+)~is',$D,$x))return"$x[1] /*+ MAX_EXECUTION_TIME(".($Kh*1000).") */ $x[2]";}}function
convertColumn($q,array$k){if(preg_match("~binary~",$k["type"]))return"HEX($q)";if($k["type"]=="bit")return"BIN($q + 0)";if($k["type"]=="vector")return($this->conn->flavor=='maria'?"VEC_ToText":"VECTOR_TO_STRING")."($q)";if(preg_match("~geom|point|linestring|polygon~",$k["type"]))return(min_version(8)?"ST_":"")."AsWKT($q)";return"";}function
convertSearch($q,array$W,array$k){return($this->convertColumn($q,$k)?:(preg_match('~'.text_type().'~',$k["type"])&&!preg_match("~^utf8~",$k["collation"])&&preg_match('~[\x80-\xFF]~',$W['val'])?"CONVERT($q USING ".charset($this->conn).")":$q));}function
typeName(\stdClass$k){$ei=array("decimal","tinyint","smallint","int","float","double",7=>"timestamp","bigint","mediumint","date","time","datetime","year",15=>"varchar","bit",242=>"vector",245=>"json","decimal","enum","set","tinytext","mediumtext","longtext","text","varchar","char","geometry",);$F=idx($ei,$k->type,"");return
parent::typeName($k)?:($k->charsetnr==63?str_replace(array("text","varchar","char"),array("blob","varbinary","binary"),$F):$F);}function
quoteBinary($Kg){return"X".q(bin2hex($Kg));}function
warnings(){$E=$this->conn->query("SHOW WARNINGS");if($E&&$E->num_rows){ob_start();print_select_result($E);return
ob_get_clean();}}function
tableHelp($y,$ae=false){$Be=($this->conn->flavor=='maria');if(information_schema(DB))return
strtolower(str_replace("_","-",DB)."-".($Be?"$y-table/":str_replace("_","-",$y)."-table.html"));if(DB=="sys")return($Be?"sys-schema/":strtolower("sys-".str_replace("_","-",preg_replace('~^x\$~','',$y)).".html"));if(DB=="mysql")return($Be?"mysql$y-table/":"system-schema.html");}function
partitionsInfo($P){$Xc="FROM information_schema.PARTITIONS WHERE TABLE_SCHEMA = ".q(DB)." AND TABLE_NAME = ".q($P);$E=$this->conn->query("SELECT PARTITION_METHOD, PARTITION_EXPRESSION, PARTITION_ORDINAL_POSITION $Xc ORDER BY PARTITION_ORDINAL_POSITION DESC LIMIT 1");$G=($E?$E->fetch_row():null);if(!$G)return
array();$F=array();list($F["partition_by"],$F["partition"],$F["partitions"])=$G;$Sf=get_key_vals("SELECT PARTITION_NAME, PARTITION_DESCRIPTION $Xc AND PARTITION_NAME != '' ORDER BY PARTITION_ORDINAL_POSITION");$F["partition_names"]=array_keys($Sf);$F["partition_values"]=array_values($Sf);return$F;}function
hasCStyleEscapes(){static$La;if($La===null){$oh=get_val("SHOW VARIABLES LIKE 'sql_mode'",1,$this->conn);$La=(strpos($oh,'NO_BACKSLASH_ESCAPES')===false);}return$La;}function
lineComment(){return"#|-- ";}function
engines(){$F=array();foreach(get_rows("SHOW ENGINES")as$G){if(preg_match("~YES|DEFAULT~",$G["Support"]))$F[]=$G["Engine"];}return$F;}function
indexAlgorithms(array$zh){return(preg_match('~^(MEMORY|NDB)$~',$zh["Engine"])?array("HASH","BTREE"):array());}}function
idf_escape($q){return"`".str_replace("`","``",$q)."`";}function
table($q){return
idf_escape($q);}function
get_databases($Mc){$F=get_session("dbs");if($F===null){$D="SELECT SCHEMA_NAME FROM information_schema.SCHEMATA ORDER BY SCHEMA_NAME";$qh=microtime(true);$F=($Mc?slow_query($D):get_vals($D));if(microtime(true)-$qh>0.1){restart_session();set_session("dbs",$F);stop_session();}}return$F;}function
limit($D,$Z,$v,$qf=0,$J=" "){return" $D$Z".($v?$J."LIMIT $v".($qf?" OFFSET $qf":""):"");}function
limit1($P,$D,$Z,$J="\n"){return
limit($D,$Z,1,0,$J);}function
db_collation($h,array$Za){$F=null;$tb=get_val("SHOW CREATE DATABASE ".idf_escape($h),1);if(preg_match('~ COLLATE ([^ ]+)~',$tb,$x))$F=$x[1];elseif(preg_match('~ CHARACTER SET ([^ ]+)~',$tb,$x))$F=$Za[$x[1]][-1];return$F;}function
logged_user(){return
get_val("SELECT USER()");}function
tables_list(){return
get_key_vals("SELECT TABLE_NAME, TABLE_TYPE FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() ORDER BY TABLE_NAME");}function
count_tables(array$Cb){$F=array();foreach($Cb
as$h)$F[$h]=count(get_vals("SHOW TABLES IN ".idf_escape($h)));return$F;}function
table_status($y="",$yc=false){$F=array();foreach(get_rows($yc?"SELECT TABLE_NAME AS Name, ENGINE AS Engine, TABLE_COMMENT AS Comment FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() ".($y!=""?"AND TABLE_NAME = ".q($y):"ORDER BY Name"):"SHOW TABLE STATUS".($y!=""?" LIKE ".q(addcslashes($y,"%_\\")):""))as$G){if($G["Engine"]=="InnoDB")$G["Comment"]=preg_replace('~(?:(.+); )?InnoDB free: .*~','\1',$G["Comment"]);if(!isset($G["Engine"]))$G["Comment"]="";if($y!="")$G["Name"]=$y;$F[$G["Name"]]=$G;}return$F;}function
is_view(array$Q){return$Q["Engine"]===null;}function
fk_support(array$Q){return
preg_match('~InnoDB|IBMDB2I'.(min_version(5.6)?'|NDB':'').'~i',$Q["Engine"]);}function
parse_type($Yc){preg_match('~^([^( ]+)(?:\((.+)\))?( unsigned)?( zerofill)?$~',$Yc,$x);return
array($x[1],$x[2],ltrim($x[3].$x[4]));}function
fields($P){$Be=(connection()->flavor=='maria');$F=array();foreach(get_rows("SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ".q($P)." ORDER BY ORDINAL_POSITION")as$G){$k=$G["COLUMN_NAME"];$S=$G["COLUMN_TYPE"];$cd=$G["GENERATION_EXPRESSION"];$wc=$G["EXTRA"];preg_match('~^(VIRTUAL|PERSISTENT|STORED)~',$wc,$bd);list($di,$u,$mi)=parse_type($S);$i=$G["COLUMN_DEFAULT"];if($i!=""){$Zd=preg_match('~text|json~',$di);if(!$Be&&$Zd)$i=preg_replace("~^(_\w+)?('.*')$~",'\2',stripslashes($i));if($Be||$Zd){$i=($i=="NULL"?null:preg_replace_callback("~^'(.*)'$~",function($x){return
stripslashes(str_replace("''","'",$x[1]));},$i));}if(!$Be&&preg_match('~binary~',$di)&&preg_match('~^0x(\w*)$~',$i,$x))$i=pack("H*",$x[1]);}$F[$k]=array("field"=>$k,"full_type"=>$S,"type"=>$di,"length"=>$u,"unsigned"=>$mi,"default"=>($bd?($Be?$cd:stripslashes($cd)):$i),"null"=>($G["IS_NULLABLE"]=="YES"),"auto_increment"=>($wc=="auto_increment"),"on_update"=>(preg_match('~\bon update (\w+)~i',$wc,$x)?$x[1]:""),"collation"=>$G["COLLATION_NAME"],"privileges"=>array_flip(explode(",","$G[PRIVILEGES],where,order")),"comment"=>$G["COLUMN_COMMENT"],"primary"=>($G["COLUMN_KEY"]=="PRI"),"generated"=>($bd[1]=="PERSISTENT"?"STORED":$bd[1]),);}return$F;}function
indexes($P,$g=null){$F=array();foreach(get_rows("SHOW INDEX FROM ".table($P),$g)as$G){$y=$G["Key_name"];$F[$y]["type"]=($y=="PRIMARY"?"PRIMARY":($G["Index_type"]=="FULLTEXT"?"FULLTEXT":($G["Non_unique"]?(preg_match('~^(SPATIAL|VECTOR)$~',$G["Index_type"])?$G["Index_type"]:"INDEX"):"UNIQUE")));$F[$y]["columns"][]=$G["Column_name"];$F[$y]["lengths"][]=($G["Index_type"]=="SPATIAL"?null:$G["Sub_part"]);$F[$y]["descs"][]=null;$F[$y]["algorithm"]=$G["Index_type"];}return$F;}function
foreign_keys($P){static$Xf='(?:`(?:[^`]|``)+`|"(?:[^"]|"")+")';$F=array();$ub=get_val("SHOW CREATE TABLE ".table($P),1);if($ub){preg_match_all("~CONSTRAINT ($Xf) FOREIGN KEY ?\\(((?:$Xf,? ?)+)\\) REFERENCES ($Xf)(?:\\.($Xf))? \\(((?:$Xf,? ?)+)\\)(?: ON DELETE (".driver()->onActions."))?(?: ON UPDATE (".driver()->onActions."))?~",$ub,$Ee,PREG_SET_ORDER);foreach($Ee
as$x){preg_match_all("~$Xf~",$x[2],$lh);preg_match_all("~$Xf~",$x[5],$Eh);$F[idf_unescape($x[1])]=array("db"=>idf_unescape($x[4]!=""?$x[3]:$x[4]),"table"=>idf_unescape($x[4]!=""?$x[4]:$x[3]),"source"=>array_map('Adminer\idf_unescape',$lh[0]),"target"=>array_map('Adminer\idf_unescape',$Eh[0]),"on_delete"=>($x[6]?:"RESTRICT"),"on_update"=>($x[7]?:"RESTRICT"),);}}return$F;}function
view($y){return
array("select"=>preg_replace('~^(?:[^`]|`[^`]*`)*\s+AS\s+~isU','',get_val("SHOW CREATE VIEW ".table($y),1)));}function
collations(){$F=array();foreach(get_rows("SHOW COLLATION")as$G){if($G["Default"])$F[$G["Charset"]][-1]=$G["Collation"];else$F[$G["Charset"]][]=$G["Collation"];}ksort($F);foreach($F
as$t=>$W)sort($F[$t]);return$F;}function
information_schema($h,$Lg=""){return($h=="information_schema")||(min_version(5.5)&&$h=="performance_schema");}function
error(){return
h(preg_replace('~^You have an error.*syntax to use~U',"Syntax error",connection()->error));}function
create_database($h,$Ya){return
queries("CREATE DATABASE ".idf_escape($h).($Ya?" COLLATE ".q($Ya):""));}function
drop_databases(array$Cb){$F=apply_queries("DROP DATABASE",$Cb,'Adminer\idf_escape');restart_session();set_session("dbs",null);return$F;}function
rename_database($y,$Ya){$F=false;if(create_database($y,$Ya)){$R=array();$Di=array();foreach(tables_list()as$P=>$S){if($S=='VIEW')$Di[]=$P;else$R[]=$P;}$F=(!$R&&!$Di)||move_tables($R,$Di,$y);drop_databases($F?array(DB):array());}return$F;}function
auto_increment(){$xa=" PRIMARY KEY";if($_GET["create"]!=""&&$_POST["auto_increment_col"]){foreach(indexes($_GET["create"])as$r){if(in_array($_POST["fields"][$_POST["auto_increment_col"]]["orig"],$r["columns"],true)){$xa="";break;}if($r["type"]=="PRIMARY")$xa=" UNIQUE";}}return" AUTO_INCREMENT$xa";}function
alter_table($P,$y,array$l,array$Oc,$cb,$gc,$Ya,$wa,$Rf){$b=array();foreach($l
as$k){if($k[1]){$i=$k[1][3];if(preg_match('~ GENERATED~',$i)){$k[1][3]=(connection()->flavor=='maria'?"":$k[1][2]);$k[1][2]=$i;}$b[]=($P!=""?($k[0]!=""?"CHANGE ".idf_escape($k[0]):"ADD"):" ")." ".implode($k[1]).($P!=""?$k[2]:"");}else$b[]="DROP ".idf_escape($k[0]);}$b=array_merge($b,$Oc);$N=($cb!==null?" COMMENT=".q($cb):"").($gc?" ENGINE=".q($gc):"").($Ya?" COLLATE ".q($Ya):"").($wa!=""?" AUTO_INCREMENT=$wa":"");if($Rf){$Sf=array();if($Rf["partition_by"]=='RANGE'||$Rf["partition_by"]=='LIST'){foreach($Rf["partition_names"]as$t=>$W){$X=$Rf["partition_values"][$t];$Sf[]="\n  PARTITION ".idf_escape($W)." VALUES ".($Rf["partition_by"]=='RANGE'?"LESS THAN":"IN").($X!=""?" ($X)":" MAXVALUE");}}$N
.="\nPARTITION BY $Rf[partition_by]($Rf[partition])";if($Sf)$N
.=" (".implode(",",$Sf)."\n)";elseif($Rf["partitions"])$N
.=" PARTITIONS ".(+$Rf["partitions"]);}elseif($Rf===null)$N
.="\nREMOVE PARTITIONING";if($P=="")return
queries("CREATE TABLE ".table($y)." (\n".implode(",\n",$b)."\n)$N");if($P!=$y)$b[]="RENAME TO ".table($y);if($N)$b[]=ltrim($N);return($b?queries("ALTER TABLE ".table($P)."\n".implode(",\n",$b)):true);}function
alter_indexes($P,$b){$Ma=array();foreach($b
as$W)$Ma[]=($W[2]=="DROP"?"\nDROP INDEX ".idf_escape($W[1]):"\nADD $W[0] ".($W[0]=="PRIMARY"?"KEY ":"").($W[1]!=""?idf_escape($W[1])." ":"")."(".implode(", ",$W[2]).")");return
queries("ALTER TABLE ".table($P).implode(",",$Ma));}function
truncate_tables(array$R){return
apply_queries("TRUNCATE TABLE",$R);}function
drop_views(array$Di){return
queries("DROP VIEW ".implode(", ",array_map('Adminer\table',$Di)));}function
drop_tables(array$R){return
queries("DROP TABLE ".implode(", ",array_map('Adminer\table',$R)));}function
move_tables(array$R,array$Di,$Eh){$Cg=array();foreach($R
as$P)$Cg[]=table($P)." TO ".idf_escape($Eh).".".table($P);if(!$Cg||queries("RENAME TABLE ".implode(", ",$Cg))){$Fb=array();foreach($Di
as$P)$Fb[table($P)]=view($P);connection()->select_db($Eh);$h=idf_escape(DB);foreach($Fb
as$y=>$Ci){if(!queries("CREATE VIEW $y AS ".str_replace(" $h."," ",$Ci["select"]))||!queries("DROP VIEW $h.$y"))return
false;}return
true;}return
false;}function
copy_tables(array$R,array$Di,$Eh){queries("SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO'");foreach($R
as$P){$y=($Eh==DB?table("copy_$P"):idf_escape($Eh).".".table($P));if(($_POST["overwrite"]&&!queries("\nDROP TABLE IF EXISTS $y"))||!queries("CREATE TABLE $y LIKE ".table($P))||!queries("INSERT INTO $y SELECT * FROM ".table($P)))return
false;foreach(get_rows("SHOW TRIGGERS LIKE ".q(addcslashes($P,"%_\\")))as$G){$Yh=$G["Trigger"];list($nc,$pf)=trigger_event($G);if(!queries("CREATE TRIGGER ".($Eh==DB?idf_escape("copy_$Yh"):idf_escape($Eh).".".idf_escape($Yh))." $G[Timing] $nc".($pf!=""?" $pf":"")." ON $y FOR EACH ROW\n$G[Statement];"))return
false;}}foreach($Di
as$P){$y=($Eh==DB?table("copy_$P"):idf_escape($Eh).".".table($P));$Ci=view($P);if(($_POST["overwrite"]&&!queries("DROP VIEW IF EXISTS $y"))||!queries("CREATE VIEW $y AS $Ci[select]"))return
false;}return
true;}function
trigger_event(array$G){$oc=explode(",",$G["Event"]);$F=array();foreach(array("DELETE","INSERT","UPDATE")as$nc){if(in_array($nc,$oc))$F[]=$nc;}$F=implode(" OR ",$F);if(in_array("UPDATE",$oc)&&min_version('','12.0.1')&&preg_match('~\s(?:BEFORE|AFTER)\s+(.+?)\s+ON\s~is',get_val("SHOW CREATE TRIGGER ".idf_escape($G["Trigger"]),2),$x)&&preg_match('~\bOF\s+(.+)~is',$x[1],$pf))return
array("$F OF",$pf[1]);return
array($F,"");}function
trigger($y,$P){if($y=="")return
array();$H=get_rows("SHOW TRIGGERS WHERE `Trigger` = ".q($y));$F=reset($H);if($F)list($F["Event"],$F["Of"])=trigger_event($F);return$F;}function
triggers($P){$F=array();foreach(get_rows("SHOW TRIGGERS LIKE ".q(addcslashes($P,"%_\\")))as$G){list($nc)=trigger_event($G);$F[$G["Trigger"]]=array($G["Timing"],$nc);}return$F;}function
trigger_options(){return
array("Timing"=>array("BEFORE","AFTER"),"Event"=>(min_version('','12.0.1')?array("INSERT","UPDATE","UPDATE OF","DELETE","INSERT OR UPDATE","INSERT OR UPDATE OF","DELETE OR INSERT","DELETE OR UPDATE","DELETE OR UPDATE OF","DELETE OR INSERT OR UPDATE","DELETE OR INSERT OR UPDATE OF",):array("INSERT","UPDATE","DELETE")),"Type"=>array("FOR EACH ROW"),);}function
routine($y,$S){$H=get_rows("SELECT PARAMETER_NAME, DTD_IDENTIFIER, PARAMETER_MODE, COLLATION_NAME
FROM information_schema.PARAMETERS
WHERE SPECIFIC_SCHEMA = DATABASE() AND ROUTINE_TYPE = '$S' AND SPECIFIC_NAME = ".q($y)."
ORDER BY ORDINAL_POSITION");$l=array();foreach($H
as$G){$Yc=$G["DTD_IDENTIFIER"];list($di,$u,$mi)=parse_type($Yc);$l[]=array("field"=>$G["PARAMETER_NAME"],"type"=>$di,"length"=>$u,"unsigned"=>$mi,"null"=>true,"full_type"=>$Yc,"inout"=>($S=="FUNCTION"?"":$G["PARAMETER_MODE"]),"collation"=>$G["COLLATION_NAME"],);}$F=connection()->query("SELECT
	ROUTINE_COMMENT comment,
	CONCAT(IF(IS_DETERMINISTIC = 'YES', 'DETERMINISTIC\\n', ''), IF(SQL_DATA_ACCESS != 'CONTAINS SQL', CONCAT(SQL_DATA_ACCESS, '\\n'), ''), ROUTINE_DEFINITION) definition,
	'SQL' language
FROM information_schema.ROUTINES
WHERE ROUTINE_SCHEMA = DATABASE() AND ROUTINE_TYPE = '$S' AND ROUTINE_NAME = ".q($y))->fetch_assoc();if($l&&$l[0]['field']=='')$F['returns']=array_shift($l);$F['fields']=$l;return$F;}function
routines(){return
get_rows("SELECT SPECIFIC_NAME, ROUTINE_NAME, ROUTINE_TYPE, DTD_IDENTIFIER FROM information_schema.ROUTINES WHERE ROUTINE_SCHEMA = DATABASE()");}function
routine_languages(){return
array();}function
routine_id($y,array$G){return
idf_escape($y);}function
last_id($E){return
get_val("SELECT LAST_INSERT_ID()");}function
explain(Db$f,$D){return$f->query("EXPLAIN ".(min_version(5.7)?"":"PARTITIONS ").$D);}function
found_rows(array$Q,array$Z){return($Z||$Q["Engine"]!="InnoDB"?null:$Q["Rows"]);}function
create_sql($P,$wa,$uh){$F=get_val("SHOW CREATE TABLE ".table($P),1);if(!$wa)$F=preg_replace('~(\n\)[^\n]*?) AUTO_INCREMENT=\d+~','\1',$F);return$F;}function
truncate_sql($P){return"TRUNCATE ".table($P);}function
use_sql($Bb,$uh=""){$y=idf_escape($Bb);$F="";if(preg_match('~CREATE~',$uh)&&($tb=get_val("SHOW CREATE DATABASE $y",1))){set_utf8mb4($tb);if($uh=="DROP+CREATE")$F="DROP DATABASE IF EXISTS $y;\n";$F
.="$tb;\n";}return$F."USE $y";}function
trigger_sql($P){$F="";foreach(get_rows("SHOW TRIGGERS LIKE ".q(addcslashes($P,"%_\\")),null,"-- ")as$G){list($G["Event"],$G["Of"])=trigger_event($G);$F
.="\n".create_trigger(" ON ".table($G["Table"]),$G+array("Type"=>"FOR EACH ROW")).";\n";}return$F;}function
show_variables(){return
get_rows("SHOW VARIABLES");}function
show_status(){return
get_rows("SHOW STATUS");}function
process_list(){return
get_rows("SHOW FULL PROCESSLIST");}function
convert_field(array$k){return
driver()->convertColumn(idf_escape($k["field"]),$k);}function
unconvert_field(array$k,$F){if(preg_match("~binary~",$k["type"]))$F="UNHEX($F)";if($k["type"]=="bit")$F="CONVERT(b$F, UNSIGNED)";if($k["type"]=="vector")$F=(connection()->flavor=='maria'?"VEC_FromText":"STRING_TO_VECTOR")."($F)";if(preg_match("~geom|point|linestring|polygon~",$k["type"])){$jg=(min_version(8)?"ST_":"");$F=$jg."GeomFromText($F, $jg"."SRID($k[field]))";}return$F;}function
support($zc){return
preg_match('~^(comment|columns|copy|database|drop_col|dump|event|indexes|kill|privileges|move_col|procedure|processlist|routine|sql|status|table|trigger|variables|view'.(min_version(8)?'|descidx':'').(min_version('8.0.16','10.2.1')?'|check':'').(min_version(8,99)?'|fast_status':'').')$~',$zc);}function
kill_process($p){return
queries("KILL ".number($p));}function
connection_id(){return"SELECT CONNECTION_ID()";}function
max_connections(){return
get_val("SELECT @@max_connections");}function
types($vc=false){return
array();}function
type_values($p){return"";}function
type_definition($p){return
array("kind"=>"","definition"=>"");}function
schemas(){return
array();}function
get_schema(){return"";}function
set_schema($Lg,$g=null){return
true;}}define('Adminer\JUSH',Driver::$jush);define('Adminer\SERVER',"".$_GET[DRIVER]);define('Adminer\DB',"$_GET[db]");define('Adminer\ME',preg_replace('~\?.*~','',relative_uri()).'?'.(sid()?SID.'&':'').($_GET["ext"]?"ext=".url_escape($_GET["ext"]).'&':'').(isset($_GET[DRIVER])?DRIVER."=".url_escape(SERVER).'&':'').(isset($_GET["username"])?"username=".url_escape($_GET["username"]).'&':'').(isset($_GET["db"])?'db='.url_escape(DB).'&'.(isset($_GET["ns"])?"ns=".url_escape($_GET["ns"])."&":""):''));function
page_header($Mh,$j="",$Ia=array(),$Nh=""){page_headers();if(is_ajax()&&$j){page_messages($j);exit;}if(!ob_get_level())ob_start('ob_gzhandler',4096);$Oh=$Mh.($Nh!=""?": $Nh":"");$Ph=strip_tags($Oh.(SERVER!=""&&SERVER!="localhost"?h(" - ".SERVER):"")." - ".adminer()->name());echo'<!DOCTYPE html>
<html lang=\'',LANG,'\' dir=\'',lang(67),'\' class=\'',lang(67),' nojs\'>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="robots" content="noindex">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>',$Ph,'</title>
<link rel="stylesheet" href="',h(preg_replace("~\\?.*~","",ME)."?file=default.css&version=6.0.1"),'">
';$xb=adminer()->css();if(is_int(key($xb)))$xb=array_fill_keys($xb,'light');$md=in_array('light',$xb)||in_array('',$xb);$jd=in_array('dark',$xb)||in_array('',$xb);$_b=($md?($jd?null:false):($jd?:null));$Pe=" media='(prefers-color-scheme: dark)'";if($_b!==false)echo"<link rel='stylesheet'".($_b?"":$Pe)." href='".h(preg_replace("~\\?.*~","",ME)."?file=dark.css&version=6.0.1")."'>\n";echo"<meta name='color-scheme' content='".($_b===null?"light dark":($_b?"dark":"light"))."'>\n",script_src(preg_replace("~\\?.*~","",ME)."?file=functions.js&version=6.0.1");if(adminer()->head($_b))echo"<link rel='icon' href='data:image/gif;base64,"."R0lGODlhEAAQAJEAAAQCBPz+/PwCBAROZCH5BAEAAAAALAAAAAAQABAAAAI2hI+pGO1rmghihiUdvUBnZ3XBQA7f05mOak1RWXrNq5nQWHMKvuoJ37BhVEEfYxQzHjWQ5qIAADs='>\n","<link rel='apple-touch-icon' href='".h(preg_replace("~\\?.*~","",ME)."?file=logo.png&version=6.0.1")."'>\n";foreach($xb
as$qi=>$Ye){$c=($Ye=='dark'&&!$_b?$Pe:($Ye=='light'&&$jd?" media='(prefers-color-scheme: light)'":""));echo"<link rel='stylesheet'$c href='".h($qi)."'>\n";}echo"\n<body class='";adminer()->bodyClass();echo"'>\n",script((isset($_COOKIE["adminer_version"])||!adminer()->verifyVersion()?"":"onload = partial(verifyVersion, '".VERSION."');\n")."
const offlineMessage = '".js_escape(lang(68))."';
const thousandsSeparator = '".js_escape(lang(5))."';
const urlSeparators = '".js_escape(ini_get("arg_separator.input"))."';"),"<div id='help' class='jush-".JUSH." jsonly hidden'".on('mouseover','helpKeep').on('mouseout','helpMouseout')."></div>\n","<div id='content'>\n","<span id='menuopen' class='jsonly'".on('click','menuToggle')."><button title='".lang(69)."' class='icon icon-move' aria-expanded='false'></button></span>\n";if($Ia!==null){$w=substr(preg_replace('~\b(username|db|ns)=[^&]*&~','',ME),0,-1);echo'<p id="breadcrumb"><a href="'.h($w?:".").'">'.get_driver(DRIVER).'</a> » ';$w=substr(preg_replace('~\b(db|ns)=[^&]*&~','',ME),0,-1);$K=adminer()->serverName(SERVER);$K=($K!=""?$K:lang(70));if($Ia===false)echo"$K\n";else{echo"<a href='".h($w.(DB!=""&&support("single_db")?"&db=":""))."' accesskey='1' title='Alt+Shift+1'>$K</a> » ";if($_GET["ns"]!=""||(DB!=""&&is_array($Ia)))echo'<a href="'.h($w."&db=".url_escape(DB).(support("scheme")?"&ns=":"").(support("single_table")?"&select=":"")).'">'.h(DB).'</a> » ';if(is_array($Ia)){if($_GET["ns"]!="")echo'<a href="'.h(substr(ME,0,-1)).'">'.h($_GET["ns"]).'</a> » ';foreach($Ia
as$t=>$W){$Hb=(is_array($W)?$W[1]:h($W));if($Hb!="")echo"<a href='".h(ME."$t=").url_escape(is_array($W)?$W[0]:$W)."'>$Hb</a> » ";}}echo"$Mh\n";}}echo"<h2>$Oh</h2>\n","<div id='ajaxstatus' role='status' class='jsonly'></div>\n";restart_session();page_messages($j);$Cb=&get_session("dbs");if(DB!=""&&$Cb&&!in_array(DB,$Cb,true))$Cb=null;stop_session();define('Adminer\PAGE_HEADER',1);ob_flush();flush();}function
page_headers(){header("Content-Type: text/html; charset=utf-8");header("Cache-Control: no-cache");header("X-Frame-Options: deny");header("X-XSS-Protection: 0");header("X-Content-Type-Options: nosniff");header("Referrer-Policy: origin-when-cross-origin");foreach(adminer()->csp(csp())as$wb){$qd=array();foreach($wb
as$t=>$W)$qd[]="$t $W";header("Content-Security-Policy: ".implode("; ",$qd));}adminer()->headers();}function
csp(){return
array(array("script-src"=>"'self' 'unsafe-inline' 'nonce-".get_nonce()."' 'strict-dynamic'","connect-src"=>"'self' https://www.adminer.org","frame-src"=>"https://www.adminer.org","object-src"=>"'none'","base-uri"=>"'none'","form-action"=>"'self'",),);}function
design_checksums(){$vi=array();foreach(array_keys(adminer()->css())as$qi)$vi[preg_replace('~\?.*~','',$qi)]=true;$F=array();foreach(array("adminer.css","adminer-dark.css")as$m){if($vi[$m]&&file_exists($m)){preg_match('~^/\* Adminer design ([-\w]+) \*/~',file_get_contents($m),$x);$F[$m]=array((string)$x[1],Plugins::checksum($m));}}return$F;}function
official_design_checksums(){return
array('adminer-border/adminer-dark.css'=>'b2527e3','adminer-border/adminer.css'=>'430977ad','adminer-dark/adminer-dark.css'=>'a26bcd7b','brade/adminer.css'=>'be4161f0','bueltge/adminer.css'=>'1a8f00b4','dracula/adminer-dark.css'=>'cfaf61dd','esterka/adminer.css'=>'1f805f36','flat/adminer.css'=>'49a61af9','galkaev/adminer-dark.css'=>'16c46f94','haeckel/adminer.css'=>'147a3565','hever/adminer.css'=>'1f626deb','konya/adminer.css'=>'2b409696','lavender-light/adminer.css'=>'bf03f5d7','lucas-sandery/adminer.css'=>'6596353','mancave/adminer-dark.css'=>'e1ac813d','mvt/adminer.css'=>'ebd3afdc','nette/adminer.css'=>'5ab360e7','ng9/adminer.css'=>'488583cf','nicu/adminer.css'=>'ecb9bd1e','pappu687/adminer.css'=>'b58d128c','paranoiq/adminer.css'=>'64d27e5','pepa-linha/adminer.css'=>'baf25f0','pokorny/adminer.css'=>'ee9eea6d','price/adminer.css'=>'81be9a85','rmsoft/adminer.css'=>'6cd4a237','rmsoft_blue-dark/adminer.css'=>'32102a8','rmsoft_blue/adminer.css'=>'7d8d5b18','win98/adminer.css'=>'e82d63c3',);}function
version_iframe(){return(isset($_COOKIE["adminer_version"])||!adminer()->verifyVersion()?"":"<noscript><iframe sandbox src='https://www.adminer.org/version/?current=".VERSION."&amp;noscript=1'></iframe></noscript>");}function
get_nonce(){static$kf;if(!$kf)$kf=base64_encode(rand_string());return$kf;}function
page_messages($j){$pi=preg_replace('~^[^?]*~','',$_SERVER["REQUEST_URI"]);$Se=idx($_SESSION["messages"],$pi);if($Se){echo"<div class='message'>".implode("</div>\n<div class='message'>",$Se)."</div>".script("messagesPrint();");unset($_SESSION["messages"][$pi]);}if($j)echo"<div class='error'>$j</div>\n";if(adminer()->error)echo"<div class='error'>".adminer()->error."</div>\n";}function
page_footer($Xe=""){echo"</div>\n\n<div id='foot' class='foot'>\n<div id='menu'>\n";adminer()->navigation($Xe);echo"</div>\n";if($Xe!="auth")echo'<form action="" method="post">
<p class="logout">
<span title="',lang(38),'">',h($_GET["username"])."\n",'</span>
<input type=\'submit\' name=\'logout\' value=\'',lang(71),'\' id=\'logout\'>
',input_token(),'</form>
';echo"</div>\n\n",script("setupSubmitHighlight(document);");}function
int32($ef){while($ef>=2147483648)$ef-=4294967296;while($ef<=-2147483649)$ef+=4294967296;return(int)$ef;}function
long2str(array$V,$Fi){$Kg='';foreach($V
as$W)$Kg
.=pack('V',$W);if($Fi)return
substr($Kg,0,end($V));return$Kg;}function
str2long($Kg,$Fi){$V=array_values(unpack('V*',str_pad($Kg,4*ceil(strlen($Kg)/4),"\0")));if($Fi)$V[]=strlen($Kg);return$V;}function
xxtea_mx($Ni,$Mi,$wh,$de){return
int32((($Ni>>5&0x7FFFFFF)^$Mi<<2)+(($Mi>>3&0x1FFFFFFF)^$Ni<<4))^int32(($wh^$Mi)+($de^$Ni));}function
encrypt_string($th,$t){if($th=="")return"";$t=array_values(unpack("V*",pack("H*",md5($t))));$V=str2long($th,true);$ef=count($V)-1;$Ni=$V[$ef];$Mi=$V[0];$rg=floor(6+52/($ef+1));$wh=0;while($rg-->0){$wh=int32($wh+0x9E3779B9);$Zb=$wh>>2&3;for($Jf=0;$Jf<$ef;$Jf++){$Mi=$V[$Jf+1];$df=xxtea_mx($Ni,$Mi,$wh,$t[$Jf&3^$Zb]);$Ni=int32($V[$Jf]+$df);$V[$Jf]=$Ni;}$Mi=$V[0];$df=xxtea_mx($Ni,$Mi,$wh,$t[$Jf&3^$Zb]);$Ni=int32($V[$ef]+$df);$V[$ef]=$Ni;}return
long2str($V,false);}function
decrypt_string($th,$t){if($th=="")return"";if(!$t)return
false;$t=array_values(unpack("V*",pack("H*",md5($t))));$V=str2long($th,false);$ef=count($V)-1;$Ni=$V[$ef];$Mi=$V[0];$rg=floor(6+52/($ef+1));$wh=int32($rg*0x9E3779B9);while($wh){$Zb=$wh>>2&3;for($Jf=$ef;$Jf>0;$Jf--){$Ni=$V[$Jf-1];$df=xxtea_mx($Ni,$Mi,$wh,$t[$Jf&3^$Zb]);$Mi=int32($V[$Jf]-$df);$V[$Jf]=$Mi;}$Ni=$V[$ef];$df=xxtea_mx($Ni,$Mi,$wh,$t[$Jf&3^$Zb]);$Mi=int32($V[0]-$df);$V[0]=$Mi;$wh=int32($wh-0x9E3779B9);}return
long2str($V,true);}$Zf=array();if($_COOKIE["adminer_permanent"]){foreach(explode(" ",$_COOKIE["adminer_permanent"])as$W){list($t)=explode(":",$W);$Zf[$t]=$W;}}function
add_invalid_login(){$Ca=get_temp_dir()."/adminer-invalid";foreach(glob("$Ca*")?:array($Ca)as$m){$Vc=file_open_lock($m);if($Vc)break;}if(!$Vc)$Vc=file_open_lock("$Ca-".rand_string());if(!$Vc)return;$Ud=json_decode(stream_get_contents($Vc),true);$Jh=time();if($Ud){foreach($Ud
as$Vd=>$W){if($W[0]<$Jh)unset($Ud[$Vd]);}}$Sd=&$Ud[adminer()->bruteForceKey()];if(!$Sd)$Sd=array($Jh+30*60,0);$Sd[1]++;file_write_unlock($Vc,json_encode($Ud));}function
check_invalid_login(array&$Zf){$Ud=array();foreach(glob(get_temp_dir()."/adminer-invalid*")as$m){$Vc=file_open_lock($m);if($Vc){$Ud=json_decode(stream_get_contents($Vc),true);file_unlock($Vc);break;}}$t=adminer()->bruteForceKey();$Sd=idx($Ud,$t,array());$jf=($Sd[1]>29?$Sd[0]-time():0);if($jf>0){$j=lang(72,ceil($jf/60));if($_SERVER["HTTP_X_FORWARDED_FOR"]!=""&&$t==$_SERVER["REMOTE_ADDR"])$j
.='<br>'.lang(73,'<b>login-reverse-proxy</b>'," href='https://www.adminer.org/plugins/?version=".VERSION."'".target_blank());auth_error($j,$Zf,false);}}function
password_required(){static$F;if($F===null){$F=(bool)get_session("password_required");if(!$F){$vb=adminer()->credentials();$F=!is_object(Driver::connect($vb[0],$vb[1],""));if($F)set_session("password_required",true);}}return$F;}function
require_password_link($B){$Ze="<a href='https://www.adminer.org/password/'".target_blank().">".lang(74)."</a>";if(!function_exists('password_hash'))return" $Ze";$bg=($B!==null?$B:base64_encode(substr(pack("H*",rand_string()),0,12)));$pd=password_hash($bg,PASSWORD_DEFAULT);$m="adminer-plugins.php";$sc=file_exists("adminer-plugins.php");if($sc)$Rd=($B!==null?lang(75,"<b>$m</b>"):lang(76,"<b>$m</b>","<b>$bg</b>"));else{$m="<button name='password_less' value='".h($pd)."' class='link'>$m</button>";$Rd=($B!==null?lang(77,$m):lang(78,$m,"<b>$bg</b>"));}$ue="\t<a>new</a> Adminer\\Password(<span class='jush-apo'>'".h($pd)."'</span>),";$F="<p>$Rd
<pre><code class='jush'>".($sc?$ue:"&lt;?php\n<a>return</a> <a>array</a>(\n$ue\n);")."</code></pre>
<p>$Ze
";return" <a href='#password-less' class='toggle'>".lang(79)."</a>
<div id='password-less' class='hidden'>".($sc?$F:"<form action='' method='post'>\n".$F.input_token()."</form>")."</div>";}if(preg_match('~^[-\w$./]+$~',$_POST["password_less"])&&verify_token()){header("Content-Type: application/octet-stream");header("Content-Disposition: attachment; filename=adminer-plugins.php");echo"<?php\nreturn array(\n\tnew Adminer\\Password('$_POST[password_less]'),\n);\n";exit;}$va=$_POST["auth"];if($va&&verify_token()){session_regenerate_id();$Ai=$va["driver"];$K=$va["server"];$U=$va["username"];$B=(string)$va["password"];$h=$va["db"];set_password($Ai,$K,$U,$B);$_SESSION["db"][$Ai][$K][$U][$h]=true;if($va["permanent"]){$t=implode("-",array_map('base64_encode',array($Ai,$K,$U,$h)));$ng=adminer()->permanentLogin(true);$Zf[$t]="$t:".base64_encode($ng?encrypt_string($B,$ng):"");cookie("adminer_permanent",implode(" ",$Zf));}if(!array_diff(array_keys($_POST),array("auth","token"))||$Ai!=DRIVER||$K!=SERVER||$U!==$_GET["username"]||$h!=DB)redirect(auth_url($Ai,$K,$U,$h));}elseif($_POST["logout"]&&(!$_SESSION["token"]||verify_token())){foreach(array("pwds","db","dbs","queries")as$t)set_session($t,null);unset_permanent($Zf);redirect(substr(preg_replace('~\b(username|db|ns)=[^&]*&~','',ME),0,-1),lang(80).' '.lang(81));}elseif($Zf&&!$_SESSION["pwds"]){session_regenerate_id();$ng=adminer()->permanentLogin();foreach($Zf
as$t=>$W){list(,$Ta)=explode(":",$W);list($Ai,$K,$U,$h)=array_map('base64_decode',explode("-",$t));set_password($Ai,$K,$U,decrypt_string(base64_decode($Ta),$ng));$_SESSION["db"][$Ai][$K][$U][$h]=true;}}function
unset_permanent(array&$Zf){foreach($Zf
as$t=>$W){list($Ai,$K,$U,$h)=array_map('base64_decode',explode("-",$t));if($Ai==DRIVER&&$K==SERVER&&$U==$_GET["username"]&&$h==DB)unset($Zf[$t]);}cookie("adminer_permanent",implode(" ",$Zf));}function
auth_error($j,array&$Zf,$Td=true){$bh=session_name();if(isset($_GET["username"])){header("HTTP/1.1 403 Forbidden");if(($_COOKIE[$bh]||$_GET[$bh])&&!$_SESSION["token"])$j=lang(82);elseif($Td&&($B=get_password())!==null){restart_session();add_invalid_login();if($B===false)$j
.=($j?'<br>':'').lang(83,target_blank(),'<code>permanentLogin()</code>');set_password(DRIVER,SERVER,$_GET["username"],null);unset_permanent($Zf);}}if(!$_COOKIE[$bh]&&$_GET[$bh]&&ini_bool("session.use_only_cookies"))$j=lang(84);$Nf=session_get_cookie_params();cookie("adminer_key",($_COOKIE["adminer_key"]?:rand_string()),$Nf["lifetime"]);if(!$_SESSION["token"])$_SESSION["token"]=rand(1,1e6);page_header(lang(40),$j,null);echo"<form action='' method='post'>\n","<div>";if(hidden_fields($_POST,array("auth")))echo"<p class='message'>".lang(85)."\n";echo
input_token(),"</div>\n";adminer()->loginForm();echo"</form>\n";page_footer("auth");exit;}if(isset($_GET["username"])&&!class_exists('Adminer\Db')){unset($_SESSION["pwds"][DRIVER]);unset_permanent($Zf);page_header(lang(86),lang(87,implode(", ",Driver::$extensions)),false);page_footer("auth");exit;}$f='';if(isset($_GET["username"])&&is_string(get_password())){check_invalid_login($Zf);$vb=adminer()->credentials();$f=Driver::connect($vb[0],$vb[1],$vb[2]);if(is_object($f)){Db::$instance=$f;Driver::$instance=new
Driver($f);if($f->flavor)save_settings(array("vendor-".DRIVER."-".SERVER=>get_driver(DRIVER)));}}$_e=null;if(!is_object($f)||($_e=adminer()->login($_GET["username"],get_password()))!==true){$j=(is_string($f)?nl_br(h($f)):(is_string($_e)?$_e:lang(88))).(preg_match('~^ | $~',get_password())?'<br>'.lang(89):'');auth_error($j,$Zf);}if($_POST["logout"]&&$_SESSION["token"]&&!verify_token()){page_header(lang(71),lang(90));page_footer("db");exit;}if(!$_SESSION["token"])$_SESSION["token"]=rand(1,1e6);stop_session(true);if($va&&$_POST["token"])$_POST["token"]=get_token();$j='';if($_POST){if(!verify_token()){header("HTTP/1.1 403 Forbidden");$j=lang(90).' '.lang(91);}}elseif($_SERVER["REQUEST_METHOD"]=="POST"){header("HTTP/1.1 413 Content Too Large");$j=lang(92,"<b>post_max_size</b>");if(isset($_GET["sql"]))$j
.=' '.lang(93);}function
doc_link(array$Wf,$Gh=""){return"";}function
like_bool(array$k){return$k["type"]=="bool"||(preg_match('~bit|tinyint~',$k["type"])&&$k["length"]==1);}function
sql_comment($D,$Jh=""){return"<!--\n".str_replace("--","--><!-- ",$D)."\n".($Jh!=""?"($Jh)\n":"")."-->\n";}connection()->select_db(adminer()->database());if(support("scheme")){$Lg=get_schema();if($Lg!="")set_schema($Lg);}adminer()->afterConnect();add_driver(DRIVER,lang(40));if(isset($_GET["select"])&&($_POST["edit"]||$_POST["clone"])&&!$_POST["save"])$_GET["edit"]=$_GET["select"];if(isset($_GET["download"])){$a=$_GET["download"];$l=fields($a);header("Content-Type: application/octet-stream");header("Content-Disposition: attachment; filename=".friendly_url("$a-".implode("_",$_GET["where"])).".".friendly_url($_GET["field"]));$I=array(idf_escape($_GET["field"]));$E=driver()->select($a,$I,array(where($_GET,$l)),$I);$G=($E?$E->fetch_row():array());echo
driver()->value($G[0],$l[$_GET["field"]]);exit;}elseif(isset($_GET["edit"])){$a=$_GET["edit"];$l=fields($a);$Z=(isset($_GET["select"])?($_POST["check"]&&count($_POST["check"])==1?where_check($_POST["check"][0],$l):""):where($_GET,$l));$T=(isset($_GET["select"])?$_POST["edit"]:$Z);foreach($l
as$y=>$k){if((!$T&&!isset($k["privileges"]["insert"]))||adminer()->fieldName($k)=="")unset($l[$y]);}if($_POST&&!$j&&!isset($_GET["select"])){$ze=relative_uri((string)$_POST["referer"]);if($_POST["insert"])$ze=($T?null:relative_uri());elseif(!preg_match('~^.+&select=.+$~',$ze))$ze=ME."select=".url_escape($a);$s=indexes($a);$ii=unique_array($_GET["where"],$s);$ug="\nWHERE $Z";if(isset($_POST["delete"]))queries_redirect($ze,lang(94),driver()->delete($a,$ug,$ii?0:1));else{$L=array();foreach($l
as$y=>$k){$W=process_input($k);if($W!==false&&$W!==null)$L[idf_escape($y)]=$W;}if($T){if(!$L)redirect($ze);queries_redirect($ze,lang(95),driver()->update($a,$L,$ug,$ii?0:1));if(is_ajax()){page_headers();page_messages($j);exit;}}else{$E=driver()->insert($a,$L);$oe=($E?last_id($E):0);queries_redirect($ze,lang(96,($oe?" $oe":"")),$E);}}}$G=null;$D="";$Jh="";if($Z){$I=array();$Qg=array("*");foreach($l
as$y=>$k){if(isset($k["privileges"]["select"])){$ta=($_POST["clone"]&&$k["auto_increment"]?"''":convert_field($k));$d=($ta?"$ta AS ":"").idf_escape($y);$I[]=$d;if($ta)$Qg[]=$d;}}$G=array();if(!support("table")){$I=array("*");$Qg=$I;}if($I){$qh=microtime(true);$E=driver()->select($a,$I,array($Z),$I,array(),(isset($_GET["select"])?2:1));$D=str_replace("SELECT ".implode(", ",$I),"SELECT ".implode(", ",$Qg),driver()->query);$Jh=format_time($qh);if(!$E)$j=adminer()->error();else{$G=$E->fetch_assoc();if(!$G)$G=false;}if(isset($_GET["select"])&&(!$G||$E->fetch_assoc()))$G=null;}}if(!$l&&driver()->primary!=""){if(!$Z){$E=driver()->select($a,array("*"),array(),array("*"));$G=($E?$E->fetch_assoc():false);if(!$G)$G=array(driver()->primary=>"");}if($G){foreach($G
as$t=>$W){if(!$Z)$G[$t]=null;$l[$t]=array("field"=>$t,"null"=>($t!=driver()->primary),"auto_increment"=>($t==driver()->primary));}}}if($_POST["save"]){$gg=array();foreach((array)$_POST["fields"]as$t=>$W)$gg[bracket_escape($t,true)]=$W;$G=$gg+($G?$G:array());}edit_form($a,$l,$G,$T,$j,$D,$Jh);}elseif(isset($_GET["select"])){$a=$_GET["select"];$Q=table_status1($a);$s=indexes($a);$l=fields($a);$Sc=column_foreign_keys($a);$rf=$Q["Oid"];$ja=get_settings("adminer_import");$Ig=array();$e=array();$Ng=array();$Af=array();$Hh=null;foreach($l
as$t=>$k){$y=adminer()->fieldName($k);$ff=html_entity_decode(strip_tags($y),ENT_QUOTES);if(isset($k["privileges"]["select"])&&$y!=""){$e[$t]=$ff;if(is_shortable($k))$Hh=adminer()->selectLengthProcess();}if(isset($k["privileges"]["where"])&&$y!="")$Ng[$t]=$ff;if(isset($k["privileges"]["order"])&&$y!="")$Af[$t]=$ff;$Ig+=$k["privileges"];}list($I,$dd)=adminer()->selectColumnsProcess($e,$s);$I=array_unique($I);$dd=array_unique($dd);$Xd=count($dd)<count($I);$Z=adminer()->selectSearchProcess($l,$s);$_f=adminer()->selectOrderProcess($l,$s);$v=adminer()->selectLimitProcess();if($_GET["val"]&&is_ajax()){header("Content-Type: text/plain; charset=utf-8");foreach($_GET["val"]as$ji=>$G){$ta=convert_field($l[key($G)]);$I=array($ta?:idf_escape(key($G)));$Z[]=where_check(bracket_escape($ji,true),$l);$F=driver()->select($a,$I,$Z,$I);if($F)echo
first($F->fetch_row());}exit;}$C=$li=array();foreach($s
as$r){if($r["type"]=="PRIMARY"){$C=array_flip($r["columns"]);$li=($I?$C:array());foreach($li
as$t=>$W){if(in_array(idf_escape($t),$I))unset($li[$t]);}break;}}if($rf&&!$C){$C=$li=array($rf=>0);$s[]=array("type"=>"PRIMARY","columns"=>array($rf));}if($_POST&&!$j){$Ji=$Z;if(!$_POST["all"]&&is_array($_POST["check"])){$Sa=array();foreach($_POST["check"]as$Pa)$Sa[]=where_check($Pa,$l);$Ji[]="((".implode(") OR (",$Sa)."))";}$Li=$Ji;$Ji=($Ji?"\nWHERE ".implode(" AND ",$Ji):"");if($_POST["export"]){save_settings(array("output"=>$_POST["output"],"format"=>$_POST["format"]),"adminer_import");dump_headers($a);adminer()->dumpTable($a,"");$Pg=($I?:array("*"));$pb=convert_fields($e,$l,$I);if($pb)$Pg[]=substr($pb,2);$D="";if(is_array($_POST["check"])&&!$C){$Xc=implode(", ",$Pg)."\nFROM ".table($a);$fd=($dd&&$Xd?"\nGROUP BY ".implode(", ",$dd):"").($_f?"\nORDER BY ".implode(", ",$_f):"");$hi=array();foreach($_POST["check"]as$W)$hi[]="(SELECT".limit($Xc,"\nWHERE ".($Z?implode(" AND ",$Z)." AND ":"").where_check($W,$l).$fd,1).")";$D=implode(" UNION ALL ",$hi);}adminer()->dumpData($a,"table",$D,$Pg,$Li,($Xd?$dd:array()),$_f);adminer()->dumpFooter();exit;}if(!adminer()->selectEmailProcess($Z,$Sc)){if($_POST["save"]||$_POST["delete"]){$E=true;$ka=0;$Ea=false;$L=array();if(!$_POST["delete"]){foreach($l
as$y=>$W){$q=bracket_escape($y);if(isset($_POST["fields"][$q])||$_FILES["fields-$q"]){$W=process_input($l[$y]);if($W!==null&&($_POST["clone"]||$W!==false))$L[idf_escape($y)]=($W!==false?$W:idf_escape($y));}}}if($_POST["delete"]||$L){$D=($_POST["clone"]?"INTO ".table($a)." (".implode(", ",array_keys($L)).")\nSELECT ".implode(", ",$L)."\nFROM ".table($a):"");if($_POST["all"]||($C&&is_array($_POST["check"]))||$Xd){$E=($_POST["delete"]?driver()->delete($a,$Ji):($_POST["clone"]?queries("INSERT $D$Ji".driver()->insertReturning($a)):driver()->update($a,$L,$Ji)));$ka=connection()->affected_rows;if(is_object($E))$ka+=$E->num_rows;}else{$Ea=count((array)$_POST["check"])>1&&driver()->begin();foreach((array)$_POST["check"]as$W){$Ii="\nWHERE ".($Z?implode(" AND ",$Z)." AND ":"").where_check($W,$l);$E=($_POST["delete"]?driver()->delete($a,$Ii,1):($_POST["clone"]?queries("INSERT".limit1($a,$D,$Ii)):driver()->update($a,$L,$Ii,1)));if(!$E)break;$ka+=connection()->affected_rows;}if($Ea&&$E&&!driver()->commit())$E=false;}}$Re=lang(97,$ka);if($_POST["clone"]&&$E&&$ka==1){$oe=last_id($E);if($oe)$Re=lang(96," $oe");}queries_redirect(remove_from_uri($_POST["all"]&&$_POST["delete"]?"page|next":""),$Re,$E);if($Ea)driver()->rollback();if(!$_POST["delete"]){$gg=(array)$_POST["fields"];edit_form($a,array_intersect_key($l,$gg),$gg,!$_POST["clone"],$j);page_footer();exit;}}elseif(!$_POST["import"]){$E=true;$ka=0;$Ea=count((array)$_POST["val"])>1&&driver()->begin();foreach((array)$_POST["val"]as$ji=>$G){$L=array();foreach($G
as$t=>$W){$t=bracket_escape($t,true);$L[idf_escape($t)]=(preg_match('~char|text~',$l[$t]["type"])||$W!=""?adminer()->processInput($l[$t],$W):"NULL");}$E=driver()->update($a,$L," WHERE ".($Z?implode(" AND ",$Z)." AND ":"").where_check(bracket_escape($ji,true),$l),($Xd||$C?0:1)," ");if(!$E)break;$ka+=connection()->affected_rows;}if($Ea)$E=$E&&driver()->commit();queries_redirect(remove_from_uri(),lang(97,$ka),$E);if($Ea)driver()->rollback();}elseif(!is_string($Bc=get_file("csv_file",true)))$j=upload_error($Bc);elseif(!preg_match('~~u',$Bc))$j=lang(98);else{save_settings(array("output"=>$ja["output"],"format"=>$_POST["separator"]),"adminer_import");$ab=array_keys($l);$J=($_POST["separator"]=="csv"?",":($_POST["separator"]=="tsv"?"\t":";"));$yb=parse_csv($Bc,$J);$ka=count($yb);driver()->begin();$H=array();foreach($yb
as$t=>$Y){if(!$t&&!array_diff($Y,$ab)){$ab=$Y;$ka--;}else{$L=array();foreach($Y
as$o=>$Xa)$L[idf_escape($ab[$o])]=($Xa==""&&$l[$ab[$o]]["null"]?"NULL":q(csv_value($Xa)));$H[]=$L;}}$E=(!$H||driver()->insertUpdate($a,$H,$C));if($E)driver()->commit();queries_redirect(remove_from_uri("page|next"),lang(99,$ka),$E);driver()->rollback();}}}$Bh=adminer()->tableName($Q);if(is_ajax()){page_headers();ob_start();}else
page_header(lang(56).": $Bh",$j);$L=null;if(isset($Ig["insert"])||!support("table")){$L="";foreach((array)$_GET["where"]as$W){$X=$W["val"];if(is_array($X))$X=(count($X)==1&&preg_match('~^val-(.*)~s',reset($X),$x)?$x[1]:"");if($W["col"]!=""&&$X!=""&&($W["op"]=="="||(!$W["op"]&&(is_array($W["val"])||!preg_match('~[_%]~',$X)))))$L
.="&set[".url_escape(bracket_escape($W["col"]))."]=".url_escape($X);}}adminer()->selectLinks($Q,$L);if(!$e&&support("table"))echo"<p class='error'>".lang(100).($l?".":": ".adminer()->error())."\n";else{echo"<form action='' id='form'>\n","<div hidden>";hidden_fields_get();echo(DB!=""?input_hidden("db",DB).(isset($_GET["ns"])?input_hidden("ns",$_GET["ns"]):""):""),input_hidden("select",$a),"</div>\n";adminer()->selectColumnsPrint($I,$e);adminer()->selectSearchPrint($Z,$Ng,$s);adminer()->selectOrderPrint($_f,$Af,$s);adminer()->selectLimitPrint($v);if($Hh!==null)adminer()->selectLengthPrint($Hh);adminer()->selectActionPrint($s);echo"</form>\n";foreach((array)$_GET["where"]as$W){if($W["op"]=="SQL"&&!in_array($_SERVER["HTTP_SEC_FETCH_SITE"],array("","same-origin"))){echo"<p class='error'>".lang(90).' '.lang(91)."\n";page_footer();exit;}}$A=$_GET["page"];$Uc=null;if($A=="last"){$Uc=get_val(count_rows($a,$Z,$Xd,$dd));$A=floor(max(0,intval($Uc)-1)/$v);}$Og=$I;$ed=$dd;if(!$Og){$Og[]="*";$pb=convert_fields($e,$l,$I);if($pb)$Og[]=substr($pb,2);}foreach($I
as$t=>$W){$k=$l[idf_unescape($W)];if($k&&($ta=convert_field($k)))$Og[$t]="$ta AS $W";}if(JUSH=="pgsql"||JUSH=="mssql"){foreach((array)$_GET["columns"]as$t=>$W){if(isset($Og[$t])&&$W["fun"])$Og[$t].=" AS ".idf_escape(apply_sql_function($W["fun"],($W["col"]!=""?$W["col"]:"*")));}}if(!$Xd&&$li){foreach($li
as$t=>$W){$Og[]=idf_escape($t);if($ed)$ed[]=idf_escape($t);}}$E=driver()->select($a,$Og,$Z,$ed,$_f,$v,$A,true);if(!is_object($E))echo"<p class='error'>".(adminer()->error()?:lang(25))."\n";else{if(JUSH=="mssql"&&$A)$E->seek($v*$A);$ec=array();$H=array();while($G=$E->fetch_assoc()){if($A&&JUSH=="oracle")unset($G["RNUM"]);$H[]=$G;}$nd=($v&&(support("cursor")?$_GET["next"]!="":count($H)>=$v));if(is_ajax()&&$nd)header("X-Next-Page: ".pagination_href($A+1));if($_GET["modify"]&&$H){$Ke=max_input_vars(count($H[0])+1,20);echo($Ke&&count($H)>$Ke?"<p class='error'>".max_input_vars_error()."\n":"");}echo"<form action='' method='post' enctype='multipart/form-data'".on_upload_progress($oi).">\n";if($_GET["page"]!="last"&&$v&&$dd&&$Xd&&JUSH=="sql")$Uc=get_val(" SELECT FOUND_ROWS()");if(!$H)echo"<p class='message'>".lang(15)."\n";else{$Ba=adminer()->backwardKeys($a,$Bh);echo"<div class='scrollable'>","<table id='table' class='nowrap checkable odds'".on('click','tableClick').on('dblclick','tableClick').on('keydown','editingKeydown').">\n","<thead><tr>".(!$dd&&$I?"":"<td class='hover check'><input type='checkbox' id='all-page' class='jsonly' title='".lang(101)."'".on('click','formCheck','^check').">");$gf=array();$ad=array();reset($I);$xg=1;foreach($H[0]as$t=>$W){if(!isset($li[$t])){$W=idx($_GET["columns"],key($I))?:array();$k=$l[$I?($W?$W["col"]:current($I)):$t];$y=($k?adminer()->fieldName($k,$xg):($W["fun"]?"*":h($t)));if($y!=""){$xg++;$gf[$t]=$y;$d=idf_escape($t);$xd=remove_from_uri('(order|desc)[^=]*|page|next').'&order[0]='.url_escape($t);$Hb="&desc[0]=1";$ih=preg_replace('~ DESC( NULLS LAST)?$~','',$_f[0]);$kh=($ih==$d||$ih==$t);echo"<th id='th[".h(bracket_escape($t))."]'".($kh?" aria-sort='".($ih==$_f[0]?"ascending":"descending")."'":"").">";$Zc=apply_sql_function($W["fun"],$y);$jh=isset($k["privileges"]["order"])||$Zc!=$y;echo($jh?"<a href='".h($xd.($kh&&$ih==$_f[0]?$Hb:''))."'>$Zc</a>":$Zc);$Qe=($jh?"<a href='".h($xd.$Hb)."' title='".lang(102)."' class='text'> ↓</a>":'');if(!$W["fun"]&&isset($k["privileges"]["where"]))$Qe
.="<a href='#fieldset-search' title='".lang(50)."' class='text jsonly'".on('click','selectSearch',$t)."> =</a>";echo($Qe?"<span class='column'>$Qe</span>":"");}$ad[$t]=$W["fun"];next($I);}}$se=array();if($_GET["modify"]){foreach($H
as$G){foreach($G
as$t=>$W)$se[$t]=max($se[$t],min(40,strlen(utf8_decode($W))));}}echo($Ba?"<th>".lang(103):"")."<tbody>\n";if(is_ajax())ob_end_clean();foreach(adminer()->rowDescriptions($H,$Sc)as$ef=>$G){$ii=unique_array($H[$ef],$s);if(!$ii){$ii=array();reset($I);foreach($H[$ef]as$t=>$W){if(!preg_match('~^(COUNT|AVG|GROUP_CONCAT|MAX|MIN|SUM)\(~',current($I)))$ii[$t]=$W;next($I);}}$ji="";foreach($ii
as$t=>$W){$k=(array)$l[$t];$Wd=is_blob($k);if((JUSH=="sql"||JUSH=="pgsql")&&($Wd||preg_match('~'.text_type().'~',$k["type"]))&&strlen($W)>64){$t=(strpos($t,'(')?$t:idf_escape($t));$t="MD5(".($Wd||JUSH!='sql'||preg_match("~^utf8~",$k["collation"])?$t:"CONVERT($t USING ".charset(connection()).")").")";$W=md5($Wd?(string)driver()->value($W,$k):$W);}$ji
.="&".($W!==null?"where[".url_escape(bracket_escape($t))."]=".url_escape($W===false?"f":$W):"null[]=".url_escape($t));}echo"<tr>".(!$dd&&$I?"":"<td class='hover check'>".($Xd||information_schema(DB)?"":"<a href='".h(ME."edit=".url_escape($a).$ji)."' class='edit'>".lang(104)."</a> ").checkbox("check[]",substr($ji,1),in_array(substr($ji,1),(array)$_POST["check"])));reset($I);foreach($G
as$t=>$W){if(isset($gf[$t])){$d=current($I);$k=(array)$l[$t];if($W!=""&&(!isset($ec[$t])||$ec[$t]!=""))$ec[$t]=(is_mail($W)?$gf[$t]:"");$w="";if(is_blob($k)&&$W!="")$w=ME.'download='.url_escape($a).'&field='.url_escape($t).$ji;if(!$w&&$W!==null){foreach((array)$Sc[$t]as$Rc){if(count($Sc[$t])==1||end($Rc["source"])==$t){$w="";foreach($Rc["source"]as$o=>$lh)$w
.=where_link($o,$Rc["target"][$o],$H[$ef][$lh]);$w=($Rc["db"]!=""?preg_replace('~([?&]db=)[^&]+~','\1'.url_escape($Rc["db"]),ME):ME).'select='.url_escape($Rc["table"]).$w;if($Rc["ns"])$w=preg_replace('~([?&]ns=)[^&]+~','\1'.url_escape($Rc["ns"]),$w);if(count($Rc["source"])==1)break;}}}if($d=="COUNT(*)"){$w=ME."select=".url_escape($a);$o=0;foreach((array)$_GET["where"]as$V){if(!array_key_exists($V["col"],$ii))$w
.=where_link($o++,$V["col"],$V["val"],$V["op"]);}foreach($ii
as$de=>$V)$w
.=where_link($o++,$de,$V);}$yd=select_value($W,$w,$k,$Hh);$q=bracket_escape($ji);$p=h("val[$q][".bracket_escape($t)."]");$ig=idx(idx($_POST["val"],$q),bracket_escape($t));$T=idx($k["privileges"],"update");$bc=!is_array($G[$t])&&!is_blob($k)&&is_utf8($W)&&$H[$ef][$t]==$W&&!$ad[$t]&&!$k["generated"]&&$T;$S=(preg_match('~^(AVG|MIN|MAX)\((.+)\)~',$d,$x)?$l[idf_unescape($x[2])]["type"]:$k["type"]);$Gh=preg_match('~text|json|lob~',$S);$Yd=preg_match(number_type(),$S)||preg_match('~^(CHAR_LENGTH|ROUND|FLOOR|CEIL|TIME_TO_SEC|COUNT|SUM)\(~',$d);echo"<td id='$p'".($Yd&&($W===null||is_numeric(strip_tags($yd))||$S=="money")?" class='number'":"");if(($_GET["modify"]&&$bc&&$W!==null)||$ig!==null){$hd=h($ig!==null?$ig:$W);echo">".($Gh?"<textarea name='$p' cols='30' rows='".(substr_count($W,"\n")+1)."'>$hd</textarea>":"<input name='$p' value='$hd' size='$se[$t]'>");}else{$Ae=strpos($yd,"<i>…</i>");echo($T?" data-text='".($Ae?2:($Gh?1:0))."'".($bc?"":" data-warning='".lang(105)."'"):"").">$yd";}}next($I);}if($Ba)echo"<td>";adminer()->backwardKeysPrint($Ba,$H[$ef]);echo"</tr>\n";}if(is_ajax())exit;echo"</table>\n","</div>\n";}if(!is_ajax()){if($H||$A||$nd){$qc=true;if($_GET["page"]!="last"){if(!$v||(count($H)<$v&&($H||!$A)))$Uc=($A?$A*$v:0)+count($H);elseif(JUSH!="sql"||!$Xd){$Uc=($Xd?false:found_rows($Q,$Z));if(intval($Uc)<max(1e4,2*($A+1)*$v))$Uc=first(slow_query(count_rows($a,$Z,$Xd,$dd)));elseif(JUSH=='sql'||JUSH=='pgsql')$qc=false;}}if(!support("cursor"))$nd=(($Uc===false?count($H)+1:$Uc-$A*$v)>$v);$Lf=($v&&($nd||$A));if($Lf)echo($nd?'<p><a href="'.h(pagination_href($A+1)).'" class="loadmore"'.on('click','selectLoadMore',lang(106)).'>'.lang(107).'</a>':''),"\n";echo"<div class='footer'><div>\n";if($Lf){$Je=($Uc===false?$A+($H?(count($H)>=$v?2:1):0):floor(($Uc-1)/$v));echo"<fieldset><legend>".lang(108)."</legend>";if(!support("cursor")){echo
pagination(0,$A).($A>5?" …":"");for($o=max(1,$A-4);$o<min($Je,$A+5);$o++)echo
pagination($o,$A);if($Je>0)echo($A+5<$Je?" …":""),($qc&&$Uc!==false?pagination($Je,$A):" <a href='".h(remove_from_uri("page")."&page=last")."' title='~$Je'>".lang(109)."</a>");}else
echo
pagination(0,$A).($A>1?" …":""),($A?pagination($A,$A):""),($nd?pagination($A+1,$A)." …":"");echo"</fieldset>\n";}echo"<fieldset>","<legend>".lang(110)."</legend>";$Lb=($qc?"":"~ ").$Uc;$je=($Uc!==false?($qc?"":"~ ").lang(111,$Uc):"");echo
checkbox("all",1,0,$je,on('click','countRows',$Lb))."\n","</fieldset>\n";if(adminer()->selectCommandPrint())echo'<fieldset',($_GET["modify"]?'':" title='".lang(112)."'"),'>
<legend><a href=\'',h($_GET["modify"]?remove_from_uri("modify"):relative_uri()."&modify=1"),'\'>',lang(113),'</a></legend><div>
<input type=\'submit\' id=\'save\' value=\'',lang(17),'\'',($_GET["modify"]?'':" class='jsonly' disabled"),'>
</div></fieldset>

<fieldset><legend>',lang(114),' <span id="selected"></span></legend><div>
<input type=\'submit\' name=\'edit\' value=\'',lang(13),'\'>
<input type=\'submit\' name=\'clone\' value=\'',lang(115),'\'>
<input type=\'submit\' name=\'delete\' value=\'',lang(21),'\'',confirm(),'>
</div></fieldset>
';$Tc=adminer()->dumpFormat();foreach((array)$_GET["columns"]as$d){if($d["fun"]){unset($Tc['sql']);break;}}if($Tc){print_fieldset("export",lang(116)." <span id='selected2'></span>");$Hf=adminer()->dumpOutput();echo($Hf?html_select("output",$Hf,$ja["output"])." ":""),html_select("format",$Tc,$ja["format"])," <input type='submit' name='export' value='".lang(116)."'>\n","</div></fieldset>\n";}adminer()->selectEmailPrint(array_filter($ec,'strlen'),$e);echo"</div></div>\n";}if(adminer()->selectImportPrint())echo"<p>","<a href='#import' class='toggle'>".lang(117)."</a>","<span id='import'".($_POST["import"]?"":" class='hidden'").">: ",($oi?input_hidden(ini_get("session.upload_progress.name"),$oi):""),file_input(" name='csv_file'"," ".html_select("separator",array("csv"=>"CSV,","csv;"=>"CSV;","tsv"=>"TSV"),$ja["format"])." <input type='submit' name='import' value='".lang(117)."'>".($oi?" <progress class='jsonly hidden' max='1' value='0'></progress>":"")),"</span>";echo
input_token(),"</form>\n",(!$dd&&$I?"":script("tableCheck();"));}}}if(is_ajax()){ob_end_clean();exit;}}elseif(isset($_GET["script"])){if($_GET["script"]=="kill"){if(!$j)connection()->query("KILL ".number($_POST["kill"]));}elseif(list($P,$p,$y)=adminer()->_foreignColumn(column_foreign_keys($_GET["source"]),$_GET["field"])){$v=11;$E=connection()->query("SELECT $p, $y FROM ".table($P)." WHERE ".(preg_match('~^[0-9]+$~',$_GET["value"])?"$p = $_GET[value] OR ":"")."$y LIKE ".q("$_GET[value]%")." ORDER BY 2 LIMIT $v");for($o=1;($G=$E->fetch_row())&&$o<$v;$o++)echo"<a href='".h(ME."edit=".url_escape($P)."&where[".url_escape(bracket_escape(idf_unescape($p)))."]=".url_escape($G[0]))."'>".h($G[1])."</a><br>\n";if($G)echo"...\n";}exit;}else{page_header(lang(70),"",false);if(adminer()->homepage()){echo"<form action='' method='post'>\n","<p>".lang(118).": <input type='search' name='query' value='".h($_POST["query"])."'> <input type='submit' value='".lang(50)."'>\n";if($_POST["query"]!="")search_tables();echo"<div class='scrollable'>\n","<table class='nowrap checkable odds'".on('click','tableClick').on('dblclick','tableClick').">\n",'<thead><tr class="wrap">','<td class="hover"><input id="check-all" type="checkbox" class="jsonly"'.on('click','formCheck','^tables\[').'>','<th>'.lang(119),'<td>'.lang(120),"<tbody>\n";foreach(table_status()as$P=>$G){$y=adminer()->tableName($G);if($y!="")echo'<tr><td class="hover">'.checkbox("tables[]",$P,in_array($P,(array)$_POST["tables"],true)),"<th><a href='".h(ME).'select='.url_escape($P)."'>$y</a>","<td align='right'><a href='".h(ME."edit=").url_escape($P)."'>".format_status($G,"Rows")."</a>";}echo"</table>\n","</div>\n","</form>\n",script("tableCheck();");adminer()->pluginsLinks();}}page_footer();