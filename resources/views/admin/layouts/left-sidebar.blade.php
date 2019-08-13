<?php $admin = \Auth::user(); ?>
<div class="left_paretboxpart">
	<!-- <div class="innercategories"></div> -->
	<div class="nav-offcanvas">
		<button type="button" class="close closebtnboxiner" id="offCanvasClose" aria-label="Close">
			<span>×</span>
		</button>
		<div class="nav-offcanvas-menu">
			<ul>

				@if(Route::currentRouteName() == 'admin.dashboards' || Route::currentRouteName() == 'admin.userList' || Route::currentRouteName() == 'admin.news.index' || Route::currentRouteName() == 'admin.game.index' || Route::currentRouteName() == 'admin.gameVariation.index' || Route::currentRouteName() == 'admin.gameVariation.create' || Route::currentRouteName() == 'admin.gameVariation.show' || Route::currentRouteName() == 'admin.usersParticipatedList' || Route::currentRouteName() == 'admin.complexityTarget.index' || Route::currentRouteName() == 'admin.mapsList' ||Route::currentRouteName() == 'admin.add_location' || Route::currentRouteName() == 'admin.boundary_map' || Route::currentRouteName() == 'admin.starComplexityMap' || Route::currentRouteName() == 'admin.edit_location' || Route::currentRouteName() == 'admin.test_location' || Route::currentRouteName() == 'admin.avatar.index' || Route::currentRouteName() == 'admin.avatarDetails' || Route::currentRouteName() == 'admin.adminManagement.index' || Route::currentRouteName() == 'admin.setPassword' || Route::currentRouteName() == 'admin.practiceGame' || Route::currentRouteName() == 'admin.event.index' || Route::currentRouteName() == 'admin.event.create' || Route::currentRouteName() == 'admin.event.miniGame' || Route::currentRouteName() == 'admin.event.basicDetails' || Route::currentRouteName() == 'admin.event.show')
				@if($admin->hasPermissionTo('Dashboard'))
				<li  class="@if(Route::currentRouteName() == 'admin.dashboards') {{ 'activelist' }} @endif">
					<a href="{{ route('admin.dashboards') }}">Dashboard</a>
				</li>
				@endif
				@if($admin->hasPermissionTo('View Users'))
				<li  class="@if(Route::currentRouteName() == 'admin.userList') {{ 'activelist' }} @endif">
					<a href="{{ route('admin.userList') }}">Users</a>
				</li>
				@endif
				@if($admin->hasPermissionTo('View News'))
				<li  class="@if(Route::currentRouteName() == 'admin.news.index') {{ 'activelist' }} @endif">
					<a href="{{ route('admin.news.index') }}">News</a>
				</li>
				@endif
				@if($admin->hasPermissionTo('View Games') || $admin->hasPermissionTo('View Game Variations') || $admin->hasPermissionTo('Add Practice Games'))
					<!-- <li  class="@if(Route::currentRouteName() == 'admin.game.index') {{ 'activelist' }} @endif">
						<a href="{{ route('admin.game.index') }}">Games</a>
					</li> -->
					<!-- <li  class="@if(Route::currentRouteName() == 'admin.gameVariation.index' || Route::currentRouteName() == 'admin.gameVariation.create' || Route::currentRouteName() == 'admin.gameVariation.show') {{ 'activelist' }} @endif">
						<a href="{{ route('admin.gameVariation.index') }}">Game Variations</a>
					</li> -->
					<li >
						<a href="javascript:void(0)" class="plusbttnbox myBtn">Manage Games <i class="fa @if(Route::currentRouteName() == 'admin.game.index' || Route::currentRouteName() == 'admin.gameVariation.index' || Route::currentRouteName() == 'admin.gameVariation.create' || Route::currentRouteName() == 'admin.gameVariation.show' || Route::currentRouteName() == 'admin.practiceGame') {{ 'fa-minus' }} @else {{ 'fa-plus' }} @endif" aria-hidden="true"></i></a>
						<div class="dropdown custmenbox">
							<!-- <button id="myBtn" class="dropbtn">Dropdown</button> -->
							<div  class="dropdown-content myDropdown @if(Route::currentRouteName() == 'admin.game.index' || Route::currentRouteName() == 'admin.gameVariation.index' || Route::currentRouteName() == 'admin.gameVariation.create' || Route::currentRouteName() == 'admin.gameVariation.show' || Route::currentRouteName() == 'admin.practiceGame') {{ 'show' }} @endif">
								@if($admin->hasPermissionTo('View Games'))
								<a href="{{ route('admin.game.index') }}" class="@if(Route::currentRouteName() == 'admin.game.index') {{ 'activelistsub' }} @endif" >Games</a>
								@endif
								@if($admin->hasPermissionTo('View Game Variations'))
								<a href="{{ route('admin.gameVariation.index') }}" class="@if(Route::currentRouteName() == 'admin.gameVariation.index' || Route::currentRouteName() == 'admin.gameVariation.create' || Route::currentRouteName() == 'admin.gameVariation.show') {{ 'activelistsub' }} @endif">Game Variations</a>
								@endif

								@if($admin->hasPermissionTo('Add Practice Games'))
									<a href="{{ route('admin.practiceGame') }}" class="@if(Route::currentRouteName() == 'admin.practiceGame') {{ 'activelistsub' }} @endif" >Practice Games Targets</a>
								@endif

							</div>
						</div>
					</li>
					@endif
					@if($admin->hasPermissionTo('View Treasure Locations') || $admin->hasPermissionTo('View Complexity Targets'))
					<li >
						<a href="javascript:void(0)" class="plusbttnbox myBtn" >Manage Hunts <i class="fa @if(Route::currentRouteName() == 'admin.mapsList' ||Route::currentRouteName() == 'admin.add_location' || Route::currentRouteName() == 'admin.boundary_map' || Route::currentRouteName() == 'admin.starComplexityMap' || Route::currentRouteName() == 'admin.edit_location' || Route::currentRouteName() == 'admin.complexityTarget.index' ) {{ 'fa-minus' }} @else {{ 'fa-plus' }} @endif" aria-hidden="true"></i></a>
						<div class="dropdown custmenbox">
							<!-- <button id="myBtn" class="dropbtn">Dropdown</button> -->
							<div  class="dropdown-content myDropdown @if(Route::currentRouteName() == 'admin.mapsList' ||Route::currentRouteName() == 'admin.add_location' || Route::currentRouteName() == 'admin.boundary_map' || Route::currentRouteName() == 'admin.starComplexityMap' || Route::currentRouteName() == 'admin.edit_location' || Route::currentRouteName() == 'admin.complexityTarget.index' ) {{ 'show' }} @endif">
								@if($admin->hasPermissionTo('View Treasure Locations'))
								<a href="{{ route('admin.mapsList') }}" class="@if(Route::currentRouteName() == 'admin.mapsList' ||Route::currentRouteName() == 'admin.add_location' || Route::currentRouteName() == 'admin.boundary_map' || Route::currentRouteName() == 'admin.starComplexityMap' || Route::currentRouteName() == 'admin.edit_location') {{ 'activelistsub' }} @endif">Treasure Locations</a>
								@endif
								@if($admin->hasPermissionTo('View Complexity Targets'))
								<a href="{{ route('admin.complexityTarget.index') }}" class="@if(Route::currentRouteName() == 'admin.complexityTarget.index') {{ 'activelistsub' }} @endif">Games Targets</a>
								@endif
							</div>
						</div>
					</li>
					@endif
					@if($admin->hasPermissionTo('View Avatars'))
					<li  class="@if(Route::currentRouteName() == 'admin.avatar.index' || Route::currentRouteName() == 'admin.avatarDetails'	) {{ 'activelist' }} @endif">
						<a href="{{ route('admin.avatar.index') }}">Avatars</a>
					</li>
					@endif
					
					<!-- <li  class="@if(Route::currentRouteName() == 'admin.event.index') {{ 'activelist' }} @endif">
						<a href="{{ route('admin.event.index') }}">Events</a>
					</li> -->
					
					@if($admin->hasRole('Super Admin'))
					<li  class="@if(Route::currentRouteName() == 'admin.adminManagement.index') {{ 'activelist' }} @endif">
						<a href="{{ route('admin.adminManagement.index') }}">User Access</a>
					</li>
					@endif
					
					@else
					<li  class="@if(Route::currentRouteName() == 'admin.accountInfo') {{ 'activelist' }} @endif">
						<a href="{{ route('admin.accountInfo',$id) }}">Account Info</a>
					</li>
					<li  class="@if(Route::currentRouteName() == 'admin.treasureHunts' || Route::currentRouteName() == 'admin.userHuntDetails') {{ 'activelist' }} @endif">
						<a href="{{ route('admin.treasureHunts',$id) }}">Treasure Hunts</a>
					</li>
					<li  class="@if(Route::currentRouteName() == 'admin.activity') {{ 'activelist' }} @endif">
						<a href="{{ route('admin.activity',$id) }}">Activity</a>
					</li>
					<li  class="@if(Route::currentRouteName() == 'admin.practiceGameUser') {{ 'activelist' }} @endif">
						<a href="{{ route('admin.practiceGameUser',$id) }}">Practice Game Users</a>
					</li>
					@endif         
				</ul>
			</div>
		</div>
	</div>