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
        return view('admin.slider.index', [
            'dataUrl' => route('admin.slider.data'),
        ]);
    }

    public function data(Request $request)
    {
        $start = max(0, (int) $request->input('start', 0));
        $requestedLength = (int) $request->input('length', 10);
        $length = $requestedLength < 0 ? 100 : min(100, max(1, $requestedLength));
        $searchValue = $request->input('search.value', '');
        $search = is_string($searchValue) ? mb_substr(trim($searchValue), 0, 100) : '';
        $query = Slider::query();
        $recordsTotal = (clone $query)->count();

        if ($search !== '') {
            $query->where(function ($builder) use ($search) {
                $builder->where('judul_putih', 'like', "%{$search}%")
                    ->orWhere('judul_kuning', 'like', "%{$search}%")
                    ->orWhere('subjudul', 'like', "%{$search}%");
            });
        }

        $recordsFiltered = (clone $query)->count();
        $orderColumns = [0 => 'urutan', 2 => 'judul_putih', 3 => 'status'];
        $orderIndex = (int) $request->input('order.0.column', 0);
        $orderDirection = $request->input('order.0.dir') === 'desc' ? 'desc' : 'asc';
        $items = $query->orderBy($orderColumns[$orderIndex] ?? 'urutan', $orderDirection)
            ->orderBy('id')
            ->skip($start)
            ->take($length)
            ->get();

        $data = $items->map(function (Slider $slider) {
            $title = trim($slider->judul_putih.' '.$slider->judul_kuning);
            $image = '<img src="'.e(asset('uploads/slider/'.$slider->gambar)).'" alt="'.e($title).'" class="rounded-2 border" style="width: 120px; height: 68px; object-fit: cover;">';
            $status = $slider->status
                ? '<span class="badge bg-success-subtle text-success-emphasis border border-success-subtle">Aktif</span>'
                : '<span class="badge bg-secondary-subtle text-secondary-emphasis border">Nonaktif</span>';
            $actions = '<div class="table-action-buttons"><a href="'.route('admin.slider.edit', $slider).'" class="btn btn-primary btn-icon-action" title="Edit slider" aria-label="Edit slider"><i class="bi bi-pencil"></i></a>'
                .'<form method="POST" action="'.route('admin.slider.destroy', $slider).'" onsubmit="return confirm(\'Hapus slider ini?\')"><input type="hidden" name="_token" value="'.e(csrf_token()).'"><input type="hidden" name="_method" value="DELETE"><button type="submit" class="btn btn-danger btn-icon-action" title="Hapus slider" aria-label="Hapus slider"><i class="bi bi-trash3"></i></button></form></div>';

            return [
                'urutan' => '<span class="badge bg-light text-dark border">'.(int) $slider->urutan.'</span>',
                'gambar' => $image,
                'judul' => '<div class="fw-semibold text-dark">'.e($title).'</div><small class="text-muted">'.e(\Illuminate\Support\Str::limit($slider->subjudul, 100)).'</small>',
                'status' => $status,
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
