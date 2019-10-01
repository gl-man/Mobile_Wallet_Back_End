<?php

namespace App\Http\Controllers;

use Denpa\Bitcoin\Client as BitcoinClient;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use App\Mail\BalanceUpdate;
use App\Mail\CodeSend;
use App\Addressbook;
use App\Transaction;
use App\User;

class ApiController extends Controller
{
	//Send email Code
    public function codesend($key){
    	$msg = array();
    	$user = User::where('api_token',$key)->first();
    	if($user){
    		$user->email_token =  (int)mt_rand(10000000,99999999);
    		$user->save();
    		Mail::to($user->email)->send(new CodeSend($user));
    		$content =  array('response'=>'success','code'=>202);
    	} else {
    		$content = array('response'=>'error','code'=>101);
    	}
    	
    	if(isset($_GET['callback'])){ 
    		$content = "{$_GET['callback']}(".json_encode($content,JSON_FORCE_OBJECT).")";
    		return $content;
    	} else {
    		return response()->json($content);
    	}
    }
    
	//Email Code Verify
    public function verifycode($code,$key,$validator=1 ){
    	$msg = array();
    	$msg['101'] = 'errortxt.codewrong';

    	$user = User::where('api_token',$key)->where('email_token',$code)->first();
    	if($user){
    		$user->email_token =  null;
    		if($validator == 1){
    			$tmppass = str_random(12);
    			$user->password = \Hash::make($tmppass);
    		} else {
    			$tmppass = '';
    		}
    		$user->save();
    		$content =  array('response'=>'success','code'=>202,'msg' => $tmppass);
    	} else {
    		$content = array('response'=>'error','code'=>101,'msg'=>$msg['101']);
    	}
    	
    	if(isset($_GET['callback'])){ 
    		$content = "{$_GET['callback']}(".json_encode($content,JSON_FORCE_OBJECT).")";
    		return $content;
    	} else {
    		return response()->json($content);
    	}
    }
    
	//Login Api
	public function login($email,$password){	
    	$msg = array();
		$msg['101'] = 'errortxt.emailnotfound';
    	$msg['102'] = 'errortxt.passwordinvalid';
    	
    	$user = User::where('email',urldecode($email))->first();
    	if($user){
    		if(\Hash::check(urldecode($password),$user->password )){
    			if($user->address == '0' ){
    				$daemon = new bitcoinClient(env('BPC_RPC'));
    				try{
	    				$user->address = $daemon->getnewaddress();
	    				$user->save();
	    			} catch (\Exception $e) {
						$data = '';
				    }
    			}
    			
    			if($user->google2fa_secret == '0') {
    				$content =  array('response'=>'success','code'=>202,'msg'=>$user->api_token);
    			} else {
    				$content =  array('response'=>'error','code'=>103,'msg'=>$user->api_token);
    			}
    		} else {
    			$content =  array('response'=>'error','code'=>102,'msg'=>$msg['102']);
    		}
    	} else {
    		$content = array('response'=>'error','code'=>101,'msg'=>$msg['101']);
    	}
    	
    	if(isset($_GET['callback'])){ 
    		$content = "{$_GET['callback']}(".json_encode($content,JSON_FORCE_OBJECT).")";
    		return $content;
    	} else {
    		return response()->json($content);
    	}
    }
    
    //Otp Check
    public function otpconfirm($key , $code){
    	$msg = array();
    	$msg['101'] = 'errortxt.otpnotvalid';
    	
    	$user = User::where('api_token',$key)->first();
    	if($user){
    		$google2fa = app('pragmarx.google2fa');
    		$valid = $google2fa->verifyKey($user->google2fa_secret, $code);
    		if($valid){
    			$content =  array('response'=>'success','code'=>202,'key'=>$user->api_token);
    		} else {
    			$content = array('response'=>'error','code'=>101,'msg'=>$msg['101']);
    		}
    	} else {
    		$content = array('response'=>'error','code'=>203);
    	}
    	
    	if(isset($_GET['callback'])){ 
    		$content = "{$_GET['callback']}(".json_encode($content,JSON_FORCE_OBJECT).")";
    		return $content;
    	} else {
    		return response()->json($content);
    	}
    }
    
