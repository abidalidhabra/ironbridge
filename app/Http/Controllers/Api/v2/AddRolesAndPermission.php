<?php

namespace App\Http\Controllers\Api\v2;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Maklad\Permission\Models\Role;
use Maklad\Permission\Models\Permission;
use App\Models\v1\Admin;



class AddRolesAndPermission extends Controller
{
	public function addRolesAndPermissions()
	{
		Role::create(['name' => 'Super Admin','guard_name' => 'admin']);
		Role::create(['name' => 'Admin','guard_name' => 'admin']);

		Permission::create(['name' => 'Dashboard','guard_name' => 'admin', 'module'=>'Dashboard']);
		Permission::create(['name' => 'View Users','guard_name' => 'admin', 'module'=>'Users']);
		Permission::create(['name' => 'Add Users','guard_name' => 'admin', 'module'=>'Users']);


		Permission::create(['name' => 'View News','guard_name' => 'admin', 'module'=>'News']);
		Permission::create(['name' => 'Add News','guard_name' => 'admin', 'module'=>'News']);
		Permission::create(['name' => 'Edit News','guard_name' => 'admin', 'module'=>'News']);
		Permission::create(['name' => 'Delete News','guard_name' => 'admin', 'module'=>'News']);

		Permission::create(['name' => 'View Games','guard_name' => 'admin', 'module'=>'Manage Games / Games']);
		Permission::create(['name' => 'Add Games','guard_name' => 'admin', 'module'=>'Manage Games / Games']);
		Permission::create(['name' => 'Edit Games','guard_name' => 'admin', 'module'=>'Manage Games / Games']);

		Permission::create(['name' => 'View Game Variations','guard_name' => 'admin', 'module'=>'Manage Games / Game Variations']);
		Permission::create(['name' => 'Add Game Variations','guard_name' => 'admin', 'module'=>'Manage Games / Game Variations']);
		Permission::create(['name' => 'Edit Game Variations','guard_name' => 'admin', 'module'=>'Manage Games / Game Variations']);
		Permission::create(['name' => 'Delete Game Variations','guard_name' => 'admin', 'module'=>'Manage Games / Game Variations']);

		Permission::create(['name' => 'View Treasure Locations','guard_name' => 'admin', 'module'=>'Manage Hunts / Treasure Locations']);
		Permission::create(['name' => 'Add Treasure Locations','guard_name' => 'admin', 'module'=>'Manage Hunts / Treasure Locations']);
		Permission::create(['name' => 'Edit Treasure Locations','guard_name' => 'admin', 'module'=>'Manage Hunts / Treasure Locations']);
		Permission::create(['name' => 'Delete Treasure Locations','guard_name' => 'admin', 'module'=>'Manage Hunts / Treasure Locations']);
		
		Permission::create(['name' => 'View Complexity Targets','guard_name' => 'admin', 'module'=>'Manage Hunts / Games Targets']);
		Permission::create(['name' => 'Edit Complexity Targets','guard_name' => 'admin', 'module'=>'Manage Hunts / Games Targets']);

		Permission::create(['name' => 'View Avatars','guard_name' => 'admin', 'module'=>'Avatars']);
		Permission::create(['name' => 'Edit Avatars','guard_name' => 'admin', 'module'=>'Avatars']);

		Permission::create(['name' => 'Add Practice Games','guard_name' => 'admin', 'module'=>'Manage Games / Practice Games Targets']);
		Permission::create(['name' => 'Edit Practice Games','guard_name' => 'admin', 'module'=>'Manage Games / Practice Games Targets']);


		Permission::create(['name' => 'View Event','guard_name' => 'admin', 'module'=>'Events / Create Event']);
		Permission::create(['name' => 'Add Event','guard_name' => 'admin', 'module'=>'Events / Create Event']);
		Permission::create(['name' => 'Edit Event','guard_name' => 'admin', 'module'=>'Events / Create Event']);
		Permission::create(['name' => 'Delete Event','guard_name' => 'admin', 'module'=>'Events / Create Event']);

		Permission::create(['name' => 'View Event Participated','guard_name' => 'admin', 'module'=>'Events / Event Participated']);

		Permission::create(['name' => 'View Payments','guard_name' => 'admin', 'module'=>'Payments']);



		Permission::create(['name' => 'View Discount Coupons','guard_name' => 'admin', 'module'=>'Discount Coupons']);
		Permission::create(['name' => 'Add Discount Coupons','guard_name' => 'admin', 'module'=>'Discount Coupons']);
		Permission::create(['name' => 'Edit Discount Coupons','guard_name' => 'admin', 'module'=>'Discount Coupons']);
		Permission::create(['name' => 'Delete Discount Coupons','guard_name' => 'admin', 'module'=>'Discount Coupons']);

		Permission::create(['name' => 'View Analytics','guard_name' => 'admin', 'module'=>'Analytics']);


		Permission::create(['name' => 'View Hunt Loot Tables','guard_name' => 'admin', 'module'=>'Manage Hunts / Hunt Loot Tables']);
		Permission::create(['name' => 'Edit Hunt Loot Tables','guard_name' => 'admin', 'module'=>'Manage Hunts / Hunt Loot Tables']);
		/*$data = [
					['name' => 'Practice Games Targets','guard_name' => 'admin', 'module'=>'practiceGamesTargets'],
					
				]*/
		  // Auth::user()->givePermissionTo('Dashboard');
    //     Auth::user()->givePermissionTo('View Users');

    //     Auth::user()->givePermissionTo('View News');
    //     Auth::user()->givePermissionTo('Add News');
    //     Auth::user()->givePermissionTo('Edit News');
    //     Auth::user()->givePermissionTo('Delete News');

    //     Auth::user()->givePermissionTo('View Games');
    //     Auth::user()->givePermissionTo('Add Games');
    //     Auth::user()->givePermissionTo('Edit Games');

    //     Auth::user()->givePermissionTo('View Game Variations');
    //     Auth::user()->givePermissionTo('Add Game Variations');
    //     Auth::user()->givePermissionTo('Edit Game Variations');
    //     Auth::user()->givePermissionTo('Delete Game Variations');

    //     Auth::user()->givePermissionTo('View Treasure Locations');
    //     Auth::user()->givePermissionTo('Add Treasure Locations');
    //     Auth::user()->givePermissionTo('Edit Treasure Locations');
    //     Auth::user()->givePermissionTo('Delete Treasure Locations');
        
    //     Auth::user()->givePermissionTo('View Complexity Targets');
    //     Auth::user()->givePermissionTo('Edit Complexity Targets');

    //     Auth::user()->givePermissionTo('View Avatars');
    //     Auth::user()->givePermissionTo('Edit Avatars');

       // Auth::user()->assignRole('Super Admin');

		return response()->json(['message'=>'Roles And Permission created successfully.']);
	}


