@if(auth()->user()->role === 'social_worker')



    {{-- Placement Modal --}}

    <div class="modal fade" id="addPlacementModal" tabindex="-1" aria-labelledby="addPlacementModalLabel" aria-hidden="true">

        <div class="modal-dialog modal-lg modal-dialog-centered">

            <div class="modal-content rounded-3xl border-0 shadow-lg">

                <div class="modal-header">

                    <h5 class="modal-title" id="addPlacementModalLabel">Placement History & Add Placement</h5>

                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

                </div>



                <div class="modal-body">

                    <h6 class="mb-3">Placement History</h6>



                    @if($case->placements->isEmpty())

                        <p class="text-muted">No placements recorded yet.</p>

                    @else

                        @foreach($case->placements->sortByDesc('start_date') as $placementRecord)

                            <div class="card mb-3 p-3">

                                <strong>{{ $placementRecord->type ?? '-' }}</strong>

                                <p class="mb-1">Location: {{ $placementRecord->address ?? '-' }}</p>

                                <p class="mb-1">

                                    {{ $placementRecord->start_date ?? '-' }} -

                                    {{ $placementRecord->end_date ?? 'Current' }}

                                </p>



                                @if($placementRecord->carer)

                                    <p class="mb-1">Carer: {{ $placementRecord->carer->name }}</p>

                                @endif



                                @if($placementRecord->notes)

                                    <p class="mb-0">Notes: {{ $placementRecord->notes }}</p>

                                @endif

                            </div>

                        @endforeach

                    @endif



                    <hr class="my-4">



                    <h6 class="mb-3">Add New Placement</h6>



                    <form action="{{ route('socialworker.cases.placements.store', $case) }}" method="POST">

                        @csrf



                        <div class="mb-3">

                            <input type="text" name="type" placeholder="Type" class="form-control" required>

                        </div>



                        <div class="mb-3">

                            <input type="text" name="location" placeholder="Location" class="form-control" required>

                        </div>



                        <div class="mb-3">

                            <input type="date" name="start_date" class="form-control" required>

                        </div>



                        <div class="mb-3">

                            <input type="date" name="end_date" class="form-control">

                        </div>



                        <div class="mb-3">

                            <textarea name="notes" class="form-control" placeholder="Notes" rows="3"></textarea>

                        </div>



                        <div class="text-end">

                            <button type="submit" class="btn btn-primary">Save Placement</button>

                        </div>

                    </form>

                </div>

            </div>

        </div>

    </div>



    {{-- Medical Modal --}}

    <div class="modal fade" id="addMedicalModal" tabindex="-1" aria-labelledby="addMedicalModalLabel" aria-hidden="true">

        <div class="modal-dialog modal-dialog-centered">

            <div class="modal-content rounded-3xl border-0 shadow-lg">

                <form action="{{ route('socialworker.cases.medical.store', $case) }}" method="POST">

                    @csrf



                    <div class="modal-header">

                        <h5 class="modal-title" id="addMedicalModalLabel">Add Medical Info</h5>

                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

                    </div>



                    <div class="modal-body">

                        <div class="mb-3">

                            <input type="text" name="condition" placeholder="Condition" class="form-control" required>

                        </div>



                        <div class="mb-3">

                            <textarea name="notes" placeholder="Notes" class="form-control" rows="3"></textarea>

                        </div>

                    </div>



                    <div class="modal-footer">

                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>

                        <button type="submit" class="btn btn-primary">Save</button>

                    </div>

                </form>

            </div>

        </div>

    </div>



    {{-- Education Modal --}}

    <div class="modal fade" id="addEducationModal" tabindex="-1" aria-labelledby="addEducationModalLabel" aria-hidden="true">

        <div class="modal-dialog modal-dialog-centered">

            <div class="modal-content rounded-3xl border-0 shadow-lg">

                <form action="{{ route('socialworker.cases.education.store', $case) }}" method="POST">

                    @csrf



                    <div class="modal-header">

                        <h5 class="modal-title" id="addEducationModalLabel">Add Education Info</h5>

                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

                    </div>



                    <div class="modal-body">

                        <div class="mb-3">

                            <input type="text" name="school_name" placeholder="School Name" class="form-control" required>

                        </div>



                        <div class="mb-3">

                            <input type="text" name="grade" placeholder="Grade" class="form-control">

                        </div>



                        <div class="mb-3">

                            <textarea name="notes" placeholder="Notes" class="form-control" rows="3"></textarea>

                        </div>

                    </div>



                    <div class="modal-footer">

                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>

                        <button type="submit" class="btn btn-primary">Save</button>

                    </div>

                </form>

            </div>

        </div>

    </div>



    {{-- Document Modal --}}

    <div class="modal fade" id="addDocumentModal" tabindex="-1" aria-labelledby="addDocumentModalLabel" aria-hidden="true">

        <div class="modal-dialog modal-dialog-centered">

            <div class="modal-content rounded-3xl border-0 shadow-lg">

                <form action="{{ route('socialworker.cases.documents.store', $case) }}" method="POST" enctype="multipart/form-data">

                    @csrf



                    <div class="modal-header">

                        <h5 class="modal-title" id="addDocumentModalLabel">Upload Document</h5>

                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

                    </div>



                    <div class="modal-body">

                        <div class="mb-3">

                            <input type="text" name="name" placeholder="Document Name" class="form-control" required>

                        </div>



                        <div class="mb-3">

                            <input type="file" name="file" class="form-control" required>

                        </div>

                    </div>



                    <div class="modal-footer">

                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>

                        <button type="submit" class="btn btn-primary">Upload</button>

                    </div>

                </form>

            </div>

        </div>

    </div>



@endif