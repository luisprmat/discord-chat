<script setup lang="ts">
import { Channel, Message } from '@/types';
import Editor from '@/components/Editor.vue';
import { Link } from '@inertiajs/vue3';
import { join } from '@/routes/channels';
import { onMounted, useTemplateRef, watch } from 'vue';
import { useEchoPublic } from '@laravel/echo-vue';

type MessageData = {
    message: Message;
};

const props = defineProps<{
    channel: Channel;
    messages: Message[];
    subscribed: boolean;
}>();

const messagesContainer = useTemplateRef<HTMLDivElement>('messagesContainer');

const scrollToBottom = (): void => {
    if (messagesContainer.value) {
        messagesContainer.value.scrollTo({
            top: messagesContainer.value.scrollHeight,
            behavior: 'smooth',
        });
    }
};

useEchoPublic<MessageData>(
    `channels.${props.channel.id}`,
    'MessageSent',
    (e) => {
        const messageExists = props.messages.some((m) => m.id === e.message.id);

        if (!messageExists) {
            props.messages.push(e.message);
        }
    },
);

onMounted(scrollToBottom);

watch(() => [props.channel.id, props.messages.length], scrollToBottom, {
    flush: 'post',
});
</script>

<template>
    <div
        class="flex h-full w-full flex-col justify-between p-4 pb-2"
        style="height: calc(100vh - 125px)"
    >
        <div
            ref="messagesContainer"
            class="mb-4 flex h-full grow scrollbar-thin flex-col overflow-y-scroll"
        >
            <span
                class="mt-auto w-full py-4 text-center text-lg"
                :class="{ 'mb-4 border-b': messages.length > 0 }"
                v-html="
                    $t('This is the very beginning of the :name channel.', {
                        name: `<strong>${channel.name}</strong>`,
                    })
                "
            ></span>

            <div class="flex gap-x-2" v-for="message in messages">
                <img
                    :src="message.user.avatar"
                    :alt="message.user.name"
                    class="size-10 rounded-md"
                />

                <div>
                    <div class="flex items-center gap-x-2">
                        <span
                            class="text-lg font-bold"
                            v-text="message.user.name"
                        ></span>

                        <time
                            :datetime="message.sent_at"
                            class="text-sm text-gray-600"
                            v-text="message.sent_at"
                        ></time>
                    </div>

                    <div v-html="message.content" class="text-lg"></div>
                </div>
            </div>
        </div>

        <div class="flex w-full">
            <div v-if="subscribed" class="flex w-full flex-col gap-y-1">
                <Editor :channel />

                <!-- Typing Indicator -->
                <span
                    class="block shrink-0 text-xs text-gray-500 after:content-['\200b']"
                ></span>
            </div>

            <div
                v-else
                class="flex grow flex-col items-center justify-center gap-y-4 rounded-md border bg-gray-100 p-6"
            >
                <span class="text-lg font-bold">#{{ channel.name }}</span>

                <Link
                    :href="join(channel.id)"
                    method="post"
                    class="rounded-md bg-green-800 px-4 py-2 text-base text-white"
                    as="button"
                >
                    {{ $t('Join Channel') }}
                </Link>
            </div>
        </div>
    </div>
</template>
