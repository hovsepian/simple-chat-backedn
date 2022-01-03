<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\PageUser;
use App\Models\UserSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Rules\MatchOldPassword;
use Illuminate\Support\Facades\Hash;
use App\User;
use App\Models\Settings;

class AuthController extends Controller
{
    private $user;

    public function getUser(Request $request){
        $user = $request->user();
        $user->settings;
        return response()->json($user, 200);
    }

    protected function validatorGeneral(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $this->user->id],
            'username' => ['required', 'string', 'min:6', 'max:255', 'unique:users,username,' . $this->user->id],
        ]);
    }

    public function updateProfile(Request $request){
        $this->user = $request->user();
        $validator = $this->validatorGeneral($request->all());
        if(!$validator->fails()){
            $this->user->name = $request->input('name');
            $this->user->email = $request->input('email');
            $this->user->username = $request->input('username');
            $this->user->save();
            return response()->json(['user' => $this->user], 200);
        }else{
            $errors = $validator->errors();
            return response()->json(['errors' => $errors, 'message' => 'Please check your entered data.'], 409);
        }
    }

    protected function validatorPassword(array $data)
    {
        return Validator::make($data, [
            'password' => ['required', new MatchOldPassword],
            'new_password' => ['required', 'string', 'min:6',],
            'password_confirmation' => ['same:new_password']
        ]);
    }

    public function updatePassword(Request $request){
        $this->user = $request->user();
        $validator = $this->validatorPassword($request->all());
        if(!$validator->fails()){
            $this->user->password = Hash::make($request->input('new_password'));
            $this->user->save();
            return response()->json(['user' => $this->user], 200);
        }else{
            $errors = $validator->errors();
            return response()->json(['errors' => $errors, 'message' => 'Please check your entered data.'], 409);
        }
    }

    public function updateSettings(Request $request){
        $this->user = $request->user();
        if($this->user){
            $data = $request->all();
            $settings = UserSettings::updateOrCreate([
                'user_id' => $this->user->id
            ],[
                'dark_theme' => !empty($data['dark_theme']) ? 1 : 0,
                'important_updates' => !empty($data['important_updates']) ? 1 : 0,
                'notifications' => !empty($data['notifications']) ? 1 : 0,
                'new_tab' => !empty($data['new_tab']) ? 1 : 0,
                'last_page' => !empty($data['last_page']) ? 1 : 0
            ]);
            $this->user->settings;
            return response()->json(['status' => 'success', 'user' => $this->user], 200);
        }
    }

    public function reorderPages(Request $request){
        $sortedData = $request->input('order');
        if(!empty($sortedData)){
            foreach($sortedData as $order){
                PageUser::where('id', $order['id'])->update(['order' => $order['order']]);
            }
        }
    }

    protected function validatorDeleteAccount(array $data)
    {
        return Validator::make($data, [
            'password' => ['required', new MatchOldPassword]
        ]);
    }

    public function deleteProfileData(){

    }

    public function transferProfileData(){

    }

    public function deleteProfile(Request $request){
        $this->user = $request->user();
        $validator = $this->validatorDeleteAccount($request->all());
        if(!$validator->fails()){
            $transferred_email = $request->input("transferred_email");
            if(!empty($transferred_email)){
                $transferred_user = User::where('email', $transferred_email)->where('id', '!=', $this->user->id)->first();
                if($transferred_user){
                    $this->transferProfileData();
                }else{
                    return response()->json(['errors' => ['transferred_email' => ['User not found. Please enter a existing user email.']]], 404);
                }
            }
            $this->deleteProfileData();
            $this->user->delete();
            return response()->json(['deleted' => true], 200);
        }else{
            $errors = $validator->errors();
            return response()->json(['errors' => $errors], 409);
        }
    }
}
