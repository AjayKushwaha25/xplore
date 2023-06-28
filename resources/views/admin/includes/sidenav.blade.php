<!-- ========== Left Sidebar Start ========== -->
<div class="vertical-menu">

    <div data-simplebar class="h-100">

        <!--- Sidemenu -->
        <div id="sidebar-menu">
            <!-- Left Menu Start -->
            <ul class="metismenu list-unstyled" id="side-menu">
                <li>
                    <a href="{{ url('admin/home') }}" class="waves-effect">
                        <i class="bx bx-home-circle"></i>
                        <span key="t-dashboards">Dashboard</span>
                    </a>
                </li>
                @if(Auth::user()->roles->first()->slug == 'super_admin')
                <li>
                    <a href="javascript: void(0);" class="has-arrow waves-effect">
                        <i class="mdi mdi-qrcode-scan"></i>
                        <span key="t-qr-code">QR Codes</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="{{ route('admin.qr-codes.index') }}" key="t-view-qr-codes">View QR Code</a></li>
                        <li><a href="{{ route('admin.qr-codes.create') }}" key="t-add-qr-codes">Generate QR Code</a></li>
                        {{-- <li><a href="{{ route('admin.qr-codes.create') }}" key="t-add-qr-code">Generate QR Code</a></li> --}}
                    </ul>
                </li>
                <li>
                    <a href="{{ url('admin/export') }}" class="waves-effect">
                        <i class="bx bx-export"></i>
                        <span key="t-export">Export Excel</span>
                    </a>
                </li>
                @endif
                <li>
                    <a href="javascript: void(0);" class="has-arrow waves-effect">
                        <i class="bx bx-user"></i>
                        <span key="t-user">Retailers</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="{{ route('admin.retailers.index') }}" key="t-view-user">View Retailer</a></li>
                        <li><a href="{{ route('admin.login-histories') }}" key="t-view-user">Login History</a></li>
                    </ul>
                </li>
                <li>
                    <a href="javascript: void(0);" class="has-arrow waves-effect">
                        <i class="bx bx-user"></i>
                        <span key="t-user">LP Retailers</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="{{ route('admin.lpretailer_index') }}" key="t-view-user">View LP Retailer</a></li>
                        <li><a href="{{ route('admin.lpretailer_histories_index') }}" key="t-view-user">LP Login History</a></li>
                    </ul>
                </li>
                @if(Auth::user()->roles->first()->slug == 'super_admin')
                <li>
                    <a href="javascript: void(0);" class="has-arrow waves-effect">
                        <i class="bx bx-user"></i>
                        <span key="t-admin">Payouts</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="{{ route('admin.payouts.index') }}" key="t-view-admin">View Payouts</a></li>
                        {{-- <li><a href="{{ route('admin.users.create') }}" key="t-add-admin">Import Payouts</a></li> --}}
                    </ul>
                </li>
                <li>
                    <a href="javascript: void(0);" class="has-arrow waves-effect">
                        <i class="bx bx-user"></i>
                        <span key="t-admin">Admins</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="{{ route('admin.users.index') }}" key="t-view-admin">View Admin</a></li>
                        <li><a href="{{ route('admin.users.create') }}" key="t-add-admin">Add Admin</a></li>
                    </ul>
                </li>
                @endif
            </ul>
        </div>
        <!-- Sidebar -->
    </div>
</div>
<!-- Left Sidebar End -->
