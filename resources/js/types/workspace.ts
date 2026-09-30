import { User } from '@/types';

export type Channel = {
    id: number;
    name: string;
    created_at: string;
    updated_at: string;
    [key: string]: unknown;
};

export type Message = {
    id: number;
    channel_id: number;
    user_id: number;
    content: string;
    sent_at: string;
    created_at: string;
    updated_at: string;
    user: User;
    [key: string]: unknown;
};
