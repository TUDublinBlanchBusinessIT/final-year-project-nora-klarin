<?php



namespace App\Http\Controllers;



use App\Models\Message;

use App\Models\Thread;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;



class SocialWorkerMessageController extends Controller

{

    public function index(Request $request)

    {

        $socialWorker = Auth::user();



        if (($socialWorker->role ?? null) !== 'social_worker') {

            abort(403);

        }



        $selectedThreadId = (int) $request->query('thread', 0);



        $threads = Thread::with([

                'child:id,name,email',

                'carer:id,name,email',

                'messages' => function ($query) {

                    $query->latest('created_at');

                },

            ])

            ->where('social_worker_id', $socialWorker->id)

            ->orderByDesc('updated_at')

            ->get()

            ->map(function ($thread) use ($socialWorker) {

                $lastMessage = $thread->messages->first();



                $partner = $thread->conversation_type === 'carer'

                    ? $thread->carer

                    : $thread->child;



                $thread->partner_name = $partner->name ?? 'Unknown User';

                $thread->partner_email = $partner->email ?? null;

                $thread->partner_role_label = $thread->conversation_type === 'carer'

                    ? 'Carer'

                    : 'Young Person';



                $thread->last_body = $lastMessage?->body;

                $thread->last_at = $lastMessage?->created_at;

                $thread->unread_count = Message::query()

                    ->where('thread_id', $thread->id)

                    ->where('sender_id', '!=', $socialWorker->id)

                    ->whereNull('read_at')

                    ->count();



                return $thread;

            })

            ->values();



        if ($selectedThreadId === 0 && $threads->count() > 0) {

            $selectedThreadId = $threads->first()->id;

        }



        $selectedThread = $selectedThreadId

            ? Thread::with([

                'child:id,name,email',

                'carer:id,name,email',

            ])->where('social_worker_id', $socialWorker->id)->find($selectedThreadId)

            : null;



        $messages = collect();



        if ($selectedThread) {

            $messages = Message::query()

                ->with(['sender:id,name'])

                ->where('thread_id', $selectedThread->id)

                ->orderBy('created_at', 'asc')

                ->get();



            Message::query()

                ->where('thread_id', $selectedThread->id)

                ->where('sender_id', '!=', $socialWorker->id)

                ->whereNull('read_at')

                ->update([

                    'read_at' => now(),

                ]);

        }



        return view('socialworker.messages', [

            'threads' => $threads,

            'selectedThread' => $selectedThread,

            'messages' => $messages,

        ]);

    }



    public function store(Request $request, Thread $thread)

    {

        $socialWorker = Auth::user();



        if (($socialWorker->role ?? null) !== 'social_worker') {

            abort(403);

        }



        $request->validate([

            'body' => ['required', 'string', 'max:2000'],

        ]);



        $thread = Thread::findOrFail($thread->id);



        if ((int) $thread->social_worker_id !== (int) $socialWorker->id) {

            abort(403, 'This thread does not belong to this social worker.');

        }



        Message::create([

            'thread_id' => $thread->id,

            'sender_id' => $socialWorker->id,

            'body' => $request->body,

        ]);



        $thread->touch();



        return redirect()->route('socialworker.messages.index', [

            'thread' => $thread->id,

        ]);

    }

}

