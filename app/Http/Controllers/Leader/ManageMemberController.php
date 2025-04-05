<?php

namespace App\Http\Controllers\Leader;

use App\Http\Controllers\Controller;
use App\Models\specialization;
use App\Models\Team;
use App\Models\Team_Members;
use App\Models\User;
use Exception;
use http\Env\Response;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Auth;

class ManageMemberController extends Controller
{
    //                        ------------- Manage Team Members -------------

    ####################### Add Developer By Team Leader To The Team #######################
    public function addDeveloper(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users,email',
            'specialization' => 'required|string|exists:specializations,name',
        ]);

        try {
            $leader = Auth::user();
            if (!$leader)
            {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            if (!($leader->can('add team member')))
            {
                return response()->json([
                    'massage' => 'Forbidden access.'
                ]);
            }
            $randomPassword = Str::random(9);
            $developer = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($randomPassword),
            ]);

            $developer->assignRole('developer');
            $team = (new Team)->where('user_id', $leader->id)->firstOrFail();

            $specialization = (new specialization)->where('name', $data['specialization'])->firstOrFail();

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
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Failed to create developer.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    ####################### Remove Member From Team By Leader #######################
    public function removeDeveloper($id): JsonResponse
    {
        try {
            $leader = Auth::user();

            if (!$leader)
            {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            if (!($leader->can('remove team member')))
            {
                return response()->json([
                    'massage' => 'Forbidden access.'
                ]);
            }
            $team = Team::where('user_id', $leader->id)->firstOrFail();

            $developer = User::findOrFail($id);
            Team_Members::where('team_id', $team->id)
                ->where('developer_id',$developer->id)
                ->delete();

            return response()->json([
                'message' => 'Developer deleted successfully.',
            ]);
        }
        catch (Exception $e) {
            return response()->json([
                'message' => 'Failed to delete developer.',
                'error' => $e->getMessage(),
            ]);
        }
    }


    ####################### Send Developer Credentials To His Email #######################
    public function sendCredentialsByEmail($developer , $password): JsonResponse
    {
        try {
            Mail::send('email.new-developer-credentials', [
                'name' => $developer->name,
                'password' => $password,
            ], function ($message) use ($developer) {
                $message->to($developer->email);
                $message->subject('Account credentials');
            });

            return  response()->json([
                'message' => 'Credentials sent successfully.',
            ]);
        }catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ]);
        }
    }



    //                        ------------- Manage Team Permissions -------------

    ####################### Assign Permission To Developer By Team Leader #######################
    public function assignPermission(Request $request,$id): JsonResponse
    {
        $data = $request->validate([
            'permission' => 'required|string|exists:permissions,name',
        ]);
        try {
            $leader = Auth::user();

            if (!$leader)
            {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            if (!($leader->can('assign member permissions')))
            {
                return response()->json([
                    'massage' => 'Forbidden access.'
                ]);
            }

            $team = Team::where('user_id', $leader->id)->firstOrFail();

            $developer = User::whereHas('team_member', function ($query) use ($team) {
                $query->where('team_id', $team->id);
            })->findOrFail($id);

            $permission = Permission::where('name', $data['permission'])->firstOrFail();
            $developer->givePermissionTo($permission);

            return response()->json([
                'message' => 'Permission assigned successfully.',
                'developer' => $developer,
                'permission' => $permission,
            ],200);
        }
        catch (Exception $e) {
            return response()->json([
                'message' => 'Failed to assign permission.',
                'error' => $e->getMessage(),
            ]);
        }
    }


    ####################### Unsigned Permission From Developer By Team Leader #######################
    public function unsignedPermission(Request $request,$id): JsonResponse
    {
        $data = $request->validate([
            'permission' => 'required|string|exists:permissions,name',
        ]);
        try {
            $leader = Auth::user();

            if (!$leader)
            {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            if (!($leader->can('revoke member permissions')))
            {
                return response()->json([
                    'massage' => 'Forbidden access.'
                ]);
            }

            $team = Team::where('user_id', $leader->id)->firstOrFail();

            $developer = User::whereHas('team_member', function ($query) use ($team) {
                $query->where('team_id', $team->id);
            })->findOrFail($id);

            $permission = Permission::where('name', $data['permission'])->firstOrFail();

            if (!$developer->hasPermissionTo($permission))
            {
                return response()->json([
                    'message' => 'The developer does not have this permission.',
                ], 400);
            }

            $developer->revokePermissionTo($permission);
            return response()->json([
                'message' => 'Permission removed successfully.',
                'developer' => $developer,
                'permission' => $permission->name,
            ]);
        }
        catch (Exception $e) {
            return response()->json([
                'message' => 'Failed to remove permission.',
                'error' => $e->getMessage(),
            ]);
        }
    }


    ####################### Display Team Member To Leader #######################
    public function displayTeamMembersToLeader(): JsonResponse
    {
        try {
            $leader = Auth::user();
            $team = $leader->ownedTeam()->firstOrFail();

            $teamMembers = $team->members()->get();
            return response()->json([
                'leader' => $leader,
                'teamMembers' => $teamMembers,
            ]);
        }
        catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ]);
        }
    }

}
