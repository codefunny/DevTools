<?php  
header('content-Type: text/html; charset=utf-8');  
if(isset($_GET['dir'])){ //设置文件目录，如果没有设置，则自动设置为当前文件所在目录  
    $basedir=$_GET['dir'];  
}else{  
    $basedir='.';  
}  
$auto=1;/*设置为1标示检测BOM并去除，设置为0标示只进行BOM检测，不去除*/  
  
echo '当前查找的目录为：'.$basedir.'当前的设置是：';  
echo $auto?'检测文件BOM同时去除检测到BOM文件的BOM<br />':'只检测文件BOM不执行去除BOM操作<br />';  
  
checkdir($basedir);  
function checkdir($basedir){  
    if($dh=opendir($basedir)){  
        while (($file=readdir($dh)) !== false){  
            if($file != '.' && $file != '..'){  
                if(!is_dir($basedir.'/'.$file)){  
                    echo '文件: '.$basedir.'/'.$file .checkBOM($basedir.'/'.$file).' <br>';  
                }else{  
                    $dirname=$basedir.'/'.$file;  
                    checkdir($dirname);  
                }  
            }  
        }  
        closedir($dh);  
    }  
}  
function checkBOM($filename){  
    global $auto;  
    $contents=file_get_contents($filename);  
    $charset[1]=substr($contents,0,1);  
    $charset[2]=substr($contents,1,1);  
    $charset[3]=substr($contents,2,1);  
    if(ord($charset[1])==239 && ord($charset[2])==187 && ord($charset[3])==191){  
        if($auto==1){  
            $rest=substr($contents,3);  
            rewrite($filename,$rest);  
            return (' <font color=red>找到BOM并已自动去除</font>');  
        }else{  
            return (' <font color=red>找到BOM</font>');  
        }  
    }else{  
        return (' 没有找到BOM');  
    }  
}  
function rewrite($filename,$data){  
    $filenum=fopen($filename,'w');  
    flock($filenum,LOCK_EX);  
    fwrite($filenum,$data);  
    fclose($filenum);  
}  
?>  
