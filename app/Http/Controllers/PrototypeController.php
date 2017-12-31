<?php

namespace App\Http\Controllers;

use App\Events\LogEvent;
use App\Events\WebhookEvent;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class PrototypeController extends Controller
{
    private $prototype_path;

    private $types;

    private $categories;

    private $prototype_host;

    public function __construct()
    {
        $this->prototype_path = config('prototype.path');
        $this->types          = config('prototype.types');
        $this->categories     = config('prototype.categories');
        $this->prototype_host = config('prototype.host');
    }

    public function upload()
    {
        if (!$category_type = request()->get('category_type')) {
            return redirect('/prototype');
        }
        if (!$files = request()->file('file')) {
            return redirect('/prototype/' . $category_type);
        }
        $pdfs = $prototypes = [];
        foreach ($files as $file) {
            $extension = $file->extension();
            if (!in_array($extension, ['zip', 'pdf'])) {
                return redirect('/prototype/' . $category_type);
            }
            if ($extension == 'zip') {
                $res          = $this->uploadZip($file);
                $prototypes[] = $res['filename'];
            } elseif ($extension == 'pdf') {
                $res    = $this->uploadPdf($file);
                $pdfs[] = $res['filename'];
            }
        }

        $this->handleEvent($category_type, $prototypes, $pdfs);

        return redirect('/prototype/' . $category_type);
    }

    private function handleEvent($category_type, $prototypes, $pdfs)
    {
        $create_content   = '';
        $update_content   = '';
        $create_prototype = $create_pdf = [];
        $update_prototype = $update_pdf = [];
        $pdf_links        = $prototype_links = [];
        foreach ($prototypes as $prototype) {
            if (File::exists($this->prototype_path . $category_type . '/' . $prototype)) {
                $update_prototype[] = $prototype;
            } else {
                $create_prototype[] = $prototype;
            }
            $prototype_links[] = [
                'name' => $prototype,
                'url'  => $this->prototype_host . $category_type . '/' . $prototype,
            ];
        }
        foreach ($pdfs as $pdf) {
            if (File::exists($this->prototype_path . $category_type . '/pdf/' . $pdf . '.pdf')) {
                $update_pdf[] = $pdf;
            } else {
                $create_pdf[] = $pdf;
            }
            $pdf_links[] = [
                'name' => $pdf,
                'url'  => $this->prototype_host . $category_type . '/pdf/' . $pdf . '.pdf',
            ];
        }
        event(new WebhookEvent($category_type, $prototype_links, $pdf_links));
        if (!empty($create_prototype)) {
            $create_content .= '新增了原型：' . implode('、', $create_prototype) . PHP_EOL;
        }
        if (!empty($update_prototype)) {
            $update_content .= '更新了原型：' . implode('、', $update_prototype) . PHP_EOL;
        }
        if (!empty($create_pdf)) {
            $create_content .= '新增了需求文档：' . implode('、', $create_pdf) . PHP_EOL;
        }
        if (!empty($update_pdf)) {
            $update_content .= '更新了需求文档：' . implode('、', $update_pdf) . PHP_EOL;
        }
        if ($create_content) {
            event(new LogEvent('create', trim($create_content)));
        }
        if ($update_content) {
            event(new LogEvent('update', trim($update_content)));
        }
    }

    private function uploadPdf(UploadedFile $file)
    {
        $category_type = request()->get('category_type');
        $pdf_path      = $this->prototype_path . $category_type . '/pdf/';
        $filename      = $file->getClientOriginalName();
        $file->move($pdf_path, $filename);

        return ['filename' => rtrim($filename, '.pdf')];
    }

    private function uploadZip(UploadedFile $file)
    {
        $category_type      = request()->get('category_type');
        $filename           = $file->getClientOriginalName();
        $prototype_zip_path = $this->prototype_path . 'zips/';
        $prototype_path     = $this->prototype_path . $category_type . '/';
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
            return ['filename' => ''];
        }
        return ['filename' => rtrim($filename, '.zip')];
    }

    public function index()
    {
        return view('prototype.index');
    }

    public function prototypeList($category)
    {
        $search         = request('search');
        $prototype_path = $this->prototype_path;
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
                        'url'         => $this->prototype_host . $dir . '/pdf/' . $pdf_file->getFilename(),
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
                    'url'         => $this->prototype_host . $dir . '/' . $prototype,
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
        $type      = request('type');
        $category  = request('category');
        $filename  = request('filename');
        $file_type = '其他';
        if ($type == 'pdf') {
            $path = $this->prototype_path . $category . '/pdf/' . $filename;
            if (FIle::exists($path)) {
                File::delete($path);
            }
            $filename  = rtrim($filename, '.pdf');
            $file_type = '需求文档';
        } elseif ($type == 'prototype') {
            $path = $this->prototype_path . $category . '/' . $filename;
            if (File::isDirectory($path)) {
                File::deleteDirectory($path);
            }
            $file_type = '原型文档';
        }
        event(new LogEvent('delete', '删除了' . $file_type . ' : ' . $filename));

        return response()->json(['result' => 'success']);
    }
}
