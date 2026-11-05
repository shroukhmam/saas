<x-tenant-guest-layout>
    <form method="POST" action="{{ route('tenant.login') }}" class="flex flex-col gap-4">
        @csrf
        <div class="flex flex-col gap-1">
            <h1 class="text-lg font-bold">Sign in to your store</h1>
            <p class="text-muted">Use the email and password from the store owner details.</p>
        </div>

        <div class="flex flex-col gap-1.5">
            <label for="email" class="font-semibold">Email</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                   class="min-h-11 rounded-[10px] border-slate-300 focus:border-teal-500 focus:ring-teal-500 @error('email') border-red-500 @enderror">
            @error('email')<p class="text-xs text-red-700">{{ $message }}</p>@enderror
        </div>

        <div class="flex flex-col gap-1.5">
            <label for="password" class="font-semibold">Password</label>
            <input id="password" name="password" type="password" required autocomplete="current-password"
                   class="min-h-11 rounded-[10px] border-slate-300 focus:border-teal-500 focus:ring-teal-500">
            @error('password')<p class="text-xs text-red-700">{{ $message }}</p>@enderror
        </div>

        <label for="remember" class="flex items-center gap-2.5 min-h-11">
            <input id="remember" name="remember" type="checkbox" value="1" class="w-[18px] h-[18px] rounded text-teal-700 focus:ring-teal-500">
            Keep me signed in
        </label>

        <button type="submit" class="min-h-11 rounded-[10px] bg-teal-700 text-white font-bold hover:bg-teal-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-500 focus-visible:ring-offset-2">Sign in</button>
    </form>
</x-tenant-guest-layout>
