<?php

namespace Zhiyi\Plus\Http\Controllers\APIs\V2;

use Illuminate\Http\Request;
use Zhiyi\Plus\Models\User as UserModel;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Illuminate\Contracts\Routing\ResponseFactory as ResponseContract;
// use Illuminate\Contracts\Routing\UrlGenerator as UrlGeneratorContract;

class UserAvatarController extends Controller
{
    public function show(Request $request, UserModel $user)
    {
        $size = intval($request->query('s', 0));
        $size = max($size, 0);
        $size = min($size, 500);

        dd(
            $user->avatar($size)
        );
    }

    public function update(Request $request, ResponseContract $response)
    {
        $this->validate($request, $this->uploadAvatarRules(), $this->uploadAvatarMessages());

        $avatar = $request->file('avatar');
        if (! $avatar->isValid()) {
            return $response->json(['messages' => [$avatar->getErrorMessage()]], 400);
        }

        dd(
            $request->user()->storeAvatar($avatar)
        );
    }

    /**
     * Get upload valodate rules.
     *
     * @return array
     * @author Seven Du <shiweidu@outlook.com>
     */
    protected function uploadAvatarRules(): array
    {
        return [
            'avatar' => [
                'required',
                'image',
                'max:'.$this->getMaxFilesize() / 1024,
                'dimensions:min_width=100,min_height=100,max_width=500,max_height=500,ratio=1/1',
            ]
        ];
    }

    /**
     * Get upload validate messages.
     *
     * @return array
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function uploadAvatarMessages(): array
    {
        return [
            'avatar.required' => '请上传头像.',
            'avatar.image' => '头像必须是 png/jpeg/bmp/gif/svg 图片',
            'avatar.max' => sprintf('头像尺寸必须小于%sMB', $this->getMaxFilesize() / 1024 / 1024),
            'avatar.dimensions' => '头像必须是正方形，宽高必须在 100px - 500px 之间'
        ];
    }

    /**
     * Get upload max file size.
     *
     * @return int
     * @author Seven Du <shiweidu@outlook.com>
     */
    protected function getMaxFilesize()
    {
        return UploadedFile::getMaxFilesize();
    }
}
