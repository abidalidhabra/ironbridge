<div class="left_paretboxpart">
	<!-- <div class="innercategories"></div> -->
	<div class="nav-offcanvas">
	    <button type="button" class="close closebtnboxiner" id="offCanvasClose" aria-label="Close">
	        <span>×</span>
	    </button>
	    <div class="nav-offcanvas-menu">
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
				<!-- <li  class="@if(Route::currentRouteName() == 'admin.game.index') {{ 'activelist' }} @endif">
					<a href="{{ route('admin.game.index') }}">Game</a>
				</li> -->
				<li  class="@if(Route::currentRouteName() == 'admin.mapsList' ||Route::currentRouteName() == 'admin.add_location' || Route::currentRouteName() == 'admin.boundary_map' || Route::currentRouteName() == 'admin.starComplexityMap' || Route::currentRouteName() == 'admin.edit_location') {{ 'activelist' }} @endif">
					<a href="{{ route('admin.mapsList') }}">Treasure locations</a>
				</li>	           
	        </ul>
	    </div>
	</div>
</div>