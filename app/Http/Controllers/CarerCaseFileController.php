<?php



namespace App\Http\Controllers;



use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;



class CarerCaseFileController extends Controller

{

    public function show(Request $request)

    {

        $user = $request->user();



        if (($user->role ?? null) !== 'carer') {

            abort(403);

        }



        $cases = DB::table('case_user')

            ->join('case_files', 'case_user.case_id', '=', 'case_files.id')

            ->where('case_user.user_id', $user->id)

            ->select('case_files.*')

            ->orderByDesc('case_files.id')

            ->get();



        return view('carer.case-file.show', compact('cases', 'user'));

    }

}