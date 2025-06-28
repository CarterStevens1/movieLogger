        <x-navigation.mobile-navigation />

        <!-- Desktop -->
        <div id="navigation"
            class=" mx-4 my-0 xl:ml-8 xl:mr-0 xl:my-8 xl:h-[calc(100vh_-_4rem)]! sticky max-xl:hidden top-0 xl:top-4 z-10 rounded-xl transform xl:translate-x-0  ease-in-out transition duration-500 flex justify-start items-start h-screen w-full sm:w-70 bg-gray-900 flex-col">

            <div class="hidden xl:flex p-6 items-center">
                <a class="xl:flex justify-start gap-3" href="{{ Route('home') }}">
                    <img src="{{ asset('images/logo.svg') }}" alt="logo" class="w-8 h-8 rounded-full">
                    <p class="text-2xl font-semibold">Boardchive</p>
                </a>
            </div>
            <div class="mt-6 flex flex-col justify-start items-center pl-4 w-full border-gray-600 border-y gap-6 py-5">
                @auth
                    <a href="{{ url('/my-boards') }}"
                        class="flex jusitfy-start items-center space-x-6 w-full focus:outline-none focus:text-lightGreen-400 hover:text-lightGreen-400 cursor-pointer">
                        <svg class="fill-stroke " width="24" height="24" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M9 4H5C4.44772 4 4 4.44772 4 5V9C4 9.55228 4.44772 10 5 10H9C9.55228 10 10 9.55228 10 9V5C10 4.44772 9.55228 4 9 4Z"
                                stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            <path
                                d="M19 4H15C14.4477 4 14 4.44772 14 5V9C14 9.55228 14.4477 10 15 10H19C19.5523 10 20 9.55228 20 9V5C20 4.44772 19.5523 4 19 4Z"
                                stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            <path
                                d="M9 14H5C4.44772 14 4 14.4477 4 15V19C4 19.5523 4.44772 20 5 20H9C9.55228 20 10 19.5523 10 19V15C10 14.4477 9.55228 14 9 14Z"
                                stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            <path
                                d="M19 14H15C14.4477 14 14 14.4477 14 15V19C14 19.5523 14.4477 20 15 20H19C19.5523 20 20 19.5523 20 19V15C20 14.4477 19.5523 14 19 14Z"
                                stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <p class="text-base leading-4 ">Dashboard</p>
                    </a>
                    <a href="{{ Route('boards.create') }}"
                        class="flex jusitfy-start items-center space-x-6 w-full focus:outline-none focus:text-lightGreen-400 hover:text-lightGreen-400 cursor-pointer">
                        <svg width="24" height="24" fill="currentColor" viewBox="0 0 24 24" transform=""
                            id="injected-svg">
                            <!-- Boxicons v3.0 https://boxicons.com | License  https://docs.boxicons.com/free -->
                            <path d="M3 13h8v8h2v-8h8v-2h-8V3h-2v8H3z" />
                        </svg>
                        <p class="text-base leading-4">New Board</p>
                    </a>
                @endauth
                @guest
                    <a href="{{ Route('register') }}"
                        class="flex jusitfy-start items-center space-x-6 w-full focus:outline-none focus:text-lightGreen-400 hover:text-lightGreen-400 cursor-pointer">
                        <x-mdi-account-plus-outline class="w-6 h-6" />
                        <p class="text-base leading-4">Create account</p>
                    </a>
                @endguest
            </div>

            <div class="flex flex-col justify-end p-6 w-full mt-auto">
                @guest
                    <div class="space-x-6 font-bold">

                        <a id="logIn"
                            class="bg-green-500 rounded-lg px-4 py-2 justify-center flex gap-2 hover:bg-green-700 transition-all duration-300 ease-in-out"
                            href="{{ url('/login') }}">Log In
                            <x-monoicon-log-in class="w-6 h-6" /></a>
                    </div>
                @endguest
                @auth
                    <div class="flex justify-center items-center space-x-6 font-bold pb-6">
                        <form method="POST" action="/logout">
                            @csrf
                            @method('POST')
                            <x-global.button id="logOut" variant="peach" href="{{ Route('logout') }}">Log Out
                                <x-monoicon-log-out class="w-6 h-6" /></x-global.button>
                        </form>
                    </div>
                    <div class="flex justify-between items-center gap-1">
                        <div class="flex justify-start flex-col items-start gap-1">
                            <p class="cursor-pointer text-sm leading-5">{{ Auth::user()->name }}</p>
                            <p class="cursor-pointer text-xs leading-3 text-gray-300">{{ Auth::user()->email }}</p>
                        </div>
                        <a href="{{ url('/edit') }}">
                            <svg class="cursor-pointer" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M10.325 4.317C10.751 2.561 13.249 2.561 13.675 4.317C13.7389 4.5808 13.8642 4.82578 14.0407 5.032C14.2172 5.23822 14.4399 5.39985 14.6907 5.50375C14.9414 5.60764 15.2132 5.65085 15.4838 5.62987C15.7544 5.60889 16.0162 5.5243 16.248 5.383C17.791 4.443 19.558 6.209 18.618 7.753C18.4769 7.98466 18.3924 8.24634 18.3715 8.51677C18.3506 8.78721 18.3938 9.05877 18.4975 9.30938C18.6013 9.55999 18.7627 9.78258 18.9687 9.95905C19.1747 10.1355 19.4194 10.2609 19.683 10.325C21.439 10.751 21.439 13.249 19.683 13.675C19.4192 13.7389 19.1742 13.8642 18.968 14.0407C18.7618 14.2172 18.6001 14.4399 18.4963 14.6907C18.3924 14.9414 18.3491 15.2132 18.3701 15.4838C18.3911 15.7544 18.4757 16.0162 18.617 16.248C19.557 17.791 17.791 19.558 16.247 18.618C16.0153 18.4769 15.7537 18.3924 15.4832 18.3715C15.2128 18.3506 14.9412 18.3938 14.6906 18.4975C14.44 18.6013 14.2174 18.7627 14.0409 18.9687C13.8645 19.1747 13.7391 19.4194 13.675 19.683C13.249 21.439 10.751 21.439 10.325 19.683C10.2611 19.4192 10.1358 19.1742 9.95929 18.968C9.7828 18.7618 9.56011 18.6001 9.30935 18.4963C9.05859 18.3924 8.78683 18.3491 8.51621 18.3701C8.24559 18.3911 7.98375 18.4757 7.752 18.617C6.209 19.557 4.442 17.791 5.382 16.247C5.5231 16.0153 5.60755 15.7537 5.62848 15.4832C5.64942 15.2128 5.60624 14.9412 5.50247 14.6906C5.3987 14.44 5.23726 14.2174 5.03127 14.0409C4.82529 13.8645 4.58056 13.7391 4.317 13.675C2.561 13.249 2.561 10.751 4.317 10.325C4.5808 10.2611 4.82578 10.1358 5.032 9.95929C5.23822 9.7828 5.39985 9.56011 5.50375 9.30935C5.60764 9.05859 5.65085 8.78683 5.62987 8.51621C5.60889 8.24559 5.5243 7.98375 5.383 7.752C4.443 6.209 6.209 4.442 7.753 5.382C8.753 5.99 10.049 5.452 10.325 4.317Z"
                                    stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                <path
                                    d="M12 15C13.6569 15 15 13.6569 15 12C15 10.3431 13.6569 9 12 9C10.3431 9 9 10.3431 9 12C9 13.6569 10.3431 15 12 15Z"
                                    stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </a>

                    </div>
                @endauth
            </div>
        </div>
