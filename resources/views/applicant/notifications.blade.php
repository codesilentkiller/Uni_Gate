<x-app-layout>
    <x-slot name="header"><h2 class="text-xl font-semibold">Notifications</h2></x-slot>
    <div class="mx-auto max-w-5xl p-8">
        <div class="flex items-end justify-between">
            <div><p class="text-sm text-slate-500">Applicant updates</p><h1 class="mt-1 text-2xl font-bold">Notifications</h1></div>
            @if(auth()->user()->unreadNotifications()->exists())
                <form method="POST" action="{{ route('applicant.notifications.read-all') }}">@csrf @method('PATCH')<button class="rounded-lg border px-4 py-2 text-sm font-semibold">Mark all as read</button></form>
            @endif
        </div>
        @if(session('status'))<div class="mt-5 rounded-lg border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-700">{{ session('status') }}</div>@endif
        <div class="mt-6 space-y-3">
            @forelse($notifications as $notification)
                <article class="rounded-2xl border bg-white p-5 {{ $notification->read_at ? '' : 'border-blue-300 bg-blue-50/30' }}">
                    <div class="flex items-start justify-between gap-4"><div><p class="font-semibold">{{ $notification->data['title'] ?? 'UniGate update' }}</p><p class="mt-2 text-sm text-slate-600">{{ $notification->data['message'] ?? ($notification->data['decision'] ?? 'A new update is available.') }}</p><p class="mt-2 text-xs text-slate-400">{{ $notification->created_at->diffForHumans() }}</p></div>
                    @if(! $notification->read_at)<span class="rounded-full bg-blue-700 px-2.5 py-1 text-xs font-bold text-white">Unread</span>@endif</div>
                    <form method="POST" action="{{ route('applicant.notifications.read', $notification) }}" class="mt-4">@csrf @method('PATCH')<button class="text-sm font-semibold text-blue-700">{{ $notification->read_at ? 'View application' : 'Mark read and view' }}</button></form>
                    @if(($notification->data['action'] ?? null) === 'payments')
                        <a href="{{ route('applicant.payments') }}" class="mt-4 inline-block rounded-lg bg-blue-700 px-4 py-2 text-sm font-semibold text-white">Pay fees</a>
                    @endif
                </article>
            @empty
                <div class="rounded-2xl border bg-white py-16 text-center text-sm text-slate-500">No notifications yet.</div>
            @endforelse
        </div>
        <div class="mt-6">{{ $notifications->links() }}</div>
    </div>
</x-app-layout>
