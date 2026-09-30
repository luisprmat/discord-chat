<script setup lang="ts">
import Channel from '@/components/Channel.vue';
import Airplane from '@/components/icons/Airplane.vue';
import ChevronDown from '@/components/icons/ChevronDown.vue';
import Hashtag from '@/components/icons/Hashtag.vue';
import Message from '@/components/icons/Message.vue';
import Minus from '@/components/icons/Minus.vue';
import Pencil from '@/components/icons/Pencil.vue';
import Plus from '@/components/icons/Plus.vue';
import Users from '@/components/Users.vue';
import { store } from '@/routes/channels';
import { Channel as ChannelType, Message as MessageType, User } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { nextTick, ref, watch } from 'vue';

type Props = {
    channels: ChannelType[];
    channel: ChannelType;
    messages: MessageType[];
    users: User[];
    subscribers: User[];
    subscribed: boolean;
};

const props = defineProps<Props>();

const open = ref(true);
const openChannelForm = ref(false);
const openUsers = ref(true);
const channelInput = ref<HTMLInputElement | null>(null);

watch(openChannelForm, async (isOpen) => {
    await nextTick();

    if (isOpen) {
        channelInput.value?.focus();
    } else {
        channelInput.value?.blur();
    }
});

const form = useForm({
    name: '',
}).withPrecognition(store());

const handleSubmit = () => {
    form.submit(store(), {
        preserveScroll: true,
        onSuccess: () => {
            openChannelForm.value = false;
            form.reset();
        },
    });
};
</script>

<template>
    <Head :title="`#${channel.name}`" />

    <div
        class="flex w-full rounded-lg bg-fuchsia-800"
        style="height: calc(100vh - 70px)"
    >
        <!-- Channels -->
        <div
            class="shrink-0 scrollbar-thin scrollbar-thumb-fuchsia-800 scrollbar-track-fuchsia-100 overflow-y-scroll rounded-l-lg bg-chatsidebar"
        >
            <div class="flex h-14 items-center gap-x-32 border-b p-4">
                <h1 class="text-xl font-bold">Laravel</h1>

                <Pencil class="h-6 w-6 text-gray-500" />
            </div>

            <div class="flex flex-col gap-y-6 p-2 text-lg text-gray-700">
                <ul class="flex flex-col gap-y-3">
                    <li class="flex items-center gap-x-2 rounded-md px-4 py-1">
                        <Message class="h-6 w-6 text-gray-700" />

                        Threads
                    </li>

                    <li class="flex items-center gap-x-2 rounded-md px-4 py-1">
                        <Airplane class="h-6 w-6 text-gray-700" />

                        Drafts & sent
                    </li>
                </ul>

                <ul class="flex flex-col gap-y-2">
                    <li class="flex flex-col">
                        <div class="flex">
                            <button
                                @click="open = !open"
                                class="flex items-center gap-x-2 rounded-md px-4 py-1 hover:bg-white/30"
                            >
                                <ChevronDown class="size-3 text-gray-700" />
                            </button>

                            <button
                                class="flex w-full items-center justify-between"
                            >
                                Channels

                                <Plus
                                    v-show="!openChannelForm"
                                    class="size-5 text-gray-700"
                                    @click="openChannelForm = !openChannelForm"
                                />

                                <Minus
                                    v-show="openChannelForm"
                                    class="size-5 text-gray-700"
                                    @click="openChannelForm = !openChannelForm"
                                />
                            </button>
                        </div>

                        <form
                            v-show="openChannelForm"
                            @submit.prevent="handleSubmit"
                        >
                            <input
                                v-model="form.name"
                                @input="form.validate('name')"
                                class="w-full rounded-md border border-gray-300 px-3 py-1"
                                ref="channelInput"
                                placeholder="general"
                            />
                            <p
                                v-if="form.invalid('name')"
                                class="text-sm text-fuchsia-700"
                            >
                                {{ form.errors.name }}
                            </p>
                        </form>
                    </li>

                    <li>
                        <ul v-show="open">
                            <li v-for="chnl in channels">
                                <Link
                                    :href="`/${chnl.name}`"
                                    class="flex items-center gap-x-2 rounded-md px-4 py-1 hover:bg-fuchsia-900 hover:text-white"
                                    :class="{
                                        'bg-fuchsia-900 text-white':
                                            channel.name === chnl.name,
                                    }"
                                >
                                    <Hashtag class="size-4" />

                                    {{ chnl.name }}
                                </Link>
                            </li>
                        </ul>
                    </li>
                </ul>

                <ul class="flex flex-col gap-y-2">
                    <li>
                        <button
                            @click="openUsers = !openUsers"
                            class="flex w-full items-center gap-x-2 rounded-md px-4 py-1 hover:bg-white/30"
                        >
                            <ChevronDown class="size-3 text-gray-700" />

                            Direct Messages
                        </button>
                    </li>

                    <li>
                        <Users :open-users :users />
                    </li>
                </ul>
            </div>
        </div>

        <div class="flex w-full flex-col rounded-r-lg bg-white">
            <div class="flex h-14 items-center justify-between border-b p-3">
                <h1 class="text-xl font-bold"># {{ channel.name }}</h1>

                <div class="flex rounded border p-1">
                    <div class="flex items-center -space-x-1">
                        <template v-for="subscriber in subscribers.slice(0, 3)">
                            <img
                                :src="subscriber.avatar"
                                :alt="subscriber.name"
                                class="size-5 rounded-md border border-white"
                            />
                        </template>

                        <span class="px-2">{{ subscribers.length }}</span>
                    </div>
                </div>
            </div>

            <!-- Channel -->
            <Channel :channel :messages :subscribed />
        </div>
    </div>
</template>
