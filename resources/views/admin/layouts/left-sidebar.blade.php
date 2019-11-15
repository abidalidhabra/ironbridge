<?php $admin = \Auth::user(); ?>
<div class="left_paretboxpart">
	<!-- <div class="innercategories"></div> -->
	<div class="nav-offcanvas">
		<button type="button" class="close closebtnboxiner" id="offCanvasClose" aria-label="Close">
			<span>×</span>
		</button>
		<div class="nav-offcanvas-menu">
			<ul>

				@if(Route::currentRouteName() == 'admin.accountInfo' || Route::currentRouteName() == 'admin.treasureHunts' || Route::currentRouteName() == 'admin.userHuntDetails' || Route::currentRouteName() == 'admin.activity' || Route::currentRouteName() == 'admin.eventsUser' || Route::currentRouteName() == 'admin.practiceGameUser')

					<li  class="@if(Route::currentRouteName() == 'admin.accountInfo') {{ 'activelist' }} @endif">
						<a href="{{ route('admin.accountInfo',$id) }}">Account Info</a>
					</li>
					<li  class="@if(Route::currentRouteName() == 'admin.treasureHunts' || Route::currentRouteName() == 'admin.userHuntDetails') {{ 'activelist' }} @endif">
						<a href="{{ route('admin.treasureHunts',$id) }}">Treasure Hunts</a>
					</li>
					<li  class="@if(Route::currentRouteName() == 'admin.activity') {{ 'activelist' }} @endif">
						<a href="{{ route('admin.activity',$id) }}">Activity</a>
					</li>
					<li  class="@if(Route::currentRouteName() == 'admin.eventsUser') {{ 'activelist' }} @endif">
						<a href="{{ route('admin.eventsUser',$id) }}">Events</a>
					</li>
					<li  class="@if(Route::currentRouteName() == 'admin.practiceGameUser') {{ 'activelist' }} @endif">
						<a href="{{ route('admin.practiceGameUser',$id) }}">Practice Game Users</a>
					</li>
				@else
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
						<li >
							<a href="javascript:void(0)" class="plusbttnbox myBtn">Manage Minigames <i class="fa @if(Route::currentRouteName() == 'admin.game.index' || Route::currentRouteName() == 'admin.gameVariation.index' || Route::currentRouteName() == 'admin.gameVariation.create' || Route::currentRouteName() == 'admin.gameVariation.show' || Route::currentRouteName() == 'admin.practiceGame.index' || Route::currentRouteName() == 'admin.practiceGame.edit' || Route::currentRouteName() == 'admin.practiceGame.show') {{ 'fa-minus' }} @else {{ 'fa-plus' }} @endif" aria-hidden="true"></i></a>
							<div class="dropdown custmenbox">
								<!-- <button id="myBtn" class="dropbtn">Dropdown</button> -->
								<div  class="dropdown-content myDropdown @if(Route::currentRouteName() == 'admin.game.index' || Route::currentRouteName() == 'admin.gameVariation.index' || Route::currentRouteName() == 'admin.gameVariation.create' || Route::currentRouteName() == 'admin.gameVariation.show' || Route::currentRouteName() == 'admin.practiceGame.index' || Route::currentRouteName() == 'admin.practiceGame.edit' || Route::currentRouteName() == 'admin.practiceGame.show') {{ 'show' }} @endif">
									@if($admin->hasPermissionTo('View Games'))
									<a href="{{ route('admin.game.index') }}" class="@if(Route::currentRouteName() == 'admin.game.index') {{ 'activelistsub' }} @endif" >Games</a>
									@endif
									@if($admin->hasPermissionTo('View Game Variations'))
									<a href="{{ route('admin.gameVariation.index') }}" class="@if(Route::currentRouteName() == 'admin.gameVariation.index' || Route::currentRouteName() == 'admin.gameVariation.create' || Route::currentRouteName() == 'admin.gameVariation.show') {{ 'activelistsub' }} @endif">Game Variations</a>
									@endif

									@if($admin->hasPermissionTo('Add Practice Games'))
										<a href="{{ route('admin.practiceGame.index') }}" class="@if(Route::currentRouteName() == 'admin.practiceGame.index' || Route::currentRouteName() == 'admin.practiceGame.edit' || Route::currentRouteName() == 'admin.practiceGame.show') {{ 'activelistsub' }} @endif" >Mini Games XP/Score</a>
									@endif

								</div>
							</div>
						</li>
					@endif
					@if($admin->hasPermissionTo('View Treasure Locations') || $admin->hasPermissionTo('View Complexity Targets') || $admin->hasPermissionTo('View Hunt Loot Tables'))
					<li >
						<a href="javascript:void(0)" class="plusbttnbox myBtn" >Manage Hunts <i class="fa @if(Route::currentRouteName() == 'admin.mapsList' ||Route::currentRouteName() == 'admin.add_location' || Route::currentRouteName() == 'admin.boundary_map' || Route::currentRouteName() == 'admin.starComplexityMap' || Route::currentRouteName() == 'admin.edit_location' || Route::currentRouteName() == 'admin.complexityTarget.index' || Route::currentRouteName() == 'admin.rewards.index' || Route::currentRouteName() == 'admin.xpManagement.index' || Route::currentRouteName() == 'admin.xpManagement.edit') {{ 'fa-minus' }} @else {{ 'fa-plus' }} @endif" aria-hidden="true"></i></a>
						<div class="dropdown custmenbox">
							<!-- <button id="myBtn" class="dropbtn">Dropdown</button> -->
							<div  
								class="dropdown-content myDropdown 
								@if(
								Route::currentRouteName() == 'admin.mapsList' ||
								Route::currentRouteName() == 'admin.add_location' || 
								Route::currentRouteName() == 'admin.boundary_map' || 
								Route::currentRouteName() == 'admin.starComplexityMap' || 
								Route::currentRouteName() == 'admin.edit_location' || 
								Route::currentRouteName() == 'admin.complexityTarget.index' || 
								Route::currentRouteName() == 'admin.rewards.index' || Route::currentRouteName() == 'admin.xpManagement.index' || Route::currentRouteName() == 'admin.xpManagement.edit') {{ 'show' }} @endif">
								@if($admin->hasPermissionTo('View Treasure Locations'))
								<a href="{{ route('admin.mapsList') }}" class="@if(Route::currentRouteName() == 'admin.mapsList' ||Route::currentRouteName() == 'admin.add_location' || Route::currentRouteName() == 'admin.boundary_map' || Route::currentRouteName() == 'admin.starComplexityMap' || Route::currentRouteName() == 'admin.edit_location') {{ 'activelistsub' }} @endif">Treasure Locations</a>
								@endif
								@if($admin->hasPermissionTo('View Complexity Targets'))
								<a href="{{ route('admin.complexityTarget.index') }}" class="@if(Route::currentRouteName() == 'admin.complexityTarget.index') {{ 'activelistsub' }} @endif">Games Targets</a>
								@endif
								@if($admin->hasPermissionTo('View Hunt Loot Tables'))
									<a href="{{ route('admin.rewards.index') }}" class="@if(Route::currentRouteName() == 'admin.rewards.index') {{ 'activelistsub' }} @endif">Hunt Loot Tables</a>
								@endif
									<a href="{{ route('admin.xpManagement.index') }}" class="@if(Route::currentRouteName() == 'admin.xpManagement.index' || Route::currentRouteName() == 'admin.xpManagement.edit') {{ 'activelistsub' }} @endif">Hunts XP</a>
							</div>
						</div>
					</li>
					@endif
					








					@if($admin->hasPermissionTo('View Treasure Locations') || $admin->hasPermissionTo('View Complexity Targets') || $admin->hasPermissionTo('View Hunt Loot Tables'))
					<!-- <li>
						<a href="javascript:void(0)" class="plusbttnbox myBtn">Seasonal Hunts
							<i 
								class="fa 
								@if(
									Route::currentRouteName() == 'admin.seasons.index' ||
									Route::currentRouteName() == 'admin.seasons.create' || 
									Route::currentRouteName() == 'admin.seasons.edit' || 
									Route::currentRouteName() == 'admin.seasons.show' || 
									Route::currentRouteName() == 'admin.relics.index' || 
									Route::currentRouteName() == 'admin.relics.create' || 
									Route::currentRouteName() == 'admin.relics.edit' || 
									Route::currentRouteName() == 'admin.relics.show') 
									{{ 'fa-minus' }} 
								@else 
									{{ 'fa-plus' }} 
								@endif" 
								aria-hidden="true">
							</i>
						</a>

						<div class="dropdown custmenbox">
							<div  
							class="dropdown-content myDropdown 
							@if(
								Route::currentRouteName() == 'admin.seasons.index' ||
								Route::currentRouteName() == 'admin.seasons.create' || 
								Route::currentRouteName() == 'admin.seasons.edit' || 
								Route::currentRouteName() == 'admin.seasons.show' || 
								Route::currentRouteName() == 'admin.relics.index' || 
								Route::currentRouteName() == 'admin.relics.create' || 
								Route::currentRouteName() == 'admin.relics.edit' || 
								Route::currentRouteName() == 'admin.relics.show') 
								{{ 'show' }} 
							@endif">
								
								@if($admin->hasPermissionTo('View Treasure Locations'))
									<a 
									href="{{ route('admin.seasons.index') }}" 
									class="
									@if(
										Route::currentRouteName() == 'admin.seasons.index' ||
										Route::currentRouteName() == 'admin.seasons.create' || 
										Route::currentRouteName() == 'admin.seasons.edit' || 
										Route::currentRouteName() == 'admin.seasons.show') 
										{{ 'activelistsub' }} 
									@endif
									">Seasons</a>
								@endif

								@if($admin->hasPermissionTo('View Treasure Locations'))
									<a 
									href="{{ route('admin.relics.index') }}" 
									class="
									@if(
										Route::currentRouteName() == 'admin.relics.index' ||
										Route::currentRouteName() == 'admin.relics.create' || 
										Route::currentRouteName() == 'admin.relics.edit' || 
										Route::currentRouteName() == 'admin.relics.show') 
										{{ 'activelistsub' }} 
									@endif
									">Relics</a>
								@endif
							</div>
						</div>
					</li> -->
					@endif
					@if($admin->hasPermissionTo('View Relics') || $admin->hasPermissionTo('View Agent Levels'))
					<li>
						<a href="javascript:void(0)" class="plusbttnbox myBtn">Manage Relics
							<i 
								class="fa 
								@if(Route::currentRouteName() == 'admin.relics.index' || 
									Route::currentRouteName() == 'admin.relics.create' || 
									Route::currentRouteName() == 'admin.relics.edit' || 
									Route::currentRouteName() == 'admin.relics.show' ||
									Route::currentRouteName() == 'admin.relicReward.index' ||
									Route::currentRouteName() == 'admin.relicReward.create' || 
									Route::currentRouteName() == 'admin.relicReward.edit' || 
									Route::currentRouteName() == 'admin.relicReward.show') 
									{{ 'fa-minus' }} 
								@else 
									{{ 'fa-plus' }} 
								@endif" 
								aria-hidden="true">
							</i>
						</a>

						<div class="dropdown custmenbox">
							<div  
							class="dropdown-content myDropdown 
							@if(Route::currentRouteName() == 'admin.relics.index' || 
								Route::currentRouteName() == 'admin.relics.create' || 
								Route::currentRouteName() == 'admin.relics.edit' || 
								Route::currentRouteName() == 'admin.relics.show' ||
								Route::currentRouteName() == 'admin.relicReward.index' ||
								Route::currentRouteName() == 'admin.relicReward.create' || 
								Route::currentRouteName() == 'admin.relicReward.edit' || 
								Route::currentRouteName() == 'admin.relicReward.show') 
								{{ 'show' }} 
							@endif">
									@if($admin->hasPermissionTo('View Relics'))
									<a 
									href="{{ route('admin.relics.index') }}" 
									class="
									@if(
										Route::currentRouteName() == 'admin.relics.index' ||
										Route::currentRouteName() == 'admin.relics.create' || 
										Route::currentRouteName() == 'admin.relics.edit' || 
										Route::currentRouteName() == 'admin.relics.show') 
										{{ 'activelistsub' }} 
									@endif
									">Relics</a>
									@endif
									
									@if($admin->hasPermissionTo('View Agent Levels'))
									<a 
									href="{{ route('admin.relicReward.index') }}" 
									class="
									@if(
										Route::currentRouteName() == 'admin.relicReward.index' ||
										Route::currentRouteName() == 'admin.relicReward.create' || 
										Route::currentRouteName() == 'admin.relicReward.edit' || 
										Route::currentRouteName() == 'admin.relicReward.show') 
										{{ 'activelistsub' }} 
									@endif
									">Agent Levels</a>
									@endif
							</div>
						</div>
					</li>
					@endif

					<li class="@if(Route::currentRouteName() == 'admin.agent-levels.index') {{ 'activelist' }} @endif">
						<a href="{{ route('admin.agent-levels.index') }}">Agent Level</a>
					</li>
					<li class="@if(Route::currentRouteName() == 'admin.hunts-agent-levels.index') {{ 'activelist' }} @endif">
						<a href="{{ route('admin.hunts-agent-levels.index') }}">Hunt / Agent Level</a>
					</li>
					<li class="@if(Route::currentRouteName() == 'admin.minigames-agent-levels.index') {{ 'activelist' }} @endif">
						<a href="{{ route('admin.minigames-agent-levels.index') }}">Minigames / Agent Level</a>
					</li>
					<li class="@if(Route::currentRouteName() == 'admin.avatar-agent-levels.index' || Route::currentRouteName() == 'admin.avatar-agent-levels.create' || Route::currentRouteName() == 'admin.avatar-agent-levels.edit' || Route::currentRouteName() == 'admin.avatar-agent-levels.show') {{ 'activelist' }} @endif">
						<a href="{{ route('admin.avatar-agent-levels.index') }}">Avatar / Agent Level</a>
					</li>
					<li class="@if(Route::currentRouteName() == 'admin.bucket-sizes.index') {{ 'activelist' }} @endif">
						<a href="{{ route('admin.bucket-sizes.index') }}">Bucket Size / Agent Level</a>
					</li>
					
					{{-- @if($admin->hasPermissionTo('View Treasure Locations') || $admin->hasPermissionTo('View Complexity Targets') || $admin->hasPermissionTo('View Hunt Loot Tables')) --}}
					{{--<li >
						<a 
							href="javascript:void(0)" 
							class="plusbttnbox myBtn" >Relics Maps
							<i class="fa 
								@if(Route::currentRouteName() == 'admin.mapsList' || 
									Route::currentRouteName() == 'admin.add_location' || 
									Route::currentRouteName() == 'admin.boundary_map' || 
									Route::currentRouteName() == 'admin.starComplexityMap' || 
									Route::currentRouteName() == 'admin.edit_location' || 
									Route::currentRouteName() == 'admin.complexityTarget.index' || 
									Route::currentRouteName() == 'admin.rewards.index' ) {{ 'fa-minus' }} 
								@else {{ 'fa-plus' }} 
								@endif" 
								aria-hidden="true"></i></a>
						<div class="dropdown custmenbox">
							<!-- <button id="myBtn" class="dropbtn">Dropdown</button> -->
							<div  
								class="dropdown-content myDropdown 
								@if(
								Route::currentRouteName() == 'admin.mapsList' ||
								Route::currentRouteName() == 'admin.add_location' || 
								Route::currentRouteName() == 'admin.boundary_map' || 
								Route::currentRouteName() == 'admin.starComplexityMap' || 
								Route::currentRouteName() == 'admin.edit_location' || 
								Route::currentRouteName() == 'admin.complexityTarget.index' || 
								Route::currentRouteName() == 'admin.rewards.index' ) {{ 'show' }} 
								@endif">
								@if($admin->hasPermissionTo('View Treasure Locations'))
									<a 
									href="{{ route('admin.mapsList') }}" 
									class="@if(
										Route::currentRouteName() == 'admin.mapsList' ||
										Route::currentRouteName() == 'admin.add_location' || 
										Route::currentRouteName() == 'admin.boundary_map' || 
										Route::currentRouteName() == 'admin.starComplexityMap' || 
										Route::currentRouteName() == 'admin.edit_location') {{ 'activelistsub' }} 
										@endif">Seasons</a>
								@endif
								
								@if($admin->hasPermissionTo('View Complexity Targets'))
								<a 
								href="{{ route('admin.complexityTarget.index') }}" 
								class="@if(
									Route::currentRouteName() == 'admin.complexityTarget.index') {{ 'activelistsub' }} 
									@endif">Relics</a>
								@endif
								
								@if($admin->hasPermissionTo('View Hunt Loot Tables'))
									<a 
									href="{{ route('admin.rewards.index') }}" 
									class="@if(
										Route::currentRouteName() == 'admin.rewards.index') {{ 'activelistsub' }} 
										@endif">Hunt Loot Tables</a>
								@endif
							</div>
						</div>
					</li> --}}
					{{-- @endif --}}

					@if($admin->hasPermissionTo('View Payments'))
					<li  class="@if(Route::currentRouteName() == 'admin.payment.index'	) {{ 'activelist' }} @endif">
						<a href="{{ route('admin.payment.index') }}">Payments</a>
					</li>
					@endif
					@if($admin->hasPermissionTo('View Avatars'))
					<li  class="@if(Route::currentRouteName() == 'admin.avatar.index' || Route::currentRouteName() == 'admin.avatarDetails'	) {{ 'activelist' }} @endif">
						<a href="{{ route('admin.avatar.index') }}">Avatars</a>
					</li>
					@endif
					
					@if($admin->hasPermissionTo('View Event') || $admin->hasPermissionTo('View Event Participated'))
					<li>
						<a href="javascript:void(0)" class="plusbttnbox myBtn" >Events <i class="fa @if(Route::currentRouteName() == 'admin.eventparticipated.index' || Route::currentRouteName() == 'admin.event.index' || Route::currentRouteName() == 'admin.event.miniGame' || Route::currentRouteName() == 'admin.event.basicDetails' || Route::currentRouteName() == 'admin.event.show' || Route::currentRouteName() == 'admin.event.huntDetails') {{ 'fa-minus' }} @else {{ 'fa-plus' }} @endif" aria-hidden="true"></i></a>
						<div class="dropdown custmenbox">
							<!-- <button id="myBtn" class="dropbtn">Dropdown</button> -->
							<div  class="dropdown-content myDropdown @if(Route::currentRouteName() == 'admin.eventparticipated.index' || Route::currentRouteName() == 'admin.event.index' || Route::currentRouteName() == 'admin.event.miniGame' || Route::currentRouteName() == 'admin.event.basicDetails' || Route::currentRouteName() == 'admin.event.show' || Route::currentRouteName() == 'admin.event.huntDetails') {{ 'show' }} @endif">
								@if($admin->hasPermissionTo('View Event'))
								<a href="{{ route('admin.event.index') }}" class="@if(Route::currentRouteName() == 'admin.event.index' || Route::currentRouteName() == 'admin.event.miniGame' || Route::currentRouteName() == 'admin.event.basicDetails' || Route::currentRouteName() == 'admin.event.show' || Route::currentRouteName() == 'admin.event.huntDetails') {{ 'activelistsub' }} @endif">Create Event</a>
								@endif
								@if($admin->hasPermissionTo('View Event Participated'))
								<a href="{{ route('admin.eventparticipated.index') }}" class="@if(Route::currentRouteName() == 'admin.eventparticipated.index') {{ 'activelistsub' }} @endif">Event Participated</a>
								@endif
							</div>
						</div>
					</li>
					@endif

					@if($admin->hasPermissionTo('View Discount Coupons'))
					<li  class="@if(Route::currentRouteName() == 'admin.discounts.index') {{ 'activelist' }} @endif">
						<a href="{{ route('admin.discounts.index') }}">Discount Coupons</a>
					</li>
					@endif

					<!-- <li  class="@if(Route::currentRouteName() == 'admin.event.index' || Route::currentRouteName() == 'admin.event.miniGame' || Route::currentRouteName() == 'admin.event.basicDetails' || Route::currentRouteName() == 'admin.event.show' || Route::currentRouteName() == 'admin.event.huntDetails') {{ 'activelist' }} @endif">
						<a href="{{ route('admin.event.index') }}">Events</a>
					</li> -->
					
					@if($admin->hasPermissionTo('View Analytics'))
					<li  class="@if(Route::currentRouteName() == 'admin.analyticsMetrics') {{ 'activelist' }} @endif">
						<a href="{{ route('admin.analyticsMetrics') }}">Analytics</a>
					</li>
					@endif


					@if($admin->hasRole('Super Admin'))
					<li  class="@if(Route::currentRouteName() == 'admin.adminManagement.index') {{ 'activelist' }} @endif">
						<a href="{{ route('admin.adminManagement.index') }}">User Access</a>
					</li>
					@endif

					@if($admin->hasPermissionTo('View App Settings'))
					<li  class="@if(Route::currentRouteName() == 'admin.app.settings.index'	) {{ 'activelist' }} @endif">
						<a href="{{ route('admin.app.settings.index') }}">App Settings</a>
					</li>
					@endif
					
				@endif         
				</ul>
			</div>
		</div>
	</div>