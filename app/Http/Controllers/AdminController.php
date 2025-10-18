<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
 
/*
 |--------------------------------------------------------------------------
 | AdminController
 |--------------------------------------------------------------------------
 | Handles admin dashboard/auth, profile management, and CRUD operations for
 | admin and agent users, including role assignment and status changes.
 */
class AdminController extends Controller
{
    // Display the admin dashboard view.
    public function AdminDashboard(){

        return view('admin.index');

    } // End Method  

    // Log out the admin and invalidate the current session.
    public function AdminLogout(Request $request){
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

         $notification = array(
            'message' => 'Admin Logout Successfully',
            'alert-type' => 'success'
        ); 

        return redirect('/admin/login')->with($notification);
    }// End Method 


    // Show the admin login page.
    public function AdminLogin(){

        return view('admin.admin_login');

    }// End Method 


    // Show the authenticated admin profile page.
    public function AdminProfile(){

        $id = Auth::user()->id;
        $profileData = User::find($id);
        return view('admin.admin_profile_view',compact('profileData'));

     }// End Method 


     // Update admin profile details and optionally replace the profile photo.
     public function AdminProfileStore(Request $request){

        $id = Auth::user()->id;
        $data = User::find($id);
        $data->username = $request->username;
        $data->name = $request->name;
        $data->email = $request->email;
        $data->phone = $request->phone;
        $data->address = $request->address; 

        if ($request->file('photo')) {
            $file = $request->file('photo');
            // Remove previous photo if it exists and store the new one.
            @unlink(public_path('upload/admin_images/'.$data->photo));
            $filename = date('YmdHi').$file->getClientOriginalName(); 
            $file->move(public_path('upload/admin_images'),$filename);
            $data['photo'] = $filename;  
        }

        $data->save();

        $notification = array(
            'message' => 'Admin Profile Updated Successfully',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification);

     }// End Method 



     // Show the change password form for the authenticated admin.
     public function AdminChangePassword(){

         $id = Auth::user()->id;
        $profileData = User::find($id);
        return view('admin.admin_change_password',compact('profileData'));

     }// End Method 


     // Validate and update the authenticated admin password.
     public function AdminUpdatePassword(Request $request){

        // Validation 
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|confirmed'

        ]);

        /// Match The Old Password

        if (!Hash::check($request->old_password, auth::user()->password)) {
          
           $notification = array(
            'message' => 'Old Password Does not Match!',
            'alert-type' => 'error'
        );

        return back()->with($notification);
        }

        /// Update The New Password 

        User::whereId(auth()->user()->id)->update([
            'password' => Hash::make($request->new_password)

        ]);

         $notification = array(
            'message' => 'Password Change Successfully',
            'alert-type' => 'success'
        );

        return back()->with($notification); 

     }// End Method 


     /////////// Agent User All Method ////////////
 
  // List all agent users.
  public function AllAgent(){

    $allagent = User::where('role','agent')->get();
    return view('backend.agentuser.all_agent',compact('allagent'));

  }// End Method 

  // Show the form to create a new agent.
  public function AddAgent(){

    return view('backend.agentuser.add_agent');

  }// End Method 


  // Store a newly created agent user.
  public function StoreAgent(Request $request){

    User::insert([
        'name' => $request->name,
        'email' => $request->email,
        'phone' => $request->phone,
        'address' => $request->address,
        'password' => Hash::make($request->password),
        'role' => 'agent',
        'status' => 'active', 
    ]);


       $notification = array(
            'message' => 'Agent Created Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('all.agent')->with($notification); 


  }// End Method 


  // Show the form to edit an existing agent user.
  public function EditAgent($id){

    $allagent = User::findOrFail($id);
    return view('backend.agentuser.edit_agent',compact('allagent'));

  }// End Method 


  // Update basic details of an existing agent user.
  public function UpdateAgent(Request $request){

    $user_id = $request->id;

    User::findOrFail($user_id)->update([
        'name' => $request->name,
        'email' => $request->email,
        'phone' => $request->phone,
        'address' => $request->address, 
    ]);


       $notification = array(
            'message' => 'Agent Updated Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('all.agent')->with($notification);  

  }// End Method 


  // Delete an agent user by id.
  public function DeleteAgent($id){

    User::findOrFail($id)->delete();

     $notification = array(
            'message' => 'Agent Deleted Successfully',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification); 

  }// End Method 


  // Toggle a user's status (active/inactive) via AJAX.
  public function changeStatus(Request $request){

    $user = User::find($request->user_id);
    $user->status = $request->status;
    $user->save();

    return response()->json(['success'=>'Status Change Successfully']);

  }// End Method 


       /////////// Admin User All Method ////////////
 
  // List all admin users.
  public function AllAdmin(){

    $alladmin = User::where('role','admin')->get();
    return view('backend.pages.admin.all_admin',compact('alladmin'));

  }// End Method 


  // Show the form to create a new admin user (with roles).
  public function AddAdmin(){

    $roles = Role::all();
    return view('backend.pages.admin.add_admin',compact('roles'));

  }// End Method 


  // Store a newly created admin user and optionally assign roles.
  public function StoreAdmin(Request $request){

    $user = new User();
    $user->username = $request->username;
    $user->name = $request->name;
    $user->email = $request->email;
    $user->phone = $request->phone;
    $user->address = $request->address;
    $user->password =  Hash::make($request->password);
    $user->role = 'admin';
    $user->status = 'active';
    $user->save();

    if ($request->roles) {
        $user->assignRole($request->roles);
    }

    $notification = array(
            'message' => 'New Admin User Inserted Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('all.admin')->with($notification); 

  }// End Method 


  // Show the form to edit an existing admin user and manage roles.
  public function EditAdmin($id){

    $user = User::findOrFail($id);
    $roles = Role::all();
    return view('backend.pages.admin.edit_admin',compact('user','roles'));

  }// End Method

  // Update admin user details and sync roles.
   public function UpdateAdmin(Request $request,$id){

    $user = User::findOrFail($id);
    $user->username = $request->username;
    $user->name = $request->name;
    $user->email = $request->email;
    $user->phone = $request->phone;
    $user->address = $request->address; 
    $user->role = 'admin';
    $user->status = 'active';
    $user->save();

    // Reset existing roles, then assign the provided roles if any.
    $user->roles()->detach();
    if ($request->roles) {
        $user->assignRole($request->roles);
    }

    $notification = array(
            'message' => 'New Admin User Updated Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('all.admin')->with($notification); 

  }// End Method 


  // Delete an admin user by id.
  public function DeleteAdmin($id){

    $user = User::findOrFail($id);
    if (!is_null($user)) {
        $user->delete();
    }

    $notification = array(
            'message' => 'New Admin User Deleted Successfully',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification); 

  }// End Method 



}
