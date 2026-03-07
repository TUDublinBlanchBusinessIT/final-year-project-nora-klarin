<?php



namespace App\Http\Controllers;



use App\Models\Document;

use App\Models\CaseFile;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Storage;



class CarerDocumentController extends Controller

{

    private function ensureCarer(Request $request)

    {

        $user = $request->user();



        if (($user->role ?? null) !== 'carer') {

            abort(403);

        }



        return $user;

    }



    public function index(Request $request)

    {

        $user = $this->ensureCarer($request);



        $docs = Document::where('uploaded_by', $user->id)

            ->latest()

            ->get();



        $caseFiles = CaseFile::latest()->get();



        return view('carer.documents.index', compact('docs', 'user', 'caseFiles'));

    }



    public function store(Request $request)

    {

        $user = $this->ensureCarer($request);



        $data = $request->validate([

            'case_file_id' => ['required', 'exists:case_files,id'],

            'title' => ['required', 'string', 'max:255'],

            'file' => ['required', 'file', 'mimes:pdf', 'max:10240'],

        ]);



        $file = $data['file'];

        $path = $file->store("documents/{$user->id}", 'local');



        Document::create([

            'case_file_id' => $data['case_file_id'],

            'uploaded_by' => $user->id,

            'title' => $data['title'],

            'file_path' => $path,

            'file_type' => $file->getClientOriginalExtension(),

        ]);



        return back()->with('status', 'Document uploaded successfully.');

    }



    public function download(Request $request, Document $doc)

    {

        $user = $this->ensureCarer($request);



        if ($doc->uploaded_by !== $user->id) {

            abort(403);

        }



        return Storage::disk('local')->download(

            $doc->file_path,

            $doc->title . '.' . ($doc->file_type ?: 'pdf')

        );

    }



    public function destroy(Request $request, Document $doc)

    {

        $user = $this->ensureCarer($request);



        if ($doc->uploaded_by !== $user->id) {

            abort(403);

        }



        Storage::disk('local')->delete($doc->file_path);

        $doc->delete();



        return back()->with('status', 'Document deleted.');

    }

}