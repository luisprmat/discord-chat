<script setup lang="ts">
import { Channel, Message } from '@/types';
import Editor from '@/components/Editor.vue';
import { Link, usePage } from '@inertiajs/vue3';
import { join } from '@/routes/channels';
import { computed, onMounted, ref, useTemplateRef, watch } from 'vue';
import { useEcho } from '@laravel/echo-vue';
import { trans } from 'laravel-vue-i18n';

type MessageData = {
    message: Message;
};

type TypingData = {
    id: number;
    name: string;
};

const props = defineProps<{
    channel: Channel;
    messages: Message[];
    subscribed: boolean;
}>();

const page = usePage();
const user = computed(() => page.props.auth.user);

const isTyping = ref(false);
const usersTyping = ref<TypingData[]>([]);
const debouncer = ref<number | null>(null);

const messagesContainer = useTemplateRef<HTMLDivElement>('messagesContainer');

const scrollToBottom = (): void => {
    if (messagesContainer.value) {
        messagesContainer.value.scrollTo({
            top: messagesContainer.value.scrollHeight,
            behavior: 'smooth',
        });
    }
};

const { channel: socketChannel } = useEcho<MessageData>(
    `channels.${props.channel.id}`,
    'MessageSent',
    (e) => {
        const messageExists = props.messages.some((m) => m.id === e.message.id);

        if (!messageExists) {
            props.messages.push(e.message);
        }
    },
);

socketChannel().listenForWhisper('StartTyping', (event: TypingData) => {
    usersTyping.value.push(event);
});

socketChannel().listenForWhisper('StopTyping', (event: TypingData) => {
    usersTyping.value = usersTyping.value.filter(
        (user) => user.id !== event.id,
    );
});

onMounted(scrollToBottom);

watch(() => [props.channel.id, props.messages.length], scrollToBottom, {
    flush: 'post',
});

const debounce = (startCallback: () => void, stopCallback: () => void) => {
    if (debouncer.value) {
        clearTimeout(debouncer.value);
    }

    debouncer.value = setTimeout(() => {
        isTyping.value = false;
        stopCallback();
    }, 2000);

    if (!isTyping.value) {
        isTyping.value = true;
        startCallback();
    }
};

const typing = () => {
    debounce(
        () => {
            socketChannel().whisper('StartTyping', {
                id: user.value.id,
                name: user.value.name,
            });
        },
        () => {
            socketChannel().whisper('StopTyping', {
                id: user.value.id,
                name: user.value.name,
            });
        },
    );
};

const typingUsers = (): string => {
    switch (usersTyping.value.length) {
        case 0:
            return '';
        case 1:
            return `${trans(':userA is typing', { userA: usersTyping.value[0].name })}...`;
        case 2:
            return `${trans(':userA and :userB are typing', { userA: usersTyping.value[0].name, userB: usersTyping.value[1].name })}...`;
        default:
            return `${trans('Several people are typing')}...`;
    }
};
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
            <template v-if="subscribed">
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
            </template>
        </div>

        <div class="flex w-full">
            <div v-if="subscribed" class="flex w-full flex-col gap-y-1">
                <Editor :channel @typing="typing" />

                <!-- Typing Indicator -->
                <span
                    class="block shrink-0 text-xs text-gray-500 after:content-['\200b']"
                    >{{ typingUsers() }}</span
                >
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
