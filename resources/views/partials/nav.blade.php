<header class="flex items-center justify-between relative bg-white
shadow-sm dark:bg-gray-800/50 dark:shadow-none dark:after:pointer-events-none dark:after:absolute dark:after:inset-x-0
dark:after:bottom-0 dark:after:h-px dark:after:bg-white/10">

    <nav class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 w-full">
        <div class="py-2">
            <div class="flex justify-between">
                <div class="flex">
                    <div class="flex shrink-0 items-center">
                        <a href="{{ route('home') }}" class="flex items-center gap-2" wire:navigate>
                            <img src="{{ Vite::asset('resources/images/trash_logo.webp') }}" alt="TrashConnect" class="w-20 md:w-24">
                        </a>
                    </div>

                </div>
                <div class="hidden sm:ml-6 sm:flex sm:space-x-8 self-center">
                    <!-- Current: "bg-gray-900 dark:bg-gray-950/50 text-white", Default: "text-gray-300 hover:bg-white/5 hover:text-white" -->
                    <x-nav-link :href="route('home')" route="home" wire:navigate>Home</x-nav-link>
                    <x-nav-link :href="route('shows.index')" route="shows.*" wire:navigate>Alle Shows</x-nav-link>

                    @foreach($navPages ?? [] as $p)
                        <x-nav-link :href="route('pages.show', $p->slug)" :route="'pages.show'" wire:navigate>
                            {{ $p->title }}
                        </x-nav-link>
                    @endforeach
                </div>
                <div class="-mr-2 flex items-center sm:hidden">
                    <!-- Mobile menu button -->
                    <button type="button" command="--toggle" commandfor="mobile-menu" class="relative inline-flex items-center justify-center rounded-md p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-500 focus:outline-2 focus:-outline-offset-1 focus:outline-indigo-600 dark:hover:bg-white/5 dark:hover:text-white dark:focus:outline-indigo-500">
                        <span class="absolute -inset-0.5"></span>
                        <span class="sr-only">Open main menu</span>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" data-slot="icon" aria-hidden="true" class="size-6 in-aria-expanded:hidden">
                            <path d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" data-slot="icon" aria-hidden="true" class="size-6 not-in-aria-expanded:hidden">
                            <path d="M6 18 18 6M6 6l12 12" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <el-disclosure id="mobile-menu" hidden class="block sm:hidden">
            <div class="space-y-1 pt-2 pb-3">
                <a href="#" class="block border-l-4 border-indigo-600 bg-indigo-50 py-2 pr-4 pl-3 text-base font-medium text-indigo-700 dark:border-indigo-500 dark:bg-indigo-600/10 dark:text-indigo-400">Dashboard</a>
                <a href="#" class="block border-l-4 border-transparent py-2 pr-4 pl-3 text-base font-medium text-gray-500 hover:border-gray-300 hover:bg-gray-50 hover:text-gray-700 dark:text-gray-300 dark:hover:border-white/20 dark:hover:bg-white/5 dark:hover:text-white">Team</a>
                <a href="#" class="block border-l-4 border-transparent py-2 pr-4 pl-3 text-base font-medium text-gray-500 hover:border-gray-300 hover:bg-gray-50 hover:text-gray-700 dark:text-gray-300 dark:hover:border-white/20 dark:hover:bg-white/5 dark:hover:text-white">Projects</a>
                <a href="#" class="block border-l-4 border-transparent py-2 pr-4 pl-3 text-base font-medium text-gray-500 hover:border-gray-300 hover:bg-gray-50 hover:text-gray-700 dark:text-gray-300 dark:hover:border-white/20 dark:hover:bg-white/5 dark:hover:text-white">Calendar</a>
            </div>
        </el-disclosure>
    </nav>

</header>
