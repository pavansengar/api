<?php

namespace App\Http\Controllers;

use Validator;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use SHelpers;
use THelpers;
use Constant;
use Jonasva\GoogleTrends\GoogleSession;
use Cookie;
use Illuminate\Support\Facades\Auth;
use DB;
use DHelpers;
use Session;
use DateTime;
use Image;
use Youtube;
use Illuminate\Support\Facades\Crypt;
use Spatie\GoogleCalendar\Event;


class DemoController extends Controller
{
    /**
     * To display the show page
     *
     * @return \Illuminate\Http\Response
     */
    public function showJqueryImageUpload()
    {
        $wCond = array("topic_name"=>"Entrepreneurship");
        $topic1_id = DHelpers::get_record_value("si_topic_master","top_id",$wCond);
        
        return view('demo');
    }
    
    /**
     * ****************************************************************
     * Function : htmltopdf
     * Purpose : Get list of all city according to country
     * Created : 08-Jan-2020
     * Author : Pavan Sengar
     * ****************************************************************
     */
    public function htmltopdf(Request $request)
    {
        
        
        
        echo $encrypted = Crypt::encryptString('Hello world.');
        
        echo $decrypted = Crypt::decryptString($encrypted);
        die();
        $upload_dir = "expert_profile";
        $isConvert = SHelpers::html_to_pdf("<p>Hellp Pavan, This is test page so please ignore it.</p>","expert_profiles",$upload_dir,false);
        return SHelpers::getDownloadPdf($isConvert['getPath'],$isConvert['getFilename']);
    }
    
