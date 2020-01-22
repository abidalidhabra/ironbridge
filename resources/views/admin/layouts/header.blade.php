@php
    $env = config('app.env');
    $envPath = config('servers.url.'.$env);
    $oppEnv = ($env == 'staging')? 'production': 'staging';
    $oppEnvPath = config('servers.url.'.$oppEnv);
@endphp
<header>
    <div class="hamburgerboxdis">
        <a id="offCanvas" class="hamburger"><span> ☰ </span></a>
    </div>
    <div class="logo_leftbox">
        <img src="{{ asset('admin_assets/svg/ib-logo.svg') }}">
        <div class="headerLabel left_label Label_active">
            <h5>{{ strtoupper($env) }} SERVER</h5>
            <p>{{ $envPath }}</p>
        </div>
    </div>
    <div class="logout_rightbox">
        <a href="{{ route('admin.logout') }}" onclick="event.preventDefault();
                            document.getElementById('logout-form').submit();">
            <h4>LOG OUT</h4>
        </a>
        <div class="headerLabel right_label">
            <h5>{{ strtoupper($oppEnv) }} SERVER</h5>
            <p>{{ $oppEnvPath }}</p>
        </div>
        
        <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
    </div>
</header>