<?php

namespace App\Http\Controllers\MP;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class Inspector extends Controller
{
    public function docks(request $request){
        $path = substr($request->path(),3);
        if($this->validateIp()){
            switch ($path) {
                case "editor":
                    return view("MP.mp_editor");
                    break;
                default:
                    return redirect("/");
            }
        }
    }
    public function postport(request $request){
        $path = substr($request->path(),7);
        if($this->validateIp()){
            switch ($path) {
                case "mp_file_system":
                    return $this->mp_file_system();
                    break;
                case "load_contents":
                    return $this->load_contents();
                    break;
                default:
                    return redirect("/");
            }
        }
    }
    private function validateIp(){
        $ips = explode(",",env("MP_ENABLE_IP"));
        if(in_array($_SERVER["REMOTE_ADDR"], $ips)){
            return true;
        }else{
            return false;
        }
    }
    private function mp_file_system(){
        return $this->throwFTP("../");
    }
    private function throwFTP($path_find) {
        $folders = $this->ftpFolders($path_find);
        //$files = $this->ftpFiles($path_find);
        return $folders;//.$files;
    }
    private function ftpFolders($path_find) {
        $contents = glob("$path_find/". '{,.}[!.,!..]*',GLOB_MARK|GLOB_BRACE);
        $fol = "";
        //return count($contents);
        foreach($contents as $file_each){
            if (filetype($file_each) == "dir") {
                $file_name = substr(str_replace($path_find, '', $file_each), 1,-1);
                if($file_name != "vendor"){
                    $opened = "false";
                    $fol .= '<li class="mp-folder" rel="'.$path_find."/".$file_name.'"data-jstree=\'{"opened":'.$opened.',"icon":"https://icons.iconarchive.com/icons/fatcow/farm-fresh/24/folder-icon.png"}\'>'.$file_name;
                    $fol .= '<ul>';
                    $fol .= $this->ftpFolders($path_find."/".$file_name);
                    $fol .= $this->ftpFiles($path_find."/".$file_name,);
                    $fol .= '</ul>';
                    $fol .= '</li>';
                }
            }
        }
        return $fol;
    }
    private function ftpFiles($path_find) {
        $contents = glob("$path_find/". '{,.}[!.,!..]*',GLOB_MARK|GLOB_BRACE);
        $fil = "";
        foreach ($contents as $file_each) {
            if (filetype($file_each) == "file") {
                $file_name = substr(str_replace($path_find, '', $file_each), 1);
                $image = "https://icons.iconarchive.com/icons/chaninja/chaninja/24/File-Help-icon.png";
                if (substr($file_name, -3)== "php"){
                    $image = "https://icons.iconarchive.com/icons/papirus-team/papirus-mimetypes/24/app-x-php-icon.png";
                }
                if (substr($file_name, -4)== "docx"){
                    $image = "https://icons.iconarchive.com/icons/fatcow/farm-fresh/24/file-extension-doc-icon.png";
                }
                if (substr($file_name, -4)== "html"){
                    $image = "https://icons.iconarchive.com/icons/fatcow/farm-fresh/24/file-extension-html-icon.png";
                }
                if (substr($file_name, -4)== "jpeg"){
                    $image = "https://icons.iconarchive.com/icons/fatcow/farm-fresh/24/file-extension-jpeg-icon.png";
                }
                if (substr($file_name, -3)== "jpg"){
                    $image = "https://icons.iconarchive.com/icons/fatcow/farm-fresh/24/file-extension-jpg-icon.png";
                }
                if (substr($file_name, -3)== "png"){
                    $image = "https://icons.iconarchive.com/icons/fatcow/farm-fresh/24/file-extension-png-icon.png";
                }
                if (substr($file_name, -3)== "pdf"){
                    $image = "https://icons.iconarchive.com/icons/fatcow/farm-fresh/24/file-extension-pdf-icon.png";
                }
                if (substr($file_name, -3)== "txt"){
                    $image = "https://icons.iconarchive.com/icons/fatcow/farm-fresh/24/file-extension-txt-icon.png";
                }
                if (substr($file_name, -3)== "css"){
                    $image = "https://icons.iconarchive.com/icons/franksouza183/fs/24/Mimetypes-text-css-icon.png";
                }
                if (substr($file_name, -2)== "js"){
                    $image = "https://icons.iconarchive.com/icons/franksouza183/fs/24/Mimetypes-javascript-icon.png";
                }
                $fil .= '<li class="mp-file " file="'.$file_name.'" data="" rel="'.$path_find."/".$file_name.'" data-jstree=\'{"icon":"'.$image.'"}\'>'.$file_name.'</li>';
            }
        }
        return $fil;
    }
}
