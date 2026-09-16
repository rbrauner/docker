<?php
/** Adminer Editor - Compact database editor
* @link https://www.adminer.org/
* @author Jakub Vrana, https://www.vrana.cz/
* @copyright 2009 Jakub Vrana
* @license https://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
* @license https://www.gnu.org/licenses/gpl-2.0.html GNU General Public License, version 2 (one or other)
* @version 6.1.0
*/namespace
Adminer;const
VERSION="6.1.0";error_reporting(24575);set_error_handler(function($rc,$tc){return!!preg_match('~^Undefined (array key|offset|index)~',$tc);},E_WARNING|E_NOTICE);$Nc=!preg_match('~^(unsafe_raw)?$~',ini_get("filter.default"));if($Nc||ini_get("filter.default_flags")){foreach(array('_GET','_POST','_COOKIE','_SERVER')as$W){$Ri=filter_input_array(constant("INPUT$W"),FILTER_UNSAFE_RAW);if($Ri)$$W=$Ri;}}$_COOKIE=array_filter($_COOKIE,'is_scalar');if(function_exists("mb_internal_encoding"))mb_internal_encoding("8bit");function
connection($g=null){return($g?:Db::$instance);}function
adminer(){return
Adminer::$instance;}function
driver(){return
Driver::$instance;}function
connect(){$Ab=adminer()->credentials();$F=Driver::connect($Ab[0],$Ab[1],$Ab[2]);return(is_object($F)?$F:null);}function
idf_unescape($r){if(!preg_match('~^[`\'"[]~',$r))return$r;$Ae=substr($r,-1);return
str_replace($Ae.$Ae,$Ae,substr($r,1,-1));}function
q($P){return
connection()->quote($P);}function
idx($ta,$u,$i=null){return($ta&&array_key_exists($u,$ta)?$ta[$u]:$i);}function
number($W){return
preg_replace('~[^0-9]+~','',$W);}function
int_type(){return'(tiny|small|medium|big)?int(eger|\d)?';}function
number_type(){return'(^('.int_type().'|decimal|numeric|number|real|(binary_|half_|scaled_)?float\d?|(binary_)?double( precision)?|(small)?money)$)';}function
text_type(){return'char|text'.(JUSH=="sql"?'|enum|set':'');}function
is_searchable(array$k,array$W){if(!isset($k["privileges"]["where"]))return
false;$T=$k["type"];$gh=$W["val"];$Ha='binary$|bytea|raw|image|bfile|^vector$'.(JUSH=="mssql"?'|^timestamp$':'|^bit').(JUSH=="oracle"?'|^blob|^long|rowid':'');if(preg_match("~$Ha~",$T))return
false;if(preg_match(number_type(),$T)){$_='-?\d+(\.\d+)?';return(bool)preg_match('~^'.$_.(preg_match('~IN$~',$W["op"])?"( *, *$_)*":'').'$~',$gh);}if(preg_match('~^(small)?date|^timestamp~',$T))return(bool)preg_match('~^\d+-\d+-\d+~',$gh);if(preg_match('~^time~',$T))return(bool)preg_match('~^\d+:\d+~',$gh);if(preg_match('~^bool~',$T)||(JUSH=="mssql"&&$T=="bit"))return(bool)preg_match('~^(t|f|true|false|[01])$~i',$gh);return
true;}function
remove_slashes(array$Y,$Nc=false){$F=array();foreach($Y
as$u=>$W)$F[stripslashes($u)]=(is_array($W)?remove_slashes($W,$Nc):($Nc?$W:stripslashes($W)));return$F;}function
bracket_escape($r,$Aa=false){static$yi=array(':'=>':1',']'=>':2','['=>':3','"'=>':4','='=>':5');return
strtr($r,($Aa?array_flip($yi):$yi));}function
url_escape($P){static$yi=array();if(!$yi){$yi=array(' '=>'+');foreach(str_split("\"'<>#%&+=?".ini_get("arg_separator.input"))as$Qa)$yi[$Qa]=sprintf('%%%02X',ord($Qa));for($p=0;$p<256;$p++){if($p<32||$p>126)$yi[chr($p)]=sprintf('%%%02X',$p);}}return
strtr((string)$P,$yi);}function
min_version($lj,$Qe="",$g=null){$g=connection($g);$Ah=$g->server_info;if($Qe&&preg_match('~([\d.]+)-MariaDB~',$Ah,$y)){$Ah=$y[1];$lj=$Qe;}return$lj&&version_compare($Ah,$lj)>=0;}function
charset(Db$f){return(min_version("5.5.3",0,$f)?"utf8mb4":"utf8");}function
ini_set($Kf,$X){return(function_exists('ini_set')?\ini_set($Kf,$X):false);}function
ini_bool($Xd){$W=ini_get($Xd);return(preg_match('~^(on|true|yes)$~i',$W)||(int)$W);}function
ini_bytes($Xd){$W=ini_get($Xd);switch(strtolower(substr($W,-1))){case'g':$W=(int)$W*1024;case'm':$W=(int)$W*1024;case'k':$W=(int)$W*1024;}return$W;}function
max_input_vars($G,$Tf){$Ue=(int)ini_get("max_input_vars");return($Ue?(int)floor(($Ue-$Tf)/$G):0);}function
max_input_vars_error(){$Xd="max_input_vars";return
lang(0,"<b>$Xd = ".ini_get($Xd)."</b>");}function
sid(){static$F;if($F===null)$F=(SID&&!($_COOKIE&&ini_bool("session.use_cookies")));return$F;}function
set_password($kj,$L,$U,$C){$_SESSION["pwds"][$kj][$L][$U]=($_COOKIE["adminer_key"]&&is_string($C)?array(encrypt_string($C,$_COOKIE["adminer_key"])):$C);}function
get_password(){$F=get_session("pwds");if(is_array($F))$F=($_COOKIE["adminer_key"]?decrypt_string($F[0],$_COOKIE["adminer_key"]):false);return$F;}function
get_val($D,$k=0,$kb=null){$kb=connection($kb);$E=$kb->query($D);if(!is_object($E))return
false;$G=$E->fetch_row();return($G?$G[$k]:false);}function
get_vals($D,$d=0){$F=array();$E=connection()->query($D);if(is_object($E)){while($G=$E->fetch_row())$F[]=$G[$d];}return$F;}function
get_key_vals($D,$g=null,$Dh=true){$g=connection($g);$F=array();$E=$g->query($D);if(is_object($E)){while($G=$E->fetch_row()){if($Dh)$F[$G[0]]=$G[1];else$F[]=$G[0];}}return$F;}function
get_rows($D,$g=null,$j="<p class='error'>"){$kb=connection($g);$F=array();$E=$kb->query($D);if(is_object($E)){while($G=$E->fetch_assoc())$F[]=$G;}elseif(!$E&&!$g&&$j&&(defined('Adminer\PAGE_HEADER')||$j=="-- "))echo$j.adminer()->error()."\n";return$F;}function
unique_array($G,array$t){foreach($t
as$s){if(preg_match("~^(PRIMARY|UNIQUE)$~",$s["type"])&&!$s["partial"]){$F=array();foreach($s["columns"]as$u){if(!isset($G[$u]))continue
2;$F[$u]=$G[$u];}return$F;}}}function
where_function($ld,$d,array$k){if($ld=="md5")return"MD5(".(is_blob($k)||JUSH!='sql'||preg_match("~^utf8~",$k["collation"])?$d:"CONVERT($d USING ".charset(connection()).")").")";return(in_array($ld,driver()->functions)||in_array($ld,driver()->grouping)?apply_sql_function($ld,$d):$d);}function
where(array$Z,array$l=array()){$F=array();foreach((array)$Z["where"]as$u=>$W){$u=bracket_escape($u,true);$d=idf_escape($u);$k=idx($l,$u,array());$Ic=$k["type"];$je=$k&&(is_blob($k)||preg_match('~binary~',$Ic));$F[]=$d.($je&&!is_utf8($W)?" = ".driver()->quoteBinary($W):(JUSH=="sql"&&$Ic=="json"?" = CAST(".q($W)." AS JSON)":(JUSH=="pgsql"&&preg_match('~^jsonb?$~',$k["full_type"])?"::jsonb = ".q($W)."::jsonb":(JUSH=="sql"&&is_numeric($W)&&preg_match('~\.~',$W)?" LIKE ".q($W):(JUSH=="mssql"&&strpos($Ic,"datetime")===false?" LIKE ".q(preg_replace('~[_%[]~','[\0]',$W)):" = ".unconvert_field($k,q($W)))))));if(JUSH=="sql"&&preg_match('~char|text~',$Ic)&&preg_match("~[^ -@]~",$W))$F[]="$d = ".q($W)." COLLATE ".charset(connection())."_bin";}foreach((array)$Z["null"]as$u)$F[]=idf_escape($u)." IS NULL";foreach((array)$Z["col"]as$p=>$ab){$W=idx($Z["val"],$p);$F[]=where_function(idx($Z["fun"],$p),idf_escape($ab),idx($l,$ab,array())).($W!==null?" = ".q($W):" IS NULL");}return
implode(" AND ",$F);}function
where_columns(array$l){$F=array();foreach((array)$_GET["null"]as$u)$F[$u]=true;foreach(array_keys((array)$_GET["where"])as$u)$F[bracket_escape($u,true)]=true;foreach((array)$_GET["col"]as$ab)$F[$ab]=true;return
array_intersect_key($F,$l);}function
where_check($W,array$l=array()){parse_str($W,$Sa);remove_slashes(array(&$Sa));return
where($Sa,$l);}function
where_link($p,$d,$X,$Jf="="){$If=($X!==null?$Jf:"IS NULL");return"&where[$p][col]=".url_escape($d).($If!=first(adminer()->operators())?"&where[$p][op]=".url_escape($If):"")."&where[$p][val]=".url_escape($X);}function
convert_fields(array$e,array$l,array$J=array()){$F="";foreach($e
as$u=>$W){if($J&&!in_array(idf_escape($u),$J))continue;$ua=convert_field($l[$u]);if($ua)$F
.=", $ua AS ".idf_escape($u);}return$F;}function
cookie_path(){return
strtr(preg_replace('~\?.*~','',$_SERVER["REQUEST_URI"]),array(";"=>"%3B",","=>"%2C"));}function
cookie($z,$X,$Ge=2592000){header("Set-Cookie: $z=".rawurlencode($X).($Ge?"; expires=".gmdate("D, d M Y H:i:s",time()+$Ge)." GMT":"")."; path=".cookie_path().(HTTPS?"; secure":"").($z=="adminer_import"?"":"; HttpOnly")."; SameSite=lax",false);}function
get_url($Yi,$sb){$http_response_header=null;$sc=array();set_error_handler(function($rc,$j)use(&$sc){$sc[]=preg_replace('~^file_get_contents\([^)]*\):\s*~','',$j);return
true;});$F=file_get_contents($Yi,false,$sb);restore_error_handler();$Cd=(function_exists('http_get_last_response_headers')?http_get_last_response_headers():$http_response_header);return
array($F,(preg_match('~^HTTP/[\d.]+ (\d+)~',idx($Cd,0,''),$y)?$y[1]:''),(array)$Cd,($F===false?implode("\n",$sc):''),);}function
get_settings($wb){parse_str($_COOKIE[$wb],$Eh);return$Eh;}function
get_setting($u,$wb="adminer_settings",$i=null){return
idx(get_settings($wb),$u,$i);}function
save_settings(array$Eh,$wb="adminer_settings"){$X=http_build_query($Eh+get_settings($wb));cookie($wb,$X);$_COOKIE[$wb]=$X;}function
restart_session(){if(!ini_bool("session.use_cookies")&&(!function_exists('session_status')||session_status()==PHP_SESSION_NONE))session_start();}function
stop_session($Vc=false){$aj=ini_bool("session.use_cookies");if(!$aj||$Vc){session_write_close();if($aj&&ini_set("session.use_cookies",'0')===false)session_start();}}function&get_session($u){return$_SESSION[$u][DRIVER][SERVER][$_GET["username"]];}function
set_session($u,$W){$_SESSION[$u][DRIVER][SERVER][$_GET["username"]]=$W;}function
auth_url($kj,$L,$U,$h=null){$Xi=remove_from_uri(implode("|",array_keys(SqlDriver::$drivers))."|username|ext|".($h!==null?"db|":"").($kj=='mssql'||$kj=='pgsql'?"":"ns|").session_name());preg_match('~([^?]*)\??(.*)~',$Xi,$y);return"$y[1]?".(sid()?SID."&":"").($_GET["ext"]?"ext=".url_escape($_GET["ext"])."&":"").($kj!="server"||$L!=""?url_escape($kj)."=".url_escape($L)."&":"")."username=".url_escape($U).($h!=""?"&db=".url_escape($h):"").($y[2]?"&$y[2]":"");}function
is_ajax(){return($_SERVER["HTTP_X_REQUESTED_WITH"]=="XMLHttpRequest");}function
redirect($Me,$ff=null){if($ff!==null){restart_session();$_SESSION["messages"][preg_replace('~^[^?]*~','',($Me!==null?$Me:$_SERVER["REQUEST_URI"]))][]=$ff;}if($Me!==null){if($Me=="")$Me=".";header("Location: $Me");exit;}}function
query_redirect($D,$Me,$ff,$Ng=true,$zc=true,$Ec=false,$oi=""){if($zc){$Sh=microtime(true);$Ec=!connection()->query($D);$oi=format_time($Sh);}$Ph=($D?adminer()->messageQuery($D,$oi,$Ec):"");if($Ec){adminer()->error
.=adminer()->error().$Ph.script("messagesPrint();")."<br>";return
false;}if($Ng)redirect($Me,$ff.$Ph);return
true;}class
Queries{static$queries=array();static$start=0;}function
remember_query($D){if(!Queries::$start)Queries::$start=microtime(true);Queries::$queries[]=(driver()->delimiter!=';'?$D:(preg_match('~;$~',$D)?"DELIMITER ;;\n$D;\nDELIMITER ":$D).";");}function
queries($D){remember_query($D);return
connection()->query($D);}function
apply_queries($D,array$S,$uc='Adminer\table'){foreach($S
as$Q){if(!queries("$D ".$uc($Q)))return
false;}return
true;}function
queries_redirect($Me,$ff,$Ng){$Hg=implode("\n",Queries::$queries);$oi=format_time(Queries::$start);return
query_redirect($Hg,$Me,$ff,$Ng,false,!$Ng,$oi);}function
format_time($Sh){return
lang(1,max(0,microtime(true)-$Sh));}function
relative_uri($Xi=''){return
preg_replace_callback('~^[^?]*~',function($y){return
str_replace(":","%3A",$y[0]);},preg_replace('~^[^?]*/([^?]*)~','\1',($Xi?:$_SERVER["REQUEST_URI"])));}function
remove_from_uri($Zf=""){return
substr(preg_replace("~(?<=[?&])($Zf".(SID?"":"|".session_name()).")=[^&]*&~",'',relative_uri()."&"),0,-1);}function
get_files($z,$Kb=false){$Jc=$_FILES[$z];if(!$Jc)return
null;foreach($Jc
as$u=>$W)$Jc[$u]=(array)$W;$F=array();foreach($Jc["error"]as$u=>$j){if($j)return$j;$m=$Jc["name"][$u];$vi=$Jc["tmp_name"][$u];$rb=file_get_contents($Kb&&preg_match('~\.gz$~',$m)?"compress.zlib://$vi":$vi);if($Kb){$Sh=substr($rb,0,3);if(function_exists("iconv")&&preg_match("~^\xFE\xFF|^\xFF\xFE~",$Sh))$rb=iconv("utf-16","utf-8",$rb);elseif($Sh=="\xEF\xBB\xBF")$rb=substr($rb,3);}$F[]=array($m,$rb);}return$F;}function
get_file($u,$Kb=false,$Ob=""){$Mc=get_files($u,$Kb);if(!is_array($Mc))return$Mc;$F='';foreach($Mc
as$Jc){$rb=$Jc[1];$F
.=$rb;if($Ob)$F
.=(preg_match("($Ob\\s*\$)",$rb)?"":$Ob)."\n\n";}return$F;}function
upload_error($j){$Ze=($j==UPLOAD_ERR_INI_SIZE?ini_get("upload_max_filesize"):0);return($j?lang(2).($Ze?" ".lang(3,$Ze):""):lang(4));}function
is_utf8($W){return(preg_match('~~u',$W)&&!preg_match('~[\0-\x8\xB\xC\xE-\x1F]~',$W));}function
utf8_length($W){return
strlen(preg_replace('~[\x80-\xBF]~','',$W));}function
format_number($W){preg_match('~^#+([^#0]+)(?:(#+)\1)?(#*0)$~u',lang(5),$y);$Gh=strlen($y[3]);$F=number_format($W,0,".","");$F=preg_replace('~\B(?=(\d{'.(strlen($y[2])?:$Gh).'})*\d{'.$Gh.'}$)~',$y[1],$F);return
strtr($F,preg_split('~~u',lang(6),-1,PREG_SPLIT_NO_EMPTY));}function
format_status(array$R,$u){$W=idx($R,$u,'?');if(!is_numeric($W))return
h($W);if($W<0)return'?';$qa=($u=="Rows"&&(JUSH=="sqlite"||$R["Engine"]==(JUSH=="pgsql"?"table":"InnoDB")));return($qa?"~ ":"").format_number($W);}function
friendly_url($W){return
preg_replace('~\W~i','-',$W);}function
table_status1($Q,$Fc=false){$F=table_status($Q,$Fc);return($F?reset($F):array("Name"=>$Q));}function
column_foreign_keys($Q){$F=array();foreach(adminer()->foreignKeys($Q)as$Zc){foreach($Zc["source"]as$W)$F[$W][]=$Zc;}return$F;}function
fields_from_edit(){$F=array();foreach((array)$_POST["field_keys"]as$u=>$W){if($W!=""){$W=bracket_escape($W);$_POST["function"][$W]=$_POST["field_funs"][$u];$_POST["fields"][$W]=$_POST["field_vals"][$u];}}foreach((array)$_POST["fields"]as$u=>$W){$z=bracket_escape($u,true);$F[$z]=array("field"=>$z,"full_type"=>"","type"=>"","privileges"=>array("insert"=>1,"update"=>1,"where"=>1,"order"=>1),"null"=>true,"auto_increment"=>($z==driver()->primary),);}return$F;}function
dump_headers($Ld,$qf=false){$F=adminer()->dumpHeaders($Ld,$qf);$Uf=$_POST["output"];if($Uf!="text"||$F=="tar"){$hb=($Uf!="text"&&$Uf!="file"&&preg_match('~^[0-9a-z]+$~',$Uf)?".$Uf":"");header("Content-Disposition: attachment; filename=".adminer()->dumpFilename($Ld).".$F$hb");}session_write_close();if(!ob_get_level())ob_start(null,4096);ob_flush();flush();return$F;}function
dump_csv(array$G){$Ii=$_POST["format"]=="tsv";foreach($G
as$u=>$W){if(preg_match('~["\n]|^0[^.]|\.\d*0$|'.($Ii?'\t':'[,;]|^$').'~',$W))$G[$u]='"'.str_replace('"','""',$W).'"';}echo
implode(($_POST["format"]=="csv"?",":($Ii?"\t":";")),$G)."\r\n";}function
parse_csv($Db,$K){$F=array();preg_match_all('~(?>"[^"]*"|[^"\r\n]+)+~',$Db,$Se);foreach($Se[0]as$G){preg_match_all("~((?>\"[^\"]*\")+|[^$K]*)$K~",$G.$K,$Te);$F[]=$Te[1];}return$F;}function
csv_value($W){return(preg_match('~^".*"$~s',$W)?str_replace('""','"',substr($W,1,-1)):$W);}function
apply_sql_function($n,$d){return($n?($n=="unixepoch"?"DATETIME($d, '$n')":($n=="count distinct"?"COUNT(DISTINCT ":strtoupper("$n("))."$d)"):$d);}function
get_temp_dir(){return
ini_get("upload_tmp_dir")?:sys_get_temp_dir();}function
file_open_lock($m){if(is_link($m))return;$ed=@fopen($m,"c+");if(!$ed)return;@chmod($m,0660);if(!flock($ed,LOCK_EX)){fclose($ed);return;}return$ed;}function
file_write_unlock($ed,$Gb){rewind($ed);fwrite($ed,$Gb);ftruncate($ed,strlen($Gb));file_unlock($ed);}function
file_unlock($ed){flock($ed,LOCK_UN);fclose($ed);}function
first(array$ta){return
reset($ta);}function
password_file($zb){$m=get_temp_dir()."/adminer.key";if(!$zb&&!file_exists($m))return'';$ed=file_open_lock($m);if(!$ed)return'';$F=stream_get_contents($ed);if(!$F){$F=rand_string();file_write_unlock($ed,$F);}else
file_unlock($ed);return$F;}function
rand_string(){return(function_exists('random_bytes')?bin2hex(random_bytes(16)):md5(uniqid(strval(mt_rand()),true)));}function
select_value($W,$x,array$k,$mi){if(is_array($W)){$F="";if(array_filter($W,'is_array')==array_values($W)){$se=array();foreach($W
as$V)$se+=array_fill_keys(array_keys($V),null);foreach(array_keys($se)as$qe)$F
.="<th>".h($qe);foreach($W
as$V){$F
.="<tr>";foreach(array_merge($se,$V)as$gj)$F
.="<td>".select_value($gj,$x,$k,$mi);}}else{foreach($W
as$qe=>$V)$F
.="<tr>".($W!=array_values($W)?"<th>".h($qe):"")."<td>".select_value($V,$x,$k,$mi);}return"<table>$F</table>";}if(!$x)$x=adminer()->selectLink($W,$k);if($x===null){if(is_mail($W))$x="mailto:$W";if(is_url($W))$x=$W;}$W=driver()->value($W,$k);$F=adminer()->editVal($W,$k);if($F!==null){if(!is_utf8($F))$F="\0";elseif($mi!=""&&is_shortable($k))$F=shorten_utf8($F,max(0,+$mi));else$F=h($F);}return
adminer()->selectVal($F,$x,$k,$W);}function
is_blob(array$k){return
preg_match('~blob|bytea|raw|file'.(JUSH=="mssql"?'|binary|image':'').'~',$k["type"])&&!in_array($k["type"],idx(driver()->structuredTypes(),lang(7),array()));}function
is_mail($kc){$va='[-a-z0-9!#$%&\'*+/=?^_`{|}~]';$ac='[a-z0-9]([-a-z0-9]{0,61}[a-z0-9])';$lg="$va+(\\.$va+)*@($ac?\\.)+$ac";return
is_string($kc)&&preg_match("(^$lg(,\\s*$lg)*\$)i",$kc);}function
is_url($P){$ac='[a-z0-9]([-a-z0-9]{0,61}[a-z0-9])';return
preg_match("~^((https?):)?//($ac?\\.)+$ac(:\\d+)?(/.*)?(\\?.*)?(#.*)?\$~i",$P);}function
is_ipv6($ia){$o='[\da-f]{1,4}';$ie='\d{1,3}(\.\d{1,3}){3}';return(bool)preg_match("~^(($o:){7}$o|($o:){6}$ie|(($o:)*$o)?::(($o:)*($o|$ie))?)$~iD",$ia);}function
is_shortable(array$k){return!preg_match('~'.number_type().'|date|time|year~',$k["type"]);}function
url_host($Hd){return(strpos($Hd,":")!==false?"[$Hd]":$Hd);}function
server_parts(array$gg){return
array("scheme"=>(string)$gg["scheme"],"host"=>(string)$gg["host"],"port"=>(string)$gg["port"],"socket"=>(string)$gg["socket"],"path"=>(string)$gg["path"],);}function
parse_server($L){if($L=="")return
server_parts(array());if($L[0]==":"&&!is_ipv6($L)){$Wg=substr($L,1);if(preg_match('~^\d+$~D',$Wg))return
server_parts(array("port"=>$Wg));return(preg_match('~^/[-\w.:/]*$~D',$Wg)?server_parts(array("socket"=>$Wg)):null);}$fh="";if(preg_match('~^([-+.\w]+)://~',$L,$y)){$fh=strtolower($y[1]);$L=substr($L,strlen($y[0]));}if(preg_match('~^\[(.+)](:(\d+))?(/[-\w./]*)?$~D',$L,$y))return(is_ipv6($y[1])?server_parts(array("scheme"=>$fh,"host"=>$y[1],"port"=>$y[3],"path"=>$y[4])):null);if(is_ipv6($L))return
server_parts(array("scheme"=>$fh,"host"=>$L));if(preg_match('~^(/[-\w./]*)(:(\d+))?$~D',$L,$y))return
server_parts(array("scheme"=>$fh,"host"=>$y[1],"port"=>$y[3]));return(preg_match('~^([-\w.]*)(:(\d+))?(/[-\w./]*)?$~D',$L,$y)?server_parts(array("scheme"=>$fh,"host"=>$y[1],"port"=>$y[3],"path"=>$y[4])):null);}function
count_rows($Q,array$Z,$ke,array$o){$D=" FROM ".table($Q).($Z?" WHERE ".implode(" AND ",$Z):"");return($ke&&(JUSH=="sql"||count($o)==1)?"SELECT COUNT(DISTINCT ".implode(", ",$o).")$D":"SELECT COUNT(*)".($ke?" FROM (SELECT 1$D GROUP BY ".implode(", ",$o).") x":$D));}function
slow_query($D){$h=adminer()->database();$pi=adminer()->queryTimeout();$Ih=driver()->slowQuery($D,$pi);$g=null;if(!$Ih&&support("kill")){$g=connect();if($g&&($h==""||$g->select_db($h))){$te=number(get_val(connection_id(),0,$g));echo
script("const timeout = setTimeout(() => { ajax('".js_escape(ME)."script=kill', function () {}, 'kill=$te&token=".get_token()."'); }, 1000 * $pi);");}}ob_flush();flush();$F=@get_key_vals(($Ih?:$D),$g,false);if($g){echo
script("clearTimeout(timeout);");ob_flush();flush();}return$F;}function
get_token(){$Kg=rand(1,1e6);return($Kg^$_SESSION["token"]).":$Kg";}function
verify_token(){list($wi,$Kg)=explode(":",$_POST["token"]);return($Kg^$_SESSION["token"])==$wi&&in_array($_SERVER["HTTP_SEC_FETCH_SITE"],array("","same-origin"));}function
compress_alphabet(){return
strtr(implode(range('"','~')),"'\\","!\n");}function
decompress_string($P,$Rb=""){$oa=array_flip(str_split(compress_alphabet()));$v=strlen($P);$jj=($v?13*($v-1)/2-$oa[$P[0]]:0);$Ha="";$Wg=0;$Xg=0;for($p=1;$p<$v;$p+=2){$Wg=($Wg<<13)+$oa[$P[$p]]*93+$oa[$P[$p+1]];$Xg+=13;while($Xg>=8&&$jj>=8){$Xg-=8;$jj-=8;$Ha
.=chr($Wg>>$Xg);$Wg&=(1<<$Xg)-1;}}if($Ha=="")return"";if($Rb!=""&&function_exists('inflate_init'))return
inflate_add(inflate_init(ZLIB_ENCODING_RAW,array('dictionary'=>$Rb)),$Ha,ZLIB_FINISH);return($Rb==""&&function_exists('gzinflate')?gzinflate($Ha):inflate($Ha,$Rb));}function
inflate($Ha,$Rb=""){$De=array(3,4,5,6,7,8,9,10,11,13,15,17,19,23,27,31,35,43,51,59,67,83,99,115,131,163,195,227,258);$Ee=array(0,0,0,0,0,0,0,0,1,1,1,1,2,2,2,2,3,3,3,3,4,4,4,4,5,5,5,5,0);$Ub=array(1,2,3,4,5,7,9,13,17,25,33,49,65,97,129,193,257,385,513,769,1025,1537,2049,3073,4097,6145,8193,12289,16385,24577);$Wb=array(0,0,0,0,1,1,2,2,3,3,4,4,5,5,6,6,7,7,8,8,9,9,10,10,11,11,12,12,13,13);$F=$Rb;$sg=0;do{$Oc=inflate_bits($Ha,$sg,1);$T=inflate_bits($Ha,$sg,2);if(!$T){$sg=($sg+7)&~7;$v=inflate_bits($Ha,$sg,16);$sg+=16;$F
.=substr($Ha,$sg>>3,$v);$sg+=$v<<3;}else{if($T==1){$Ke=array_merge(array_fill(0,144,8),array_fill(0,112,9),array_fill(0,24,7),array_fill(0,8,8));$Xb=array_fill(0,30,5);}else{$Je=inflate_bits($Ha,$sg,5)+257;$Vb=inflate_bits($Ha,$sg,5)+1;$Mf=array(16,17,18,0,8,7,9,6,10,5,11,4,12,3,13,2,14,1,15);$if=array_fill(0,19,0);$hf=inflate_bits($Ha,$sg,4)+4;for($p=0;$p<$hf;$p++)$if[$Mf[$p]]=inflate_bits($Ha,$sg,3);$jf=inflate_table($if);$Fe=array();while(count($Fe)<$Je+$Vb){$Zh=inflate_symbol($Ha,$sg,$jf);if($Zh==16)$Fe=array_merge($Fe,array_fill(0,inflate_bits($Ha,$sg,2)+3,end($Fe)));elseif($Zh==17)$Fe=array_merge($Fe,array_fill(0,inflate_bits($Ha,$sg,3)+3,0));elseif($Zh==18)$Fe=array_merge($Fe,array_fill(0,inflate_bits($Ha,$sg,7)+11,0));else$Fe[]=$Zh;}$Ke=array_slice($Fe,0,$Je);$Xb=array_slice($Fe,$Je);}$Le=inflate_table($Ke);$Zb=inflate_table($Xb);while(($Zh=inflate_symbol($Ha,$sg,$Le))!=256){if($Zh<256)$F
.=chr($Zh);else{$v=$De[$Zh-257]+inflate_bits($Ha,$sg,$Ee[$Zh-257]);$Yb=inflate_symbol($Ha,$sg,$Zb);$Df=strlen($F)-$Ub[$Yb]-inflate_bits($Ha,$sg,$Wb[$Yb]);for($p=0;$p<$v;$p++)$F
.=$F[$Df+$p];}}}}while(!$Oc);return($Rb==""?$F:substr($F,strlen($Rb)));}function
inflate_bits($Ha,&$sg,$yb){$F=0;for($p=0;$p<$yb;$p++){$F+=((ord($Ha[$sg>>3])>>($sg&7))&1)<<$p;$sg++;}return$F;}function
inflate_table(array$Fe){$Q=array();$Za=0;for($Ia=1;$Ia<=max($Fe);$Ia++){foreach($Fe
as$Zh=>$v){if($v==$Ia){$Q[$Ia][$Za]=$Zh;$Za++;}}$Za<<=1;}return$Q;}function
inflate_symbol($Ha,&$sg,array$Q){$Za=0;$Ia=0;do{$Za=($Za<<1)+inflate_bits($Ha,$sg,1);$Ia++;}while(!isset($Q[$Ia][$Za]));return$Q[$Ia][$Za];}function
script($Nh,$xi="\n"){return"<script".nonce().">$Nh</script>$xi";}function
script_src($Yi,$Lb=false){return"<script src='".h($Yi)."'".nonce().($Lb?" defer":"")."></script>\n";}function
nonce(){return' nonce="'.get_nonce().'"';}function
on($vc,$ud,$ra=null){$sa=array();foreach(array_slice(func_get_args(),2)as$W)$sa[]=json_encode($W,256);return" data-on$vc='".str_replace(array('&','<',"'"),array('&amp;','&lt;','&#039;'),"$ud(".implode(", ",$sa).")")."'";}function
input_hidden($z,$X=""){return"<input type='hidden' name='".h($z)."' value='".h($X)."'>\n";}function
input_token(){return
input_hidden("token",get_token());}function
target_blank(){return' target="_blank" rel="noreferrer noopener"';}function
h($P){return
str_replace(array('&','<','"',"'","\0"),array('&amp;','&lt;','&quot;','&#039;','&#0;'),$P);}function
nl_br($P){return
str_replace("\n","<br>",$P);}function
checkbox($z,$X,$Ua,$we="",$c="",$Xa="",$ye=""){$F="<input type='checkbox' name='$z' value='".h($X)."'".($Ua?" checked":"").($we==""&&$Xa?" class='$Xa'":"").($ye?" aria-labelledby='$ye'":"").$c.">";return($we!=""?"<label".($Xa?" class='$Xa'":"").">$F".h($we)."</label>":$F);}function
optionlist($A,$nh=null,$bj=false){$F="";foreach($A
as$qe=>$V){$Lf=array($qe=>$V);if(is_array($V)){$F
.='<optgroup label="'.h($qe).'">';$Lf=$V;}foreach($Lf
as$u=>$W)$F
.='<option'.($bj||is_string($u)?' value="'.h($u).'"':'').($nh!==null&&($bj||is_string($u)?(string)$u:$W)===$nh?' selected':'').'>'.h($W);if(is_array($V))$F
.='</optgroup>';}return$F;}function
html_select($z,array$A,$X="",$c="",$ye=""){static$we=0;$xe="";if(!$ye&&substr($A[""],0,1)=="("){$we++;$ye="label-$we";$xe="<option value='' id='$ye'>".h($A[""]);unset($A[""]);}return"<select name='".h($z)."'".($ye?" aria-labelledby='$ye'":"")."$c>".$xe.optionlist($A,$X)."</select>";}function
html_radios($z,array$A,$X="",$K=""){$F="";foreach($A
as$u=>$W)$F
.="<label><input type='radio' name='".h($z)."' value='".h($u)."'".($u==$X?" checked":"").">".h($W)."</label>$K";return$F;}function
confirm($ff=""){return
on('click','confirmClick',$ff?:lang(8));}function
print_fieldset($q,$Ce,$oj=false){echo"<fieldset><legend>","<a href='#fieldset-$q' class='toggle'>$Ce</a>","</legend>","<div id='fieldset-$q'".($oj?"":" class='hidden'").">\n";}function
bold($Ja,$Xa=""){return($Ja?" class='active $Xa'":($Xa?" class='$Xa'":""));}function
js_escape($P){return
str_replace("<","\\x3C",addcslashes($P,"\r\n'\\"));}function
js_escape_re($P){return
addcslashes(preg_quote($P,"/"),"\r\n");}function
pagination_href($B){return
remove_from_uri("page|next").($B?"&page=$B".($_GET["next"]!=""?"&next=".url_escape($_GET["next"]):""):"");}function
pagination($B,$Eb){return" ".($B==$Eb?($B?"<b>".($B+1)."</b>":$B+1):'<a href="'.h(pagination_href($B)).'">'.($B+1)."</a>");}function
hidden_fields(array$Eg,array$Od=array(),$xg=''){$F=false;foreach($Eg
as$u=>$W){if(!in_array($u,$Od)){if(is_array($W))hidden_fields($W,array(),$u);else{$F=true;echo
input_hidden(($xg?$xg."[$u]":$u),$W);}}}return$F;}function
hidden_fields_get(){echo(sid()?input_hidden(session_name(),session_id()):''),($_GET["ext"]?input_hidden("ext",$_GET["ext"]):""),(isset($_GET[DRIVER])?input_hidden(DRIVER,SERVER):""),input_hidden("username",$_GET["username"]);}function
on_upload_progress(&$Wi){$Wi=(ini_bool("session.upload_progress.enabled")&&ini_get("session.upload_progress.name")?rand_string():"");return($Wi?on('submit','uploadProgress',ME."upload=$Wi",SESSION_NAME."=$Wi"):"");}function
file_input($c,$Wg=""){$Ve="max_file_uploads";$We=ini_get($Ve);$Ze="upload_max_filesize";$af=ini_bytes($Ze);$vg=ini_bytes("post_max_size");if($vg&&$vg<$af){$Ze="post_max_size";$af=$vg;}$bf=ini_get($Ze);return(ini_bool("file_uploads")?"<input type='file'$c".on('change','fileChange',(int)$We,lang(9,"$Ve = $We"),$af,lang(9,"$Ze = $bf")).">$Wg":lang(10));}function
enum_input($T,$c,array$k,$X,$nc=""){preg_match_all("~'((?:[^']|'')*)'~",$k["length"],$Se);$xg=($k["type"]=="enum"?"val-":"");$Ua=(is_array($X)?in_array("null",$X):$X===null);$F=($k["null"]&&$xg?"<label><input type='$T'$c value='null'".($Ua?" checked":"")."><i>$nc</i></label>":"");foreach($Se[1]as$W){$W=stripcslashes(str_replace("''","'",$W));$Ua=(is_array($X)?in_array($xg.$W,$X):$X===$W);$F
.=" <label><input type='$T'$c value='".h($xg.$W)."'".($Ua?' checked':'').'>'.h(adminer()->editVal($W,$k)).'</label>';}return$F;}function
input(array$k,$X,$n,$za=false,$Vi=false){$z=h(bracket_escape($k["field"]));echo"<td class='function'>";$qc=driver()->enumLength($k);if($qc){$k["type"]="enum";$k["length"]=$qc;}$A=($k["type"]=="enum"||$k["type"]=="set");if(is_array($X)&&!$n&&!$A)$n="json";$oe=($n=="json"||preg_match('~^jsonb?$~',$k["full_type"]));if($oe&&$X!=''&&(JUSH!="pgsql"||$k["type"]!="json")&&(is_array($X)||!$_POST["save"]))$X=json_encode(is_array($X)?$X:json_decode($X),128|64|256);$Vg=(JUSH=="mssql"&&$Vi&&$k["auto_increment"]);if($Vg&&!$_POST["save"])$n=null;$md=(isset($_GET["select"])||$Vg?array("orig"=>lang(11)):array())+adminer()->editFunctions($k);$c=" name='fields[$z]".($A?"[]":"")."'".($za?" autofocus":"");echo
driver()->unconvertFunction($k)." ";$Q=$_GET["edit"]?:$_GET["select"];if($k["type"]=="enum")echo
h($md[""])."<td>".adminer()->editInput($Q,$k,$c,$X);else{$xd=(in_array($n,$md)||isset($md[$n]));$Pc=0;foreach($md
as$u=>$W){if($u===""||!$W)break;$Pc++;}echo(count($md)>1?"<select name='function[$z]'".on('change','functionChange').on_help_value('^SQL$').">".optionlist($md,$n===null||$xd?$n:"")."</select>":h(reset($md)))."<td".($Pc&&count($md)>1?on('input','skipOriginal',$Pc):"").">";$Zd=adminer()->editInput($Q,$k,$c,$X);if($Zd!="")echo$Zd;elseif(preg_match('~bool~',$k["type"]))echo"<input type='hidden'$c value='0'>"."<input type='checkbox'".(preg_match('~^(1|t|true|y|yes|on)$~i',$X)?" checked":"")."$c value='1'>";elseif($k["type"]=="set")echo
enum_input("checkbox",$c,$k,(is_string($X)?explode(",",$X):$X));elseif(is_blob($k)&&ini_bool("file_uploads"))echo"<input type='file' name='fields-$z'>";elseif($oe)echo"<textarea$c cols='50' rows='12' class='jush-json'>".h($X).'</textarea>';elseif(($li=preg_match('~text|lob|memo~i',$k["type"]))||preg_match("~\n~",$X)){if($li&&JUSH!="sqlite")$c
.=" cols='50' rows='12'";else{$H=min(12,substr_count($X,"\n")+1);$c
.=" cols='30' rows='$H'";}echo"<textarea$c>".h($X).'</textarea>';}else{$Li=driver()->types();$Ji=$Li[$k["type"]];if(preg_match('~date|time|year~',$k["type"])){$fd=(preg_match('~time~',$k["type"])&&preg_match('~^\d+$~',$k["length"])?$k["length"]+1:0);$cf=($Ji?$Ji+$fd:0);}elseif(!preg_match('~int|vector~',$k["type"])&&preg_match('~^(\d+)(,(\d+))?$~',$k["length"],$y))$cf=(preg_match("~binary~",$k["type"])?2:1)*$y[1]+($y[3]?1:0)+($y[2]&&!$k["unsigned"]?1:0);else$cf=($Ji?$Ji+($k["unsigned"]?0:1):0);echo"<input".((!$xd||$n==="")&&preg_match('~^'.int_type().'$~',$k["type"])&&!preg_match('~\[]~',$k["full_type"])?" type='number'":"")." value='".h($X)."'".($cf?" data-maxlength='$cf'":"").(preg_match('~char|binary~',$k["type"])&&$cf>20?" size='".($cf>99?60:40)."'":"")."$c>";}echo
adminer()->editHint($Q,$k,$X),(count($md)>1?script("fire(qs('select', qsl('td').previousSibling), 'change');",""):"");}}function
process_input(array$k){$r=bracket_escape($k["field"]);$n=idx($_POST["function"],$r);if($n=="orig")return(preg_match('~^CURRENT_TIMESTAMP~i',$k["on_update"])?idf_escape($k["field"]):false);if($n=="NULL")return"NULL";if(is_blob($k)&&ini_bool("file_uploads")){$Jc=get_file("fields-$r");if(!is_string($Jc))return
false;return
driver()->quoteBinary($Jc);}$X=idx($_POST["fields"],$r);if($X===null)return
false;if($k["type"]=="enum"||driver()->enumLength($k)){$X=idx($X,0);if($X=="orig"||!$X)return
false;if($X=="null")return"NULL";$X=substr($X,4);}if($k["auto_increment"]&&$X=="")return
null;if($k["type"]=="set")$X=implode(",",(array)$X);if($n=="json"){$X=json_decode($X,true);if(!is_array($X))return
false;return$X;}return
adminer()->processInput($k,$X,$n);}function
search_tables(){$_GET["where"][0]["val"]=$_POST["query"];$qh="<ul>\n";foreach(table_status('',true)as$Q=>$R){$z=adminer()->tableName($R);if(isset($R["Engine"])&&$z!=""&&(!$_POST["tables"]||in_array($Q,$_POST["tables"]))){$E=connection()->query("SELECT".limit("1 FROM ".table($Q)," WHERE ".implode(" AND ",adminer()->selectSearchProcess(fields($Q),array(),$R)),1));if(!$E||$E->fetch_row()){$Bg="<a href='".h(ME."select=".url_escape($Q)."&where[0][op]=".url_escape($_GET["where"][0]["op"])."&where[0][val]=".url_escape($_GET["where"][0]["val"]))."'>$z</a>";echo"$qh<li>".($E?$Bg:"<p class='error'>$Bg: ".adminer()->error())."\n";$qh="";}}}echo($qh?"<p class='message'>".lang(12):"</ul>")."\n";}function
on_help($li,$Fh=0){return
on('mouseover','helpMouseover',$li,$Fh).on('mouseout','helpMouseout');}function
on_help_value($Pg="",$Ug=""){return
on('mouseover','helpValueMouseover',$Pg,$Ug).on('mouseout','helpMouseout');}function
edit_form($Q,array$l,$G,$Vi,$j='',$D='',$oi=''){$fi=adminer()->tableName(table_status1($Q,true));page_header(($Vi?lang(13):lang(14)),$j,array("select"=>array($Q,$fi)),$fi);adminer()->editRowPrint($Q,$l,$G,$Vi,$D,$oi);if($G===false){echo"<p class='error'>".lang(15)."\n";return;}echo"<form action='' method='post' enctype='multipart/form-data' id='form'>\n";$jc=false;$uj=($Vi&&!isset($_GET["select"])?where_columns($l):array());$tb=(count($uj)!=count($l));if(!$tb)$uj=array();if(!$l)echo"<p class='error'>".lang(16)."\n";else{echo"<table class='layout nowrap'".on('keydown','editingKeydown').">\n";$za=!$_POST;foreach($l
as$z=>$k){echo"<tr".($uj[$z]?on('change','whereChange'):"")."><th>".adminer()->fieldName($k);$i=idx($_GET["set"],bracket_escape($z));if($i===null){$i=$k["default"];if($k["type"]=="bit"&&preg_match("~^b'([01]*)'\$~",$i,$Rg))$i=$Rg[1];if(JUSH=="sql"&&preg_match('~binary~',$k["type"]))$i=bin2hex($i);}$X=($G!==null?($k["type"]=="set"&&is_array($G[$z])?implode(",",$G[$z]):(is_bool($G[$z])?+$G[$z]:$G[$z])):(!$Vi&&$k["auto_increment"]?"":(isset($_GET["select"])?false:$i)));if(!$_POST["save"]&&is_string($X))$X=adminer()->editVal($X,$k);if(($Vi&&!isset($k["privileges"]["update"]))||$k["generated"])echo"<td class='function'><td>".select_value($X,'',$k,null);else{$jc=true;$n=($_POST["save"]?idx($_POST["function"],bracket_escape($z),""):($Vi&&preg_match('~^CURRENT_TIMESTAMP~i',$k["on_update"])?"now":($X===false?null:($X!==null?'':'NULL'))));if(!$_POST&&!$Vi&&$X==$k["default"]&&preg_match('~^[\w.]+\(~',$X))$n="SQL";if(preg_match("~time~",$k["type"])&&preg_match('~^CURRENT_TIMESTAMP~i',$X)){$X="";$n="now";}if($k["type"]=="uuid"&&$X=="uuid()"){$X="";$n="uuid";}if($za!==false)$za=($k["auto_increment"]||$n=="now"||$n=="uuid"?null:true);input($k,$X,$n,$za,$Vi);if($za)$za=false;}}if(!fields($Q)&&driver()->primary!="")echo"<tr>"."<th><input name='field_keys[]'".on('input','fieldChange').">"."<td class='function'>".html_select("field_funs[]",adminer()->editFunctions(array("null"=>isset($_GET["select"]))))."<td><input name='field_vals[]'>";echo"</table>\n";}echo"<p>\n";if($jc){echo"<input type='submit' value='".lang(17)."'>\n";if(!isset($_GET["select"])&&$tb){$Sb=($uj&&($j!=""||adminer()->error!="")?" disabled":"");echo"<input type='submit' name='insert' value='".($Vi?lang(18):lang(19))."' title='Ctrl+Shift+Enter'$Sb".($Vi?on('click','ajaxForm',lang(20)):"").">\n";}}echo($Vi?"<input type='submit' name='delete' value='".lang(21)."'".confirm().">\n":"");if(isset($_GET["select"]))hidden_fields(array("check"=>(array)$_POST["check"],"clone"=>$_POST["clone"],"all"=>$_POST["all"]));echo
input_hidden("referer",(isset($_POST["referer"])?$_POST["referer"]:$_SERVER["HTTP_REFERER"])),input_hidden("save",1),input_token(),"</form>\n";}function
repeat_pattern($lg,$v){return
str_repeat("$lg{0,65535}",$v/65535)."$lg{0,".($v%65535)."}";}function
shorten_utf8($P,$v=80,$Xh=""){if(!preg_match("(^(".repeat_pattern("[\t\r\n -\x{10FFFF}]",$v).")($)?)u",$P,$y))preg_match("(^(".repeat_pattern("[\t\r\n -~]",$v).")($)?)",$P,$y);return(isset($y[2])?h($y[1]).$Xh:h(preg_replace('~\n[^\n]*\z~',"\n",$y[1]))."$Xh<i>…</i>");}function
icon($Kd,$z,$Jd,$ri,$c=""){return"<button ".($z?"type='submit' name='$z'":"draggable='true' tabindex='-1'")." title='".h($ri)."' class='icon icon-$Kd".($z?"":" jsonly")."'$c><span>$Jd</span></button>";}function
copy_icon(){$xb=lang(22);return"<a href='' class='jsonly icon-copy' title='$xb'><span>$xb</span></a>";}if(isset($_GET["file"])){if($_SERVER["HTTP_IF_MODIFIED_SINCE"]){header("HTTP/1.1 304 Not Modified");exit;}header("Expires: ".gmdate("D, d M Y H:i:s",time()+365*24*60*60)." GMT");header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");header("Cache-Control: immutable");ini_set("zlib.output_compression",'1');if($_GET["file"]=="default.css"){header("Content-Type: text/css; charset=utf-8");echo
decompress_string('(c07.iDWB2P^44#-?4|;B/44]7H4lw&-l1us[;Aw3F@y!!u8h&5lJ+s8~8#xAbk"cQ]R{MZ?o^v^a#|o%1RN~:A`c_3u#xO`W`9[#3#G)@_XLW[-LOm1M)2wpnapz(kol`E24;IX=.@Pjc$1IGNic^j_.v9AwAsnJOwxpxBh7RYIt;KG-(FMG
Adpv
[

(2-Sc0GQ}EQWnGcol-$Qv^2ECs<q/xhBTm9lCLa!B^}TAkT&WMl_M,o&)J,GCT(D^eVTk",/D_,TO=~j=,26Z#vX|nWICK#Z5@NwvYGiL=N$ql4#(^xBbp(j}eC)/89nXNb$8Rxx/6tP{05s"W9+u=JOjJU-t.|!`%5YLa5:sHJJ|"LU
5T$x2_+?dSb%)*E:0<<rC?0q1UiV1}oYV_O
`kco<lho=6=`Ychz_-_,l[QT.*_[u"H_+n5$B[>E5yJQ:Pq7mFK|=Ud8)[&j@D?y>4b9?}so%Z6Fo-V?dMO%s"9btR`w3&;ZW}){Ouw}aojLBDg5LhkDGc7H(L5BaglUX,Od3>jeDx`e#rbIRl$=?<@,@n/?X.9AV67C_5>`5.OJw;/804lNiP#^S6=LJ
^vCMb3mh1wUR@|E8Y.&q[qUw#bagm.1[auaxp%AsJm[mQQKl4J90^0w+MS6*k"ulGTs=p1SmZQRn>]X7F
C|lZLl^,_XhqbVcjV1.-f{@jAC]V]9H=6,YCs4=JfSU*K$`.$8a+fA^[n]ZB`]:1"OQ6suk/>rFgZK#UW!B#OteI@L;3:q)a0USDe3
@n0nFH~kwU<=-`x$^)j4b`-g<OJlz3]Gy!J<qV#*qL63nW<KuW3&mb$xR;0vkC+$;5485A"0rhccZ[l`.1{B=NX^j7F>q_xk0V5n.cH,%>uU/ThJ97-NIj&E6O-56*{FE:[G%n&!T4@z&8z)Ar/e`?+/-Ec]la7rO4mST2zjS3:)7rc(Vez_v<I;
NU.W@aM26LRI,nYW!wijTprIQ8ltv<8<,#2#Ep6v1nLTZ0/L-d2cLud<-f"vY0oy&@>ZI2Cmf-(zb*H|paF<
&-"_+HOS14RmQy=C(njc`[i99y#P)D}
!"4WIQ1I!bL?^LjJtYVZB1@2@^S[*;_]CTXx+)cpb9v[^p5!6J<O6K|Kr?BfUJ$"`F0F%o{+!HGu~`%fyt.lf%n-K0;@ZX1
?.J6e#Vx]!cm!yX#!c**YQgfnO?7q(-apY5Y:]Z[:j|$V![p>w=RRN-,k^?Ae9W(2cPYk;d={sR*:UTDP%6"dP=F<[A$z9w-P")+NjnqOl:qa/e^tgqH}mc9+X!=v>yZXS?Qjcj@qGp_.)Kg2=
O2&"bW1sh`-~lj"(8L3C"ydv_mDS,#?p&e-I_1$&K(a%$39/
$4sha,=US(t.Ef~3u?R<Ng>SFbV]n:H,zXZ8lQd53K~-znpCx`I#?$h-C1a-6nfmY(AVj334"P2F!G]0A^&kaxg_[T3r],&7WTj/
WaQaJ(_B<=ASx0_/?.<Ky1!K-+3*gIDnq2wAg7/mZLRbRPN((|4">yXa<Z?^gLrdir@yMsW)YP26>?;3l%OdEKq~sWTW!NbVc(gZJ_pTaUY<]N7Tw
WQgN+9K;
j7/a-o_:b-:h|.Qs49{Ad(,rpC+=)j,nR2h"9g~(+8$W3/U3E%E+|8Qj=]M%t`">&LJUwX/E4<wJPwdCoV+C)rCm!,*hd<`gR<6yG"b-Hm=9%1](EdUAAYf+NHsgh7mGC8!etCyWO-wdmoFb2sZQg.>uGZ+A)9&#<Cn>ICrPEgy=(YLd~Aj([4,`-Y
5h2
+r"]R@#etebW[/R/J
8.cB"MOaJ@uAukW?#H-;5c7vh~_;71ai9}X*6@$QBh.C:xAv=d+paH*c4L;En(-qx$_C2|wJQ{&|aD"zy!c.wfg2@OEx,RW:q/Q4*Ad]CRYqenC|3Qu^dVBYym"+.T$&c#hb7z&WHH(2`6FWRVT6P3`wM_Y|]o9;xW#CI&"jT*f!3fl-(*;P9`UnK@7+%x8dJT!fh;IVWew[6&&EbQHn
{.5_^LG&`FQrLn&?f0VVDVyhw.~%X8VHGGd0j8K_=HO=kQ3e^g{^_lr`umEX^*E.Ot&AtCksU6~YS]A:^$x"&I0!bX3!$g*
.MZN+p2C+IGxqQGjoR3C]h"cN9P7?bR*[)G_|S)c5Z%s)2:I!>zv)?5vtR:
Gn9yp*075sxi1A7`0kp;=Ftj#9%cpd.6*Sx=/>y
KY.cY@BUyZDXI/^1tT:X^h
v&ii^-wgN3L"UhT.u$i9$Gc;)N0A"k:7q38.n~Rq11e,wj+exx^BCYd)Xzbze41eiB-J!4!#5A;e,lcoLilHpFK1m{]mv4szhdI:gU>=b+6!aAFn;a;*FEGlDaaY4xf&bPuBh!mkpu16n_;<#ew+U0-(
2M`#M1elq/S^8S*O~Om$C:4Krh{61FR5WTJ1)TRPvp$Ft`*I!A4wmIfXT=!-#^Y;lnfI>6i+$J0.+Ml.Mi:6ZERVHq@q$?c)yl,Jl!?^MkA&.&c+NPFF_/
@=@hRA]$sdO@Y-O[i5b1Qd=`b_H0Gd+dW"Tx0Rijg^B(pt4]N2w7gbPh&mS^tIK?4[`%MZ)wI6b1Uznewp&yCC]Zl;IM^Ay1^6>L]-bQY`x$*7ctE`x<dOXvu(ebuxA6^:%jHJmiHGv<,rlEr65zmtsId-u*x}ym+Jv[x"v=7PtOkdO>x{Jtn{[g_P48(~?s4p*(Y2%G3QwfnfwHxsS(lcYW`(D7X^_O#j<!I57De;,LgI:lh)4FrvBHE-aN.QDwiE,R5f5<y:[cIUe*jN?iuHx#w[lRGtQF>`uJc^MHbS9r(JLkSolna[4,NCB5"RlFKx_OIF=A*jNHSQVJ/SmVdbSIw@SA,Mb?BIt?Z%j(gqv*V0y_?J[~dq-BB6_GX37r`S$3o:VRJ=5|jH.8L

!Y/l|?DB^"k(01m&Z7jsYC*<]KeZIz%<_VlatXkGy0Jt<q8
O(
k}w"Ub$5gbD~pO4KgJXd0Cx{Gp74DBF^#_Eom+=q
^qWiAy52uIM]"vV(gtRlEcps&Hb/M-)]7yn^QQusr="MbD$Wpnmp"biwsgOA>Z5MRA^Z6%8U>2R.rtln#Mu)7XFvVUu@`<S/F"v4BQSQUVT,![!8uS^%a.X5LyH?#M_r|@&Fxo9/#4lLXY@99)WHYAbWiWp"9*{7;RC>q!
iWW`$|+SOT2ggVD#tC@Z6cs|w:XM0FC9Y-__%JnuA~ZKF@siDiH37XCEAyNZ?D6i6PPhj8K&]Tmok$uvl=VJk+6+a=>:O0-D:+lz;#2au3eBXE]CC8>xfPLmK,6QCiOjAoxd($jrBYgA.3[]/}KPaJ1V(a1p)y!|O($zoy4]MFCKVr8ei8?FdJsX:&M
ub4vZNn49n&Uc+*n#[0i6aQW@[G1+yXzZ,"pH26yu)K_CIkAK|+Kk_rPGtd$1o');}elseif($_GET["file"]=="dark.css"){header("Content-Type: text/css; charset=utf-8");echo
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
decompress_string('!X/F{iDZ5/ftCpSDt$)#J-N@e_M93%_SBTkUrS+3Svz0]FG#!0$8<#H")?K[,nulvO)/i
,uo(8CYv9XorQl*XT
{B?@"gelv0_?O0@gJAj/`m@MYarfGU%[9b!Z!jfpbjt[uBHo#qXEUYxs^$ws4[bMg1?m1iRjjl
Y4xc&Ot-hVInBzw(9.@_r2ix*r<.D=I2ao7P[`
)/hx^#Dg/JOR`T]@b;{<iAa2STG
mAFdA+Rp|Z+_DW|n5*vy
"D0$6eld@+14@4!fb#,-h^[H<.ydy!h/&@@!7Xy0?6`e+XZ?XdI]WZ2j^sML8tqX-z5i"HWI4~yr6rPVnr^&<KuZ:UB1/$h2B{4",K@q)ajhNcN*XS?2*bQ}SNe#:Dasi1F^ADK)jUTRbFG*?UUZ
D@i8`)lAjGH;/m/iASRtTwXR.bG"Q<8Z+Z|t<g{tA(iIz`>x2cOFIDw[UrtrG%ZW/c`_kI.[RqNMA@KXQSHGo6ycvo!u:bx
tuqxs4SxgS89-E*.?2lt5xb-MD;6!kHoj,0vl
><|7wI:3Y*w^Ta{d=!Qw~yz<iXxC-e!FC%<%/JM-u[Lc8H;.)z)tH*Yjw%"@GV<s;!Ab
rZ?.ru-^BgJa5G[dU8kkJdFl/2ZS$YwEv;G_8>SfeN?r_B>eG!I7Z2p*.V%$Eai2/ET/FQ!_f^A9eOp}"B$Hq[e5Ib?-(6X.5ZE>1hyd9g1<^)J+_0&+
_3C3DSj`rJA@xS8jz/HF,M30+=<UpBOc.T>Oun$D(mHpMt9^qT}sgNOcMq5Jc<HXn0Hd7r9cIE.,l&!WHCe;,XGO.H4kRC)$Ju|v3L
y>(Mf_LsP%
ls-^!@
!MT)A5Y=IXE[aFPbGXCbKuSc_@;Pb9/5ciV!S>vf8WAQtUie3zcTY1H!XGxisaJRE(js?kw=(*_!3^#nUnVQ(-?sjhwo_b+5dL4p_BTaqM(^BkNrHweYup/~Yu-t2r:2H#WhnQGd
]VgZNkWySWMrtMX*`)VkW0bV)?>.Aro"=u!3(;?K,UAM_G__T7uyA
j3z$qOt;i@^>LT=kj&XnSjZNE]/){LriP3kXRS.kWc<Y]).)ac[:R=<ijGw$}pQ)7mXJubD=QA_K|yebqJ0SfvZW[@L#0VjvNvfNCO34%6jNi1,__5G7`5u7%$|YBi^&VqVErh4vU>4NfY1tXU{;}Ztb`[1Jq7hStyXr+k>/_*C.[.uL,"HF3+m)YIH7t^NWECL:U=5VZ/EgD>
6I4JN$*Gt,jgTS[gE,.kmMBm1rr}!|[{/[T*3|U]XIDKKeErm)Y{HXD=0v
X*a#p-:bsx>"SU!ELfB:>Q6dX@0/
=U<n<cr4x7?V[,1AjVrK9vZ-P,+r=OyN:=_k/Hx^JaO.p{GKXsly*7ur=JX#LoNZ/|rYEa?T@4T#(tr}Ea,cYu2JWR2T0eN2h2ZMSDhc2MxX`zb9V0:gq"lW*73`!tu3Cs/<CY;&h0icukr]iGcqfWu>s/w90v+</pn#Bi!i_rIJ5ohYnT8;)~t#xi0+>Q&>Zxui89@MZz,]@x2v%awv0sT$Hy)9*sf8I)Wm%I45E!O$NL
#S.EJ-8;gFp3L1YdXEs"g
%c6r`%PBKs[6N#a2%tY<
E^r"LdS1m5mE2y!.4%:9)
1Fd)RqilWF$k4(BOFHV>Zi%d.K[#Q7@MF9WIP@K*jkIgtY*y&~#OmLC7n
#|"<"vu@EwGYWx
!_"r2_[+X:x!JIkl6W$=t)v)jBTIr%IJXEKJMxwZY)WlP*}L"
CWp@KFUD~0>ayXVv}fSW0L0LeSxbsv*l]7}E6WgZ/c3ZsfjCLoWbt;%#CWxa4Uh_A%)e>5XKM4jQqVTFH4$mGY:HEjS]J2qv
c::Z>c1(@eY6QjPr9X,oYPc,BMv=;=xTH|+D:1a<%DV
_;054~j`y]ycch-jbJh5kGs$
7AOwd3=vTCkPba6oDP[sr+Tq,c:@457x|&Xc;K~)R?hT4c%KdprmH_tARK)S,-$dWh~1Ts:^3I8F9]O1D`cX,Lj4B?w$sZLFWD78cDSaiea))Prb""PdNkFgBoau45

yQ&"TifRdjoYsSTCFWq<T$e8!N4:3"vooa(0`N?+[5G/L:VQ0QVZ.5pB!QY^$Jw7N$0l_wRbz)Y]Zq*hR1<F~M9$VJ_S(0_#T[0y7TxUK"s".C3(@U(&&+N_;9HJLGakIRzLF7KNCW56KQZT^&keUc<ov4ZL@*YA:GqB@ZC$;M%;p
Ad$@vuGF+Z<mod_+EBqj}yuSfC$c`E[8(JSH7_>C8+VoykXt
+{7OIKN/&x1K0%^s+[5[/,9/
6<n_|c(Wi+}3#65CeiaY[qSngA^20Bp9J0h&U;!bqu<Q3R!6qEBy2b
&.w$`VGwws-zZ6b-@?i;/bhAOu)i[MM=)9:nZMPdckHq,fB7*B]D+^;N$51x^]6^#b!}>M[R<7e2`Kb/-}nK!9=v<hl2q@`o+Y#}xv13,4o>i)=Gi]T#$W``>&_15Z&R7`X^.7X8yH8?=Ki1rp7Ib~.VHIHciX!=r:%|bb,
ol(7yd:)rv#t#)yEbq^b"82s*=N|dyGl:j3&](H,n:[bfW2o+}:`Ww
8$BZ5efNrRsdpdvEI!Enc%ohM*9xyZccv(ePh
co^s`WVI8nhT:iMD:dkB.XX*gi.?(Gk^6t%.Hdyk*cK#V-iIDDtWIefo;:8O"1}^
[mP!KF3V.|ix>ZW]&aj
*I@wXKorU?^uN6Tv"3#@@ZJNA]?{Ve-,1A
rOEx+>~"E=Ly83qSIK6s1C6FLNyc96l!ip
]h*qS(G:(:Uo=D_
gSu9gv+[KLP4T?Jl&cDC(.
@[90u6<f&IU!efJGXC/wm9`qJMjb"<2EuG.Zlx/19iTYRA5"6xJ4E]U-^=L
hb
C_#5ek^N&uexD,&Lqbuzg2<0-ykZf|P&,-iZv.8~0^):RAFa`y!B*i
@yIN/W$r.6}&MMk4"@f4x+33[R%J&^{>pkHNI
w7f>xV?ss"8kQXR_mG!;ny{h4@eF+*zM1cFH`
]H?jrlZf`f
[Bnz?!Bop@LR-jWC$/
%?Vo4ow&3,+i{bk2!:@_iX-j=^Jvv!F3@!^o{9~/s7|W!-9d!hYFL;A++v`&zrArP)~9kWZlC)juQe..9&%iuEIkI$cYoVN^4N.>c0/q?e,!8*~X`,+@N4ObdRTvln?bayLyG=!7K,QJ[/]+yZU:OtQ-
UMX2a
,S4p)}8JT^tfsYQD.qT:;*3lLgG=Njx$oJDhN-)/X$Z[$o[|LOWN[*)9I%&*)>pA!1Nldz(OVcqt8;]n=Y#lgrB6"F,j)4wvb`+<EvxG6jxW)7UZb_KEplk`R5ZtB=7!2X<]+nC/rjurAm8!VukJg"1p.8f2[7")QjsT!!OVduqi%(J>O-P|$TtuHz>TV5"+11Dw/9e$-
<lJZm{KDF<,L_%#-PkC.a7?yf.,L-&wxX.XSoixfYp4kHI&;>iPZ&ndF6^Hc["5Ki5x^e!026*:R1FalBL_s/WF4)d3e/-Wq`TEy=}&*;;tYQ7R>
_p$NW"JrHv/7#5wp@?`q$GI%/=%*d*rg~r8kb[HtrZcngt+<P@?Ny%z1?Y=r9<Wil(?$0<MK+gkZ{baBwmQ0^lXg^B<<2U-,X]D@)x:t,t.DJ-f,,.W]6OUGMV5nzVpw**v:RCvxMs;A{MAAiMOZgU5yqSKi52lN];=Le&V8!TII:vh).1&RT&PmlIehieue>H-YnhJcx*P9I=l!?QtQ5-9>e^0?,ozLH.I:6=Zw3;|Z*P4G?M1CLdkxq35LdA`:Yd10o?8.Zxd5x/Gp}?vYFuKtxkGde2T-r1=b4vp
{Ep9HF2Hc&sT%vW<m
FQ#K^KiN[Jw8
IG%ziPJ(XD8>?JJtR@Qx:=xg#4.36s#iqkc.n]/l_hAGZm_GUVc5HYPrgHXH$EM-W|>CiZ>{lMuntmLaNJ5*po&%SgnOV-o(&UHmnOdEb6^hl6i8oZ-)ZG3{Vqn4cqDJwn!Xc|m^7&mlbEAJAiU|%#dDi+[n_^)./Kqqh_(&8`>8MXCoE,Q(c-ASVrH,2s]VW.X9t3o6Q:ZD*k?abYTopWi@N"m:vpn!P7HmZ~M,[SG$[:/Q",t6n(l9BZVS?~xhNkALPq/#n62n]ht%a{]b"U"bNlQbA(&u5=24=</p:_
X=o,j#7dM"@Fe%Ded?KoF4PaIBMP94.9P^,yj9DQ5X7[NA/Ak^"]uUQk*r^2|!`o-&Vub3h*h<}A%_QKF.N1=2|<2-sirXF-z:"P%JWTs9p[5[;f/<p
|34<6Jspq0&PV6vr|0
A6?8R%$>@s9c&/!
"q$$hV_H:`O[S(vDTyYu9:5m<2e)Ai/#Jc)[&w6#[X3A@!Yaa8rL8
f@]qos0!SNL
+%:<"%
fh@rH(;b0#,L1p[Pj1c"A5l^zNL[>"W7X.~#3Rn,7Q&VPw&*:n5oD++F,Uo&3dQ@s4AU%V!O~6HN8N|^H+s20J0r`Y}](Q!y_m.Lr7/#+!j8
7q2NP.8AdA3%l<LV#SL)Ns^?_w&I7c,C57MO;g8B1DW0AifIw.B3l^Mf;J!YG228i>gh5OITd-r-oI:}a@Ios!bM6ntE&Z)1(#8iz$38J5fnyQ)`5M;,ILAC.&8v]N85>SZqSxw8CKj20BC[vjO7_6fGWffC:7*RmpGSy5<dOIblc+xu7WiMvVOQ65/B^n>$Ac4N5&p/Rn=o?7yb1DB5H6:rbuMMB)2dj0/ERD#mYuV-L("JaMujOZ02A69zz%<=wyX*D4.xpV=R0YA{KP*F(Q@ap,pJh)x|qm+#i|HkP$Q=T{r~K|W}38yO.ON@?37
Q(XA$MA>m:Qd:1]kRz$!8%hRi
?vr6q^]v0740cv3,_`c`f1M$S<=HkrS6i8SNuO.Ku4kqY^w
oB/NT
)dHG_U+E*@!<Qk]Mj_
:`fj-"Z$-mfapjYA>+Hrn2M1yJ`)q_Yq3fYu&Ceb_C-fQFpJ=R69YU3
1Qf
UmdUxtoSPGZP@J{vNF6`k/7w&gm
eB]QOXUh3pSjeO7%?k2u"H%E}qURYIYW-jB9]0,kg[8v!!WU[ndO4qOWfQB#AL]_gMPIfT5*)3h[!Y>rCI9!CXc[h5oK?hR0Z[+6~1/)fe=[dff&n%dE]02YXEke[vq7ll.p$L0xKvZwU,x&p_=.9?.g93fT~,~TA
P%
.=s3"/N=%]N.I=@[<SbZLOHAYd;N!A.1Xi>M0/!uc
iV&C^b#4X|Ph^GG=/KA}0sxc,i=X5B
(6^3#Z&0)m>-}NEFEa!d5Cb>XI9YL;YP/I8NK8RPd]c(H<LWxCNFJ"d>ZypT,=FfsANy)sP%h4dxQh)[PauTv#2aQ&2O9V<mNL&rG9L
{#Q;4Gd2t?>[QtXB;aWIlKjg/f<#i^u`#Vn,5pM>v:9j|D$Gj+MPt<(VQf/UP=lxp92dVNHXq8vPH_j?M$(f2y>Fr7*36Vrx]c9DUn%qUidIay:vDA-!Rl4:-/|1.4[D!$S]BH#(h
3
8ljgHmZ]/T;nzI1]mOI^#UtY*m72V5n=U]i8wETE.BW2/%;[O:gk!EDDaj;+jlK1f`~B`4[@MOS+PYP3h?SH}Bd0+!!x8SP9haD<I.(leD=^P^n"Ek<0R:EAh#=P0ej,QFOkLHytKis6Qyxl<LmQld:h>CLG9C1$ZZSR;3"]m5Lj]`aR0tAeel4O:lKU_<RtT,]bvvn<Qah$2??rRmMfHEBkmWdWfbi(uJOZ|6X43w1,;#`P72~!,g5mddCLCB6.Z%[0j-)tFU*aQoNwwmGI%&(5I+G$0-FeNZ.DUiITokqp(fsHvR1CJtlBO9TYolSsFe?[(p=Kr974dU[N!t}rF[AT[6Cv?l2H=,@d{lKc6`+w2^g87gp;q:mDm.]VC_:hB"p3F2qp<d+h*4E1=,yYl<ua"*88sPGZ}#QOC`0D/dzBJ>|[(]2VpNF]?b2K:2zG3sMz&:gA^R2xD4Ui;w`,STdDF>cKernL^>O/*w}]j4GP4928%#J1z8z^<3GHu$n#$]Z<=yb@AM7WQ2[bcCE<()Rqeq/mA>nj-Ww!c4H62SgY}w9]s>q#io8tED_B)%B&nhMb-FUQYDxR{vN/9ILlS=6U?!LOySK
a)Ts$kY9;3R:</Nn/yQSd7s]xGis
a}VG2j8Mr0kG.fb1_$BdZn>1$^k<Q~;QXyG
&TC_;0b8@v+"c2"S%AJkUi#=*h%@YuaRMc
DNwo5p52a:U<~j_^>!-JVRH1dM_oqixDDgC>;(JlOWCVJl,Lo/QnQ%#ICpd@mQa2f&(M?k~0zs*QzHL$-o^ulGTeLSD4mg"I_N?dYZFT=QJ!{wJRDIP1?,Q3CQ*o6I}K(_nY27x:L:%nh6C8d[zV)l!I{#4;e)O8e-h"4Z<)IJ&gwmC&Y9zqa.V`%3IN7oBl"0#?|0ZI8sV`vnh4ti{aE]Zik&yX0+XAU!1$lDS^,$UJT*qkQy61%tHS]Z$^@$(d2U:4q0#-:[&3iJm%oI`/xs_
|b+/LSL8u_V^)L=dVF~o&x:i3dvLKYSV64CLu<qG{f1H""SH}ykt<Mg[w`rBIfy0U]nNL&n[r(G?3Yvk9HLAb8;K2Er?hS71(-,4dpPyx[[r?!lsv6S6]-H/*x1=)*&kf<
(kXES*5ljeKLcTxMMFAGYh6Q3-XMLf^
*uG[6Y;GEWwJ$PkRoeriy(^,d*,<55Tvox1*Q%2dr6peyK0<j;8zBun7rM!s_*[Oo{oFFFwZi._P?mnefI!b1`>gJ$y1b?&olEu,6{@NfcR%C<rCpz<e*9[f(#gJk|oqAPY.<{bN*7-Z@bc~fs$vP^
YOqeAMEWCj?
rF(mP6|0#8*.Or;y=iv;VN1su;
HM
!P,2hGDfYDM/tYw^CFET6$]8*M5.B@WA`R^pD3zU3454nw;$T$RIi[JGLqd^frD4tQ)aP.6cD"&ACgrg<:4bi%EgTj6ba?P3oEr*)A$oG)*;*<K4y.^Is0?P3-WuHe=0C/)wZ)`05J5
2WbTa<HvW#AIh
/mhcNu0;6*sKo7a5{EvEJ1yE;Tt8OGnrI)rU^Zj;s*@RO
W/[pclv^;^x,Rksu}>)q5w.aBD;_e*una)Oo%)dVvV//.)2/jZ3E($35-O~W|v!>dnxsn@(,~g,=B[LB<h1g%KB3l7
TG.M(~BE_&p?[GsT=16q=)dstsk(*9bCT9jec4j=y<[m&CXsEaBb');}elseif($_GET["file"]=="jush.js"){header("Content-Type: text/javascript; charset=utf-8");echo
decompress_string('');}elseif($_GET["file"]=="worker.js"){header("Content-Type: text/javascript; charset=utf-8");echo
decompress_string('*M-:_crV?&ivhwW00>hyk#FBR?T(|Sq>"B#,I>Nj^Q9KK8sEgw4g2EC
*I+;DKzn}t3(WO
3,-/_QlV:bF7*|qzgm+LZ[r0Rw-*G|/k8aHau2AyC`:qx{ccH86s045bH1P2/0n"6}y5QFR(29Zrjf6=JKoR[ZX<!#*8i<5q.ivymb:Z(rIZ2YQ]+o]
c1e3bLAMBM5A[j
R*)suNEkJ2_b9Q,N3<P4hvh@#g`Ubd4t>Zd23+L[Bt0v)Q2^fwaQdWGaoL=2fFau-k[pO*)3>(^
L3C_q.O7n@ic/h_PH^?3P(D2iqS^rMAuO_swO`)*TiGN,)~(n+#u:.`d2HKi,UCsH@.]A%*j08LgKJ|@kX+bbB8Zu(St;xKfJ=}=9(nou8]":5u&EO~jfR@9f.[9)!m!m>c&!6F6vOL1`]MawSXj)67Xp@ygy7)9*q,X#[h/L6mfb@W@"#ngdi7B#e$3RlPyr[q*H-nfIGG."G="QLE1bbI!fYOm7erh[>m4s7/_nwA');}elseif($_GET["file"]=="logo.svg"){header("Content-Type: image/svg+xml");echo
decompress_string('%s_VkbOV?&!t"do^rQ`p`ZU/yp&Ye3upHt|/H&y4sA3gG1#^TM/psE"F.!L"b-TOg,=_&!?SQvUpK1NG?UVTm6[[a>X*ZfBqY!LKF
fO{QWHay6P%Mxk-@i/qV|wo57>CjpjQuWGGgYH{O@sDx@a=t3J8^4xXkLUkz!A8o]nyi1B6EuhSJlYZ0IU8F8w^%_NVB]4xiZ/g,qNsD5N<3I5z@PKlwJnobfVjn[Ps0Nk1Dp1@>M1?3j7#!a_W13^gLnlX:$#{3
8i+/0=Y3h6/1,{i{28SyT]@Eu=r"Cz(I8et3V~F)%#.@C6^yX&2~ff.YQQ(5WnhDY<DSV20%H2f?Um0n)kC6+)X&<0DhT=GdXfG>W}N
_itFLhYXgQ-?9$q+dW7/s)Vvp*s9<u=9Wu"5]B@h-)l%Z0$vcYCQ:>M#CF$ONU$8f3.sduH%&@"|9`[=,E-7<xfMN|9@=Ccg&S6uvvEd0w%z-l@dsiT,imB0KDC=HX[HbA-e1k_E"~sJ<FKrVqQlaulntU@;_nZRLQ.qyk*ch&y@KSbULF^1JuDW`W+bWA."U,D&Z89.[5Y.EDYJ$A]=t5LNi>n}`Oc
*0;=MJ_8W,XQa$w^=ohbWF!H30]5ctm(Bky7@-Lh7OogMB"*');}exit;}if(preg_match('~^/[-\w.]~',$_SERVER["HTTP_X_FORWARDED_PREFIX"]))$_SERVER["REQUEST_URI"]=$_SERVER["HTTP_X_FORWARDED_PREFIX"].$_SERVER["REQUEST_URI"];define('Adminer\HTTPS',($_SERVER["HTTPS"]&&strcasecmp($_SERVER["HTTPS"],"off"))||ini_bool("session.cookie_secure"));ini_set("session.use_trans_sid",'0');ini_set("arg_separator.output","&");define('Adminer\SESSION_NAME',session_name());if(isset($_GET["upload"])){$Fg=null;if(!defined("SID")&&$_COOKIE[SESSION_NAME]!=""){session_start();$Fg=$_SESSION[ini_get("session.upload_progress.prefix").$_GET["upload"]];}header("Content-Type: application/json; charset=utf-8");echo
json_encode(isset($Fg["bytes_processed"])?array($Fg["bytes_processed"],$Fg["content_length"]):array());exit;}if(function_exists('session_status')?session_status()==PHP_SESSION_NONE:!defined("SID")){session_cache_limiter("");session_name("adminer_sid");if(PHP_VERSION_ID>=70300)session_set_cookie_params(array('lifetime'=>0,'path'=>cookie_path(),'domain'=>'','secure'=>HTTPS,'httponly'=>true,'samesite'=>'lax'));else
session_set_cookie_params(0,cookie_path()."; SameSite=lax","",HTTPS,true);session_start();}if(function_exists("get_magic_quotes_gpc")&&get_magic_quotes_gpc()){$_GET=remove_slashes($_GET,$Nc);$_POST=remove_slashes($_POST,$Nc);$_COOKIE=remove_slashes($_COOKIE,$Nc);}if(function_exists("get_magic_quotes_runtime")&&get_magic_quotes_runtime())set_magic_quotes_runtime(false);if(function_exists('set_time_limit'))set_time_limit(0);ini_set("precision",'16');function
lang($r,$_=null){$sa=func_get_args();$sa[0]=Lang::$translations[$r]?:$r;return
call_user_func_array('Adminer\lang_format',$sa);}function
lang_format($_i,$_=null){if(is_array($_i)){$sg=($_==1?0:(LANG=='cs'||LANG=='sk'?($_&&$_<5?1:2):(LANG=='fr'?(!$_?0:1):(LANG=='pl'?($_%10>1&&$_%10<5&&$_/10%10!=1?1:2):(LANG=='sl'?($_%100==1?0:($_%100==2?1:($_%100==3||$_%100==4?2:3))):(LANG=='lt'?($_%10==1&&$_%100!=11?0:($_%10>1&&$_/10%10!=1?1:2)):(LANG=='lv'?($_%10==1&&$_%100!=11?0:($_?1:2)):(LANG=='ro'?(!$_||($_%100>0&&$_%100<20)?1:2):(in_array(LANG,array('bs','hr','ru','sr','uk'))?($_%10==1&&$_%100!=11?0:($_%10>1&&$_%10<5&&$_/10%10!=1?1:2)):1)))))))));$_i=$_i[$sg];}$_i=str_replace("'",'’',$_i);$sa=func_get_args();array_shift($sa);$bd=str_replace("%d","%s",$_i);if($bd!=$_i)$sa[0]=format_number($_);return
vsprintf($bd,$sa);}function
langs(){return
array('en'=>'English','id'=>'Bahasa Indonesia','ms'=>'Bahasa Melayu','bs'=>'Bosanski','ca'=>'Català','cs'=>'Čeština','da'=>'Dansk','de'=>'Deutsch','et'=>'Eesti','es'=>'Español','fr'=>'Français','gl'=>'Galego','hr'=>'Hrvatski','it'=>'Italiano','lv'=>'Latviešu','lt'=>'Lietuvių','ro'=>'Limba Română','hu'=>'Magyar','nl'=>'Nederlands','no'=>'Norsk','uz'=>'Oʻzbekcha','pl'=>'Polski','pt'=>'Português','pt-br'=>'Português (Brazil)','sk'=>'Slovenčina','sl'=>'Slovenski','fi'=>'Suomi','sv'=>'Svenska','vi'=>'Tiếng Việt','tr'=>'Türkçe','bg'=>'Български','el'=>'Ελληνικά','ru'=>'Русский','sr'=>'Српски','uk'=>'Українська','he'=>'עברית','ar'=>'العربية','fa'=>'فارسی','hi'=>'हिन्दी','bn'=>'বাংলা','ta'=>'த‌மிழ்','th'=>'ภาษาไทย','ka'=>'ქართული','ja'=>'日本語','zh'=>'简体中文','zh-tw'=>'繁體中文','ko'=>'한국어',);}function
switch_lang(){echo"<form action='' method='post'>\n<div id='lang'>","<label>".lang(23).": ".html_select("lang",langs(),LANG,on('change','formSubmit'))."</label>"," <input type='submit' value='".lang(24)."' class='hidden'>\n",input_token(),"</div>\n</form>\n";}if(isset($_POST["lang"])&&verify_token()){cookie("adminer_lang",$_POST["lang"]);$_SESSION["lang"]=$_POST["lang"];redirect(remove_from_uri());}$aa="en";if(idx(langs(),$_COOKIE["adminer_lang"])){cookie("adminer_lang",$_COOKIE["adminer_lang"]);$aa=$_COOKIE["adminer_lang"];}elseif(idx(langs(),$_SESSION["lang"]))$aa=$_SESSION["lang"];else{$da=array();preg_match_all('~([-a-z]+)(;q=([0-9.]+))?~',str_replace("_","-",strtolower($_SERVER["HTTP_ACCEPT_LANGUAGE"])),$Se,PREG_SET_ORDER);foreach($Se
as$y)$da[$y[1]]=(isset($y[3])?$y[3]:1);arsort($da);foreach($da
as$u=>$Gg){if(idx(langs(),$u)){$aa=$u;break;}$u=preg_replace('~-.*~','',$u);if(!isset($da[$u])&&idx(langs(),$u)){$aa=$u;break;}}}define('Adminer\LANG',$aa);class
Lang{static$translations;}function
get_compressed($ze){switch($ze){case"en":return'"X.hibp+.!Ma~d}Bo;1(=4=U4;]05A)*]/%aaU<#T+`H;^?^Gm.LNH9wYUZP/w]cuRLv|B
.K9oW=VzLGXS2?WHFfnY&A^+%f>H*4:O1ZuGA%iq_@;3qE9Z2.WS;R>>jQPkR6@QVXa{EwYzb01TafTjqtbAF#,CJC(Pr)vE)3;Ru;m4!ebwVN/8_}v@JAlFGu?*-@s<S8dM,GY&i;]!s2z#vccgA_V$l4)/
TO%wY5SPT6
2;GWQvBF!UY1IR:z,SUY*@_?0AG*r^bjhxBoIEN&?:33!@u2%:=n`E]4&{Scq2DqA_h)hZ@,35cU_J;5^fhKv/g>F{otf/ZZ/.fC^.mv&g*VmgPk*(t?z&;dpQ-gpG7]FF>cfDY1lZHei<L{@c0,OE
0BGG]iY:XbftPx
qDGA164(+3iRSY;+@]]G/&(ai$
abnBT2e<@rb[M-49%-h<4SD01^Lo2Bs$G>.@@(t!nYrj:n{oL^!x4;lI;<46:`!d?!?yN6rUh,k#0FaFN!Qrc+?(pRMcx1K`8ASo*[D2r84ARS8Szq1PWV"(JXks}w)h;3H!yU",W_Q
g?4.s=ddzZ
T-rI
NN2c72?9tfZr6su:7?vgI%
m*6C759rIj;2Q6Z6y7m`g^QiMA-Gp0RW_1:%1]5WJ&J5UT;;UZuA^~34iHHr7|!Dy7yE)8FVP#8{@6_{4Zhk))#HN/3gDO+$[wgodafx(C*&@sH5m"6eR"chk^d#vytO=I`aY.DkVfbF40pZEA1FNP>88acExfm*Pi8;UD)uY9p6hUUZ"S.x2EQ:Bz=+q%#)=P2:g3`-vWL]NYFe3xnGYAc`eV6lZJ7<&#E4(Yr-6P#xp
?8)"J)E90i2
XHEu8mDcEgC7)yGRJ~fJjqhX8_u
EO$?5lD9eo+DQx7>U~<1P(6:)P]6,y9^^zg
JD?Z-QgZ1?&_.qtW_rAc$va.k-D>C=]V
+<fS.C!UT.`N8he=U#PK"(RG7^%!)YZvf1`F5_A2/pEjuV+#_>vlbT`$:iqJ:7`_N*c2HH4oR%1;>)-+]Bu7b^MQpX@DEB%Fa-msu@sJmGv?"A_nJ3P
m)OTM*kYA@/pwC(4SxYc.vL7DO9BWsc>x7NP&Y5pLw*hxS
w,u+Mq`>hV%woA6{ndPni$_EPLh=(eJ)
Q(qmj/60Jeiggu~xY!d=*A
8g&
:,?&N0h"b~^?mW9pZ5uFH[HuGHDimR;AQS1i?-d,dum:f}hv)c5g
@j?y3B;SZEh8g`1VPD2m)@fP!*hdqUY<IV6(t>RhRJz[)yTp(w(yqA2/U5Zv|&":hZ`h?P^(TxOm5tpy]`/mUB%@!)vUp^/?{kE?,E?t;pDaO-4bF;l/Kl@8Rh>B5m~`vDabcR_0?VpDt4x
TP~eOns`I
7
uG@DxnbD#_/&|MNR#WL3RN6m;)E#ac)$_=d@Sg!#lGexvk4MDW8,J"2LFG$=/-pGtTRQ%;GqkJ0gJ6Sj0<Ch34xKw+{W."5J9ZKLS#HCHh8-4vZu0"a6afL<i$-&UQ},?N3Q(LQq@hTDhp)G+q4I4a$sQf]:DFVZiKk=QjvP7m@K0US<?`BAAl.f,@a]p=nW-tFH1_&eY1MfS_cD:PIwu20sS#YmERLXQB<-8F)+OA50PeJ^gD@UFCXduEJNc;Z#EWpjFcLQVPlUa`HB@#E:)I%M0PaPoec)2Q|s%qP6>cj)M[Dh=T-
g]J1AI^7}_2R5;~GK0wR,PK_SX0yb2,V,<}U!QhM{S,2XNe';case"id":return'-Z}%@cs+>$uo!Ori{fSm#KzU6-}-SoYVA=-$4B+1$fBga9j&fc|l|t*A.13>M6~P3x(&Jo8w4i;fHU}c|sm7N;*LUed(p/ykn@[88gbrAhs5pJ"d_H"<<v:!LRj[+<ny?N:6"O])ne=5^xD2^$@s$sPB,USb=?nnLgG"ir1Bu,6mr"(LW@+!LG8.g93C]
bL#pG$/p<ma]JR]og[vGsVL47gEj5;)]BcJFGOig!rP(V+#FAtL.CslKYAkWHAEIVJ7mYM@w90Qm$3FH*Lnafehsx3@-puj&m!Hs>rxgFyovn+:61Np)m>U@7M&"NHM2JLI,Kjk.8D%plpCGrQTf2`1D>WwV!
io:)ftI!2cHqlp86wAxj]ED^IBT50M=YuVcp2?eS*CL@vf()76vxgZ:8wEZ<wfZd{NUQBohM.ASOktmeifA<H@fWu%(3WJpqSEfX._&ypciUQ=tA.0tC/oPvOfG$tlEo`[
VT/"CU=8noAm:Pax]">o$gB&mf2O.yEu;.%HPz(_HkGFVQ5Bf6N@0Fp!AvxOCT(|)cM9=+aD`O
V75d.J%iH(nj~O[FakpadQp/XMoY&5Yqrj$MGULc-qyb*ynJ$KL2{7CQs-
XO?GvE4-8om_?IFXqL7K$9Jyd3m^cS=I,roRi<H#:g9_tXmVLvo[XYoycGH"q[Kl"1&*<olRIGTy(0SsugXZ@+pmk}qyAi2~3rIDM2ip]q(28,-*rqm)#kP9My,p7pOSd!8*B;E}/(3G_<A;BBL7Us4^L^WEugys:"G0DZl6r
6y;/&R2X.:IdXYV}52("B"P,jr5GDjIf06`$wZ9?r<r!R=fhuly?uwyiM/:|^U%5dOUhMVhql7Aml2c~jlfqg{9r7H6!?ed4R,;=L3QTC6(fxi@7Vxl
g=%J7^te1h_Uk?Bi7h97Ai,YtNXsK[@0o&u%WsCkj"baQhl{9lK2.K=vFDTKq=9l4i^hWT8("k0V7^i)BzM"O}Q<B*V;wL3z"
L7XGe/q]>hY=bWQ
kj=0gXl~c,Dnr1m?Bbj[=ve4wuUS9gM!$1V&>NK!3jZxlu_":GhurJWf`}_@t.$xnUNX]aM3MDq1qV5HS|ViH=cS`eM:#dv2Cj:k^TnL5?VgINdmJ}fJEl7rF)5.mC]JB5xDczE;S1p,3C5H1la?M8$,p#^awVujB=i90^[Xwn*9k,F9Xo,!(kxXA,s]@K=^_|4PwMR`9mU7q5NoA,,cyFg`k3ghd?Sw,p#PaPi/#[$s/3:r-$Ts+Z.ZYcBLW1T]&MiYs|yv/_<2-PpH$5GOG)-7Hr5wBYd/0j1D1ftkYnRxy!]r0]fe,vo):%,vkoh}C=RfbVs$azUfTM!o>J)TPJ@(x!Ea4Kon4l`J+;m|<o/$C8;^R;wy]z<RCn9aSLj$)q%yj5(BmabS.wC<QS)&Zx,u2HM
bTuPEa6:z(%rD!kxhKVclUq]_~h}d1kcb]@vdw)957dMaoUi/4mQk*M_p+ZuJ(51EUv6p:YYO.hzd3D1fr9R3hupsbxd8$';case"ms":return'+Zu%@crWb&)qDx*P)`<i17_Ud.QYo_)_:Td@zRVA5O^D`.N:&c|nE4`@jg]&$P_!xxgVy32wsF|N[sw,f"=7c31)k$l/Cw<h,_n0FCHkJ>7!)eM!TADBKJb9O+GE*hl<$[l%Lu{`f_3Y[k:d"ytY(Hn?M1-JG.X3m_&2$b(Bk)7LInq>T!n!0a#/tBHn+j-QFD<J*N$tVt;Zv%g@Vng.Du3VmI4)sAl]J/C5v7[b$<|08!/MXIUPM*qVG^b=|hXvb@02ggQB)j&9bp0<Cim2a3UfarP*!ned(rC$w`qj$_|/FC5aQH?_-7mN(8z=^3beUp(J*^"/hL@k})3R/t"iW)Ow?VHXb)eCk4_kn:G8mcc.:Oc,+(jH$IX=jZARF:VY63yWv$MVRc6O?ioG6y(!o(K11Z}__s<c/p<H6tr.}cdmg_
U53vN~_)><KP>d$l6(p?eYLRXDk7l
n=?5;%bY1_B5@}=YxY*FI@io`gx<"9*J7JbaXSwNs6R]4"Y`/-JA9.%N<smu=fo/BogEgX%/@##A5E]}s)JSArDV"V_~B2JGj;B;A}je[^+5(lgCL+oOp!V?21;1XAd1x}R@:ov?vVn4x=tWlJq1CUgw5FpNPKu3:`U7:HqsFIN[<$w8yeJgtYgHs|IdJ$iIDD@#<ag|QzZV^7$#a;`V4FK!-be}>}y{/}oxQ+3v&9uls[:rGi&oi6QNmBIdRFyh?-PLb9<nt*S4P
#X!hZ)R4Zu_:B$t_kh1P;@%,.}xA*/"g3!Dun1nJ%(DaUv9!(J+)Kq!%/qtYTU8KYJZ)i|+W]^U?m*Wi!fwC:ra62nO.f>#87S0JR"i"v]8&h@Ly:ItLkH7$V0YYW//`UoG*h;o?#""|-B9Vx*px>Jcx*]orMQ"Ij]7k9!8$WY5:QX[dxt8d>ycDaTS/5n#JwaoQu(<Ia)(Sq+a<X!54h@fETIcR
j&IYpfuXU"%j=Y5T`(<Yy6YUs)c^wHTFKGE9pBIp0mS1{GfM-B]oWBibK;S45=2t{lKi0TdN{QkVy?t2HTxp_W1T6"HX0"cQc%22YL,t_DpvS`t%nt)>_8|T/1Jj1r6XuZB8W*NvAJ-FanZkRHrP8lC]?wBD_wa+;5}Qn!ZeuEC/QG82,Qi&L2n:e8rxUY?]4?6%8d0+$v93^RwElx-b|fFe;E)%s"lIA(36o$WUcGuEGLEW;Fo-KDU<%i`:oLWBS16Ri9V.xU_WmaV_rMRp2p0?P$A%zg?%=0Y2{Yx>^^
Z9t+hKCpCi)HlK+9<gtJM8$=5OaY@39l($[Xa)Ai`.B?d|AlPs=Wc!GG3@bhk)=[LqW3$,v+9uHvgo4kkMf053DNY}TQA1E<C$7OeWnFxbn7L%G<(je_/R
d!G
2s<Gwbj
ch^m<i7v)-tK?im(v!6^F8_mRqIk~j?0/
}lh/=`DwB#R84CHgi2hu|aT%%!wssPI4z`E=)iFu[l!"HJ9n,imCNkhPmr.%V;J6{1$L4(I_$2!lTtTp%hc?Q1b8SmzE-crP$
!FZ&J796]K(UI^F!Q';case"bs":return'.Zu$Uh"Z+%$%_fq"jZ3=+q!^_Jl]e<AG<Dy8VI]cBNj2Aj"%z(0",/uL`R*!yu6n?LNe*"KW|&G$i1=fMJ3rBn]OF6sU_wS
)UlA%mL5~Go+HY:9`/crdW:Pa$^W*>%v_4NlwvP:Sx.JA3<U6:A;5bfaW0A[cCwUI5,[y/1.tL8l8Tm4IfVpo%E[S@@X3e,KA&N$V6a](XUrJO%s3#x[bll>1F/3dEDCK"tZ<k!oIwT[#EhTcK+AI7O`9m]MbRhPHlMq_qJ4d=J%q:3N6"-TuH9Y-;|=~7G0=j|qK/C]!bE5$&;Kz;rf-7@@D3R+aoM
denpKv0Fm0-a.":r2k+DjSFtHD/GQq`mF=FNv6pw)1q7)/r`R.a,mpgs>IF7,AxMR4wKt3P$U(f^]1*bU:JAO7tIQX0/<htuQ_BM+hY@unoHd*_fz1{%^],PL^)#M*?qo<Q"{3@qIHWP@4Lx0&
kL>9wPfnWHg;<-A@5NxP,<9!oS9=5w!pTAghf<!rN9tX_!!ijN4sf/=ow`)wutvyjA^RGX6X+h*m@Er/n<*S*$IZurIa9)DL
LldxJV^?0*9x0b*G>8+AkQX$`tgc~<|l/b=NX3)b%:lwI*P"TK#$Uyw9iTEK32T3kJPR|.iux3>h>>1f59/9op^U6-*YhV6Rqo76CL9U;5CjNy3,jHP1l-)M5>L;],Yl8clBdc"bbWp:^"Q[$]6hD*cn%l<(pl1d,)9tH*5Jkf5>3NUEi!2ddk)>KjtO_:%D##QQ]Vdofvs8UHdC845@S(.NCi2L,dR]d
`&U..I7s3fMN&Z$3^Y-py`8:2j}A3:9oNS5rScKa?Np;(vBfoN_x}NzN,8]>P^(i-nSYJ[pc>Lc/*cN3o#wHK$Cs@i~h?#"IXS4Ri/$oZ0T<`FDWQ_3VVO~%E"*-$]
Rtdu=!4V"X.#sg6B_$DI(bI
Za-&L~6vl)qMb/:EU|Gj%MV6v+c}hD%?5/u<PF`IC4qo/OJkff(~n&pj%*;:nX>M1,q)ePAR=+t]+G><%5V@v?1M..+IS*X!6>qpDg6c^ID+X!3V7V>CNdY[iHn$>unE7+AK!G,BkJ)S-~3~9K!E>kd{EBX$lSmpViTIV!+LA0)6!>yxsN$^4
VzJwO]pB#HM5-Ubog1-UO0I!/2C|D%.@D2.,x^w%Bhy)7$Hp_$S)BT,o[oT^!TG!Ra@$@k(P3Oish19%@{eh.R.E<++^Geh_Vl++jr+g/z9jEXuMNFWP%rMN&"/;f2v`$z)rM?PWZ?.Br<UiZd:E<E=6]"t{JcBF6TWUj[F#Eb>(kVf~@13{[Z*ev-/iKko[Aj$nyJh%vG.#AT
3bYWQ8Hrrka`:&.BIpXn#f@H@ix1[K1..Ep(V3O,^INP]3%<rKkR~4*7b`)%Qm3U5aZ!
.;m3Y!1!Q=_Z>d,U/ESI
pr
>6D8lbcNi4UR4(cYhNMKlo^dmb)ke3RiWi"_ji5bmfkW&Q3^b$YMVD#qri8HrUEpnWi,#wUB&XByet8ulJjV[C.)Y~YRRZ]f+9/.V!6H6<N!6Y!$$>"QE6&doqiDDh*oY`N/xzPsXdM.%4/gdub.&n3Vv[3kTCvmy^=84p5!u#VK
oZ+#nxxX.#+FQUD5{$&DSr-@ok^2v[@*UNR.2E=M54bSF[F]l-s+;[*YO[!RO2;-L6-T?BJ5;_>9VtvR21Hum)V3
vOLkw{3LT8I("?6)]QdZx9Ni/
u>[&_>>LI3+-2jJ_W,wMA7B`<MgJb^T4NeSnF4]39Q!=a!=a!Th7qNu~N&';case"ca":return'*]]rGcw-d#@SDtl6&Ir0>ig)xeKiY+a:.;N-k;~/t*tTIfannJko3xw+X/w!s:L_;X"#DS3kDF-&o-oMP-
3(HB
mA=c4B?RjSw:a;hpVJm#?_a
2n7Xb90-##fG!CX1Xgcp
RkTTpRN/FsJn`~0Hv9mzG76VB#K%a*A(b3%X8,934,jlLcH60QKx7ymXqLO4mfbuqh*92KZeF+vs1{=vQ=aPExoxaBkn+Pp&MeW34s9hYx#{u%h
L-xPMthvyH)!pzi8o}GGu4"C4=ag@IVUTJh;ZXV_jUq":?*3:9"Z6Q2g<GRV
]ixNrHfKeS:%F/CwOHfO(ZtMEfKu!_meIUp2%lk.vE*Z@pVR*[g)N!]e!9Za(P&k
,/@Lsb^@4bHDhP,K_j:YwqA?!d`-i7K2n@l/BA(05U)wtD[-nN4ro(Q68@kE]2<ydCUV"DF?p]Tcj)et:m8J
?b?tmcir^A2t^:7NDOiaM3X$P7R4W0XH|9$Ztd:K$(
1DPoDRm{"F]Tz(?&iJQlW110a7%[E$7XtlPMbna4OL;Dv~d]xZ7G#owgHh@_#?Lv:0.Lx"<I<P+SF89_-[KT)mg8vRyUd?,uMwDMD43`^XHrr
X6fD@ph6sCK]DiwA9;6BP:sNFlU7BA*],P>BY)/x!xg9,pS{CNbeoL*,Y+O?^tL-8g#D?+7H:_wyL/S@D"cf0Q?IR?4_e/W8)BtGIN%YMJ:XCS>`#g?BdQ
0NG-*=msTGwk<LdcprPAKbBA|,EVYSbc,O0BkJz9w(?yO5sV^v*QYsh^X0a:AGxU~piy/JblHrveCg&_H-E0il^LF0~/J%wjHhKjjhOeac?0A1[F2wj#<!(fl;Mf:?i)2d:L88$w`6By/_
HK.%p|0[GY(=-*YrPTkKBI&<>m(
Vk^R4{M#H<+oFJa|6DW@HN*OrRU{+E;+do2#x_djxFG
FQHj71Ln9;ykmY(arP:/w#_ioc0~l*RrN*nPK7OXX73G!tDx0QoU,"nPb{?pC8I]Z3P&M2BW
>nQZ+6M&;-lbaB:yQ@i-"x<u/)n"hss_IHT/e$w^QGiDX@e_(ul1lsD!5KHd;hM>4Q,2(c1uE!|tW[PyCYZO"u2
Cd[9w9(M<IDj(]@w&@(N7^1w}g]lS&WgC*Q%lu=SEN=+]6]@.Kndh2NHRC~EwJq:ilTE{I7J0Ec2pUCsa#+,t"`@&5qw*.sYm

5]7d9A%?:7e0>{Y)v&I[AtU)/o-kGM!$UpH+OoK_DoD4b^0}UQ>
gf/~"bN"f9
IWw^f*o.sd^NE^GLjA(0l]9)Dk~(}A4bX,4t+g8LKckLM3hXdLh^DQHlQ>!:3a>$@;eEe4]ve8@kD;olE1"P~S.;@>PM^1(Vkl3[(*"U$^`T9$WUmN,CPW_8DLgWubDJyyK5LG1mEhAM@qp&72o@9f[r/tM+Zd7^0L3?Me*+/PBIBmN2I$(kJ4aZ6B}3`<IcMZd=,
9sR<Bh0g-ygme/>)>A_;*=`XI(jUxZ%1~-d5m2>21sQqZ<#7AN{`v%;_b62oFt@xsgCIbMK+cZ{x4M3:*jCjtFw*xr]iHlP?LOA>A.Pbwl&lf,&_n`9J+q$w*N8uvP,<Dig_9QR.X%bn6f4hWH,cqVGXClY3F:Iqx9r_^6G
>s/4n`b,s5Z:knNreqAM}W0lb_Y3LAgUHTj<+DKq{<f%/5MF9R:+{@"C_@+$+R%2[,y_Ecd-%s5T5W~=gst*I-c;#=&oC;A]c5l_mT)TtX7(NGVt,.y;r68b
Qd4RLa5;';case"cs":return',]]w.bP.!;hQzfnP%(U$H-L>6V_HV%m-U-2CQ)+_bHZ@Yu;s72B)JfnQ,.S^xT$>#)bJz2"4tSH.%(BJW3dRS`-O/.1egYZcsmj1~6zY$;p7gT5;?n-u|[qZhQl+Jw)$j^>=^HK#Yu+Pnv)R)%*Xr
+4t+F.cod-QhasG
1^grY2tbMMH6;FyH]^(p^a>ciS=1R$Z6(ESJm;|pV`Io!1m8$?TfS$VX{^%HLt&kT/N![Q@^%@2.aG`b}E?8O1fn`,*U78XX-b4m=n+hF5?Xh`(&EW=J/!/5wh[^.aML%0xC9%IZ@!S#VFoS;_"[/7==TWO.
gm9,r71exutbB|<4F$xx$&mh0N5y)OyoR#F@i~yHI
K<r_,nf%>c+!?}XlCgG<1JvBCZj2]%;95$1Q($^4c?GRj^)P2:_F]jQ%v@Ge$l+/Z;/fkT<(0q+]A`j^-
5+$@uZ
VbK?>:cT1[9;x,bGbwe,SvUt2*X%mAdTYMRpOg8cOVTy<gv/RMhlvZ#L]j}wnChxlNA+pQ}c;w)(CLO3EA8,CGn$@wb?fJbMWL",*a9d(og/7PLB"M{N<OTQ-0LXhHmeK<b&ux-6xfaUuv?RA/}yvb{]vI/kwV2lVvu;u$~d9h>N*]h%7,T@v_4,;E~YG/=x*.~iLL|R11biD%K,!/h.:4s$%73!q)^;E
]TjYH!ehGePm>FevA:W8ZT)A#Aa4+f7WbH"Q
erNvNs^YC/&,qmL//**nd&m#WxBrjWME<"wC*gI7x<-<PF(!q_=xq_"54iibt!@
Heg?rXV]^wLGv7Wi#)U{U|SSydKXTVNT
rh_&BwJ`>EOh7+1.<_C+|bb2g,n)md{Pu]Siz8Vf9H)6a]C&yB2
)
6(%aVkXr`y%9_n!u+34b$u#ePTjr@`T[*wtn0!h%bT?0):-1IoBSw,.))h,nL6-2eB>wQVDo/i(Y8_P:vICrvxPv1Fk;?:eL%XtB1-Y*?5")2]hd)r*vMu`I5${S?24NBu|=KA%?0r(S|),]yA0tspM<hdx@HJ}@[m]uN7Hq!aj0Z5+PXE7EDg~8DCjLC0).2-D(@!+]+Y><A-nl@]wpk,Bhh6-,4D4Nmg]Vnh1w(taD^fyht2-AiUc(,X9W,xa`8=OMCx3QT^tAAEmZ#<+5EQ|>4:>Vi)2)_6bIL@*Y4EW!~L03
<]MyTP]qK#Y7+{?lLf]*-:kWE
?VV{N4r:e3:.pEHV+{fGv=bHo))H!i-iwrZ2Bl"0W]IINb)eh1e)j+Ix`f16jG-qb:PNQOI@*>c?1~^c34I{hlTIR2WRSSIPV"G[RBkq=5$/?:SbXa/Ut6)r:3k|!X0)$|[Ec+`D8tQN&FOa1BAn+6
d?k#R=u=}dtW|Un+8dn*,1yZ;:VKpDRLz5ZSQw]H-[Z,0!u[fojg#W;42cCpn]6-3WO)~"bT4XOXQ-0lb`e"!yTZo2wEc*tYy*]g-SHBm&ASKo`Y):l+q1+fhI4S+)[/4=VIN*6Y<8p>*J@e(YGFuh6Cr8dHHH*$lD%+$wb<JB^]6J9mS7O+yU:wA
X0Qf5uD8Ix9Iah-:N(_D[G4]ylTTe
Akn>~^/6E/vO@(a8ZcIbXkP]J`V#:&o2lJ^wpI}/rB8vN_,>,Q&KPTNJl$y1zIwc}Ur>pr&uL=a"&:g<R[<f;9B>_i`oD4PgU__Y<tK?wP
KxS,_VI4s7N2MZ,ac~_7]R!D&Glq#wb>-t&shzJo+%3kFa;r5$vb:>NDaV=$/f=.Jj)+Z/0_pfx-bk4c]rIJgq/S*yc&8hp3I6-)x$yA9--LY$X5wLG&<+aUU39VYLA?>uT~11%:ZLr
:S5d:NCt0_jtOe&6cXt_*Z7)uSkFyO/pL4YMG"H~y3?)faIzjqJr5**2ZSR50#HnvOYFy@dkLvH2OBA5<^o^a;0@5<`/Mc-#';case"da":return'&Z}*p6KWB:u^N?4:KsB8@[2>$Q[NJ>?3eb_.YDJ2.NFAQpo9JYg^9B,tT<lsotFvB=M<|aSetk0*#!yP8&7](<+xWd%ID,k#+s.JuD=F{a#dIArY?Un:(AAbl_[irJxZR<`mn]<tc$9*`>8e#Ro[#G+_zvigjH#chh[6U%Msajs+rYuFzPW)+ZRLyxcx`Ck</J.p7DJU"MW+s"S/PB`terPaJf*Q?
X/AZ%BHQ[uQ?&g,d4l"$c`u31
Ui^PB1.l1rnL}b@F=O^L.WkeN-)0zl;u|rF/)]-]GcH)Ulq1rAY->E}B>"RxiyGfYuE;=h(v4Ij%Vf?Tae|_5CyclFbp@=F"8A.]Tl
+H7-!,B{KkZD4,oWcVLG,av
a%(T
[[ROJ8R.~cPWjiy/~$%6)ZV;?(^X6n&wy-No|(.P@tx/jxC,(!H#,.$YBwai`%&oK)zA-&#1@ZetisRWo_D!n]"cMo!Y,QNb6RWZj][-Lf|)](FRY:2<7>}Agr@x
xd+~!p2tkTTuL<?g
5pMBu%fWT,r(P%]F>bTALe77mZQ_~"nOw0`[)qDJ^y7b5x1d<fuh,0WE_HAh;6L#6Xm&x
keTke*JWm4z6Pc5<gtAGHkL)rqjDF0>f*>wr3h&Pen:*{vVyA,/X6*!Zd9LnCm*L4%H@Io_`L?YWZ*/t@Q`xpV<M6?qT25+mwMSn>IKnT_?v5jy[[(Kg?#<OE]MT3sNF4S0#L"oOX$>
EPmviy!d%G(PPd}7/qY%iV.dtaW,3uZ<Q?2
D@olU7=iPx-3_1gEGUs7S$

,cj;}rN;+$3/~<!&2.aclVK$:-Z
S+{NU"L)1t8FSe/i^OeQ.^+_Xn|Z}Lt_DO#l1H{_so,XSBgjz0G"TGH0D"Tr&7ghIJ^>J,4Ob7gbV-KhTGNgh]bFDxq`+7"<nwHPJ/[N>5`+nSc"1^;%?e"6/x#nU5-:_TzpyX[C?d,?*]`Z%HkWpynA.1{"FmwH$<^Y(^].g[w(vbgCZ4BP@LvRh_oD9+mifLYhvy5&<_?s9nCR{3dbMfHg?6oUDQy?}emT`WB*e8]n9[ljW6"L8a68MGPkS8FF+7lE-vpcDf~hXh<Hk+uvQXs<s^wIQG^-E)+c!+OJLNO?";ePtBwuB$AXsKk,r1YU[9|XSr--[E8oh@:A65t4xDJJ;#Oru7[p5xN`2#aJE%buqmhJrT[_fAB,]#<:<Pmvh><;&$Ll~;=4
,/P-Kk2F]
eY@2[1>PN7&,.+a[w3NPQ`UfYskc]|7tDRq[8-,iCWGk7y=Qk%%c;sh|
JAZ3C.S1?t]FZ@2*i57+?SlqxyX+s=N#evgx!;t3ZG%JNh(NAcJw!VcOq2X(!:m%+2yJD9(j1=YtYRNXMvl<Ra*!!Ku+y"Vrso]uM&#O
A:ETlJg9-%MVTG*2nFg+f`=GA*]~CQdpuYI}."@*V)m6@o(x2BV`W&e72uKDcn(Ja]F3=SCBXk7""BJ)B.Zs7y
%`Aq6T@kaOX:1(#$s,GcweUT)6tAwjS=:YKk]JCy49C":yo)P6/H|+ld{-:/&=(8/x^K{`inKy+0u+AH3@W!@=;.Phcvlo#^tggdBB"3/_pZcf2)+gSdTDXijDU%fv?IB*8Nv';case"de":return'+]^*!bSZ+;hPWq/"pELL@NJ:|/l%A,NeYRp]K44.J)K:NOz
BwW"!@iu>xPZ1uo
JyP53Tksxbi&sO"dhAz$1W}L
qj5Yp?vj("+(W=""(MeTN1GLbHlx0-T{d.APfjCmjw
7_p@o0jl-_=(sDzjSEfg?)98^!LMr_];!<}(gOg<V/E^#%U$Xb4FuW=LmQenX_HM!wn@NKEb9f"`3&#/}<yloyneFdFUYd8)PG^V,jNtO+;FI=b"bDOC^awUS$[:[95X/vheE8b4"t9XzVN__.)aT)N_("w/gmqBjP/AAP]0M@Pp$?/_&5tma^CH|59.YAyc<N22SMODw5iD")rr}IYS?5ZTTMnhZ$&0oAePvSz,|50*OiRcE/[2hVzjfi|^MqCy5bOt.I!EQ!~rmCUsJUjB)[xW^,7lhfD&)*f:hDcs,6GUw!N_HC_v^Q_=Ti8?r>C4tcz,LAE4[f_Dmw7gA9>#+2}sw*L&`obUoAe,h_<r~x%Hj9("}!^X_xjrzS4S)]Xam4}=pai[pbOV[ZRGi:i03DT`@#*GDm5[>;p;#8hVRPkJmW)=~.GO9U]bD+**ldiBi#<QrK)5PQJT@BpPq5%-7nvySDj*("r]WD,?GtDrTxxE>t+8t2]07O|3]@|UK)-Ktd_c~FlxUXRdZ5li.@3pHpHJd0T`^8ynt#=]RAUYFSWX(+=aHgD+,9(Vss#l$$~u17U?W1#;d%xq;@M*kom($MZZ>GnX!H@g.cV<hGd)YZ)9g_GIfSic+RDpbv^[2ka@!:()mZPWn
|^NYgv*+oB#r:S$JxEiKU*xMB4KdQ=uOGLk*j/NT{Ot!*srxBYIu_CZh+3oR[Jn%?10Y/^Q@4L%[5rMOu[5W+EjmfnlHgK**pt
5c>?=POW35U+qsPyv$Z3%;D5c=Ah<lXKN&G~FC5m3y`KGESC>+be6=ZZL*sVB5%X>lDk+-caFS<&df
B=9d9h?t+BB$MaOhH]|!gRVGJsuLeJ2`
,89KDV@u>$;rg#?YN,%pdao8S.*9x.N]5mY,x?BL)#<UVapI?(^Rw%=MyC5K<s!,!`n(M0&_Cng<?S!(>~43YbE;]-osxq^<;kNhQH(T"TDh7o"AOuGyIQ7h4p"5y!!8[7n/
z=0SF;F@Bs,z&`owC?]dF3m4|bmZC^[L?H=kL7>,Jb0:b$0uJx~-gVXa/19%A5<`
guZ"+|=`pxJ;V7&$)ekMj29yS`j~X3_|<^J1BpLtjZ>;]APvD;CrV0^)+pqxV@98mV44Jz7s
V/$%0xyPVGt](i&aX.llVA1e:eo;3Ds#hraEb*^5.PT6Wrd6QBZo+;2REE(CCEKAWYP)!&N^g;0#*X%QJEt"?%$6P)1[z8d&F^XSV*l&@"M"VbKXR!-[*<>"N+<fV[p<ocLO*Yf+aq-/5I68bGZHwcW^98E(,2p;<R{9v@wChQkVdQG?F)z#Oa("A$]$@TZQOSGCl*NMxpx*c2x1[bDuE@hTC^26;:UqdE+D+fDVwPF8J&N?:?[8U%s56[QUv$2jst"e`-y3h)>_"g&[+cVqT17Ul&cpFhlE81C8Tuc]EFO+!T>ALw+2gNCi!IuuRQr*:sqt>h+_xG:ATGgGFxWT6?j00wT!5SbB(9K#o<`WyZOV
bTHv`
XC&4WY)$fnk7^t.sV>tsidmSxXH>[7=zgx
K#Ie=c%]Pj"wI^OFSoRbiN
btAfb:O[hk=Vh&GQ3K()pex$(0KZ$9na%zjjec^,&;w2&:4m(s$JP`<G5~dV9di}Q4[AR:k?CRLlbF25:+@_JwlgQW@lcZRwWbNN$o`f;GKcb`*#-5[jjFjswMp`rZG3sF-.3@VRHN&fElV(jCC3H)lKg?8rA"guJiI&EDE/Y{Qho3@5oWoq#ylj)_ad14SSO!={u>v(:hcu!Q';case"et":return'#sh*wbKY(&;Y")1em;*`":2$hM`gq)s%srA`OJ-;Bmr1^L9VMT_J9I4Kvce;,&K`747Rf[1.jQ1.TM:K)hCW`uD*y-~2#"/*&x2
r;Ov!Z!l+k(pV;.NWsz]I$EBM0"xIu5u;XyqEj:5Q::xH/nb?c]5A##haw0-m,R)n;OIB[rf9&ra,hNKyM"vn*,W|CzZnlKyfAa]nV^dlqJg]5}yb.t;y9:seSf)TTf?1`m8a*
*S#I?wQK.vU@udf$ima9nmYJ[1>4;@
tt1cCN3G"ZHg#^Ejnv.^s66"j<ivaTFSpW8*PyhwDXM";/.s_,:*USqF7V~M#LtFc&%_KX.xZc1SoK-01kVau*34l90&(r[RHy;yND84P,w9gL
$_<6tNb0u>9y[nThs=iaPN<|Il<&lf"u"^WAx=e.-_&M/hR`)6WMSk]dD2Fc#QSHL%*sO5TFdrYgUD3rkcPk?8:viPb*[c%GIFf<w?$fh/5A2>o@7gG^WF#_#xI/@S?<8OsbB@u,5+P75ih)]
H
sMU$lecPG]WrbCc
.]-A3:(z@_fme<OPE7x+PcO(o8mGN$9FXtOH
;3TjO:II.HZ!6PiADT6p
V{RM[7Sb)LW4!c&Vh<=GRLyP7)cP@Cd]O?r~DP>PC7VS-]j}ji
6jQ+OG-)(G3=c8!R+$C*K]SOXBRa"R.sCTyMP>_WrvS!b#+33&iIt@>o+3Ch2
[]!O{F""n/V$f>nL]UXI6U_,Gs:oOe[F&*]@f6_ZK(}RBf/F
QZU-]k%6
ry
fcY-7dOC,q.MM%2G-|JYs_a@_ZXiD!P!f!#I*_#(c[F@&Pb6qQLA@Un?yLIC0`F{_Q^jbH#4UbO?!gpyY:eQy.I@uAw"(14u1#>;:#$;o-c!xsEKPvPr0Q+YCv"]yh*w7ooPV?fe,&CG1]L&bnVU
HG:QP?bS1C+x=jw1&R~grwzNgG0&k!3#oqgUuGzT{9>Cql>E>izC78DEt(AqVWHiY';case"es":return'&]^*gbPWB$"S,mo!+6VF!FHFBnHI2NA:FYu`h/F&2bJBDjCYkgF)f
gboVH-d4cgdYF>CweqhT:>M<l@rGSl800g-5mE7x#88OLGQVn#O"E,L,5t[Sul5P12[x5QOr7=
(2YZ##;Nc~0n@g6eWGL6ei-gwp^&/v"#a"FyP#NK[g)_i+i6P:QBN>`5^4?,iSG|@?u.U{kNz%uuqI6:yBW/xG21
MhXDwUW05f9oQMb,vjA5tm!,M-ox;fSID4zr9a2bQhH(/f#a0T%nzYmJw5-Dy=<;>#}+kS?wN<rpBc&!^o5"IH31=J}2F20tm,}RTftC
3j8f^C4R.6J1q~
us{3x<jGpW<Ktrn$IOSOjhK8v&*:,Q56kucW9f3OAm3Lj$)0,E
xC5"i!%<aNhHxwt7+aioD.,eL_#&YB[LhbkU4i/W$gvNqzgMopb^SC-dN4@_C8FS&Mi3iv6>gN>%AvY~I(RbF`Wb06ou)[%sYg]D6wt@
`#4)/#;Y@<7ZT<_2MY3f![&_0>R"o(4ykZ".*1/XY]Nws7_V:7s-n8hI(KI)RRr8OeI0u.cT!s/arVQc+k!06CJCH3)iXfztai!**Qft%pC64+"O=+iQ6-
%Gd,d2[},6pZ*Zp:/byWEe!]""_j6p;w))rR1#O4lAv9+bnkv?Idyq$%T}tHrL6VX!c9yZ#~1l?`cGLy"^&QC@>ePTf-dZSc&h@D>T$%_.O1SfHEYt>0pBv!MJb5vX@X312>.(xN;&CdKNDxV?"c_sTFKJJtt[frOj
q<L/jC&0e=ZWK:[7d.xD)ZwCET$)x@S/G0Q]fKDv@2N%|lo/{iyG?z(i6d
c)2*pyRic8$|?%>8E>RbWwWL>+[-x.uEZg9://T!
~Iqr9j8@o<gj/C}P3-D)+5<Le$5-pNHoUFe5Q<R-]N$/Z_Cp,[}ByMZUwRXxMyc.BCRxW,:WQip8},Ht6aUZe9_00=iKIo@X%68jW@?ZV>:8+
c6YwJ+1Xc
`osbrl=Y*]c>Sga0$xalOII>C4d],y.VBvZ)LyP7Hcq%FDO`)h{Y1X|b
v_1J(:?<:fqNhjBl+|8=#4>_C:hyPt1>[=ZXxZorXIoO5Go=.cpt,&Y;Zc*Nitr!0$7i.VK9":iQhtD=2rNrh7s3NM&za`jAgG3t!y+sh5&RP=#s?[F}e?=P8`=N^l8J,@dXMu./w+=?..U}8|J(a3Qi`^9?ho&h.19DQjh%_dy#)woUum7e@N9w-h7aU#<&3HF<(=X(hJkkvxkb();4i_8y*a)+nMfKL%t*lZUWm>Z@,brdVb9O2
#i">^FW_Jr->B`Q
3!j(W"1hQD^kJH$#ht/3dmL~>Y(U*[<RD/D&<ufePI+nrF$Bkk*~0e.s<$vDT*-;]aU|loVy;eO0#|MVO2qxumjZQZ8V3jj8^G,w"~NO_n5`kRab:k_B6%[!)bpj;r0)NwBf.rTHm^P62Xv_EQhaTto=BV=Afw",Wo)d
$<#:uCJNXjub,SA[Gh"e`4``gM&7-;<]sy
&z%6Hp9s2(*ms}+r!yjt`?^wQ,T{hON~x/Q`X4O+V2
b/,vhP`PZUyO~/$RVmevvc:9daRes]=Z=SkHq05W#4D#/vp>?0&
b
E2,)3MfT>"|X1,jA0Bg*<h0>E
B>V]Wb`/iF,CN?LmE-PE=JbMH@+jV7N&KYM1oaaQq)=!&O"R^M91=#!y!(422fB2G)Q;"AH$D%j3tpJ0=R]stZZjF6oD,v_>]jbR
*7<P<QCnNNAP2B';case"fr":return'&Zu/_6kWR%fA6s?&|SV]&bR"hX<$iwePRT<:Lo-
3&%0/Yfr"aSA`<zs$T">e<!!w<]B|=Q;8e:PU;Tf~.
d,!:0.:8iN^9i&cY7T_8-:$".ye:l"i%O82vFX*L/?gSHkkBD7"$bGwtC`
5x.>/nu"7/0;sVlM=
[s_m_q0JVGqvE[oaXyI>~Y}jxb;kZ/Fox,WHO]vlM5_xccBMr=3Xp`vc&ALB3KCyao+yKn,E=w-P<40#YF)hSl|x@nJ?JHHc]H>?+[<fPxo?|g7%hWnOP[DYHn{*ArZ&$=n5Z/Zdbey7!k!_|3L];9EQ;&g/LZNX5FmWaO8WE7b;GQ~StMxyBqIp9K6w^L_F(x4NESNY}AM_w7Z>D?0Ab>2pf>6I3?#/@)wyIj~pJc/)&6):n*Prr$L2~T2o%:ag4uc,
=7i~XM(`00
RI96(H<$2wg/=+5Op`TK,RthH6[BD>}3cT}X|NR/!o)#<wL.>T?Cty{?vY.SR:eR64D7FJh9)pho,eRDuYOwad.)5;abNPr-u2z4*tzYES{-3,d+q%3+VZA16=lo0aL%7$/FTw)_#hN`hE=Tr)"A[7B0GEuQ|H
4@$We{OFH`)Zn<bC=MeGQ
!g=rcL`pDZ<k2Ug%k6?Cs%
b[r`>w
-AX1GD@i```H7ZFB,oX7?l&/`DmAI"YKwa1BV[5&Q<m@hf]}(>K?P?`u)[dzLdT0oCe3xPX|e"#+&o:HT&i.V:xaJ),X%Y]Y4sq*lP6"1Pf253l<A71FJ.?qG9s{$2:cBn6B.0!lB4vU/gy]B.a~VmS<qgW724%Ju+plW#Sp`![EZ>+RCi;#hc%;[cIq_>^^0
$>ZWBZ]6QM3lGMIy*Lx}BY^1Q}MMblh4>3;pRuJiq3`5HDTh[z:y;gLA<`aDo)PBp!j>LT2::P.IM1tfX22Cw82Z4W0::Ciu40N%d1a(fB`2Q6CAJaIW*="JnV`xO[T3%v7O-6Ey4nOEBnnY2+T1MjrC:4k^DmJZ$l%
%|]Qf,xNvvc78z`#jWN,T:1bhJdDHyxIKj0{&34?Q@E"]E_7b2dPmpfgptyB+VE#/z;{`^]z7wtY"{i$yXMT6b<wEp*#m$.lQQNWhy$A7aF_uqaENs_d+&bbYInOyt>7A26KwWg4?Z/Iq2hHyOB&vy-Qq%*i-+S8a3A+fmRcyP5_]Mc;_NWh7B6!VI/ODQ-KEffIA,/wYp!:?%_v=A9
djO[UC9/D
GS0Lei29Q3y54?2zvQxM9,ZJ?NjHtKAiP3GYcN/(YAZqjel$p_QXMU`6%Y(DJ.i45Y4ei~IN!$rD^BC^B|j7-=9mOskO8<wcR9FT+c%=Iwqpc%9|1tt{3HEbAu4aD=Aj%kKH(C6Z3|ZMs;y(/!d(C8Y1RZ0K.ZbL;w8>:efzuHN)@zYAUrU|%{an_N:hn<I$lV?PD~4n*`NX#oYo(VNlh/Jn[b-+sR$Teo^$lUJ}9AhI1U<of"geo9i^LsG+IPItSk`ap!fNRfH&i;I8T7(,%(XZKs!?j%F,:?(P"(Je!:c%JBM2Qt7CWvNusi^->A^FJQy:y0)|4?m6nw%-1I,rCtB;oroM0&7rE}H*=Pg_H:=j=dPH[:GF*6REH~1P7J<U#Gm3NeZ~ZxFS3l*QDxO{8L$;=pu
G|SE#0`Ot}k:Ad/>>Qi@*hX{q|:W=k!t-)1N?P2Mh^;aJ^Y";"Nwh$(CO_,`0!Rlw~.b3751S?.gtWp@9OLr3yZR;;#k[YC7=;`V%qqmG@UUXmTmJ-7%pN`%aR"I,1c<T=ZI"2';case"gl":return'!Zu%@]A.7#?v}_#"6:r%+o9h)ed@>5wNn7F"b-BFZr{;.`k<ULHp|s!l>S&$*$yb?qbyiBtB
^33k<X&u)_l.FUw/
d^Aw8Vw#Kx$3OFmnwKZORmo!C*~$ZOC;mU/b6#iFVC/G1g~K"!HhHO=vpPQroSBv$3>_uD#%+PtLt4^rM,xF3j>!^D.M~+?]"iBGsK(<F]26Yc;?EVY5bev!@y|vnG{utfIK*^E*_eVZZ>S36Z+x5p9fNs5"(QTpvMNHibF;IKZz!3HP$?P:|hrufGV)aycr;L@oPIf[%jd]C>Z"m4Hc?SDSNtLb9m1(}EKkgu/v7f;"fYQYbN67?885GR-gWojD__zd>c]M-XFP5Yf?fC;unkl:YC0,Y</":.D!##L9p=Q7H^ImmMg.-MjivP]R;p.M1tiTDG42<0Yv9Xk<-Q6U|J).(-(iC1L81%<Ew-S
5dY2fUZ2FGB0ZJ>JF9?#7N3xWj}m{$KZ.&|N
HUe*W)0$^&4.q9.a%MUn_>]yf7#VGa*2*W=zo:opqoI~ndx0ttR%$;$)u+y}rX;XLRdgCc-32u25p5pcag8v?#EvZqK?(#B,7~T-$8O3mA,WS$d(1-05HK3kL=0Rsav`Fr&O/0>&F^_h,:ot*}sbHkRp*dx=e4)MoIt$ZrBZSC2brF
R:ak"q#7Ts*v6M*:ejnIV@1ir>fyY^!2|%j
=u1jE
)3$i}SMg0+aB
/]G8+!#
bFPJ_tu
]dro^?z&>tBG_ui8rMmK,#&,)Mv
pPR}hWpNMq+uL`)YXpe"Q]g3C[[|ELBj(%soZTFROD8o7Zfe#WrB
cQ!rqV[,(I:moNpfx3x5V%%t!5%/QLdGX+(rVw
XXsE%rhIX^qFecha(W0v]fW`?N;uMbqPLk3QvZ?gyc0|uW8PBf9)3V.=f]GjRgs1KEqh3=XQ5HHm";_SK;8,lMbq"[`=#--|wNSH"[P=[yUEuzioUDmQc~tY#8-=U<
GNc[Ukh(8+UCu:s+;7Pwtp&#W?$B;+O"hu$P[+o4wvN?L6
7E"^Tm^HNLZdTun^yjGqp/")9~(NTL&z,2)rE1s@x;t3T%RXdA.`pOjgFCSUVK&V1*Q+FsnmC.t2<q%Z1Tpl:qyFt[HE]ifpykCMhy(3KtoZ_EOubcJf::h~`f^^k"u|=w-/moYmJ~A=04%i``f#r[_fO#kW"N4!n>wO)l.y)YCF2Zy1Pu&`w9*Hx.c"p+H
2)PEU>N^^S&g<Xgm#|k8[
?*kaI6fR?Jju50s2vgb6^0X.xK6UM
?oe+:LV<-6>](+I@xStoJhPU^WQ!4[UE?-=m-,^n
MSP3r[_gPY_cLw+-2.H#:?E9pE<i]$O]5QE9?K>YZ?K[Ha"^pcxO;hvZ>qauKtnBQ>6ya.7_nnEwUKJM}/1@Vo[MqP)hB"h]_oUXM%T1KqH.W=V3sR"C1^7po[N4Wn(9?O<F@s/:JW%a%OlC<[9IHeQ=0*-5M)rc#
{3J&_Rwpzdv!HIV?vbS5zoI
CO]!kX2[J7KxW!pDx
0ni,{3Abr7[[XZ:*b.j*z$_V`Bk7MjSv.]x7FQ"mzYQS
=6X[W]cJ0~i~UCEh=ClfEk3s@*n5uAl`nmmERxXJB^x%@;,!Ybhqe+`R/t,tLTR9e!z!<cKy7Os#o-4a[wGTTq3&RjFPWjqio>LN5Nxjlke%(wGmj$QG)d?I_f4PYs6TE>)0Cgg8DqU#QZ)nO"xM08^J22';case"hr":return'-]^*!f|+N%$INs=%NL5GCAhO@Dz]X;{VP@n?:".w"DR,=Y4B_eknr&t%YNMt`p#K;h/Y6_juG;:1+,Cd4N93oEbB@kufATwO`_^N|v8>
otcbK1&b<DJXJPuK)pB_
F9&wl928D,}]z:FHlgBd%Y91mqm3uY;,:icY@OD(Mu=BXhAD,7ZA0eIU5vPE9:@BzPs&E8$6T#Ct`g@MG)CBrGdmy`a8)B$D"=I/O8h)BY6r2@gMgUxX_yf1tR[o<i$Mttt,r7}G4$gx5`u+U"Gj|N,N
*1!^cX@d@9"~HMT*(Mv6mv[g*aX11TrlDXE>wA=GcyS~LYPo]:YC`97b-F50TF#huNl8]+N1<2;~)IBkl)7(/Y"a8BfJ6d<ZE}2XB`6`XOg,t7m]%UuNxL=mQ|rL/SHs(%UrS!f;659k&H6Eq$b-uj2N4Jv.svr3<DDA7Af~`z:=(vp^!OUC-]nC^XaQc+&inr1R7tI^[2*h,wxT1kSz06XJmdHyr9nkI8,p
psc#E-NSZdss|B{I63Tl_6t30?YxI
Q2};;3n25JmowCS9%MKCqF0"jsPcuD6N+x2bzK29B6bUl[
DNI0!*XML;ly6R8o:v2ahn$(3C`tt/#U&p<01Vh4a&VH6H6&Y>qAZo-?2JN#>8r<KLGS7f#{ty5B&I$!7i5rStifaJv/6wz"3?_S<FZSgtu*1>ew`jtfN6,4;MhE2V<MsvL}(|
8^cn@#Gbni|5Vj`g3Rxv4$Q;Ge9CS(8.x1B?3-D2goML4?e[vcKI>$/4^6o0a
DFRaHf$6APT`8!sYLcEOI!`
{:D>1W5%=Yp/8P,6_o4P`JhOjpDJ,nGMMCQlyr:
9%">wd&6fcK##DsC=+)TyPEt>f:$Xl#[fYJ/lR+hYJX2[,yBCN9d.o?iAj+Osi7Ca@PEl*D-c!{dUh(Pvruj/_:bFE+9Ww.9.;.]Vqd^<U
9KpHAGEDH@YJ6;23-
OGczV=5[[0+XGCAOa,<Go7M)D%D@B2tNO/UHt(s2y[r*WcRo=o&)$1FI/=b-2v?-c9KMJxa6%9%,4k:`VLoG*6v)yeXB
-[>%nOM$oN@nj/
2Cz(0Y]eb:=pm=QH<$-.yQG5@(!L^rFfUv=iP=I(cWl#XQMTcDGugE=;"&+B/7N{E<>z0~C6(y:hP9(9JiM8*dh-`wyYo<F8$C8Zc`
.g|R+-2DK;D[.>OlVvP4lE_;hXYv`fIJ!Sg
k88@[Ab-PLg,if<g7S0S|?%.79zKvp}IeAru9`e#Q*2$_-J(D]gfVUC.CQ~[)i[ulQD^/U;
U
0`gp$H0hOKb@htgQ3oq/N!#e@8rnOLRt2_y!FQno6Qs)%7!l)uma{*YP/Kx-iIxcj<K]!R#I^F"`CFnhqTRpKO~qEJ^1W"I25NSRa-YAafpt}JJDk-(-IC-+zQVsP3
pmp&jJr|Zy)zY@5^u-*A6TY{^=&P;e%4@?,Bii;e&KM/geYR;qc9SL3xgaJ,NGRr%:.c`}HG!Ubi85-0?S=-*
4c8lt;h:rd/P7#_T]{>X(:+xocaa5<ia=U=wEzj50Xcj^qKa"uU}C8k&<^(bjp:C!(pQ1<NhI)
`<$OWPA)%jgKNH$
20U=b6J#c6#3!C@.~G"@Ebu#P0Vd=bHf7jr-Z^_YT51Z`iy_9&20"A_19KGI/E..Q%Orp+=NaNCmX9xIsf@g#7eP/ZzT$,c@.l]W{5]tUfa:|E2CS$5=I+RQO?C!Wu6,*NetE3L]zfNM1YxP6[q0[l|"OnP?1*F(/66H)ON+L$8XoaK-7vuY93Awlb^3k)I)u^oCP+1H^3@c_6mxg8$';case"it":return'!]f*obL.G:u^T%TC6eKe_%m93jK(zRVlAQ0Xs%b+"K"Ji1d@Sy-VKa{9$Xq*-="b8l|[.-KoiCHyl0kJiazy.GIP45{s8YA#oir!0bFB]ZZTP8|a#M?F)$9md)G63UtrH5D>fEs&.oE>vbm;,.8sUy#mrBEq>d_)V)|ys.OR5*0v<DF+L#pG`vlx$#:5cL<u^*N?g]Zr>9*wVV,lnJKx{Vx4v?|jS,6p$E9>tO76TYY2bt;3Y1v"38Y<V0+jDB
7nr
0];rdSpPA@$oCJN*NjO?cNM/rK7(K%sPxMX+2Dv/xhNf$D6owFbHn(8zhid6.@:Te!+|!kgXpc1X,"Z5`aWA"K,NDQ`&#oR=-[F7M99YoKb5u8"<3h7Po!`ENBjp%(mL8453aOX9Ma
+_:0p,H/Udy*AQGZfs%H:[|@U?%P;ZfJ!qS?$Ib^4i&vZtgC]xS3M5[gV%Q,Bd+V,=vE3MYXvc],lkh8IpEok3,PWQ-I%w|vWS<(K=o;{=q%Y.L15i<nbmZ4)O#n%I4k[-O(:r6f%C!-D-H6CR%b5<{sDCReZAD!L(,CkmNo0,{2H;/bUM11zh1Jx?_tQ!cSC3uEcE2lTBP]:_%U;[JI$NR+odGw(0fnU),oDM^
HAb0)BQ_U.ch14(#M
7d>gD#fyT3:FU)fgLh*SQPk`dK}Bgk3vj,6IwFJynSb#=<<uIr|spN1sx6+WUASm<T#f(xxLAQ$;Z*,ROEam:n,dy.v&1yKdzDok@s52M,?Eh.WJJ_{ICYbbEW}p,W@4CN,[>T!ODgT`-R"+QE)Zhd/C$Y{^iuRs(Y4rGI#5:X{a1CM;-_APDlLl7>++!%>$@iOL,mgFniX`]3GB*H=Ps`0`PRM;j]k?0?`T|l79Q9R1f7xJx4TH4iYGuId#Z,pp6RKS33ya^ao,h)tTQcYfV$F`1$ZOJ:@CIEVNS5XTr7i<~uaN.ZK5r+/O!td/=9Z9Ov~.|^^lk8
2/`jH$`5gZ,]*O=
o.:J^No61*fFg-Picdun"YfyVMx|
~v[C
(Ct:0uO9O#@.?/rgrjRn^*fnd(jz@fZf$LI4S,1jFUEbu:P"Au<R":`H*kAK[:-^oZtDg<.G<v$jY-H/^2*RmmU=3~UbMf%6id1LIZ-X+7"4$CXrQnT5rkV)n$^I;t@@Ps"m5>bRqja>@.t-&gR_$*c_0kE[KO!i!*7ua]e(
(fI2R+%==,9Lt1ZHu)gP^Pqo6o|G@W-eLdovIoUxg!^kL(Nw)W^2(Jm[=IPJjR.*%=6:x4
H$^ay$>hq0%i
Yj&$@rW,bM}_5[*<LB^R`.5]N+^Q6Lny_GPtHZ.f
-Z@Ms+F/&6F#MH%j2,f;`fQv<|jh-9I
-%PRHDElE4:==ZWYbqXR"u_W=}5RmFNXAwCuiiu_3Goc%u=&(K=il34Wh6H7@II+aA18.,/)Y6$Amqo#sI?!A9ex5OlQs"%&2jPeUSTO[-AI:(;$`BySL,[}2f*xGz#)p@y|C,5zsaIoE~r*NFK)HGdk?~uxISR
K2*m%c[F$2do`sM(%k]sb*dZ?ibBGeU"2A-|XbiznTEW4-j*PV&kw=,`';case"lv":return'%s`*/bT+>%hAHlC!1QV"N!qSA#O)Uh&2b,{N6Z})mG4gcUo9k%u`9XpBJ%1pnpo<<](##p_pkJ=0)`T5>wo-oN])
"hpecKFEo&UQp9IxA!%)2fg;vaDNy,lA&(]62PWN_sVM,3!#Y5Lps@9KH*PiAQW^[V;lYhsob=lRx*,2";
VkD8VdhQn32,cm{r!K"
^MBBC1ZJ5"et2f/g0K/,"M"MRpe;<twhASTZ2g7MyfB5Tfo`S[D"pSD#?@vZ@vccuvakZtOi7XuV*$8vaJxO$g7/4J
B3%30+N"FlGC3Q3ZsLK";g,S*Hd,rT-ml
+@RW,r:9(z^:7R1M3OoNItUZBc;Os73%lYWPHYDt_Z@X2xQ/C8fH/8o/5/di2Y/Lk6O{><cTHD
&Z
`#tH(T`w7LROsH;Xg25Gu5t#i}h,`9Q>pzKdx"arUrxX88(^/=7gM+ecph.U!Nt|;IID-W-OfP-Pm=s%EVFy^(x{"8Rwx[)v.9cLB*x=
xWOd%?D8YlrFV@gQ(.?fte9DW6q@`TXpk?hlG(t&IS^Dld!CTFMe+dt+7?$q!5`N|C$2=IISzS`Ftu&sdepx%Ut5fR>"|6%*Scup|8<x<_>ckJDXzkEEe,|Y2_~:>v!)ot=PbEg=~VXyceV]zxJu*i.R0?WOtJ
H0.Ndxw-tu2|j)G^&<bf!NR1Q|MF3);G>rq%<!+[0C37,
Gijj=bH.usV>@ih

h_5^TMZ:.S{NuI67"WR0qA6Tt,W#@n^[%m3q!g>*(Ep:0)/EsBWo_Uj4,DI1kFZ"d&K0>h*T"_F%qZAI-p7.PC|
NG0s:7i7pJ0a0@#]^L_Pf59i}(<eE8!`|]*1!EenGB&XXdfk)M-vanVC.Fa<rll1k>#8]V
9KYye|EF,AHzz!ny%U%ug
eBU[C}oE([ggU{g:bNSX-JOdBT=5s]41;9X#^ihRD_.)`6b"HdVaTgqB)yS"ZKOM^&-G!OH9#8fzOpHsj<pa@`P8q~lf!o%kX[4{;q5$0L%sCC5
4Ru!E
i`C}8gmibFGi:
,0&=U~FU2|F,yr91(RQ`89C1v;$;KlU~n_ESl!E^Z}43O@?9o{4,8,+.=5L*PJ9oa-X+0oW#w&hdpl0keT-C<i89W#3#P0lT8|f!B^JkVd5,qN"Dw6j}QFRbC|^#wuk.!iV[9JYits3xeUJ=aHpQ4<+T
!0dYTF_4Xgbs|!+I;-,_m$V+;d(f1-WQIFJr4kotDNrl"Ju
`Xn5yTG]}b+98:-H9l]8l0qIXu"u9CX,wR9%Yr0F(4k
,O|kD:<AE;B!k66n.YbRk@(v=<gez$lu0iZUOE$:P*thGCZpYCejYci<U7gc,3dBhF)a1oH)E=Z%<kk8ZP}3U&v@.F}%Rk0aIJmD!JxJ?q$h<#F$/SG#ecl-p$"[ooZ+.i%sz8x5l9O.Y`mK@u.)`S=M3)Hi
7,4NLXDXWS[I@0FekU)y/mW
.q#r8nPMV3gDS%H.,8ea]03-RvlCW4d0w[e^c+ZBKCXIe`!y<{M!/CCt<{0C]Qch,xx5k453Qz2}PK4Ml4b`wywfn2^u9n`<Qbm2NhIt%C';case"lt":return'%sh*7bOV?&;iU%T[#9FCf)4j<VC8&H:VSG$RTDIMtOj<;P]l[*aYEYjvgWq2LDgYbQaA3=gg>::..$BE|pj7<p:w4dr/Vr~*}YIIP[{"Y@j
AUsTmbg4KhyZ,o9P=?-R7YC1Yow-DhB^uQ_n7F`[(%2D`A&eWqdI]
3ED5TZ!VC0.kYh|wp`k9,xv=hD=pt#VN%vht9u@4[5X^,T}Ex&I${HB-3S8Eq:o00M#b13+3%+5tk3vu[.3exy%>/2I9djzn21>dX12,q3x$51GL]]S;l&J,]96%REc50t<>R!ML:0k_jntPL`"I9USabdhd7>k@Wry82OI7Awbs0n"ce%LS4QLk>0g!9R=Vq(d0;wkxjP+GySicG<"Iw&fD"$rMJI90MHQ6M@"8n6!.WOPk)*7W=>j^>u"p.AH_]?}3*fNQ_B]*:8K@?qJ()<pj2,AH%6M,`,avoC-29%e/M9~KAy!`"1C^8=,Y$65;&)1Mu"iV+VIi:J3IWWyiP#y.7,.OS>L,W25hYSL"{fCWn>.,vp?I[ex&xWpZ_)+=h.5"}0@Wu<mmcX+tlaRw!,Uj}Mm%LswiV7CI4dg6>X7
>7u$`USFGJlWZyw^Moxxmp[x_wRZh>dfl=UP*ent`%@UrvsxhwkGbERj%1)Xao=e+m.
#Qtb+#02BA[CL]rt8k?&++E.QL9D!QU
C*O]QI2"XkV[zme?#:Yp_i.uW;vKR$#m5%>;s!ki6:>^^Emz!LdT0QUW:f|W;L[YFh4o+iDXr(0(;Pr6y`5NS&:A*5jC4:aoys>N+8|YP]Rw2phI$-+HF$J-]"gSbL2ZC&BO>jk69%R=v?_iCFK^kJ0h,GiU5?m.jQeo4Jc,IsE/S-j<s^!emZJT`VK_;)I7zZO2(]0r[Tdyxm.CpxaKLuQT-WUR3mAhDX^"Q>#um/Sj=FRcs33f?p
ox[?Q12*oIc*F.4o1Bkc#WN#c
ibR<+>A.=idu*z:7bRg<hL(fXQKB^jJRfxLAeP<i]kCu0/.@N(4e%gt).ZxEbOPbu{P6(Z<)pWZXikZ|)fbk$@PL6.M$tqbNB4#Xb4Ctij4/200EDn!"n5qw!7=r`56U%P5ZI=*x<m":';case"ro":return'!]^$]cs.!%fQftg(~#W"FoIhW:mf1t
0("DmVc}
yI"tYD[nK`Vv<6.)}lAwaB(^kw7MNLFK&.?_I*?lRS@P%X7tCf`Xh+;5~S9kW[;
/2Kp3lq9BPa05$Um;kTE5`"dUrZ>~GvMO]lF}<ysmadiU,H]QKw7qGwepeceaV:v/s,dlw).z5841x/NiMLXp4sM+;23kSztY?Bfu9APVe~0Vy?i-K~sjR^"7pJv,^DH9"=M@l@$n5q&Tw^S@tV;{iiYU$Gng,D%0j&M]%e=Y^
MK({GLaAcXYwvMpYJfo!;OS>&gN+Sf
F[}>@j}Byf>I$uYcFI%WS%t0i6?$"1de/^x^9Nc0jH#"gJ2!,3mL0G_VkjDT6f?&j##:Nwze&=vJ22_HFJ9&gdW=;=F^&].o5N,jjG[#?8aGOb_;rio53/<agb|[wi_5+[`)*N.F3TjYhgqE%A$%`(?(,QuUO1Ng_g)/Bq~SjZ8*1@F?vI;010Y9;cyiT"I=4qPN^$don[C-,e7vrd?U@7XlF3uLuve/QeDOB$joZ!S2b"RL}wySlwTYoZ|TZOiKIN%2mtzV77E-yhyO<U*crL&kg2huvq%A[f/4Hs}
T&H?!sD%#R!d{8SwSE->J&$QxWfXg`uWWcxj~fd:oJk(`)ji
*LfFb>qKi(c>5{&nP0Ey76S,H{)s0Ke4o*F9ZUNxt0?A(*LK>)BBQ=Sch,QbK?c<2U1BiL>l7kew,
vZ6aq_yP`iaU@}GvH+O!v)t87"pD2,(Z#.;xgIc7lbS*Jn&#TQ:0bQH.6N#2PSq7Rm:7/habD^<Q.nIO">`n_!Z_6y.(K~]nEB2I3DlVOfZ]6vpH*w/zaH
I).J9#2#]H`[sXwu+7PyR(Hh2([&?_4(y7sTJnioe$jN^p]e:t#R$g(5-j[)p*7XV48dfC3O)7J"j(@Fvr-i?^cJtA@HU=>T7v`@d5=@tA%FX<%vs=1jg_;QWM=hI-&l=^g>Q.q]aXS?YFx,p=OkB;k%;.g/1f<*qn[2V%Ir6poK+eN2-%6#:EwpYp`S}+6+AO&PXHD+,,u8*&6=c!-`ab}S@!FuEn8Re?VaH.-?UL_4SLC:is3G/j:cTTKmoI$hG.AVXuuC7u$_:rt*,<qw<+K3t[K[TD[JHbzVC<{oB:|3te^6dRgUvXJP-Jk-C<Z+kkK_`eT<V_P6M9`J.8JidAgmlh[@F@!7Yl]77c~4`V0CWJnFg).5pcy`"CkPW$D,!WtYk;iOJ5i.p(4Coj+#cqAn5D
NUV].oa/XA&aw_;U6~5.N:g|+DD:`A(OZdwJo_n,,@CM9j."UO(nj;V&u&Wlv,`ekIs]a">O#zNKLMcq]ctj"0pG09HM5{0&<t2HDM_"
L9
9ooUnipUuT6
-7&HUlj)[}jhrZSEsKcG$[O__XBKdt]T+7fg58q;OS_H9>o7L&iO$rq2d=n7O@N9Qox.Xh/LVW,u,_qBCvXRpA=zR@DbQVZ{<|Ep!!etWbZ}]F#/PqiMb=`.wIlZRWiXL8gy"(.)$S6o#9?y`zTHAV6),(K7,>&bH%N2+gK4;:f&2IgMoKNZm:F%i=/DR;Eu.S1^Ixl"GI?-)`TbVWNbHpwn_*8{c>)8EWh?cSLz[^`ZZ<
jKO^>Uu6tBzQw?lKWp%$`7O1BOLraVrY
L2.nqlxMKqC@HM`bg&XSY+T0>]*L;6mTDm)k]tG9yD^`YIi~LD6:=EKcmBK|Q-!(lYMI-/gz,_ob]zeCtU.
"CbRQ
8IYmuCK?[^fZ,!MSYJ79oV=boLPxNcMv"i&l
+KSQ2&,Mn2pw%J~cVHrC)*;op2i8TlT,hCl&Cprv`vZM`1P;/3N8SZ6`s?kHil?""';case"hu":return'%Zu$]cvZ+!,uEf5#O6F#aNF:v.adZdaHeh}cu,KI
[*mGRX9
"8F8H=E<BT_!Rr-*"G8Umhy`,y.u&kn*.bol!u2ncBXzE4bQnc#8tXE|d4i)$?tVcbKJ8LR}ce8c_RMEGqH*6R_SqemP=LnNI$X:EtJa",*zRWFYCB;usDbBt*lJ3k,0IM,_Sl&HfO&+S|_4=,D(p+ps+L^EG9PSN%c`Wzc$r8sE!SyjY=$>pSiMsd01^ALh&!+4c^4!2f%G^)yJ&-(WdX5Ysx<dI^OIQ_e
#F6|7yRe<LU"vbl-"!!ubhY5.-vxs/n0b]sHc},#mOk$EUix$vXd(*y&qjVy,Jv-
s%]^~9x]{BE>N.eem3?!#JU-323oQndQ(xqyx3qrJn5psxB4uwq^LJwGB8`]uXPWN;cE|Mus8A|+v0LqmDjt5FDlgLs(jIus:#,_q8Sh<20*ihpn`HC4:p{I},y.eo^#6BM8%+LA*l"3bXR]Z6xJs$^>m)?-`Kle;K%"YXvSpO.7qVDRXj7A,avm{[>A5:V-$XM1*2F77?(,0;T"8Jz/ZkKu-J@^PNwN+]YUJTJj4%I812zL7,DJ!5i)@MrYgrT/XDtpQ
yk#A]ixm^J+LcS>1:crRtBlncw0Kc,OxM)>eEi@Qw
7UZ4Caf4SOP&WocDHRjZ+r;X
@SdnL<"VVipK4!<{@x:UhU5&WWo|]l?_p"$=kWW{ZX/?lPc-A~U](Mj#"Ee^M[Yu+$R~!]XeSG`T$Ci&;/o)IuE=l#7:7pMVa=reMi^-lp@}[)LO$qMoV.?7D(FS8

rB_IqfF#3tc2qw4fL`-G=/CdQr/jzd]C%$+4eV{avqiY{rWb}>T;a(I^FE<9z(E
{C9cT$i?[=/x>&Km_Nec/<Ea)0%xFSr?)NH_F$y.WxV5B<T<>_A76&Fp#sBv}-GZ_vS=)vf/AN(m~-Ed4^0q~+z:V<AGFODq5yf`|)oyI)bLhIv(R)_1w/?J+KTd[OUg1]5JlPwvNk1eFcUW5K:RC<B3G&n$(7n]@@E,}J`S%jNsoG8Tq7t%IHdX(Q>D8)clWo[6e?kN1?t:4x=p
Z$wZg}/d$3dIGBIY7q=hLBrj(^#=KKby;$(:kvoLqPC!JjdvMXd
0MLrQr:K:W&7e<`~UXL7w^RH&X%[=V4yBN%W[#lcc3%L2g;Onug870MJ2jfBatT:tz+r<d6.o`6pJB29QsqHX;cX8~MIx2.OH3s/"iz#u}SU=b28MQ)ZygI]/dxsihdPO5CoeX#Mdh_Rp!OLb=bW%jF,^))Vo%EC@@3Ektk#Q#)Z$9&)E$o:;m4E587o#LJGb!xN$i?6BR(b9i%@/^?ko.6G(/Q2]yEdM.yqd!lfm72()MAeIV(}g85%Ie?hD`<JjSjsn.c@__w_DO[t];K)gxQ:+qnpW$x?O)VQuK8L(&j5*ga@!b/]NY
$3`2-7jWVs9r7Bw]U`4keJ0]Vk413p&9)W9
"#QICp}5}(WbU/4G=gTVm4P^!9Z!~=A%<7r(a,.7-`eFeUW2}"E;L+z,%#vM=N{G]t?%}WKg7?a;Hw4s#ND05-N]3i5"g2>hXb@[MUyZ[Eb/j@=?=@p#!G#[A4:K1
<GP,+c^@;cfEP[9eU9U(T,Z[%0B3$D=jTT%88W=(w9HO%",2$7Y`o*3bQGN<.R5>IpAt;/txj,LeMTbUHDyLi&X!$J?!RwXJZ"9lvx1&^>G&|;;J6<U;V(;"Bv$ty=`bO=N8P)obD
<N*NnhR,b_"p|T0w7_zDfLM#(`.$Q>^*BpM"BZaI%axV>cD]o9s
m.fq~2"Ywbi0,<4lTn0Rm0~geXQbH.5W1Y6H0h.s6NfLLH@=d$h=tv%iZLv:nJ5,,QG2cmvVW@O%A$;t.d9yGdUX?8%xGVf^Cixve%dq&ZOi9;Q
]Szk_wqqgL$8)+&.QOo%t$grffka=Tx]%@nG]+(1_c;V8!F:E+
:B';case"nl":return'$Zu*gaLZ;#=w0lT%3t`Z$N&A}8H_%/q-a$y&BDl:v49`s3lIgPW#~/)7w=DdKv"M(
lvc&O5Lsb#*,767B=])U1<LjZ3Vlw?)A?&n>c[bMn$K%gME?N,>sevsa"Gkh?C`
HQ;r>9,VFXKQ_S-r@:SbOf[pk[X3(Mex|5d>QloKDB>5)]L,WuR)s@ol43R0EEXC8qj2>.S=#7BJ7&Qu*-u3rAq
.6+pU&MT7MB^XCDWQOeB$hdQv,0T};@RAEb37%+?6qd5~y7hwx>!.w=N9Eoe.I1y"fH]FFa.;..pq17Ao.N#v)eazGaJ)>Lwb4ZYYryYRKdZ_w<"tnlOAPKTC4{WAbKQ&SPym5%2Pw[ok&60v"*U{$B$:V$!76Fkxi<3-ib
zV[m$VP4u.W2B
UKyI
-(<epv:,jzG_I
Iw4`>%.SYI:TIKn/Cc%4vy3<Gw2L6G@7[+iNAO/bTE2|
_Ajtg
rn
47-M7IWDU%1H&!.I65y}swLfTJSTs/L&U[ye9r;eiX7^JESFWP$Qeve8PB]L"
N^.$3Ctx
__<ZK):m1spNJ6{iWe@$FFgG^y3Ry?QyyjDs^5%#n9^-Ox}#J){K|_9EwlSl"pXayj[nrJzTig/ix6<"gJeOwt7Vn;H>f7&&hwK.=$"x?_GR|QMTN,[H
ya"^

51<3$
#X,j5Jq{,2-0#3Gq,c^Qx2Hdvh]lk9>nQ><*1MUk,DGOBB-1KveF-_4y"ZQ"6n5brr^383`mJHv&0*Ng`,C+^,!&b$gXU&XErR@k
NTx79+vC3i{)gj_vS&wGsNC#%t:4z+sgHE~Li/%0oG)c6Wsqr[2QI>PCR32idfzIysZvCCMoe%xWI-1.L8{.
xd-rqQ+Ou
(-W[8]ZM+dukI#9qxCnn%[q%e+yxdzuUyp1,<Jiz1`+KV-@J/v?pZJpm)he}*`%qif%7l+:v6lVp>rN-]%^B3FP?z%)(Cid].by"jak%e+jhcVA^u>v}va%vxKr(=U`[<5v$y2X.+iNdY^k_g+wC=y>}gVm5BRc!d63Y+pq~$jKr&MwoVbDQ-A!*%UpL4dD{]1922@1_;)r$FzBN0jEEt#r&Wp>-*LuNc+^kjB#,,l@ZeiNPN5,Y8P"VCwQ,hKRJOy#Xic&vy/Rd:AK4+9j[X$X-p!Mj:~W,8"ggqL_K-w(>S?+Sb#"fQY9F.sjdCQGAH9@jj)ljX%Z/yv]nPGlEB7]a%2C1SFtan+*mNY*P4qH+?k].SMr+Nc9.,9e<3Vsep)^("OxW#8&|`L.nCwV<9w"5M
ov9+Bs/t><8+2]XNmHQ8iXNI<dMa[VWAseE<+~g@3NKDvrcd;VL$rl7_wf];]3Rj
,iI=D<"r{iM6FK$?BQP$DBSn:TrxYX:=<;%#{rR*UqBX33fu$
,R|jkYa`7_p
Vk>vc=F464GD/R%*5/3m.*&?-
gNYdwis]
$h4*>7TIPqK)@!r2<[H;qx:VryL"1)dw9d2AroF
9Iq{O^fw],I=o))<HvGn%-H3/H99jui`mxh
&e6CK
f8^CN[Oce=Y"uq:_EI"a098^P<CR>3-V8avBM?558|;KY9)%p>4R$;C5XLe2h~dIBV5noFU%l*bVp1`hV4WtOMY]24"b#frScfOXwWbJS?Tcvkmt"l.*gt8
L$bH#46v<,';case"no":return'&Z}*?bP+>$uX|?89waj.4Si8N0;9h:XfFv:O[BS#6]w?/W6:oem`^n%.+q/$a4=BqL2Ed/hcyO^)>8$Iy#cjd;PFZeIEbxHyavp3ZIwCV?P[.
H6RB8dc1Y#y@N<jvs3;AwiEsWKYiyo?&N^{PAwTly=a.wdLaK"$YoCg.;o`S4L:KG/LEO;+fPE=f~_"<9PeN%yzsuDwygRtnn&yc<8.C%psW@yZD0xQ/1B57Bo8Aq
GX|!652#gaW4lc/:f:@o7Bbb,?>WXP!pFakSB*{S|b!&c#D-(,G<Na{C[YR7-V/&AQg4qi[acs-YTMC@:?f"!G:5rHX$gR#pDW5xrN>dEnh62t{b*Ou,ZJ*tp+{xs)pQWL4t5!|?w*a7vK{`B/PV7GkLKafC&)t3-:Ug2wRi4s!(T!Jbd(?a{>##p;jik5Nn~gMo@xgk`#GK=kU&FeL=/ZelL&VUrIge;9!y+&E2|P!x#R{C(+

c&A&sT4-"kOF?MCfpHvliTET~(FjZ=c]A=6&+2LBfBf0#dlBy]acM1d^&EVYLZgHuxr(ZAMdbL.4`OX(nn0[Nm#mFap%HpZr#EjW_Kj/Rp]<8p&mR=M.(?$!}*y>$34q-js>[+3^AkNx;0nvHASTx0z^=j*IzeVu{=CMCt"^inWR
+5ZDP/`aY
uWH;Vz4DA
u5t:6]ypC[hU_3;$%$x>/rX~=CWJXExEbjXVEt[5HDlZEn#-Ol&j=6539`I]cgZ"<FK7Rp"O>[#44$^s1u:Vbmc#(%&G`NX>fzK/9l=yoZ<{L:Wi:{r%*H]mws.]"HU:)`&-!#7)kEhb
(FCM2H^4]=/OKU(;GeoHEo*Hd<,HK8~+0.%NO>79ekfz%C!@mHY<pz$j_>|u@cTC"gSU~K~[F@*isQTHSM7pW?v,Xl$/vy<g~+ehIW5GZZEmq.GBmO0f3gme0mkO]pKYY0Gf&tiMRyVS>!e,ID5aKo+3}l=j?Lsv}cn)nmUF#1eN?g]F.f.>{I=#JH:[DQqO@CNK!;rW#&frN:JI>h/[M8p,nW2^~/Xf]KL
CJ2%wjd.p5g-,n7y_lfO/9Y),1po$8Z;$-NMS^=KgYb:}UPEt#Odt%BS-DM=I=;VWV@$yO-(,uJ9ojlC`+%-L/}]jgIYV^ahuu[6v!_!$5?./+?s$l][k(aU0T_Mu4XI3i)=xBno|1p=Z2Wi!vA
-xF]$^O,q9&#|4Waz9e6hOv30Rm714{qL$ju|wmL:mcJxVOpGB`G)C`ty&PT|--$G+</S5iNcl4B
D$tg.x=w2lZe4Z00<Ae+Hhfm"*uH?!pyA8QLdm<vmGNa@e@+a?7,5G<"h^E6`!3Ua>3C*5aU[wTCT+S=Y;x[[&`**H
W6f-a[E-!KYklX(s):!2~`
ZqYWC4b$53R9pPcp=gF,Z5^L.L;A`#UR"73e_8^,4CRIR{"uZyf,q|R>nByL)EjoZ`a[A8]TU9-RDn)mhyVR.
kVAxIdM94Hl|3MSzr)&+iEK#nW[h_NNOOb8mlr6x(1Y?9]P0d{o%Z7RM#Rw{G}pzWZ(2^Y*^XD.c:G)@HJ(D;B
}E:3}wFp]nStBQ+/6T!BJ>2.@f-J!ufJR_8
.n&?Y/aoNkOy3>fNV[pJ}5a#Il3#Lg1Iuyw';case"uz":return',s_wvbSWB#BBiY9"%wS9/Yl,l$Tyg0rQ=TNSqLCg!7#;>>vSbh(w::Yk1y+ng
1E$B6*)D9NJ)p)_mtfahZx`u]0@v?Oishnffh-ClAbl#7`GBhyo]Y@*PW4O?g9@=6ikui;x]
0u-{yZr02E2u8v_fM,@!!Q?5tA`@KNq+ru7Z.1oGW:W_Gn,cYW8^J*Wly~w4tUJ>x#K:@n?)mr=D
k80wF?Gt7K[lg?aBl_?6[Z>xOJMh+u0]q>%X_,glq^+)u?
v?*|L}rXcFJ;;BXAmEmPC,&OttTrOa*,IuRODMa7iI)PFIfbpfl^5x<qoo*Of*ta$lmI=GNyZ^D>%14;
V6;^r$jUgsMo"Fk%dFZu#rkHGYcioDkx2?qKCc]XM%Js[sWP-EcfSCQ$
Npc6mBQ~_hmw*Yy"1
9B/+0TfLfDun#9798T;K#=Yo8Un>j@F=d3#/:bdl?<`bFAS^dc5FAqwP?)SQ(HftrORRG2%9PC$@3Ke+E;HKd)&de,!Q?fOMfhy|,xUQ1Q"*s+,IB[9sWZr6HI*?)nj%Gm^W1yZsG4d6Q*L*E(:0T@f<$0#98cnr:1.>2l1S5Bz$[#bhWPLq[tN-e51c;^:pV>e*9py@/J
d2uvLF]s(DGAzXy=BJ-`V4aoxq~6&X+&/-X%|!:<fE:@KkZJ*-O`j?
fAopu^p?da
MJoh)*|6xBec]M{R6G.:XBErh"GomJq-nVtaP2e7#)ZNd88gZ+
Y!.h^}kwpD?1j$C5PGizGwNdEW+p=Z,lN~8#+2o:9e]^u7"hPdETG>GBw?MNu70BR3pKZQQJD2vZoJg-iw#oVY"^9~edc]5Ul^"o?I(e@jSO)VTFp^(+MH`6Li1RdMkkE,K6!GYydBe*E:t)ZlhD/%_S%}1s/kJ%cY5><hyB^yRUS`0L)oSz/zfpVE,F$p))lUU<eWB7m)vzsSy-W<ZkGMVJ<=fxd%fe3~Czm
E@tSW_;0TS6Dyi5W.BoC!Nwq8AZO&u8J$Txp4GK%Rrdqn/,-X<o/?WUBW-05Bu`mJc)r[BJd#B@ou&liP
e<AzCo9pB?a6@9gd8u,wOHKJ?Q_,oYMiPW/|K1)+U;i6]]yPDA5rkh#
.4T8"tm;)63FM+D-aPOC/A1@m~aVX@(WS.>6j=a&5ebp((ZNQu-P.>S0.^X@qG$zyEucp-TT5_&7?*$(Ij3
+YQrfGhsipebuuT+Yo$Pd6kg=y@$=:276OdqR[HLj#Wk-ui5Hw#c-E.nB@Wb*afsS`mI`Og&tcSXj^a`#$egL3s1S<U#TN>n3Je)[!kxZf/PK/1LJ[m^:7k6TpC^$;nDX|E+S$-PBMQkE&px>>JNR5+Yh@0D-R85O=%0otXjmZS%[ZKS_S)J`5)~_x57WjZZ7-BE(%Yp#6ksMKT>i!:4?]v@C=%F;zps;
U.[&r@Kp#}pcIwX#U,*-Jj[T9?W%QaN>J3j|[l[$@3,P"QE:6l_y]CK=(tn%vg2lL6w=@?,ly0-
VMUI55Z^bZEjBhOb({P7R=DHk(cN.`<wygC%';case"pl":return'*]^*h0z.!:$`sf|9?EN98$s1A;.^Z+=8!D.E+P1g68W2<Ut0f(H<Z>|+Lq{hCQ#A8E9hovBBd="aIX1BINa#XKi4EBHM6SDca5voB`wk}gm.0b)oo]fMwtT0tE0MuvvLWr|^NkjM_q[drHmM2a4:/NiA:!XJUn-+OboxUjYWK6op)/I(-r(^-=IynkQB18@*6+jxV)DJ))V+8@;qdmFoe(pUXx(cXcBvU=h@V])9E$O5qx9R#nrZiWAU~u7@zmdck4=H"(4y>K%0)ell$CQF`<nn{@he?A;>>ngt/mI0f
,7n`p(0HleuwHvsG3"1H@1Q#&!BE8nSK!KmFsT#kP:$K3unKc`~,.wM9*cd_4p@u%nfh.stn|cSLT"/,gI}rGc^<*p5DC2}tob{[*u+L#JdMI$Hm-P|JS:(Rfi]V_){GPDOUi`otFR"hF@E]#d=5=BrY5s)tFI_!v(8kO
>R?f$$)<xI_?Yo*2,m?RG8$:uE%,p:b0I7Le-v]Y%a2:b-K1_M1l9kKN.gAKF2P_NZ?s)K}Kj",Ht>x3.2;)9f%"2>B%5cvgt:@["A&5c]q:s/tWE^Gx!-b0Mm
&n@~AgK)s?e5O)uu9~L,K.XfY=!B#kd[CKbnQK&/QaOI@c8v#AoZ5f-+2h&FPgGOUfQ6UT_KXSey-n9Y)^2"R^0H-DQ!T%t]]tQ=5ZwLV9EHD3$R9jeW0.s_bVy,N%cw#7IZj_3rVm6=2;l;F
X`jWOa-.)-pQ:9^+<1e.RX?iR,dB>GsI>0F1O|4o63J-pScZ5D:c]-]Nl9Q~w>TkVf%Hl)7CH(."6Ox@)Ol$V=R=1-a),rB]+/j=76M|9PMd/w
x-Sw1_F<uHtj=OJagU).Y2*.UB&2:fM,67Sxq.se/d(&LU-3J?YoTX.vT+n!N7W"~O6j[x,fzo)6kDe+wUr@<`jsy0H8<1P[hI1V>&F$<WeDz`
^:Rk.2`-%4$"YjaZOP%HxD<-ELfgYN=tuyw0?jMjwXxseEhutu2+^]laceZ-!!`TFfcI[_p6XI!fB8GvLo6;v+g7P39nqkR>$vNlUPY@;/`8cKBaPqq%gtR9YR!>T{U(+N)GUfCzj[c$3kc=9Wid(Mi&^lh7f5hY+DsPbSpq0eAgTpeB>SwYQgOXIKv#vcXE&Q@voBfI)m6YN1"h@7V96`Ji[W7{9jnRCHo*m*Mhbzu[^*M37-OBAP=wS_qS@&I-CGeqf*L^
6W/:LJIUN"<>X"<U*
d*W"xa@;l3)N{L"uM/D%~fIH*R%u7CDZqealj-Q80c%"WN~uF#:%{%!c*"@t%I59J`~=SZKdDg4%
Cmc#UKB))
ta.;Il(W>uh%;1W@gdZ}jeee!7:m;-YtCg7ki?Ya:y)`!olKok&h:jeDwK!F3$.G^9ETA8!.[WTS-.M{0qJAm]>wJP.L5&QzQ(bS2!>;onZ5!
XtRg)->7W[AcH#soSISc2rVo$(28]v*/#E$Ag#&Q$/$i--%#vp/jooMSVM2aDQ7f>0v,Iwu[`~!>G=GY
0_mTh04^:xY(5<,XM#|*=#|>zV^+xY,YbV[@#1?5^&D"4+Wr"n,G.:
a:W^u79S^[09&?wj+g$&T68}+f6Q*-G|c5^$<oOrOB&pYotv^oyN2x5X!wmW$GN_N6;LLb>G`_f/T{;"cii#3JpL0~#?c9XE^wScM6oZx
fmrQyL:ukokZh$Y``.dMn|53oho]arAWT?^&kqISwh0{+qu
=MhV*#Dh^?S|K1+qQOr(&_6Z<,QP270=A_P=V)cFJQO[=#ahO(^tC%TN&~YR6G5^*u1
3(Szj9UwlPqeDNZCDJd.(x%XeB?4HtLN#$XrSA;rJ;t>/;`PRAw5+%_@8=U8.|z#5NH83f!vYuZ,L|on7B,f&.r%P
Oaf7:DebPwg@U4;p*DvkW"3@1N77s)H>1`i0p(x-jJKAHZ"C#OEuZzy9[^#6^_eqS5yw2R';case"pt":return'&]^)
bP.!%eqFi`"lETGs%%4(11I8%|!zQ&9J)cB=[$$W!@K[!I;dq9YWZP&Q#i%&`[Nsy+tA`O.csE%N^`c%@aafIhw:_do%c!II:seFiEw3*v8>)Wju5`&G#iB(R5a[N_o,2_sS1-r;/R+JM]7}=(ygsyC^y}t.5"$^")]Tu|@aiL.N0N$+9`;njAQCBFS$)k]+Vi1t)a75c
G8d!tLO4@,3{s4QDCYjP]qNu+W!cJvDL/@d<0
kem|5yiPdaQjS)ST&DhuwHdDP97?);tuQN9q:4<PD*ow-]TA3c9>:bd$N~CG@n++Hm$EnD@|^r
4UHPUPJEmYY!*A;yUM`ZZpb1vp"Me,tCzq<Ao,Kd
?:9@r;dc^8i1)+<sHtsy54Y!N^5#8QN?p/6nyVWv_8A*BfH~TL5~(6!,M^y*I3A?3MY!"zG6FJV*>/Px<b*1smvw1D@-Fq(?7P"8OIj{MVqx(2Wtyw"U2K#4#BS6
q&MjzS.3F$DxBqgAh[s!6?PpE8ME7&&;o>-=l(c]R@+@0/a"0+}w5.
P#I1N?ZGx>g^V7F`><O8,^`KWruBf!H[%7=5(e_*-DFM8L-KaKl0,~lxoecVFWqg4.P~q"g=UMXT-{445D
@NjjI_@(he?J,0=5>o#anxZrFECA
8c$gU;BbQF(/
!f**Uxt9.Lm!*gBy%X#Rsm
4oF8feLm[$_qCK3rWL)#0#%FZ(B,UjO|IoIy:r3v-QgAAwu8O7GN`YxNMDnqoGnrkJ.6H~??UP*RSmm!!PqV]s5|-$NJe1IXA$6JQi$Q`m@/+[,BC9Xh^5=pG?T>F)c5j@vZ"+ie5JN^ezIEt<!hl,>VNU11Q}-_HD*`6dYj#qU1Z:8*9X-6xU1kTH5v-FrHgXlp3z@CwP0(?`I<??Q>L
YmyRS5rzev2w5Oj
FDxay-RS(N.8&q3oLRJ(X=2-5b#T:>/$Dnfml:mW,<-64?M8NkktB#7a)&(I2u9eu,[MvQx]I%!Ux
-+RN:|(8s0pU8otjeW@LH%W~=2Z{XgnbT6de0Z7>rmI|&sF]jIt6g4X`^3g^RZ$8q&BV(V"r4NO.!l(U,1)|x("nB]B!9$`2=4)`s?Mx]Yq7i$@OW<5R.%]baA5KQi3(9_Et%4Fn.08:_ks_9{7V>n.4h/T(?tNv*uhrRvtrW%;>_$;%<Qv7C@Kh(6_w
tus
QX"3L6%@`"dO[%dbE`Jd$MbXT"p[-A1Ira5y)IT%RYw*x
_t[Jzk#/@m8L16&+rKM#~yWW~&R2%KeZ@4CZq"eBzd}Y)5::t`"6zP06T
y9(YZMI-A?]o!&Bb"VV9RHTq4]_S]4!9/v
[>[2+vbA$<LzR=ee&qd,Hl%nYFMm%yo/O!LQx:ldv8s+NItgWY"AEJ)^ALnCem<W_r6Tp-,k)K;K.=(dJ`$@8@(T&ABSDD=-*]jn0tCCOy]E1Yb^fR9a:3lWy.fd"R>Kb%R1(3n}y;5
iNY{ML6!wm6{4";H@I>#Qlh55Z
e)Mp!WbbTpE1Y!$d>jTEQLKlL=jI@$AS:ID@&^/Qb>dkwdkW1MUHgK{VaNOYIE>lYCz
m?%JqT+(A_w>m[YJ#4XmCW"dGvkyqEKmqggh=pFbl2]Zm>bd2TECUh805<|EgbRkOKc`z^O>1v
lx,%S0RSm_M}O+0{"fV1Z:n>te!m52k:QT;)C#Mo>L/jnV0
TkRxP1=E=_s~r?(4kVr#?Fg}*eXot4OPX$;X0^xzYDoWqRs3,p';case"pt-br":return'!]^%@bsYx#@fEh<Ud1Rrz8/U}Qg9c@IX4NGLk8pv;Q)Kqlvj]-Vyg]>9<?E`W83F3l?ejtaH>b77-H=A/#L5LSP[4@+r|k
MPK-EwZdl$,=q2brhVP<^
XM[mFV[qkId_bb>r$4LVqvybPsF*F,D!q6]A=b($psUvR]xoiW,fPg/uyJg$=sN_(N9JE.$uQb,SRCOO1-ppD<&3]_7mMbA>c|fQ=Im[-smb,g;te0*,+"[Q,;s=_
sCK,
X:2WC+Sri_nK4)LlefkW3G4x?!-Ep-e3JR)RmIVjc2w^"0R<0EVL:ckNEn`Xppc7/<O];cd&60iBrd/]~BXb[JJ,xE|J,#mi#A.wXmSO!7f<,2R@/U~^G+&ES+":<yv7mHDiWeK33-1)
4
?i66p+9/,@,$MF%vpZ/s!XV"OR4g4v>ep.32XFOA#FVY!uZj6/FD-G&g_yy"!:_hL!WL(#JyaCL)#F"QU_)xS]gQq!fZCtT,$8Ep[r93cdxHSrCmKacB+-&8mcSS1a$"u&U&2
SP`=X^!mW#/*P4cCAV%ql;a(ndc-;`&,`jF6;IOL:NAf(
W^!r.H;wWVLs/2O+Cn#xj.Iy*Ab>Q#r?[ugEJQx&_z"iv=@XE^B#6t&<Qkc~<o6)[C*JK}yz_|lbovG-HS,o83>C&Ku06M1:h&KyafiCf-sa$eiBRMDT98ou`N$fhesp1yEuiG_^F4_B$nNJ$C_oV[7)L-IRJx?7+|iVM`[<J7f"=X.Uf27[Jlw>x<$duETwRyZ%Wy-}5;R6?u3ikB6<yIAu.R.`HI&Efg-yQJU-;kn&7wq.,HI1%9NFlHEelUHvDS#li_1]m3ekLz*idfE|j;)roc$"dfPCF4p7+7<;"Gfq"Da|T;Bdn6I0<+36Aw:[p>M{RwbjhDQ.X7TX4AuuH
pYA*SA1<Nv<6p`sd$R#Y
EH2KxVM]V>5hEP}0;m/WQ#`a6VC;oCE-z]{M|dB$LVi?*$RK@wi
L_Ym`/K2EyaCR;!?M)(tf
Xl%5*p?k6tL;&t6`;:@T>w:]g!,X!N3sI?Hiu6?:lb$GKDdWY3|LjMx.Dsp]mw#H(XJfR=IZi"W^|We:[0T$3pEm#Hipq+R8iR~_fo?$Wxw9a7.V37vR>=6N
7<h{8oEM@AQop4[#F4]ZDif=bgt,[?bG?Lq@aL,R-6AD"ja:LtyGWK^Zg0l%Uj^0`fTPn?FLk!b2$X@}F}-2RHimZf9U4[nrA04`wSn:m6I8A7Q?i`"jD<1#ZCD{`&&=N3t@T9q[;|d^JSP0"BMz)RCiZnCyimbpg=Lq;{BVWVM/Qj3z[z;j1Fr5M#Zve/P`Z0oO.jPDr^Rgg`PxuP>2oQ^00iv@"93^5MjI4(aGox-a9wZ0C<yGal(6SRr[3+Wx(Ofw`(hyiT)iXUX-2M44Zm48&a=L*mRClZH<>uYoF@l;.U1ZYdZNJ@H
A0dB0{+~e?W-
R?#9?b5-4xU]HlS%JKPAjHM6d0MG+!!MSfW5m3E^M.*m~y#X%wfx)#6`_5N:$wR+B7*c|B-@~I-jBF%?-ik=zD<Uy$R#dJzlrJMD?X5@"Mn#R+8>EgsJxQma+ruCkbR45%;rKx*pJnPOdw6UTFWBmM|1|WduX^:7}e_$A&S)X(qEMML8]g)GS1[QqX?sTZlD4<i6z*<t`R%J,,3TdA#ZoDB/bAZ@SBck=b3Ni;?@Z@P^G5@?>/("B';case"sk":return')]^$^7o+>%eXsii$6B8!S&>-|Y((Vfn6z&U+XQP%Xg<"W?.lcr"HU>hO?;3M*CvB8@PSw]4`}`0yP2K.5P@UlnFf~;o5&j%T0+dl9qV^K5XsY62T~9>?w7Q#{P=a8gs$`+N!]3nCfRv014vV`cKM
fd4VTQ0[LIJowlMqd)5}^apWQQQtUeK.BO
8ntc-W|d]f+b)%NlzE@K`gki3efL3[!SKgP)JW>`Ds9tWx^M:6XM*bP:jU3K$<+DU-]4,k*77Bj
^b0I)nkCT?(llvMI=/eSRO*g-e2itF6mpS(;8md;]D_9itI8nFKBJwmIZ@{tiqAXf1bt"D8e>Fza6"~ZRxK?P<k";i-n5_]kX<f[FJ
PY6b,{*W0T3xV`P&lXcc"}DG6wgZ#XwOcZ=Ja5).b0pq/vD
DG6Q`FeJB$*?73Yq]p/X>P,%bN?HOx2,o^[Y?pWOk1pKNOP:C&*-d{E+]jU+7,-#!r<ynw"^(:y2a^Re7+U}1^=T^/V6aD"L76,EM=JS<+#@(E!GGarQ"!35]$m-s`<a
pITYRq1[~7|/Fh?4("c3@`GUA,d$)$F&)+WoVEyX66OKS/2TWI)rN+]$<hl=yy-?"iN;3a.aS)m
_:e(Udb-8yMITO"#-/H5jaJ+t5HLT>J:`Pi`yu]+:&MuES(@;eZrk#mMUe$F9Ko:!F`":>d,Yo9$0$=/wXgV?JZOs>2OB=?aC%{F^nZxUWA[PomNG#z(8k#o#gpxZ*I_;1H=5.AV;f*Bmi9x8`
dtqQ]uH`+.mC5u;4
IeYr#Gqjl,0a02:4%s`DI
A9%PYkCp8)I@Qc}Y_TtMvWx-JNp``Wt_-gb]B-`H!%_Gs^0a6<IGXa++n&%rYRN"5UhI{C:D"9s@*PJ1tWM:-hVYa/[QtNcP/gGtL$HBaSAh4.BGH9O$`o.v3k[(@
btE3Wi3>&p9.q;oP(<SWlDdnPjyd.V/5<@d(Xw3sSQ?X{XNWbQ,"I6NM>:e=m[|s#SLRA#(br2HY4p)An((nzoYqsXo;AUT<o0oe!,AGInl8Kg"fL+]N!Mo3rJ8ls."c

-MC2+R<]-$Jc?[B*qwe(PD$plr[&PAWE1S+1:VBqDGPnG(ps^$-+d9
h#a~q.wlU+Ces;^kwO26gCo
7la&)&tGuz@c1imiS=)tWg:
@><&RjoHq2?2R)-3la,[[t19Qg&vu6tFj8x5
9Kz!%%K?MbjcUhn0`4N_ko)`f_3w&$k]8=,%tNo#]8YG6A0BYr8?yk1$:O]L|-hc~?R!D(v.]qsi/7sBg,
jmNx[9rXU75|Hv?W.t"H(MEwOS7Es,,#V@kMS_9|r*po!>bvwsY*hl"H=?dM3/t>:,W[;:>&-U1/BclpvRO)bV(O[W+*$wHwWbZD0rJ@1]C|2n/sWSs:,[.m6|LwWU>v_=^wZ>fvPUIm1?RnAcrU8X<#R]X3ax4{<qr:C,&?t5;T#HKa4e-iy.4&fTy9LDKW)+(fb.6(EwqF(cM/-*2~gM4gagoZ$J=[$[MJCys`$%,Zs5=1DA
F4`yO;G_0="yKc%Pt=^ODG1E)KDhm%}8`@H+GlRP8o5fH[XE06;D1`m%}[%V6>1TCR2^N_m+MOr7Yiz::VQDo^myO];x]ZnSrn|WVr4dN`DaoujOK-Cn#5QYk-=nNypRIYIu"_m?`O,Sp1->P))j0);NQOW@1Vi"1s>MA9`[uVS0Kq(`z/EEu1hQTUSf:1wu7BSkmh[/Z8+%GN_S]IyP)XQ3pRo!JZC<>MnB1>K3?Hv;mM}GX=otbM&Dhik/gF~A(wBw><s<Pm6`sm<lv!Z?tZ;VWq8w]sJ1JqbYn!ACWKcCHPKsSFi](!SY7Q-kdr%E<,<d*,|N<
tgEQTN#2-rDwQ>bA0klv%dw">;@8
FLFI]7^af<DD$11n6
eXrXwyS1JkRkO7=F[ekU@o!7-p^W];(22$*n;xiS#E';case"sl":return'$Z}*?bT-d$uY#?8e[D>)g-[t`-+?d@F1%es8^1KL,;gVkiFJ+ss5?Wp8/d.u;a2"q3Zvg7b1,B=s6$p&I+i?8HB^+nq^-o(1uwZ.>Ma)kw`:;Jj1SgSQxc"mWBGo}XY;-g!+),mO(Qg,`vz07E7`Cf_QF@8fkGE9Cjjm/,R<9g-b|Aa@zRsLG@V^gQ=TF>!^Pgx&/0|egjZ43v%8CN%MRbw=#asWZjnLm93c=J/0>k(:BUR7?2-fhl8D3<xpEmLuOi/RNt-Ct(vA|rHpi5TFVdg9+JRp.fzWto^.C*upBqKiT!W5*gd4`<s/w4KBSp-_g-;=JGb[TZZ2`U7n
,oPRBF!pl<t%SD;fsO!Q:xxKF2cE
OAXjhGNB`$Q8=fPxcH@W=NZ3O5qIl5Ty<3&-dXJijUU!HCno]r}0ZaoI1/dq![|MbS,j{Fvd:T(+
5zT{Y?Q0e?@_HyKBm<P&0Iw@DMb1
oni#BflGm!8OVY~!Queq4-Xa6S`VrZl>&Ve&H"qtjKxP*n=X?UcpQ]%Ib/Ghy;F%t9:>?K|"*ZxOKc{EkGIo@U}B]u]K71Z_u8|vMv<Gv_
$B3%V:w%fH$ycBX-LJ=^?Tp/E9M|^cc~E%VK2daJC%;(r:W[$SvbBKT[FG#|bDMerRq_F^pX,Oi?twfq.lFWd3Wn0Vb4!B9v>aulKaap)MOl/hp~x;U&rp@SeaRk>6q`XmccJv=
]1Cukrx4P8r</q@XB;[B;Rt2x:b_>TMR^#/hp:?VxsN:-uKAMqT8_`D!wYYmD<B[Rt53J@0@x(.d<[4RG]Yb:g/67xn9$&Das;Y4sLHe41kbr9whE@ft0i;>TqA{etr:+
lLa7[X4_XRTDegEYhQuQ`%CANX=t(#4>ucZE"8troe0BIPL%FjHcehq2_ov3+O[M;6C^3v%@lQPqf|YZp)SdkUZhc?qdPC7mGg>Re1T?tV%+;UoZT=y?Z
16/>G[n~F`k/*gX?KS5=F.2ne{p>(5-tUTo-o1;jT
3:W"CSF#m.hp"_)9D>R@QgQ^:briv]=Ju:39wkg?&^G#rM"/z%kyyr@(L,ScnLYLL&9[0,8g-^V?kSF,%uSzmjbK?ANT0F4nR{jv;`t#XbE"ik
f0T[?O`Kx]P-Av&A(=VO#?PR]4r1uDz29`IPt(X^P#8-Dl%SU5>9kemT@@_YB6b0$+"">9b=u3=e~L|Ut%Auru_R!%;>tf)2-iu]Xp|z&jTc9JmIWd1#:;Ks!5KHTk!E`3LRbq=kR>B!alVs?EZNl0<[7_cmObV"Sw*5+-45Lq2kgMTF)!n:KM+g{i)
4l&7gQI0gxF1d4
/lODGHVOKzg13*7]fr]h-8UFSQB,
,!`67k3UA%MCuX7KyOf<kn!;!VQqK?K5D(=?t?CmuG+U0tE.9w5[~4q.(IR73xW-v^oJx^cUY+kx8XtH,V6n+T=?)fE;]7_JC<N
)bf@Htu>QRE9k?AXRDP!TIF_5[p9x^xA1h{fx&G?Cp+$U5<cpghj%+ysb2,e1"e#J^%V|F2W`gF^%bFt!H!<H=%Vs0yegSuUr]I7Y[E,-<x.XldvP5Yp!P?c^Yn+c@@hng$fKV+OF_CJo0EaPm<C!4:m576Z"dorvJK8+k">TuDD4PX3+P$o}!~p"N^Ds1`?69?0osxHee-I%>gqf;~j0EG9H>GFR+!
UX+O.h
lTo$;g-jWMW11A[bgiM956G+W4Dj+U40Crq`GhE["rZfg^@^sq[>0V$",P^V;W7c_.UG`8uLvZew`&Ea1
Xc"~';case"fi":return'!X7*@/vZ;$uXk
HeSM.CJ%oC~Ku*j?iVQ4YDOT`@<krrl(+D<!JO}7Rc;tN#cZcorc
tArd+-8]`uUZK&rGnp^K@7xM:>)!#s!qZ!;r=Tl]SGi#EgaRTtcxbPEmX^K2_45$fY.d[e6U1(#>[MCNO|rz/M5+ZK`q03l)a8&/bDE-q@Nhu*<)HB:Ial9PvHtp4Uvy[jz(y_?&c
IjW+V<Legt?pTt.-*88{i*.73p
E5V0rM18!XF;LX.=bjQfK!9
|RxkW1;p,2_G>:b)WpqeGIGIi)$i@$<.UXH2vvQHUm~JTrhiD!e@y.R5GoQfyA&T+rDodTV#?!1x_vNMp8cr[ia8Il*Q6t;vzURF3XX"*Ff,OD?
ZaxtfvwRfq1(!D>g;)/$+o9Y_k,2.,8?hLZ*P*.B8V_Ox-3)ry>gNx#K]e_>;lz"IPcYwZ.glfir*He2EaWgf":OPP4kYkkIjw&*gJ^$JXn5ShtCU[md11HohU=,aS%[ql!R-6,1#k{,Pmx2j5.^$(MEb*c:_yH>~*H=fkKS%ZO+CGEF"Hqa1XDk+?qk
U&)*9%pv`DCUNHUP9SuXKDl#uE<ix6Q572ZY3Yg&GXAwK"aMRVN&Q=:a.`skD6SJgk#2q|*M%p/KsaBUx@lw!8q.O}LS3VRNE
/sZ>Yh;@6,cRxwNmiBj)Z6B=Zu[Tt~".$<%]j-(p+Q;.3ZtAdPN^IfUv`Z1gr{.&MPmFv{cDs@tR7>nrXj7@$:9{.3Z+3RbZJ^n7FaI7cFgb>uYKe5,M4N.Zlyy-ruRvsk3"o!DDKsM=Yhoxb_(+#jn!jF!vGh,M>?UG
e&uG}dQ7Sc|"q,%<Y9+N8d%,X$B3Jcb<H".Qm!ij_/HD,<S//)PK=JNcnpqlcvP^:?AuXHxg)v=7)xkK!V/#rrA2&L^86$JL1V)*,x)7e^),]-%MAh?cF8-R(*$u+!)*bY80va%!SJv^2)d9qA,!7aDGi&`(C"Hu9!CQVWq-Fo6gH7D8Qz$+4U^2X8K7R6nj(v
m<)j$M]9@XT:4}`."!971d0a-?&fj$aHe/]>oz]eW,8J&n^0=i#]]iy$e=^t)!.4AF"yDQk+x"?N;d9;.`1fCV`_j@;~a6"HwwnZXg^JFu,Qi:yM_td_MQ*m0mc-+N(v?9/d!_Ft=D)9iNvFy}n#::lmC@/ufwxjkA[j(Rfpo*3AVH31ig@^sh@j1L`od#cy;LiS(B!.a~tnlIUuW3s`!bUASzvZSO7k/i1kAH0V[:0RU=h;-lbMYLPc?F7:)r&"tK"`LP-lhmOl[GjE-=&@ZQys)<u27F]_:FFLY-NyES%^I#7
p2u}msFLKO:K>_5}%vaOMDKp&sBm<L4^4oG?9),?)LoW)<k7)qxniRBQ/u*XA,%yv?2Z]^0&K3b&IO#zCB%gC,[K(,gpg-k
:vMr
G]7"wOIlQqf$&[wsOhkBo4S
"bp$[.sU"B^f8GrgpwujgZbWj+!s4m^h35xV;t(&4jeLRo-ITOq3hc5#FJN(1CG-5r%)WM@#$XCO>fQ)1u)bhwjVk!
B%]}>J_RWf-R0B1"
wxgK98~Pr%>DISep:W"4RZl%q+3Jv.FS@:2[.S=o#.-4!FQ[z:v1F+1MjQeHYvi(.8]Y57BYlJT@"bq^@bX7SdZaRM
7l2(2"Zzyv:27FO$?@In(
uuN?K9
_hWF&>Zd/6
5qf~=xg>d28r
Ul2KWFX_Xc"JImPag
69w-l/yqDq1S-=}Z.<7emqIqIflM}38>wUsO3&odlQcU@U`O^#0Swsrq&dRNK_nOguMGEN0cXS|FB;qM1&uZ?18W9v;f>;_&mB9?bLcd{DQ%$u6*zS1t[7_V>wvcDJ,8QCO$OT%,s
O+3d$%k';case"sv":return'(Zu*gaLWR:$iFVN`e]u>6b;U0gZ&.h]N6*56*JO,".P(@pl$U_B!OOxS_mZ[0xfM}.mc$g)b/d,(`]A6ZB@@75Wnfc3y7PLfgUzY~?(*!qqZ+sT
}5Lsko2."g"]m/(GxHbi2Z:t[xhrfv=URm9tNoA4LX~"!9xwUuG[*/-KiX0h@eeefkEdX?
K(&kII97(1z&x3M2P{8ZtI&Wj/TLT557$1o6+d_k4!7gbfcVVb5CpC^g$~FPOx
,6ti3JoLoZAVRi%jTk]5c9nRs?%^Dw70uj8Z;t=Z.sDeU&I&/]~Hp]z6Ot7gq9,#Lo}bC3HYW5Yv_@[0sbJLa@NF$*SmZ!(30Ig@Cag,/$tffcK9sx=`p7GKC
~7yO>S1<>gDhka?aiA_MJb_oH
RBWBy*`L^%CCCn=co)&)"eHuM[}fpP((CInm6!*=^5-J)X+/`v2p7/`l@^<&1o>1rsufN9N-3siiJffZxmaBM.Lt]sk)fR*0J[-E]Q}&oO)^*ct0:-;qc+w?IT@)><i2ZrIhaxj1&=*R/X
b%:)-;+cv
Q2Pjy$>i6$:yQjh?gz2DBn@lkx=Fwj4|M!,`M*!+GoD+>?.X:%f,XdO$`"U=O"#_j6*?7xN7">.Nn2=Mmn
4)]%)I_H%-4Q?eS%2"!yma?WC[Pv2jB#5`=]2`iU1k%QQz%6xM(Xs*sQ4t`N%9?$^R5<&jJ-!@NY)%+r2(ZI)-.V?`Q!]#
Z=]Y%6F~0P=mSk!N>gZ+IYY.B=-pF%!W0l(8A@OfFZoayaS1*;6{gt8(;(-{)Wof</&x!ndG$AAO!6uWOb1iG2Irn.S^iit6!Lk&,94B#kN5@A?n8@0`-&>7
[P7q?)ud
`dJmDqw9FyGZf^)tC&D,8q
~Y.XV"9#>C|I3-UiM^qM"6m1E!X:3+Hl5y#m64~j=#@@[U3(VJ$AQI9o{da=>@Q6^MW)nNy2#A"g}.OeWrMZ64J>(*9=Oo:/UbrFMx}H;k<DUFV>^VI,z.$6.4!TbcdC2K{oRL.;O[d4y3AtpsE"ST@Qh
t!9pRSVAT8_#g_q]8]|vN$0
1@JiIsO.e>aEJ[}bPa_r]q
<4l:Z.N`eH5>l+r`x?t7ZT?G1r1/"+%]mm<>S8(uFfTB(9?BcMI3qKpQu{ot_S*|_2+Pa81K,g7GN*a$8xwlkGCz7&^f1rE@4A!&:qvPT4C%)x<!#N!r8Dn-U:[%bR[?KqRhXLT/"Aja_e5I?iYdyng`UC$w@nWwsV*<RvKR>fxv")&.7M8+U+H-h5O,7OZ~QtX*3)(=1)q?,.Q,jo&lnlCq(ETmX(YwCr`sCGP
a7mU]{N5E}p_2/44@!8,8r[Aa`C-%Th(9X9(G!mZih40*VAD]<:ok>WgL8g@`DC1gR%^:r;-r3&i*"OUHh
?ljGmFbPXoM@4oj>,A=]rKF7b<9wZCeF#=zy8+7/9o>r1_+v39v]B)oOnVn]t/s!:XP@;mw#S4ThVbi/vO#37Kb,,8+COa%[e,R1K=?rni=Oqm&RM(;d}UC[_POOPaPiy#(gG-mAQ]&06Hk=sHt
Z*v6eVh;>T*TZ:n
$XlYt3USW15:g)8uUk`Nh#HhUa@3hEZ5(eWC!>sb/H9F86~C:hPXZE$w`i&hE@cdttJ"5GKkMNb0M.?PasIAcoMb#<dXY8@MiOF^P1o';case"vi":return')X//n1=1@&+tCnJ5g0%#wt;XVDL1ST8_5aWvcXxa+H03b?up/tZ(L8h8e#MOp#&(W-{i-BJ!:DCKmN$STD_v]]-x%b)#>0"XwB
vKB|rgJYjT[O[RMz%4?hL0?]
nC8t+7>@3Ct>XmICD@F6Dql<<k)uMD]?K@G)y
SRM?K!BlV)rsusDQB7;n,R63HROa%6H!6GUk9Y+t;hKY37sM9/J=LPb2IH;-M1G/qoM46vw^pw#YzmCi?BJ/pWqhnZfv232>Ba;[r^,L94G3X93VF+Wn]Dg^ej{hfsu:0^<AAT`cJjh$VqT]
RRc=e%ZB,`l?Fvo=:pP%$]r.PPm.ey5
G?./X$e0)Hm,A7f_lq(l+Gi5Tnxw
t3DL0&:],6DGS"/!Gw/X5N"FXTEi7$4H|mqn
e8`ie#&cK]<xWofa<dL`xBLebx,l-,PPhJlS^rs"ss4{n7h7qp4EfFdKLbtCld7jeRQ#(y$M8e5^Am%<IJf*L1/JvM.;>UJo]v^gkRd]#56jqkU,LCul)
&Hsd>1=Qo>MSo]kKYMGzqRNQ2Y:B<[g6b7ML<
t=fuIJ`PH[OBfG.pY/ELsg05dfKU`}(
4wO$Af8PLho+WQEY%H,ne]A%3tXL0kO^To2{57Mmn9OQ^LxR)~={ZF(Ck5(YV6(lJ|H
QB_IU|xe3fYrMNBv04Uf("sWY5;}oo1?W7_B)@3qpAaxgs5q[{&*Q>NJJuS!y7VXY$t?LKrh%m-lbE1mFPBJCsSU,w*"=^dVcH6-S05jfeYyi8L3*ebIcri.xyP-QVM=^;Q=MRTo5l1O+/6@Oyiy^y^VtEtR)!@9X?@Is5>bPfkKb=Q1jRBSR3vn*5=YZ4cqOc^/DH4L4Cr>]yC1(bhAV9&p8)E;52FVY"XVC-Yn$uqq1FUy*^UeFU+~AOhU^o_5ABTxI|ZH6c!mu"gNHW0FUwyo-6xiQL^r_P.8]=/=MM8njC
fwog;CZ;<rWO%Np]"xRi8GU!3^%d_3*aCRdfVxG:<Hu4W
9ek;J07!!f)rlnnwo>dTyJY+5L,&wg"3O"!4TPnl@=vkc]k$E"#3ee6=_`N!K8PK:r%lO
Rq
`de]_H5J?gdIY/wq?O#!M5.vTNBG.]-YeRy5J3&/Qc0!i2JPv:r:E/vzq
h}sEuzbR9!+|QZ5m-
L`>-d(:@YkAbL5AU_xB}IS7jNz%"JS"]1-"hlS5Gtqmv:7dq@h#$c_oG+rIx>J+~uEG.W.fUuX)f,P`T4X>:UwTEH-s_.DPz.0WSOm`TrN^bK,Zs:=0;a`c7r8yZ^uL}Kw,g-F${n_l4=7HYso6GZk
#OCh<m~6+Tv=n0UiK]ffadN5%5-])wtkbf(9z/xp&%;)]7GN{%

6YaH2.C.(Ok]}i&XaHuFhshZ5Gj
XJ{1nskYgmEv#f
.Ef
fG4n1P`i#E=tH=*V+&8+UrFw4qy*DyLlcINeAYeZ]sT%Hi6:A,$#;hKD^tr|M&BAv6-9p#m&4De
gU
=7tn]W.VYml!#mm?n0{/?19&CSmoV=vxXV_)ZB-1cJ25u")%.CNeDRe%xy5xQ8bG&Uz)+Q)P8ngMAM[8~8U[Bt<]9r|i3Wggh,2;o
{!G>21.Y@"]#_ij:j#rDms~cTGIA+]zD"!
7Bt!!tkNpq7+G;nt44,)ep("-k;=njPaMnJd,Wb3Z{@.BiWa(l=Rh;:>Hf4kUSc7%{xIJ>kj-JRHv~TX58Nxi`v4D.tXrgqRB3wQ[Jck[`+TqJEV4@RW%)]EFR+Y;KWm`=d@a4&udUo@Vzh;_.=hV:_1DuP`[~A6R,Jb@yd)T7lqUI;`d]YS&(V<SOOnvMb*N,N|(t;M,tECqPRuN9M%=WDJx>@@09aED96p^G9W>?_VncB$EH]3ulr{EFeMev9R[(Z4!"=>FD#2(MCiow5fMM9Kc|BZxd';case"tr":return'.UF%@]A.7&)Xom!8p(6"/5
$KYoT#.b"$%@NMp8B?3LFliM.B*Fo""aD)qZriXjkxhSk[u&t76@4xAKNSs^S2kWrl`KB_cKfO3$GS0XHjL/Jo!oHU<B]m+~W2g9M>C_+w&>ruw-SSaye@dYpRc*0Y__Uq
z6aajP4/[a7
e0vaNxxqc:_hJjLBuO;FriksWExRcc:Qq4O?w80fk_YHWhO$WHP8R`[HF=4s#+>Q<t)xo7{(^2oCM7?m`W:[fL<7!q2h9;Yt%[aIzQ>f&S$Em`=sxUzmEE9@ScKo`tD+1LFyc3w<#HVR*tI]
;8.mGPo_Fd<KKwcgnr<YdmlU&@C+f:`;<0uo[Fxa@^28Y[Z+a|K6.<.fQ4e%fH52=qaaF`/b[1#Q93"tp]f8ZDf1UVMseM"/
i3d`]beKNQd
J,Ao
xOZ:f_lQ-](b]cfg,%fjj[+;Js</.:<U+]UL0YZYJ0x3YAKG8+ZkpDIe9]wNwkMBOjv"s=7:F5:Q:9ga$V4bq:Sv_cJ2rz/Jo)D1r!
k+T
&TP[J[}dRO[nhqS@%2,=T&D#EM[s
XBm)Jzf~NMNFCb,^KO:>e69^TZS&EeA7&<oqp<:#
CTK$"8#11d<&2EzVsG"*3r`:X!h<=b!3=1GH>`7:@]sIMa%W)Syd[t4
8o+1F=2E|">T&gu+-mbeX/P"u:J+T@D0b(,L
kegmy*].v(^Af:,hjf&8S%r{:}8%I#-4XtY?7Tnad4)t3qRa+8,"v=GKkz?#cU9<s5tay9OqbouMN~<P"~)%uc;~Wd)dF<cM^Ek}
zMsW!7Enq?`xf:U.L6Qux?Xb-
#YI.dwmMVG0-.NVP9Mze_mmNz>*.:l#INi(;SB?;}"M0<iNEK?9JM![voO}"iYemEU08f3ShxPgk-h9ec"RN!(J4sV8>tJ5"c
>L2IrA;)o!3xgWjML#22`h^ge/:X}MQ8,SI/87<
BEiV
")ka*lo)Mt>RoEd3<:o!=$7MyJ#7-8?d/lf;GcPAV>B)7.po"yFX>Nm5=fWQ*:WSw:%;0}Z-CSV}3hXT3%?h9/s@!b.8Ib4*@J)J&.T[[(3bZlHVpr8MlWS<P4!D;gp[a&p&_DCa7?4;D}@O7.N=q,FJdVu!71);8wa:WI+.LYqhMV1.Xc7NGvcQfu,h2Ey#4u*4WCW*Eht~q1Xl(dF)!?-%QMcEY5-]>yZ*(aK(qZ?awiN(:4c$2%,iiBirMRT,({ULh8?[h~s`3~!a#"OG)F,Sqv-9)wjI^{$m@9Lm?PaC,!ER1{px7@h.ly)0N89P#Q^2..H_B&Ck,sB|%*u$,mB!ASgDEaL2gpj:vCh???lr0zErqt-q6vPf=?d:kY%$%K/l2nyW.ekC:QYi`=3q$Ihmqo1RSthHC*+#jX&"K#Vmn7P("q[sRtenCAj&wv
Z/aI9&O${8=13:%%Bn9?Vw#?WV3rIJL.S!0^gPARg1_1Q0f@Z*j:Y%X`l?
0xEn(ENKkH<k%dcC.H9o=fF)MT,;XBKI94`rA>f{qCl.JEBp$li!Yb$
lFxyY7qF`=CGlGlg2%8CVC:-t8GIGc4+g0_i7dJH;{<$f.:k/~k"Od$L8a:45XKN5-`;::M}^*@m3iwhy-yioD#ho+Mae@FfIhwfExJHw@nFXKRUSjI@>iT,T6#B]e@+"DRwnnQHE8c{#X-QF1M.JRJ%a`CF6F/Sb3Dy1abOa"Se%t.nt?;z*K[u0PR-X
N0v*vBtmVVB;*u&s:=K/8[uE+gd">&z"WrE-KT`8rK,TA=QitYZ,T}RF)yoDQf#6,@R)m)kfHRk/u0D"5(M1D4b};|R3!$84n*g6)@H7%;)yDEEh.n%%po)WSduTd(].u~N&';case"bg":return'$ev%@bP+>:&vq[w1/G4N5a(218dQbv&BN/ze@s=m[qu%,@*6K"GvM:0!vT_Za-0j<=MB?vej>NA6FWM$*N.v).)xcb
7]lX?l&oL.C;i:FZE^AchMpr$FvZq;dDMIk5Gj02;
OWSGZpf#mBpB:M.gO,aFVn+(s?u
K#Ijem%`H/h9W:LQj[l>c=[gxl4Lq?!X;XW`.LS+o]X@748Z:`C7&le15F>W9f/k#^+Z0Xp(q{fvUkiP8FG2xOe))
Saw2g=tgN_[rqqTJS5HK$7wduJ6>GzV;T"C61sh<]&e6<RG7UkmM$o3cI5AiorlVOUv(;Sp&`xiP]h.ahOV6iOlXqwYe]]1<U?[D9oO,f?r{19Xth4r:LwYmxh6;c#W3jf6w,N]H0weAk)hM1%97bBYuNTL@<pK.xH@5524$I|uej)>2H1GA]NC{HmI_3W!;6xi4CQN~rEiqxVgN)+jkw,&f4"sZ2~qh"@wcZPV:RQ_he&1+$Uj{d_O.M%5b9r?N%3HC8i1,GUa;n`C$)ahg"1W+yeDi^%eI.|y#;a"hJs9=F}Tp#g/^iy9>bYD`H<G#5O,k_cs<^rB3Xy!&+!@s4r9g+.m&IoA?>RU^cAf7rP,aTPNykZZfpHR"pFW70Qm(hNqOZg)^UW,.:36.ekY~c-2,*A=ih#*J">MhvDJ,^^/ujDM:t
nx1~ETr+O;>Q9fZeJGhln[-~1uM4"/@G)~YKp6p$1<8>]+4/aJSB+rJ
s-iEeAP[?dZ{4eD3uDal*P-/75I&d2<XgD5CVtkkLKLf`#y,+7_D#7xXBD`~wpi($JW@AF)GD![r&PO=]:+/$33Fa?rMHki{u6mn>N!lw{Ze9NLfk}YSx%ilL(?X2q361V>5!2.&wChhbtiUQ5tQKqcwM_*("trg8xp)JhPxgi,XADKF
`"b,zp-%RS7%]x*J<$[<+!_Y^1;V3^h<3wx":=Bh|4qx_2/D:HSIU8Vy;2:7/2M
KF.@RqOIT2<$[voG2wx3iHYNO^{X6[rRX5I]t<;W>8x&3m?]Tn~gM@(a(>N-4.mHsc$4<0u
RoroF*/(Re7W8a{e&?8QeXIGe1z^c4yk{qm/B&2D`[G/Hq)<K:Jh~)`s7aaS)[}5y^|b;dCPZ!judj,);H6:?j{hE<r6nK2O0(tHH?<8r2X7x,1)l(l[#aa@@_S=eH?U?ki[>h$KrP>OmI~2MdP)0RRhE*wen460v5{>8AZ$gbYUbANZHxTUPuz#qNNI
%Ya1cTM[$~$qBXxjjN3AeY/TO`wSvRgFL
fp_ga]e!$#twd-a,yNNoJt2<4z4<v~=zHn:h]"Y]N}7zP^/n8-BO9w-?2Jg2"C0---l_Mx2~%XFvl_S2(|7r^Yi.lRmrxB]|7MIW-3)koCpfoDj;9oHH)ycV3%Q7>Mh^Y7gy0{*zId$+3G7K3+:hS)^w@VU=-nv?x|eF4H0!P5s:/{?oaDR^^V&P*,a2/^<%Rs1R[[lb;Bk1Xwbv^GTaY]3_f8]h"nf-tTiDlZ>IC0uS0g`<$iN@y1@C;wu3mi$kGQw*YKDfFt>?ZJ]CQT_GW:l(5D
Y6n>`?k[U<f?P@BP3V|y])HXKmE=-(H`1vO&]
E^7dz^w@P=J@[9NVjT_lX4Z^O>TWJ-;S%U-11NtDm9<+9.{u1?/jY]+tH4WPU//1>rSFtErXR(VCX)X,xKE^]VU;90PglKbGXt[V/[[I.2&<X/^o9ERVgLUZ@K4e6_!A"D4#PIyms7-1+qQrbtI.9v7:cT35IEDbV&nTR-m"VkhC8&+Gg`G?%O?&<M]f
Ys:]2777r*w;pZBu;l@<A.w6.vuh)/7-)%ncO#i`<[]43bi/Q7hy;vQ$V)Z83yu=QwFmfCs0+LC(I{GLk<=Gv7lAFl0:8)YZu9BP6NE5Zh<OvUgD(}ttZ!@D!?_>hYWFo4
w<5dr9VLq)$jSz%!BwPoARTrkQ"f%Oe@-<4rR$rJuHpe)mmd>();z4?PHi-6^tdyRO}/dD.(%V7Y4J{:T(&kwl9HGTx%zIePzP59]I:p,53hdTS)@ds&GA)vAahqTQ^,=38
<&V<>.^At*X+&AC^e#~w,c>`*rQ5r?Ny@0>rg7Dspu2%<B/&dwr,dSb0o-J?.DKaxm]
g]"Z-w$PTg+N73638<WG9PU#rdx#O&E?;@QgyImRkZjR~ith}$@

ZrFzaUe
d)gR6!;JAX&cc-ZXv"mY*~
C1WJD3
2n6@
ba|$JF0yKc-PUj/*++w#&"2qwW&B@R.';case"el":return'(h_6K[z]$:eB1VNw=EmW,;XQ^Yn;dpTy71H4Q#L>>SgH^>a;;#SR9^|.(4K6mh{W,B/M8i>1r9+m-EwY9E$hiMn2A^{tGS(R=FjRkI$:?l}HbspP#^+*U?{;j>^ooB2[B#TME^#2f7MsbZq(Wocng=~;}Xph/]Mm8*!fK3c1am1n&z!cZ3rjtrOm`<J_eqrcTn2vQ`tKYrlaQPuTd;bJg
;:I!=%dFhK2Cs_2X/%Q#5odl&g"wu*;OxO1+tLNebsU$x`p0G6446#|AaQa<^8#I6V;#A*HxP+X@!]<1shn[SSm:A5>JzbPDA/oH^ZF&$^wh5v_$MX1/q<L^Tdc)3
"0>+i0RvNKkN,omb|q~?L@%-;d]P5$qCr_F.OK"d7aU*B+Fn+!joYF=6Vcn@yL]ux4*bv[!dCmHnK[#spEH2vfqp1oRIo0ALdcxQ1x*5}q{!Sn@6So@g-6!1$mI<+s5(5&j_J@18IR:t*I>U
lep_g
Zy:bV{%_u+)r8uVkn_nojP[Nx
PzRcDYJR<;NU.ZiJ$iX]K_7Y/k)nRO$RG*A|cGVrMjZL-CVEf^!Y$|kDaN;*,%aBNmVqK+`rOu3n94-Jpro2X77"QlRy-oeoccu)).!SpNA,A&pQ@p5hb~=xf)S$+$r1mLoS-Es7qEX8UjDQ/E%
oMVOt)Ryhe=2cQ3j:[dSOtPJSk"aIARXQc3hG_!`86sac=G)=V7{#^Sz*gW-U+GClNc&8jA/Us_[>9?71H89Tyh7$Cf}-0l$uUB?x)&y&-I<S8,a#iheuxUfNmeb9;Nepg5=+dGkSI<hFj<GHtGPAFUh4=nKWRlwuhx&n-8bm#9j[C0!U;q<m0M7
|c-cV(O+o*cig(%VksGb@1)XkSZ.GCo/jPejq2eTl^UBr$QxgTY-CRI#eUZF=Hj*}B^JORqVb*(9U8A`ihyN8<UjF8{BS;"5J:AU3h9M04(+Xme"a95=dyW",r}6Y^FUmY@pKRyu3xcx+:%:Ru8(}]BN*3>Y8^R`x6j-,BK*NPp1>Z0%aN{IGmYn
VV?eUdK28%b(P<l|%h%e2>$mOGA.t"IFn3Baqs-b&x#5E<yJJHj3yhYFntpkB9!@#?fIw%1luND.-%-7v>8&b=r}1B_5uGg=s"!ppY_)Bn!P!],pY<Q.-VF*?>9_c5l$a]kmWIk}:spx<UNLtve|Ti.Jov%q=<2J@A<m#Xg,JBas;Z_Fqw`*-{_lM]<n>(]C$]<"-m)eFO<4v(*@,l$0MN7Vtd);R%qNcy&?Nz=,]gp=7fB{,%I0pEr
nO2ef`R%Ky="QA.]FG>s-p_ujR(I<C-5Yt_:8O0(1N3IiQx@ko6;QF3_3dT54{9[+O.EI[*Hvu=$aI9F+b:]6(O4wzX}+iWBoGR.`~EB0oY.M::StW^WH2EH,cq`2h=EDc6kV>Or)2fq1sodj("M!AC{7i;6)KsI
z]VDGQx&*Rp3/"zF{P6GmDU>^D>8>bxDHZ9<_T9wIU]1DY=.|;O.K5w_.WmvN)x%aUpIXQD.C)pQy?Nu8;<rF,L`txU6=>Wlze|=-P|P8J9=[(B
q2p`3NzJV2Pa:0m8l?@m5Xs0i`^,n*Te{fHwP+Gqu_NakF.bg0(xH]1[0i"0PGo`DLv#o62p2aW=B1D>?pVW
&bB?%]_>y_CiR/2ODQ3[ycE]0ZOp-r0yO-){X>N
,JMd5qsh9
0?Wy:%f|%emB
xUS^gv#=ez#Y(:tYmyC(VJ^KRieuJ[J@Z+TJ=P7+R@}f"%8v9Vq3sqB)zq:W6F<V@r]rq;T&0JKM+DU_6FL_gf0)6D9r!VXC~R}AUqa6DG9<oEajZfR/@]dUUv3O0/wb:9JO^1Y?5P7Ur<n`Vn1A<+4,BM4-X[()&l!pvWlBnp9@hFef-FfY{!6R{<J]FryIE@C)F+Z_>_,MI?n>fs(Mpm9l#ZvI$1&`Xkhn3,k.4rF=_&6F&y*N|A#2e_2;]v;d2H!nj.2PHR&"J)B_(2{l+x89At#I?Se8Hh$MnT8):m"7wFUy/<
f&m~L?TJ5Ila/cZ#UN2#2P6"`t+CEy;(+<+3_G4L3BLyEppLR
`mSBS(t#klnkos%[p<Q?c;e=cZ;G?SmadY,F;/W@=sonW}:%ZLc<n7jRhikG0a$4x=O8%5Nk8DYs=EYT+/wn(<@+9ye"n~CVdbydv{U)/"<eV><eNabtk&U[UU1N`LIK:m4V]MFr?Rkw6[JU6Q.Kkx]_S/ydWgcDea3PsYRZOzsJAes(JA,@H/ghP9uiZN0@U}bNt59,MLX*gQl)@Bu)t*mW/X/5g&`j<KFb>WD7<W7_uW`*mM/kh,Nb8V_L3dWReQ,SQ*RFO&-BwjnavDT%0coU=F=3)3@%fZp2PG(_YIdW3|!UW:Kq)6W#LPa%NVRdsiv#`4eZ9$AKYgh^r[>$r90Z_5[
.M!rF7Z<>YCFJ}gH$|_8Riupe#fcAY"kAT_pJ;rv!m@"kx#g5OC8*:$,wy4nDx55x4IJ
/cKjiKK</6w`6pIav"v7a;gi1GT:h"b';case"ru":return'%ev%8aLZ;:#*#q31E(4.e.)<edi4`3N#lfn
3/BI<"N8uo1GxRvDf"*Nn*H/s9>xd=M!E)fT=BmJ3nY2z.)o7*jheiDK
[hl57^li`ZHExWP#Gv
OfBrIVi)~HkcviruUx2JwlMW]n>*Amvyq[L*9NgO"p*bR>.T+Ji6vVQF?xZA8#<orP-q_n@wwt$y7r_<mq(yN5U4vsoUz31lSBGPSXb+DIfM$u4X@JJQ.wASAn@DpiggeXC9B*72=MIc24GaB54suT3V}eBrkywTg[>sy@sh,-frqL^??$Fuc%;o9ly/0-#/Qm3x@K_w{,E[tlM3mnuLNC:Gscy"T_`NexlVmpZems}VO@K@C,>wQ7u"_T~d&tdc"6YW{KPBgX$2&=MqAvonkg&SHeo2nu<9gxe1)Cq.@#FhEWw>*>ZW9f54QZXW=fS"P!O7kbj+&+{dh=s7txk.}[bwNRw<l!e#.dWHh.R<.Vxy6!Z@"iw;-HXdN5>T$5swT]DR0QUSI&^Fj+z*1l>6IA0q0@((_;>_h.<==t.@Ocd.Rs!m|j17{gP,*OcRN4PDgCi+sLQfTn,qJo7ZKA@"Saa!k=Y8}rEP^?]ms83KcK-KENKjp/x"c7JP6NuU%kjyGNWFw;Z_i[c3hm^?!Y<(I:w@F73@.dIh&Q|OKN_ROMqPC6x3v:Bi("=opj7KNUXN"=Tm!);"ko
f6h}04]jTxnGn}s=kcwjBa0Q+]0/eUP%_f@lT5e>bPC^.NxVp}TzJ,O+CZ&0d*o(so=q@txk?xux,Q"<;D5/iE`>SVHKDPXUd>TLFYX@%.iweA=_$Cuz9f]I?&*HY[D)2N08U(rJ,ls,;~0/#H8B&I:{BkU@LY>}B#AX@y/1@^`m=H_U<O%~PX[=dn([(v?nQC*9>^nRj>7/1#Tin
`>Bt3@)O!*vQ;n*$uC,iQotq7ReT>uX%5KFgPH(]T]HQ7qr-H}FUDIMv49
Utdsxei(vZ44+TyxkZ)uv6tj1bg@,,gJXfmZAN#q*M|x.5GdF9tJV+a5sQ%kMga]k(X1iG^=HEZ-)(U]o%P9FY|5%Wm.v1dN=)dfTy`9X2Of}dbe=,PO|
glmAs/hLztLKc^:Q6;e#2)86S&BP^?b9KBp5N7bP)V,i?fzjp4i,~euyVCZmQn.T
6eV#RP3-b4B&C|&v(pIz7+$gN9uS@kX9&)]nO>DqAM"CK>rArbbzD7S8""V$[qVFR
FrnnR[W8qDE,:lU8V)b@PK9d1`
D,*:UQ:mFCr-<B?Y4hgT>VXaEe@2O<PnZ8sh1]a=1W8-iO|!NS|
csgMHX:BmES6$D}p7`Cg-d#.iyrgE(8S%e89|FQrO$mVl.!$D8|(2sriLf}O2v[&)@/-(K
seJ^/9$GP[sdtW#D=Ux,PlUP;Sq@JgRs_<#s.kR`c/.`CVB1k;mZD.Sgj](kFT;P>_s^C);)FQi@%3!_gtWS81hD[|C9P
heKtUd>;3K_+j{t.NojA?5qj7w7>1A4KkD@{AC*O<BKQLfT7V.Mbv9ue1l
|Kp_qg6?NIr4X"CEw1)k^_OY926%2]WdK-T]|g9wIS#%s:~0s]TJA>mO![).&639Us}"r>th0_[bAQm_6TPTz"1O61+GJ*[)]_J*?^!-]_IQhZ{(P%wV{FlR9le;7-G@YWR_2utoWYG0ZFt1RTWR0@svM3f>;UJT#j/[b?nr*:`cnR%,`9(Jb44n6+eOlf[k<j:sI4KE}PCdv+L"WymWK#k<sD*bh=pN3p2jE(AsKp8-(fC4`TE`xfrYItkm>Tf#nsGo#VwWt/zEF7wn[H/L1;zS&Q-2!CBC&LjyL?4o$b,g:8cmV[$!=gjFbH7Ny^1C*+*./H9;1b:,%FG9@+#9c@H.id9fHf%TC^{/w
+<-lMgDH2QDF>=f`<^XycAbs"P=/^&O_-8N*]+$s9Pxn,"A>NLx!B7QS>(0
ne<mi
[xlGe]oqLF/@Yl=BX8IxQ.J^I+}a?</^t%_`T:^g
u#(iuqK2Gf16
FWd)DD9Hw=8Y:u4fB$KbC*T
(b!N&mN?+n)OV+rf?A)n24SQeo6!Za/w#IklQnm-3G-W>msjtcS^#U;-Wh6eA!:cGmIt(H+o$
h]iA`gG`-VN_i5-?a1?y>Eq$sVDj`#,`v")URW+`Ej!t!L,M0n/Ak#2s^,B8;B$Z12NI5)-r"0ogKZnUL%F_aJ0RVMVL]%NdJ,[[?UR&RJtvc*y[Gg#$*Swb!/],N@Qnk]wn8l{j9pwhtU:cs6uGWHjyvX7nAZ77Rim0@I5/x,PF9&"jU^:t),&f)!M@9le
<p_%.#5_aD71h8|mhkf?`"y=0jpCkisxyty7oi74RkGpB>6Ox3G,w*KuG--

s*!!tWgC%"=qwnm4FF_b=>I&PL4bW`eiQWk;K2>@XNut%B/rwX<`ysD}MH!7$3<pCJrX^sL]`B9yB.=e`wqfPO,o>S!"FXEaG":>nB[*[{Y]dJ4/b/`8XeX>@4qj=ibow``3f?!T8Q&P8:)LMc..K
5NAOuS4`uxhl';case"sr":return',c0%8g~Z+$#5$tZ(?#o
H
]`c-UnuIG2JQ6g>1Z0]&Q..
TT-:j6p=w8II(aI7kw8&.d)Jnmhf+SVY3v7t2v[yxSRb5C$K1cr`[ST.3CRI)yEvYa%,3t3xT20Dv9}]U281*yi`Kq!yFD9^5^xucz"_rq6wfXRgwH.Or+4`E6@y<IR/L[^yP2e1*!=JEpX/(
2otyK%HYP*&8vPS2?hzyn(sd@mh1GiF/>W7?SvgKmuP[dqEmU)u!/vETLrm
sP:wXn_1q8+e+59BirEU_R=<+.o<(WZP>S#xUp-QxTf.Ro2-.lV@P;|juoLZjqrolu}"2H9*,S{fDZ"`{"WF`)a9NyX+Lc@QFu@5h_~Bx0P--YHCbb7ORM{k).>9?VD(9?B@E`z<95e6f.v=1vGdqKs=iUmtU#1=2rC)d<)v[hPBHgoSWG<l03UKB(0SzGv8G,^a,]rVdKPuI>i`D"$&+Y2@JwRcG;C08">%S",gdit-%"#vaBv$eAE7=kUVIgeH[3BY*iy!~MWGL@~,`<ZPLljU/@<WhQCS.n%bW"3+MI8.t9=1BaJ/k*vG6(0Ys@WR[9OkE!j`q)r83(#2RXc#/THQ`^iMd5EG`[TeZuU#gQ)B~&D!%At4b=E@<ADQGg:foKEd%ohkHckg1)$.ir&Ix2c:Yy=H9M$B#R1Ha.wC=v^*,:g`5H,8."Ki!Bc[4"w-Ip,g`cJ^zi!6~=l,@9SoVKOB!*+D!O#r<`(GoTUp;v+8jK&[A?.?Un~;qRW
pe^g@FN6@r"dW/rO}rQ2=+UbndQ*pO/#$*sl(En):szY:!PM*3-Y@^Xu.jZC#P$"g.
gK>lOOi;!nRz:wkP:k+a
z$I(4!@TDxam"A/[a%jwcx-lqynE2xyX;a~;$py.uhEs}fkb,t%*r2K"D7#I("IZ[_C,h;2X|Q"22M=89h?^WY8%MV9]&HcMaq,xcW~$FH[e@&[#^J#)*Tb?B*Vni(h8Ts
584k36&T7wV9,QC<d1cfs7gMds!;gy5dYY9NTlvW(bgcH"1!lX^8L5iV4g*3NZfDQ6Y!6i@$WD@G!5,B[,3z!p*ZC{qqB_3d@UU(@+xG#~<=Dhn/Kitrbh@nY|=qUT&a-i!/C"@eF;iyb=>/jA64%mDJ"prcJj`CFy<1T?N`I{XLQ)XjY"7ky_0@!r5QIN$HA<Z0NqnuN;wh54WNt&5E?Ow0Zr.)U0tv,lhd^~x;UUGl7@D
Rz<ccZ0d#WEP4TPD
|6fEK;<E>oV_2Wn^.l9*JH;6E@yj1ke<RyPRD(JU{%qy;
1CEj"<^kqW<m-j(N%;uX~A]A)RaxAQej583au`#j(f-D"HL
Puqgc<&1PNiA=WTdpK%s8=ziPJfd;1T5u^a(Psm]472BjaQ<.HPc@.K:20%D]W=KvG7[W,lxTi3pi01j!`::3Yp-:E;!Lp>J%f[/wD4QJ_6TWJ"ZRX;V&=&Es.Vn%BXQz%e_DO7[hJ;Pz4~3(`Lic`Z?c:3-ooR`R5$A&azaVx&?l6rTeZG))IJt{rFTn9N.lgw)iZJ/?B/?(M#wa:7^#CCZGk.22C5_Go`l<
<M9Kp!PsqOaR9)is2FZADaqBm21MwC
lG[gw;7VO73{`.`zfBc#bP&cs%Nb=`x@_4gIxkSm-D
Yj=n,OCEDKrJ~i%3S^{E.6T/rI[_Hv/T`j
jhA`9X[-N=C>i:vpRQ-c
cWj2*]2gqQ3%[j34`qZV=6zKqf4>8Oi`IpwYf+vY^XH@|=$+<a=bOUy>$P~`f9.a[0n?C;LE(aheeAlao
|9__v),Uls^iz?.WCWm5QEdfu*MN#b#C;-MB=y]!Z93at%e&|)iZ{.O<SRy:ZX9%uq+iQbC&t9.>`=O[bp@6B(hxtE;ybh
bhz!PxA9PNaL9rh.V,n~VwTKj/kSTRyk.RM6AjYR$
>/ToJpX7EHQDt,@@aL;kME6^nl!r?qEBvf]T`uAe
WL+??BvHj.dpuRPvc0bX4
AZvs!=Y&%4jsYlB)vA!$]bX@a3V]&9nq298+IeB`DQvZPpP35AN^-Z9
Jw(YuGTL)k0e^K)L,Ce&In{tzcLMzY)EI"
#)LcN
iI;;V]3`_Rg9&0N"78)D:gYyi[gtGmvQ=lYYMTW+Ql;PCBJQOa9/Ki+xuvEM3X]J>T3+Gp%u
`UcpwLPiCYZ
jmtZc(lVYmCOXtvYtLp+iD>xeC%';case"uk":return'"ev%y5DZ;=NR
x(LiwA08T]$[vPSz)B-&GRq;3^DUEkf8dlTD$qB{ZEsDRmbr`D_Q;FfTn[B?]yT8$u&6RO`Vw{w<XRM:I9]qq_yB+SyQXcJbW{m:aUcCqAXjo"<&xs76,}CTxU:Es4Vu(UMzy<6MS;F7vitl5I[EUVapK-oAi%G
Y,q$0ohEbRj
GAcU#K00]j^Ea2Aj?~o%ImU0^28P)6%X"A`TH
TwV12P%"UVMUsl0e,z?c@w`L
HyVp&6(`5iU6m%AaV4U*60FP-B*6!M~-SC(qR*j?-!yig7}n>38O8CLQH`"-%NvXwK%D_GtcPS$C^53_yx:TzYTq`!4[J.^J(3I9g$fmYoo$`6kW1+28pJHGF%9S#t]rakCj."`89Zb+7-Ug%XA0,))+yyGCbW]JlH%bGrb(~0QX:,}L1i@3!Mn0CVFSHp4:d9Eo
b+Hnq+iP%-_v$Y2d>Q?MFKPP,Y(!vqkj9w(GbubqT>k4!1$?*Lb7IDmI84a4`dGxC"xPmL/{UkXxn%^R&J(]_qRnQ*4O)nlAe6vMZT6#fMoDV1wf:jSkr[E+?(uI/ve?1W*n;.8;gV?,ko[)c~fdbNULe{"&J(=]M3#A2fYO9)6f8+v{pY6`sLr7Qr3uM688#6#NgEpG-TH>IY:AGBU)wg1e5oHqj)th4w4P1OqJ"adv2
[w+^WPOZU!q/jdwwo5XAe`I(KuANQq*3x%D?/s/2DAl.>X)NEJ(`%Icmcq+>R
e0bcS1N;iiP]_JK2R:Md@{NM@6@+_U+UF/uBTvqecHor:;SLn::EL7S!1JL.sw:BVSkJk0AOueP,alOq6;"C8d5XP2,q/lf+G4K(W/u_C|&fo4`7ACCVkL4PP`*GEq/Fw<-UA7-p#
U"$zTL*M4C>9u[?H&Vb$eN4it{,98>9%f8bY#XD_:M"_EbTQvW.%g/"
&+^,+h6"xan-nta{KePe?}ljEfC(wluIt/4E<]Yz?lMD(HVU!jWvjC$t:Ax|));0Ld$:#cT6+gf+v[pI&^66I;iN>8fdygRuvYrN,7O>Iwl=?)%|<?04B{.8X"U9_d(K/Y:@"7Qd!~>sWgA6NCH~y:O@mOY0D7g5bp.4b6H39Nyqf`fkatWUXb1EEJ+"+=Hxk3*K8}Em-)Ro*zt]];YA^UhJ;V+?il!Fo55A1Rq0
ukSW,!zs+U`
ciph>0}YmwL?iS8MwHhw{#i_F"gLzGu=X
")GD`VHso!gg*PJPxbh3]`w?lU#X"3Aorx()EpEwx/~[%p2@dG
#ySKx6)O$fqLcCqFc@q)w7z"4jrw"OX{lb"7-J_s7x:NEWDR;20^tu@m
&KUSkai5@mLM+*vU^X;_/rrhWu:[SI/C6FQ
t0a@K@mOuE"`CS<1F`aBC$,c:cY/$5<T
i">8$5_,pX05w?3?p
;gTLV55D"7F[5Y
MD{JYXoqDM})Nm}:c47Zo7[#JcXF1DtZSB&!B_cvG0MR;XA`g1Y/S0L.z_63>Ue>07=&wuY4yDt
QG~4a
-HbZpH%hdYg0{$7Tcv["f*e89V)nJ0.AJ*-+tRX`G?:9$g$5@aI5iTc@g&
Z+mBtlw7e&z&qY;#]M-Q]2[B5^>OK,RDiR0N[fU><2@TN!A7]^DZC;RTuScv"Ws#0*f5r^BfVF%6jB_cpv;bQ]+6?r<VG.-L4h2!&4Pa&RxL8=
+R]HZ$])/SWS!Bor"S/mn!o6O=1@O[ah/^F9o.78oDbvL=Y6DpSB3hQa(e8P&T[Kte6Te)"!DEa$
Y8_6%"
msq=Dx"QQ*a8qjOT15f^I@gQ,R}$tf>rWARcUdtjVlG<$XS%-`o1/p~xh);h]UXik(EvwhEDq]
8P^@_
Yn[CdW6h,%:v87&MA!-@h*Y*W#e7P956)ar64)B_K;h#q$"d31_ylq+
:oX.eX-?g|oW@|eWTCJ^1b-[jq*wUk(r12=#C^[~a2@)r&F!itWTDVCm8;u(9QcK:H`>,U]JKW_
xfBh,j!r9`*L!]Rs/@q[B0A`-Q.q;P*TXGaKCU$z>Ttw`V%7aV]T0fR*(;HN27X,)MGTv=?J>WKjTSql!ARGSDgHjOj40<*7-;8^-<w!VA?36zR3Cxr~
^;hFgc3Y;EB+Cg($Vm-?,L"i_eNF[CZnb"ls/4{+-u?ukn(oHx]of+Wau/<QigTg0o6NS&U)-T0W4m4eefj;?Icr89,C%f>(0aW^V.|HSo[k>^pyubP-LY0OfQe`=o@k^Ofl.F$hsAv`k=<J)/and>]UJ&mM;rxSZdm
0;4!!Lsi?6cV=Y)dsyu!l#"YcL}/nGD`Ce3Xmgol+`qZ9K}-+kW2y!3;4@RM!T<;JGT,~j)?r8!Mb=v
D6mxTeRp,,jrw4d*y:6lWp
yr&PY^tc';case"he":return'.s_r@6KX%&gkb4}o~ot1MJoNrN{@+5[uM9Xf#G|3MubGN$ru"?SZZa2NadWVGSVP,lDl.H:vDEYs26h@xf)5OwHKNRQpF]4`a4XkVrh282yJ[ohx1bn*fsim>*)D)_zz)E7j5k&P9A+u[Cn3sJH3%t,L=)EY)rCe+xG$Uu$Bp7s_oDfyD]ee#N&UW)Z;}w@uBhwfKCDY`pYtE$g/LUQdDHswtw
]j-WB>:JJn92tK,wg^<$OBhxe7-ZlC4Gp[u~i|qPd`GwgQ0zXz"uQZ>]H5wW056F;1kTkU.q9B1xk.c^E,"TnnC[2h[&$Qfm*bC5=x^2_(&Ml0`zW+yVSHGoJTRqb"gsheaRV[22lO2iQH!<1+^i#Als.KXS!{a6qiq$uIHl3b!/DR38]}$S>yXz*q3K/WN@"D6/tg)Ut>GhC2&
C[RGCCFv.~e2.TJQf!Bf*URMM[y-&1P0ylmyE3.7vCjc&WDn&S/eE03]PUpeMyX*Rta52c=~O.>|AXYAI@+K16HwEoN`T[&WIC!Fm?!q8x*jcoOqgu67Yvw`D;trwxmrgP2}9)3:P^(-gRw@pn/yox`H$N[+MR:l>2<?8]l#5P#AIXdU<i&]7
Etw+RH^z;u`a_CR;;d%
et2p%bTu=0p2oLS<3),0ebUi8M3#uXnDIp,75Pk
r#Zz>=Y6eR/$pp1
t{n@
|)Cibg[Oa)!d#wH*Hf@Jw_rf_afa*0la31<X^Y^wXy/WVQ|4!T}MQG|h<=(gcw+DN^PN:N"qO$8pgQ"Aj0r>bo~#e#DT>N
TY(>"xltpaV
c4P,:H@?;npzryiY8cYuPzf.Q45G;W+hi?,eG~urWEy5m`wVccP@.B%bx`7$LY
mr[R;9r1,rVy#JWH1=sU=49rx>]G%rpz%=w`ju&(|aT>Q03t}5`9%6+l?0Ct}F_!^40[/:eby0oCD/fdeBn]=jS2y$jvNr
VG@xil/^-@S"&hY{E=XU-.,_=(PB#uUb!N`^_ex8LKce_~4vJ
y]c15C6#FLA(;F1UGbae=x<e[1u3k:0YJDH?RHriW9R.Tj-30d#Ay|UX/M1~)[RlYz-Iy*bQaAb.Olix*A@zMr@9r#
H@@JL<?E,A+g0X8kd&F%hK;gJ&+Y@WhVdpnJ^U!c$Y)7a[Vn&><b~&5j1@
i9/31"Q"g[>C4tTbAp5l2~]?[<-hQ}w^[&^$gR0_@(
L
ho#Z3TU$}@x/EHR,!(Sh3J7P27^1#s-J<3UJ}20)lm|3Kdt>hm]4z-?VR,VVNSn@q7d=M-*AtbvGnsO--:8^%^cN&';case"ar":return',c0+JaPYx$c<_r![w(KPMEs*9d=eKCm75CyY~12Psc]HuP)tX1af<B>,C`~5vwcB<ywi%
m,SmP$V-lq3p?nA
m<sZtgxyTQz]HLSG#cst4b;LlP%hz]&XuQ4a,y8VQMiFUy`i&fHf-I,
}Q;LK=RU8JTK:1Lb8d{8^=BBo7eMPB~5JU,HzY82tY,Nec"xS7[`pq]Ky*XAwp*6Rj-y>IvOkR0<$A<4]f4=4Y8VIHzIW.QgbowCAhs+KY]Q,S)]SM=vtxmK!tVdeoO0V_g%5[;x|6jhvRLT%yh-"ZqHH8PD&wZ`|>m7i+Gt)y
87V+mTrD)3vyjuCj.7]Mk*LBVK=|gLa?sn4Btq"CAi[|AfmgCc@{-[AQ;g3k.[JUQU]}bX<u-u[q(9QEbR0SfFFTqBh&.iwWsCo=au5(n,<u=yPtmT6jd*^tv{fIo0(_T37l%g)JEj*+h+_7d|@G8*?kME;f
<U.v8n<8te5%)6<9%%YhxOMue%G*!eYliDIiH>y.8R51ma:S3w*Eo+4(|"8/#/3&~)hvmY5_6RN$RYm%ZNH6[C("<aQY9"tq//0;0o+Gdj"[P(/@
`bcMNHz!1y0/!J9Omnnk)}!zDb(Or|3GT4qoxlO$1+jv:{1W5",n9G
+Q*8q)WLK?_Pj)kAbFQw)Cyox"*E/+MffsPi{9uTA.wO]K1$[Q5?%yaNPrRR"Xw[Oexlb:"1rw}#pL"&vcF$/a[85&IulLT7l)
i:Cjeq_5;Q@.Hn3kNo!sY.1]hDe%jGv$DM^v)uP}_4bE2q4zR+I&6uV3o^KhK)r#CZ^Bfw+.rSY)Id)>A[!@ON%I-1-ml&Pv7}+Z*iIg-4K{fZ5ITs)DBMl5v
yD96v
P<:Mj6Hw?5h21b_~SXmI+K,]=V#FOO.:61`f&u5mP$<lf@IC^g!T,@[=e]p?-8"C;Zg97rtB@2-3w6;u
1=B
M&O>JIZ=9-}3Vh9OG#u,v1]?_ikQ}VAL_ep#KP6&15O6I0-iOKFHI[282`er0wO7phi"bv=
^9f"Uv}>18/5a
FYh0w.C.vk-5jJ,h6=CvN"Cj
"d]8.]&$Ird+OIdM>7TLB/(cD?2ROjc5O0E()&y|nu(yu4,S>If}^.#Q@9gXCc%S$awo-}I[Dr>WHDp>gAK"&5+"cA)UeDf%=%rJHZIcT9c_D=)#k:gZ;<n*DljN:&?*UUq+&=%&X4E%U"WiB_#ttKp[pR9h?*:F1ElUJ+/6DaC^Co:Lo2.ld%?7d"**8<MRBr^hvvbb)i4+uBVxE6*t*
3{ffO|4f&UExlIJ,
(ya8J9SgPx[A`ms"v+ir$Q]P,?*%b]bG"0
.d#jRu<ar
&Thql!E~Wdbuj@]2TF.&9>C?`wX]746
((YfY1utF01x2n@4W):G8gr+sj!dM7"*qnYZ
)a[wg$2QXe+k9&Hof4B28//`kaWI,Uk]I`z%U@~@CE*WDv|]$tAj]4-Ofx2G!H"b"y7n</
:QpDLkwzmWJu`No;)Gls:HIV%8sE2
<RK$C~.WYs;QYS+hwSHk]u(:"d(7wc5R<iT;S3!e,VxO@/V>jD#{ZMm=bu/amE@7/0<X:IU=:vU2taDNEaQ7wpF2:,C~1exQLV6#!:e-1)3W,O.MqoNMf&=QD+`gda`X"TgjdX)q84rmC]i`rO;Sj].S>dk9Sk5hQzDI&y/gHZ6nbm[p9o"{]zs%!vHLU0@jv]<Q=
>gpa#-bf=y=$a]d`BeiQ6L6Mv]Zujhv
7(TE4x:lK$Z_=%0Q8V^Ef[a7;7KP>`coP-nophHReM7UG})&?ET{V}1#U$g|!"N?&~La/hFj"AZn
L($mV^,G-WR.gPO/36`!3=xrb
~H?yYsdG8;qVIp!OY-N#]nUaY!*My@w>qm;n^](o4k@aEPw@vaYW{%y_{W
uOcp2T8C
M@{/])vpM4@1w5"
i=IZe[t-r>kR,X}V:I$+mLHh:15Pam^,1Y%l0"o&84i$by!-#';case"fa":return',s_q]6KV?!+u=N==J>C0X)sd(kSsm=+/ACiZxgkg<?|5S_s%UV72>h~.MT28KjgQO%fIH2YIv4ehW=0fHq.b[vVfEA*K94&^,2BjoM_s[vVM>0nyB%hs#2)qPBsLv%r>1Qx]Qy?+{,R4W2"p[1Xw(5d"#H?QFMoU}Kgxhn1(7@7m*cSAZ)^X%Bm3hPXpu:[3na-Fci&eeZ%Q20KIC6ut,?8*>;{FS6|INDg@KS=$6k}4FaJ=K61Srcfo~"b,4&0q0#5]_3k,n7hd4e%KAM>X#p
KI-8s|JrkGJJ6>?ge5723#gNw[JZlz_wg}c<)UQw1W!2bYU0"S)fs)FvX2O<=%w^et.9nYr4f_;yl^GK
z2[r^c&yyOdp]1S@M0qajwj=(GHg@j,/)(82X>.L$t2/&3Tx`y!7|YU>43]%0$CRq=qf?t=qa8<>6
d$G%fR9T&xz2.MW4h1|-4"YIxz&&5C=0<9DY^f;wV@OUg0fE+,~iKL@ur[]iR)bUFDqYK9wue6J0(&sLU!?PN"ui~DB%hSwBlGI#tq1wYG_8x"j.!OJQp.~2ZPq1o)JfPhnEu0OiFfcARHCHrhy6beK[GI
-G`Oo6<@Wx"{S`;)N
#$x6lpmi9QCpd]t[?AI@nr,>Q`%att(vpwlA6XorUmZf4o^]kc6NE9m%j;NZ01)&VmR{Wi2%V7(A4@SHqh2|v;p+-.Uo!MW:5E;y`9VaT:OYj{/4VK)1Y>7YN&WDeAo[qOKr&Q^{a/Z?VPvch{)`.JK}v8<I0;f9gEtk+;z"MVMq;Af#+AeOE+8zNK
fxMxCctpQ-1P|HIUn3.(M@g7"*EQ&*K&@_s__Oj`fqt<D?Wl(+%$|_?b8=VgwH1YMrjPAbdduOUt6X^+@m}r4dp$U2dJ:*7@f,b.q3?)^%#Z,rTca/~6tFKO_^-4lI_iQ)l(f8npOlp!6k2fm>X
qK#.KZ|?48C,j5&OO^fia]_N-9;=e<&]er,[f_I.^v&b}ai+tPAju0.2jSM@e
%QY3
BNPz4`ta=F!uen<)J
IwQ&A^nyCiRgM&-`-d*|wu_k1F4-n=^*E(?v.FBCp~I-9CQXMhjN$K7%rgTh"wd3L+8em,
XMxB&FZgsr`1@f<C/St6sQ+r4*zQ9QXKwN`u7t2HUAE)FPA1Z+LPs/Ycx:z2cXy2[.?AX&t/riOj;JrvzGlrg30/7+|lC4j$C2:S
s9G6Z;VX*`?U6W4E]m=eChjTN.Vgt`ha*A..uo7"m79v68,$a`RjRs>NC[<zbyA5172ILUL&O1ISlC9!>:pr[k%es<,&`U%_nIy|N,az1.3b0~u#mE7|Ms=E!-]flr"F1GrNEny0@^SF
Oj%No=+gKSSM&`>SJ0ov3:2,(;@"frQNm`Os^O[Nj=Y3vqC;djRepEMX6+)<;^v-)rRo)';case"hi":return'"s`5haP.7:$Xcj%M5uvxx,6.)Fz5moqY-g4=gdQf?+i3"=s4<$wk1xvb>x6`greFJuA94&I
oK7XkH+JfDtx}JSr+UEi*5!h[]z7>3bW8Kx3mb``Z,;4tk(,wG}8Wu1dUvx`thuqVh7U@urEX]#c|Z0GXsks(F}r*K9l)]9INh@#P1]^)Vj]Qw{u`y`I<lLF/j
xK3}B,fzjCGg;/xY?-_~R9[Cyh+"Wg2JFxI?i!3Ra/]|

G9SH:2u/#NUF]3TVlqC5v{jdP56SM]mJcSj0kY0t`$_W>hWUS@POg.Z@JvaV2vy8I_p|lM0SUKvlga-V#`
zoYDJrS]qc5L^M{!/SRE=&%SD#S_Dk[HaP%-QSK):]h[2H+)o1B#TleZjd&q/FZ2Y3S2q9q.[TFR{SJ^R6!&+OBZt$H@.^L&Y!b=TYzcM=KUn:?QFr/o{whK`!gh;jS"vXflLAe-,KDE:B}7Vy`,;/*VXki,d>SZ3w;jcO`
"uTUcZBp!FP27hJG|#r@AYE&9R/f`)%F[a~`}*qxG_pws1IEHdw+IIVlHACkd)A@,EE:(GcXLGd0YbN6kcKn0
~vQ$jW,-e<7Zw`6/%Q<2CYb0<dNOEDIHu[K+s1W(S*g<lMQ&2PBe&-PmFWZ_
OnE4xdkkRgBVG6G/dLvss/;RS

oW|sc=ZjYv)#!U=%P)}WD&RZM,h<mNR]~.Bg:rLoQn~WZE<VOU(n&YU5]
y>kR:*ovk&Pfie%W~2IE}Q(mJ+!qY9.*>;/m;Kh>?WJM>+-q$9JL%?F%//Mu@r14(."Z=f<NXuRl,xE-wLxc]$lo,`+#ZHg?<
#iR/8cK!.q)[y:Y!1"Jl04`x4cJaME<!CG
6Zq:Gu)U*]eHi3GW1f)G&`U{q!d
L7UNTtuc(%#|sH2u(`kL5uikqPhld!M~t&Te=g39s`)%xOU;Q"
NhheDHA/8%/G;iDiS%W,*7>
^@|8!ut?WiNi{>QN5]ab"x
9AidM&VM@y">ijM8ZRuC?YNo?*u}$p,|aKKd;
hVexPhjMOyJ:5r2ve7<GP^Dv#0dv&*P[Tb)*6~2HQiOZ&9@_
CJiFTSz,S9ewq3JpjpI1|Pb0nyy6%7G:9g}5l(Do0j)g0kUyh5rX&u:J

JE
VOS8/qrO,8F0W&ISN7/=srK$=hhp7qdwU?2{>h]stU`1x1;.b
.BcB)k$Lt5Fp*6!*>4JA`9i#4-b
%-N>@{h@h*_V&%OLue2H2TT19n799t+G2;x,i&9&8J5p_g
.4xm!a~),5?vPBYwlc5mbv*#=sRB"oGL`<kog-~tlhnCFp(mOmUsDpqg9$>R|jwkN2#Cf"-_~0w[,^ij<B5.z:DoJOrV6VDKq=.LeT^>!Nye7"9
ZE$&E!b(C:}nY@qidncKtm5gI0PqW9YQZ,$-_i?[c?mo|t:Cic_$ieWp+e)F|v!m{+z+3]T@1q=HFa9@hJSP.s`@P@%z"Qie5]BIy,*K76zGN&Y-r&gY9uvDy-vXa1a5PH%1h37sw"5.OfN<S$7PtKH2ufB=qAY@fWQMQ@W%gEjT}XQIVVJ?YG_]iPURS?
tBvfYavz;6M]Q
N0NKasyXJF()S.9YBlo/:o@B0|U_Ke`?nlF-n;XHA3tE5Q#,slin
6xE:8DqbT/%JWL*d7;_Oll.eg5SVM;"nglSH6C8ea4i;uX4A.>exu6[^:"VdF"HX4]9+?r;^<C*/LNj&}]
wS_HL)u=P[(J3XLMEL_B1hx"f)>5QpfWD-*;`zLm[>o3*VA_+u${`]mI#dJ7E!At:/`QW7!02rYRx^D[n$GG-,fHYR%|JZWpeQRq*q!VW*($xHXK>UN:z!g)QqQ6QUC/bKL!")eMs153>Jl?If6vndNQ@t,L#]Osr^(<`&(J%b?WjNB?00ib_np
V:]u,Y@SYuxc';case"bn":return'!s`5p[~Z;#BnU!yx`:v$,!r24Gy%K^%/j1/tD;OF*+m9tVH_QW+1@DW6)$2((
oZmZgJHba0HvNcB[.saqcv<s2?Sw><*e%rAI}L#kJ+Q4S^Da6AY[;Xz-xfDrnq~<_abB8y"7lMO+D0wXAjh^c@lcl65lFtKwus7s85:,irxgVixQ3[SFZB}ZG:YrQXY/oA#kty,rfhs4YXP<x(Q4Gu=m];T:JpMg4*xTdd/Wy7*[gGJ_b1xZ2C
ptL`G%n&C)SKDXOx_7:)_p]P=^)#mfd;rbL"[Fh-[`ii)t.%Z!CKIR/qh<Aubz_Q3hf9KK]T$n&54Xjp3,$*g+n92-xXDD!@><dF`.H%]25=lV3*@XIWoZ*sHgJxoEj~9-%ks}LHlN`Y8>#|]}A#Ma"Ud:tyd>1;7Wyon^q)8fTx%urkGJ,snj89HW)
_{_(?kVo
Ft:FG]r2"Uv7I5aw}R:]kJ.?,nL*S@nI5qQ
L
O$PH<7>Q.8xNCE$RhiX,Z6DU
]"#{roQaejeYA7,1bMXY:#Y1PG^
;x*(d9Xb/?xi#Ppy-X!lxO@WImYHj}x$G.-ep_8iCuL|Qvd2UagDKI:(d9v[OA&$L_"w&-i;xo3(;sYo+(W.Q^L<=Y8a4t2SDiFO98Y=:6.PU<PrCGDF^n7Ce&xDM&sM1wCpXaWIA.R#-E4(0&,buD!vD<=_*<KwItscsPn~0t0)=Il
*wTb
yoqE55sC]ZfI;`7MGI9dy(ZY_XW=k#;(n(>r}+C*>d`DfXj.."JBz:wA8&`K,l0^yGz?=j$N2Qz"HT`gX$FTjBN8v$eo@t,dpCFE:9:PkDny/AlZ-2R3M:ENlx
C&@7f4#r0"Hv;dqiV0O$x+b6GFyLHTL)VU5ejBrg1s``lAU=-(#Jl:UVYOf4M~dRQ[D>k>^w+dUnrK33WtXu=3CulUXm7.e[@GKJpqQmHjg,]}F1
X:w-<3:@z#1?|Oav]DnTZ@GmWgb.;)Ze$hAv<yUyvghuxp*5;5tJMpX[P]9#xX8%"^F:q.wd|&qswGZELllChw(@}T:g?3^-EMUOlN(P^*m]x9cEbgzV^Z:Mh$wd&
p.!:
Tq%%:!NOCJ"!0cB$3!7>8w4/WAae?N@38)1$*"bbH0H`ke;{/z%cT/1:)bKpG/D7,N.JG"=wmUWBtYCxG*/Ktn`OZE)-%U6Vc#c`?N<!Xfcw54NB4PTC:pkKW}6,biSd[lf+<XVmVQubh&CPU0VAfiG[/vMmTb;EFu0g5VJ^ccc#7hEt%St0;r&KC2P^C>YZS=qP^)]XA<;wIv2:<pA1(>amQIYQP3OUS7?#P}vEApcgMzAJDzZl3Yy{q(k
<u1m;/F0*S;4M?mjUNaRSRth#N6;R&H"!~GcPrPjT>G@q]U3(pcLmCxSvfArjU,UF?a*MKfdVfj~n5^YQR:#IkjPy]pn<A.$)mg/fIXpX;;>ja4,4}pmA)TKEM;+Uzvy6d,j!vrR(rl
J&pT2zYzJQCwd&_MF6Mr!mY6?BIf.Ib:ICMSxM
e.jlK@>T
[/a0gzj3!gKt/"oYY}laDE)8kr9.W0u/oZ"@pl31PiOx_M+uk-j@S.qqxPiww"8/7gJX=Zgx5qP<rGdn^&YA4=!vA2Q!r}T`5+_BAC%,2oX!l=-_qnRevvDX;S520odA49f_b))>dieFl*Mmb?l7pKE,KX3OQQXTP)eIA/W<yX51e3Mc[=4mS5sasilY)Z6v.jv8[h
&6
P5PVI=jC9pRkB9nY$8I~[dH,g0bX3j<>wqu/^zNdoVr[sAOeM_8m6j?qr>3"U4%Ml-Zfbn8~3c`E`QJpUO.{9^i}_oPY2Bv_9hHzS~i>aJ(l;|D#>VXt:bB1,3c/fpO/&io*YZ[*X+@^A7bbLUgWc77lSg<k4/#8;hDeR:Y#^!5^4j#zUp3[)HU8O?3pdLY}>y=-6iAicx4=&qn{ol[MfsBJ?V^Hp6xH[f7"Sp<HG]g|mY?I*]z"t:8Dc9CSVD(l[<Q>aiVe
"#zvs/g77q/EcF7"TvVXwrZ_6C}Sm_VWOt<y0BCG{5M=vYvT=k_;[`5g+)-!*F^!7Q9U*
X!Y9!SQ!1';case"ta":return',s`*/bOY!gN:Sv"P-"s9(*"W]
E>AeKG:UOhn#F!?1:$7HtuI_;I|HRUCIsT[weRT<txI.tQO+Q(2R~QKu?I6uQAz)cmUv~l3s4>"k2`[G^VY&y
gLvKDs;t:ygy?LRY#jov7kRC~ewH`M<!QUU+%Y%EEVyJ+@9Fgoz?.@rq+/cEX^;__6J"ACMIXJwm%[g?UkzjosOmaA9w"D#%$pN$`B0w{c"RLmwAZ.:F+X)tDP~F
t;K.#(-TyZ((]E@h0=-*<l+D_W,#$)!8w.]kq|%X5XvebQeuLh0Z7|^X8(]UHlPd=9/[gGsb]r@jg&@fLh%mkeU}ITMtsXR`J`0>&G=UUOVC=n"cX.1k,7#-M<e7HV>.uL7=[R`SJ$q:P/)tsY]N4u<S1TYS"k*`dFmJ?,ZhfrnPo2)YIT:B:L00cR$]+m%2H/KzPw:r*:`8cA"f&U`RX:OS^J*pm[)3
*aR;6@+"P_}vnF]D;>
@<"r=U?*(fe&%=QAF+/6q_/Q%"-I^dnY=*JCrlaJv#,P-X3Q@?4G_TQRI]e,sFAtS66.it3m;T$$(A"CK8P4ats|]>B(8z"8r1rz?e
8%F+{"XLX$ovX;:KnlkNm8m0(l#X4%PJnP`J$-!R*v(xUZ"n,cS?>cv--Bg;XT?T.Pn$1AO.z"ZSX_!(za9oye&_UvZ*HiPdA9H)Q0*(p)9fyvmxOsDR8g*-[WKo1o.2OA1Ce(o9F*V!TW:taVU&@RI3*7Rl|yMEhv5ZUBeFSd1@$>}9s$Xg61?K{<(,dU,mEUl?e#"h<wwUI!Hm0U$Z[/amG?QZqF5>V8{Lp$JAv^Ei<!.]xr/Bm_+/!Pz[:k#Jt9_,j,V@X<_&PnKs<,H++x<hcNv*.^.eZ?1Y7xO<y1Wn~
c3t8ww4vspZ`F6C2H^36!@$Ujk{3W:4rIg<_kw@iNyJ5ywOLr3w9r3|@/@qE]39iL%IS&*]<aY-1YZO^s9ToPf5pfCv7|Ut]hUm4(In0jXu>"H5&Ih}gm(zfA7$tDL#>mm,_Rlu`4/#QFfd*K1d2mZaDQQq]sd:%p<SiZBOTNFQigS0)edJZQ(w/TyfS!j6cGi"@ANi5EGX)IYaxp-NRR//OAl$Ml@cf^*bac4Abkl2Ja`;d"!rjk2<<>t*K@Rgn*l_Kt:y`$^3/DjzTZY?fc($;t0)EvfwtOYs:-HA;)BUC1:W3g(44Rf$RV
=<oF].UQ)BUT^YsvDRtI0a4f}n@i]#w]{L|)-I}[+ce9Xj<^bfP0}]BYb,+Q]a?J7!3.^fOJR!@X,Qdm(siX-Gs/bht.7WfP+U_5Y<WkAM]DfmqjSsrfuc,!"J4L(PsAkk]u&921KOUW.$.T.sXCg4:NkH3ef.NqSg{hi;N25=LtLwzCjz!46m/oXaw&;Hsrch~`ikcJ?$kv,5{MfuE^(vcd[Z*s7*//Y-OW]%l.N045bd:,#om+=EA`/#ltWZjE&qUo)';case"th":return'!s`&#bKY($et"Mi"%6f%[Op8WN02<!m-vW*;.kJ<u:t<RP|T=-l/xB]sO6?qbLSjy+suK-M9XLvc8u^u6MjE8<*>SO]YyR@6Q-KZ_)^%}(~8u?U=h?pg:e`p$dU?Y9yBRWj4^9x(~^tZU*e
2
|Dlfv<"XA<X!*5ux2rIxyA>!l;H#gFknfK]`N)QVmk:g&@Ziy@X%`WXN{>;ePO_CjUGbXl.,k"?T)fWB7_kt}5<8Ql$hTpfvv^n`o
Rx#ix:+a_>
B>=XxUYDk#j?AVGN$OY+6(-bY[&g$P9&8OtbfTE-bVCu5]O,_X"JTD"!63f7F7,DB9RkO1D;=|e1h#GU((k[=J]E$mL&5kAI7N*?!}(z@F.->/O&T>^*`VdEW
b6hG//I@0o
h<j;)/{fA*f*nK,P>=O]jll#[ivrN)i^jTO1j5w%x_U=|i~3~?SLpQ>..xT`fk~DV+cVE)qs5XA[:lm,vgf
#*N]BWiA_m#hfW~o#[;/?6,&:-0kij|h`%!fv$]jOnLCvT8_<bN0%^_dt]SewuHqtWD=Ql:HN_zWiHwvI&+40NuC4;FWosUF6?!4JQZ/i.?(@UL"]4M.wQJ.N+]slg57*FzE7TP/B^!yi@80ZP",lNdM>^]DH:&d,pfE{M`+PLpFD)n]$BP3m$ak_("Qs;[,WsHvwr8qYl),~94ph+s-a(4]/v92/h:&KwH8U21lRG+90R}`]3AA)"5R_([/ikviV
GXpJVxH:#B&rNU~ZEiDIKV2ei=_z(B:H`okUf*pa4w9"e&|]lP1m>=g`uI[b+8?lQ?fX9S
G=BJ3wDKUPu!`Jx=RsX0sO&#N1mDlYKvH$HOinh%b1W*i:<87=G?"$`*;6^>#
2]h:%6gt9b%>Q1s[5I+Pw6z"7Ni:Jvidd
z!.1%LTp@3a=ACCNt@N&<!;i/rY}tM[TFr_tFzHQsBv|ye7D&x=U_Gl(WUG_aIX3[]Kv%
c<?qx(Zw
|[=f#kVgW/jfk8~PK*(f+uK%XA7Gtk|JnErVvGc[[;uH@%,sXF^s;-ynT^x+yudr.uQ`ubehe9On
s^Z1Wmpo
o1|Tbrvi=RR70J?D"l^9"Dru%)H#8%k1]RNGf%#6$]Y6sf&6y4URSO5iZ1y.ur*r}6(*@2x1+
DkgqcMXQ_=q)KnL,"`)(A!L^XXOM,pUHC"fE)UZA
4*8[B^Ex^bfv=>0Adqm$nmNJi(*ds&bUYZ5GImL
%B6$ilL+rTJ<fI]MJ&m/:fF`"<*%hZdlp/AXA<,*Cmv[V>ii^=mD*r7%i[R;]f78j=^-5W[DEz(hGIB7P4X$6R->&n#uSwC`_j94i$B!;zA"h$si"
1OEWWIW]-")w';case"ka":return'*s`0:;~WAhokb)~Z|]h;^
+AsY|Jh>;>j"]4iH-)n?L1Mu2HBa@u>K+-fU-;-#=-Hx*IC_R.S@d`dS}N*NFxe4c`wb;llR,tNnRakatn<eQ:XL)Uvz!4aR}ksPHB6dZ*U<NOuF/rAYQC$8|HEs%m>`XH,q{j~a0M0r).$8CxM2i470"w^7F2%f]_IKqA0DW%^O?/]ZuF<[q,tDLNC43jAu-X-ETC$Tu/Y2^1JH~EnLXV.K7p"I!q.W}6[3c8Cyvu,FV1O,oIge5sK6$
}F*;==::*X6F7j"`rlZ!qJTVC!hQBI%k,]JG9!JjK>^pA$Y[Yd+B
x^ghMSyKkM[dLlO7K1&ybE_s/"-@;@ov`ytp6[P))vrpC^J/I$(zTXWz(-w@8}/J6lnHA&TR6QLW"
y.k2nR-gjk=%3;B6O.kz5fZ1UrpR3dCc+S>c^bhF3{h)TpN+keQ>g,7r4W6>g_o?t718sD
;&e/3lzVnXmd9ah`IeeJmnX="Pp]egEAPx
+a33]`2%7QtNvoiW%hs>Y293%xVm&b/8:`YMOCr{rin[Sl5`OT[eE,T$/C?@*h%ntI$<4wV7"*59U],n<|j]K="/&RQ+?E=6-HlW3d182Kk=4@A9&{o`"bM6T-XpYR
0WjEgMTv,@wx2)E#8Uz0%(jYYfaW"Y5403Qw!`_;j_m;&@-g#+}s,)h
b4S)WuM]wr?@AE`QKFqnj"N`m#X$gthqqEYExO&yJZ&$`iC_uoz$U=g,0TEbFG6#
x]uI)>vZ+|l0>V?{FSx8uOnE*$aYtb(T^hb7Y/LisN3vKcd)+$Z.u$,Jvh4tsUcJV8ul+m/&,?uPxl8=dgmNh&WS;)@0?|=*yF9{X},L;1X0sr9wSTM]Y!f;(>MZC)_OD.DD<D=j;}$V&}O0/"&]/Z#a&v&OQ@^}?.>Oe#"D&mQx5vp=nol?YGMQg!%7r=5-]KLP%8XG.efR.US]X]Yzubha*[vdPkt>KLtoWfa]-U+5=*#_@LZv#T0U]uMNV^*}W6@%-kOnNaYVW6tBPaKrY)_ADtbQI,OtIM_H#7>f
],yBE8O6CWHOX+R6/Vo:.&qC9Bq5NesqN`QS:fuLHRLXaA4/rQ+rcXDD;gz`>W}?t))>kuo*e_)!;]@g2G}7RX|-{bMnG9k-H[wJ0RIcV:g1zIz`>n>Z{vw;uev/8int736e8vJ2+ZrsG4MH!Fyc"/$V[j.Yxp/lEjNaVP=n`={>y=?2jd#08Gtv}=,+f!!F*1>sVC{_P=5=6740L:(](*#RAj7dk=mYlTD(h
E&I?3=bL*nsfR3zcAVoYET)F022S0@y;Q04+?rh7.11A=iRg[/G3rAQlU+!H)MfMQf;1<8WGHg,!y"Eh664vi#R$k]}e*W)CZJ.,L*D?RwSAlAvU5@Cmfl7"d;`uj<fN&Dmfd+XU`ZD49K2H[62as&AwIIkOo%A53TKOinH#8i~@PR+Zpe=Q>VzXnObI9I@85!mm?Gi]e*$Q/uf/Oo`0N?G_#9UW%0yv!Jm#@2uK.6PVJZh
B%w4G=7-K9Ry4o@$+OA*}?9R@G&N@aX#Sw2OTp9K{cqYs_CuX8VqD34xzdvlS(O^os0q`I.Emjis:my3P_KZ[ieD92_7SG:Y

2h-m+,1q{ES4=Edp]P1Dm,[eYSz4cYlpYuynX=Yn.)BKoo<ex.LHf^2Bq#YiZ+*I>+~;5K=RF`?_BsTDv%"`@)-A{XTj6]4[LBVjmYBo$jYQa)f3eZcg=tIM]0uWr.[TN/tVZN8HhU]_hQhD%_G8sTGseV;:K2}&pu^R0>"VEG-3M9N]ZBS>RenL=Dj>zuHmck_9jCKI!&yI2/V%Q"e*`ioyoEk';case"ja":return'*X/*_:wZ[&)^+yG"3vv^i+xSLgJ[4*m)}(pn0<oY,>QC:Q2*^-GNh171Yd3E1.(OH<E5$GDck(YEYUur?%,u3N&65BMv*c"2&C@upj$2pO{,dX@s/8ja}UEg?.n`4m2]WUIg9RFbMgH*s%Y;4TBp]ci8l&gPHn0U,-l-`Gz%}9F.u%.g#B
!dJ8tN(=p"-t3?({FXHxE$.rQeXnU.UzR%g/.`v1oj<*6J%8s0RQu#MKPe6wMk?[]/loI+7,cYy0^o)I4y#?>S>{s-NYw<S|*jg+`#RAIo+o^HsQJ}$!K!kpPD/5Vw5*4HgLazc0<1M@a!bkXa"zfp//30XwMRFYKp/U`K5T."IQv1xeM-4]%>`9mUQ[L0X=$Sa&?^[%y%A/x$k<PkF.x^H)c@7]K[fNa|wi<B7G0i/V*.xB]aWJ<EWLTa1s[C2-*<u6CS_6E%CDHB`w7QmZvdi^UD*|B-O1ipfN@{o|65Dg
UpG%BMCv{.;80i^%85X<bMCb{$CBa8,:09X%~(G$lQZ.@UWd{5iJEd|yGTu>K-P$z<:g*F<xm%YliF/
4<1Nj05Gu]V1$R:s)u-/V3JGTKA$Y$:^CTs9xFX,UGwl)v<#/(;Lbnx>iv(UN

oqnLE1][fF,u%8;PC+.@@/n,afR,(H?/i)v24Qhz&+$d`|j,jCB;#`Cjc$SM_[S3xA1(YiVo-o.%3At,2*bALaLGWaQ?2s!:3ue%/}6_P&9r;lxZDD>&-[Xi@%_))/H;.DGy0sc)P|F""(PUo)s1;X60PvY!9VSp0m,D2U95"kHf+pkB4~0n&9r:CRyt#:z"u[1`?dgu4IA(rwK?MUYf!$)80|xe1GV`LrT17C<qFdf5m^8YD.=)M<L.VIMj$;L_L}DH7rp49MIKs-l11HhzTgexTo6oaoYH"U$~&"@3
V^,6KV#s`G`Xfd(n;YWZmJs&[!gKquE"=EWvcs9,t>13bL%hdlE-Q7t`)"As@vyW7[u8
Iekdw,l,7GQIxAiyJ[yvq{$j`t%?-uCEd}CqU;mGCN6wwhyB._4eT+!`"^vu*:ySu*##QvD$Q%)bwg!8Gx6=O);Hd
"=x$Y:9GI+H2(=<WOBKiXd&%;[3*uQV!E7Gg@G[,.7Q;=Y#}*mL|hAS+B"n6#BXp-M4(+oy).P)_.k;YPr&YKY(D
,l!U9G.Q^c_C=6=wC42O[,RlyBpJE@B8QaA2v_6>@^gxLU$4x$I<3NRWz-Os
qoNk?
])Z{9s-/8TCM
`ubjSxe$;?+2U
nO<"3?4n8doSHX2;rQW=ygkX13f8Z+&.uUsjrCjvAC335a47JJx(|JzbmyMH.3]Y%!CKa8>hrR49A7bao/gE*IU-bm"D@%gJ".%#9PsK9j=!Q2%D"sJr
,IkOuqQOmSA}<{8MMbcc

7Jr1ZFkc_oi$/pE7e4f)9u[@7:i<"z68riR
<l"6
JOh_m#[,,
AFxsb
n[y2T/SX4Vp:2T)l[_y2NENfmTWov/]J(EV/H=v>5J?vNTaC,a<7gpnKrD
m!W26*7lxMcoF"dgfc1a:e5.>dhNO}&%hh)<ha$thlEZZ[1GiwkDDZOMa7/w.6<SBRpskdqi5SkhtJd8I<C|>-fTJ4$1gwmzC|jpD:0{"&6H`m8.c7O9[mO=IG=S<?hU3pWKbV1X,jJPWvQ1!
8/MZqou4//4tmfv!l"eEdn*pUNv5oSBqp0J~iL(Q5IN?-m`38E
9*I${&xd3w*$65!i,Tk+;*&]@nOavY2[^&)1u-6/g9>33$P>&5K>W5SZk
M"&L]8TY|#>fia>z!>1D]O2-"xKiUqZX;H=$&`s%`[.%we]6#q^SNd~*,!WljG`202N>)6-uog@]bxU0J%xpR6|!O-hkycmFP7FvH1.J%jz1]G#9=`
I,T#Udw$Y?6/7Uh~P1EEcx_-9{+L3kaK?C^K/{<sDP$h7g<~8J1p4M7h7[y>2I7235rOX)`+nz@~e^J3^hYtw]slB>j8@i#ZwtHivdxQw(B$Ib7
!"BvrU?Sk-2o.3P.3;L2XB/p$BP:r&F-GCJ`=&N=$+*Q%r!{H(WldG#I,jt;YPc}*:';case"zh":return'"UEwn@M.W&i^+sxP"XJL/tW<]Xq?8d*[Q-K_M/z8S/9YT-O(bAO4#NWTZ3[l9<.E8R2S?I#x.e,:6V/aa.;nnUns,X7U/7}jl@c_s[b_*]^h2-`CrjIXEBu/mL?
>r<S
1`#T<h65yd(ZG-KO2wHyU>^DDSwYSJwtf1v?y.gE&F1PYem>?ZNRjW>#GR(3_!+2J?"Pb|BcH;9m_,ZErk5IPa:L+"Cd]fDC2(o#vc`r7.%4,Zy
B$g,2UkH=%7?a<kO?SR&]
q#b
JVlcNtao.e]Aj<iRD5AxfR]1AwY]ds2D@GgLkz!gF,WwFqHebaI~onf,ptKYgF_L`a]MVogy+:v&@u,R*DX*T>nzK67CR,9,Zzp-6p4@8>0Sk@k.?
:T0B-%G($XGP0_
98T5<xiW"rPUM^
?,):R@Sob@-d*/FlGA<}%ISj`v_XjI?g,@c0YNEFqae_JQB(tmuGh>ABmwsF?.)}QaIuXi.3:bGw+oRF#;9R5]QpqA`zUuw@RY@1ytgj0LNN:id
FI.,y[EWP6f",<:#A/w>,h9U`fKrCn<X*R1CpA$Nru=O.ny~Sl?J+Yk9H)EzazUKuUqO^m&nPe_3xes$@,v="9k%Z:G1JhLNgwE;rKr
M_^$W&RBW,oCOuW0WG!}J=2f/KP+E8bo"$`APqv(7"BA$H$gDD6>C;B2Z}iAKz16TZ9*d<Pe0o!;gJ[U`_$6Zn5^F=&PWu[Nuu
6"0fdA~bi=&"Y$KMoWps-SOl&k*1Y61]H+3vYZ$Oa
/+Ph6M%4SSD`}yuL[/ivYxCN%Ub4$mlNaysIs]@NzD.Hay5sN9phf=$Axq[G~c/rh<qrgkz>Li)fD>32tWXAGX8mLTPaSP3Eyvvvx?_l%&<Ugx^"8@)&^RKq<ed"Pk^H*Cs17u~)v<_B%1bSG&(qn>EvLK?w{Q"[y5|Oa=]PBmSFS>lV14h_MjqvVQJbiIMn
4fl45umSgI>2%To<tzp"L$LrV!^Y2N@yO<`#U[J
3<F~8@*=7K3oitxZPQ#^Y92K+)IgQ#j@gFV}jl&rit62(wD)D$Ki7{ZMe9u$ujg}jL3D5$"dZpqV/&0vyTM|.uX~9iMBuzUf9G/yQd]l4viJIb;>s|iR&C-]IkO6Xp%V-$NMyx5RV-emM.57aCM*@R`@n)y,52`qX=[}gvLUJ*EPUv^?`W:)XIi32YQdR97H^u[,jxD)VyH;iFl+gJV9h]s3YE3;I]CheZgcvya5B@1>N*6rZ}85Xoi8D*(tN:"P6oH~KqE-_N0uws(VV,wW.T1*J+FeVL_c715f*c:[6tjw8F<04m<7D%mXe9mWx"G#3%4D3&V>O2S/@?E2AeEl5r+29a0WXSVWX+c*J(!SH"i1m/a!bSBdVg2kA_dS6G/*Df7ln]acodsf<}d@N}Aww#t86ebs;6K$`P7A<MJV2~,fmPN5k+Y(>1=E"KaxClHn3
IBIEi:2{&J/J5xT>A#9EUg4;8-3B&FE-F{rM$AH3atLhGhCI"b"%3hd3kosD0@#0S(^{/KXh;9B.ICR_7G3qKidW@GS(_=YVxwjXau!?thV$G;S2YwJhCTKXts&#f-KH+^Z@E3k_C!=5f0?!g
mRe1!q0BJper?Gh
e5mU]_oh[OOn`C@msFk$3.9x`+>Npi.II4Ps,PGiy?`.ei*Df_m8tx`G^WLF<"=w^YO)85:dNd)db$_F[62J%`k^7t2pD&$/Z^)j&a[5Z{PC&zhifx^)_gn
r6E3.(q
<gg,';case"zh-tw":return'"UEr7lM.W&ih~GSP+EXyXL;xWpfnuLp/sh9"J/uN:F63>%+*LZ6QU2o/.MLCYOxjaE-[xgt2<+vU>.-d9u7F:x70f.Oj[6DMG9x;&k<MoW8#7XVe=nz6V7:+LumTV?v$3)j^/8h14Z,A~Xg0.kYcd?1+gBJL!qfWO="x^H.1nQg41AJEUQKQ9]kcW^R_"3~cwxSB.AyS;rQQ-@5jc+(t{J:63c-?11h*h@7(}s)c"4dn}JswL^C$,BIQfV0U]:g4$LT[Hv2*=tWUZH9q>uF:wmIR-,QV1CLS/)5m[I/:H?aMybPMKW_*!=B7M$Kg^36<l_A`@pM9~s:d9QpbDptf-_4vs4ZY)"<6j3:SHrI,hfWrf+R8>7%iDE!m9?~^zXm6`OW2NZ7G-
KSmIteY<:S$"@G{h$cn-VQQZ7IAP:)Ls*<g)>.]tkAGJ#[P>w]9G+xWc~Z7.pkP$A%;EO07@#7NdU_G/KAdmfhm&$rfVQ)60"44b:9#[XTo;TwBTZ"#@/hTJ-B`tI3sZtOm-wb6AC+]W2sVNUW&-cVu8gO$2^@:$$GgZ+2kI.15Qgji92fbfpprZ2aQt-0EEeHhaf5Ns|abx,?Bpg3dH9b.
{M:,?@WveXJM&!y4FR*vL@4*;xc25R[l|4$Kwa5V"51<fsuc3?z^CCYQ]5F9!-Ij/>BAEaQh]cdBxM{5#Qmx$Z-uny#"6H"gxgE1U$-H$K~RKqT-D];shBQ;7]:`:FBcQ%]Yj1hx6Q?%eg7be_:v3s)uwx2Y1q&EXU<FerG4LrnT*A1_i[&:0UQ9j)Y)4jxz!)S7diOwa(;7Xn~uctZ0!kt=RiM%>^8-^L2^N
?6^8$]F
0uKGtv*Gj95(CTT@/lkl~Zj^.t=46/~EI.P%Hr|KRg,?TE!2K5p#pRcl(pXf=2PVsXWM+v4tE*}?7;U^moN?(mk+}^.;rFAe}1.`+H^L6G)`Un@r_L
*EK?Q7c:]o[HSn?Xg)s`_Qa#oAfcr[uMl>7AGQce#y$}L(q%Grv0puU{_i$SBH`u(9*b)NHUtLKM]V##lBHL%#Aai}T"%tR&pB=m>#Dr]b"`Qgv:Od#D!<,OWRG,cuk0F}m;.[a!Pdm+gYER>0+HQA[`v4jxk%M]KOHOP_Hn%9<DskG[lpr|gxMAA;ZTqz[;V9eE&.5DiJ_:iR9;5u1acpK4c>*]&tFy37G&(=p;uI#bQ=$13y8D9FDkfu[X@uf0B-FkBewPb1v]Ga]4H=A%yxXQFWFP+k0rX}QKfKhh@+j^7j5gfY&>+;^8T|:N#Q-$C~-(hzN2
-c-/7X"5d)AJ]c3]N!2KKA$90m.A1dGexQ>12D@%HB4ytAhNlm*..6ueoC=9JEA(LG8!nhgS>F_,+qHNk<QGzs5F/FHm>w5G>i4MO,m&z]Uu$fG00d(6~c0t@<?J$Uo(SvZ=]/p8S8Wp^QmrivMT8![_{wp`<4%4iw^"YOTVBVEhW9BEQfo!~9S>{f@(lU]QypW[]Pq;~LuO:kX#~.6Y/ORvJ">f
Sbc}4Dv*!`Q`B;*ELF#"F:WM/@oN]Kf`:wuB+Ur^Tc(B$@hx/2Q9oATa6HM9X8Mp^KD=@I-X/6L2$%*%^}P3c}tuU3tLkj&f7s?rHsrMY?nT6I-8j)qY(8i8i~ILY!l?Hm_+^w-9>`/oEm
XSF(nE+<OfMSFZKHR=5pU,GE-TD:<a7%84<rKr&:6e"NRi;+H2nEfau,-[[`i6=?W^qJ;1mAY8+5^v,>ksrF-hBXF"Uj2?d.&+$O)[z7aBEP}&jP
5ALC.-wB';case"ko":return'-UExQ;zZK$c^+u]+/UERg:Dn<w4T7T_tx7m!^2^siU39}87.u[~#%<YQ*Cf9Gc_&HXSxG<Nn,1_WHWGGV[Rv;7<
N<V@P,NUlO/Ln,E?}3pk!5Xo#(knp#dB75dsqugsNv+1q`_cXlXsu3-asIbyQlO"q]J)8[&Y30=!~*Hh%e14LL@K$(}^Er,3>Wn,V%}a5F|65/9F8&%UGt}a,pxkHYA>UWvtX;vk!xl/x%6&bxZ?gV4Tt]49s[3/7#;F13d-c_+&4C|T*_@MJMq1,<CiHT6P%c>*]^@Tym$,a2:XQ"PN#&s#.e0ocJ`L2EQVAhnU8Kq=*H>LQTR,(K.t%LAgdDgCG^p(iXsKOLsEr*AE1v+()Q!c(!*a}/x]<PJoFa]V<6J_xUuUaT7N9E?Fv%SxYoA?vOs/phH#.w3?$9/hja0
8>@4+`*)P!P>vg]6|"xS,EK5m]-)0d0>$EuvYRgbJ(4$=q<r89@@T$kr@o|.aq|WQ"3pHbnQlc!i6[+-{C^+vaPh-V:=yWu7*"J`x*:B!SW+_SqM&-a^H@1qc0:p=iIlK!aL[*
^ABC24dQ/~2UHT((7g@<Npn6XuvuWmWH!:p382+AhotY3J9a%@=Ud80N@w9$[*J@[6f
WU,Md9cvsX@xgT9-l5H|r
GTUEX8A)e@hkH^/}1BjJ#A]v-3;o#T!OM<lv)1yRklHZc!"~$fG
GU3(xW5y0g<qJ`8ogZQWvbslIvHeBDhUH7`v6~Q/*r>Qe_e3UC;DxL<i=1K>NM+<rcJ1;Rg0oQE
!^0Fu(Vfd/&wD1P~l1q{q,C``*lMx&4Imt_kv6a&wK!aK55{LRQWyVaCD`(G/_E
dH$Z;O_|!8URnMLx^iA,;#c$CEaj=e=?#3$e"l23[*c.jC"M]5KOVRC%KMMsr2aUQ*G4x#V{X;Q#E[
_M{+
EhdW-.Q*uyw?qEoA
nFSx9tZvu/5a1:ic;ABPJ;2Mig"b>Zp
xx
]+D#,FF4@d0v`p=R^Vit?dhHEE_[hI]a5
5ex}.7p/Wv-;ENM=*@E.AOugX9vgHHrKU*U*8RUDm[C5Ei5.`@sR@L<Cl)rBJl"R<E0M4|tAk
(tt5in>}HJ"(i^[+@y6VO&Dqh%5J3Y]vS]Cats6$f^)4CAh"!VL;YaD6MnSC-ggV(8&`iNi?pofr&nHZe*5#DYPT0<aS?(RLpy5~`6x40[Vcxj<h#L=~k6%`JP)/fa"<Qz^;#70
x
k)>~mIAC;tnUQN^<vCbzt]xIUlA,p%FYl5n1r:`p7lxiAJ/U`".Xnt?Pl%b
Z+>PH>Nkqawri?K^SYxp_$]F29?yrLduntTXI5"LZ:VHw:8%"mx^N@N&:[ux&4GZI"O^#b[[VIWra$;!cW&nx{4~*LOsdT>$cDsdS+bym]5-YZO#Kn1xGU3)TuPogB^yb*4d4.^2AIk&O>VgI|oO5AZQ.N;/+qn5V/@73oD&va%PI9lHe-j[%DYf:/#acz"+Ax.u.
2+6QZJu;`k4>>,0+:GN5/^2"94TS4m4bTc`::mx<9<J89[QByf"7)*m_o2jUP3-zDR:mh/WFakkFJcwNM_S>//^[@XGK:z&.rr3~Psn_Yepdp(C<s9phiAUA"L229gKo$Vz%8!?k6$$d6<oGf8%$SJKhDX3R6YBg0
liV`9Z:66+1$I-!!e3poG&Hog.+MV}9=MI1GM4i#/y^,X3yl`y
vY`04gV+=vO/f^c_2+km?[=iAn)XDGTvcm0IhX,tf,C^<<C=r`z94(w_sqh6WtE1j6d<k-X)*ftHuH@>DWql2m?F/9iBMDJG)_PtSlnXELzBT=Pip6*5~;#Y(Oj@Rj|3uUIVCc5/2[>C)xNfF2Yr(;h+c+gcYr9ZmJuAc@VLP92.fW[#Ce#,2_199$(mZExqP50Sk+Rjcl%jhU,>^^$Z-un-xp=1Sn`b~6;2EO60D]JUIe9EvE=TbWIwPyGN&';}return"";}$Bi=LANG.crc32(get_compressed(LANG));$Ai=$_SESSION["translations"];if(!is_string($Ai)||$_SESSION["translations_version"]!=$Bi){$Ai=decompress_string(get_compressed(LANG),(LANG!="en"?decompress_string(get_compressed("en")):""));$_SESSION["translations"]=$Ai;$_SESSION["translations_version"]=$Bi;}Lang::$translations=array();foreach(explode("\n",$Ai)as$W)Lang::$translations[]=(strpos($W,"\t")?explode("\t",$W):$W);abstract
class
SqlDb{static$instance;static$untrusted=false;var$extension;var$flavor='';var$server_info;var$affected_rows=0;var$info='';var$errno=0;var$error='';protected$multi;abstract
function
attach(array$L,$U,$C);abstract
function
quote($P);abstract
function
select_db($Hb);abstract
function
query($D,$Mi=false);function
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
dsn($gc,$U,$C,array$A=array()){$A[\PDO::ATTR_ERRMODE]=\PDO::ERRMODE_SILENT;$A[\PDO::ATTR_STATEMENT_CLASS]=array('Adminer\PdoResult');try{$this->pdo=new
\PDO($gc,$U,$C,$A);}catch(\Exception$xc){return$xc->getMessage();}$this->server_info=@$this->pdo->getAttribute(\PDO::ATTR_SERVER_VERSION);return'';}function
quote($P){return$this->pdo->quote($P);}function
query($D,$Mi=false){$E=$this->pdo->query($D);$this->error="";if(!$E)return$this->store_error(false);$this->store_result($E);return$E;}private
function
store_error($F){if(!$F){list(,$this->errno,$this->error)=$this->pdo->errorInfo();if(!$this->error)$this->error=lang(25);}return$F;}function
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
fetch_array($mf){$F=$this->fetch($mf);return($F?array_map(array($this,'normalize'),$F):$F);}private
function
normalize($W){if(is_bool($W))return(JUSH=='pgsql'?($W?"t":"f"):+$W);return(is_resource($W)?stream_get_contents($W):$W);}function
fetch_field(){return(object)$this->getColumnMeta($this->_offset++);}function
seek($Df){for($p=0;$p<$Df;$p++)$this->fetch();}}}function
add_driver($q,$z){SqlDriver::$drivers[$q]=$z;}function
get_driver($q){return
SqlDriver::$drivers[$q];}abstract
class
SqlDriver{static$instance;static$drivers=array();static$extensions=array();static$jush;static$passwords=true;static$serverSchemes=array();static$serverSocket=false;static$serverPath=false;static$serverFile=false;protected$conn;protected$types=array();var$delimiter=";";var$insertFunctions=array();var$editFunctions=array();var$unsigned=array();var$fulltextOperator="AGAINST";var$functions=array();var$grouping=array();var$onActions="RESTRICT|NO ACTION|CASCADE|SET NULL|SET DEFAULT";var$partitionBy=array();var$inout="IN|OUT|INOUT";var$enumLength="'(?:''|[^'\\\\]|\\\\.)*'";var$generated=array();var$primary="";var$query="";static
function
jushModule(){return"";}static
function
jushAutocomplete(array$S,$Th){$hi=array();foreach($S
as$Q=>$O){if(!$O["dependent"])$hi[$Q]=array();}foreach(driver()->allFields()as$Q=>$l){foreach($l
as$k)$hi[$Q][]=$k["field"];}return"jush.autocompleteSql('".idf_escape("")."', ".json_encode($hi).", ".json_encode($Th).")";}static
function
connect($L,$U,$C){if(static::$serverFile)$gg=server_parts(array("path"=>$L));else{$gg=parse_server($L);if(!$gg||($gg["scheme"]&&!in_array($gg["scheme"],static::$serverSchemes))||($gg["socket"]&&!static::$serverSocket)||($gg["path"]&&!static::$serverPath)||(substr($gg["host"],0,1)=="/"&&!static::$serverSocket))return
lang(26);if($gg["port"]!=""&&($gg["port"]<1024||$gg["port"]>65535))return
lang(27);}$f=new
Db;return($f->attach($gg,$U,$C)?:$f);}function
__construct(Db$f){$this->conn=$f;}function
types(){return
call_user_func_array('array_merge',array_values($this->types));}function
structuredTypes(){return
array_map('array_keys',$this->types);}function
enumLength(array$k){}function
unconvertFunction(array$k){}function
select($Q,array$J,array$Z,array$o,array$Mf=array(),$w=1,$B=0,$Bg=false){$ke=(count($o)<count($J));$D=adminer()->selectQueryBuild($J,$Z,$o,$Mf,$w,$B);if(!$D)$D="SELECT".limit(($_GET["page"]!="last"&&$w&&$o&&$ke&&JUSH=="sql"?"SQL_CALC_FOUND_ROWS ":"").implode(", ",$J)."\nFROM ".table($Q),($Z?"\nWHERE ".implode(" AND ",$Z):"").($o&&$ke?"\nGROUP BY ".implode(", ",$o):"").($Mf?"\nORDER BY ".implode(", ",$Mf):""),$w,($B?$w*$B:0),"\n");$this->query=$D;$Sh=microtime(true);$F=$this->conn->query($D,(!$w&&!$Bg?1:0));if($Bg)echo
adminer()->selectQuery($D,$Sh,!$F);return$F;}function
delete($Q,$Ig,$w=0){$D="FROM ".table($Q);return
queries("DELETE".($w?limit1($Q,$D,$Ig):" $D$Ig"));}function
update($Q,array$M,$Ig,$w=0,$K="\n"){$Y=array();foreach($M
as$u=>$W)$Y[]="$u = $W";$D=table($Q)." SET$K".implode(",$K",$Y);return
queries("UPDATE".($w?limit1($Q,$D,$Ig,$K):" $D$Ig"));}function
insert($Q,array$M){return
queries("INSERT INTO ".table($Q).($M?" (".implode(", ",array_keys($M)).")\nVALUES (".implode(", ",$M).")":" DEFAULT VALUES").$this->insertReturning($Q));}function
insertReturning($Q){return"";}function
insertUpdate($Q,array$H,array$_g){foreach($H
as$M){$Z=array();foreach($M
as$u=>$W){if(isset($_g[idf_unescape($u)]))$Z[]="$u = $W";}if(!($Z&&$this->update($Q,$M," WHERE ".implode(" AND ",$Z))&&$this->conn->affected_rows)&&!$this->insert($Q,$M))return
false;}return
true;}function
begin(){remember_query("BEGIN");return$this->conn->begin();}function
commit(){remember_query("COMMIT");return$this->conn->commit();}function
rollback(){remember_query("ROLLBACK");return$this->conn->rollback();}function
slowQuery($D,$pi){}function
operators($ci){return
array();}function
convertSearch($r,array$W,array$k){return$r;}function
value($W,array$k){return(method_exists($this->conn,'value')?$this->conn->value($W,$k):$W);}function
quoteBinary($eh){return
q($eh);}function
typeName(\stdClass$k){return(isset($k->native_type)?$k->native_type:"");}function
warnings(){}function
tableHelp($z,$ne=false){}function
inheritsFrom($Q){return
array();}function
inheritedTables($Q){return
array();}function
partitionsInfo($Q){return
array();}function
hasCStyleEscapes(){return
false;}function
lineComment(){return"--";}function
engines(){return
array();}function
supportsIndex(array$R){return!is_view($R);}function
supportsAlterIndex(array$R){return
true;}function
supportsAlterTable(array$ci){return
true;}function
indexAlgorithms(array$ci){return
array();}function
indexOpclasses(){return
array();}function
shadowTables($Q){return
array();}function
fulltextSql($z,array$s,$D,$Ka){return"MATCH (".implode(", ",array_map('Adminer\idf_escape',$s["columns"])).") AGAINST (".q($D).($Ka?" IN BOOLEAN MODE":"").")";}function
checkConstraints($Q){return
get_key_vals("SELECT c.CONSTRAINT_NAME, CHECK_CLAUSE
FROM INFORMATION_SCHEMA.CHECK_CONSTRAINTS c
JOIN INFORMATION_SCHEMA.TABLE_CONSTRAINTS t
	ON c.CONSTRAINT_SCHEMA = t.CONSTRAINT_SCHEMA AND c.CONSTRAINT_NAME = t.CONSTRAINT_NAME".($this->conn->flavor=='maria'?" AND c.TABLE_NAME = ".q($Q):"")."
WHERE c.CONSTRAINT_SCHEMA = ".q($_GET["ns"]!=""?$_GET["ns"]:DB)."
AND t.TABLE_NAME = ".q($Q).(JUSH=="pgsql"?"
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
_error($rc,$j){if(ini_bool("html_errors"))$j=html_entity_decode(strip_tags($j));$j=preg_replace('~^[^:]*: ~','',$j);$this->error=$j;}function
attach(array$L,$U,$C){$h=adminer()->database();set_error_handler(array($this,'_error'));$rg=$L["port"];$Hd=($L["host"]?:$L["socket"]);$this->string="host='$Hd'".($rg?" port=$rg":"")." user='".addcslashes($U,"'\\")."' password='".addcslashes($C,"'\\")."'";$N=adminer()->connectSsl();if(isset($N["mode"]))$this->string
.=" sslmode=$N[mode]";$this->link=@pg_connect("$this->string dbname='".($h!=""?addcslashes($h,"'\\"):"postgres")."'",PGSQL_CONNECT_FORCE_NEW);if(!$this->link&&$h!=""){$this->database=false;$this->link=@pg_connect("$this->string dbname='postgres'",PGSQL_CONNECT_FORCE_NEW);}restore_error_handler();if($this->link)pg_set_client_encoding($this->link,"UTF8");return($this->link?'':$this->error);}function
quote($P){return(function_exists('pg_escape_literal')?pg_escape_literal($this->link,$P):"'".pg_escape_string($this->link,$P)."'");}function
value($W,array$k){return($k["type"]=="bytea"&&$W!==null?pg_unescape_bytea($W):$W);}function
select_db($Hb){if($Hb==adminer()->database())return$this->database;$F=@pg_connect("$this->string dbname='".addcslashes($Hb,"'\\")."'",PGSQL_CONNECT_FORCE_NEW);if($F)$this->link=$F;return$F;}function
close(){$this->link=@pg_connect("$this->string dbname='postgres'");}function
query($D,$Mi=false){if(self::$untrusted)$E=(@pg_query($this->link,"BEGIN READ ONLY")?@pg_query_params($this->link,$D,array()):false);else$E=@pg_query($this->link,$D);$this->error="";if(!$E){$this->error=pg_last_error($this->link);$F=false;}elseif(!pg_num_fields($E)){$this->affected_rows=pg_affected_rows($E);$F=true;}else$F=new
Result($E);if(self::$untrusted)@pg_query($this->link,"COMMIT");if($this->timeout){$this->timeout=0;$this->query("RESET statement_timeout");}return$F;}function
warnings(){if(PHP_VERSION_ID>=70100){$F=implode("\n",pg_last_notice($this->link,PGSQL_NOTICE_ALL));pg_last_notice($this->link,PGSQL_NOTICE_CLEAR);}else$F=pg_last_notice($this->link);return
nl_br(h($F));}function
inTransaction(){$O=pg_transaction_status($this->link);return$O==PGSQL_TRANSACTION_INTRANS||$O==PGSQL_TRANSACTION_INERROR;}function
copyFrom($Q,array$H){$this->error='';set_error_handler(function($rc,$j){$this->error=(ini_bool('html_errors')?html_entity_decode($j):$j);return
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
PdoDb{var$extension="PDO_PgSQL";var$timeout=0;function
attach(array$L,$U,$C){$h=adminer()->database();$rg=$L["port"];$Hd=($L["host"]?:$L["socket"]);$gc="pgsql:host='$Hd'".($rg?" port=$rg":"")." client_encoding=utf8 dbname='".($h!=""?addcslashes($h,"'\\"):"postgres")."'";$N=adminer()->connectSsl();if(isset($N["mode"]))$gc
.=" sslmode=$N[mode]";return$this->dsn($gc,$U,$C);}function
select_db($Hb){return(adminer()->database()==$Hb);}function
query($D,$Mi=false){$F=(self::$untrusted?$this->readOnlyQuery($D):parent::query($D,$Mi));if($this->timeout){$this->timeout=0;parent::query("RESET statement_timeout");}return$F;}private
function
readOnlyQuery($D){$this->error="";if(!$this->pdo->query("BEGIN READ ONLY")){list(,$this->errno,$this->error)=$this->pdo->errorInfo();return
false;}$E=$this->pdo->prepare($D);$F=false;if($E&&$E->execute()){$this->store_result($E);$F=$E;}else{list(,$this->errno,$this->error)=($E?$E->errorInfo():$this->pdo->errorInfo());if(!$this->error)$this->error=lang(25);}$this->pdo->query("COMMIT");return$F;}function
warnings(){}function
copyFrom($Q,array$H){$F=$this->pdo->pgsqlCopyFromArray($Q,$H);$this->error=idx($this->pdo->errorInfo(),2)?:'';return$F;}function
close(){}}}if(class_exists('Adminer\PgsqlDb')){class
Db
extends
PgsqlDb{function
multi_query($D){if(preg_match('~\bCOPY\s+(.+?)\s+FROM\s+stdin;\n?(.*)\n\\\\\.$~is',str_replace("\r\n","\n",$D),$y)){$H=explode("\n",$y[2]);$this->multi=false;$this->affected_rows=count($H);return$this->copyFrom($y[1],$H);}return
parent::multi_query($D);}}}class
Driver
extends
SqlDriver{static$extensions=array("PgSQL","PDO_PgSQL");static$jush="pgsql";static$serverSocket=true;var$functions=array("char_length","lower","round","to_hex","to_timestamp","upper");var$grouping=array("avg","count","count distinct","max","min","sum");var$nsOid="(SELECT oid FROM pg_namespace WHERE nspname = current_schema())";private$userTypes=array();function
operators($ci){return
array("=","<",">","<=",">=","!=","~","~*","!~","LIKE","LIKE %%","ILIKE","ILIKE %%","IN","IS NULL","NOT LIKE","NOT ILIKE","NOT IN","IS NOT NULL","SQL");}static
function
connect($L,$U,$C){$f=parent::connect($L,$U,$C);if(is_string($f))return$f;$lj=get_val("SELECT version()",0,$f);$f->flavor=(preg_match('~CockroachDB~',$lj)?'cockroach':'');$f->server_info=preg_replace('~^\D*([\d.]+[-\w]*).*~','\1',$lj);if(min_version(9,0,$f))$f->query("SET application_name = 'Adminer'");if($f->flavor=='cockroach')add_driver(DRIVER,"CockroachDB");return$f;}function
__construct(Db$f){parent::__construct($f);$this->types=array(lang(28)=>array("smallint"=>5,"integer"=>10,"bigint"=>19,"boolean"=>1,"numeric"=>0,"real"=>7,"double precision"=>16,"money"=>20),lang(29)=>array("date"=>13,"time"=>17,"timestamp"=>20,"timestamptz"=>21,"interval"=>0),lang(30)=>array("character"=>0,"character varying"=>0,"text"=>0,"tsquery"=>0,"tsvector"=>0,"uuid"=>0,"xml"=>0),lang(31)=>array("bit"=>0,"bit varying"=>0,"bytea"=>0),lang(32)=>array("cidr"=>43,"inet"=>43,"macaddr"=>17,"macaddr8"=>23,"txid_snapshot"=>0),lang(33)=>array("box"=>0,"circle"=>0,"line"=>0,"lseg"=>0,"path"=>0,"point"=>0,"polygon"=>0),);if(min_version(9.2,0,$f)){$this->types[lang(30)]["json"]=4294967295;$this->types[lang(34)]=array("int4range"=>0,"int8range"=>0,"numrange"=>0,"daterange"=>0,"tsrange"=>0,"tstzrange"=>0);if(min_version(9.4,0,$f))$this->types[lang(30)]["jsonb"]=4294967295;}$this->insertFunctions=array("char"=>"md5","date|time"=>"now",);$this->editFunctions=array(number_type()=>"+/-","date|time"=>"+ interval/- interval","char|text"=>"||",);if(min_version(12,0,$f)){$this->generated[]="STORED";if(min_version(18,0,$f))$this->generated[]="VIRTUAL";}$this->partitionBy=array("RANGE","LIST");if(!$f->flavor)$this->partitionBy[]="HASH";}function
enumLength(array$k){$Ef=$this->userTypes[$k["type"]];return($Ef?type_values($Ef):"");}function
setUserTypes(array$Li){$this->userTypes=array_flip($Li);$this->types[lang(7)]=array_fill_keys(array_keys($this->userTypes),0);}function
insertReturning($Q){$xa=array_filter(fields($Q),function($k){return$k['auto_increment'];});return(count($xa)==1?" RETURNING ".idf_escape(key($xa)):"");}function
insertUpdate($Q,array$H,array$_g){$e=array_keys(reset($H));$jb=array();$Vi=array();foreach($e
as$u){if(isset($_g[idf_unescape($u)]))$jb[]=$u;else$Vi[]="$u = EXCLUDED.$u";}if(!$jb||!min_version(9.5)||count($jb)!=count($_g))return
parent::insertUpdate($Q,$H,$_g);$xg="INSERT INTO ".table($Q)." (".implode(", ",$e).") VALUES\n";$Xh="\nON CONFLICT (".implode(", ",$jb).")".($Vi?" DO UPDATE SET ".implode(", ",$Vi):" DO NOTHING");$Y=array();$v=0;foreach($H
as$M){$X="(".implode(", ",$M).")";if($Y&&strlen($xg)+$v+strlen($X)+strlen($Xh)>1e6){if(!queries($xg.implode(",\n",$Y).$Xh))return
false;$Y=array();$v=0;}$Y[]=$X;$v+=strlen($X)+2;}return
queries($xg.implode(",\n",$Y).$Xh);}function
slowQuery($D,$pi){$this->conn->query("SET statement_timeout = ".(1000*$pi));$this->conn->timeout=1000*$pi;return$D;}function
convertSearch($r,array$W,array$k){$lg=preg_match('(LIKE|^!?~)',$W["op"]);$vf=preg_match('~^(character( varying)?|text|citext|bpchar|name)$~',$k["type"])||(!$lg&&preg_match('~'.number_type().'|^(date|time|timetz|timestamp|timestamptz|boolean)$~',$k["type"]));return($vf&&!preg_match('~\[]$~',$k["full_type"])?$r:"CAST($r AS text)");}function
quoteBinary($eh){return"'\\x".bin2hex($eh)."'";}function
warnings(){return$this->conn->warnings();}function
tableHelp($z,$ne=false){$Ie=array("information_schema"=>"infoschema","pg_catalog"=>($ne?"view":"catalog"),);$x=$Ie[$_GET["ns"]];if($x)return"$x-".str_replace("_","-",$z).".html";}function
inheritsFrom($Q){return
get_rows("SELECT relname AS table, nspname AS ns FROM pg_class JOIN pg_inherits ON inhparent = oid JOIN pg_namespace ON relnamespace = pg_namespace.oid WHERE inhrelid = ".$this->tableOid($Q)." ORDER BY 2, 1");}function
inheritedTables($Q){return
get_rows("SELECT relname AS table, nspname AS ns FROM pg_inherits JOIN pg_class ON inhrelid = oid JOIN pg_namespace ON relnamespace = pg_namespace.oid WHERE inhparent = ".$this->tableOid($Q)." ORDER BY 2, 1");}function
partitionsInfo($Q){$G=(min_version(10)?$this->conn->query("SELECT * FROM pg_partitioned_table WHERE partrelid = ".$this->tableOid($Q))->fetch_assoc():null);if($G){$c=get_vals("SELECT attname FROM pg_attribute WHERE attrelid = $G[partrelid] AND attnum IN (".str_replace(" ",", ",$G["partattrs"]).")");$Ma=array('h'=>'HASH','l'=>'LIST','r'=>'RANGE');return
array("partition_by"=>$Ma[$G["partstrat"]],"partition"=>implode(", ",array_map('Adminer\idf_escape',$c)),);}return
array();}function
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
indexAlgorithms(array$ci){static$F=array();if(!$F)$F=get_vals("SELECT amname FROM pg_am".(min_version(9.6)?" WHERE amtype = 'i'":"")." ORDER BY amname = '".($this->conn->flavor=='cockroach'?"prefix":"btree")."' DESC, amname");return$F;}function
indexOpclasses(){static$F=array();if(!$F&&$this->conn->flavor!='cockroach')$F=get_vals("SELECT DISTINCT opcname FROM pg_catalog.pg_opclass WHERE NOT opcdefault ORDER BY opcname");return$F;}function
supportsIndex(array$R){return$R["Engine"]!="view";}function
hasCStyleEscapes(){static$Oa;if($Oa===null)$Oa=(get_val("SHOW standard_conforming_strings",0,$this->conn)=="off");return$Oa;}}function
idf_escape($r){return'"'.str_replace('"','""',$r).'"';}function
table($r){return($_POST["schema_style"]===""&&$_GET["ns"]!=""?idf_escape($_GET["ns"]).".":"").idf_escape($r);}function
get_databases($Uc){return
get_vals("SELECT datname FROM pg_database
WHERE datallowconn = TRUE AND has_database_privilege(datname, 'CONNECT')
ORDER BY datname");}function
limit($D,$Z,$w,$Df=0,$K=" "){return" $D$Z".($w?$K."LIMIT $w".($Df?" OFFSET $Df":""):"");}function
limit1($Q,$D,$Z,$K="\n"){return(preg_match('~^INTO~',$D)?limit($D,$Z,1,0,$K):" $D".(is_view(table_status1($Q))?$Z:$K."WHERE (tableoid, ctid) = (SELECT tableoid, ctid FROM ".table($Q).$Z.$K."LIMIT 1)"));}function
db_collation($h,array$cb){return
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
count_tables(array$Ib){$F=array();foreach($Ib
as$h){if(connection()->select_db($h))$F[$h]=count(tables_list());}return$F;}function
table_status($z="",$Fc=false){static$_d;if($_d===null)$_d=get_val("SELECT 'pg_table_size'::regproc");$uh=(!$Fc&&min_version(10));$F=array();foreach(get_rows("SELECT
	relname AS \"Name\",
	CASE relkind WHEN 'v' THEN 'view' WHEN 'm' THEN 'materialized view' ELSE 'table' END AS \"Engine\"".($_d?",
	pg_table_size(c.oid) AS \"Data_length\",
	pg_indexes_size(c.oid) AS \"Index_length\"":"").",
	obj_description(c.oid, 'pg_class') AS \"Comment\",
	".(min_version(12)?"''":"CASE WHEN relhasoids THEN 'oid' ELSE '' END")." AS \"Oid\",
	reltuples AS \"Rows\",
	".($uh?"seq.last_value":"NULL")." AS \"Auto_increment\"".(min_version(10)?",
	relispartition::int AS dependent":"")."
FROM pg_class c
".($uh?"LEFT JOIN (
	SELECT d.refobjid, max(s.last_value) AS last_value
	FROM pg_depend d
	JOIN pg_class sc ON sc.oid = d.objid AND sc.relkind = 'S' AND sc.relnamespace = ".driver()->nsOid."
	JOIN pg_sequences s ON s.schemaname = current_schema() AND s.sequencename = sc.relname
	WHERE d.classid = 'pg_class'::regclass AND d.refclassid = 'pg_class'::regclass AND d.deptype IN ('a', 'i')
	".($z!=""?"AND d.refobjid = ".driver()->tableOid($z):"")."
	GROUP BY d.refobjid
) seq ON seq.refobjid = c.oid
":"")."WHERE relkind IN ('r', 'm', 'v', 'f', 'p')
AND relnamespace = ".driver()->nsOid."
".($z!=""?"AND relname = ".q($z):"ORDER BY relname"))as$G)$F[$G["Name"]]=$G;return$F;}function
is_view(array$R){return
in_array($R["Engine"],array("view","materialized view"));}function
fk_support(array$R){return
true;}function
parse_full_type(array&$G){static$na=array('timestamp without time zone'=>'timestamp','timestamp with time zone'=>'timestamptz','time without time zone'=>'time','time with time zone'=>'timetz',);preg_match('~([^([]+)(\((.*)\))?([a-z ]+)?((\[[0-9]*])*)$~',$G["full_type"],$y);list(,$T,$v,$G["length"],$ha,$ta)=$y;$G["length"].=$ta;$Ta=$T.$ha;if(isset($na[$Ta])){$G["type"]=$na[$Ta];$G["full_type"]=$G["type"].$v.$ta;}else{$G["type"]=$T;$G["full_type"]=$G["type"].$v.$ha.$ta;}}function
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
ORDER BY a.attnum")as$G){parse_full_type($G);if(in_array($G['attidentity'],array('a','d')))$G['default']='GENERATED '.($G['attidentity']=='d'?'BY DEFAULT':'ALWAYS').' AS IDENTITY';$G["generated"]=idx(array("s"=>"STORED","v"=>"VIRTUAL"),$G["attgenerated"],"");$G["composite"]=($G["typcategory"]=="C");$G["null"]=!$G["attnotnull"];$G["auto_increment"]=$G['attidentity']||preg_match('~^nextval\(~i',$G["default"])||preg_match('~^unique_rowid\(~',$G["default"]);$G["privileges"]=array("insert"=>1,"select"=>1,"update"=>1,"where"=>1,"order"=>1);if(!$G['generated']&&preg_match('~(.+)::[^,)]+(.*)~',$G["default"],$y))$G["default"]=($y[1]=="NULL"?null:idf_unescape($y[1]).$y[2]);$F[$G["field"]]=$G;}return$F;}function
indexes($Q,$g=null){$g=connection($g);$F=array();$gi=driver()->tableOid($Q);$e=get_key_vals("SELECT attnum, attname FROM pg_attribute WHERE attrelid = $gi AND attnum > 0",$g);foreach(get_rows("SELECT relname, indisunique::int, indisprimary::int, indkey, indoption, amname,
	pg_get_expr(indpred, indrelid, true) AS partial, pg_get_expr(indexprs, indrelid) AS indexpr".($g->flavor=='cockroach'?"":",
	(SELECT string_agg(CASE WHEN opcdefault THEN '' ELSE opcname END, ' ' ORDER BY s)
		FROM generate_subscripts(indclass, 1) AS s JOIN pg_catalog.pg_opclass ON pg_opclass.oid = indclass[s]) AS opclasses")."
FROM pg_index
JOIN pg_class ON indexrelid = oid
JOIN pg_am ON pg_am.oid = pg_class.relam
WHERE indrelid = $gi
ORDER BY indisprimary DESC, indisunique DESC",$g)as$G){$Sg=$G["relname"];$F[$Sg]["type"]=($G["indisprimary"]?"PRIMARY":($G["indisunique"]?"UNIQUE":"INDEX"));$F[$Sg]["columns"]=array();$F[$Sg]["descs"]=array();$F[$Sg]["algorithm"]=$G["amname"];$F[$Sg]["partial"]=$G["partial"];$Td=preg_split('~(?<=\)), (?=\()~',$G["indexpr"]);foreach(explode(" ",$G["indkey"])as$Ud)$F[$Sg]["columns"][]=($Ud?$e[$Ud]:array_shift($Td));foreach(explode(" ",$G["indoption"])as$Vd)$F[$Sg]["descs"][]=(intval($Vd)&1?'1':null);$F[$Sg]["opclasses"]=($G["opclasses"]!=""?explode(" ",$G["opclasses"]):array());$F[$Sg]["lengths"]=array();}return$F;}function
foreign_keys($Q){$F=array();foreach(get_rows("SELECT conname, condeferrable::int AS deferrable, condeferred::int AS deferred, pg_get_constraintdef(oid) AS definition
FROM pg_constraint
WHERE conrelid = ".driver()->tableOid($Q)."
AND contype = 'f'::char
ORDER BY conkey, conname")as$G){$G['deferrable']=($G['deferrable']?'':'NOT ').'DEFERRABLE'.($G['deferred']?' INITIALLY DEFERRED':'');if(preg_match('~FOREIGN KEY\s*\((.+)\)\s*REFERENCES (.+)\((.+)\)(.*)$~iA',$G['definition'],$y)){$G['source']=array_map('Adminer\idf_unescape',array_map('trim',explode(',',$y[1])));if(preg_match('~^(("([^"]|"")+"|[^"]+)\.)?"?("([^"]|"")+"|[^"]+)$~',$y[2],$Re)){$G['ns']=idf_unescape($Re[2]);$G['table']=idf_unescape($Re[4]);}$G['target']=array_map('Adminer\idf_unescape',array_map('trim',explode(',',$y[3])));$G['on_delete']=(preg_match("~ON DELETE (".driver()->onActions.")~",$y[4],$Re)?$Re[1]:'NO ACTION');$G['on_update']=(preg_match("~ON UPDATE (".driver()->onActions.")~",$y[4],$Re)?$Re[1]:'NO ACTION');$F[$G['conname']]=$G;}}return$F;}function
view($z){return
array("select"=>trim(get_val("SELECT pg_get_viewdef(".driver()->tableOid($z).")")));}function
collations(){return
array();}function
information_schema($h,$I=""){$ai=array("information_schema","pg_catalog","pg_toast");if(connection()->flavor=='cockroach'){$ai[]="crdb_internal";$ai[]="pg_extension";}return
in_array($I!=""?$I:get_schema(),$ai);}function
error(){$F=h(connection()->error);if(preg_match('~^(.*\n)?([^\n]*)\n( *)\^(\n.*)?$~s',$F,$y))$F=$y[1].preg_replace('~((?:[^&]|&[^;]*;){'.strlen($y[3]).'})(.*)~','\1<b>\2</b>',$y[2]).$y[4];return
nl_br($F);}function
create_database($h,$bb){return
queries("CREATE DATABASE ".idf_escape($h).($bb?" ENCODING ".idf_escape($bb):""));}function
drop_databases(array$Ib){connection()->close();return
apply_queries("DROP DATABASE",$Ib,'Adminer\idf_escape');}function
rename_database($z,$bb){connection()->close();return!!queries("ALTER DATABASE ".idf_escape(DB)." RENAME TO ".idf_escape($z));}function
auto_increment(){return"";}function
alter_table($Q,$z,array$l,array$Wc,$fb,$oc,$bb,$xa,$eg){$b=array();$Hg=array();if($Q!=""&&$Q!=$z)$Hg[]="ALTER TABLE ".table($Q)." RENAME TO ".table($z);$rh="";foreach($l
as$k){$d=idf_escape($k[0]);$W=$k[1];if(!$W)$b[]="DROP $d";else{$ij=$W[5];unset($W[5]);if($k[0]==""){if(isset($W[6]))$W[1]=($W[1]==" bigint"?" big":($W[1]==" smallint"?" small":" "))."serial";$b[]=($Q!=""?"ADD ":"  ").implode($W);if(isset($W[6]))$b[]=($Q!=""?"ADD":" ")." PRIMARY KEY ($W[0])";}else{if($d!=$W[0])$Hg[]="ALTER TABLE ".table($z)." RENAME $d TO $W[0]";$b[]="ALTER $d TYPE$W[1]";$sh=$Q."_".idf_unescape($W[0])."_seq";$b[]="ALTER $d ".($W[3]?"SET".preg_replace('~GENERATED ALWAYS(.*) (STORED|VIRTUAL)~','EXPRESSION\1',$W[3]):(isset($W[6])?"SET DEFAULT nextval(".q($sh).")":"DROP DEFAULT"));if(isset($W[6]))$rh="CREATE SEQUENCE IF NOT EXISTS ".idf_escape($sh)." OWNED BY ".idf_escape($Q).".$W[0]";$b[]="ALTER $d ".($W[2]==" NULL"?"DROP NOT":"SET").$W[2];}if($k[0]!=""||$ij!="")$Hg[]="COMMENT ON COLUMN ".table($z).".$W[0] IS ".($ij!=""?substr($ij,9):"''");}}$b=array_merge($b,$Wc);if($Q==""){$O="";if($eg){$Ya=(connection()->flavor=='cockroach');$O=" PARTITION BY $eg[partition_by]($eg[partition])";if($eg["partition_by"]=='HASH'){$fg=+$eg["partitions"];for($p=0;$p<$fg;$p++)$Hg[]="CREATE TABLE ".idf_escape($z."_$p")." PARTITION OF ".idf_escape($z)." FOR VALUES WITH (MODULUS $fg, REMAINDER $p)";}else{$yg="MINVALUE";foreach($eg["partition_names"]as$p=>$W){$X=$eg["partition_values"][$p];$cg=" VALUES ".($eg["partition_by"]=='LIST'?"IN ($X)":"FROM ($yg) TO ($X)");if($Ya)$O
.=($p?",":" (")."\n  PARTITION ".(preg_match('~^DEFAULT$~i',$W)?$W:idf_escape($W))."$cg";else$Hg[]="CREATE TABLE ".idf_escape($z."_$W")." PARTITION OF ".idf_escape($z)." FOR$cg";$yg=$X;}$O
.=($Ya?"\n)":"");}}array_unshift($Hg,"CREATE TABLE ".table($z)." (\n".implode(",\n",$b)."\n)$O");}elseif($b)array_unshift($Hg,"ALTER TABLE ".table($Q)."\n".implode(",\n",$b));if($rh)array_unshift($Hg,$rh);if($fb!==null)$Hg[]="COMMENT ON TABLE ".table($z)." IS ".q($fb);foreach($Hg
as$D){if(!queries($D))return
false;}if($xa!=""){foreach(fields($z)as$Hc=>$k){if($k["auto_increment"])return!!queries("SELECT setval(pg_get_serial_sequence(".q(table($z)).", ".q($Hc)."), $xa)");}}return
true;}function
alter_indexes($Q,$b){$zb=array();$dc=array();$Hg=array();foreach($b
as$W){if($W[0]!="INDEX")$zb[]=($W[2]=="DROP"?"\nDROP CONSTRAINT ".idf_escape($W[1]):"\nADD".($W[1]!=""?" CONSTRAINT ".idf_escape($W[1]):"")." $W[0] ".($W[0]=="PRIMARY"?"KEY ":"")."(".implode(", ",$W[2]).")");elseif($W[2]=="DROP")$dc[]=idf_escape($W[1]);else$Hg[]="CREATE INDEX ".idf_escape($W[1]!=""?$W[1]:uniqid($Q."_"))." ON ".table($Q).($W[3]?" USING $W[3]":"")." (".implode(", ",$W[2]).")".($W[4]?" WHERE $W[4]":"");}if($zb)array_unshift($Hg,"ALTER TABLE ".table($Q).implode(",",$zb));if($dc)array_unshift($Hg,"DROP INDEX ".implode(", ",$dc));foreach($Hg
as$D){if(!queries($D))return
false;}return
true;}function
truncate_tables(array$S){return!!queries("TRUNCATE ".implode(", ",array_map('Adminer\table',$S)));}function
drop_kinds(array$S){$F=array("MATERIALIZED VIEW"=>array(),"VIEW"=>array(),"TABLE"=>array());foreach($S
as$z=>$R)$F[strtoupper($R["Engine"])][]=table($z);return
array_filter($F);}function
drop_views(array$nj){return
drop_tables($nj);}function
drop_tables(array$S){$Uh=array();foreach($S
as$Q)$Uh[$Q]=table_status1($Q);foreach(drop_kinds($Uh)as$ue=>$uf){if(!queries("DROP $ue ".implode(", ",$uf)))return
false;}return
true;}function
move_tables(array$S,array$nj,$ii){foreach(array_merge($S,$nj)as$Q){$O=table_status1($Q);if(!queries("ALTER ".strtoupper($O["Engine"])." ".table($Q)." SET SCHEMA ".idf_escape($ii)))return
false;}return
true;}function
trigger($z,$Q){if($z=="")return
array("Statement"=>"EXECUTE PROCEDURE ()");$e=array();$Z="WHERE trigger_schema = current_schema() AND event_object_table = ".q($Q)." AND trigger_name = ".q($z);foreach(get_rows("SELECT * FROM information_schema.triggered_update_columns $Z")as$G)$e[]=$G["event_object_column"];$F=array();foreach(get_rows('SELECT trigger_name AS "Trigger", action_timing AS "Timing", event_manipulation AS "Event", \'FOR EACH \' || action_orientation AS "Type", action_statement AS "Statement"
FROM information_schema.triggers'."
$Z
ORDER BY event_manipulation DESC")as$G){if($e&&$G["Event"]=="UPDATE")$G["Event"].=" OF";$G["Of"]=implode(", ",$e);if($F)$G["Event"].=" OR $F[Event]";$F=$G;}return$F;}function
triggers($Q){$F=array();foreach(get_rows("SELECT * FROM information_schema.triggers WHERE trigger_schema = current_schema() AND event_object_table = ".q($Q))as$G){$Ei=trigger($G["trigger_name"],$Q);$F[$Ei["Trigger"]]=array($Ei["Timing"],$Ei["Event"]);}return$F;}function
trigger_options(){return
array("Timing"=>array("BEFORE","AFTER"),"Event"=>array("INSERT","UPDATE","UPDATE OF","DELETE","INSERT OR UPDATE","INSERT OR UPDATE OF","DELETE OR INSERT","DELETE OR UPDATE","DELETE OR UPDATE OF","DELETE OR INSERT OR UPDATE","DELETE OR INSERT OR UPDATE OF",),"Type"=>array("FOR EACH ROW","FOR EACH STATEMENT"),);}function
routine($z,$T){$A=routine_options($T);$oh=array_intersect_key(array("VOLATILITY"=>"CASE p.provolatile WHEN 'i' THEN 'IMMUTABLE' WHEN 's' THEN 'STABLE' ELSE 'VOLATILE' END","NULL_INPUT"=>"CASE WHEN p.proisstrict THEN 'RETURNS NULL ON NULL INPUT' ELSE 'CALLED ON NULL INPUT' END","SECURITY"=>"CASE WHEN p.prosecdef THEN 'SECURITY DEFINER' ELSE 'SECURITY INVOKER' END","PARALLEL"=>"CASE p.proparallel WHEN 's' THEN 'PARALLEL SAFE' WHEN 'r' THEN 'PARALLEL RESTRICTED' ELSE 'PARALLEL UNSAFE' END",),$A);foreach($oh
as$u=>$J)$oh[$u]="$J AS \"$u\"";$H=get_rows('SELECT r.routine_definition AS definition, LOWER(r.external_language) AS language, '.($oh?implode(', ',$oh).', ':'').'r.*
FROM information_schema.routines r
LEFT JOIN pg_catalog.pg_proc p ON p.oid::text = substring(r.specific_name, \'[0-9]+$\')
WHERE r.routine_schema = current_schema() AND r.specific_name = '.q($z));if(!$H)return
array();$F=$H[0];$F["options"]=array_intersect_key($F,$A);$F["returns"]=array("type"=>preg_replace('~^_(.*)~','\1[]',"$F[type_udt_name]"));$F["fields"]=get_rows("SELECT COALESCE(parameter_name, ordinal_position::text) AS field,
	CASE data_type WHEN 'USER-DEFINED' THEN udt_name WHEN 'ARRAY' THEN substr(udt_name, 2) || '[]' ELSE data_type END AS type,
	character_maximum_length AS length, parameter_mode AS inout
FROM information_schema.parameters
WHERE specific_schema = current_schema() AND specific_name = ".q($z)."
ORDER BY ordinal_position");return$F;}function
routines(){return
get_rows('SELECT specific_name AS "SPECIFIC_NAME", routine_type AS "ROUTINE_TYPE", routine_name AS "ROUTINE_NAME", type_udt_name AS "DTD_IDENTIFIER"
FROM information_schema.routines
WHERE routine_schema = current_schema()'.(connection()->flavor=='cockroach'?'':"
AND substring(specific_name, '[0-9]+\$')::oid NOT IN (SELECT objid FROM pg_catalog.pg_depend WHERE classid = 'pg_proc'::regclass AND deptype = 'e')").'
ORDER BY SPECIFIC_NAME');}function
routine_languages(){$F=array();foreach(get_vals("SELECT LOWER(lanname) FROM pg_catalog.pg_language")as$_e)$F[$_e]=(preg_match('~sql$~',$_e)?"pgsql":"txt");return$F;}function
routine_options($ch){$Ya=(connection()->flavor=='cockroach');$jh=($Ya?array():array("SECURITY"=>array("SECURITY INVOKER","SECURITY DEFINER")));if($ch=="PROCEDURE")return$jh;return
array("VOLATILITY"=>array("VOLATILE","STABLE","IMMUTABLE"),"NULL_INPUT"=>array("CALLED ON NULL INPUT","RETURNS NULL ON NULL INPUT"),)+$jh+($Ya?array():array("PARALLEL"=>array("PARALLEL UNSAFE","PARALLEL RESTRICTED","PARALLEL SAFE"),));}function
routine_id($z,array$G){$F=array();foreach($G["fields"]as$k){$v=$k["length"];$F[]=$k["type"].($v?"($v)":"");}return
idf_escape($z)."(".implode(", ",$F).")";}function
last_id($E){$G=(is_object($E)?$E->fetch_row():array());return($G?$G[0]:0);}function
explain(Db$f,$D){return$f->query("EXPLAIN $D");}function
found_rows(array$R,array$Z){if(preg_match("~ rows=([0-9]+)~",get_val("EXPLAIN SELECT * FROM ".idf_escape($R["Name"]).($Z?" WHERE ".implode(" AND ",$Z):"")),$Rg))return$Rg[1];}function
types($Cc=false){$Ya=connection()->flavor=='cockroach';$ve=($Ya?"'e'":"'b','c','d','e'".(min_version(9.2)?",'r'":""));return
get_key_vals("SELECT t.oid, t.typname
FROM pg_type t
WHERE t.typnamespace = ".driver()->nsOid."
AND t.typtype IN ($ve)".($Ya?"
AND t.typelem = 0":"
AND (t.typrelid = 0 OR (SELECT c.relkind FROM pg_class c WHERE c.oid = t.typrelid) = 'c')"."
AND NOT EXISTS (SELECT 1 FROM pg_type e WHERE e.typarray = t.oid)".($Cc?'':"
AND t.oid NOT IN (SELECT objid FROM pg_catalog.pg_depend WHERE classid = 'pg_type'::regclass AND deptype = 'e')"))."
ORDER BY t.typname");}function
type_values($q){$qc=get_vals("SELECT enumlabel FROM pg_enum WHERE enumtypid = $q ORDER BY enumsortorder");return($qc?"'".implode("', '",array_map('addslashes',$qc))."'":"");}function
collation_name($Ef){return(min_version(9.1)?"(SELECT collname FROM pg_collation WHERE oid = $Ef AND collname != 'default')":"NULL");}function
type_definition($q){$T=first(get_rows("SELECT typtype, typisdefined::int AS defined, typrelid FROM pg_type WHERE oid = $q"));$F=array("kind"=>($T?$T["typtype"]:""),"definition"=>"");if(!$T||!$T["defined"])return$F;switch($F["kind"]){case'e':$Y=get_vals("SELECT enumlabel FROM pg_enum WHERE enumtypid = $q ORDER BY enumsortorder");$F["definition"]="AS ENUM (".implode(", ",array_map('Adminer\q',$Y)).")";break;case'c':$e=array();foreach(get_rows("SELECT attname, format_type(atttypid, atttypmod) AS full_type, ".collation_name("attcollation")." AS collation
FROM pg_attribute
WHERE attrelid = $T[typrelid] AND attnum > 0 AND NOT attisdropped
ORDER BY attnum")as$G)$e[]=idf_escape($G["attname"])." $G[full_type]".($G["collation"]?" COLLATE ".idf_escape($G["collation"]):"");$F["definition"]="AS (\n\t".implode(",\n\t",$e)."\n)";break;case'd':$ac=first(get_rows("SELECT format_type(typbasetype, typtypmod) AS base, typnotnull::int AS notnull, typdefault, ".collation_name("typcollation")." AS collation
FROM pg_type WHERE oid = $q"));$F["definition"]="AS $ac[base]".($ac["collation"]?" COLLATE ".idf_escape($ac["collation"]):"").($ac["typdefault"]!=""?" DEFAULT $ac[typdefault]":"").($ac["notnull"]?" NOT NULL":"");foreach(get_rows("SELECT conname, pg_get_constraintdef(oid) AS definition FROM pg_constraint WHERE contypid = $q AND contype != 'n' ORDER BY conname")as$G)$F["definition"].=" CONSTRAINT ".idf_escape($G["conname"])." $G[definition]";break;case'r':$Lg=first(get_rows("SELECT format_type(rngsubtype, NULL) AS subtype,
(SELECT opcname FROM pg_opclass WHERE oid = rngsubopc) AS subtype_opclass,
".collation_name("rngcollation")." AS collation,
NULLIF(rngcanonical, 0)::regproc::text AS canonical,
NULLIF(rngsubdiff, 0)::regproc::text AS subtype_diff".(min_version(14)?",
(SELECT typname FROM pg_type WHERE oid = rngmultitypid) AS multirange_type_name":"")."
FROM pg_range WHERE rngtypid = $q"));$A=array();foreach(array("subtype"=>0,"subtype_opclass"=>1,"collation"=>1,"canonical"=>0,"subtype_diff"=>0,"multirange_type_name"=>1)as$u=>$uc){if($Lg[$u]!="")$A[]=strtoupper($u)." = ".($uc?idf_escape($Lg[$u]):$Lg[$u]);}$F["definition"]="AS RANGE (".implode(", ",$A).")";}return$F;}function
schemas(){return
get_vals("SELECT nspname FROM pg_namespace ORDER BY nspname");}function
get_schema(){return(string)get_val("SELECT current_schema()");}function
set_schema($I,$g=null){$_GET["ns"]=$I;$F=get_val("SELECT set_config('search_path', ".q(idf_escape($I)).", false) FROM pg_namespace WHERE nspname = ".q($I),0,$g);driver()->setUserTypes(types(true));return!!$F;}function
drop_sql(array$S){$F="";foreach(drop_kinds($S)as$ue=>$uf)$F
.="DROP $ue IF EXISTS ".implode(", ",$uf).";\n";return($F?"$F\n":"");}function
foreign_keys_sql($Q){$F="";$Sc=foreign_keys($Q);ksort($Sc);foreach($Sc
as$Rc=>$Qc){$F
.="ALTER TABLE ONLY ".table($Q)." ADD CONSTRAINT ".idf_escape($Rc)." ".preg_replace_callback('~( REFERENCES )([^(.]+)\(~',function(array$y){return$y[1].table(idf_unescape($y[2]))."(";},$Qc["definition"]).";\n";}return($F?"$F\n":$F);}function
indexes_sql($Q,$_g=""){$F="";$D="SELECT indexdef, quote_ident(schemaname) || '.' || quote_ident(tablename) AS qualified, quote_ident(current_database()) AS db
FROM pg_catalog.pg_indexes
WHERE schemaname = current_schema() AND tablename = ".q($Q).($_g!=""?" AND indexname != ".q($_g):"");foreach(get_rows($D,null,"-- ")as$G)$F
.="\n\n".str_replace(array(" $G[db].$G[qualified] USING "," $G[qualified] USING ")," ".table($Q)." USING ",$G["indexdef"]).";";return$F;}function
create_sql($Q,$xa,$Wh){$ah=array();$uh=array();$vh=array();$th=array();$O=table_status1($Q);if(is_view($O)){$mj=view($Q);$zb="CREATE ".strtoupper($O["Engine"])." ".table($Q)." AS ".rtrim($mj["select"],";").";";return
rtrim($zb.indexes_sql($Q),';');}$l=fields($Q);if(count($O)<2||empty($l))return"";$F="CREATE TABLE ".table($O['Name'])." (\n    ";$ei=q(table($O['Name']));foreach($l
as$k){$wh="";if($k['default']=="nextval('$O[Name]_$k[field]_seq')"){$wh=table("$O[Name]_$k[field]_seq");$k['default']=null;$k['full_type']=preg_replace('~int(eger)?~','serial',$k['full_type']);}$bg=idf_escape($k['field']).' '.$k['full_type'].preg_replace_callback('~(nextval\(\')([^.\']+)\'~',function(array$y){return$y[1].str_replace("'","''",table(idf_unescape($y[2])))."'";},default_value($k)).($k['null']?"":" NOT NULL");$ah[]=$bg;if(preg_match('~nextval\(\'([^\']+)\'\)~',$k['default'],$Se)){$sh=$Se[1];$Oh=first(get_rows((min_version(10)?"SELECT *, cache_size AS cache_value FROM pg_sequences WHERE schemaname = current_schema() AND sequencename = ".q(idf_unescape($sh)):"SELECT * FROM $sh"),null,"-- "));$rh=table(idf_unescape($sh));$uh[]=($Wh=="DROP+CREATE"?"DROP SEQUENCE IF EXISTS $rh;\n":"")."CREATE SEQUENCE $rh INCREMENT $Oh[increment_by] MINVALUE $Oh[min_value] MAXVALUE $Oh[max_value]"." CACHE $Oh[cache_value];";if(get_val("SELECT pg_get_serial_sequence($ei, ".q($k['field']).")"))$vh[]="\n\nALTER SEQUENCE $rh OWNED BY ".table($O['Name']).".".idf_escape($k['field']).";";if($xa)$th[]=$rh;}elseif($xa&&$k['auto_increment']){$rh=($wh?"":get_val("SELECT pg_get_serial_sequence($ei, ".q($k['field']).")::regclass"));$th[]=($rh?table(idf_unescape($rh)):$wh);}}if(!empty($uh))$F=implode("\n\n",$uh)."\n\n$F";$_g="";foreach(indexes($Q)as$Rd=>$s){if($s['type']=='PRIMARY'){$_g=$Rd;$ah[]="CONSTRAINT ".idf_escape($Rd)." PRIMARY KEY (".implode(', ',array_map('Adminer\idf_escape',$s['columns'])).")";}}foreach(driver()->checkConstraints($Q)as$lb=>$nb)$ah[]="CONSTRAINT ".idf_escape($lb)." CHECK ($nb)";$F
.=implode(",\n    ",$ah)."\n)";$cg=driver()->partitionsInfo($O['Name']);if($cg)$F
.="\nPARTITION BY $cg[partition_by]($cg[partition])";$F
.=(min_version(12)?"":"\nWITH (oids = ".($O['Oid']?'true':'false').")").";";$F
.=implode($vh);if($O['Comment'])$F
.="\n\nCOMMENT ON TABLE ".table($O['Name'])." IS ".q($O['Comment']).";";foreach($l
as$Hc=>$k){if($k['comment'])$F
.="\n\nCOMMENT ON COLUMN ".table($O['Name']).".".idf_escape($Hc)." IS ".q($k['comment']).";";}$F
.=indexes_sql($Q,$_g);foreach(array_filter($th)as$rh){$Oh=first(get_rows("SELECT last_value, is_called::int FROM $rh",null,"-- "));if($Oh['is_called'])$F
.="\n\nDO \$\$ BEGIN PERFORM setval(".q($rh).", $Oh[last_value]); END \$\$;";}return
rtrim($F,';');}function
truncate_sql($Q){return"TRUNCATE ".table($Q);}function
truncate_all_sql(array$S){return($S?"TRUNCATE ".implode(", ",array_map('Adminer\table',$S)).";\n\n":"");}function
trigger_sql($Q){$O=table_status1($Q);$F="";foreach(triggers($Q)as$Di=>$Ci){$Ei=trigger($Di,$O['Name']);$F
.="\nCREATE TRIGGER ".idf_escape($Ei['Trigger'])." $Ei[Timing] $Ei[Event] ON ".table($O['Name'])." $Ei[Type] $Ei[Statement];;\n";}return$F;}function
use_sql($Hb,$Wh=""){$z=idf_escape($Hb);$F="";if(preg_match('~CREATE~',$Wh)){if($Wh=="DROP+CREATE")$F="DROP DATABASE IF EXISTS $z;\n";$F
.="CREATE DATABASE $z;\n";}return"$F\\connect $z";}function
use_schema_sql($I,$Wh){$z=idf_escape($I);$F="";if(preg_match('~CREATE~',$Wh)){if($Wh=="DROP+CREATE")$F="DROP SCHEMA IF EXISTS $z CASCADE;\n";$F
.="CREATE SCHEMA IF NOT EXISTS $z;\n";}return$F."SET search_path TO $z";}function
show_variables(){return
get_rows("SHOW ALL");}function
process_list(){return
get_rows("SELECT * FROM pg_stat_activity ORDER BY ".(min_version(9.2)?"pid":"procpid"));}function
convert_field(array$k){if(preg_match('~^(geometry|geography)$~',$k["type"])&&strpos($k["full_type"],"[")===false)return"ST_AsEWKT(".idf_escape($k["field"]).")";}function
unconvert_field(array$k,$F){return($k["composite"]?"$F::$k[type]":$F);}function
support($Gc){return
preg_match('~^(check|columns|comment|database|drop_col|dump|descidx|fast_status|indexes|kill|partial_indexes|routine|scheme|sequence|sql|table'.'|transaction_ddl|trigger|type|variables|view'.(min_version(9.3)?'|materializedview':'').(min_version(11)?'|procedure':'').(connection()->flavor=='cockroach'?'':'|deferrable').(connection()->flavor=='cockroach'||!min_version(9.1)?'':'|extension').(connection()->flavor=='cockroach'?'':'|processlist').')$~',$Gc);}function
kill_process($q){return
queries("SELECT pg_terminate_backend(".number($q).")");}function
connection_id(){return"SELECT pg_backend_pid()";}function
max_connections(){return
get_val("SHOW max_connections");}}add_driver("sqlite","SQLite");if(isset($_GET["sqlite"])){define('Adminer\DRIVER',"sqlite");if(class_exists("SQLite3")&&$_GET["ext"]!="pdo"){abstract
class
SqliteDb
extends
SqlDb{var$extension="SQLite3";private$link;function
attach(array$L,$U,$C){$this->link=new
\SQLite3($L["path"]);$lj=\SQLite3::version();$this->server_info=$lj["versionString"];return'';}function
query($D,$Mi=false){$E=@$this->link->query($D);$this->error="";if(!$E){$this->errno=$this->link->lastErrorCode();$this->error=$this->link->lastErrorMsg();return
false;}elseif($E->numColumns())return
new
Result($E);$this->affected_rows=$this->link->changes();return
true;}function
quote($P){return(is_utf8($P)?"'".$this->link->escapeString($P)."'":"x'".bin2hex($P)."'");}}class
Result{var$num_rows;private$result,$offset=0;function
__construct($E){$this->result=$E;}function
fetch_assoc(){return$this->result->fetchArray(SQLITE3_ASSOC);}function
fetch_row(){return$this->result->fetchArray(SQLITE3_NUM);}function
fetch_field(){$Li=array(1=>"integer","real","text","blob","null");$d=$this->offset++;return(object)array("name"=>$this->result->columnName($d),"native_type"=>$Li[$this->result->columnType($d)],);}}}elseif(extension_loaded("pdo_sqlite")){abstract
class
SqliteDb
extends
PdoDb{var$extension="PDO_SQLite";function
attach(array$L,$U,$C){return$this->dsn(DRIVER.":".$L["path"],"","");}function
quote($P){return(is_utf8($P)?parent::quote($P):"x'".bin2hex($P)."'");}}}if(class_exists('Adminer\SqliteDb')){class
Db
extends
SqliteDb{function
attach(array$L,$U,$C){parent::attach($L,$U,$C);$this->query("PRAGMA foreign_keys = 1");$this->query("PRAGMA busy_timeout = 500");return'';}function
select_db($m){$D="ATTACH ".$this->quote(preg_match("~(^[/\\\\]|:)~",$m)?$m:dirname($_SERVER["SCRIPT_FILENAME"])."/$m")." AS a";if(is_readable($m)&&$this->query($D))return!self::attach(server_parts(array("path"=>$m)),'','');return
false;}}}class
Driver
extends
SqlDriver{static$extensions=array("SQLite3","PDO_SQLite");static$jush="sqlite";static$passwords=false;static$serverFile=true;protected$types=array(array("integer"=>0,"real"=>0,"numeric"=>0,"text"=>0,"blob"=>0));var$insertFunctions=array();var$editFunctions=array("integer|real|numeric"=>"+/-","text"=>"||",);var$fulltextOperator="MATCH";var$functions=array("hex","length","lower","round","unixepoch","upper");var$grouping=array("avg","count","count distinct","group_concat","max","min","sum");function
operators($ci){$F=array("=","<",">","<=",">=","!=","LIKE","LIKE %%","IN","IS NULL","NOT LIKE","NOT IN","IS NOT NULL");if(preg_match('~^fts\d+$~i',(string)idx($ci,"Engine")))$F[]="MATCH";$F[]="SQL";return$F;}static
function
connect($L,$U,$C){return
parent::connect(":memory:","","");}function
__construct(Db$f){parent::__construct($f);if(min_version(3.31,0,$f))$this->generated=array("STORED","VIRTUAL");if(min_version(3.37,0,$f))$this->types[0]["any"]=0;}function
structuredTypes(){return
array_keys($this->types[0]);}function
quoteBinary($eh){return"x".q(bin2hex($eh));}function
typeName(\stdClass$k){$F=strtolower(idx((array)$k,'sqlite:decl_type',parent::typeName($k)));return
idx(array("string"=>"text","double"=>"real"),$F,$F);}function
engines(){$F=array("table");if(min_version("3.8.2")){if(min_version(3.37)){$F[]="STRICT";$F[]="STRICT, WITHOUT ROWID";}$F[]="WITHOUT ROWID";}return$F;}private
function
isVirtual(array$R){$oc=$R["Engine"];return$oc!=""&&!in_array($oc,array_merge(array("view"),$this->engines()));}function
supportsIndex(array$R){return!is_view($R)&&!$this->isVirtual($R);}function
supportsAlterIndex(array$R){return$this->supportsIndex($R);}function
supportsAlterTable(array$ci){return!$this->isVirtual($ci);}function
shadowTables($Q){$F=array();if(min_version(3.37)){foreach(get_vals("SELECT name FROM pragma_table_list WHERE schema = 'main' AND type = 'shadow' ORDER BY name")as$z){if(preg_match('(^'.preg_quote($Q).'_[^_]*$)',$z))$F[]=array("table"=>$z,"ns"=>"");}}return$F;}function
fulltextSql($z,array$s,$D,$Ka){return
idf_escape($z)." MATCH ".q($D);}function
insertUpdate($Q,array$H,array$_g){$Y=array();foreach($H
as$M)$Y[]="(".implode(", ",$M).")";return
queries("REPLACE INTO ".table($Q)." (".implode(", ",array_keys(reset($H))).") VALUES\n".implode(",\n",$Y));}function
tableHelp($z,$ne=false){if(preg_match('~^sqlite_(seq|stat.)~',$z,$y))return"fileformat2.html#$y[1]tab";if(preg_match('~^sqlite(_temp)?_(master|schema)$~',$z))return"schematab.html";}function
checkConstraints($Q){preg_match_all('~ CHECK *(\( *(((?>[^()]*[^() ])|(?1))*) *\))~',get_val("SELECT sql FROM sqlite_master WHERE type = 'table' AND name = ".q($Q),0,$this->conn),$Se);return
array_combine($Se[2],$Se[2]);}function
allFields(){$F=array();if(min_version(3.16)){$H=get_rows('SELECT m.name AS tab, p.name AS field, p.type, p."notnull", p.pk AS '.idf_escape("primary")."
FROM sqlite_master m, pragma_table_".(min_version(3.31)?"x":"")."info(m.name) p
WHERE m.type IN ('table', 'view')".(min_version(3.31)?"
AND p.hidden != 1":"").(min_version(3.37)?"
AND m.name NOT IN (SELECT name FROM pragma_table_list WHERE type = 'shadow')":"")."
ORDER BY (m.name LIKE 'sqlite_%'), m.name, p.cid",$this->conn);foreach($H
as$G){$G["type"]=type_affinity($G["type"]);$G["null"]=!$G["notnull"];$F[$G["tab"]][]=$G;}}else{foreach(tables_list()as$Q=>$T){foreach(fields($Q)as$k)$F[$Q][]=$k;}}return$F;}}function
idf_escape($r){return'"'.str_replace('"','""',$r).'"';}function
table($r){return
idf_escape($r);}function
get_databases($Uc){return
array();}function
limit($D,$Z,$w,$Df=0,$K=" "){return" $D$Z".($w?$K."LIMIT $w".($Df?" OFFSET $Df":""):"");}function
limit1($Q,$D,$Z,$K="\n"){return(preg_match('~^INTO~',$D)||get_val("SELECT sqlite_compileoption_used('ENABLE_UPDATE_DELETE_LIMIT')")?limit($D,$Z,1,0,$K):" $D WHERE rowid = (SELECT rowid FROM ".table($Q).$Z.$K."LIMIT 1)");}function
db_collation($h,array$cb){return
get_val("PRAGMA encoding");}function
logged_user(){return
get_current_user();}function
virtual_module($Ph){return(preg_match('~^CREATE\s+VIRTUAL\s+TABLE\s+(?:IF\s+NOT\s+EXISTS\s+)?(?:"[^"]*+"|`[^`]*+`|\[[^\]]*+\]|[^\s(]+)\s+USING\s+([a-z0-9_]+)~i',$Ph,$y)?$y[1]:"");}function
tables_list(){return
get_key_vals("SELECT name, type FROM sqlite_master WHERE type IN ('table', 'view')".(min_version(3.37)?" AND name NOT IN (SELECT name FROM pragma_table_list WHERE type = 'shadow')":"")."
ORDER BY (name LIKE 'sqlite_%'), name");}function
count_tables(array$Ib){return
array();}function
db_status(){$Xf=get_val("PRAGMA page_size");$gd=get_val("PRAGMA freelist_count")*$Xf;return
array("Data_length"=>get_val("PRAGMA page_count")*$Xf-$gd,"Index_length"=>0,"Data_free"=>$gd,);}function
table_status($z="",$Fc=false){$F=array();$H=array();if(!$Fc&&$z==""){connection()->query("PRAGMA optimize = 0x10002");$H=get_key_vals("SELECT tbl, MAX(CAST(stat AS integer)) FROM sqlite_stat1 GROUP BY tbl");}foreach(get_rows("SELECT name AS Name, type AS Engine, sql, 'rowid' AS Oid, '' AS Auto_increment".(min_version(3.37)?", name IN (SELECT name FROM pragma_table_list WHERE type = 'shadow') AS dependent":"")." FROM sqlite_master WHERE type IN ('table', 'view') ".($z!=""?"AND name = ".q($z):"ORDER BY (name LIKE 'sqlite_%'), name"))as$G){if($G["Engine"]=="table"){$Ph=preg_replace('~(?:\s|--[^\n]*|/\*.*?\*/)+$~s','',$G["sql"]);$Xh=preg_replace('~.*\)~s','',$Ph);$G["Engine"]=virtual_module($G["sql"])?:(implode(", ",array_filter(array((preg_match('~\bSTRICT\b~i',$Xh)?"STRICT":0),(preg_match('~\bWITHOUT\s+ROWID\b~i',$Xh)?"WITHOUT ROWID":0),)))?:"table");}unset($G["sql"]);$G["Rows"]=idx($H,$G["Name"],0);$F[$G["Name"]]=$G;}if(!$Fc){foreach(get_rows("SELECT * FROM sqlite_sequence".($z!=""?" WHERE name = ".q($z):""),null,"")as$G)$F[$G["name"]]["Auto_increment"]=$G["seq"];}return$F;}function
is_view(array$R){return$R["Engine"]=="view";}function
fk_support(array$R){return!get_val("SELECT sqlite_compileoption_used('OMIT_FOREIGN_KEY')");}function
type_affinity($T){$T=strtolower($T);return(preg_match('~int~i',$T)?"integer":(preg_match('~char|clob|text~i',$T)?"text":(preg_match('~blob~i',$T)?"blob":(preg_match('~real|floa|doub~i',$T)?"real":(preg_match('~any~i',$T)?"any":"numeric")))));}function
fields($Q){$F=array();$Ph=get_val("SELECT sql FROM sqlite_master WHERE type = 'table' AND name = ".q($Q));$Dg=array("select"=>1,"where"=>1,"order"=>1);if(!preg_match('~^sqlite(_temp)?_(master|schema)$~',$Q))$Dg+=array("insert"=>1,"update"=>1);$id=preg_match('~^fts\d+$~i',virtual_module($Ph));foreach(get_rows("PRAGMA table_".(min_version(3.31)?"x":"")."info(".table($Q).")")as$G){if($G["hidden"]==1)continue;$z=$G["name"];$T=strtolower($G["type"]);$i=$G["dflt_value"];$F[$z]=array("field"=>$z,"type"=>($id?"text":type_affinity($T)),"full_type"=>$T,"default"=>(preg_match("~^'(.*)'$~",$i,$y)?str_replace("''","'",$y[1]):($i=="NULL"?null:$i)),"null"=>!$G["notnull"],"privileges"=>$Dg,"primary"=>$G["pk"],);if($G["pk"]&&preg_match('~\bAUTOINCREMENT\b~i',$Ph))$F[$z]["auto_increment"]=true;}$r='[(,]\s*(("[^"]*+")+|[a-z0-9_]+)';$Wg='(?:[^,()\']|\'[^\']*+\'|\([^)]*+\))*?';preg_match_all('~'.$r.'\s+text\b'.$Wg.'COLLATE\s+(\'[^\']+\'|[a-z0-9_]+)~i',$Ph,$Se,PREG_SET_ORDER);foreach($Se
as$y){$z=str_replace('""','"',preg_replace('~^"|"$~','',$y[1]));if($F[$z])$F[$z]["collation"]=trim($y[3],"'");}preg_match_all('~'.$r.'\s'.$Wg.'GENERATED\s+ALWAYS\s+AS\s*\((.+?)\)\s+(STORED|VIRTUAL)~i',$Ph,$Se,PREG_SET_ORDER);foreach($Se
as$y){$z=str_replace('""','"',preg_replace('~^"|"$~','',$y[1]));if($F[$z]){$F[$z]["default"]=$y[3];$F[$z]["generated"]=strtoupper($y[4]);}}return$F;}function
indexes($Q,$g=null){$g=connection($g);$F=array();$Ph=get_val("SELECT sql FROM sqlite_master WHERE type = 'table' AND name = ".q($Q),0,$g);if(preg_match('~^fts\d+$~i',virtual_module($Ph)))return
array($Q=>array("type"=>"FULLTEXT","columns"=>array_keys(fields($Q)),"lengths"=>array(),"descs"=>array()));if(preg_match('~\bPRIMARY\s+KEY\s*\((([^)"]+|"[^"]*"|`[^`]*`)++)~i',$Ph,$y)){$F[""]=array("type"=>"PRIMARY","columns"=>array(),"lengths"=>array(),"descs"=>array());preg_match_all('~((("[^"]*+")+|(?:`[^`]*+`)+)|(\S+))(\s+(ASC|DESC))?(,\s*|$)~i',$y[1],$Se,PREG_SET_ORDER);foreach($Se
as$y){$F[""]["columns"][]=idf_unescape($y[2]).$y[4];$F[""]["descs"][]=(preg_match('~DESC~i',$y[5])?'1':null);}}if(!$F){foreach(fields($Q)as$z=>$k){if($k["primary"])$F[""]=array("type"=>"PRIMARY","columns"=>array($z),"lengths"=>array(),"descs"=>array(null));}}$Rh=get_key_vals("SELECT name, sql FROM sqlite_master WHERE type = 'index' AND tbl_name = ".q($Q),$g);foreach(get_rows("PRAGMA index_list(".table($Q).")",$g)as$G){$z=$G["name"];$s=array("type"=>($G["unique"]?"UNIQUE":"INDEX"));$s["lengths"]=array();$s["descs"]=array();foreach(get_rows("PRAGMA index_info(".idf_escape($z).")",$g)as$dh){$s["columns"][]=$dh["name"];$s["descs"][]=null;}if(preg_match('~^CREATE( UNIQUE)? INDEX '.preg_quote(idf_escape($z).' ON '.idf_escape($Q),'~').' \((.*)\)$~i',$Rh[$z],$Rg)){preg_match_all('/("[^"]*+")+( DESC)?/',$Rg[2],$Se);foreach($Se[2]as$u=>$W){if($W)$s["descs"][$u]='1';}}if(!$F[""]||$s["type"]!="UNIQUE"||$s["columns"]!=$F[""]["columns"]||$s["descs"]!=$F[""]["descs"]||!preg_match("~^sqlite_~",$z))$F[$z]=$s;}return$F;}function
foreign_keys($Q){$F=array();foreach(get_rows("PRAGMA foreign_key_list(".table($Q).")")as$G){$Zc=&$F[$G["id"]];if(!$Zc)$Zc=$G;$Zc["source"][]=$G["from"];$Zc["target"][]=$G["to"];}return$F;}function
view($z){return
array("select"=>preg_replace('~^(?:[^`"[]+|`[^`]*`|"[^"]*")* AS\s+~iU','',get_val("SELECT sql FROM sqlite_master WHERE type = 'view' AND name = ".q($z))));}function
collations(){return(isset($_GET["create"])?get_vals("PRAGMA collation_list",1):array());}function
information_schema($h,$I=""){return
false;}function
error(){return
h(connection()->error);}function
check_sqlite_name($z){$Cc="db|sdb|sqlite";if(!preg_match("~^[^\\0]*\\.($Cc)\$~",$z)){connection()->error=lang(35,str_replace("|",", ",$Cc));return
false;}return
true;}function
create_database($h,$bb){if(file_exists($h)){connection()->error=lang(36);return
false;}if(!check_sqlite_name($h))return
false;try{$x=new
Db();$x->attach(server_parts(array("path"=>$h)),'','');}catch(\Exception$xc){connection()->error=$xc->getMessage();return
false;}$x->query('PRAGMA encoding = "UTF-8"');$x->query('CREATE TABLE adminer (i)');$x->query('DROP TABLE adminer');return
true;}function
drop_databases(array$Ib){connection()->attach(server_parts(array("path"=>":memory:")),'','');foreach($Ib
as$h){if(!check_sqlite_name($h))return
false;if(!@unlink($h)){connection()->error=lang(36);return
false;}}return
true;}function
rename_database($z,$bb){if(!check_sqlite_name($z))return
false;connection()->attach(server_parts(array("path"=>":memory:")),'','');connection()->error=lang(36);return@rename(DB,$z);}function
auto_increment(){return" PRIMARY KEY AUTOINCREMENT";}function
alter_table($Q,$z,array$l,array$Wc,$fb,$oc,$bb,$xa,$eg){$Zi=($Q==""||$Wc||$oc);foreach($l
as$k){if($k[0]!=""||!$k[1]||$k[2]){$Zi=true;break;}}$b=array();$Sf=array();foreach($l
as$k){if($k[1]){$b[]=($Zi?$k[1]:"ADD ".implode($k[1]));if($k[0]!="")$Sf[$k[0]]=$k[1][0];}}if(!$Zi){foreach($b
as$W){if(!queries("ALTER TABLE ".table($Q)." $W"))return
false;}if($Q!=$z&&!queries("ALTER TABLE ".table($Q)." RENAME TO ".table($z)))return
false;}elseif(!recreate_table($Q,$z,$b,$Sf,$Wc,$xa,array(),"","",$oc))return
false;if($xa){queries("BEGIN");queries("UPDATE sqlite_sequence SET seq = $xa WHERE name = ".q($z));if(!connection()->affected_rows)queries("INSERT INTO sqlite_sequence (name, seq) VALUES (".q($z).", $xa)");queries("COMMIT");}return
true;}function
recreate_table($Q,$z,array$l,array$Sf,array$Wc,$xa="",$t=array(),$ec="",$ga="",$oc=""){if($Q!=""){if(!$l){foreach(fields($Q)as$u=>$k){if($t)$k["auto_increment"]=0;$l[]=process_field($k,$k);$Sf[$u]=idf_escape($u);}}$Ag=false;foreach($l
as$k){if($k[6])$Ag=true;}$fc=array();foreach($t
as$u=>$W){if($W[2]=="DROP"){$fc[$W[1]]=true;unset($t[$u]);}}foreach(indexes($Q)as$re=>$s){$e=array();foreach($s["columns"]as$u=>$d){if(!$Sf[$d])continue
2;$e[]=$Sf[$d].($s["descs"][$u]?" DESC":"");}if(!$fc[$re]){if($s["type"]!="PRIMARY"||!$Ag)$t[]=array($s["type"],$re,$e);}}foreach($t
as$u=>$W){if($W[0]=="PRIMARY"){unset($t[$u]);$Wc[]="  PRIMARY KEY (".implode(", ",$W[2]).")";}}foreach(foreign_keys($Q)as$re=>$Zc){foreach($Zc["source"]as$u=>$d){if(!$Sf[$d])continue
2;$Zc["source"][$u]=idf_unescape($Sf[$d]);}if(!isset($Wc[" $re"]))$Wc[]=" ".format_foreign_key($Zc);}queries("BEGIN");}$Pa=array();foreach($l
as$k){if(preg_match('~GENERATED~',$k[3]))unset($Sf[array_search($k[0],$Sf)]);$Pa[]="  ".implode($k);}$Pa=array_merge($Pa,array_filter($Wc));foreach(driver()->checkConstraints($Q)as$Sa){if($Sa!=$ec)$Pa[]="  CHECK ($Sa)";}if($ga)$Pa[]="  CHECK ($ga)";$ki=($Q!=""&&$Q==$z?"adminer_$z":$z);if(!$oc&&$Q!="")$oc=idx(table_status1($Q),"Engine");if(!queries("CREATE TABLE ".table($ki)." (\n".implode(",\n",$Pa)."\n)".($oc!="table"&&in_array($oc,driver()->engines())?" $oc":"")))return
false;if($Q!=""){if($Sf&&!queries("INSERT INTO ".table($ki)." (".implode(", ",$Sf).") SELECT ".implode(", ",array_map('Adminer\idf_escape',array_keys($Sf)))." FROM ".table($Q)))return
false;$Hi=array();foreach(triggers($Q)as$Fi=>$qi){$Ei=trigger($Fi,$Q);$Hi[]="CREATE TRIGGER ".idf_escape($Fi)." ".implode(" ",$qi)." ON ".table($z)."\n$Ei[Statement]";}$xa=$xa?"":get_val("SELECT seq FROM sqlite_sequence WHERE name = ".q($Q));if(!queries("DROP TABLE ".table($Q))||($Q==$z&&!queries("ALTER TABLE ".table($ki)." RENAME TO ".table($z)))||!alter_indexes($z,$t))return
false;if($xa)queries("UPDATE sqlite_sequence SET seq = $xa WHERE name = ".q($z));foreach($Hi
as$Ei){if(!queries($Ei))return
false;}queries("COMMIT");}return
true;}function
index_sql($Q,$T,$z,$e){return"CREATE $T ".($T!="INDEX"?"INDEX ":"").idf_escape($z!=""?$z:uniqid($Q."_"))." ON ".table($Q)." $e";}function
alter_indexes($Q,$b){foreach($b
as$_g){if($_g[0]=="PRIMARY")return
recreate_table($Q,$Q,array(),array(),array(),"",$b);}foreach(array_reverse($b)as$W){if(!queries($W[2]=="DROP"?"DROP INDEX ".idf_escape($W[1]):index_sql($Q,$W[0],$W[1],"(".implode(", ",$W[2]).")")))return
false;}return
true;}function
truncate_tables(array$S){return
apply_queries("DELETE FROM",$S);}function
drop_views(array$nj){return
apply_queries("DROP VIEW",$nj);}function
drop_tables(array$S){return
apply_queries("DROP TABLE",$S);}function
move_tables(array$S,array$nj,$ii){return
false;}function
trigger($z,$Q){if($z=="")return
array("Statement"=>"BEGIN\n\t;\nEND");$r='(?:[^`"\s]+|`[^`]*`|"[^"]*")+';$Gi=trigger_options();preg_match("~^CREATE\\s+TRIGGER\\s*$r\\s*(".implode("|",$Gi["Timing"]).")\\s+([a-z]+)(?:\\s+OF\\s+($r))?\\s+ON\\s*$r\\s*(?:FOR\\s+EACH\\s+ROW\\s)?(.*)~is",get_val("SELECT sql FROM sqlite_master WHERE type = 'trigger' AND name = ".q($z)),$y);if(!$y)return
array();$Cf=$y[3];return
array("Timing"=>strtoupper($y[1]),"Event"=>strtoupper($y[2]).($Cf?" OF":""),"Of"=>idf_unescape($Cf),"Trigger"=>$z,"Statement"=>$y[4],);}function
triggers($Q){$F=array();$Gi=trigger_options();foreach(get_rows("SELECT * FROM sqlite_master WHERE type = 'trigger' AND tbl_name = ".q($Q))as$G){preg_match('~^CREATE\s+TRIGGER\s*(?:[^`"\s]+|`[^`]*`|"[^"]*")+\s*('.implode("|",$Gi["Timing"]).')\s*(.*?)\s+ON\b~i',$G["sql"],$y);$F[$G["name"]]=array($y[1],$y[2]);}return$F;}function
trigger_options(){return
array("Timing"=>array("BEFORE","AFTER","INSTEAD OF"),"Event"=>array("INSERT","UPDATE","UPDATE OF","DELETE"),"Type"=>array("FOR EACH ROW"),);}function
last_id($E){return
get_val("SELECT LAST_INSERT_ROWID()");}function
explain(Db$f,$D){return$f->query("EXPLAIN QUERY PLAN $D");}function
found_rows(array$R,array$Z){}function
types($Cc=false){return
array();}function
create_sql($Q,$xa,$Wh){$F=get_val("SELECT sql FROM sqlite_master WHERE type IN ('table', 'view') AND name = ".q($Q));foreach(indexes($Q)as$z=>$s){if($z==''||$s['type']=='FULLTEXT')continue;$F
.=";\n\n".index_sql($Q,$s['type'],$z,"(".implode(", ",array_map('Adminer\idf_escape',$s['columns'])).")");}return$F;}function
truncate_sql($Q){return"DELETE FROM ".table($Q);}function
use_sql($Hb,$Wh=""){return"";}function
trigger_sql($Q){return
implode(get_vals("SELECT sql || ';;\n' FROM sqlite_master WHERE type = 'trigger' AND tbl_name = ".q($Q)));}function
show_variables(){$F=array();foreach(get_rows("PRAGMA pragma_list")as$G){$z=$G["name"];if($z!="pragma_list"&&$z!="compile_options"){$F[$z]=array($z,'');foreach(get_rows("PRAGMA $z")as$G)$F[$z][1].=implode(", ",$G)."\n";}}return$F;}function
show_status(){$F=array();foreach(get_vals("PRAGMA compile_options")as$Kf)$F[]=explode("=",$Kf,2)+array('','');return$F;}function
convert_field(array$k){}function
unconvert_field(array$k,$F){return$F;}function
support($Gc){return
preg_match('~^(check|columns|database|drop_col|dump|fast_status|indexes|descidx|move_col|sql|status|table|transaction_ddl|trigger|variables|view|view_trigger)$~',$Gc);}}add_driver("mssql","MS SQL");if(isset($_GET["mssql"])){define('Adminer\DRIVER',"mssql");if(extension_loaded("sqlsrv")&&$_GET["ext"]!="pdo"){class
Db
extends
SqlDb{var$extension="sqlsrv";private$link,$result,$warnings,$transaction=false;private
function
get_error(){$this->error="";foreach(sqlsrv_errors()as$j){$this->errno=$j["code"];$this->error
.="$j[message]\n";}$this->error=rtrim($this->error);}function
attach(array$L,$U,$C){sqlsrv_configure("WarningsReturnAsErrors",0);$mb=array("UID"=>$U,"PWD"=>$C,"CharacterSet"=>"UTF-8");if(isset($_GET["sql"])&&!self::$instance)$mb["MultipleActiveResultSets"]=false;$N=adminer()->connectSsl();if(isset($N["Encrypt"]))$mb["Encrypt"]=$N["Encrypt"];if(isset($N["TrustServerCertificate"]))$mb["TrustServerCertificate"]=$N["TrustServerCertificate"];$h=adminer()->database();if($h!="")$mb["Database"]=$h;$rg=$L["port"];$this->link=@sqlsrv_connect($L["host"].($rg?",$rg":""),$mb);if($this->link){$Wd=sqlsrv_server_info($this->link);$this->server_info=$Wd['SQLServerVersion'];}else$this->get_error();return($this->link?'':$this->error);}function
quote($P){return
unicode_prefix($P)."'".str_replace("'","''",$P)."'";}function
select_db($Hb){return$this->query(use_sql($Hb));}function
query($D,$Mi=false){$E=sqlsrv_query($this->link,$D);$this->error="";if(!$E){$this->get_error();return
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
as$qj)$F[]=$qj["message"];return$F;}function
inTransaction(){return$this->transaction||(isset($_GET["sql"])&&get_val("SELECT @@TRANCOUNT",0,$this));}function
begin(){$this->transaction=sqlsrv_begin_transaction($this->link);if(!$this->transaction)$this->get_error();return$this->transaction;}function
commit(){if($this->transaction&&!sqlsrv_commit($this->link)){$this->get_error();return
false;}$this->transaction=false;return
true;}function
rollback(){if(!$this->transaction&&isset($_GET["sql"]))return!!$this->query("IF @@TRANCOUNT > 0 ROLLBACK");if($this->transaction&&!sqlsrv_rollback($this->link)){$this->get_error();return
false;}$this->transaction=false;return
true;}}class
Result{var$num_rows;private$result,$offset=0,$fields;function
__construct($E){$this->result=$E;}private
function
convert($G){foreach((array)$G
as$u=>$W){if(is_a($W,'DateTime'))$G[$u]=$W->format("Y-m-d H:i:s");}return$G;}function
fetch_assoc(){return$this->convert(sqlsrv_fetch_array($this->result,SQLSRV_FETCH_ASSOC));}function
fetch_row(){return$this->convert(sqlsrv_fetch_array($this->result,SQLSRV_FETCH_NUMERIC));}function
fetch_field(){if(!$this->fields)$this->fields=sqlsrv_field_metadata($this->result);$Li=array(-155=>"datetimeoffset","time",-152=>"xml","varbinary","sql_variant",-11=>"uniqueidentifier","ntext","nvarchar","nchar","bit","tinyint","bigint","image","varbinary","binary","text",1=>"char","numeric","decimal","int","smallint","float","real","float",12=>"varchar",91=>"date","time","datetime",);$k=$this->fields[$this->offset++];$F=new
\stdClass;$F->name=$k["Name"];$F->native_type=idx($Li,$k["Type"],"");return$F;}function
seek($Df){for($p=0;$p<$Df;$p++)sqlsrv_fetch($this->result);}}function
last_id($E){return(string)get_val("SELECT SCOPE_IDENTITY()");}function
explain(Db$f,$D){$f->query("SET SHOWPLAN_ALL ON");$F=$f->query($D);$f->query("SET SHOWPLAN_ALL OFF");return$F;}}else{abstract
class
MssqlDb
extends
PdoDb{function
quote($P){return
unicode_prefix($P).parent::quote($P);}function
select_db($Hb){return$this->query(use_sql($Hb));}function
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
attach(array$L,$U,$C){$rg=$L["port"];$gc="sqlsrv:Server=$L[host]".($rg?",$rg":"").(isset($_GET["sql"])&&!self::$instance?";MultipleActiveResultSets=0":"");$N=adminer()->connectSsl();foreach(array("Encrypt","TrustServerCertificate")as$u){if(isset($N[$u]))$gc
.=";$u=".($N[$u]?1:0);}return$this->dsn($gc,$U,$C,array(\PDO::SQLSRV_ATTR_DIRECT_QUERY=>true));}function
inTransaction(){return
parent::inTransaction()||(isset($_GET["sql"])&&get_val("SELECT @@TRANCOUNT",0,$this));}function
rollback(){return(parent::inTransaction()||!isset($_GET["sql"])?parent::rollback():!!$this->query("IF @@TRANCOUNT > 0 ROLLBACK"));}}}elseif(extension_loaded("pdo_dblib")){class
Db
extends
MssqlDb{var$extension="PDO_DBLIB";function
attach(array$L,$U,$C){$rg=$L["port"];$Jh=$L["socket"];return$this->dsn("dblib:charset=utf8;host=$L[host]".($rg!=""?";port=$rg":($Jh!=""?";unix_socket=$Jh":"")),$U,$C);}}}}class
Driver
extends
SqlDriver{static$extensions=array("SQLSRV","PDO_SQLSRV","PDO_DBLIB");static$jush="mssql";static$serverSocket=true;var$insertFunctions=array("date|time"=>"getdate");var$editFunctions=array("int|decimal|real|float|money|datetime"=>"+/-","char|text"=>"+",);var$functions=array("len","lower","round","upper");var$grouping=array("avg","count","count distinct","max","min","sum");var$generated=array("PERSISTED","VIRTUAL");var$onActions="NO ACTION|CASCADE|SET NULL|SET DEFAULT";var$inout="|OUTPUT";private$unknownTypes=array();function
operators($ci){return
array("=","<",">","<=",">=","!=","LIKE","LIKE %%","IN","IS NULL","NOT LIKE","NOT IN","IS NOT NULL");}static
function
connect($L,$U,$C){if($L=="")$L="localhost:1433";return
parent::connect($L,$U,$C);}function
__construct(Db$f){parent::__construct($f);$this->types=array(lang(28)=>array("tinyint"=>3,"smallint"=>5,"int"=>10,"bigint"=>20,"bit"=>1,"decimal"=>0,"numeric"=>0,"real"=>12,"float"=>53,"smallmoney"=>10,"money"=>20,"vector"=>0,),lang(29)=>array("date"=>10,"smalldatetime"=>19,"datetime"=>19,"datetime2"=>19,"time"=>8,"datetimeoffset"=>26),lang(30)=>array("char"=>8000,"varchar"=>8000,"text"=>2147483647,"nchar"=>4000,"nvarchar"=>4000,"ntext"=>1073741823,"uniqueidentifier"=>36,"xml"=>2147483647,"json"=>2147483647,"sql_variant"=>8000,"hierarchyid"=>892,),lang(31)=>array("binary"=>8000,"varbinary"=>8000,"image"=>2147483647),lang(33)=>array("geometry"=>0,"geography"=>0),);$Li=array_flip(get_vals("SELECT name FROM sys.types WHERE is_user_defined = 0 ORDER BY name"));if($Li){foreach($this->types
as$o=>$rd){foreach($rd
as$T=>$v){if(isset($Li[$T]))unset($Li[$T]);else
unset($this->types[$o][$T]);}if(!$this->types[$o])unset($this->types[$o]);}$this->unknownTypes=array_keys($Li);}}function
types(){return
parent::types()+array_fill_keys($this->unknownTypes,0);}function
structuredTypes(){return
array_merge(parent::structuredTypes(),$this->unknownTypes);}function
typeName(\stdClass$k){return
idx((array)$k,'sqlsrv:decl_type',parent::typeName($k));}function
insertUpdate($Q,array$H,array$_g){$l=fields($Q);$Vi=array();$Z=array();$M=reset($H);$e="c".implode(", c",range(1,count($M)));$Na=0;$ae=array();foreach($M
as$u=>$W){$Na++;$z=idf_unescape($u);if(!$l[$z]["auto_increment"])$ae[$u]="c$Na";if(isset($_g[$z]))$Z[]="$u = c$Na";else$Vi[]="$u = c$Na";}$Y=array();foreach($H
as$M)$Y[]="(".implode(", ",$M).")";if($Z){$Md=queries("SET IDENTITY_INSERT ".table($Q)." ON");$F=queries("MERGE ".table($Q)." USING (VALUES\n\t".implode(",\n\t",$Y)."\n) AS source ($e) ON ".implode(" AND ",$Z).($Vi?"\nWHEN MATCHED THEN UPDATE SET ".implode(", ",$Vi):"")."\nWHEN NOT MATCHED THEN INSERT (".implode(", ",array_keys($Md?$M:$ae)).") VALUES (".($Md?$e:implode(", ",$ae)).");");if($Md)queries("SET IDENTITY_INSERT ".table($Q)." OFF");}else$F=queries("INSERT INTO ".table($Q)." (".implode(", ",array_keys($M)).") VALUES\n".implode(",\n",$Y));return$F;}function
begin(){remember_query("BEGIN TRANSACTION");return$this->conn->begin();}function
convertSearch($r,array$W,array$k){return(preg_match('~^(bit|n?text|xml|json|vector|uniqueidentifier|sql_variant|hierarchyid|geography|geometry)$~',$k["type"])?"CAST($r AS nvarchar(max))":$r);}function
quoteBinary($eh){return"0x".bin2hex($eh);}function
warnings(){$F=array();foreach($this->conn->warnings()as$ff){$ff=trim(preg_replace('~^(\[[^]]+])+~','',$ff));if($ff!="")$F[]=$ff;}return
nl_br(h(implode("\n",$F)));}function
tableHelp($z,$ne=false){$Ie=array("sys"=>"catalog-views/sys-","INFORMATION_SCHEMA"=>"information-schema-views/",);$x=$Ie[get_schema()];if($x)return"relational-databases/system-$x".preg_replace('~_~','-',strtolower($z))."-transact-sql";}}function
unicode_prefix($P){return(strlen($P)!=utf8_length($P)?"N":"");}function
idf_escape($r){return"[".str_replace("]","]]",$r)."]";}function
table($r){return($_GET["ns"]!=""?idf_escape($_GET["ns"]).".":"").idf_escape($r);}function
get_databases($Uc){return
get_vals("SELECT name FROM sys.databases WHERE name NOT IN ('master', 'tempdb', 'model', 'msdb')");}function
limit($D,$Z,$w,$Df=0,$K=" "){return($w?" TOP (".($w+$Df).")":"")." $D$Z";}function
limit1($Q,$D,$Z,$K="\n"){return
limit($D,$Z,1,0,$K);}function
db_collation($h,array$cb){return
get_val("SELECT collation_name FROM sys.databases WHERE name = ".q($h));}function
logged_user(){return
get_val("SELECT SUSER_NAME()");}function
tables_list(){return
get_key_vals("SELECT name, type_desc FROM sys.all_objects WHERE schema_id = SCHEMA_ID(".q(get_schema()).") AND type IN ('S', 'U', 'V') ORDER BY name");}function
count_tables(array$Ib){$F=array();foreach($Ib
as$h){connection()->select_db($h);$F[$h]=get_val("SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES");}return$F;}function
table_status($z="",$Fc=false){$F=array();$Hh=array();foreach(get_rows("SELECT object_id, SUM(CASE WHEN index_id < 2 THEN row_count ELSE 0 END) AS [Rows],
SUM(CASE WHEN index_id < 2 THEN used_page_count ELSE 0 END) * 8192 AS Data_length,
SUM(CASE WHEN index_id > 1 THEN used_page_count ELSE 0 END) * 8192 AS Index_length,
SUM(reserved_page_count - used_page_count) * 8192 AS Data_free
FROM sys.dm_db_partition_stats
GROUP BY object_id",null,"")as$G){$Bf=$G["object_id"];unset($G["object_id"]);$Hh[$Bf]=$G;}foreach(get_rows("SELECT ao.object_id, ao.name AS Name, ao.type_desc AS Engine,
	(SELECT cast(value as varchar(max)) FROM fn_listextendedproperty(default, 'SCHEMA', schema_name(schema_id), 'TABLE', ao.name, null, null)) AS Comment
FROM sys.all_objects AS ao
WHERE schema_id = SCHEMA_ID(".q(get_schema()).") AND type IN ('S', 'U', 'V') ".($z!=""?"AND name = ".q($z):"ORDER BY name"))as$G){$Bf=$G["object_id"];unset($G["object_id"]);$F[$G["Name"]]=$G+idx($Hh,$Bf,array());}return$F;}function
is_view(array$R){return$R["Engine"]=="VIEW";}function
fk_support(array$R){return
true;}function
type_length($T,array$G){return(preg_match("~char|binary~",$T)?($G["max_length"]==-1?"max":intval($G["max_length"])/($T[0]=='n'?2:1)):($T=="decimal"?"$G[precision],$G[scale]":($T=="vector"?(intval($G["max_length"])-8)/4:"")));}function
fields($Q){$gb=get_key_vals("SELECT objname, cast(value as varchar(max)) FROM fn_listextendedproperty('MS_DESCRIPTION', 'schema', ".q(get_schema()).", 'table', ".q($Q).", 'column', NULL)");$F=array();$di=get_val("SELECT object_id FROM sys.all_objects WHERE schema_id = SCHEMA_ID(".q(get_schema()).") AND type IN ('S', 'U', 'V') AND name = ".q($Q));foreach(get_rows("SELECT c.max_length, c.precision, c.scale, c.name, c.is_nullable, c.is_identity, c.collation_name,
	COALESCE(bt.name, t.name) type, d.definition [default], d.name default_constraint, i.is_primary_key
FROM sys.all_columns c
JOIN sys.types t ON c.user_type_id = t.user_type_id
LEFT JOIN sys.types bt ON t.system_type_id = bt.user_type_id AND t.is_user_defined = 1
LEFT JOIN sys.default_constraints d ON c.default_object_id = d.object_id
LEFT JOIN sys.index_columns ic ON c.object_id = ic.object_id AND c.column_id = ic.column_id
LEFT JOIN sys.indexes i ON ic.object_id = i.object_id AND ic.index_id = i.index_id
WHERE c.object_id = ".q($di))as$G){$T=$G["type"];$v=type_length($T,$G);$F[$G["name"]]=array("field"=>$G["name"],"full_type"=>$T.($v?"($v)":""),"type"=>$T,"length"=>$v,"default"=>(preg_match("~^\(N?'(.*)'\)$~s",$G["default"],$y)?str_replace("''","'",$y[1]):$G["default"]),"default_constraint"=>$G["default_constraint"],"null"=>$G["is_nullable"],"auto_increment"=>$G["is_identity"],"collation"=>$G["collation_name"],"privileges"=>array("insert"=>1,"select"=>1,"update"=>1,"where"=>1,"order"=>1),"primary"=>$G["is_primary_key"],"comment"=>$gb[$G["name"]],);}foreach(get_rows("SELECT * FROM sys.computed_columns WHERE object_id = ".q($di))as$G){$F[$G["name"]]["generated"]=($G["is_persisted"]?"PERSISTED":"VIRTUAL");$F[$G["name"]]["default"]=$G["definition"];}return$F;}function
indexes($Q,$g=null){$F=array();foreach(get_rows("SELECT i.name, key_ordinal, is_unique, is_primary_key, c.name AS column_name, is_descending_key
FROM sys.indexes i
INNER JOIN sys.index_columns ic ON i.object_id = ic.object_id AND i.index_id = ic.index_id
INNER JOIN sys.columns c ON ic.object_id = c.object_id AND ic.column_id = c.column_id
WHERE OBJECT_NAME(i.object_id) = ".q($Q),$g)as$G){$z=$G["name"];$F[$z]["type"]=($G["is_primary_key"]?"PRIMARY":($G["is_unique"]?"UNIQUE":"INDEX"));$F[$z]["lengths"]=array();$F[$z]["columns"][$G["key_ordinal"]]=$G["column_name"];$F[$z]["descs"][$G["key_ordinal"]]=($G["is_descending_key"]?'1':null);}return$F;}function
view($z){return
array("select"=>preg_replace('~^(?:[^[]|\[[^]]*])*\s+AS\s+~isU','',get_val("SELECT VIEW_DEFINITION FROM INFORMATION_SCHEMA.VIEWS WHERE TABLE_SCHEMA = SCHEMA_NAME() AND TABLE_NAME = ".q($z))));}function
collations(){$F=array();foreach(get_vals("SELECT name FROM fn_helpcollations()")as$bb)$F[preg_replace('~_.*~','',$bb)][]=$bb;return$F;}function
information_schema($h,$I=""){return
in_array($I!=""?$I:get_schema(),array("INFORMATION_SCHEMA","sys"));}function
error(){return
nl_br(h(preg_replace('~^(\[[^]]*])+~m','',connection()->error)));}function
create_database($h,$bb){return
queries("CREATE DATABASE ".idf_escape($h).(preg_match('~^[a-z0-9_]+$~i',$bb)?" COLLATE $bb":""));}function
drop_databases(array$Ib){return!!queries("DROP DATABASE ".implode(", ",array_map('Adminer\idf_escape',$Ib)));}function
rename_database($z,$bb){if(preg_match('~^[a-z0-9_]+$~i',$bb))queries("ALTER DATABASE ".idf_escape(DB)." COLLATE $bb");queries("ALTER DATABASE ".idf_escape(DB)." MODIFY NAME = ".idf_escape($z));return
true;}function
auto_increment(){return" IDENTITY".($_POST["Auto_increment"]!=""?"(".number($_POST["Auto_increment"]).",1)":"")." PRIMARY KEY";}function
alter_table($Q,$z,array$l,array$Wc,$fb,$oc,$bb,$xa,$eg){$b=array();$gb=array();$Qf=fields($Q);foreach($l
as$k){$d=idf_escape($k[0]);$W=$k[1];if(!$W)$b["DROP"][]=" COLUMN $d";else{$W[1]=preg_replace("~( COLLATE )'(\\w+)'~",'\1\2',$W[1]);$gb[$k[0]]=$W[5];unset($W[5]);if(preg_match('~ AS ~',$W[3]))unset($W[1],$W[2]);if($k[0]=="")$b["ADD"][]="\n  ".implode("",$W).($Q==""?substr($Wc[$W[0]],16+strlen($W[0])):"");else{$i=$W[3];unset($W[3]);unset($W[6]);if($d!=$W[0])queries("EXEC sp_rename ".q(table($Q).".$d").", ".q(idf_unescape($W[0])).", 'COLUMN'");$b["ALTER COLUMN ".implode("",$W)][]="";$Pf=$Qf[$k[0]];if(default_value($Pf)!=$i){if($Pf["default"]!==null)$b["DROP"][]=" ".idf_escape($Pf["default_constraint"]);if($i)$b["ADD"][]="\n $i FOR $d";}}}}if($Q==""){$fa=(array)$b["ADD"];foreach($Wc
as$u=>$W){if(!is_string($u))$fa[]="\n$W";}return
queries("CREATE TABLE ".table($z)." (".implode(",",$fa)."\n)");}if($Q!=$z)queries("EXEC sp_rename ".q(table($Q)).", ".q($z));if($Wc)$b[""]=$Wc;foreach($b
as$u=>$W){if(!queries("ALTER TABLE ".table($z)." $u".implode(",",$W)))return
false;}foreach($gb
as$u=>$W){$fb=substr($W,9);queries("EXEC sp_dropextendedproperty @name = N'MS_Description', @level0type = N'Schema', @level0name = ".q(get_schema()).", @level1type = N'Table', @level1name = ".q($z).", @level2type = N'Column', @level2name = ".q($u));queries("EXEC sp_addextendedproperty
@name = N'MS_Description',
@value = $fb,
@level0type = N'Schema',
@level0name = ".q(get_schema()).",
@level1type = N'Table',
@level1name = ".q($z).",
@level2type = N'Column',
@level2name = ".q($u));}return
true;}function
alter_indexes($Q,$b){$s=array();$dc=array();foreach($b
as$W){if($W[2]=="DROP"){if($W[0]=="PRIMARY")$dc[]=idf_escape($W[1]);else$s[]=idf_escape($W[1])." ON ".table($Q);}elseif(!queries(($W[0]!="PRIMARY"?"CREATE $W[0] ".($W[0]!="INDEX"?"INDEX ":"").idf_escape($W[1]!=""?$W[1]:uniqid($Q."_"))." ON ".table($Q):"ALTER TABLE ".table($Q)." ADD PRIMARY KEY")." (".implode(", ",$W[2]).")"))return
false;}return(!$s||queries("DROP INDEX ".implode(", ",$s)))&&(!$dc||queries("ALTER TABLE ".table($Q)." DROP ".implode(", ",$dc)));}function
found_rows(array$R,array$Z){}function
foreign_keys($Q){$F=array();$Hf=array("CASCADE","NO ACTION","SET NULL","SET DEFAULT");$I=get_schema();foreach(get_rows("EXEC sp_fkeys @fktable_name = ".q($Q).", @fktable_owner = ".q($I))as$G){$Zc=&$F[$G["FK_NAME"]];$Zc["db"]=($G["PKTABLE_QUALIFIER"]==DB?"":$G["PKTABLE_QUALIFIER"]);$Zc["ns"]=($G["PKTABLE_OWNER"]==$I?"":$G["PKTABLE_OWNER"]);$Zc["table"]=$G["PKTABLE_NAME"];$Zc["on_update"]=$Hf[$G["UPDATE_RULE"]];$Zc["on_delete"]=$Hf[$G["DELETE_RULE"]];$Zc["source"][]=$G["FKCOLUMN_NAME"];$Zc["target"][]=$G["PKCOLUMN_NAME"];}return$F;}function
truncate_tables(array$S){return
apply_queries("TRUNCATE TABLE",$S);}function
drop_views(array$nj){return
queries("DROP VIEW ".implode(", ",array_map('Adminer\table',$nj)));}function
drop_tables(array$S){return
queries("DROP TABLE ".implode(", ",array_map('Adminer\table',$S)));}function
move_tables(array$S,array$nj,$ii){return
apply_queries("ALTER SCHEMA ".idf_escape($ii)." TRANSFER",array_merge($S,$nj));}function
trigger($z,$Q){if($z=="")return
array();$H=get_rows("SELECT s.name [Trigger],
CASE WHEN OBJECTPROPERTY(s.id, 'ExecIsInsertTrigger') = 1 THEN 'INSERT'
	WHEN OBJECTPROPERTY(s.id, 'ExecIsUpdateTrigger') = 1 THEN 'UPDATE'
	WHEN OBJECTPROPERTY(s.id, 'ExecIsDeleteTrigger') = 1 THEN 'DELETE' END [Event],
CASE WHEN OBJECTPROPERTY(s.id, 'ExecIsInsteadOfTrigger') = 1 THEN 'INSTEAD OF' ELSE 'AFTER' END [Timing],
c.text
FROM sysobjects s
JOIN syscomments c ON s.id = c.id
WHERE s.xtype = 'TR' AND s.name = ".q($z));$F=reset($H);if($F)$F["Statement"]=preg_replace('~^.+\s+AS\s+~isU','',$F["text"]);return($F?:array());}function
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
routine($z,$T){$Mb=get_val("SELECT m.definition
FROM sys.objects o
JOIN sys.sql_modules m ON m.object_id = o.object_id
WHERE o.schema_id = SCHEMA_ID(".q(get_schema()).") AND o.name = ".q($z)." AND o.type = ".q($T=="PROCEDURE"?"P":"FN"));if(!$Mb)return
array();$F=array("definition"=>preg_replace('~^(?:[^[]|\[[^]]*])*\s+AS\s+~isU','',$Mb),"fields"=>array());foreach(get_rows("SELECT p.name, TYPE_NAME(p.user_type_id) [type], p.max_length, p.precision, p.scale, p.is_output
FROM sys.parameters p
JOIN sys.objects o ON p.object_id = o.object_id
WHERE o.schema_id = SCHEMA_ID(".q(get_schema()).") AND o.name = ".q($z)."
ORDER BY p.parameter_id")as$G){$Ic=$G["type"];$v=type_length($Ic,$G);$k=array("field"=>preg_replace('~^@~','',$G["name"]),"type"=>$Ic,"length"=>$v,"full_type"=>$Ic.($v?"($v)":""),"null"=>true,"inout"=>($G["is_output"]?"OUTPUT":""),);if($k["field"]=="")$F["returns"]=$k;else$F["fields"][]=$k;}return$F;}function
routines(){return
get_rows("SELECT o.name SPECIFIC_NAME, o.name ROUTINE_NAME,
	CASE o.type WHEN 'P' THEN 'PROCEDURE' ELSE 'FUNCTION' END ROUTINE_TYPE, TYPE_NAME(p.user_type_id) DTD_IDENTIFIER
FROM sys.objects o
LEFT JOIN sys.parameters p ON o.object_id = p.object_id AND p.parameter_id = 0
WHERE o.schema_id = SCHEMA_ID(".q(get_schema()).") AND o.type IN ('P', 'FN')
ORDER BY o.name");}function
routine_languages(){return
array();}function
routine_options($ch){return
array();}function
routine_id($z,array$G){return
table($z);}function
schemas(){return
get_vals("SELECT name FROM sys.schemas");}function
get_schema(){if($_GET["ns"]!="")return$_GET["ns"];return
get_val("SELECT SCHEMA_NAME()");}function
set_schema($I,$g=null){$_GET["ns"]=$I;return!!get_val("SELECT 1 FROM sys.schemas WHERE name = ".q($I),0,$g);}function
create_sql($Q,$xa,$Wh){if(is_view(table_status1($Q))){$mj=view($Q);return"CREATE VIEW ".table($Q)." AS $mj[select]";}$l=array();$_g=false;foreach(fields($Q)as$z=>$k){$W=process_field($k,$k);if($W[6])$_g=true;$l[]=implode("",$W);}foreach(indexes($Q)as$z=>$s){if(!$_g||$s["type"]!="PRIMARY"){$e=array();foreach($s["columns"]as$u=>$W)$e[]=idf_escape($W).($s["descs"][$u]?" DESC":"");$z=idf_escape($z);$l[]=($s["type"]=="INDEX"?"INDEX $z":"CONSTRAINT $z ".($s["type"]=="UNIQUE"?"UNIQUE":"PRIMARY KEY"))." (".implode(", ",$e).")";}}foreach(driver()->checkConstraints($Q)as$z=>$Sa)$l[]="CONSTRAINT ".idf_escape($z)." CHECK ($Sa)";return"CREATE TABLE ".table($Q)." (\n\t".implode(",\n\t",$l)."\n)";}function
foreign_keys_sql($Q){$l=array();foreach(foreign_keys($Q)as$Wc)$l[]=ltrim(format_foreign_key($Wc));return($l?"ALTER TABLE ".table($Q)." ADD\n\t".implode(",\n\t",$l).";\n\n":"");}function
truncate_sql($Q){return"TRUNCATE TABLE ".table($Q);}function
use_sql($Hb,$Wh=""){return"USE ".idf_escape($Hb);}function
use_schema_sql($I,$Wh){$z=idf_escape($I);return($Wh=="DROP+CREATE"?"DROP SCHEMA IF EXISTS $z;\n":"")."IF SCHEMA_ID(".q($I).") IS NULL EXEC(".q("CREATE SCHEMA $z").")";}function
trigger_sql($Q){$F="";foreach(triggers($Q)as$z=>$Ei)$F
.=create_trigger(" ON ".table($Q),trigger($z,$Q)).";";return$F;}function
convert_field(array$k){}function
unconvert_field(array$k,$F){return$F;}function
support($Gc){return
preg_match('~^(check|comment|columns|database|drop_col|dump|fast_status|indexes|descidx|procedure|routine|scheme|sql|table|transaction_ddl|trigger|view|view_trigger)$~',$Gc);}}add_driver("oracle","Oracle");if(isset($_GET["oracle"])){define('Adminer\DRIVER',"oracle");function
easy_connect(array$L){return
url_host($L["host"]).($L["port"]!=""?":$L[port]":"").$L["path"];}if(extension_loaded("oci8")&&$_GET["ext"]!="pdo"){class
Db
extends
SqlDb{var$extension="oci8";private$link,$transaction=false;function
_error($rc,$j){if(ini_bool("html_errors"))$j=html_entity_decode(strip_tags($j));$j=preg_replace('~^[^:]*: ~','',$j);$this->error=$j;}function
attach(array$L,$U,$C){$this->link=@oci_new_connect($U,$C,easy_connect($L),"AL32UTF8");if($this->link){$this->server_info=oci_server_version($this->link);return'';}$j=oci_error();return($j?$j["message"]:lang(25));}function
quote($P){return"'".str_replace("'","''",$P)."'";}function
select_db($Hb){return$this->query("ALTER SESSION SET CURRENT_SCHEMA = ".idf_escape($Hb));}function
query($D,$Mi=false){$E=oci_parse($this->link,$D);$this->error="";if(!$E){$j=oci_error($this->link);$this->errno=$j["code"];$this->error=$j["message"];return
false;}set_error_handler(array($this,'_error'));$F=@oci_execute($E,($this->transaction?OCI_NO_AUTO_COMMIT:OCI_COMMIT_ON_SUCCESS));restore_error_handler();if($F){if(oci_num_fields($E))return
new
Result($E);$this->affected_rows=oci_num_rows($E);oci_free_statement($E);}return$F;}function
timeout($of){return
function_exists('oci_set_call_timeout')&&oci_set_call_timeout($this->link,$of);}function
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
as$u=>$W){if(is_a($W,'OCILob')||is_a($W,'OCI-Lob'))$G[$u]=$W->load();}return$G;}function
fetch_assoc(){return$this->convert(oci_fetch_assoc($this->result));}function
fetch_row(){return$this->convert(oci_fetch_row($this->result));}function
fetch_field(){$d=$this->offset++;$F=new
\stdClass;$F->name=oci_field_name($this->result,$d);$T=oci_field_type($this->result,$d);$F->native_type=idx(array(100=>"binary_float","binary_double"),$T,$T);return$F;}}}elseif(extension_loaded("pdo_oci")){class
Db
extends
PdoDb{var$extension="PDO_OCI";function
attach(array$L,$U,$C){return$this->dsn("oci:dbname=//".easy_connect($L).";charset=AL32UTF8",$U,$C);}function
select_db($Hb){return$this->query("ALTER SESSION SET CURRENT_SCHEMA = ".idf_escape($Hb));}}}class
Driver
extends
SqlDriver{static$extensions=array("OCI8","PDO_OCI");static$jush="oracle";static$serverPath=true;var$insertFunctions=array("date"=>"current_date","timestamp"=>"current_timestamp",);var$editFunctions=array("number|float|double"=>"+/-","date|timestamp"=>"+ interval/- interval","char|clob"=>"||",);var$functions=array("length","lower","round","upper");var$grouping=array("avg","count","count distinct","max","min","sum");function
operators($ci){return
array("=","<",">","<=",">=","!=","LIKE","LIKE %%","IN","IS NULL","NOT LIKE","NOT IN","IS NOT NULL","SQL");}static
function
connect($L,$U,$C){$f=parent::connect($L,$U,$C);if(is_object($f))$f->query("ALTER SESSION SET CURSOR_SHARING = FORCE"." NLS_DATE_FORMAT = 'YYYY-MM-DD HH24:MI:SS'"." NLS_TIMESTAMP_FORMAT = 'YYYY-MM-DD HH24:MI:SS.FF'"." NLS_TIMESTAMP_TZ_FORMAT = 'YYYY-MM-DD HH24:MI:SS.FF TZH:TZM'");return$f;}function
__construct(Db$f){parent::__construct($f);$this->types=array(lang(28)=>array("number"=>38,"binary_float"=>12,"binary_double"=>21),lang(29)=>array("date"=>10,"timestamp"=>29,"interval year"=>12,"interval day"=>28),lang(30)=>array("char"=>2000,"varchar2"=>4000,"nchar"=>2000,"nvarchar2"=>4000,"clob"=>4294967295,"nclob"=>4294967295),lang(31)=>array("raw"=>2000,"long raw"=>2147483648,"blob"=>4294967295,"bfile"=>4294967296),lang(33)=>array("sdo_geometry"=>0),);}function
begin(){return$this->conn->begin();}function
convertSearch($r,array$W,array$k){$T=$k["type"];$lg=strpos($W["op"],"LIKE")!==false;if($T=="xmltype")return"XMLSERIALIZE(CONTENT $r AS VARCHAR2(4000))";if($T=="json")return"JSON_SERIALIZE($r)";if(preg_match('~^(date|timestamp)~',$T))return"TO_CHAR($r, 'YYYY-MM-DD HH24:MI:SS')";if(preg_match('~char~',$T)||(preg_match('~clob~',$T)&&$lg))return$r;return(!$lg&&preg_match(number_type(),$T)?$r:"TO_CHAR($r)");}function
quoteBinary($eh){return"HEXTORAW(".q(bin2hex($eh)).")";}function
typeName(\stdClass$k){return
strtolower(parent::typeName($k));}function
hasCStyleEscapes(){return
true;}function
select($Q,array$J,array$Z,array$o,array$Mf=array(),$w=1,$B=0,$Bg=false){if(in_array("*",$J)){$ub=array();$cd=false;foreach(fields($Q)as$z=>$k){$ua=convert_field($k);$cd=($cd||$ua);$ub[]=($ua?"$ua AS ":"").idf_escape($z);}if($cd)$J=$ub;}return
parent::select($Q,$J,$Z,$o,$Mf,$w,$B,$Bg);}function
allFields(){$F=array();$H=get_rows('SELECT c.table_name "tab", c.column_name "field", c.data_type "type", c.nullable "nullable",
	c.data_precision "precision", c.data_scale "scale", c.char_col_decl_length "char_length"
FROM all_tab_columns c
WHERE '.where_owner("c.owner").'
ORDER BY c.table_name, c.column_id',$this->conn);foreach($H
as$G){$v="$G[precision],$G[scale]";$G["length"]=($v==","?$G["char_length"]:$v);$G["type"]=strtolower($G["type"]);$G["null"]=($G["nullable"]=="Y");$F[$G["tab"]][]=$G;}return$F;}}function
idf_escape($r){return'"'.str_replace('"','""',$r).'"';}function
table($r){return
idf_escape($r);}function
get_databases($Uc){$F=get_vals("SELECT username FROM all_users WHERE oracle_maintained = 'N' ORDER BY 1");return($F?:get_vals("SELECT username FROM all_users ORDER BY 1"));}function
limit($D,$Z,$w,$Df=0,$K=" "){return($Df?" * FROM (SELECT t.*, rownum AS rnum FROM (SELECT $D$Z) t WHERE rownum <= ".($w+$Df).") WHERE rnum > $Df":($w?" * FROM (SELECT $D$Z) WHERE rownum <= ".($w+$Df):" $D$Z"));}function
limit1($Q,$D,$Z,$K="\n"){return" $D$Z";}function
db_collation($h,array$cb){return
get_val("SELECT value FROM nls_database_parameters WHERE parameter = 'NLS_CHARACTERSET'");}function
logged_user(){return
get_val("SELECT USER FROM DUAL");}function
where_owner($Vf="owner"){return"$Vf = ".q(DB);}function
views_table($e){return"(SELECT $e FROM all_views WHERE ".where_owner().")";}function
objects_table(){return"(SELECT object_name, DECODE(object_type, 'VIEW', 'view', 'table') object_type FROM all_objects WHERE ".where_owner()." AND object_type IN ('TABLE', 'VIEW'))";}function
tables_list(){return
get_key_vals("SELECT * FROM ".objects_table()." ORDER BY 1");}function
count_tables(array$Ib){$F=array();foreach($Ib
as$h)$F[$h]=get_val("SELECT COUNT(*) FROM all_objects WHERE object_type IN ('TABLE', 'VIEW') AND owner = ".q($h));return$F;}function
table_status($z="",$Fc=false){$F=array();$gh=q($z);if($Fc||$z!=""){foreach(get_rows('SELECT object_name "Name", object_type "Engine" FROM '.objects_table().($z!=""?" WHERE object_name = $gh":"").' ORDER BY 1')as$G)$F[$G["Name"]]=$G;return$F;}foreach(get_rows('SELECT t.table_name "Name", \'table\' "Engine", s.bytes "Data_length", i.bytes "Index_length", t.num_rows "Rows"
FROM all_tables t
LEFT JOIN (SELECT segment_name, SUM(bytes) bytes FROM user_segments WHERE segment_type LIKE \'TABLE%\' GROUP BY segment_name) s ON s.segment_name = t.table_name
LEFT JOIN (SELECT i.table_name, SUM(s.bytes) bytes FROM user_indexes i
	JOIN user_segments s ON s.segment_name = i.index_name AND s.segment_type LIKE \'INDEX%\' GROUP BY i.table_name) i ON i.table_name = t.table_name
WHERE '.where_owner("t.owner")."
UNION SELECT view_name, 'view', 0, 0, 0 FROM ".views_table("view_name")."
ORDER BY 1")as$G)$F[$G["Name"]]=$G;return$F;}function
is_view(array$R){return$R["Engine"]=="view";}function
fk_support(array$R){return
true;}function
fields($Q){$F=array();foreach(get_rows("SELECT * FROM all_tab_columns WHERE table_name = ".q($Q)." AND ".where_owner()." ORDER BY column_id")as$G){$T=$G["DATA_TYPE"];$v="$G[DATA_PRECISION],$G[DATA_SCALE]";if($v==",")$v=$G["CHAR_COL_DECL_LENGTH"];$i=$G["DATA_DEFAULT"];if($i!==null){$i=rtrim($i);if(preg_match("~^'(.*)'\$~s",$i,$y))$i=str_replace("''","'",$y[1]);}$Dg=array("insert"=>1,"select"=>1,"update"=>1,"order"=>1);if($G["DATA_TYPE_OWNER"]==""||$T=="XMLTYPE")$Dg["where"]=1;$F[$G["COLUMN_NAME"]]=array("field"=>$G["COLUMN_NAME"],"full_type"=>$T.($v?"($v)":""),"type"=>strtolower($T),"length"=>$v,"default"=>$i,"null"=>($G["NULLABLE"]=="Y"),"privileges"=>$Dg,);}return$F;}function
table_constraints($Q,$g=null){$F=array();foreach(get_rows('SELECT c.constraint_name "name", c.constraint_type "type", c.r_owner "r_owner", c.r_constraint_name "r_constraint", c.delete_rule "delete_rule", cc.column_name "column"
FROM all_constraints c
JOIN all_cons_columns cc ON cc.owner = c.owner AND cc.constraint_name = c.constraint_name
WHERE c.constraint_type IN (\'P\', \'U\', \'R\') AND '.where_owner("c.owner")." AND c.table_name = ".q($Q).'
ORDER BY cc.position',$g)as$G){$z=$G["name"];$F[$z]["type"]=$G["type"];$F[$z]["r_owner"]=$G["r_owner"];$F[$z]["r_constraint"]=$G["r_constraint"];$F[$z]["delete_rule"]=$G["delete_rule"];$F[$z]["columns"][]=$G["column"];}return$F;}function
indexes($Q,$g=null){$F=array();$pb=array();foreach(table_constraints($Q,$g)as$z=>$ob)$pb[$z]=$ob["type"];foreach(get_rows("SELECT aic.*, atc.data_default
FROM all_ind_columns aic
LEFT JOIN all_tab_cols atc ON aic.column_name = atc.column_name AND aic.table_name = atc.table_name AND aic.index_owner = atc.owner
WHERE aic.table_name = ".q($Q)." AND ".where_owner("aic.table_owner")."
ORDER BY aic.column_position",$g)as$G){$Rd=$G["INDEX_NAME"];$eb=$G["DATA_DEFAULT"];$eb=($eb?trim($eb,'"'):$G["COLUMN_NAME"]);$T=idx($pb,$Rd);$F[$Rd]["type"]=($T=="P"?"PRIMARY":($T=="U"?"UNIQUE":"INDEX"));$F[$Rd]["columns"][]=$eb;$F[$Rd]["lengths"][]=($G["CHAR_LENGTH"]&&$G["CHAR_LENGTH"]!=$G["COLUMN_LENGTH"]?$G["CHAR_LENGTH"]:null);$F[$Rd]["descs"][]=($G["DESCEND"]&&$G["DESCEND"]=="DESC"?'1':null);}uasort($F,function($ca,$_a){$Mf=array("PRIMARY"=>0,"UNIQUE"=>1,"INDEX"=>2);return$Mf[$ca["type"]]-$Mf[$_a["type"]];});return$F;}function
view($z){$H=get_rows('SELECT text "select" FROM '.views_table("view_name, text").' WHERE view_name = '.q($z));return($H?$H[0]:array());}function
collations(){return
array();}function
information_schema($h,$I=""){return($I!=""?$I:$h)=="INFORMATION_SCHEMA";}function
error(){return
h(connection()->error);}function
explain(Db$f,$D){$f->query("EXPLAIN PLAN FOR $D");return$f->query("SELECT * FROM plan_table");}function
found_rows(array$R,array$Z){}function
auto_increment(){return"";}function
alter_table($Q,$z,array$l,array$Wc,$fb,$oc,$bb,$xa,$eg){$b=$dc=array();$Qf=($Q?fields($Q):array());foreach($l
as$k){$W=$k[1];if($W&&$k[0]!=""&&idf_escape($k[0])!=$W[0])queries("ALTER TABLE ".table($Q)." RENAME COLUMN ".idf_escape($k[0])." TO $W[0]");$Pf=$Qf[$k[0]];if($W&&$Pf){$Ff=process_field($Pf,$Pf);if($W[2]==$Ff[2])$W[2]="";}if($W){list($W[2],$W[3])=array($W[3],$W[2]);$b[]=($Q!=""?($k[0]!=""?"MODIFY (":"ADD ("):"  ").implode($W).($Q!=""?")":"");}else$dc[]=idf_escape($k[0]);}if($Q=="")return
queries("CREATE TABLE ".table($z)." (\n".implode(",\n",array_merge($b,$Wc))."\n)");return(!$b||queries("ALTER TABLE ".table($Q)."\n".implode("\n",$b)))&&(!$dc||queries("ALTER TABLE ".table($Q)." DROP (".implode(", ",$dc).")"))&&($Q==$z||queries("ALTER TABLE ".table($Q)." RENAME TO ".table($z)));}function
alter_indexes($Q,$b){$dc=array();$Hg=array();foreach($b
as$W){if($W[0]!="INDEX"){$W[2]=preg_replace('~ DESC$~','',$W[2]);$zb=($W[2]=="DROP"?"\nDROP CONSTRAINT ".idf_escape($W[1]):"\nADD".($W[1]!=""?" CONSTRAINT ".idf_escape($W[1]):"")." $W[0] ".($W[0]=="PRIMARY"?"KEY ":"")."(".implode(", ",$W[2]).")");array_unshift($Hg,"ALTER TABLE ".table($Q).$zb);}elseif($W[2]=="DROP")$dc[]=idf_escape($W[1]);else$Hg[]="CREATE INDEX ".idf_escape($W[1]!=""?$W[1]:uniqid($Q."_"))." ON ".table($Q)." (".implode(", ",$W[2]).")";}if($dc)array_unshift($Hg,"DROP INDEX ".implode(", ",$dc));foreach($Hg
as$D){if(!queries($D))return
false;}return
true;}function
foreign_keys($Q){$F=array();$ji=array();foreach(table_constraints($Q)as$z=>$ob){if($ob["type"]=="R"){$F[$z]=array("source"=>$ob["columns"],"target"=>array(),"on_delete"=>$ob["delete_rule"],"on_update"=>null,);$ji[$z]=array($ob["r_owner"],$ob["r_constraint"]);}}if($ji){$Z=array();foreach($ji
as$ii)$Z[]="(owner = ".q($ii[0])." AND constraint_name = ".q($ii[1]).")";foreach(get_rows("SELECT owner, constraint_name, table_name, column_name FROM all_cons_columns WHERE ".implode(" OR ",array_unique($Z))." ORDER BY position")as$G){foreach($ji
as$z=>$ii){if($ii==array($G["OWNER"],$G["CONSTRAINT_NAME"])){$F[$z]["db"]=$G["OWNER"];$F[$z]["table"]=$G["TABLE_NAME"];$F[$z]["target"][]=$G["COLUMN_NAME"];}}}}return$F;}function
trigger($z,$Q){if($z=="")return
array();$H=get_rows('SELECT trigger_name "Trigger", trigger_type "Type", triggering_event "Event", trigger_body "Statement"
FROM all_triggers
WHERE trigger_name = '.q($z)." AND ".where_owner());$F=reset($H);if($F){$T=$F["Type"];$F["Timing"]=(preg_match('~^(BEFORE|AFTER|INSTEAD OF)~',$T,$y)?$y[1]:$T);$F["Type"]=(preg_match('~EACH ROW~',$T)||$T=="INSTEAD OF"?"FOR EACH ROW":"");}return($F?:array());}function
triggers($Q){$F=array();foreach(get_rows("SELECT trigger_name, trigger_type, triggering_event FROM all_triggers WHERE table_name = ".q($Q)." AND ".where_owner())as$G)$F[$G["TRIGGER_NAME"]]=array(preg_replace('~ (STATEMENT|EACH ROW)$~','',$G["TRIGGER_TYPE"]),$G["TRIGGERING_EVENT"]);return$F;}function
trigger_options(){return
array("Timing"=>array("BEFORE","AFTER","INSTEAD OF"),"Event"=>array("INSERT","UPDATE","DELETE","INSERT OR UPDATE","INSERT OR DELETE","UPDATE OR DELETE","INSERT OR UPDATE OR DELETE"),"Type"=>array("FOR EACH ROW",""),);}function
truncate_tables(array$S){return
apply_queries("TRUNCATE TABLE",$S);}function
drop_views(array$nj){return
apply_queries("DROP VIEW",$nj);}function
drop_tables(array$S){return
apply_queries("DROP TABLE",$S);}function
last_id($E){return"0";}function
create_database($h,$bb){$F=queries("CREATE USER ".idf_escape($h)." NO AUTHENTICATION");return($F?queries("GRANT UNLIMITED TABLESPACE TO ".idf_escape($h)):$F);}function
drop_databases(array$Ib){$F=true;foreach($Ib
as$h)$F=!!queries("DROP USER ".idf_escape($h)." CASCADE")&&$F;return$F;}function
rename_database($z,$bb){return!!queries("ALTER USER ".idf_escape(DB)." RENAME TO ".idf_escape($z));}function
show_variables(){return
get_rows('SELECT name, display_value FROM v$parameter');}function
show_status(){$F=array();$H=get_rows('SELECT * FROM v$instance');foreach(reset($H)as$u=>$W)$F[]=array($u,$W);return$F;}function
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
support($Gc){return
preg_match('~^(columns|database|drop_col|fast_status|indexes|descidx|processlist|sql|status|table|trigger|variables|view|view_trigger)$~',$Gc);}}class
Adminer{static$instance;var$error='';private$values=array();function
name(){return"<a href='https://www.adminer.org/editor/'".target_blank()." id='h1'><img src='".h(preg_replace("~\\?.*~","",ME)."?file=logo.svg&version=6.1.0+5f12f1ea")."' width='24' height='24' alt='' id='logo'>".lang(37)."</a>";}function
credentials(){return
array(SERVER,$_GET["username"],get_password());}function
connectSsl(){}function
permanentLogin($zb=false){return
password_file($zb);}function
bruteForceKey(){return$_SERVER["REMOTE_ADDR"];}function
verifyLoginToken(){return
true;}function
serverName($L){return'';}function
database(){if(connection()){$Ib=adminer()->databases(false);if(!$Ib)return
get_val("SELECT SUBSTRING_INDEX(CURRENT_USER, '@', 1)");foreach($Ib
as$h){if(!information_schema($h))return$h;}return$Ib[0];}return
null;}function
operators($ci=null){return
array("<=",">=");}function
schemas(){return
schemas();}function
databases($Uc=true){return
get_databases($Uc);}function
pluginsLinks(){}function
queryTimeout(){return
5;}function
afterConnect(){}function
headers(){}function
csp(array$Bb){return$Bb;}function
verifyVersion(){return
true;}function
serviceWorker(){service_worker();}function
manifest(){$Hd=$_SERVER["HTTP_HOST"]?:$_SERVER["SERVER_NAME"];$ph=preg_replace('~\?.*~','',ME)?:'.';return
array('name'=>"Adminer Editor".($Hd!=""?" - $Hd":""),'short_name'=>'Adminer Editor','description'=>lang(38),'start_url'=>$ph,'scope'=>$ph,'display'=>'minimal-ui','icons'=>array(array('src'=>preg_replace("~\\?.*~","",ME)."?file=logo.svg&version=6.1.0+5f12f1ea",'sizes'=>'any','type'=>'image/svg+xml')),);}function
head($Fb=null){return
true;}function
bodyClass(){echo" editor";}function
css(){$F=array();foreach(array("","-dark")as$mf){$m="adminer$mf.css";if(file_exists($m)){$Jc=file_get_contents($m);$F["$m?v=".crc32($Jc)]=($mf?"dark":(preg_match('~prefers-color-scheme:\s*dark~',$Jc)?'':'light'));}}return$F;}function
loginForm(){echo"<table class='layout'>\n",adminer()->loginFormField('username','<tr><th>'.lang(39).'<td>',input_hidden("auth[driver]","server").'<input name="auth[username]" autofocus value="'.h($_GET["username"]).'" autocomplete="username" autocapitalize="off">'),adminer()->loginFormField('password','<tr><th>'.lang(40).'<td>','<input type="password" name="auth[password]" autocomplete="current-password">'),"</table>\n","<p><input type='submit' value='".lang(41)."'>\n",checkbox("auth[permanent]",1,$_COOKIE["adminer_permanent"],lang(42))."\n";}function
loginFormField($z,$Dd,$X){return$Dd.$X."\n";}function
login($Ne,$C){if($C=="")return
lang(43);if(!Driver::$passwords)return
lang(44);if(!password_required())return
lang(45);return
true;}function
tableName(array$ci){return
h(isset($ci["Engine"])?($ci["Comment"]!=""?$ci["Comment"]:$ci["Name"]):"");}function
fieldName(array$k,$Mf=0){return
h(preg_replace('~\s+\[.*\]$~','',($k["comment"]!=""?$k["comment"]:$k["field"])));}function
selectLinks(array$ci,$M=""){$a=$ci["Name"];if($M!==null)echo'<p class="tabs"><a href="'.h(ME.'edit='.url_escape($a).$M).'">'.lang(46)."</a>\n";}function
foreignKeys($Q){return
foreign_keys($Q);}function
backwardKeys($Q,$bi){$F=array();foreach(get_rows("SELECT TABLE_NAME, CONSTRAINT_NAME, COLUMN_NAME, REFERENCED_COLUMN_NAME
FROM information_schema.KEY_COLUMN_USAGE
WHERE TABLE_SCHEMA = ".q(adminer()->database())."
AND REFERENCED_TABLE_SCHEMA = ".q(adminer()->database())."
AND REFERENCED_TABLE_NAME = ".q($Q)."
ORDER BY ORDINAL_POSITION",null,"")as$G)$F[$G["TABLE_NAME"]]["keys"][$G["CONSTRAINT_NAME"]][$G["COLUMN_NAME"]]=$G["REFERENCED_COLUMN_NAME"];foreach($F
as$u=>$W){$z=adminer()->tableName(table_status1($u,true));if($z!=""){$gh=preg_quote($bi);$K="(:|\\s*-)?\\s+";$F[$u]["name"]=(preg_match("(^$gh$K(.+)|^(.+?)$K$gh\$)iu",$z,$y)?$y[2].$y[3]:$z);}else
unset($F[$u]);}return$F;}function
backwardKeysPrint(array$Ca,array$G){foreach($Ca
as$Q=>$Ba){foreach($Ba["keys"]as$db){$x=ME.'select='.url_escape($Q);$p=0;foreach($db
as$d=>$W)$x
.=where_link($p++,$d,$G[$W]);echo"<a href='".h($x)."'>".h($Ba["name"])."</a>";$x=ME.'edit='.url_escape($Q);foreach($db
as$d=>$W)$x
.="&set[".url_escape(bracket_escape($d))."]=".url_escape($G[$W]);echo"<a href='".h($x)."' title='".lang(46)."'>+</a> ";}}}function
selectQuery($D,$Sh,$Ec=false){return
sql_comment($D,format_time($Sh));}function
rowDescription($Q){foreach(fields($Q)as$k){if(preg_match("~varchar|character varying~",$k["type"]))return
idf_escape($k["field"]);}return"";}function
rowDescriptions(array$H,array$Yc){$F=$H;foreach($H[0]as$u=>$W){if(list($Q,$q,$z)=$this->_foreignColumn($Yc,$u)){$Nd=array();foreach($H
as$G){if(isset($G[$u]))$Nd[$G[$u]]=q($G[$u]);}if(!$Nd)continue;$Qb=$this->values[$Q];if(!$Qb)$Qb=get_key_vals("SELECT $q, $z FROM ".table($Q)." WHERE $q IN (".implode(", ",$Nd).")");foreach($H
as$sf=>$G){if(isset($G[$u]))$F[$sf][$u]=(string)$Qb[$G[$u]];}}}return$F;}function
selectLink($W,array$k){}function
selectVal($W,$x,array$k,$Rf){$F="$W";$x=h($x);if(is_blob($k)&&!is_utf8($W)){$F=lang(47,strlen($Rf));$Gh=(function_exists('getimagesizefromstring')?@getimagesizefromstring($Rf):array());if($Gh)$F="<img src='$x' alt='$F' $Gh[3] loading='lazy'>";}if(like_bool($k)&&$F!="")$F=(preg_match('~^(1|t|true|y|yes|on)$~i',$W)?lang(48):lang(49));if($x)$F="<a href='$x'".(is_url($x)?target_blank():"").">$F</a>";if(preg_match('~date~',$k["type"]))$F="<div class='datetime'>$F</div>";return$F;}function
editVal($W,array$k){if(preg_match('~date|timestamp~',$k["type"])&&$W!==null)return
preg_replace('~^(\d{2}(\d+))-(0?(\d+))-(0?(\d+))~',lang(50),$W);return$W;}function
config(){return
array();}function
selectColumnsPrint(array$J,array$e){}private
function
searchColumns(array$l){$F=array();$Q=$_GET["select"];if($Q=="")return$F;$p=0;foreach($l
as$z=>$k){if(isset($k["privileges"]["where"])&&$this->fieldName($k)!=""&&($k["type"]=="enum"||like_bool($k)||is_array($this->foreignKeyOptions($Q,$z))))$F[--$p]=$z;}return$F;}function
selectSearchPrint(array$Z,array$e,array$t,$ci=null){$Z=(array)$_GET["where"];echo'<fieldset id="fieldset-search"><legend>'.lang(51)."</legend><div>\n";$l=fields($_GET["select"]);foreach($this->searchColumns($l)as$p=>$z){$k=$l[$z];$W=idx($Z[$p],"val");echo"<div>".h($e[$z]);if($k["type"]=="enum"||like_bool($k))echo": ",(like_bool($k)?"<select name='where[$p][val]' data-default=''>".optionlist(array(""=>"",lang(49),lang(48)),$W,true)."</select>":enum_input("checkbox"," name='where[$p][val][]'",$k,(array)$W,lang(52)));else{$A=$this->foreignKeyOptions($_GET["select"],$z);if($k["null"])$A[0]='('.lang(52).')';echo": <select name='where[$p][val]' data-default=''>".optionlist($A,$W,true)."</select>";}echo"</div>\n";unset($e[$z]);}$p=0;foreach($Z
as$u=>$W){if($u>=0&&($W["col"]==""||$e[$W["col"]])&&"$W[col]$W[val]"!=""){echo"<div><select name='where[$p][col]' data-default=''><option value=''>(".lang(53).")".optionlist($e,$W["col"],true)."</select>",html_select("where[$p][op]",array(-1=>"")+adminer()->operators($ci),$W["op"]," data-default=''"),"<input type='search' name='where[$p][val]' value='".h($W["val"])."' data-default=''".on('keydown','selectSearchKeydown').on('search','selectSearchSearch')."></div>\n";$p++;}}echo"<div><select name='where[$p][col]' data-default=''".on('change','selectAddRow')."><option value=''>(".lang(53).")".optionlist($e,null,true)."</select>",html_select("where[$p][op]",array(-1=>"")+adminer()->operators($ci),null," data-default=''"),"<input type='search' name='where[$p][val]' data-default=''".on('change','selectFirstChange').on('keydown','selectSearchKeydown').on('search','selectSearchSearch')."></div>\n","</div></fieldset>\n";}function
selectOrderPrint(array$Mf,array$e,array$t){$Of=array();foreach($t
as$u=>$s){$Mf=array();foreach($s["columns"]as$W)$Mf[]=$e[$W];if(count(array_filter($Mf,'strlen'))>1&&$u!="PRIMARY")$Of[$u]=implode(", ",$Mf);}if($Of)echo'<fieldset><legend>'.lang(54)."</legend><div>","<select name='index_order' data-default=''>".optionlist(array(""=>"")+$Of,(idx($_GET["order"],0)!=""?"":$_GET["index_order"]),true)."</select>","</div></fieldset>\n";if($_GET["order"])echo"<div hidden>".hidden_fields(array("order"=>array(1=>reset($_GET["order"])),"desc"=>($_GET["desc"]?array(1=>1):array()),))."</div>\n";}function
selectLimitPrint($w){echo"<fieldset><legend>".lang(55)."</legend><div>",html_select("limit",array("","50","100"),(string)$w," data-default='50'"),"</div></fieldset>\n";}function
selectLengthPrint($mi){}function
selectActionPrint(array$t){echo"<fieldset><legend>".lang(56)."</legend><div>","<input type='submit' value='".lang(57)."'>","</div></fieldset>\n";}function
selectCommandPrint(){return
true;}function
selectImportPrint(){return
true;}function
selectEmailPrint(array$lc,array$e){}function
selectColumnsProcess(array$e,array$t){return
array(array(),array());}function
selectSearchProcess(array$l,array$t,$ci=null){$F=array();$hh=$this->searchColumns($l);if($_GET["select"]!=""&&!$_POST&&!is_ajax()){$se=array();$x="";foreach((array)$_GET["where"]as$u=>$Z){$tg=($u>=0?array_search($Z["col"],$hh,true):false);$k=idx($l,$Z["col"],array());if($tg!==false&&$k["type"]!="enum"&&!is_array($Z["val"])&&$Z["val"]!=""&&$Z["op"]==(like_bool($k)?"":"=")){$se[]=$u;$x
.="&where[$tg][val]=".url_escape($Z["val"]);}}if($se)redirect(remove_from_uri("where(%5B|\[)(".implode("|",$se).")(%5D|\])[^=]*").$x);}foreach((array)$_GET["where"]as$u=>$Z){if($u<0){$Z["col"]=idx($hh,$u,"");$k=idx($l,$Z["col"],array());$Z["op"]=($k&&($k["type"]=="enum"||like_bool($k))?"":"=");$_GET["where"][$u]=$Z;}$Z+=array("col"=>"","op"=>"","val"=>"");$ab=$Z["col"];$If=$Z["op"];$W=$Z["val"];if(($u>=0&&$ab!="")||$W!=""){$ib=array();foreach(($ab!=""?array($ab=>$l[$ab]):$l)as$z=>$k){if($ab!=""||is_searchable($k,$Z)){$z=idf_escape($z);if($ab!=""&&$k["type"]=="enum"){$Pd=array();foreach(preg_grep('~^val-~',$W)as$hj)$Pd[]=q(substr($hj,4));$ib[]=(in_array("null",$W)?"$z IS NULL OR ":"").($Pd?"$z IN (".implode(", ",$Pd).")":"0");}else{$ni=preg_match('~'.text_type().'~',$k["type"]);$X=q(!$If&&$ni&&preg_match('~^[^%]+$~',$W)?"%$W%":$W);$ib[]=driver()->convertSearch($z,$Z,$k).($X=="NULL"?" IS".($If==">="?" NOT":"")." $X":(in_array($If,adminer()->operators($ci))||$If=="="?" $If $X":($ni?" LIKE $X":" IN (".($X[0]=="'"?str_replace(",","', '",$X):$X).")")));if($u<0&&$W=="0")$ib[]="$z IS NULL";}}}$F[]=($ib?"(".implode(" OR ",$ib).")":"1 = 0");}}return$F;}function
selectOrderProcess(array$l,array$t){$Sd=$_GET["index_order"];if($Sd!="")unset($_GET["order"][1]);if($_GET["order"])return
array(idf_escape(reset($_GET["order"])).($_GET["desc"]?" DESC":""));foreach(($Sd!=""?array($t[$Sd]):$t)as$s){if($Sd!=""||$s["type"]=="INDEX"){$wd=array_filter($s["descs"]);$Pb=false;foreach($s["columns"]as$W){if(preg_match('~date|timestamp~',$l[$W]["type"])){$Pb=true;break;}}$F=array();foreach($s["columns"]as$u=>$W)$F[]=idf_escape($W).(($wd?$s["descs"][$u]:$Pb)?" DESC":"");return$F;}}return
array();}function
selectLimitProcess(){return(isset($_GET["limit"])?intval($_GET["limit"]):50);}function
selectLengthProcess(){return"100";}function
selectEmailProcess(array$Z,array$Yc){return
false;}function
selectQueryBuild(array$J,array$Z,array$o,array$Mf,$w,$B){return"";}function
messageQuery($D,$oi,$Ec=false){return" <span class='time'>".@date("H:i:s")."</span>".sql_comment($D,$oi);}function
error(){return
error();}function
editRowPrint($Q,array$l,$G,$Vi,$D='',$oi=''){echo($D!=""?sql_comment($D,$oi):"");}function
editFunctions(array$k){$F=array();if($k["null"]&&preg_match('~blob~',$k["type"]))$F["NULL"]=lang(52);$F[""]=($k["null"]||$k["auto_increment"]||like_bool($k)?"":"*");if(preg_match('~date|time~',$k["type"]))$F["now"]=lang(58);if(preg_match('~_(md5|sha1)$~i',$k["field"],$y))$F[]=strtolower($y[1]);return$F;}function
editInput($Q,array$k,$c,$X){if($k["type"]=="enum")return(isset($_GET["select"])?"<label><input type='radio'$c value='orig' checked><i>".lang(11)."</i></label> ":"").enum_input("radio",$c,$k,$X,lang(52));$A=$this->foreignKeyOptions($Q,$k["field"],$X);if($A!==null){if(!$k["null"]&&is_array($A))unset($A[""]);return(is_array($A)?"<select$c>".optionlist($A,(string)$X,true)."</select>":"<input value='".h($X)."'$c class='hidden'>"."<input value='".h($A)."' class='jsonly'".on('input','whisper',ME."script=complete&source=".url_escape($Q)."&field=".url_escape($k["field"])."&value=").">"."<div".on('click','whisperClick')."></div>");}if(like_bool($k))return'<input type="checkbox" value="1"'.(preg_match('~^(1|t|true|y|yes|on)$~i',$X)?' checked':'')."$c>";$Fd="";if(preg_match('~time~',$k["type"]))$Fd=lang(59);if(preg_match('~date|timestamp~',$k["type"]))$Fd=lang(60).($Fd?" [$Fd]":"");if($Fd)return"<input value='".h($X)."'$c> ($Fd)";if(preg_match('~_(md5|sha1)$~i',$k["field"]))return"<input type='password' value='".h($X)."'$c>";return'';}function
editHint($Q,array$k,$X){return(preg_match('~\s+(\[.*\])$~',($k["comment"]!=""?$k["comment"]:$k["field"]),$y)?h(" $y[1]"):'');}function
processInput(array$k,$X,$n=""){if($n=="now")return"$n()";$F=$X;if(preg_match('~date|timestamp~',$k["type"])&&preg_match('(^'.str_replace('\$1','(?P<p1>\d*)',preg_replace('~(\\\\\\$([2-6]))~','(?P<p\2>\d{1,2})',preg_quote(lang(50)))).'(.*))',$X,$y))$F=($y["p1"]!=""?$y["p1"]:($y["p2"]!=""?($y["p2"]<70?20:19).$y["p2"]:gmdate("Y")))."-$y[p3]$y[p4]-$y[p5]$y[p6]".end($y);$F=q($F);if($X==""&&like_bool($k))$F="'0'";elseif($X==""&&($k["null"]||!preg_match('~char|text~',$k["type"])))$F="NULL";elseif(preg_match('~^(md5|sha1)$~',$n))$F="$n($F)";return
unconvert_field($k,$F);}function
dumpOutput(){return
array();}function
dumpFormat(){return
array('csv'=>'CSV,','csv;'=>'CSV;','tsv'=>'TSV');}function
dumpDatabase($h){}function
dumpTable($Q,$Wh,$ne=0){echo"\xef\xbb\xbf";}function
dumpData($Q,$Wh,$D){$E=connection()->query($D,1);if($E){while($G=$E->fetch_assoc()){if($Wh=="table"){dump_csv(array_keys($G));$Wh="INSERT";}dump_csv($G);}}}function
dumpFilename($Ld){return
friendly_url($Ld);}function
dumpHeaders($Ld,$qf=false){$Ac="csv";header("Content-Type: text/csv; charset=utf-8");return$Ac;}function
dumpFooter(){}function
importServerPath(){return'';}function
homepage(){return
true;}function
navigation($lf){echo"<h1>".adminer()->name()." <span class='version'>".VERSION;$wf=$_COOKIE["adminer_version"];echo" <a href='https://www.adminer.org/editor/#download'".target_blank()." id='version'>".(version_compare(VERSION,$wf)<0?h($wf):"").version_iframe()."</a>","</span></h1>\n";switch_lang();if($lf=="auth"){$Pc=true;foreach((array)$_SESSION["pwds"]as$kj=>$Bh){foreach($Bh[""]as$U=>$C){if($C!==null){if($Pc){echo"<ul id='logins'".on('mouseover','menuOver').on('mouseout','menuOut').">";$Pc=false;}echo"<li><a href='".h(auth_url($kj,"",$U))."'>".($U!=""?h($U):"<i>".lang(52)."</i>")."</a>\n";}}}}else{adminer()->databasesPrint($lf);$ea=adminer()->menuActions(array(),$lf);echo($ea?"<p class='links'>\n".implode("\n",$ea)."\n":"");if($lf!="db"&&$lf!="ns"){$R=table_status('',true);if(!$R)echo"<p class='message'>".lang(12)."\n";else
adminer()->tablesPrint($R);}}}function
syntaxHighlighting(array$S){}function
databasesPrint($lf){}function
menuActions(array$ea,$lf){return$ea;}function
tablesPrint(array$S){echo"<ul id='tables'".on('mouseover','menuOver').on('mouseout','menuOut').">";foreach($S
as$G){echo'<li>';$z=adminer()->tableName($G);if($z!="")echo"<a href='".h(ME).'select='.url_escape($G["Name"])."'".bold($_GET["select"]==$G["Name"]||$_GET["edit"]==$G["Name"],"select")." title='".lang(61)."'>$z</a>\n";}echo"</ul>\n";}function
_foreignColumn(array$Yc,$d){foreach((array)$Yc[$d]as$Xc){if(count($Xc["source"])==1){$z=adminer()->rowDescription($Xc["table"]);if($z!=""){$q=idf_escape($Xc["target"][0]);return
array($Xc["table"],$q,$z);}}}}private
function
foreignKeyOptions($Q,$d,$X=null){if(list($ii,$q,$z)=$this->_foreignColumn(column_foreign_keys($Q),$d)){$F=&$this->values[$ii];if($F===null){$R=table_status1($ii);$F=($R["Rows"]>1000?"":array(""=>"")+get_key_vals("SELECT $q, $z FROM ".table($ii)." ORDER BY 2"));}if(!$F&&$X!==null)return
get_val("SELECT $z FROM ".table($ii)." WHERE $q = ".q($X));return$F;}}}class
Plugins{private
static$append=array('dumpFormat'=>true,'dumpOutput'=>true,'editRowPrint'=>true,'editFunctions'=>true,'config'=>true);var$plugins;var$drivers=array();var$driverFiles=array();var$error='';private$hooks=array();function
__construct($qg){$cc=SqlDriver::$drivers;$Ed=" href='https://www.adminer.org/plugins/#use'".target_blank();if($qg===null){$qg=array();$Fa="adminer-plugins";if(is_dir($Fa)){foreach(glob("$Fa/*.php")as$m){$Kc=SqlDriver::$drivers;$this->includeOnce($m);foreach(array_diff_key(SqlDriver::$drivers,$Kc)as$q=>$z)$this->driverFiles[$q]=$m;}}if(file_exists("$Fa.php")){$Qd=$this->includeOnce("$Fa.php");if(is_array($Qd)){foreach($Qd
as$u=>$og)$qg[is_object($og)?get_class($og):$u]=$og;}else$this->error
.=lang(62,"<b>$Fa.php</b>",$Ed)."<br>";}foreach(get_declared_classes()as$Xa){if(!$qg[$Xa]&&(preg_match('~^Adminer\w~i',$Xa)||is_subclass_of($Xa,'Adminer\Plugin'))){$Og=new
\ReflectionClass($Xa);$qb=$Og->getConstructor();if($qb&&$qb->getNumberOfRequiredParameters())$this->error
.=lang(63,$Ed,"<b>$Xa</b>","<b>$Fa.php</b>")."<br>";else$qg[$Xa]=new$Xa;}}}$ee=array_filter($qg,function($og){return!is_object($og);});if($ee){$this->error
.=lang(64,$Ed)."<br>";$qg=array_diff_key($qg,$ee);}$this->drivers=array_diff_key(SqlDriver::$drivers,$cc);$this->plugins=$qg;$ja=new
Adminer;$qg[]=$ja;$Og=new
\ReflectionObject($ja);foreach($Og->getMethods()as$kf){foreach($qg
as$og){$z=$kf->getName();if(method_exists($og,$z))$this->hooks[$z][]=$og;}}}function
includeOnce($m){return
include_once"./$m";}static
function
checksum($m){$Jc=str_replace("\r","",file_get_contents($m));$Jc=preg_replace('~\n\tprotected \$translations = array\(.*?\n\t\);~s','',$Jc);return
dechex(crc32($Jc));}function
checksums(){$Lc=array_values($this->driverFiles);foreach($this->plugins
as$og){$Og=new
\ReflectionObject($og);$Lc[]=$Og->getFileName();}$F=array();foreach($Lc
as$m)$F[basename($m,'.php')]=self::checksum($m);return$F;}static
function
officialChecksums(){return
array('adminer.js'=>'a0599090','backward-keys'=>'ed1ef78f','before-unload'=>'2a613523','config'=>'722eb4af','dark-switcher'=>'3d490dea','database-hide'=>'e304a899','designs'=>'ed7e44e3','dump-alter'=>'896b579e','dump-bz2'=>'f0d0e336','dump-date'=>'adc7f1c7','dump-json'=>'767dd321','dump-xml'=>'4fc3cd60','dump-zip'=>'93817d96','edit-foreign'=>'72ad1562','edit-textarea'=>'a24c3cc','editor-setup'=>'a7dc3a37','editor-views'=>'5c12b185','enum-option'=>'1e24970e','file-upload'=>'10add0e8','foreign-system'=>'ebb4c654','frames'=>'b0e1d11a','highlight-codemirror'=>'c5716555','highlight-monaco'=>'edd1b0af','highlight-prism'=>'267948e5','import-csv'=>'d429c77','login-ip'=>'4d174fea','login-otp'=>'5b5a68af','login-passkey'=>'f69f2f06','login-password-less'=>'e150daac','login-reverse-proxy'=>'24558ea2','login-servers'=>'19c42e45','login-ssl'=>'6ed147bc','login-table'=>'811f8cef','menu-links'=>'c78461b3','remote-color'=>'ddeecc48','row-numbers'=>'eec8698c','select-email'=>'f84fbd2c','select-image'=>'f55c0231','slugify'=>'dec64713','sql-gemini'=>'c60ab309','sql-log'=>'8e435000','table-indexes-structure'=>'a90cc0c9','table-structure'=>'a8458e02','tables-filter'=>'ec2bcd6e','timeout'=>'97321caf','version-github'=>'627cadf9','version-noverify'=>'966937e9','clickhouse'=>'ed04ed31','elastic'=>'af0361c1','firebird'=>'99307ba8','igdb'=>'db772c05','imap'=>'385b5247','mongo'=>'f75dfcf','redis'=>'139ed221','simpledb'=>'d2226cc',);}function
__call($z,array$ag){$sa=array();foreach($ag
as$u=>$W)$sa[]=&$ag[$u];$F=null;foreach($this->hooks[$z]as$og){$X=call_user_func_array(array($og,$z),$sa);if($X!==null){if(!self::$append[$z])return$X;$F=$X+(array)$F;}}return$F;}}abstract
class
Plugin{protected$translations=array();function
description(){return$this->lang('');}function
screenshot(){return"";}protected
function
lang($r,$_=null){$sa=func_get_args();$sa[0]=idx($this->translations[LANG],$r)?:$r;return
call_user_func_array('Adminer\lang_format',$sa);}}class
Password{private$password_hash;private$password_matches=null;function
__construct($hg){$this->password_hash=$hg;}function
description(){return
lang(65);}function
credentials(){$C=get_password();return
array(SERVER,$_GET["username"],($this->passwordMatches($C)&&!password_required()?"":$C));}function
login($Ne,$C){if($this->passwordMatches($C))return
true;}protected
function
passwordMatches($C){if($this->password_matches===null)$this->password_matches=(function_exists('password_verify')&&password_verify(strval($C),$this->password_hash));return$this->password_matches;}}Adminer::$instance=(function_exists('adminer_object')?adminer_object():(is_dir("adminer-plugins")||file_exists("adminer-plugins.php")?new
Plugins(null):new
Adminer));SqlDriver::$drivers=array("server"=>"MySQL / MariaDB")+SqlDriver::$drivers;if(!defined('Adminer\DRIVER')){define('Adminer\DRIVER',"server");if(extension_loaded("mysqli")&&$_GET["ext"]!="pdo"){class
Db
extends
\mysqli{static$instance;var$extension="MySQLi",$flavor='';function
__construct(){parent::init();}function
attach(array$L,$U,$C){mysqli_report(MYSQLI_REPORT_OFF);$rg=$L["port"];$nc=("$L[host]$rg$L[socket]"=="");$N=adminer()->connectSsl();$cj=($N&&($N['key']||$N['cert']||$N['ca']||isset($N['verify'])));if($cj)$this->ssl_set($N['key'],$N['cert'],$N['ca'],'','');$F=@$this->real_connect((!$nc?$L["host"]:ini_get("mysqli.default_host")),(!$nc||$U!=""?$U:ini_get("mysqli.default_user")),(!$nc||$U.$C!=""?$C:ini_get("mysqli.default_pw")),null,($rg!=""?intval($rg):ini_get("mysqli.default_port")),($rg!=""?null:$L["socket"]),($cj?($N['verify']!==false?MYSQLI_CLIENT_SSL:64):0));$this->options(MYSQLI_OPT_LOCAL_INFILE,0);return($F?'':$this->error);}function
set_charset($Ra){if(parent::set_charset($Ra))return
true;parent::set_charset('utf8');return$this->query("SET NAMES $Ra");}function
next_result(){return
self::more_results()&&parent::next_result();}function
quote($P){return"'".$this->escape_string($P)."'";}function
inTransaction(){return
false;}function
begin(){return$this->begin_transaction();}}}elseif(extension_loaded("mysql")&&!((ini_bool("sql.safe_mode")||ini_bool("mysql.allow_local_infile"))&&extension_loaded("pdo_mysql"))){class
Db
extends
SqlDb{private$link;function
attach(array$L,$U,$C){if(ini_bool("mysql.allow_local_infile"))return
lang(66,"'mysql.allow_local_infile'","MySQLi","PDO_MySQL");$rg="$L[port]$L[socket]";$z=$L["host"].($rg!=""?":$rg":"");$this->link=@mysql_connect(($z!=""?$z:ini_get("mysql.default_host")),($z.$U!=""?$U:ini_get("mysql.default_user")),($z.$U.$C!=""?$C:ini_get("mysql.default_password")),true,131072);if(!$this->link)return
mysql_error();$this->server_info=mysql_get_server_info($this->link);return'';}function
set_charset($Ra){return
mysql_set_charset($Ra,$this->link)||mysql_set_charset('utf8',$this->link);}function
quote($P){return"'".mysql_real_escape_string($P,$this->link)."'";}function
select_db($Hb){return
mysql_select_db($Hb,$this->link);}function
query($D,$Mi=false){$E=@($Mi?mysql_unbuffered_query($D,$this->link):mysql_query($D,$this->link));$this->error="";if(!$E){$this->errno=mysql_errno($this->link);$this->error=mysql_error($this->link);return
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
attach(array$L,$U,$C){$A=array(\PDO::MYSQL_ATTR_LOCAL_INFILE=>false);if(isset($_GET["select"]))$A[\PDO::MYSQL_ATTR_MULTI_STATEMENTS]=false;$N=adminer()->connectSsl();if($N){if($N['key'])$A[\PDO::MYSQL_ATTR_SSL_KEY]=$N['key'];if($N['cert'])$A[\PDO::MYSQL_ATTR_SSL_CERT]=$N['cert'];if($N['ca'])$A[\PDO::MYSQL_ATTR_SSL_CA]=$N['ca'];if(isset($N['verify']))$A[\PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT]=$N['verify'];}$Hd=$L["host"];$rg=$L["port"];$Jh=$L["socket"];return$this->dsn("mysql:charset=utf8".($Hd!=""?";host=$Hd":'').($rg!=""?";port=$rg":($Jh!=""?";unix_socket=$Jh":"")),$U,$C,$A);}function
set_charset($Ra){return$this->query("SET NAMES $Ra");}function
select_db($Hb){return$this->query("USE ".idf_escape($Hb));}function
query($D,$Mi=false){$this->pdo->setAttribute(\PDO::MYSQL_ATTR_USE_BUFFERED_QUERY,!$Mi);return
parent::query($D,$Mi);}}}class
Driver
extends
SqlDriver{static$extensions=array("MySQLi","MySQL","PDO_MySQL");static$jush="sql";static$serverSocket=true;var$unsigned=array("unsigned","zerofill","unsigned zerofill");var$functions=array("char_length","date","from_unixtime","lower","round","floor","ceil","sec_to_time","time_to_sec","upper");var$grouping=array("avg","count","count distinct","group_concat","max","min","sum");var$partitionBy=array("HASH","LINEAR HASH","KEY","LINEAR KEY","RANGE","LIST");function
operators($ci){return
array("=","<",">","<=",">=","!=","LIKE","LIKE %%","REGEXP","IN","FIND_IN_SET","IS NULL","NOT LIKE","NOT REGEXP","NOT IN","IS NOT NULL","SQL");}static
function
connect($L,$U,$C){$f=parent::connect($L,$U,$C);if(is_string($f)){if(function_exists('iconv')&&!is_utf8($f)&&strlen($eh=iconv("windows-1252","utf-8//IGNORE",$f))>strlen($f))$f=$eh;return$f;}$f->set_charset(charset($f));$f->query("SET sql_quote_show_create = 1, autocommit = 1");$f->flavor=(preg_match('~MariaDB~',$f->server_info)?'maria':'mysql');add_driver(DRIVER,($f->flavor=='maria'?"MariaDB":"MySQL"));return$f;}function
__construct(Db$f){parent::__construct($f);$this->types=array(lang(28)=>array("tinyint"=>3,"smallint"=>5,"mediumint"=>8,"int"=>10,"bigint"=>20,"decimal"=>66,"float"=>12,"double"=>21),lang(29)=>array("date"=>10,"datetime"=>19,"timestamp"=>19,"time"=>10,"year"=>4),lang(30)=>array("char"=>255,"varchar"=>65535,"tinytext"=>255,"text"=>65535,"mediumtext"=>16777215,"longtext"=>4294967295),lang(67)=>array("enum"=>65535,"set"=>64),lang(31)=>array("bit"=>20,"binary"=>255,"varbinary"=>65535,"tinyblob"=>255,"blob"=>65535,"mediumblob"=>16777215,"longblob"=>4294967295),lang(33)=>array("geometry"=>0,"point"=>0,"linestring"=>0,"polygon"=>0,"multipoint"=>0,"multilinestring"=>0,"multipolygon"=>0,"geometrycollection"=>0),);$this->insertFunctions=array("char"=>"md5/sha1/password/encrypt/uuid","binary"=>"md5/sha1","date|time"=>"now",);$this->editFunctions=array(number_type()=>"+/-","date"=>"+ interval/- interval","time"=>"addtime/subtime","char|text"=>"concat",);if(min_version('5.7.8',10.2,$f))$this->types[lang(30)]["json"]=4294967295;if(min_version('',10.7,$f)){$this->types[lang(30)]["uuid"]=128;$this->insertFunctions['uuid']='uuid';}if(min_version('',10.5,$f)){$this->types[lang(32)]["inet6"]=39;if(min_version('','10.10',$f))$this->types[lang(32)]["inet4"]=15;}if(min_version(9,11.7,$f))$this->types[lang(28)]["vector"]=16383;if(min_version(5.7,10.2,$f))$this->generated=array("STORED","VIRTUAL");}function
unconvertFunction(array$k){return(preg_match("~binary~",$k["type"])?"<code class='jush-sql'>UNHEX</code>":($k["type"]=="bit"?doc_link(array('sql'=>'bit-value-literals.html'),"<code>b''</code>"):($k["type"]=="vector"?"<code class='jush-sql'>".($this->conn->flavor=='maria'?"VEC_FromText":"STRING_TO_VECTOR")."</code>":(preg_match("~geom|point|linestring|polygon~",$k["type"])?"<code class='jush-sql'>GeomFromText</code>":""))));}function
insert($Q,array$M){return($M?parent::insert($Q,$M):queries("INSERT INTO ".table($Q)." ()\nVALUES ()"));}function
insertUpdate($Q,array$H,array$_g){$e=array_keys(reset($H));$xg="INSERT INTO ".table($Q)." (".implode(", ",$e).") VALUES\n";$Y=array();foreach($e
as$u)$Y[$u]="$u = VALUES($u)";$Xh="\nON DUPLICATE KEY UPDATE ".implode(", ",$Y);$Y=array();$v=0;foreach($H
as$M){$X="(".implode(", ",$M).")";if($Y&&(strlen($xg)+$v+strlen($X)+strlen($Xh)>1e6)){if(!queries($xg.implode(",\n",$Y).$Xh))return
false;$Y=array();$v=0;}$Y[]=$X;$v+=strlen($X)+2;}return
queries($xg.implode(",\n",$Y).$Xh);}function
slowQuery($D,$pi){if(min_version('5.7.8','10.1.2')){if($this->conn->flavor=='maria')return"SET STATEMENT max_statement_time=$pi FOR $D";elseif(preg_match('~^(SELECT\b)(.+)~is',$D,$y))return"$y[1] /*+ MAX_EXECUTION_TIME(".($pi*1000).") */ $y[2]";}}function
convertColumn($r,array$k){if(preg_match("~binary~",$k["type"]))return"HEX($r)";if($k["type"]=="bit")return"BIN($r + 0)";if($k["type"]=="vector")return($this->conn->flavor=='maria'?"VEC_ToText":"VECTOR_TO_STRING")."($r)";if(preg_match("~geom|point|linestring|polygon~",$k["type"]))return(min_version(8)?"ST_":"")."AsWKT($r)";return"";}function
convertSearch($r,array$W,array$k){return($this->convertColumn($r,$k)?:(preg_match('~'.text_type().'~',$k["type"])&&!preg_match("~^utf8~",$k["collation"])&&preg_match('~[\x80-\xFF]~',$W['val'])?"CONVERT($r USING ".charset($this->conn).")":$r));}function
typeName(\stdClass$k){$z=parent::typeName($k);if($z!=""){$Li=array("TINY"=>"tinyint","SHORT"=>"smallint","LONG"=>"int","INT24"=>"mediumint","LONGLONG"=>"bigint","NEWDECIMAL"=>"decimal","VAR_STRING"=>"varchar","STRING"=>"char",);return
idx($Li,$z,strtolower($z));}$Li=array("decimal","tinyint","smallint","int","float","double",7=>"timestamp","bigint","mediumint","date","time","datetime","year",15=>"varchar","bit",242=>"vector",245=>"json","decimal","enum","set","tinytext","mediumtext","longtext","text","varchar","char","geometry",);$F=idx($Li,$k->type,"");return($k->charsetnr==63?str_replace(array("text","varchar","char"),array("blob","varbinary","binary"),$F):$F);}function
quoteBinary($eh){return"X".q(bin2hex($eh));}function
warnings(){$E=$this->conn->query("SHOW WARNINGS");if($E&&$E->num_rows){ob_start();print_select_result($E);return
ob_get_clean();}}function
tableHelp($z,$ne=false){$Pe=($this->conn->flavor=='maria');if(information_schema(DB))return
strtolower(str_replace("_","-",DB)."-".($Pe?"$z-table/":str_replace("_","-",$z)."-table.html"));if(DB=="sys")return($Pe?"sys-schema/":strtolower("sys-".str_replace("_","-",preg_replace('~^x\$~','',$z)).".html"));if(DB=="mysql")return($Pe?"mysql$z-table/":"system-schema.html");}function
partitionsInfo($Q){$hd="FROM information_schema.PARTITIONS WHERE TABLE_SCHEMA = ".q(DB)." AND TABLE_NAME = ".q($Q);$E=$this->conn->query("SELECT PARTITION_METHOD, PARTITION_EXPRESSION, PARTITION_ORDINAL_POSITION $hd ORDER BY PARTITION_ORDINAL_POSITION DESC LIMIT 1");$G=($E?$E->fetch_row():null);if(!$G)return
array();$F=array();list($F["partition_by"],$F["partition"],$F["partitions"])=$G;$fg=get_key_vals("SELECT PARTITION_NAME, PARTITION_DESCRIPTION $hd AND PARTITION_NAME != '' ORDER BY PARTITION_ORDINAL_POSITION");$F["partition_names"]=array_keys($fg);$F["partition_values"]=array_values($fg);return$F;}function
checkConstraints($Q){$F=parent::checkConstraints($Q);return($this->conn->flavor=='maria'?$F:array_map('stripslashes',$F));}function
hasCStyleEscapes(){static$Oa;if($Oa===null){$Qh=get_val("SHOW VARIABLES LIKE 'sql_mode'",1,$this->conn);$Oa=(strpos($Qh,'NO_BACKSLASH_ESCAPES')===false);}return$Oa;}function
lineComment(){return"#|-- ";}function
engines(){$F=array();foreach(get_rows("SHOW ENGINES")as$G){if(preg_match("~YES|DEFAULT~",$G["Support"]))$F[]=$G["Engine"];}return$F;}function
indexAlgorithms(array$ci){return(preg_match('~^(MEMORY|NDB)$~',$ci["Engine"])?array("HASH","BTREE"):array());}}function
idf_escape($r){return"`".str_replace("`","``",$r)."`";}function
table($r){return
idf_escape($r);}function
get_databases($Uc){$F=get_session("dbs");if($F===null){$D="SELECT SCHEMA_NAME FROM information_schema.SCHEMATA ORDER BY SCHEMA_NAME";$Sh=microtime(true);$F=($Uc?slow_query($D):get_vals($D));if(microtime(true)-$Sh>0.1){restart_session();set_session("dbs",$F);stop_session();}}return$F;}function
limit($D,$Z,$w,$Df=0,$K=" "){return" $D$Z".($w?$K."LIMIT $w".($Df?" OFFSET $Df":""):"");}function
limit1($Q,$D,$Z,$K="\n"){return
limit($D,$Z,1,0,$K);}function
db_collation($h,array$cb){$F=null;$zb=get_val("SHOW CREATE DATABASE ".idf_escape($h),1);if(preg_match('~ COLLATE ([^ ]+)~',$zb,$y))$F=$y[1];elseif(preg_match('~ CHARACTER SET ([^ ]+)~',$zb,$y))$F=$cb[$y[1]][-1];return$F;}function
logged_user(){return
get_val("SELECT CURRENT_USER()");}function
tables_list(){return
get_key_vals("SELECT TABLE_NAME, TABLE_TYPE FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() ORDER BY TABLE_NAME");}function
count_tables(array$Ib){$F=array();foreach($Ib
as$h)$F[$h]=count(get_vals("SHOW TABLES IN ".idf_escape($h)));return$F;}function
table_status($z="",$Fc=false){$F=array();$D="SELECT ENGINE AS Engine, TABLE_NAME AS Name, TABLE_COMMENT AS Comment FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() ".($z!=""?"AND TABLE_NAME = ".q($z):"ORDER BY Name");$I=array();foreach(($Fc?array():get_rows($D))as$G)$I[$G["Name"]]=$G;$zg=null;foreach(get_rows($Fc?$D:"SHOW TABLE STATUS".($z!=""?" LIKE ".q(addcslashes($z,"%_\\")):""))as$G){$Rf=idx($I,$G["Name"]);if($Rf){if($G["Comment"]!==$Rf["Comment"]&&$G["Comment"]!==$zg)$G["Error"]=$G["Comment"];$zg=$G["Comment"];$G["Comment"]=$Rf["Comment"];$G["Engine"]=$Rf["Engine"];}if($G["Engine"]=="InnoDB")$G["Comment"]=preg_replace('~(?:(.+); )?InnoDB free: .*~','\1',$G["Comment"]);if(!isset($G["Engine"]))$G["Comment"]="";if($z!="")$G["Name"]=$z;$F[$G["Name"]]=$G;}return$F;}function
is_view(array$R){return$R["Engine"]===null;}function
fk_support(array$R){return
preg_match('~InnoDB|IBMDB2I'.(min_version(5.6)?'|NDB':'').'~i',$R["Engine"]);}function
parse_type($jd){preg_match('~^([^( ]+)(?:\((.+)\))?( unsigned)?( zerofill)?$~',$jd,$y);return
array($y[1],$y[2],ltrim($y[3].$y[4]));}function
fields($Q){$Pe=(connection()->flavor=='maria');$F=array();foreach(get_rows("SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ".q($Q)." ORDER BY ORDINAL_POSITION")as$G){$k=$G["COLUMN_NAME"];$T=$G["COLUMN_TYPE"];$od=$G["GENERATION_EXPRESSION"];$Dc=$G["EXTRA"];preg_match('~^(VIRTUAL|PERSISTENT|STORED)~',$Dc,$nd);list($Ki,$v,$Ti)=parse_type($T);$i=$G["COLUMN_DEFAULT"];if($i!=""){$me=preg_match('~text|json~',$Ki);if(!$Pe&&$me)$i=preg_replace("~^(_\w+)?('.*')$~",'\2',stripslashes($i));if($Pe||$me){$i=($i=="NULL"?null:preg_replace_callback("~^'(.*)'$~",function($y){return
stripslashes(str_replace("''","'",$y[1]));},$i));}if(!$Pe&&preg_match('~binary~',$Ki)&&preg_match('~^0x(\w*)$~',$i,$y))$i=pack("H*",$y[1]);}$F[$k]=array("field"=>$k,"full_type"=>$T,"type"=>$Ki,"length"=>$v,"unsigned"=>$Ti,"default"=>($nd?($Pe?$od:stripslashes($od)):$i),"null"=>($G["IS_NULLABLE"]=="YES"),"auto_increment"=>($Dc=="auto_increment"),"on_update"=>(preg_match('~\bon update (\w+)~i',$Dc,$y)?$y[1]:""),"collation"=>$G["COLLATION_NAME"],"privileges"=>array_flip(explode(",","$G[PRIVILEGES],where,order")),"comment"=>$G["COLUMN_COMMENT"],"primary"=>($G["COLUMN_KEY"]=="PRI"),"generated"=>($nd[1]=="PERSISTENT"?"STORED":$nd[1]),);}return$F;}function
indexes($Q,$g=null){$F=array();foreach(get_rows("SHOW INDEX FROM ".table($Q),$g)as$G){$z=$G["Key_name"];$F[$z]["type"]=($z=="PRIMARY"?"PRIMARY":($G["Index_type"]=="FULLTEXT"?"FULLTEXT":($G["Non_unique"]?(preg_match('~^(SPATIAL|VECTOR)$~',$G["Index_type"])?$G["Index_type"]:"INDEX"):"UNIQUE")));$F[$z]["columns"][]=$G["Column_name"];$F[$z]["lengths"][]=($G["Index_type"]=="SPATIAL"?null:$G["Sub_part"]);$F[$z]["descs"][]=null;$F[$z]["algorithm"]=$G["Index_type"];}return$F;}function
foreign_keys($Q){static$lg='(?:`(?:[^`]|``)+`|"(?:[^"]|"")+")';$F=array();$_b=get_val("SHOW CREATE TABLE ".table($Q),1);if($_b){preg_match_all("~CONSTRAINT ($lg) FOREIGN KEY ?\\(((?:$lg,? ?)+)\\) REFERENCES ($lg)(?:\\.($lg))? \\(((?:$lg,? ?)+)\\)(?: ON DELETE (".driver()->onActions."))?(?: ON UPDATE (".driver()->onActions."))?~",$_b,$Se,PREG_SET_ORDER);foreach($Se
as$y){preg_match_all("~$lg~",$y[2],$Nh);preg_match_all("~$lg~",$y[5],$ii);$F[idf_unescape($y[1])]=array("db"=>idf_unescape($y[4]!=""?$y[3]:$y[4]),"table"=>idf_unescape($y[4]!=""?$y[4]:$y[3]),"source"=>array_map('Adminer\idf_unescape',$Nh[0]),"target"=>array_map('Adminer\idf_unescape',$ii[0]),"on_delete"=>($y[6]?:"RESTRICT"),"on_update"=>($y[7]?:"RESTRICT"),);}}return$F;}function
view($z){return
array("select"=>preg_replace('~^(?:[^`]|`[^`]*`)*\s+AS\s+~isU','',get_val("SHOW CREATE VIEW ".table($z),1)));}function
collations(){$F=array();foreach(get_rows("SHOW COLLATION")as$G){if($G["Default"])$F[$G["Charset"]][-1]=$G["Collation"];else$F[$G["Charset"]][]=$G["Collation"];}ksort($F);foreach($F
as$u=>$W)sort($F[$u]);return$F;}function
information_schema($h,$I=""){return($h=="information_schema")||(min_version(5.5)&&$h=="performance_schema");}function
error(){return
h(preg_replace('~^You have an error.*syntax to use~U',"Syntax error",connection()->error));}function
create_database($h,$bb){return
queries("CREATE DATABASE ".idf_escape($h).($bb?" COLLATE ".q($bb):""));}function
drop_databases(array$Ib){$F=apply_queries("DROP DATABASE",$Ib,'Adminer\idf_escape');restart_session();set_session("dbs",null);return$F;}function
rename_database($z,$bb){$F=false;if(create_database($z,$bb)){$S=array();$nj=array();foreach(tables_list()as$Q=>$T){if($T=='VIEW')$nj[]=$Q;else$S[]=$Q;}$F=(!$S&&!$nj)||move_tables($S,$nj,$z);drop_databases($F?array(DB):array());}return$F;}function
auto_increment(){$ya=" PRIMARY KEY";if($_GET["create"]!=""&&$_POST["auto_increment_col"]){foreach(indexes($_GET["create"])as$s){if(in_array($_POST["fields"][$_POST["auto_increment_col"]]["orig"],$s["columns"],true)){$ya="";break;}if($s["type"]=="PRIMARY")$ya=" UNIQUE";}}return" AUTO_INCREMENT$ya";}function
alter_table($Q,$z,array$l,array$Wc,$fb,$oc,$bb,$xa,$eg){$b=array();foreach($l
as$k){if($k[1]){$i=$k[1][3];if(preg_match('~ GENERATED~',$i)){$k[1][3]=(connection()->flavor=='maria'?"":$k[1][2]);$k[1][2]=$i;}$b[]=($Q!=""?($k[0]!=""?"CHANGE ".idf_escape($k[0]):"ADD"):" ")." ".implode($k[1]).($Q!=""?$k[2]:"");}else$b[]="DROP ".idf_escape($k[0]);}$b=array_merge($b,$Wc);$O=($fb!==null?" COMMENT=".q($fb):"").($oc?" ENGINE=".q($oc):"").($bb?" COLLATE ".q($bb):"").($xa!=""?" AUTO_INCREMENT=$xa":"");if($eg){$fg=array();if($eg["partition_by"]=='RANGE'||$eg["partition_by"]=='LIST'){foreach($eg["partition_names"]as$u=>$W){$X=$eg["partition_values"][$u];$fg[]="\n  PARTITION ".idf_escape($W)." VALUES ".($eg["partition_by"]=='RANGE'?"LESS THAN":"IN").($X!=""?" ($X)":" MAXVALUE");}}$O
.="\nPARTITION BY $eg[partition_by]($eg[partition])";if($fg)$O
.=" (".implode(",",$fg)."\n)";elseif($eg["partitions"])$O
.=" PARTITIONS ".(+$eg["partitions"]);}elseif($eg===null)$O
.="\nREMOVE PARTITIONING";if($Q=="")return
queries("CREATE TABLE ".table($z)." (\n".implode(",\n",$b)."\n)$O");if($Q!=$z)$b[]="RENAME TO ".table($z);if($O)$b[]=ltrim($O);return($b?queries("ALTER TABLE ".table($Q)."\n".implode(",\n",$b)):true);}function
alter_indexes($Q,$b){$Pa=array();foreach($b
as$W)$Pa[]=($W[2]=="DROP"?"\nDROP INDEX ".idf_escape($W[1]):"\nADD $W[0] ".($W[0]=="PRIMARY"?"KEY ":"").($W[1]!=""?idf_escape($W[1])." ":"")."(".implode(", ",$W[2]).")");return
queries("ALTER TABLE ".table($Q).implode(",",$Pa));}function
truncate_tables(array$S){return
apply_queries("TRUNCATE TABLE",$S);}function
drop_views(array$nj){return
queries("DROP VIEW ".implode(", ",array_map('Adminer\table',$nj)));}function
drop_tables(array$S){return
queries("DROP TABLE ".implode(", ",array_map('Adminer\table',$S)));}function
move_tables(array$S,array$nj,$ii){$Tg=array();foreach($S
as$Q)$Tg[]=table($Q)." TO ".idf_escape($ii).".".table($Q);if(!$Tg||queries("RENAME TABLE ".implode(", ",$Tg))){$Nb=array();foreach($nj
as$Q)$Nb[table($Q)]=view($Q);connection()->select_db($ii);$h=idf_escape(DB);foreach($Nb
as$z=>$mj){if(!queries("CREATE VIEW $z AS ".str_replace(" $h."," ",$mj["select"]))||!queries("DROP VIEW $h.$z"))return
false;}return
true;}return
false;}function
copy_tables(array$S,array$nj,$ii){queries("SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO'");foreach($S
as$Q){$z=($ii==DB?table("copy_$Q"):idf_escape($ii).".".table($Q));if(($_POST["overwrite"]&&!queries("\nDROP TABLE IF EXISTS $z"))||!queries("CREATE TABLE $z LIKE ".table($Q))||!queries("INSERT INTO $z SELECT * FROM ".table($Q)))return
false;foreach(get_rows("SHOW TRIGGERS LIKE ".q(addcslashes($Q,"%_\\")))as$G){$Ei=$G["Trigger"];list($vc,$Cf)=trigger_event($G);if(!queries("CREATE TRIGGER ".($ii==DB?idf_escape("copy_$Ei"):idf_escape($ii).".".idf_escape($Ei))." $G[Timing] $vc".($Cf!=""?" $Cf":"")." ON $z FOR EACH ROW\n$G[Statement];"))return
false;}}foreach($nj
as$Q){$z=($ii==DB?table("copy_$Q"):idf_escape($ii).".".table($Q));$mj=view($Q);if(($_POST["overwrite"]&&!queries("DROP VIEW IF EXISTS $z"))||!queries("CREATE VIEW $z AS $mj[select]"))return
false;}return
true;}function
trigger_event(array$G){$wc=explode(",",$G["Event"]);$F=array();foreach(array("DELETE","INSERT","UPDATE")as$vc){if(in_array($vc,$wc))$F[]=$vc;}$F=implode(" OR ",$F);if(in_array("UPDATE",$wc)&&min_version('','12.0.1')&&preg_match('~\s(?:BEFORE|AFTER)\s+(.+?)\s+ON\s~is',get_val("SHOW CREATE TRIGGER ".idf_escape($G["Trigger"]),2),$y)&&preg_match('~\bOF\s+(.+)~is',$y[1],$Cf))return
array("$F OF",$Cf[1]);return
array($F,"");}function
trigger($z,$Q){if($z=="")return
array();$H=get_rows("SHOW TRIGGERS WHERE `Trigger` = ".q($z));$F=reset($H);if($F)list($F["Event"],$F["Of"])=trigger_event($F);return($F?:array());}function
triggers($Q){$F=array();foreach(get_rows("SHOW TRIGGERS LIKE ".q(addcslashes($Q,"%_\\")))as$G){list($vc)=trigger_event($G);$F[$G["Trigger"]]=array($G["Timing"],$vc);}return$F;}function
trigger_options(){return
array("Timing"=>array("BEFORE","AFTER"),"Event"=>(min_version('','12.0.1')?array("INSERT","UPDATE","UPDATE OF","DELETE","INSERT OR UPDATE","INSERT OR UPDATE OF","DELETE OR INSERT","DELETE OR UPDATE","DELETE OR UPDATE OF","DELETE OR INSERT OR UPDATE","DELETE OR INSERT OR UPDATE OF",):array("INSERT","UPDATE","DELETE")),"Type"=>array("FOR EACH ROW"),);}function
routine($z,$T){$H=get_rows("SELECT PARAMETER_NAME, DTD_IDENTIFIER, PARAMETER_MODE, COLLATION_NAME
FROM information_schema.PARAMETERS
WHERE SPECIFIC_SCHEMA = DATABASE() AND ROUTINE_TYPE = '$T' AND SPECIFIC_NAME = ".q($z)."
ORDER BY ORDINAL_POSITION");$l=array();foreach($H
as$G){$jd=$G["DTD_IDENTIFIER"];list($Ki,$v,$Ti)=parse_type($jd);$l[]=array("field"=>$G["PARAMETER_NAME"],"type"=>$Ki,"length"=>$v,"unsigned"=>$Ti,"null"=>true,"full_type"=>$jd,"inout"=>($T=="FUNCTION"?"":$G["PARAMETER_MODE"]),"collation"=>$G["COLLATION_NAME"],);}$F=connection()->query("SELECT
	ROUTINE_COMMENT comment,
	ROUTINE_DEFINITION definition,
	LOWER(EXTERNAL_LANGUAGE) language,
	IF(DEFINER = CURRENT_USER(), '', DEFINER) definer,
	IF(IS_DETERMINISTIC = 'YES', 'DETERMINISTIC', 'NOT DETERMINISTIC') is_deterministic,
	SQL_DATA_ACCESS data_access,
	CONCAT('SQL SECURITY ', SECURITY_TYPE) security
FROM information_schema.ROUTINES
WHERE ROUTINE_SCHEMA = DATABASE() AND ROUTINE_TYPE = '$T' AND ROUTINE_NAME = ".q($z))->fetch_assoc();if(!$F)return
array();$F['options']=array("DEFINER"=>$F['definer'],"DETERMINISTIC"=>$F['is_deterministic'],"SQL_DATA_ACCESS"=>$F['data_access'],"SQL_SECURITY"=>$F['security'],"COMMENT"=>$F['comment'],);if($l&&$l[0]['field']=='')$F['returns']=array_shift($l);$F['fields']=$l;return$F;}function
routines(){return
get_rows("SELECT SPECIFIC_NAME, ROUTINE_NAME, ROUTINE_TYPE, DTD_IDENTIFIER FROM information_schema.ROUTINES WHERE ROUTINE_SCHEMA = DATABASE()");}function
routine_languages(){return(min_version(9,99)?array("sql"=>"sql","javascript"=>"js"):array());}function
routine_options($ch){return
array("DEFINER"=>array(),"DETERMINISTIC"=>array("NOT DETERMINISTIC","DETERMINISTIC"),"SQL_DATA_ACCESS"=>array("CONTAINS SQL","NO SQL","READS SQL DATA","MODIFIES SQL DATA"),"SQL_SECURITY"=>array("SQL SECURITY DEFINER","SQL SECURITY INVOKER"),"COMMENT"=>array(),);}function
routine_id($z,array$G){return
idf_escape($z);}function
last_id($E){return
get_val("SELECT LAST_INSERT_ID()");}function
explain(Db$f,$D){return$f->query("EXPLAIN ".(min_version(5.7)?"":"PARTITIONS ").$D);}function
found_rows(array$R,array$Z){return($Z||$R["Engine"]!="InnoDB"?null:$R["Rows"]);}function
create_sql($Q,$xa,$Wh){$F=get_val("SHOW CREATE TABLE ".table($Q),1);if(!$xa)$F=preg_replace('~(\n\)[^\n]*?) AUTO_INCREMENT=\d+~','\1',$F);return$F;}function
truncate_sql($Q){return"TRUNCATE ".table($Q);}function
use_sql($Hb,$Wh=""){$z=idf_escape($Hb);$F="";if(preg_match('~CREATE~',$Wh)&&($zb=get_val("SHOW CREATE DATABASE $z",1))){set_utf8mb4($zb);if($Wh=="DROP+CREATE")$F="DROP DATABASE IF EXISTS $z;\n";$F
.="$zb;\n";}return$F."USE $z";}function
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
unconvert_field(array$k,$F){if(preg_match("~binary~",$k["type"]))$F="UNHEX($F)";if($k["type"]=="bit")$F="CONVERT(b$F, UNSIGNED)";if($k["type"]=="vector")$F=(connection()->flavor=='maria'?"VEC_FromText":"STRING_TO_VECTOR")."($F)";if(preg_match("~geom|point|linestring|polygon~",$k["type"])){$xg=(min_version(8)?"ST_":"");$F=$xg."GeomFromText($F, $xg"."SRID($k[field]))";}return$F;}function
support($Gc){return
preg_match('~^(comment|columns|copy|database|drop_col|dump|event|indexes|kill|privileges|move_col|procedure|processlist|routine|sql|status|table|trigger|variables|view'.(min_version(8)?'|descidx':'').(min_version('8.0.16','10.2.1')?'|check':'').(min_version(8,99)?'|fast_status':'').')$~',$Gc);}function
kill_process($q){return
queries("KILL ".number($q));}function
connection_id(){return"SELECT CONNECTION_ID()";}function
max_connections(){return
get_val("SELECT @@max_connections");}function
types($Cc=false){return
array();}function
type_values($q){return"";}function
type_definition($q){return
array("kind"=>"","definition"=>"");}function
schemas(){return
array();}function
get_schema(){return"";}function
set_schema($I,$g=null){return
true;}}define('Adminer\JUSH',Driver::$jush);define('Adminer\SERVER',"".$_GET[DRIVER]);define('Adminer\DB',"$_GET[db]");define('Adminer\ME',preg_replace('~\?.*~','',relative_uri()).'?'.(sid()?SID.'&':'').($_GET["ext"]?"ext=".url_escape($_GET["ext"]).'&':'').(isset($_GET[DRIVER])?DRIVER."=".url_escape(SERVER).'&':'').(isset($_GET["username"])?"username=".url_escape($_GET["username"]).'&':'').(isset($_GET["db"])?'db='.url_escape(DB).'&'.(isset($_GET["ns"])?"ns=".url_escape($_GET["ns"])."&":""):''));if(isset($_GET["manifest"])){header("Content-Type: application/manifest+json; charset=utf-8");header("Cache-Control: no-cache");echo
json_encode(adminer()->manifest(),64|256);exit;}function
page_header($ri,$j="",$La=array(),$si="",$zf=false){if($zf){header("HTTP/1.1 404 Not Found");$j=($j?:lang(68));}page_headers();if(is_ajax()&&$j){page_messages($j);exit;}if(!ob_get_level())ob_start('ob_gzhandler',4096);$ti=$ri.($si!=""?": $si":"");$ui=strip_tags($ti.(SERVER!=""&&SERVER!="localhost"?h(" - ".SERVER):"")." - ".adminer()->name());echo'<!DOCTYPE html>
<html lang=\'',LANG,'\' dir=\'',lang(69),'\' class=\'',lang(69),' nojs\'>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="robots" content="noindex">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>',$ui,'</title>
<link rel="stylesheet" href="',h(preg_replace("~\\?.*~","",ME)."?file=default.css&version=6.1.0+5f12f1ea"),'">
';$Cb=adminer()->css();if(is_int(key($Cb)))$Cb=array_fill_keys($Cb,'light');$yd=in_array('light',$Cb)||in_array('',$Cb);$vd=in_array('dark',$Cb)||in_array('',$Cb);$Fb=($yd?($vd?null:false):($vd?:null));$df=" media='(prefers-color-scheme: dark)'";if($Fb!==false)echo"<link rel='stylesheet'".($Fb?"":$df)." href='".h(preg_replace("~\\?.*~","",ME)."?file=dark.css&version=6.1.0+5f12f1ea")."'>\n";echo"<meta name='color-scheme' content='".($Fb===null?"light dark":($Fb?"dark":"light"))."'>\n",script_src(preg_replace("~\\?.*~","",ME)."?file=functions.js&version=6.1.0+5f12f1ea");if(adminer()->head($Fb))echo"<link rel='icon' href='data:image/gif;base64,"."R0lGODlhEAAQAJEAAAQCBPz+/PwCBAROZCH5BAEAAAAALAAAAAAQABAAAAI2hI+pGO1rmghihiUdvUBnZ3XBQA7f05mOak1RWXrNq5nQWHMKvuoJ37BhVEEfYxQzHjWQ5qIAADs='>\n","<link rel='apple-touch-icon' href='".h(preg_replace("~\\?.*~","",ME)."?file=logo.svg&version=6.1.0+5f12f1ea")."'>\n";if(adminer()->manifest())echo"<link rel='manifest' href='".h(preg_replace('~\?.*~','',ME)."?manifest=")."' crossorigin='use-credentials'>\n";foreach($Cb
as$Yi=>$mf){$c=($mf=='dark'&&!$Fb?$df:($mf=='light'&&$vd?" media='(prefers-color-scheme: light)'":""));echo"<link rel='stylesheet'$c href='".h($Yi)."'>\n";}echo"\n<body class='";adminer()->bodyClass();echo"'>\n",script((isset($_COOKIE["adminer_version"])||!adminer()->verifyVersion()?"":"onload = partial(verifyVersion, '".VERSION."');\n")."
const offlineMessage = '".js_escape(lang(70))."';
const numberFormat = '".js_escape(lang(5))."';
const numberDigits = '".js_escape(lang(6))."';
const urlSeparators = '".js_escape(ini_get("arg_separator.input"))."';"),"<div id='help' class='jush-".JUSH." jsonly hidden'".on('mouseover','helpKeep').on('mouseout','helpMouseout')."></div>\n","<div id='content'>\n","<span id='menuopen' class='jsonly'".on('click','menuToggle')."><button title='".lang(71)."' class='icon icon-move' aria-expanded='false'></button></span>\n";if($La!==null){$x=substr(preg_replace('~\b(username|db|ns)=[^&]*&~','',ME),0,-1);echo'<p id="breadcrumb"><a href="'.h($x?:".").'">'.get_driver(DRIVER).'</a> » ';$x=substr(preg_replace('~\b(db|ns)=[^&]*&~','',ME),0,-1);$L=adminer()->serverName(SERVER);$L=($L!=""?$L:lang(72));if($La===false)echo"$L\n";else{echo"<a href='".h($x.(DB!=""&&support("single_db")?"&db=":""))."' accesskey='1' title='Alt+Shift+1'>$L</a> » ";$ih="";if(is_string($La)){$ih=$La;$La=array();}if($_GET["ns"]!=""||(DB!=""&&is_array($La))){$Jb="$x&db=".url_escape(DB).(support("scheme")?"&ns=":"").(support("single_table")?"&select=":"");echo'<a href="'.h($Jb.($_GET["ns"]==""?$ih:"")).'">'.h(DB).'</a> » ';}if(is_array($La)){if($_GET["ns"]!="")echo'<a href="'.h(substr(ME,0,-1).$ih).'">'.h($_GET["ns"]).'</a> » ';foreach($La
as$u=>$W){$Pb=(is_array($W)?$W[1]:h($W));if($Pb!="")echo"<a href='".h(ME."$u=").url_escape(is_array($W)?$W[0]:$W)."'>$Pb</a> » ";}}echo"$ri\n";}}echo"<h2>$ti</h2>\n","<div id='ajaxstatus' role='status' class='jsonly'></div>\n";restart_session();page_messages($j);adminer()->serviceWorker();$Ib=&get_session("dbs");if(DB!=""&&$Ib&&!in_array(DB,$Ib,true))$Ib=null;stop_session();define('Adminer\PAGE_HEADER',1);ob_flush();flush();if($zf){page_footer($zf===true?"":$zf);exit;}}function
service_worker(){$Qg=has_passwords();$Za=($Qg?"navigator.serviceWorker.register('".js_escape(preg_replace('~\?.*~','',ME)."?file=worker.js&version=6.1.0+5f12f1ea")."', {scope: location.pathname}).catch(() => {});":"navigator.serviceWorker.getRegistration().then(registration => registration && registration.unregister());
	caches.keys().then(keys => keys.forEach(key => key.startsWith('adminer-') && caches.delete(key)));");echo
script("if (navigator.serviceWorker) {\n\t$Za\n}");}function
has_passwords(){foreach((array)$_SESSION["pwds"]as$Bh){foreach($Bh
as$fj){foreach($fj
as$C){if($C!==null)return
true;}}}return
false;}function
page_headers(){header("Content-Type: text/html; charset=utf-8");header("Cache-Control: no-cache");header("X-Frame-Options: deny");header("X-XSS-Protection: 0");header("X-Content-Type-Options: nosniff");header("Referrer-Policy: origin-when-cross-origin");foreach(adminer()->csp(csp())as$Bb){$Bd=array();foreach($Bb
as$u=>$W)$Bd[]="$u $W";header("Content-Security-Policy: ".implode("; ",$Bd));}adminer()->headers();}function
csp(){return
array(array("script-src"=>"'self' 'unsafe-inline' 'nonce-".get_nonce()."' 'strict-dynamic'","connect-src"=>"'self' https://www.adminer.org","frame-src"=>"https://www.adminer.org","object-src"=>"'none'","base-uri"=>"'none'","form-action"=>"'self'",),);}function
design_checksums(){$dj=array();foreach(array_keys(adminer()->css())as$Yi)$dj[preg_replace('~\?.*~','',$Yi)]=true;$F=array();foreach(array("adminer.css","adminer-dark.css")as$m){if($dj[$m]&&file_exists($m)){preg_match('~^/\* Adminer design ([-\w]+) \*/~',file_get_contents($m),$y);$F[$m]=array((string)$y[1],Plugins::checksum($m));}}return$F;}function
official_design_checksums(){return
array('adminer-border/adminer.css'=>'ec757f3e','adminer-dark/adminer-dark.css'=>'a26bcd7b','brade/adminer.css'=>'be4161f0','bueltge/adminer.css'=>'1a8f00b4','cpanel/adminer.css'=>'59ce604e','dracula/adminer-dark.css'=>'cfaf61dd','esterka/adminer.css'=>'1f805f36','flat/adminer.css'=>'49a61af9','galkaev/adminer-dark.css'=>'16c46f94','haeckel/adminer.css'=>'147a3565','hever/adminer.css'=>'ef0e1948','konya/adminer.css'=>'2b409696','lavender-light/adminer.css'=>'bf03f5d7','lucas-sandery/adminer.css'=>'6596353','mancave/adminer-dark.css'=>'e1ac813d','mvt/adminer.css'=>'ebd3afdc','nette/adminer.css'=>'5ab360e7','ng9/adminer.css'=>'488583cf','nicu/adminer.css'=>'216f097b','pappu687/adminer.css'=>'b58d128c','paranoiq/adminer.css'=>'64d27e5','pepa-linha/adminer.css'=>'baf25f0','pokorny/adminer.css'=>'ee9eea6d','price/adminer.css'=>'81be9a85','rmsoft/adminer.css'=>'6cd4a237','rmsoft_blue-dark/adminer.css'=>'32102a8','rmsoft_blue/adminer.css'=>'7d8d5b18','win98/adminer.css'=>'e82d63c3',);}function
version_iframe(){return(isset($_COOKIE["adminer_version"])||!adminer()->verifyVersion()?"":"<noscript><iframe sandbox src='https://www.adminer.org/version/?current=".VERSION."&amp;noscript=1'></iframe></noscript>");}function
get_nonce(){static$yf;if(!$yf)$yf=base64_encode(rand_string());return$yf;}function
page_messages($j){$Xi=preg_replace('~^[^?]*~','',$_SERVER["REQUEST_URI"]);$gf=idx($_SESSION["messages"],$Xi);if($gf){echo"<div class='message'>".implode("</div>\n<div class='message'>",$gf)."</div>".script("messagesPrint();");unset($_SESSION["messages"][$Xi]);}if($j)echo"<div class='error'>$j</div>\n";if(adminer()->error)echo"<div class='error'>".adminer()->error."</div>\n";}function
page_footer($lf=""){echo"</div>\n\n<div id='foot' class='foot'>\n<div id='menu'>\n";adminer()->navigation($lf);echo"</div>\n";if($lf!="auth")echo'<form action="" method="post">
<p class="logout">
<span title="',lang(39),'">',h($_GET["username"])."\n",'</span>
<input type=\'submit\' name=\'logout\' value=\'',lang(73),'\' id=\'logout\'>
',input_token(),'</form>
';echo"</div>\n\n",script("setupSubmitHighlight(document);");}function
int32($sf){while($sf>=2147483648)$sf-=4294967296;while($sf<=-2147483649)$sf+=4294967296;return(int)$sf;}function
long2str(array$V,$pj){$eh='';foreach($V
as$W)$eh
.=pack('V',$W);if($pj)return
substr($eh,0,end($V));return$eh;}function
str2long($eh,$pj){$V=array_values(unpack('V*',str_pad($eh,4*ceil(strlen($eh)/4),"\0")));if($pj)$V[]=strlen($eh);return$V;}function
xxtea_mx($xj,$wj,$Yh,$qe){return
int32((($xj>>5&0x7FFFFFF)^$wj<<2)+(($wj>>3&0x1FFFFFFF)^$xj<<4))^int32(($Yh^$wj)+($qe^$xj));}function
encrypt_string($Vh,$u){if($Vh=="")return"";$u=array_values(unpack("V*",pack("H*",md5($u))));$V=str2long($Vh,true);$sf=count($V)-1;$xj=$V[$sf];$wj=$V[0];$Gg=floor(6+52/($sf+1));$Yh=0;while($Gg-->0){$Yh=int32($Yh+0x9E3779B9);$hc=$Yh>>2&3;for($Wf=0;$Wf<$sf;$Wf++){$wj=$V[$Wf+1];$rf=xxtea_mx($xj,$wj,$Yh,$u[$Wf&3^$hc]);$xj=int32($V[$Wf]+$rf);$V[$Wf]=$xj;}$wj=$V[0];$rf=xxtea_mx($xj,$wj,$Yh,$u[$Wf&3^$hc]);$xj=int32($V[$sf]+$rf);$V[$sf]=$xj;}return
long2str($V,false);}function
decrypt_string($Vh,$u){if($Vh=="")return"";if(!$u)return
false;$u=array_values(unpack("V*",pack("H*",md5($u))));$V=str2long($Vh,false);$sf=count($V)-1;$xj=$V[$sf];$wj=$V[0];$Gg=floor(6+52/($sf+1));$Yh=int32($Gg*0x9E3779B9);while($Yh){$hc=$Yh>>2&3;for($Wf=$sf;$Wf>0;$Wf--){$xj=$V[$Wf-1];$rf=xxtea_mx($xj,$wj,$Yh,$u[$Wf&3^$hc]);$wj=int32($V[$Wf]-$rf);$V[$Wf]=$wj;}$xj=$V[$sf];$rf=xxtea_mx($xj,$wj,$Yh,$u[$Wf&3^$hc]);$wj=int32($V[0]-$rf);$V[0]=$wj;$Yh=int32($Yh-0x9E3779B9);}return
long2str($V,true);}$ng=array();if($_COOKIE["adminer_permanent"]){foreach(explode(" ",$_COOKIE["adminer_permanent"])as$W){list($u)=explode(":",$W);$ng[$u]=$W;}}function
add_invalid_login(){$Ea=get_temp_dir()."/adminer-invalid";foreach(glob("$Ea*")?:array($Ea)as$m){$ed=file_open_lock($m);if($ed)break;}if(!$ed)$ed=file_open_lock("$Ea-".rand_string());if(!$ed)return;$ge=json_decode(stream_get_contents($ed),true);$oi=time();if($ge){foreach($ge
as$he=>$W){if($W[0]<$oi)unset($ge[$he]);}}$ee=&$ge[adminer()->bruteForceKey()];if(!$ee)$ee=array($oi+30*60,0);$ee[1]++;file_write_unlock($ed,json_encode($ge));}function
check_invalid_login(array&$ng){$ge=array();foreach(glob(get_temp_dir()."/adminer-invalid*")as$m){$ed=file_open_lock($m);if($ed){$ge=json_decode(stream_get_contents($ed),true);file_unlock($ed);break;}}$u=adminer()->bruteForceKey();$ee=idx($ge,$u,array());$xf=($ee[1]>29?$ee[0]-time():0);if($xf>0){$j=lang(74,ceil($xf/60));if($_SERVER["HTTP_X_FORWARDED_FOR"]!=""&&$u==$_SERVER["REMOTE_ADDR"])$j
.='<br>'.lang(75,'<b>login-reverse-proxy</b>'," href='https://www.adminer.org/plugins/?version=".VERSION."'".target_blank());auth_error($j,$ng,false);}}function
password_required(){static$F;if($F===null){$F=(bool)get_session("password_required");if(!$F){$Ab=adminer()->credentials();$F=!is_object(Driver::connect($Ab[0],$Ab[1],""));if($F)set_session("password_required",true);}}return$F;}function
require_password_link($C){$nf="<a href='https://www.adminer.org/password/'".target_blank().">".lang(76)."</a>";if(!function_exists('password_hash'))return" $nf";$pg=($C!==null?$C:base64_encode(substr(pack("H*",rand_string()),0,12)));$Ad=password_hash($pg,PASSWORD_DEFAULT);$m="adminer-plugins.php";$_c=file_exists("adminer-plugins.php");if($_c)$de=($C!==null?lang(77,"<b>$m</b>"):lang(78,"<b>$m</b>","<b>$pg</b>"));else{$m="<button name='password_less' value='".h($Ad)."' class='link'>$m</button>";$de=($C!==null?lang(79,$m):lang(80,$m,"<b>$pg</b>"));}$He="\t<a>new</a> Adminer\\Password(<span class='jush-apo'>'".h($Ad)."'</span>),";$F="<p>$de
<pre><code class='jush'>".($_c?$He:"&lt;?php\n<a>return</a> <a>array</a>(\n$He\n);")."</code></pre>
<p>$nf
";return" <a href='#password-less' class='toggle'>".lang(81)."</a>
<div id='password-less' class='hidden'>".($_c?$F:"<form action='' method='post'>\n".$F.input_token()."</form>")."</div>";}if(preg_match('~^[-\w$./]+$~',$_POST["password_less"])&&verify_token()){header("Content-Type: application/octet-stream");header("Content-Disposition: attachment; filename=adminer-plugins.php");echo"<?php\nreturn array(\n\tnew Adminer\\Password('$_POST[password_less]'),\n);\n";exit;}$wa=$_POST["auth"];if($wa&&(!adminer()->verifyLoginToken()||verify_token())){session_regenerate_id();$kj=$wa["driver"];$L=$wa["server"];$U=$wa["username"];$C=(string)$wa["password"];$h=$wa["db"];set_password($kj,$L,$U,$C);$_SESSION["db"][$kj][$L][$U][$h]=true;if($wa["permanent"]){$u=implode("-",array_map('base64_encode',array($kj,$L,$U,$h)));$Cg=adminer()->permanentLogin(true);$ng[$u]="$u:".base64_encode($Cg?encrypt_string($C,$Cg):"");cookie("adminer_permanent",implode(" ",$ng));}if(!array_diff(array_keys($_POST),array("auth","token"))||$kj!=DRIVER||$L!=SERVER||$U!==$_GET["username"]||$h!=DB)redirect(auth_url($kj,$L,$U,$h));}elseif($_POST["logout"]&&(!$_SESSION["token"]||verify_token())){foreach(array("pwds","db","dbs","queries")as$u)set_session($u,null);unset_permanent($ng);redirect(substr(preg_replace('~\b(username|db|ns)=[^&]*&~','',ME),0,-1),lang(82).' '.lang(83));}elseif($ng&&!$_SESSION["pwds"]){session_regenerate_id();$Cg=adminer()->permanentLogin();foreach($ng
as$u=>$W){list(,$Wa)=explode(":",$W);list($kj,$L,$U,$h)=array_map('base64_decode',explode("-",$u));set_password($kj,$L,$U,decrypt_string(base64_decode($Wa),$Cg));$_SESSION["db"][$kj][$L][$U][$h]=true;}}function
unset_permanent(array&$ng){foreach($ng
as$u=>$W){list($kj,$L,$U,$h)=array_map('base64_decode',explode("-",$u));if($kj==DRIVER&&$L==SERVER&&$U==$_GET["username"]&&$h==DB)unset($ng[$u]);}cookie("adminer_permanent",implode(" ",$ng));}function
auth_error($j,array&$ng,$fe=true){$Ch=session_name();if(isset($_GET["username"])){header("HTTP/1.1 403 Forbidden");if(($_COOKIE[$Ch]||$_GET[$Ch])&&!$_SESSION["token"])$j=lang(84);elseif($fe&&($C=get_password())!==null){restart_session();add_invalid_login();if($C===false)$j
.=($j?'<br>':'').lang(85,target_blank(),'<code>permanentLogin()</code>');set_password(DRIVER,SERVER,$_GET["username"],null);unset_permanent($ng);}}if(!$_COOKIE[$Ch]&&$_GET[$Ch]&&ini_bool("session.use_only_cookies"))$j=lang(86);$ag=session_get_cookie_params();cookie("adminer_key",($_COOKIE["adminer_key"]?:rand_string()),$ag["lifetime"]);if(!$_SESSION["token"])$_SESSION["token"]=rand(1,1e6);page_header(lang(41),$j,null);echo"<form action='' method='post'>\n","<div>";if(hidden_fields($_POST,array("auth","token")))echo"<p class='message'>".lang(87)."\n";echo
input_token(),"</div>\n";adminer()->loginForm();echo"</form>\n";page_footer("auth");exit;}if(isset($_GET["username"])&&!class_exists('Adminer\Db')){unset($_SESSION["pwds"][DRIVER]);unset_permanent($ng);page_header(lang(88),lang(89,implode(", ",Driver::$extensions)),false);page_footer("auth");exit;}$f='';if(isset($_GET["username"])&&is_string(get_password())){check_invalid_login($ng);$Ab=adminer()->credentials();$f=Driver::connect($Ab[0],$Ab[1],$Ab[2]);if(is_object($f)){Db::$instance=$f;Driver::$instance=new
Driver($f);if($f->flavor)save_settings(array("vendor-".DRIVER."-".SERVER=>get_driver(DRIVER)));}}$Ne=null;if(!is_object($f)||($Ne=adminer()->login($_GET["username"],get_password()))!==true){$j=(is_string($f)?nl_br(h($f)):(is_string($Ne)?$Ne:lang(90))).(preg_match('~^ | $~',get_password())?'<br>'.lang(91):'');auth_error($j,$ng);}if($_POST["logout"]&&$_SESSION["token"]&&!verify_token()){page_header(lang(73),lang(92));page_footer("db");exit;}if(!$_SESSION["token"])$_SESSION["token"]=rand(1,1e6);stop_session(true);if($wa&&$_POST["token"])$_POST["token"]=get_token();$j='';if($_POST){if(!verify_token()){header("HTTP/1.1 403 Forbidden");$j=lang(92).' '.lang(93);}}elseif($_SERVER["REQUEST_METHOD"]=="POST"){header("HTTP/1.1 413 Content Too Large");$j=lang(94,"<b>post_max_size</b>");if(isset($_GET["sql"]))$j
.=' '.lang(95);}function
doc_link(array$kg,$li=""){return"";}function
like_bool(array$k){return$k["type"]=="bool"||(preg_match('~bit|tinyint~',$k["type"])&&$k["length"]==1);}function
sql_comment($D,$oi=""){return"<!--\n".str_replace("--","--><!-- ",$D)."\n".($oi!=""?"($oi)\n":"")."-->\n";}connection()->select_db(adminer()->database());if(support("scheme")){$I=get_schema();if($I!="")set_schema($I);}adminer()->afterConnect();add_driver(DRIVER,lang(41));if(isset($_GET["select"])&&($_POST["edit"]||$_POST["clone"])&&!$_POST["save"])$_GET["edit"]=$_GET["select"];if(isset($_GET["download"])){$a=$_GET["download"];$l=fields($a);header("Content-Type: application/octet-stream");$Y=array_merge((array)$_GET["where"],(array)$_GET["val"]);header("Content-Disposition: attachment; filename=".friendly_url("$a-".implode("_",$Y)).".".friendly_url($_GET["field"]));$J=array(idf_escape($_GET["field"]));$E=driver()->select($a,$J,array(where($_GET,$l)),$J);$G=($E?$E->fetch_row():array());echo
driver()->value($G[0],$l[$_GET["field"]]);exit;}elseif(isset($_GET["edit"])){$a=$_GET["edit"];$l=fields($a);$Z=(isset($_GET["select"])?($_POST["check"]&&count($_POST["check"])==1?where_check($_POST["check"][0],$l):""):where($_GET,$l));$Vi=(isset($_GET["select"])?$_POST["edit"]:$Z);foreach($l
as$z=>$k){if((!$Vi&&!isset($k["privileges"]["insert"]))||adminer()->fieldName($k)=="")unset($l[$z]);}if($_POST&&!$j&&!isset($_GET["select"])){$Me=relative_uri((string)$_POST["referer"]);if($_POST["insert"])$Me=($Vi?null:relative_uri());elseif(!preg_match('~^.+&select=.+$~',$Me))$Me=ME."select=".url_escape($a);$t=indexes($a);$Oi=unique_array($_GET["where"],$t);$Jg="\nWHERE $Z";if(isset($_POST["delete"]))queries_redirect($Me,lang(96),driver()->delete($a,$Jg,$Oi?0:1));else{$M=array();foreach($l
as$z=>$k){$W=process_input($k);if($W!==false&&$W!==null)$M[idf_escape($z)]=$W;}if($Vi){if(!$M)redirect($Me);queries_redirect($Me,lang(97),driver()->update($a,$M,$Jg,$Oi?0:1));if(is_ajax()){page_headers();page_messages($j);exit;}}else{$E=driver()->insert($a,$M);$Be=($E?last_id($E):0);queries_redirect($Me,lang(98,($Be?" $Be":"")),$E);}}}$G=null;$D="";$oi="";if($Z){$J=array();$mh=array("*");foreach($l
as$z=>$k){if(isset($k["privileges"]["select"])){$ua=($_POST["clone"]&&$k["auto_increment"]?"''":convert_field($k));$d=($ua?"$ua AS ":"").idf_escape($z);$J[]=$d;if($ua)$mh[]=$d;}}$G=array();if(!support("table")){$J=array("*");$mh=$J;}if($J){$Sh=microtime(true);$E=driver()->select($a,$J,array($Z),$J,array(),(isset($_GET["select"])?2:1));$D=str_replace("SELECT ".implode(", ",$J),"SELECT ".implode(", ",$mh),driver()->query);$oi=format_time($Sh);if(!$E)$j=adminer()->error();else{$G=$E->fetch_assoc();if(!$G)$G=false;}if(isset($_GET["select"])&&(!$G||$E->fetch_assoc()))$G=null;}}if(!$l&&driver()->primary!=""){if(!$Z){$E=driver()->select($a,array("*"),array(),array("*"));$G=($E?$E->fetch_assoc():false);if(!$G)$G=array(driver()->primary=>"");}if($G){foreach($G
as$u=>$W){if(!$Z)$G[$u]=null;$l[$u]=array("field"=>$u,"null"=>($u!=driver()->primary),"auto_increment"=>($u==driver()->primary));}}}if($_POST["save"]){$ug=array();foreach((array)$_POST["fields"]as$u=>$W)$ug[bracket_escape($u,true)]=$W;$G=$ug+($G?$G:array());}edit_form($a,$l,$G,$Vi,$j,$D,$oi);}elseif(isset($_GET["select"])){$a=$_GET["select"];$R=table_status1($a);$t=indexes($a);$l=fields($a);$ad=column_foreign_keys($a);$Ef=$R["Oid"];$bh=array();$e=array();$hh=array();$Nf=array();$mi=null;foreach($l
as$u=>$k){$z=adminer()->fieldName($k);$tf=html_entity_decode(strip_tags($z),ENT_QUOTES);if(isset($k["privileges"]["select"])&&$z!=""){$e[$u]=$tf;if(is_shortable($k))$mi=adminer()->selectLengthProcess();}if(isset($k["privileges"]["where"])&&$z!="")$hh[$u]=$tf;if(isset($k["privileges"]["order"])&&$z!="")$Nf[$u]=$tf;$bh+=$k["privileges"];}list($J,$o)=adminer()->selectColumnsProcess($e,$t);$J=array_unique($J);$o=array_unique($o);$ke=count($o)<count($J);$Z=adminer()->selectSearchProcess($l,$t,$R);$Mf=adminer()->selectOrderProcess($l,$t);$w=adminer()->selectLimitProcess();if($_GET["val"]&&is_ajax()){header("Content-Type: text/plain; charset=utf-8");foreach($_GET["val"]as$Pi=>$G){$ua=convert_field($l[key($G)]);$J=array($ua?:idf_escape(key($G)));$Z[]=where_check(bracket_escape($Pi,true),$l);$F=driver()->select($a,$J,$Z,$J);if($F)echo
first($F->fetch_row());}exit;}$_g=$Si=array();foreach($t
as$s){if($s["type"]=="PRIMARY"){$_g=array_flip($s["columns"]);$Si=($J?$_g:array());foreach($Si
as$u=>$W){if(in_array(idf_escape($u),$J))unset($Si[$u]);}break;}}if($Ef&&!$_g){$_g=$Si=array($Ef=>0);$t[]=array("type"=>"PRIMARY","columns"=>array($Ef));}if($_POST&&!$j){$tj=$Z;if(!$_POST["all"]&&is_array($_POST["check"])){$Va=array();foreach($_POST["check"]as$Sa)$Va[]=where_check($Sa,$l);$tj[]="((".implode(") OR (",$Va)."))";}$vj=$tj;$tj=($tj?"\nWHERE ".implode(" AND ",$tj):"");if($_POST["export"]){save_settings(array("output"=>$_POST["output"],"format"=>$_POST["format"]),"adminer_import");dump_headers($a);adminer()->dumpTable($a,"");$lh=($J?:array("*"));$vb=convert_fields($e,$l,$J);if($vb)$lh[]=substr($vb,2);$D="";if(is_array($_POST["check"])&&!$_g){$hd=implode(", ",$lh)."\nFROM ".table($a);$qd=($o&&$ke?"\nGROUP BY ".implode(", ",$o):"").($Mf?"\nORDER BY ".implode(", ",$Mf):"");$Ni=array();foreach($_POST["check"]as$W)$Ni[]="(SELECT".limit($hd,"\nWHERE ".($Z?implode(" AND ",$Z)." AND ":"").where_check($W,$l).$qd,1).")";$D=implode(" UNION ALL ",$Ni);}adminer()->dumpData($a,"table",$D,$lh,$vj,($ke?$o:array()),$Mf);adminer()->dumpFooter();exit;}if(!adminer()->selectEmailProcess($Z,$ad)){if($_POST["save"]||$_POST["delete"]){$E=true;$la=0;$Ga=false;$M=array();if(!$_POST["delete"]){foreach($l
as$z=>$W){$r=bracket_escape($z);if(isset($_POST["fields"][$r])||$_FILES["fields-$r"]){$W=process_input($l[$z]);if($W!==null&&($_POST["clone"]||$W!==false))$M[idf_escape($z)]=($W!==false?$W:idf_escape($z));}}}if($_POST["delete"]||$M){$D=($_POST["clone"]?"INTO ".table($a)." (".implode(", ",array_keys($M)).")\nSELECT ".implode(", ",$M)."\nFROM ".table($a):"");if($_POST["all"]||($_g&&is_array($_POST["check"]))||$ke){$E=($_POST["delete"]?driver()->delete($a,$tj):($_POST["clone"]?queries("INSERT $D$tj".driver()->insertReturning($a)):driver()->update($a,$M,$tj)));$la=connection()->affected_rows;if(is_object($E))$la+=$E->num_rows;}else{$Ga=count((array)$_POST["check"])>1&&driver()->begin();foreach((array)$_POST["check"]as$W){$sj="\nWHERE ".($Z?implode(" AND ",$Z)." AND ":"").where_check($W,$l);$E=($_POST["delete"]?driver()->delete($a,$sj,1):($_POST["clone"]?queries("INSERT".limit1($a,$D,$sj)):driver()->update($a,$M,$sj,1)));if(!$E)break;$la+=connection()->affected_rows;}if($Ga&&$E&&!driver()->commit())$E=false;}}$ff=lang(99,$la);if($_POST["clone"]&&$E&&$la==1){$Be=last_id($E);if($Be)$ff=lang(98," $Be");}queries_redirect(remove_from_uri($_POST["all"]&&$_POST["delete"]?"page|next":""),$ff,$E);if($Ga)driver()->rollback();if(!$_POST["delete"]){$ug=(array)$_POST["fields"];edit_form($a,array_intersect_key($l,$ug),$ug,!$_POST["clone"],$j);page_footer();exit;}}elseif(!$_POST["import"]){$E=true;$la=0;$Ga=count((array)$_POST["val"])>1&&driver()->begin();foreach((array)$_POST["val"]as$Pi=>$G){$M=array();foreach($G
as$u=>$W){$u=bracket_escape($u,true);$M[idf_escape($u)]=(preg_match('~char|text~',$l[$u]["type"])||$W!=""?adminer()->processInput($l[$u],$W):"NULL");}$E=driver()->update($a,$M," WHERE ".($Z?implode(" AND ",$Z)." AND ":"").where_check(bracket_escape($Pi,true),$l),($ke||$_g?0:1)," ");if(!$E)break;$la+=connection()->affected_rows;}if($Ga)$E=$E&&driver()->commit();queries_redirect(remove_from_uri(),lang(99,$la),$E);if($Ga)driver()->rollback();}else{save_settings(array("format"=>$_POST["separator"]),"adminer_import");$Jc=get_file("csv_file",true);if(!is_string($Jc))$j=upload_error($Jc);elseif(!preg_match('~~u',$Jc))$j=lang(100);else{$db=array_keys($l);$K=($_POST["separator"]=="csv"?",":($_POST["separator"]=="tsv"?"\t":";"));$Db=parse_csv($Jc,$K);$la=count($Db);driver()->begin();$H=array();foreach($Db
as$u=>$Y){if(!$u&&!array_diff($Y,$db)){$db=$Y;$la--;}else{$M=array();foreach($Y
as$p=>$ab)$M[idf_escape($db[$p])]=($ab==""&&$l[$db[$p]]["null"]?"NULL":q(csv_value($ab)));$H[]=$M;}}$E=(!$H||driver()->insertUpdate($a,$H,$_g));if($E)driver()->commit();queries_redirect(remove_from_uri("page|next"),lang(101,$la),$E);driver()->rollback();}}}}$fi=adminer()->tableName($R);if(is_ajax()){page_headers();ob_start();}else
page_header(lang(57).": $fi",$j,array(),"",(!$l&&support("table")));$M=null;if(isset($bh["insert"])||!support("table")){$M="";foreach((array)$_GET["where"]as$W){$X=$W["val"];if(is_array($X))$X=(count($X)==1&&preg_match('~^val-(.*)~s',reset($X),$y)?$y[1]:"");if($W["col"]!=""&&$X!=""&&($W["op"]=="="||(!$W["op"]&&(is_array($W["val"])||!preg_match('~[_%]~',$X)))))$M
.="&set[".url_escape(bracket_escape($W["col"]))."]=".url_escape($X);}}adminer()->selectLinks($R,$M);if(!$e&&support("table"))echo"<p class='error'>".lang(102)."\n";else{echo"<form action='' id='form'>\n","<div hidden>";hidden_fields_get();echo(DB!=""?input_hidden("db",DB).(isset($_GET["ns"])?input_hidden("ns",$_GET["ns"]):""):""),input_hidden("select",$a),"</div>\n";adminer()->selectColumnsPrint($J,$e);adminer()->selectSearchPrint($Z,$hh,$t,$R);adminer()->selectOrderPrint($Mf,$Nf,$t);adminer()->selectLimitPrint($w);if($mi!==null)adminer()->selectLengthPrint($mi);adminer()->selectActionPrint($t);echo"</form>\n";foreach((array)$_GET["where"]as$W){if($W["op"]=="SQL"&&!in_array($_SERVER["HTTP_SEC_FETCH_SITE"],array("","same-origin"))){echo"<p class='error'>".lang(92).' '.lang(93)."\n";page_footer();exit;}}$B=$_GET["page"];$dd=null;if($B=="last"){$dd=get_val(count_rows($a,$Z,$ke,$o));$B=floor(max(0,intval($dd)-1)/$w);}$kh=$J;$pd=$o;if(!$kh){$kh[]="*";$vb=convert_fields($e,$l,$J);if($vb)$kh[]=substr($vb,2);}foreach($J
as$u=>$W){$k=$l[idf_unescape($W)];if($k&&($ua=convert_field($k)))$kh[$u]="$ua AS $W";}if(JUSH=="pgsql"||JUSH=="mssql"){foreach((array)$_GET["columns"]as$u=>$W){if(isset($kh[$u])&&$W["fun"])$kh[$u].=" AS ".idf_escape(apply_sql_function($W["fun"],($W["col"]!=""?$W["col"]:"*")));}}if(!$ke&&$Si){foreach($Si
as$u=>$W){$kh[]=idf_escape($u);if($pd)$pd[]=idf_escape($u);}}$E=driver()->select($a,$kh,$Z,$pd,$Mf,$w,$B,true);if(!is_object($E))echo"<p class='error'>".(adminer()->error()?:lang(25))."\n";else{if(JUSH=="mssql"&&$B)$E->seek($w*$B);$mc=array();$H=array();while($G=$E->fetch_assoc()){if($B&&JUSH=="oracle")unset($G["RNUM"]);$H[]=$G;}$zd=($w&&(support("cursor")?$_GET["next"]!="":count($H)>=$w));if(is_ajax()&&$zd)header("X-Next-Page: ".pagination_href($B+1));if($_GET["modify"]&&$H){$Ye=max_input_vars(count($H[0])+1,20);echo($Ye&&count($H)>$Ye?"<p class='error'>".max_input_vars_error()."\n":"");}echo"<form action='' method='post' enctype='multipart/form-data'".on_upload_progress($Wi).">\n";if($_GET["page"]!="last"&&$w&&$o&&$ke&&JUSH=="sql")$dd=get_val(" SELECT FOUND_ROWS()");if(!$H)echo"<p class='message'>".lang(15)."\n";else{$Da=adminer()->backwardKeys($a,$fi);$Zg=array();reset($J);foreach($H[0]as$u=>$W){if(!isset($Si[$u])){$W=idx($_GET["columns"],key($J))?:array();$Zg[$u]=array("fun"=>$W["fun"],"col"=>($J?$W["col"]:$u));next($J);}}echo"<div class='scrollable'>","<table id='table' class='nowrap checkable odds'".on('click','tableClick').on('dblclick','tableClick').on('keydown','editingKeydown').">\n","<thead><tr>".(!$o&&$J?"":"<td class='hover check'><input type='checkbox' id='all-page' class='jsonly' title='".lang(103)."'".on('click','formCheck','^check').">");$uf=array();$Mg=1;foreach($Zg
as$u=>$W){$k=$l[$W["col"]];$z=($k?adminer()->fieldName($k,$Mg):($W["fun"]?"*":h($u)));if($z!=""){$Mg++;$uf[$u]=$z;$d=idf_escape($u);$Id=remove_from_uri('(order|desc)[^=]*|page|next').'&order[0]='.url_escape($u);$Pb="&desc[0]=1";$Kh=preg_replace('~ DESC( NULLS LAST)?$~','',$Mf[0]);$Mh=($Kh==$d||$Kh==$u);echo"<th id='th[".h(bracket_escape($u))."]'".($Mh?" aria-sort='".($Kh==$Mf[0]?"ascending":"descending")."'":"").">";$ld=apply_sql_function(h($W["fun"]),$z);$Lh=isset($k["privileges"]["order"])||$W["fun"];echo($Lh?"<a href='".h($Id.($Mh&&$Kh==$Mf[0]?$Pb:''))."'>$ld</a>":$ld);$ef=($Lh?"<a href='".h($Id.$Pb)."' title='".lang(104)."' class='text'> ↓</a>":'');if(!$W["fun"]&&isset($k["privileges"]["where"]))$ef
.="<a href='#fieldset-search' title='".lang(51)."' class='text jsonly'".on('click','selectSearch',$u)."> =</a>";echo($ef?"<span class='column'>$ef</span>":"");}}$Fe=array();if($_GET["modify"]){foreach($H
as$G){foreach($G
as$u=>$W)$Fe[$u]=max($Fe[$u],min(40,utf8_length($W)));}}echo($Da?"<th>".lang(105):"")."<tbody>\n";if(is_ajax())ob_end_clean();foreach(adminer()->rowDescriptions($H,$ad)as$sf=>$G){$Oi=unique_array($H[$sf],$t);if(!$Oi){$Oi=array();foreach($H[$sf]as$u=>$W){if(!in_array(idx(idx($Zg,$u,array()),"fun"),driver()->grouping))$Oi[$u]=$W;}}$Pi="";$p=0;foreach($Oi
as$u=>$W){$Yg=idx($Zg,$u,array());$ld=idx($Yg,"fun","");$ab=($ld?$Yg["col"]:$u);$k=(array)$l[$ab];$je=is_blob($k);if(!$ld&&(JUSH=="sql"||JUSH=="pgsql")&&($je||preg_match('~'.text_type().'~',$k["type"]))&&strlen($W)>64){$ld="md5";$W=md5($je?(string)driver()->value($W,$k):$W);}if($ld){$Pi
.="&fun[$p]=".url_escape($ld)."&col[$p]=".url_escape($ab).($W!==null?"&val[$p]=".url_escape($W===false?"f":$W):"");$p++;}else$Pi
.="&".($W!==null?"where[".url_escape(bracket_escape($ab))."]=".url_escape($W===false?"f":$W):"null[]=".url_escape($ab));}echo"<tr>".(!$o&&$J?"":"<td class='hover check'>".($ke||information_schema(DB)?"":"<a href='".h(ME."edit=".url_escape($a).$Pi)."' class='edit'>".lang(106)."</a> ").checkbox("check[]",substr($Pi,1),in_array(substr($Pi,1),(array)$_POST["check"])));foreach($G
as$u=>$W){if(isset($uf[$u])){$ld=$Zg[$u]["fun"];$ab=$Zg[$u]["col"];$k=(array)$l[$u];if($W!=""&&(!isset($mc[$u])||$mc[$u]!=""))$mc[$u]=(is_mail($W)?$uf[$u]:"");$x="";if(is_blob($k)&&$W!="")$x=ME.'download='.url_escape($a).'&field='.url_escape($u).$Pi;if(!$x&&$W!==null){foreach((array)$ad[$u]as$Zc){if(count($ad[$u])==1||end($Zc["source"])==$u){$x="";foreach($Zc["source"]as$p=>$Nh)$x
.=where_link($p,$Zc["target"][$p],$H[$sf][$Nh]);$x=($Zc["db"]!=""?preg_replace('~([?&]db=)[^&]+~','\1'.url_escape($Zc["db"]),ME):ME).'select='.url_escape($Zc["table"]).$x;if($Zc["ns"])$x=preg_replace('~([?&]ns=)[^&]+~','\1'.url_escape($Zc["ns"]),$x);if(count($Zc["source"])==1)break;}}}if($ld=="count"&&$ab==""){$x=ME."select=".url_escape($a);$p=0;foreach((array)$_GET["where"]as$V){if(!array_key_exists($V["col"],$Oi))$x
.=where_link($p++,$V["col"],$V["val"],$V["op"]);}foreach($Oi
as$qe=>$V){if(idx(idx($Zg,$qe,array()),"fun")){$x="";break;}$x
.=where_link($p++,$qe,$V);}}$Jd=select_value($W,$x,$k,$mi);$r=bracket_escape($Pi);$q=h("val[$r][".bracket_escape($u)."]");$wg=idx(idx($_POST["val"],$r),bracket_escape($u));$Vi=idx($k["privileges"],"update");$jc=!is_array($G[$u])&&!is_blob($k)&&is_utf8($W)&&$H[$sf][$u]==$W&&!$ld&&!$k["generated"]&&$Vi;$T=($ld=="min"||$ld=="max"?$l[$ab]["type"]:$k["type"]);$li=preg_match('~text|json|lob~',$T);$le=preg_match(number_type(),$T)||preg_match('~^(avg|ceil|char_length|count|count distinct|floor|len|length|round|sum|time_to_sec)$~',$ld);echo"<td id='$q'".($le&&($W===null||is_numeric(strip_tags($Jd))||$T=="money")?" class='number'":"");if(($_GET["modify"]&&$jc&&$W!==null)||$wg!==null){$td=h($wg!==null?$wg:$W);echo">".($li?"<textarea name='$q' cols='30' rows='".(substr_count($W,"\n")+1)."'>$td</textarea>":"<input name='$q' value='$td' size='$Fe[$u]'>");}else{$Oe=strpos($Jd,"<i>…</i>");echo($Vi?" data-text='".($Oe?2:($li?1:0))."'".($jc?"":" data-warning='".lang(107)."'"):"").">$Jd";}}}if($Da)echo"<td>";adminer()->backwardKeysPrint($Da,$H[$sf]);echo"</tr>\n";}if(is_ajax())exit;echo"</table>\n","</div>\n";}if(!is_ajax()){$ka=get_settings("adminer_import");if($H||$B||$zd){$yc=true;if($_GET["page"]!="last"){if(!$w||(count($H)<$w&&($H||!$B)))$dd=($B?$B*$w:0)+count($H);elseif(JUSH!="sql"||!$ke){$dd=($ke?false:found_rows($R,$Z));if(intval($dd)<max(1e4,2*($B+1)*$w))$dd=first(slow_query(count_rows($a,$Z,$ke,$o)));elseif(JUSH=='sql'||JUSH=='pgsql')$yc=false;}}if(!support("cursor"))$zd=(($dd===false?count($H)+1:$dd-$B*$w)>$w);$Yf=($w&&($zd||$B));if($Yf)echo($zd?'<p><a href="'.h(pagination_href($B+1)).'" class="loadmore"'.on('click','selectLoadMore',lang(108)).'>'.lang(109).'</a>':''),"\n";echo"<div class='footer'><div>\n";if($Yf){$Xe=($dd===false?$B+($H?(count($H)>=$w?2:1):0):floor(($dd-1)/$w));echo"<fieldset><legend>".lang(110)."</legend>";if(!support("cursor")){echo
pagination(0,$B).($B>5?" …":"");for($p=max(1,$B-4);$p<min($Xe,$B+5);$p++)echo
pagination($p,$B);if($Xe>0)echo($B+5<$Xe?" …":""),($yc&&$dd!==false?pagination($Xe,$B):" <a href='".h(remove_from_uri("page")."&page=last")."' title='~$Xe'>".lang(111)."</a>");}else
echo
pagination(0,$B).($B>1?" …":""),($B?pagination($B,$B):""),($zd?pagination($B+1,$B)." …":"");echo"</fieldset>\n";}echo"<fieldset>","<legend>".lang(112)."</legend>";$Tb=($yc?"":"~ ").$dd;$we=($dd!==false?($yc?"":"~ ").lang(113,$dd):"");echo
checkbox("all",1,0,$we,on('click','countRows',$Tb))."\n","</fieldset>\n";if(adminer()->selectCommandPrint())echo'<fieldset',($_GET["modify"]?'':" title='".lang(114)."'"),'>
<legend><a href=\'',h($_GET["modify"]?remove_from_uri("modify"):relative_uri()."&modify=1"),'\'>',lang(115),'</a></legend><div>
<input type=\'submit\' id=\'save\' value=\'',lang(17),'\'',($_GET["modify"]?'':" class='jsonly' disabled"),'>
</div></fieldset>

<fieldset><legend>',lang(116),' <span id="selected"></span></legend><div>
<input type=\'submit\' name=\'edit\' value=\'',lang(13),'\'>
<input type=\'submit\' name=\'clone\' value=\'',lang(117),'\'>
<input type=\'submit\' name=\'delete\' value=\'',lang(21),'\'',confirm(),'>
</div></fieldset>
';$bd=adminer()->dumpFormat();foreach((array)$_GET["columns"]as$d){if($d["fun"]){unset($bd['sql']);break;}}if($bd){print_fieldset("export",lang(118)." <span id='selected2'></span>");$Uf=adminer()->dumpOutput();echo($Uf?html_select("output",$Uf,$ka["output"])." ":""),html_select("format",$bd,$ka["format"])," <input type='submit' name='export' value='".lang(118)."'>\n","</div></fieldset>\n";}adminer()->selectEmailPrint(array_filter($mc,'strlen'),$e);echo"</div></div>\n";}if(adminer()->selectImportPrint())echo"<p>","<a href='#import' class='toggle'>".lang(119)."</a>","<span id='import'".($_POST["import"]?"":" class='hidden'").">: ",($Wi?input_hidden(ini_get("session.upload_progress.name"),$Wi):""),file_input(" name='csv_file'"," ".html_select("separator",array("csv"=>"CSV,","csv;"=>"CSV;","tsv"=>"TSV"),$ka["format"])." <input type='submit' name='import' value='".lang(119)."'>".($Wi?" <progress class='jsonly hidden' max='1' value='0'></progress>":"")),"</span>";echo
input_token(),"</form>\n",(!$o&&$J?"":script("tableCheck();"));}}}if(is_ajax()){ob_end_clean();exit;}}elseif(isset($_GET["script"])){if($_GET["script"]=="kill"){if(!$j)connection()->query("KILL ".number($_POST["kill"]));}elseif(list($Q,$q,$z)=adminer()->_foreignColumn(column_foreign_keys($_GET["source"]),$_GET["field"])){$w=11;$E=connection()->query("SELECT $q, $z FROM ".table($Q)." WHERE ".(preg_match('~^[0-9]+$~',$_GET["value"])?"$q = $_GET[value] OR ":"")."$z LIKE ".q("$_GET[value]%")." ORDER BY 2 LIMIT $w");for($p=1;($G=$E->fetch_row())&&$p<$w;$p++)echo"<a href='".h(ME."edit=".url_escape($Q)."&where[".url_escape(bracket_escape(idf_unescape($q)))."]=".url_escape($G[0]))."'>".h($G[1])."</a><br>\n";if($G)echo"...\n";}exit;}else{page_header(lang(72),"",false);if(adminer()->homepage()){echo"<form action='' method='post'>\n","<p>".lang(120).": <input type='search' name='query' value='".h($_POST["query"])."'> <input type='submit' value='".lang(51)."'>\n";if($_POST["query"]!="")search_tables();echo"<div class='scrollable'>\n","<table class='nowrap checkable odds'".on('click','tableClick').on('dblclick','tableClick').">\n",'<thead><tr>','<td class="hover"><input id="check-all" type="checkbox" class="jsonly"'.on('click','formCheck','^tables\[').'>','<th>'.lang(121),'<th>'.lang(122),"<tbody>\n";foreach(table_status()as$Q=>$G){$z=adminer()->tableName($G);if($z!="")echo'<tr><td class="hover">'.checkbox("tables[]",$Q,in_array($Q,(array)$_POST["tables"],true)),"<th><a href='".h(ME).'select='.url_escape($Q)."'>$z</a>","<td align='right'><a href='".h(ME."edit=").url_escape($Q)."'>".format_status($G,"Rows")."</a>";}echo"</table>\n","</div>\n","</form>\n",script("tableCheck();");adminer()->pluginsLinks();}}page_footer();