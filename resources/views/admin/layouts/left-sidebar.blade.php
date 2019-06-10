<div class="left_paretboxpart">
	<div class="innercategories">
		<ul>
			<li  class="@if(Route::currentRouteName() == 'admin.dashboards') {{ 'activelist' }} @endif">
				<a href="{{ route('admin.dashboards') }}">Dashboard</a>
			</li>
			<li  class="@if(Route::currentRouteName() == 'admin.userList') {{ 'activelist' }} @endif">
				<a href="{{ route('admin.userList') }}">Users</a>
			</li>
			<li  class="@if(Route::currentRouteName() == 'admin.news.index') {{ 'activelist' }} @endif">
				<a href="{{ route('admin.news.index') }}">News</a>
			</li>
			<li  class="@if(Route::currentRouteName() == 'admin.mapsList' ||Route::currentRouteName() == 'admin.add_location' || Route::currentRouteName() == 'admin.boundary_map' || Route::currentRouteName() == 'admin.starComplexityMap') {{ 'activelist' }} @endif">
				<a href="{{ route('admin.mapsList') }}">Treasure locations</a>
			</li>
		</ul>
	</div>
</div>