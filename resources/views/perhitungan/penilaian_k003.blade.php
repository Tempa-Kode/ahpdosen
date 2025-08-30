@extends("app")

@section("judul", "Penilaian Kriteria K003")

@section("konten")
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Penilaian - {{ $kriteria ?? "K003" }} - {{ $kriteria_nama ?? "" }}</h4>
                        <p class="text-muted">Total data:
                            <strong>{{ $count ?? (is_array($data) ? count($data) : 0) }}</strong></p>

                        <div class="table-responsive">
                            <table id="datatable" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Dosen (NIDN)</th>
                                        <th>Prodi</th>
                                        <th>Nilai</th>
                                        <th>Skala Interval</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $no = 1; @endphp
                                    @if (!empty($data) && is_iterable($data))
                                        @foreach ($data as $item)
                                            <tr>
                                                <td>{{ $no++ }}</td>
                                                <td>
                                                    {{ data_get($item, "dosen.nama_dosen", data_get($item, "dosen.nama", "")) }}
                                                    <div class="small text-muted">{{ data_get($item, "dosen.nidn", "") }}
                                                    </div>
                                                </td>
                                                <td>{{ data_get($item, "dosen.prodi", "") }}</td>
                                                <td>{{ $item["nilai"] ?? ($item->nilai ?? "") }}</td>
                                                <td>{{ $item["skala_interval"] ?? ($item->skala_interval ?? "") }}</td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section("js")
    <script>
        // DataTable already initialized globally in app template, but ensure re-init if needed
        $(document).ready(function() {
            if ($.fn.DataTable.isDataTable('#datatable')) {
                $('#datatable').DataTable().draw();
            } else {
                $('#datatable').DataTable({
                    "pageLength": 25
                });
            }
        });
    </script>
@endsection
