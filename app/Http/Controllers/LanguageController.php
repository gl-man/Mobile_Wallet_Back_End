<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Addressbook;
use App\Transaction;
use Storage;
use App\User;

class LanguageController extends Controller
{
    public function index($lang = 'us',$key = 'none'){
    
    	$filename = $lang . '/language.json';
    	$content = Storage::get($filename); 
		if($key != 'none'){
			$user = User::where('api_token',$key)->first();
			$address = $user->address;
			$balance = $user->balance;
			$google2fa = app('pragmarx.google2fa');
    		if($user->google2fa_secret != '0'){
    			$securitykey = $user->google2fa_secret;
    			$security = 'true' ;
    		} else {
    			$securitykey = $google2fa->generateSecretKey();
    			$security = 'false' ;
    		}
    		
			$securitycode = '546576';
			
			$content = str_replace('myaccount@gmail.com',$user->email,$content);
			$content = str_replace('OTPCODE',$securitycode,$content);
			$content = str_replace('OTPKEY',$securitykey,$content);
			$content = str_replace('vmyonoffswitch',$security,$content);
			$content = str_replace('lladdressv',$address,$content);
			$content = str_replace('llcountercode',$balance,$content);
			$content = str_replace('lladdressimg','http://18.162.78.221/qr/'.$address,$content);
		}
		
		isset($_GET['callback'])? $content = "{$_GET['callback']}($content)" : $content = $content;
    	
    	return $content;
    }
    
    public function transaction($lang = 'us',$key = 'none') {
    	$user = User::where('api_token',$key)->first();
    	$transactions = Transaction::where('user_id',$user->id)->orderBy('id', 'desc')->get();
    	$data = array();
    	foreach($transactions as $transaction){
    		$data[] = array(
    						'time'=>$transaction->created_at,
    						'type'=>$transaction->type,
    						'hash'=>$transaction->hash,
    						'amount'=>$transaction->amount,
    						'address'=>$transaction->address
    					);
    	}
    	
    	$data = json_encode($data);
    	isset($_GET['callback'])? $data = "{$_GET['callback']}($data)" : $data = $data;
    	return $data;
    }
    public function addressbook($lang = 'us',$key = 'none') {
    	
    	$user = User::where('api_token',$key)->first();
    	$addressbooks = Addressbook::where('user_id',$user->id)->get();
    	$data = array();
    	foreach($addressbooks as $addressbook){
    		$data[] = array(
    						'name'=>$addressbook->name,
    						'address'=>$addressbook->address,
    						'id' => $addressbook->id
    					);
    	}
    	$data = json_encode($data);
    	isset($_GET['callback'])? $data = "{$_GET['callback']}($data)" : $data = $data;
    	return $data;
    }
    public function addressbookfavirote($lang = 'us',$key = 'none') {
    	
    	$user = User::where('api_token',$key)->first();
    	$addressbooks = Addressbook::where('user_id',$user->id)->where('faviroute',1)->get();
    	$data = array();
    	foreach($addressbooks as $addressbook){
    		$data[] = array(
    						'name'=>$addressbook->name,
    						'address'=>$addressbook->address,
    						'id' => $addressbook->id
    					);
    	}
    	$data = json_encode($data);
    	isset($_GET['callback'])? $data = "{$_GET['callback']}($data)" : $data = $data;
    	return $data;
    }
}
