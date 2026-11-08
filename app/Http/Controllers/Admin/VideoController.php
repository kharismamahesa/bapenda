<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Video;
use Illuminate\Http\Request;

class VideoController extends Controller
{
    public function index()
    {
        $videos = Video::orderByDesc('id')->paginate(15);

        return view('admin.video.index', compact('videos'));
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
