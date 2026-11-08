@extends('layouts.admin')

@section('title', 'Survei Masyarakat')
@section('page_title', 'Survei Masyarakat')
@section('page_subtitle', 'Ringkasan jawaban Survei Kepuasan Masyarakat dan Survei Anti Korupsi.')

@section('content')
    <form method="GET" action="{{ route('admin.survei.index') }}" id="survey-filter-form"
        class="card card-custom p-3 p-md-4 mb-4">
        <div class="row g-3 align-items-end">
            <div class="col-12 col-md-3">
                <label for="start_date" class="form-label fw-semibold">Tanggal mulai</label>
                <input type="date" id="start_date" name="start_date" class="form-control" value="{{ $startDate }}"
                    required>
                @error('start_date')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-12 col-md-3">
                <label for="end_date" class="form-label fw-semibold">Tanggal akhir</label>
                <input type="date" id="end_date" name="end_date" class="form-control" value="{{ $endDate }}"
                    required>
                @error('end_date')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-12 col-md-4">
                <label for="id_uptup" class="form-label fw-semibold">Kantor Pelayanan (UPTUP)</label>
                <select id="id_uptup" name="id_uptup" class="form-select">
                    <option value="">Semua kantor</option>
                    <option value="10000" @selected($selectedOffice === '10000')>BAPENDA PROVINSI RIAU</option>
                    @foreach ($offices as $office)
                        <option value="{{ $office->KodeWilayah }}" @selected($selectedOffice === (string) $office->KodeWilayah)>
                            {{ $office->NamaUPTUP }}
                        </option>
                    @endforeach
                </select>
                @error('id_uptup')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-12 col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-green">Terapkan</button>
                <a href="{{ route('admin.survei.index') }}" id="survey-filter-reset"
                    class="btn btn-outline-secondary">Reset</a>
            </div>
        </div>
        <div class="text-danger small mt-2" id="survey-filter-error" role="alert" aria-live="polite"></div>
    </form>

    <div id="survey-dashboard-results">
        <script type="application/json" id="survey-dashboard-data">{!! json_encode([
            'totalSurvei' => $totalSurvei,
            'surveiHariIni' => $surveiHariIni,
            'suspectedSpam' => $suspectedSpam,
            'averageSkm' => $averageSkm,
            'averageSak' => $averageSak,
            'ikmValue' => $ikmValue,
            'ikmQuality' => $ikmQuality,
            'sakQuality' => $sakQuality,
            'skmQuestionAverages' => $skmQuestionAverages,
            'sakQuestionAverages' => $sakQuestionAverages,
            'dailyLabels' => $dailyLabels,
            'dailyValues' => $dailyValues,
            'genderLabels' => $genderLabels,
            'genderValues' => $genderValues,
            'educationLabels' => $educationLabels,
            'educationValues' => $educationValues,
            'ageLabels' => $ageLabels,
            'ageValues' => $ageValues,
            'occupationLabels' => $occupationLabels,
            'occupationValues' => $occupationValues,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!}</script>
        <div class="row g-3 mb-4">
            <div class="col-12 col-sm-6 {{ auth()->user()->role === 'superadmin' ? 'col-xl-4' : 'col-xl-6' }}">
                <div class="card card-custom p-4 h-100">
                    <div class="stat-title">Total Responden</div>
                    <div class="stat-value">{{ number_format($totalSurvei) }}</div>
                    <div class="text-muted small mt-1">Sesuai filter tanggal dan kantor</div>
                </div>
            </div>
            <div class="col-12 col-sm-6 {{ auth()->user()->role === 'superadmin' ? 'col-xl-4' : 'col-xl-6' }}">
                <div class="card card-custom p-4 h-100">
                    <div class="stat-title">Responden Hari Ini</div>
                    <div class="stat-value">{{ number_format($surveiHariIni) }}</div>
                    <div class="text-muted small mt-1">Berdasarkan tanggal pengisian</div>
                </div>
            </div>
            @if (auth()->user()->role === 'superadmin')
                <div class="col-12 col-sm-6 col-xl-4">
                    <div class="card card-custom p-4 h-100">
                        <div class="stat-title">Perlu Ditinjau</div>
                        <div class="stat-value">{{ number_format($suspectedSpam) }}</div>
                        <div class="text-muted small mt-1 mb-3">Dugaan duplikat, tidak masuk perhitungan sampai ditinjau.
                        </div>
                        <a href="{{ route('admin.survei.review', ['start_date' => $startDate, 'end_date' => $endDate, 'id_uptup' => $selectedOffice]) }}"
                            class="btn btn-sm btn-outline-warning mt-auto align-self-start">
                            <i class="bi bi-search me-1"></i> Tinjau Data
                        </a>
                    </div>
                </div>
            @endif
        </div>

        <div class="row g-3 mb-4">
            <div class="col-12 col-md-6">
                <div class="card card-custom p-4 h-100">
                    <div class="stat-title fs-6">Nilai IKM (SKM)</div>
                    <div class="stat-value mt-2" style="font-size: clamp(2.75rem, 5vw, 3.75rem); line-height: 1.1;">
                        {{ $ikmValue !== null ? number_format($ikmValue, 2) : '-' }}
                        <span class="fs-5 fw-normal text-muted">/ 100</span>
                    </div>
                    <div class="text-muted small mt-1">
                        @if ($ikmQuality)
                            Konversi SKM {{ number_format($averageSkm, 2) }}/4 × 25 · Mutu {{ $ikmQuality['mutu'] }} —
                            {{ $ikmQuality['kinerja'] }}
                        @else
                            Belum ada jawaban SKM
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="card card-custom p-4 h-100">
                    <div class="stat-title fs-6">Nilai SAK (konversi)</div>
                    <div class="stat-value mt-2" style="font-size: clamp(2.75rem, 5vw, 3.75rem); line-height: 1.1;">
                        {{ $averageSak !== null ? number_format($averageSak, 2) : '-' }}
                        <span class="fs-5 fw-normal text-muted">/ 100</span>
                    </div>
                    <div class="text-muted small mt-1">
                        @if ($averageSak !== null)
                            Konversi SAK {{ number_format($averageSak / 25, 2) }}/4 × 25 · Mutu {{ $sakQuality['mutu'] }}
                            —
                            {{ $sakQuality['kinerja'] }}
                        @else
                            Belum ada jawaban SAK
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-12 col-xl-7">
                <div class="card card-custom p-4 h-100">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h6 class="fw-bold mb-0 text-dark">Unsur SKM</h6>
                        <span class="badge bg-success-subtle text-success border border-success-subtle">Dasar perhitungan
                            IKM ·
                            Skala 1–4</span>
                    </div>
                    <div style="height: 380px; position: relative;">
                        <canvas id="surveySkmChart" role="img"
                            aria-label="Grafik rata-rata nilai sembilan unsur SKM"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-12 col-xl-5">
                <div class="card card-custom p-4 h-100">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h6 class="fw-bold mb-0 text-dark">Unsur SAK</h6>
                        <span class="badge bg-warning-subtle text-dark border">Skala 1–4 · Hasil konversi memiliki mutu
                            terpisah</span>
                    </div>
                    <div style="height: 380px; position: relative;">
                        <canvas id="surveySakChart" role="img"
                            aria-label="Grafik rata-rata nilai lima unsur SAK"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-12 col-lg-3">
                <div class="card card-custom p-4 h-100">
                    <h6 class="fw-bold mb-3 text-dark">Responden Berdasarkan Jenis Kelamin</h6>
                    <div style="height: 270px; position: relative;">
                        <canvas id="surveyGenderChart" role="img"
                            aria-label="Jumlah responden berdasarkan jenis kelamin"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-3">
                <div class="card card-custom p-4 h-100">
                    <h6 class="fw-bold mb-3 text-dark">Responden Berdasarkan Pendidikan</h6>
                    <div style="height: 270px; position: relative;">
                        <canvas id="surveyEducationChart" role="img"
                            aria-label="Jumlah responden berdasarkan pendidikan"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-3">
                <div class="card card-custom p-4 h-100">
                    <h6 class="fw-bold mb-3 text-dark">Responden Berdasarkan Rentang Usia</h6>
                    <div style="height: 270px; position: relative;">
                        <canvas id="surveyAgeChart" role="img"
                            aria-label="Jumlah responden berdasarkan kelompok usia"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-3">
                <div class="card card-custom p-4 h-100">
                    <h6 class="fw-bold mb-3 text-dark">Responden Berdasarkan Pekerjaan</h6>
                    <div style="height: 270px; position: relative;">
                        <canvas id="surveyOccupationChart" role="img"
                            aria-label="Jumlah responden berdasarkan pekerjaan"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="card card-custom p-4 mb-4">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h6 class="fw-bold mb-0 text-dark">Tren Pengisian Survei</h6>
                <span class="badge bg-light text-muted border">30 hari terakhir · Tidak mengikuti filter</span>
            </div>
            <div style="height: 270px; position: relative;">
                <canvas id="surveyDailyChart" role="img"
                    aria-label="Grafik jumlah survei selama tiga puluh hari terakhir"></canvas>
            </div>
        </div>

        <div class="d-flex justify-content-end mb-4">
            <a href="#" id="survey-download" class="btn btn-green">
                <i class="bi bi-download me-1"></i> Unduh Data Survei sesuai Filter
            </a>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const surveyChartText = '#64748b';
        const surveyGridColor = 'rgba(148, 163, 184, .18)';
        const surveyFilterForm = document.getElementById('survey-filter-form');
        const surveyFilterError = document.getElementById('survey-filter-error');

        function readSurveyDashboardData(container = document) {
            return JSON.parse(container.querySelector('#survey-dashboard-data').textContent);
        }

        function renderSurveyCharts(container = document) {
            const data = readSurveyDashboardData(container);
            const surveyQuestionChartOptions = {
                indexAxis: 'y',
                maintainAspectRatio: false,
                scales: {
                    x: {
                        min: 0,
                        max: 4,
                        ticks: {
                            color: surveyChartText
                        },
                        grid: {
                            color: surveyGridColor
                        }
                    },
                    y: {
                        ticks: {
                            color: surveyChartText,
                            autoSkip: false
                        },
                        grid: {
                            display: false
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            };

            new Chart(container.querySelector('#surveySkmChart'), {
                type: 'bar',
                data: {
                    labels: [
                        'Persyaratan', 'Prosedur', 'Kecepatan pelayanan', 'Biaya/tarif', 'Kesesuaian hasil',
                        'Kompetensi petugas', 'Sikap petugas', 'Sarana prasarana', 'Penanganan pengaduan'
                    ],
                    datasets: [{
                        label: 'Rata-rata SKM',
                        data: data.skmQuestionAverages,
                        backgroundColor: 'rgba(22, 132, 71, .72)',
                        borderRadius: 5
                    }]
                },
                options: surveyQuestionChartOptions
            });

            new Chart(container.querySelector('#surveySakChart'), {
                type: 'bar',
                data: {
                    labels: ['Anti diskriminasi', 'Anti kecurangan', 'Anti imbalan', 'Anti pungli', 'Anti calo'],
                    datasets: [{
                        label: 'Rata-rata SAK',
                        data: data.sakQuestionAverages,
                        backgroundColor: 'rgba(253, 185, 12, .78)',
                        borderRadius: 5
                    }]
                },
                options: surveyQuestionChartOptions
            });

            new Chart(container.querySelector('#surveyGenderChart'), {
                type: 'doughnut',
                data: {
                    labels: data.genderLabels,
                    datasets: [{
                        data: data.genderValues,
                        backgroundColor: ['#168447', '#fdb90c'],
                        borderWidth: 2,
                        borderColor: '#fff'
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                color: surveyChartText,
                                boxWidth: 12,
                                padding: 14
                            }
                        }
                    }
                }
            });

            new Chart(container.querySelector('#surveyEducationChart'), {
                type: 'bar',
                data: {
                    labels: data.educationLabels,
                    datasets: [{
                        label: 'Jumlah responden',
                        data: data.educationValues,
                        backgroundColor: 'rgba(22, 132, 71, .72)',
                        borderRadius: 5
                    }]
                },
                options: {
                    indexAxis: 'y',
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0,
                                color: surveyChartText
                            },
                            grid: {
                                color: surveyGridColor
                            }
                        },
                        y: {
                            ticks: {
                                color: surveyChartText,
                                autoSkip: false
                            },
                            grid: {
                                display: false
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });

            new Chart(container.querySelector('#surveyAgeChart'), {
                type: 'bar',
                data: {
                    labels: data.ageLabels,
                    datasets: [{
                        label: 'Jumlah responden',
                        data: data.ageValues,
                        backgroundColor: 'rgba(36, 123, 160, .78)',
                        borderRadius: 5
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            ticks: {
                                color: surveyChartText
                            },
                            grid: {
                                display: false
                            }
                        },
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0,
                                color: surveyChartText
                            },
                            grid: {
                                color: surveyGridColor
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });

            new Chart(container.querySelector('#surveyOccupationChart'), {
                type: 'bar',
                data: {
                    labels: data.occupationLabels,
                    datasets: [{
                        label: 'Jumlah responden',
                        data: data.occupationValues,
                        backgroundColor: 'rgba(142, 108, 190, .78)',
                        borderRadius: 5
                    }]
                },
                options: {
                    indexAxis: 'y',
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0,
                                color: surveyChartText
                            },
                            grid: {
                                color: surveyGridColor
                            }
                        },
                        y: {
                            ticks: {
                                color: surveyChartText,
                                autoSkip: false
                            },
                            grid: {
                                display: false
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });

            new Chart(container.querySelector('#surveyDailyChart'), {
                type: 'line',
                data: {
                    labels: data.dailyLabels,
                    datasets: [{
                        label: 'Jumlah responden',
                        data: data.dailyValues,
                        borderColor: '#168447',
                        backgroundColor: 'rgba(22, 132, 71, .12)',
                        fill: true,
                        tension: .3,
                        pointRadius: 3
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            ticks: {
                                color: surveyChartText,
                                maxRotation: 0,
                                autoSkip: true
                            },
                            grid: {
                                display: false
                            }
                        },
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0,
                                color: surveyChartText
                            },
                            grid: {
                                color: surveyGridColor
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
        }

        async function loadSurveyResults(url, updateHistory = true) {
            surveyFilterError.textContent = '';
            const submitButton = surveyFilterForm.querySelector('button[type="submit"]');
            submitButton.disabled = true;
            surveyFilterForm.setAttribute('aria-busy', 'true');

            try {
                const response = await fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json, text/html'
                    }
                });
                if (!response.ok) {
                    const error = response.headers.get('content-type')?.includes('application/json') ?
                        await response.json() :
                        null;
                    throw new Error(Object.values(error?.errors || {}).flat().join(' ') || error?.message ||
                        `Filter survei gagal diterapkan (${response.status}).`);
                }

                const html = await response.text();
                const nextDocument = new DOMParser().parseFromString(html, 'text/html');
                const nextResults = nextDocument.querySelector('#survey-dashboard-results');
                if (!nextResults) throw new Error('Data hasil filter tidak dapat ditampilkan.');

                const nextForm = nextDocument.querySelector('#survey-filter-form');
                ['start_date', 'end_date', 'id_uptup'].forEach(name => {
                    surveyFilterForm.elements[name].value = nextForm.elements[name].value;
                });
                ['surveySkmChart', 'surveySakChart', 'surveyGenderChart', 'surveyEducationChart', 'surveyAgeChart',
                    'surveyOccupationChart', 'surveyDailyChart'
                ].forEach(id => {
                    const current = Chart.getChart(document.getElementById(id));
                    if (current) current.destroy();
                });
                document.querySelector('#survey-dashboard-results').replaceWith(nextResults);
                renderSurveyCharts(nextResults);
                updateDownloadUrl();

                if (updateHistory) history.pushState({}, '', url);
            } catch (error) {
                surveyFilterError.textContent = error.message;
            } finally {
                submitButton.disabled = false;
                surveyFilterForm.removeAttribute('aria-busy');
            }
        }

        renderSurveyCharts();

        function updateDownloadUrl() {
            const url = new URL(@json(route('admin.survei.download')), window.location.origin);
            new FormData(surveyFilterForm).forEach((value, key) => {
                if (value) url.searchParams.set(key, value);
            });
            document.getElementById('survey-download').href = url.toString();
        }

        updateDownloadUrl();
        surveyFilterForm.addEventListener('input', updateDownloadUrl);
        surveyFilterForm.addEventListener('change', updateDownloadUrl);

        surveyFilterForm.addEventListener('submit', event => {
            event.preventDefault();
            const url = new URL(surveyFilterForm.action, window.location.origin);
            new FormData(surveyFilterForm).forEach((value, key) => {
                if (value) url.searchParams.set(key, value);
            });
            loadSurveyResults(url.toString());
        });

        document.getElementById('survey-filter-reset').addEventListener('click', event => {
            event.preventDefault();
            loadSurveyResults(event.currentTarget.href);
        });

        window.addEventListener('popstate', () => loadSurveyResults(window.location.href, false));
    </script>
@endpush
