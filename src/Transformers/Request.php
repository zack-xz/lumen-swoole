<?php
/**
 * Created by PhpStorm.
 * User: zack
 * Date: 2020/5/2
 * Time: 15:48
 */

namespace Zackx\LumenSwoole\Transformers;

use Laravel\Lumen\Http\Request as IlluminateRequest;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

class Request
{
    /**
     * Notes:convert swoole request to illuminate request
     * @param $request
     * User: zack
     * Date: 2020/5/3
     * @return IlluminateRequest
     */
    public static function convertRequest($request)
    {
        $get = isset($request->get) ? $request->get : [];
        $post = isset($request->post) ? $request->post : [];
        $cookie = isset($request->cookie) ? $request->cookie : [];
        $header = isset($request->header) ? $request->header : [];
        $swooleServer = isset($request->server) ? $request->server : [];
        $server = self::convertServer($header, $swooleServer);
        $files = isset($request->files) ? $request->files : [];
        $content = empty($request) ? null : $request->rawContent();

        return self::createIlluminateRequest($get, $post, $cookie, $files, $server, $content);
    }

    /**
     * Notes:
     * @param $header
     * @param $server
     * User: zack
     * Date: 2020/5/4
     * @return array
     */
    protected static function convertServer($header, $server)
    {
        $resultServer =[];
        if (!empty($server)) {
            foreach ($server as $k => $v) {
                $resultServer[strtoupper($k)] = $v;
            }
        }

        $headerServerMapping = [
            'x-real-ip'       => 'REMOTE_ADDR',
            'x-real-port'     => 'REMOTE_PORT',
            'server-protocol' => 'SERVER_PROTOCOL',
            'server-name'     => 'SERVER_NAME',
            'server-addr'     => 'SERVER_ADDR',
            'server-port'     => 'SERVER_PORT',
            'scheme'          => 'REQUEST_SCHEME',
        ];
        //header头信息
        if (!empty($header)) {
            foreach ($header as $key => $value) {
                if (isset($headerServerMapping[$key])) {
                    $resultServer[$headerServerMapping[$key]] = $value;
                } else {
                    $key = str_replace('-', '_', $key);
                    $resultServer[strtoupper('http_' . $key)] = $value;
                }
            }
        }
        //是否开启https
        if (isset($resultServer['REQUEST_SCHEME']) && $resultServer['REQUEST_SCHEME'] === 'https') {
            $resultServer['HTTPS'] = 'on';
        }
        //request uri
        if (isset($resultServer['REQUEST_URI']) &&
            strpos($resultServer['REQUEST_URI'], '?') === false &&
            isset($resultServer['QUERY_STRING']) &&
            strlen($resultServer['QUERY_STRING']) > 0
        ) {
            $resultServer['REQUEST_URI'] .= '?' . $resultServer['QUERY_STRING'];
        }

        //全局的
        if (!isset($resultServer['argv'])) {
            $resultServer['argv'] = isset($GLOBALS['argv']) ? $GLOBALS['argv'] : [];
            $resultServer['argc'] = isset($GLOBALS['argc']) ? $GLOBALS['argc'] : 0;
        }

        return $resultServer;
    }

    /**
     * Notes:Create Illuminate Request
     * @param $get
     * @param $post
     * @param $cookie
     * @param $files
     * @param $server
     * @param $content
     * User: zack
     * Date: 2020/5/3
     */
    protected static function createIlluminateRequest($get, $post, $cookie, $files, $server, $content = null)
    {
        IlluminateRequest::enableHttpMethodParameterOverride();

        if ('cli-server' === PHP_SAPI) {
            if (array_key_exists('HTTP_CONTENT_LENGTH', $server)) {
                $server['CONTENT_LENGTH'] = $server['HTTP_CONTENT_LENGTH'];
            }
            if (array_key_exists('HTTP_CONTENT_TYPE', $server)) {
                $server['CONTENT_TYPE'] = $server['HTTP_CONTENT_TYPE'];
            }
        }
        $request = new SymfonyRequest($get, $post, [], $cookie, $files, $server, $content);

        if (0 === strpos($request->headers->get('CONTENT_TYPE'), 'application/x-www-form-urlencoded')
            && in_array(strtoupper($request->server->get('REQUEST_METHOD', 'GET')), ['PUT', 'DELETE', 'PATCH'])
        ) {
            parse_str($request->getContent(), $data);
            $request->request = new ParameterBag($data);
        }

        return IlluminateRequest::createFromBase($request);
    }
}