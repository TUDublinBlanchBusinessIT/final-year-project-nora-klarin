<?php



namespace App\Http\Controllers;



use App\Models\CaseFile;

use App\Models\Message;

use App\Models\Thread;

use App\Models\User;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\DB;



class CarerMessageController extends Controller

{

    public function index(Request $request)

    {

        $carer = Auth::user();



        if (($carer->role ?? null) !== 'carer') {

            abort(403);

        }



        $caseLink = DB::table('case_user')

            ->where('user_id', $carer->id)

            ->where('role', 'carer')

            ->first();



        if (!$caseLink) {

            abort(404, 'No case linked to this carer.');

        }



        $caseFile = CaseFile::find($caseLink->case_id);



        if (!$caseFile) {

            abort(404, 'Case file not found.');

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

            abort(404, 'Social worker record not found.');

        }



        $child = null;



        if (!empty($caseFile->young_person_id)) {

            $child = User::find($caseFile->young_person_id);

        } elseif (!empty($caseFile->youngpersonid)) {

            $child = User::find($caseFile->youngpersonid);

        }



        if (!$child) {

            abort(404, 'No child linked to this case.');

        }



        $thread = Thread::firstOrCreate([

            'child_id' => $child->id,

            'carer_id' => $carer->id,

            'social_worker_id' => $socialWorker->id,
            
            'conversation_type' => 'carer',

        ]);



        $messages = Message::query()

            ->with(['sender:id,name'])

            ->where('thread_id', $thread->id)

            ->orderBy('created_at', 'asc')

            ->get();



        Message::query()

            ->where('thread_id', $thread->id)

            ->where('sender_id', '!=', $carer->id)

            ->whereNull('read_at')

            ->update([

                'read_at' => now(),

            ]);



        return view('carer.messages.index', [

            'thread' => $thread,

            'messages' => $messages,

            'socialWorker' => $socialWorker,

            'caseFile' => $caseFile,

            'child' => $child,

        ]);

    }



    public function store(Request $request, Thread $thread)

    {

        $carer = Auth::user();



        if (($carer->role ?? null) !== 'carer') {

            abort(403);

        }



        $request->validate([

            'body' => ['required', 'string', 'max:2000'],

        ]);



        $thread = Thread::findOrFail($thread->id);



        if ((int) $thread->carer_id !== (int) $carer->id) {

            abort(403, 'This thread does not belong to this carer.');

        }



        Message::create([

            'thread_id' => $thread->id,

            'sender_id' => $carer->id,

            'body' => $request->body,

        ]);



        $thread->touch();



        return redirect()->route('carer.messages.index');

    }

}

