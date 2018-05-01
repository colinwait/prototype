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

    private $file_types;

    public function __construct()
    {
        $this->prototype_path = config('prototype.path');
        $this->types          = config('prototype.types');
        $this->categories     = config('prototype.categories');
        $this->prototype_host = config('prototype.host');
        $this->file_types     = config('prototype.file_types');
    }

    public function upload()
    {
        if (!$category_type = request()->get('category_type')) {
            return redirect('/prototype');
        }
        if (!$files = request()->file('file')) {
            return redirect('/prototype/' . $category_type);
        }
        $type           = request()->get('type');
        $uploaded_files = [];
        foreach ($files as $file) {
            $extension = $file->extension();
            if (!in_array($extension, $this->file_types)) {
                return redirect('/prototype/' . $category_type);
            }
            if ($extension == 'zip') {
                $res = $this->uploadZip($file);
            } else {
                $res = $this->uploadFile($file);
            }
            $uploaded_files[] = $res['filename'];
        }

        $this->handleEvent($category_type, $type, $uploaded_files);

        return redirect('/prototype/' . $category_type . '?type=' . $type);
    }

    private function handleEvent($category_type, $type, $files)
    {
        $create_content = '';
        $update_content = '';
        $create_file    = $update_file = [];
        $file_links     = [];
        foreach ($files as $file) {
            if (File::exists($this->prototype_path . $category_type . '/' . $type . '/' . $file)) {
                $update_file[] = $file;
            } else {
                $create_file[] = $file;
            }
            $file_links[] = [
                'name' => $file,
                'url'  => $this->prototype_host . $category_type . '/' . $type . '/' . $file,
            ];
        }
        event(new WebhookEvent($category_type, $type, $file_links));
        $type_name = $this->types[$type];
        if (!empty($create_file)) {
            $create_content .= '新增了' . $type_name . '：' . implode('、', $create_file) . PHP_EOL;
        }
        if (!empty($update_file)) {
            $update_content .= '更新了' . $type_name . '：' . implode('、', $update_file) . PHP_EOL;
        }
        if ($create_content) {
            event(new LogEvent('create', trim($create_content)));
        }
        if ($update_content) {
            event(new LogEvent('update', trim($update_content)));
        }
    }

    private function uploadFile(UploadedFile $file)
    {
        $category_type = request()->get('category_type');
        $type          = request()->get('type');
        $file_path     = $this->prototype_path . $category_type . '/' . $type . '/';
        if (!is_dir($file_path)) {
            mkdir($file_path, 0755, 1);
        }
        $filename = $file->getClientOriginalName();
        $file->move($file_path, $filename);

        return ['filename' => rtrim($filename, '.pdf')];
    }

    private function uploadZip(UploadedFile $file)
    {
        $type          = request()->get('type');
        $category_type = request()->get('category_type');
        $filename      = $file->getClientOriginalName();
        $zip_file_path = $this->prototype_path . 'zips/';
        $file_path     = $this->prototype_path . $category_type . '/' . $type . '/';
        if (!is_dir($file_path)) {
            mkdir($file_path, 0777, 1);
        }
        $zip_file = $zip_file_path . $filename;
        if (!is_dir($zip_file_path)) {
            mkdir($zip_file_path, 0755, 1);
        }
        $file->move($zip_file_path, $filename);
        $zip = new \ZipArchive();
        $res = $zip->open($zip_file);
        if ($res === true) {
            $zip->extractTo($file_path);
            $zip->close();
        } else {
            return ['filename' => ''];
        }
        return ['filename' => rtrim($filename, '.zip')];
    }

    public function index()
    {
        $categories   = $this->categories;
        $types        = $this->types;
        $current_type = request()->get('type');

        return view('prototype.index', ['categories' => $categories, 'types' => $types, 'current_type' => $current_type]);
    }

    public function prototypeList($category)
    {
        $page           = intval(request('page')) ?: 1;
        $per_num        = intval(request('per_page')) ?: 1000;
        $categories     = $this->categories;
        $search         = request('search');
        $current_type   = request('type') ?: current(array_keys($this->types));
        $prototype_path = $this->prototype_path;
        $dir            = $category;
        $files[$dir]    = [];
        $types          = $this->types;
        foreach ($types as $type => $type_name) {
            $files[$dir][$type] = [];
            $type_dir           = $prototype_path . $dir . '/' . $type;
            if (File::isDirectory($type_dir)) {
                // 扫描所有目录下文件，目录
                $type_files = array_values(array_diff(scandir($type_dir), ['.', '..']));
                foreach ($type_files as $type_file) {
                    $file = $type_dir . '/' . $type_file;
                    if (is_dir($file)) {
                        // 目录
                        if (File::exists($file . '/index.html')) {
                            $last_modified_time = FIle::lastModified($file . '/index.html');
                        } else {
                            // 原型不存在index
                            continue;
                        }
                        $prototype = last(explode('/', $type_file));
                        if (in_array($prototype, ['__MACOSX', 'pdf'])) {
                            continue;
                        }
                    } else {
                        $last_modified_time = File::lastModified($file);
                    }
                    // 文件
                    if ($search && !str_contains($type_file, $search)) {
                        continue;
                    }
                    $files[$dir][$type][] = [
                        'name'        => $type_file,
                        'url'         => $this->prototype_host . $dir . '/' . $type . '/' . $type_file,
                        'update_time' => date('Y-m-d H:i', intval($last_modified_time)),
                    ];
                }
                if (isset($files[$dir][$type])) {
                    $files[$dir][$type] = collect($files[$dir][$type])->sortByDesc(function ($list, $key) {
                        return $list['update_time'];
                    })->values()->forPage($page, $per_num)->toArray();
                }
            }
        }

        return view('prototype.list')->with(
            [
                'files'        => $files,
                'types'        => $types,
                'current_type' => $current_type,
                'category'     => $category,
                'categories'   => $categories,
                'page'         => $page,
                'per_num'      => $per_num
            ]
        );
    }

    public function delete()
    {
        $type      = request('type');
        $category  = request('category');
        $filename  = request('filename');
        $path      = $this->prototype_path . $category . '/' . $type . '/' . $filename;
        $file_type = $this->types[$type];
        if (FIle::exists($path)) {
            File::delete($path);
        }
        if (File::isDirectory($path)) {
            File::deleteDirectory($path);
        }
        event(new LogEvent('delete', '删除了' . $file_type . ' : ' . $filename));

        return response()->json(['result' => 'success']);
    }
}
