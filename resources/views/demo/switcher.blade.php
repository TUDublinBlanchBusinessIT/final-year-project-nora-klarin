

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Demo — Account Switcher</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center p-6">

<div class="max-w-lg w-full space-y-6">

    <div class="text-center">
        <div class="text-4xl mb-3">🔀</div>
        <h1 class="text-2xl font-semibold text-gray-800">Demo Account Switcher</h1>
        <p class="text-sm text-gray-500 mt-1">Click any account to log in instantly.</p>
    </div>

    {{-- Group by role --}}
    @foreach(['young_person' => '👦 Young People', 'social_worker' => '👩‍💼 Social Workers', 'carer' => '🏠 Carers'] as $role => $label)
        @php $roleAccounts = $accounts->where('role', $role) @endphp
        @if($roleAccounts->isNotEmpty())
            <div>
                <p class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-2">{{ $label }}</p>
                <div class="space-y-2">
                    @foreach($roleAccounts as $account)
                        <form action="{{ route('demo.login-as', $account) }}" method="POST">
                            @csrf
                            <button type="submit"
                                    class="w-full flex items-center justify-between bg-white border border-gray-200 rounded-xl px-4 py-3 hover:border-indigo-300 hover:bg-indigo-50 transition text-left group">
                                <div>
                                    <p class="font-medium text-gray-800 group-hover:text-indigo-700">
                                        {{ $account->name }}
                                    </p>
                                    <p class="text-xs text-gray-400">{{ $account->email }}</p>
                                </div>
                                <span class="text-xs font-semibold px-2.5 py-1 rounded-full
                                    {{ $role === 'young_person'  ? 'bg-blue-100 text-blue-700'   :
                                       ($role === 'social_worker' ? 'bg-purple-100 text-purple-700' :
                                                                    'bg-green-100 text-green-700') }}">
                                    {{ str_replace('_', ' ', $role) }}
                                </span>
                            </button>
                        </form>
                    @endforeach
                </div>
            </div>
        @endif
    @endforeach

    {{-- Currently logged in --}}
    @auth
        <div class="bg-amber-50 border border-amber-200 rounded-xl px-4 py-3 text-sm text-amber-700">
            Currently logged in as <strong>{{ auth()->user()->name }}</strong>
            ({{ auth()->user()->role }})
        </div>
    @endauth

</div>

</body>
</html>
