<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAccountabilityFileRequest;
use App\Models\AccountabilityFile;
use App\Models\Asset;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AccountabilityFileController extends Controller
{
    public function store(StoreAccountabilityFileRequest $request, Asset $asset)
    {
        $this->authorize('uploadAccountability', $asset);

        $uploaded = $request->file('file');
        $path = $uploaded->store("accountability/{$asset->id}", 'public');

        AccountabilityFile::create([
            'asset_id'            => $asset->id,
            'original_filename'   => $uploaded->getClientOriginalName(),
            'path'                => $path,
            'mime_type'           => $uploaded->getClientMimeType(),
            'size_bytes'          => $uploaded->getSize(),
            'uploaded_by_user_id' => auth()->id(),
        ]);

        if ($asset->accountability_signed === Asset::ACCOUNTABILITY_PENDING) {
            $asset->update(['accountability_signed' => Asset::ACCOUNTABILITY_YES]);
        }

        return back()->with('success', 'Accountability file uploaded.');
    }

    public function download(Asset $asset, AccountabilityFile $file)
    {
        abort_unless($file->asset_id === $asset->id, 404);
        $this->authorize('view', $asset);

        $disk = Storage::disk('public');
        abort_unless($disk->exists($file->path), 404);

        return response()->download(
            $disk->path($file->path),
            $file->original_filename,
        );
    }

    public function destroy(Asset $asset, AccountabilityFile $file)
    {
        abort_unless($file->asset_id === $asset->id, 404);

        if (! auth()->user()->can('delete accountability files')) {
            abort(403);
        }

        Storage::disk('public')->delete($file->path);
        $file->delete();

        return back()->with('success', 'File removed.');
    }
}
