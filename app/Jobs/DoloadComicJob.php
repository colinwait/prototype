<?php

namespace App\Jobs;

use GuzzleHttp\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class DoloadComicJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $id, $collect;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($comic_id, $comic_collect)
    {
        $this->id      = $comic_id;
        $this->collect = $comic_collect;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->download();
//        $this->test();
    }

    private function test()
    {
        logger($this->id.'/'.$this->collect);
    }

    private function download()
    {
        $id       = $this->id;
        $collect  = $this->collect;
        $pages    = 100;
        $client   = new  Client(['verify' => false]);
        $img_host = 'http://p1.xiaoshidi.net/';
        $path     = '/Users/luyang/Pictures/comic/' . $id . '/' . $collect . '/';
        if (!is_dir($path)) {
            mkdir($path, 0755, 1);
        }
        $pics = [];
        for ($i = 0; $i < $pages; $i++) {
            $url = $this->getHost() . $id . '/' . $collect . '/index_' . $i . '.html';
            try {
                $content = file_get_contents($url);
            } catch (\Exception $e) {
                return;
            }
            preg_match('/img\.src=\"(.*?)\"\+mhurl/s', $content, $host);
            preg_match('/var mhurl = \"(.*?)\";/s', $content, $uri);
            if (isset($host[1])) {
                $img_host = $host[1];
            }
            if (isset($uri[1])) {
                $uri = $uri[1];
            } else {
                continue;
            }
            $img = $img_host . $uri;
            try {
                $client->get($img, ['save_to' => $path . $i . '.jpg']);
            } catch (\Exception $e) {
                continue;
            }
            $pics[] = $img;
        }
    }

    private function getHost()
    {
        return 'http://manhua.fzdm.com/';
    }

}
