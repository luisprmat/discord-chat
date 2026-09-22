<?php

namespace App\Models;

use App\Events\MessageSent;
use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Unguarded]
class Channel extends Model
{
    public function subscribers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'subscriptions')
            ->withTimestamps();
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function isSubscribed(User $user): bool
    {
        return $this->subscribers->contains($user);
    }

    public function getSubscribers(): Collection
    {
        return $this->subscribers()->inRandomOrder()->get();
    }

    public function getMessages(): Collection
    {
        return $this->messages()->with('user')->get();
    }

    public function subscribe(User $user): bool
    {
        return $this->subscribers()->attach($user) === null;
    }

    public function send(User $user, string $message): void
    {
        if (! $message) {
            return;
        }

        $message = $this->messages()->create([
            'user_id' => $user->id,
            'content' => $message,
            'sent_at' => now(),
        ]);

        MessageSent::dispatch($message);
    }
}
