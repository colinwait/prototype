<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class PrototypeController extends Controller
{
    public function upload()
    {
        if (!$category_type = request()->get('category_type')) {
            return redirect('/prototype');
        }
        if (!$files = request()->file('file')) {
            return redirect('/prototype/' . $category_type);
        }
        foreach ($files as $file) {
            $extension = $file->extension();
            if (!in_array($extension, ['zip', 'pdf'])) {
                return redirect('/prototype/' . $category_type);
            }
            if ($extension == 'zip') {
                $this->uploadZip($file);
            } elseif ($extension == 'pdf') {
                $this->uploadPdf($file);
            }
        }
        return redirect('/prototype/' . $category_type);
    }

    private function uploadPdf(UploadedFile $file)
    {
        $category_type = request()->get('category_type');
        $pdf_path      = config('prototype.path') . $category_type . '/pdf/';
        $filename      = $file->getClientOriginalName();
        $file->move($pdf_path, $filename);
    }

    private function uploadZip(UploadedFile $file)
    {
        $category_type      = request()->get('category_type');
        $filename           = $file->getClientOriginalName();
        $prototype_zip_path = config('prototype.path') . 'zips/';
        $prototype_path     = config('prototype.path') . $category_type . '/';
        $file_path          = $prototype_zip_path . $filename;
        if (!is_dir($prototype_zip_path)) {
            mkdir($prototype_zip_path, 0755, 1);
        }
        if (!is_dir($prototype_path)) {
            mkdir($prototype_path, 0755, 1);
        }
        $file->move($prototype_zip_path, $filename);
        $zip = new \ZipArchive();
        $res = $zip->open($file_path);
        if ($res === true) {
            $zip->extractTo($prototype_path);
            $zip->close();
        } else {
            return redirect()->back()->with(['message' => '压缩包出问题了']);
        }
    }

    public function index()
    {
        return view('prototype.index');
    }

    public function prototypeList($category)
    {
        $search         = request('search');
        $prototype_path = config('prototype.path');
        $dirs           = File::directories(rtrim($prototype_path, '/'));
        $lists          = [];
        $pdfs           = [];
        foreach ($dirs as $dir) {
            $dir = last(explode('/', $dir));
            if ($dir == 'zips') {
                continue;
            }
            $prototypes = File::directories($prototype_path . $dir);
            $pdfs[$dir] = [];
            if (File::isDirectory($prototype_path . $dir . '/pdf')) {
                $pdf_files = File::files($prototype_path . $dir . '/pdf');
                foreach ($pdf_files as $pdf_file) {
                    if ($search && !str_contains($pdf_file->getFilename(), $search)) {
                        continue;
                    }
                    $pdfs[$dir][] = [
                        'name'        => $pdf_file->getFilename(),
                        'url'         => config('prototype.host') . $dir . '/pdf/' . $pdf_file->getFilename(),
                        'update_time' => date('Y-m-d H:i', intval($pdf_file->getMTime())),
                    ];
                }
            }
            $lists[$dir] = [];
            foreach ($prototypes as $prototype) {
                $last_modified_time = '';
                if (File::exists($prototype . '/index.html')) {
                    $last_modified_time = FIle::lastModified($prototype . '/index.html');
                }
                $prototype = last(explode('/', $prototype));
                if (in_array($prototype, ['__MACOSX', 'pdf'])) {
                    continue;
                }
                if ($search && !str_contains($prototype, $search)) {
                    continue;
                }
                $lists[$dir][] = [
                    'name'        => $prototype,
                    'url'         => config('prototype.host') . $dir . '/' . $prototype,
                    'update_time' => date('Y-m-d H:i', intval($last_modified_time)),
                ];
            }
        }
        $lists = $lists[$category] ?? [];
        $lists = collect($lists)->sortByDesc(function ($list, $key) {
            return $list['update_time'];
        })->values()->all();
        $pdfs  = $pdfs[$category] ?? [];
        $pdfs  = collect($pdfs)->sortByDesc(function ($list, $key) {
            return $list['update_time'];
        })->values()->all();

        return view('prototype.list')->with(['lists' => $lists, 'pdfs' => $pdfs, 'category' => $category]);
    }

    public function delete()
    {
        $type           = request('type');
        $category       = request('category');
        $filename       = request('filename');
        $prototype_path = config('prototype.path');
        if ($type == 'pdf') {
            $path = $prototype_path . $category . '/pdf/' . $filename;
            if (FIle::exists($path)) {
                File::delete($path);
            }
        } elseif ($type == 'prototype') {
            $path = $prototype_path . $category . '/' . $filename;
            if (File::isDirectory($path)) {
                File::deleteDirectory($path);
            }
        }

        return response()->json(['result' => 'success']);
    }
}
