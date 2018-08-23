@foreach ($group as $item)
    <div class="group_item @if($loop->iteration % 2 == 0) group_item_right @endif">
        <dl class="clearfix">
            <dt>
                <a
                @if (in_array($item->mode, ['paid', 'private']) && !$item->joined)
                    href="javascript:noticebox('必须先加入圈子', 0);"
                @else
                    href="{{Route('pc:groupread', $item->id)}}"
                @endif
                >
                    <img src="{{ $item->avatar or asset('assets/pc/images/default_picture.png') }}" width="120" height="120">
                </a>
            </dt>
            <dd>
                <a class="title"
                    @if (in_array($item->mode, ['paid', 'private']) && !$item->joined)
                        href="javascript:noticebox('必须先加入圈子', 0);"
                    @else
                        href="{{Route('pc:groupread', $item->id)}}"
                    @endif
                    alt="{{ $item->name }}"
                >{{ str_limit($item->name, 16, '...') }}
                    @if ($item->mode == 'paid')
                    <span class="paid">付费</span>
                    @endif
                </a>
                <div class="tool">
                    <span>帖子 <font class="mcolor">{{ $item->posts_count }}</font></span>
                    <span>成员 <font class="mcolor" id="join-count-{{ $item->id }}">{{ $item->users_count }}</font></span>
                </div>
                <div class="join">
                    @if ($item->joined)
                        @if ($item->joined->role == 'administrator')
                            <span class="role" >管理员</span>
                        @elseif ($item->joined->role == 'founder')
                            <span class="role" >圈主</span>
                        @else
                             <button
                                class="J-join joined"
                                gid="{{$item->id}}"
                                state="1"
                                mode="{{$item->mode}}"
                                money="{{$item->money}}"
                                onclick="grouped.init(this);"
                            >已加入</button>
                        @endif
                    @else
                        @if ($item->joined($TS['id'], $item->id))
                            <button class="J-join joined" >待审核</button>
                        @else
                            <button
                                class="J-join"
                                gid="{{$item->id}}"
                                state="0"
                                mode="{{$item->mode}}"
                                money="{{$item->money}}"
                                onclick="grouped.init(this);"
                            >+加入</button>
                        @endif
                    @endif
                </div>
            </dd>
        </dl>
    </div>
@endforeach