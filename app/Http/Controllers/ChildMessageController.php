<?php



namespace App\Http\Controllers;



use App\Models\CaseFile;

use App\Models\Message;

use App\Models\Thread;

use App\Models\User;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\DB;



class ChildMessageController extends Controller

{

    public function index()

    {

        $child = Auth::user();



        $caseFile = $child->caseFile

            ?? $child->childCase

            ?? CaseFile::where('youngpersonid', $child->id)->first()

            ?? CaseFile::where('young_person_id', $child->id)->first();



        if (!$caseFile) {

            abort(404, 'No case file found for this child.');

        }



        $socialWorkerLink = DB::table('case_user')

            ->where('case_id', $caseFile->id)

            ->where('role', 'social_worker')

            ->first();



        if (!$socialWorkerLink) {

            abort(404, 'No social worker linked to this case.');

        }



        $socialWorker = User::find($socialWorkerLink->user_id);



        if (!$socialWorker) {

            abort(404, 'Linked social worker not found.');

        }



        $thread = Thread::firstOrCreate([

            'child_id' => $child->id,

            'social_worker_id' => $socialWorker->id,

            'conversation_type' => 'young_person',

        ]);



        $messages = $thread->messages()

            ->orderBy('created_at', 'asc')

            ->get();



        $thread->messages()

            ->where('sender_id', '!=', $child->id)

            ->whereNull('read_at')

            ->update([

                'read_at' => now(),

            ]);



        return view('child.messages', [

            'thread' => $thread,

            'messages' => $messages,

            'socialWorker' => $socialWorker,

            'caseFile' => $caseFile,

        ]);

    }



    public function store(Request $request, Thread $thread)

    {

        $request->validate([

            'body' => ['required', 'string', 'max:2000'],

        ]);



        $userId = Auth::id();



        $allowed =

            ((int) $thread->child_id === (int) $userId) ||

            ((int) $thread->social_worker_id === (int) $userId);



        abort_unless($allowed, 403);



        Message::create([

            'thread_id' => $thread->id,

            'sender_id' => $userId,

            'body' => $request->body,

        ]);



        return redirect()->route('child.messages.index');

    }

}

