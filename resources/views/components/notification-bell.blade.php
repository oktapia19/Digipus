@if(auth('admin')->check() || auth('petugas')->check() || auth()->check())
<div class="app-notif-wrap" data-csrf-token="{{ csrf_token() }}">
  <button type="button" class="app-notif-btn" id="appNotifBtn" title="Notifikasi">
    <i class="fa-solid fa-bell"></i>
    @if(($appNotificationUnreadCount ?? 0) > 0)
      <span class="app-notif-badge">{{ $appNotificationUnreadCount > 99 ? '99+' : $appNotificationUnreadCount }}</span>
    @endif
  </button>

  <div class="app-notif-dropdown" id="appNotifDropdown">
    <div class="app-notif-head">
      <strong>Notifikasi</strong>
      <div class="app-notif-actions">
        <form action="{{ route('notifications.readAll') }}" method="POST">
          @csrf
          <button type="submit" class="app-notif-readall" title="Tandai dibaca">
            <i class="fa-regular fa-envelope-open"></i>
          </button>
        </form>
        <form action="{{ route('notifications.destroyAll') }}" method="POST">
          @csrf
          <button type="button" class="app-notif-clearall" id="appNotifClearAllBtn" data-url="{{ route('notifications.destroyAllAjax') }}" title="Hapus semua">
            <i class="fa-solid fa-broom"></i>
          </button>
        </form>
      </div>
    </div>

    <div class="app-notif-list">
      @forelse(($appNotifications ?? collect()) as $n)
        <div class="app-notif-item {{ $n->is_read ? '' : 'unread' }}">
          <button
            type="button"
            class="app-notif-link app-notif-open"
            data-read-url="{{ route('notifications.read', $n->id) }}"
            aria-label="Tandai notifikasi dibaca"
          >
            <div class="app-notif-title">{{ $n->title }}</div>
            @if($n->message)
              <div class="app-notif-msg">{{ $n->message }}</div>
            @endif
            <div class="app-notif-time">{{ $n->created_at->diffForHumans() }}</div>
          </button>
        </div>
      @empty
        <div class="app-notif-empty">Belum ada notifikasi.</div>
      @endforelse
    </div>
  </div>
</div>

<link rel="stylesheet" href="{{ asset('css/komponen-notifikasi-lonceng.css') }}">
<script src="{{ asset('js/popup-indonesia.js') }}"></script>
<script src="{{ asset('js/komponen-notifikasi-lonceng.js') }}"></script>
@endif


