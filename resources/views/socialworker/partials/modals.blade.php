{{-- Placement History & Add Placement --}}
@if(auth()->user()->role === 'social_worker')
<div class="modal fade" id="addPlacementModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5>Placement History & Add Placement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                {{-- Placement History --}}
                <h6>Placement History</h6>
                @if($case->placementsHistory->isEmpty())
                    <p class="text-muted">No placements recorded yet.</p>
                @else
                    @foreach($case->placementsHistory->sortByDesc('start_date') as $placementRecord)
                        <div class="card mb-2 p-2">
                            <strong>{{ $placementRecord->placement->type }}</strong>
                            <p>Location: {{ $placementRecord->placement->address }}</p>
                            <p>{{ $placementRecord->start_date }} - {{ $placementRecord->end_date ?? 'Current' }}</p>
                            @if($placementRecord->placement->carer)
                                <p>Carer: {{ $placementRecord->placement->carer->name }}</p>
                            @endif
                            @if($placementRecord->notes)
                                <p>Notes: {{ $placementRecord->notes }}</p>
                            @endif
                        </div>
                    @endforeach
                @endif

                <hr>

                {{-- Add Placement Form --}}
                <h6>Add New Placement</h6>
                <form action="{{ route('case.addPlacement', $case) }}" method="POST">
                    @csrf
                    <div class="mb-2">
                        <input type="text" name="type" placeholder="Type" class="form-control" required>
                    </div>
                    <div class="mb-2">
                        <input type="text" name="location" placeholder="Location" class="form-control" required>
                    </div>
                    <div class="mb-2">
                        <input type="date" name="start_date" class="form-control" required>
                    </div>
                    <div class="mb-2">
                        <input type="date" name="end_date" class="form-control">
                    </div>
                    <div class="mb-2">
                        <textarea name="notes" class="form-control" placeholder="Notes"></textarea>
                    </div>
                    <div class="text-end">
                        <button class="btn btn-primary">Save Placement</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endif

{{-- Add Medical --}}
<div class="modal fade" id="addMedicalModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('cases.medical.store', $case) }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header"><h5>Add Medical Info</h5></div>
                <div class="modal-body">
                    <input type="text" name="condition" placeholder="Condition" class="form-control mb-2" required>
                    <textarea name="notes" placeholder="Notes" class="form-control"></textarea>
                </div>
                <div class="modal-footer"><button class="btn btn-primary">Save</button></div>
            </div>
        </form>
    </div>
</div>

{{-- Add Education --}}
<div class="modal fade" id="addEducationModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('cases.education.store', $case) }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header"><h5>Add Education Info</h5></div>
                <div class="modal-body">
                    <input type="text" name="school_name" placeholder="School Name" class="form-control mb-2" required>
                    <input type="text" name="grade" placeholder="Grade" class="form-control mb-2">
                    <textarea name="notes" placeholder="Notes" class="form-control"></textarea>
                </div>
                <div class="modal-footer"><button class="btn btn-primary">Save</button></div>
            </div>
        </form>
    </div>
</div>

{{-- Add Document --}}
<div class="modal fade" id="addDocumentModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('cases.documents.store', $case) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-content">
                <div class="modal-header"><h5>Upload Document</h5></div>
                <div class="modal-body">
                    <input type="text" name="name" placeholder="Document Name" class="form-control mb-2" required>
                    <input type="file" name="file" class="form-control" required>
                </div>
                <div class="modal-footer"><button class="btn btn-primary">Upload</button></div>
            </div>
        </form>
    </div>
</div>