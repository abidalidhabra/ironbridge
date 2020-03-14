<?php $admin = \Auth::user(); ?>
<div class="left_paretboxpart">

	<div class="nav-offcanvas">
		<button type="button" class="close closebtnboxiner" id="offCanvasClose" aria-label="Close">
			<span>×</span>
		</button>
		<div class="nav-offcanvas-menu">
			<ul>

				@if(Route::currentRouteName() == 'admin.accountInfo' || Route::currentRouteName() == 'admin.treasureHunts' || Route::currentRouteName() == 'admin.userHuntDetails' || Route::currentRouteName() == 'admin.activity' || Route::currentRouteName() == 'admin.practiceGameUser' || Route::currentRouteName() == 'admin.user.avatarItems' || Route::currentRouteName() == 'admin.user.planPurchase' || Route::currentRouteName() == 'admin.miniGameStatistics' || Route::currentRouteName() == 'admin.tutorialsProgress' || Route::currentRouteName() == 'admin.chestInverntory')

					<li  class="@if(Route::currentRouteName() == 'admin.accountInfo') {{ 'activelist' }} @endif">
						<a href="{{ route('admin.accountInfo',$id) }}">Account Info</a>
					</li>
					<li  class="@if(Route::currentRouteName() == 'admin.treasureHunts' || Route::currentRouteName() == 'admin.userHuntDetails') {{ 'activelist' }} @endif">
						<a href="{{ route('admin.treasureHunts',$id) }}">Treasure Hunts</a>
					</li>
					<li  class="@if(Route::currentRouteName() == 'admin.activity') {{ 'activelist' }} @endif">
						<a href="{{ route('admin.activity',$id) }}">Activity</a>
					</li>
					<li  class="@if(Route::currentRouteName() == 'admin.tutorialsProgress') {{ 'activelist' }} @endif">
						<a href="{{ route('admin.tutorialsProgress',$id) }}">Tutorials Progress</a>
					</li>
					<li  class="@if(Route::currentRouteName() == 'admin.practiceGameUser') {{ 'activelist' }} @endif">
						<a href="{{ route('admin.practiceGameUser',$id) }}">Practice Game Users</a>
					</li>
					<li  class="@if(Route::currentRouteName() == 'admin.user.avatarItems') {{ 'activelist' }} @endif">
						<a href="{{ route('admin.user.avatarItems',$id) }}">Avatar Item</a>
					</li>
					<li  class="@if(Route::currentRouteName() == 'admin.user.planPurchase') {{ 'activelist' }} @endif">
						<a href="{{ route('admin.user.planPurchase',$id) }}">Plan Purchases</a>
					</li>
					<li  class="@if(Route::currentRouteName() == 'admin.miniGameStatistics') {{ 'activelist' }} @endif">
						<a href="{{ route('admin.miniGameStatistics',$id) }}">Mini-Games Statistics</a>
					</li>
					<li  class="@if(Route::currentRouteName() == 'admin.chestInverntory') {{ 'activelist' }} @endif">
						<a href="{{ route('admin.chestInverntory',$id) }}">Chest Inverntory</a>
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


					@php $route = Route::currentRouteName(); @endphp 
					
					@php 
						$relicRoutes = (
						$route == 'admin.relics.index' || 
						$route == 'admin.relics.create' || 
						$route == 'admin.relics.edit' || 
						$route == 'admin.relics.show'
						)? true:false;

						$hstateRoutes = ($route == 'admin.hunt_statistics.index')? true:false;

						$showIcon = ($relicRoutes || $hstateRoutes)? 'fa-minus': 'fa-plus';
					@endphp
					<li>
						
						<a href="javascript:void(0)" class="plusbttnbox myBtn">
							Hunts<i class="fa {{ $showIcon }}" aria-hidden="true"></i>
						</a>

						<div class="dropdown custmenbox">
							<div class="dropdown-content myDropdown {{ ($showIcon == 'fa-minus')? 'show': '' }}">
								@if($admin->hasPermissionTo('View Relics'))
									<a href="{{ route('admin.relics.index') }}" class="@if($relicRoutes) {{ 'activelistsub' }} @endif">Relics</a>
								@endif
								
								@if($admin->hasPermissionTo('View Hunt Statistics'))
									<a href="{{ route('admin.hunt_statistics.index') }}" class="@if($hstateRoutes) {{ 'activelistsub' }} @endif">Distance and XP</a>
								@endif
							</div>
						</div>
					</li>


					@php 
						$eventRoutes = (
						$route == 'admin.events.index' || 
						$route == 'admin.events.create' || 
						$route == 'admin.events.edit' || 
						$route == 'admin.events.show'
						)? true:false;


						$showIcon = ($eventRoutes)? 'fa-minus': 'fa-plus';
					@endphp
					<li>
						
						<a href="javascript:void(0)" class="plusbttnbox myBtn">
							Events<i class="fa {{ $showIcon }}" aria-hidden="true"></i>
						</a>

						<div class="dropdown custmenbox">
							<div class="dropdown-content myDropdown {{ ($showIcon == 'fa-minus')? 'show': '' }}">
								<a href="{{ route('admin.events.index') }}" class="@if($eventRoutes) {{ 'activelistsub' }} @endif">Events</a>
							</div>
						</div>
					</li>


					@php 
						
						$chestTargets = ($route == 'admin.complexityTarget.index')? true:false;
						
						$MGCTargets = (
						$route == 'admin.treasure_nodes_targets.index' || 
						$route == 'admin.treasure_nodes_targets.edit'
						)? true:false;
						
						$practiceTargets = (
						$route == 'admin.practiceGame.index' || 
						$route == 'admin.practiceGame.edit' || 
						$route == 'admin.practiceGame.show'
						)? true:false;
						
						$MGVariationsImages = (
						$route == 'admin.gameVariation.index' || 
						$route == 'admin.gameVariation.create' || 
						$route == 'admin.gameVariation.show'
						)? true:false;

						$showIcon = ($chestTargets || $MGCTargets || $practiceTargets || $MGVariationsImages)? 'fa-minus': 'fa-plus';
					@endphp
					<li>
						
						<a href="javascript:void(0)" class="plusbttnbox myBtn">
							Mini Games<i class="fa {{ $showIcon }}" aria-hidden="true"></i>
						</a>

						<div class="dropdown custmenbox">
							
							<div class="dropdown-content myDropdown {{ ($showIcon == 'fa-minus')? 'show': '' }}">
								
								@if($admin->hasPermissionTo('View Complexity Targets'))
								<a href="{{ route('admin.complexityTarget.index') }}" class="@if($chestTargets) {{ 'activelistsub' }} @endif">Chest Targets</a>
								@endif

								@if($admin->hasPermissionTo('View Challenge Nodes'))
								<a href="{{ route('admin.treasure_nodes_targets.index') }}" class="@if($MGCTargets) {{ 'activelistsub' }} @endif">Challenge Nodes</a>
								@endif

								@if($admin->hasPermissionTo('Add Practice Games'))
								<a href="{{ route('admin.practiceGame.index') }}" class="@if($practiceTargets) {{ 'activelistsub' }} @endif">Practice Targets</a>
								@endif

								@if($admin->hasPermissionTo('View Game Variations'))
								<a href="{{ route('admin.gameVariation.index') }}" class="@if($MGVariationsImages) {{ 'activelistsub' }} @endif">Random Hunt Variations</a>
								@endif

							</div>
						</div>
					</li>

					@php 
						
						$chestLoot = ($route == 'admin.rewards.index')? true:false;

						$MGCLoot = ($route == 'admin.mgc_loot.index')? true:false;
						
						$relicLootBind = (
						$route == 'admin.loots.index' || 
						$route == 'admin.loots.show' || 
						$route == 'admin.loots.create'
						)? true:false;						

						$huntXPMngmnt = (
						$route == 'admin.xpManagement.index' || 
						$route == 'admin.xpManagement.edit'
						)? true:false;
						
						$mapPiecesLoot = ($route == 'admin.map-pieces-loots.index')? true:false;
						
						$showIcon = ($chestLoot || $MGCLoot || $relicLootBind || $huntXPMngmnt || $mapPiecesLoot)? 'fa-minus': 'fa-plus';
					@endphp
					<li>
						
						<a href="javascript:void(0)" class="plusbttnbox myBtn">
							Rewards<i class="fa {{ $showIcon }}" aria-hidden="true"></i>
						</a>

						<div class="dropdown custmenbox">
							
							<div class="dropdown-content myDropdown {{ ($showIcon == 'fa-minus')? 'show': '' }}">
								
								

								@if($admin->hasPermissionTo('View Hunt Loot Tables'))
								<a href="{{ route('admin.rewards.index') }}" class="@if($chestLoot) {{ 'activelistsub' }} @endif">Chest Loot Table</a>
								@endif

								@if($admin->hasPermissionTo('View MGC Loot Table'))
								<a href="{{ route('admin.mgc_loot.index') }}" class="@if($MGCLoot) {{ 'activelistsub' }} @endif">MGC Loot Table</a>
								@endif

								@if($admin->hasPermissionTo('View Loot'))
								<a href="{{ route('admin.loots.index') }}" class="@if($relicLootBind) {{ 'activelistsub' }} @endif">Relic Loots</a>
								@endif

								@if($admin->hasPermissionTo('View Hunts XP'))
								<a href="{{ route('admin.xpManagement.index') }}" class="@if($huntXPMngmnt) {{ 'activelistsub' }} @endif">XP rewards</a>
								@endif
									
								<a href="{{ route('admin.map-pieces-loots.index') }}" class="@if($mapPiecesLoot) {{ 'activelistsub' }} @endif">Map Pieces rewards</a>
							</div>
						</div>
					</li>
					
					@if($admin->hasPermissionTo('View Agent Levels') || $admin->hasPermissionTo('View Hunt / Agent Levels') || $admin->hasPermissionTo('View Minigames / Agent Levels') || $admin->hasPermissionTo('View Avatar / Agent Levels') || $admin->hasPermissionTo('View Bucket Size / Agent Levels'))
					<li>
						<a href="javascript:void(0)" class="plusbttnbox myBtn">Manage Agent Levels
							<i 
								class="fa 
								@if(Route::currentRouteName() == 'admin.agent-levels.index' || Route::currentRouteName() == 'admin.minigames-agent-levels.index' ||
								Route::currentRouteName() == 'admin.avatar-agent-levels.index' || Route::currentRouteName() == 'admin.avatar-agent-levels.create' || Route::currentRouteName() == 'admin.avatar-agent-levels.edit' || Route::currentRouteName() == 'admin.avatar-agent-levels.show' ||
								Route::currentRouteName() == 'admin.bucket-sizes.index' || 
								Route::currentRouteName() == 'admin.nodes-agent-levels.index') 
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
							@if(Route::currentRouteName() == 'admin.agent-levels.index' || Route::currentRouteName() == 'admin.hunts-agent-levels.index' || Route::currentRouteName() == 'admin.minigames-agent-levels.index' ||
								Route::currentRouteName() == 'admin.avatar-agent-levels.create' || Route::currentRouteName() == 'admin.avatar-agent-levels.edit' || Route::currentRouteName() == 'admin.avatar-agent-levels.show' ||
								Route::currentRouteName() == 'admin.bucket-sizes.index'|| 
								Route::currentRouteName() == 'admin.nodes-agent-levels.index') 
								{{ 'show' }} 
							@endif">
									@if($admin->hasPermissionTo('View Agent Levels'))
									<a href="{{ route('admin.agent-levels.index') }}" 
									class="
									@if(Route::currentRouteName() == 'admin.agent-levels.index') 
										{{ 'activelistsub' }} 
									@endif
									">Agent Levels</a>
									@endif

									@if($admin->hasPermissionTo('View Minigames / Agent Levels'))
									<a href="{{ route('admin.minigames-agent-levels.index') }}" 
									class="
									@if(Route::currentRouteName() == 'admin.minigames-agent-levels.index') 
										{{ 'activelistsub' }} 
									@endif
									">Minigames / Agent Levels</a>
									@endif

									@if($admin->hasPermissionTo('View Nodes / Agent Levels'))
									<a href="{{ route('admin.nodes-agent-levels.index') }}" 
									class="
									@if(Route::currentRouteName() == 'admin.nodes-agent-levels.index') 
										{{ 'activelistsub' }} 
									@endif
									">Nodes / Agent Levels</a>
									@endif
									

									@if($admin->hasPermissionTo('View Avatar / Agent Levels'))
									<a href="{{ route('admin.avatar-agent-levels.index') }}" 
									class="
									@if(Route::currentRouteName() == 'admin.avatar-agent-levels.index' || Route::currentRouteName() == 'admin.avatar-agent-levels.create' || Route::currentRouteName() == 'admin.avatar-agent-levels.edit' || Route::currentRouteName() == 'admin.avatar-agent-levels.show') 
										{{ 'activelistsub' }} 
									@endif
									">Avatar / Agent Levels</a>
									@endif
									
									@if($admin->hasPermissionTo('View Bucket Size / Agent Levels'))
										<a href="{{ route('admin.bucket-sizes.index') }}" 
										class="
										@if(Route::currentRouteName() == 'admin.bucket-sizes.index') 
											{{ 'activelistsub' }} 
										@endif
										">Bucket Size / Agent Levels</a>
									@endif
							</div>
						</div>
					</li>
					@endif

					@if($admin->hasPermissionTo('View Analytics'))
					<li  class="@if($route == 'admin.analyticsMetrics' || $route == 'admin.analyticsMetrics.XPList' || $route == 'admin.analyticsMetrics.relicsList') {{ 'activelist' }} @endif">
						<a href="{{ route('admin.analyticsMetrics') }}">Analytics</a>
					</li>
					@endif
					<li  class="@if($route == 'admin.plans.index') {{ 'activelist' }} @endif">
						<a href="{{ route('admin.plans.index') }}">Plans</a>
					</li>
					@if($admin->hasPermissionTo('View Avatars'))
						<li  class="@if($route == 'admin.avatar.index' || $route == 'admin.avatarDetails'	) {{ 'activelist' }} @endif">
							<a href="{{ route('admin.avatar.index') }}">Avatars</a>
						</li>
					@endif

					@php 
						
						$userAccessRoute = ($route == 'admin.adminManagement.index')? true:false;
						$appSettingsRoute = ($route == 'admin.app.settings.index')? true:false;
						$notificationsRoute = ($route == 'admin.notifications.index')? true:false;
						$couponRoute = ($route == 'admin.discounts.index')? true:false;
						$paymentRoute = ($route == 'admin.payment.index')? true:false;
						$reportLocations = ($route == 'admin.reported-locations.index')? true:false;
						$cityRoute = ($route == 'admin.city.index')? true:false;
					
						$countryRoute = ($route == 'admin.country.index')? true:false;
						$stateRoute = ($route == 'admin.state.index')? true:false;

						$eventNotificationRoute = ($route == 'admin.event-notifications.index')? true:false;
						
						$showIcon = (
						$userAccessRoute || 
						$appSettingsRoute || 
						$notificationsRoute || 
						$cityRoute || 
						$countryRoute || 
						$stateRoute || 					
						$paymentRoute || 
						$reportLocations || 
						$eventNotificationRoute || 
						$couponRoute)? 'fa-minus': 'fa-plus';
					@endphp
					<li>
						
						<a href="javascript:void(0)" class="plusbttnbox myBtn">
							Settings<i class="fa {{ $showIcon }}" aria-hidden="true"></i>
						</a>

						<div class="dropdown custmenbox">
							
							<div class="dropdown-content myDropdown {{ ($showIcon == 'fa-minus')? 'show': '' }}">
								
								@if($admin->hasPermissionTo('View Payments'))
								<a href="{{ route('admin.payment.index') }}" class="@if($paymentRoute) {{ 'activelistsub' }} @endif">Payments</a>
								@endif

								@if($admin->hasPermissionTo('View Discount Coupons'))
								<a href="{{ route('admin.discounts.index') }}" class="@if($couponRoute) {{ 'activelistsub' }} @endif">Discount Coupons</a>
								@endif

								<a href="{{ route('admin.notifications.index') }}" class="@if($notificationsRoute) {{ 'activelistsub' }} @endif">Notifications</a>
								<a href="{{ route('admin.event-notifications.index') }}" class="@if($eventNotificationRoute) {{ 'activelistsub' }} @endif">Event Notifications</a>

								@if($admin->hasPermissionTo('View App Settings'))
								<a href="{{ route('admin.app.settings.index') }}" class="@if($appSettingsRoute) {{ 'activelistsub' }} @endif">App Settings</a>
								@endif

								@if($admin->hasRole('Super Admin'))
								<a href="{{ route('admin.adminManagement.index') }}" class="@if($userAccessRoute) {{ 'activelistsub' }} @endif">User Access</a>
								@endif
								<a href="{{ route('admin.country.index') }}" class="@if($countryRoute) {{ 'activelistsub' }} @endif">Country</a>

								<a href="{{ route('admin.state.index') }}" class="@if($stateRoute) {{ 'activelistsub' }} @endif">State</a>

								<a href="{{ route('admin.city.index') }}" class="@if($cityRoute) {{ 'activelistsub' }} @endif">Cities</a>

								<a href="{{ route('admin.reported-locations.index') }}" class="@if($reportLocations) {{ 'activelistsub' }} @endif">Reported Google Locations</a>

							</div>
						</div>
					</li>
				@endif
			</div>
		</div>
	</div>