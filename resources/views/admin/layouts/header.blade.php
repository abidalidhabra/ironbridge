<header>
    <div class="hamburgerboxdis">
        <a id="offCanvas" class="hamburger"><span> ☰ </span></a>
    </div>
    <div class="logo_leftbox">
        <img src="{{ asset('admin_assets/svg/ib-logo.svg') }}">
        <div class="headerLabel">
            {{ strtoupper(config('app.env')) . ' SERVER' }}
        </div>
    </div>
    <div class="logout_rightbox">
        <a href="{{ route('admin.logout') }}" onclick="event.preventDefault();
                            document.getElementById('logout-form').submit();">
            <h4>LOG OUT</h4>
        </a>
        <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
    </div>
</header>