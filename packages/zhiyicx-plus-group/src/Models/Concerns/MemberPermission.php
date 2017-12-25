<?php

namespace Zhiyi\PlusGroup\Models\Concerns;

use Zhiyi\Plus\Models\User as UserModel;

trait MemberPermission
{
    // 圈子成员权限等级
    protected $level = [
        'member' => 0,
        'administrator' => 1,
        'founder' => 2
    ];

    /**
     * 判断当前用户是否可以被认证用户操作.
     *
     * @param $user
     * @return boolen
     * @author BS <414606094@qq.com>
     */
    public function canBeSet($user)
    {
        if ($this->role === 'founder') {
            return false;
        }

        if ($user instanceof UserModel) {
            $user = $user->id;
        }

        $memberInfo = $this->newQuery()->where('user_id', $user)->where('group_id', $this->group_id)->first();
        if (! $memberInfo) {
            return false;
        }

        return $this->level[$memberInfo->role] > $this->level[$this->role];
    }

    /**
     * 判断当前用户是否可以被设置管理(认证用户是否为圈主).
     *
     * @param  $user
     * @return boolen
     * @author BS <414606094@qq.com>
     */
    public function canBeSetManager($user)
    {
        if ($user instanceof UserModel) {
            $user = $user->id;
        }

        if ($this->newQuery()->where('user_id', $user)->where('group_id', $this->group_id)->value('role') === 'founder') {
            return true;
        }

        return false;
    }
}