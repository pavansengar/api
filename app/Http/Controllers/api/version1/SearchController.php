<?php

namespace App\Http\Controllers\api\version1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Constant;
use THelpers;
use App\Models\api\version1\SearchModel;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class SearchController extends Controller
{
    public function index(Request $request){
        DB::enableQueryLog();
        if(isset($request->limit)){
            $limit = $request->limit;
        }else{
            $limit = 500;
        }
        $offset = $request->offset;
        $sort_by = $request->sort_by;
        $selected_field = $request->fields;

        $k = $request->k;
        $gender_str = $request->input('gender_str');
        $delivery_type_str = $request->input('delivery_type_str');
        $industry_str = $request->input('industry_str');
        $function_str = $request->input('function_str');
        $management_str = $request->input('management_str');
        
        $topic_str = $request->input('topic_str');
        $city_str = $request->input('city_str');
        $list['page_name'] = "search";
        
        $salutation_str = $request->input('salutation_str');
        $s_currency_str = $request->input('s_currency_str');
        $expert_language_str = $request->input('expert_language_str');
        $endorsement_str = $request->input('endorsement_str');
        $price_range_str = $request->input('price_str');
        $is_probono = $request->input('is_probono');
        $main_page = $request->input('main_page');
        
        $is_display_order = $request->input('is_display_order'); // Only for home page.
        
        if($is_display_order = 'Yes' || $is_display_order = 'yes'){
            $order_by = '`displayorder` ASC';
            if($sort_by == "a2z"){
                //$order_by = '`first_name` ASC';
                $order_by = '`displayorder`< 9999 DESC, `first_name` ASC';
                $limit = $request->offset;
                $offset = 0;
            }else if($sort_by == "z2a"){
                //$order_by = '`first_name` DESC';
                $order_by = '`displayorder`< 9999 DESC, `first_name` DESC';
                $limit = $request->offset;
                $offset = 0;
            }else if($sort_by == "endorsed"){
                //$order_by = '`first_name` DESC';
                $order_by = '`endorsement` DESC';
                $limit = $request->offset;
                $offset = 0;
            }
        }else{
            $order_by = '`contact_status`="Special" DESC';
            if($sort_by == "a2z"){
                //$order_by = '`first_name` ASC';
                $order_by = '`contact_status`="Special" DESC, `first_name` ASC';
                $limit = $request->offset;
                $offset = 0;
            }else if($sort_by == "z2a"){
                //$order_by = '`first_name` DESC';
                $order_by = '`contact_status`="Special" DESC, `first_name` DESC';
                $limit = $request->offset;
                $offset = 0;
            }else if($sort_by == "endorsed"){
                //$order_by = '`first_name` DESC';
                $order_by = '`endorsement` DESC';
                $limit = $request->offset;
                $offset = 0;
            }
        }
        
        /*if($offset != "" and $offset > 0){
            $limit = $request->offset;
            $offset = 0;
        }*/

        $query = DB::table('si_experts');
        if($selected_field != ""){
            //$field_arr = explode(",", $selected_field);
            $query = $query->select(DB::raw($selected_field));
        }else{
            $query = $query->select('expert_id','salutation','first_name','last_name','sex','contact_status','city','state','country','designation',
                'organisation','industry','function','mode_id','topic1','topic2','topic3','topic4','topic5','contact_image','is_visible','main_page',
                'version','seo_url','endorsement','s_currency','expert_language');
        }

        $query = $query->where('is_visible', '=', 1)
        ->where(function ($query) {
            $query->where('contact_status', '=', 'Registered')
            ->orWhere('contact_status', '=', 'Special')
            ->orWhere('contact_status', '=', 'SEMP')
            ->orWhere('expert_type', '=', "Registered")
            ->orWhere('expert_type', '=', "Special")
            ->orWhere('expert_type', '=', "SEMP");
        });
        if($main_page=="Yes" || $main_page=="yes"){
            $query = $query->where('main_page', '=', 1);
        }
        /*$k_arr = explode(" ", $k);
        if(count($k_arr)> 0){
            $query = $query->where(function ($query) use ($k_arr) {
                $query->where('first_name','LIKE','%'.$k_arr[0].'%');
                foreach($k_arr as $key) {
                    $query->orWhere('last_name','LIKE','%'.$key.'%')
                    ->orWhere('topic1','LIKE','%'.$key.'%')
                    ->orWhere('topic2','LIKE','%'.$key.'%')
                    ->orWhere('topic3','LIKE','%'.$key.'%')
                    ->orWhere('topic4','LIKE','%'.$key.'%')
                    ->orWhere('topic5','LIKE','%'.$key.'%')
                    ->orWhere('designation','LIKE','%'.$key.'%')
                    ->orWhere('organisation','LIKE','%'.$key.'%')
                    ->orWhere('expert_type','LIKE','%'.$key.'%')
                    ->orWhere('city','LIKE','%'.$key.'%')
                    ->orWhere('state','LIKE','%'.$key.'%');
                }
            });
        }*/
        
        
            
        if(@$k != ""){
            $query = $query->where(function ($query) use ($k) {
                $query->where('first_name','LIKE','%'.$k.'%')
                ->orWhere('last_name','LIKE','%'.$k.'%')
                ->orWhere('full_name','LIKE','%'.$k.'%')
                ->orWhere('topic1','LIKE','%'.$k.'%')
                ->orWhere('topic2','LIKE','%'.$k.'%')
                ->orWhere('topic3','LIKE','%'.$k.'%')
                ->orWhere('topic4','LIKE','%'.$k.'%')
                ->orWhere('topic5','LIKE','%'.$k.'%')
                ->orWhere('designation','LIKE','%'.$k.'%')
                ->orWhere('organisation','LIKE','%'.$k.'%')
                ->orWhere('expert_type','LIKE','%'.$k.'%')
                ->orWhere('city','LIKE','%'.$k.'%')
                ->orWhere('state','LIKE','%'.$k.'%');
            });
        }
        
        if($gender_str!=""){
            $gen_arr = explode(",", $gender_str);
            $query = $query->whereIn('sex', $gen_arr);
        }
        if($delivery_type_str!=""){
            $dty_arr = explode(",", $delivery_type_str);
            $query = $query->whereIn('delivery_type', $dty_arr);
        }
        if($industry_str!=""){
            $ind_arr = explode(",", $industry_str);
            $query = $query->whereIn('industry', $ind_arr);
        }
        if($function_str!=""){
            $fun_arr = explode(",", $function_str);
            $query = $query->whereIn('function', $fun_arr);
        }
        if($management_str!=""){
            $man_arr = explode(",", $management_str);
            $query = $query->whereIn('management', $man_arr);
        }
        if($topic_str!=""){
            $top_arr = explode(",", $topic_str);
            $query = $query->where(function ($query) use ($top_arr) {
                $query->orWhereIn('topic1', $top_arr)
                ->orWhereIn('topic2', $top_arr)
                ->orWhereIn('topic3', $top_arr)
                ->orWhereIn('topic4', $top_arr)
                ->orWhereIn('topic5', $top_arr);
            });
        }
        if($salutation_str!=""){
            $sal_arr = explode(",", $salutation_str);
            $query = $query->whereIn('salutation', $sal_arr);
        }
        if($s_currency_str!=""){
            $sal_arr = explode(",", $s_currency_str);
            $query = $query->whereIn('s_currency', $sal_arr);
        }
        /*if($expert_language_str!=""){
            $lang_arr = explode(",", $expert_language_str);
            $query = $query->whereIn('expert_language', $lang_arr);
        }*/
        if($expert_language_str!=""){
            $lang_arr = explode(",", $expert_language_str);
            $query = $query->where(function ($query) use ($lang_arr) {
                $query->orWhereIn('expert_language', $lang_arr)
                ->orWhereIn('expert_secondar_language', $lang_arr);
            });
        }
        if($endorsement_str!=""){
            $endo_arr = explode(",", $endorsement_str);
            foreach ($endo_arr as $v){
                $endo_arr1 = explode("||", $v);
                $end_arr[]=$endo_arr1[0];
                $end_arr[]=$endo_arr1[1];
            }
            $min_endo = min($end_arr);
            $max_endo = max($end_arr);
            $query = $query->whereBetween('endorsement', [$min_endo, $max_endo]);
        }
        
        if($price_range_str!=""){
            $pran_arr0 = explode(",", $price_range_str);
            foreach ($pran_arr0 as $v){
                $pran_arr1 = explode("||", $v);
                $pran_arr[]=$pran_arr1[0];
                $pran_arr[]=$pran_arr1[1];
            }
            $min_price = min($pran_arr);
            $max_price = max($pran_arr);
            $query = $query->whereBetween('expert_cost', [$min_price, $max_price]);
        }
        if($is_probono == 'Yes' || $is_probono == 'yes'){
            $query = $query->where('is_probono', 1);
        }
        if($city_str!=""){
            $city_arr = explode(",", $city_str);
            $query = $query->whereIn('city', $city_arr);
        }
        
        $query  = $query->orderByRaw(DB::raw($order_by));
        //$query  = $query->orderBy("FIELD(`first_name`,'John','Branding')", 'asc');
        /*$k_arr = explode(" ", $k);
        if(count($k_arr)> 0){
            foreach($k_arr as $key) {
                $query  = $query->orderByRaw(DB::raw("FIELD(`first_name`,'{{$key}}') DESC"))
                ->orderByRaw(DB::raw("FIELD(`last_name`,$key) DESC"))
                ->orderByRaw(DB::raw("FIELD(`topic1`,$key) DESC"))
                ->orderByRaw(DB::raw("FIELD(`topic2`,$key) DESC"))
                ->orderByRaw(DB::raw("FIELD(`topic3`,$key) DESC"))
                ->orderByRaw(DB::raw("FIELD(`topic4`,$key) DESC"))
                ->orderByRaw(DB::raw("FIELD(`topic5`,$key) DESC"))
                ->orderByRaw(DB::raw("FIELD(`designation`,$key) DESC"))
                ->orderByRaw(DB::raw("FIELD(`organisation`,$key) DESC"))
                ->orderByRaw(DB::raw("FIELD(`expert_type`,$key) DESC"))
                ->orderByRaw(DB::raw("FIELD(`state`,$key) DESC"))
                ->orderByRaw(DB::raw("FIELD(`city`,$key) DESC"));
            }
        }*/
        
        
        
        if($offset != ""){
            $query  = $query->offset($offset);
        }
        
        if($limit != ""){
            $query  = $query->limit($limit);
        }
        
       
        
        $list['speaker'] = $query->get();
        //dd(DB::getQueryLog());

        
        return response()->json($list['speaker'],200);

            
        //return response()->json(SearchModel::get(['expert_id','email','first_name','last_name']),200);
    }
}
