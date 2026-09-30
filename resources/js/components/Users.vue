<script setup lang="ts">
import { User } from '@/types';

defineProps<{
    openUsers: boolean;
    users: User[];
}>();
</script>

<template>
    <ul v-show="openUsers">
        <li v-for="user in users" :key="user.id">
            <span
                class="flex items-center gap-x-2 rounded-md px-4 py-1 hover:bg-fuchsia-900 hover:text-white"
            >
                <span class="relative">
                    <img
                        :src="user.avatar"
                        :alt="user.name"
                        class="size-6 rounded-md"
                    />

                    <span class="absolute -right-1 -bottom-1">
                        <span
                            class="flex h-3.5 w-3.5 items-center justify-center rounded-full bg-chatsidebar"
                        >
                            <span
                                class="flex h-2 w-2 rounded-full border border-gray-400 bg-chatsidebar"
                            ></span>
                        </span>
                    </span>
                </span>

                <span v-text="user.name"></span>
            </span>
        </li>
    </ul>
</template>
