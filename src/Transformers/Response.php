<?php
/**
 * Created by PhpStorm.
 * User: zack
 * Date: 2020/5/3
 * Time: 15:48
 */

namespace Zackx\LumenSwoole\Transformers;

use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class Response
{
    /**
     * Notes:
     * @param $swooleResponse
     * @param $illuminateResponse
     * @param string $echoContent
     * User: zack
     * Date: 2020/5/5
     * @return bool
     */
    public static function handle($swooleResponse, $illuminateResponse, $echoContent = '')
    {
        // status
        $swooleResponse->status($illuminateResponse->getStatusCode());
        foreach ($illuminateResponse->headers->allPreserveCase() as $name => $values) {
            foreach ($values as $value) {
                $swooleResponse->header($name, $value);
            }
        }
        // cookies
        foreach ($illuminateResponse->headers->getCookies() as $cookie) {
            $swooleResponse->rawcookie(
                $cookie->getName(),
                urlencode($cookie->getValue()),
                $cookie->getExpiresTime(),
                $cookie->getPath(),
                $cookie->getDomain(),
                $cookie->isSecure(),
                $cookie->isHttpOnly()
            );
        }
        if (!$illuminateResponse instanceof SymfonyResponse) {
            $content = $echoContent . (string)$illuminateResponse;
            $swooleResponse->end($content);
            return true;
        }
        // content
        if ($illuminateResponse instanceof BinaryFileResponse) {
            $realPath = realpath($illuminateResponse->getFile()->getPathname());
            $swooleResponse->sendfile($realPath);
            return true;
        }
        $content = $echoContent . $illuminateResponse->getContent();
        $swooleResponse->end($content);
    }
}
