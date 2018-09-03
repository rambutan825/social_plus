<?php

declare(strict_types=1);

namespace Zhiyi\Plus\FileStorage\Filesystems;

use OSS\OssClient;
use Illuminate\Http\Request;
use Zhiyi\Plus\FileStorage\Task;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Facades\Auth;
use Zhiyi\Plus\FileStorage\TaskInterface;
use Zhiyi\Plus\FileStorage\ResourceInterface;
use Zhiyi\Plus\FileStorage\FileMetaInterface;
use Symfony\Component\HttpFoundation\Response;

class AliyunOSS implements FilesystemInterface
{
    protected $oss;
    protected $bucket;

    public function __construct(OssClient $oss, string $bucket)
    {
        $this->bucket = $bucket;
        $this->oss = $oss;
    }

    /**
     * Create upload task.
     * @param \Illuminate\Http\Request $request
     * @param \Zhiyi\Plus\FileStorage\ResourceInterface $resource
     * @return \Zhiyi\Plus\FileStorage\TaskInterface
     */
    public function createTask(Request $request, ResourceInterface $resource): TaskInterface
    {
        $user = $this->guard()->user();
        $expiresSecond = 360;
        $headers = [
            OssClient::OSS_CONTENT_MD5 => $request->input('hash'),
            OssClient::OSS_CONTENT_LENGTH => $request->input('size'),
            OssClient::OSS_CONTENT_TYPE => $request->input('mime_type'),
            OssClient::OSS_CALLBACK => json_encode([
                'callbackBodyType' => 'application/json',
                'callbackUrl' => route('storage:callback', [
                    'channel' => $resource->getChannel(),
                    'path' => base64_encode($resource->getPath()),
                ]),
                'callbackBody' => json_encode([
                    'jwt' => '${x:auth-token}'
                ]),
            ]),
            OssClient::OSS_CALLBACK_VAR => json_encode([
                'x:auth-token' => $this->guard()->login($user)
            ])
        ];

        $url = $this->oss->signUrl(
            $this->bucket,
            $resource->getPath(),
            $expiresSecond,
            OssClient::OSS_HTTP_PUT,
            $headers
        );
        $headers[OssClient::OSS_CALLBACK] = base64_encode($headers[OssClient::OSS_CALLBACK]);
        $headers[OssClient::OSS_CALLBACK_VAR] = base64_encode($headers[OssClient::OSS_CALLBACK_VAR]);

        return new Task($resource, $url, 'PUT', null, null, $headers);
    }

    /**
     * Get file meta.
     * @param \Zhiyi\Plus\FileStorage\ResourceInterface $resource
     * @return \Zhiyi\Plus\FileStorage\FileMetaInterface
     */
    public function meta(ResourceInterface $resource): FileMetaInterface
    {
        return new Local\FileMeta;
    }

    /**
     * Get file response.
     * @param \Zhiyi\Plus\FileStorage\ResourceInterface $resource
     * @param string|null $rule
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function response(ResourceInterface $resource, ?string $rule = null): Response
    {
        return new Response;
    }

    /**
     * Delete file.
     * @param string $path
     * @return bool
     */
    public function delete(string $path): bool
    {
        return false;
    }

    /**
     * Put a file.
     * @param string $path
     * @param mixed $contents
     * @return bool
     */
    public function put(string $path, $contents): bool
    {
        return false;
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\Guard
     */
    public function guard(): Guard
    {
        return Auth::guard('api');
    }
}
