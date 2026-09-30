<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreChannelRequest;
use App\Models\Channel;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class ChannelController extends Controller
{
    public function index(Request $request, Channel $channel): Response
    {
        return Inertia::render('workspace/Index', [
            'channels' => Channel::all()->toArray(),
            'channel' => $channel->toArray(),
            'messages' => $channel->getMessages()->toArray(),
            'users' => User::orderBy('name')->get(),
            'subscribers' => $channel->getSubscribers(),
            'subscribed' => $channel->isSubscribed(Auth::user()),
        ]);
    }

    public function store(StoreChannelRequest $request): RedirectResponse
    {
        Channel::create($request->validated());

        return back();
    }

    public function join(Request $request, Channel $channel): RedirectResponse
    {
        $channel->subscribe(Auth::user());

        return back();
    }

    public function send(Request $request, Channel $channel): RedirectResponse
    {
        $data = $request->validate([
            'content' => ['required'],
        ]);

        $channel->send(Auth::user(), $data['content']);

        return back();
    }
}
