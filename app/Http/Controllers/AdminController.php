<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Transaction;
use App\Addressbook;
use App\User;

class AdminController extends Controller
{
    public function __construct() {
      $this->middleware('admin');
	}
	
	public function userlist(Request $request)
    {
        $data = User::where('email','not like','%@example.com')->orderBy('id','DESC')->paginate(20);
        return view('admin.users',compact('data'))
            ->with('i', ($request->input('page', 1) - 1) * 20);
    }
    
    public function addressbooks(Request $request)
    {
        $data = Addressbook::orderBy('id','DESC')->paginate(20);
        return view('admin.addressbooks',compact('data'))
            ->with('i', ($request->input('page', 1) - 1) * 20);
    }
    
    public function transactions(Request $request)
    {
        $data = Transaction::orderBy('id','DESC')->paginate(20);
        return view('admin.transactions',compact('data'))
            ->with('i', ($request->input('page', 1) - 1) * 20);
    }
    
    public function user($id)
    {
        $data = User::find($id);
        return view('admin.user',compact('data'));
    }
    
    public function index()
    {
        return view('admin.index');
    }
    
    public function g2fa($id)
    {
    	$user = User::find($id);
    	$user->google2fa_secret = '0';
    	$user->save();
        return Redirect::route('user',$id)->withErrors(['msg', 'The Message']);
    }
    public function add($id)
    {
    	$user = User::find($id);
    	$user->address = 0;
    	$user->save();
        return Redirect::route('user',$id)->withErrors(['msg', 'The Message']);
    }
    
}
