<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Slider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class SliderController extends Controller
{
    private const IMAGE_DIRECTORY = 'uploads/slider';

    public function index()
    {
        $sliders = Slider::orderBy('urutan')->orderBy('id')->paginate(15);

        return view('admin.slider.index', compact('sliders'));
    }

    public function create()
    {
        return view('admin.slider.form', ['slider' => new Slider]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'gambar' => ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'judul_putih' => ['required', 'string', 'max:150'],
            'judul_kuning' => ['required', 'string', 'max:150'],
            'subjudul' => ['required', 'string', 'max:1000'],
            'urutan' => ['required', 'integer', 'min:0'],
            'status' => ['required', 'boolean'],
        ]);

        $validated['gambar'] = $this->storeImage($request);
        Slider::create($validated);

        return redirect()->route('admin.slider.index')
            ->with('success', 'Slider berhasil ditambahkan.');
    }

    public function edit(Slider $slider)
    {
        return view('admin.slider.form', compact('slider'));
    }

    public function update(Request $request, Slider $slider)
    {
        $validated = $request->validate([
            'gambar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'judul_putih' => ['required', 'string', 'max:150'],
            'judul_kuning' => ['required', 'string', 'max:150'],
            'subjudul' => ['required', 'string', 'max:1000'],
            'urutan' => ['required', 'integer', 'min:0'],
            'status' => ['required', 'boolean'],
        ]);

        $oldImage = $slider->gambar;
        if ($request->hasFile('gambar')) {
            $validated['gambar'] = $this->storeImage($request);
        }

        $slider->update($validated);

        if (isset($validated['gambar']) && $oldImage !== $validated['gambar']) {
            $this->deleteImage($oldImage);
        }

        return redirect()->route('admin.slider.index')
            ->with('success', 'Slider berhasil diperbarui.');
    }

    public function destroy(Slider $slider)
    {
        $image = $slider->gambar;
        $slider->delete();
        $this->deleteImage($image);

        return redirect()->route('admin.slider.index')
            ->with('success', 'Slider berhasil dihapus.');
    }

    private function storeImage(Request $request): string
    {
        $directory = public_path(self::IMAGE_DIRECTORY);
        if (! File::isDirectory($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        $file = $request->file('gambar');
        $filename = Str::uuid().'.'.$file->extension();
        $file->move($directory, $filename);

        return $filename;
    }

    private function deleteImage(?string $filename): void
    {
        if (! $filename || basename($filename) !== $filename) {
            return;
        }

        $path = public_path(self::IMAGE_DIRECTORY.'/'.$filename);
        if (File::exists($path)) {
            File::delete($path);
        }
    }
}
