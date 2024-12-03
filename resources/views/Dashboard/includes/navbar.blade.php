<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light ">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button">
                <i class="fas fa-bars"></i>
            </a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="{{ route('dashboard.home') }}" class="nav-link">{{ trans('admin.Home') }}</a>
        </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto ">
        <!-- Notification Dropdown -->
        <li class="nav-item dropdown">
            <a class="nav-link px-3" href="#" role="button" data-toggle="dropdown">
                <i class="far fa-bell"></i>
                <span class="notification-badge">{{ auth()->user()->unreadNotifications->count() }}</span>
            </a>
            <div class="dropdown-menu dropdown-menu-right notification-dropdown">
                <div class="notification-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">{{ trans('admin.Notifications') }}</h6>
                        <span class="text-primary mark-all-as-read" style="cursor: pointer;">{{ trans('admin.Mark all as read') }}</span>
                    </div>
                </div>
            
                <div class="notification-body">
                    @foreach (auth()->user()->unreadNotifications()->orderBy('created_at', 'desc')->get() as $notification)
                        <div class="notification-item unread" data-notification-id="{{ $notification->id }}">
                            <div class="notification-content">
                                <div class="notification-icon">
                                    <i class="fas fa-bell text-primary"></i>
                                </div>
                                <div class="notification-text">
                                    <p class="mb-1">{{ $notification->data['message'] }}</p>
                                    <span class="notification-time">{{ $notification->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="notification-footer">
                    <a href="#" class="text-decoration-none text-primary">{{ trans('admin.View All') }}</a>
                </div>
            </div>
        </li>

        <!-- Action Buttons -->
        <li class="nav-item">
            @if (auth('user')->user()->has_permission('create-products'))
                <a href="{{ route('dashboard.sells.create') }}"
                    class="btn btn-info mr-2">{{ trans('admin.Add_sale') }}</a>
            @else
                <a href="#" class="btn btn-info disabled">{{ trans('admin.Add') }}</a>
            @endif
        </li>
        <li class="nav-item">
            @if (auth('user')->user()->has_permission('view-payment-history-contacts'))
                <a href="{{ route('dashboard.contacts.payment-history', ['id' => \App\Models\Contact::where('type', 'customer')->first()->id ?? 0, 'type' => 'customer']) }}"
                    class="btn btn-info mr-2">{{ trans('admin.Customer_collection') }}</a>
            @else
                <a href="#" class="btn btn-info disabled">{{ trans('admin.Customer_collection') }}</a>
            @endif
        </li>
        <li class="nav-item">
            @if (auth('user')->user()->has_permission('view-payment-history-contacts'))
                <a href="{{ route('dashboard.contacts.payment-history', ['id' => \App\Models\Contact::where('type', 'supplier')->first()->id ?? 0, 'type' => 'supplier']) }}"
                    class="btn btn-info mr-2">{{ trans('admin.Supplier_collection') }}</a>
            @else
                <a href="#" class="btn btn-info disabled">{{ trans('admin.Supplier_collection') }}</a>
            @endif
        </li>

        <!-- Language Dropdown -->
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <i class="fas fa-globe"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                @foreach (LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
                    <a href="{{ LaravelLocalization::getLocalizedURL($localeCode, null, [], true) }}"
                        class="dropdown-item">
                        {{ $properties['native'] }}
                    </a>
                @endforeach
            </div>
        </li>

        <!-- User Profile Dropdown -->
        <li class="nav-item dropdown ">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <div class="d-flex align-items-center">
                    <img src="{{ auth('user')->user()->getImage() }}" style="width: 50px" class="img-circle mr-2" alt="User Image">
                    <span>{{ auth('user')->user()->name }}</span>
                </div>
            </a>
            <div class="dropdown-menu dropdown-menu-right">
                <a href="{{ route('dashboard.profile.edit') }}" class="dropdown-item">
                    {{ trans('admin.Profile') }}
                </a>
                <a href="{{ route('dashboard.logout') }}" class="dropdown-item">
                    {{ trans('admin.logout') }}
                </a>
            </div>
        </li>
    </ul>
</nav>
<!-- /.navbar -->
<script>
    $(document).ready(function() {
        // Function to update notification count
        function updateNotificationCount() {
            $.get("{{ route('getUnreadCount') }}", function(response) {
                $('.notification-badge').text(response.count);
                if (response.count === 0) {
                    $('.notification-badge').hide();
                }
            });
        }
    
        // Mark single notification as read
        $(document).on('click', '.notification-item.unread', function(e) {
            e.preventDefault();
            const notificationId = $(this).data('notification-id');
            const $notificationItem = $(this);
    
            $.ajax({
                url: "{{ route('markAsRead') }}",
                method: 'POST',
                data: {
                    notification_id: notificationId,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        // Remove unread class and update styling
                        $notificationItem.removeClass('unread');
                        updateNotificationCount();
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error marking notification as read:', error);
                }
            });
        });
    
        // Mark all notifications as read
        $('.mark-all-as-read').click(function(e) {
            e.preventDefault();
            e.stopPropagation(); // Prevent dropdown from closing
    
            $.ajax({
                url: "{{ route('markAllAsRead') }}",
                method: 'GET',
                success: function(response) {
                    if (response.success) {
                        // Remove unread class from all notifications
                        $('.notification-item').removeClass('unread');
                        // Update the notification count
                        $('.notification-badge').text('0').hide();
                        // Optionally close the dropdown
                        // $('.notification-dropdown').dropdown('hide');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error marking all notifications as read:', error);
                }
            });
        });
    
        // Optional: Close dropdown when clicking outside
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.notification-dropdown, .nav-link').length) {
                $('.notification-dropdown').removeClass('show');
            }
        });
    });
    </script>
