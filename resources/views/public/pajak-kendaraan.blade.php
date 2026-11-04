@extends('layouts.public')

@section('title', 'Informasi Pajak Kendaraan Bermotor')
@section('meta_description', 'Cek informasi pajak kendaraan bermotor Provinsi Riau secara cepat dan mudah.')

@section('content')

    <section class="page-hero">
        <div class="container">
            <h1>Informasi Pajak Kendaraan Bermotor</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('beranda') }}">Beranda</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Informasi Pajak Kendaraan</li>
                </ol>
            </nav>
        </div>
    </section>

    <section class="page-content">
        <div class="container">
            <div class="row g-4">

                <div class="col-lg-8">
                    <div class="pajak-panel bg-white border rounded-4 p-4 p-lg-5">
                        <div class="section-label">CEK PAJAK KENDARAAN</div>
                        <h2 class="pajak-title">Cek Informasi Pajak Kendaraan Anda</h2>
                        <p class="text-muted small mb-4">
                            Masukkan nomor polisi atau nomor rangka kendaraan untuk melihat informasi pajak.
                            Data diambil langsung dari layanan informasi pajak Bapenda Provinsi Riau.
                        </p>

                        <form id="form-pajak" class="pajak-search" novalidate>
                            <div class="pajak-search-fields">
                                <div class="bapenda-input-group is-tracked pajak-search-input">
                                    <i class="bi bi-car-front input-icon"></i>
                                    <input type="text" id="nopol" name="nopol" placeholder="Nomor polisi: BM 1234 XY"
                                        maxlength="15" autocomplete="off" aria-label="Nomor polisi kendaraan">
                                </div>
                                <div class="bapenda-input-group is-tracked pajak-search-input">
                                    <i class="bi bi-upc-scan input-icon"></i>
                                    <input type="text" id="no-rangka-input" name="no_rangka" placeholder="No. rangka / 5 digit terakhir"
                                        maxlength="20" autocomplete="off" aria-label="Nomor rangka kendaraan">
                                </div>
                            </div>
                            <button type="submit" class="btn-bapenda btn-bapenda-accent btn-cta" id="btn-cek">
                                <span id="btn-cek-text">Cek Sekarang</span>
                                <i class="bi bi-search"></i>
                            </button>
                        </form>

                        <p class="pajak-search-hint">
                            <i class="bi bi-info-circle"></i>
                            Isi nomor polisi dan/atau nomor rangka (5 digit terakhir).
                        </p>

                        <div id="pajak-alert" class="alert d-none mt-3" role="alert"></div>

                        <div id="pajak-loading" class="bapenda-loading pajak-loading d-none mt-4">
                            <span class="bapenda-spinner pajak-spinner"></span>
                            Mencari data kendaraan...
                        </div>

                        <div id="pajak-result" class="d-none mt-4">
                            <div class="pajak-result-head">
                                <div class="pajak-result-icon">
                                    <i class="bi bi-car-front-fill"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="pajak-result-label">Nomor Polisi</div>
                                    <div class="pajak-nopol" id="result-nopol">-</div>
                                </div>
                            </div>

                            <div class="pajak-vehicle-details mt-4">
                                <div class="pajak-vehicle-row">
                                    <span>Merk</span>
                                    <strong id="result-merk">-</strong>
                                </div>
                                <div class="pajak-vehicle-row">
                                    <span>Tipe</span>
                                    <strong id="result-tipe">-</strong>
                                </div>
                                <div class="pajak-vehicle-row">
                                    <span>Model</span>
                                    <strong id="result-model">-</strong>
                                </div>
                                <div class="pajak-vehicle-row">
                                    <span>Jenis</span>
                                    <strong id="result-jenis">-</strong>
                                </div>
                                <div class="pajak-vehicle-row">
                                    <span>Warna</span>
                                    <strong id="result-warna">-</strong>
                                </div>
                                <div class="pajak-vehicle-row">
                                    <span>Warna TNKB</span>
                                    <strong id="result-warna-tnkb">-</strong>
                                </div>
                                <div class="pajak-vehicle-row">
                                    <span>Golongan</span>
                                    <strong id="result-golongan">-</strong>
                                </div>
                                <div class="pajak-vehicle-row">
                                    <span>Tahun Pembuatan</span>
                                    <strong id="result-tahun">-</strong>
                                </div>
                            </div>

                            <!-- Blok Perbandingan Pajak (Didekatkan Berdampingan) -->
                            <div class="row g-3 mt-3">
                                <!-- Pajak Sebelumnya -->
                                <div class="col-md-12">
                                    <div class="pajak-section-card h-100 d-flex flex-column">
                                        <h5 class="pajak-section-title mb-2">Pajak Sebelumnya</h5>

                                        <div class="pajak-date-line">
                                            <span>Tanggal Bayar</span>
                                            <strong id="result-tgl-bayar" class="text-end">-</strong>
                                        </div>

                                        <button class="bapenda-accordion-btn is-compact pajak-accordion-btn collapsed" type="button" data-bs-toggle="collapse"
                                            data-bs-target="#detailPajakSebelumnya" aria-expanded="false"
                                            aria-controls="detailPajakSebelumnya">
                                            <span>Rincian Pajak Sebelumnya</span>
                                            <i class="bi bi-chevron-down accordion-arrow"></i>
                                        </button>

                                        <div id="detailPajakSebelumnya" class="collapse">
                                            <table class="pajak-tax-table mt-2">
                                                <tbody>
                                                    <tr>
                                                        <td>Pokok PKB</td>
                                                        <td id="result-old-pokok-pkb">-</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Denda PKB</td>
                                                        <td id="result-old-denda-pkb">-</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Pokok PKB Opsen</td>
                                                        <td id="result-old-pokok-pkb-opsen">-</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Denda PKB Opsen</td>
                                                        <td id="result-old-denda-pkb-opsen">-</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Pokok BBNKB</td>
                                                        <td id="result-old-pokok-bbnkb">-</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Denda BBNKB</td>
                                                        <td id="result-old-denda-bbnkb">-</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Pokok BBNKB Opsen</td>
                                                        <td id="result-old-pokok-bbnkb-opsen">-</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Denda BBNKB Opsen</td>
                                                        <td id="result-old-denda-bbnkb-opsen">-</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Pokok SWDKLLJ</td>
                                                        <td id="result-old-pokok-swdkllj">-</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Denda SWDKLLJ</td>
                                                        <td id="result-old-denda-swdkllj">-</td>
                                                    </tr>
                                                    <tr>
                                                        <td>STNK</td>
                                                        <td id="result-old-stnk">-</td>
                                                    </tr>
                                                    <tr>
                                                        <td>TNKB</td>
                                                        <td id="result-old-tnkb">-</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>

                                        <div class="pajak-total mt-auto pt-3">
                                            <span>Total Sebelumnya</span>
                                            <strong id="result-old-total">-</strong>
                                        </div>
                                    </div>
                                </div>

                                <!-- Pajak yang Harus Dibayar -->
                                <div class="col-md-12">
                                    <div class="pajak-section-card h-100 d-flex flex-column pajak-card-current">
                                        <h5 class="pajak-section-title mb-2">Pajak yang Harus Dibayar</h5>

                                        <div class="pajak-date-line">
                                            <span>Jatuh Tempo</span>
                                            <div id="result-jatuh-tempo" class="text-end fw-bold">-</div>
                                        </div>

                                        <button class="bapenda-accordion-btn is-compact pajak-accordion-btn collapsed" type="button" data-bs-toggle="collapse"
                                            data-bs-target="#detailPajakSekarang" aria-expanded="false"
                                            aria-controls="detailPajakSekarang">
                                            <span>Rincian yang Harus Dibayar</span>
                                            <i class="bi bi-chevron-down accordion-arrow"></i>
                                        </button>

                                        <div id="detailPajakSekarang" class="collapse">
                                            <table class="pajak-tax-table mt-2">
                                                <tbody>
                                                    <tr>
                                                        <td>Pokok PKB</td>
                                                        <td id="result-pokok-pkb">-</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Denda PKB</td>
                                                        <td id="result-denda-pkb">-</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Pokok PKB Opsen</td>
                                                        <td id="result-pokok-pkb-opsen">-</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Denda PKB Opsen</td>
                                                        <td id="result-denda-pkb-opsen">-</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Pokok BBNKB</td>
                                                        <td id="result-pokok-bbnkb">-</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Denda BBNKB</td>
                                                        <td id="result-denda-bbnkb">-</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Pokok BBNKB Opsen</td>
                                                        <td id="result-pokok-bbnkb-opsen">-</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Denda BBNKB Opsen</td>
                                                        <td id="result-denda-bbnkb-opsen">-</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Pokok SWDKLLJ</td>
                                                        <td id="result-pokok-swdkllj">-</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Denda SWDKLLJ</td>
                                                        <td id="result-denda-swdkllj">-</td>
                                                    </tr>
                                                    <tr>
                                                        <td>STNK</td>
                                                        <td id="result-stnk">-</td>
                                                    </tr>
                                                    <tr>
                                                        <td>TNKB</td>
                                                        <td id="result-tnkb">-</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>

                                        <div class="pajak-total mt-auto pt-3">
                                            <span>Total yang harus dibayar</span>
                                            <strong id="result-total">-</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <p class="pajak-note mt-3 mb-0">
                                <i class="bi bi-info-circle"></i>
                                Nilai pajak merupakan perkiraan dan dapat berubah sewaktu-waktu.
                                Sumber: <span id="result-sumber">Bapenda Provinsi Riau</span>.
                                Diperbarui: <span id="result-diupdate">-</span>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="pajak-info-card">
                        <div class="pajak-info-icon">
                            <i class="bi bi-lightbulb"></i>
                        </div>
                        <h5>Tips Pengecekan</h5>
                        <ul>
                            <li>Gunakan nomor polisi dan nomor rangka kendaraan.</li>
                            <li>Nomor rangka dapat diisi 5 digit terakhir.</li>
                            <li>Contoh nomor polisi: <strong>BM 1234 XY</strong>.</li>
                            <li>Pastikan kendaraan terdaftar di Provinsi Riau.</li>
                        </ul>
                    </div>

                    <div class="pajak-info-card">
                        <div class="pajak-info-icon">
                            <i class="bi bi-info-circle"></i>
                        </div>
                        <h5>Informasi</h5>
                        <p class="mb-0">
                            Layanan ini menyajikan informasi pajak kendaraan bermotor.
                            Untuk pembayaran dan pengesahan, silakan datang ke Samsat terdekat
                            atau gunakan kanal resmi yang tersedia.
                        </p>
                    </div>
                </div>

            </div>
        </div>
    </section>

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('form-pajak');
            const nopolInput = document.getElementById('nopol');
            const noRangkaInput = document.getElementById('no-rangka-input');
            const btn = document.getElementById('btn-cek');
            const btnText = document.getElementById('btn-cek-text');
            const alertBox = document.getElementById('pajak-alert');
            const loading = document.getElementById('pajak-loading');
            const result = document.getElementById('pajak-result');
            const apiUrl = "{{ route('pajak-kendaraan.api') }}";

            const fields = {
                nopol: document.getElementById('result-nopol'),
                merk: document.getElementById('result-merk'),
                tipe: document.getElementById('result-tipe'),
                model: document.getElementById('result-model'),
                jenis: document.getElementById('result-jenis'),
                warna: document.getElementById('result-warna'),
                warnaTnkb: document.getElementById('result-warna-tnkb'),
                golongan: document.getElementById('result-golongan'),
                tahun: document.getElementById('result-tahun'),
                tglBayar: document.getElementById('result-tgl-bayar'),
                oldTotal: document.getElementById('result-old-total'),
                oldPokokPkb: document.getElementById('result-old-pokok-pkb'),
                oldDendaPkb: document.getElementById('result-old-denda-pkb'),
                oldPokokPkbOpsen: document.getElementById('result-old-pokok-pkb-opsen'),
                oldDendaPkbOpsen: document.getElementById('result-old-denda-pkb-opsen'),
                oldPokokBbnkb: document.getElementById('result-old-pokok-bbnkb'),
                oldDendaBbnkb: document.getElementById('result-old-denda-bbnkb'),
                oldPokokBbnkbOpsen: document.getElementById('result-old-pokok-bbnkb-opsen'),
                oldDendaBbnkbOpsen: document.getElementById('result-old-denda-bbnkb-opsen'),
                oldPokokSwdkllj: document.getElementById('result-old-pokok-swdkllj'),
                oldDendaSwdkllj: document.getElementById('result-old-denda-swdkllj'),
                oldStnk: document.getElementById('result-old-stnk'),
                oldTnkb: document.getElementById('result-old-tnkb'),
                jatuhTempo: document.getElementById('result-jatuh-tempo'),
                total: document.getElementById('result-total'),
                pokokPkb: document.getElementById('result-pokok-pkb'),
                dendaPkb: document.getElementById('result-denda-pkb'),
                pokokPkbOpsen: document.getElementById('result-pokok-pkb-opsen'),
                dendaPkbOpsen: document.getElementById('result-denda-pkb-opsen'),
                pokokBbnkb: document.getElementById('result-pokok-bbnkb'),
                dendaBbnkb: document.getElementById('result-denda-bbnkb'),
                pokokBbnkbOpsen: document.getElementById('result-pokok-bbnkb-opsen'),
                dendaBbnkbOpsen: document.getElementById('result-denda-bbnkb-opsen'),
                pokokSwdkllj: document.getElementById('result-pokok-swdkllj'),
                dendaSwdkllj: document.getElementById('result-denda-swdkllj'),
                stnk: document.getElementById('result-stnk'),
                tnkb: document.getElementById('result-tnkb'),
                sumber: document.getElementById('result-sumber'),
                diupdate: document.getElementById('result-diupdate'),
            };

            const rupiah = new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                maximumFractionDigits: 0,
            });

            function showAlert(message, type) {
                alertBox.className = 'alert alert-' + type + ' mt-3';
                alertBox.textContent = message;
                alertBox.classList.remove('d-none');
            }

            function hideAlert() {
                alertBox.classList.add('d-none');
                alertBox.textContent = '';
            }

            function setLoading(state) {
                btn.disabled = state;
                btnText.textContent = state ? 'Mencari...' : 'Cek Sekarang';
                loading.classList.toggle('d-none', !state);
            }

            function fillResult(data) {
                const text = function (element, value) {
                    element.textContent = value !== null && value !== undefined && value !== '' ? value : '-';
                };

                const money = function (element, value) {
                    element.textContent = rupiah.format(Number(value) || 0);
                };

                text(fields.nopol, data.nopol);
                text(fields.merk, data.merk);
                text(fields.tipe, data.tipe);
                text(fields.model, data.model);
                text(fields.jenis, data.jenis_kendaraan);
                text(fields.warna, data.warna);
                text(fields.warnaTnkb, data.warna_tnkb);
                text(fields.golongan, data.golongan_kendaraan);
                text(fields.tahun, data.tahun);

                text(fields.tglBayar, data.tanggal_bayar_sebelumnya);
                money(fields.oldPokokPkb, data.pokok_pkb_sebelumnya);
                money(fields.oldDendaPkb, data.denda_pkb_sebelumnya);
                money(fields.oldPokokPkbOpsen, data.pokok_pkb_opsen_sebelumnya);
                money(fields.oldDendaPkbOpsen, data.denda_pkb_opsen_sebelumnya);
                money(fields.oldPokokBbnkb, data.pokok_bbnkb_sebelumnya);
                money(fields.oldDendaBbnkb, data.denda_bbnkb_sebelumnya);
                money(fields.oldPokokBbnkbOpsen, data.pokok_bbnkb_opsen_sebelumnya);
                money(fields.oldDendaBbnkbOpsen, data.denda_bbnkb_opsen_sebelumnya);
                money(fields.oldPokokSwdkllj, data.pokok_swdkllj_sebelumnya);
                money(fields.oldDendaSwdkllj, data.denda_swdkllj_sebelumnya);
                money(fields.oldStnk, data.stnk_sebelumnya);
                money(fields.oldTnkb, data.tnkb_sebelumnya);
                money(fields.oldTotal, data.total_sebelumnya);

                if (data.tanggal_jatuh_tempo_formatted && data.estimasi_jatuh_tempo) {
                    const isPast = data.status_jatuh_tempo === 'past';
                    const isToday = data.status_jatuh_tempo === 'today';
                    const colorClass = isPast ? 'text-danger' : (isToday ? 'text-primary' : 'text-success');

                    fields.jatuhTempo.innerHTML = '<span class="d-block text-dark fw-bold">' + data.tanggal_jatuh_tempo_formatted + '</span>' +
                        '<span class="d-block small fw-semibold mt-1 ' + colorClass + '" style="font-size: 11px;">' + data.estimasi_jatuh_tempo + '</span>';
                } else {
                    text(fields.jatuhTempo, data.tanggal_jatuh_tempo);
                }

                money(fields.pokokPkb, data.pokok_pkb);
                money(fields.dendaPkb, data.denda_pkb);
                money(fields.pokokPkbOpsen, data.pokok_pkb_opsen);
                money(fields.dendaPkbOpsen, data.denda_pkb_opsen);
                money(fields.pokokBbnkb, data.pokok_bbnkb);
                money(fields.dendaBbnkb, data.denda_bbnkb);
                money(fields.pokokBbnkbOpsen, data.pokok_bbnkb_opsen);
                money(fields.dendaBbnkbOpsen, data.denda_bbnkb_opsen);
                money(fields.pokokSwdkllj, data.pokok_swdkllj);
                money(fields.dendaSwdkllj, data.denda_swdkllj);
                money(fields.stnk, data.stnk);
                money(fields.tnkb, data.tnkb);
                money(fields.total, data.total_bayar);

                text(fields.sumber, data.sumber);
                text(fields.diupdate, data.diperbarui);
            }

            form.addEventListener('submit', async function (event) {
                event.preventDefault();

                const nopol = nopolInput.value.trim();
                const noRangka = noRangkaInput.value.trim();

                if (!nopol && !noRangka) {
                    hideAlert();
                    result.classList.add('d-none');
                    showAlert('Isi nomor polisi atau nomor rangka terlebih dahulu.', 'danger');
                    nopolInput.focus();
                    return;
                }

                hideAlert();
                result.classList.add('d-none');
                setLoading(true);

                try {
                    const params = new URLSearchParams();
                    if (nopol) params.append('nopol', nopol);
                    if (noRangka) params.append('no_rangka', noRangka);

                    const response = await fetch(apiUrl + '?' + params.toString(), {
                        headers: {
                            'Accept': 'application/json',
                        },
                    });

                    const payload = await response.json();

                    if (!response.ok || !payload.success) {
                        throw new Error(payload.message || 'Data kendaraan tidak ditemukan.');
                    }

                    fillResult(payload.data);
                    result.classList.remove('d-none');
                } catch (error) {
                    showAlert(error.message || 'Terjadi kesalahan saat mengambil data.', 'danger');
                } finally {
                    setLoading(false);
                }
            });
        });
    </script>
@endpush