    //Reset Password
    public function resetpassword($email){
    	$msg = array();
		$msg['101'] = 'errortxt.emailnotfound';
		
    	$user = User::where('email',$email)->first();
    	if($user){
    		$user->email_token =  (int)mt_rand(10000000,99999999);
    		$user->save();
    		Mail::to($user->email)->send(new CodeSend($user));
    		$content =  array('response'=>'success','code'=>202,'msg'=>$user->api_token);
    	} else {
    		$content = array('response'=>'error','code'=>101,'msg'=>$msg['101']);
    	}
    	
    	if(isset($_GET['callback'])){ 
    		$content = "{$_GET['callback']}(".json_encode($content,JSON_FORCE_OBJECT).")";
    		return $content;
    	} else {
    		return response()->json($content);
    	}
    }
    
    //Registration
    public function registration($email,$pass){
    	$msg = array();
    	$msg['101'] = 'errortext.emailtaken';
    	$msg['102'] = 'errortext.passwordinvalid';

    	$user = User::where('email',$email)->first();
    	if(!$user){
    		$validator = Validator::make(array('pass'=>urldecode($pass)), [
				'pass' => 'required|string|min:8|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9]).{8,}$/',
			]);
			
			if ($validator->fails()) {
				$content = array('response'=>'error','code'=>102,'msg'=>$msg['102']);
			} else {
				$key = $this->getToken(40,time());
				$u = New User;
			    $u->email = urldecode($email) ;
			    $u->name = str_replace('@','',str_replace('.','',urldecode($email))) ;
			    $u->password = \Hash::make(urldecode($pass)) ;
			    $u->email_token =  (int)mt_rand(10000000,99999999);
			    $u->api_token =  $key;
			    $u->save();
			    Mail::to($u->email)->send(new CodeSend($u));
			    $content = array('response'=>'success','code'=>202,'msg'=>$key);
			}
    	} else {
    		$content = array('response'=>'error','code'=>101,'msg'=>$msg['101']);
    	}
    	
    	if(isset($_GET['callback'])){ 
    		$content = "{$_GET['callback']}(".json_encode($content,JSON_FORCE_OBJECT).")";
    		return $content;
    	} else {
    		return response()->json($content);
    	}
    	
    }




    //User Information
    public function user($key){
    	$user = User::where('api_token',$key)->first();
    	if($user){
    		if($user->google2fa_secret==0){
    			$otp = false;
    		} else{
    			$otp = true ;
    		}
    		$msg = array('address'=>$user->address,'balance'=>$user->balance,'email'=>$user->email,'otp'=>$otp);
    		$content =  array('response'=>'success','code'=>202,'msg'=>$msg);
    	} else {
    		$content = array('response'=>'error','code'=>101);
    	}
    	
    	if(isset($_GET['callback'])){ 
    		$content = "{$_GET['callback']}(".json_encode($content,JSON_FORCE_OBJECT).")";
    		return $content;
    	} else {
    		return response()->json($content);
    	}
    }
    
    //Account Delete
    public function acdelete($key){
    	$user = User::where('api_token',$key)->first();
    	if($user){
    		Addressbook::where('user_id',$user->id)->delete();
    		Transaction::where('user_id',$user->id)->delete();
    		User::destroy($user->id);
    		$content =  array('response'=>'success','code'=>202);
    	} else {
    		$content = array('response'=>'error','code'=>101);
    	}
    	
    	if(isset($_GET['callback'])){ 
    		$content = "{$_GET['callback']}(".json_encode($content,JSON_FORCE_OBJECT).")";
    		return $content;
    	} else {
    		return response()->json($content);
    	}
    }
    
    //Address Book Delete
    public function adddelete($key,$id){
    	$user = User::where('api_token',$key)->first();
    	if($user){
    		Addressbook::where('user_id',$user->id)->where('id',$id)->delete();
    		$content =  array('response'=>'success','code'=>202);
    	} else {
    		$content = array('response'=>'error','code'=>101);
    	}
    	
    	if(isset($_GET['callback'])){ 
    		$content = "{$_GET['callback']}(".json_encode($content,JSON_FORCE_OBJECT).")";
    		return $content;
    	} else {
    		return response()->json($content);
    	}
    }
	
