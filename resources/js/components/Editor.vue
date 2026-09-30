<script setup lang="ts">
import Smile from '@/components/icons/Smile.vue';
import Airplane from './icons/Airplane.vue';
import { Channel } from '@/types';
import { useForm } from '@inertiajs/vue3';
import { send } from '@/routes/channels';

const props = defineProps<{
    channel: Channel;
}>();

const form = useForm({
    content: '',
});

const handleSubmit = () => {
    form.submit(send(props.channel.id), {
        onSuccess: () => form.resetAndClearErrors(),
    });
};
</script>

<template>
    <div class="w-full rounded-md border border-gray-400 shadow-md">
        <div class="flex rounded-t-md bg-gray-100 p-2">
            <button>
                <Smile class="size-6 text-gray-400" />
            </button>
        </div>

        <form @submit.prevent="handleSubmit">
            <input
                v-model="form.content"
                class="h-12 w-full resize-none border-none p-3 text-gray-700 focus:ring-0"
                :placeholder="`Message #${channel.name}`"
                autofocus
            />

            <p v-if="form.errors.content" class="text-sm text-fuchsia-600">
                {{ form.errors.content }}
            </p>
        </form>

        <div class="flex justify-end p-2">
            <button @click="handleSubmit">
                <Airplane class="size-6 text-gray-400" />
            </button>
        </div>
    </div>
</template>
