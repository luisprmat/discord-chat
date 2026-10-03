import { User } from '@/types';
import { echo } from '@laravel/echo-vue';
import { readonly, ref } from 'vue';

type PresenceUser = Pick<User, 'id' | 'name'>;

export type UseOnlineUsersReturn = {
    isOnline: (user: Pick<User, 'id'>) => boolean;
};

const WORKSPACE_CHANNEL = 'workspace';

const onlineUserIds = ref<Set<User['id']>>(new Set());

const markOnline = (presenceUsers: PresenceUser[]): void => {
    const ids = new Set(onlineUserIds.value);
    presenceUsers.forEach((user) => ids.add(user.id));
    onlineUserIds.value = ids;
};

const markOffline = (presenceUsers: PresenceUser[]): void => {
    const ids = new Set(onlineUserIds.value);
    presenceUsers.forEach((user) => ids.delete(user.id));
    onlineUserIds.value = ids;
};

/**
 * Joins the workspace presence channel. Meant to be called when ChatLayout
 * mounts: Inertia keeps the layout alive between chat pages and unmounts it
 * when navigating to a page with another layout.
 */
export const joinWorkspace = (): void => {
    echo()
        .join(WORKSPACE_CHANNEL)
        .here((users: PresenceUser[]) => markOnline(users))
        .joining((user: PresenceUser) => markOnline([user]))
        .leaving((user: PresenceUser) => markOffline([user]));
};

/**
 * Leaves the workspace presence channel so other users see this one offline.
 */
export const leaveWorkspace = (): void => {
    echo().leave(WORKSPACE_CHANNEL);
    onlineUserIds.value = new Set();
};

export const useOnlineUsers = (): UseOnlineUsersReturn => {
    const ids = readonly(onlineUserIds);

    return {
        isOnline: (user) => ids.value.has(user.id),
    };
};
