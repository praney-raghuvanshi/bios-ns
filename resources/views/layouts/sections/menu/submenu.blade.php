<ul class="menu-sub">
  @if (isset($menu))
  @foreach ($menu as $submenu)

  @if(auth()->user()->can('view ' . $submenu->slug))
  {{-- active menu method --}}
  @php
  $activeClass = null;
  $active = $configData["layout"] === 'vertical' ? 'active open':'active';
  $currentRouteName = Route::currentRouteName();

  if ($currentRouteName === $submenu->slug) {
  $activeClass = 'active';
  }
  elseif (isset($submenu->submenu)) {
  if (gettype($submenu->slug) === 'array') {
  foreach($submenu->slug as $slug){
  if (str_contains($currentRouteName,$slug) and strpos($currentRouteName,$slug) === 0) {
  $activeClass = $active;
  }
  }
  }
  else{
  if (str_contains($currentRouteName,$submenu->slug) and strpos($currentRouteName,$submenu->slug) === 0) {
  $activeClass = $active;
  }
  }
  }
  @endphp

  <li class="menu-item {{$activeClass}} d-flex flex-row align-items-center justify-content-between">
    <a href="{{ isset($submenu->url) ? url($submenu->url) : 'javascript:void(0)' }}"
      class="{{ isset($submenu->submenu) ? 'menu-link menu-toggle' : 'menu-link' }} flex-grow-1"
      @if(isset($submenu->target) and !empty($submenu->target)) target="_blank" @endif>
      @if (isset($submenu->icon))
      <i class="{{ $submenu->icon }}"></i>
      @endif
      <div>{{ isset($submenu->name) ? __($submenu->name) : '' }}</div>
      @isset($submenu->badge)
      <div class="badge bg-{{ $submenu->badge[0] }} rounded-pill ms-auto">{{ $submenu->badge[1] }}</div>
      @endisset
    </a>
    <button class="btn p-0 cursor-pointer bg-transparent shadow-none favourite-btn" data-id="{{ $submenu->slug }}"
      data-name="{{ $submenu->name }}" data-url="{{ $submenu->url }}" data-icon="{{ $submenu->icon }}"
      data-favourite="{{ in_array($submenu->slug, auth()->user()->favourites()) ? 'true' : 'false' }}">
      <i
        class="ti {{in_array($submenu->slug, auth()->user()->favourites()) ? 'ti-heart-filled text-danger' : 'ti-heart'}}  ti-md "></i>
    </button>

    {{-- submenu --}}
    @if (isset($submenu->submenu))
    @include('layouts.sections.menu.submenu',['menu' => $submenu->submenu])
    @endif
  </li>
  @endif
  @endforeach
  @endif
</ul>