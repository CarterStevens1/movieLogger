 @props(['tag', 'size' => 'base'])

 @php
     $classes = 'rounded-xl font-bold transition-colors duration-300';
     if ($size === 'base') {
         $classes .= ' text-sm px-5 py-1';
     } elseif ($size === 'small') {
         $classes .= ' text-2xs px-3 py-1';
     }
 @endphp

 <span {{ $attributes(['class' => $classes]) }}>{{ $slot }}</span>
