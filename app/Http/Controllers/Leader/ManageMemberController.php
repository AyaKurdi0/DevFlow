<?php

namespace App\Http\Controllers\Leader;

use App\Http\Controllers\Controller;
use App\Models\specialization;
use App\Models\Team;
use App\Models\Team_Members;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;

class ManageMemberController extends Controller
{

    ####################### Add Developer By Team Leader To The Team #######################
    public function addDeveloper(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users,email',
            'specialization' => 'required|string|exists:specializations,name',
        ]);

        try {
            $randomPassword = Str::random(9);
            $developer = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($randomPassword),
            ]);

            $developer->assignRole('developer');

            $leader = Auth::user();
            $team = Team::where('user_id', $leader->id)->firstOrFail();

            $specialization = Specialization::where('name', $data['specialization'])->firstOrFail();

            $teamMember = Team_Members::create([
                'team_id' => $team->id,
                'developer_id' => $developer->id,
                'specialization_id' => $specialization->id,
            ]);

            event(new Registered($developer));
            $this->sendCredentialsByEmail($developer, $randomPassword);
            return response()->json([
                'message' => 'Developer created successfully.',
                'team_member' => $teamMember,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create developer.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    ####################### Remove Member From Team By Leader #######################
    public function removeDeveloper(Request $request)
    {
        
    }

    ####################### Send Developer Credentials To His Email #######################
    public function sendCredentialsByEmail($developer , $password)
    {
        Mail::send('email.new-developer-credentials', [
            'name' => $developer->name,
            'password' => $password,
        ], function ($message) use ($developer) {
            $message->to($developer->email);
            $message->subject('Account credentials');
        });
    }



}
