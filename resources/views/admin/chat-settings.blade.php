@extends('layouts.app')

@section('content')
    <section class="rounded-3xl bg-white p-6 shadow-md ring-1 ring-black/5" data-aos="fade-up">
        <h1 class="section-title">Chat Settings</h1>
        <form method="POST" action="{{ route('admin.chat.settings.save') }}" class="mt-6 space-y-6">
            @csrf
            <div class="flex items-center gap-3">
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="force_bot_only" {{ old('force_bot_only', $settings->force_bot_only ?? false) ? 'checked' : '' }}>
                    <span>Force bot only (disable live agents)</span>
                </label>
            </div>
            <div class="flex items-center gap-3">
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="force_live_only" {{ old('force_live_only', $settings->force_live_only ?? false) ? 'checked' : '' }}>
                    <span>Force live only (bypass bot)</span>
                </label>
            </div>
            <div>
                <label class="block font-medium">Business Hours (JSON)</label>
                <textarea name="business_hours" rows="6" class="mt-2 w-full rounded-lg border border-slate-300 px-3 py-2">{{ json_encode(old('business_hours', $settings->business_hours ?? []), JSON_PRETTY_PRINT) }}</textarea>
                <p class="mt-1 text-xs text-slate-500">Example: [{"day":"mon","start":"09:00","end":"18:00"}]</p>
            </div>
            <button class="btn-primary">Save</button>
        </form>
    </section>
@endsection


