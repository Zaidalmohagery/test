<?php
/**
 * Created by PhpStorm.
 * User: hasanen
 * Date: 10/1/18
 * Time: 4:46 PM
 */
//bootstrap the frontend using PHP.
$db_config = parse_ini_file("/opt/sas4/etc/config.ini");
$db_host = $db_config['db_host'];
$db_name = $db_config['db_name'];
$db_username = $db_config['db_username'];
$db_password = $db_config['db_password'];
$db_link = new mysqli($db_host, $db_username, $db_password, $db_name);
if ($db_link->connect_errno){
    echo "sas4 db error: ".mysqli_connect_error();
    mysqli_close($db_link);
    exit(-1);
}
$query = "select * from sas_settings";
$result = $db_link->query($query);
$config = array();
while ($row = $result->fetch_assoc()){
    $key = $row['key'];
    $value = $row['value'];
    $config[$key] = $value;
}
$result->free();
mysqli_close($db_link);
//force redirect to HTTPs
if ( isset($config['portal_admin_https']) && $config['portal_admin_https'] == '1'){
    if(empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == "off"){
        $redirect = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        header('HTTP/1.1 301 Moved Permanently');
        header('Location: ' . $redirect);
        exit();
    }
}


//load the HTML file
$html = file_get_contents('index_template.html');
//rewrite ht base reference
$base_reference = $config['portal_admin_alias'];
if ($base_reference == '')
    $base_reference = '/';
if (substr($base_reference,-1) != '/')
    $base_reference.="/";
if (substr($base_reference,0,1) !='/')
    $base_reference = '/'.$base_reference;
$html = str_replace('{{base_reference}}', $base_reference, $html);
echo $html;
