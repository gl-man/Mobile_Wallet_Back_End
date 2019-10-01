<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Denpa\Bitcoin\Client as BitcoinClient;
use Illuminate\Support\Facades\Mail;
use App\Mail\BalanceUpdate;
use App\Transaction;
use App\Block;
use App\User;

class Deposit extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bitpetcoin:deposit';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
    	$daemon = new bitcoinClient(env('BPC_RPC')); 
        $current = $daemon->getblockcount()->get();
	    $system = Block::max('height');
	    
	    if($current > $system ){
	    	for($i= $system+1 ; $i <= $current; $i++ ){
	    		echo $i . PHP_EOL;
	    		$hash = $daemon->getblockhash($i)->get() ;
	    		
	    		$block = new Block;
			    $block->height = $i;
			    $block->blocks = $hash;
			    $block->save();

			    $tx = $daemon->getblock($hash)->get();
			    
			    for($j=0;$j<count($tx['tx']);$j++){ 
			    
			    	try {
				    	$data = $daemon->gettransaction($tx['tx'][$j])->get();
				    	for($k=0;$k<count($data['details']);$k++){
				    		print_r($data['details']);
				    		$response = $data['details'][$k];
				    		if($response['category'] == 'receive'){
					    		$user = User::where('address',$response['address'])->first();
					    		
					    		if($user){
					    		
					    			$transaction = New Transaction ;
					    			$transaction->user_id = $user->id ;
					    			$transaction->hash = $hash;
					    			$transaction->address = $response['address'];
					    			$transaction->vout = $response['vout'] ;
					    			$transaction->type = $response['category'] ;
					    			
						    		
						    		$transaction->amount = $response['amount'] ;
						    		$transaction->save();
						    			
						    		$user->balance = $user->balance + $transaction->amount ;
						    		$user->save();
						    			
						    		$data1 = array('balance'=>$user->balance,'amount'=>$response['amount'],'hash'=>$hash,'type'=>'receive');
						    		Mail::to($user->email)->send(new BalanceUpdate($data1));
						    		
					    		}
				    		}
				    	}
				    } catch (\Exception $e) {
						continue;
				    }
			    }
	    	}
	    }   	
    }
}