    public function api_call(Request $request)
    {
        // Make Post Fields Array
        $data1 = [
            'k' => 'p',
            'i' => 'name',
            'fields' =>''
        ];
        $token = THelpers::tokenGeneration('search');
        //echo $token['token'];
        
        $curl = curl_init();
        
        curl_setopt_array($curl, array(
            CURLOPT_URL => url("api/v1/search"),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30000,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($data1),
            CURLOPT_HTTPHEADER => array(
                // Set here requred headers
                "accept: */*",
                "accept-language: en-US,en;q=0.8",
                "content-type: application/json",
                "x-api-key: ".$token['token'],
            ),
        ));
        
        $response = curl_exec($curl);
        $err = curl_error($curl);
        
        curl_close($curl);
        
        echo"<pre>";
        print_r(json_decode($err));
        echo"</pre>";
        
        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            echo"<pre>";
            print_r(json_decode($response));
            echo"</pre>";
        }
    }
    
    public function generateToken(Request $request)
    {
        $token = THelpers::tokenGeneration('search');
        
        echo"<pre>";
        print_r($token);
        echo"</pre>";
        
        $token_read = THelpers::tokenValidate('search',$token['token']);
        
        echo"<pre>";
        print_r($token_read);
        echo"</pre>";
        
        $exp = explode("||",$token['token_str']);
        echo"<pre>";
        print_r($exp);
        echo"</pre>";
        
        
    }
    
    
    /**
     * To handle the comming post request
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function saveJqueryImageUpload(Request $request)
    {
        
        
        $data = SHelpers::upload_crop_image($request, "profile_picture", "jpg,jpeg,png,gif", $file_size = 2, 'upload', true);
        
        /*$data = $request->profile_picture;
        list($type, $data) = explode(';', $data);
        list(, $data)      = explode(',', $data);
        $data = base64_decode($data);
        $image_name= time().'.png';
        $path = storage_path() . "/upload/" . $image_name;
        file_put_contents($path, $data);*/
        return $data;
        print_r($data); die();
        
        
        
        /*list($type, $data) = explode(';', $data);
        list(, $data)      = explode(',', $data);
        $data = base64_decode($data);
        $image_name= time().'.png';
        $path = public_path() . "/upload/" . $image_name;
        file_put_contents($path, $data);*/
        //return response()->json(['success'=>'done']);
        
       
        
        /*$validator = Validator::make($request->all(), [
            'profile_picture' => 'required|image|max:1000',
        ]);
        
        if ($validator->fails()) {
            
            return $validator->errors();
        }
        
        $status = "";
        
        if ($request->hasFile('profile_picture')) {
            $image = $request->file('profile_picture');
            // Rename image
            $filename = time().'.'.$image->guessExtension();
            
            $path = $request->file('profile_picture')->storeAs(
                'profile_pictures', $filename
                );
            
            $status = "uploaded";
        }*/
        
        //$path_parts = pathinfo($_FILES['profile_picture']["name"]);
        //print_r($path_parts);
        //echo $_FILES['profile_picture']["name"];
        //die();
        
        //$extension = $path_parts['extension'];
        //$filename = strtolower($path_parts['filename']);
        
        print_r($_FILES); die();
        
        $status = SHelpers::upload_file($request,'profile_picture','jpg,jpeg,png,gif',2,'expert',false,true,true);
        print_r($status);
        //return response($status,200);
    }
    
    public function cookie(Request $request)
    {
        $status = SHelpers::setCookie("PavanSengar","CookieValue",1000);
        echo $status1 = SHelpers::getCookie($request,"PavanSengar");
        //return $status;
    }
    
    public function getcookie(Request $request)
    {
        echo $status = SHelpers::getCookie($request,"_e");
        //return $status;
    }
    
    
    public function check_login(Request $request)
    {
        SHelpers::authenticate($request);
        //print_r($a);
        //return $status;
    }
    
    public function validate_popup_login_demo(Request $request)
    {
        $email = $request->email;
        $password = $request->password;
        
        if (Auth::attempt(['email' => $email, 'password' => $password])) {
            // Authentication passed...
            SHelpers::setCookie("_e",$email,10000);
            SHelpers::setCookie("_p",$password,10000);
            $getBrowserDetails = SHelpers::getBrowser();
            $ip = $_SERVER['REMOTE_ADDR'];
            $dRow = DHelpers::getUserDetailsByEmail($email);
            DB::table('si_login_history')->insert(["user_id"=>$dRow->user_id,"ip_address"=>$ip,"browser_details"=>$getBrowserDetails,"curr_date"=>NOW()]);
            $request->session()->put('user_id', $dRow->user_id);
            $request->session()->put('email', $email);
            $request->session()->put('user_type', $dRow->user_type);
            $request->session()->put('user_name', $dRow->first_name." ".$dRow->last_name);
            $request->session()->save();
            //redirect()->intended('home');
            return "success";
        }else{
            return "Invalid login details.";
        }
    }
    
    public function si_users_migration(Request $request)
    {
        // Get Data from si_users table Live DB
        $si_users = DB::connection('mysql2')->select("select * from si_users where email !='' limit 10000");
         foreach($si_users as $val){
            //$si_users_n = DB::connection('mysql')->select("select * from si_users where email=".$val->email);
            $si_users_n = DHelpers::isRegisterUserExists($val->email);
             if($si_users_n==true){
                echo "Email Already Exists :: ".$val->email."<br>";
             }else{
                 $city_id = DHelpers::getrecordnamebyid($val->city,"id","si_cities","name");
                 $state_id = DHelpers::getrecordnamebyid($val->city,"state_id","si_cities","name");
                 $country_id = DHelpers::getrecordnamebyid($val->city,"country_id","si_cities","name");
                 if($city_id==""){
                    $city_id = 0;
                 }
                 if($state_id==""){
                    $state_id = 0;
                 }
                 if($country_id==""){
                    $country_id = 0;
                 }
                 $first_name = strtolower($val->first_name);
                 $last_name = strtolower($val->last_name);
                 
                 $data = [
                     "user_id"=>$val->user_id,
                     "first_name"=>ucwords($first_name),
                     "last_name"=>ucwords($last_name),
                     "email"=>strtolower($val->email),
                     "user_type"=>$val->user_type,
                     "gender"=>$val->gender,
                     "country_name"=>$val->country_name,
                     "password"=>$val->pwd,
                     "oauth_provider"=>$val->oauth_provider,
                     "oauth_uid"=>$val->oauth_uid,
                     "picture"=>$val->picture,
                     "linkedin_profile"=>$val->link,
                     "google_profile"=>'',
                     "phone"=>$val->phone,
                     "company_name"=>$val->company_name,
                     "city"=>$val->city,
                     "country_id"=>$country_id,
                     "state_id"=>$state_id,
                     "city_id"=>$city_id,
                     "leaddescription"=>$val->leaddescription,
                     "ip_address"=>$val->ip_address,
                     "refreance_url"=>$val->refreance_url,
                     "budget"=>$val->budget,
                     "event_date"=>$val->event_date,
                     "speaker_interested"=>$val->speaker_interested,
                     "status"=>$val->status,
                     "hash_value"=>$val->hash_value,
                     "is_accept_legal"=>$val->is_accept_legal,
                     "created_at"=>$val->created,
                     "updated_at"=>$val->modified,
                     "pro_comp_percentage"=>$val->pro_comp_percentage,
                     "direct_access_hash_value"=>$val->direct_access_hash_value,
                     "is_send_mail"=>$val->is_send_mail,
                     "forgot_pass_hash_value"=>$val->forgot_pass_hash_value,
                     "remember_token"=>'',
                     "assign_admin_id"=>$val->assign_admin_id,
                     "lead_status"=>$val->lead_status,
                     "priority"=>$val->priority,
                     "send_reminder_status"=>$val->send_reminder_status
                 ];
                 DB::connection('mysql')->table('si_users')->insert($data);
                 echo "Record Successfully Inserted :: ".$val->email."<br>";
             }
         }
    }
    public function si_expert_migration(Request $request)
    {
        // Get Data from cms_contact table Live DB
        //$cms_contact = DB::connection('mysql2')->select("select * from cms_contact limit 10");
        $cms_contact = DB::connection('mysql2')->select("select * from cms_contact where speaker_email !=''");
        foreach($cms_contact as $val){
            $cms_contact_n = DHelpers::isExpertProfileExists($val->speaker_email);
            if($cms_contact_n==true){
                //echo "Email Already Exists :: ".$val->speaker_email."<br>";
            }else{
                $city_id = DHelpers::getrecordnamebyid($val->city,"id","si_cities","name");
                $state_id = DHelpers::getrecordnamebyid($val->city,"state_id","si_cities","name");
                $country_id = DHelpers::getrecordnamebyid($val->city,"country_id","si_cities","name");
                
                
                $wCond1 = array("topic_name"=>$val->topic1);
                $topic1_id = DHelpers::get_record_value("si_topic_master","top_id",$wCond1);
                
                $wCond2 = array("topic_name"=>$val->topic2);
                $topic2_id = DHelpers::get_record_value("si_topic_master","top_id",$wCond2);
                
                $wCond3 = array("topic_name"=>$val->topic3);
                $topic3_id = DHelpers::get_record_value("si_topic_master","top_id",$wCond3);
                
                $wCond4 = array("topic_name"=>$val->topic4);
                $topic4_id = DHelpers::get_record_value("si_topic_master","top_id",$wCond4);
                
                $wCond5 = array("topic_name"=>$val->topic5);
                $topic5_id = DHelpers::get_record_value("si_topic_master","top_id",$wCond5);
                
                if($topic1_id==""){
                    $topic1_id = 0;
                }
                if($topic2_id==""){
                    $topic2_id = 0;
                }
                if($topic5_id==""){
                    $topic5_id = 0;
                }
                if($topic3_id==""){
                    $topic3_id = 0;
                }
                if($topic4_id==""){
                    $topic4_id = 0;
                }
                
                /*$wCondOrg = array("topic_name"=>$val->contact_company);
                $org_id = DHelpers::get_record_value("si_topic_master","top_id",$wCondOrg);*/
                $org_id = 0;

                if($city_id==""){
                    $city_id = 0;
                }
                if($state_id==""){
                    $state_id = 0;
                }
                if($country_id==""){
                    $country_id = 0;
                }
                
                $created_at = date('Y-m-d H:i:s');
                $updated_at = date('Y-m-d H:i:s');
                
                $created_at = '2019-12-31 00:00:00';
                $updated_at = '2019-12-31 00:00:00';
                $active_date = null;
                
                if (DateTime::createFromFormat('Y-m-d H:i:s', $val->created_at) !== FALSE) {
                    $created_at = $val->created_at;
                }
                if (DateTime::createFromFormat('Y-m-d H:i:s', $val->updated_at) !== FALSE) {
                    $updated_at = $val->updated_at;
                }
                if (DateTime::createFromFormat('Y-m-d H:i:s', $val->active_date) !== FALSE) {
                    $active_date = $val->active_date;
                }
                
                $wCondInd = array("ind_name"=>$val->industry);
                $ind_id = DHelpers::get_record_value("si_industry_master","ind_id",$wCondInd);
                
                $wCondFun = array("fun_name"=>$val->contact_function);
                $fun_id = DHelpers::get_record_value("si_function_master","fun_id",$wCondFun);
                
                $wCondDel = array("delivery_type"=>$val->delivery_type);
                $del_id = DHelpers::get_record_value("si_delivery_type_master","del_id",$wCondDel);
                
                $wCondCur = array("currency_code"=>$val->s_currency);
                $s_currency_id = DHelpers::get_record_value("si_currency_master","currency_id",$wCondCur);
                
                if($ind_id==""){
                    $ind_id = 0;
                }
                if($fun_id==""){
                    $fun_id = 0;
                }
                if($s_currency_id==""){
                    $s_currency_id = 0;
                }
                if($del_id==""){
                    $del_id = 0;
                }
                
                // Get Price ID
                $price_id = 0;
                $cost = 0;
                $margin_in_perc = 0;
                $margin_cost = 0;
                $actual_expert_cost = 0;
                if ((int)($val->cost)) {
                    //echo $val->cost;
                    $cost = (float)$val->cost;
                    $margin_in_perc= Constant::SPEAKIN_MARGIN_IN_PERCENTAGE_OF_EXPERT_COST;
                    $margin_cost = (((int)$cost *  (float)$margin_in_perc ) / 100);
                    $actual_expert_cost = (float)($cost + $margin_cost);

                    $p_range = DB::table("si_expert_price_master")
                    ->select('price_id')
                    ->where('price_from', '<=', $actual_expert_cost)
                    ->where('price_to', '>=', $actual_expert_cost)
                    ->first();
                    $price_id = @$p_range->price_id;
                }
                
                if($val->probono=="Yes"){
                    $probono = 1;
                }else{
                    $probono = 0;
                }
                
                
                
                $full_name = strtolower($val->speaker_name);
                $first_name = "";
                $last_name = "";
                $name = explode(" ", $full_name);

                if(count($name)==3){
                    if($name[0]!="" and $name[1]!="" and $name[2]!=""){
                        $first_name = $name[0]." ".$name[1];
                        $last_name = $name[2];
                    }elseif($name[0]!="" and $name[1]!="" and $name[2]==""){
                        $first_name = $name[0];
                        $last_name = $name[1];
                    }
                }else if(count($name)==2){
                    $first_name = $name[0];
                    $last_name = $name[1];
                }
                
                

                $data = [
                    "expert_id"=>$val->contact_id,
                    "user_id"=>$val->useracc_id,
                    "first_name"=>ucwords($first_name),
                    "last_name"=>ucwords($last_name),
                    "full_name"=>$val->speaker_name,
                    "email"=>strtolower($val->speaker_email),
                    "dob"=>$val->dob,
                    "secondary_email"=>$val->secondary_email,
                    "salutation"=>$val->salutation,
                    "expert_type"=>$val->category,
                    "org_id"=>$org_id,
                    "organisation"=>$val->contact_company,
                    
                    "designation"=>$val->contact_jobtitle,
                    "ind_id"=>$ind_id,
                    "industry"=>$val->industry,
                    "fun_id"=>$fun_id,
                    "function"=>$val->contact_function,
                    
                    "sex"=>$val->sex,
                    "address"=>$val->contact_address,
                    "contact_phone"=>$val->contact_phone,
                    "contact_otherno"=>$val->contact_otherno,
                    "topic1_id"=>$topic1_id,
                    "topic1"=>$val->topic1,
                    "topic2_id"=>$topic2_id,
                    "topic2"=>$val->topic2,
                    "topic3_id"=>$topic3_id,
                    "topic3"=>$val->topic3,
                    "topic4_id"=>$topic4_id,
                    "topic4"=>$val->topic4,
                    "topic5_id"=>$topic5_id,
                    "topic5"=>$val->topic5,
                    "description"=>$val->description,
                    "profile_details"=>$val->profile,
                    "contact_image"=>$val->contact_image,
                    "video_link"=>$val->video_link,
                    "del_id"=>$del_id,
                    "delivery_type"=>$val->delivery_type,
                    
                    "expert_cost"=>$cost,
                    "margin_in_perc"=>$margin_in_perc,
                    "margin_cost"=>$margin_cost,
                    "actual_expert_cost"=>$actual_expert_cost,
                    "price_id"=>$price_id,
                    
                    
                    "is_probono"=>$probono,
                    "website"=>$val->website,
                    "rating"=>$val->rating,
                    "status"=>$val->status,
                    "remarks"=>$val->remarks,
                    "management"=>$val->management,
                    "is_visible"=>$val->contact_shared,
                    "contact_status"=>$val->contact_status,
                    
                    "s_currency"=>$val->s_currency,
                    "s_currency_id"=>$s_currency_id,
                    
                    "country_id"=>$country_id,
                    "country"=>$val->country,
                    "state_id"=>$state_id,
                    "state"=>$val->state,
                    "city_id"=>$city_id,
                    "city"=>$val->city,
                    
                    "twitter"=>$val->twitter,
                    "linkedin"=>$val->linkedin,
                    "facebook"=>$val->facebook,
                    "instagram"=>$val->instagram,
                    
                    "endorsement"=>$val->endorsement,
                    "expert_language"=>$val->language,
                    
                    "created_at"=>$created_at,
                    "updated_at"=>$updated_at,
                    "active_date"=>$active_date,
                    
                    "main_page"=>$val->main_page,
                    "seo_url"=>$val->seo_url,
                    "meta_title"=>$val->meta_title,
                    "meta_description"=>$val->meta_description,
                    "meta_keywords"=>$val->meta_keywords,
                    "updated_by"=>$val->updated_by,
                    "version"=>5    // 5 For Demo Test
                ];
                DB::connection('mysql')->table('si_experts')->insert($data);
                $referral_url=url('/profile/'.$val->seo_url.'.html');
                echo "Record Successfully Inserted In SI_Expert:: <a target='_blank' href=".$referral_url.">".$val->speaker_email."</a><br>";
            }
        }
        
        
        /*echo"<pre>";
        print_r($data);*/
    }
    
    public function si_expert_pending_migration(Request $request)
    {
        // Get Data from cms_contact table Live DB
        //$cms_contact = DB::connection('mysql2')->select("select * from cms_contact limit 10");
        $cms_contact = DB::connection('mysql2')->select("select * from pending_cms_contact where speaker_email !=''");
        foreach($cms_contact as $val){
            $cms_contact_n = DHelpers::isExpertPendingProfileExists($val->speaker_email);
            if($cms_contact_n==true){
                //echo "Email Already Exists :: ".$val->speaker_email."<br>";
            }else{
                $city_id = DHelpers::getrecordnamebyid($val->city,"id","si_cities","name");
                $state_id = DHelpers::getrecordnamebyid($val->city,"state_id","si_cities","name");
                $country_id = DHelpers::getrecordnamebyid($val->city,"country_id","si_cities","name");
                
                
                $wCond1 = array("topic_name"=>$val->topic1);
                $topic1_id = DHelpers::get_record_value("si_topic_master","top_id",$wCond1);
                
                $wCond2 = array("topic_name"=>$val->topic2);
                $topic2_id = DHelpers::get_record_value("si_topic_master","top_id",$wCond2);
                
                $wCond3 = array("topic_name"=>$val->topic3);
                $topic3_id = DHelpers::get_record_value("si_topic_master","top_id",$wCond3);
                
                $wCond4 = array("topic_name"=>$val->topic4);
                $topic4_id = DHelpers::get_record_value("si_topic_master","top_id",$wCond4);
                
                $wCond5 = array("topic_name"=>$val->topic5);
                $topic5_id = DHelpers::get_record_value("si_topic_master","top_id",$wCond5);
                
                if($topic1_id==""){
                    $topic1_id = 0;
                }
                if($topic2_id==""){
                    $topic2_id = 0;
                }
                if($topic5_id==""){
                    $topic5_id = 0;
                }
                if($topic3_id==""){
                    $topic3_id = 0;
                }
                if($topic4_id==""){
                    $topic4_id = 0;
                }
                
                /*$wCondOrg = array("topic_name"=>$val->contact_company);
                 $org_id = DHelpers::get_record_value("si_topic_master","top_id",$wCondOrg);*/
                $org_id = 0;
                
                if($city_id==""){
                    $city_id = 0;
                }
                if($state_id==""){
                    $state_id = 0;
                }
                if($country_id==""){
                    $country_id = 0;
                }
                
                $created_at = date('Y-m-d H:i:s');
                $updated_at = date('Y-m-d H:i:s');
                
                $created_at = '2019-12-31 00:00:00';
                $updated_at = '2019-12-31 00:00:00';
                
                $active_date = null;
                
                if (DateTime::createFromFormat('Y-m-d H:i:s', $val->created_at) !== FALSE) {
                    $created_at = $val->created_at;
                }
                if (DateTime::createFromFormat('Y-m-d H:i:s', $val->updated_at) !== FALSE) {
                    $updated_at = $val->updated_at;
                }
                /*if (DateTime::createFromFormat('Y-m-d H:i:s', $val->active_date) !== FALSE) {
                    $active_date = $val->active_date;
                }*/
                
                $wCondInd = array("ind_name"=>$val->industry);
                $ind_id = DHelpers::get_record_value("si_industry_master","ind_id",$wCondInd);
                
                $wCondFun = array("fun_name"=>$val->contact_function);
                $fun_id = DHelpers::get_record_value("si_function_master","fun_id",$wCondFun);
                
                $wCondDel = array("delivery_type"=>$val->delivery_type);
                $del_id = DHelpers::get_record_value("si_delivery_type_master","del_id",$wCondDel);
                
                $wCondCur = array("currency_code"=>$val->s_currency);
                $s_currency_id = DHelpers::get_record_value("si_currency_master","currency_id",$wCondCur);
                
                if($ind_id==""){
                    $ind_id = 0;
                }
                if($fun_id==""){
                    $fun_id = 0;
                }
                if($s_currency_id==""){
                    $s_currency_id = 0;
                }
                if($del_id==""){
                    $del_id = 0;
                }
                
                // Get Price ID
                $price_id = 0;
                $cost = 0;
                $margin_in_perc = 0;
                $margin_cost = 0;
                $actual_expert_cost = 0;
                if ((int)($val->cost)) {
                    //echo $val->cost;
                    $cost = (float)$val->cost;
                    $margin_in_perc= Constant::SPEAKIN_MARGIN_IN_PERCENTAGE_OF_EXPERT_COST;
                    $margin_cost = (((int)$cost *  (float)$margin_in_perc ) / 100);
                    $actual_expert_cost = (float)($cost + $margin_cost);
                    
                    $p_range = DB::table("si_expert_price_master")
                    ->select('price_id')
                    ->where('price_from', '<=', $actual_expert_cost)
                    ->where('price_to', '>=', $actual_expert_cost)
                    ->first();
                    $price_id = @$p_range->price_id;
                }
                
                if($val->probono=="Yes"){
                    $probono = 1;
                }else{
                    $probono = 0;
                }
                
                
                
                $full_name = strtolower($val->speaker_name);
                $first_name = "";
                $last_name = "";
                $name = explode(" ", $full_name);
                
                if(count($name)==3){
                    if($name[0]!="" and $name[1]!="" and $name[2]!=""){
                        $first_name = $name[0]." ".$name[1];
                        $last_name = $name[2];
                    }elseif($name[0]!="" and $name[1]!="" and $name[2]==""){
                        $first_name = $name[0];
                        $last_name = $name[1];
                    }
                }else if(count($name)==2){
                    $first_name = $name[0];
                    $last_name = $name[1];
                }
                
                
                
                $data = [
                    "expert_id"=>$val->contact_id,
                    "user_id"=>$val->useracc_id,
                    "first_name"=>ucwords($first_name),
                    "last_name"=>ucwords($last_name),
                    "full_name"=>$val->speaker_name,
                    "email"=>strtolower($val->speaker_email),
                    "dob"=>$val->dob,
                    "secondary_email"=>$val->secondary_email,
                    "salutation"=>$val->salutation,
                    "expert_type"=>$val->category,
                    "org_id"=>$org_id,
                    "organisation"=>$val->contact_company,
                    "designation"=>$val->contact_jobtitle,
                    "ind_id"=>$ind_id,
                    "industry"=>$val->industry,
                    "fun_id"=>$fun_id,
                    "function"=>$val->contact_function,
                    "sex"=>$val->sex,
                    "address"=>$val->contact_address,
                    "contact_phone"=>$val->contact_phone,
                    "contact_otherno"=>$val->contact_otherno,
                    "topic1_id"=>$topic1_id,
                    "topic1"=>$val->topic1,
                    "topic2_id"=>$topic2_id,
                    "topic2"=>$val->topic2,
                    "topic3_id"=>$topic3_id,
                    "topic3"=>$val->topic3,
                    "topic4_id"=>$topic4_id,
                    "topic4"=>$val->topic4,
                    "topic5_id"=>$topic5_id,
                    "topic5"=>$val->topic5,
                    "description"=>$val->description,
                    "profile_details"=>$val->profile,
                    "contact_image"=>$val->contact_image,
                    "video_link"=>$val->video_link,
                    "del_id"=>$del_id,
                    "delivery_type"=>$val->delivery_type,
                    "expert_cost"=>$cost,
                    "margin_in_perc"=>$margin_in_perc,
                    "margin_cost"=>$margin_cost,
                    "actual_expert_cost"=>$actual_expert_cost,
                    "price_id"=>$price_id,
                    "is_probono"=>$probono,
                    "website"=>$val->website,
                    "rating"=>$val->rating,
                    "status"=>$val->status,
                    "remarks"=>$val->remarks,
                    "management"=>$val->management,
                    "is_visible"=>$val->contact_shared,
                    "contact_status"=>$val->contact_status,
                    "s_currency"=>$val->s_currency,
                    "s_currency_id"=>$s_currency_id,
                    "country_id"=>$country_id,
                    "country"=>$val->country,
                    "state_id"=>$state_id,
                    "state"=>$val->state,
                    "city_id"=>$city_id,
                    "city"=>$val->city,
                    "twitter"=>$val->twitter,
                    "linkedin"=>$val->linkedin,
                    "facebook"=>$val->facebook,
                    "instagram"=>$val->instagram,
                    "endorsement"=>$val->endorsement,
                    "expert_language"=>$val->language,
                    "created_at"=>$created_at,
                    "updated_at"=>$updated_at,
                    "active_date"=>$active_date,
                    "main_page"=>$val->main_page,
                    "seo_url"=>$val->seo_url,
                    //"meta_title"=>$val->meta_title,
                    //"meta_description"=>$val->meta_description,
                    //"meta_keywords"=>$val->meta_keywords,
                    //"updated_by"=>$val->updated_by,
                    "version"=>5    // 5 For Demo Test
                ];
                DB::connection('mysql')->table('si_experts_pending')->insert($data);
                $referral_url=url('/profile/'.$val->seo_url.'.html');
                echo "Record Successfully Inserted In SI_Expert:: <a target='_blank' href=".$referral_url.">".$val->speaker_email."</a><br>";
            }
        }
        
        
        /*echo"<pre>";
         print_r($data);*/
    }
    
    public function chosen(Request $request)
    {
        $data = array();
        return view('account.speaker_invoice_request',['dir_arr'=> $data]);
    }
    public function payment(Request $request)
    {
        $data = array();
        return view('demo',['dir_arr'=> $data]);
    }
    
    public function image_cache(Request $request)
    {
        $img = Image::cache(function($image) {
            return $image->make('storage/foo.png')->resize(100, 100)->greyscale();
        });
        
            // create a cached image and set a lifetime and return as object instead of string
            $img = Image::cache(function($image) {
                $image->make('storage/foo.png')->resize(300, 200)->greyscale();
            }, 10, true);
            
    }
    
    //https://github.com/alaouy/Youtube
    public function youtube_chanel(Request $request)
    {
        $videoList = Youtube::listChannelVideos('UCe-LWnnna02Q4IGlZm5jQEA', 50);
        
        echo"<pre>";
        print_r($videoList);
        
        // Get youTube Video Url
        /*$youTube_url = "https://www.youtube.com/embed/wSWPMeieMM4";
        if(!empty($profile->video_link)){
            $youTube_url = $profile->video_link;
            if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $youTube_url, $match)) {
                $video_id = $match[1];
                $youTube_url = "https://www.youtube.com/embed/".$video_id;
            }else{
                $youTube_url = "https://www.youtube.com/embed/wSWPMeieMM4";
            }
        }*/
        
        /*$limit = rand(3, 10);
        $response = $this->youtube->listChannelVideos('UCVHFbqXqoYvEWM1Ddxl0QDg', $limit);
        $this->assertEquals($limit, count($response));
        $this->assertEquals('youtube#searchResult', $response[0]->kind);
        $this->assertEquals('youtube#video', $response[0]->id->kind);*/
                
    }
    
    
    
    
    public function pymtstatus(Request $request)
    {
        $msg=$_POST["msg"];
        echo $msg;
        
        $splitdata = explode('|', $msg);
        
        $common_string="CGFB23KK";  // checksum key provided by BillDesk
        
        if($msg!="")
        
        {
            
            $code = substr(strrchr($msg, "|"), 1); //Last check sum value
            
            $string_new=str_replace("|".$code,"",$msg);//string replace : with empy space
            
            $checksum = strtoupper(hash_hmac('sha256',$string_new,$common_string, false));// calculated  check sum
            
            if($checksum==$code && $splitdata[14]=="0300") // success trans condition
            
            {
                
                // Here success txn data base save code
                
                echo "success";
                
            }else{
                
                echo "Txn Failed";
                
            }
            
        }
    }
    
    public function payment_responce_message(Request $request)
    {
        $list['page_name'] = "payment_msaage";
        return view('payment_message',["result"=>$list]);
    }
    
    public function google_calendar(Request $request)
    {
        // get all future events on a calendar
        $events = Event::get();
        echo"<pre>";
        print_r($events);
    }
    
    public function google_calendar_php(Request $request)
    {
        require_once 'vendor/autoload.php';
        $client = new Google_Client();
        $client->setApplicationName("SpeakIn");
        $client->setDeveloperKey("947437301524-09t35kljorp5t1go71ub5jdb3osppd6c.apps.googleusercontent.com");
        
        $service = new Google_Service_Books($client);
        $calendarListEntry = $service->calendarList->get('speakin.co.in_m5chrr27ce6a2tbb7b0tcic3mg@group.calendar.google.com');
        
        echo $calendarListEntry->getSummary();
        
        /*$optParams = array('filter' => 'free-ebooks');
        $results = $service->volumes->listVolumes('Henry David Thoreau', $optParams);
        
        foreach ($results->getItems() as $item) {
            echo $item['volumeInfo']['title'], "<br /> \n";
        }*/
        
       /* echo"<pre>";
        print_r($events);*/
    }
    public function webinar_report(Request $request)
    {
        
        $wph = DB::table('si_coupon_master as cm')
        ->join('si_coupon_redeem', 'cm.coupon_id', '=', 'si_coupon_redeem.coupon_id')
        ->join('si_webinar_users', 'cm.web_user_id', '=', 'si_webinar_users.web_user_id')
        //->join('si_webinar', 'cm.web_user_id', '=', 'si_webinar.web_user_id')
        ->select('cm.coupon_code','cm.amount', 'si_webinar_users.*')
        ->where('si_coupon_redeem.payment_status', '=','Successful' )
        //->whereRaw("find_in_set('".$val->webinar_id."',wph.webinar_id_str)")
        ->get();
        foreach($webinar as $val){
            
        }
        
        
        $wph = DB::table('si_coupon_redeem as cr')
        ->join('si_coupon_redeem', 'cm.coupon_id', '=', 'si_coupon_redeem.coupon_id')
        ->join('si_webinar_users', 'cm.web_user_id', '=', 'si_webinar_users.web_user_id')
        //->join('si_webinar', 'cm.web_user_id', '=', 'si_webinar.web_user_id')
        ->select('cm.coupon_code','cm.amount', 'si_webinar_users.*')
        ->where('si_coupon_redeem.payment_status', '=','Successful' )
        //->whereRaw("find_in_set('".$val->webinar_id."',wph.webinar_id_str)")
        ->get();
        foreach($webinar as $val){
            
        }
        
        
        
        $webinar = DB::table('si_webinar')
        ->select('*')
        ->orderByRaw(DB::raw('`booking_datetime` ASC'))
        ->get();
        foreach($webinar as $val){
            $regUser = 0;
            $paid = 0;
            $paidUser = 0;
            $unPaidUser = 0;
            $wph = DB::table('si_webinar_payment_history as wph')
            ->join('si_webinar_users', 'wph.web_user_id', '=', 'si_webinar_users.web_user_id')
            ->select('wph.web_pay_id','wph.price','wph.webinar_id_str','wph.price','wph.currency_type','wph.order_id','wph.is_send_thankyou_mail', 'si_webinar_users.*')
            ->where('wph.payment_status', '=','Successful' )
            ->whereRaw("find_in_set('".$val->webinar_id."',wph.webinar_id_str)")
            ->get();
            if (count($wph) > 0) {
                foreach($wph as $row){
                    if($row->price > 0){
                        $paid = $paid + $row->price;
                        $paidUser++;
                    }else if($row->price == 0){
                        $unPaidUser++;
                    }
                    $regUser++;
                }
            }
            echo "Webinar Topic: ".$val->topic."<br>";
            echo "Total Register User: ".$regUser."<br>";
            echo "Paid User: ".$paidUser."<br>";
            echo "UnPaid User: ".$unPaidUser."<br>";
            echo "Amount: ".$paid."<br><hr>";
        }
    }
    
}