	//Password Change
    public function passchange($key,$old,$new,$cnew){
    
    	$msg = array();
    	$msg['101'] = 'korean Try Again';
    	$msg['102'] = 'korean Password & Confirmd not Match';
    	$msg['103'] = 'korean Old Password Not Match';
    	$msg['104'] = 'korean New Password Not Match Condition';
    	$msg['202'] = 'korean Successfully Change Password';

    	
    	$user = User::where('api_token',$key)->first();
    	if($user){
    		if($new == $cnew){
    			if(\Hash::check(urldecode($old),$user->password )){
    				$validator = Validator::make(array('password'=>urldecode($new)), [
			            'password' => 'required|string|min:8|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9]).{8,}$/',
			        ]);
			        if ($validator->fails()) {
			            $content = array('response'=>'error','code'=>104,'msg'=>$msg['104']);
			        } else {
		    			$user->password = \Hash::make(urldecode($new)) ;
		    			$user->save();
			    		$content =  array('response'=>'success','code'=>202,'msg'=>$msg['202']);
			    	}
		    	} else{
		    		$content = array('response'=>'error','code'=>103,'msg'=>$msg['103']);
		    	}
    		} else {
    			$content = array('response'=>'error','code'=>102,'msg'=>$msg['102']);
    		}
    	} else {
    		$content = array('response'=>'error','code'=>101,'msg'=>$msg['101']);
    	}
    	
    	if(isset($_GET['callback'])){ 
    		$content = "{$_GET['callback']}(".json_encode($content,JSON_FORCE_OBJECT).")";
    		return $content;
    	} else {
    		return response()->json($content);
    	}
    }

    //Address Book Entry
    public function addresscreate($key,$name,$address,$fav){
    	$msg = array();
    	$msg['101'] = 'Try Again';
    	$msg['102'] = 'Already Have This Record';

    	$user = User::where('api_token',$key)->first();
    	if($user){
    		$valid = Addressbook::where('user_id',$user->id)->where('address',$address)->first();
    		if($valid){
    			$content =  array('response'=>'error','code'=>102,'msg'=>$msg['102']);
    		} else {
	    		$addressbook = new Addressbook;
	    		$addressbook->user_id = $user->id;
	    		$addressbook->name = $name;
	    		$addressbook->address = $address;
	    		$addressbook->faviroute = $fav;
	    		$addressbook->save();
	    		
	    		$content =  array('response'=>'success','code'=>202);
	    	}
    	} else {
    		$content = array('response'=>'error','code'=>101,'msg'=>$msg['101']);
    	}
    	
    	if(isset($_GET['callback'])){ 
    		$content = "{$_GET['callback']}(".json_encode($content,JSON_FORCE_OBJECT).")";
    		return $content;
    	} else {
    		return response()->json($content);
    	}
    }
    
    //OTP check
    public function otp($key,$code,$type){
    	$msg = array();
    	$msg['101'] = 'korean Try Again';
    	$msg['102'] = 'korean Wrong Code';

    	$user = User::where('api_token',$key)->first();
    	if($user){
    		$google2fa = app('pragmarx.google2fa');
    		
    		if($user->google2fa_secret == '0'){
    			$user->google2fa_secret = $type ;
    			$msg['202'] = 'Successfully Enable';

		    	$valid = $google2fa->verifyKey($user->google2fa_secret, $code);
    		} else {
    			$valid = $google2fa->verifyKey($user->google2fa_secret, $code);
    			if($valid){
    				$user->google2fa_secret = '0' ;
    			}
    			$msg['202'] = 'Successfully Disable';
    		}
    		
    		
    		if($valid){
    			$user->save();
    			$content =  array('response'=>'success','code'=>202,'msg'=>$msg['202']);
    		} else {
    			$content =  array('response'=>'error','code'=>102,'msg'=>$msg['102']);
    		}
    		
    	} else {
    		$content = array('response'=>'error','code'=>101,'msg'=>$msg['101']);
    	}
    	
    	if(isset($_GET['callback'])){ 
    		$content = "{$_GET['callback']}(".json_encode($content,JSON_FORCE_OBJECT).")";
    		return $content;
    	} else {
    		return response()->json($content);
    	}
    }
    
