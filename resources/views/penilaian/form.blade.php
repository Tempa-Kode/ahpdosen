{{-- filepath: resources/views/penilaian/form.blade.php --}}
@extends("app")

@section("konten")
    <nav class="page-breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Data</a></li>
            <li class="breadcrumb-item"><a href="{{ route('dosen.index') }}">Dosen</a></li>
            <li class="breadcrumb-item active" aria-current="page">Penilaian</li>
        </ol>
    </nav>

    <div class="page-content">
        <h6 class="card-title">Formulir Penilaian Dosen: {{ $dosen->nama_dosen }}</h6>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        {{-- PENTING: beri id agar JS mudah mengikat event --}}
        <form id="form-penilaian" action="{{ route('penilaian.store', $dosen->id) }}" method="POST">
            @csrf

            <div class="accordion" id="accordionKriteria">
                @foreach ($kriterias as $kriteriaIndex => $kriteria)
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading-kriteria-{{ $kriteria->id }}">
                            <button class="accordion-button {{ $kriteriaIndex === 0 ? '' : 'collapsed' }}" type="button"
                                data-bs-toggle="collapse" data-bs-target="#collapse-kriteria-{{ $kriteria->id }}"
                                aria-expanded="{{ $kriteriaIndex === 0 ? 'true' : 'false' }}"
                                aria-controls="collapse-kriteria-{{ $kriteria->id }}">
                                <strong>{{ $kriteria->nama_kriteria }}</strong>
                            </button>
                        </h2>

                        <div id="collapse-kriteria-{{ $kriteria->id }}"
                             class="accordion-collapse collapse {{ $kriteriaIndex === 0 ? 'show' : '' }}"
                             aria-labelledby="heading-kriteria-{{ $kriteria->id }}"
                             data-bs-parent="#accordionKriteria">
                            <div class="accordion-body">

                                @if ($kriteria->nama_kriteria === 'Pendidikan dan Pembelajaran')
                                    <button class="btn btn-primary mb-3" id="tarikData" type="button"
                                            data-nidn="{{ $dosen->nidn }}">
                                        <i class="link-icon text-sm" data-feather="file-text"></i>
                                        Tarik Data Spreadsheet
                                    </button>
                                @endif

                                <div class="accordion" id="accordionIndikator-{{ $kriteria->id }}">
                                    @foreach ($kriteria->indikator as $indikator)

                                        @if ($indikator->subIndikator->isNotEmpty())
                                            {{-- INDIKATOR DENGAN SUB-INDIKATOR --}}
                                            <div class="accordion-item">
                                                <h2 class="accordion-header" id="heading-indikator-{{ $indikator->id }}">
                                                    <button class="accordion-button collapsed" type="button"
                                                            data-bs-toggle="collapse"
                                                            data-bs-target="#collapse-indikator-{{ $indikator->id }}"
                                                            aria-expanded="false"
                                                            aria-controls="collapse-indikator-{{ $indikator->id }}">
                                                        {{ $indikator->nama_indikator }}
                                                    </button>
                                                </h2>

                                                <div id="collapse-indikator-{{ $indikator->id }}"
                                                     class="accordion-collapse collapse"
                                                     aria-labelledby="heading-indikator-{{ $indikator->id }}"
                                                     data-bs-parent="#accordionIndikator-{{ $kriteria->id }}">

                                                    <div class="accordion-body" style="padding-top:.75rem">

                                                        <div class="accordion" id="accordionSubIndikator-{{ $indikator->id }}">
                                                            @foreach ($indikator->subIndikator as $subIndikator)

                                                                @if ($subIndikator->subSubIndikator->isNotEmpty())
                                                                    {{-- SUB-INDIKATOR YG PUNYA SUB-SUB --}}
                                                                    <div class="accordion-item question-item mb-2">
                                                                        <h2 class="accordion-header">
                                                                            <button class="accordion-button collapsed question-title" type="button"
                                                                                    data-bs-toggle="collapse"
                                                                                    data-bs-target="#collapse-sub-{{ $subIndikator->id }}"
                                                                                    aria-expanded="false"
                                                                                    aria-controls="collapse-sub-{{ $subIndikator->id }}">
                                                                            {{ $subIndikator->nama_sub_indikator }}
                                                                            </button>
                                                                        </h2>

                                                                        <div id="collapse-sub-{{ $subIndikator->id }}" class="accordion-collapse collapse">
                                                                            <div class="accordion-body question-body">

                                                                            <div class="table-responsive">
                                                                                <table class="table table-bordered align-middle rating-table">
                                                                                <thead>
                                                                                    <tr>
                                                                                    <th>Sub/Sub-Sub Indikator</th>
                                                                                    <th style="width:15%">Nilai</th>
                                                                                    </tr>
                                                                                </thead>
                                                                                <tbody>
                                                                                    @foreach ($subIndikator->subSubIndikator as $subSub)
                                                                                    @php
                                                                                        // Deteksi apakah subSub punya anak level-3
                                                                                        $hasLevel3 = $subSub->subSubSubIndikator && $subSub->subSubSubIndikator->isNotEmpty();
                                                                                    @endphp

                                                                                    @if ($hasLevel3)
                                                                                        {{-- Judul kelompok (nama subSubIndikator) --}}
                                                                                        <tr class="table-light">
                                                                                        <td colspan="2">
                                                                                            <em>{{ $subSub->nama_sub_sub_indikator }}</em>
                                                                                        </td>
                                                                                        </tr>

                                                                                        {{-- Render baris untuk masing-masing SubSubSubIndikator --}}
                                                                                        @foreach ($subSub->subSubSubIndikator as $subSubSub)
                                                                                        @php $nilai = $subSubSub->penilaians->first()->nilai ?? ''; @endphp
                                                                                        <tr class="rating-row">
                                                                                            <td class="rating-label">
                                                                                            {{ strtolower($subSubSub->nama_sub_sub_sub_indikator) }}
                                                                                            </td>
                                                                                            <td>
                                                                                            <input
                                                                                                type="number"
                                                                                                step="0.01"
                                                                                                class="form-control"
                                                                                                @if ($kriteria->nama_kriteria === 'Pendidikan dan Pembelajaran') readonly @endif
                                                                                                name="nilai[sub_sub_sub_indikator][{{ $subSubSub->id }}]"
                                                                                                value="{{ $nilai }}"
                                                                                                placeholder="Masukkan nilai">
                                                                                            </td>
                                                                                        </tr>
                                                                                        @endforeach

                                                                                    @else
                                                                                        {{-- TANPA LEVEL-3 -> pakai nilai subSubIndikator seperti semula --}}
                                                                                        @php $nilai = $subSub->penilaians->first()->nilai ?? ''; @endphp
                                                                                        <tr class="rating-row">
                                                                                        <td class="rating-label">
                                                                                            {{ strtolower($subSub->nama_sub_sub_indikator) }}
                                                                                        </td>
                                                                                        <td>
                                                                                            <input
                                                                                            type="number"
                                                                                            step="0.01"
                                                                                            class="form-control"
                                                                                            @if ($kriteria->nama_kriteria === 'Pendidikan dan Pembelajaran') readonly @endif
                                                                                            name="nilai[sub_sub_indikator][{{ $subSub->id }}]"
                                                                                            value="{{ $nilai }}"
                                                                                            placeholder="Masukkan nilai">
                                                                                        </td>
                                                                                        </tr>
                                                                                    @endif
                                                                                    @endforeach
                                                                                </tbody>
                                                                                </table>
                                                                            </div>

                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                @else
                                                                    {{-- SUB-INDIKATOR TANPA ANAK -> 1 INPUT SAJA --}}
                                                                    <div class="card mb-2">
                                                                        <div class="card-body">
                                                                            <div class="row g-2 align-items-center">
                                                                                <div class="col-12 col-md-8">
                                                                                    <strong>{{ $subIndikator->nama_sub_indikator }}</strong>
                                                                                </div>
                                                                                <div class="col-12 col-md-4">
                                                                                    @php $nilai = $subIndikator->penilaians->first()->nilai ?? ''; @endphp
                                                                                    <input type="number" step="0.01" class="form-control"
                                                                                           name="nilai[sub_indikator][{{ $subIndikator->id }}]"
                                                                                           value="{{ $nilai }}"
                                                                                           placeholder="Masukkan nilai">
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                @endif

                                                            @endforeach
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>

                                        @else
                                            {{-- INDIKATOR TANPA SUB-INDIKATOR -> 1 INPUT SAJA --}}
                                            <div class="card mb-2">
                                                <div class="card-body">
                                                    <div class="row g-2 align-items-center">
                                                        <div class="col-12 col-md-8">
                                                            <strong>{{ $indikator->nama_indikator }}</strong>
                                                        </div>
                                                        <div class="col-12 col-md-4">
                                                            @php $nilai = $indikator->penilaians->first()->nilai ?? ''; @endphp
                                                            <input type="number" step="0.01" class="form-control"
                                                                   name="nilai[indikator][{{ $indikator->id }}]"
                                                                   value="{{ $nilai }}"
                                                                   placeholder="Masukkan nilai">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                    @endforeach
                                </div>

                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-4">
                <div class="d-grid d-md-block">
                    <button type="submit" class="btn btn-primary btn-lg mb-2 me-md-2">
                        <i class="fas fa-save me-2"></i>Simpan Nilai
                    </button>
                    <a href="{{ route('dosen.index') }}" class="btn btn-secondary btn-lg mb-2 me-md-2">
                        <i class="fas fa-arrow-left me-2"></i>Kembali
                    </a>
                    <button type="button" class="btn btn-info btn-lg mb-2 me-md-2" id="expandAll">
                        <i class="fas fa-expand me-2"></i>Buka Semua
                    </button>
                    <button type="button" class="btn btn-warning btn-lg mb-2" id="collapseAll">
                        <i class="fas fa-compress me-2"></i>Tutup Semua
                    </button>
                </div>
            </div>
        </form>
    </div>

    <style>
        .accordion-button:not(.collapsed){background-color:#e7f3ff;color:#0d6efd;}
        .accordion-item{border:1px solid #dee2e6;margin-bottom:5px;}
        .card{border:1px solid #e3e6f0;}
        .table th{background-color:#f8f9fc;font-weight:600;}
        .form-control:focus{border-color:#80bdff;box-shadow:0 0 0 0.2rem rgba(0,123,255,.25);}
        input[type="number"]{-webkit-appearance:none;-moz-appearance:textfield;appearance:none;background:#fff;border:1px solid #ced4da;border-radius:.375rem;}
        input[type="number"]::-webkit-outer-spin-button,
        input[type="number"]::-webkit-inner-spin-button{-webkit-appearance:none;margin:0;}
    </style>

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function () {
      // ===== Utilities untuk mapping label -> input =====
      const ORDER = ['sangat baik','baik','cukup baik','kurang baik'];
      const norm = s => (s||'').toLowerCase().replace(/\s+/g,' ').replace(/[^\w\s]/g,'').trim();

      // Urutkan baris tabel agar konsisten (opsional)
      function sortRowsIn(tbody){
        const rows = Array.from(tbody.querySelectorAll('tr.rating-row'));
        rows.sort((a,b)=>{
          const ka = ORDER.indexOf(norm(a.querySelector('.rating-label')?.textContent||''));
          const kb = ORDER.indexOf(norm(b.querySelector('.rating-label')?.textContent||''));
          return (ka<0?999:ka) - (kb<0?999:kb);
        });
        rows.forEach(r=>tbody.appendChild(r));
      }

      // Dapatkan peta { 'sangat baik' : <input>, ... } untuk satu section
      function getMap(section){
        const map = {};
        section.querySelectorAll('table.rating-table tbody').forEach(tbody=>{
          sortRowsIn(tbody);
          tbody.querySelectorAll('tr.rating-row').forEach(tr=>{
            const label = norm(tr.querySelector('.rating-label')?.textContent || '');
            if(ORDER.includes(label)){
              const inp = tr.querySelector('input[type="number"]');
              if (inp) map[label] = inp;
            }
          });
        });
        return map;
      }

      // Kumpulkan semua section pertanyaan (sub-indikator yang punya sub-sub)
      function collectSections(){
        return Array.from(document.querySelectorAll('.question-item .question-body'));
      }

      // Isi 4 nilai (SB, B, CB, KB) ke inputs dalam section
      function fillSection(section, vals){
        const map = getMap(section);
        let filled = 0;
        ORDER.forEach((k, i) => {
          if (map[k]) {
            map[k].value = vals[i] ?? 0;
            map[k].dispatchEvent(new Event('input', {bubbles:true}));
            map[k].dispatchEvent(new Event('change', {bubbles:true}));
            map[k].style.borderColor='#28a745';
            map[k].style.backgroundColor='#f8fff9';
            setTimeout(()=>{ map[k].style.borderColor=''; map[k].style.backgroundColor=''; }, 900);
            filled++;
          }
        });
        return filled;
      }

      // ========= Terapkan data dari Spreadsheet ke form =========
      window.applyDataToForm = function () {
        if (!window.spreadsheetData || !Array.isArray(window.spreadsheetData.summary)) {
          alert('Data spreadsheet tidak tersedia'); return;
        }
        const data = window.spreadsheetData.summary;
        const sections = collectSections();
        const n = Math.min(data.length, sections.length);

        let applied = 0;
        for (let i=0;i<n;i++){
          const it = data[i];
          const vals = [it.sangat_baik ?? 0, it.baik ?? 0, it.cukup_baik ?? 0, it.kurang_baik ?? 0];
          applied += fillSection(sections[i], vals);
        }

        // Tutup modal & tampilkan toast
        const modalEl = document.getElementById('spreadsheetModal');
        if (modalEl) { bootstrap.Modal.getInstance(modalEl)?.hide(); }
        const toast = document.createElement('div');
        toast.className='alert alert-success alert-dismissible fade show position-fixed';
        toast.style.cssText='top:20px;right:20px;z-index:9999;max-width:420px;';
        toast.innerHTML = `<strong>Berhasil</strong><br>${applied} input terisi.
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>`;
        document.body.appendChild(toast);
        setTimeout(()=>{toast.remove();}, 5000);
      };

      // ===== Ambil Spreadsheet & Modal pratinjau =====
      const tarikBtn = document.getElementById('tarikData');
      if (tarikBtn) {
        tarikBtn.addEventListener('click', function(e){
          e.preventDefault();
          const nidn = this.getAttribute('data-nidn');
          const btn = this;
          btn.disabled = true;
          btn.innerHTML = '<i class="spinner-border spinner-border-sm me-2"></i>Loading...';

          fetch(`/penilaian/spreadsheet/${nidn}`)
            .then(r=>r.json())
            .then(data=>{
              if(data.status === 'success'){ displaySpreadsheetModal(data.data); }
              else { alert('Error: ' + data.message); }
            })
            .catch(()=>{ alert('Terjadi kesalahan saat mengambil data'); })
            .finally(()=>{ btn.disabled=false; btn.innerHTML='<i class="link-icon text-sm" data-feather="file-text"></i> Tarik Data Spreadsheet';});
        });
      }

      function displaySpreadsheetModal(data){
        const html = `
        <div class="modal fade" id="spreadsheetModal" tabindex="-1">
          <div class="modal-dialog modal-xl">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title">Data Jawaban Kuisioner Pendidikan dan Pembelajaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
              </div>
              <div class="modal-body">
                <div class="table-responsive">
                  <table class="table table-striped">
                    <thead>
                      <tr>
                        <th style="width:50%;">Pertanyaan</th>
                        <th class="text-center" style="width:12.5%;">Sangat Baik</th>
                        <th class="text-center" style="width:12.5%;">Baik</th>
                        <th class="text-center" style="width:12.5%;">Cukup Baik</th>
                        <th class="text-center" style="width:12.5%;">Kurang Baik</th>
                      </tr>
                    </thead>
                    <tbody>
                      ${(data.summary||[]).map(it=>`
                        <tr>
                          <td class="question-text">${it.question}</td>
                          <td class="text-center"><span class="badge bg-success">${it.sangat_baik}</span></td>
                          <td class="text-center"><span class="badge bg-primary">${it.baik}</span></td>
                          <td class="text-center"><span class="badge bg-warning">${it.cukup_baik}</span></td>
                          <td class="text-center"><span class="badge bg-danger">${it.kurang_baik}</span></td>
                        </tr>`).join('')}
                    </tbody>
                  </table>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-success" onclick="applyDataToForm()">
                  <i class="fas fa-check me-2"></i>Terapkan ke Form
                </button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
              </div>
            </div>
          </div>
        </div>`;
        document.getElementById('spreadsheetModal')?.remove();
        document.body.insertAdjacentHTML('beforeend', html);
        window.spreadsheetData = data;
        new bootstrap.Modal(document.getElementById('spreadsheetModal')).show();
      }

      // Expand / Collapse all
      document.getElementById('expandAll')?.addEventListener('click', function(){
        document.querySelectorAll('.accordion-collapse').forEach(c=>{
          if(!c.classList.contains('show')) new bootstrap.Collapse(c, {show:true});
        });
      });
      document.getElementById('collapseAll')?.addEventListener('click', function(){
        document.querySelectorAll('.accordion-collapse.show').forEach(c=>{
          bootstrap.Collapse.getInstance(c)?.hide();
        });
      });
    });
    </script>
    @endpush
@endsection
