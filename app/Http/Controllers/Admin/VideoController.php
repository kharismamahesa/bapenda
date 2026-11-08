<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Video;
use Illuminate\Http\Request;

class VideoController extends Controller
{
    public function index()
    {
        return view('admin.video.index', [
            'dataUrl' => route('admin.video.data'),
        ]);
    }

    public function data(Request $request)
    {
        $start = max(0, (int) $request->input('start', 0));
        $requestedLength = (int) $request->input('length', 10);
        $length = $requestedLength < 0 ? 100 : min(100, max(1, $requestedLength));
        $searchValue = $request->input('search.value', '');
        $search = is_string($searchValue) ? mb_substr(trim($searchValue), 0, 100) : '';
        $query = Video::query();
        $recordsTotal = (clone $query)->count();

        if ($search !== '') {
            $query->where(function ($builder) use ($search) {
                $builder->where('judul', 'like', "%{$search}%")
                    ->orWhere('video', 'like', "%{$search}%")
                    ->orWhere('keterangan', 'like', "%{$search}%");
            });
        }

        $recordsFiltered = (clone $query)->count();
        $orderColumns = [0 => 'id', 1 => 'judul', 2 => 'video', 3 => 'keterangan'];
        $orderIndex = (int) $request->input('order.0.column', 0);
        $orderDirection = $request->input('order.0.dir') === 'asc' ? 'asc' : 'desc';
        $items = $query->orderBy($orderColumns[$orderIndex] ?? 'id', $orderDirection)
            ->skip($start)
            ->take($length)
            ->get();

        $data = $items->map(function (Video $video) {
            $embedUrl = Video::normalizeYoutubeUrl((string) $video->video);
            $videoLink = $embedUrl
                ? '<a href="'.e($embedUrl).'" target="_blank" rel="noopener noreferrer" class="text-break">'.e($embedUrl).'</a>'
                : '<span class="badge bg-warning-subtle text-warning-emphasis">Link tidak valid</span>';
            $actions = '<div class="table-action-buttons"><a href="'.route('admin.video.edit', $video).'" class="btn btn-primary btn-icon-action" title="Edit video" aria-label="Edit video"><i class="bi bi-pencil"></i></a>'
                .'<form method="POST" action="'.route('admin.video.destroy', $video).'" onsubmit="return confirm(\'Hapus video ini?\')"><input type="hidden" name="_token" value="'.e(csrf_token()).'"><input type="hidden" name="_method" value="DELETE"><button type="submit" class="btn btn-danger btn-icon-action" title="Hapus video" aria-label="Hapus video"><i class="bi bi-trash3"></i></button></form></div>';

            return [
                'id' => '<span class="text-muted">#'.(int) $video->id.'</span>',
                'judul' => '<span class="fw-semibold text-dark">'.e($video->judul).'</span>',
                'video' => $videoLink,
                'keterangan' => '<span class="text-muted">'.e(\Illuminate\Support\Str::limit($video->keterangan, 100) ?: '—').'</span>',
                'aksi' => $actions,
            ];
        });

        return response()->json([
            'draw' => (int) $request->input('draw', 1),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ]);
    }

    public function create()
    {
        return view('admin.video.form', ['video' => new Video]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateVideo($request);
        $validated['video'] = Video::normalizeYoutubeUrl($validated['video']);

        Video::create($validated);

        return redirect()->route('admin.video.index')
            ->with('success', 'Video berhasil ditambahkan.');
    }

    public function edit(Video $video)
    {
        return view('admin.video.form', compact('video'));
    }

    public function update(Request $request, Video $video)
    {
        $validated = $this->validateVideo($request);
        $validated['video'] = Video::normalizeYoutubeUrl($validated['video']);

        $video->update($validated);

        return redirect()->route('admin.video.index')
            ->with('success', 'Video berhasil diperbarui.');
    }

    public function destroy(Video $video)
    {
        $video->delete();

        return redirect()->route('admin.video.index')
            ->with('success', 'Video berhasil dihapus.');
    }

    private function validateVideo(Request $request): array
    {
        return $request->validate([
            'judul' => ['required', 'string', 'max:255'],
            'video' => [
                'required',
                'string',
                'max:2048',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    if (! is_string($value) || Video::normalizeYoutubeUrl($value) === null) {
                        $fail('Masukkan link YouTube yang valid, seperti youtube.com/watch?v=... atau youtu.be/....');
                    }
                },
            ],
            'keterangan' => ['nullable', 'string', 'max:5000'],
        ], [
            'judul.required' => 'Judul video wajib diisi.',
            'video.required' => 'Link YouTube wajib diisi.',
        ]);
    }
}
