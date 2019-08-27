<?php

namespace App\Http\Controllers\Api\v2;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Maklad\Permission\Models\Role;
use Maklad\Permission\Models\Permission;


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


		Permission::create(['name' => 'View Event','guard_name' => 'admin', 'module'=>'Events']);
		Permission::create(['name' => 'Add Event','guard_name' => 'admin', 'module'=>'Events']);
		Permission::create(['name' => 'Edit Event','guard_name' => 'admin', 'module'=>'Events']);
		Permission::create(['name' => 'Delete Event','guard_name' => 'admin', 'module'=>'Events']);

		Permission::create(['name' => 'View Payments','guard_name' => 'admin', 'module'=>'Payments']);

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
}
