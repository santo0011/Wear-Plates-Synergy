@php $groupId = 'nav-'.$group['key']; @endphp
<div @class(['nav-group', 'open' => $group['open'], 'has-active' => $group['open']]) data-nav-group>
    <a href="{{ $group['children'][0]['url'] ?? '#' }}" class="nav-group-toggle" title="{{ $group['label'] }}"
       aria-expanded="{{ $group['open'] ? 'true' : 'false' }}" aria-controls="{{ $groupId }}">
        <i class="{{ $group['icon'] }}"></i><span>{{ $group['label'] }}</span>
        <em class="fa-solid fa-chevron-down caret" aria-hidden="true"></em>
    </a>
    <div class="nav-sub" id="{{ $groupId }}">
        <div class="nav-sub-inner">
            @foreach ($group['children'] as $child)
                <a href="{{ $child['url'] }}" @class(['active' => $child['active']])>
                    <span>{{ $child['label'] }}</span>
                </a>
            @endforeach
        </div>
    </div>
</div>
