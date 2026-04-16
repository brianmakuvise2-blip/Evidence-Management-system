@extends('layouts.admin')

@section('page-title', 'Notifications')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Notifications</h4>
                    @if($notifications->total() > 0)
                        <form method="POST" action="{{ route('notifications.mark-all-read') }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="bi bi-check-all"></i> Mark All as Read
                            </button>
                        </form>
                    @endif
                </div>

                <div class="card-body">
                    @if($notifications->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($notifications as $notification)
                                <div class="list-group-item {{ $notification->read_at ? 'bg-light' : 'bg-white border-primary' }}">
                                    <div class="d-flex w-100 justify-content-between">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1 {{ $notification->read_at ? 'text-muted' : 'text-dark' }}">
                                                @if(!$notification->read_at)
                                                    <i class="bi bi-circle-fill text-primary me-2" style="font-size: 0.5rem;"></i>
                                                @endif
                                                {{ $notification->data['title'] ?? 'Notification' }}
                                            </h6>
                                            <p class="mb-1 text-muted">{{ $notification->data['message'] ?? '' }}</p>
                                            <small class="text-muted">
                                                {{ $notification->created_at->format('M d, Y H:i') }}
                                                @if($notification->data['type'] ?? false)
                                                    <span class="badge bg-secondary">{{ $notification->data['type'] }}</span>
                                                @endif
                                            </small>
                                        </div>
                                        <div class="d-flex flex-column gap-2">
                                            @if(!$notification->read_at)
                                                <form method="POST" action="{{ route('notifications.mark-read', $notification) }}" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline-primary">
                                                        <i class="bi bi-check"></i> Mark Read
                                                    </button>
                                                </form>
                                            @endif

                                            @if(isset($notification->data['action_url']))
                                                <a href="{{ $notification->data['action_url'] }}" class="btn btn-sm btn-primary">
                                                    <i class="bi bi-eye"></i> View
                                                </a>
                                            @endif

                                            <form method="POST" action="{{ route('notifications.destroy', $notification) }}" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger"
                                                        onclick="return confirm('Are you sure you want to delete this notification?')">
                                                    <i class="bi bi-trash"></i> Delete
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="d-flex justify-content-center mt-4">
                            {{ $notifications->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-bell-slash text-muted" style="font-size: 3rem;"></i>
                            <h5 class="text-muted mt-3">No notifications</h5>
                            <p class="text-muted">You don't have any notifications at the moment.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection