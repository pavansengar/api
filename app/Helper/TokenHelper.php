<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Token Generation
 *
 * @author Pavan Sengar
 */
namespace App\Helper;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use Redirect,Response,File;
use Constant;


class TokenHelper {
    
    public static function tokenGeneration($page_name,$str_name = ""){
        $output = false;
        $encrypt_method = "AES-256-CBC";
        $my_key = get_string_value();
        $secret_key = 'This is my secret key';
        $secret_iv = 'This is my secret iv';
        $domain = url('/');
        $token_str = "";
        $date = date('Y-m-d H:i:s');
        if($domain == "http://speakin.localhost" OR $domain == "https://speakin.co" OR $domain == "https://www.speakin.co" OR $domain == "https://www.speakin.co/" OR $domain == "speakin.co" OR $domain == "www.speakin.co" OR $domain == "http://stage.speakin.co"){
            $token_str .= $domain;
        }else{
            return array();
        }
        
        if($page_name != ""){
            $token_str .= "||".$page_name;
        }else{
            return array();
        }
        
        if($my_key != ""){
            $token_str .= "||".$my_key;
        }else{
            $my_key = get_string_value();
            $token_str .= "||".$my_key;
        }
        
        if($date != ""){
            $token_str .= "||".$date;
        }else{
            return array();
        }

        $key = hash('sha256', $secret_key);
        
        if($key != ""){
            $token_str .= "||".$key;
        }else{
            return array();
        }
        
        
        $iv = substr(hash('sha256', $secret_iv), 0, 16);
        
        if($str_name==""){
            $str_name = $token_str;
        }
        
        $output = openssl_encrypt($str_name, $encrypt_method, $key, 0, $iv);
        $output = base64_encode($output);
        
        $data = ["status"=>'success',"method"=>'encrypt',"token"=>$output,"token_str"=>$token_str];
        
        return $data;

    }
    public static function tokenValidate($page_name, $token = ""){
        $output = false;
        $encrypt_method = "AES-256-CBC";
        //$secret_key = get_string_value();
        $secret_key = 'This is my secret key';
        $secret_iv = 'This is my secret iv';
        $key = hash('sha256', $secret_key);
        $iv = substr(hash('sha256', $secret_iv), 0, 16);
        if(openssl_decrypt(base64_decode($token), $encrypt_method, $key, 0, $iv)){
            $output = openssl_decrypt(base64_decode($token), $encrypt_method, $key, 0, $iv);
        }
        if($output == ""){
            return false;
        }
        
        $exp = explode("||",$output);
        if(count($exp) < 3){
            return false;
        }
        
        $domain = $exp[0];
        $page_name = $exp[1];
        $my_key = $exp[2];
        $key_val = $exp[4];

        if($domain == "http://speakin.localhost" OR $domain == "https://speakin.co" OR $domain == "https://www.speakin.co" OR $domain == "https://www.speakin.co/" OR $domain == "speakin.co" OR $domain == "www.speakin.co" OR $domain == "http://stage.speakin.co"){
              
        }else{
            return false;
        }
        
        if($page_name != "search"){
            return false;
        }
        if($key != $key_val){
            return false;
        }
        
        $find_my_key = find_my_key($my_key);
        if($find_my_key == false){
            return false;
        }
        return true;
    }
    
    
    public static function tokenRead($page_name, $token = ""){
        $output = false;
        $encrypt_method = "AES-256-CBC";
        //$secret_key = get_string_value();
        $secret_key = 'This is my secret key';
        $secret_iv = 'This is my secret iv';
        $key = hash('sha256', $secret_key);
        $iv = substr(hash('sha256', $secret_iv), 0, 16);
        if(openssl_decrypt(base64_decode($token), $encrypt_method, $key, 0, $iv)){
            $output = openssl_decrypt(base64_decode($token), $encrypt_method, $key, 0, $iv);
        }
        
        $data = ["status"=>'success',"method"=>'decrypt',"token"=>$token,"token_str"=>$output];
        return $data;
    }
}

function get_string_value(){
    $arr = array(
        "Pavan!!",
        "Surya@@",
        "Dev##",
        "Ajit$$",
        "Brajesh%%",
        "Lalit^^",
        "Himanshu&&",
        "Rahul**",
        "Neeraj((",
        "Kohali))",
        "Rahul--"
    );
    $random_keys=array_rand($arr,1);
    return $arr[$random_keys];
}

function find_my_key($key){
    $arr = array(
        "Pavan!!",
        "Surya@@",
        "Dev##",
        "Ajit$$",
        "Brajesh%%",
        "Lalit^^",
        "Himanshu&&",
        "Rahul**",
        "Neeraj((",
        "Kohali))",
        "Rahul--"
    );
    if (in_array($key, $arr)){
        return true;
    }else{
        return false;
    }
}


