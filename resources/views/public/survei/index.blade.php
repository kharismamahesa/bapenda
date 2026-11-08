@extends('layouts.public')

@section('title', 'Survei Kepuasan Masyarakat')
@section('meta_description', 'Survei Kepuasan Masyarakat dan Survei Anti Korupsi Bapenda Provinsi Riau.')

@section('content')
    @php
        $questions = [
            [
                'field' => 'tanya1',
                'section' => 'SKM',
                'text' => 'Bagaimana pendapat Saudara tentang kesesuaian persyaratan pelayanan dengan jenis pelayanannya?',
                'options' => ['Tidak sesuai', 'Kurang sesuai', 'Sesuai', 'Sangat sesuai'],
            ],
            [
                'field' => 'tanya2',
                'section' => 'SKM',
                'text' => 'Bagaimana pemahaman Saudara tentang prosedur pelayanan pajak kendaraan bermotor di kantor Samsat?',
                'options' => ['Tidak mudah', 'Kurang mudah', 'Mudah', 'Sangat mudah'],
            ],
            [
                'field' => 'tanya3',
                'section' => 'SKM',
                'text' => 'Bagaimana pendapat Saudara tentang kecepatan waktu petugas pelayanan dalam memberikan pelayanan?',
                'options' => ['Tidak cepat', 'Kurang cepat', 'Cepat', 'Sangat cepat'],
            ],
            [
                'field' => 'tanya4',
                'section' => 'SKM',
                'text' => 'Bagaimana pendapat Saudara tentang kewajaran biaya/tarif untuk mendapatkan pelayanan?',
                'options' => ['Tidak wajar', 'Kurang wajar', 'Wajar', 'Sangat wajar'],
            ],
            [
                'field' => 'tanya5',
                'section' => 'SKM',
                'text' => 'Bagaimana pendapat Saudara tentang kesesuaian produk layanan dan standar pelayanan dengan hasil yang diberikan?',
                'options' => ['Tidak sesuai', 'Kurang sesuai', 'Sesuai', 'Sangat sesuai'],
            ],
            [
                'field' => 'tanya6',
                'section' => 'SKM',
                'text' => 'Bagaimana pendapat Saudara tentang kompetensi/kemampuan petugas pelayanan dalam memberikan informasi yang jelas kepada wajib pajak?',
                'options' => ['Tidak kompeten', 'Kurang kompeten', 'Kompeten', 'Sangat kompeten'],
            ],
            [
                'field' => 'tanya7',
                'section' => 'SKM',
                'text' => 'Bagaimana pendapat Saudara tentang perilaku petugas dalam pelayanan terkait kesopanan dan keramahan?',
                'options' => ['Tidak sopan dan ramah', 'Kurang sopan dan ramah', 'Sopan dan ramah', 'Sangat sopan dan ramah'],
            ],
            [
                'field' => 'tanya8',
                'section' => 'SKM',
                'text' => 'Bagaimana pendapat Saudara tentang kualitas sarana dan prasarana?',
                'options' => ['Buruk', 'Cukup', 'Baik', 'Sangat baik'],
            ],
            [
                'field' => 'tanya9',
                'section' => 'SKM',
                'text' => 'Bagaimana pendapat Saudara tentang kecepatan penanganan pengaduan pelayanan pada wajib pajak?',
                'options' => ['Tidak cepat', 'Kurang cepat', 'Cepat', 'Sangat cepat'],
            ],
            [
                'field' => 'tanya21',
                'section' => 'SAK',
                'text' => 'Bagaimana pendapat Saudara bahwa tidak ada diskriminasi pelayanan pada unit kerja ini?',
                'options' => ['Selalu', 'Sering', 'Jarang', 'Tidak ada'],
            ],
            [
                'field' => 'tanya22',
                'section' => 'SAK',
                'text' => 'Bagaimana pendapat Saudara bahwa tidak ada pelayanan di luar prosedur/kecurangan pelayanan pada unit layanan ini?',
                'options' => ['Selalu', 'Sering', 'Jarang', 'Tidak ada'],
            ],
            [
                'field' => 'tanya23',
                'section' => 'SAK',
                'text' => 'Bagaimana pendapat Saudara bahwa tidak ada penerimaan imbalan uang/barang/fasilitas di luar ketentuan yang berlaku pada unit layanan ini?',
                'options' => ['Selalu', 'Sering', 'Jarang', 'Tidak ada'],
            ],
            [
                'field' => 'tanya24',
                'section' => 'SAK',
                'text' => 'Bagaimana pendapat Saudara bahwa tidak ada pungutan liar (pungli) pada unit layanan ini?',
                'options' => ['Selalu', 'Sering', 'Jarang', 'Tidak ada'],
            ],
            [
                'field' => 'tanya25',
                'section' => 'SAK',
                'text' => 'Bagaimana pendapat Saudara bahwa tidak ada percaloan/perantara tidak resmi pada unit layanan ini?',
                'options' => ['Selalu', 'Sering', 'Jarang', 'Tidak ada'],
            ],
        ];
    @endphp

    <section class="page-hero">
        <div class="container">
            <h1>Survei Kepuasan Masyarakat</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('beranda') }}">Beranda</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Survei</li>
                </ol>
            </nav>
        </div>
    </section>

    <section class="page-content survey-page">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-9">
                    <div class="bg-white border rounded-4 p-4 p-lg-5">
                        <div class="section-label">LAYANAN PUBLIK</div>
                        <h2 class="section-title">Survei Kepuasan Masyarakat</h2>
                        <p class="text-muted mb-4">
                            Masukan Anda membantu kami meningkatkan mutu pelayanan. Data identitas tertentu bersifat opsional.
                        </p>

                        <div id="survey-message" class="alert d-none" role="alert" aria-live="polite"></div>
                        <div id="survey-success" class="survey-success d-none text-center" role="status" aria-live="polite">
                            <div class="survey-success-icon"><i class="bi bi-check-lg"></i></div>
                            <h3 class="h4 fw-bold mt-3">Terima Kasih!</h3>
                            <p id="survey-success-message" class="text-muted mb-4"></p>
                            <a href="{{ route('beranda') }}" class="btn btn-success">Kembali ke Beranda <i class="bi bi-house-door ms-1"></i></a>
                        </div>
                        <div id="survey-progress" class="mb-4" aria-live="polite">
                            <div class="d-flex justify-content-between small text-muted mb-2">
                                <span id="survey-step-label">Data wajib pajak</span>
                                <span id="survey-step-count">1 dari 15</span>
                            </div>
                            <div class="progress" role="progressbar" aria-label="Progress survei" aria-valuemin="0" aria-valuemax="100">
                                <div id="survey-progress-bar" class="progress-bar bg-success" style="width: 7%"></div>
                            </div>
                        </div>

                        <form id="survey-form" action="{{ route('survei.store') }}" method="POST" novalidate>
                            @csrf
                            <div aria-hidden="true" style="position:absolute;left:-10000px;width:1px;height:1px;overflow:hidden">
                                <label for="website_url">Jangan isi kolom ini</label>
                                <input type="text" id="website_url" name="website_url" tabindex="-1" autocomplete="off">
                            </div>
                            <section class="survey-step" data-step="0">
                                <div class="survey-step-heading">
                                    <span class="badge rounded-pill text-bg-success">DATA WAJIB PAJAK</span>
                                    <h3 class="h5 mt-3">Informasi Responden</h3>
                                    <p class="text-muted small">Kolom bertanda wajib harus diisi untuk melanjutkan.</p>
                                </div>

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="id_uptup" class="form-label">Kantor Pelayanan <span class="text-danger">*</span></label>
                                        <select id="id_uptup" name="id_uptup" class="form-select" required>
                                            <option value="">Pilih kantor pelayanan</option>
                                            <option value="10000">BAPENDA PROVINSI RIAU</option>
                                            @foreach ($kantorPelayanan as $kantor)
                                                @php
                                                    $status = trim((string) $kantor->Status);
                                                    $namaWilayah = trim((string) $kantor->NamaUPTUP);
                                                    $label = $status !== '' ? $status.' '.$namaWilayah : $namaWilayah;
                                                @endphp
                                                <option value="{{ $kantor->KodeWilayah }}">{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="nama" class="form-label">Nama <span class="text-muted small">(opsional)</span></label>
                                        <input type="text" id="nama" name="nama" class="form-control" maxlength="100" placeholder="Nama Anda">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="no_hp" class="form-label">Nomor HP / WhatsApp <span class="text-muted small">(opsional)</span></label>
                                        <input type="tel" id="no_hp" name="no_hp" class="form-control" maxlength="30" placeholder="08xxxxxxxxxx">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="email" class="form-label">Email <span class="text-muted small">(opsional)</span></label>
                                        <input type="email" id="email" name="email" class="form-control" maxlength="255" inputmode="email" autocomplete="email" spellcheck="false" placeholder="nama@email.com">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="usia" class="form-label">Usia <span class="text-danger">*</span></label>
                                        <input type="number" id="usia" name="usia" class="form-control" min="1" max="999" placeholder="Usia dalam tahun" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="alamat" class="form-label">Alamat <span class="text-muted small">(opsional)</span></label>
                                        <textarea id="alamat" name="alamat" class="form-control" rows="2" placeholder="Alamat Anda"></textarea>
                                    </div>
                                    <div class="col-12">
                                        <fieldset class="survey-demographic">
                                            <legend class="form-label">Jenis Kelamin <span class="text-danger">*</span></legend>
                                            <div class="d-flex flex-wrap gap-3">
                                                <label class="form-check"><input class="form-check-input" type="radio" name="jenis_kelamin" value="L" required> Laki-laki</label>
                                                <label class="form-check"><input class="form-check-input" type="radio" name="jenis_kelamin" value="P"> Perempuan</label>
                                            </div>
                                        </fieldset>
                                    </div>
                                    <div class="col-12">
                                        <fieldset class="survey-demographic">
                                            <legend class="form-label">Pendidikan Terakhir <span class="text-danger">*</span></legend>
                                            <div class="d-flex flex-wrap gap-3">
                                                @foreach (['SD', 'SLTP/SMP', 'SLTA/SMA', 'D1/D2/D3', 'S1', 'S2/S3'] as $education)
                                                    <label class="form-check"><input class="form-check-input" type="radio" name="pendidikan" value="{{ $education }}" @required($loop->first)> {{ $education }}</label>
                                                @endforeach
                                            </div>
                                        </fieldset>
                                    </div>
                                    <div class="col-12">
                                        <fieldset class="survey-demographic">
                                            <legend class="form-label">Pekerjaan <span class="text-danger">*</span></legend>
                                            <div class="d-flex flex-wrap gap-3">
                                                @foreach (['PNS/TNI/POLRI', 'PEGAWAI SWASTA', 'WIRASWASTA', 'MAHASISWA', 'LAINNYA'] as $job)
                                                    <label class="form-check"><input class="form-check-input" type="radio" name="pekerjaan" value="{{ $job }}" @required($loop->first)> {{ $job }}</label>
                                                @endforeach
                                            </div>
                                        </fieldset>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end mt-4">
                                    <button type="button" class="btn btn-success survey-next">Mulai Survei <i class="bi bi-arrow-right ms-1"></i></button>
                                </div>
                            </section>

                            @foreach ($questions as $index => $question)
                                <section class="survey-step d-none" data-step="{{ $index + 1 }}" data-field="{{ $question['field'] }}">
                                    <div class="survey-step-heading">
                                        <span class="badge rounded-pill text-bg-success">{{ $question['section'] === 'SKM' ? 'SKM · KEPUASAN MASYARAKAT' : 'SAK · ANTI KORUPSI' }}</span>
                                        <div class="small text-muted mt-3">Pertanyaan {{ $index + 1 }} dari {{ count($questions) }}</div>
                                        <h3 class="h5 mt-2">{{ $question['text'] }} <span class="text-danger">*</span></h3>
                                    </div>

                                    <fieldset class="survey-options" aria-label="Pilih satu jawaban">
                                        @foreach ($question['options'] as $optionIndex => $option)
                                            <label class="survey-option">
                                                <input type="radio" name="{{ $question['field'] }}" value="{{ $optionIndex + 1 }}" @required($loop->first)>
                                                <span class="survey-option-value">{{ $optionIndex + 1 }}</span>
                                                <span>{{ $option }}</span>
                                            </label>
                                        @endforeach
                                    </fieldset>

                                    <div class="d-flex justify-content-between gap-2 mt-4">
                                        <button type="button" class="btn btn-outline-secondary survey-prev"><i class="bi bi-arrow-left me-1"></i> Sebelumnya</button>
                                        @if ($loop->last)
                                            <button type="submit" id="survey-submit" class="btn btn-success"><span class="survey-submit-label">Kirim Survei</span> <i class="bi bi-send ms-1"></i></button>
                                        @else
                                            <button type="button" class="btn btn-success survey-next">Selanjutnya <i class="bi bi-arrow-right ms-1"></i></button>
                                        @endif
                                    </div>
                                </section>
                            @endforeach
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('styles')
    <style>
        .survey-page .survey-step-heading { border-bottom: 1px solid #e8eee9; padding-bottom: 1rem; margin-bottom: 1.25rem; }
        .survey-demographic { border: 0; padding: 0; margin: 0; }
        .survey-demographic legend { float: none; width: auto; margin-bottom: .65rem; font-weight: 600; }
        .survey-demographic .form-check { margin: 0; }
        .survey-options { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: .75rem; border: 0; padding: 0; }
        .survey-option { display: flex; align-items: center; gap: .75rem; min-height: 64px; padding: .8rem 1rem; border: 1px solid #dfe8e2; border-radius: .75rem; cursor: pointer; transition: border-color .15s, background-color .15s; }
        .survey-option:has(input:checked) { border-color: #168447; background: #f0f8f3; }
        .survey-option input { position: absolute; opacity: 0; }
        .survey-option input:focus-visible + .survey-option-value { outline: 3px solid rgba(22, 132, 71, .3); }
        .survey-option-value { display: inline-flex; width: 2rem; height: 2rem; align-items: center; justify-content: center; flex: 0 0 2rem; border-radius: 50%; background: #e8f3ec; color: #075c32; font-weight: 700; }
        .survey-success { padding: 2rem 1rem; }
        .survey-success-icon { display: inline-flex; width: 5rem; height: 5rem; align-items: center; justify-content: center; border-radius: 50%; background: #e8f5ec; color: #168447; font-size: 2.5rem; box-shadow: 0 0 0 .6rem #f4faf6; }
        @media (max-width: 575.98px) { .survey-options { grid-template-columns: 1fr; } }
    </style>
@endpush

@push('scripts')
    <script>
        (() => {
            const form = document.getElementById('survey-form');
            const steps = Array.from(form.querySelectorAll('.survey-step'));
            const progressBar = document.getElementById('survey-progress-bar');
            const stepLabel = document.getElementById('survey-step-label');
            const stepCount = document.getElementById('survey-step-count');
            const message = document.getElementById('survey-message');
            const submitButton = document.getElementById('survey-submit');
            const successPanel = document.getElementById('survey-success');
            const questionFields = [
                'tanya1', 'tanya2', 'tanya3', 'tanya4', 'tanya5', 'tanya6', 'tanya7',
                'tanya8', 'tanya9', 'tanya21', 'tanya22', 'tanya23', 'tanya24', 'tanya25'
            ];
            let currentStep = 0;

            function showStep(index) {
                currentStep = index;
                steps.forEach((step, stepIndex) => step.classList.toggle('d-none', stepIndex !== index));
                const percent = Math.round(((index + 1) / steps.length) * 100);
                progressBar.style.width = `${percent}%`;
                progressBar.parentElement.setAttribute('aria-valuenow', String(percent));
                stepCount.textContent = `${index + 1} dari ${steps.length}`;
                stepLabel.textContent = index === 0
                    ? 'Data wajib pajak'
                    : `Pertanyaan ${index} · ${index <= 9 ? 'SKM' : 'SAK'}`;
                message.classList.add('d-none');
                form.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }

            function validateStep(index) {
                const step = steps[index];
                const invalidControl = Array.from(step.querySelectorAll('input, select, textarea'))
                    .find(control => !control.checkValidity());

                if (invalidControl) {
                    invalidControl.reportValidity();
                    return false;
                }

                return true;
            }

            function validateAllSteps() {
                for (let index = 0; index < steps.length; index++) {
                    const invalidControl = Array.from(steps[index].querySelectorAll('input, select, textarea'))
                        .find(control => !control.checkValidity());

                    if (invalidControl) {
                        showStep(index);
                        invalidControl.focus();
                        invalidControl.reportValidity();
                        return false;
                    }
                }

                return true;
            }

            form.querySelectorAll('.survey-next').forEach(button => {
                button.addEventListener('click', () => {
                    if (validateStep(currentStep)) showStep(currentStep + 1);
                });
            });

            form.querySelectorAll('.survey-prev').forEach(button => {
                button.addEventListener('click', () => showStep(currentStep - 1));
            });

            form.addEventListener('submit', async event => {
                event.preventDefault();
                if (!validateAllSteps()) return;

                const formData = new FormData(form);
                const payload = {
                    id_uptup: formData.get('id_uptup'),
                    nama: formData.get('nama') || null,
                    no_hp: formData.get('no_hp') || null,
                    email: formData.get('email') || null,
                    alamat: formData.get('alamat') || null,
                    jenis_kelamin: formData.get('jenis_kelamin'),
                    usia: Number(formData.get('usia')),
                    pendidikan: formData.get('pendidikan'),
                    pekerjaan: formData.get('pekerjaan'),
                    app: 'website-public',
                    website_url: formData.get('website_url') || '',
                };

                questionFields.forEach(field => {
                    payload[field] = Number(formData.get(field));
                });

                submitButton.disabled = true;
                submitButton.querySelector('.survey-submit-label').textContent = 'Mengirim...';
                message.classList.add('d-none');

                try {
                    const response = await fetch(form.action, {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': form.querySelector('[name="_token"]').value,
                        },
                        body: JSON.stringify(payload),
                    });
                    const result = await response.json();

                    if (!response.ok || !result.success) {
                        const firstValidationError = Object.values(result.errors || {}).flat()[0];
                        throw new Error(firstValidationError || result.message || 'Pengiriman survei gagal.');
                    }

                    document.getElementById('survey-success-message').textContent = result.message;
                    successPanel.classList.remove('d-none');
                    form.classList.add('d-none');
                    document.getElementById('survey-progress').classList.add('d-none');
                } catch (error) {
                    console.error('Gagal mengirim payload survei:', error);
                    message.className = 'alert alert-danger';
                    message.textContent = error.message || 'Terjadi kesalahan saat mengirim survei.';
                    message.classList.remove('d-none');
                    submitButton.disabled = false;
                    submitButton.querySelector('.survey-submit-label').textContent = 'Kirim Survei';
                }
            });
        })();
    </script>
@endpush
