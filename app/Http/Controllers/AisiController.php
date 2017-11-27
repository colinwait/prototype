<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;

class AisiController extends Controller
{
    public function getPicHost()
    {
        $pic_host = 'http://aiss-1254466972.cosbj.myqcloud.com/picture/';
        return $pic_host;
    }

    public function index()
    {
        $data = [];

        $url = 'http://api.pmkoo.cn/aiss/suite/sourceList.do';

        $client = new Client();
        $res    = $client->request('POST', $url, ['form_params' => ['page' => 1]]);
        $res    = \GuzzleHttp\json_decode($res->getBody(), 1);
        if (isset($res['data']['list']) && is_array($res['data']['list'])) {
            $data = $res['data']['list'];
        }

        foreach ($data as &$datum) {
            $datum['url']   = $this->getPicHost() . $datum['hearderImagePath'];
            $datum['suits'] = $datum['suiteCount'];
            $datum['pics']  = $datum['pictureCount'];
        }

        return view('aisi.index', ['catalogs' => $data]);
    }

    public function show($catalog_id)
    {
        $data = [];

        $url   = 'http://api.pmkoo.cn/aiss/suite/suiteList.do';
        $page  = request('page') ?? 1;
        $suits = request('suits');

        if ($page < 1) {
            $page = 1;
        }
        $page_num  = floor($suits / 10);
        $pre_page  = $page > 1 ? $page - 1 : 0;
        $next_page = $page < $page_num ? $page + 1 : 0;

        $client = new Client();
        $res    = $client->request('POST', $url, ['form_params' => [
            'page'     => $page,
            'sourceId' => $catalog_id,
        ]]);
        $res    = \GuzzleHttp\json_decode($res->getBody(), 1);
        if (isset($res['data']['list']) && is_array($res['data']['list'])) {
            $data = $res['data']['list'];
        }

        foreach ($data as &$datum) {
            $datum['url']  = $this->getPicHost() . $datum['source']['catalog'] . '/' . $datum['issue'] . '/' . $datum['headImageFilename'];
            $datum['name'] = $datum['source']['name'];
            $datum['pics'] = $datum['pictureCount'];
        }

        if (request('api')) {
            return ['suites' => $data, 'next_page' => $next_page];
        }

        return view('aisi.suite', ['suites' => $data, 'page' => $page, 'id' => $catalog_id, 'pre_page' => $pre_page, 'next_page' => $next_page, 'suits' => $suits]);
    }

    public function pics($suit_id)
    {
        $count   = request('count');
        $catalog = request('catalog');
        $pics    = [];
        for ($i = 0; $i < $count; $i++) {
            $pics[] = $this->getPicHost() . $catalog . '/' . $suit_id . '/' . $i . '.jpg';
        }

        return view('aisi.pic', ['pics' => $pics]);
    }

    public function newList()
    {
        $data = [];

        $url  = 'http://api.pmkoo.cn/aiss/suite/suiteList.do';
        $page = request('page') ?? 1;

        if ($page < 1) {
            $page = 1;
        }
        $pre_page  = $page > 1 ? $page - 1 : 0;
        $next_page = $page + 1;

        $client = new Client();
        $res    = $client->request('POST', $url, ['form_params' => [
            'page' => $page,
        ]]);
        $res    = \GuzzleHttp\json_decode($res->getBody(), 1);
        if (isset($res['data']['list']) && is_array($res['data']['list'])) {
            $data = $res['data']['list'];
        }

        foreach ($data as &$datum) {
            $datum['url']  = $this->getPicHost() . $datum['source']['catalog'] . '/' . $datum['issue'] . '/' . $datum['headImageFilename'];
            $datum['name'] = $datum['source']['name'];
            $datum['pics'] = $datum['pictureCount'];
        }

        if (request('api')) {
            return ['suites' => $data, 'next_page' => $next_page];
        }

        return view('aisi.new', ['suites' => $data, 'page' => $page, 'pre_page' => $pre_page, 'next_page' => $next_page]);
    }
}
