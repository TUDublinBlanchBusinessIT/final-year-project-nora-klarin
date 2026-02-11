<?php

namespace App\Http\Controllers;

use App\Models\CarerDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CarerDocumentController extends Controller
{
    private function ensureCarer(Request $request)
    {
        $user = $request->user();
        if (($user->role ?? null) !== 'carer') abort(403);
        return $user;
    }

    public function index(Request $request)
    {
        $user = $this->ensureCarer($request);

        $docs = CarerDocument::where('user_id', $user->id)
            ->latest()
            ->get();

        return view('carer.documents.index', compact('docs', 'user'));
    }

    public function store(Request $request)
    {
        $user = $this->ensureCarer($request);

        $data = $request->validate([
            'title' => ['nullable', 'string', 'max:120'],
            'file' => ['required', 'file', 'mimes:pdf', 'max:10240'], // 10MB
        ]);

        $file = $data['file'];
        $path = $file->store("carer-documents/{$user->id}", 'local'); // private

        CarerDocument::create([
            'user_id' => $user->id,
            'title' => $data['title'] ?? null,
            'original_name' => $file->getClientOriginalName(),
            'path' => $path,
            'size' => $file->getSize() ?? 0,
        ]);

        return back()->with('status', 'PDF uploaded!');
    }

    public function download(Request $request, CarerDocument $doc)
    {
        $user = $this->ensureCarer($request);

        if ($doc->user_id !== $user->id) abort(403);

        return Storage::disk('local')->download($doc->path, $doc->original_name);
    }

    public function destroy(Request $request, CarerDocument $doc)
    {
        $user = $this->ensureCarer($request);

        if ($doc->user_id !== $user->id) abort(403);

        Storage::disk('local')->delete($doc->path);
        $doc->delete();

        return back()->with('status', 'Document deleted.');
    }
}
