<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

use App\Models\Admin;

use Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public $successStatus = 200;
	public $errorStatus = 400;

    public $profileDir = 'users/profile/';

    /* Create user profile avatar image directory */
    public function getUserProfileDirectory($userId)
    {        
        $year  = Carbon::now()->format('Y');
        $month = Carbon::now()->format('m');
        $day   = Carbon::now()->format('d');

        return array(
            'storagePath' => public_path( $this->profileDir.$userId.'/'.$year.'/'.$month.'/'.$day),
            'storageDir'  => $userId.'/'.$year.'/'.$month.'/'.$day
        );        
    }

    public function createUserProfileDirectory($userId)
    {
        $path = $this->getUserProfileDirectory($userId);

        if(!File::isDirectory($path['storagePath'])){
            File::makeDirectory($path['storagePath'], 0777, true, true);
        }   
    } 
    
    //todo: admin login form
    public function login_form()
    {
        return view('auth.login');
    }

    //todo: register form
    public function register_form()
    {
        return view('auth.register');
    }

    //todo: admin login functionality
    public function login_validate(Request $request)
    {
        $validator = Validator::make($request->all(), [             
            'email'       => 'required|email',            
            'password'    => 'required',                        
        ]);
        if ($validator->fails()) { 
            return redirect()->back()->withErrors($validator->errors())->withInput($request->input());
        }

        //if (Auth::guard('admin')->attempt(['email' => $request->email, 'password' => $request->password])) {
        $userdata = array(
            'email' => $request->email,
            'password' => $request->password
        );
        if (Auth::guard('admin')->attempt($userdata, true)) {               
            return redirect()->route('dashboard');
        }else{
            Session::flash('error-message','Invalid Email or Password');
            return back();
        }
    }

    public function register(Request $request) 
    {
        try {          

            $validator = Validator::make($request->all(), [ 
                'name'        => 'required',
                'email'       => 'required|email', 
                'username'    => 'required', 
                'password'    => 'required|min:6|same:password_confirmation',
				'password_confirmation' => 'required',
                'dob'         => 'required|date_format:Y-m-d',
                'phone'       => 'required|numeric',
                'location'    => 'required',
                'profile_pic' => 'required|mimes:jpeg,png,jpg,svg|max:2048'                
            ]);
            if ($validator->fails()) { 
                return redirect()->back()->withErrors($validator->errors())->withInput($request->input());
            }
            $input = $request->all();  

            $input['password'] = Hash::make($input['password']);  

            $storagePath = public_path( $this->profileDir );
            $storageDir = $this->profileDir;

            $fileName = $input['username'].'_'.time() .'.'.$request->profile_pic->extension(); 
            $request->profile_pic->move($storagePath, $fileName);   
            $input['profile_pic'] = $storageDir.'/'.$fileName;
            
            $user = Admin::create($input); 
            
            return redirect()->back()->withSuccess('User Registered Successfully'); 
        }
        catch (\Throwable $exception) {
            return redirect()->back()->withErrors( json_encode($exception->getMessage(), true) )->withInput($request->input());
        } catch (\Illuminate\Database\QueryException $exception) {
            return redirect()->back()->withErrors( json_encode($exception->getMessage(), true) )->withInput($request->input());
        } catch (\PDOException $exception) {
            return redirect()->back()->withErrors( json_encode($exception->getMessage(), true) )->withInput($request->input());
        } catch (\Exception $exception) {
            return redirect()->back()->withErrors( json_encode($exception->getMessage(), true) )->withInput($request->input());
        }  
    }

    public function dashboard()
    {
        return view('admin.dashboard');
    }


    //todo: admin logout functionality
    public function logout(Request $request ){  
        Auth::guard('admin')->logout();
        $request->session()->flush();
        $request->session()->regenerate();
        return redirect()->route('login.form');
    }
}