	public function createPermissions(){
		$permission = [
						['name' => 'Dashboard','guard_name' => 'admin', 'module'=>'Dashboard'],
						['name' => 'View Users','guard_name' => 'admin', 'module'=>'Users'],
						['name' => 'Add Users','guard_name' => 'admin', 'module'=>'Users'],
						['name' => 'View News','guard_name' => 'admin', 'module'=>'News'],
						['name' => 'Add News','guard_name' => 'admin', 'module'=>'News'],
						['name' => 'Edit News','guard_name' => 'admin', 'module'=>'News'],
						['name' => 'Delete News','guard_name' => 'admin', 'module'=>'News'],
						['name' => 'View Games','guard_name' => 'admin', 'module'=>'Manage Games / Games'],
						['name' => 'Add Games','guard_name' => 'admin', 'module'=>'Manage Games / Games'],
						['name' => 'Edit Games','guard_name' => 'admin', 'module'=>'Manage Games / Games'],
						['name' => 'View Game Variations','guard_name' => 'admin', 'module'=>'Manage Games / Game Variations'],
						['name' => 'Add Game Variations','guard_name' => 'admin', 'module'=>'Manage Games / Game Variations'],
						['name' => 'Edit Game Variations','guard_name' => 'admin', 'module'=>'Manage Games / Game Variations'],
						['name' => 'Delete Game Variations','guard_name' => 'admin', 'module'=>'Manage Games / Game Variations'],
						['name' => 'View Treasure Locations','guard_name' => 'admin', 'module'=>'Manage Hunts / Treasure Locations'],
						['name' => 'Add Treasure Locations','guard_name' => 'admin', 'module'=>'Manage Hunts / Treasure Locations'],
						['name' => 'Edit Treasure Locations','guard_name' => 'admin', 'module'=>'Manage Hunts / Treasure Locations'],
						['name' => 'Delete Treasure Locations','guard_name' => 'admin', 'module'=>'Manage Hunts / Treasure Locations'],
						['name' => 'View Complexity Targets','guard_name' => 'admin', 'module'=>'Manage Hunts / Games Targets'],
						['name' => 'Edit Complexity Targets','guard_name' => 'admin', 'module'=>'Manage Hunts / Games Targets'],
						['name' => 'View Avatars','guard_name' => 'admin', 'module'=>'Avatars'],
						['name' => 'Edit Avatars','guard_name' => 'admin', 'module'=>'Avatars'],
						['name' => 'Add Practice Games','guard_name' => 'admin', 'module'=>'Manage Games / Practice Games Targets'],
						['name' => 'Edit Practice Games','guard_name' => 'admin', 'module'=>'Manage Games / Practice Games Targets'],
						['name' => 'View Event','guard_name' => 'admin', 'module'=>'Events / Create Event'],
						['name' => 'Add Event','guard_name' => 'admin', 'module'=>'Events / Create Event'],
						['name' => 'Edit Event','guard_name' => 'admin', 'module'=>'Events / Create Event'],
						['name' => 'Delete Event','guard_name' => 'admin', 'module'=>'Events / Create Event'],
						['name' => 'View Event Participated','guard_name' => 'admin', 'module'=>'Events / Event Participated'],
						['name' => 'View Payments','guard_name' => 'admin', 'module'=>'Payments'],
						['name' => 'View Discount Coupons','guard_name' => 'admin', 'module'=>'Discount Coupons'],
						['name' => 'Add Discount Coupons','guard_name' => 'admin', 'module'=>'Discount Coupons'],
						['name' => 'Edit Discount Coupons','guard_name' => 'admin', 'module'=>'Discount Coupons'],
						['name' => 'Delete Discount Coupons','guard_name' => 'admin', 'module'=>'Discount Coupons'],
						['name' => 'View Analytics','guard_name' => 'admin', 'module'=>'Analytics'],
						['name' => 'View Hunt Loot Tables','guard_name' => 'admin', 'module'=>'Manage Hunts / Hunt Loot Tables'],
						['name' => 'Edit Hunt Loot Tables','guard_name' => 'admin', 'module'=>'Manage Hunts / Hunt Loot Tables'],
						//['name' => 'View Seasonal Hunt','guard_name' => 'admin', 'module'=>'Seasonal Hunt'],
					//	['name' => 'Add Seasonal Hunt','guard_name' => 'admin', 'module'=>'Seasonal Hunt'],
						//['name' => 'Edit Seasonal Hunt','guard_name' => 'admin', 'module'=>'Seasonal Hunt'],
						//['name' => 'Delete Seasonal Hunt','guard_name' => 'admin', 'module'=>'Seasonal Hunt'],
						['name' => 'View App Settings','guard_name' => 'admin', 'module'=>'App Settings'],
						['name' => 'View Relics','guard_name' => 'admin', 'module'=>'Manage Relics'],
						['name' => 'Add Relics','guard_name' => 'admin', 'module'=>'Manage Relics'],
						['name' => 'Edit Relics','guard_name' => 'admin', 'module'=>'Manage Relics'],
						['name' => 'Delete Relics','guard_name' => 'admin', 'module'=>'Manage Relics'],
						['name' => 'View Agent Levels','guard_name' => 'admin', 'module'=>'Manage Levels / Agent Levels'],
						['name' => 'Add Agent Levels','guard_name' => 'admin', 'module'=>'Manage Levels / Agent Levels'],
						['name' => 'Edit Agent Levels','guard_name' => 'admin', 'module'=>'Manage Levels / Agent Levels'],
						['name' => 'Delete Agent Levels','guard_name' => 'admin', 'module'=>'Manage Levels / Agent Levels'],
						['name' => 'View Hunt / Agent Levels','guard_name' => 'admin', 'module'=>'Manage Levels / Hunt / Agent Levels'],
						// ['name' => 'Add Hunt / Agent Levels','guard_name' => 'admin', 'module'=>'Manage Levels / Hunt / Agent Levels'],
						['name' => 'Edit Hunt / Agent Levels','guard_name' => 'admin', 'module'=>'Manage Levels / Hunt / Agent Levels'],
						['name' => 'Delete Hunt / Agent Levels','guard_name' => 'admin', 'module'=>'Manage Levels / Hunt / Agent Levels'],
						['name' => 'View Minigames / Agent Levels','guard_name' => 'admin', 'module'=>'Manage Levels / Minigames / Agent Levels'],
						// ['name' => 'Add Minigames / Agent Levels','guard_name' => 'admin', 'module'=>'Manage Levels / Minigames / Agent Levels'],
						['name' => 'Edit Minigames / Agent Levels','guard_name' => 'admin', 'module'=>'Manage Levels / Minigames / Agent Levels'],
						['name' => 'Delete Minigames / Agent Levels','guard_name' => 'admin', 'module'=>'Manage Levels / Minigames / Agent Levels'],
						['name' => 'View Avatar / Agent Levels','guard_name' => 'admin', 'module'=>'Manage Levels / Avatar / Agent Levels'],
						['name' => 'Add Avatar / Agent Levels','guard_name' => 'admin', 'module'=>'Manage Levels / Avatar / Agent Levels'],
						['name' => 'Edit Avatar / Agent Levels','guard_name' => 'admin', 'module'=>'Manage Levels / Avatar / Agent Levels'],
						['name' => 'Delete Avatar / Agent Levels','guard_name' => 'admin', 'module'=>'Manage Levels / Avatar / Agent Levels'],
						['name' => 'View Bucket Size / Agent Levels','guard_name' => 'admin', 'module'=>'Manage Levels / Bucket Size / Agent Levels'],
						['name' => 'Add Bucket Size / Agent Levels','guard_name' => 'admin', 'module'=>'Manage Levels / Bucket Size / Agent Levels'],
						['name' => 'Edit Bucket Size / Agent Levels','guard_name' => 'admin', 'module'=>'Manage Levels / Bucket Size / Agent Levels'],
						['name' => 'Delete Bucket Size / Agent Levels','guard_name' => 'admin', 'module'=>'Manage Levels / Bucket Size / Agent Levels'],
						['name' => 'View Hunts XP','guard_name' => 'admin', 'module'=>'Manage Hunts / Hunts XP'],
						['name' => 'Edit Hunts XP','guard_name' => 'admin', 'module'=>'Manage Hunts / Hunts XP'],

					];
		$admin = Admin::where('email','support@ironbridge1779.com')->first();

		$allPermission = Permission::all()->pluck('module')->toArray();
		$newPermission = [];
		foreach ($permission as $key => $value) {
			if (!in_array($value['module'], $allPermission)) {
				$permissionId =Permission::updateOrCreate(
											['name' => $value['name'],
											'guard_name' =>$value['guard_name'],
											'module'=>$value['module'],
											],[
											'name' => $value['name'],
											'guard_name' =>$value['guard_name'],
											'module'=>$value['module'],
											'admin_ids'=>[$admin->id]
										]);

				$newPermission[] = $permissionId->id;
			}
		}
		if (count($newPermission)) {
			$admin->push('permission_ids',$newPermission,true);
		}
		// Permission::create([]);
		return response()->json(['message'=>'Permission updated successfully.']);

	}
}