    //Send Transactions
    public function send($key,$address,$amount){
    	$msg = array();
    	$msg['101'] = 'korean Try Again';
    	$msg['102'] = 'korean Address Not Valid';
    	$msg['103'] = 'korean Not have Sufficient Balance';
    	$msg['202'] = 'korean Successfully Complete Send';

    	$user = User::where('api_token',$key)->first();
    	if($user){
    		$daemon = new bitcoinClient(env('BPC_RPC'));
    		$l = $daemon->listunspent(1,9999999,array($user->address))->get();
    		$amt = 0 ;
    		$input = array();
    		$output = array();
    		
    		for($z=0;$z<count($l);$z++){
    			try {
	    			$amt = $amt + $l['amount'];
	    			$input[0]['txid'] = $l['txid'] ;
	    			$input[0]['vout'] = (int)$l['vout'] ;
	    			break;
    			} catch (\Exception $e) {
	    			$amt = $amt + $l[$z]['amount'];
	    			$input[$z]['txid'] = $l[$z]['txid'] ;
	    			$input[$z]['vout'] = (int)$l[$z]['vout'] ;
	    			continue;
    			}
    		}
    		$input = array_reverse($input);
    		
    		try {
    			$flag = $daemon->validateaddress($address)->get();
    			
    			if($flag['isvalid']){
    				
    				$fee = ((count($l)* 180) + 10 + 34 ) * 0.000001 ;
    				$output[$address] = (float)$amount ;
    				if((float)$amount + $fee != $user->balance){
    					$fee = $fee + (34*0.000001) ;
    					if(count($l)%2 == 0 ){
    						$fee = $fee + 0.000001 ;	
    					} else {
    						$fee = $fee - 0.000001 ;
    					}
    					$output[$user->address] = $amt - (float)$amount - $fee;
    				} else {
    					if(count($l)%2 == 1 ){
    						$fee = $fee + 0.000001 ;	
    					} else {
    						$fee = $fee - 0.000001 ;
    					}
    				}
    				
    				
    				$out = json_decode(json_encode($output, JSON_FORCE_OBJECT)) ;
    				$b = (float)$user->balance;
    				$a = (float)((float)$amount + $fee) ;
    				$c = (float)$amt ;
    				if( $b >= $a && $b <= $c  ){
    					$daemon->settxfee(0.001)->get();
    					$raw = $daemon->createrawtransaction($input,$out)->get();
    					$raw = $daemon->signrawtransaction($raw)->get();
    					$hash = $daemon->sendrawtransaction($raw['hex'])->get();
    					
    							$transaction = New Transaction ;
				    			$transaction->user_id = $user->id ;
				    			$transaction->hash = $hash;
				    			$transaction->vout = 0 ;
				    			$transaction->type = 'send' ;
				    			$transaction->amount = $amount ;
				    			$transaction->address = $address ;
					    		$transaction->save();
					    			
					    		$user->balance = 0 ;
					    		$user->save();
					    		
					    		$data = array('address'=>$address,'lock'=>($amt - (float)$amount - $fee),'balance'=>0,'amount'=>(float)$amount,'hash'=>$hash,'type'=>'send');
					    		Mail::to($user->email)->send(new BalanceUpdate($data));
					    		
    					$content =  array('response'=>'success','code'=>202,'msg'=>$msg['202']);
    				} else {
    					$content =  array('response'=>'error','code'=>103,'msg'=>$msg['103']);
    				}
    			} else {
    				$content =  array('response'=>'errors','code'=>102,'msg'=>$msg['102']);
    			}
    		} catch (\Exception $e) {
				$content =  array('response'=>'error','code'=>102,'msg'=>$msg['102']);
			}
    		
    	} else {
    		$content = array('response'=>'error','code'=>101,'msg'=>$msg['101']);
    	}
    	
    	if(isset($_GET['callback'])){ 
    		$content = "{$_GET['callback']}(".json_encode($content,JSON_FORCE_OBJECT).")";
    		return $content;
    	} else {
    		return response()->json($content);
    	}
    }
    
    
    //Address Book Edit
    public function addressedit($key,$name,$address,$fav,$id){
    	$msg = array();
    	$msg['101'] = 'korean Try Again';
    	$msg['202'] = 'korean Successfully Update Record';

    	$user = User::where('api_token',$key)->first();
    	if($user){
    		$addressbook = Addressbook::find($id);
    		$addressbook->name = $name;
	    	$addressbook->address = $address;
	    	$addressbook->faviroute = $fav;
	    	$addressbook->save();
	    		
	    	$content =  array('response'=>'success','code'=>202,'msg'=>$msg['202']);
	    	
    	} else {
    		$content = array('response'=>'error','code'=>101,'msg'=>$msg['101']);
    	}
    	
    	if(isset($_GET['callback'])){ 
    		$content = "{$_GET['callback']}(".json_encode($content,JSON_FORCE_OBJECT).")";
    		return $content;
    	} else {
    		return response()->json($content);
    	}
    }
    
    
    private function getToken($length, $seed){    
        $token = "";
        $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $codeAlphabet .= "abcdefghijklmnopqrstuvwxyz";
        $codeAlphabet.= "0123456789";
        mt_srand($seed);
        for($i=0;$i<$length;$i++){
            $token .= $codeAlphabet[mt_rand(0,strlen($codeAlphabet)-1)];
        }
        $user = User::where('api_token',$token)->first();
        
        if($user){
        	return $this->getToken(40,time());
        } else{
        	return $token;
        }
    }
 
}
