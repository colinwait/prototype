<?php

namespace App\Http\Controllers;

use App\Jobs\DoloadComicJob;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class ComicController extends Controller
{
    public function index()
    {
        return view('comic.index');
    }

    private function getHost()
    {
        return 'http://manhua.fzdm.com/';
    }

    public function download()
    {
        $id       = request('id');
        $collects = request('collects');
        if ($collects) {
            $collects = explode(',', $collects);
        } else {
            $collects = [];
            try {
                $contents = file_get_contents($this->getHost() . $id);
                $pattern  = "/<li class=\"pure-u-1-2 pure-u-lg-1-4\"><a href=\"(.*?)\"/";
                preg_match_all($pattern, $contents, $matchs);
            } catch (\Exception $e) {
                return;
            }
            if (isset($matchs[1]) && is_array($matchs[1])) {
                $collects = $matchs[1];
                krsort($collects);
            }
        }
        foreach ($collects as $collect) {
            DoloadComicJob::dispatch($id, $collect);
        }

        return redirect()->back()->with(['message' => '已下发漫画id：' . $id . ' 下载任务']);
    }
}
