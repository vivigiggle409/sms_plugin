<?php

class Autoloader{
  
  /**
     * ����Զ����أ�д��·����ȷ�������������ļ���
     * @param string $class ��������
     * @return void
     */
    public static function autoload($class) {
        $name = $class;
        if(false !== strpos($name,'\\')){
          $name = strstr($class, '\\', true);
        }

        $filename = DISCUZ_ROOT.'source/plugin/smstong/aliyun'."/Util/".$name.".php";
        if(is_file($filename)) {
            include $filename;
            return;
        }

        $filename = DISCUZ_ROOT.'source/plugin/smstong/aliyun'."/Http/".$name.".php";
        if(is_file($filename)) {
            include $filename;
            return;
        }

        $filename = DISCUZ_ROOT.'source/plugin/smstong/aliyun'."/Constant/".$name.".php";
        if(is_file($filename)) {
            include $filename;
            return;
        }       
    }
}

if (version_compare(phpversion(),'5.3.0','>=')) {
    spl_autoload_register('Autoloader::autoload',false,true);
}else{
    Autoloader::autoload("DictionaryUtil");
    Autoloader::autoload("HttpUtil");
    Autoloader::autoload("MessageDigestUtil");
    Autoloader::autoload("SignUtil");
    Autoloader::autoload("HttpClient");
    Autoloader::autoload("HttpRequest");
    Autoloader::autoload("HttpResponse");
	Autoloader::autoload("Constants");
    Autoloader::autoload("ContentType");
    Autoloader::autoload("HttpHeader");
    Autoloader::autoload("HttpMethod");
	Autoloader::autoload("HttpSchema");
    Autoloader::autoload("SystemHeader");
}

?>