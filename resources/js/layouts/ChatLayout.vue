<script setup lang="ts">
import Back from '@/components/icons/Back.vue';
import Clock from '@/components/icons/Clock.vue';
import Forward from '@/components/icons/Forward.vue';
import Home from '@/components/icons/Home.vue';
import Question from '@/components/icons/Question.vue';
import { joinWorkspace, leaveWorkspace } from '@/composables/useOnlineUsers';
import { logout, reset } from '@/routes';
import { Form, Link, router, usePage } from '@inertiajs/vue3';
import { computed, onMounted, onUnmounted } from 'vue';

onMounted(joinWorkspace);
onUnmounted(leaveWorkspace);

const handleLogout = () => {
    router.flushAll();
};

const page = usePage();
const user = computed(() => page.props.auth.user);
</script>

<template>
    <div
        class="flex h-screen w-full flex-col items-center bg-linear-to-tr from-fuchsia-600 from-10% to-fuchsia-900 p-1"
    >
        <div class="flex w-full items-center justify-between px-2 py-1.5">
            <!-- Window actions -->
            <div class="flex gap-x-2">
                <span class="h-3 w-3 rounded-full bg-red-400"></span>

                <span class="h-3 w-3 rounded-full bg-yellow-400"></span>

                <span class="h-3 w-3 rounded-full bg-green-400"></span>
            </div>

            <!-- Controls -->
            <div class="flex w-1/2 items-center gap-x-2">
                <Back class="h-5 w-5 text-white/20" />

                <Forward class="h-5 w-5 text-white/20" />

                <Clock class="h-5 w-5 text-white" />

                <input
                    type="search"
                    class="w-full rounded bg-white/20 px-3 py-1 text-base placeholder:text-white"
                    :placeholder="$t('Search :name', { name: 'Laravel' })"
                />
            </div>

            <!-- Help -->
            <Form v-bind="reset.form()">
                <button>
                    <Question class="h-5 w-5 text-white" />
                </button>
            </Form>
        </div>

        <div class="flex w-full flex-1">
            <!-- Sidebar -->
            <div class="flex flex-col justify-between py-2 pr-5 pl-3.5">
                <ul class="flex flex-col gap-y-4">
                    <li>
                        <img
                            src="/images/laravel.png"
                            alt="Avatar"
                            class="h-10 w-10 rounded-md"
                        />
                    </li>

                    <li
                        class="flex h-10 w-10 items-center justify-center rounded-md bg-white/20 text-white"
                    >
                        <Home class="h-6 w-6" />
                    </li>
                </ul>

                <div class="mb-2">
                    <Link
                        class="relative"
                        :href="logout()"
                        @click="handleLogout"
                        as="button"
                        data-test="logout-button"
                    >
                        <img
                            :src="user.avatar"
                            :alt="user.name"
                            class="h-10 w-10 rounded-md"
                        />

                        <span class="absolute -right-1 -bottom-1">
                            <span
                                class="flex h-3.5 w-3.5 items-center justify-center rounded-full bg-fuchsia-600"
                            >
                                <span
                                    class="flex h-2 w-2 rounded-full border border-green-600 bg-green-600"
                                ></span>
                            </span>
                        </span>
                    </Link>
                </div>
            </div>

            <!-- Workspace -->
            <slot />
        </div>
    </div>
</template>
